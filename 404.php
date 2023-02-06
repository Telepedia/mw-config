<?php

header( 'Content-Type: text/html; charset=utf-8' );
header( 'Cache-Control: s-maxage=2678400, max-age=2678400' );

$path = $_SERVER['REQUEST_URI'];
$actual_link = 'https://'.$_SERVER['HTTP_HOST'].$path;
$encUrl = htmlspecialchars( $path );

if ( preg_match( '/(%2f)/i', $path, $matches )
	|| preg_match( '/^\/(?:upload|style|wiki|w|extensions)\/(.*)/i', $path, $matches )
) {
	// "/w/Foo" -> "/wiki/Foo"
	$target = '/wiki/' . $matches[1];
} else {
	// "/Foo" -> "/wiki/Foo"
	$target = '/wiki' . $path;
}

$encTarget = htmlspecialchars( $target );

echo <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<link rel="icon" type="image/x-icon" href="https://meta.whiki.online/images/metawiki/6/64/Favicon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>
Page Not Found
</title>
<style type="text/css">
* {
    font-family: 'Gill Sans', 'Gill Sans MT', sans-serif;
}
a:link { 
    color: #005b90;
    }
a:visited { 
    color: #005b90;
    }
a:hover { 
    color: #900000;
    }
a:active { 
    color: #900000;
    }
body {
    background-color: white;
    color: #484848
}
h1 {
    color: black;
    margin: 0px;
}
h2 {
    color: #484848
    padding: 0px;
    margin: 0px;
}
p {
    margin-top: 10px;
    margin-bottom: 0px
}
#logo {
    display: block;
    float: left;
    height: 300px;
    width: 250px;
}
#logo > img:nth-child(1) {
    width: 200px;
    right: -20px;
}	   
#center {
    position: absolute;
    top: 50%;
    width: 100%;
    height: 1px;
    overflow: visible
}  
#main {
    position: absolute;
    left: 50%;
    width: 720px;
    margin-left: -360px;
    height: 300px;
    top: -150px
}
#divider {
    display: block;
    float: left;
    background-repeat: no-repeat;
    height: 300px;
    width: 2px;
}
#message {
    padding-left: 10px;
    float: left;
    display: block;
    height: 300px;
    width: 390px;
}
@media (prefers-color-scheme: dark) {
    body {
        background-color: #282828;
    }
    h1, p, h2 {
        color: white;
    }

    a:link, a:visited {
        color: cyan;
    }

}
</style>
<link rel="shortcut icon" href="/favicon.ico" />
</head>
<body>

<div id="center"><div id="main">


<div id="logo">
    <img src="https://meta.whiki.online/images/metawiki/b/b7/Whiki_Logo_No_text.png" />
</div>
<div id="divider">

</div>

<div id="message">
<h1>ERROR</h1>
<h2>404 &ndash; Page Not found</h2>
<p style="font-style: italic">$actual_link</p>
<p>We could not find the above page on our servers. Perhaps you mistyped it?</p>
<p><b>Did you mean: <a href="$encTarget">$encTarget</a>?</b></p>
<p>Alternatively, you can visit the <a href="/">main page</a> of this wiki.</p>
</div>

</div></div>
</html>
EOF;