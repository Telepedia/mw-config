<?php

use MediaWiki\Context\RequestContext;

class TelepediaProfiler {

	private static $excimer = null;

	/**
	 * Is this profiling forced
	 * @var bool
	 */
	private static $isForced = false;

	/**
	 * Did the page view cause a parse
	 * @var bool
	 */
	private static $pageViewCausedParse = false;

	/**
	 * Initialize the profiler.
	 * @param float $sampleRate Percentage of standard traffic to profile (e.g., 0.01 for 1%)
	 */
	public static function setup( float $sampleRate = 0.01 ): void {
		if ( !extension_loaded( 'excimer' ) ) {
			return;
		}

		self::$isForced = isset( $_GET['forceprofile'] );

		// Only profile a percentage of standard traffic so we don't overwhelm the network
		// if we aren't included in the sample AND the request wasn't forced, return
		if ( !self::$isForced && mt_rand( 1, (int)( 1 / $sampleRate ) ) > 1 ) {
			return; 
		}

		self::$excimer = new ExcimerProfiler();
		self::$excimer->setPeriod( 0.001 );
		self::$excimer->setEventType( EXCIMER_REAL );
		self::$excimer->start();

		// Track if this specific page view triggered the parser
		global $wgHooks;
		$wgHooks['ParserBeforeInternalParse'][] = static function ( $parser ) {
			if ( str_starts_with( $parser->getOptions()->getRenderReason() ?? '', 'page_view' ) ) {
				self::$pageViewCausedParse = true;
			}
		};

		// We register a shutdown function, which waits for PHP to start shutting down,
		// and then appends our ACTUAL push function to the very end of the queue.
		// This guarantees we capture all MediaWiki DeferredUpdates.
		register_shutdown_function( static function () {
			register_shutdown_function( [ self::class, 'finishAndPush' ] );
		} );
	}

	/**
	 * Stops the profiler, formats the data, and pushes to Pyroscope if it was a sample, or to disk if forced.
	 * @TODO: we need to do something else with those that are forced to get them into R2 or something
	 */
	public static function finishAndPush(): void {
		if ( !self::$excimer ) {
			return;
		}

		self::$excimer->stop();
		$log = self::$excimer->getLog();

		if ( self::$isForced ) {
			$data = $log->getSpeedscopeData();
			$data['profiles'][0]['name'] = $_SERVER['REQUEST_URI'] ?? 'Unknown';
			
			$logDir = '/var/log/mediawiki-profiling';
			if ( !is_dir( $logDir ) ) {
				@mkdir( $logDir, 0755, true );
			}
			
			$filename = $logDir . '/speedscope-' . ( new DateTime )->format( 'Y-m-d_His_v' ) . '-' . MW_ENTRY_POINT . '.json';
			@file_put_contents( $filename, json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
		}

		global $wgDBname;
		$wiki = $wgDBname ?? 'unknown';
		$action = $_GET['action'] ?? 'view';
		$parsed = self::$pageViewCausedParse ? 'true' : 'false';
		$forcedStr = self::$isForced ? 'true' : 'false';
		
		$loggedIn = 'false';
		try {
			if ( class_exists( 'MediaWiki\Context\RequestContext' ) ) {
				$loggedIn = RequestContext::getMain()->getUser()->isRegistered() ? 'yes' : 'no';
			}
		} catch ( Throwable $e ) {
			// do nothing if we errored
		}

		$appName = sprintf(
			'mediawiki.cpu{wiki="%s", action="%s", logged_in="%s", parsed="%s", forced="%s"}',
			$wiki, $action, $loggedIn, $parsed, $forcedStr
		);

		$collapsedData = $log->formatCollapsed();

		$url = "http://obs1.telepedia.internal:4040/ingest?name=" . urlencode( $appName ) . "&format=collapsed";

		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $collapsedData );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, [ 'Content-Type: text/plain' ] );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		
		curl_setopt( $ch, CURLOPT_TIMEOUT, 1 ); 
		
		@curl_exec( $ch );
		curl_close( $ch );
	}
}