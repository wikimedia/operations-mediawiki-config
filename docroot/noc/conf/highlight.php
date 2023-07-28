<?php
// Only allow viewing of files listed in filelist.php
require_once __DIR__ . '/filelist.php';
require_once __DIR__ . '/../../../src/Noc/utils.php';
use function Wikimedia\MWConfig\Noc\wmfNocHeader;

$confFiles = wmfLoadRoutes();

// Name of file from user input
$selectedFileName = $_GET['file'] ?? null;

// Absolute path to file on disk
$selectedFilePath = $confFiles->getDiskPathByLabel( $selectedFileName );

// Path to file in mediawiki-config repository
$selectedFileRepoPath = $confFiles->getRepoPath( $selectedFilePath );

// Relative path to the symlink in conf/*
$selectedFileViewRawUrl = $confFiles->getRouteFromLabel( $selectedFileName );

$hlHtml = "";

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
	// Check the file exists
	if ( !file_exists( $selectedFilePath ) ) {
		wmfNocHeader( 'HTTP/1.1 404 Not Found' );
		$hlHtml = "Invalid symlink :(";
		$selectedFilePath = false;
	} else {
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
		<li><a href="/conf/" class="wm-nav-item-active">Config files</a><li>
		<li><a href="/db.php">Database config</a></li>
		<li><a href="/wiki.php">Wiki config</a></li>
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
