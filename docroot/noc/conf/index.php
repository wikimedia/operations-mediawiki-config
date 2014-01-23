<?php
require_once( '/usr/local/apache/common-local/multiversion/activeMWVersions.php' );

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
				echo '<a href="./' . htmlspecialchars( urlencode( $viewFilename ) ) . '">'
					. htmlspecialchars( $srcFilename ) . '</a>';
			}
			echo '</li>';
		}
	}

?><!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8" />
<head>
	<title>Wikimedia configuration files</title>
	<link rel="shortcut icon" href="//bits.wikimedia.org/favicon/wmf.ico">
	<link rel="stylesheet" href="../css/base.css">
</head>
<body>

<p>Below is a selection of Wikimedia configuration files available for easy viewing.
	The files are dynamically generated and are perfectly up-to-date.
	Each of these files is also available in public version control in one of the following repositories:
</p>
<ul>
	<li><a href="https://git.wikimedia.org/tree/operations%2Fapache-config.git">operations/apache-config.git</a></li>
	<li><a href="https://git.wikimedia.org/tree/operations%2Fmediawiki-config.git">operations/mediawiki-config.git</a></li>
	<li><a href="https://git.wikimedia.org/tree/operations%2Fpuppet.git">operations/puppet.git</a></li>
</ul>

<hr>
<p>Currently active MediaWiki versions: <?php echo implode( ' ', getActiveWikiVersions() ); ?></p>
<hr>

<h2><img src="./images/document.png" alt=""> Apache configuration</h2>
<ul>
<?php
    outputFiles( glob( __DIR__ . '/*.conf' ) );
?>
</ul>

<h2><img src="./images/source_php.png" alt=""> MediaWiki configuration</h2>
<ul>
<?php
	$viewFilenames = array_merge(
		glob( __DIR__ . '/*.php.txt' ),
		glob( __DIR__ . '/{fc-list,langlist,wikiversions.dat}.txt', GLOB_BRACE ),
		glob( __DIR__ . '/*.yaml.txt' )
	);
	outputFiles( $viewFilenames );
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
<?php
	outputFiles( array(
		'./lsearch-global-2.1.conf',
	) );
?>
</ul>

<hr>
</body>
</html>
