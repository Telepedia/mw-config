<?php

define( 'MW_NO_SESSION', 1 );
require_once getenv( 'MW_INSTALL_PATH' ) . '/includes/WebStart.php';

use MediaWiki\Content\TextContent;
use MediaWiki\MediaWikiServices;
use Telepedia\ConfigCentre\Utils;

header( 'Content-Type: text/plain; charset=utf-8' );
header( 'X-Telepedia-Robots: Default' );
// 1 Week heh!
header( 'Cache-Control: public, max-age=604800' );
header( 'Cache-Tag: robots-txt' ); // so we can purge all at the same time in CF

$host = $_SERVER['SERVER_NAME'] ?? parse_url( $GLOBALS['wgServer'], PHP_URL_HOST );
$langs = Utils::getLangsUnderPath( $host );

# Cover all of the language variants under a wiki in robots.txt
$disallow = function( string $path ) use ( $langs ) {
	foreach ( $langs as $lang ) {
		if ( $lang === 'en' || $lang === 'en-gb' ) {
			echo "Disallow: $path\r\n";
		} else {
			echo "Disallow: /$lang$path\r\n";
		}
	}
};

# User-Agent: *
echo "User-Agent: *\r\n";

# Throttle access to certain pages
$disallow('/api.php');
$disallow('/cors/');
$disallow('/geoip$');
$disallow('/rest_v1/');
$disallow('/wiki/Property:');
$disallow('/wiki/MediaWiki:');
$disallow('/wiki/Property%3A');
$disallow('/wiki/property:');
$disallow('/wiki/User_talk:');
$disallow('/wiki/User:');
$disallow('/wiki/Template:');
$disallow('/wiki/Template_talk:');
$disallow('/*?title=Property:');
$disallow('/*?title=User talk:');
$disallow('/*?title=Property%3A');
$disallow('/*?*&title=Property:');
$disallow('/*?*&title=Property%3A');
$disallow('/wiki/Special:');
$disallow('/wiki/Special%3A');
$disallow('/wiki/special:');
$disallow('/*?title=Special:');
$disallow('/*?title=MediaWiki:');
$disallow('/*?title=Special%3A');
$disallow('/*?*&title=Special:');
$disallow('/*?*&title=Special%3A');
$disallow('/wiki/Especial:');
$disallow('/wiki/Especial%3A');
$disallow('/wiki/especial:');
$disallow('/*?title=Especial:');
$disallow('/*?title=Especial%3A');
$disallow('/*?*&title=Especial:');
$disallow('/*?*&title=Especial%3A');
$disallow('/*?action=');
$disallow('/*?*&action=');
$disallow('/*?feed=');
$disallow('/*?*&feed=');
$disallow('/*?from=');
$disallow('/*?*&from=');
$disallow('/*?mobileaction=');
$disallow('/*?*&mobileaction=');
$disallow('/*?oldid=');
$disallow('/*?*&oldid=');
$disallow('/*?printable=');
$disallow('/*?*&printable=');
$disallow('/*?redirect=');
$disallow('/*?*&redirect=');
$disallow('/*?uselang=');
$disallow('/*?*&uselang=');
$disallow('/*?useskin=');
$disallow('/*?*&useskin=');
$disallow('/*?veaction=');
$disallow('/*?*&veaction=');
$disallow('/*?filefrom=');
$disallow('/*?*&filefrom=');
$disallow('/*?fileuntil=');
$disallow('/*?*&fileuntil=');
$disallow('/*?navbox=');
$disallow('/*?*&navbox=');
$disallow('/*?pageuntil=');
$disallow('/*?*&pageuntil=');
$disallow('/*?pagefrom=');
$disallow('/*?*&pagefrom=');
$disallow('/*?diff=');
$disallow('/*?*&diff=');
$disallow('/*?curid=');
$disallow('/*?*&curid=');
$disallow('/*?search=');
$disallow('/*?*&search=');
$disallow('/*?section=');
$disallow('/*?*&section=');

# Throttle specific bots
echo "\r\n# Throttle YandexBot\r\n";
echo "User-Agent: YandexBot\r\n";
echo "Crawl-Delay: 2.5\r\n\r\n";

echo "# Throttle BingBot\r\n";
echo "User-agent: bingbot\r\n";
echo "Crawl-delay: 2.5\r\n\r\n";

echo "# Throttle MJ12Bot\r\n";
echo "User-agent: MJ12bot\r\n";
echo "Crawl-Delay: 10\r\n\r\n";

# Block unwanted bots
$blockedBots = [
	'Bytespider',
	'PetalBot',
	'DotBot',
	'MegaIndex',
	'serpstatbot',
	'Barkrowler',
	'SeekportBot'
];

foreach ( $blockedBots as $bot ) {
	echo "# Block $bot\r\n";
	echo "User-agent: $bot\r\n";
	echo "Disallow: /\r\n\r\n";
}

# Dynamic sitemap url
echo "# Dynamic sitemap url\r\n";
foreach ( $langs as $lang ) {
	if ( $lang === 'en' || $lang === 'en-gb' ) {
		echo "Sitemap: {$wgServer}/sitemap.xml\r\n";
	} else {
		echo "Sitemap: {$wgServer}/$lang/sitemap.xml\r\n";
	}
}
echo "\r\n";


// Include custom MediaWiki Robots.txt page if exists
$wikiPageFactory = MediaWikiServices::getInstance()->getWikiPageFactory();
$titleFactory = MediaWikiServices::getInstance()->getTitleFactory();
$page = $wikiPageFactory->newFromTitle( $titleFactory->newFromText( 'Robots.txt', NS_MEDIAWIKI ) );

if ( $page->exists() ) {
	header( 'X-Telepedia-Robots: Custom' );
	echo "# -- BEGIN CUSTOM -- #\r\n\r\n";
	$content = $page->getContent();
	echo ( $content instanceof TextContent ) ? $content->getText() : '';
}
