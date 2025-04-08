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
 * There are several exceptions that need to be special-cased:
 * - Domains that do not have a www prefix, so they just get m. added to the front:
 *   wikisource.org, and beta Wikidata (beta does not use www prefixes)
 * - Domains that do not have a mobile version: Wikitech and the shared auth domain (not a wiki).
 *
 * These mobile URL rules are manually mirrored by the following codebases.
 * If you change these rules, please notify the appropriate code stewards.
 *
 * 1. Varnish mobile URL redirection
 *    steward: WMF SRE Traffic
 *    Phabricator tag: #Traffic
 *    https://gerrit.wikimedia.org/g/operations/puppet/+/production/modules/varnish/templates/text-frontend.inc.vcl.erb
 * 2. Canonical wiki dataset
 *    steward: WMF Movement Insights
 *    Phabricator tag: #Movement-Insights
 *    https://gitlab.wikimedia.org/repos/movement-insights/canonical-data/-/blob/main/wiki/generate.ipynb
 *
 * If you need a history of changes, previously these rules lived under
 * $wgMobileUrlTemplate in InitialiseSettings.php.
 */
function wmfMobileUrlCallback( string $domain ): string {
	static $specialCases = [
		'wikisource.org' => 'm.wikisource.org',
		'wikidata.beta.wmflabs.org' => 'm.wikidata.beta.wmflabs.org',
		'wikitech.wikimedia.org' => false,
		// No mobile version for authentication-related domains,
		// we don't want to split cookies
		'login.wikimedia.org' => false,
		'login.wikimedia.beta.wmflabs.org' => false,
		'auth.wikimedia.org' => false,
		'auth.wikimedia.beta.wmflabs.org' => false,
	];
	if ( isset( $specialCases[$domain] ) ) {
		return $specialCases[$domain] ?: $domain;
	}

	$domainParts = explode( '.', $domain );
	if ( $domainParts[0] === 'www' ) {
		$domainParts[0] = 'm';
	} else {
		array_splice( $domainParts, 1, 0, [ 'm' ] );
	}
	return implode( '.', $domainParts );
}
