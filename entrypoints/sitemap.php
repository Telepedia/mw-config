<?php

use Telepedia\Extensions\SitemapOnTheFly\SitemapGenerator;

define( 'MW_NO_SESSION', 1 );
require_once getenv( 'MW_INSTALL_PATH' ) . '/includes/WebStart.php';

// Set the content type header for all responses
header( 'Content-Type: application/xml; charset=utf-8' );
header( 'Cache-Control: public, max-age=604800' ); // 1 week
header( 'Cache-Tag: sitemap-xml' ); // so we can purge all at the same time in CF

$generator = SitemapGenerator::newInstance();
$requestUri = $_SERVER['REQUEST_URI'] ?? '';

$pattern = '#/sitemap-NS-(\d+)(?:-part-(\d+))?\.xml#';

if ( preg_match( $pattern, $requestUri, $matches ) ) {
	// we are generating a specific sitemap page
	$namespaceId = (int)$matches[1];
	$partNumber = isset($matches[2]) ? (int)$matches[2] : 1;
	echo $generator->generateSitemapPage( $namespaceId, $partNumber );
} else {
	// we want the index
	echo $generator->generateIndex();
}