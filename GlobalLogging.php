<?php

use MediaWiki\Logger\LoggerFactory;
use MediaWiki\Logger\Monolog\BufferHandler;
use MediaWiki\Logger\Monolog\LogstashFormatter;
use MediaWiki\Logger\Monolog\SyslogHandler;
use MediaWiki\Logger\Monolog\WikiProcessor;
use MediaWiki\Logger\MonologSpi;
use Monolog\Handler\NullHandler;
use Monolog\Handler\SamplingHandler;
use Monolog\Handler\WhatFailureGroupHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\WebProcessor;
use Psr\Log\LogLevel;

// Monolog logging configuration

$wmgMonologProcessors = [
	'wiki' => [
		'class' => WikiProcessor::class,
	],
	'psr' => [
		'class' => PsrLogMessageProcessor::class,
	],
	'web' => [
		'class' => WebProcessor::class,
	],
	'tpconfig' => [
		'factory' => static function () {
			return static function ( array $record ) {
				global $wgLBFactoryConf, $wgDBname;
				$record['extra']['shard'] = $wgLBFactoryConf['sectionsByDB'][$wgDBname] ?? 'c1';

				return $record;
			};
		}
	],
	'tpgelf' => [
		'factory' => static function () {
			return static function ( array $record ) {
				$record['extra']['application_name'] = 'mediawiki';
				$record['extra']['mediawiki_channel'] = $record['channel'] ?? '';
				// GELF 'level' is the numeric syslog severity; keep the text too
				$record['extra']['level_name'] = $record['level_name'] ?? '';

				return $record;
			};
		}
	],
];

$wmgMonologHandlers = [
	'blackhole' => [
		'class' => NullHandler::class,
	],
];

foreach ( [ 'debug', 'info', 'warning', 'error' ] as $logLevel ) {
	$wmgMonologHandlers[ "logstash-$logLevel" ] = [
		'factory' => static function () use ( $logLevel ) {
			return new \Monolog\Handler\GelfHandler(
				new \Gelf\Publisher(
					new \Gelf\Transport\UdpTransport( '100.85.182.237', 12201 )
				),
				$logLevel
			);
		},
	];
}

$wmgMonologHandlers['what-debug'] = [
	'class'     => WhatFailureGroupHandler::class,
	'formatter' => 'logstash',
	'args' => [
		static function () {
			$provider = LoggerFactory::getProvider();
			return array_map( [ $provider, 'getHandler' ], [ 'logstash-debug' ] );
		}
	],
];

// Post construction calls to make for new Logger instances
$wmgMonologLoggerCalls = [
	'setTimezone' => [ new DateTimeZone( 'UTC' ) ],
];

$wmgMonologConfig = [
	'loggers' => [
		// Template for all undefined log channels
		'@default' => [
			'handlers' => [ 'what-debug' ],
			'processors' => array_keys( $wmgMonologProcessors ),
			'calls' => $wmgMonologLoggerCalls,
		],
	],
	'processors' => $wmgMonologProcessors,
	'handlers' => $wmgMonologHandlers,
	'formatters' => [
		'logstash' => [
			'class' => LogstashFormatter::class,
			'args' => [ 'mediawiki', php_uname( 'n' ), '', '', 1 ],
		],
	],
];

// Add logging channels defined in $wmgMonologChannels
foreach ( $wmgMonologChannels as $channel => $opts ) {
	if ( $opts === false ) {
		// Log channel disabled on this wiki
		$wmgMonologConfig['loggers'][$channel] = [
			'handlers' => [ 'blackhole' ],
			'calls' => $wmgMonologLoggerCalls,
		];
		continue;
	}

	$opts = is_array( $opts ) ? $opts : [ 'logstash' => $opts ];
	$opts = array_merge(
		[
			'logstash' => 'debug',
			'buffer' => false,
			'sample' => false,
		],
		$opts
	);

	$handlers = [];

	// Configure Logstash handler
	if ( $opts['logstash'] ) {
		$level = $opts['logstash'];
		$logstashHandler = "logstash-{$level}";
		if ( isset( $wmgMonologHandlers[ $logstashHandler ] ) ) {
			$handlers[] = $logstashHandler;
		}
	}

	if ( $opts['sample'] ) {
		$sample = $opts['sample'];
		foreach ( $handlers as $idx => $handlerName ) {
			$sampledHandler = "{$handlerName}-sampled-{$sample}";
			if ( !isset( $wmgMonologConfig['handlers'][$sampledHandler] ) ) {
				// Register a handler that will sample the event stream and
				// pass events on to $handlerName for storage
				$wmgMonologConfig['handlers'][$sampledHandler] = [
					'class' => SamplingHandler::class,
					'args' => [
						static function () use ( $handlerName ) {
							return LoggerFactory::getProvider()->getHandler(
								$handlerName
							);
						},
						$sample,
					],
				];
			}
			$handlers[$idx] = $sampledHandler;
		}
	}

	if ( $opts['buffer'] ) {
		foreach ( $handlers as $idx => $handlerName ) {
			$bufferedHandler = "{$handlerName}-buffered";
			if ( !isset( $wmgMonologConfig['handlers'][$bufferedHandler] ) ) {
				// Register a handler that will buffer the event stream and
				// pass events to the nested handler after closing the request
				$wmgMonologConfig['handlers'][$bufferedHandler] = [
					'class' => BufferHandler::class,
					'args' => [
						static function () use ( $handlerName ) {
							return LoggerFactory::getProvider()->getHandler(
								$handlerName
							);
						},
					],
				];
			}
			$handlers[$idx] = $bufferedHandler;
		}
	}

	if ( $handlers ) {
		// wrap the collection of handlers in a WhatFailureGroupHandler
		// to swallow any exceptions that might leak out otherwise
		$failureGroupHandler = 'failuregroup|' . implode( '|', $handlers );
		if ( !isset( $wmgMonologConfig['handlers'][$failureGroupHandler] ) ) {
			$wmgMonologConfig['handlers'][$failureGroupHandler] = [
				'class' => WhatFailureGroupHandler::class,
				'args' => [
					static function () use ( $handlers ) {
						$provider = LoggerFactory::getProvider();
						return array_map(
							[ $provider, 'getHandler' ],
							$handlers
						);
					}
				],
			];
		}

		$wmgMonologConfig['loggers'][$channel] = [
			'handlers' => [ $failureGroupHandler ],
			'processors' => array_keys( $wmgMonologProcessors ),
			'calls' => $wmgMonologLoggerCalls,
		];

	} else {
		// No handlers configured, so use the blackhole route
		$wmgMonologConfig['loggers'][$channel] = [
			'handlers' => [ 'blackhole' ],
			'calls' => $wmgMonologLoggerCalls,
		];
	}
}

$wgMWLoggerDefaultSpi = [
	'class' => MonologSpi::class,
	'args' => [ $wmgMonologConfig ],
];

if ( $wgCommandLineMode ) {
	ini_set( 'display_startup_errors', 1 );
	ini_set( 'display_errors', 1 );

	$wgShowExceptionDetails = true;
	$wgDebugDumpSql = true;
}
