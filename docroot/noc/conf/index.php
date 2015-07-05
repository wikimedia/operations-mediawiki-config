<?php
require_once( '/srv/mediawiki/multiversion/activeMWVersions.php' );

	function outputFiles( $viewFilenames ) {
		$viewFilenames = array_map( 'basename', $viewFilenames );
		natsort( $viewFilenames );
		foreach ( $viewFilenames as $viewFilename ) {
			$srcFilename = substr( $viewFilename, -4 ) === '.txt'
				? substr( $viewFilename, 0, -4 )
				: $viewFilename;
			echo "\n<li>";

			echo '<a href="./' . htmlspecialchars( urlencode( $viewFilename ) ) . '">'
				. htmlspecialchars( $srcFilename ) . '</a>';
			echo '</li>';
		}
	}

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Wikimedia configuration files</title>
	<link rel="shortcut icon" href="//bits.wikimedia.org/favicon/wmf.ico">
	<link rel="stylesheet" href="../css/base.css">
</head>
<body>

<p>Below is a selection of Wikimedia configuration files available for easy viewing.
	The files are dynamically generated and are up-to-date with the current versions on our servers.
	Most other important configuration files are available in public version control in one of the following repositories:
</p>
<ul>
	<li><a href="https://git.wikimedia.org/tree/operations%2Fmediawiki-config.git">operations/mediawiki-config.git</a></li>
	<li><a href="https://git.wikimedia.org/tree/operations%2Fpuppet.git">operations/puppet.git</a></li>
</ul>

<hr>
<p>Currently active MediaWiki versions: <?php echo implode( ', ', getActiveWikiVersions() ); ?></p>
<hr>

<h3><img src="./document.png" alt=""> CDB files</h3>
<ul>
<?php
	outputFiles( glob( __DIR__ . '/*.cdb' ), false );
?>
</ul>

</body>
</html>
