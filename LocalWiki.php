<?php

switch ( $wi->dbname ) {

    case 'tokyovicewiki':
        wfLoadExtension('AbuseFilter');
        break;

    case 'timebanditswiki':
        wfLoadSkin( 'Telepedia' );
        break;

    case 'citadelwiki':
        wfLoadExtension( 'SEO' );
        break;

    case 'spicewarswiki':
        wfLoadExtension( 'SearchStats' );
        $wgAdConfig = [
            'enabled' => true, // enabled or not? :P
            'adsense-client' => '5974970328084579', // provider number w/o the "pub-" part
            'namespaces' => [ NS_MAIN, NS_TALK ], // array of enabled namespaces
            'right-column' => true, // do we want a skyscraper ad column (Monobook)?
            'toolbox-button' => true, // or a "button" ad below the toolbox (Monobook)?
            'monaco-sidebar' => true, // 200x200 sidebar ad in the sidebar on Monaco skin
            'monaco-leaderboard' => true, // leaderboard (728x90) ad in the footer on Monaco skin
            'truglass-leaderboard' => true, // leaderboard ad for Truglass skin
            'vector-skyscraper-ad-slot' => '8501358891',
            'westeros-skyscraper-ad-slot' => '8501358891',
            'vector-leaderboard-ad-slot' => '8133693231',
            'westeros-leaderboard-ad-slot' => '8133693231',
            'minerva-leaderboard-ad-slot' => '8133693231',
            'minerva-skyscraper-ad-slot' => '8133693231',
            'mode' => 'responsive'
        ];
        wfLoadExtension( 'SEO' );
        break;

    case 'gamecheatswiki':
        wfLoadExtension( 'SearchStats' );
        break;

    case 'metawiki':
        //wfLoadExtension('Sudo');
        wfLoadExtension('CheckUser');
        wfLoadExtension( 'WikiDiscover' );
        wfLoadExtension( 'AbuseFilter' );
        wfLoadExtension('OAuth');
        $wgGroupPermissions['autoconfirmed']['mwoauthproposeconsumer'] = true;
        $wgGroupPermissions['autoconfirmed']['mwoauthupdateownconsumer'] = true;
        break;

    case 'bitterrootwiki':
        wfLoadSkin( 'Westeros' );
        break;

    case '1899wiki':
        wfLoadExtension( 'MultiBoilerplate' );
        wfLoadExtension( 'Video' );
        break;

    case 'theperipheralwiki':
        wfLoadExtension( 'MultiBoilerplate' );
        wfLoadExtension( 'Video' );
        wfLoadExtension( 'Popups' );
        $wgPopupsRestGatewayEndpoint = '/api.php';
        wfLoadSkin( 'Cosmos' );
        break;

    case 'testingoawiki':
        $wgAdConfig = [
            'enabled' => false, // enabled or not? :P
            'adsense-client' => '5974970328084579', // provider number w/o the "pub-" part
            'namespaces' => [ NS_MAIN, NS_TALK ], // array of enabled namespaces
            'right-column' => true, // do we want a skyscraper ad column (Monobook)?
            'toolbox-button' => true, // or a "button" ad below the toolbox (Monobook)?
            'monaco-sidebar' => true, // 200x200 sidebar ad in the sidebar on Monaco skin
            'monaco-leaderboard' => true, // leaderboard (728x90) ad in the footer on Monaco skin
            'truglass-leaderboard' => true, // leaderboard ad for Truglass skin
            'mode' => 'responsive',
            'vector-skyscraper-ad-slot' => '7336095813'
        ];
        require_once( "$IP/extensions/SocialProfile/UserStats/EditCount.php" ); // Necessary edit counter
// The actual user level definitions -- key is simple: 'Level name' => points needed
        $wgUserLevels = [
            'Recruit' => 0,
            'Apprentice' => 1200,
            'Private' => 1750,
            'Corporal' => 2500,
            'Sergeant' => 5000,
            'Gunnery Sergeant' => 10000,
            'Lieutenant' => 20000,
            'Captain' => 35000,
            'Major' => 50000,
            'Lieutenant Commander' => 75000,
            'Commander' => 100000,
            'Colonel' => 150000,
            'Brigadier' => 250000,
            'Brigadier General' => 350000,
            'Major General' => 500000,
            'Lieutenant General' => 650000,
            'General' => 800000,
            'General of the Army' => 1000000,
        ];
        $wgNamespacesWithSubpages = [
            NS_TALK => true,
            NS_USER => true,
            NS_USER_TALK => true,
            NS_PROJECT => true,
            NS_PROJECT_TALK => true,
            NS_FILE_TALK => true,
            NS_MEDIAWIKI => true,
            NS_MEDIAWIKI_TALK => true,
            NS_TEMPLATE => true,
            NS_TEMPLATE_TALK => true,
            NS_HELP => true,
            NS_HELP_TALK => true,
            NS_CATEGORY_TALK => true
        ];

        $wgShowExceptionDetails = true;
        $wgUploadBaseUrl = 'https://static.whiki.online';
        $wgHooks['SiteNoticeAfter'][] = function(&$siteNotice, $skin) {
            $siteNotice .= <<< EOT
      <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5974970328084579"
      crossorigin="anonymous"></script>
   <ins class="adsbygoogle"
      style="display:block"
      data-ad-format="autorelaxed"
      data-ad-client="ca-pub-5974970328084579"
      data-ad-slot="1483071706"></ins>
   <script>
      (adsbygoogle = window.adsbygoogle || []).push({});
   </script>
   EOT;
            return true;
        };
        $wgImageMagickConvertCommand = '/usr/local/bin/convert';
        break;

    case 'ixionwiki':
        wfLoadExtension('WikiSEO');
        $wgGoogleSiteVerificationKey = "LOjLlkDRQhEBtYbsaes-1JtZ4FS1FlO0CrJetZE0GaE";
        break;

    case 'tbsatdhwiki':
        wfLoadExtension( 'MultiBoilerplate' );
        wfLoadExtension( 'AutoCreateCategoryPages' );
        break;

    case 'citizensleeperwiki':
        wfLoadExtension( 'ShoutWikiAds' );
        $wgAdConfig = [
            'enabled' => true, // enabled or not? :P
            'adsense-client' => '5974970328084579', // provider number w/o the "pub-" part
            'namespaces' => [ NS_MAIN, NS_TALK ], // array of enabled namespaces
            'right-column' => true, // do we want a skyscraper ad column (Monobook)?
            'toolbox-button' => true, // or a "button" ad below the toolbox (Monobook)?
            'monaco-sidebar' => true, // 200x200 sidebar ad in the sidebar on Monaco skin
            'monaco-leaderboard' => true, // leaderboard (728x90) ad in the footer on Monaco skin
            'truglass-leaderboard' => true, // leaderboard ad for Truglass skin
            'vector-skyscraper-ad-slot' => '8501358891',
            'westeros-skyscraper-ad-slot' => '8501358891',
            'vector-leaderboard-ad-slot' => '8133693231',
            'westeros-leaderboard-ad-slot' => '8133693231',
            'minerva-leaderboard-ad-slot' => '8133693231',
            'minerva-skyscraper-ad-slot' => '8133693231',
            'mode' => 'responsive'
        ];
        break;

}
