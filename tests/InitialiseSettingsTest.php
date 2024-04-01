<?php
// Ensure that we're not casting any types
declare( strict_types = 1 );

use Wikimedia\MWConfig\MWConfigCacheGenerator;

/**
 * Structure test for the wmf-config settings themselves.
 *
 * @covers \Wikimedia\MWConfig\MWConfigCacheGenerator
 */
class InitialiseSettingsTest extends PHPUnit\Framework\TestCase {
	private $settings;
	private $conf;
	private $config;

	/**
	 * @var string original value of $wmgDatacenter
	 */
	private $originalWmfDC;

	public function setUp(): void {
		// This global is set by multiversion/MWRealm.php
		$this->originalWmfDC = $GLOBALS['wmgDatacenter'];
		$GLOBALS['wmgDatacenter'] = 'testvalue';

		$this->settings = MWConfigCacheGenerator::getStaticConfig();

		$conf = new SiteConfiguration();
		$conf->suffixes = MWMultiVersion::SUFFIXES;
		$conf->settings = $this->settings;
		$this->config = $conf;
	}

	public function tearDown(): void {
		$GLOBALS['wmgDatacenter'] = $this->originalWmfDC;
	}

	public function testConfigIsScalar() {
		foreach ( $this->settings as $settingName => $settingsArray ) {
			$this->assertIsArray( $settingsArray, "Each setting set must be an array, but $settingName is not" );

			foreach ( $settingsArray as $wiki => $value ) {
				$this->assertTrue(
					is_scalar( $value ) || is_array( $value ) || $value === null,
					"Each setting must be scalar, an array, or null, but $settingName is not for $wiki."
				);
			}
		}
	}

	public function testUrlSettingsAreLocalhost() {
		$knownToContainExternalURLs = [
			// These are user-facing, not in-cluster, and are fine
			'wgCanonicalServer', 'wgServer', 'wgUploadPath', 'wgRedirectSources', 'wgUploadNavigationUrl', 'wgScorePath', 'wgPhonosPath', 'wgUploadMissingFileUrl', 'wgRightsUrl', 'wgWelcomeSurveyPrivacyPolicyUrl', 'wgWBCitoidFullRestbaseURL', 'wgGlobalRenameDenylist', 'wgWMEClientErrorIntakeURL', 'wgEventLoggingServiceUri',
			// FIXME: Just… wow. By name, this should be a boolean.
			'wmgUseFileExporter',
			// FIXME: Just set in wikibase.php? Most of these are user-facing.
			'wgEntitySchemaShExSimpleUrl', 'wmgWBRepoSettingsSparqlEndpoint', 'wmgWikibaseClientRepoUrl', 'wmgWikibaseClientPropertyOrderUrl', 'wgArticlePlaceholderRepoApiUrl', 'wgMediaInfoExternalEntitySearchBaseUri', 'wgMediaSearchExternalEntitySearchBaseUri', 'wmgWikibaseSSRTermboxServerUrl', 'wmgWikibaseClientDataBridgeHrefRegExp',
		];

		foreach ( $this->settings as $settingName => $settingsArray ) {
			if ( in_array( $settingName, $knownToContainExternalURLs ) ) {
				// Skip for now.
				continue;
			}

			foreach ( $settingsArray as $wiki => $value ) {
				if ( !is_string( $value ) ) {
					continue;
				}

				$this->assertFalse(
					strpos( $value, '//' ) !== false && strpos( $value, 'localhost' ) === false,
					"URLs must point to localhost, or be defined in CommonSettings, but $settingName for $wiki is '$value'."
				);
			}
		}
	}

	public function testUseFlagsAreBoolean() {
		$knownToBeBad = [
			'wgCirrusSearchUseCompletionSuggester',
			'wgCirrusSearchUseIcuFolding',
			"wgMFUseDesktopSpecialWatchlistPage",
			'wmgUseCognate',
			'wmgUseFileExporter',
			'wmgUseFileImporter',
			'wgWMESchemaVisualEditorFeatureUseSamplingRate',
		];

		foreach ( $this->settings as $settingName => $settingsArray ) {
			if ( preg_match( '/Use[A-Z]/', $settingName ) ) {
				if ( in_array( $settingName, $knownToBeBad ) ) {
					// Skip for now.
					continue;
				}

				foreach ( $settingsArray as $wiki => $value ) {
					$this->assertTrue(
						is_bool( $value ),
						"Use flags should be boolean, but $settingName for $wiki is " . ( is_array( $value ) ? "an array" : "'" . (string)$value . "'" ) . "."
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
			'icon' => 'wmgSiteLogoIcon',
		];

		$pairedSizes = [ '1.5x', '2x' ];

		// Test if all scalar logos exist
		foreach ( $scalarLogoKeys as $size => $key ) {
			foreach ( $this->settings[ $key ] as $db => $entry ) {
				$this->assertFileExists( __DIR__ . '/..' . $entry, "$db has non-existent $size logo in $key" );

				if ( in_array( $size, $pairedSizes ) ) {
					$otherSize = array_values( array_diff( $pairedSizes, [ $size ] ) )[ 0 ];
					$otherKey = $scalarLogoKeys[ $otherSize ];

					$this->assertArrayHasKey(
						$db,
						$this->settings[ $otherKey ],
						"$db has a logo set for $size in $key but not for $otherSize in $otherKey"
					);

					$baseKey = $scalarLogoKeys['1x'];

					$this->assertArrayHasKey(
						$db,
						$this->settings[ $baseKey ],
						"$db has an over-ride HD logo set for $size in $key but not for regular resoltion in $baseKey"
					);

					// Test if 2x and 1.5x is really of correct size
					// Tolerate up to 5 px difference
					$imagesizeOne = getimagesize( __DIR__ . '/..' . $this->settings[ $scalarLogoKeys[ '1x' ] ][ $db ] )[0];
					$imagesizeOneAndHalf = getimagesize( __DIR__ . '/..' . $this->settings[ $scalarLogoKeys[ '1.5x' ] ][ $db ] )[0];
					$imagesizeTwo = getimagesize( __DIR__ . '/..' . $this->settings[ $scalarLogoKeys[ '2x' ] ][ $db ] )[0];

					// Remove this exception as soon as the logos are updated to meet the condition
					if ( !in_array( $db, [
						'hiwiki',
						'cawikiquote', 'enwikiquote', 'eowikiquote', 'eswikiquote', 'hrwikiquote', 'hywikiquote', 'knwikiquote', 'slwikiquote', 'srwikiquote',
						'zhwikinews',
						'ruwikivoyage', 'zhwikivoyage'
					] ) ) {
						$this->assertTrue(
							$imagesizeOneAndHalf >= (int)( $imagesizeOne * 1.5 - 5 ),
							"$db has 1.5x HD logo of $imagesizeOneAndHalf width, at least " . (int)( $imagesizeOne * 1.5 ) . " expected"
						);
					}

					// Remove this exception as soon as the logo is updated to meet the condition
					if ( $db !== 'zhwikivoyage' ) {
						$this->assertTrue(
							$imagesizeTwo >= $imagesizeOne * 2 - 5,
							"$db has 2x HD logo of $imagesizeTwo width, at least " . $imagesizeOne * 2 . " expected"
						);
					}

				}
			}
		}

		// Test if all wordmark logo values are set and the file exists
		foreach ( $this->settings[ 'wmgSiteLogoWordmark' ] as $db => $entry ) {
			if ( !$entry || !count( $entry ) ) {
				// Wordmark logo over-ridden to unset.
				continue;
			}
			$this->assertArrayHasKey( 'src', $entry, "$db has no path set for its wordmark logo in wmgSiteLogoWordmark" );
			$this->assertFileExists( __DIR__ . '/..' . $entry['src'], "$db has non-existent wordmark logo in wmgSiteLogoWordmark" );
			$this->assertArrayHasKey( 'width', $entry, "$db has no width set for its wordmark logo in wmgSiteLogoWordmark" );
			$this->assertArrayHasKey( 'height', $entry, "$db has no height set for its wordmark logo in wmgSiteLogoWordmark" );
		}
	}

	public function testLogosAreSet() {
		$config = yaml_parse_file( __DIR__ . '/../logos/config.yaml' );

		// Test that every special wiki has a logo set
		// We could do this for every wiki, but a lot fall back to their project, so it's not worth it
		$configuredImages = $config['Special wikis'];
		$dbLists = DBList::getLists();
		$dblistValues = $dbLists['special'];
		foreach ( $dblistValues as $index => $db ) {
			// Exceptions; new special wikis should generally be created with logos, unless there's a good reason not to
			if ( in_array( $db, [
				// Special cases
				'labswiki',
				'labtestwiki',
				// Defined in the 'Wikisource' list for sensible reasons
				'sourceswiki',
			] ) ) {
				continue;
			}

			$this->assertArrayHasKey( $db, $configuredImages, "Special wiki $db is a known wiki but has not images set" );
		}

		// Test that every configured-entry is about a real wiki
		foreach ( $config as $list => $values ) {
			if ( $list === 'Projects' ) {
				continue;
			}

			foreach ( $values as $db => $entry ) {
				if ( $db === 'wikitech' ) {
					// Special case
					continue;
				}

				$this->assertTrue( DBList::isInDblist( $db, "all" ), "$db has images set but is not a known wiki" );
			}
		}
	}

	public function testwgExtraNamespaces() {
		foreach ( $this->settings['wgExtraNamespaces'] as $db => $entry ) {
			foreach ( $entry as $number => $namespace ) {
				// Test for invalid spaces
				$this->assertStringNotContainsString( ' ', $namespace, "Unexpected space in '$number' namespace title for $db, use underscores instead" );

				// Test for invalid colons
				$this->assertStringNotContainsString( ':', $namespace, "Unexpected colon in '$number' namespace title for $db, final colon is not needed and can be removed" );

				// Test namespace numbers
				if ( $number < 100 || in_array( $number, [ 828, 829 ] ) ) {
					// It's not an extra namespace, do not test
					continue;
				}
				if ( $number % 2 == 0 ) {
					$this->assertArrayHasKey( $number + 1, $entry, "Namespace $namespace (ID $number) for $db doesn't have corresponding talk namespace set" );
				} else {
					$this->assertArrayHasKey( $number - 1, $entry, "Namespace $namespace (ID $number) for $db doesn't have corresponding non-talk namespace set" );
				}
			}
		}
	}

	public function testMetaNamespaces() {
		foreach ( $this->settings['wgMetaNamespace'] as $db => $namespace ) {
			// Test for invalid spaces
			$this->assertStringNotContainsString( ' ', $namespace, "Unexpected space in meta namespace title for $db, use underscores instead" );

			// Test for invalid colons
			$this->assertStringNotContainsString( ':', $namespace, "Unexpected colon in meta namespace title for $db, final colon is not needed and should be removed" );
		}

		foreach ( $this->settings['wgMetaNamespaceTalk'] as $db => $namespace ) {
			// Test for invalid spaces
			$this->assertStringNotContainsString( ' ', $namespace, "Unexpected space in meta talk namespace title for $db, use underscores instead" );

			// Test for invalid colons
			$this->assertStringNotContainsString( ':', $namespace, "Unexpected colon in meta talk namespace title for $db, final colon is not needed and should be removed" );
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
					$this->settings[ $setting ],
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
					$this->settings[ $setting ],
					"Wiki '$db' is in the 'special' family but has no $setting set."
				);
			}

			foreach ( $mustHaveConfigForSpecialWikisButSomeDoNot as $j => $setting ) {
				if ( in_array( $db, $knownFailures ) ) {
					continue;
				}

				$this->assertArrayHasKey(
					$db,
					$this->settings[ $setting ],
					"Wiki '$db' is in the 'special' family but has no $setting set."
				);
			}
		}
	}

	public function testwgServer() {
		foreach ( $this->settings['wgCanonicalServer'] as $db => $entry ) {
			// Test if wgCanonicalServer start with https://
			$this->assertStringStartsWith( "https://", $entry, "wgCanonicalServer for $db doesn't start with https://" );
		}

		foreach ( $this->settings['wgServer'] as $db => $entry ) {
			// Test if wgServer start with //
			$this->assertStringStartsWith( "//", $entry, "wgServer for $db doesn't start with //" );
		}
	}

	public function testwgSitename() {
		foreach ( $this->settings['wgSitename'] as $db => $entry ) {
			// Test that the string doesn't contain invalid charcters T249014
			$this->assertStringNotContainsString( ',', $entry, "wgSitename for $db contains a ',' which breaks e-mails" );
		}
	}

	public function testOnlyExistingWikis() {
		$dblistNames = array_keys( DBList::getLists() );
		$langs = file( __DIR__ . "/../langlist", FILE_IGNORE_NEW_LINES );
		$settings = $this->settings;
		unset( $settings['@replaceableSettings'] );
		foreach ( $settings as $setting => $config ) {
			foreach ( $config as $db => $entry ) {
				$dbNormalized = str_replace( "+", "", $db );
				$this->assertTrue(
					in_array( $dbNormalized, $dblistNames ) ||
					DBList::isInDblist( $dbNormalized, "all" ) ||
					in_array( $dbNormalized, $langs ) ||
					// TODO: revert back to $db == "default"
					in_array( $dbNormalized, [ "default", "lzh", "yue", "nan" ] ),
					"$dbNormalized is referenced for $setting, but it isn't either a wiki or a dblist" );
			}
		}
	}

	public function testNoAmbiguouslyTaggedSettings() {
		self::expectNotToPerformAssertions();

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

		$ambiguous = [];

		foreach ( $this->settings as $configName => $values ) {
			foreach ( $overlapping as $listA => $lists ) {
				if ( isset( $values[$listA] ) ) {
					foreach ( $lists as $listB ) {
						if (
							isset( $values[$listB] )
							&& $values[$listA] !== $values[$listB]
						) {
							$ambiguous[$configName][] = [
								$listA => $values[$listA],
								$listB => $values[$listB]
							];
						}
					}
				}
			}
		}

		if ( count( $ambiguous ) ) {
			$detailsString = "";
			foreach ( $ambiguous as $ambiguouslySetVariable => $errorEntries ) {
				$detailsString .= "\nThe variable $ambiguouslySetVariable is set differently in some dblists which overlap:\n";
				foreach ( $errorEntries as $index => $entry ) {
					foreach ( $entry as $listname => $value ) {
						if ( is_scalar( $value ) ) {
							$detailsString .= "\t " . $listname . ' sets it to `' . $value . "`\n";
						} else {
							$detailsString .= "\t " . $listname . ' sets it to `' . json_encode( $value ) . "` (JSON encoded for readability)\n";
						}
					}
				}
			}

			$this->fail( "Overlapping dblists are setting the same variable to different values. This is banned as it would rely on runtime sequence of dblists being read, which is not guaranteed.\n" . $detailsString );
		}
	}

	public function testImportantProductionSettings() {
		$enwikiSettings = Wikimedia\MWConfig\MWConfigCacheGenerator::getMWConfigForCacheing(
			'enwiki', $this->config, 'production'
		);
		$dewikiSettings = Wikimedia\MWConfig\MWConfigCacheGenerator::getMWConfigForCacheing(
			'dewiki', $this->config, 'production'
		);
		$officewikiSettings = Wikimedia\MWConfig\MWConfigCacheGenerator::getMWConfigForCacheing(
			'officewiki', $this->config, 'production'
		);

		$this->assertFalse(
			$officewikiSettings['groupOverrides2']['*']['read'],
			'Set by "private" tag, restrict reading on officewiki'
		);
		$this->assertTrue(
			count( $officewikiSettings['wgWhitelistRead'] ) > 1,
			'Enable by "private" tag, wgWhitelistRead for officewiki'
		);
	}

	public function testExampleLabsSettings() {
		$settings = Wikimedia\MWConfig\MWConfigCacheGenerator::getMWConfigForCacheing(
			'enwiki', $this->config, 'production'
		);
		$this->assertFalse(
			$settings['wmgUseFlow'],
			"settings array must have 'wmgUseFlow' set to 'false' for production enwiki."
		);

		$settings = Wikimedia\MWConfig\MWConfigCacheGenerator::getMWConfigForCacheing(
			'mediawikiwiki', $this->config, 'production'
		);
		$this->assertTrue(
			$settings['wmgUseFlow'],
			"settings array must have 'wmgUseFlow' set to 'true' for production mediawikiwiki."
		);

		$settings = Wikimedia\MWConfig\MWConfigCacheGenerator::getMWConfigForCacheing(
			'enwiki', $this->config, 'labs'
		);
		$this->assertTrue(
			$settings['wmgUseFlow'],
			"settings array must have 'wmgUseFlow' set to 'true' for labs enwiki."
		);
	}

	public function testSettingNames(): void {
		$invalidKeys = [];
		foreach ( $this->settings as $key => $_ ) {
			if (
				!str_starts_with( $key, 'wg' ) &&
				!str_starts_with( $key, 'wmg' ) &&
				$key !== 'groupOverrides' && $key !== 'groupOverrides2' &&
				$key !== '@replaceableSettings'
			) {
				$invalidKeys[] = $key;
			}
		}

		$this->assertSame( [], $invalidKeys, 'Setting names must begin with wg or wmg!' );
	}
}
