<?php declare(strict_types = 1);
/**
 * Twig Factory
 *
 * @package Twiggy
 * @author  Samuel Hilson
 * @license GPL-2.0-or-later
 */

namespace Twiggy;

use GlobalVarConfig;
use Twig\Environment;
use Twig\Loader\LoaderInterface;
use Umpirsky\Twig\Extension\PhpFunctionExtension;

class TwiggyService extends Environment {
	/**
	 * Constructor
	 *
	 * @param LoaderInterface $loader
	 * @param array           $mwConfig
	 * @param array           $options
	 */
	public function __construct(LoaderInterface $loader, $mwConfig, $options = []) {
		parent::__construct($loader, $options);
		$this->registerPhpFunctionExtension($mwConfig);
	}

	/**
	 * Add a Template Location
	 *
	 * @param string $namespace
	 * @param string $directory
	 *
	 * @return void
	 */
	public function setTemplateLocation(string $namespace, string $directory): void {
		$this->getLoader()->addPath($directory, $namespace);
	}

	/**
	 * Handle setup for PhpFunctionExtension
	 *
	 * @param GlobalVarConfig $mwConfig
	 *
	 * @return void
	 */
	private function registerPhpFunctionExtension($mwConfig) {
		$functionList = array_unique($mwConfig->get('TwiggyAllowedPHPFunctions'));
		$functionList = array_filter($functionList, function ($func) use ($mwConfig) {
			return !in_array($func, $mwConfig->get('TwiggyBlacklistedPHPFunctions'));
		});

		$pfExtension = new PhpFunctionExtension($functionList);
		$this->addExtension($pfExtension);
	}
}
