<?php

/**
 * ManageWiki extensions and skins are added using the variable below.
 *
 * name: MUST match the name in extension.json, skin.json, or $wgExtensionCredits.
 * displayname: the plain text display name, or a localised message key to be displayed.
 * linkPage: full url for an information page for the extension.
 * description: the plain text description, or a localised message key to be displayed.
 * help: additional help information for the extension.
 * var: the relevant var that enables the extension.
 * conflicts: string of extensions that cause this extension to not work.
 * requires: an array. See below for available options.
 * install: an array. See below for available options.
 * remove: an array. See install for available options.
 * section: string name of groupings for extension.
 *
 * 'requires' can be one of:
 *
 * activeusers: max integer amount of active users a wiki may have in order to enable this extension.
 * articles: max integer amount of articles a wiki may have in order to enable this extension.
 * extensions: array of other extensions that must be enabled in order to enable this extension.
 * pages: max integer amount of pages a wiki may have in order to enable this extension.
 * permissions: array of permissions a user must have to be able to enable this extension. Regardless of this value, a user must always have the managewiki permission.
 * visibility['state']: can be either 'private' or 'public'. If set to 'private' this extension can only be enabled on private wikis. If set to 'public' it can only be enabled on public wikis.
 *
 * 'install'/'remove' can be one of:
 *
 * files: array, mapped to location => source.
 * mwscript: array, mapped to script path => array of options.
 * namespaces: array of which namespaces and namespace data to install with extension; 'remove' only needs namespace ID.
 * permissions: array of which permissions to install with extension.
 * settings: array of ManageWikiSettings to modify when the extension is enabled, mapped variable => value.
 * sql: array of sql files to install with extension, mapped table name => sql file path.
 */

$wgManageWikiExtensions = [
	// Editors
	'codeeditor' => [
		'name' => 'CodeEditor',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:CodeEditor',
		'var' => 'wmgUseCodeEditor',
		'conflicts' => false,
		'requires' => [
			'extensions' => [
				'wikieditor',
			],
		],
		'section' => 'editors',
	],
	'visualeditor' => [
		'name' => 'VisualEditor',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:VisualEditor',
		'var' => 'wmgUseVisualEditor',
		'conflicts' => false,
		'requires' => [],
		'section' => 'editors',
	],
	'wikieditor' => [
		'name' => 'WikiEditor',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:WikiEditor',
		'var' => 'wmgUseWikiEditor',
		'conflicts' => false,
		'requires' => [],
		'section' => 'editors',
	],
	'codemirror' => [
		'name' => 'CodeMirror',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:CodeMirror',
		'var' => 'wmgUseCodeMirror',
		'conflicts' => false,
		'requires' => [],
		'section' => 'editors',
	],
	'charinsert' => [
		'name' => 'CharInsert',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:CharInsert',
		'var' => 'wmgUseCharInsert',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'wikiseo' => [
		'name' => 'WikiSEO',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:WikiSEO',
		'var' => 'wmgUseWikiSeo',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'dummyfandoommainpagetags' => [
		'name' => 'DummyFandoomMainpageTags',
		'linkpage' => 'https://github.com/ciencia/mediawiki-extensions-DummyFandoomMainpageTags',
		'var' => 'wmgUseFandoomMainpageTags',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'inputbox' => [
		'name' => 'InputBox',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:InputBox',
		'var' => 'wmgUseInputBox',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'math' => [
		'name' => 'Math',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Math',
		'var' => 'wmgUseMath',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'sql' => [
				'mathlatexml' => "$IP/extensions/Math/db/mathlatexml.mysql.sql",
				'mathoid' => "$IP/extensions/Math/db/mathoid.mysql.sql"
			],
		],
	],
	'msupload' => [
		'name' => 'MsUpload',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:MsUpload',
		'var' => 'wmgUseMsUpload',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],

	'poem' => [
		'name' => 'Poem',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Poem',
		'var' => 'wmgUsePoem',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'syntaxhighlight_geshi' => [
		'name' => 'SyntaxHighlight',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:SyntaxHighlight',
		'var' => 'wmgUseSyntaxHighlightGeSHi',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'replacetext' => [
		'name' => 'Replace Text',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Replace_Text',
		'var' => 'wmgUseReplaceText',
		'conflicts' => false,
		'requires' => [
			'permissions' => [
				'managewiki-restricted',
			],
		],
		'install' => [
			'permissions' => [
				'sysop' => [
					'permissions' => [
						'replacetext',
					],
				],
			],
		],
		'section' => 'specialpages',
	],
	'description2' => [
		'name' => 'Description2',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Description2',
		'var' => 'wmgUseDescription2',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'youtube' => [
		'name' => 'YouTube',
		'linkPage' => 'https://github.com/miraheze/YouTube',
		'var' => 'wmgUseYouTube',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'uploadwizard' => [
		'name' => 'Upload Wizard',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:UploadWizard',
		'var' => 'wmgUseUploadWizard',
		'conflicts' => false,
		'requires' => [],
		'section' => 'mediahandlers',
	],
	'twittertag' => [
		'name' => 'Twitter Tag',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:TwitterTag',
		'var' => 'wmgUseTwitterTag',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'parserfunctions' => [
		'name' => 'ParserFunctions',
		'linkPage' => 'https://www.mediawiki.org/wiki/Extension:ParserFunctions',
		'var' => 'wmgUseParserFunctions',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'embedvideo' => [
		'name' => 'EmbedVideo',
		'linkPage' => 'https://github.com/StarCitizenWiki/mediawiki-extensions-EmbedVideo',
		'var' => 'wmgUseEmbedVideo',
		'conflicts' => false,
		'requires' => [],
		'section' => 'mediahandlers',
	],
	'video' => [
		'name' => 'Video',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Video',
		'var' => 'wmgUseVideo',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'sql' => [
				'oldvideo' => "$IP/extensions/Video/sql/oldvideo.sql",
				'video' => "$IP/extensions/Video/sql/video.sql",
			],
			'permissions' => [
				'user' => [
					'permissions' => [
						'addvideo',
					],
				],
			],
			'namespaces' => [
				'Video' => [
					'id' => 400,
					'searchable' => 0,
					'subpages' => 0,
					'protection' => 'addvideo',
					'content' => 0,
					'aliases' => [],
					'contentmodel' => 'wikitext',
					'additional' => []
				],
				'Video_talk' => [
					'id' => 401,
					'searchable' => 0,
					'subpages' => 0,
					'protection' => '',
					'content' => 0,
					'aliases' => [],
					'contentmodel' => 'wikitext',
					'additional' => []
				],
			],
		],
		'section' => 'specialpages',
	],
	'pageimages' => [
		'name' => 'PageImages',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:PageImages',
		'var' => 'wmgUsePageImages',
		'conflicts' => false,
		'requires' => [],
		'section' => 'api',
	],
	'textextracts' => [
		'name' => 'TextExtracts',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:TextExtracts',
		'var' => 'wmgUseTextExtracts',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'popups' => [
		'name' => 'Popups',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Popups',
		'var' => 'wmgUsePopups',
		'conflicts' => false,
		'requires' => [
			'extensions' => [
				'pageimages',
				'textextracts',
			],
		],
		'section' => 'other',
	],
	'tabberneue' => [
		'name' => 'TabberNeue',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:TabberNeue',
		'var' => 'wmgUseTabberNeue',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'comments' => [
		'name' => 'Comments',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Comments',
		'var' => 'wmgUseComments',
		'conflicts' => 'protectionindicator',
		'requires' => [],
		'install' => [
			'sql' => [
				'Comments' => "$IP/extensions/Comments/sql/comments.sql",
				'Comments_block' => "$IP/extensions/Comments/sql/comments_block.sql",
				'Comments_Vote' => "$IP/extensions/Comments/sql/comments_vote.sql",
			],
			'permissions' => [
				'*' => [
					'permissions' => [
						'comment',
					],
				],
				'autoconfirmed' => [
					'permissions' => [
						'commentlinks',
					],
				],
				'commentadmin' => [
					'permissions' => [
						'commentadmin',
					],
				],
				'sysop' => [
					'permissions' => [
						'commentadmin',
					],
				],
			],
		],
		'section' => 'parserhooks',
	],
	'socialprofile' => [
		'name' => 'SocialProfile',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:SocialProfile',
		'description' => 'socialprofile-desc',
		'var' => 'wmgUseSocialProfile',
		'conflicts' => false,
		'requires' => [
			'permissions' => [
				'managewiki-restricted',
			],
		],
		'install' => [
			'sql' => [
				'user_profile' => "$IP/extensions/SocialProfile/UserProfile/sql/user_profile.sql",
				'user_fields_privacy' => "$IP/extensions/SocialProfile/UserProfile/sql/user_fields_privacy.sql",
				'user_system_messages' => "$IP/extensions/SocialProfile/UserStats/sql/user_system_messages.sql",
				'user_points_monthly' => "$IP/extensions/SocialProfile/UserStats/sql/user_points_monthly.sql",
				'user_points_archive' => "$IP/extensions/SocialProfile/UserStats/sql/user_points_archive.sql",
				'user_points_weekly' => "$IP/extensions/SocialProfile/UserStats/sql/user_points_weekly.sql",
				'user_stats' => "$IP/extensions/SocialProfile/UserStats/sql/user_stats.sql",
				'user_system_gift' => "$IP/extensions/SocialProfile/SystemGifts/sql/user_system_gift.sql",
				'system_gift' => "$IP/extensions/SocialProfile/SystemGifts/sql/system_gift.sql",
				'user_relationship' => "$IP/extensions/SocialProfile/UserRelationship/sql/user_relationship.sql",
				'user_relationship_request' => "$IP/extensions/SocialProfile/UserRelationship/sql/user_relationship_request.sql",
				'user_gift' => "$IP/extensions/SocialProfile/UserGifts/sql/user_gift.sql",
				'gift' => "$IP/extensions/SocialProfile/UserGifts/sql/gift.sql",
				'user_board' => "$IP/extensions/SocialProfile/UserBoard/sql/user_board.sql"
			],
			'permissions' => [
				'sysop' => [
					'permissions' => [
						'awardsmanage',
						'giftadmin',
						'avatarremove',
						'editothersprofiles'
					],
				],
			],
		],
		'section' => 'other',
	],
	'categorytree' => [
		'name' => 'CategoryTree',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:CategoryTree',
		'var' => 'wmgUseCategoryTree',
		'conflicts' => false,
		'requires' => [],
		'install' => [],
		'section' => 'parserhooks',
	],
	'gadgets' => [
		'name' => 'Gadgets',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Gadgets',
		'var' => 'wmgUseGadgets',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'imagemap' => [
		'name' => 'ImageMap',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:ImageMap',
		'var' => 'wmgUseImageMap',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'pdfhandler' => [
		'name' => 'PDF Handler',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:PdfHandler',
		'var' => 'wmgUsePdfHandler',
		'conflicts' => false,
		'requires' => [],
		'section' => 'mediahandlers',
	],
	'thanks' => [
		'name' => 'Thanks',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Thanks',
		'var' => 'wmgUseThanks',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'simpleblogpage' => [
		'name' => 'SimpleBlogPage',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:SimpleBlogPage',
		'var' => 'wmgUseSimpleBlogPage',
		'conflicts' => 'blogpage',
		'requires' => [],
		'install' => [
			'namespaces' => [
				'User_blog' => [
					'id' => 502,
					'searchable' => 1,
					'subpages' => 1,
					'protection' => 'edit',
					'content' => 0,
					'aliases' => [],
					'contentmodel' => 'wikitext',
					'additional' => []
				],
				'User_blog_talk' => [
					'id' => 503,
					'searchable' => 0,
					'subpages' => 1,
					'protection' => '',
					'content' => 0,
					'aliases' => [],
					'contentmodel' => 'wikitext',
					'additional' => []
				],
			],
			'permissions' => [
				'user' => [
					'permissions' => [
						'createblogpost',
					],
				],
			],
		],
		'section' => 'other',
	],
];
