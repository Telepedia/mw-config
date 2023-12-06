<?php

switch ( $wi->dbname ) {

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
}
