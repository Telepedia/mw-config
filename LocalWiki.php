<?php

switch ( $wi->dbname ) {

	case 'metawiki':
		wfLoadExtension( 'CheckUser' );
		wfLoadExtension( 'WikiDiscover' );
		wfLoadExtension( 'AbuseFilter' );
		break;

	case 'theperipheralwiki':
		wfLoadExtension( 'MultiBoilerplate' );
		wfLoadExtension( 'Video' );
		wfLoadExtension( 'Popups' );
		$wgPopupsRestGatewayEndpoint = '/api.php';
		break;

	case 'witchhatatelierwiki':
		wfLoadExtension( 'MigrateUserAccount' );
		$wgMUARemoteWikiContentPath = 'https://witch-hat-atelier.fandom.com/wiki/';
		$wgMUARemoteWikiAPI = 'https://witch-hat-atelier.fandom.com/api.php';
		$wgDefaultMobileSkin = "cosmos";
		break;

	case 'latelierdessorcierswiki':
		wfLoadExtension( 'MigrateUserAccount' );
		$wgMUARemoteWikiContentPath = 'https://latelier-des-sorciers.fandom.com/fr/wiki/';
		$wgMUARemoteWikiAPI = 'https://latelier-des-sorciers.fandom.com/fr/api.php';
		$wgDefaultMobileSkin = "cosmos";

		$wgForeignFileRepos[] = [
			'class' => ForeignDBViaLBRepo::class,
			'name' => 'shared-witchhatatelierwiki',
			'backend' => 'AmazonS3',
			'url' => 'https://static.telepedia.net/witchhatatelierwiki',
			'hashLevels' => 2,
			'thumbScriptUrl' => false,
			'transformVia404' => true,
			'hasSharedCache' => true,
			'descBaseUrl' => 'https://witchhatatelier.telepedia.net/wiki/File:',
			'scriptDirUrl' => 'https://witchhatatelier.telepedia.net/',
			'fetchDescription' => true,
			'descriptionCacheExpiry' => 86400 * 7,
			'wiki' => 'witchhatatelierwiki',
			'initialCapital' => true,
			'zones' => [
				'public' => [
					'container' => 'local-public',
				],
				'thumb' => [
					'container' => 'local-thumb',
				],
				'temp' => [
					'container' => 'local-temp',
				],
				'deleted' => [
					'container' => 'local-deleted',
				],
			],
			'abbrvThreshold' => 160
		];

		break;

	case 'backroomsdewiki':
		wfLoadExtension( 'MigrateUserAccount' );
		$wgMUARemoteWikiContentPath = 'https://backrooms.fandom.com/de/wiki/';
		$wgMUARemoteWikiAPI = 'https://backrooms.fandom.com/de/api.php';
		break;

	case 'duneawakeningwiki':
		wfLoadExtension( 'Cargo' );
		break;
}
