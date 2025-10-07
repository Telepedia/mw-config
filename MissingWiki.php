<?php

header( 'Content-Type: text/html; charset=utf-8' );
header( 'Cache-Control: s-maxage=2678400, max-age=2678400' );

$path = $_SERVER['REQUEST_URI'];
$actual_link = 'https://' . $_SERVER['HTTP_HOST'] . $path;
$encUrl = htmlspecialchars( $path );
http_response_code( 410 );

echo <<<EOF
<!DOCTYPE html">
<html lang="en">
<head>
<link rel="icon" type="image/x-icon" href="https://meta.telepedia.net/images/metawiki/1/18/Telepedia_Favicon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>
Wiki Not Found
</title>
<style type="text/css">
@import url('https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&family=Gabarito:wght@400..900&display=swap');
* {
    font-family: 'Figtree', 'Gill Sans MT', sans-serif;
}
.missing-title {
	font-family: 'Gabarito', 'Gill Sans MT', sans-serif;
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

.tp-button {
	padding: 7px 18px;
	background: #d9e111;
	border: 0;
	width: 100%;
	margin-top: 1rem;
	cursor: pointer;
	font-weight: bold;
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
<link rel="shortcut icon" href="https://static.telepedia.net/metawiki/1/18/Telepedia_Favicon.ico" />
</head>
<body>

<div id="center"><div id="main">


<div id="logo">
    <img src="https://static.telepedia.net/metawiki/f/f4/Wiki_Error.svg" />
</div>
<div id="divider">

</div>

<div id="message">
<h1 class="missing-title">ERROR</h1>
<h2>410 &ndash; Wiki Not found</h2>
<p style="font-style: italic">$actual_link</p>
<p>We couldn't find that wiki on our platform. Check the spelling and try again.</p>
<a href="https://meta.telepedia.net/wiki/Telepedia_Meta_Wiki">
	<button class="tp-button">To Meta Wiki  &rarr;</button>
</a>
</div>

</div></div>
</html>
EOF;
die( 1 );
