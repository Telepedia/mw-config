fo<?php
/**
 * Class file for management of Whiki Platform advertisements served through Google Adsense
 *
 * @file
 * @ingroup Extensions
 * @author Luke Rigby
 * @license MIT
 */

use MediaWiki\MediaWikiServices;
 class whikiAds {
    public static function isEnabled() {
        $config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'whikiAds' );
        $wgAdsenabled = $config->get( 'WhikiAdsConfig' );
        global $wgAdsenabled;
        if ( $wgAdsenabled = false ) {
            return;
        }
    }
     public static function onOutputPageBeforeHTML( OutputPage $out, &$text ) {
        $html .= <<<BEFOREP
            <script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
            <script>
              window.googletag = window.googletag || {cmd: []};
              googletag.cmd.push(function() {
                googletag.defineSlot('/22705328868/testing1202', [728, 90], 'div-gpt-ad-1672465783553-0').addService(googletag.pubads());
                googletag.pubads().enableSingleRequest();
                googletag.enableServices();
              });
            </script>
        BEFOREP;
         $out->addHTML( $html );
     }

    public static function onSiteNoticeAfter( &$siteNotice, $skin ) {
        $siteNotice .= <<< EOT
        <!-- Ezoic -  sitenotice_leaderboard - under_page_title -->
        <div id="ezoic-pub-ad-placeholder-162"> </div>
        <!-- End Ezoic -  sitenotice_leaderboard - under_page_title -->
    EOT;
        return true;
     }


  /*   public static function onSkinAfterContent( &$afterCode, $skin ) {
        global $wgRequest, $wgTitle;
        $config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'whikiAds' );
        $wgAdsenabled = $config->get( 'WhikiAdsConfig' );
        // lets bail if the user is registered
        if ( $skin->getUser()->isRegistered() || $wgAdsenabled != true ) {
            return;
        }

        // lets also bail if the page is the login page, or recent changes; we don't want advertisemnts here

        if ( $wgTitle->isSpecial( 'Userlogin' ) || $wgTitle->isSpecial( 'Recentchanges' ) ) {
            return;
    }

    $afterCode .= <<< EOT
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5974970328084579"
     crossorigin="anonymous"></script>
    <!-- Before Content Ad -->
    <ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-5974970328084579"
     data-ad-slot="7381277286"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
    <script>
     (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
    EOT;
            return true;
     }

    /*
    */
}
