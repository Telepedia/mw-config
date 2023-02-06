<?php

use MediaWiki\MediaWikiServices;
use MediaWiki\Specials\SpecialShortpages;
// use Telepedia\Community\CommunityPageShortPagesCardModel;
class SpecialCommunity extends \SpecialPage {
    public function __construct() {
        parent::__construct( 'Community' );
    }

    public function execute( $par ) {
        // set the headers and all of that stuff.
        $this->setHeaders();
        // show the welcome module
        $this->showWelcomeModule();
        $this->getPages();
    }

    public function showWelcomeModule()
    {
        global $wgSitename;
        $out = $this->getOutput();
        $out->addModuleStyles('ext.community.styles');
        // generate a welcome module at the top of the article
        $out->addHTML('<div class="welcome-module"><div class="text-center">' . $this->msg('communityheader') . '</div></div>');
    }

        private function getPages() {
            $pages = [ ];
            shuffle( $pages );

            foreach ( ( new ShortPagesPage() )->doQuery() as $obj ) {
                $pages[] = $obj->title;
            }

            return $pages;
        }

        private function getPage( $title ) {
            $title = Title::newFromText( $title );
            return [
                'link' => [
                    'text' => $title->getText(),
                    'articleurl' => $title->getFullURL(),
                    'editlink' => LinkHelper::forceLoginLink( $title, LinkHelper::WITH_EDIT_MODE )
                ]
            ];
        }
    }
