<?php

namespace MediaWiki\CheckUser\Tests;

use LoggedServiceOptions;
use MediaWiki\CheckUser\ComparePager;
use MediaWiki\CheckUser\CompareService;
use MediaWiki\CheckUser\DurationManager;
use MediaWiki\CheckUser\TokenQueryManager;
use MediaWiki\MediaWikiServices;
use MediaWiki\User\UserIdentity;
use MediaWiki\User\UserIdentityLookup;
use MediaWikiIntegrationTestCase;
use RequestContext;
use TestAllServiceOptionsUsed;
use Wikimedia\IPUtils;

/**
 * @group CheckUser
 * @group Database
 * @covers \MediaWiki\CheckUser\ComparePager
 */
class ComparePagerTest extends MediaWikiIntegrationTestCase {
	use TestAllServiceOptionsUsed;

	/**
	 * @dataProvider provideDoQuery
	 */
	public function testDoQuery( $targets, $excludeTargets, $expected ) {
		$services = MediaWikiServices::getInstance();

		$tokenQueryManager = $this->getMockBuilder( TokenQueryManager::class )
			->disableOriginalConstructor()
			->onlyMethods( [ 'getDataFromRequest' ] )
			->getMock();
		$tokenQueryManager->method( 'getDataFromRequest' )
			->willReturn( [
				'targets' => $targets,
				'exclude-targets' => $excludeTargets,
			] );

		$user = $this->createMock( UserIdentity::class );
		$user->method( 'getId' )
			->willReturn( 11111 );

		$user2 = $this->createMock( UserIdentity::class );
		$user2->method( 'getId' )
			->willReturn( 22222 );

		$user3 = $this->createMock( UserIdentity::class );
		$user3->method( 'getId' )
			->willReturn( 0 );

		$userIdentityLookup = $this->createMock( UserIdentityLookup::class );
		$userIdentityLookup->method( 'getUserIdentityByName' )
			->willReturnMap(
				[
					[ 'User1', 0, $user, ],
					[ 'User2', 0, $user2, ],
					[ 'InvalidUser', 0, $user3, ],
					[ '', 0, $user3, ],
					[ '1.2.3.9/120', 0, $user3, ]
				]
			);

		$compareService = new CompareService(
			new LoggedServiceOptions(
				self::$serviceOptionsAccessLog,
				CompareService::CONSTRUCTOR_OPTIONS,
				$services->getMainConfig()
			),
			$services->getDBLoadBalancer(),
			$userIdentityLookup
		);

		$durationManager = $this->createMock( DurationManager::class );

		$pager = new ComparePager(
			RequestContext::getMain(),
			$services->get( 'LinkRenderer' ),
			$tokenQueryManager,
			$durationManager,
			$compareService
		);
		$pager->doQuery();

		$this->assertSame( $expected, $pager->mResult->numRows() );
	}

	public function provideDoQuery() {
		// $targets, $excludeTargets, $expected
		return [
			'Valid and invalid targets' => [ [ 'User1', 'InvalidUser', '1.2.3.9/120' ], [], 2 ],
			'Valid and empty targets' => [ [ 'User1', '' ], [], 2 ],
			'Valid user target' => [ [ 'User2' ], [], 1 ],
			'Valid user target with excluded name' => [ [ 'User2' ], [ 'User2' ], 0 ],
			'Valid user target with excluded IP' => [ [ 'User2' ], [ '1.2.3.4' ], 0 ],
			'Valid IP target' => [ [ '1.2.3.4' ], [], 4 ],
			'Valid IP target with users excluded' => [ [ '1.2.3.4' ], [ 'User1', 'User2' ], 2 ],
			'Valid IP range target' => [ [ '1.2.3.0/24' ], [], 7 ],
		];
	}

	public function addDBData() {
		$testData = [
			[
				'cuc_user'       => 0,
				'cuc_user_text'  => '1.2.3.4',
				'cuc_type'       => RC_NEW,
				'cuc_ip'         => '1.2.3.4',
				'cuc_ip_hex'     => IPUtils::toHex( '1.2.3.4' ),
				'cuc_agent'      => 'foo user agent',
			], [
				'cuc_user'       => 0,
				'cuc_user_text'  => '1.2.3.4',
				'cuc_type'       => RC_EDIT,
				'cuc_ip'         => '1.2.3.4',
				'cuc_ip_hex'     => IPUtils::toHex( '1.2.3.4' ),
				'cuc_agent'      => 'foo user agent',
			], [
				'cuc_user'       => 0,
				'cuc_user_text'  => '1.2.3.4',
				'cuc_type'       => RC_EDIT,
				'cuc_ip'         => '1.2.3.4',
				'cuc_ip_hex'     => IPUtils::toHex( '1.2.3.4' ),
				'cuc_agent'      => 'bar user agent',
			], [
				'cuc_user'       => 0,
				'cuc_user_text'  => '1.2.3.5',
				'cuc_type'       => RC_EDIT,
				'cuc_ip'         => '1.2.3.5',
				'cuc_ip_hex'     => IPUtils::toHex( '1.2.3.5' ),
				'cuc_agent'      => 'bar user agent',
			], [
				'cuc_user'       => 0,
				'cuc_user_text'  => '1.2.3.5',
				'cuc_type'       => RC_EDIT,
				'cuc_ip'         => '1.2.3.5',
				'cuc_ip_hex'     => IPUtils::toHex( '1.2.3.5' ),
				'cuc_agent'      => 'foo user agent',
			], [
				'cuc_user'       => 11111,
				'cuc_user_text'  => 'User1',
				'cuc_type'       => RC_EDIT,
				'cuc_ip'         => '1.2.3.4',
				'cuc_ip_hex'     => IPUtils::toHex( '1.2.3.4' ),
				'cuc_agent'      => 'foo user agent',
			], [
				'cuc_user'       => 22222,
				'cuc_user_text'  => 'User2',
				'cuc_type'       => RC_EDIT,
				'cuc_ip'         => '1.2.3.4',
				'cuc_ip_hex'     => IPUtils::toHex( '1.2.3.4' ),
				'cuc_agent'      => 'foo user agent',
			], [
				'cuc_user'       => 11111,
				'cuc_user_text'  => 'User1',
				'cuc_type'       => RC_EDIT,
				'cuc_ip'         => '1.2.3.5',
				'cuc_ip_hex'     => IPUtils::toHex( '1.2.3.5' ),
				'cuc_agent'      => 'foo user agent',
			],
		];

		$commonData = [
			'cuc_namespace'  => NS_MAIN,
			'cuc_title'      => 'Foo_Page',
			'cuc_minor'      => 0,
			'cuc_page_id'    => 1,
			'cuc_timestamp'  => '',
			'cuc_xff'        => 0,
			'cuc_xff_hex'    => null,
			'cuc_actiontext' => '',
			'cuc_comment'    => '',
			'cuc_this_oldid' => 0,
			'cuc_last_oldid' => 0,
		];

		foreach ( $testData as $row ) {
			$this->db->insert( 'cu_changes', $row + $commonData );
		}

		$this->tablesUsed[] = 'cu_changes';
	}
}
