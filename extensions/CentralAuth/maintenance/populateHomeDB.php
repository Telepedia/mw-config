<?php

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

use MediaWiki\Extension\CentralAuth\CentralAuthServices;
use MediaWiki\Extension\CentralAuth\User\CentralAuthUser;

class PopulateHomeDB extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->requireExtension( 'CentralAuth' );
		$this->addDescription( 'Populate the globaluser.gu_home_db field' );
		$this->setBatchSize( 500 );
	}

	public function execute() {
		$db = CentralAuthServices::getDatabaseManager()->getCentralDB( DB_REPLICA );
		$conds = [];
		$count = 0;
		do {
			$result = $db->select(
				'globaluser',
				[ 'gu_name' ],
				array_merge( $conds, [ 'gu_home_db IS NULL OR gu_home_db = ""' ] ),
				__METHOD__,
				[
					'LIMIT' => $this->mBatchSize,
					'ORDER BY' => 'gu_name',
				]
			);

			foreach ( $result as $row ) {
				$central = new CentralAuthUser( $row->gu_name, CentralAuthUser::READ_LATEST );
				$central->mStateDirty = true;
				$central->saveSettings();
				$count++;
			}
			$this->output( "$count\n" );
			CentralAuthServices::getDatabaseManager()->waitForReplication();
			if ( $result->numRows() < $this->mBatchSize ) {
				break;
			}
			// @phan-suppress-next-line PhanPossiblyUndeclaredVariable
			$conds = [ 'gu_name > ' . $db->addQuotes( $row->gu_name ) ];
		} while ( true );
		$this->output( "done.\n" );
	}
}

$maintClass = PopulateHomeDB::class;
require_once RUN_MAINTENANCE_IF_MAIN;
