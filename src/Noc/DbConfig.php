<?php

namespace Wikimedia\MWConfig\Noc;

use Wikimedia\MWConfig\MWConfigCacheGenerator;

class DbConfig {
	/** @var string[] */
	private $sectionLabels;

	/**
	 * @param string[] $sectionLabels
	 */
	public function __construct( array $sectionLabels = [] ) {
		$this->sectionLabels = $sectionLabels;
	}

	/**
	 * @return array<string,string>
	 */
	public function getSections(): array {
		global $wgLBFactoryConf;
		$sections = [];
		foreach ( array_keys( $wgLBFactoryConf['sectionLoads'] ) as $sectionName ) {
			$sections[$sectionName] = $this->getLabel( $sectionName );
		}
		// natsort for s1 < s2 < s10 rather than s1 < s10 < s2
		natsort( $sections );
		return $sections;
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
	 * @param string $sectionName
	 * @return string
	 */
	public function getLabel( string $sectionName ): string {
		// Try to resolve 'DEFAULT'
		return $this->sectionLabels[$sectionName] ?? $sectionName;
	}

	/**
	 * @param string $db
	 * @return string
	 */
	public function getServer( $db ) {
		static $canonicalServers;
		if ( $canonicalServers === null ) {
			require_once __DIR__ . '/../../src/defines.php';
			$settings = MWConfigCacheGenerator::getStaticConfig();
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
		$ret = [ "<strong>Hosts</strong>:<br>" ];
		foreach ( $this->getHosts( $sectionName ) as $host ) {
			$ret[] = "<code>$host</code>";
		}
		$ret[] = '<br><strong>Loads</strong>:<br>';
		$first = true;
		foreach ( $this->getLoads( $sectionName ) as $host => $load ) {
			$line = "$host => $load";
			if ( $first ) {
				$line .= " (primary)";
				$first = false;
			}
			$line .= "<br>";
			$ret[] = $line;
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
					$ret[] = ' (replag: <a href="' . htmlspecialchars( $replagUrl ) . '">api</a>)';
				}
				$ret[] = '<br>';
			}
		}
		return implode( "\n", $ret );
	}
}
