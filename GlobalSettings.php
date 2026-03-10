<?php

// set to false for now
$cwPrivate = false;

/** Some Stuff for AWS **/
$wgLocalFileRepo = [
	'class' => LocalRepo::class,
	'name' => 'local',
	'backend' => 'AmazonS3',
	'url' => $wgUploadBaseUrl ? $wgUploadBaseUrl . $wgUploadPath : $wgUploadPath,
	'scriptDirUrl' => $wgScriptPath,
	'hashLevels' => 2,
	'thumbScriptUrl' => $wgThumbnailScriptPath,
	'transformVia404' => true, // regenerate the thumbnail on 404 with ImageMagick
	'useJsonMetadata'   => true,
	'useSplitMetadata'  => true,
	'deletedHashLevels' => 3,
	'abbrvThreshold' => 160,
	'isPrivate' => $cwPrivate,
	'zones' => $cwPrivate
			? [
					'thumb' => [ 'url' => "$wgScriptPath/thumb_handler.php" ] ]
			: [],
];

$wgSharedTables[] = 'spoofuser';

/** Captcha triggers */
$wgCaptchaTriggers['edit']          = false;
$wgCaptchaTriggers['create']        = false;
$wgCaptchaTriggers['createtalk']    = false;
$wgCaptchaTriggers['addurl']        = true;
$wgCaptchaTriggers['createaccount'] = true;
$wgCaptchaTriggers['badlogin']      = true;

/**

$wgHooks['SiteNoticeAfter'][] = 'metaConditionalSiteNotice';

function metaConditionalSiteNotice( &$siteNotice, $skin ) {
	$skin->getOutput()->enableOOUI();
	$skin->getOutput()->addInlineStyle('.mw-dismissable-notice .mw-dismissable-notice-body { margin: unset; } .Message *{box-sizing:border-box}.Message{display:table;position:relative;margin:40px auto 0;width:60%;color:#fff;transition:.2s}.Message-body,.Message-icon{display:table-cell;vertical-align:middle}.Message--orange{background-color: #00032b}.Message-icon{width:60px;padding:30px;text-align:center;background-color:rgba(0,0,0,.25)}.fa-exclamation{font-size:26px}.Message-body{padding:30px 20px 30px 10px}.Message-body p{line-height:1.2;margin-top:6px}');

	$siteNotice .= <<<EOF
			<table style="width: 100%;">
			<div class="Message Message--orange">
			<div class="Message-icon">
			  <span class="fa fa-exclamation">!</span>
			</div>
			<div class="Message-body">
			  <p>ManageWiki access will be disabled on 07/11/2025 19:00 GMT as we prepare to replace ManageWiki with our custom solution. Please make any changes to wiki configuration or extensions before this time.</p>
			  <p>ConfigCentre access will be enabled before the end of the weekend.</p>
			</div>
		  </div>
			</table>
		EOF;
}
*/

/** Extension:TelepediaAds configuration */
$wgAdConfig = [
	'enabled' => true, // enabled or not? :P
];

/** Normalise action URLS into someting prettier */
$actions = [
    'view',
    'edit',
    'watch',
    'unwatch',
    'delete',
    'revert',
    'rollback',
    'protect',
    'unprotect',
    'markpatrolled',
    'render',
    'submit',
    'history',
    'purge',
    'info',
];

foreach ( $actions as $action ) {
    $wgActionPaths[$action] = "$wgArticlePath?action=$action";
}

/** Icon, and Wordmarks */
$wgLogos = [
	'1x' => $wgLogo,
];

if ( $wgIcon ) {
	$wgLogos['icon'] = $wgIcon;
}

if ( $wgWordmark ) {
	$wgLogos['wordmark'] = [
		'src' => $wgWordmark,
		'width' => $wgWordmarkWidth,
		'height' => $wgWordmarkHeight,
	];
}

/** UserProfileV2 **/
$wgFileBackends[] = [
    'class' => 'AmazonS3FileBackend',
    'name' => 'telepedia-userprofile',
    'region' => 'eu-west-2',
    'wikiId' => 'global',
    'lockManager' => 'nullLockManager',
    'connTimeout' => 10,
    'reqTimeout' => 900,
    'containerPaths' => [
        "global-upv2avatars" => "static.telepedia.net/upv2avatars",
    ]
];

/** Vector  */
$vectorVersion = $wgDefaultSkin === 'vector' ? '2' : '1';

// Don't need a global here
unset( $vectorVersion );

if ( $wi->dbName !== 'commonswiki' ) {
	/** Shared File Repo for Telepedia Commons */
	$wgForeignFileRepos[] = [
		'class' => ForeignDBViaLBRepo::class,
		'name' => 'shared-commonswiki',
		'backend' => 'AmazonS3',
		'url' => 'https://static.telepedia.net/commonswiki',
		'hashLevels' => 2,
		'thumbScriptUrl' => false,
		'transformVia404' => true,
		'hasSharedCache' => false,
		'descBaseUrl' => 'https://commons.telepedia.net/wiki/File:',
		'scriptDirUrl' => 'https://commons.telepedia.net/',
		'fetchDescription' => true,
		'descriptionCacheExpiry' => 86400 * 7,
		'wiki' => 'commonswiki',
		'initialCapital' => true,
		'zones' => [
			'public' => [
				'container' => 'local-public',
			],
			'thumb' => [
				'container' => 'local-thumb',
			],
			'temp' => [
				'container' => 'local-temp',
			],
			'deleted' => [
				'container' => 'local-deleted',
			],
		],
		'abbrvThreshold' => 160
	];
}

// linter stuff
$wgVirtualRestConfig = [
	'modules' => [
		'parsoid' => [
			'url' => '/rest.php',
			'domain' => $wgServer,
			'prefix' => $wi->dbName,
			'forwardCookies' => (bool)$cwPrivate,
			'restbaseCompat' => false,
		],
	],
	'global' => [
		'domain' => $wgCanonicalServer,
		'timeout' => 360,
		'forwardCookies' => false,
		'HTTPProxy' => null,
	],
];