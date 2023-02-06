<?php
/**
 * Example Test
 *
 * @package Tests\Unit
 * @author  Samuel Hilson
 * @license GPL-2.0-or-later
 */

namespace Test\Unit;

use Tests\TestCase;
use Twig\Loader\FilesystemLoader;
use Twiggy\TwiggyService;

class TwiggyServiceTest extends TestCase {
	/**
	 * Create a new TwiggyService Instance
	 *
	 * @return TwiggyService
	 */
	protected function createTwiggyService(): TwiggyService {
		$mockConfig = $this->getMock('GlobalVarConfig');
		$mockConfig->shouldReceive('get')->with('TwiggyAllowedPHPFunctions')->andReturn(['strtoupper']);
		$mockConfig->shouldReceive('get')->with('TwiggyBlacklistedPHPFunctions')->andReturn(['shell_exec']);
		return new TwiggyService(new FilesystemLoader(self::WORKING_DIR), $mockConfig);
	}

	/**
	 * Test that TwiggyService can be initialized
	 *
	 * @covers TwiggyService::_construct
	 *
	 * @return void
	 */
	public function testNewTwiggyService(): void {
		$twiggy = $this->createTwiggyService();
		$this->assertInstanceOf(TwiggyService::class, $twiggy);
	}

	/**
	 * Test that template location is added correctly
	 *
	 * @covers TwiggyService::setTemplateLocation
	 *
	 * @return void
	 */
	public function testCanAddTemplateLocation(): void {
		$twiggy = $this->createTwiggyService();
		$twiggy->setTemplateLocation('test', self::WORKING_DIR);
		$loader = $twiggy->getLoader();
		$namespaces = $loader->getNamespaces();
		$paths = $loader->getPaths('test');
		$this->assertArrayHasKey('test', array_flip($namespaces));
		$this->assertArrayHasKey(self::WORKING_DIR, array_flip($paths));
	}
}
