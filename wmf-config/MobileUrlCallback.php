<?php
// Used in CommonSettings.php. Split into a separate file so it can be tested.

/**
 * Desktop domain to mobile domain conversion.
 * Usually www.* becomes m.*, for everything else an 'm' is inserted after the first segment.
 * E.g.
 *   www.wikidata.org -> m.wikidata.org
 *   en.wikipedia.org -> en.m.wikipedia.org
 *   meta.wikimedia.beta.wmflabs.org
 *     -> meta.m.wikimedia.beta.wmflabs.org
 *
 * There are three exceptions that need to be special-cased:
 * - wikisource.org, which does not have a www prefix and turns into m.wikisource.org;
 * - beta Wikidata and Wikifunctions as the two www-prefixed wikis which have a beta version
 *   (beta does not use www prefixes so they just get m. added to the front);
 * - wikitech, which does not use mobile domains.
 *
 * These mobile URL rules are manually mirrored by the following codebases.
 * If you change these rules, please notify the appropriate code stewards.
 *
 * 1. Varnish mobile URL redirection
 *    steward: WMF SRE Traffic
 *    Phabricator tag: #Traffic
 *    repo: operations/puppet
 *    file: modules/varnish/templates/text-frontend.inc.vcl.erb
 * 2. Canonical wiki dataset
 *    steward: WMF Movement Insights
 *    Phabricator tag: #Movement-Insights
 *    repo: wikimedia-research/canonical-data on GitHub
 *    file: wiki/generate.ipynb
 *
 * If you need a history of changes, previously these rules lived under
 * $wgMobileUrlTemplate in InitialiseSettings.php.
 */
function wmfMobileUrlCallback( string $domain ): string {
	// special cases
	switch ( $domain ) {
		case 'wikisource.org':
			return 'm.wikisource.org';
		case 'wikitech.wikimedia.org':
			return 'wikitech.wikimedia.org';
		case 'wikidata.beta.wmflabs.org':
			return 'm.wikidata.beta.wmflabs.org';
		case 'wikifunctions.beta.wmflabs.org':
			return 'm.wikifunctions.beta.wmflabs.org';
	}

	$domainParts = explode( '.', $domain );
	if ( $domainParts[0] === 'www' ) {
		$domainParts[0] = 'm';
	} else {
		array_splice( $domainParts, 1, 0, [ 'm' ] );
	}
	return implode( '.', $domainParts );
}
