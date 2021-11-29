<?php
/**
 * To test the script locally, run:
 *
 *     cd docroot/noc$ php -S localhost:9412
 *
 * Then view <http://localhost:9412/db.php>.
 */

$format = isset( $_GET['format'] ) && $_GET['format'] === 'json' ? 'json' : 'html';

if ( $format === 'json' ) {
	error_reporting( 0 );
} else {
	// Verbose error reporting
	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );
}

// Default to eqiad but allow limited other DCs to be specified with ?dc=foo.
$dbConfigEtcdPrefix = '/srv/dbconfig';
$allowedDCs = [
	'codfw' => 'db-codfw.php',
	'eqiad' => 'db-eqiad.php',
];
$dbctlJsonByDC = [
	'codfw' => 'codfw.json',
	'eqiad' => 'eqiad.json',
];
$defaultDC = 'eqiad';

if ( !is_dir( $dbConfigEtcdPrefix ) ) {
	// Local testing and debugging fallback
	$dbConfigEtcdPrefix = __DIR__ . '/../../tests/data/dbconfig';
	$allowedDCs = [
		'tmsx' => 'db-eqiad.php',
		'tmsy' => 'db-codfw.php',
	];
	$dbctlJsonByDC = [
		'tmsx' => 'tmsx.json',
		'tmsy' => 'tmsy.json',
	];
	$defaultDC = 'tmsx';
}

$dbConfigFileName = ( isset( $_GET['dc'] ) && isset( $allowedDCs[$_GET['dc']] ) )
	? $allowedDCs[$_GET['dc']]
	: $allowedDCs[$defaultDC];

$dbConfigEtcdJsonFilename = ( isset( $_GET['dc'] ) && isset( $dbctlJsonByDC[$_GET['dc']] ) )
	? $dbctlJsonByDC[$_GET['dc']]
	: $dbctlJsonByDC[$defaultDC];

// Mock vars needed by db-*.php (normally set by CommonSettings.php)
$wgDBname = null;
$wgDBuser = null;
$wgDBpassword = null;
$wgDebugDumpSql = false;
$wgSecretKey = null;
$wmfMasterDatacenter = null;

// Load the actual db vars
require_once __DIR__ . "/../../wmf-config/{$dbConfigFileName}";

// Now load the JSON written to Etcd by dbctl, from the local disk and merge it in.
// This is mimicking what wmfEtcdApplyDBConfig (wmf-config/etcd.php) does in prod.
//
// On mwmaint hosts, these JSON files are produced by a 'fetch_dbconfig' script,
// run via systemd timer, defined in puppet.
$wmfDbconfigFromEtcd = json_decode( file_get_contents( "$dbConfigEtcdPrefix/$dbConfigEtcdJsonFilename" ), true );
global $wgLBFactoryConf;
$wgLBFactoryConf['readOnlyBySection'] = $wmfDbconfigFromEtcd['readOnlyBySection'];
$wgLBFactoryConf['groupLoadsBySection'] = $wmfDbconfigFromEtcd['groupLoadsBySection'];
foreach ( $wmfDbconfigFromEtcd['sectionLoads'] as $section => $sectionLoads ) {
	$wgLBFactoryConf['sectionLoads'][$section] = array_merge( $sectionLoads[0], $sectionLoads[1] );
}

require_once __DIR__ . '/../../src/Noc/DbConfig.php';

$dbConf = new Wikimedia\MWConfig\Noc\DbConfig();

if ( $format === 'json' ) {
	$data = [];
	foreach ( $dbConf->getNames() as $name ) {
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
<html>
<head>
	<meta charset="UTF-8">
	<title>Wikimedia database configuration</title>
	<link rel="stylesheet" href="css/base.css">
	<style>
	h2 { font-weight: normal; }
	code { color: #000; background: #f8f9fa; border: 1px solid #c8ccd1; border-radius: 2px; padding: 1px 4px; }
	main { display: flex; flex-wrap: wrap; }
	nav li { float: left; list-style: none; border: 1px solid #eaecf0; padding: 1px 4px; margin: 0 1em 1em 0; }
	section { flex: 1; min-width: 300px; border: 1px solid #eaecf0; padding: 0 1em; margin: 0 1em 1em 0; }
	main, footer { clear: both; }
	section:target { border-color: orange; }
	section:target h2 { background: #fef6e7; }
	</style>
</head>
<body>
<?php

$sectionNames = $dbConf->getNames();
natsort( $sectionNames ); // natsort for s1 < s2 < s10 rather than s1 < s10 < s2

// Generate navigation links
print '<nav><ul>';
foreach ( $sectionNames as $name ) {
	$id = urlencode( 'tabs-' . $name );
	print '<li><a href="#' . htmlspecialchars( $id ) . '">Section ' . htmlspecialchars( $name ) . '</a></li>';
}
print '</ul></nav><main>';

// Generate content sections
foreach ( $sectionNames as $name ) {
	$id = urlencode( 'tabs-' . $name );
	print "<section id=\"" . htmlspecialchars( $id ) . "\"><h2>Section <strong>" . htmlspecialchars( $name ) . '</strong></h2>';
	print $dbConf->htmlFor( $name ) . '</section>';
}
print '</main>';
print '<footer>Automatically generated based on <a href="./conf/highlight.php?file=' . htmlspecialchars( $dbConfigFileName ) . '">';
print 'wmf-config/' . htmlspecialchars( $dbConfigFileName ) . '</a> ';
print 'and on <a href="/dbconfig/' . htmlspecialchars( $dbConfigEtcdJsonFilename ) . '">';
print htmlspecialchars( $dbConfigEtcdJsonFilename ) . '</a>.<br/>';
foreach ( $allowedDCs as $dc => $file ) {
	if ( $file !== $dbConfigFileName ) {
		print 'View <a href="' . htmlspecialchars( "?dc=$dc" ) . '">' . htmlspecialchars( ucfirst( $dc ) ) . '</a>. ';
	}
}
print '</footer>';
?>
</body>
</html>
