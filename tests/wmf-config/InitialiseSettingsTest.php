<?php
/**
 * Test InitialiseSettings.php syntax
 *
 * @author Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @file
 */

@include( "wmf-config/InitialiseSettings.php" );

/**
 * Test cases for the InitialiseSettings.php file
 */
class InitialiseSettingsTests extends PHPUnit_Framework_TestCase {
	//
	// Configuration members and constructor
	//

	/**
	 * List of the settings badly prefixed
	 * (each setting should start with wg, wmf or wmg)
	 *
	 * @var Array
	 */
	private $settingsWithBadPrefix;

	/**
	 * Allowed configuration keys, in addition to the wiki projects
	 * we also accept meta projects such as 'wikipedia' or 'special'
	 *
	 * @var Array
	 */
	private $allowedProjectKeys;

	public function __construct () {
		$this->settingsWithBadPrefix = array(
			'groupOverrides',
			'groupOverrides2',
		);

		$this->allowedProjectKeys = array(
			// Generic keys
			'default',
			'special',

			// Projects families
			'wikibooks',
			'wikimedia',
			'wikinews',
			'wikipedia',
			'wikiquote',
			'wikisource',
			'wikiversity',
			'wiktionary',

			// Lists (matchin .dblist files)
			'closed',
			'fishbowl',
			'private',
			'flaggedrevs',

			// Strange generic keys
			'sourcewiki',
			'sourceswiki',
			'quotewiki',
			'wiki',

			// Closed wikis not in all.dblist
			'ru_sibwiki',
			'wikimaniawiki',
			'nomcom',

			// Ghost projects not in lists
			'zh-min-nanwikisource',
			'yiwikinews',

			// Future projects now in Incubator
			'liwikinews',
		);
	}

	/**
	 * Tests if meta namespaces doesn't use spaces instead underscores.
	 */
	public function testMetaNamespaces () {
		$configSettings = array( 'wgMetaNamespace', 'wgMetaNamespaceTalk' );
		foreach ( $configSettings as $configSetting ) {
			$namespaces = $this::getSetting( $configSetting );
			foreach ( $namespaces as $key => $value ) {
				$this->assertFalse(
					strpos( $value, ' ' ),
					"[$configSetting] Namespace names must use underscores, not spaces at '$key' => '$value')"
				);
			}
		}
	}

	/**
	 * Tests if wgExtraNamespaces looks correct.
	 * It asserts no space is used instead underscores.
	 * It also checks numeric values are used in keys, avoiding reversed syntax.
	 *
	 * @todo see if NS_* should be enforced by tests.
	 */
	public function testExtraNamespaces () {
		$namespaces = $this::getSetting( 'wgExtraNamespaces' );
		foreach ( $namespaces as $project => $config ) {
			foreach ( $config as $namespaceId => $namespaceName ) {
				$this->assertFalse(
					strpos( $namespaceName, ' ' ),
					"[wgExtraNamespaces] Namespace names must use underscores, not spaces at $namespaceId => '$namespaceName' for $project."
				);
				$this->assertInternalType(
					'int', $namespaceId,
					"[wgExtraNamespaces] Key should be numeric at $namespaceId => '$namespaceName' for $project. Check the syntax hasn't been reversed and the settting doesn't belong to wgNamespaceAliases."
				);
			}
		}
	}

	/**
	 * Tests if wgNamespaceAliases looks correct.
	 * It asserts no space is used instead underscores.
	 * It also checks numeric values are used in values, avoiding reversed syntax.
	 *
	 * @todo see if NS_* should be enforced by tests.
	 */
	public function testNamespaceAliases () {
		// wgNamespaceAliases
		$namespaces = $this::getSetting( 'wgNamespaceAliases' );
		foreach ( $namespaces as $project => $config ) {
			foreach ( $config as $namespaceAlias => $namespaceId ) {
				$this->assertFalse(
					strpos( $namespaceAlias, ' ' ),
					"[wgNamespaceAliases] Namespace names must use underscores, not spaces at '$namespaceAlias' => $namespaceId for $project)"
				);
				$this->assertInternalType(
					'int', $namespaceId,
					"[wgNamespacesAliases] Value should be numeric at '$namespaceAlias' => $namespaceId for $project. Check the syntax hasn't been reversed and the settting doesn't belong to wgNamespaceAliases."
				);

			}
		}

		// wgNamespaceAliases
	}

	/**
	 * Tests settings start with 'wg' and 'wmg' followed by an uppercase letter
	 */
	public function testSettingsPrefix () {
		global $wgConf;
		foreach ( $wgConf->settings as $setting => $value ) {
			// Skips test for allowed bogus names
			if ( in_array( $setting, $this->settingsWithBadPrefix ) ) {
				continue;
			}
			// Prefix
			$this->assertTrue(
				substr( $setting, 0, 2 ) == 'wg'  ||
				substr( $setting, 0, 3 ) == 'wmg' ||
				substr( $setting, 0, 3 ) == 'wmf',
				"[$setting] Setting name should start with wg, wmg or wmf"
			);
			// Camelcase
			$this->assertTrue(
				preg_match( '/^[a-z][a-zA-Z0-9]*$/', $setting ) == 1,
				"[$setting] Setting name should use camelCase"
			);
		}
	}

	/**
	 * Tests projects codes are valid
	 */
	public function testProjectsCodes () {
		global $wgConf;
		foreach ( $wgConf->settings as $setting => $value ) {
			foreach ( $value as $project => $config ) {
				$this->assertTrue(
					self::isValidConfigurationKey( $project ),
					"[$setting] $project isn't a valid projet nor a generic configuration key."
				);
			}
		}
	}

	/**
	 * Gets a $wgCong setting
	 *
	 * @param string $setting the setting to get
	 * @return mixed the setting value
	 */
	private function getSetting ( $setting ) {
		global $wgConf;

		if ( array_key_exists( $setting, $wgConf->settings ) ) {
			return $wgConf->settings[$setting];
		}

		throw new Exception( "Setting doesn't exist: $setting" );
	}

	//
	// Helper methods
	//

	/**
	 * Determines if the specified project exists
	 *
	 * @param string $project The project to check
	 * @return Boolean true if the specified project exists; otherwise, false.
	 */
	private function isValidProject ( $project ) {
		return in_array( $project, DBList::getAll() );
	}

	/**
	 * Determines if the specified configuration key is a generic value or a valid project
	 *
	 * @param string $key The configuration key to check
	 * @return Boolean true if 'default', 'wikipedia', etc. or a valid project; otherwise, false.
	 */
	private function isValidConfigurationKey ( $key ) {
		if ( $key[0] == '+' ) $key = substr( $key, 1 );
		foreach ( $this->allowedProjectKeys as $genericKey ) {
			if ( $key == $genericKey ) {
				return true;
			}
		}
		return self::isValidProject( $key );
	}
}
