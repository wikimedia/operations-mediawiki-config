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
 * this one recommends corrections for common URL mistakes (like a missing wiki/ prefix),
 * offers a search box (except on Wikipedia) and uses a project-specific favicon.
 * Please keep them in sync apart from these differences.
 */
header( 'Content-Type: text/html; charset=utf-8' );
header( 'Cache-Control: s-maxage=2678400, max-age=2678400' );

$isWikipedia = (bool)preg_match( '/(^|\.)wikipedia\.org$/', $_SERVER['HTTP_HOST'] ?? '' );
$path = $_SERVER['REQUEST_URI'];
$encUrl = htmlspecialchars( $path );
// There is no . neither / in the path. Also check the encoded version of slash
$isRedirect = strpbrk( substr( $path, 1 ), './' ) === false && !preg_match( '/(%2f)/i', $path );
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
$redirectDelay = 3;
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Not Found</title>
<link rel="shortcut icon" href="/favicon.ico">
<style>
* { margin: 0; padding: 0; }
body { background: #fff; font: 14px/22px system-ui, -apple-system, 'Segoe UI', Roboto, Lato, Helvetica, Arial, sans-serif; color: #202122; }
.content { box-sizing: border-box; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 32px; margin: 0 auto; padding: 32px; max-width: 640px; text-align: center; }
#logo { display: block; max-width: 100%; height: auto; }
a img { border: 0; }
.text { display: flex; flex-direction: column; gap: 8px; }
h1 { font-size: 18px; line-height: 28px; font-weight: bold; }
#countdown { font-size: 64px; line-height: 74px; font-weight: bold; }
a { color: #36c; text-decoration: none; }
a:hover { text-decoration: underline; }
em { font-style: normal; word-break: break-all; }
.search { display: flex; width: 100%; max-width: 348px; }
.search input { flex: 1; min-width: 0; height: 32px; box-sizing: border-box; padding: 4px 8px; border: 1px solid #a2a9b1; border-radius: 2px 0 0 2px; font: inherit; color: inherit; background: #fff; }
.search input:focus { outline: 0; border-color: #36c; box-shadow: inset 0 0 0 1px #36c; }
.search button { height: 32px; padding: 0 12px; border: 1px solid #a2a9b1; border-left: 0; border-radius: 0 2px 2px 0; background: #f8f9fa; font: inherit; font-weight: bold; color: #202122; cursor: pointer; }
.home { font-weight: bold; }
@media (prefers-color-scheme: dark) {
  body { background: #101418; color: #eaecf0; }
  a { color: #88a3e8; }
  .search input { background: #101418; border-color: #72777d; }
  .search button { background: #27292d; border-color: #72777d; color: #eaecf0; }
  #wmfLogo { filter: invert(1) hue-rotate(180deg); }
}
</style>
<meta name="color-scheme" content="light dark">
<?php
if ( $isRedirect ) {
	echo "<meta http-equiv=\"refresh\" content=\"$redirectDelay;url=$encTarget\" />\n";
}
?>
<div class="content" role="main">
<?php if ( $isRedirect ) { ?>
<div id="countdown" role="timer"><?php echo $redirectDelay; ?></div>
<script>
// Browsers start the meta refresh delay at the load event.
window.addEventListener( 'load', function () {
	var countdown = document.getElementById( 'countdown' ),
		seconds = Number( countdown.textContent ),
		timer = setInterval( function () {
			if ( --seconds > 0 ) {
				countdown.textContent = seconds;
			} else {
				clearInterval( timer );
			}
		}, 1000 );
} );
</script>
<?php } elseif ( $isWikipedia ) { ?>
<a id="logoContainer" href="https://www.wikimedia.org"><img id="logo" class="globe" src="https://www.wikimedia.org/static/images/errorpages/baby-globe-dreaming-250px.png" alt="Wikipedia" width="120" height="120"></a>
<?php } else { ?>
<a id="logoContainer" href="https://www.wikimedia.org"><img id="logo" src="https://www.wikimedia.org/static/images/footer/wikimedia.svg" alt="Wikimedia" width="120" height="120"></a>
<?php } ?>
<div class="text">
<h1>Page not found.</h1>
<p><em><?php echo $encUrl; ?></em> was not found on our servers.</p>
<?php if ( $isRedirect ) { ?>
<p>Redirecting to <a href="<?php echo $encTarget; ?>"><?php echo $encTarget; ?></a>…</p>
<?php } else { ?>
<p>Did you mean <a href="<?php echo $encTarget; ?>"><?php echo $encTarget; ?></a>?</p>
<?php } ?>
</div>
<?php if ( !$isRedirect ) { ?>
<?php if ( !$isWikipedia ) { ?>
<form class="search" action="/w/index.php" role="search">
<input type="search" name="search" placeholder="Search" aria-label="Search" autocomplete="off">
<button type="submit">Search</button>
</form>
<?php } ?>
<a class="home" href="/">Go back home</a>
<?php } ?>
<a href="https://wikimediafoundation.org/"><img id="wmfLogo" src="https://www.wikimedia.org/static/images/wmf.png" srcset="https://www.wikimedia.org/static/images/wmf-2x.png 2x" alt="Wikimedia Foundation" width="86" height="86"></a>
</div>
</html>
<!-- 404.php -->
