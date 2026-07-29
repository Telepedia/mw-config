<?php

// Don't allow web access.
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

// Start the profiler if requested, before anything else is done
require_once __DIR__ . '/Profiler.php';
TelepediaProfiler::setup( 0.01 );

// Load some stuff that must be initialise and available as globals before
// we determine the wiki context
require_once '/srv/mediawiki/PrivateSettings.php';
require_once '/prod/mediawiki/config/GlobalSkins.php';
require_once '/prod/mediawiki/config/GlobalExtensions.php';
require_once '/prod/mediawiki/config/GlobalCache.php';
require_once '/prod/mediawiki/config/ConfigCentreNamespaces.php';
require_once '/prod/mediawiki/config/GlobalDatabase.php';
require_once '/prod/mediawiki/config/LoadWiki.php';

// Determine the wiki context
$wi = new LoadWiki();
$wi->execute();

// send some data to Prometheus
$wgStatsFormat = 'dogstatsd';
$wgStatsTarget = 'udp://logging.telepedia.net:9125';

$wgConf->settings += [
	// this invalidates user sessions if we ever need to; don't change unless its an emergency!
	'wgAuthenticationTokenVersion' => [
		'default' => '1',
	],
	'wgAWSRegion' => [
		'default' => 'eu-west-2'
	],
	'wgAWSBucketDomain' => [
		'default' => 'static.telepedia.net'
	],
	'wgAWSRepoHashLevels' => [
		'default' => 2
	],
	'wgAWSRepoDeletedHashLevels' => [
		'default' => 3
	],
	'wgAWSBucketName' => [
		'default' => 'static.telepedia.net'
	],
	'wgAWSBucketTopSubdirectory' => [
		'default' => "/$wgDBname"
	],
	'wgUploadBaseUrl' => [
		'default' => 'https://static.telepedia.net'
	],
	'wgGenerateThumbnailOnParse' => [
		'default' => true
	],
	'wgUseCdn' => [
		'default' => true
	],
	'wgDBserver' => [
		'default' => '10.0.0.6',
	],
	'wgCdnMaxAge' => [
		'default' => 2592000
	],
	'wgCdnServersNoPurge' => [
		'default' => [
			"173.245.48.0/20",
			"103.21.244.0/22",
			"103.22.200.0/22",
			"103.31.4.0/22",
			"141.101.64.0/18",
			"108.162.192.0/18",
			"190.93.240.0/20",
			"188.114.96.0/20",
			"197.234.240.0/22",
			"198.41.128.0/17",
			"162.158.0.0/15",
			"104.16.0.0/13",
			"104.24.0.0/14",
			"172.64.0.0/13",
			"131.0.72.0/22",
			"127.0.0.1",
			"95.216.69.18",
			"10.0.0.5"
		]
	],
	'wgImageMagickConvertCommand' => [
		'default' => '/usr/bin/convert'
	],
	'wgAdsEnabled' => [
		'default' => true,
	],
	// Global user page
	'wgGlobalUserPageAPIUrl' => [
		'default' => 'https://meta.telepedia.net/api.php',
	],
	'wgGlobalUserPageDBname' => [
		'default' => 'metawiki',
	],

	// Skins
	'wgCosmosContentWidth' => [
		'default' => 'large',
	],
	'wgVectorResponsive' => [
		'default' => true,
	],
	'wgVectorDefaultSkinVersion' => [
		'default' => 2,
	],
	'wgMFCollapseSectionsByDefault' => [
		'default' => false
	],
	'wgPopupsHideOptInOnPreferencesPage' => [
		'default' => false,
	],

	// AbuseFilter
	'wgAbuseFilterCentralDB' => [
		'default' => 'metawiki',
	],
	'wgAbuseFilterIsCentral' => [
		'default' => false,
		'metawiki' => true,
	],
	'wgAbuseFilterActions' => [
		'default' => [
			'block' => true,
			'blockautopromote' => true,
			'degroup' => false,
			'disallow' => true,
			'rangeblock' => false,
			'tag' => true,
			'throttle' => true,
			'warn' => true,
		],
	],
	'wgGlobalPreferencesDB' => [
		'default' => 'centralauth'
	],
	'wgOATHExclusiveRights' => [
		'default' => [
			'abusefilter-privatedetails',
			'abusefilter-privatedetails-log',
			'centralauth-lock',
			'centralauth-rename',
			'centralauth-suppress',
			'checkuser',
			'checkuser-log',
			'globalblock',
			'globalgrouppermissions',
			'globalgroupmembership',
			'suppressionlog',
			'suppressrevision',
			'userrights',
			'userrights-interwiki',
			'configcentre-restricted',
			'request-to-be-forgotten-admin'
		],
		'+metawiki' => [
			'edituserjs',
			'editsitejs',
		],
	],
	'wgOATHRequiredForGroups' => [
		'default' => [
			'checkuser',
			'steward',
			'staff',
			'saber'
		],
	],

	// VisualEditor
	'wgVisualEditorEnableWikitext' => [
		'default' => true
	],
	'wgVisualEditorEnableDiffPage' => [
		'default' => true
	],
	'wgVisualEditorUseSingleEditTab' => [
		'default' => true
	],

	// CreateWiki
	'wgCreateWikiSQLFiles' => [
		'default' => [
			"$IP/maintenance/tables-generated.sql",
			"$IP/extensions/AbuseFilter/db_patches/mysql/tables-generated.sql",
			"$IP/extensions/CheckUser/schema/mysql/tables-generated.sql",
			"$IP/extensions/Echo/sql/mysql/tables-generated.sql",
			"$IP/extensions/GlobalBlocking/sql/mysql/tables-generated-global_block_whitelist.sql",
			"$IP/extensions/AntiSpoof/sql/mysql/tables-generated.sql"
		],
	],
	'wgCreateWikiSubdomain' => [
		'default' => '.telepedia.net',
	],
	'wgCreateWikiDefaultSkinOptions' => [
		'default' => [
			[
				'name' => 'vector',
				'example' => 'https://static.telepedia.net/commonswiki/5/57/Vector_Example.png',
				'display' => 'Vector'
			], 
			[
				'name' => 'vector-2022',
				'example' => 'https://static.telepedia.net/commonswiki/9/93/Vector-2022_Example.png',
				'display' => 'Vector (2022)'
			], 
			[
				'name' => 'monobook',
				'example' => 'https://static.telepedia.net/commonswiki/4/47/MonoBook_Example.png',
				'display' => 'MonoBook'
			],
			[
				'name' => 'timeless',
				'example' => 'https://static.telepedia.net/commonswiki/7/7b/Timeless_Example.png',
				'display' => 'Timeless'
			]
		]
	],
	'wgCreateWikiClusters' => [
		'default' => [
			'c1'
		]
	],

	// Logo
	'wgLogo' => [
		'default' => 'https://static.telepedia.net/metawiki/9/9f/Telepedia_icon.svg',
	],

	// DisplayTitle
	'wgAllowDisplayTitle' => [
		'default' => true,
	],
	'wgRestrictDisplayTitle' => [
		'default' => false,
	],

	// GlobalBlocks
	'wgApplyGlobalBlocks' => [
		'default' => true
	],
	'wgGlobalBlockingDatabase' => [
		'default' => 'globalblocks',
	],

	// Database
	'wgAllowSchemaUpdates' => [
		'default' => false,
	],
	'wgSharedTables' => [
		'default' => [],
	],

	// Permissions and Groups
	'wgImplicitGroups' => [
		'default' => [
			'*',
			'user',
			'autoconfirmed'
		],
	],

	// HTTPS and env
	'wgForceHTTPS' => [
		'default' => true
	],

	// Licenses
	'wgRightsUrl' => [
		'default' => 'https://creativecommons.org/licenses/by-sa/3.0/',
	],
	'wgRightsText' => [
		'default' => 'Creative Commons Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0)',
	],

	// Files
	'wgEnableUploads' => [
		'default' => true,
	],
	'wgAllowCopyUploads' => [
		'default' => false,
	],
	'wgCookieSameSite' => [
		'default' => "None",
	],
	'wgFileExtensions' => [
		'default' => [
			'gif',
			'ico',
			'jpg',
			'jpeg',
			'ogg',
			'pdf',
			'png',
			'svg',
			'webp',
		]
	],
	'wgUseInstantCommons' => [
		'default' => false
	],
	'wgMaxImageArea' => [
		'default' => '1.25e7',
	],
	'wgMaxAnimatedGifArea' => [
		'default' => '1.25e7',
	],
	'wgUseImageMagick' => [
		'default' => true,
	],
	'wgSVGConverter' => [
		'default' => 'Inkscape',
	],
	'wgImageLimits' => [
		'default' => [
			[ 320, 240 ],
			[ 640, 480 ],
			[ 800, 600 ],
			[ 1024, 768 ],
			[ 1280, 1024 ],
			[ 2560, 2048 ],
			[ 2560, 2048 ],
		],
	],
	'wgNativeImageLazyLoading' => [
		'default' => true
	],

	'wgMaxUploadSize' => [
		'default' => 1024 * 1024 * 12,
	],
	'wgCopyUploadsFromSpecialUpload' => [
		'default' => true,
	],

	// Miscellaneous
	'wgCaptchaClass' => [
		'default' => MediaWiki\Extension\ConfirmEdit\Turnstile\Turnstile::class
	],
	'wgGitInfoCacheDirectory' => [
		'default' => '/srv/mediawiki/cache/gitinfo',
	],
	'wgLogSpamBlacklistHits' => [
		'default' => true
	],
	'wgSFSIPListLocation' => [
		'default' => '/srv/mediawiki/stopforumspam/listed_ip_90_ipv46_all.txt',
	],
	'wgBlacklistSettings' => [
		'default' => [
			'spam' => [
				'files' => [
					'https://meta.telepedia.net/index.php?title=MediaWiki:Global_spam_blacklist&action=raw&sb_ver=1',
				],
			],
			'email' => [
				'files' => [
					'https://meta.telepedia.net/index.php?title=MediaWiki:Global_email_blacklist&action=raw&sb_ver=1',
				],
			],
		],
	],
	'wgParsoidSettings' => [
		'default' => [
			'useSelser' => true,
		],
		'+ext-Linter' => [
			'linting' => true,
		],
	],
	'wgScribuntoDefaultEngine' => [
		'default' => 'luasandbox'
	],
	'wgUsePrivateIPs' => [
		'default' => true
	],
	'wgAllowUserCss' => [
		'default' => true
	],
	'wgNoFollowDomainExceptions' => [
		'default' => [
			'miraheze.org',
			'mediawiki.org',
			'en.wikipedia.org',
			'whiki.online',
			'fallout.wiki',
			'jojowiki.com'
		],
	],
	'wgSitename' => [
		'default' => 'No sitename set!',
	],
	'wgEnableCanonicalServerLink' => [
		'default' => false,
	],
	'wgLanguageCode' => [
		'default' => 'en',
	],
	'wgPortableInfoboxUseTidy' => [
		'default' => false,
	],
	'wgPortableInfoboxCacheRenderers' => [
		'default' => true,
	],

	// Parser Cache -- 30 days to reduce unecessary parsing of articles where it is 
	// unecessary
	'wgParserCacheExpireTime' => [
		'default' => 60 * 60 * 24 * 30
	],

	// OAuth
	'wgMWOAuthCentralWiki' => [
		'default' => 'metawiki',
	],
	'wgOAuth2GrantExpirationInterval' => [
		'default' => 'PT4H',
	],
	'wgMWOAuthSecureTokenTransfer' => [
		'default' => true,
	],
	'wgOAuth2PublicKey' => [
		'default' => '/etc/private/public.key',
	],
	'wgOAuth2PrivateKey' => [
		'default' => '/etc/private/private.key',
	],

	// Compress revisions
	'wgCompressRevisions' => [
		'default' => true
	],
	'wgDisableOutputCompression' => [
		'default' => true,
	],

	// Server
	'wgScriptPath' => [
		'default' => '',
	],
	'wgScript' => [
		'default' => "/index.php",
	],
	'wgArticlePath' => [
		'default' => "/wiki/$1",
	],
	'wgLoadPath' => [
		'default' => "/load.php",
	],
	'wgServer' => [
		'default' => 'https://telepedia.net',
	],
	'wgShowHostnames' => [
		'default' => true,
	],
	'wgUsePathInfo' => [
		'default' => true,
	],

	// Shell
	'wgMaxShellMemory' => [
		'default' => 524288
	],

	// Logos and Favicons
	'wgFavicon' => [
		'default' => 'https://static.telepedia.net/metawiki/1/18/Telepedia_Favicon.ico',
	],
	'wgIcon' => [
		'default' => false,
	],
	'wgWordmark' => [
		'default' => false,
	],
	'wgWordmarkHeight' => [
		'default' => 18,
	],
	'wgWordmarkWidth' => [
		'default' => 116,
	],

	// TOC
	'wgMaxTocLevel' => [
		'default' => 999,
	],

	// Extensions
	'wgAllowSiteCSSOnRestrictedPages' => [
		'default' => true,
	],
	'wgMFSiteStylesRenderBlocking' => [
		'default' => true,
	],
	'wgMFCollapseSectionsByDefaul' => [
		'default' => true,
	],
	'wgEnableMetaDescriptionFunctions' => [
		'default' => true,
	],
	'wgThanksConfirmationRequired' => [
		'default' => false,
	],
	'wgWikiSeoEnableSocialImages' => [
		'default' => false
	],
	'wgEchoSharedTrackingDB' => [
		'default' => 'metawiki',
	],
	'wgEchoCrossWikiNotifications' => [
		'default' => true,
	],
	'wgEchoUseJobQueue' => [
		'default' => true,
	],
	'wgNotifyTypeAvailabilityByCategory' => [
		'default' => [
			'agora-reply' => [
				'web' => true,
				'email' => false
			],
			'agora-mention' => [
				'web' => true,
				'email' => false
			]
		]
	],
	'wgCargoDBuser' => [
		'default' => 'cargouser',
	],
	'wgCargoFileDataColumns' => [
		'default' => []
	],
	'wgCargoPageDataColumns' => [
		'default' => []
	],
	'wgCheckUserEnableSpecialInvestigate' => [
		'default' => true
	],
	'wgInterwikiCentralDB' => [
		'default' => 'metawiki',
	],
	'wgEnableScaryTranscluding' => [
		'default' => false,
	],
	'wgCommentsInRecentChanges' => [
		'default' => true,
	],
	'wgCommentsSortDescending' => [
		'default' => false,
	],
	'wgUserProfileV2UseGlobalAvatars' => [
		'default' => true
	],
	'wgUserProfileV2CacheType' => [
		'default' => 'redis'
	],
	'wgUserProfileGlobalUploadBaseUrl' => [
		'default' => 'https://static.telepedia.net'
	],
	'wgUserProfileV2Backend' => [
		'default' => 'telepedia-userprofile'
	],
	'wgMFStripResponsiveImages' => [
		'default' => false
	],

	// Email
	'wgEnableEmail' => [
		'default' => true,
	],
	'wgEnableUserEmail' => [
		'default' => false,
	],
	'wgEmergencyContact' => [
		'default' => 'no-reply@telepedia.net',
	],
	'wgPasswordSender' => [
		'default' => 'no-reply@telepedia.net',
	],
	'wgAllowHTMLEmail' => [
		'default' => true,
	],

	// Other misc
	'wgEnableCanonicalServerLink' => [
		'default' => true
	],

	// GDPR
	'wgRemovePIIAllowedWikis' => [
		'default' => ''
	],
	'wgRemovePIIHashPrefixOptions' => [
		'default' => [
			'Staff' => 'TelepediaGDPR_',
			'Stewards' => 'Vanished user ',
		],
	],
	'wgRemovePIIHashPrefix' => [
		'default' => 'TelepediaGDPR_',
	],
	'wgRemovePIIAutoPrefix' => [
		'default' => 'TelepediaGDPR_',
	],

	// Logging & Logstash
	'wmgLogToDisk' => [
		'default' => false,
		'testingoawiki' => true
	],
	'wmgMonologChannels' => [
		'default' => [
			'@default' => 'error',
			'plat74' => 'debug',
			'404' => 'debug',
			'AbuseFilter' => false,
			'ActionFactory' => false,
			'antispoof' => false,
			'api' => 'warning',
			'api-feature-usage' => false,
			'api-readonly' => false,
			'api-request' => false,
			'api-warning' => false,
			'authentication' => 'info',
			'authevents' => 'info',
			'autoloader' => false,
			'BlockManager' => false,
			'BlogPage' => false,
			'BounceHandler' => false,
			'cache-cookies' => false,
			'caches' => false,
			'captcha' => 'error',
			'cargo' => false,
			'CentralNotice' => false,
			'cite' => false,
			'ContentHandler' => false,
			'CookieWarning' => false,
			'cookie' => false,
			'ConfigCentre' => 'debug',
			'Cloudflare' => 'error',
			'CreateWiki' => 'debug',
			'rdbms' => 'warning',
			'DeferredUpdates' => 'error',
			'DBConnection' => 'warning',
			'DBPerformance' => false,
			'DBQuery' => false,
			'DBReplication' => false,
			'DBTransaction' => false,
			'deprecated' => [ 'logstash' => 'debug', 'sample' => 100 ],
			'diff' => 'debug',
			'DuplicateParse' => false,
			'dynamic-sidebar' => false,
			'editpage' => false,
			'Echo' => 'debug',
			'EditConflict' => 'error',
			'EditConstraintRunner' => 'error',
			'ExternalVideo' => 'debug',
			'error' => 'debug',
			'error-json' => false,
			'EventLogging' => false,
			'EventStreamConfig' => false,
			'exception' => 'debug',
			'exception-json' => false,
			'exec' => 'debug',
			'export' => false,
			'ExternalStore' => false,
			'fatal' => 'debug',
			'FileImporter' => false,
			'FileOperation' => false,
			'formatnum' => false,
			'FSFileBackend' => 'debug',
			'gitinfo' => false,
			'GlobalTitleFail' => false,
			'headers-sent' => false,
			'http' => 'warning',
			'HttpError' => 'error',
			'JobQueueRedis' => 'debug',
			'localisation' => false,
			'LinkBatch' => false,
			'Linter' => 'warning',
			'LocalFile' => 'warning',
			'localhost' => false,
			'LockManager' => 'warning',
			'logging' => false,
			'LoginNotify' => 'info',
			'MassMessage' => false,
			'Math' => 'info',
			'Mime' => false,
			'message-format' => false,
			'MessageCache' => false,
			'MessageCacheError' => 'debug',
			'mobile' => false,
			'NewUserMessage' => false,
			'OAuth' => 'info',
			'objectcache' => false,
			'OldRevisionImporter' => false,
			'OutputBuffer' => false,
			'PageTriage' => false,
			'ParserCache' => false,
			'ParsoidCachePrewarmJob' => 'error',
			'Parsoid' => 'warning',
			'poolcounter' => 'debug',
			'preferences' => false,
			'purge' => false,
			'query' => false,
			'quickinstantcommons' => 'error',
			'ratelimit' => false,
			'readinglists' => false,
			'recursion-guard' => false,
			'RecursiveLinkPurge' => false,
			'redis' => 'info',
			'Renameuser' => 'debug',
			'resourceloader' => false,
			'ResourceLoaderImage' => false,
			'RevisionStore' => false,
			'runJobs' => 'warning',
			'SaveParse' => false,
			'security' => 'debug',
			'session' => false,
			'session-ip' => 'info',
			'SimpleAntiSpam' => false,
			'slow-parse' => 'debug',
			'slow-parsoid' => 'info',
			'SpamBlacklist' => false,
			'SpamBlacklistHit' => false,
			'SpamRegex' => false,
			'StopForumSpam' => false,
			'SQLBagOStuff' => false,
			'squid' => false,
			'StashEdit' => false,
			'texvc' => false,
			'throttler' => false,
			'thumbnail' => 'debug',
			'thumbnailaccess' => false,
			'TitleBlacklist' => false,
			'TitleBlacklist-cache' => false,
			'TranslationNotifications.Jobs' => false,
			'Translate.Jobs' => false,
			'Translate' => false,
			'UpdateRepo' => false,
			'updateTranstagOnNullRevisions' => false,
			'upload' => false,
			'UserOptionsManager' => false,
			'VisualEditor' => 'debug',
			'warning' => false,
			'wfDebug' => false,
			'wfLogDBError' => 'debug',
			'xff' => false,
			'XMP' => false,
			'RTBF' => 'info',
			'Gadgets' => false
		],
	],
	'wgTelelyticsConfigFilePath' => [
		'default' => '/srv/mediawiki/ga-private.json'
	],
	
	//sentinel 
	'wgSentinelDatabase' => [
		'default' => 'centralauth'
	],
	'wgSentinelCentralWiki' => [
		'default' => 'metawiki'
	],

	// Virtual Domains
	'+wgVirtualDomainsMapping' => [
		'default' => [
			'virtual-centralauth' => [
				'db' => 'centralauth',
			],
			'virtual-createwiki-central' => [
				'db' => 'metawiki',
			],
			'virtual-createwiki' => [
				'db' => 'centralauth',
			],
			'virtual-checkuser-global' => [
				'db' => 'centralauth',
			],
			'virtual-globalblocking' => [
				'db' => 'centralauth',
			],
			'virtual-global-database' => [
				'db' => 'centralauth'
			],
			'virtual-configcentre' => [
				'db' => 'centralauth'
			],
			'virtual-LoginNotify' => [
				'db' => 'centralauth'
			],
			'virtual-rtbf' => [
				'db' => 'centralauth'
			],
			'virtual-oathauth' => [
				'db' => 'centralauth'
			]
		]
	],
	'wgGlobalPermissionsConfiguration' => [
		'default' => [
			'metawiki' => [ 'staff', 'saber' ]
		]
	],

	// ConfigCentre
	'wgConfigCentreMasterWiki' => [
		'default' => '32099726c907980eeb175bb00f9f51b3cdd9b08f'
	],
	'wgConfigCentreReservedGroups' => [
		'default' => [
			'staff',
			'saber'
		]
	],
	'wgConfigCentreVerticals' => [
		'default' => [
			'M/TV' => 'Movies & TV',
			'Literature' => 'Books/Manga',
			'Lifestyle' => 'Lifestyle',
			'Gaming' => 'Gaming (Board, Video)',
			'Music' => 'Music',
			'Anime' => 'Anime'
		]
	],
	'wgConfigCentreIsBeta' => [
		'default' => true
	],
	'wgConfigCentreProhibitedPermissions' => [
		'default' => [
				'abusefilter-hide-log',
				'abusefilter-hidden-log',
				'abusefilter-modify-global',
				'abusefilter-private',
				'abusefilter-private-log',
				'abusefilter-privatedetails',
				'abusefilter-privatedetails-log',
				'abusefilter-modify-blocked-external-domains',
				'aft-oversighter',
				'autocreateaccount',
				'bigdelete',
				'centralauth-createlocal',
				'centralauth-lock',
				'centralauth-suppress',
				'centralauth-rename',
				'centralauth-unmerge',
				'checkuser',
				'checkuser-log',
				'checkuser-temporary-account',
 				'checkuser-temporary-account-no-preference',
 				'checkuser-temporary-account-log',
				'createwiki',
				'editincidents',
				'editothersprofiles-private',
				'flow-suppress',
				'generate-random-hash',
				'globalblock',
				'globalblock-exempt',
				'globalgroupmembership',
				'globalgrouppermissions',
				'handle-import-dump-interwiki',
				'handle-import-dump-requests',
				'handle-pii',
				'hideuser',
				'deletelogentry',
				'deleterevision',
				'investigate',
				'ipinfo',
				'ipinfo-view-basic',
				'ipinfo-view-full',
				'ipinfo-view-log',
				'ipblock-exempt',
				'override-antispoof',
				'managewiki-restricted',
				'managewiki-editdefault',
				'checkuser-temporary-account',
				'checkuser-temporary-account-log',
				'protectsite',
				'createwiki-deleterequest',
				'createwiki-suppressionlog',
				'createwiki-suppressrequest',
				'moderation-checkuser',
				'mwoauthmanageconsumer',
				'mwoauthmanagemygrants',
				'mwoauthsuppress',
				'mwoauthviewprivate',
				'mwoauthviewsuppressed',
				'oathauth-api-all',
				'oathauth-enable',
				'oathauth-disable-for-user',
				'oathauth-verify-user',
				'oathauth-view-log',
				'renameuser',
				'requestwiki',
				'siteadmin',
				'smw-patternedit',
				'smw-viewjobqueuewatchlist',
				'stopforumspam',
				'suppressionlog',
				'suppressrevision',
				'themedesigner',
				'titleblacklistlog',
				'updatepoints',
				'userrights',
				'userrights-interwiki',
				'view-private-import-dump-requests',
				'viewglobalprivatefiles',
				'viewpmlog',
				'viewsuppressed',
				'writeapi',
				'profilemanager',
				'sentinel',
				'sentinel-view-logs',
				'gtag-exempt',
				'editinterface-platform',
				'read',
				'skipcaptcha',
				'torunblocked',
				'centralauth-merge',
				'generate-dump',
				'editsitecss',
				'editsitejson',
				'editsitejs',
				'editusercss',
				'edituserjson',
				'edituserjs',
				'editmyoptions',
				'editmyprivateinfo',
				'editmywatchlist',
				'globalblock-whitelist',
				'ipblock-exempt',
				'viewmyprivateinfo',
				'viewmywatchlist',
				'managewiki',
				'profilemanager',
				'no-createwiki',
				'configcentre-restricted',
				'request-to-be-forgotten-admin',
				// T422244
				'import',
				'importupload'
		]
	],
	'wgConfigCentreSettingGroups' => [
		'default' => [
			'Core' => 'Core',
			'Anti-Spam' => 'Anti-Spam',
			'Categories' => 'Categories',
			'Editing' => 'Editing',
			'Links' => 'Links',
			'Localisation' => 'Localisation',
			'Parser Functions' => 'Parser Functions',
			'Media' => 'Media',
			'Permissions' => 'Permissions',
			'Preferences' => 'Preferences',
			'Recent Changes' => 'Recent Changes',
			'SEO' => 'SEO',
			'Styling' => 'Styling'
		]
	],
	'wgSitemapNamespaces' => [
		'default' => [
			0,
			14
		]
	],
	'wgCosmosFetchWantedPagesFromCache' => [
		'default' => true
	],
	'wgCacheDirectory' => [
		'default' => '/srv/mediawiki/cache'
	],

	// DNS blacklist to prevent spam accounts
	'wgEnableDnsBlacklist' => [
		'default' => false
	],
	'wgDnsBlacklistUrls' => [
		'default' => [
			'all.s5h.net.'
		],
	],
	'wgShowCreditsIfMax' => [
		'default' => false
	],
	'wgMaxCredits' => [
		'default' => 0
	],
	'wgSVGNativeRendering' => [
		'default' => true
	],
	'wgRSSUrlWhitelist' => [
		'default' => [
			'*'
		]
	],
	'wgArtemisHost' => [
		'default' => 'jobrunner.telepedia.internal'
	],
	'wgArtemisPort' => [
		'default' => '61613'
	],
	'wgUseArtemisJobQueue' => [
		'default' => false,
		'silowiki' => true
	]
];


// PLAT-87 extract the globals so that anything initialising after LocalSettings has ran has access to the 
// configuration. When MediaWikiServices is called, we then will overwrite $GLOBALS again with our custom configuration
// this ensures that anything added AFTER (such as permissions to groups from extensions) will be disregarded
$globals = $wgConf->getAll( $wgDBname );

require_once '/prod/mediawiki/config/GlobalPermissions.php';

GlobalPermissions::modifyPermissionsAfterManageWiki( $globals );
$globals['wgSharedDB'] = 'metawiki';
$globals['wgSharedTables'][] = 'user';
$globals['wgSharedTables'][] = 'user_autocreate_serial';
$globals['wgSharedTables'][] = 'actor';
$globals['wgSessionName'] = 'telepedia_session';
$globals['wgCookieDomain'] = '.telepedia.net';
$globals['wgCookieSameSite'] = null;
$globals['wgCookiePath'] = '/';

$globals['wgGroupPermissions']['bot']['skipcaptcha'] = true;
$globals['wgExtraNamespaces'][2900] = "Map";
$globals['wgExtraNamespaces'][2901] = "Map_talk";
$globals['wgNamespacesToBeSearchedDefault'][2900] = true;
$globals['wgNamespacesWithSubpages'][2900] = false;
$globals['wgNamespacesWithSubpages'][2901] = false;
$globals['wgNamespaceContentModels'][2900] = "wikimap";
$globals['wgContentNamespaces'][] = 2900;

extract( $globals );

$wgHooks['MediaWikiServices'][] = 'LoadWiki::onMediaWikiServices';

require_once __DIR__ . '/GlobalLogging.php';

$wgUploadDirectory = "{$IP}/images/$wgDBname";
$wgUploadPath = "{$wgScriptPath}/$wgDBname";

// $wgLocalisationCacheConf['storeClass'] = LCStoreCDB::class;
// $wgLocalisationCacheConf['storeDirectory'] = "/srv/mediawiki/cache/l10n/$wgDBname";
// use array here to try and improve performance
$wgLocalisationCacheConf['store'] = 'array';
$wgLocalisationCacheConf['storeDirectory'] = "/srv/mediawiki/cache/l10n/$wgDBname";


if ( $wgRequestTimeLimit ) {
	$wgHTTPMaxTimeout = $wgHTTPMaxConnectTimeout = $wgRequestTimeLimit;
}

// this is a bit of a hack - since ConfigCentre doesn't allow associative arrays, we bodge it here. Furthermore,
// since it also will interpret all options as strings in the list, we need to check it here and force it to a boolean
$recentChangesType = $wgCosmosRailModuleRecentChangesType;

$wgCosmosEnabledRailModules['recentchanges'] = ($recentChangesType === 'false') 
    ? false 
    : $recentChangesType;

$wgAdConfig['enabled'] = true; 

// Define last to avoid all dependencies
require_once '/prod/mediawiki/config/GlobalSettings.php';
require_once '/prod/mediawiki/config/LocalWiki.php';

// Route this wiki's jobs to Apache Artemis if it opted in via $wgUseArtemisJobQueue.
if ( !empty( $wgUseArtemisJobQueue ) ) {
	$wgJobTypeConf['default'] = [
		'class' => \Telepedia\Extensions\TelepediaCore\Artemis\JobQueueArtemis::class,
	];
}

// Configure last to ensure that database name has been set properly
$wgCargoDBname = $wgDBname . 'cargo';

// Temp - to be moved to TelepediaCore ext.
$wgResourceModules['telepedia.fetch'] = [
	'scripts' => 'config/TPRest.js',
	'dependencies' => [
		'mediawiki.util'
	]
];

// T422244 – temporarily disable import and importupload
$wgRevokePermissions['*']['importupload'] = true;
$wgRevokePermissions['*']['import'] = true; 

// Define last - Extension message files for loading extensions
if (
	file_exists( __DIR__ . '/ExtensionMessageFiles.php' ) &&
	!defined( 'MW_NO_EXTENSION_MESSAGES' )
) {
	require_once __DIR__ . '/ExtensionMessageFiles.php';
}

// Placeholder because I don't want to rewrite this every time
// $wgReadOnly = ( PHP_SAPI === 'cli' ) ? false : 'This wiki is currently being upgraded to a newer software version. Please check back in a couple of hours.';

// Don't need a global here
unset( $wi );