<?php
/**
 * Serve static files in a multiversion-friendly way.
 *
 * See https://phabricator.wikimedia.org/T99096 for design requirements.
 *
 * Overview:
 *
 * - multiversion requires the MediaWiki script directory (/w) to be shared
 *   across all domains. Files in /w are generic and load the real MediaWiki
 *   entry point based on the currently configured version based on host name.
 * - MediaWiki configuration sets $wgResourceBasePath to "/w/static".
 * - Apache configuration rewrites "/w/static/*" to /w/static.php (this file).
 * - static.php streams the file from the appropiate MediaWiki branch directory.
 *
 * In addition to the above, this file also looks in older MediaWiki branch
 * directories in order to support references from our static HTML cache for 30 days.
 * While responses from static may also be cached, they are not linked or guruanteed.
 * As such, this file must be able to respond to requests for older resources as well.
 *
 * StatD metrics:
 *
 * - wmfstatic.success.<cacheMaxAge>.<branchName>
 * - wmfstatic.notfound
 * - wmfstatic.fallback
 */
require_once './MWVersion.php';
require getMediaWiki( 'includes/WebStart.php' );

function wmfStaticShowError( $message ) {
	header( 'Content-Type: text/plain; charset=utf-8' );
	echo "$message\n";
}

/**
 * Stream file from disk to web response
 * Based on StreamFile::stream()
 * @param string $filePath
 * @param int $maxAge Time in seconds to cache successful response
 */
function wmfStaticStreamFile( $filePath, $maxAge = 500 ) {
	$stat = stat( $filePath );
	if ( !$stat ) {
		header( 'HTTP/1.1 404 Not Found' );
		wmfStaticShowError( 'Unknown file path' );
		return;
	}

	$ctype = StreamFile::contentTypeFromPath( $filePath, /* safe: not for upload */ false );
	if ( !$ctype || $ctype === 'unknown/unknown' ) {
		header( 'HTTP/1.1 400 Bad Request' );
		wmfStaticShowError( 'Invalid file type' );
		return;
	}

	header( 'Last-Modified: ' . wfTimestamp( TS_RFC2822, $stat['mtime'] ) );
	header( "Content-Type: $ctype" );
	$maxAge = (int) $maxAge;
	header( "Cache-Control: public, s-maxage=$maxAge, max-age=$maxAge" );

	if ( !empty( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ) {
		$ims = preg_replace( '/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE'] );
		if ( wfTimestamp( TS_UNIX, $stat['mtime'] ) <= strtotime( $ims ) ) {
			ini_set( 'zlib.output_compression', 0 );
			header( 'HTTP/1.1 304 Not Modified' );
			return;
		}
	}

	header( 'Content-Length: ' . $stat['size'] );
	readfile( $filePath );
}

function wmfStaticRespond() {
	global $wgScriptPath;

	if ( !isset( $_SERVER['REQUEST_URI'] ) ) {
		header( 'HTTP/1.1 500 Internal Server Error' );
		wmfStaticShowError( 'Invalid request' );
		return;
	}

	// Strip query parameters
	$uriPath = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

	// Strip prefix
	$urlPrefix = "$wgScriptPath/static/";
	if ( strpos( $uriPath, $urlPrefix ) !== 0 ) {
		header( 'HTTP/1.1 400 Bad Request' );
		wmfStaticShowError( 'Bad request' );
		return;
	}
	$path = substr( $uriPath, strlen( $urlPrefix ) );

	$cacheLong = 30 * 24 * 3600; // 30 days
	$cacheShort = 5 * 60; // 5 minutes

	// Validation hash
	$urlHash = isset( $_SERVER['QUERY_STRING'] ) ? $_SERVER['QUERY_STRING'] : false;
	$fallback = false;
	$maxAge = $cacheShort;

	// Get branch dirs and sort with newest first
	$branchDirs = MWWikiversions::getAvailableBranchDirs();
	usort( $branchDirs, function ( $a, $b ) {
		return version_compare( $b, $a );
	} );

	$stats = RequestContext::getMain()->getStats();

	// Try each version in descending order
	// - Requests without a validation hash will get the latest version.
	//   (If the file no longer exists in the latest version, it will correctly
	//   fall back to the last available version.)
	// - Requests with validation hash get the first match. If none found, falls back to the last
	//   available version. Cache expiry is shorted in that case to allow eventual-consistency and
	//   avoids cache poisoning (see T47877).
	foreach ( $branchDirs as $branchDir ) {
		// Use realpath() to prevent path escalation through e.g. "../"
		$filePath = realpath( "$branchDir/$path" );
		if ( !$filePath ) {
			continue;
		}

		if ( strpos( $filePath, $branchDir ) !== 0 ) {
			header( 'HTTP/1.1 400 Bad Request' );
			wmfStaticShowError( 'Bad request' );
			return;
		}

		if ( !file_exists( $filePath ) ) {
			continue;
		}

		if ( $hash ) {
			// Set fallback to the newest existing version.
			if ( !$fallback ) {
				$fallback = $branchDir;
			}
			// Match OutputPage::transformFilePath()
			$fileHash = substr( md5_file( $filePath ), 0, 5 );
			if ( $fileHash !== $urlHash ) {
				// Hash mis-match, continue search in older branches
				continue;
			}
			// Cache hash-validated responses for long
			$maxAge = $cacheLong;
		}


		wmfStaticStreamFile( $filePath, $maxAge );

		$branchName = basename( $branchDir );
		$stats->increment( "wmfstatic.success.$maxAge.$branchName" );
		return;
	}

	if ( !$fallback ) {
		header( 'HTTP/1.1 404 Not Found' );
		wmfStaticShowError( 'Unknown file' );
		$stats->increment( 'wmfstatic.notfound' );
		return;
	}

	wmfStaticStreamFile( "$fallback/$path", $maxAge );
	$stats->increment( 'wmfstatic.fallback' );
	return;
}

wfResetOutputBuffers();
wmfStaticRespond();
