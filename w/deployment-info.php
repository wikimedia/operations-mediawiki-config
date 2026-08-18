<?php
require_once __DIR__ . '/../multiversion/MWMultiVersion.php';

header( 'Cache-Control: no-cache' );

$file = MEDIAWIKI_DEPLOYMENT_DIR . '/deployment-info.json';

// "?all" reports the whole deployment: the checkouts of every MediaWiki
// version, regardless of which wiki the request went to.
if ( isset( $_GET['all'] ) ) {
	header( 'Content-Type: application/json; charset=utf-8' );

	if ( !is_readable( $file ) ) {
		http_response_code( 404 );
		echo "{}\n";
		return;
	}

	readfile( $file );
	return;
}

// Resolve before any output. MWMultiVersion writes a plain text error and exits
// if the request host does not map to a wiki.
$multiVersion = MWMultiVersion::initializeFromServerData(
	$_SERVER['SERVER_NAME'] ?? null,
	$_SERVER['SCRIPT_NAME'] ?? null,
	$_SERVER['PATH_INFO'] ?? null,
	$_SERVER['REQUEST_URI'] ?? null
);

header( 'Content-Type: application/json; charset=utf-8' );

if ( $multiVersion->isMissing() ) {
	// The wiki has no version, so no code runs for it.
	echo json_encode( [
		'dbname' => $multiVersion->getDatabase(),
		'version' => null,
		'branch' => null,
		'checkouts' => [],
	], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), "\n";
	return;
}

$version = $multiVersion->getVersionNumber();

$info = is_readable( $file ) ? json_decode( (string)file_get_contents( $file ), true ) : null;

// Report nothing rather than something incomplete. Without the checkouts of the
// version that this wiki runs, the response would look like a wiki that runs
// none of that code.
if ( !is_array( $info ) || !isset( $info['versions'][$version] ) ) {
	http_response_code( 404 );
	echo "{}\n";
	return;
}

echo json_encode( [
	'dbname' => $multiVersion->getDatabase(),
	'version' => $version,
	// wikiversions holds "php-master" or "php-X"; the core branch is "master" or "wmf/X".
	'branch' => $version === 'master' ? $version : "wmf/$version",
	// scap keeps the checkouts of each version apart from the ones that every
	// version uses, because a wiki runs one version.
	'checkouts' => array_merge( $info['common'] ?? [], $info['versions'][$version] ),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), "\n";
