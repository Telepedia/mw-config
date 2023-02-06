<?php

use MediaWiki\MediaWikiServices;

/**
 * DI service wiring for the WhikiMagic extension.
 */
return [
	'WhikiMagic.LogEmailManager' => static function ( MediaWikiServices $services ): WhikiMagicLogEmailManager {
		return new WhikiMagicLogEmailManager(
			$services->getConfigFactory()->makeConfig( 'whikimagic' )
		);
	},
];
