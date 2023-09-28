<?php
/**
 * To test the script locally, run:
 *
 *     cd docroot/noc$ php -S localhost:9412
 *
 * Then view <http://localhost:9412/>.
 */

use Wikimedia\MWConfig\MWConfigCacheGenerator;

// Verbose error reporting
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

require_once __DIR__ . '/../../multiversion/MWConfigCacheGenerator.php';
require_once __DIR__ . '/../../multiversion/MWWikiversions.php';
require_once __DIR__ . '/../../tests/data/MWDefines.php';
require_once __DIR__ . '/../../tests/data/SiteConfiguration.php';

// Based on $wgConf in CommonSettings.php
$wgConf = new SiteConfiguration();
$wgConf->suffixes = MWMultiVersion::SUFFIXES;
$wgConf->wikis = $wikis = MWWikiversions::readDbListFile( 'all' );
$wgConf->settings = MWConfigCacheGenerator::getStaticConfig();

$selected = $_GET['wiki'] ?? '';
$selected = in_array( $selected, $wikis ) ? $selected : 'enwiki';
$compare = $_GET['compare'] ?? '';
$compare = in_array( $compare, $wikis ) ? $compare : null;

$selectedGlobals = MWConfigCacheGenerator::getConfigGlobals(
	$selected,
	$wgConf
);
$selectedGlobals['* wikitags'] = MWMultiVersion::getTagsForWiki( $selected );
$selectedGlobals['* dblists'] = array_keys( array_filter(
	MWWikiversions::getAllDbListsForCLI(),
	static function ( array $list ) use ( $selected ) {
		return in_array( $selected, $list );
	}
) );
wmfAssertNoPrivateSettings( $selectedGlobals );
$isComparing = false;
if ( $compare !== null ) {
	$beforeGlobals = MWConfigCacheGenerator::getConfigGlobals(
		$compare,
		$wgConf
	);
	$beforeGlobals['* wikitags'] = MWMultiVersion::getTagsForWiki( $compare );
	$beforeGlobals['* dblists'] = array_keys( array_filter(
		MWWikiversions::getAllDbListsForCLI(),
		static function ( array $list ) use ( $compare ) {
			return in_array( $compare, $list );
		}
	) );
	wmfAssertNoPrivateSettings( $beforeGlobals );
	$allKeys = array_unique( array_merge( array_keys( $selectedGlobals ), array_keys( $beforeGlobals ) ) );
	$afterGlobals = $selectedGlobals;
	$selectedGlobals = [];
	foreach ( $allKeys as $key ) {
		$before = $beforeGlobals[$key] ?? null;
		$after = $afterGlobals[$key] ?? null;
		if ( $before === $after ) {
			continue;
		}

		$selectedGlobals[$key] = [
			'before' => $before,
			'after' => $after,
		];
	}
	unset( $beforeGlobals );
	unset( $afterGlobals );
	$isComparing = true;
}

function wmfAssertNoPrivateSettings( array $globals ) {
	foreach ( [
		// Spot-check for InitialiseSettings first
		// to confrim that the remainder checks work correctly
		'wgLanguageCode' => true,
		// Spot-check against private/ inclusion just in case
		'wgSecretKey' => false,
		'wgDBpassword' => false,
		'wgCaptchaSecret' => false,
		'wmgCaptchaSecret' => false,
	] as $key => $expected ) {
		if ( array_key_exists( $key, $globals ) !== $expected ) {
			http_response_code( 500 );
			print 'Unexpected settings data.';
			exit;
		}
	}
}

function wmfNocJsonText( $val ): string {
	return json_encode( $val, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
}

function wmfNocJsonForHtml( $val ): string {
	return htmlspecialchars( wmfNocJsonText( $val ) );
}

function wmfNocWrapLines( string $str, string $pre = '', string $post = '' ): string {
	return $pre . str_replace( "\n", "$post\n$pre", $str ) . $post;
}

// Disable moved line detection
//
// Value: 0.0 (max) - 1.0 (disabled)
ini_set( 'wikidiff2.moved_line_threshold', 1.0 );

// Disabled moved paragraph detection
//
// Value: Input upto N lines will be subject to moved paragraph detection
ini_set( 'wikidiff2.moved_paragraph_detection_cutoff', 0 );

// Inline differences
//
// Value: 0.0 (max) - 1.0 (disabled)
ini_set( 'wikidiff2.change_threshold', 1.0 );

function wmfNocDiff( $before, $after ): string {
	if ( is_array( $before ) && function_exists( 'wikidiff2_inline_diff' ) ) {
		$html = wikidiff2_inline_diff( wmfNocJsonText( $before ), wmfNocJsonText( $after ), 10 );
		return strtr( $html, [
			"<div class=\"mw-diff-inline-header\"><!-- LINES 1,1 --></div>\n" => '',
		] );
	} else {
		return wmfNocWrapLines( wmfNocJsonForHtml( $before ), '<del>- ', '</del>' ) . "\n"
			. wmfNocWrapLines( wmfNocJsonForHtml( $after ), '<ins>+ ', '</ins>' );
	}
}

function wmfNocViewWiki( array $globals, bool $isComparing ): array {
	$navHtml = '<li><a href="#">Top</a></li>';
	$preHtml = '';

	uksort( $globals, static function ( $a, $b ) {
		// Sort "wg*", "wmg*" and "groupOverrides" alongside each other.
		$a = preg_replace( '/^(wmg|wg)/', '', $a );
		$b = preg_replace( '/^(wmg|wg)/', '', $b );
		return strcasecmp( $a, $b );
	} );

	$lastPrefix = null;
	foreach ( $globals as $name => $val ) {
		$prefix = strtoupper( preg_replace( '/^(wmg|wg)/', '', $name )[0] );
		if ( $prefix !== $lastPrefix ) {
			$navHtml .= '<li><a href="#' . htmlspecialchars( $prefix ) . '">' . htmlspecialchars( $prefix ) . '</a></li>';
			$preHtml .= '<a name="' . htmlspecialchars( $prefix ) . '"></a>';
			$lastPrefix = $prefix;
		}
		$preHtml .= '<a href="#' . htmlspecialchars( $name ) . '" name="' . htmlspecialchars( $name ) . '"></a>'
			. htmlspecialchars( $name ) . ':';
		if ( !$isComparing ) {
			$preHtml .= ' ' . wmfNocJsonForHtml( $val );
		} else {
			$preHtml .= "\n" . wmfNocDiff( $val['before'], $val['after'] );
		}
		$preHtml .= "\n\n";
	}

	return [ 'nav' => $navHtml, 'pre' => $preHtml ];
}

$title = "Settings: " . ( $compare !== null ? "Compare $selected to $compare" : $selected );
$data = wmfNocViewWiki( $selectedGlobals, $isComparing );

$formatIsJson = ( $_GET['format'] ?? null ) === 'json';
if ( $formatIsJson ) {
	$data = [];
	foreach ( $selectedGlobals as $name => $val ) {
		$data[$name] = $isComparing ? $val['after'] : $val;
	}
	header( 'Content-Type: application/json; charset=utf-8' );
	echo json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	exit;
}

?><!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="utf-8">
	<title><?php echo htmlspecialchars( "$title – Wikimedia NOC" ); ?></title>
	<link rel="shortcut icon" href="/static/favicon/wmf.ico">
	<link rel="stylesheet" href="/css/base.css">
	<style>
	pre {
		background-color: #f8f9fa;
		border: 1px solid #a2a9b1;
		color: #000;
		padding: 1.2rem 2rem;
		white-space: pre-wrap;

		position: relative;
	}
	pre a[href] {
		display: inline-block;
		position: absolute;
		left: 0.4rem;
	}
	pre a[href]:after {
		content: '#';
	}
	pre .mw-diff-inline-header {
		display: none;
	}
	pre .mw-diff-inline-context,
	pre .mw-diff-inline-deleted,
	pre .mw-diff-inline-added,
	pre .mw-diff-inline-changed {
		display: inline;
		width: 100%;
	}
	pre .mw-diff-inline-context,
	pre .mw-diff-inline-added {
		display: inline-block;
		width: 70%;
		margin-left: 30%;
	}
	del {
		text-decoration: none;
		background: #feeec8;
	}
	ins {
		text-decoration: none;
		background: #d8ecff;
	}
	</style>
</head>
<body>
<header><div class="wm-container">
	<a role="banner" href="/" title="Visit the home page"><em>Wikimedia</em> NOC</a>
</div></header>

<main role="main"><div class="wm-container">

	<nav class="wm-site-nav"><ul class="wm-nav">
		<li><a href="/conf/">Config files</a></li>
		<li><a href="/db.php">Database config</a></li>
		<li><a href="/wiki.php" class="wm-nav-item-active">Wiki config</a>
			<ul><?php print $data['nav']; ?></ul>
		</li>
	</ul></nav>

	<article>
		<h1><?php print htmlspecialchars( $title ); ?></h1>
		<form action="./wiki.php" autocomplete="off">
			<label>Wiki:
				<select name="wiki"><?php
					foreach ( $wikis as $wiki ) {
						$selectedAttr = ( $wiki === $selected ? ' selected' : '' );
						print "<option{$selectedAttr}>" . htmlspecialchars( $wiki ) . '</option>';
					}
				?>
				</select>
			</label>
			<button type="submit" id="noc-submit-view">View</button>
			<label>Base:
				<select name="compare"><?php
					$selectedAttr = ( !$compare ? ' selected' : '' );
					print "<option{$selectedAttr}></option>";
					foreach ( $wikis as $wiki ) {
						$selectedAttr = ( $wiki === $compare ? ' selected' : '' );
						print "<option{$selectedAttr}>" . htmlspecialchars( $wiki ) . '</option>';
					}
				?>
				</select>
			</label>
			<button type="submit">Compare</button>
			<script>
			{
				// Clear comparison if submitting plain view
				const btnView = document.querySelector( '#noc-submit-view' );
				btnView.onclick = () => {
					const selectCompare = document.querySelector( '#noc-submit-view ~ label > select' );
					selectCompare.selectedIndex = -1;
				};

				// Clean up address bar
				const curr = new URL( location );
				if ( curr.searchParams.get( 'compare' ) === '' ) {
					curr.searchParams.delete( 'compare' );
					history.replaceState( null, '', curr );
				}
			}
			</script>
			<a class="noc-tab-action" href="./wiki.php?<?php echo htmlspecialchars( "wiki=$selected&format=json" ); ?>">View JSON</a>
		</form>
		<pre><?php
			if ( $isComparing ) {
				print '<del>' . htmlspecialchars( "--- before/$compare" ) . '</del>' . "\n";
				print '<ins>' . htmlspecialchars( "+++ after/$selected" ) . '</ins>' . "\n";
				print "\n";
			}
			print $data['pre'];
		?></pre>
	</article>

</div></main>

</body>
</html>
