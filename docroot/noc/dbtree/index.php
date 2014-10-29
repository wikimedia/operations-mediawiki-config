<?php
//
// dbtree - provides a map of wmf's core db servers, pulling in mediawiki configs
// and stats from ganglia, parsed directly from gmetad's xml output.
//
// this is why i'm not a frontend developer.
// love,
// asher feldman, <afeldman@wikimedia.org>
//
error_reporting( E_ERROR );
date_default_timezone_set( "GMT" );
$ghost = 'http://ganglia.wikimedia.org/latest/?r=hour&amp;cs=&amp;ce=&amp;m=&amp;tab=ch&amp;vn=&amp;hreg%5B%5D=';
$ishmael = 'https://ishmael.wikimedia.org/';
$gangcache = '/tmp/ganglia-dbtree.dat';
// ttl for the local cache file
$ttl = 120;
// if a server hsan't reported in this many more seconds than the most recent
// reporting server, mark it black.
$stale = 120;
$dbdata = array();
$dbdata['latest'] = 0;

function get_ganglia_xml() {
	global $xml;
	if ( file_exists( 'ganglia.xml' ) ) {
		$xml = simplexml_load_file( 'ganglia.xml' );
		return;
	}

	$errst = '';
	$sock = fsockopen("ganglia.wikimedia.org", 8653, $errno, $errst, 30);
	if (!$sock) {
		echo "Failed reading from ganglia: $errno - $errst<br />\n";
	} else {
		$buf = '';
		while ( !feof( $sock ) ) {
			$buf .= fgets( $sock, 8192 );
		}
		$xml = simplexml_load_string( $buf );
	}
}

/**
 * @param string $db
 */
function parse_ganglia_xml( $db ) {
	global $xml, $dbdata;
	$metricAttributes = array(
		'mysql_queries',
		'mysql_slave_lag',
		'mysql_slave_running',
		'mysql_threads_running',
		'mysql_slow_queries',
		// 'mysql_innodb_data_reads',
		// 'mysql_innodb_data_writes'
	);
	$name = "@NAME='";
	$dbdata[$db] = array();
	$metricAttributeXPath = $name . implode( "' or {$name}", $metricAttributes );

	$fqdn = '';
	if ( preg_match( '/^db1\d{3}/', $db ) ) {
		$fqdn = $db . '.eqiad.wmnet';
	}

	$rootXmlXpath = "//GANGLIA_XML/GRID/CLUSTER/HOST[@NAME='{$fqdn}']";
	$result = $xml->xpath( "{$rootXmlXpath}/@REPORTED | {$rootXmlXpath}/METRIC[{$metricAttributeXPath}']" );
	foreach ( $result as $item ) {
		$attrib = $item->attributes();
		if ( $item->REPORTED ) {
			$dbdata[$db]['REPORTED'] = (int) $item->REPORTED;
			if ( $dbdata['latest'] < (int) $item->REPORTED ) {
				$dbdata['latest'] = (int) $item->REPORTED;
			}
		} else {
			$k = (string) $attrib['NAME'];
			$dbdata[$db][$k] = (int) $attrib['VAL'];
		}
	}
}

/**
 * @return array
 */
function parse_db_conf() {
	$confdir = '/srv/mediawiki/wmf-config/';
	$s3file = '/srv/mediawiki/s3.dblist';
	$confs = array( 'db-eqiad.php' );
	$dbs = array();

	foreach ( $confs as $conf ) {
		if ( !file_exists( $confdir . $conf ) ) {
			continue;
		}
		$type = ( $conf == "db-eqiad.php" ) ? 'pri' : 'sec';
		include $confdir . $conf;
		foreach ( $wgLBFactoryConf['sectionLoads'] as $cluster => $sectionload ) {
			if ( $cluster == "DEFAULT" ) {
				$cluster = "s3";
			}
			if ( !isset( $dbs[$cluster] ) ) {
				$dbs[$cluster] = array();
			}
			$dbs[$cluster][$type] = array();
			foreach ( array_keys( $sectionload ) as $db ) {
				if ( $db ) {
					$dbs[$cluster][$type][] = $db;
					parse_ganglia_xml( $db );
				}
			}
		}
		$dbs['s3']['wikis'] = array( 'DEFAULT' );
		if ( $type == 'pri' ) {
			foreach ( $wgLBFactoryConf['sectionsByDB'] as $wiki => $cluster ) {
				if ( !isset( $dbs[$cluster]['wikis'] ) ) {
					$dbs[$cluster]['wikis'] = array();
				}
				$dbs[$cluster]['wikis'][] = $wiki;
			}
		}
	}
	$s3wikis = file_get_contents( $s3file );
	$dbs['s3']['wikis'] = preg_split( "/$\R?^/m", $s3wikis );
	return( $dbs );
}

/**
 * @param string $db
 * @return string
 */
function get_db_color( $db ) {
	global $dbdata, $stale;

	if ( $dbdata['latest'] - $dbdata[$db]['REPORTED'] > $stale || !$dbdata[$db]['REPORTED'] ) {
		$color = 'black';
	} elseif ( $dbdata[$db]['mysql_slave_running'] != 1 ) {
		$color = 'black';
	} elseif ( $dbdata[$db]['mysql_slave_lag'] > 10 ) {
		$color = 'red';
	} elseif ( $dbdata[$db]['mysql_slave_lag'] > 2 ) {
		$color = 'yellow';
	} else {
		$color = 'white';
	}
	return $color;
}

/**
 * @param string $db
 * @param bool $lag
 */
function print_dbdata( $db, $lag = true ) {
	global $dbdata, $ishmael;

	if ( $lag ) {
		print 'Lag: ' . $dbdata[$db]['mysql_slave_lag'] . '<br>';
	}
	print 'QPS: ' . sprintf( "%.2f", $dbdata[$db]['mysql_queries'] / 60 ) . '<br>
		Active Threads: ' . $dbdata[$db]['mysql_threads_running'] . '<br>
		Slow Q (-1min): ' . $dbdata[$db]['mysql_slow_queries'] . '<br>
		Analyze:
		<a href="' . $ishmael . '?host=' . $db . '" target="_blank">slow</a> |
		<a href="' . $ishmael . 'sample/?host=' . $db . '" target="_blank">sampled</a>';
}

if ( file_exists( $gangcache ) && time() - $ttl < filemtime( $gangcache ) ) {
	$fp = fopen( $gangcache, 'r' );
	$data = unserialize( fread( $fp, filesize( $gangcache ) ) );
	$dbs = $data['clusters'];
	$dbdata = $data['ganglia'];
}

# getting a ganglia xml dump and parse can be slow, attempt to cache
# using the local fs
if ( ( time() - $ttl > filemtime( $gangcache ) ) || $_GET['recache'] == 'true' ) {
	get_ganglia_xml();
	if ( !$xml ) {
		echo "failure reading ganglia";
	} else {
		$dbs = parse_db_conf();
		$data = array( 'ganglia' => $dbdata, 'clusters' => $dbs );
		$fp = fopen( $gangcache, 'w' );
		fwrite( $fp, serialize( $data ) );
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Core Databases</title>
	<link type="text/css" href="css/jquery.jOrgChart.min.css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.24.custom.min.js"></script>
	<link rel="stylesheet" href="css/custom.min.css"/>
	<script type="text/javascript" src="js/jquery.jOrgChart.js"></script>
	<script type="text/javascript" src="js/jquery.bt.min.js"></script>
</head>
<body>

<?php

ksort( $dbs );
foreach ( $dbs as $s => $cluster ) {
	$master = array_shift( $cluster['pri'] );
	$second = isset( $cluster['sec'] ) ? array_shift( $cluster['sec'] ): null;

	# handle the case where the secondary datacenter is still active,
	# using the write masters in the other dc.  in this case, skip
	# those host the master slot and treat the next host as the
	# secondary master.
	if ( $master === $second ) {
		$second = array_shift( $cluster['sec'] );
	}

	print '<ul class="org' . $s . '" style="display:none">
		<li><div class="' . $s . '" title="wikis: ';

	foreach ( $cluster['wikis'] as $wiki ) {
		print "$wiki ";
	}

	$color = ( $dbdata['latest'] - $dbdata[$master]['REPORTED'] > $stale || !$dbdata[$master]['REPORTED'] ) ?
		 'black' : 'white';
	print ' "><h2>' . $s . '</h2></div>
		<ul>
		<li><div style="background: ' . $color . '">
		<a href="' . $ghost . $master . '" target="_blank"><h3>' . $master . '</h3></a><hr></div>';
	print_dbdata( $master, false );

	print '<ul>';
	foreach ( $cluster['pri'] as $db ) {
		$color = get_db_color( $db );
		print '<li><div style="background: ' . $color . '">
			<a href="' . $ghost . $db . '" target="_blank">
			<h4>' . $db . '</h4></a><hr></div>';
		print_dbdata( $db );
		print '</li>';
	}

	if ( $second ) {
		$second_color = get_db_color( $second );
		print '<li><div style="background: ' . $second_color . '"><a href="' . $ghost . $second . '" target="_blank"><h3>' .
			$second . '</h3></a><hr></div>';
		print_dbdata( $second );

		print '<ul>';
		foreach ( $cluster['sec'] as $db ) {
			if ( $second_color == 'black' || $second_color == 'red' ) {
				$color = $second_color;
			} else {
				$color = get_db_color( $db );
			}
			print '<li><div style="background: ' . $color . '"><a href="' . $ghost . $db . '" target="_blank">
				<h4>' . $db . '</h4></a><hr></div>';
			print_dbdata( $db );
			print '</li>';
		}

	}

	print '				</ul>
				</li>
			</ul>
			</li>
		</ul>';
}

print '<div id=txt style="color:#000000; background:#aaaaaa; width:600px; position:relative; left:+40px">
	Ganglia derived db data recent as of: ' . date( "D M j G:i:s T Y", $dbdata['latest'] );
print '<div style="text-align:center;">
	<blockquote><div style="background-color:white;width:120px;border:1px solid #000">Ok</div>
	<div style="background-color:yellow;width:120px;border:1px solid #000">2 > Lag > 10</div>
	<div style="background-color:red;width:120px;border:1px solid #000">Lag > 10</div>
	<div style="background-color:black;width:120px;border:1px solid #000;color:#ffffff">Not Reporting or Replicating</div></blockquote>
	</div>
	Click shard name for list of wikis.
	<div id="wikis" style="font-weight:bold;"> </div></div>';

$charts = '';
foreach ( $dbs as $s => $cluster ) {
	$charts .= "\t$('.org{$s}').jOrgChart();\n";
	if ( $s == "s3" ) {
		$charts .= "\t$('.{$s}').bt({fill: 'red', trigger: 'click', width: 600});\n";
	} else {
		$charts .= "\t$('.{$s}').bt({fill: 'red', trigger: 'click'});\n";
	}
}

print "<script>jQuery(document).ready(function() {
	jQuery.bt.defaults.closeWhenOthersOpen = true;
$charts
})";

print '
</script>
</body>
</html>';
