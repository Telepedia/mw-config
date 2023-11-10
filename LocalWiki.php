<?php

switch ( $wi->dbname ) {

    case 'spicewarswiki':
        wfLoadExtension( 'SearchStats' );
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

    case 'newqualitipediawiki':
        wfLoadExtension( 'Description2' );
        break;

    case 'silowiki':
        wfLoadExtension( 'SemanticMediaWiki' );
        enableSemantics( 'telepedia.net' );
        break;
}
