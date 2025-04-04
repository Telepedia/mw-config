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
	'cargo' => [
		'name' => 'Cargo',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Cargo',
		'help' => 'Use this wisely, it can have profound impacts on database performance.',
		'conflicts' => 'semanticmediawiki',
		'requires' => [
			'permissions' => [
				'managewiki-restricted',
			],
		],
		'install' => [
			'mwscript' => [
				"$IP/extensions/TelepediaMagic/maintenance/createCargoDB.php" => [],
			],
			'sql' => [
				'cargo_tables' => "$IP/extensions/Cargo/sql/Cargo.sql",
				'cargo_backlinks' => "$IP/extensions/Cargo/sql/cargo_backlinks.sql"
			],
			'permissions' => [
				'*' => [
					'permissions' => [
						'runcargoqueries',
					],
				],
				'sysop' => [
					'permissions' => [
						'recreatecargodata',
						'deletecargodata',
					],
				],
			],
		],
		'section' => 'parserhooks',
	],
	'linter' => [
		'name' => 'Linter',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Linter',
		'var' => 'wmgUseLinter',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'sql' => [
				'linter' => "$IP/extensions/Linter/sql/tables-generated.sql"
			],
		],
		'section' => 'specialpages',
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
		'linkPage' => 'https://github.com/ciencia mediawiki-extensions-DummyFandoomMainpageTags',
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
	'math' => [
		'name' => 'Math',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Math',
		'var' => 'wmgUseMath',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'sql' => [
				'mathlatexml' => "$IP/extensions/Math/sql/mysql/mathlatexml.sql",
				'mathoid' => "$IP/extensions/Math/sql/mysql/mathoid.sql"
			],
		],
		'section' => 'parserhooks',
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
	'josa' => [
		'name' => 'Josa',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Josa',
		'var' => 'wmgUseJosa',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
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
	'commentstreams' => [
		'name' => 'CommentStreams',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:CommentStreams',
		'var' => 'wmgUseCommentStreams',
		'conflicts' => 'moderation',
		'requires' => [],
		'install' => [
			'sql' => [
				'cs_comments' => "$IP/extensions/CommentStreams/sql/mysql/cs_comments.sql",
				'cs_replies' => "$IP/extensions/CommentStreams/sql/mysql/cs_replies.sql",
				'cs_votes' => "$IP/extensions/CommentStreams/sql/mysql/cs_votes.sql",
				'cs_watchlist' => "$IP/extensions/CommentStreams/sql/mysql/cs_watchlist.sql",
			],
			'permissions' => [
				'user' => [
					'permissions' => [
						'cs-comment',
					],
				],
				'csmoderator' => [
					'permissions' => [
						'cs-moderator-delete',
					],
				],
				'bureaucrat' => [
					'addgroups' => [
						'csmoderator',
					],
					'removegroups' => [
						'csmoderator',
					],
				],
			],
		],
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
	'softredirector' => [
		'name' => 'SoftRedirector',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:SoftRedirector',
		'var' => 'wmgUseSoftRedirector',
		'conflicts' => false,
		'requires' => [],
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
	'newsletter' => [
		'name' => 'Newsletter',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Newsletter',
		'var' => 'wmgUseNewsletter',
		'conflicts' => 'lingo',
		'requires' => [],
		'install' => [
			'namespaces' => [
				'Newsletter' => [
					'id' => 5500,
					'searchable' => 0,
					'subpages' => 0,
					'protection' => 'newsletter-manage',
					'content' => 0,
					'aliases' => [],
					'contentmodel' => 'NewsletterContent',
					'additional' => []
				],
				'Newsletter_talk' => [
					'id' => 5501,
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
				'sysop' => [
					'permissions' => [
						'newsletter-create',
						'newsletter-delete',
						'newsletter-manage',
						'newsletter-restore',
					],
				],
			],
			'sql' => [
				'nl_newsletters' => "$IP/extensions/Newsletter/sql/mysql/tables-generated.sql",
			],
		],
		'section' => 'other',
	],
	'spoilers' => [
		'name' => 'Spoilers',
		'var' => 'wmgUseSpoilers',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Spoilers',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'newusermessage' => [
		'name' => 'NewUserMessage',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:NewUserMessage',
		'var' => 'wmgUseNewUserMessage',
		'conflicts' => 'flow',
		'requires' => [],
		'section' => 'other',
	],
	'newusernotif' => [
		'name' => 'New User Email Notification',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:New_User_Email_Notification',
		'var' => 'wmgUseNewUserNotif',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'pdfhandler' => [
		'name' => 'PDF Handler',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:PdfHandler',
		'var' => 'wmgUsePdfHandler',
		'conflicts' => false,
		'requires' => [],
		'section' => 'mediahandlers',
	],
	'pdfembed' => [
		'name' => 'PDFEmbed',
		'displayname' => 'PDF Embed',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:PDFEmbed',
		'var' => 'wmgUsePDFEmbed',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'permissions' => [
				'sysop' => [
					'permissions' => [
						'embed_pdf',
					],
				],
			],
		],
		'section' => 'parserhooks',
	],
	'protectionindicator' => [
		'name' => 'ProtectionIndicator',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:ProtectionIndicator',
		'var' => 'wmgUseProtectionIndicator',
		'conflicts' => 'comments',
		'install' => [],
		'requires' => [],
		'section' => 'other',
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
	'adminlinks' => [
		'name' => 'Admin Links',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Admin_Links',
		'var' => 'wmgUseAdminLinks',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'permissions' => [
				'sysop' => [
					'permissions' => [
						'adminlinks',
					],
				],
			],
		],
		'section' => 'specialpages',
	],
	'sandboxlink' => [
		'name' => 'SandboxLink',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:SandboxLink',
		'var' => 'wmgUseSandboxLink',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'groupssidebar' => [
		'name' => 'GroupsSidebar',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:GroupsSidebar',
		'var' => 'wmgUseGroupsSidebar',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'ajaxpoll' => [
		'name' => 'AJAX Poll',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:AJAXPoll',
		'var' => 'wmgUseAJAXPoll',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'sql' => [
				'ajaxpoll_info' => "$IP/extensions/AJAXPoll/sql/create-table--ajaxpoll_info.sql",
				'ajaxpoll_vote' => "$IP/extensions/AJAXPoll/sql/create-table--ajaxpoll_vote.sql"
			],
			'permissions' => [
				'user' => [
					'permissions' => [
						'ajaxpoll-vote',
						'ajaxpoll-view-results',
					],
				],
			],
		],
		'section' => 'parserhooks',
	],
	'lastmodified' => [
		'name' => 'LastModified',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:LastModified',
		'var' => 'wmgUseLastModified',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'masseditregex' => [
		'name' => 'MassEditRegex',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:MassEditRegex',
		'var' => 'wmgUseMassEditRegex',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'permissions' => [
				'sysop' => [
					'permissions' => [
						'masseditregex',
					],
				],
			],
		],
		'section' => 'specialpages',
	],
	'ratepage' => [
		'name' => 'RatePage',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:RatePage',
		'var' => 'wmgUseRatePage',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'sql' => [
				'ratepage_contest' => "$IP/extensions/RatePage/sql/create-table--ratepage-contest.sql",
				'ratepage_vote' => "$IP/extensions/RatePage/sql/create-table--ratepage-vote.sql"
			],
			'permissions' => [
				'*' => [
					'permissions' => [
						'ratepage-vote',
						'ratepage-contests-view-list',
					],
				],
				'sysop' => [
					'permissions' => [
						'ratepage-contests-view-details',
						'ratepage-contests-edit',
					],
				],
				'bureaucrat' => [
					'permissions' => [
						'ratepage-contests-clear',
					],
				],
			],
		],
		'section' => 'other',
	],
	'css' => [
		'name' => 'CSS',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:CSS',
		'var' => 'wmgUseCSS',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'wikilove' => [
		'name' => 'WikiLove',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:WikiLove',
		'var' => 'wmgUseWikiLove',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'sql' => [
				'wikilove_log' => "$IP/extensions/WikiLove/patches/tables-generated.sql"
			],
		],
		'section' => 'other',
	],
	'webchat' => [
		'name' => 'WebChat',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:WebChat',
		'var' => 'wmgUseWebChat',
		'conflicts' => false,
		'requires' => [],
		'section' => 'specialpages',
	],
	'voteny' => [
		'name' => 'VoteNY',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:VoteNY',
		'var' => 'wmgUseVoteNY',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'sql' => [
				'Vote' => "$IP/extensions/VoteNY/sql/vote.mysql"
			],
			'permissions' => [
				'user' => [
					'permissions' => [
						'voteny',
					],
				],
			],
		],
		'section' => 'parserhooks',
	],
	'variables' => [
		'name' => 'Variables',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Variables',
		'var' => 'wmgUseVariables',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'variableslua' => [
		'name' => 'VariablesLua',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:VariablesLua',
		'var' => 'wmgUseVariablesLua',
		'conflicts' => false,
		'requires' => [
			'extensions' => [
				'variables',
			],
		],
		'section' => 'other',
	],
	'universallanguageselector' => [
		'name' => 'UniversalLanguageSelector',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:UniversalLanguageSelector',
		'var' => 'wmgUseUniversalLanguageSelector',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'translate' => [
		'name' => 'Translate',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Translate',
		'var' => 'wmgUseTranslate',
		'conflicts' => false,
		'requires' => [
			'extensions' => [
				'universallanguageselector',
			],
		],
		'install' => [
			'permissions' => [
				'*' => [
					'permissions' => [
						'translate',
					],
				],
				'sysop' => [
					'permissions' => [
						'pagetranslation',
						'translate-import',
						'translate-manage',
					],
				],
				'user' => [
					'permissions' => [
						'translate-messagereview',
					],
				],
			],
			'sql' => [
				'revtag' => "$IP/extensions/Translate/sql/mysql/revtag.sql",
				'translate_cache' => "$IP/extensions/Translate/sql/mysql/translate_cache.sql",
				'translate_groupreviews' => "$IP/extensions/Translate/sql/mysql/translate_groupreviews.sql",
				'translate_groupstats' => "$IP/extensions/Translate/sql/mysql/translate_groupstats.sql",
				'translate_messageindex' => "$IP/extensions/Translate/sql/mysql/translate_messageindex.sql",
				'translate_metadata' => "$IP/extensions/Translate/sql/mysql/translate_metadata.sql",
				'translate_reviews' => "$IP/extensions/Translate/sql/mysql/translate_reviews.sql",
				'translate_sections' => "$IP/extensions/Translate/sql/mysql/translate_sections.sql",
				'translate_stash' => "$IP/extensions/Translate/sql/mysql/translate_stash.sql",
				'translate_tms' => "$IP/extensions/Translate/sql/mysql/translate_tm.sql",
				'translate_translatable_bundles' => "$IP/extensions/Translate/sql/mysql/translate_translatable_bundles.sql",
			],
		],
		'section' => 'specialpages',
	],
	'babel' => [
		'name' => 'Babel',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Babel',
		'var' => 'wmgUseBabel',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'sql' => [
				'babel' => "$IP/extensions/Babel/sql/tables-generated.sql"
			],
		],
		'section' => 'parserhooks',
	],
	'templatestyles' => [
		'name' => 'TemplateStyles',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:TemplateStyles',
		'var' => 'wmgUseTemplateStyles',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'templatestylesextender' => [
		'name' => 'TemplateStylesExtender',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:TemplateStylesExtender',
		'conflicts' => false,
		'requires' => [
			'extensions' => [
				'templatestyles',
			],
		],
		'section' => 'parserhooks',
	],
	'templatedata' => [
		'name' => 'TemplateData',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:TemplateData',
		'var' => 'wmgUseTemplateData',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'templatewizard' => [
		'name' => 'TemplateWizard',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:TemplateWizard',
		'var' => 'wmgUseTemplateWizard',
		'conflicts' => false,
		'requires' => [
			'extensions' => [
				'templatedata',
			],
		],
		'section' => 'other',
	],
	'timemachine' => [
		'name' => 'TimeMachine',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:TimeMachine',
		'conflicts' => false,
		'requires' => [],
		'section' => 'specialpages',
	],
	'theme' => [
		'name' => 'Theme',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Theme',
		'var' => 'wmgUseTheme',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'approvedrevs' => [
		'name' => 'Approved Revs',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Approved_Revs',
		'var' => 'wmgUseApprovedRevs',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'sql' => [
				'approved_revs_files' => "$IP/extensions/ApprovedRevs/sql/ApprovedFiles.sql",
				'approved_revs' => "$IP/extensions/ApprovedRevs/sql/ApprovedRevs.sql"
			],
			'permissions' => [
				'sysop' => [
					'permissions' => [
						'viewapprover',
						'approverevisions',
					],
				],
				'*' => [
					'permissions' => [
						'viewlinktolatest',
					],
				],
			],
		],
		'section' => 'antispam',
	],
	'arrays' => [
		'name' => 'Arrays',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Arrays',
		'var' => 'wmgUseArrays',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'articlecreationworkflow' => [
		'name' => 'ArticleCreationWorkflow',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:ArticleCreationWorkflow',
		'var' => 'wmgUseArticleCreationWorkflow',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'permissions' => [
				'*' => [
					'permissions' => [
						'createpagemainns',
					],
				],
				'autoconfirmed' => [
					'permissions' => [
						'createpagemainns',
					],
				],
				'user' => [
					'permissions' => [
						'createpagemainns',
					],
				],
			],
		],
		'section' => 'other',
	],
	'articleratings' => [
		'name' => 'ArticleRating',
		'displayname' => 'ArticleRatings',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:ArticleRatings',
		'var' => 'wmgUseArticleRatings',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'permissions' => [
				'reviewer' => [
					'permissions' => [
						'change-rating',
					],
				],
			],
			'sql' => [
				'ratings' => "$IP/extensions/ArticleRatings/ratings.sql"
			],
		],
		'section' => 'other',
	],
	'articletocategory2' => [
		'name' => 'ArticleToCategory2',
		'displayname' => 'Article To Category 2',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:ArticleToCategory2',
		'var' => 'wmgUseArticleToCategory2',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'capiunto' => [
		'name' => 'Capiunto',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Capiunto',
		'var' => 'wmgUseCapiunto',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'categoryexplorer' => [
		'name' => 'CategoryExplorer',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:CategoryExplorer',
		'var' => 'wmgUseCategoryExplorer',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'categorysortheaders' => [
		'name' => 'CategorySortHeaders',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:CategorySortHeaders',
		'var' => 'wmgUseCategorySortHeaders',
		'conflicts' => false,
		'requires' => [],
		'install' => [],
		'section' => 'other',
	],
	'citethispage' => [
		'name' => 'CiteThisPage',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:CiteThisPage',
		'var' => 'wmgUseCiteThisPage',
		'conflicts' => false,
		'requires' => [],
		'section' => 'specialpages',
	],
	'cleanchanges' => [
		'name' => 'Clean Changes',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:CleanChanges',
		'var' => 'wmgUseCleanChanges',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'contributionscores' => [
		'name' => 'ContributionScores',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Contribution_Scores',
		'var' => 'wmgUseContributionScores',
		'conflicts' => false,
		'requires' => [],
		'section' => 'specialpages',
	],
	'countdownclock' => [
		'name' => 'CountDownClock',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:CountDownClock',
		'var' => 'wmgUseCountDownClock',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'createredirect' => [
		'name' => 'CreateRedirect',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:CreateRedirect',
		'var' => 'wmgUseCreateRedirect',
		'conflicts' => false,
		'requires' => [],
		'section' => 'specialpages',
	],
	'deleteuserpages' => [
		'name' => 'DeleteUserPages',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:DeleteUserPages',
		'var' => 'wmgUseDeleteUserPages',
		'conflicts' => false,
		'requires' => [],
		'install' => [],
		'section' => 'other',
	],
	'disambiguator' => [
		'name' => 'Disambiguator',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Disambiguator',
		'var' => 'wmgUseDisambiguator',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'editsubpages' => [
		'name' => 'EditSubpages',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:EditSubpages',
		'var' => 'wmgUseEditSubpages',
		'conflicts' => false,
		'requires' => [],
		'install' => [],
		'section' => 'other',
	],
	'flaggedrevs' => [
		'name' => 'FlaggedRevs',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:FlaggedRevs',
		'var' => 'wmgUseFlaggedRevs',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'sql' => [
				'flaggedpages' => "$IP/extensions/FlaggedRevs/backend/schema/mysql/tables-generated.sql",
			],
			'permissions' => [
				'editor' => [
					'permissions' => [
						'review',
						'autoreview',
						'autoconfirmed',
						'editsemiprotected',
						'unreviewedpages',
					],
				],
				'reviewer' => [
					'permissions' => [
						'validate',
						'review',
						'autoreview',
						'autoconfirmed',
						'editsemiprotected',
						'unreviewedpages',
					],
				],
				'sysop' => [
					'permissions' => [
						'autoreview',
						'stablesettings',
						'movestable',
						'review',
						'unreviewedpages',
					],
					'addgroups' => [
						'editor',
						'autoreview',
					],
					'removegroups' => [
						'editor',
						'autoreview',
					],
				],
				'autoreview' => [
					'permissions' => [
						'autoreview',
					],
				],
				'bot' => [
					'permissions' => [
						'autoreview',
					],
				],
			],
		],
		'section' => 'specialpages',
	],
	'imagemap' => [
		'name' => 'ImageMap',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:ImageMap',
		'var' => 'wmgUseImageMap',
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
	'lockauthor' => [
		'name' => 'LockAuthor',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:LockAuthor',
		'var' => 'wmgUseLockAuthor',
		'conflicts' => 'authorprotect',
		'requires' => [],
		'install' => [
			'permissions' => [
				'sysop' => [
					'permissions' => [
						'editall',
					],
				],
			],
		],
		'section' => 'antispam',
	],
	'mobilefrontend' => [
		'name' => 'MobileFrontend',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:MobileFrontend',
		'var' => 'wmgUseMobileFrontend',
		'conflicts' => false,
		'requires' => [
			'extensions' => [
				'minervaneue',
			],
		],
		'section' => 'other',
	],
	'discussiontools' => [
		'name' => 'DiscussionTools',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:DiscussionTools',
		'var' => 'wmgUseDiscussionTools',
		'conflicts' => false,
		'requires' => [
			'extensions' => [
				'linter',
				'visualeditor',
			],
		],
		'install' => [
			'sql' => [
				'discussiontools_items' => "$IP/extensions/DiscussionTools/sql/mysql/discussiontools_persistent.sql",
				'discussiontools_subscription' => "$IP/extensions/DiscussionTools/sql/mysql/discussiontools_subscription.sql",
			],
		],
		'section' => 'other',
	],
	'searchlogger' => [
		'name' => 'Search Logger',
		'linkPage' => 'https://gitlab.com/telepedia/extensions/SearchLogger',
		'var' => 'wmgUseSearchlogger',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'sql' => [
				'search_log' => "$IP/extensions/SearchLogger/install/sql/searchlogger_table_search_log.sql",
			],
			'permissions' => [
				'sysop' => [
					'permissions' => [
						'search_log'
					],
				],
			],
		],
		'section' => 'other',
	],
	'simpletooltip' => [
		'name' => 'SimpleTooltip',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:SimpleTooltip',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'skinperpage' => [
		'name' => 'Skin per page',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:SkinPerPage',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'softredirector' => [
		'name' => 'SoftRedirector',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:SoftRedirector',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'revisionslider' => [
		'name' => 'RevisionSlider',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:RevisionSlider',
		'conflicts' => false,
		'requires' => [],
		'section' => 'other',
	],
	'purge' => [
		'name' => 'Purge',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Purge',
		'conflicts' => false,
		'requires' => [],
		'install' => [
			'permissions' => [
				'user' => [
					'permissions' => [
						'purge',
					],
				],
			],
		],
		'section' => 'other',
	],
	'telelytics' => [
		'name' => 'Telelytics',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Telelytics',
		'var' => 'wmgUseTelelytics',
		'conflicts' => false,
		'requires' => [
			'extensions' => [
				'searchlogger'
			],
		],
		'section' => 'other',
	],
	'cloudflare' => [
		'name' => 'Cloudflare',
		'linkPage' => 'https://gitlab.com/telepedia/extensions/cloudflare',
		'var' => 'wmgUseCloudflare',
		'conflicts' => false,
		'requires' => [
			'permissions' => [
				'managewiki-restricted',
			],
		],
		'section' => 'specialpages',
	],
	'labeledsectiontransclusion' => [
		'name' => 'LabeledSectionTransclusion',
		'displayname' => 'Labeled Section Transclusion',
		'var' => 'wmgUseLabeledSectionTransclusion',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:Labeled_Section_Transclusion',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],
	'randomselection' => [
		'name' => 'RandomSelection',
		'var' => 'wmgUseRandomSelection',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Extension:RandomSelection',
		'conflicts' => false,
		'requires' => [],
		'section' => 'parserhooks',
	],


	/// Skins
	'apex' => [
		'name' => 'Apex',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Skin:Apex',
		'var' => 'wmgUseApex',
		'conflicts' => false,
		'requires' => [],
		'section' => 'skins',
	],
	'erudite' => [
		'name' => 'Erudite',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Skin:Erudite',
		'var' => 'wmgUseErudite',
		'conflicts' => false,
		'requires' => [],
		'section' => 'skins',
	],
	'metrolook' => [
		'name' => 'Metrolook',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Skin:Metrolook',
		'var' => 'wmgUseMetrolook',
		'conflicts' => false,
		'requires' => [],
		'section' => 'skins',
	],
	'dusktodawn' => [
		'name' => 'Dusk To Dawn',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Skin:DuskToDawn',
		'var' => 'wmgUseDuskToDawn',
		'conflicts' => false,
		'requires' => [],
		'section' => 'skins',
	],
	'citizen' => [
		'name' => 'Citizen',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Skin:Citizen',
		'var' => 'wmgUseCitizen',
		'conflicts' => false,
		'requires' => [],
		'section' => 'skins',
	],
	'cosmos' => [
		'name' => 'Cosmos',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Skin:Cosmos',
		'var' => 'wmgUseCosmos',
		'conflicts' => false,
		'requires' => [],
		'section' => 'skins',
	],
	'gamepress' => [
		'name' => 'Gamepress',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Skin:Gamepress',
		'var' => 'wmgUseGamepress',
		'conflicts' => false,
		'requires' => [],
		'section' => 'skins',
	],
	'minervaneue' => [
		'name' => 'MinervaNeue',
		'linkPage' => 'https://www.mediawiki.org/wiki/Special:MyLanguage/Skin:Minerva_Neue',
		'var' => 'wmgUseMinervaNeue',
		'conflicts' => false,
		'requires' => [],
		'section' => 'skins',
	],
];
