<?php

global $wgContentNamespaces, $wgDefaultRobotPolicy;

$wgConfigCentreNamespacesAdditional = [
	'wgExtraSignatureNamespaces' => [
		'name' => 'Enable "Signature" button on the edit toolbar under both main and talk pages?',
		'from' => 'mediawiki',
		'type' => 'check',
		'main' => true,
		'talk' => false,
		'excluded' => [],
		'default' => false,
	],
	'wgCapitalLinkOverrides' => [
		'name' => 'Force the first letter of links to capitals.',
		'from' => 'mediawiki',
		'type' => 'vestyle',
		'main' => true,
		'talk' => false,
		'excluded' => [
			2,
			8,
		],
		'default' => false,
		'help' => 'Overrides <code>$wgCapitalLinks</code> for this namespace. Warning: This may break your existing wiki links.',
	],
	'wgNoFollowNsExceptions' => [
		'name' => 'Enable if the rel="nofollow" attribute should not be used for external links in this namespace, even if $wgNoFollowLinks is enabled.',
		'from' => 'mediawiki',
		'type' => 'check',
		'main' => true,
		'talk' => true,
		'excluded' => [],
		'default' => false,
		'help' => '',
	],
	'wgCosmosRailDisabledNamespaces' => [
		'name' => 'Disable Cosmos side rail in this namespace?',
		'from' => 'c1520f1ee2352a24d4f980ce6a6a92f559fd0ee6',
		'type' => 'check',
		'main' => true,
		'talk' => true,
		'excluded' => [],
		'default' => [
			-1 => true,
			8 => true,
			9 => true,
			'default' => false,
		],
		'help' => '',
	],
	'wgNamespaceRobotPolicies' => [
		'name' => 'What should the robot policy for this namespace be?',
		'from' => 'mediawiki',
		'type' => 'select',
		'main' => true,
		'talk' => true,
		'excluded' => [],
		'options' => [
			'index' => 'index',
			'follow' => 'follow',
			'nofollow' => 'nofollow',
			'noindex' => 'noindex',
			'index,follow' => 'index,follow',
			'index,nofollow' => 'index,nofollow',
			'noindex,follow' => 'noindex,follow',
			'noindex,nofollow' => 'noindex,nofollow',
		],
		'default' => [
			NS_SPECIAL => 'noindex',
			'default' => $wgDefaultRobotPolicy,
		],
		'help' => 'Overrides <code>$wgDefaultRobotPolicy</code> for this namespace.',
	],
	'wgExemptFromUserRobotsControl' => [
		'name' => 'Exempt from user robots control?',
		'from' => 'mediawiki',
		'type' => 'check',
		'main' => true,
		'talk' => true,
		'excluded' => [],
		'default' => array_merge(
			array_fill_keys( $wgContentNamespaces, true ),
			[ 'default' => false ]
		),
		'help' => 'If this is enabled, the <code><nowiki>__INDEX__</nowiki></code> and <code><nowiki>__NOINDEX__</nowiki></code> magic words will not function in this namespace.',
	],
	'wgCommentStreamsAllowedNamespaces' => [
		'name' => 'Can comments appear in this namespace?',
		'from' => '061931f375b80b78965d623a8fb992780302ae40',
		'type' => 'check',
		'main' => true,
		'talk' => false,
		'excluded' => [],
		'default' => array_merge(
			array_fill_keys( $wgContentNamespaces, true ),
			[ 'default' => false ]
		),
		'help' => '',
	],
	'wgCodeMirrorLineNumberingNamespaces' => [
		'name' => 'Enable CodeMirror in this namespace?',
		'from' => '9ede905c79b014f6a89cf7a5e16fa2dbdfa3c700',
		'type' => 'check',
		'main' => true,
		'talk' => true,
		'excluded' => [],
		'default' => true,
		'help' => '',
	],
	'wgVisualEditorAvailableNamespaces' => [
		'name' => 'Enable VisualEditor in this namespace?',
		'from' => 'af1c23e0ce05ef367eea551dd99847bce85dd7bf',
		'type' => 'vestyle',
		'main' => true,
		'talk' => true,
		'excluded' => [],
		'default' => [
			NS_CATEGORY => true,
			NS_FILE => true,
			NS_MAIN => true,
			NS_USER => true,
			'default' => false,
		],
		'help' => '',
	],
	'wgTemplateStylesNamespaces' => [
		'name' => 'Can TemplateStyles be used in this namespace?',
		'from' => '94f95eb5054cab7497ead8b0133c04b4dab84a88',
		'type' => 'vestyle',
		'main' => true,
		'talk' => true,
		'excluded' => [],
		'default' => [
			10 => true,
			'default' => false,
		],
		'help' => '',
	],
	'wgARENamespaces' => [
		'name' => 'Enable Article Ratings in this namespace?',
		'from' => 'da5ebe6050b0686d9404f2883e671c2f3afefc56',
		'type' => 'check',
		'main' => true,
		'talk' => false,
		'excluded' => [],
		'default' => array_merge(
			array_fill_keys( $wgContentNamespaces, true ),
			[ 'default' => false ]
		),
		'help' => '',
	]
];