<!DOCTYPE html>
<html>
<head>
	<title>Wikimedia databases configuration</title>
	<script src="https://www.mediawiki.org/w/load.php?modules=startup&only=scripts"></script>
	<style>body { font-family: sans-serif; }</style>
</head>
<body>
<?php

// ERRORS
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

# some lame definitions used in db.php
$wgDBname = $wgDBuser = $wgDBpassword = null;
define( 'DBO_DEFAULT', 'uniq' );

require_once( '../../wmf-config/db-eqiad.php' );

class wmfClusters {
	private $clusters;

	function names( ) {
		global $wgLBFactoryConf;
		return array_keys( $wgLBFactoryConf['sectionLoads'] );
	}

	/** Returns a list of wikis for a clustername */
	function wikis( $clusterName ) {
		global $wgLBFactoryConf;
		$ret = array();
		foreach( $wgLBFactoryConf['sectionsByDB'] as $wiki => $cluster ) {
			if( $cluster == $clusterName ) {
				$ret[] = $wiki;
			}
		}
		return $ret;
	}
	function loads( $clusterName ) {
		global $wgLBFactoryConf;
		return $wgLBFactoryConf['sectionLoads'][$clusterName];
	}
	function databases( $clusterName ) {
		global $wgLBFactoryConf;
		return array_keys( $wgLBFactoryConf['sectionLoads'][$clusterName] );
	}

	function contentFor( $name ) {
		print "Cluster <b>$name</b><br><b>Databases:</b><br>";
		foreach( $this->databases($name) as $db ) {
			print "$db ";
		}
		print '<br><b>Loads</b>:<br>';
		foreach( $this->loads($name) as $k => $v ) {
			print "$k => $v<br>";
		}
		print '<br><b>Wikis</b>:<br>';
		if( $name == 'DEFAULT' ) {
			print 'Any wiki not hosted on the other clusters.<br>';
		} else {
			foreach ( $this->wikis($name) as $w ) {
				print "$w<br>";
			}
		}
	}
}

$wmf = new wmfClusters();

print '<div id="tabs">';

# Generates tabs:
print '<ul>';
$tab = 0;
foreach ( $wmf->names() as $name ) {
	$tab++;
	print "\n";
	print '<li><a href="#tabs-' . $tab . '">Cluster ' . $name . '</a></li>';
}
print '</ul>';

# Generates tabs content
$tab=0;
foreach ( $wmf->names() as $name ) {
	$tab++;
	print "<div id=\"tabs-$tab\">\n";
	$wmf->contentFor( $name );
	print '</div>';
}
?></div>
<div id="footer">Automatically generated from <a href="//noc.wikimedia.org/conf/highlight.php?file=db-eqiad.php">wmf-config/db-eqiad.php</a></div>
<script>
window.RLQ = window.RLQ || [];
window.RLQ.push( function () {
	mw.loader.using( 'jquery.ui.tabs', function () {
		$( '#tabs' ).tabs();
	} );
} );
</script>
</body>
</html>
