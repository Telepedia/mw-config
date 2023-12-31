<?php

$wgObjectCaches['redis'] = [
	'class'                => 'RedisBagOStuff',
	'servers'              => [ '10.0.0.7:6379' ],
	'connectTimeout'       => 2,
	'persistent'           => true,
];

$wgInvalidateCacheOnLocalSettingsChange = false;

$wgMainCacheType = CACHE_MEMCACHED;
$wgSessionCacheType = CACHE_MEMCACHED;

$wgMemCachedServers = [ '10.0.0.8:11000' ];

$wgJobTypeConf['default'] = [
	'class'          => 'JobQueueRedis',
	'redisServer'    => '10.0.0.7:6379',
	'redisConfig'    => [],
	'daemonized'     => true
 ];
