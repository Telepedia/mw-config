<?php

use Telepedia\Extensions\SitemapOnTheFly\GlobalSitemapGenerator;

define( 'MW_NO_SESSION', 1 );
require_once getenv( 'MW_INSTALL_PATH' ) . '/includes/WebStart.php';

header( 'Content-Type: application/xml; charset=utf-8' );
header( 'Cache-Control: public, max-age=86400' ); // 1 day, so new wikis are picked up reasonably quickly
header( 'Cache-Tag: sitemap-xml' ); // so we can purge all at the same time in CF

$generator = GlobalSitemapGenerator::newInstance();
$requestUri = $_SERVER['REQUEST_URI'] ?? '';

// only needed if we ever exceed 50k wikis, at which point we'll need an index for the parts
// but for now we're not worrying about it
$pattern = '#-part-(\d+)\.xml#';
$partNumber = preg_match( $pattern, $requestUri, $matches ) ? (int)$matches[1] : 1;

echo $generator->generateSitemap( $partNumber );
