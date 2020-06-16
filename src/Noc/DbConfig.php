<?php

namespace Wikimedia\MWConfig\Noc;

class DbConfig {
	/**
	 * @return array
	 */
	public function getNames() {
		global $wgLBFactoryConf;
		return array_keys( $wgLBFactoryConf['sectionLoads'] );
	}

	/**
	 * @param string $sectionName
	 * @return array
	 */
	public function getReadOnly( $sectionName ) {
		global $wgLBFactoryConf;
		return $wgLBFactoryConf['readOnlyBySection'][$sectionName] ?? false;
	}

	/**
	 * @param string $sectionName
	 * @return array
	 */
	public function getHosts( $sectionName ) {
		global $wgLBFactoryConf;
		return array_keys( $wgLBFactoryConf['sectionLoads'][$sectionName] );
	}

	/**
	 * @param string $sectionName
	 * @return string
	 */
	public function getLoads( $sectionName ) {
		global $wgLBFactoryConf;
		return $wgLBFactoryConf['sectionLoads'][$sectionName];
	}

	/**
	 * @param string $sectionName
	 * @return string
	 */
	public function getGroupLoads( $sectionName ) {
		global $wgLBFactoryConf;
		return $wgLBFactoryConf['groupLoadsBySection'][$sectionName];
	}

	/**
	 * @param string $sectionName
	 * @return array
	 */
	public function getDBs( $sectionName ) {
		global $wgLBFactoryConf;
		$ret = [];
		foreach ( $wgLBFactoryConf['sectionsByDB'] as $db => $section ) {
			if ( $section == $sectionName ) {
				$ret[] = $db;
			}
		}
		return $ret;
	}

	/**
	 * @param string $db
	 * @return string
	 */
	public function getServer( $db ) {
		static $canonicalServers;
		if ( $canonicalServers === null ) {
			// Mock variable to capture the property assignment
			global $wmfDatacenter;
			$wmfDatacenter = 'bogus';
			require_once __DIR__ . '/../../src/defines.php';
			require_once __DIR__ . '/../../wmf-config/InitialiseSettings.php';
			$settings = wmfGetVariantSettings();
			$canonicalServers = $settings['wgCanonicalServer'];
		}
		if ( isset( $canonicalServers[$db] ) ) {
			// If the wiki is special or otherwise has an explicit server name, use it.
			$server = $canonicalServers[$db];
		} else {
			// Try the tag defaults (from db suffix to wgConf tag)
			$suffixes = [
				'wiki' => 'wikipedia',
				'wiktionary' => 'wiktionary',
				'wikiquote' => 'wikiquote',
				'wikibooks' => 'wikibooks',
				'wikiquote' => 'wikiquote',
				'wikinews' => 'wikinews',
				'wikisource' => 'wikisource',
				'wikiversity' => 'wikiversity',
				'wikimedia' => 'wikimedia',
				'wikivoyage' => 'wikivoyage',
			];
			foreach ( $suffixes as $suffix => $tag ) {
				if ( substr( $db, -strlen( $suffix ) ) === $suffix ) {
					$lang = substr( $db, 0, -strlen( $suffix ) );
					$server = strtr( $canonicalServers[$tag], [ '$lang' => $lang ] );
					break;
				}
			}
		}
		return $server;
	}

	/**
	 * @param string $sectionName
	 * @return string HTML
	 */
	public function htmlFor( $sectionName ) {
		$ret = [ "<strong>Hosts</strong><br>" ];
		foreach ( $this->getHosts( $sectionName ) as $host ) {
			$ret[] = "<code>$host</code>";
		}
		$ret[] = '<br><strong>Loads</strong> (master first; replicas follow):<br>';
		foreach ( $this->getLoads( $sectionName ) as $host => $load ) {
			$ret[] = "$host => $load<br>";
		}
		$ret[] = '<br><strong>Databases</strong>:<br>';
		if ( $sectionName == 'DEFAULT' ) {
			$ret[] = 'Any wiki not hosted on the other sections.<br>';
		} else {
			foreach ( $this->getDBs( $sectionName ) as $i => $db ) {
				$ret[] = $db;
				// labtestweb seems unresponsive, avoid crawlers hitting it
				if ( $i === 0 && $db !== 'labtestwiki' ) {
					// Use format=xml because it's cheap to generate and view
					// and browsers tend to render it nicely.
					// (json is hard to read by default, jsonfm is slower)
					$replagUrl = $this->getServer( $db ) . '/w/api.php?format=xml&action=query&meta=siteinfo&siprop=dbrepllag&sishowalldb=1';
					$ret[] = ' (replag: <a href="' . htmlspecialchars( $replagUrl ) . '">mw-api</a> &bull;';
					$ret[] = ' <a href="https://dbtree.wikimedia.org/">dbtree</a>)';
				}
				$ret[] = '<br>';
			}
		}
		return implode( "\n", $ret );
	}
}
