<?php
/**
 * Hooks for SearchStats extension
 *
 * @file
 * @ingroup Extensions
 */

class SearchStatsHooks {

	/**
	 * Add search_query table
	 * @param DatabaseUpdater $updater
	 * @return true
	 */
	public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater ) {
		$updater->addExtensionTable( 'search_query',
			__DIR__ . '/table.sql' );
		return true;
	}

	/**
	 * A search was done that went directly to a page
	 * https://www.mediawiki.org/wiki/Manual:Hooks/SpecialSearchCreateLink
	 * @param Title $t
	 * @param array &$params
	 */
	public static function onSpecialSearchCreateLink( $t, &$params ) {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->insert( 'search_query',
			[ [ 'sq_query' => $t ] ],
			__METHOD__,
			[]
			);
	}

	/**
	 * A search was done that found no match
	 * https://www.mediawiki.org/wiki/Manual:Hooks/SpecialSearchNogomatch
	 * @param Title &$title
	 */
	public static function onSpecialSearchNogomatch( &$title ) {
	/*
		$dbw = wfGetDB( DB_MASTER );
		$dbw->insert( 'search_query',
			array(array('sq_query' => $title)),
			__METHOD__,
			array()
			);
			*/
	}

}
