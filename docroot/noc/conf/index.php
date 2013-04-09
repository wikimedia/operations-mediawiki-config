<!DOCTYPE html>
<html lang="en">
<head>
	<title>Wikimedia configuration files</title>
	<link rel="stylesheet" href="../base.css">
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
<?php
        outputFiles( glob( __DIR__ . '/*.conf' ), false );
?>
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
	function outputFiles( $viewFilenames, $highlight = true ) {
		$viewFilenames = array_map( 'basename', $viewFilenames );
		natsort( $viewFilenames );
		foreach ( $viewFilenames as $viewFilename ) {
			$srcFilename = substr( $viewFilename, -4 ) === '.txt'
				? substr( $viewFilename, 0, -4 )
				: $viewFilename;
			echo "\n<li>";

			if ( $highlight ) {
				echo  '<a href="./highlight.php?file=' . htmlspecialchars( urlencode( $srcFilename ) ) . '">'
					. htmlspecialchars( $srcFilename );
				echo '</a> (<a href="./' . htmlspecialchars( urlencode( $viewFilename ) ) . '">raw text</a>)';
			} else {
				echo '</a> <a href="./' . htmlspecialchars( urlencode( $viewFilename ) ) . '">'
					. htmlspecialchars( $srcFilename ) . '</a>';
			}
			echo '</li>';
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

<h3><img src="./images/document.png" alt=""> CDB files</h3>
<ul>
<?php
	outputFiles( glob( __DIR__ . '/*.cdb' ), false );
?>
</ul>

<h2>Lucene search configuration</h2>
<ul>
<li><a href="./lsearch-global-2.1.conf">lsearch-global-2.1.conf</a></li>
</ul>

<hr>
</body>
</html>
