<?php
header( 'Content-Type: text/html; charset=utf-8' );

# $_SERVER['REQUEST_URI'] has two different definitions depending on PHP version
if ( preg_match( '!^([a-z]*://)([a-z.]*)(/.*)$!', $_SERVER['REQUEST_URI'], $matches ) ) {
	$prot = $matches[1];
	$serv = $matches[2];
	$loc = $matches[3];
} else {
	$prot = "http://";
	$serv = strlen($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] :
	$_SERVER['SERVER_NAME'];
	$loc = $_SERVER["REQUEST_URI"];
}
# Fix protocol if needed
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
	$prot = "https://";
}

$encUrl = htmlspecialchars( $prot . $serv . $loc );
header( "Cache-Control: s-maxage=2678400, max-age=2678400");
header( "X-Wikimedia-Debug: prot=$prot serv=$serv loc=$loc");
if( preg_match( "|(%2f)|i", $loc, $matches ) ||
    preg_match( "|^/upload/(.*)|i",$loc, $matches ) || preg_match("|^/style/(.*)|i",$loc, $matches ) ||
    preg_match( "|^/wiki/(.*)|i",$loc, $matches ) || preg_match("|^/w/(.*)|i",$loc, $matches ) ||
    preg_match( "|^/extensions/(.*)|i",$loc, $matches ) ) {
	$title = $matches[1];
	$details = "<p style=\"font-weight: bold;\">To check for \"$title\" on Wikipedia, see: 
                <a href=\"//en.wikipedia.org/wiki/$title\" title=\"Wikipedia:$title\">
                //en.wikipedia.org/wiki/$title</a></p>";
} else {
	if( in_array( $loc, array( '/Broccoli',
                               '/Romanesco',
                               '/Mandelbrot_set',
                               '/Mandelbrotmenge' ) ) ) {
		# HACKHACKHACK
		# Special case for broken URLs which somebody
		# put into a print ad. Why??!!?!??!?!?!?!
		if (headers_sent()) return false;
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: ".$prot.$serv."/wiki".$loc);
		header("X-Wikimedia-Debug: prot=$prot serv=$serv loc=$loc");
	}

	$target = $prot . $serv . "/wiki" . $loc;
	$encTarget = htmlspecialchars( $target );
	header( "Refresh: 5; url=$encTarget" );
	$details="<p><b>Did you mean to type <a href=$encTarget>$encTarget</a>?</b>
              You will be automatically redirected there in five seconds.</p>";
}
$base_404=<<<END
<html>
       <head>
               <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
               <title>Wikimedia page not found: $encUrl</title>
               <link rel="shortcut icon" href="/favicon.ico">
               <style type="text/css">
                       * {
                               font-family: 'Gill Sans', 'Gill Sans MT', sans-serif;
                       }
                       a:link, a:visited {
                               color: #005b90;
                       }
                       a:hover, a:active {
                               color: #900000;
                       }
                       body {
                               background-image:
url('//upload.wikimedia.org/wikipedia/commons/9/96/Errorbg.png');
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
                               background-image:
url('//upload.wikimedia.org/wikipedia/commons/thumb/8/81/Wikimedia-logo.svg/300px-Wikimedia-logo.svg.png');
                               background-position: top center;
                               background-repeat: no-repeat;
                               height: 340px;
                               width: 300px;
                       }
                       #divider {
                               display: block;
                               float: left;
                               background-image:
url('//upload.wikimedia.org/wikipedia/commons/9/97/Errorline.png');
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
                                       <p>Alternatively, you can visit the <a href="/">Main Page</a> or
read <a href="//en.wikipedia.org/wiki/HTTP_404" title="Wikipedia:
HTTP 404">more information</a> about this type of error.</p>
                                       <p style="font-size: smaller;">A project of the <a
href="//wikimediafoundation.org/wiki/Home" title="Wikimedia
Foundation">Wikimedia Foundation</a></p>
                               </div>
                       </div>
               </div>
       </body>
</html>
END;

print ($base_404);
