<?php
//
// This is why I'm not a frontend developer.  
// Love,
// Asher
//
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Core Databases</title>
	<link type="text/css" href="css/jquery.jOrgChart.css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.17.custom.min.js"></script>
	<link rel="stylesheet" href="css/custom.css"/>
	<script type="text/javascript" src="js/jquery.jOrgChart.js"></script>
	<script type="text/javascript" src="js/jquery.bt.min.js"></script>
<script>
	jQuery(document).ready(function() {
		$(".org").jOrgChart();
	});
</script>
</head>
<body>

<?php
error_reporting(E_ERROR);
date_default_timezone_set("GMT");
$confdir = '/home/wikipedia/common/wmf-config/';
$confs = array('db.php', 'db-secondary.php');
$url = 'http://ganglia.wikimedia.org/latest/graph.php?r=hour&title=&vl=&x=&n=&hreg[]=^db\d%2B&mreg[]=mysql_slave_lag&aggregate=1&json=1';
$ghost = 'http://ganglia.wikimedia.org/latest/?r=hour&cs=&ce=&m=&tab=ch&vn=&hreg%5B%5D=';
$gangcache = 'cache/ganglia.json';
$ttl = 60;
$dbs = array();

foreach ( $confs as $conf ) {
	$type = ( $conf == "db.php" ) ? 'pri' : 'sec';
	include $confdir . $conf;
	foreach( $wgLBFactoryConf['sectionLoads'] as $cluster => $sectionload) {
		if ($cluster == "DEFAULT") {
			$cluster = "s3";
		}
		if (!isset($dbs[$cluster])) {
			$dbs[$cluster] = array();
		}
		$dbs[$cluster][$type] = array();
		foreach (array_keys($sectionload) as $db) {
			if ($db) {
				$dbs[$cluster][$type][] = $db;
			}
		}
	}
	$dbs['s3']['wikis'] = array('DEFAULT');
	if ($type == 'pri') {
		foreach ( $wgLBFactoryConf['sectionsByDB'] as $wiki => $cluster ) {
			if (!isset($dbs[$cluster]['wikis'])) { 
				$dbs[$cluster]['wikis'] = array();
			}
			$dbs[$cluster]['wikis'][] = $wiki;
		}
	}
}

#var_dump($dbs);
if (file_exists($gangcache)) {
	$fp = fopen($gangcache, 'r');
	$json = fread($fp, filesize($gangcache));
	$dump = json_decode($json, $assoc=true);
}
if (time() - $ttl > filemtime($gangcache)) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$json = curl_exec($ch);
	if (curl_errno > 1) {
		echo "failure reading ganglia";
	} else {
		$fp = fopen($gangcache, 'w');
		fwrite($fp, $json);
		$dump = json_decode($json, $assoc=true);
	}
}
fclose($fp);
$dblag = array();
$latest = 0;

foreach ($dump as $metric) {
	$host = explode('.', trim($metric['metric_name']));
	$points = $metric['datapoints'];
	array_pop($points);
	$point = array_pop($points);
	$lag = ($point[0] > 0) ? sprintf("%.2f", $point[0]) : $point[0];
	$time = $point[1];
	if ($time > $latest) {
		$latest = $time;
	}
	$dblag[$host[0]] = array( 'lag' => $lag, 'age' => $time, 'fqdn' => trim($metric['metric_name']) );
}

#var_dump($dblag);

print '<ul class="org" style="display:none"><li>cluster<ul>'; 
ksort($dbs);
foreach ($dbs as $s => $cluster) {
	$master = array_shift($cluster['pri']);
	$second = array_shift($cluster['sec']);
	print '<li><div class="'.$s.'" title="wikis: ';
	foreach ($cluster['wikis'] as $wiki) {
		print "$wiki ";
	}
	print ' "><h2>' . $s . '</h2></div>
		<ul>
		<li><a href="' . $ghost . ($dblag[$master]['fqdn'] ? $dblag[$master]['fqdn'] : $master) . '" target=_new><h3>'
		. $master . '</h3></a><ul>';
	foreach ($cluster['pri'] as $db) {
		print '		<li><a href="' . $ghost . ($dblag[$db]['fqdn'] ? $dblag[$db]['fqdn'] : $db) . '" target=_new>
			<h4>' . $db . '</h4></a><hr>lag: ' . $dblag[$db]['lag'] . '</li>';
	}
	print '		<li><a href="' . $ghost . ($dblag[$second]['fqdn'] ? $dblag[$second]['fqdn'] : $second) . '" target=_new><h3>' .
			$second . '</h3></a><hr>lag: ' . $dblag[$second]['lag'] .'
					<ul>';
	foreach ($cluster['sec'] as $db) {
		print '			<li><a href="' . $ghost . ($dblag[$db]['fqdn'] ? $dblag[$db]['fqdn'] : $db) . '" target=_new>
			<h4>' . $db . '</h4></a><hr>lag: ' . $dblag[$db]['lag'] . '</li>';
	}
	print '				</ul>
				</li>
			</ul>
			</li>
		</ul>
		</li>';
}
print '</li></ul></ul>';
print '<div id=txt style="color:#000000; background:#aaaaaa; width:500px; position:relative; left:+40px">Replication lag data recent as of: ' . date("D M j G:i:s T Y", $latest);
print '<p><div id="wikis" style="font-weight:bold";> </div></div>';

print '<script>jQuery(document).ready(function() {';
foreach ($dbs as $s => $cluster) {
	print '$(".' . $s . '").bt({fill: "red"}); ';
}
print '}); </script>
</body></html>';
