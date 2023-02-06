<?php
/**
 * SkinTemplate class for the Example skin
 *
 * @ingroup Skins
 */
class SkinTelepedia extends SkinTemplate {
	public $skinname = 'telepedia',
		$stylename = 'Telepedia',
		$template = 'TelepediaTemplate';

	/**
	 * Add CSS via ResourceLoader
	 *
	 * @param OutputPage $out
	 */
	public function initPage( OutputPage $out ) {
		$out->addMeta( 'viewport',
			'width=device-width, initial-scale=1.0, ' .
			'user-scalable=yes, minimum-scale=0.25, maximum-scale=5.0'
		);

		$out->addModuleStyles( [
			'skins.telepedia',
            'mediawiki.skinning.interface',
            'mediawiki.skinning.content',
            'mediawiki.skinning.elements',
		] );
		$out->addModules( [
			'skins.telepedia.js'
		] );
	}

	/**
	 * @param OutputPage $out
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
        $out->addModuleStyles( array(
			'mediawiki.skinning.interface', 'mediawiki.skinning.elements', 'mediawiki.skinning.content'
			/* 'skins.foobar' is the name you used in your skin.json file */
		) );
	}
}
