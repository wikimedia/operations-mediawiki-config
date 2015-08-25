<?php
header( 'Content-Type: text/html; charset=utf-8' );

$prot = ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )
	? "https://"
	: "http://";
$serv = strlen( $_SERVER['HTTP_HOST'] )
	? $_SERVER['HTTP_HOST']
	: $_SERVER['SERVER_NAME'];
$loc = $_SERVER["REQUEST_URI"];

$encUrl = htmlspecialchars( $prot . $serv . $loc );
header( "Cache-Control: s-maxage=2678400, max-age=2678400");
header( "X-Wikimedia-Debug: prot=$prot serv=$serv loc=$loc");
if( preg_match( "/(%2f)/i", $loc, $matches )
	|| preg_match( "/^\/(?:upload|style|wiki|w|extensions)\/(.*)/i", $loc, $matches )
) {
	$title = htmlspecialchars( $matches[1] );
	$details = "<p style=\"font-weight: bold;\">To check for \"$title\" see: <a href=\"//$serv/wiki/$title\" title=\"$title\">$prot$serv/wiki/$title</a></p>";
} else {
	$target = $prot . $serv . "/wiki" . $loc;
	$encTarget = htmlspecialchars( $target );
	$details="<p><b>Did you mean to type <a href=$encTarget>$encTarget</a>?</b></p>";
}
$base_404=<<<END
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Wikimedia page not found: $encUrl</title>
			<link rel="shortcut icon" href="/favicon.ico">
			<style type="text/css">
				* {
					font-family: 'Gill Sans MT', 'Gill Sans', sans-serif;
				}
				a:link, a:visited {
					color: #005b90;
				}
				a:hover, a:active {
					color: #900000;
				}
				body {
					background-image: url('//upload.wikimedia.org/wikipedia/commons/9/96/Errorbg.png');
					background-repeat: repeat-x;
					background-color: white;
					color: #484848;
					margin: 0;
					padding: 0;
					height: 100%;
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
					<p style="font-style: italic;">$encUrl</p>
					<p>We could not find the above page on our servers.</p>
					$details
					<p>Alternatively, you can visit the <a href="/">Main Page</a> or read <a href="//en.wikipedia.org/wiki/HTTP_404" title="Wikipedia: HTTP 404">more information</a> about this type of error.</p>
					<p style="font-size: smaller;">A project of the <a href="//wikimediafoundation.org/wiki/Home" title="WikimediaFoundation">Wikimedia Foundation</a></p>
				</div>
			</div>
		</div>
	</body>
</html>
END;

print ($base_404);
