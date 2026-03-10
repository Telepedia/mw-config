<?php

wfLoadExtensions( [
	'AWS',
	'Cloudflare',
	'ConfirmEdit',
	'ConfirmEdit/Turnstile',
	'GTag',
	'TelepediaAds',
	'ProtectSite',
	'Echo',
	'CheckUser',
	'Nuke',
	'Scribunto',
	'AbuseFilter',
	'TelepediaMagic',
	'IPInfo',
	'SpamBlacklist',
	'StopForumSpam',
	'Interwiki',
	'OAuth',
	'GlobalPermissions',
	'GlobalBlocking',
	'UserProfileV2',
	'UAM',
	'Sentinel',
	'TelepediaCore',
	'ConfigCentre',
	'ToastNotifications',
	'SitemapOnTheFly',
	'MissingWiki',
	'CreateWiki',
	'GlobalPreferences',
	'RequestToBeForgotten'
] );

wfLoadExtension( 'Parsoid', "$IP/vendor/wikimedia/parsoid/extension.json" );