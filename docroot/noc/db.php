<?php
/**
 * To test the script locally, run:
 *
 *     cd docroot/noc$ php -S localhost:9412
 *
 * Then view <http://localhost:9412/db.php>.
 */

$format = ( $_GET['format'] ?? null ) === 'json' ? 'json' : 'html';

if ( $format === 'json' ) {
	error_reporting( 0 );
} else {
	// Verbose error reporting
	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );
}

// Default to eqiad but allow limited other DCs to be specified with ?dc=foo.
$dbConfigEtcdPrefix = '/srv/dbconfig';
$dbctlJsonByDC = [
	'codfw' => 'codfw.json',
	'eqiad' => 'eqiad.json',
];
$dbSelectedDC = 'codfw';

if ( !is_dir( $dbConfigEtcdPrefix ) ) {
	// Local testing and debugging fallback
	$dbConfigEtcdPrefix = __DIR__ . '/../../tests/data/dbconfig';
	$dbctlJsonByDC = [
		'tmsx' => 'tmsx.json',
		'tmsy' => 'tmsy.json',
	];
	$dbSelectedDC = 'tmsx';
}

if ( isset( $_GET['dc'] ) && isset( $dbctlJsonByDC[$_GET['dc']] ) ) {
	$dbSelectedDC = $_GET['dc'];
}

$dbConfigEtcdJsonFilename = $dbctlJsonByDC[$dbSelectedDC];

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

// Now load the JSON written to Etcd by dbctl, from the local disk and merge it in.
// This is mimicking what wmfApplyEtcdDBConfig (wmf-config/etcd.php) does in prod.
//
// On mwmaint hosts, these JSON files are produced by a 'fetch_dbconfig' script,
// run via systemd timer, defined in puppet.
$dbconfig = json_decode( file_get_contents( "$dbConfigEtcdPrefix/$dbConfigEtcdJsonFilename" ), true );
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
foreach ( $dbctlJsonByDC as $dc => $file ) {
	$active = ( $file === $dbConfigEtcdJsonFilename ) ? ' wm-btn-active' : '';
	print '<a role="button" class="wm-btn' . $active . '" href="' . htmlspecialchars( "?dc=$dc" ) . '">' . htmlspecialchars( ucfirst( $dc ) ) . '</a> ';
}
print '</p>';

print '<p>Automatically generated based on <a href="./conf/highlight.php?file=db-production.php">';
print 'wmf-config/db-production.php</a> ';
print 'and on <a href="/dbconfig/' . htmlspecialchars( $dbConfigEtcdJsonFilename ) . '">';
print htmlspecialchars( $dbConfigEtcdJsonFilename ) . '</a>.';
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
