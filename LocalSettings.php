<?php

// Don't allow web access.
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

require_once '/srv/mediawiki/PrivateSettings.php';

require_once '/var/www/html/mediawiki/config/TelepediaFunctions.php';

$wi = new TelepediaFunctions();

// Load global skins and extensions
require_once '/var/www/html/mediawiki/config/GlobalSkins.php';
require_once '/var/www/html/mediawiki/config/GlobalExtensions.php';

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
	'wgUseCdn' => [
		'default' => true
	],
	'wgDBserver' => [
		'default' => 'prod-db1.service.consul',
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
	'wgDefaultMobileSkin' => [
		'default' => 'minerva',
		'witchhatatelierwiki' => 'cosmos',
		'elatelierdesombrerosdemagowiki' => 'cosmos'
	],
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
	'wgGlobalPreferencesDB' => [
		'default' => 'centralauth'
	],

	// OATHAuth
	'wgOATHAuthDatabase' => [
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
			'staff'
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
	'wgCreateWikiGlobalWiki' => [
		'default' => 'metawiki',
	],
	'wgCreateWikiDisallowedSubdomains' => [
		'default' => [
			'auth',
			'static',
			'localhost',
			'telepedia'
		]
	],
	'wgCreateWikiEnableManageInactiveWikis' => [
		'default' => true
	],
	'wgCreateWikiDatabase' => [
		'default' => 'centralauth',
		'betatest' => 'centralauth',
	],
	'wgCreateWikiDatabaseSuffix' => [
		'default' => 'wiki',
		'betatest' => 'wikibeta',
	],
	'wgCreateWikiEmailNotifications' => [
		'default' => true,
	],
	'wgCreateWikiPurposes' => [
		'default' => [
			'Video game (specified video game) information wiki' => 'Video game (specified video game) information wiki',
			'Video game (broad genre or video game series) information wiki' => 'Video game (broad genre or video game series) information wiki',
			'Movie franchise information wiki' => 'Movie franchise information wiki',
			'TV Series information wiki' => 'TV Series information wiki',
			'Platform Administration' => 'Platform Administration wiki',
			'Book or Graphic' => 'Manga, Comic, or other graphical material',
			'None of the above' => 'None of the above',
		],
	],
	'wgCreateWikiShowBiographicalOption' => [
		'default' => true,
	],
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
	'wgCreateWikiCacheDirectory' => [
		'default' => '/srv/mediawiki/cache',
	],
	'wgCreateWikiCategories' => [
		'default' => [
			'Movies & TV' => 'm/tv',
			'Fandom' => 'fandom',
			'Fantasy' => 'fantasy',
			'Gaming' => 'gaming',
			'Literature/Writing' => 'literature',
			'Uncategorised' => 'uncategorised',
			'Platform Administration' => 'administration',
			'Reception Wiki' => 'reception'
		],
	],
	'wgCreateWikiUseCategories' => [
		'default' => true,
	],
	'wgCreateWikiSubdomain' => [
		'default' => 'telepedia.net',
	],
	'wgCreateWikiUseClosedWikis' => [
		'default' => true,
	],
	'wgCreateWikiUseCustomDomains' => [
		'default' => true,
	],
	'wgCreateWikiUseEchoNotifications' => [
		'default' => true,
	],
	'wgCreateWikiUseExperimental' => [
		'default' => false,
	],
	'wgCreateWikiUseInactiveWikis' => [
		'default' => true,
	],
	'wgCreateWikiUsePrivateWikis' => [
		'default' => true,
	],
	'wgCreateWikiUseJobQueue' => [
		'default' => true,
		'betatest' => false,
	],
	'cwClosed' => [
		'default' => false,
	],
	'cwExperimental' => [
		'default' => false,
	],
	'cwInactive' => [
		'default' => false,
	],
	'cwPrivate' => [
		'default' => false,
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

	// ManageWiki
	'wgManageWiki' => [
		'default' => [
			'core' => true,
			'extensions' => true,
			'namespaces' => true,
			'permissions' => true,
			'settings' => true
		],
	],
	'wgManageWikiExtensionsDefault' => [
		'default' => [
			'minervaneue',
			'mobilefrontend',
		],
	],
	'wgManageWikiPermissionsDisallowedRights' => [
		'default' => [
			'*' => [
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
			],
			'any' => [
				'abusefilter-hide-log',
				'abusefilter-hidden-log',
				'abusefilter-modify-global',
				'abusefilter-private',
				'abusefilter-private-log',
				'abusefilter-privatedetails',
				'abusefilter-privatedetails-log',
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
				'investigate',
				'ipinfo',
				'ipinfo-view-basic',
				'ipinfo-view-full',
				'ipinfo-view-log',
				'sfsblock-bypass',
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
				'gtag-exempt'
			],
		],
	],
	'wgManageWikiSidebarLinks' => [
		'default' => false,
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

	// WikiDiscover
	'wgWikiDiscoverUseDescriptions' => [
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
		'default' => true,
		'witchhatatelierwiki' => false,
		'latelierdessorcierswiki' => false,
		'atelierspiczastychkapeluszywiki' => false
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
	'wgFileStorageMonitorAWSBucketName' => [
		'default' => 'static.telepedia.net'
	],
	'wgFileStorageMonitorAWSRegion' => [
		'default' => 'eu-west-2'
	],
	'wgMaxUploadSize' => [
		'default' => 1024 * 1024 * 8,
	],
	'wgCopyUploadsFromSpecialUpload' => [
		'default' => true,
	],

	// Miscellaneous
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
	'wgScribuntoDefaultEngine' => [
		'default' => 'luastandalone'
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
		'default' => 2097152
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
	'wgDiscordAvatarUrl' => [
		'default' => '',
	],
	'wgDiscordIgnoreMinorEdits' => [
		'default' => false,
	],
	'wgDiscordIncludePageUrls' => [
		'default' => true,
	],
	'wgDiscordIncludeUserUrls' => [
		'default' => true,
	],
	'wgDiscordIncludeDiffSize' => [
		'default' => true,
	],
	'wgDiscordNotificationMovedArticle' => [
		'default' => true,
	],
	'wgDiscordNotificationFileUpload' => [
		'default' => true,
	],
	'wgDiscordNotificationProtectedArticle' => [
		'default' => true,
	],
	'wgDiscordNotificationAfterImportPage' => [
		'default' => true,
	],
	'wgDiscordNotificationShowSuppressed' => [
		'default' => false,
	],
	'wgDiscordNotificationWikiUrl' => [
		'default' => $wi->server . '/',
	],
	'wgDiscordNotificationCentralAuthWikiUrl' => [
		'default' => 'https://meta.telepedia.net/',
	],
	'wgDiscordNotificationBlockedUser' => [
		'default' => true,
	],
	'wgDiscordNotificationNewUser' => [
		'default' => true,
	],
	'wgDiscordNotificationIncludeAutocreatedUsers' => [
		'default' => true,
		'commonswiki' => false,
		'devwiki' => false,
		'loginwiki' => false,
		'metawiki' => false,
		'testwiki' => false,
	],
	'wgDiscordAdditionalIncomingWebhookUrls' => [
		'default' => [],
	],
	'wgDiscordDisableEmbedFooter' => [
		'default' => false,
		'puzzleswikiwiki' => true,
	],
	'wgDiscordExcludeConditions' => [
		'default' => [
			'experimental' => [
				'article_inserted' => [
					'groups' => [
						'sysop',
					],
					'permissions' => [
						'bot',
						'managewiki',
					],
				],
				'article_saved' => [
					'groups' => [
						'sysop',
					],
					'permissions' => [
						'bot',
						'managewiki',
					],
				],
			],
			'users' => [
				'OABot',
			],
		],
	],
	'wgDiscordEnableExperimentalCVTFeatures' => [
		'default' => true,
	],
	'wgDiscordExperimentalCVTMatchFilter' => [
		'default' => [ '(n[1i!*]gg[3*e]r|r[e3*]t[4@*a]rd|f[@*4]gg[0*o]t|ch[1!i*]nk)' ],
	],
	'wgDiscordExperimentalFeedLanguageCode' => [
		'default' => 'en',
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
		'default' => true,
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
	'wgGTagAnalyticsId' => [
		'citizensleeperwiki' => 'G-8GCSBGYZE0',
		'tokyovicewiki' => 'G-5NVDEZ6RPT',
		'thesecretcirclewiki' => 'G-5GG3Y717JM',
		'manorlordswiki' => 'G-56XPY67M7Z',
		'bitterrootwiki' => 'G-42WWGQEM1F',
		'loginwiki' => 'G-VH9DBN1J7N',
		'tbsatdhwiki' => 'G-NC09124SMN',
		'citadelwiki' => 'G-HDMFZBBW7R',
		'silowiki' => 'G-3B4YRRQT5P',
		'vanataswiki' => 'G-M7T5C0VVYP'
	],
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
			'captcha' => 'debug',
			'cargo' => false,
			'CentralNotice' => false,
			'cite' => false,
			'ContentHandler' => false,
			'CookieWarning' => false,
			'cookie' => false,
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
			'Flow' => 'debug',
			'formatnum' => false,
			'FSFileBackend' => false,
			'gitinfo' => false,
			'GlobalTitleFail' => false,
			'GlobalWatchlist' => false,
			'headers-sent' => false,
			'http' => 'warning',
			'HitCounters' => false,
			// Only log http errors with a 500+ code
			'HttpError' => 'error',
			// 'JobExecutor' => [ 'logstash' => 'warning' ],
			'JobQueueRedis' => 'debug',
			'localisation' => false,
			'ldap' => 'warning',
			'LinkBatch' => false,
			'Linter' => 'debug',
			'LocalFile' => 'warning',
			'localhost' => false,
			'LockManager' => 'warning',
			'logging' => false,
			'LoginNotify' => 'info',
			'ManageWiki' => 'debug',
			'MassMessage' => false,
			'Math' => 'info',
			'MatomoAnalytics' => 'debug',
			'Mime' => false,
			// debug sprews too much information + sample
			// otherwise we get 2 million+ messages within a few minutes
			'memcached' => [ 'logstash' => 'error' ],
			'message-format' => false,
			'MessageCache' => false,
			'MessageCacheError' => 'debug',
			'MirahezeMagic' => 'debug',
			'mobile' => false,
			'NewUserMessage' => false,
			'OAuth' => 'info',
			'objectcache' => 'warning',
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
			'session' => 'info',
			'session-ip' => 'info',
			'SimpleAntiSpam' => false,
			'slow-parse' => 'debug',
			'slow-parsoid' => 'info',
			'SocialProfile' => false,
			'SpamBlacklist' => false,
			'SpamBlacklistHit' => false,
			'SpamRegex' => false,
			'StopForumSpam' => false,
			'SQLBagOStuff' => false,
			'squid' => false,
			'StashEdit' => false,
			'T263581' => false,
			'texvc' => false,
			'throttler' => false,
			'thumbnail' => 'debug',
			'thumbnailaccess' => false,
			'TitleBlacklist' => false,
			'TitleBlacklist-cache' => false,
			'torblock' => 'debug',
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
			'Wikibase' => false,
			'Wikibase.NewItemIdFormatter' => false,
			'WikibaseQualityConstraints' => false,
			'xff' => false,
			'XMP' => false,
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
			]
		]
	],
	'wgGlobalPermissionsConfiguration' => [
		'default' => [
			'metawiki' => [ 'staff', 'saber' ]
		]
	]
];

// ManageWiki settings
require_once __DIR__ . '/ManageWikiExtensions.php';

$wi::$disabledExtensions = [
	'editnotify',
	'hitcounters',
	'regexfunctions',
	'wikiforum',
	'socialprofile',
	'simpleblogpage',
	'thanks'
];

$globals = TelepediaFunctions::getConfigGlobals();

require_once '/var/www/html/mediawiki/config/GlobalPermissions.php';
GlobalPermissions::modifyPermissionsAfterManageWiki($globals);

$globals['wgSharedDB'] = 'metawiki';
$globals['wgSharedTables'][] = 'user';
$globals['wgSharedTables'][] = 'user_autocreate_serial';
$globals['wgSharedTables'][] = 'actor';
$globals['wgSessionName'] = 'telepedia_session';
$globals['wgCookieDomain'] = '.telepedia.net';
$globals['wgCookieSameSite'] = null;
$globals['wgCookiePath'] = '/';

// phpcs:ignore MediaWiki.Usage.ForbiddenFunctions.extract
extract( $globals );

$wi->loadExtensions();

require_once __DIR__ . '/ManageWikiNamespaces.php';
require_once __DIR__ . '/ManageWikiSettings.php';
require_once __DIR__ . '/GlobalLogging.php';

$wgUploadDirectory = "{$IP}/images/$wgDBname";
$wgUploadPath = "{$wgScriptPath}/$wgDBname";

$wgLocalisationCacheConf['storeClass'] = LCStoreCDB::class;
$wgLocalisationCacheConf['storeDirectory'] = '/srv/mediawiki/cache/l10n';

if ( $wgRequestTimeLimit ) {
	$wgHTTPMaxTimeout = $wgHTTPMaxConnectTimeout = $wgRequestTimeLimit;
}

if ( $wi->missing ) {
	require_once '/var/www/html/mediawiki/config/MissingWiki.php';
}

$wgAdConfig['enabled'] = true; 
// Define last to avoid all dependencies
require_once '/var/www/html/mediawiki/config/GlobalSettings.php';
require_once '/var/www/html/mediawiki/config/LocalWiki.php';
require_once '/var/www/html/mediawiki/config/GlobalCache.php';

// Configure last to ensure that database name has been set properly
$wgCargoDBname = $wgDBname . 'cargo';

// During the migration away from CentralAuth, only load CentralAuth on a wiki if this flag is set to true
// As above, default to true, overwritten in LocalWiki.php so this must come before LocalWiki.php
// // All of the below depend on CentralAuth to function, therefore, we cannot load them without CA.
// if ( $tpUseCentralAuth ) {
// 	wfLoadExtensions( [
// 		'CentralAuth',
// 		'AntiSpoof',
// 		'GlobalUserPage',
// 		'Sentinel',
// 		'UserProfileV2',
// 		'OATHAuth',
// 		'GlobalBlocking',
// 		'GlobalPreferences',
// 		'Echo'
// 	] );
// }

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
