<?php
/**
 * Handle "Missing wiki" HTTP response (either redirect or 404).
 *
 * To test the script locally:
 *
 * - Uncomment MISSING_PHP_TEST
 * - Run `php -S localhost:9412` from this directory.
 * - <http://localhost:9412/missing.php?host=aa.wikinews.org> (Incubator redirect)
 * - <http://localhost:9412/missing.php?host=nl.wikiversity.org> (404 Subdomain)
 * - <http://localhost:9412/missing.php?host=foo.example.org> (404 Generic)
 * - <http://localhost:9412/missing.php?host=auth.wikimedia.org> (404 Auth)
 * - <http://localhost:9412/missing.php?host=auth.wikimedia.beta.wmcloud.org> (404 Auth in Beta)
 * - <http://localhost:9412/missing.php?host=nl.m.wikipedia.org> (404 Mobile)
 * - <http://localhost:9412/missing.php?host=nl.wikiversity.org&title=wikt:foo> (lateral interwiki redirect)
 * - <http://localhost:9412/missing.php?host=nl.wikiversity.org&title=f:foo> (global interwiki redirect)
 * - <http://localhost:9412/missing.php?host=nl.wikiversity.org&title=w:foo> ("w:" redirect to Wikipedia is special-cased)
 * - <http://localhost:9412/missing.php?host=als.wikibooks.org (redirect to namespace in Wikipedia)
 * - <http://localhost:9412/missing.php?host=als.wikivoyage.org (redirect to subpage in Wikipedia)
 *
 * We redirect non-existing languages of Wikipedia, Wiktionary, Wikiquote,
 * Wikibooks, and Wikinews to the Wikimedia Incubator.
 *
 * Non-existing languages of Wikisource get redirected to Multilingual Wikisource.
 *
 * Non-existing languages of Wikiversity show an error page.
 *
 * Certain special cases where another project is hosted by the language's Wikipedia
 * get redirected to that Wikipedia.
 *
 * The WikimediaIncubator extension ensures a localised "welcome page"
 * adapted to the given project/language.
 *
 * These redirects are relied upon as part of the interwiki map
 * and language link databases, by allowing any two languages to
 * have a stable and canonical link between them, regardless of whether
 * it is still on Incubator, e.g. [[xyz:Page]] on en.wikipedia redirects
 * via missing.php to <https://incubator.wikimedia.org/wiki/Wp/xyz/Page>.
 *
 * @copyright Copyright 2011-2013, Danny B., SPQRobin, Tim Starling
 * @license GPL-2.0-or-later
 */

// define( 'MISSING_PHP_TEST', 1 );

/**
 * The main function
 */
function wmfHandleMissingWiki() {
	$projects = [
		'wikibooks'   => 'b',
		'wikinews'    => 'n',
		'wikipedia'   => 'p',
		'wikiquote'   => 'q',
		'wikisource'  => 's',
		'wikiversity' => 'v',
		'wikivoyage'  => 'y',
		'wiktionary'  => 't',
	];

	// List of projects that are handled as another namespace in the language's Wikipedia
	// Format: [project name => [language => [namespace, mainpage] ] ]
	$wikisAsNamespaces = [
		'wikibooks' => [
			'als' => [ 'Buech', 'Buech:Houptsyte' ],
			'bar' => [ 'Buach', 'Buach:Start' ],
			'pfl' => [ 'Buch', 'Buch:Hauptseite' ],
		],
		'wiktionary' => [
			'als' => [ 'Wort', 'Wort:Houptsyte' ],
			'bar' => [ 'Woat', 'Woat:Start' ],
			// zh-classical uses subpages in Project Namespace rather than a namespace
			'pfl' => [ 'Wort', 'Wort:Hauptseite' ],
			'sco' => [ 'Define', 'Define:Main Page' ],
		],
		'wikisource' => [
			'als' => [ 'Text', 'Text:Houptsyte' ],
			'bar' => [ 'Text', 'Text:Start' ],
			'frr' => [ 'Text', 'Text:Hoodsid' ],
			'pfl' => [ 'Text', 'Text:Hauptseite' ],
			'pdc' => [ 'Text', 'Text:Haaptblatt' ],
			'sw' => [ 'Wikichanzo', 'Wikichanzo:Mwanzo' ],
		],
		'wikiquote' => [
			'als' => [ 'Spruch', 'Spruch:Houptsyte' ],
			'bar' => [ 'Spruch', 'Spruch:Start' ],
			'pfl' => [ 'Spruch', 'Spruch:Hauptseite' ],
		],
		'wikinews' => [
			'als' => [ 'Nochricht', 'Nochricht:Dialäkt-Neuigkeite' ],
			'bar' => [ 'Nochricht', 'Nochricht:Start' ],
			'pfl' => [ 'Nochricht', 'Nochricht:Hauptseite' ],
			// Various Russian languages use Russian Wikinews instead. These aren't handled here
		],
	];

	// List of projects that are handled as a subpage of another page in the language's Wikipedia
	// Format: [project name => [language => basepage] ] ]
	$wikisAsSubpages = [
		'wiktionary' => [
			// Needs to be stated for both language codes since the lzh<-->zh-classical mapping
			// is done only for Wikipedia at the Apache layer
			'lzh' => '維基大典:維基爾雅',
			'zh-classical' => '維基大典:維基爾雅'
		],
		'wikinews' => [
			'nds' => 'Portal:Wikinews',
			'lzh' => '維基大典:世事',
			'zh-classical' => '維基大典:世事',
		],
		'wikivoyage' => [
			'als' => 'Buech:Raisefierer',
		]
	];

	[ $protocol, $host ] = wmfGetProtocolAndHost();

	if ( strpos( $host, '.m.' ) !== false ) {
		// Invalid request to mobile site, not rewritten by Varnish
		//
		// Output an error which indicates that a request for *.m.wik*.org was received

		// Disable caching, due to the suspicion that T49807 is caused by cache poisoning.
		header( 'Cache-Control: no-cache' );

		wmfShowErrorPage( [
			'logo' => 'https://www.wikimedia.org/static/images/wmf-2x.png',
			'title' => 'Internal error',
			'heading' => 'Internal error',
			'messageHtml' => '<p>This request to a mobile domain was routed to a MediaWiki server without host rewrite, and thus cannot be served here.</p>',
		] );
		return;
	}

	// Given $language.$project.org or $language.$project.beta.wmcloud.org
	$tmp = explode( '.', $host );
	$language = $project = $incubatorCode = false;
	if ( count( $tmp ) >= 3 ) {
		[ $language, $project, ] = $tmp;
		if ( isset( $_SERVER['PATH_INFO'] )
			&& preg_match( '!^/(.*)$!', $_SERVER['PATH_INFO'], $m ) ) {
			$page = $m[1];
		} elseif ( isset( $_SERVER['REQUEST_URI'] ) && preg_match( '!^/wiki/(.*)$!', $_SERVER['REQUEST_URI'], $m ) ) {
			// The "/wiki" rewrite rule does not set PATH_INFO, so check REQUEST_URI
			$page = $m[1];
		} else {
			// Fall back to title given as a query parameter, or the empty string (Main Page) otherwise
			$page = $_GET['title'] ?? '';
		}
		$incubatorCode = $projects[$project] ?? null;
	}

	if ( $language === 'auth' ) {
		// E.g. https://auth.wikimedia.org/foowiki/w/index.php
		// or https://auth.wikimedia.beta.wmcloud.org/foo/wiki/
		wmfShowErrorPage( [
			'logo' => 'https://www.wikimedia.org/static/images/wmf-2x.png',
			'title' => 'No wiki found',
			'heading' => 'No wiki found',
			'messageHtml' => '<p>Sorry, we were not able to work out what wiki you were trying to view.
				Please specify a valid wiki ID in the path.</p>',
		] );
		return;
	}

	if ( !$incubatorCode ) {
		// Show a generic error message which does not refer to any particular project.
		wmfShowErrorPage( [
			'logo' => 'https://www.wikimedia.org/static/images/wmf-2x.png',
			'title' => 'No wiki found',
			'heading' => 'No wiki found',
			'messageHtml' => '<p>Sorry, we were not able to work out what wiki you were trying to view.
				Please specify a valid Host header.</p>',
		] );
		return;
	}

	if ( strpos( $page, ':' ) !== false ) {
		# Open the interwiki file to see if we have an interwiki prefix
		$db = null;
		try {
			$db = include __DIR__ . '/../wmf-config/interwiki.php';
		} catch ( Exception $e ) {
		}

		$prefix = strtok( $page, ':' );
		if ( $db ) {
			$projectKey = ( $project === 'wikipedia' ? 'wiki' : $project );

			# Try looking for lateral links (q: voy: ...)
			# TODO: Make this work if the language doesn't have a Wikipedia
			# (occasionally langcom approves some other project first)
			$row = $db[ "{$language}wiki:$prefix" ] ?? null;

			# Also try interlanguage links
			if ( !$row && isset( $db[ "_$projectKey:$prefix" ] ) ) {
				$row = $db[ "_$projectKey:$prefix" ];
			}
			# And global links (most of which aren't local but this makes aa.wikinews.org/wiki/f: redirect to
			# Wikifunctions properly for example)
			if ( !$row && isset( $db[ "__global:$prefix" ] ) ) {
				$row = $db[ "__global:$prefix" ];
			}
			# Special-case "w:" since the above won't find it as "_wikinews:w" doesn't exist
			# in the interwiki map (instead there's "aawikinews:w" for every language) which
			# the code above can't find since it looks for Wikipedias only.
			if ( !$row && $prefix === "w" ) {
				$row = "1 https://$language.wikipedia.org/wiki/$1";
			}

			if ( $row ) {
				[ $iw_local, $iw_url ] = explode( ' ', $row );
				if ( $iw_local ) {
					# Redirect to the appropriate WMF wiki
					# strtok gives us the remainder of the page title after the interwiki prefix
					wmfShowRedirect( str_replace( '$1', strtok( '' ), $iw_url ) );
					return;
				}
			}
			# We don't have an interwiki link, keep going and see what else we could have
		}
	}
	if ( isset( $wikisAsNamespaces[ $project ][ $language ] ) ) {
		// Some languages include the other projects in the Wikipedia as namespaces,
		// like Scots Wiktionary. Redirect there instead of Incubator
		[ $namespace, $root ] = $wikisAsNamespaces[ $project ][ $language ];
		if ( $page === '' ) {
			$page = $root;
		} else {
			$page = "$namespace:$page";
		}
		wmfShowRedirect( "$protocol://$language.wikipedia.org/wiki/$page" );
	} elseif ( isset( $wikisAsSubpages[$project][$language] ) ) {
		$destTitle = $wikisAsSubpages[ $project ][ $language ];
		if ( $page !== '' ) {
			$destTitle = "$destTitle/$page";
		}
		wmfShowRedirect( "$protocol://$language.wikipedia.org/wiki/$destTitle" );
	} elseif ( $project === 'wikisource' ) {
		# Wikisource should redirect to the multilingual wikisource
		if ( $page === '' ) {
			$page = "Main_Page/$language";
		}
		wmfShowRedirect( $protocol . '://wikisource.org/wiki/' . $page );
	} elseif ( $project === 'wikiversity' ) {
		# Wikiversity gives an error page

		// Output an error message explaining that no wiki for the given subdomain exists.
		// This has been superseded by an Incubator redirect for all projects other than
		// Wikiversity.
		$escLanguage = htmlspecialchars( $language );
		wmfShowErrorPage( [
			'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Wikiversity-logo.svg/300px-Wikiversity-logo.svg.png',
			'favicon' => 'https://beta.wikiversity.org/favicon.ico',
			'title' => 'This wiki does not exist',
			'heading' => 'Welcome to Wikiversity',
			'messageHtml' => ''
				. '<p>Unfortunately, Wikiversity in "' . $escLanguage . '" does not exist on its own domain yet. You may like to visit <a href="https://beta.wikiversity.org">Beta Wikiversity</a> and start or improve "' . $escLanguage . '" pages there.</p>'
				. '<p>If you would like to request that this wiki be created, see the <a href="https://meta.wikimedia.org/wiki/Requests_for_new_languages">Requests&nbsp;for new languages</a> on Meta-Wiki.</p>',
		] );
	} else {
		if ( $language === 'zh-min-nan' ) {
			// T86915
			$language = 'nan';
		}
		# Redirect to incubator
		$incubatorBase = 'incubator.wikimedia.org/wiki/';
		$location = $protocol . '://' . $incubatorBase . 'W' . $incubatorCode . '/' . urlencode( $language );
		# Go to the page if specified (look out for slashes), otherwise go to
		# the main page Wx/xyz?goto=mainpage (WikimediaIncubator extension takes care of that)
		$location .= $page && $page !== '/' ? '/' . $page :
			'?goto=mainpage' . ( isset( $_GET['uselang'] ) ? '&uselang=' . urlencode( $_GET['uselang'] ) : '' );

		wmfShowRedirect( $location );
	}
}

/**
 * Obtaining the full self URL
 * @return string[] Actual URL except for fragment part
 */
function wmfGetProtocolAndHost() {
	if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
	if ( defined( 'MISSING_PHP_TEST' ) && isset( $_GET['host'] ) ) {
		$host = $_GET['host'];
	} else {
		$host = $_SERVER['HTTP_HOST'];
	}
	$host = strtolower( $host );
	return [ $protocol, $host ];
}

/**
 * @param array{logo:string,heading:string,content:string} $info
 * @return string HTML
 */
function wmfShowErrorPage( array $info ) {
	http_response_code( 404 );
	header( 'Content-Type: text/html; charset=utf-8' );

	$titleHtml = htmlspecialchars( $info['title'] );
	$headingHtml = htmlspecialchars( $info['heading'] );
	$messageHtml = $info['messageHtml'];
	$faviconHtml = sprintf( '<link rel="shortcut icon" href="%s" />',
			$info['favicon'] ?? '//foundation.wikimedia.org/favicon.ico'
	);
	echo <<<HTML
<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta charset="utf-8">
<title>{$titleHtml}</title>
{$faviconHtml}
<meta name="color-scheme" content="light dark">
<style>
* { margin: 0; padding: 0; }
body { background: #fff; color: #202122; font: 0.938em/1.6 sans-serif; }
.content { margin: 7% auto 0; padding: 2em 1em 1em; max-width: 640px; }
.footer { clear: both; margin-top: 14%; border-top: 1px solid #e5e5e5; background: #f9f9f9; padding: 2em 0; font-size: 0.8em; text-align: center; }
img { float: left; margin: 0 2em 5em 0; }
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
<div class="content" role="main">
<a href="https://www.wikimedia.org"><img id="logo" src="{$info['logo']}" alt=Logo width=135></a>
<h1>{$headingHtml}</h1>
{$messageHtml}
</div>
<div class="footer">
<p>A <a href="https://www.wikimedia.org">Wikimedia</a> project.</p>
</div>
</html>
<!-- missing.php -->
HTML;
}

/**
 * Show a redirect, including "short hypertext note" as suggested by RFC 2616 section 10.3.2.
 * @param string $url
 */
function wmfShowRedirect( $url ) {
	header( 'Location: ' . $url );
	header( 'Content-Type: text/html; charset=utf-8' );
	$escUrl = htmlspecialchars( $url );
	echo <<<HTML
<!DOCTYPE html>
<html>
<body>
<a href="$escUrl">The document has moved.</a>
</body>
</html>
<!-- missing.php -->
HTML;
}

wmfHandleMissingWiki();
