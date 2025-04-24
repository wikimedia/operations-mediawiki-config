<?php
/**
 * This is the 404-handler for Apache (see README).
 * It is typically served from wiki domains for urls outside
 * the scope of MediaWiki. For example:
 *
 * - https://en.wikipedia.org/Example
 * - https://en.wikipedia.org/w/Example
 *
 * Compared to 404.html, which is used on domains that do not host MediaWiki at the root,
 * this one recommends corrections for common URL mistakes (like a missing wiki/ prefix)
 * and uses a project-specific favicon. Please keep them in sync apart from these differences.
 */
header( 'Content-Type: text/html; charset=utf-8' );
header( 'Cache-Control: s-maxage=2678400, max-age=2678400' );
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta charset="utf-8">
<title>Not Found</title>
<link rel="shortcut icon" href="/favicon.ico">
<style>
* { margin: 0; padding: 0; }
body { background: #fff; color: #202122; font: 0.938em/1.6 sans-serif; }
.content { margin: 7% auto 0; padding: 2em 1em 1em; max-width: 640px; }
.footer { clear: both; margin-top: 14%; border-top: 1px solid #e5e5e5; background: #f9f9f9; padding: 2em 0; font-size: 0.8em; text-align: center; }
img { float: left; margin: 0 2em 2em 0; }
a img { border: 0; }
h1 { margin-top: 1em; font-size: 1.2em; }
p { margin: 0.7em 0 1em 0; }
a { color: #36c; text-decoration: none; }
a:hover { text-decoration: underline; }
em { color: #72777d; font-style: normal; }
@media (prefers-color-scheme: dark) {
  body { background: transparent; color: #dfdedd; }
  a { color: #9e9eff; }
  em { color: #8d8882; }
  #logo { filter: invert(1) hue-rotate(180deg); }
}
</style>
<meta name="color-scheme" content="light dark">
<div class="content" role="main">
<a href="https://www.wikimedia.org"><img id="logo" src="https://www.wikimedia.org/static/images/wmf.png" srcset="https://www.wikimedia.org/static/images/wmf-2x.png 2x" alt=Wikimedia width=135 height=135></a>
<h1>Page not found</h1>
<?php
$path = $_SERVER['REQUEST_URI'];
$encUrl = htmlspecialchars( $path );
echo "<p><em>$encUrl</em></p>\n";
?>
<p>We could not find the above page on our servers.</p>
<?php
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
echo "<p><b>Did you mean: <a href=\"$encTarget\">$encTarget</a></b></p>\n";
?>
<p>Alternatively, you can visit the <a href="/">Main Page</a> or read <a href="https://en.wikipedia.org/wiki/HTTP_404" title="Wikipedia: HTTP 404">more information</a> about this type of error.</p>
</div>
</html>
<!-- 404.php -->
