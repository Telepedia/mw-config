<?php

$wgObjectCaches['redis'] = [
	'class'                => 'RedisBagOStuff',
	'servers'              => [ '10.0.0.7:6379' ],
	'connectTimeout'       => 2,
	'persistent'           => true,
];

$wgObjectCaches['redis-session'] = [
	'class'                => 'RedisBagOStuff',
	'servers'              => [ '10.0.0.7:6379' ],
	'connectTimeout'       => 2,
	'persistent'           => true,
	'keyspace' 			   => 'session-'
];

$wgJobRunRate = 0;
$wgInvalidateCacheOnLocalSettingsChange = false;

if ( $wi->dbname == 'spicewarswiki' || $wi->dbname == 'loginwiki' ) {
	$wgMainCacheType = 'redis';
	$wgSessionCacheType = 'redis-session';
} else {
	$wgMainCacheType = CACHE_MEMCACHED;
	$wgSessionCacheType = CACHE_MEMCACHED;
}

$wgMemCachedServers = [ '10.0.0.8:11000' ];

$wgJobTypeConf['default'] = [
	'class'          => 'JobQueueRedis',
	'redisServer'    => '10.0.0.7:6379',
	'redisConfig'    => [],
	'daemonized'     => true
];

$wgRedisServers = [
	'cache'		=> [
		'host'		=> '10.0.0.7',
		'port'		=> 6379,
		'options'	=> [
			'serializer'		=> 'none',
			'readTimeout'		=> 5
		]
	],
];

