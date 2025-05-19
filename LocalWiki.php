<?php

switch ( $wi->dbname ) {

	case 'metawiki':
		wfLoadExtension( 'WikiDiscover' );
		break;

	case 'witchhatatelierwiki':
		$wgAdConfig['enabled'] = false;
		$wgDefaultMobileSkin = "cosmos";
		break;

	case 'latelierdessorcierswiki':
		$wgDefaultMobileSkin = "cosmos";
		$wgAdConfig['enabled'] = false;
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

	case 'elatelierdesombrerosdemagowiki':
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
		$wgAdConfig['enabled'] = false;
		break;

	case 'atelierspiczastychkapeluszywiki':
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
		$wgAdConfig['enabled'] = false;
		break;

	case 'duneawakeningwiki':
		wfLoadExtension( 'Cargo' );
		$wgAdConfig['enabled'] = false;
		break;

    case 'testingoawiki':
		$wgShowExceptionDetails = true;
		wfLoadExtension( 'Cloudflare' );
		$wgDebugLogGroups['Cloudflare'] = "/var/log/mediawiki/debug-testingoa.log";
		break;

	case 'landmanwiki':
		$wgAdConfig['enabled'] = false;
		break;	

	case 'loginwiki':
		$wgAdConfig['enabled'] = false;
		$tpUseCentralAuth = false;
		break;	
	
	case 'harvesterwiki':
		$wgDefaultUserOptions['vector-theme'] = 'night';
		$wgVectorNightMode['beta'] = true;
		$wgVectorNightMode['logged_out'] = true;
		$wgVectorNightMode['logged_in'] = true;
		break;
}
