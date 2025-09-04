<?php

namespace Wikimedia\MWConfig;

use Exception;
use SiteConfiguration;

/**
 * Wrapper for loading and interpreting files from the wmf-config/ directory.
 *
 * - Get tags from dblists.
 * - Create the SiteConfiguration object.
 * - Load static arrrays from InitialiseSettings.php and other wmf-config files.
 */
class WmfConfig {

	public const SUFFIXES = [
		// For legacy reasons, wikipedias and specials both use the "wiki" suffix,
		// and for both the internal $site family will map to "wikipedia".
		'wikipedia' => 'wiki',
		'wiktionary',
		'wikiquote',
		'wikibooks',
		'wikinews',
		'wikisource',
		'wikiversity',
		'wikimedia',
		'wikivoyage',
	];

	/**
	 * Note that most wiki families are available as tags for free without
	 * needing a dblist to be maintained and read from disk, because their
	 * dbname suffix (as mapped in WmfConfig::SUFFIXES) already makes them
	 * available as SiteConfiguration tag in InitialiseSettings.php.
	 *
	 * Expand computed dblists with `./multiversion/bin/expanddblist`.
	 * When updating this list, run `composer manage-dblist update` afterwards.
	 *
	 * @var string[]
	 */
	public const DB_LISTS = [
		// 'preinstall' is called on every request from MWMultiVersion::isPreInstall
		'preinstall',
		// 'wikipedia' is the only family that needs its own index. While other families
		// have their own family suffix (::SUFFIXES), the 'wiki' suffix is used by both
		// Wikipedia and by special wikis.
		'wikipedia',
		'special',
		'private',
		'fishbowl',
		'closed',
		'flow',
		'flaggedrevs',
		'small',
		'skin-themes',
		'vector-2022-language-links',
		'legacy-vector',
		'medium',
		'wikimania',
		'wikidata',
		'wikibaserepo',
		'wikidataclient',
		'wikidataclient-test',
		'visualeditor-nondefault',
		'commonsuploads',
		'lockeddown',
		'group0',
		'group1-wikipedia',
		'group1',
		'wikitech',
		'nonecho',
		'mobile-anon-categories',
		'mobile-anon-talk',
		'modern-mainpage',
		'nowikidatadescriptiontaglines',
		'cirrussearch-big-indices',
		'rtl',
		'translate',
		'wikifunctionsclient',
		'growthexperiments',
		'parsoidrendered',
		'ores',
		'sul',
		'wikilove',
		'tempaccounts_disabled',
		'checkuser-suggested-investigations',
	];

	/** @var string[] */
	private const DB_LISTS_LABS = [
		'closed' => 'closed-labs',
		'flow' => 'flow-labs',
	];

	/**
	 * Get a list of DB names from a .dblist file.
	 *
	 * This MUST NOT be called during web requests in production.
	 * Production use case have been implemented and optimised via
	 * WmfConfig::getTagsForWiki() instead.
	 *
	 * WARNING: This function is slow and should only be called in CLI code,
	 * or in code specific to the "labs" realm.
	 *
	 * @param string $dblist
	 * @return string[]
	 */
	public static function readDbListFile( $dblist ) {
		$fileName = __DIR__ . '/../dblists/' . $dblist . '.dblist';
		$lines = @file( $fileName, FILE_IGNORE_NEW_LINES );
		if ( $lines === false ) {
			throw new Exception( "Unable to read $dblist" );
		}

		$dbs = [];
		foreach ( $lines as $line ) {
			// Ignore empty lines and lines that are comments
			if ( $line !== '' && $line[0] !== '#' ) {
				$dbs[] = $line;
			}
		}
		return $dbs;
	}

	/**
	 * Evaluate a dblist expression.
	 *
	 * This MUST NOT be called during web requests in production.
	 * Production use case have been implemented and optimised via
	 * WmfConfig::getTagsForWiki() instead.
	 *
	 * WARNING: This function is slow and should only be called in CLI code.
	 *
	 * A dblist expression contains one or more dblist file names separated by '+' and '-'.
	 *
	 * @par Example:
	 * @code
	 *  %% all.dblist - wikipedia.dblist
	 * @endcode
	 *
	 * @param string $expr
	 * @return array
	 */
	public static function evalDbExpressionForCli( $expr ) {
		$expr = trim( strtok( $expr, "#\n" ), "% " );
		$tokens = preg_split( '/ +([-+&]) +/m', $expr, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
		$result = self::readDbListFile( basename( $tokens[0], '.dblist' ) );
		// phpcs:ignore MediaWiki.ControlStructures.AssignmentInControlStructures.AssignmentInControlStructures
		// phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
		while ( ( $op = next( $tokens ) ) && ( $term = next( $tokens ) ) ) {
			$dbs = self::readDbListFile( basename( $term, '.dblist' ) );
			if ( $op === '+' ) {
				$result = array_unique( array_merge( $result, $dbs ) );
			} elseif ( $op === '-' ) {
				$result = array_diff( $result, $dbs );
			} elseif ( $op === '&' ) {
				$result = array_intersect( $result, $dbs );
			}
		}
		sort( $result );
		return $result;
	}

	/**
	 * @return array<string,string[]>
	 */
	public static function getAllDbListsForCLI() {
		$lists = [];
		foreach ( glob( __DIR__ . '/../dblists/*.dblist' ) as $filename ) {
			$basename = basename( $filename, '.dblist' );
			$lists[$basename] = self::readDbListFile( $basename );
		}
		return $lists;
	}

	/**
	 * Get a list of dblist names that contain a given wiki.
	 *
	 * This is for wmf-config array keys as interpreted by SiteConfiguration ($wgConf).
	 *
	 * This exists primarily as fast alternative to readDbListFile(),
	 * for use in getConfigGlobals() below, which is called on every
	 * request from wmf-config/CommonSettings.php.
	 *
	 * @param string $dbName The wiki's database name, e.g. 'enwiki' or 'zh_min_nanwikisource'
	 * @param string $realm
	 * @return string[] The return value is ready to use as `$wikiTags` value in
	 * SiteConfiguration methods ($wgConf).
	 */
	public static function getTagsForWiki( string $dbName, string $realm = 'production' ): array {
		$dblistsIndex = require __DIR__ . '/../dblists-index.php';

		// Tolerate absence, e.g. for a labs-only wiki.
		// The structure is verified by `DbListTest.php`.
		// The freshness is asserted in CI by `composer buildDBLists` and `composer checkclean`.
		$wikiTags = $dblistsIndex[$dbName] ?? [];

		// Replace some lists with labs-specific versions
		if ( $realm === 'labs' ) {
			foreach ( self::DB_LISTS_LABS as $tag => $fileName ) {
				// Unset any reference to the prod-specific version
				$wikiTags = array_values( array_diff( $wikiTags, [ $tag ] ) );
				$dblist = self::readDbListFile( $fileName );
				if ( in_array( $dbName, $dblist ) ) {
					$wikiTags[] = $tag;
				}
			}
		}

		return $wikiTags;
	}

	/**
	 * Compute a wiki's config globals.
	 *
	 * This is called on every request in wmf-config/CommonSettings.php
	 *
	 * @param string $dbName Database name, e.g. 'enwiki' or 'zh_min_nanwikisource'
	 * @param SiteConfiguration $siteConfiguration The wgConf object
	 * @param string $realm Realm, e.g. 'production' or 'labs'
	 * @return array
	 */
	public static function getConfigGlobals(
		string $dbName,
		SiteConfiguration $siteConfiguration,
		string $realm = 'production'
	): array {
		[ $site, $lang ] = $siteConfiguration->siteFromDB( $dbName );
		$dbSuffix = ( $site === 'wikipedia' ) ? 'wiki' : $site;
		$confParams = [
			'lang' => $lang,
			'site' => $site,
		];

		// Collect all the dblist tags associated with this wiki
		// Add a per-language tag as well
		$wikiTags = self::getTagsForWiki( $dbName, $realm );
		$wikiTags[] = $siteConfiguration->get( 'wgLanguageCode', $dbName, $dbSuffix, $confParams, $wikiTags );

		return $siteConfiguration->getAll( $dbName, $dbSuffix, $confParams, $wikiTags );
	}

	/**
	 * Override or add site settings as needed for non-production realms.
	 *
	 * This depends on 'wmfGetOverrideSettings' having been declared by a
	 * `InitialiseSettings-*.php` file.
	 *
	 * TODO: Once InitialiseSettings is array-returning, this can be injected
	 * by callers instead.
	 *
	 * @param array[] $settings wgConf-style settings array from IntialiseSettings.php
	 * @return array
	 */
	private static function applyOverrides( array $settings ): array {
		$overrides = wmfGetOverrideSettings();
		foreach ( $overrides as $key => $value ) {
			if ( substr( $key, 0, 1 ) == '-' ) {
				// Settings prefixed with - are completely overriden
				$settings[substr( $key, 1 )] = $value;
			} elseif ( isset( $settings[$key] ) ) {
				$settings[$key] = array_merge( $settings[$key], $value );
			} else {
				$settings[$key] = $value;
			}
		}

		return $settings;
	}

	/**
	 * Return static configuration
	 *
	 * @param string $realm Realm, e.g. 'production' or 'labs'
	 * @return array
	 */
	public static function getStaticConfig( string $realm = 'production' ): array {
		$configDir = __DIR__ . '/../wmf-config';
		// Use of direct addition instead of for loop in array is
		// intentional and done for performance reasons.
		$settings =
			( require $configDir . '/logos.php' ) +
			( require $configDir . '/InitialiseSettings.php' ) +
			( require $configDir . '/core-Namespaces.php' ) +
			( require $configDir . '/core-Permissions.php' ) +
			( require $configDir . '/ext-ORES.php' ) +
			( require $configDir . '/ext-CirrusSearch.php' ) +
			( require $configDir . '/ext-EventLogging.php' ) +
			( require $configDir . '/ext-EventStreamConfig.php' ) +
			( require $configDir . '/ext-GrowthExperiments.php' ) +
			( require $configDir . '/skin-Minerva.php' );

		if ( $realm !== 'production' ) {
			// Override for Beta Cluster and other realms.
			// Ref: InitialiseSettings-labs.php
			require_once $configDir . "/InitialiseSettings-$realm.php";
			$settings = self::applyOverrides( $settings );
		}

		return $settings;
	}

}
