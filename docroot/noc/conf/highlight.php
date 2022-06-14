<?php
// Only allow viewing of files of which there is a copy (or link)
// in noc/conf/* by the same name.
$selectableFilepaths = array_merge( glob( __DIR__ . '/*' ),
	glob( __DIR__ . '/dblists/*.dblist' ) );

// Name of file from user input
$selectedFileName = $_GET['file'] ?? null;

// Absolute path to file on disk
$selectedFilePath = false;

// Path to file in mediawiki-config repository
$selectedFileRepoPath = false;

// Relative path to the symlink in conf/*
$selectedFileViewRawUrl = false;

$hlHtml = "";

foreach ( $selectableFilepaths as $selectableFilePath ) {
	$relativePath = str_replace( __DIR__ . '/', '', $selectableFilePath );
	$selectableName = preg_replace( '/\.txt$/', '', $relativePath );
	if ( $selectableName === $selectedFileName ) {
		$selectedFilePath = $selectableFilePath;
		$selectedFileViewRawUrl = $relativePath;
		break;
	}
}

// Don't (re)run if executing unit tests
if ( !function_exists( 'wmfNocHeader' ) ) {
	/**
	 * @param string $header
	 */
	function wmfNocHeader( $header ): void {
		// Don't emit headers in unit tests
		if ( PHP_SAPI !== 'cli' ) {
			header( $header );
		}
	}
}

wmfNocHeader( 'Content-Type: text/html; charset=utf-8' );

if ( !$selectedFilePath ) {
	if ( $selectedFileName === null ) {
		// If no 'file' is given (e.g. accessing this file directly), redirect to overview
		wmfNocHeader( 'HTTP/1.1 302 Found' );
		wmfNocHeader( 'Location: ./' );
		echo '<a href="./">Redirect</a>';
		exit;
	} else {
		wmfNocHeader( 'HTTP/1.1 404 Not Found' );
		// Parameter file is given, but not existing in this directory
		$hlHtml = '<p>Invalid filename given.</p>';
	}
} else {
	// Follow symlink
	if ( !file_exists( $selectedFilePath ) ) {
		wmfNocHeader( 'HTTP/1.1 404 Not Found' );
		$hlHtml = "Invalid symlink :(";
		$selectedFilePath = false;
	} else {
		// Resolve symlink
		// Don't use readlink since that will return a path relative to where the symlink is.
		// Which is a problem if our PWD is not the same dir (such as in unit tests).
		$selectedFilePath = realpath( $selectedFilePath );
		// Figure out path to selected file in the mediawiki-config repository
		$baseDir = basename( dirname( $selectedFilePath ) );
		if ( in_array( $baseDir, [ 'wmf-config' ] ) ) {
			$selectedFileRepoPath = $baseDir . '/' . $selectedFileName;
		} else {
			$selectedFileRepoPath = $selectedFileName;
		}

		if ( substr( $selectedFileName, -4 ) === '.php' ) {
			$hlHtml = highlight_file( $selectedFilePath, true );
			// Avoid non-breaking spaces, T21253
			$hlHtml = str_replace( '&nbsp;', ' ', $hlHtml );
			// Convert 4 spaces to a tab character, T38576
			$hlHtml = str_replace( '    ', "\t", $hlHtml );
		} else {
			$hlHtml = htmlspecialchars( file_get_contents( $selectedFilePath ) );
		}

		$hlHtml = "<pre>$hlHtml</pre>";
	}
}

$selectedFileNameEsc = htmlspecialchars( $selectedFileName );
$selectedFileViewRawUrlEsc = htmlspecialchars( $selectedFileViewRawUrl );
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="utf-8">
	<title><?php echo $selectedFileNameEsc; ?> – Wikimedia NOC</title>
	<link rel="shortcut icon" href="/static/favicon/wmf.ico">
	<link rel="stylesheet" href="../css/base.css">
</head>
<body>
<header><div class="wm-container">
	<a role="banner" href="/" title="Visit the home page"><em>Wikimedia</em> NOC</a>
</div></header>

<main role="main"><div class="wm-container">

	<nav class="wm-site-nav"><ul class="wm-nav">
		<li><a href="./">MediaWiki config</a><li>
		<li><a href="../db.php">Database config</a></li>
	</ul></nav>

	<article>

		<h1><a href="./">&laquo;</a> <?php echo $selectedFileNameEsc; ?></h1>
<?php
if ( $selectedFilePath !== false ) {
?>
<p>
<a href="https://gerrit.wikimedia.org/r/q/project:operations/mediawiki-config+branch:master+file:<?php echo urlencode( $selectedFileName ); ?>">code review</a> &bull;
<a href="https://gerrit.wikimedia.org/g/operations/mediawiki-config/+log/master/<?php echo urlencode( $selectedFileRepoPath ); ?>">git-log</a> &bull;
<a href="https://gerrit.wikimedia.org/g/operations/mediawiki-config/+blame/master/<?php echo urlencode( $selectedFileRepoPath ); ?>?blame=1">git-blame</a> &bull;
<a href="<?php echo $selectedFileViewRawUrlEsc; ?>">raw text</a>
</p>
<?php
}
?>
<hr>
<?php echo $hlHtml; ?>

	</article>

</div></main>

</body>
</html>
