<?php

$wgObjectCaches['redis'] = [
	'class'                => 'RedisBagOStuff',
	'servers'              => [ '10.0.0.7:6379' ],
	'connectTimeout'       => 2,
	'persistent'           => true,
];

/**
 * Below is a object cache that is defined for the purpose of sharing sessions across the platform. 
 * this will create sessions in the cache (at present, Redis) with the keys session:MWSession:<SESSIONID>.
 * This allows MediaWiki to read them on every wiki. Otherwise this doesn't quite work and if someone logs into Wiki A
 * and then visits Wiki B, MediaWiki will delete the session and log the user out for some reason. 
 * 
 * DO NOT use this specific object cache for anything other than sessions; this is a very hacky method
 * of doing things and potentially isn't what is supposed to happen?
 */
$wgObjectCaches['redis-session'] = [
	'class'                => 'RedisBagOStuff',
	'servers'              => [ '10.0.0.7:6379' ],
	'connectTimeout'       => 2,
	'persistent'           => true,
	'keyspace' 			   => 'globalsession'
];

$wgJobRunRate = 0;
$wgInvalidateCacheOnLocalSettingsChange = false;

if ( 
	$wi->dbname == 'spicewarswiki' || 
	$wi->dbname == 'culpritswiki' ||
	$wi->dbname == 'tbsatdhwiki' ||
	$wi->dbname == 'landmanwiki' ||
	$wi->dbname == 'supacellwiki' ||
	$wi->dbname == 'silowiki' ||
	$wi->dbname == 'citizensleeperwiki' ||
	$wi->dbname == 'commonswiki' ||
	$wi->dbname == 'latelierdessorcierswiki' ||
	$wi->dbname == 'elatelierdesombrerosdemagowiki' ||
	$wi->dbname == 'atelierspiczastychkapeluszywiki' ||
	$wi->dbname == 'witchhatatelierwiki' ||
	$wi->dbname == 'duneawakeningwiki' ||
	$wi->dbname == 'cvtwiki'
	) {
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

