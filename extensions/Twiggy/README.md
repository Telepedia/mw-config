# Twiggy

This extension adds the Twig Template Engine to MediaWiki. This extension Does nothing on its own and is meant to be a dependency of other extensions.

## Install
1. Add the extension into `extensions/Twiggy` in your MediaWiki installation.
2. Add the Twiggy `composer.json` to `composer.local.json`:
```json
{
	"extra": {
		"merge-plugin": {
			"include": [
				...,
				"extensions/Twiggy/composer.json"
			]
		}
	}
}
```
3. Run `composer update`.
4. Add this code block to the bottom of the LocalSettings.php file:
```php
wfLoadExtension('Twiggy');
```

## Usage
1. Add `Twiggy` as a required extension in `extension.json`:
```json
{
	...
	"requires": {
		"extensions": {
			"Twiggy": ">= 0.0.1"
		},
		"MediaWiki": ">= 1.31.0"
	}
	...
}

```
1. Add a hook for `SpecialPageBeforeExecute`:
```json
{
	"Hooks": {
		"SpecialPageBeforeExecute": "MyExtensionHooks::onSpecialPageBeforeExecute"
	}
}
```

2. Define the hook to register the template location:
```php
public static function onSpecialPageBeforeExecute( SpecialPage $special, $subPage ) {
	$twig = MediaWikiServices::getInstance()->getService( 'TwiggyService' );
	$twig->setTemplateLocation('MyExtensionName', __DIR__ . '/../resources/templates');
}
```
3. Example Special Page Usage:
```php
public function execute($subpage) {
	...
	$twig = MediaWikiServices::getInstance()->getService( 'TwiggyService' );
	$template = $twig->load('@MyExtensionName/template.twig');
	$this->output->addHtml($template->render(['title' => $title, 'types' => $types]));
}
```
4. Example Template `template.twig`
```twig
<h1>{{ wfMessage('title_language_key', 'plain') }}</h1>
<ul>
{% for id, type in types %}
	<li id="type_{{ id }}>{{ type.title }}</li>
{% endfor %}
</ul>
```
## wfMessage in Twig
Twiggy has a pass-through function for `wfMessage()` support. In templates all arrow methods are converted to dot syntax.

```
{{ wfMessage(string $msg).plain() }}
```
