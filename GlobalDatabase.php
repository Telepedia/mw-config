<?php

use Telepedia\Extensions\TelepediaCore\LBFactoryMulti_TP;

$wgDBtype = 'mysql';
$wgLBFactoryConf = [
	'class' => LBFactoryMulti_TP::class,
	'secret' => $wgSecretKey,
	'sectionLoads' => [
		'DEFAULT' => [
			'db1' => 0,
		],
		'c1' => [
			'db1' => 0,
		]
	],
	'serverTemplate' => [
		'user' => $wgDBuser,
		'password' => $wgDBpassword,
		'type' => 'mysql',
		'flags' => DBO_DEFAULT | ( MW_ENTRY_POINT === 'cli' ? DBO_DEBUG : 0 ),
		'variables' => [
			// https://mariadb.com/docs/reference/mdb/system-variables/innodb_lock_wait_timeout
			'innodb_lock_wait_timeout' => 120,
		],
	],
	'hostsByName' => [
		'db1' => 'db1.telepedia.internal',
	],
	'externalLoads' => [
		'specials' => [
			'db1' => 0
		]
	],
	'readOnlyBySection' => [],
];

// MUST be defined before LoadWiki::class executes
$wgVirtualDomainsMapping['virtual-configcentre'] = [ 'cluster' => 'specials', 'db' => 'centralauth'];