<?php
/**
 * To preview NOC locally, check docroot/noc/README.md
 */
	require_once __DIR__ . '/../../../src/Noc/utils.php';
	require_once __DIR__ . '/../../../src/Noc/EtcdCachedConfig.php';
	require_once __DIR__ . '/filelist.php';
	$confFiles = wmfLoadRoutes();

	/**
	 * @param array $routesByLabel
	 */
	function wmfOutputFiles( $routesByLabel ) {
		$labels = array_keys( $routesByLabel );
		natsort( $labels );
		foreach ( $labels as $label ) {
			$route = $routesByLabel[$label];
			echo "\n<li>";
			echo '<a href="./highlight.php?file=' . htmlspecialchars( $label ) . '">'
				. htmlspecialchars( $label );
			echo '</a> (<a href="' . htmlspecialchars( $route ) . '">raw text</a>)';
			echo '</li>';
		}
	}

?><!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="utf-8">
	<title>Configuration files – Wikimedia NOC</title>
	<link rel="shortcut icon" href="/static/favicon/wmf.ico">
	<link rel="stylesheet" href="../css/base.css">
</head>
<body>
<header><div class="wm-container">
	<a role="banner" href="/" title="Visit the home page"><em>Wikimedia</em> NOC</a>
</div></header>

<main role="main"><div class="wm-container">

	<nav class="wm-site-nav"><ul class="wm-nav">
		<li><a href="/conf/" class="wm-nav-item-active">Config files</a>
			<ul>
				<li><a href="#wmf-config">wmf-config/</a></li>
				<li><a href="#dblist">dblists/</a></li>
			</ul>
		</li>
		<li><a href="/db.php">Database config</a></li>
		<li><a href="/wiki.php">Wiki config</a></li>
	</ul></nav>

	<article>

<p>Below is a selection of Wikimedia configuration files available for easy viewing.
	The files are dynamically generated and are perfectly up-to-date.
	Each of these files is also available in public version control in one of the following repositories:
</p>
<ul>
	<li><a href="https://phabricator.wikimedia.org/diffusion/OMWC/">operations/mediawiki-config.git</a></li>
	<li><a href="https://phabricator.wikimedia.org/diffusion/OPUP/">operations/puppet.git</a></li>
</ul>

<hr>
<p>Currently active MediaWiki versions: <?php
	echo implode( ', ', Wikimedia\MWConfig\Noc\getWikiVersions() );
?></p>
<p>Current primary datacenter: <?php
	$masterDC = \Wikimedia\MWConfig\Noc\EtcdCachedConfig::getInstance()->getValue( 'common/WMFMasterDatacenter' );
	echo $masterDC ?? 'unknown';
?></p>
<hr>

<h2 id="wmf-config">MediaWiki configuration</h2>
<ul>
<?php
	wmfOutputFiles( $confFiles->getConfigRoutes() );
?>
</ul>

<h2 id="dblist">Database lists</h2>
<ul>
<?php
	wmfOutputFiles( $confFiles->getDblistRoutes() );
?>
</ul>

</article>
</div></main>
</body>
</html>
