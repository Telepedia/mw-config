<?php

$wgObjectCaches['redis'] = [
	'class'                => 'RedisBagOStuff',
	'servers'              => [ '10.0.0.7:6379' ],
	'connectTimeout'       => 2,
	'persistent'           => true,
];

/**
 * ConfigCentre cache for holding data about wikis; specifically kept separate from the rest
 * of the object caches to lower latency and prevent keys from being evicted due to memory et al
 * 
 * Not to be used for anything other than determining wiki context et al and stuff associated with 
 * determining what wiki we are on
 */
$wgObjectCaches['configcentre'] = [
	'class'                => 'RedisBagOStuff',
	'servers'              => [ '10.0.0.8:6379' ],
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

$wgMainCacheType = 'redis';
$wgSessionCacheType = 'redis-session';

$wgJobTypeConf['default'] = [
	'class'          => 'JobQueueRedis',
	'redisServer'    => '10.0.0.7:6379',
	'redisConfig'    => [],
	'daemonized'     => true
];

$wgMessageCacheType = CACHE_ACCEL;