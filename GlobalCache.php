<?php

$wgObjectCaches['redis'] = [
	'class'                => 'RedisBagOStuff',
	'servers'              => [ 'redis-cache:6379' ],
	'connectTimeout'       => 2,
	'persistent'           => true,
];

/**
* Mostly kept for backwards compatability, alias for redis object cache; not durable and not written to disk
*/
$wgObjectCaches['configcentre'] = [
	'class'                => 'RedisBagOStuff',
	'servers'              => [ 'redis-cache:6379' ],
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
 * of doing things and potentially isn't what is supposed to happen? This is durable in any case and is written to disk to prevent session loss
 */
$wgObjectCaches['redis-session'] = [
	'class'                => 'RedisBagOStuff',
	'servers'              => [ 'redis-sessions:6379' ],
	'connectTimeout'       => 2,
	'persistent'           => true,
	'keyspace' 			   => 'globalsession'
];

/**
* Alias for $wgObjectCache['redis]'
*/
$wgObjectCaches['parsercache-redis'] = [
	'class'          => 'RedisBagOStuff',
	'servers'        => [ 'redis-cache:6379' ],
	'connectTimeout' => 2,
	'persistent'     => true,
];

/**
 * Parser cache is backed by the database; reads go to redis first, and then fallback
 * to the database before being regenerated from scratch
 */
$wgObjectCaches['mysql-multiwrite'] = [
	'class'  => 'MultiWriteBagOStuff',
	'caches' => [
		0 => [ 'factory' => [ 'ObjectCache', 'getInstance' ], 'args' => [ 'parsercache-redis' ] ],
		1 => [
			'class'   => 'SqlBagOStuff',
			'servers' => [ 'pc1' => [
				'type'     => 'mysql',
				'host'     => 'mariadb',
				'dbname'   => 'parsercache',
				'user'     => $wgDBuser,
				'password' => $wgDBpassword,
				'flags'    => 0,
			] ],
			'purgePeriod' => 0,
			'tableName'   => 'pc',
			'shards'      => 256,
			'reportDupes' => false,
		],
	],
	'replication' => 'async',
	'reportDupes' => false,
];

$wgJobRunRate = 0;
$wgInvalidateCacheOnLocalSettingsChange = false;

$wgMainCacheType = 'redis';
$wgSessionCacheType = 'redis-session';
$wgParserCacheType = 'mysql-multiwrite';

/**
 * @TODO: remove this when everything goes through Artemis
 */
$wgJobTypeConf['default'] = [
	'class'          => 'JobQueueRedis',
	'redisServer'    => 'redis-cache:6379',
	'redisConfig'    => [],
	'daemonized'     => true
];

$wgMessageCacheType = CACHE_ACCEL;
