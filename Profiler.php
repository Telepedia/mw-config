<?php

class TelepediaProfiler {

	/**
	 * Setup the profiler and write the log for viewing in Speedscope
	 * @link https://www.mediawiki.org/wiki/Excimer
	 * @return void
	 */
	public static function setup(): void {
		if (
			extension_loaded( 'excimer' ) &&
			isset( $_GET[ 'forceprofile' ] )
		) {
			$excimer = new ExcimerProfiler();
			$excimer->setPeriod( 0.001 );
			$excimer->setEventType( EXCIMER_REAL );
			$excimer->start();
			register_shutdown_function( function () use ( $excimer ) {
				$excimer->stop();
				$data = $excimer->getLog()->getSpeedscopeData();
				$data['profiles'][0]['name'] = $_SERVER['REQUEST_URI'];
				file_put_contents( '/var/log/mediawiki-profiling/speedscope-' . ( new DateTime )->format( 'Y-m-d_His_v' ) . '-' . MW_ENTRY_POINT . '.json',
					json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
			} );
		}

		self::registerSlowLog();
	}

	/**
	 * Temporary for PLAT-74; identify which pages are slow to load so that we can profile them.
	 * StatsD sends the load time to Prometheus, but gives no context on which page, or wiki, it was
	 *
	 * We could just put thsi in self::init(), but this allows us to enable and disable it on whim
	 * @return void
	 */
	private static function registerSlowLog(): void {
		register_shutdown_function( function () {
			$start = $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime( true );
			$duration = microtime( true ) - $start;

			// threshold of 300ms for now
			if ( $duration > 0.3 ) {
				global $wgDBname;
				// gather some useful information
				$url = $_SERVER['REQUEST_URI'] ?? 'Unknown';
				$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

				$logEntry = sprintf(
					"[%s] Duration: %.4fs | Wiki: %s | IP: %s | URL: %s\n",
					date( 'Y-m-d H:i:s' ),
					$duration,
					$wgDBname ?? 'unknown',
					$clientIP,
					$url
				);

				// don't care about errors
				@file_put_contents( '/srv/mediawiki/cache/slow_requests.log', $logEntry, FILE_APPEND );
			}
		} );
	}
}