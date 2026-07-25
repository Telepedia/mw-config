<?php

use MediaWiki\MediaWikiServices;
use Telepedia\Extensions\TelepediaCore\Artemis\JobExecutor;

if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
	http_response_code( 405 );
	header( 'Allow: POST' );
	die( "Request must use POST.\n" );
}

$input = file_get_contents( "php://input" );
if ( $input === '' ) {
	// Allow for ease of testing
	http_response_code( 422 );
	die( 'Request contained no job data.' );
}

$job = json_decode( $input, true );
if ( !isset( $job['wiki'] ) ) {
	throw new Exception( 'Job must contain the database parameter. Received: ' . json_encode( $job ) );
}

define( 'MEDIAWIKI_JOB_RUNNER', 1 );
define( 'MW_DB', $job['wiki'] );

require_once __DIR__ . '/includes/WebStart.php';

// Never render PHP errors into the response body. The conductor treats any
// 200 as success and discards the body, so a warning/notice leaking into the
// output must not be able to mask a real failure. Errors still go to the log.
error_reporting( E_ERROR );
ini_set( 'display_errors', '0' );
ini_set( 'log_errors', '1' );

$chronologyProtector = MediaWikiServices::getInstance()->getChronologyProtector();
$chronologyProtector->setEnabled( false );

try {
	$mediawiki = new MediaWiki();
	$executor = new JobExecutor();
	// execute the job
	$response = $executor->execute( $job );
	if ( $response['status'] === true ) {
		http_response_code( 200 );
	} else {
		if ( $response['readonly'] ) {
			// if we detect that the DB is in read-only mode, we delay the return of the
			// response by at most 45 seconds in order to minimize the number of requests
			// made by change-prop; this will keep the request rate at a reasonably low
			// level without causing request time outs
			sleep( rand( 40, 45 ) );
			// END TODO
			header( 'X-Readonly: true' );
		}
		http_response_code( 500 );
	}
	$mediawiki->restInPeace();
} catch ( Exception $e ) {
	http_response_code( 500 );
	MWExceptionHandler::rollbackPrimaryChangesAndLog( $e );
}
