<?php
// Ensure that we're not casting any types
declare( strict_types = 1 );

class StaticSettingsTest extends PHPUnit\Framework\TestCase {

	protected $variantSettings = [];
	private $originalWmfDC;

	public function setUp() : void {
		// This global is set by multiversion/MWRealm.php
		$this->originalWmfDC = $GLOBALS['wmfDatacenter'];
		$GLOBALS['wmfDatacenter'] = 'testvalue';

		$this->variantSettings = wmfGetVariantSettings();
	}

	public function tearDown() : void {
		$GLOBALS['wmfDatacenter'] = $this->originalWmfDC;
	}

	public function testConfigIsScalar() {
		foreach ( $this->variantSettings as $variantSetting => $settingsArray ) {
			$this->assertTrue( is_array( $settingsArray ), "Each variant setting set must be an array, but $variantSetting is not" );

			foreach ( $settingsArray as $wiki => $value ) {
				$this->assertTrue(
					is_scalar( $value ) || is_array( $value ) || $value === null,
					"Each variant setting must be scalar, an array, or null, but $variantSetting is not for $wiki."
				);
			}
		}
	}

	public function testVariantUrlsAreLocalhost() {
		$knownToContainExternalURLs = [
			// These are user-facing, not in-cluster, and are fine
			'wgCanonicalServer', 'wgServer', 'wgUploadPath', 'wgRedirectSources', 'wgUploadNavigationUrl', 'wgScorePath', 'wgUploadMissingFileUrl', 'wgRightsUrl', 'wgWelcomeSurveyPrivacyPolicyUrl', 'wgWBCitoidFullRestbaseURL', 'wgGlobalRenameBlacklist',
			// FIXME: Set in mobile.php?
			'wgMFPhotoUploadEndpoint',
			// FIXME: Just… wow. By name, this should be a boolean.
			'wmgUseFileExporter',
			// FIXME: Just set in wikibase.php? Most of these are user-facing.
			'wgEntitySchemaShExSimpleUrl', 'wmgWBRepoSettingsSparqlEndpoint', 'wmgWikibaseClientRepoUrl', 'wmgWikibaseClientRepoConceptBaseUri', 'wmgWikibaseClientPropertyOrderUrl', 'wmgWBRepoConceptBaseUri', 'wgArticlePlaceholderRepoApiUrl', 'wgMediaInfoExternalEntitySearchBaseUri', 'wmgWikibaseSSRTermboxServerUrl',
			// FIXME: Just set in CirrusSearch-production?
			'wgWMEClientErrorIntakeURL',
		];

		foreach ( $this->variantSettings as $variantSetting => $settingsArray ) {
			if ( in_array( $variantSetting, $knownToContainExternalURLs ) ) {
				// Skip for now.
				continue;
			}

			foreach ( $settingsArray as $wiki => $value ) {
				if ( !is_string( $value ) ) {
					continue;
				}

				$this->assertFalse(
					strpos( $value, '//' ) !== false && strpos( $value, 'localhost' ) === false,
					"Variant URLs must point to localhost, or be defined in CommonSettings, but $variantSetting for $wiki is '$value'."
				);
			}
		}
	}

	public function testUseFlagsAreBoolean() {
		$knownToBeBad = [
			'wgCirrusSearchUseCompletionSuggester',
			'wgCirrusSearchUseIcuFolding',
			'wgMFUseDesktopSpecialHistoryPage',
			'wmgUseCognate',
			'wmgUseFileExporter',
			'wmgUseFileImporter',
		];

		foreach ( $this->variantSettings as $variantSetting => $settingsArray ) {
			if ( preg_match( '/Use[A-Z]/', $variantSetting ) ) {
				if ( in_array( $variantSetting, $knownToBeBad ) ) {
					// Skip for now.
					continue;
				}

				foreach ( $settingsArray as $wiki => $value ) {
					$this->assertTrue(
						is_bool( $value ),
						"Use flags should be boolean, but $variantSetting for $wiki is " . ( is_array( $value ) ? "an array" : "'" . (string)$value . "'" ) . "."
					);
				}
			}
		}
	}

	public function testLogos() {
		$scalarLogoKeys = [
			'1x' => 'wmgSiteLogo1x',
			'1.5x' => 'wmgSiteLogo1_5x',
			'2x' => 'wmgSiteLogo2x',
		];

		$pairedSizes = [ '1.5x', '2x' ];

		// Test if all scalar logos exist
		foreach ( $scalarLogoKeys as $size => $key ) {
			foreach ( $this->variantSettings[ $key ] as $db => $entry ) {
				$this->assertFileExists( __DIR__ . '/../..' . $entry, "$db has non-existent $size logo in $key" );

				if ( in_array( $size, $pairedSizes ) ) {
					$otherSize = array_values( array_diff( $pairedSizes, [ $size ] ) )[ 0 ];
					$otherKey = $scalarLogoKeys[ $otherSize ];

					$this->assertArrayHasKey(
						$db,
						$this->variantSettings[ $otherKey ],
						"$db has a logo set for $size in $key but not for $otherSize in $otherKey"
					);

					$baseKey = $scalarLogoKeys['1x'];

					$this->assertArrayHasKey(
						$db,
						$this->variantSettings[ $baseKey ],
						"$db has an over-ride HD logo set for $size in $key but not for regular resoltion in $baseKey"
					);
				}
			}
		}

		// Test if all wordmark logo values are set and the file exists
		foreach ( $this->variantSettings[ 'wmgSiteLogoWordmark' ] as $db => $entry ) {
			if ( !count( $entry ) ) {
				// Wordmark logo over-ridden to unset.
				continue;
			}
			$this->assertArrayHasKey( 'src', $entry, "$db has no path set for its wordmark logo in wmgSiteLogoWordmark" );
			$this->assertFileExists( __DIR__ . '/../..' . $entry['src'], "$db has non-existent wordmark logo in wmgSiteLogoWordmark" );
			$this->assertArrayHasKey( 'width', $entry, "$db has no width set for its wordmark logo in wmgSiteLogoWordmark" );
			$this->assertArrayHasKey( 'height', $entry, "$db has no height set for its wordmark logo in wmgSiteLogoWordmark" );
		}
	}

	public function testwgExtraNamespaces() {
		foreach ( $this->variantSettings['wgExtraNamespaces'] as $db => $entry ) {
			foreach ( $entry as $number => $namespace ) {
				// Test for invalid spaces
				$this->assertFalse( strpos( $namespace, ' ' ), "Unexpected space in '$number' namespace title for $db, use underscores instead" );

				// Test for invalid colons
				$this->assertFalse( strpos( $namespace, ':' ), "Unexpected colon in '$number' namespace title for $db, final colon is not needed and can be removed" );

				// Test namespace numbers
				if ( $number < 100 || in_array( $number, [ 828, 829 ] ) ) {
					continue; // It's not an extra namespace, do not test
				}
				if ( $number % 2 == 0 ) {
					$this->assertTrue( array_key_exists( $number + 1, $entry ), "Namespace $namespace (ID $number) for $db doesn't have corresponding talk namespace set" );
				} else {
					$this->assertTrue( array_key_exists( $number - 1, $entry ), "Namespace $namespace (ID $number) for $db doesn't have corresponding non-talk namespace set" );
				}
			}
		}
	}

	public function testMetaNamespaces() {
		foreach ( $this->variantSettings['wgMetaNamespace'] as $db => $namespace ) {
			// Test for invalid spaces
			$this->assertFalse( strpos( $namespace, ' ' ), "Unexpected space in meta namespace title for $db, use underscores instead" );

			// Test for invalid colons
			$this->assertFalse( strpos( $namespace, ':' ), "Unexpected colon in meta namespace title for $db, final colon is not needed and should be removed" );
		}

		foreach ( $this->variantSettings['wgMetaNamespaceTalk'] as $db => $namespace ) {
			// Test for invalid spaces
			$this->assertFalse( strpos( $namespace, ' ' ), "Unexpected space in meta talk namespace title for $db, use underscores instead" );

			// Test for invalid colons
			$this->assertFalse( strpos( $namespace, ':' ), "Unexpected colon in meta talk namespace title for $db, final colon is not needed and should be removed" );
		}
	}

	public function testMustHaveConfigs() {
		$dbLists = DBList::getLists();
		// This list EXCLUDES special. See processing below.
		$wikiFamilies = [ 'wikipedia', 'wikibooks', 'wikimedia', 'wikinews', 'wikiquote', 'wikisource', 'wikiversity', 'wikivoyage', 'wiktionary' ];

		$mustHaveWikiFamilyConfig = [ 'wgServer', 'wgCanonicalServer' ];
		foreach ( $mustHaveWikiFamilyConfig as $key => $setting ) {
			foreach ( $wikiFamilies as $j => $family ) {
				$this->assertArrayHasKey(
					$family,
					$this->variantSettings[ $setting ],
					"Family '$family' has no default $setting."
				);
			}
		}

		$mustHaveConfigForSpecialWikis = [ 'wgServer', 'wgCanonicalServer' ];

		// TODO: Fix these and fold them into the above.
		$mustHaveConfigForSpecialWikisButSomeDoNot = [ 'wgLanguageCode' ];
		$knownFailures = [
			'advisorswiki',
			'boardgovcomwiki',
			'boardwiki',
			'commonswiki',
			'electcomwiki',
			'foundationwiki',
			'internalwiki',
			'labswiki',
			'labtestwiki',
			'loginwiki',
			'mediawikiwiki',
			'metawiki',
			'movementroleswiki',
			'nostalgiawiki',
			'outreachwiki',
			'sourceswiki',
			'spcomwiki',
			'specieswiki',
			'techconductwiki',
			'testcommonswiki',
			'testwikidatawiki',
			'wikidatawiki',
			'wikimaniawiki',
			'wikimania2005wiki',
			'wikimania2006wiki',
			'wikimania2007wiki',
			'wikimania2008wiki',
			'wikimania2009wiki',
			'wikimania2010wiki',
			'wikimania2011wiki',
			'wikimania2012wiki',
			'wikimania2013wiki',
			'wikimania2014wiki',
			'wikimania2015wiki',
			'wikimania2016wiki',
			'wikimania2017wiki',
			'wikimania2018wiki',
		];

		foreach ( $dbLists['special'] as $i => $db ) {
			foreach ( $mustHaveConfigForSpecialWikis as $j => $setting ) {
				$this->assertArrayHasKey(
					$db,
					$this->variantSettings[ $setting ],
					"Wiki '$db' is in the 'special' family but has no $setting set."
				);
			}

			foreach ( $mustHaveConfigForSpecialWikisButSomeDoNot as $j => $setting ) {
				if ( in_array( $db, $knownFailures ) ) {
					continue;
				}

				$this->assertArrayHasKey(
					$db,
					$this->variantSettings[ $setting ],
					"Wiki '$db' is in the 'special' family but has no $setting set."
				);
			}
		}
	}

	public function testwgServer() {
		foreach ( $this->variantSettings['wgCanonicalServer'] as $db => $entry ) {
			// Test if wgCanonicalServer start with https://
			$this->assertStringStartsWith( "https://", $entry, "wgCanonicalServer for $db doesn't start with https://" );
		}

		foreach ( $this->variantSettings['wgServer'] as $db => $entry ) {
			// Test if wgServer start with //
			$this->assertStringStartsWith( "//", $entry, "wgServer for $db doesn't start with //" );
		}
	}

	public function testOnlyExistingWikis() {
		$dblistNames = array_keys( DBList::getLists() );
		$langs = file( __DIR__ . "/../../langlist", FILE_IGNORE_NEW_LINES );
		foreach ( $this->variantSettings as $config ) {
			foreach ( $config as $db => $entry ) {
				$dbNormalized = str_replace( "+", "", $db );
				$this->assertTrue(
					in_array( $dbNormalized, $dblistNames ) ||
					DBList::isInDblist( $dbNormalized, "all" ) ||
					in_array( $dbNormalized,  $langs ) ||
					in_array( $dbNormalized, [ "default", "lzh", "yue", "nan" ] ), // TODO: revert back to $db == "default"
					"$dbNormalized is referenced, but it isn't either a wiki or a dblist" );
			}
		}
	}

	public function testNoAmbiguouslyTaggedSettings() {
		$dblists = DBList::getLists();
		$overlapping = [];
		foreach ( $dblists as $listA => $wikisA ) {
			$overlapping[$listA] = [];
			foreach ( $dblists as $listB => $wikisB ) {
				if ( $listA !== $listB && array_intersect( $wikisA, $wikisB ) ) {
					$overlapping[$listA][] = $listB;
				}
			}
		}

		$actualAmbiguous = [];
		// The expected variable exists here so that the below logic
		// can add an empty stub for any variables with ambiguity.
		// Without this, the difference would be two levels deep,
		// in which case PHPUnit's diff printer would only show which
		// variable has an ambiguity, instead of also showing
		// between which dblists the ambiguity exists.
		$expectedAmbiguous = [];

		foreach ( $this->variantSettings as $configName => $values ) {
			foreach ( $overlapping as $listA => $lists ) {
				if ( isset( $values[$listA] ) ) {
					foreach ( $lists as $listB ) {
						if ( isset( $values[$listB] ) && $values[$listA] !== $values[$listB] ) {
							$ambigious[$configName][$listA] = $values[$listA];
							$ambigious[$configName][$listB] = $values[$listB];
							$expectedAmbiguous[$configName] = [];
						}
					}
				}
			}
		}

		$this->assertEquals(
			$expectedAmbiguous,
			$actualAmbiguous,
			'Overlapping dblist cannot set the same variable to different values'
		);
	}

	public function testCacheableLoad() {
		$settings = Wikimedia\MWConfig\MWConfigCacheGenerator::getCachableMWConfig(
			'enwiki', $this->variantSettings, 'production'
		);

		$this->assertEquals(
			'windows-1252', $settings['wgLegacyEncoding'],
			"Variant settings array must have 'wgLegacyEncoding' set to 'windows-1252' for enwiki."
		);

		$settings = Wikimedia\MWConfig\MWConfigCacheGenerator::getCachableMWConfig(
			'dewiki', $this->variantSettings, 'production'
		);

		$this->assertFalse(
			 $settings['wgLegacyEncoding'],
			"Variant settings array must have 'wgLegacyEncoding' set to 'windows-1252' for enwiki."
		);
	}

	public function testCacheableLoadForLabs() {
		$settings = Wikimedia\MWConfig\MWConfigCacheGenerator::getCachableMWConfig(
			'enwiki', $this->variantSettings, 'production'
		);
		$this->assertFalse(
			$settings['wmgUseFlow'],
			"Variant settings array must have 'wmgUseFlow' set to 'false' for production enwiki."
		);

		$settings = Wikimedia\MWConfig\MWConfigCacheGenerator::getCachableMWConfig(
			'mediawikiwiki', $this->variantSettings, 'production'
		);
		$this->assertTrue(
			$settings['wmgUseFlow'],
			"Variant settings array must have 'wmgUseFlow' set to 'true' for production mediawikiwiki."
		);

		$settings = Wikimedia\MWConfig\MWConfigCacheGenerator::getCachableMWConfig(
			'enwiki', $this->variantSettings, 'labs'
		);
		$this->assertTrue(
			$settings['wmgUseFlow'],
			"Variant settings array must have 'wmgUseFlow' set to 'true' for labs enwiki."
		);
	}
}
