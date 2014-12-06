<?php
/**
 * Generate robots.txt rules for the current wiki.
 *
 * The rules output will be:
 * - a global exclusion (Zero, beta)
 * - the local robots.txt file
 * - the contents of the wiki's [[Mediawiki:robots.txt]] (possibly empty)
 */

require_once './MWVersion.php';
require getMediaWiki( 'includes/WebStart.php' );

$robotsfile = '/srv/mediawiki/robots.txt';
$dontIndex = "User-agent: *\nDisallow: /\n";
$extraRules = '';

header( 'Content-Type: text/plain; charset=utf-8' );
header( 'X-Language: ' . $lang );
header( 'X-Site: ' . $site );
header( 'Vary: X-Subdomain' );
header( 'Cache-Control: s-maxage=3600, must-revalidate, max-age=0' );

if ( isset( $_SERVER['HTTP_X_SUBDOMAIN'] ) &&
	$_SERVER['HTTP_X_SUBDOMAIN'] === 'ZERO'
) {
	// Zero rated domains are not indexable
	echo $dontIndex;

} elseif ( $wmfRealm == 'labs' ) {
	// Beta should not be indexed
	echo $dontIndex;

} else {
	$lastmod = filemtime( $robotsfile );

	// Look for a special robots.txt page that can add new rules
	$robotsTitle = Title::makeTitle( 'Mediawiki:robots.txt' );
	$robotsRevision = Revision::newFromTitle( $robotsTitle );

	if ( $robotsRevision !== null ) {
		header( 'X-Article-ID: ' . $robotsTitle->getArticleID() );

		$extraRules = $robotsRevision->getContent()->getNativeData();

		// Change lastmod to the newer of file and wiki changes
		$wikimod = wfTimestamp( TS_UNIX, $robotsRevision->getTimestamp() );
		$lastmod = max( $lastmod, $wikimod );
	}

	// No one will ever understand why RFC 2616 was chosen for header dates
	$httpDate = gmdate( 'D, j M Y H:i:s', $lastmod ) . ' GMT';
	header( "Last-modified: {$httpDate}" );
}

// Output shared robots rules
$robots = fopen( $robotsfile, 'rb' );
fpassthru( $robots );

// Ouput wiki managed robots rules (possibly empty)
echo "#\n#\n#----------------------------------------------------------#\n#\n#\n#\n" . $extraRules;
