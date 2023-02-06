<?php declare(strict_types=1);
/**
 * Example Test
 *
 * @package Tests\Unit
 * @author  Samuel Hilson
 * @license GPL-2.0-or-later
 */

namespace Tests\Unit;

use Closure;
use Tests\TestCase;
use Twig\Loader\FilesystemLoader;
use Twiggy\Hooks;
use Twiggy\TwiggyService;

class HooksTest extends TestCase {
	/**
	 * Create a new Hooks Instance
	 *
	 * @return Hooks
	 */
	protected function createHooks(): Hooks {
		return new Hooks();
	}

	/**
	 * Test that Hooks can be initialized
	 *
	 * @covers Hooks::_construct
	 *
	 * @return void
	 */
	public function testNewHooks(): void {
		$hooks = $this->createHooks();
		$this->assertInstanceOf(Hooks::class, $hooks);
	}

	/**
	 * Test the onMediaWikiServices
	 *
	 * @covers Hooks::onMediaWikiServices
	 *
	 * @return void
	 */
	public function testOnMediaWikiServices(): void {
		$mockMWServices = $this->getMock('MediaWiki\MediaWikiServices');

		$mockConfig = $this->getMock('GlobalVarConfig');
		$mockConfig->shouldReceive('get')->with('ExtensionDirectory')->andReturn(self::WORKING_DIR);
		$mockConfig->shouldReceive('get')->with('TwiggyAllowedPHPFunctions')->andReturn(['strtoupper']);
		$mockConfig->shouldReceive('get')->with('TwiggyBlacklistedPHPFunctions')->andReturn(['shell_exec']);

		$mockMWServices->shouldReceive('getMainConfig')->andReturn($mockConfig);

		$expected = new TwiggyService(new FilesystemLoader(self::WORKING_DIR), $mockConfig);

		$mockMWServices->shouldReceive('defineService')
			->with('TwiggyService', Closure::class)
			->andReturnUsing(function (...$args) use ($mockMWServices, $expected) {
				$result = $args[1]($mockMWServices);
				$this->assertEquals($expected, $result);
			});

		$this->createHooks()->onMediaWikiServices($mockMWServices);
	}
}
