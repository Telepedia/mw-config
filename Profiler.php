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
			$excimer->setPeriod( 60 );
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
	}
}