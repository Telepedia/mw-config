<?php

switch ( $wi->dbname ) {

    case 'spicewarswiki':
        wfLoadExtension( 'SemanticMediaWiki' );
        enableSemantics( 'spicewars.telepedia.net' );
        $smwgShowFactbox = SMW_FACTBOX_NONEMPTY;
        break;

    case 'metawiki':
        wfLoadExtension('CheckUser');
        wfLoadExtension( 'WikiDiscover' );
        wfLoadExtension( 'AbuseFilter' );
        break;

    case 'theperipheralwiki':
        wfLoadExtension( 'MultiBoilerplate' );
        wfLoadExtension( 'Video' );
        wfLoadExtension( 'Popups' );
        $wgPopupsRestGatewayEndpoint = '/api.php';
        break;

    case 'testingoawiki':
        wfLoadExtension( 'NativeSvgHandler' );
        wfLoadExtension( 'Karma' );
        break;

    case 'witchhatatelierwiki':
        wfLoadExtension( 'MigrateUserAccount' );
        $wgMUARemoteWikiContentPath = 'https://witch-hat-atelier.fandom.com/wiki/';
        $wgMUARemoteWikiAPI = 'https://witch-hat-atelier.fandom.com/api.php';
	      $wgDefaultMobileSkin = "cosmos";
	      break;

    case 'latelierdessorcierswiki':
        wfLoadExtension( 'MigrateUserAccount' );
        $wgMUARemoteWikiContentPath = 'https://latelier-des-sorciers.fandom.com/fr/wiki/';
        $wgMUARemoteWikiAPI = 'https://latelier-des-sorciers.fandom.com/fr/api.php';
    	  $wgDefaultMobileSkin = "cosmos";
    	  break;

    case 'backroomsdewiki':
        wfLoadExtension( 'MigrateUserAccount' );
        $wgMUARemoteWikiContentPath = 'https://backrooms.fandom.com/de/wiki/';
        $wgMUARemoteWikiAPI = 'https://backrooms.fandom.com/de/api.php';
        break;

    case 'silowiki':
        wfLoadExtension( 'SemanticMediaWiki' );
        enableSemantics( 'telepedia.net' );
        $wgGroupPermissions['sysop']['smw-admin'] = true /* or false */;
        $wgGroupPermissions['sysop']['smw-pageedit'] = true /* or false */;
        $wgGroupPermissions['sysop']['smw-patternedit'] = true /* or false */;
        $wgGroupPermissions['sysop']['smw-schemaedit'] = true /* or false */;
        break;
}
