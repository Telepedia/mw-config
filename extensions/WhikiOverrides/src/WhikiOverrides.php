<?php

use MediaWiki\Extension\AbuseFilter\AbuseFilterServices;
use MediaWiki\Extension\AbuseFilter\Variables\VariableHolder;
use MediaWiki\Extension\CentralAuth\User\CentralAuthUser;
use MediaWiki\MediaWikiServices;
use MediaWiki\Shell\Shell;
use Miraheze\ManageWiki\Helpers\ManageWikiSettings;
use Wikimedia\IPUtils;

class WhikiOverrides {
    public static function onMessageCacheGet( &$lcKey ) {
		static $keys = [
			'copyrightwarning',
			'pagetitle'
		];

		if ( in_array( $lcKey, $keys, true ) ) {
			$prefixedKey = "whiki-$lcKey";
			// MessageCache uses ucfirst if ord( key ) is < 128, which is true of all
			// of the above.  Revisit if non-ASCII keys are used.
			$ucKey = ucfirst( $lcKey );

			$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'whikioverrides' );
			$cache = MediaWikiServices::getInstance()->getMessageCache();

			if (
			// Override order:
			// 1. If the MediaWiki:$ucKey page exists, use the key unprefixed
			// (in all languages) with normal fallback order.  Specific
			// language pages (MediaWiki:$ucKey/xy) are not checked when
			// deciding which key to use, but are still used if applicable
			// after the key is decided.
			//
			// 2. Otherwise, use the prefixed key with normal fallback order
			// (including MediaWiki pages if they exist).
			$cache->getMsgFromNamespace( $ucKey, $config->get( 'LanguageCode' ) ) === false
			) {
				$lcKey = $prefixedKey;
			}
		}

		return true;
	}

	/**
	 * Enables global interwiki for [[tp:wiki:Page]]
	 */
	public static function onHtmlPageLinkRendererEnd( $linkRenderer, $target, $isKnown, &$text, &$attribs, &$ret ) {
		$target = (string)$target;
		$tooltip = $target;
		$useText = true;

		$ltarget = strtolower( $target );
		$ltext = strtolower( HtmlArmor::getHtml( $text ) );

		if ( $ltarget == $ltext ) {
			// Allow link piping, but don't modify $text yet
			$useText = false;
		}

		$target = explode( ':', $target );

		if ( count( $target ) < 2 ) {
			// Not enough parameters for interwiki
			return true;
		}

		if ( $target[0] == '0' ) {
			array_shift( $target );
		}

		$prefix = strtolower( $target[0] );

		if ( $prefix != 'tp' ) {
			// Not interesting
			return true;
		}

		$wiki = strtolower( $target[1] );
		$target = array_slice( $target, 2 );
		$target = implode( ':', $target );

		if ( !$useText ) {
			$text = $target;
		}
		if ( $text == '' ) {
			$text = $wiki;
		}

		$target = str_replace( ' ', '_', $target );
		$target = urlencode( $target );
		$linkURL = "https://$wiki.telepedia.net/wiki/$target";

		$attribs = [
			'href' => $linkURL,
			'class' => 'extiw',
			'title' => $tooltip
		];

		return true;
	}

	/**
	 * Hard redirects all pages like Tp:Wiki:Page as global interwiki.
	 */
	public static function onInitializeArticleMaybeRedirect( $title, $request, &$ignoreRedirect, &$target, $article ) {
		$title = explode( ':', $title );
		$prefix = strtolower( $title[0] );

		if ( count( $title ) < 3 || $prefix !== 'tp' ) {
			return true;
		}

		$wiki = strtolower( $title[1] );
		$page = implode( ':', array_slice( $title, 2 ) );
		$page = str_replace( ' ', '_', $page );
		$page = urlencode( $page );

		$target = "https://$wiki.telepedia.net/wiki/$page";

		return true;
	}

	public static function onTitleReadWhitelist( Title $title, User $user, &$whitelisted ) {
		if ( $title->equals( Title::newMainPage() ) ) {
			$whitelisted = true;
			return;
		}

		$specialsArray = [
			'CentralAutoLogin',
			'CentralLogin',
			'ConfirmEmail',
			'CreateAccount',
			'Notifications',
			'OAuth',
			'ResetPassword',
			'Watchlist'
		];

		if ( $title->isSpecialPage() ) {
			$rootName = strtok( $title->getText(), '/' );
			$rootTitle = Title::makeTitle( $title->getNamespace(), $rootName );

			foreach ( $specialsArray as $page ) {
				if ( $rootTitle->equals( SpecialPage::getTitleFor( $page ) ) ) {
					$whitelisted = true;
					return;
				}
			}
		}
	}

	public static function onGlobalUserPageWikis( &$list ) {
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'whikioverrides' );
		$cwCacheDir = $config->get( 'CreateWikiCacheDirectory' );
		if ( file_exists( "{$cwCacheDir}/databases.json" ) ) {
			$databasesArray = json_decode( file_get_contents( "{$cwCacheDir}/databases.json" ), true );
			$list = array_keys( $databasesArray['combi'] );
			return false;
		}

		return true;
	}

    public static function onSkinAddFooterLinks( Skin $skin, string $key, array &$footerlinks  ) {
        if ( $key === 'places' ) {
            $footerlinks['discord'] = Html::rawElement( 'a', [ 'href' => 'https://discord.gg/DuhckJpYQQ' ], 'Discord' );
        }
    }
    /*
    public static function onSkinAfterContent( &$data, Skin $skin ) {
        $slideout = "<div class=\"feedback-slideout\"><div class=\"feedback-close\">&times;</div><div class=\"feedback-header\">Enjoying our Platform?</div><div class=\"feedback-content\">We're asking for your feedback on our platform. Please complete our <a href=\"https://mqarzpdvi9s.typeform.com/to/xzm9REdV\" style=\"font-weight: bold; text-decoration: underline;\">short survey!</a></div></div>";
        $data .= $slideout;
    }
   */
	
    // Quantcast tracking and consent modals
    public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) { 
    
    $out->addHeadItem("<!-- Quantcast Choice. Consent Manager Tag v2.0 (for TCF 2.0) -->
			<script type=\"text/javascript\" async=true>
			(function() {
			  var host = 'telepedia.net';
			  var element = document.createElement('script');
			  var firstScript = document.getElementsByTagName('script')[0];
			  var url = 'https://cmp.quantcast.com'
			    .concat('/choice/', 'X9wJz6QC1kV7T', '/', host, '/choice.js?tag_version=V2');
			  var uspTries = 0;
			  var uspTriesLimit = 3;
			  element.async = true;
			  element.type = 'text/javascript';
			  element.src = url;

			  firstScript.parentNode.insertBefore(element, firstScript);

			  function makeStub() {
			    var TCF_LOCATOR_NAME = '__tcfapiLocator';
			    var queue = [];
			    var win = window;
			    var cmpFrame;

			    function addFrame() {
			      var doc = win.document;
			      var otherCMP = !!(win.frames[TCF_LOCATOR_NAME]);

			      if (!otherCMP) {
				if (doc.body) {
				  var iframe = doc.createElement('iframe');

				  iframe.style.cssText = 'display:none';
				  iframe.name = TCF_LOCATOR_NAME;
				  doc.body.appendChild(iframe);
				} else {
				  setTimeout(addFrame, 5);
				}
			      }
			      return !otherCMP;
			    }

			    function tcfAPIHandler() {
			      var gdprApplies;
			      var args = arguments;

			      if (!args.length) {
				return queue;
			      } else if (args[0] === 'setGdprApplies') {
				if (
				  args.length > 3 &&
				  args[2] === 2 &&
				  typeof args[3] === 'boolean'
				) {
				  gdprApplies = args[3];
				  if (typeof args[2] === 'function') {
				    args[2]('set', true);
				  }
				}
			      } else if (args[0] === 'ping') {
				var retr = {
				  gdprApplies: gdprApplies,
				  cmpLoaded: false,
				  cmpStatus: 'stub'
				};

				if (typeof args[2] === 'function') {
				  args[2](retr);
				}
			      } else {
				if(args[0] === 'init' && typeof args[3] === 'object') {
				  args[3] = Object.assign(args[3], { tag_version: 'V2' });
				}
				queue.push(args);
			      }
			    }

			    function postMessageEventHandler(event) {
			      var msgIsString = typeof event.data === 'string';
			      var json = {};

			      try {
				if (msgIsString) {
				  json = JSON.parse(event.data);
				} else {
				  json = event.data;
				}
			      } catch (ignore) {}

			      var payload = json.__tcfapiCall;

			      if (payload) {
				window.__tcfapi(
				  payload.command,
				  payload.version,
				  function(retValue, success) {
				    var returnMsg = {
				      __tcfapiReturn: {
					returnValue: retValue,
					success: success,
					callId: payload.callId
				      }
				    };
				    if (msgIsString) {
				      returnMsg = JSON.stringify(returnMsg);
				    }
				    if (event && event.source && event.source.postMessage) {
				      event.source.postMessage(returnMsg, '*');
				    }
				  },
				  payload.parameter
				);
			      }
			    }

			    while (win) {
			      try {
				if (win.frames[TCF_LOCATOR_NAME]) {
				  cmpFrame = win;
				  break;
				}
			      } catch (ignore) {}

			      if (win === window.top) {
				break;
			      }
			      win = win.parent;
			    }
			    if (!cmpFrame) {
			      addFrame();
			      win.__tcfapi = tcfAPIHandler;
			      win.addEventListener('message', postMessageEventHandler, false);
			    }
			  };

			  makeStub();

			  var uspStubFunction = function() {
			    var arg = arguments;
			    if (typeof window.__uspapi !== uspStubFunction) {
			      setTimeout(function() {
				if (typeof window.__uspapi !== 'undefined') {
				  window.__uspapi.apply(window.__uspapi, arg);
				}
			      }, 500);
			    }
			  };

			  var checkIfUspIsReady = function() {
			    uspTries++;
			    if (window.__uspapi === uspStubFunction && uspTries < uspTriesLimit) {
			      console.warn('USP is not accessible');
			    } else {
			      clearInterval(uspInterval);
			    }
			  };

			  if (typeof window.__uspapi === 'undefined') {
			    window.__uspapi = uspStubFunction;
			    var uspInterval = setInterval(checkIfUspIsReady, 6000);
			  }
			})();
			</script>
			<!-- End Quantcast Choice. Consent Manager Tag v2.0 (for TCF 2.0) -->");
     
    }
	
    public static function onSiteNoticeAfter( &$siteNotice, $skin ) {
        $cwConfig = new GlobalVarConfig( 'cw' );

        if ( $cwConfig->get( 'Closed' ) ) {
            if ( $cwConfig->get( 'Private' ) ) {
                $siteNotice .= '<div class="wikitable" style="text-align: center; width: 60%; margin-left: auto; margin-right:auto; padding: 15px; border: 1px solid black; background-color: #f39c12;"> <span class="plainlinks">' . $skin->msg( 'telepedia-sitenotice-closed-private' )->parse() . '</span></div>';
            } else {
                $siteNotice .= '<div class="wikitable" style="text-align: center; width: 60%; margin-left: auto; margin-right:auto; padding: 15px; border: 1px solid black; background-color: #f39c12;"> <span class="plainlinks">' . $skin->msg( 'telepedia-sitenotice-closed' )->parse() . '</span></div>';
            }
        } elseif ( $cwConfig->get( 'Inactive' ) && $cwConfig->get( 'Inactive' ) !== 'exempt' ) {
            if ( $cwConfig->get( 'Private' ) ) {
                $siteNotice .= '<div class="wikitable" style="text-align: center; width: 60%; margin-left: auto; margin-right:auto; padding: 15px; border: 1px solid black; background-color: #EEE;"> <span class="plainlinks">' . $skin->msg( 'telepedia-sitenotice-inactive-private' )->parse() . '</span></div>';
            } else {
                $siteNotice .= '<div class="wikitable" style="text-align: center; width: 60%; margin-left: auto; margin-right:auto; padding: 15px; border: 1px solid black; background-color: #EEE;"> <span class="plainlinks">' . $skin->msg( 'telepedia-sitenotice-inactive' )->parse() . '</span></div>';
            }
        }
    }
}
