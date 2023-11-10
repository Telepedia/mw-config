<?php
// SocialProfile
if ( $wi->isExtensionActive( 'SocialProfile' ) ) {
	require_once "/var/www/html/mediawiki/extensions/SocialProfile/SocialProfile.php";
}

/* $wgHooks['SiteNoticeAfter'][] = 'metaConditionalSiteNotice';

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
			  <p>Telepedia will be conducting file maintenance on October 26, 2023, at 5pm (BST); file uploads will be disabled at this time. It is antiicpated that uploads will remain disabled for at least two (2) hours.</p>
			</div>
		  </div>
			</table>
		EOF;
}
*/

// Closed Wikis
if ( $cwClosed ) {
	$wgRevokePermissions = [
		'*' => [
			'block' => true,
			'createaccount' => true,
			'delete' => true,
			'edit' => true,
			'protect' => true,
			'import' => true,
			'upload' => true,
			'undelete' => true,
		],
		'user' => [
			'block' => true,
			'createaccount' => true,
			'delete' => true,
			'edit' => true,
			'protect' => true,
			'import' => true,
			'upload' => true,
			'undelete' => true,
		],
	];
	$wgHooks['SiteNoticeAfter'][] = 'wfConditionalSiteNotice';

	function wfConditionalSiteNotice( &$siteNotice, $skin ) {
		$skin->getOutput()->enableOOUI();
		$skin->getOutput()->addInlineStyle('.mw-dismissable-notice .mw-dismissable-notice-body { margin: unset; } .Message *{box-sizing:border-box}.Message{display:table;position:relative;margin:40px auto 0;width:60%;color:#fff;transition:.2s}.Message-body,.Message-icon{display:table-cell;vertical-align:middle}.Message--orange{background-color:#f39c12}.Message-icon{width:60px;padding:30px;text-align:center;background-color:rgba(0,0,0,.25)}.fa-exclamation{font-size:26px}.Message-body{padding:30px 20px 30px 10px}.Message-body p{line-height:1.2;margin-top:6px}');

		$siteNotice .= <<<EOF
			<table style="width: 100%;">
			<div class="Message Message--orange">
			<div class="Message-icon">
			  <span class="fa fa-exclamation">!</span>
			</div>
			<div class="Message-body">
			  <p>This wiki has been closed, either as a result of inactivity, or actions that violate the Platform's Terms of Use and Conditions of Service.</p>
			  <p>If you wish to reopen this wiki, please create a ticket on Phorge, including the reasons for reopening. You may also wish to join our Discord Sever to enquire about this.</p>
			</div>
		  </div>
			</table>
		EOF;
    }
}

// $wgLogos
$wgLogos = [
	'1x' => $wgLogo,
];

$wgAdConfig = [
    'enabled' => true, // enabled or not? :P
    'adsense-client' => '5974970328084579', // provider number w/o the "pub-" part
    'namespaces' => [ NS_MAIN, NS_TALK ], // array of enabled namespaces
    'right-column' => true, // do we want a skyscraper ad column (Monobook)?
    'toolbox-button' => true, // or a "button" ad below the toolbox (Monobook)?
    'monaco-sidebar' => true, // 200x200 sidebar ad in the sidebar on Monaco skin
    'monaco-leaderboard' => true, // leaderboard (728x90) ad in the footer on Monaco skin
    'truglass-leaderboard' => true, // leaderboard ad for Truglass skin
    'vector-skyscraper-ad-slot' => '8501358891',
    'telepedia-skyscraper-ad-slot' => '8501358891',
    'vector-leaderboard-ad-slot' => '8133693231',
    'telepedia-leaderboard-ad-slot' => '8133693231',
    'timeless-leaderboard-ad-slot' => '8133693231',
    'minerva-leaderboard-ad-slot' => '8133693231',
    'minerva-skyscraper-ad-slot' => '8133693231',
    'mode' => 'responsive',
    'vector-right-side' => true,
    'right-side' => true,
    'vector-right-side-ad-slot' => '8133693231',
    'cosmos-leaderboard' => true
];

$wgApexLogo = [
	'1x' => $wgLogos['1x'],
	'2x' => $wgLogos['1x'],
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

// Vector
$vectorVersion = $wgDefaultSkin === 'vector' ? '2' : '1';

$wgMFStripResponsiveImages = false;

// Don't need a global here
unset( $vectorVersion );

// Licensing variables
