<?php

switch ( $wi->dbname ) {

    case 'spicewarswiki':
        wfLoadExtension( 'SearchStats' );
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

    case 'testingoawiki':
        wfLoadExtension( 'Discussions' );
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

    case 'tbsatdhwiki':
        wfLoadExtension( 'MultiBoilerplate' );
        wfLoadExtension( 'AutoCreateCategoryPages' );
        break;
}
