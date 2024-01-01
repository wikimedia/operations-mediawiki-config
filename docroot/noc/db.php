<?php
/**
 * To test the script locally, run:
 *
 *     cd docroot/noc$ php -S localhost:9412
 *
 * Then view <http://localhost:9412/db.php>.
 */

// phpcs:disable Generic.WhiteSpace.ScopeIndent.Incorrect
// phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact

use Wikimedia\MWConfig\Noc\EtcdCachedConfig;

require_once __DIR__ . '/../../src/Noc/EtcdCachedConfig.php';

$isLocalhost = strpos( $_SERVER['HTTP_HOST'], 'localhost' ) === 0;
$format = ( $_GET['format'] ?? null ) === 'json' ? 'json' : 'html';
if ( $format === 'json' ) {
	error_reporting( 0 );
} else {
	// Verbose error reporting
	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );
}

// Default to eqiad but allow limited other DCs to be specified with ?dc=foo.
$env = require '../../wmf-config/env.php';
$validDCs = $isLocalhost ? [ 'tmsx', 'tmsy' ] : $env['dcs'];
$dbSelectedDC = in_array( $_GET['dc'] ?? null, $validDCs )
	? $_GET['dc']
	: $validDCs[0];

// Now load the JSON written to Etcd by dbctl, and merge it in.
// This is mimicking what wmfApplyEtcdDBConfig (wmf-config/etcd.php) does in prod.
if ( $isLocalhost ) {
	$dbconfig = json_decode(
		file_get_contents(
			__DIR__ . '/../../tests/data/dbconfig/' . $dbSelectedDC . '.json'
		),
		/* assoc = */ true
	);
} else {
	$dbconfig = EtcdCachedConfig::getInstance()->getValue( $dbSelectedDC . '/dbconfig' );
}

// Mock vars needed by db-*.php (normally set by CommonSettings.php)
$wgDBname = null;
$wgDBuser = null;
$wgDBpassword = null;
$wgDebugDumpSql = false;
$wgSecretKey = null;
$wmgDatacenter = null;
$wmgMasterDatacenter = null;

// Load file to obtain $wgLBFactoryConf
require_once __DIR__ . '/../../wmf-config/db-production.php';

global $wgLBFactoryConf;
$wgLBFactoryConf['readOnlyBySection'] = $dbconfig['readOnlyBySection'];
$wgLBFactoryConf['groupLoadsBySection'] = $dbconfig['groupLoadsBySection'];
foreach ( $dbconfig['sectionLoads'] as $section => $sectionLoads ) {
	$wgLBFactoryConf['sectionLoads'][$section] = array_merge( $sectionLoads[0], $sectionLoads[1] );
}
require_once __DIR__ . '/../../multiversion/MWConfigCacheGenerator.php';
require_once __DIR__ . '/../../src/Noc/DbConfig.php';

$dbConf = new Wikimedia\MWConfig\Noc\DbConfig( [
	'DEFAULT' => 's3',
] );

if ( $format === 'json' ) {
	$data = [];
	foreach ( $dbConf->getSections() as $name => $_ ) {
		$data[$name] = [
			'hosts' => $dbConf->getHosts( $name ),
			'loads' => $dbConf->getLoads( $name ),
			'groupLoads' => $dbConf->getGroupLoads( $name ),
			'dbs' => $dbConf->getDBs( $name ),
			'readOnly' => $dbConf->getReadOnly( $name ),
		];
	}
	header( 'Content-Type: application/json; charset=utf-8' );
	echo json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	exit;
}

?><!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="utf-8">
	<title><?php echo htmlspecialchars( "Database configuration: $dbSelectedDC – Wikimedia NOC" ); ?></title>
	<link rel="shortcut icon" href="/static/favicon/wmf.ico">
	<link rel="stylesheet" href="css/base.css">
	<style>
	code {
		color: #000;
		background: #f8f9fa;
		border: 1px solid #c8ccd1;
		border-radius: 2px;
		padding: 1px 4px;
	}
	.nocdb-sections {
		display: flex;
		flex-wrap: wrap;
	}
	section {
		flex: 1;
		min-width: 180px;
		border: 1px solid #eaecf0;
		padding: 0 1rem 1rem 1rem;
		margin: 0 1rem 1rem 0;
		font-size: 1.4rem;
	}
	section:target { border-color: orange; }
	section:target h2 { background: #fef6e7; }
	</style>
</head>
<body>
<header><div class="wm-container">
	<a role="banner" href="/" title="Visit the home page"><em>Wikimedia</em> NOC</a>
</div></header>

<main role="main"><div class="wm-container">

	<nav class="wm-site-nav"><ul class="wm-nav">
		<li><a href="./conf/">Config files</a></li>
		<li><a href="./db.php" class="wm-nav-item-active">Database config</a>
			<ul><?php

$sections = $dbConf->getSections();

// Generate navigation links
foreach ( $sections as $name => $label ) {
	$id = urlencode( 'tabs-' . $name );
	print '<li><a href="#' . htmlspecialchars( $id ) . '">Section ' . htmlspecialchars( $label ) . '</a></li>';
}

?>
			</ul>
		</li>
		<li><a href="/wiki.php">Wiki config</a></li>
	</ul></nav>

	<article>
		<h1>Database configuration</h1>
<?php
print '<p>';
foreach ( $validDCs as $dc ) {
	$active = ( $dc === $dbSelectedDC ) ? ' wm-btn-active' : '';
	print '<a role="button" class="wm-btn' . $active . '" href="' . htmlspecialchars( "?dc=$dc" ) . '">' . htmlspecialchars( ucfirst( $dc ) ) . '</a> ';
}
print '<a class="noc-tab-action" href="' . htmlspecialchars( "?dc=$dbSelectedDC&format=json" ) . '">View JSON</a> ';
print '</p>';

print '<p>Automatically generated based on <a href="./conf/highlight.php?file=db-production.php">';
print 'wmf-config/db-production.php</a> ';
print 'and on <a href="/dbconfig/' . htmlspecialchars( $dbSelectedDC ) . '.json">';
print htmlspecialchars( $dbSelectedDC ) . '.json</a>.';
print '</p>';

print '<div class="nocdb-sections">';
// Generate content sections
foreach ( $sections as $name => $label ) {
	$id = urlencode( 'tabs-' . $name );
	print "<section id=\"" . htmlspecialchars( $id ) . "\"><h2>Section " . htmlspecialchars( $label ) . '</h2>';
	print $dbConf->htmlFor( $name ) . '</section>';
}
print '</div>';
?>
	</article>

</div></main>

</body>
</html>
