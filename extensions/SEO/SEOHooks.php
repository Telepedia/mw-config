<?php
/**
 * SEO Hooks
 *
 * @package   SEO
 * @author    Collin Klopfenstein, Tim Aldridge
 * @copyright (c) 2013 Curse Inc.
 * @license   GPL-2.0-or-later
 * @link      https://gitlab.com/hydrawiki
 */

 /**
  * SEO Hooks
  */
class SEOHooks {
	/**
	 * Item Properties
	 *
	 * @var array
	 */
	static public $itemprop = [];

	/**
	 * Adds itemprop attributes to the <head>.
	 *
	 * @param object $out  Output Object
	 * @param object $skin Skin Object
	 *
	 * @return boolean	True
	 */
	public static function onBeforePageDisplay(OutputPage &$out, &$skin = false) {
		// Author
		$out->addHeadItem('itemprop-author', '<meta itemprop="author" content="Telepedia" />');

		if (self::shouldAddNoIndex($out->getRequest()->getValues())) {
			$out->setRobotPolicy("noindex");
		}

		if (isset($out->mItemprops) && is_array($out->mItemprops)) {
			foreach ($out->mItemprops as $itemprop) {
				$out->addHeadItem(
					'itemprop-' . md5($itemprop['name'] . '-' . $itemprop['content']),
					'<meta itemprop="' . $itemprop['itemprop'] . '" content="' . $itemprop['content'] . '" />'
				);
			}
		}

		// Description
		if (isset($out->mDescription)) {
			$out->addMeta('description', $out->mDescription);
		}

		// Keywords
		if (isset($out->mKeywords)) {
			$out->addMeta('keywords', implode(',', $out->mKeywords));
		}

		return true;
	}

	/**
	 * Check a blacklist of URL parameters and values to see if we should add a noindex meta tag
	 *
	 * @param array $paramsAndValues URL Parameters and Values
	 *
	 * @return boolean
	 */
	private static function shouldAddNoIndex($paramsAndValues) {
		$blockedURLParamKeys = [
			'curid', 'diff', 'from', 'group', 'mobileaction', 'oldid',
			'printable', 'profile', 'redirect', 'redlink', 'stableid'
		];

		$blockedURLParamKeyValuePairs = [
			'action' => [
				'delete', 'edit', 'history', 'info',
				'pagevalues', 'purge', 'visualeditor', 'watch'
			],
			'feed' => ['rss'],
			'limit' => ['500'],
			'title' => [
				'Category:Noindexed_pages',
				'MediaWiki:Spam-blacklist',
				'Special:CreateAccount',
				'Special:Random',
				'Special:Search',
				'Special:ExportRDF',
				'Special:UserLogin'
			],
			'veaction' => ['edit']
		];

		foreach ($paramsAndValues as $key => $value) {
			if (in_array($key, $blockedURLParamKeys)) {
				return true;
			}
			if (isset($blockedURLParamKeyValuePairs[$key]) && in_array($value, $blockedURLParamKeyValuePairs[$key])) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Modify template variables.
	 *
	 * @param object $skinTemplate SkinTemplate Object
	 * @param object $template     Initialized QuickTemplate Object
	 *
	 * @return boolean True
	 */
	public static function onSkinTemplateOutputPageBeforeExec(&$skinTemplate, &$template) {
		global $wgSitename, $wgLogo, $wgSocialSettings;

		if (isset($template->data['headelement'])) {
			$config = ConfigFactory::getDefaultInstance()->makeConfig('seo');

			$timestamp = $skinTemplate->getOutput()->getRevisionTimestamp();

			// No cached timestamp, load it from the database
			if ($timestamp === null) {
				$timestamp = Revision::getTimestampFromId($skinTemplate->getTitle(), $skinTemplate->getRevisionId());
			}

			$width = 150;
			$height = 150;
			$image = $wgLogo;
			$cacheHash = '';

			$localRepo = null;
			try {
				$localRepo = RepoGroup::singleton()->getLocalRepo();
			} catch (LogicException $e) {
				// Configuration error with $wgLocalFileRepo.
			}
			if ($localRepo !== null) {
				$file = $localRepo->newFile(Title::newFromText('File:Wiki.webp'));
				if ($file !== null && $file->exists()) {
					$cacheHash = '?version=' . md5($file->getTimestamp() . $file->getWidth() . $file->getHeight());
					$width = $file->getWidth();
					$height = $file->getHeight();
					$image = $file->getFullUrl();
				}
			}

			$searchPage = Title::newFromText('Special:Search');
			$search = $searchPage->getFullUrl(['search' => 'search_term'], false, PROTO_HTTPS);
			$search = str_replace('search_term', '{search_term}', $search);

			$profiles = '"' . implode('", "', $wgSocialSettings['profile_urls']) . '"';

			$safePageTitle = Xml::encodeJsVar($template->get('title'));

			$ldJson = '
<script type="application/ld+json">
{
    "@context": "http://schema.org/",
    "@type": "Article",
    "name": ' . $safePageTitle . ',
    "headline": ' . $safePageTitle . ',
    "image": {
        "@type": "ImageObject",
        "url": "' . $image . $cacheHash . '",
        "width": "' . $width . '",
        "height": "' . $height . '"
    },
    "author": {
        "@type": "Organization",
        "name": "' . $wgSitename . '"
    },
    "publisher": {
        "@type": "Organization",
        "name": "Telepedia",
        "logo": {
            "@type": "ImageObject",
            "url": "https://meta.telepedia.net/images/metawiki/1/14/Telepedia_Logo.svg"
            },
    },
    "potentialAction": {
        "@type": "SearchAction",
        "target": "' . $search . '",
        "query-input": "required name=search_term"
    },
    "datePublished": "' . wfTimestamp(TS_ISO_8601, $timestamp) . '",
    "dateModified": "' . wfTimestamp(TS_ISO_8601, $timestamp) . '",
    "mainEntityOfPage": "' . $skinTemplate->getTitle()->getFullUrl('', false, PROTO_HTTPS) . '"
}
</script>
';

			$template->set('headelement', $template->data['headelement'] . $ldJson);
		}
	}

	/**
	 * Adds itemprop attributes to HTML
	 *
	 * @param object $out          OutputPage
	 * @param object $parserOutput ParserOutput
	 *
	 * @return void
	 */
	public static function addTagsToHead($out, $parserOutput) {
		if (!isset($out->mItemprops)) {
			$out->mItemprops = [];
		}

		$out->mItemprops = $parserOutput->getProperty('itemprops');
	}

	/**
	 * Gets description and adds them to object
	 *
	 * @param object $parser  Parser Object
	 * @param object $frame   parser output
	 * @param string $content of content
	 *
	 * @return return message on error
	 */
	public static function parseDescriptionTag(Parser $parser, $frame, $content) {
		if (empty($content)) {
			return wfMessage('seo_description_missing')->inContentLanguage()->plain();
		}

		$out = $parser->getOutput();
		$out->setProperty('description', $content);
	}

	/**
	 * Adds description attributes to HTML
	 *
	 * @param object $out          OutputPage
	 * @param object $parserOutput ParserOutput
	 *
	 * @return void
	 */
	public static function addDescriptionToHead($out, ParserOutput $parserOutput) {
		if (!isset($out->mDescription)) {
			$out->mDescription = [];
		}
		$out->mDescription = $parserOutput->getProperty('description');
	}

	/**
	 * Gets keywords and adds them to object
	 *
	 * @param object $parser  Parser Object
	 * @param object $frame   frame
	 * @param string $content of content
	 *
	 * @return return message on error
	 */
	public static function parseKeywordsTag(Parser $parser, $frame, $content) {
		if (empty($content)) {
			return wfMessage('seo_description_missing')->inContentLanguage()->plain();
		}

		$out = $parser->getOutput();
		$out->setProperty('keywords', $content);
	}

	/**
	 * Adds keywords attributes to HTML
	 *
	 * @param object $out          OutputPage
	 * @param object $parserOutput ParserOutput
	 *
	 * @return void
	 */
	public static function addKeywordsToHead($out, $parserOutput) {
		if (!isset($out->mKeywords)) {
			$out->mKeywords = [];
		}
		$out->mKeywords = array_map('trim', explode(',', $parserOutput->getProperty('keywords')));
	}

	/**
	 * OutputPage ParserOutput Hook that puts all parseroutput page hooks into action.
	 *
	 * @param OutputPage   $out
	 * @param ParserOutput $parseroutput
	 *
	 * @return boolean
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/OutputPageParserOutput
	 */
	public static function onOutputPageParserOutput(OutputPage &$out, ParserOutput $parseroutput) {
		self::addDescriptionToHead($out, $parseroutput);
		self::addKeywordsToHead($out, $parseroutput);
		self::addTagsToHead($out, $parseroutput);
		return true;
	}
}
