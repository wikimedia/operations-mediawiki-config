<?php

$format = isset( $_GET['format'] ) && $_GET['format'] === 'json' ? 'json' : 'html';

if ( $format === 'json' ) {
	error_reporting( 0 );
} else {
	// Verbose error reporting
	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );
}

// Default to eqiad but allow limited other DCs to be specified with ?dc=foo.
$allowedDCs = [
	'codfw' => 'db-codfw.php',
	'eqiad' => 'db-eqiad.php',
];
$dbConfigFileName = ( isset( $_GET['dc'] ) && isset( $allowedDCs[$_GET['dc']] ) )
	? $allowedDCs[$_GET['dc']]
	: $allowedDCs['eqiad'];

// Mock vars needed by db-*.php (normally set by CommonSettings.php)
$wgDBname = null;
$wgDBuser = null;
$wgDBpassword = null;
$wgSecretKey = null;
$wmfMasterDatacenter = null;

// Mock vars needed by db-*.php and InitialiseSettings.php (normally set by MediaWiki)
require_once __DIR__ . '/../../tests/Defines.php';

// Load the actual db vars
require_once __DIR__ . "/../../wmf-config/{$dbConfigFileName}";

require_once __DIR__ . '/../../src/WmfClusters.php';

$clusters = new WmfClusters();

if ( $format === 'json' ) {
	$data = [];
	foreach ( $clusters->getNames() as $name ) {
		$data[$name] = [
			'hosts' => $clusters->getHosts( $name ),
			'loads' => $clusters->getLoads( $name ),
			'groupLoads' => $clusters->getGroupLoads( $name ),
			'dbs' => $clusters->getDBs( $name ),
			'readOnly' => $clusters->getReadOnly( $name ),
		];
	}
	header( 'Content-Type: application/json; charset=utf-8' );
	echo json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	exit;
}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Wikimedia database configuration</title>
	<link rel="stylesheet" href="css/base.css">
	<style>
	h2 { font-weight: normal; }
	code { color: #000; background: #f9f9f9; border: 1px solid #ddd; border-radius: 2px; padding: 1px 4px; }
	main { display: flex; flex-wrap: wrap; }
	nav li { float: left; list-style: none; border: 1px solid #eee; padding: 1px 4px; margin: 0 1em 1em 0; }
	section { flex: 1; min-width: 300px; border: 1px solid #eee; padding: 0 1em; margin: 0 1em 1em 0; }
	main, footer { clear: both; }
	section:target { border-color: orange; }
	section:target h2 { background: #ffe; }
	</style>
</head>
<body>
<?php

// Generate navigation links
print '<nav><ul>';
$tab = 0;
foreach ( $clusters->getNames() as $name ) {
	$tab++;
	print '<li><a href="#tabs-' . $tab . '">Cluster ' . htmlspecialchars( $name ) . '</a></li>';
}
print '</ul></nav><main>';

// Generate content sections
$tab = 0;
foreach ( $clusters->getNames() as $name ) {
	$tab++;
	print "<section id=\"tabs-$tab\"><h2>Cluster <strong>" . htmlspecialchars( $name ) . '</strong></h2>';
	print $clusters->htmlFor( $name ) . '</section>';
}
print '</main>';
print '<footer>Automatically generated based on <a href="./conf/highlight.php?file='. htmlspecialchars( $dbConfigFileName ) . '">';
print 'wmf-config/' . htmlspecialchars( $dbConfigFileName ) . '</a>.<br/>';
foreach ( $allowedDCs as $dc => $file ) {
	if ( $file !== $dbConfigFileName ) {
		print 'View <a href="' . htmlspecialchars( "?dc=$dc" ) . '">' . htmlspecialchars( ucfirst( $dc ) ) . '</a>. ';
	}
}
print '</footer>';
?>
</body>
</html>
