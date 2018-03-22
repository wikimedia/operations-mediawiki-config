<?php
	/**
	 * @param array $viewFilenames
	 * @param bool $highlight
	 */
	function outputFiles( $viewFilenames, $highlight = true, $prefixFunc = 'basename' ) {
		$viewFilenames = array_map( $prefixFunc, $viewFilenames );
		natsort( $viewFilenames );
		foreach ( $viewFilenames as $viewFilename ) {
			$srcFilename = substr( $viewFilename, -4 ) === '.txt'
				? substr( $viewFilename, 0, -4 )
				: $viewFilename;
			echo "\n<li>";

			if ( $highlight ) {
				echo '<a href="./highlight.php?file=' . htmlspecialchars( $srcFilename ) . '">'
					. htmlspecialchars( $srcFilename );
				echo '</a> (<a href="./' . htmlspecialchars( $viewFilename ) . '">raw text</a>)';
			} else {
				echo '<a href="./' . htmlspecialchars( $viewFilename ) . '">'
					. htmlspecialchars( $srcFilename ) . '</a>';
			}
			echo '</li>';
		}
	}

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Wikimedia configuration files</title>
	<link rel="shortcut icon" href="/static/favicon/wmf.ico">
	<link rel="stylesheet" href="../css/base.css">
</head>
<body>

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
	echo str_replace( ' ', ', ', exec( '/usr/bin/scap wikiversions-inuse' ) );
?></p>
<hr>

<h2><img src="./images/source_php.png" alt=""> MediaWiki configuration</h2>
<ul>
<?php
	$viewFilenames = array_merge(
		glob( __DIR__ . '/*.php.txt' ),
		glob( __DIR__ . '/{fc-list,langlist*,wikiversions*.json,extension-list}', GLOB_BRACE ),
		glob( __DIR__ . '/*.yaml.txt' )
	);
	outputFiles( $viewFilenames );
?>
</ul>

<h3><img src="./images/document.png" alt=""> Database lists</h3>
<ul>
<?php
	outputFiles( glob( __DIR__ . '/dblists/*.dblist' ), true, function( $name ) {
		return str_replace( __DIR__ . '/', '', $name );
	} );
?>
</ul>

<hr>
</body>
</html>
