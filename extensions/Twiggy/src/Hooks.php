<?php declare(strict_types=1);
/**
 * Twiggy
 * Hooks for Twiggy
 *
 * @package Twiggy
 * @author  Samuel Hilson
 * @license GPL-2.0-or-later
 */

namespace Twiggy;

use MediaWiki\MediaWikiServices;
use Twig\Loader\FilesystemLoader;

class Hooks {
	/**
	 * Handle Loading the TwiggyService
	 *
	 * @param MediaWikiServices $services
	 *
	 * @return void
	 */
	public static function onMediaWikiServices(MediaWikiServices $services): void {
		$services->defineService('TwiggyService', function (MediaWikiServices $services) {
			$mainConfig = $services->getMainConfig();
			return new TwiggyService(
				new FilesystemLoader($mainConfig->get('ExtensionDirectory')),
				$mainConfig
			);
		});
	}
}
