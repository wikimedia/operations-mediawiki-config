<?php
// Only allow viewing of files of which there is a copy (or link)
// in noc/conf/* by the same name.
$selectableFilepaths = glob( __DIR__ . '/*' );

// Name of file from user input
if ( isset( $_GET['file'] ) ) {
	$selectedFileName = $_GET['file'];
} else {
	$selectedFileName = null;
}

// Absolute path to file on disk
$selectedFilePath = false;

// Path to file in mediawiki-config repository
$selectedFileRepoDirName = false;
$selectedFileRepoPath = false;

// Relative path to the symlink in conf/*
$selectedFileViewRawUrl = false;

$hlHtml = "";

foreach ( $selectableFilepaths as $filePath ) {
	$fileName = basename( $filePath );
	// Map .txt links to the original filename
	if ( substr( $fileName, -4 ) === '.txt' ) {
		$fileName =  substr( $fileName, 0, -4 );
	}
	if ( $fileName === $selectedFileName ) {
		$selectedFilePath = $filePath;
		break;
	}
}
if ( PHP_SAPI !== 'cli' ) { // Don't run if executing unit tests
	ob_start( 'ob_gzhandler' );
	header( 'Content-Type: text/html; charset=utf-8' );
}

if ( !$selectedFilePath ) {
	if( $selectedFileName === null ){
		// No parameter file given, e.g. if you go to this file directly, redirect to overview
		if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ){
			$protocol = "https";
		} {
			$protocol = "http";
		}
		header( "HTTP/1.1 302 Found" );
		header( "Location: ". $protocol . "://" . $_SERVER[ 'SERVER_NAME'] ."/conf/index.php" );
		echo $protocol . "://" . $_SERVER[ 'SERVER_NAME'] ."/conf/index.php";
		exit;
	} else {
		// Parameter file IS given, but for whatever reason no filename given or filename not existing in this directory
		$hlHtml = "<pre>No valid, whitelisted filename in parameter \"file\" given.</pre>";
	}
} else {
	// Follow symlink
	if ( !file_exists( $selectedFilePath ) ) {
		$hlHtml = 'Whitelist contains nonexistant entry. :(';
		$selectedFilePath = false;
	} elseif ( !is_link( $selectedFilePath ) ) {
		$hlHtml = 'Whitelist must only contain symlinks.';
		$selectedFilePath = false;
	} else {
		$selectedFileViewRawUrl = './' . urlencode( basename( $selectedFilePath ) );
		// Resolve symlink
		// Don't use readlink since that will return a path relative to where the symlink is.
		// Which is a problem if our PWD is not the same dir (such as in unit tests).
		$selectedFilePath = realpath( $selectedFilePath );
		// Figure out path to selected file in the mediawiki-config repository
		$selectedFileRepoPath = ( basename( dirname( $selectedFilePath ) ) === 'wmf-config' ? 'wmf-config/' : '' ) . $selectedFileName;
		if ( substr( $selectedFileName, -4 ) === '.php' ) {
			$hlHtml = highlight_file( $selectedFilePath, true );
			$hlHtml = str_replace( '&nbsp;', ' ', $hlHtml ); // https://bugzilla.wikimedia.org/19253
			$hlHtml = str_replace( '    ', "\t", $hlHtml ); // convert 4 spaces to 1 tab character; bug #36576
		} else {
			$hlHtml = htmlspecialchars( file_get_contents( $selectedFilePath ) );
		}
	}

	$hlHtml = "<pre>$hlHtml</pre>";
}

$selectedFileNameEsc = htmlspecialchars( $selectedFileName );
$selectedFileRepoPathEsc = htmlspecialchars( $selectedFileRepoPath );
$selectedFileViewRawUrlEsc = htmlspecialchars( $selectedFileViewRawUrl );
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $selectedFileNameEsc; ?> - Wikimedia configuration files</title>
	<link rel="shortcut icon" href="//bits.wikimedia.org/favicon/wmf.ico">
	<link rel="stylesheet" href="../css/base.css">
</head>
<body>
<h1><a href="./">&laquo;</a> <?php echo $selectedFileNameEsc; ?></h1>
<?php
if ( $selectedFilePath !== false ) :
?>
<p>(
<a href="https://git.wikimedia.org/history/operations%2Fmediawiki-config.git/HEAD/<?php echo urlencode( $selectedFileRepoPathEsc ); ?>">version control</a> &bull;
<a href="https://git.wikimedia.org/blame/operations%2Fmediawiki-config.git/HEAD/<?php echo urlencode( $selectedFileRepoPathEsc ); ?>">blame</a> &bull;
<a href="<?php echo $selectedFileViewRawUrlEsc; ?>">raw text</a>
)</p>
<?php
endif;
?>
<hr>
<?php echo $hlHtml; ?>
</body>
</html>
