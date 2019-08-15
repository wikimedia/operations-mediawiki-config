<?php

class WmfClusters {
	private $clusters;

	/**
	 * @return array
	 */
	public function getNames() {
		global $wgLBFactoryConf;
		return array_keys( $wgLBFactoryConf['sectionLoads'] );
	}

	/**
	 * @param string $clusterName
	 * @return array
	 */
	public function getReadOnly( $clusterName ) {
		global $wgLBFactoryConf;
		return $wgLBFactoryConf['readOnlyBySection'][$clusterName] ?? false;
	}

	/**
	 * @param string $clusterName
	 * @return array
	 */
	public function getHosts( $clusterName ) {
		global $wgLBFactoryConf;
		return array_keys( $wgLBFactoryConf['sectionLoads'][$clusterName] );
	}

	/**
	 * @param string $clusterName
	 * @return string
	 */
	public function getLoads( $clusterName ) {
		global $wgLBFactoryConf;
		return $wgLBFactoryConf['sectionLoads'][$clusterName];
	}

	/**
	 * @param string $clusterName
	 * @return string
	 */
	public function getGroupLoads( $clusterName ) {
		global $wgLBFactoryConf;
		return $wgLBFactoryConf['groupLoadsBySection'][$clusterName];
	}

	/**
	 * @param string $clusterName
	 * @return array
	 */
	public function getDBs( $clusterName ) {
		global $wgLBFactoryConf;
		$ret = [];
		foreach ( $wgLBFactoryConf['sectionsByDB'] as $db => $cluster ) {
			if ( $cluster == $clusterName ) {
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
			global $wgConf;
			$wgConf = new stdClass();
			require_once __DIR__ . '/../wmf-config/InitialiseSettings.php';
			$canonicalServers = $wgConf->settings['wgCanonicalServer'];
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
	 * @param string $clusterName
	 */
	public function htmlFor( $clusterName ) {
		print "<strong>Hosts</strong><br>";
		foreach ( $this->getHosts( $clusterName ) as $host ) {
			print "<code>$host</code> ";
		}
		print '<br><strong>Loads</strong>:<br>';
		foreach ( $this->getLoads( $clusterName ) as $host => $load ) {
			print "$host => $load<br>";
		}
		print '<br><strong>Databases</strong>:<br>';
		if ( $clusterName == 'DEFAULT' ) {
			print 'Any wiki not hosted on the other clusters.<br>';
		} else {
			foreach ( $this->getDBs( $clusterName ) as $i => $db ) {
				print "$db";
				// labtestweb seems unresponsive, avoid crawlers hitting it
				if ( $i === 0 && $db !== 'labtestwiki' ) {
					// Use format=xml because it's cheap to generate and view
					// and browsers tend to render it nicely.
					// (json is hard to read by default, jsonfm is slower)
					$replagUrl = $this->getServer( $db ) . '/w/api.php?format=xml&action=query&meta=siteinfo&siprop=dbrepllag&sishowalldb=1';
					print ' (replag: <a href="' . htmlspecialchars( $replagUrl ) . '">mw-api</a> &bull;';
					print ' <a href="https://dbtree.wikimedia.org/">dbtree</a>)';
				}
				echo '<br>';
			}
		}
	}
}
