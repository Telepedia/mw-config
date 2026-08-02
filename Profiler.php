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
		// TEMP DEBUG: for some reason forceprofile isn't working so hmm
		if ( isset( $_GET['forceprofile'] ) ) {
			error_log(
				'TelepediaProfiler: forceprofile received'
				. ' node=' . gethostname()
				. ' uri=' . ( $_SERVER['REQUEST_URI'] ?? '?' )
				. ' host=' . ( $_SERVER['SERVER_NAME'] ?? '?' )
				. ' excimer=' . ( extension_loaded( 'excimer' ) ? '1' : '0' )
			);
		}

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

		$speedscopeData = $log->getSpeedscopeData();
		$speedscopeData['profiles'][0]['name'] = $_SERVER['REQUEST_URI'];
		$collapsedStacks = $log->formatCollapsed();

		global $wgDBname;
		$wiki = $wgDBname ?? 'unknown';
		$action = $_GET['action'] ?? 'view';
		$parsed = (bool)self::$pageViewCausedParse;

		$loggedIn = false;
		try {
			if ( class_exists( 'MediaWiki\Context\RequestContext' ) ) {
				$loggedIn = RequestContext::getMain()->getUser()->isRegistered();
			}
		} catch ( Throwable $e ) {
			// ignore
		}

		$payload = [
			'wiki' => $wiki,
			'action' => $action,
			'entryPoint' => MW_ENTRY_POINT,
			'forced' => self::$isForced,
			'labels' => [
				'loggedIn' => $loggedIn,
				'parsed'   => $parsed
			],
			'stacks' => $collapsedStacks,
			'speedscope' => json_encode(
				$speedscopeData,
				JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
			),
		];

		$json = json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		$url = 'http://obs1.telepedia.internal:4644/api/ingest';
    	$parsedUrl = parse_url( $url );
       
    	$host = $parsedUrl['host'];
    	$port = $parsedUrl['port'] ?? 80;
    	$path = $parsedUrl['path'] ?? '/';

    	// here we just use a socket and fire and forget so MediaWiki/PHP isn't waiting for the response back
		// we lose some ability to check if the response was successful here, but alas
    	$fp = fsockopen( $host, $port, $errno, $errstr, 1.0 );

    	if ( $fp ) {
        	$out = "POST {$path} HTTP/1.1\r\n";
			$out .= "Host: {$host}:{$port}\r\n";
        	$out .= "Content-Type: application/json\r\n";
        	$out .= "Content-Length: " . strlen( $json ) . "\r\n";
        	$out .= "Connection: Close\r\n\r\n";
        	$out .= $json;

        	$total = strlen( $out );
        	$sent = 0;
        	while ( $sent < $total ) {
        		$written = fwrite( $fp, substr( $out, $sent ) );
        		if ( $written === false || $written === 0 ) {
        			break;
        		}
        		$sent += $written;
        	}
        	fflush( $fp );

			// not interested in reading the response back
        	fclose( $fp );
			
        	if ( $sent < $total ) {
        		error_log(
        			"TelepediaProfiler: short write to ingest ({$sent}/{$total} bytes) "
        			. 'from node=' . gethostname() . " for {$wiki} forced=" . ( self::$isForced ? '1' : '0' )
        		);
        	}
    	} else {
			error_log(
				'TelepediaProfiler: failed to connect to profiling ingest ' . $host . ':' . $port
				. ' from node=' . gethostname()
				. " (errno {$errno}: {$errstr}); profile for {$wiki} dropped, forced=" . ( self::$isForced ? '1' : '0' )
			);
    	}
	}
}
