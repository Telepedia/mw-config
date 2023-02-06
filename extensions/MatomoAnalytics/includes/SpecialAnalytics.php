<?php
use MediaWiki\MediaWikiServices;

class SpecialAnalytics extends SpecialPage {
	public function __construct() {
		parent::__construct( 'Analytics', 'analytics' );
	}

	/**
	 * @param string $par
	 */
	public function execute( $par ) {
		$request = $this->getRequest();
		$user = $this->getUser();

		// Check that the user is allowed to access this special page...
		if ( !$user->isAllowed( 'analytics' ) ) {
			throw new PermissionsError( 'analytics' );
		}

		$this->setHeaders();
		$this->outputHeader();

		$out = $this->getOutput();
		$out->addWikiMsg( 'matomoanalytics-header' );

		$out->addModules( [ 'ext.matomoanalytics.oouiform' ] );
		$out->addModuleStyles( [ 'ext.matomoanalytics.oouiform.styles' ] );
		$out->addModuleStyles( [ 'oojs-ui-widgets.styles' ] );

		$analyticsViewer = new MatomoAnalyticsViewer();
		$htmlForm = $analyticsViewer->getForm( $this->getContext() );

		$htmlForm->show();
	}
}
