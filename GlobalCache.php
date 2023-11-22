<?php

$wgObjectCaches['redis'] = [
    'class'                => 'RedisBagOStuff',
    'servers'              => [ '10.0.0.7:6379' ],
    'connectTimeout'       => 2,
    'persistent'           => true,
];

$wgInvalidateCacheOnLocalSettingsChange = false;

$wgMainCacheType = 'redis';
$wgSessionCacheType = 'redis';