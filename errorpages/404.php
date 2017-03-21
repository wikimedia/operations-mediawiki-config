<?php
/**
 * This is the 404-handler for Apache and HHVM (see README).
 * It is typically served from wiki domains for urls outside
 * the scope of MediaWiki. For example:
 *
 * - https://en.wikipedia.org/Example
 * - https://en.wikipedia.org/w/Example
 *
 * The response is similar to errorpages/404.html, except that it uses a
 * project-specific favicon (instead of generic wmf.ico).
 */
header( 'Content-Type: text/html; charset=utf-8' );
header( 'Cache-Control: s-maxage=2678400, max-age=2678400' );

$path = $_SERVER['REQUEST_URI'];
$encUrl = htmlspecialchars( $path );

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
$outputHtml=<<<END
<!DOCTYPE html>
<html lang=en>
<meta charset="utf-8">
<title>Not Found</title>
<link rel="shortcut icon" href="/favicon.ico">
<style>
* { margin: 0; padding: 0; }
body { background: #fff; margin: 7% auto 0; padding: 2em 1em 1em; font: 15px/1.6 sans-serif; color: #333; max-width: 640px; }
img { float: left; margin: 0 2em 2em 0; }
a img { border: 0; }
h1 { margin-top: 1em; font-size: 1.2em; }
p { margin: 0.7em 0 1em 0; }
a { color: #0645AD; text-decoration: none; }
a:hover { text-decoration: underline; }
em { font-style: normal; color: #777; }
</style>
<a href="https://www.wikimedia.org"><img src="https://www.wikimedia.org/static/images/wmf.png" srcset="https://www.wikimedia.org/static/images/wmf-2x.png 2x" alt=Wikimedia width=135 height=135></a>
<h1>File not found</h1>
<p><em>$encUrl</em></p>
<p>We could not find the above page on our servers.</p>
<p><b>Did you mean: <a href="$encTarget">$encTarget</a></b></p>
<div style="clear:both;"></div>
<p>Alternatively, you can visit the <a href="/">Main Page</a> or read <a href="https://en.wikipedia.org/wiki/HTTP_404" title="Wikipedia: HTTP 404">more information</a> about this type of error.</p>
</html>
END;

print $outputHtml;
