<?php
// Ensure that we're not casting any types
declare( strict_types = 1 );

class StaticSettingsTest extends PHPUnit\Framework\TestCase {

	protected $variantSettings = [];
	private $originalWmfDC;

	public function setUp() : void {
		// HACK: Set a global still used in InitialiseSettings
		$this->originalWmfDC = $GLOBALS['wmfDatacenter'] ?? null;
		$GLOBALS['wmfDatacenter'] = 'testvalue';

		$configDir = __DIR__ . "/../../wmf-config";
		require_once "{$configDir}/InitialiseSettings.php";
		$this->variantSettings = wmfGetVariantSettings();
	}

	public function tearDown() : void {
		// HACK: This is sometimes set and sometimes not, depending on when
		// multiversion/MWRealm.php was lazy required by other PHPUnit tests.
		// Avoid PHPUnit errors about global leak so make sure we restore
		// to what we encountered.
		if ( $this->originalWmfDC === null ) {
			unset( $GLOBALS['wmfDatacenter'] );
		} else {
			$GLOBALS['wmfDatacenter'] = $this->originalWmfDC;
		}
	}

	public function testConfigIsScalar() {
		foreach ( $this->variantSettings as $variantSettting => $settingsArray ) {
			$this->assertTrue( is_array( $settingsArray ), "Each variant setting set must be an array, but $variantSettting is not" );

			foreach ( $settingsArray as $wiki => $value ) {
				$this->assertTrue(
					is_scalar( $value ) || is_array( $value ) || $value === null,
					"Each variant setting must be scalar, an array, or null, but $variantSettting is not for $wiki."
				);
			}
		}
	}

	public function testLogos() {
		// Build an array of logos to test everything in one cycle
		$logos = [];

		foreach ( $this->variantSettings['wgLogo'] as $db => $logo ) {
			$logos[$db]['1x'] = $logo;
		}

		foreach ( $this->variantSettings['wgLogos'] as $db => $entry ) {
			// Test if only 1.5x and 2x keys are used
			// Only relevant to wgLogos, so can stay here
			$keys = array_keys( $entry );
			if ( array_search( $db, $keys ) ) {
				$this->assertEqualsCanonicalizing( [ '1.5x', '2x', 'wordmark' ], $keys, "Unexpected keys for $db" );
			} else {
				$this->assertEqualsCanonicalizing( [ '1.5x', '2x' ], $keys, "Unexpected keys for $db" );
			}

			foreach ( $entry as $size => $logo ) {
				$logos[$db][$size] = $logo;
			}
		}

		// Really test stuff
		foreach ( $logos as $db => $entry ) {
			// 1x must be defined
			$this->assertArrayHasKey( '1x', $entry, "$db has HD logo defined, but no 1x logo" );

			foreach ( $entry as $size => $logo ) {
				// Test if all logos exist
				$this->assertFileExists( __DIR__ . '/../..' . $logo, "$db has nonexistent $size logo" );
			}
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

	public function testwgServer() {
		foreach ( $this->variantSettings['wgCanonicalServer'] as $db => $entry ) {
			// Test if wgCanonicalServer start with https://
			$this->assertStringStartsWith( "https://", $entry, "wgCanonicalServer for $db doesn't start with https://" );
		}

		foreach ( $this->variantSettings['wgServer'] as $db => $entry ) {
			// Wikitech and Test Wikitech don't currently comply.
			if ( $db === 'labswiki' || $db === 'labtestwiki' ) {
				continue;
			}
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

		$this->assertEquals(
			false, $settings['wgLegacyEncoding'],
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
