<?php
use MediaWiki\Config\SiteConfiguration;
use MediaWiki\Languages\Data\Names;
use MediaWiki\Registration\ExtensionRegistry;
use Wikimedia\ObjectCache\RedisBagOStuff;
use Wikimedia\Rdbms\Database;
use Wikimedia\Rdbms\DatabaseFactory;
use Wikimedia\Rdbms\Platform\ISQLPlatform;

class LoadWiki {

	/**
	 * Instance of the redis cache that we use throughout
	 * this is set to persistent in $wgObjectCaches
	 *
	 * @var RedisBagOStuff
	 */
	private RedisBagOStuff $cache;

	/**
	 * Whether we are running in CLI mode or not (if so, everything is taken
	 * from the database as opposed to using the cache to ensure we do not encounter
	 * stale data)
	 * @var bool
	 */
	private bool $commandLineMode = false;

	/**
	 * The incoming server name from NGINX
	 * @var string|null
	 */
	private ?string $serverName = null;

	/**
	 * The wiki_id of this wiki (cc_wikis.wiki_id)
	 * @var string|null
	 */
	public ?string $wikiId = null;

	/**
	 * The database name for this wiki
	 * @var string|null
	 */
	public ?string $dbName = null;

	/**
	 * An array containing all of the data about the incoming URL
	 * to allow us to determine which wiki we are on et al.
	 * This should contain
	 * - The Protocol (https etc)
	 * - The Path (/wiki/Main_Page)
	 * - The language if any (fr)
	 * The language path should be stripped from the path
	 * @var array
	 */
	private array $parsedUrl = [];

	/**
	 * The time that the platform settings were last changed. We check wiki settings
	 * against this. If the times differ, then the wikis settings are stale and need to be
	 * refetched from DB
	 * @var int
	 */
	private int $settingsTimestamp;

	/**
	 * The time that the platform extensions were last changed. We check wiki extensions
	 * against this. If the times differ, then the wikis extensions are stale and need to be
	 * refetched from DB (ie, an extension may have been globally disabled)
	 * @var int
	 */
	private int $extensionsTimestamp;

	/**
	 * An array representation of our wiki object from the database (or Redis)
	 * @var array
	 */
	private array $wiki = [];

	/**
	 * Database handler for lookups if not found in cache
	 * @var Database|null
	 */
	private ?Database $handler = null;

	/**
	 * Flags associated with this wiki (closed et al)
	 * @var int
	 */
	private int $wikiFlags = 0;

	/**
	 * All of our extensions
	 * @var array
	 */
	private array $extensions = [];

	/**
	 * All of our variables/$wg settings
	 * @var array
	 */
	private array $variables = [];

	/**
	 * The namespaces for this wiki
	 * @var array
	 */
	private array $namespaces = [];

	/**
	 * @var array
	 */
	private array $permissions = [];


	public function __construct() {
		global $wgObjectCaches, $wgConf;

		if ( !isset( $wgObjectCaches['configcentre'] ) ) {
			throw new RuntimeException( "Unable to connect to Redis. Cannot continue..." );
		}

		$wgConf = new SiteConfiguration();

		// HTTP requests will first go through the cache, and if not found, the data will be set in there
		// for cli requests, we will always get the data from the database to ensure
		// we have the most up-to-date data
		// Jobs will also utilise the database every time
		$cache = new RedisBagOStuff( $wgObjectCaches['configcentre'] );

		$this->cache = $cache;

		$this->commandLineMode = PHP_SAPI === 'cli' || defined( 'MW_ENTRY_POINT' ) && MW_ENTRY_POINT === 'cli';

		self::determineWikiContext();
		$this->settingsTimestamp = $this->getTimestampForType( 'variables' );
		$this->extensionsTimestamp = $this->getTimestampForType( 'extensions' );
	}

	/**
	 * Determine the wiki context, either through the database name if defined,
	 * or through the SERVER_NAME if HTTP case. By the time this function exits we MUST know
	 * - The wiki_id
	 * - The database name
	 * - The server
	 * @return void
	 */
	private function determineWikiContext(): void {
		if ( !empty( $_SERVER['SERVER_NAME' ] ) ) {
			$this->serverName = strtolower( $_SERVER['SERVER_NAME'] );
			$fullUrl = self::getCurrentURI();

			// let's check if we have a language path
			$this->parsedUrl = parse_url( $fullUrl );
			$path = $this->parsedUrl['path'] ?? '/';
			$trimmedPath = rtrim( $path, '/' );

			if ( $trimmedPath !== '' && $trimmedPath !== '/' ) {
				$slash = strpos( $trimmedPath, '/', 1 ) ?: strlen( $trimmedPath );

				if ( $slash ) {
					// in an ideal world, we would use LanguageNameUtils here and check against
					// the languages MediaWiki supports, but unfortunately we cannot do that here
					// without going through MediaWikiServices which is not available at the point
					// that this runs, so lets work around it
					$names = new Names();
					$languages = $names::NAMES;

					$langCode = substr( $trimmedPath, 1, $slash - 1 );

					if ( isset( $languages[ $langCode ] ) && $langCode !== 'en' && $langCode !== 'en-gb' ) {
						$this->parsedUrl['lang'] = $langCode;
						$this->parsedUrl['path'] = substr( $path, $slash ) ?: '/';
					}
				}
			}

			// we don't know it yet
			$this->wikiId = null;
		} elseif ( defined( 'MW_DB' ) ) {
			$this->commandLineMode = true;
			$this->dbName = MW_DB;
		} else {
			// nothing else can be done here so exit (potentially return some kind of 500 error or something)
			// @TODO that
			throw new RuntimeException( "Unable to determine wiki context. Cannot continue...\n" );
		}
	}

	/**
	 * Main entry point responsible for delegating to other methods to set globals et al.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function execute(): void {
		// HTTP case, we know the server name so we can check Redis first
		if ( empty( $this->commandLineMode ) && !empty( $this->serverName ) ) {
			$key = $this->cache->makeGlobalKey(
				'configcentre',
				'byDomain',
				'v0',
				rtrim( $this->serverName . '/' . ( $this->parsedUrl['lang'] ?? '' ), '/' )
			);
			$res = $this->cache->get( $key );
			$this->wiki = isset( $res['wiki_id'] ) ? $res : [];
		}

		if ( !isset( $this->wiki['wiki_id'] ) || $this->commandLineMode ) {
			if ( !empty( $this->dbName ) ) {
				$this->lookupViaDatabaseName();
			} elseif ( !empty( $this->serverName ) ) {
				$this->lookupViaServerName();
			}
		} else {
			// data taken from the cache
			$this->wikiId = $this->wiki['wiki_id'];
			$this->dbName = $this->wiki['db_name'];
			$this->wikiFlags = $this->wiki['wiki_flags'];
		}

		// either we have a wiki_id by this point, or the wiki does not exist
		if ( empty( $this->wikiId ) ) {
			require_once __DIR__ . "/MissingWiki.php";
			exit ( 0 );
		}

		// is the domain the user accessed the primary domain for this wiki? if not,
		// redirect them to it!
		$this->checkAndRedirectToCanonicalDomain();

		$this->setGlobals();
		$this->extractAndQueueExtensions();
		$this->extractAndSetVariables();
		$this->extractAndSetNamespaces();
		$this->extractAndSetPermissions();
	}

	private function lookupViaDatabaseName(): void {
		$db = $this->getDB();

		$res = $db->newSelectQueryBuilder()
			->select( [
				'wiki_id',
				'wiki_url',
				'wiki_database',
				'wiki_language',
				'wiki_vertical',
				'wiki_name',
				'wiki_flags',
				'wiki_cluster',
			] )
			->from( 'cc_wikis' )
			->where( [
				'wiki_database' => $this->dbName
			] )
			->caller( __METHOD__ . "::lookupViaDatabaseName" )
			->fetchRow();

		if ( isset( $res->wiki_id ) ) {
			$this->wikiId = $res->wiki_id;

			$this->wiki = [
				'wiki_id' => $res->wiki_id,
				'db_name' => $res->wiki_database,
				'url' => $res->wiki_url,
				'wiki_language' => $res->wiki_language,
				'wiki_name' => $res->wiki_name,
				'vertical' => $res->wiki_vertical,
				'wiki_flags' => $res->wiki_flags,
				'wiki_cluster' => $res->wiki_cluster,
			];
		}
	}

	private function checkAndRedirectToCanonicalDomain(): void {
		// no redirects in CLI!
		if ( $this->commandLineMode ) {
			return;
		}

		if ( empty( $this->wiki['url'] ) ) {
			// don't have the url to redirect to, can't do anything
			return;
		}

		$canonicalParsed = parse_url( $this->wiki['url'] );
		$canonicalDomain = $canonicalParsed['host'];
		$canonicalPath = rtrim( $canonicalParsed['path'] ?? '', '/' );

		$currentPath = rtrim( '/' . ( $this->parsedUrl['lang'] ?? '' ), '/' );
		$needsRedirect = false;

		if ( $this->serverName !== $canonicalDomain ) {
			$needsRedirect = true;
		}

		if ( !empty( $canonicalPath ) && $currentPath !== $canonicalPath ) {
			$needsRedirect = true;
		}

		if ( $needsRedirect ) {
			$redirectUrl = $this->wiki['url'];

			$requestPath = $this->parsedUrl['path'] ?? '/';

			if ( $requestPath !== '/' ) {
				$redirectUrl .= $requestPath;
			}

//			// Add query string if present
//			if ( !empty( $_SERVER['QUERY_STRING'] ) ) {
//				$redirectUrl .= '?' . $_SERVER['QUERY_STRING'];
//			}

			header( 'Location: ' . $redirectUrl, true, 301 );
			exit();
		}
	}

	/**
	 * @throws Exception
	 */
	private function lookupViaServerName(): void {
		$db = $this->getDB();
		$where = [
			'cc_domains.wiki_id = cc_wikis.wiki_id',
			'cc_domains.domain' => rtrim( $this->serverName . '/' . ( $this->parsedUrl['lang'] ?? ''), '/' )
		];

		$res = $db->newSelectQueryBuilder()
			->select( [
				'cc_wikis.wiki_id',
				'cc_wikis.wiki_url',
				'cc_wikis.wiki_database',
				'cc_wikis.wiki_language',
				'cc_wikis.wiki_vertical',
				'cc_wikis.wiki_name',
				'cc_wikis.wiki_flags',
				'cc_wikis.wiki_cluster',
			] )
			->from( 'cc_wikis' )
			->join( 'cc_domains', null, 'cc_domains.wiki_id = cc_wikis.wiki_id' )
			->where( $where )
			->caller( __METHOD__ . "::lookupViaServerName" )
			->fetchRow();

		if ( isset( $res->wiki_id ) ) {
			$this->wikiId = $res->wiki_id;
			$this->dbName = $res->wiki_database;

			$this->wiki = [
				'wiki_id' => $res->wiki_id,
				'db_name' => $res->wiki_database,
				'url' => $res->wiki_url,
				'wiki_language' => $res->wiki_language,
				'wiki_name' => $res->wiki_name,
				'vertical' => $res->wiki_vertical,
				'wiki_flags' => $res->wiki_flags,
				'wiki_cluster' => $res->wiki_cluster,
			];

			if ( empty( $this->commandLineMode ) ) {
				$this->cache->set(
					$this->cache->makeGlobalKey(
						'configcentre',
						'byDomain',
						'v0',
						rtrim( $this->serverName . '/' . ( $this->parsedUrl['lang'] ?? ''), '/' )
					),
					$this->wiki,
					86400
				);
			}
		}

	}

	/**
	 * Get the fully qualified server name
	 *
	 * @return string
	 */
	private function getCurrentURI(): string {
		return ( !str_contains( $_SERVER['REQUEST_URI'], '://' ) ) ? $_SERVER['REQUEST_SCHEME'] .
			'://' .
			$_SERVER['SERVER_NAME'] .
			$_SERVER['REQUEST_URI'] : $_SERVER['REQUEST_URI'];
	}

	/**
	 * Get the timestamp at which point settings or extensions were updated
	 * if the timestamp doesn't exist, invalidate the settings and extensions which will cause the settings to be reset
	 * @param string $type
	 *
	 * @return int
	 */
	private function getTimestampForType( string $type ): int {
		$key = $this->cache->makeGlobalKey( 'configcentre', $type, 'timestamp' );

		if ( $this->cache->get( $key ) ) {
			return $this->cache->get( $key );
		} else {
			$time = time();
			$this->cache->set( $key, $time );
			return $time;
		}
	}

	/**
	 * Get a connection to the database
	 * @throws Exception
	 */
	private function getDB(): Database {

		if ( $this->handler instanceof Database ) {
			return $this->handler;
		}

		global $wgLBFactoryConf, $wgVirtualDomainsMapping, $wgDBtype, $wgDBuser, $wgDBpassword;

		$cluster = $wgVirtualDomainsMapping['virtual-configcentre']['cluster'] ?? null;
		$dBName = $wgVirtualDomainsMapping['virtual-configcentre']['db'] ?? null;

		if ( $dBName === null ) {
			throw new ConfigException("Unable to determine the wiki. Cannot acquire handler...");
		}

		// find out which cluster this is located on
		// if cluster is set, then we need to grab it from $wgLBFactoryConf['externalLoads']
		if ( $cluster === null ) {
			throw new ConfigException("Unable to determine wiki. Cluster us null..");
		}

		// throw again
		$servers = $wgLBFactoryConf['externalLoads'][$cluster] ?? null;
		if ( $servers === null ) {
			throw new ConfigException("Unable to determine wiki. Cluster is not defined..");
		}

		$serverKey = array_key_first( $servers );

		if ( $serverKey === null ) {
			throw new ConfigException("Unable to determine wiki. No server for cluster..");
		}

		$serverHost = $wgLBFactoryConf['hostsByName'][$serverKey] ?? null;

		if ( $serverHost === null ) {
			throw new ConfigException("Unable to determine wiki. No host for cluster server..");
		}

		$db = new DatabaseFactory();

		$this->handler = $db->create(
			$wgDBtype,
			[
				'host' => $serverHost,
				'user' => $wgDBuser,
				'password' => $wgDBpassword,
				'dbname' => $wgVirtualDomainsMapping['virtual-configcentre']['db'],
			]
		);

		// something went wrong here, we did an oopsie
		if ( !$this->handler instanceof Database ) {
			throw new Exception();
		}

		return $this->handler;
	}

	private function setGlobals(): void {
		global $wgDBname, $wgConf, $wgLBFactoryConf, $wgServer;

		// Database stuff
		$wgDBname = $this->dbName;
		$wgConf->settings['wgDBname'][$this->dbName] = $this->dbName;
		$wgLBFactoryConf['sectionsByDB'][$this->dbName] = $this->wiki['wiki_cluster'];
		$wgLBFactoryConf['serverTemplate']['dbname'] = $this->dbName;

		// Wiki Context stuff
		$wgConf->settings['wgWikiId'][$this->dbName] = $this->wikiId;
		$wgConf->settings['wgServer'][$this->dbName] = $this->parsedUrl['scheme'] . '://' . $this->serverName;
		$wgServer = $this->parsedUrl['scheme'] . '://' . $this->serverName;

		// ResourceLoader et al
		$langPrefix = isset( $this->parsedUrl['lang'] ) ? "/{$this->parsedUrl['lang']}" : '';
		$wgConf->settings['wgArticlePath']['default'] = $langPrefix . "/wiki/$1";
		$wgConf->settings['wgScriptPath']['default'] = $langPrefix;
		$wgConf->settings['wgLanguageCode'][$this->dbName] = $this->wiki['wiki_language'];
		$wgConf->settings['wgScript']['default'] = $langPrefix . "/index.php";
		$wgConf->settings['wgLoadScript']['default'] = $langPrefix . "/load.php";
		$wgConf->settings['wgRestPath']['default'] = $langPrefix . "/rest.php";
		$wgConf->settings['wgConfigCentreWikiFlags'][$this->dbName] = $this->wiki['wiki_flags'];
		$wgConf->settings['wgSitename'][$this->dbName] = $this->wiki['wiki_name'];
	}

	/**
	 * Extract and queue all of our extensions onto the registry
	 *
	 * @return void
	 * @throws \MediaWiki\Registration\MissingExtensionException
	 * @throws Exception
	 */
	private function extractAndQueueExtensions(): void {
		if ( !$this->commandLineMode ) {
			$key = $this->cache->makeGlobalKey( 'configcentre', 'extensions', 'v0', $this->wikiId );
			$data = $this->cache->get( $key );

			if ( isset( $data['timestamp'] ) && $data['timestamp'] === $this->extensionsTimestamp ) {
				$this->extensions = $data['data'];
			} else {
				$this->cache->delete( $key );
				$this->extensions = [];
			}
		}

		// either nothing in cache, or expired, fetch from DB
		if ( empty( $this->extensions ) ) {
			$db = $this->getDB();

			// extensions + defaults
			$res = $db->newSelectQueryBuilder()->select( [
				// ae = cc_allowed_extensions
				'ae.extension_id',
				'ae.extension_name',
				'ae.extension_folder',
				'ae.extension_dependencies',
				'ae.extension_default',
				'ae.type'
			] )->from( 'cc_allowed_extensions', 'ae' )->leftJoin(
				'cc_extensions',
				'ce',
				'ae.extension_id = ce.extension_key AND ce.wiki_id = ' . $db->addQuotes( $this->wikiId )
			)->where( [
				'ae.deleted' => 0,
				$db->makeList( [
					'ce.wiki_id IS NOT NULL',
					// Explicitly enabled for this wiki
					'ae.extension_default' => 1
					// default extension
				], ISQLPlatform::LIST_OR )
			] )->caller( __METHOD__ . "::getExtensions" )->fetchResultSet();

			$enabledExtensions = [];
			$extensionDependencies = [];

			foreach ( $res as $row ) {
				$enabledExtensions[] = $row->extension_id;

				// Extension may have dependencies, in which case add those too
				if ( !empty( $row->extension_dependencies ) ) {
					$dependencies = explode( ',', $row->extension_dependencies );
					if ( is_array( $dependencies ) ) {
						$extensionDependencies[$row->extension_id] = $dependencies;
					}
				}
			}

			// recursiverly resolve the dependencies of those dependencies
			$finalExtensions = $this->resolveDependencies( $enabledExtensions, $extensionDependencies );

			if ( !empty( $finalExtensions ) ) {
				$extensionData = $db->newSelectQueryBuilder()->select( [
					'extension_id',
					'extension_name',
					'extension_folder',
					'type'
				] )->from( 'cc_allowed_extensions' )->where( [
					'extension_id' => $finalExtensions,
					'deleted' => 0
				] )->caller( __METHOD__ . "::getFinalExtensions" )->fetchResultSet();

				foreach ( $extensionData as $row ) {
					$this->extensions[$row->extension_id] = [
						'name' => $row->extension_name,
						'folder' => $row->extension_folder,
						'type' => $row->type
					];
				}
			}

			if ( !$this->commandLineMode && !empty( $this->wikiId ) ) {
				$this->cache->set(
					$this->cache->makeGlobalKey( 'configcentre', 'extensions', 'v0', $this->wikiId ),
					[
						'data' => $this->extensions,
						'timestamp' => $this->extensionsTimestamp,
					],
					$this->cache::TTL_DAY
				);
			}
		}

		// add them to the registry to be queued later on heh
		global $wgExtensionDirectory, $wgStyleDirectory;

		foreach ( $this->extensions as $extensionData ) {

			if ( $extensionData['type'] == 1 ) { // extension
				$extensionPath = $wgExtensionDirectory . '/' . $extensionData['folder'];
				if ( file_exists( $extensionPath . '/extension.json' ) ) {
					ExtensionRegistry::getInstance()->queue( $extensionPath . '/extension.json' );
				}
			} elseif ( $extensionData['type'] == 2 ) { // skin
				$skinPath = $wgStyleDirectory . '/' . $extensionData['folder'];
				if ( file_exists( $skinPath . '/skin.json' ) ) {
					ExtensionRegistry::getInstance()->queue( $skinPath . '/skin.json' );
				}
			}
		}
	}

	/**
	 * Resolve extension dependencies recursively
	 * @param array $extensions
	 * @param array $dependencyMap
	 *
	 */
	private function resolveDependencies( array $extensions, array $dependencyMap ): array {
		$resolved = [];
		$resolving = [];

		$resolve = function ( $extension ) use ( &$resolve, &$resolved, &$resolving, $dependencyMap ) {
			if ( in_array( $extension, $resolved ) ) {
				return;
			}

			if ( in_array( $extension, $resolving ) ) {
				// circular dependency, so skip
				return;
			}

			$resolving[] = $extension;

			if ( isset( $dependencyMap[$extension] ) ) {
				foreach ( $dependencyMap[$extension] as $dependency ) {
					$resolve( $dependency );
				}
			}

			$resolving = array_diff( $resolving, [ $extension ] );
			$resolved[] = $extension;
		};

		foreach ( $extensions as $extension ) {
			$resolve( $extension );
		}

		return $resolved;
	}

	/**
	 * Extract our variables, either from the cache or the database, and set them
	 * onto $wgConf and as a global
	 *
	 * @throws Exception
	 */
	private function extractAndSetVariables(): void {
		if ( !$this->commandLineMode ) {
			$key = $this->cache->makeGlobalKey( 'configcentre', 'variables', 'v0', $this->wikiId );
			$data = $this->cache->get( $key );

			if ( isset( $data['timestamp'] ) && $data['timestamp'] === $this->settingsTimestamp ) {
				$this->variables = $data['data'];
			} else {
				$this->cache->delete( $key );
				$this->variables = [];
			}
		}

		// cache miss or expired, fetch from db
		if ( empty( $this->variables ) ) {
			$db = $this->getDB();

			// we need to check which extensions we need to get for
			$extensionIds = array_keys( $this->extensions );

			// Include core settings (extension_id IS NULL) and settings for enabled extensions
			$extensionCondition = $db->makeList( [
				'cas.extension_id IS NULL',
				empty( $extensionIds )
					? '1=0'
					: $db->makeList( [ 'cas.extension_id' => $extensionIds ],
					ISQLPlatform::LIST_AND )
			], ISQLPlatform::LIST_OR );

			$res = $db->newSelectQueryBuilder()->select( [
				// cas = cc_allowed_settings
				'cas.setting_id',
				'cas.setting_key',
				'cas.setting_default',
				'cas.setting_type',
				'cs.setting_value'
			] )->from( 'cc_allowed_settings', 'cas' )->leftJoin( 'cc_settings', 'cs', [
				'cas.setting_id = cs.setting_key',
				'cs.wiki_id' => $this->wikiId
			] )->where( [
				'cas.deleted' => 0,
				$extensionCondition
			] )->caller( __METHOD__ . "::getVariables" )->fetchResultSet();

			foreach ( $res as $row ) {
				// custom or default
				$value = $row->setting_value !== null ? $row->setting_value : $row->setting_default;

				$convertedValue = unserialize( $value );

				$this->variables[$row->setting_key] = $convertedValue;
			}

			if ( !$this->commandLineMode && !empty( $this->wikiId ) ) {
				$this->cache->set(
					$this->cache->makeGlobalKey( 'configcentre', 'variables', 'v0', $this->wikiId ),
					[
						'data' => $this->variables,
						'timestamp' => $this->settingsTimestamp,
					],
					$this->cache::TTL_DAY
				);
			}
		}

		// global these - may need to also set in $GLOBALS but $wgConf should handle
		global $wgConf;
		foreach ( $this->variables as $key => $value ) {
			// keys in ConfigCentre are stored with their $; strip this
			$cleanKey = ltrim( $key, '$' );
			$wgConf->settings[$cleanKey][$this->dbName] = $value;
		}
	}

	private function extractAndSetNamespaces(): void {
		if ( !$this->commandLineMode ) {
			$key = $this->cache->makeGlobalKey( 'configcentre', 'namespaces', 'v0', $this->wikiId );
			$data = $this->cache->get( $key );

			if ( $data ) {
				$this->namespaces = $data;
			} else {
				$this->namespaces = [];
			}
		}

		if ( empty( $this->namespaces ) ) {
			$db = $this->getDB();

			$res = $db->newSelectQueryBuilder()
				->select( ISQLPlatform::ALL_ROWS )
				->from( 'cc_namespaces' )
				->where([
					'wiki_id' => $this->wikiId
				])
				->fetchResultSet();

			foreach ( $res as $row ) {
				$this->namespaces[] = (array)$row;
			}

			$key = $this->cache->makeGlobalKey( 'configcentre', 'namespaces', 'v0', $this->wikiId );
			$this->cache->set( $key, $this->namespaces, 86400);
		}

		global $wgConf, $wgConfigCentreNamespacesAdditional;

		foreach ( $this->namespaces as $namespace ) {
			$id = (int)$namespace['namespace_id'];
			$isMain = $id == 0;
			$wgConf->settings['wgExtraNamespaces']['default'][$id] = $isMain ? "" : $namespace['namespace_name'];
			$wgConf->settings['wgNamespacesToBeSearchedDefault']['default'][$id] = (bool)$namespace['namespace_is_searchable'];
			$wgConf->settings['wgNamespacesWithSubpages']['default'][$id] = (bool)$namespace['namespace_subpages'];
			$wgConf->settings['wgNamespaceContentModels']['default'][$id] = $namespace['namespace_content_model'];

			if ( $namespace['namespace_is_content'] ) {
				$wgConf->settings['wgContentNamespaces']['default'][] = $id;
			}

			if ( $namespace['namespace_protection'] ) {
				$wgConf->settings['wgNamespaceProtection']['default'][$id] = [ $namespace['namespace_protection'] ];
			}

			// Unserialize the content from the database and/or cache, suppressing any errors as the data
			// may not exist there so we don't want errors
			$additional = @unserialize( $namespace['namespace_additional'] );

			if ( !empty( $additional ) ) {
				foreach( $additional as $key => $value ) {
					if ( array_key_exists( $key, $wgConfigCentreNamespacesAdditional ) ) {
						if ( $wgConfigCentreNamespacesAdditional[$key]['type'] == 'vestyle' ) {
							$wgConf->settings[$key]['default'][$id] = $value;
						} else {
							$wgConf->settings[$key]['default'][] = $value;
						}
					}
				}
			}
		}
	}

	/**
	 * Set our permissions onto $wgConf
	 * @return void
	 * @throws Exception
	 */
	private function extractAndSetPermissions(): void {
		if ( !$this->commandLineMode ) {
			$key = $this->cache->makeGlobalKey( 'configcentre', 'permissions', 'v0', $this->wikiId );
			$data = $this->cache->get( $key );

			if ( $data ) {
				$this->permissions = $data;
			} else {
				$this->permissions = [];
			}
		}

		if ( empty( $this->permissions ) ) {
			$db = $this->getDB();

			$res = $db->newSelectQueryBuilder()
				->select( ISQLPlatform::ALL_ROWS )
				->from( 'cc_permissions' )
				->where([
					'wiki_id' => $this->wikiId
				])
				->fetchResultSet();

			foreach ( $res as $row ) {
				$this->permissions[] = (array)$row;
			}

			$key = $this->cache->makeGlobalKey( 'configcentre', 'permissions', 'v0', $this->wikiId );
			$this->cache->set( $key, $this->permissions, 86400);
		}

		global $wgConf;

		foreach ( $this->permissions as $permission ) {
			foreach ( unserialize( $permission['permissions'] ) as $perm ) {
				$wgConf->settings['wgGroupPermissions']['default'][$permission['group_name']][$perm] = true;
			}

			foreach ( unserialize( $permission['add_groups'] ) as $addGroup ) {
				$wgConf->settings['wgAddGroups']['default'][$permission['group_name']][] = $addGroup;
			}

			foreach ( unserialize( $permission['remove_groups'] ) as $removeGroup ) {
				$wgConf->settings['wgRemoveGroups']['default'][$permission['group_name']][] = $removeGroup;
			}

			foreach ( unserialize( $permission['add_self'] ) as $addSelf ) {
				$wgConf->settings['wgGroupsAddToSelf']['default'][$permission['group_name']][] = $addSelf;
			}

			foreach ( unserialize( $permission['remove_self'] ) as $removeSelf ) {
				$wgConf->settings['wgGroupsRemoveFromSelf']['default'][$permission['group_name']][] = $removeSelf;
			}
		}
	}
}