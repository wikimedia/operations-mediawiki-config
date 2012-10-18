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

			// Closed wiki not in all.dblist
			'wikimaniawiki',
			'nomcom',

			// Ghost projects not in lists
			'zh-min-nanwikisource',
			'yiwikinews',

			// Future projects now in Incubator
			'liwikinews',
		);

		$this->allowedProjectKeys = array_merge(
			$this->allowedProjectKeys
			// add up projects families:
			, DBList::$wiki_projects
			// .. and closed.dblist
			, DBList::get( 'closed' )
		);
	}

	/**
	 * Test whether meta namespace uses underscore instead of spaces.
	 */
	public function testMetaNamespaces () {
		$configSettings = array( 'wgMetaNamespace', 'wgMetaNamespaceTalk' );
		foreach ( $configSettings as $configSetting ) {
			$namespaces = $this::getSetting( $configSetting );
			foreach ( $namespaces as $key => $value ) {
				$this->assertNotContains( ' ', $value,
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
				$this->assertNotContains( ' ', $namespaceName,
					"[wgExtraNamespaces] Namespace names must use underscores, not spaces at $namespaceId => '$namespaceName' for $project."
				);
				$this->assertInternalType( 'int', $namespaceId,
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
				$this->assertNotContains( ' ', $namespaceAlias,
					"[wgNamespaceAliases] Namespace names must use underscores, not spaces at '$namespaceAlias' => $namespaceId for $project)"
				);
				$this->assertInternalType( 'int', $namespaceId,
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
			$this->assertRegexp( '/w(g|mg|mf)[[:upper:]]/', $setting,
				"[$setting] Setting name should start with wg, wmg or wmf "
				. "followed by an upper case letter."
			);
			// Camelcase
			$this->assertRegexp( '/^[a-z][a-zA-Z0-9]*$/', $setting,
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
