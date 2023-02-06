<?php declare(strict_types=1);
/**
 * Test Base
 *
 * @package Tests
 * @author  Samuel Hilson
 * @license GPL-2.0-or-later
 */

namespace Tests;

use Mockery;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class TestCase extends BaseTestCase {
	/**
	 * Container for build path
	 *
	 * @var string
	 */
	public const WORKING_DIR = 'build';

	/**
	 * Container for Filesystem
	 *
	 * @var Filesystem
	 */
	protected $filesystem;

	/**
	 * Setup the test case.
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();
		$this->filesystem = new Filesystem();
		if (!is_dir(self::WORKING_DIR)) {
			$this->filesystem->mkdir(self::WORKING_DIR);
		}
	}

	/**
	 * Tear down the test case.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();
		if ($container = Mockery::getContainer()) {
			$this->addToAssertionCount($container->mockery_getExpectationCount());
		}
		Mockery::close();

		if (is_dir(self::WORKING_DIR)) {
			$this->filesystem->remove(self::WORKING_DIR);
		}
	}

	/**
	 * Create Mock Class
	 *
	 * @param string $class
	 *
	 * @return mixed
	 */
	public function getMock($class) {
		return Mockery::mock($class);
	}
}
