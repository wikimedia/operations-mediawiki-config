<!DOCTYPE html>
<html lang="en">
<head>
	<title>Wikimedia configuration files</title>
	<link rel="stylesheet" href="/base.css">
</head>
<body>

<p>Below is a selection of Wikimedia configuration files available for easy viewing.
	The files are dynamically generated and are perfectly up-to-date.
	Each of these files is also available in public version control in one of the following repositories:
</p>
<ul>
	<li><a href="https://gerrit.wikimedia.org/r/gitweb?p=operations/mediawiki-config.git;a=tree">operations/mediawiki-config.git</a></li>
	<li><a href="https://gerrit.wikimedia.org/r/gitweb?p=operations/apache-config.git;a=tree">operations/apache-config.git</a></li>
	<li><a href="https://gerrit.wikimedia.org/r/gitweb?p=operations/puppet.git;a=tree;f=templates/lucene">operations/puppet.git</a></li>
</ul>
<hr>

<h2><img src="./images/document.png" alt=""> Apache configuration</h2>
<ul>
<li><a href="./en2.conf">en2.conf</a></li>
<li><a href="./foundation.conf">foundation.conf</a></li>
<li><a href="./ganglia.conf">ganglia.conf</a></li>
<li><a href="./main.conf">main.conf</a></li>
<li><a href="./nagios.conf">nagios.conf</a></li>
<li><a href="./nonexistent.conf">nonexistent.conf</a></li>
<li><a href="./postrewrites.conf">postrewrites.conf</a></li>
<li><a href="./redirects.conf">redirects.conf</a></li>
<li><a href="./remnant.conf">remnant.conf</a></li>
<li><a href="./wikimedia.conf">wikimedia.conf</a></li>
<li><a href="./www.wikipedia.conf">www.wikipedia.conf</a></li>
</ul>

<h2><img src="./images/source_php.png" alt=""> MediaWiki configuration</h2>
<ul>
<?php
	$viewFilenames = array_merge(
		glob( __DIR__ . '/*.dat' ),
		glob( __DIR__ . '/*.php.txt' )
	);
	$viewFilenames[] = './langlist';
	outputFiles( $viewFilenames );
	function outputFiles( $viewFilenames ) {
		$viewFilenames = array_map( 'basename', $viewFilenames );
		natsort( $viewFilenames );
		foreach ( $viewFilenames as $viewFilename ) {
			$srcFilename = substr( $viewFilename, -4 ) === '.txt'
				? substr( $viewFilename, 0, -4 )
				: $viewFilename;
			echo "\n"
				. '<li><a href="./highlight.php?file=' . htmlspecialchars( urlencode( $srcFilename ) ) . '">'
				. htmlspecialchars( $srcFilename )
				. '</a> (<a href="./' . htmlspecialchars( urlencode( $viewFilename ) ) . '">raw text</a>)'
				. '</li>';
		}
	}
?>
</ul>

<h3><img src="./images/document.png" alt=""> Database lists</h3>
<ul>
<?php
	outputFiles( glob( __DIR__ . '/*.dblist' ) );
?>
</ul>

<h2><img src="./images/document.png" alt=""> CDB files</h2>
<ul>
<li><a href="./interwiki.cdb">interwiki.cdb</a></li>
<li><a href="./trusted-xff.cdb">trusted-xff.cdb</a></li>


<h2>Lucene search configuration</h2>
<ul>
<li><a href="./lsearch-global-2.1.conf">lsearch-global-2.1.conf</a></li>
</ul>

<!--
<h2><img src="./images/txt.png" alt="">PHP configuration</h2>
<ul>
<li><a href="./php5-i386.ini">php5-i386.ini</a></li>
<li><a href="./php5-x86_64.ini">php5-x86_64.ini</a></li>
</ul>
-->

<hr>
</body>
</html>
