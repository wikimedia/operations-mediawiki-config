<?php
// Only allow viewing of files of which there is a copy (or link)
// in noc/conf/* by the same name.
$selectableFilepaths = glob( __DIR__ . '/**' );

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
// Don't run if executing unit tests
if ( PHP_SAPI !== 'cli' ) {
	header( 'Content-Type: text/html; charset=utf-8' );
}

if ( !$selectedFilePath ) {
	if ( $selectedFileName === null ) {
		// If no 'file' is given (e.g. accessing this file directly), redirect to overview
		header( 'HTTP/1.1 302 Found' );
		header( 'Location: ./index.php' );
		echo '<a href="./index.php">Redirect</a>';
		exit;
	} else {
		// Parameter file is given, but not existing in this directory
		$hlHtml = '<p>Invalid filename given.</p>';
	}
} else {
	// Follow symlink
	if ( !file_exists( $selectedFilePath ) ) {
		$hlHtml = "Whitelist contains nonexistent entry: $selectedFilePath :(";
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
		$baseDir = basename( dirname( $selectedFilePath ) );
		if ( in_array( $baseDir, [ 'dblists', 'wmf-config' ] ) ) {
			$selectedFileRepoPath = $baseDir . '/' . $selectedFileName;
		} else {
			$selectedFileRepoPath = $selectedFileName;
		}

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
	<link rel="shortcut icon" href="//www.wikimedia.org/static/favicon/wmf.ico">
	<link rel="stylesheet" href="../css/base.css">
</head>
<body>
<h1><a href="./">&laquo;</a> <?php echo $selectedFileNameEsc; ?></h1>
<?php
if ( $selectedFilePath !== false ) {
?>
<p>(
<a href="https://phabricator.wikimedia.org/diffusion/OMWC/history/master/<?php echo $selectedFileRepoPathEsc; ?>">version control</a> &bull;
<a href="https://phabricator.wikimedia.org/diffusion/OMWC/browse/master/<?php echo $selectedFileRepoPathEsc; ?>?blame=1">blame</a> &bull;
<a href="<?php echo $selectedFileViewRawUrlEsc; ?>">raw text</a>
)</p>
<?php
}
?>
<hr>
<?php echo $hlHtml; ?>
</body>
</html>
