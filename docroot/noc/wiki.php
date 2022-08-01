<?php
/**
 * To test the script locally, run:
 *
 *     cd docroot/noc$ php -S localhost:9412
 *
 * Then view <http://localhost:9412/>.
 */

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
$wgConf->wikis = $wikis = MWWikiversions::readDbListFile( $wmgRealm === 'labs' ? 'all-labs' : 'all' );

$selected = $_GET['wiki'] ?? '';
$selected = in_array( $selected, $wikis ) ? $selected : 'enwiki';
$compare = $_GET['compare'] ?? '';
$compare = in_array( $compare, $wikis ) ? $compare : null;

$selectedGlobals = Wikimedia\MWConfig\MWConfigCacheGenerator::getConfigGlobals(
	$selected,
	$wgConf,
	$wmgRealm
);
$selectedGlobals['* dblists'] = MWMultiVersion::getTagsForWiki( $selected, $wmgRealm );
wmfAssertNoPrivateSettings( $selectedGlobals );
$isComparing = false;
if ( $compare !== null ) {
	$beforeGlobals = Wikimedia\MWConfig\MWConfigCacheGenerator::getConfigGlobals(
		$compare,
		$wgConf,
		$wmgRealm
	);
	$beforeGlobals['* dblists'] = MWMultiVersion::getTagsForWiki( $compare, $wmgRealm );
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

		if ( $key === '* dblists' ) {
			$selectedGlobals[$key] = [
				'context' => implode( "\n", array_intersect( $before, $after ) ),
				'before' => implode( "\n", array_diff( $before, $after ) ),
				'after' => implode( "\n", array_diff( $after, $before ) ),
			];
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

function wmfNocJsonForHtml( $val ): string {
	return htmlspecialchars(
		json_encode( $val, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}

function wmfNocWrapLines( string $str, string $pre = '', string $post = '' ): string {
	return $pre . str_replace( "\n", "$post\n$pre", $str ) . $post;
}

function wmfNocDiff( $before, $after, string $context = '' ): string {
	if ( $context !== '' ) {
		return wmfNocWrapLines( htmlspecialchars( $before ), '<del>- ', '</del>' ) . "\n"
			. wmfNocWrapLines( htmlspecialchars( $after ), '<ins>+ ', '</ins>' ) . "\n"
			. htmlspecialchars( $context );
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
			$preHtml .= "\n"
				. wmfNocDiff( $val['before'], $val['after'], $val['context'] ?? '' );
		}
		$preHtml .= "\n\n";
	}

	return [ 'nav' => $navHtml, 'pre' => $preHtml ];
}

$title = "Settings: " . ( $compare !== null ? "Compare $selected to $compare" : $selected );

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
		<li><a href="/db.php">Database view</a></li>
		<li><a href="/wiki.php" class="wm-nav-item-active">Wiki view</a>
			<ul><?php
				$data = wmfNocViewWiki( $selectedGlobals, $isComparing );
				print $data['nav'];
			?></ul>
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
