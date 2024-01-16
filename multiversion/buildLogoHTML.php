<?php
use Wikimedia\MWConfig\MWConfigCacheGenerator;

require_once __DIR__ . '/MWWikiversions.php';
require_once __DIR__ . '/MWConfigCacheGenerator.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../tests/data/MWDefines.php';
require_once __DIR__ . '/../tests/data/SiteConfiguration.php';
require_once __DIR__ . '/../wmf-config/InitialiseSettings.php';

global $wmgLogosPath;

$wmgLogosPath = '../';

function wmfCheckMissingLogosAndGenerateHTML() {
	global $wmgLogosPath;
	$prodWikis = array_keys( MWWikiversions::readWikiVersionsFile( 'wikiversions.json' ) );
	$textareaVal = '';
	foreach ( $prodWikis as $dbname ) {
		$textareaVal .= $dbname . "\n";
	}
	$html = <<<HEREDOC
<!DOCTYPE HTML>
<html>
<head>
<base target="_blank">
<title>All Wikimedia deployed logos</title>
<style>
body {
	display: flex;
    flex-wrap: wrap;
    column-gap: 50px;
	row-gap: 50px;
}
.mw-logo {
    display: flex;
    align-items: center;
	width: 500px;
	position: relative;
}
.mw-logo sup {
	position: absolute;
    right: 0;
    top: 0;
}
.mw-logo-container > * {
	display: block;
}
.mw-logo-tagline {
	margin: 5px auto 0;
}
.mw-skin-vector .mw-logo-1x {
	border: solid 1px #333;
}

.mw-skin-vector-2022 .mw-logo-modern {
	border: solid 1px #333;
}
.mw-logo-variants {
	display: flex;
}

</style>
<script>
</script>
</head>
<body>
To filter paste in here:<br>
<textarea id="filter" disabled>
$textareaVal
</textarea>
HEREDOC;

	$conf = new SiteConfiguration();
	$conf->suffixes = MWMultiVersion::SUFFIXES;
	$conf->wikis = $wikis = MWWikiversions::readDbListFile( 'all' );
	$conf->settings = MWConfigCacheGenerator::getStaticConfig( 'production' );
	foreach ( $prodWikis as $dbname ) {
		$fullConfig = MWConfigCacheGenerator::getConfigGlobals( $dbname, $conf );
		$skin = $fullConfig['wgDefaultSkin'] ?? null;
		$wordmark = $fullConfig['wmgSiteLogoWordmark'] ?? null;
		$icon = $fullConfig['wmgSiteLogoIcon'] ?? null;
		$tagline = $fullConfig['wmgSiteLogoTagline'] ?? null;
		if ( !$tagline ) {
			$taglineHTML = '';
		} else {
			$taglineHTML = '<img class="mw-logo-tagline" src="' . $wmgLogosPath . $tagline['src'] . '" width="' . $tagline['width'] . '" height="' . $tagline['height'] . '">';
		}
		$wordmarkUrl = isset( $wordmark['src'] ) ? $wmgLogosPath . $wordmark['src'] : '';

		$w = $wordmark['width'] ?? 0;
		$h = $wordmark['height'] ?? 0;
		$wordmarkHTML = '<img class="mw-logo-wordmark" alt="' . $dbname . '" src="' . $wordmarkUrl . '" width="' . $w . '"' . 'height="' . $h . '">';

		$html .= '<a href="https:' . $fullConfig['wgServer'] . '" class="mw-logo-variants mw-skin-' . $skin . '" title="' . $dbname . '" id="db-' . $dbname . '">' .
			'<sup>' . $dbname . '</sup>' .
			'<img class="mw-logo-1x" src="' . $wmgLogosPath . $fullConfig['wmgSiteLogo1x'] . '" height="160">' .
			'<div class="mw-logo"><img class="mw-logo-icon" src="' . $wmgLogosPath . $icon . '" aria-hidden="true" height="50" width="50">' .
			'<span class="mw-logo-container">' .
				$wordmarkHTML . $taglineHTML .
			'</span></div>' .
		'</a>';
	}
	$html .= <<<HEREDOC
<script>
var f = document.getElementById('filter');
f.disabled = false;
f.addEventListener( 'input', function () {
	const all = this.value.split('\\n');
	document.querySelectorAll( '.mw-logo-variants' ).forEach( ( node ) => {
		node.style.display = 'none';
	} );
	all.filter((key) => key ).forEach((key) => {
		const node = document.getElementById( 'db-' + key.trim() );
		if ( node ) {
			node.style.display = '';
		}
	})
} );
</script>
HEREDOC;

	$html .= '</body></html>';
	file_put_contents( getcwd() . '/logos/index.html', $html );
}

wmfCheckMissingLogosAndGenerateHTML();
