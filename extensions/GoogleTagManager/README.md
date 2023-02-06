# MediaWiki Google Tag Manager Extension

[![CircleCI](https://circleci.com/gh/Hutchy68/GoogleTagManager.svg?style=shield)](https://circleci.com/gh/Hutchy68/GoogleTagManager)

The GoogleTagManager extension integrates a [Google Tag Manager](https://marketingplatform.google.com/about/tag-manager/) container, e.g. containing either a Universal Analytics and Google Analytics 4 tracking code, into each mediawiki page.

## Download

First, copy the Pivot source files into your MediaWiki extension directory. You can either download the files and extract them from:

https://github.com/Hutchy68/GoogleTagManager/archive/master.zip

You should extract the contents into a folder named `GoogleTagManager` in your extension directory.

Alternatively, you can use git to clone the repository, which makes it very easy to update the code, using:

`git clone https://github.com/Hutchy68/GoogleTagManager.git`

Currently, `master` is the branch to use. After that, you can issue `git pull` to update the code at anytime.

## Setup

Once the extension is in your `/extension` directory from either method above:

Add to your `LocalSetting.php` file and set you Google Tag Manager ID for your Tag
```
wfLoadExtension( 'GoogleTagManager' );
// Replace GTM-XXXXXX with your Google Tag Manager container ID
$wgGoogleTagManagerContainerID='GTM-XXXXXX';
```

### Alternative Setting

Cookie consent scripts usually have documentation on how to block the firing of a Google Tag without cookie consent. Others, allow or block the execution of a `<script>` which may add more cookies. These use a data type element attached to the `<script>`

Example `<script data-cookiescript="accepted" data-cookiecategory="performance">`

To set the data element(s) use 

`$wgGoogleTagManagerData = 'data-cookiescript="accepted" data-cookiecategory="performance"';` or if you use `"` make sure you escape the `"`'s needed.

`$wgGoogleTagManagerData = "data-cookiescript=\"accepted\" data-cookiecategory=\"performance\"";`
