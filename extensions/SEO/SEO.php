<?php
/**
 * Curse Inc.
 * SEO
 * SEO Mediawiki Settings
 *
 * @package   SEO
 * @author    Collin Klopfenstein, Tim Aldridge
 * @copyright (c) 2013 Curse Inc.
 * @license   GPL-2.0-or-later
 * @link      https://gitlab.com/hydrawiki
**/

if (function_exists('wfLoadExtension')) {
	wfLoadExtension('SEO');
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['SEO'] = __DIR__ . '/i18n';
	/*wfWarn(
		'Deprecated PHP entry point used for SEO extension. Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);*/
	return;
} else {
	die('This version of the SEO extension requires MediaWiki 1.25+');
}
