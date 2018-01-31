<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file contains the setup for transwiki importing across the WMF cluster.
# This file is referenced from an include in CommonSettings.php

# NOTE: Labs only for now. Will be put into production later

/**
 * Set up import sources for transwiki import (T17583)
 *
 * @param array $sources
 */
function wmfImportSources( &$sources ) {
	global $wgConf, $IP, $lang;
	$wikipedias = array_flip( MWWikiversions::readDbListFile(
		getRealmSpecificFilename( "$IP/../wikipedia.dblist" ) ) );
	$privates = array_flip( MWWikiversions::readDbListFile(
		getRealmSpecificFilename( "$IP/../private.dblist" ) ) );

	// REMEMBER when editing this function, the values here are *interwiki prefixes*.
	// Sometimes the interwiki map does things you don't expect.
	// Look at dumpInterwiki.php in WikimediaMaintenance for guidance.

	// Enforce a sensible order
	$sources = [
		// Put really common special wikis first
		'meta', 'commons', 'incubator',
		'wikipedia' => [],
		'wiktionary' => [],
		'wikibooks' => [],
		'wikinews' => [],
		'wikiquote' => [],
		'wikisource' => [ 'oldwikisource' ],
		'wikiversity' => [ 'betawikiversity' ],
		'wikivoyage' => [],
		'chapter' => [],
		// Add a selection of non-private special wikis
		'foundation', 'mediawikiwiki', 'nostalgia', 'outreach', 'strategy',
		'tenwiki', 'testwiki', 'test2wiki', 'testwikidata', 'usability',
		'wikidata', 'wikispecies', 'wikitech',
		'wmania' => [
			'wm2005', 'wm2006', 'wm2007', 'wm2008', 'wm2009', 'wm2010', 'wm2011',
			'wm2012', 'wm2013', 'wm2014', 'wm2015', 'wm2016',
		],
	];

	// Add all regular language projects as import sources
	foreach ( $wgConf->getLocalDatabases() as $dbname ) {
		// No importing from private wikis
		if ( isset( $privates[$dbname] ) ) {
			continue;
		}

		list( $project, $subdomain ) = $wgConf->siteFromDB( $dbname );
		if ( $project === 'wikimedia' ) {
			$sources['chapter'][] = $subdomain;
		} elseif ( $subdomain === 'en' || $subdomain === $lang ) {
			// Put $lang and en at the top for convenience
			array_unshift( $sources[$project], $subdomain );
		} elseif (
			// Don't list sites under "wikipedia" that are not Wikipedias (e.g. meta)
			( $project !== 'wikipedia' || isset( $wikipedias[$dbname] ) ) &&
			$subdomain !== 'beta' // Beta Wikiversity is handled separately
		) {
			$sources[$project][] = $subdomain;
		}
	}
}
