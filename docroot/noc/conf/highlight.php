<?php
$srcFilename = $_GET['file'];
$viewFilename = false;
$srcDir = false;

// Build whitelist
// Only allow viewing of files of which there is a link noc/conf/* by the same name.
$viewFilenames = array_map( 'basename', glob( __DIR__ . '/*' ) );

foreach ( $viewFilenames as &$viewFilename ) {
	// Map .txt links to the original filename
	if ( substr( $viewFilename, -4 ) === '.txt' ) {
		$viewFilename =  substr( $viewFilename, 0, -4 );
	}
	if ( $srcFilename === $viewFilename ) {
		$viewFilename = $viewFilename;
		break;
	}
}

ob_start( 'ob_gzhandler' );
header( 'Content-Type: text/html; charset=utf-8' );

if ( !$viewFilename ) {
	header( "HTTP/1.1 404 Not Found" );
	if ( isset( $_SERVER['HTTP_REFERER'] ) && strpos( strtolower( $_SERVER['HTTP_REFERER'] ), 'google' ) !== false ) {
		echo "File not found\n";
		exit;
	}
	// Easter egg
	$hlHtml = highlight_string( '<'."?php\n\$secretSitePassword = 'jgmeidj28gms';\n", true );

} else {
	// Location: /home/wikipedia/common/ | docroot/noc/conf
	$baseSrcDir = dirname( dirname( dirname( __DIR__ ) ) );

	// Find the original
	if ( file_exists( "$baseSrcDir/wmf-config/$srcFilename" ) ) {
		$srcPath = "$baseSrcDir/wmf-config/$srcFilename";
		$srcDir = 'wmf-config/';
	} elseif ( file_exists( "$baseSrcDir/$srcFilename" ) ) {
		$srcPath = "$baseSrcDir/$srcFilename";
		$srcDir = '';
	}

	// How to display it
	if ( $srcDir !== false ) {
		if ( substr( $srcFilename, -4 ) === '.php' ) {

			$hlHtml = highlight_file( $srcPath, true );
			$hlHtml = str_replace( '&nbsp;', ' ', $hlHtml ); // https://bugzilla.wikimedia.org/19253
			$hlHtml = str_replace( '    ', "\t", $hlHtml ); // convert 4 spaces to 1 tab character; bug #36576
		} else {
			$hlHtml = htmlspecialchars( file_get_contents( $srcPath ) );
		}
	} else {
		$hlHtml = 'Failed to read file. :(';
	}

	$hlHtml = "<pre>$hlHtml</pre>";
}

$titleSrcFilename = htmlspecialchars( $srcFilename );
$urlSrcFilename = htmlspecialchars( urlencode( $srcDir . $srcFilename ) );
$urlViewFilename = htmlspecialchars( urlencode( $viewFilename ) );
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $titleSrcFilename; ?> - Wikimedia configuration files</title>
	<link rel="shortcut icon" href="//bits.wikimedia.org/favicon/wmf.ico">
	<link rel="stylesheet" href="../base.css">
</head>
<body>
<h1><a href="./">&laquo;</a> <?php echo $titleSrcFilename; ?></h1>
<?php
if ( $srcDir !== false ) :
?>
<p>(<a href="https://gerrit.wikimedia.org/r/gitweb?p=operations/mediawiki-config.git;a=history;f=<?php echo $urlSrcFilename; ?>;hb=HEAD">version control</a> &bull; <a href="https://gerrit.wikimedia.org/r/gitweb?p=operations/mediawiki-config.git;a=blame;f=<?php echo $urlSrcFilename; ?>;hb=HEAD">blame</a> &bull; <a href="./<?php echo $urlViewFilename; ?>">raw text</a>)</p>
<?php
endif;
?>
<hr>
<?php echo $hlHtml; ?>
</body>
</html>
