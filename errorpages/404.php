<?php
header( 'Content-Type: text/html; charset=utf-8' );
header( 'Cache-Control: s-maxage=2678400, max-age=2678400' );

$prot = ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )
	? 'https://'
	: 'http://';
$serverName = strlen( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
$path = $_SERVER['REQUEST_URI'];

$encUrl = htmlspecialchars( $prot . $serverName . $path );

if( preg_match( '/(%2f)/i', $path, $matches )
	|| preg_match( '/^\/(?:upload|style|wiki|w|extensions)\/(.*)/i', $path, $matches )
) {
	// "/w/Foo" -> "/wiki/Foo"
	$target = '/wiki/' . $matches[1];
} else {
	// "/Foo" -> "/wiki/Foo"
	$target = '/wiki' . $path;
}
$encTarget = htmlspecialchars( $target );
$detailsHtml ="<p><b>Did you mean: <a href=\"$encTarget\">$encTarget</a></b></p>";
$outputHtml=<<<END
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Wikimedia page not found: $encUrl</title>
			<link rel="shortcut icon" href="/favicon.ico">
			<style>
				body {
					background-image: url('//upload.wikimedia.org/wikipedia/commons/9/96/Errorbg.png');
					background-repeat: repeat-x;
					background-color: white;
					height: 100%;
					margin: 0;
					padding: 0;
					font-family: 'Gill Sans MT', 'Gill Sans', sans-serif;
					color: #484848;
				}
				a:link, a:visited {
					color: #005b90;
				}
				a:hover, a:active {
					color: #900000;
				}
				h1 {
					color: black;
					margin: 0px;
				}
				h2 {
					color: #484848;
					padding: 0px;
					margin: 0px;
				}
				p {
					margin-top: 10px;
					margin-bottom: 0px;
					padding-bottom: 0.5em;
				}
				#center {
					position: absolute;
					top: 50%;
					width: 100%;
					height: 1px;
					overflow: visible;
				}
				#main {
					position: absolute;
					left: 50%;
					width: 720px;
					margin-left: -360px;
					height: 340px;
					top: -170px
				}
				#logo {
					display: block;
					float: left;
					background-image: url('//upload.wikimedia.org/wikipedia/commons/thumb/8/81/Wikimedia-logo.svg/300px-Wikimedia-logo.svg.png');
					background-position: top center;
					background-repeat: no-repeat;
					height: 340px;
					width: 300px;
				}
				#divider {
					display: block;
					float: left;
					background-image: url('//upload.wikimedia.org/wikipedia/commons/9/97/Errorline.png');
					background-position: center center;
					background-repeat: no-repeat;
					height: 340px;
					width: 1px;
					margin-left: 10px;
					margin-right: 10px;
				}
				#message {
					padding-left: 10px;
					float: left;
					display: block;
					height: 340px;
					width: 370px;
				}
		</style>
	</head>
	<body>
		<div id="center">
			<div id="main">
				<div id="logo"></div>
				<div id="divider"></div>
				<div id="message">
					<h1>Error</h1>
					<h2>404 – File not found</h2>
					<p><em>$encUrl</em></p>
					<p>We could not find the above page on our servers.</p>
					$detailsHtml
					<p>Alternatively, you can visit the <a href="/">Main Page</a> or read <a href="//en.wikipedia.org/wiki/HTTP_404" title="Wikipedia: HTTP 404">more information</a> about this type of error.</p>
					<p style="font-size: smaller;">A project of the <a href="//wikimediafoundation.org/wiki/Home" title="WikimediaFoundation">Wikimedia Foundation</a></p>
				</div>
			</div>
		</div>
	</body>
</html>
END;

print $outputHtml;
