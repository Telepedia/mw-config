<?php

// Don't allow web access.
if ( !defined( 'MEDIAWIKI' ) ) {
    die( 'Not an entry point.' );
}

require_once '/var/www/html/mediawiki/PrivateSettings.php';

/**
 * When using ?forceprofile=1, a profile can be found as an HTML comment
 * Disabled on production hosts because it seems to be causing performance issues (how ironic)
 */
$forceprofile = $_GET['forceprofile'] ?? 0;
if ( ( $forceprofile == 1 || PHP_SAPI === 'cli' ) && extension_loaded( 'tideways_xhprof' ) ) {
    $xhprofFlags = TIDEWAYS_XHPROF_FLAGS_CPU | TIDEWAYS_XHPROF_FLAGS_MEMORY | TIDEWAYS_XHPROF_FLAGS_NO_BUILTINS;
    tideways_xhprof_enable( $xhprofFlags );

    $wgProfiler = [
        'class' => ProfilerXhprof::class,
        'flags' => $xhprofFlags,
        'running' => true,
        'output' => 'text',
    ];
    $wgHTTPTimeout = 60;
}

require_once '/var/www/html/mediawiki/TelepediaFunctions.php';

$wi = new TelepediaFunctions();

// Load global skins and extensions
require_once '/var/www/html/mediawiki/GlobalSkins.php';
require_once '/var/www/html/mediawiki/GlobalExtensions.php';

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
        'default' => '10.0.0.6',
    ],
    'wgCdnServersNoPurge' => [
        'default' => [
            "23.235.32.0/20",
            "43.249.72.0/22",
            "103.244.50.0/24",
            "103.245.222.0/23",
            "103.245.224.0/24",
            "104.156.80.0/20",
            "151.101.0.0/16",
            "157.52.64.0/18",
            "172.111.64.0/18",
            "185.31.16.0/22",
            "199.27.72.0/21",
            "199.232.0.0/16",
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
    // CentralAuth
    'wgCentralAuthAutoCreateWikis' => [
        'default' => [
            'loginwiki',
        ],
        'betaheze' => [
            'loginwiki',
        ],
    ],
    'wgCentralAuthAutoNew' => [
        'default' => true,
    ],
    'wgCentralAuthAutoMigrate' => [
        'default' => true,
    ],
    'wgCentralAuthAutoMigrateNonGlobalAccounts' => [
        'default' => true,
    ],
    'wgCentralAuthCookies' => [
        'default' => true,
    ],
    'wgCentralAuthCookiePrefix' => [
        'default' => 'centralauth_',
        'betaheze' => 'betacentralauth_',
    ],
    'wgCentralAuthCookieDomain' => [
        'default' => 'telepedia.net',
    ],
    'wgCentralAuthCreateOnView' => [
        'default' => true,
    ],
    'wgCentralAuthDatabase' => [
        'default' => 'centralauth',
        'betaheze' => 'centralauth',
    ],
    'wgCentralAuthDryRun' => [
        'default' => false,
    ],
    'wgCentralAuthEnableGlobalRenameRequest' => [
        'default' => false,
        'metawiki' => true,
    ],
    'wgCentralAuthLoginWiki' => [
        'default' => 'loginwiki',
        'betaheze' => 'loginwiki',
    ],
    'wgUseSameSiteLegacyCookies' => [
        'default' => true,
    ],
    'wgCentralAuthSessionCacheType' => [
        'default' => CACHE_MEMCACHED,
    ],
    'wgCentralAuthPreventUnattached' => [
        'default' => true,
    ],
    'wgCentralAuthSilentLogin' => [
        'default' => true,
    ],
    'wgCentralAuthHiddenLevelMigrationStage' => [
        // Remove with 1.39
        'default' => SCHEMA_COMPAT_READ_NEW | SCHEMA_COMPAT_WRITE_NEW,
    ],

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
    'wgPasswordPolicy' => [
        'default' => [
            'policies' => [
                'default' => [
                    'MinimalPasswordLength' => [ 'value' => 6, 'suggestChangeOnLogin' => true ],
                    'PasswordCannotBeSubstringInUsername' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
                    'PasswordCannotMatchDefaults' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
                    'MaximalPasswordLength' => [ 'value' => 4096, 'suggestChangeOnLogin' => true ],
                    'PasswordNotInCommonList' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
                ],
                'bot' => [
                    'MinimalPasswordLength' => [ 'value' => 8, 'suggestChangeOnLogin' => true ],
                    'MinimumPasswordLengthToLogin' => [ 'value' => 6, 'suggestChangeOnLogin' => true ],
                    'PasswordCannotBeSubstringInUsername' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
                    'PasswordCannotMatchDefaults' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
                    'MaximalPasswordLength' => [ 'value' => 4096, 'suggestChangeOnLogin' => true ],
                    'PasswordNotInCommonList' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
                ],
                'sysop' => [
                    'MinimalPasswordLength' => [ 'value' => 8, 'suggestChangeOnLogin' => true ],
                    'MinimumPasswordLengthToLogin' => [ 'value' => 6, 'suggestChangeOnLogin' => true ],
                    'PasswordCannotBeSubstringInUsername' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
                    'PasswordCannotMatchDefaults' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
                    'MaximalPasswordLength' => [ 'value' => 4096, 'suggestChangeOnLogin' => true ],
                    'PasswordNotInCommonList' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
                ],
                'bureaucrat' => [
                    'MinimalPasswordLength' => [ 'value' => 8, 'suggestChangeOnLogin' => true ],
                    'MinimumPasswordLengthToLogin' => [ 'value' => 6, 'suggestChangeOnLogin' => true ],
                    'PasswordCannotBeSubstringInUsername' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
                    'PasswordCannotMatchDefaults' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
                    'MaximalPasswordLength' => [ 'value' => 4096, 'suggestChangeOnLogin' => true ],
                    'PasswordNotInCommonList' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
                ],
            ],
            'checks' => [
                'MinimalPasswordLength' => 'PasswordPolicyChecks::checkMinimalPasswordLength',
                'MinimumPasswordLengthToLogin' => 'PasswordPolicyChecks::checkMinimumPasswordLengthToLogin',
                'PasswordCannotBeSubstringInUsername' => 'PasswordPolicyChecks::checkPasswordCannotBeSubstringInUsername',
                'PasswordCannotMatchDefaults' => 'PasswordPolicyChecks::checkPasswordCannotMatchDefaults',
                'MaximalPasswordLength' => 'PasswordPolicyChecks::checkMaximalPasswordLength',
                'PasswordNotInCommonList' => 'PasswordPolicyChecks::checkPasswordNotInCommonList',
            ],
        ],
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
            'oversight',
            'steward',
        ],
        '+metawiki' => [
            'globalsysop',
        ],
    ],
    //VisualEditor
    'wgVisualEditorEnableWikitext' => [
        'default' => true
    ],
    'wgVisualEditorEnableDiffPage' => [
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
            'None of the above' => 'None of the above',
        ],
    ],
    'wgCreateWikiShowBiographicalOption' => [
        'default' => true,
    ],
    'wgCreateWikiSQLfiles' => [
        'default' => [
            "/var/www/html/mediawiki/maintenance/tables-generated.sql",
            "$IP/extensions/AbuseFilter/db_patches/mysql/tables-generated.sql",
            "$IP/extensions/CheckUser/cu_log.sql",
            "$IP/extensions/CheckUser/cu_changes.sql",
            "$IP/extensions/Echo/echo.sql",
            "$IP/extensions/GlobalBlocking/sql/mysql/tables-generated-global_block_whitelist.sql"
        ],
    ],
    'wgCreateWikiCacheDirectory' => [
        'default' => '/var/www/html/mediawiki/cache',
    ],
    'wgCreateWikiCategories' => [
        'default' => [
            'Movies & TV' => 'm/tv',
            'Fandom' => 'fandom',
            'Fantasy' => 'fantasy',
            'Gaming' => 'gaming',
            'Literature/Writing' => 'literature',
            'Uncategorised' => 'uncategorised',
            'Platform Administration' => 'administration'
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
    'wgLogo' => [
        'default' => 'https://static.telepedia.net/metawiki/9/9f/Telepedia_icon.svg',
    ],
    'wgAllowDisplayTitle' => [
        'default' => true,
    ],
    'wgRestrictDisplayTitle' => [
        'default' => false,
    ],

    // GlobalBlocks
    'wgApplyGlobalBlocks' => [
        'default' => true,
        'metawiki' => false,
    ],
    'wgGlobalBlockingDatabase' => [
        'default' => 'globalblocks',
    ],

    'wgAutoCreateTempUser' => [
        'default' => [
            'enabled' => false,
            'actions' => [
                'edit',
            ],
            'genPattern' => '*Unregistered $1',
            'matchPattern' => '*$1',
            'serialProvider' => [
                'type' => 'local',
            ],
            'serialMapping' => [
                'type' => 'plain-numeric',
            ],
        ],
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
                'interwiki',
                'investigate',
                'ipinfo',
                'ipinfo-view-basic',
                'ipinfo-view-full',
                'ipinfo-view-log',
                'managewiki-restricted',
                'managewiki-editdefault',
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
            ],
        ],
    ],
    'wgManageWikiSidebarLinks' => [
        'default' => false,
    ],

    'wgImplicitGroups' => [
        'default' => [
            '*',
            'user',
            'autoconfirmed'
        ],
    ],
    'wgForceHTTPS' => [
        'default' => true
    ],
    'wgWikiDiscoverUseDescriptions' => [
        'default' => true
    ],

    // Licenses
    'wgRightsUrl' => [
        'default' => 'https://creativecommons.org/licenses/by-nc-sa/3.0/',
        'backroomsdewiki' => 'https://creativecommons.org/licenses/by/3.0/',
        'backroomsdewiki' => 'Creative Commons Attribution 3.0 International (CC BY 3.0)',
    ],
    'wgRightsText' => [
        'default' => 'Creative Commons Attribution-NonCommercial 3.0 Unported (CC BY-NC-SA 3.0)',
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
    ],
    'wgMaxImageArea' => [
        'default' => '1.25e7',
    ],
    'wgMaxAnimatedGifArea' => [
        'default' => '1.25e7',
    ],

    // ImageMagick
    'wgUseImageMagick' => [
        'default' => true,
    ],
    'wgSVGConverter' => [
        'default' => 'Inkscape',
    ],

    // Image Limits
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

    // Miscellaneous
    'wgLogSpamBlacklistHits' => [
        'default' => true
    ],
    'wgBlacklistSettings' => [
		'default' => [
			'spam' => [
				'files' => [
					'https://meta.telepedia.net/index.php?title=MediaWiki:Global_spam_blacklist&action=raw&sb_ver=1',
				],
			],
	    ],
    ],
    'wgNativeImageLazyLoading' => [
        'default' => true
    ],
    'wgScribuntoDefaultEngine' => [
        'default' => 'luastandalone'
    ],
    'wgAllowUserCss' => [
        'default' => true
    ],
    'wgFileStorageMonitorAWSBucketName' => [
        'default' => 'static.telepedia.net'
    ],
    'wgFileStorageMonitorAWSRegion' => [
        'default' => 'eu-west-2'
    ],
    'wgNoFollowDomainExceptions' => [
        'default' => [
            'miraheze.org',
            'mediawiki.org',
            'en.wikipedia.org'
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


    //OAuth
    'wgMWOAuthCentralWiki' => [
        'default' => 'metawiki',
    ],
    'wgOAuth2GrantExpirationInterval' => [
        'default' => 'PT4H',
    ],
    'wgMWOAuthSharedUserSource' => [
        'default' => 'CentralAuth',
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
    //compres revisions
    'wgCompressRevisions' => [
        'default' => true
    ],
    // Server
    'wgDisableOutputCompression' => [
        'default' => true,
    ],
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
    'wgInternalServer' => [
        'default' => 'http://telepedia.net',
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
    'wgMaxTocLevel' => [
        'default' => 999,
    ],

    // CreateWiki Defined Special Variables
    'cwClosed' => [
        'default' => true,
    ],
    'cwExperimental' => [
        'default' => false,
    ],
    'cwInactive' => [
        'default' => true,
    ],
    'cwPrivate' => [
        'default' => false,
    ],
    'wgMaxUploadSize' => [
        'default' => 1024 * 1024 * 8,
    ],
    'wgEnableMetaDescriptionFunctions' => [
        'default' => true,
    ],
    'wgCopyUploadsFromSpecialUpload' => [
        'default' => true,
    ],

    // Extensions
    'wgCookieWarningEnabled' => [
        'default' => 'true',
    ],
    'wgCookieWarningMoreUrl' => [
        'default' => 'https://telepedia.net/privacy-policy/',
    ],
    'wgAllowSiteCSSOnRestrictedPages' => [
        'default' => true,
    ],
    'wgMFSiteStylesRenderBlocking' => [
        'default' => true,
    ],
    'wgMFCollapseSectionsByDefaul' => [
        'default' => true,
    ],

    //echo
    'wgEchoSharedTrackingDB' => [
        'default' => 'metawiki',
    ],
    'wgEchoCrossWikiNotifications' => [
        'default' => true,
    ],
    'wgEchoUseJobQueue' => [
        'default' => true,
    ],

    // DiscordNotifications
    'wgDiscordAvatarUrl' => [
        'default' => '',
    ],
    'wgDiscordIncomingWebhookUrl' => [
        'default' => 'https://discord.com/api/webhooks/1022160482582409289/NLxDmId0agvsqmPuv2FqAPpK63GH7L03_11MWR8h2-UFYwW9SZSQsSARu9JgUYhJPSEL',
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
    'wgDiscordNotificationBlockedUser' => [
        'default' => true,
    ],
    'wgDiscordNotificationNewUser' => [
        'default' => true,
    ],
    'wgDiscordAdditionalIncomingWebhookUrls' => [
        'default' => [],
    ],
    'wgDiscordExcludedPermission' => [
        'default' => 'sysop',
    ],
    'wgDismissableSiteNoticeForAnons' => [
        'default' => true,
    ],

    // CheckUser
    'wgCheckUserCAMultiLock' => [
        'default' => [
            'centralDB' => 'metawiki',
            'groups' => [ 'staff' ]
        ],
    ],
    'wgCheckUserEnableSpecialInvestigate' => [
        'default' => true
    ],
    // InterWiki
    'wgInterwikiCentralDB' => [
        'default' => 'metawiki',
    ],
    'wgEnableScaryTranscluding' => [
        'default' => false,
    ],
    'wgCreateWikiGlobalWiki' => [
        'default' => 'metawiki',
    ],
    'wgEnableEmail' => [
        'default' => true,
    ],
    'wgEnableUserEmail' => [
        'default' => true,
    ],
    'wgEmergencyContact' => [
        'default' => 'community@telepedia.net',
    ],
    'wgPasswordSender' => [
        'default' => 'community@telepedia.net',
    ],
    'wgAllowHTMLEmail' => [
        'default' => true,
    ],

    //comments
    'wgCommentsInRecentChanges' => [
        'default' => true,
    ],
    'wgCommentsSortDescending' => [
        'default' => false,
    ],

    // SocialProfile
    'wgUserBoard' => [
        'default' => false,
    ],
    'wgUserProfileThresholds' => [
        'default' => [
            'edits' => 0,
        ],
    ],
    'wgUserProfileDisplay' => [
        'default' => [
            'activity' => false,
            'articles' => true,
            'avatar' => true,
            'awards' => true,
            'board' => false,
            'custom' => true,
            'foes' => false,
            'friends' => false,
            'games' => false,
            'gifts' => true,
            'interests' => true,
            'personal' => true,
            'profile' => true,
            'stats' => true,
            'userboxes' => true,
        ],
    ],
    'wgUserStatsPointValues' => [
        'default' => [
            'edit' => 50,
            'vote' => 0,
            'comment' => 0,
            'comment_plus' => 0,
            'comment_ignored' => 0,
            'opinions_created' => 0,
            'opinions_pub' => 0,
            'referral_complete' => 0,
            'friend' => 0,
            'foe' => 0,
            'gift_rec' => 0,
            'gift_sent' => 0,
            'points_winner_weekly' => 0,
            'points_winner_monthly' => 0,
            'user_image' => 1000,
            'poll_vote' => 0,
            'quiz_points' => 0,
            'quiz_created' => 0,
        ],
    ],
    'wgFriendingEnabled' => [
        'default' => true,
    ],
    'wgUserPageChoice' => [
        'default' => true,
    ],
    'wgGTagAnalyticsId' => [
        'newqualitipediawiki' => 'G-P0J517S6SL',
        'spicewarswiki' => 'G-GLR0YHSP94',
        'citizensleeperwiki' => 'G-8GCSBGYZE0',
        '1899wiki' => 'G-FTQ0KG9MZW',
        'tokyovicewiki' => 'G-5NVDEZ6RPT',
        'thewatchfuleyewiki' => 'G-XZMTLND418',
        'timebanditswiki' => 'G-4RN1M135C8',
        'gamecheatswiki' => 'G-JW49FC27NT',
        'thesecretcirclewiki' => 'G-5GG3Y717JM',
        'duneawakeningwiki' => 'G-3HZV2YGQK4',
        'onlymurdersinthebuildingwiki' => 'G-85VR8WFBTH',
        'metawiki' => 'G-L7LB52KTJF',
        'manorlordswiki' => 'G-56XPY67M7Z',
        'ixionwiki' => 'G-GG3K8LT8VG',
        'bitterrootwiki' => 'G-42WWGQEM1F',
        'loginwiki' => 'G-VH9DBN1J7N',
        'bloodyhellhotelwiki' => 'G-JLHB9TSR8B',
        'theperipheralwiki' => 'G-8GTXF640GN',
        'tbsatdhwiki' => 'G-NC09124SMN',
        'citadelwiki' => 'G-HDMFZBBW7R',
        'classwiki' => 'G-44Q4VX2RLJ',
        'itendswithuswiki' => 'G-0EET2H54SQ',
        'thepowerwiki' => 'G-LFSZHHE86K',
        'thenightagentwiki' => 'G-M3WPMW75DT',
        'silowiki' => 'G-3B4YRRQT5P',
        'ikissedaboywiki' => 'G-MSFPFLHC9N',
        'moviepediawiki' => 'G-8QM65GYXPT',
        'realvillainswiki' => 'G-840Y4S2YCB',
        'mylittleponywiki' => 'G-42XR4WT5SF',
        'vanataswiki' => 'G-M7T5C0VVYP'
    ],

    'wgThanksConfirmationRequired' => [
        'default' => false,
    ],

    'wgWikiSeoEnableSocialImages' => [
        'default' => true
    ],

    'wgEnableCanonicalServerLink' => [
        'default' => true
    ],
];

// ManageWiki settings
require_once __DIR__ . '/ManageWikiExtensions.php';
$wi::$disabledExtensions = [
    'editnotify',
    'hitcounters',
    'regexfunctions',
    'wikiforum',
];

$globals = TelepediaFunctions::getConfigGlobals();

// phpcs:ignore MediaWiki.Usage.ForbiddenFunctions.extract
extract( $globals );

$wi->loadExtensions();
require_once __DIR__ . '/ManageWikiNamespaces.php';
require_once __DIR__ . '/ManageWikiSettings.php';

$wgUploadDirectory = "{$IP}/images/$wgDBname";
$wgUploadPath = "{$wgScriptPath}/$wgDBname";

$wgLocalisationCacheConf['storeClass'] = LCStoreCDB::class;
$wgLocalisationCacheConf['storeDirectory'] = '/var/www/html/mediawiki/cache/l10n';

if ( $wgRequestTimeLimit ) {
    $wgHTTPMaxTimeout = $wgHTTPMaxConnectTimeout = $wgRequestTimeLimit;
}

if ( $wi->missing ) {
    require_once '/var/www/html/mediawiki/MissingWiki.php';
}

// Define last to avoid all dependencies
require_once '/var/www/html/mediawiki/GlobalSettings.php';
require_once '/var/www/html/mediawiki/LocalWiki.php';
require_once '/var/www/html/mediawiki/GlobalCache.php';

// Define last - Extension message files for loading extensions
if (
    file_exists( __DIR__ . '/ExtensionMessageFiles.php' ) &&
    !defined( 'MW_NO_EXTENSION_MESSAGES' )
) {
    require_once __DIR__ . '/ExtensionMessageFiles.php';
}

// Don't need a global here
unset( $wi );
