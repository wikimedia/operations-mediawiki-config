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
$dbctlJsonByDC = [
	'codfw' => 'codfw.json',
	'eqiad' => 'eqiad.json',
];
$dbConfigEtcdJsonFilename = ( isset( $_GET['dc'] ) && isset( $dbctlJsonByDC[$_GET['dc']] ) )
	? $dbctlJsonByDC[$_GET['dc']]
	: $dbctlJsonByDC['eqiad'];

// Mock vars needed by db-*.php (normally set by CommonSettings.php)
$wgDBname = null;
$wgDBuser = null;
$wgDBpassword = null;
$wgSecretKey = null;
$wmfMasterDatacenter = null;

// Mock vars needed by db-*.php (normally set by MediaWiki)
require_once __DIR__ . '/../../tests/Defines.php';

// Load the actual db vars
require_once __DIR__ . "/../../wmf-config/{$dbConfigFileName}";
// Now load dbctl JSON from disk and merge it in, mimicking what etcd.php's wmfEtcdApplyDBConfig()
// does in production.
// On mwmaint hosts, the JSON files are produced by a 'fetch_dbconfig' script & systemd timer defined in puppet.
// For local testing, you can fetch JSON files from https://noc.wikimedia.org/dbconfig/eqiad.json
// and then set WMF_NOC_ETCD_JSON_PATH to the directory containing the downloaded eqiad.json.
$dbConfigEtcdPrefix = getenv( 'WMF_NOC_ETCD_JSON_PATH' ) !== false
	? getenv( 'WMF_NOC_ETCD_JSON_PATH' )
	: '/srv/dbconfig';
$wmfDbconfigFromEtcd = json_decode( file_get_contents( "$dbConfigEtcdPrefix/$dbConfigEtcdJsonFilename" ), true );
global $wgLBFactoryConf;
$wgLBFactoryConf['readOnlyBySection'] = $wmfDbconfigFromEtcd['readOnlyBySection'];
$wgLBFactoryConf['groupLoadsBySection'] = $wmfDbconfigFromEtcd['groupLoadsBySection'];
foreach ( $wmfDbconfigFromEtcd['sectionLoads'] as $section => $sectionLoads ) {
	$wgLBFactoryConf['sectionLoads'][$section] = array_merge( $sectionLoads[0], $sectionLoads[1] );
}

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
