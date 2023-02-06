<?php

// Set the default global variables

$wgGoogleTagManagerContainerID = '';
$wgGoogleTagManagerData = '';

class TagManager {

	/**
 	 * @link https://www.mediawiki.org/wiki/Manual:Hooks/SkinAfterBottomScripts
 	 * This is the description for the class.
 	 * @param $skin  Skin object
 	 * @param &$text: bottomScripts Text. Append to $text to add additional text/scripts after the stock bottom scripts.
 	 * @return bool
 	 */

	public static function onSkinAfterBottomScripts( $skin, &$text='' ) {
		global $wgGoogleTagManagerContainerID, $wgGoogleTagManagerData, $wgUser;
		
    	// Check if $wgGoogleTagManagerData has any value and add a " "(space) for formating
    	if ( $wgGoogleTagManagerData != '' ) {  
        	$wgGoogleTagManagerData = ' '.$wgGoogleTagManagerData;
        }

    	// User a member of nonanalytics do not add tag but just a notice and return output
		if ( $wgUser->isAllowed( 'noanalytics' ) ) {
			$text = "\n<!-- Google Tag Manager tracking is disabled for this user -->";
        	return true;
		}

    	// Output the Google Tag
		if ( $wgGoogleTagManagerContainerID == '' ) {

        	// Missing ContainerID add notice only and return output
			$text = "\n<!-- Set \$wgGoogleTagManagerContainerID to your container ID provided by Google Tag Manager in LocalSettings.php. -->";
        	return true;
		} else {

        	// Output the Google Tag <noscript> and <script> and return output
			$text = <<<HTML
<!-- Google Tag Manager (added by extension GoogleTagManager) -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id={$wgGoogleTagManagerContainerID}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script{$wgGoogleTagManagerData}>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$wgGoogleTagManagerContainerID}');</script>
<!-- End Google Tag Manager -->
HTML;
    		return true;
        }

	}

}