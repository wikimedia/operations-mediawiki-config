#!/usr/bin/env php
<?php declare( strict_types=1 );
/**
 * Helper script for locally making patches to dblists/ files.
 *
 * For compatibility and performance reasons, the files used
 * at run-time by web requests and maintenance scripts are
 * organised by list name.
 *
 * When making changes to dblists, the intent is sometimes thought of
 * relating to a dblist and sometimes as relating to a wiki.
 *
 * This script makes it easy to edit the files no matter which way
 * you think about it. It also validates and normalizes the files.
 */

require_once __DIR__ . '/../MWWikiversions.php';
require_once __DIR__ . '/../../src/WmfConfig.php';

use Wikimedia\MWConfig\WmfConfig;

// phpcs:ignore MediaWiki.Files.ClassMatchesFilename.NotMatch
final class WmfManageDblistCli {
	private string $command;
	private array $params;

	/**
	 * @param string[] $argv
	 */
	public function __construct( array $argv ) {
		$this->command = $argv[1] ?? '';
		$this->params = array_slice( $argv, 2 );
	}

	private function help(): void {
		print <<<TEXT
	usage: composer manage-dblist <command> [<args>]

	COMMANDS

	list <dbname>
			Show which dblists contain a given wiki.

	add <dbname> <dblist>
			Add a wiki to a dblist, and normalize all dblist files.

			If the wiki isn't valid for that list (e.g. not yet in "all.dblist"),
			then the addition is ignored.

			Example: composer manage-dblist add aawiki closed

	del <dbname> <dblist>
			Remove a wiki from a dblist, and normalize all dblist files.
			Example: composer manage-dblist del aawiki open
			Alias: remove
	update
			Normalize all dblist files,
			including expansion of ".dbexpr" files.

	prepare <dbname> <language code> <family> <privacy>
			Set up a new wiki, getting it ready for installation. It will be
			added to preinstall.dblist.
			<privacy> must be one of 'public', 'fishbowl' or 'private'
			Example: composer manage-dblist prepare fatwiki fat wikipedia public

	activate <dbname>
			Move a wiki from preinstall.dblist to all.dblist. This indicates
			that the database is ready to use.

	init-labs <dbname> <language code>
			Prepare and activate a new wiki in the Beta Cluster, adding it to
			all-labs.dblist.

			The wiki must be present in the production config
			(wikiversions.json) already.

			Example: composer manage-dblist init-labs fatwiki fat

	close <dbname>
			Update the dblists of a wiki to reflect it being closed
			Example: composer manage-dblist close enwiki

	TEXT;
	}

	public function run(): void {
		try {
			switch ( $this->command ) {
				case 'add':
					$this->doAdd( ...$this->params );
					return;
				case 'list':
					$this->doList( ...$this->params );
					return;
				case 'del':
				case 'remove':
					$this->doDel( ...$this->params );
					return;
				case 'update':
					$this->doUpdate( ...$this->params );
					return;
				case 'prepare':
					$this->doPrepare( ...$this->params );
					return;
				case 'activate':
					$this->doActivate( ...$this->params );
					return;
				case 'init-labs':
					$this->doInitLabs( ...$this->params );
					return;
				case 'close':
					$this->doClose( ...$this->params );
					return;
				case '':
				case 'help':
					$this->help();
					exit( 1 );
				default:
					$this->error( 'Unknown command' );
					return;
			}
		} catch ( Throwable $e ) {
			$this->error( (string)$e );
		}
	}

	private function doList( string $dbname ): void {
		$dblists = array_keys( array_filter(
			WmfConfig::getAllDbListsForCLI(),
			static function ( array $list ) use ( $dbname ) {
				return in_array( $dbname, $list );
			}
		) );
		sort( $dblists );
		foreach ( $dblists as $dblist ) {
			print "* $dblist\n";
		}
	}

	private function doAdd( string $dbname, string $listname ): void {
		if ( !in_array( $listname, [ 'all', 'all-labs', 'preinstall', 'preinstall-labs' ] ) ) {
			$validDbnames = strpos( $listname, 'labs' ) !== false
				? WmfConfig::readDbListFile( 'all-labs' )
				: WmfConfig::readDbListFile( 'all' );
			if ( !in_array( $dbname, $validDbnames ) ) {
				$this->error( "Unknown wiki: $dbname" );
				return;
			}
		}

		$content = WmfConfig::readDbListFile( $listname );
		$content[] = $dbname;
		$this->writeDblist( $listname, $content );
		$this->doUpdate();
	}

	private function doDel( string $dbname, string $listname ): void {
		$content = WmfConfig::readDbListFile( $listname );
		$i = array_search( $dbname, $content );
		if ( $i !== false ) {
			unset( $content[$i] );
		}

		$this->writeDblist( $listname, $content );
		$this->doUpdate();
	}

	private function doUpdate(): void {
		$prodWikis = WmfConfig::readDbListFile( 'all' );
		$labsOnlyWikis = WmfConfig::readDbListFile( 'all-labs' );
		$knownDBLists = WmfConfig::getAllDbListsForCLI();

		// There is only one set of dblists for all realms combined.
		// This means it is important that a labs-only wiki never be included in
		// production dblists like "all", "closed", "fishbowl", "echo", etc.
		//
		// This caveat is validated by DblistTest::testDblistAllContainsEverything().
		//
		$labsOnlyTags = [ 'all-labs', 'closed-labs', 'flow_only_labs', 'flow-labs', 'preinstall-labs' ];
		$untracked = [
			// This naturally contains wikis we don't know about
			'deleted',
		];

		$sources = [];
		foreach ( glob( __DIR__ . '/../../dblists/*.dbexpr' ) as $filepath ) {
			$listname = basename( $filepath, '.dbexpr' );
			$contents = WmfConfig::evalDbExpressionForCli( file_get_contents( $filepath ) );
			$knownDBLists[$listname] = $contents;
			$sources[$listname] = 'dbexpr';
		}

		foreach ( $knownDBLists as $listname => $content ) {
			// Ensure no Beta Cluster-only dblists under Labs-specific lists.
			$validDbnames = in_array( $listname, $labsOnlyTags ) ? $labsOnlyWikis : $prodWikis;
			if ( !in_array( $listname, $untracked ) ) {
				foreach ( $content as $i => $dbname ) {
					if ( !in_array( $dbname, $validDbnames ) ) {
						unset( $content[$i] );
					}
				}
				$this->writeDblist( $listname, $content, $sources[$listname] ?? null );
			}

		}

		$this->writeDblistsIndex( $knownDBLists );

		$this->generateDbSectionMapping();
	}

	private function doPrepare( string $dbName, string $lang, string $family, string $visibility ) {
		$visibility = strtolower( $visibility );

		if ( !in_array( $visibility, [ 'public', 'fishbowl', 'private' ] ) ) {
			$this->error( 'Unknown visibility' );
			exit( 1 );
		}

		// add wiki to wikiversion.json
		$versions = MWWikiVersions::readWikiVersionsFile( 'wikiversions.json' );
		switch ( $family ) {
			case 'wikipedia':
				$key = 'enwiki';
				break;
			case 'wikimania':
				$key = 'wikimaniawiki';
				break;
			case 'wikimedia':
				$key = 'vewikimedia';
				break;
			case 'wikibooks':
				$key = 'enwikibooks';
				break;
			case 'wikinews':
				$key = 'enwikinews';
				break;
			case 'wikiquote':
				$key = 'enwikiquote';
				break;
			case 'wikisource':
				$key = 'enwikisource';
				break;
			case 'wikiversity':
				$key = 'enwikiversity';
				break;
			case 'wikivoyage':
				$key = 'enwikivoyage';
				break;
			case 'wiktionary':
				$key = 'enwiktionary';
				break;
			case 'special':
				$key = 'apiportalwiki';
				break;
			default:
				$this->error( 'Unknown family' );
				exit( 1 );
		}
		$versions[$dbName] = $versions[$key];
		ksort( $versions );
		MWWikiVersions::writeWikiVersionsFile( 'wikiversions.json', $versions );

		$this->doAdd( $dbName, 'preinstall' );
		$this->doAdd( $dbName, $family );
		// all wikis are currently created in s5
		$this->doAdd( $dbName, 's5' );
		$this->doAdd( $dbName, 'small' );

		// (T376827) All new wikis are created with parsoidrendered enabled, except Wikisources
		if ( $family !== 'wikisource' ) {
			$this->doAdd( $dbName, 'parsoidrendered' );
		}

		$data = file( 'langlist' );
		if ( !in_array( $lang . "\n", $data ) ) {
			$data[] = $lang . "\n";
			asort( $data );
			file_put_contents( 'langlist', implode( '', $data ) );
		}

		switch ( $visibility ) {
			case 'public':
				if ( $family != 'wikimedia' ) {
					$this->doAdd( $dbName, 'wikidataclient' );
				}
				$this->doAdd( $dbName, 'commonsuploads' );
				$this->doAdd( $dbName, 'securepollglobal' );
				break;
			case 'fishbowl':
				$this->doAdd( $dbName, 'fishbowl' );
				break;
			case 'private':
				$this->doAdd( $dbName, 'private' );
				break;
			default:
		}

		$this->generateDbSectionMapping();
	}

	private function doActivate( string $dbName ) {
		$preinstallWikis = WmfConfig::readDbListFile( 'preinstall' );
		if ( !in_array( $dbName, $preinstallWikis ) ) {
			$this->error( "Wiki not in preinstall.dblist: $dbName" );
			return;
		}
		$this->doAdd( $dbName, 'all' );
		$this->doDel( $dbName, 'preinstall' );
	}

	private function doInitLabs( string $dbName, string $lang ) {
		// Bail if the wiki doesn't exist in production. Standard configuration structure
		// assumes the wiki receives settings from production dblists such as the wiki family.
		// Adding a wiki that doesn't exist in production to the Beta Cluster is possible but
		// more complicated and not supported by this tool.
		$validDbNames = WmfConfig::readDbListFile( 'all' );
		if ( !in_array( $dbName, $validDbNames ) ) {
			$this->error( "Wiki not in production config: $dbName" );
			return;
		}

		$this->doAdd( $dbName, 'all-labs' );

		// add wiki to wikiversion-labs.json
		$versions = MWWikiVersions::readWikiVersionsFile( 'wikiversions-labs.json' );
		if ( isset( $versions[$dbName] ) ) {
			$this->error( "Wiki already in Beta Cluster: $dbName" );
			return;
		}
		$versions[$dbName] = 'php-master';
		ksort( $versions );
		MWWikiVersions::writeWikiVersionsFile( 'wikiversions-labs.json', $versions );

		$data = file( 'langlist-labs' );
		if ( !in_array( $lang . "\n", $data ) ) {
			$data[] = $lang . "\n";
			asort( $data );
			file_put_contents( 'langlist-labs', implode( '', $data ) );
		}
	}

	private function doClose( string $dbName ): void {
		$this->doAdd( $dbName, 'closed' );
		$this->doAdd( $dbName, 'group0' );
		$this->doDel( $dbName, 'securepollglobal' );
		$this->doDel( $dbName, 'commonsuploads' );
	}

	/**
	 * @param string $listname The name of the db list to write.
	 * @param string[] $listcontent The wikidbs for the dblist contents.
	 * @param string|null $source One of "dbexpr" or null
	 */
	private function writeDblist( $listname, $listcontent, $source = null ) {
		$path = __DIR__ . '/../../dblists/' . $listname . '.dblist';

		// Alpha-sort the contents of the list by array value for consistency
		asort( $listcontent );

		$from = ( $source === 'dbexpr' ? " from {$listname}.dbexpr" : '' );

		if ( !file_put_contents(
			$path,
			[
				// Header warning about being a generated file
				"# NOTE: This file is automatically generated{$from}."
					. " Do not edit it directly, run 'composer manage-dblist' instead.\n",
				// The contents of the list, written one per line, unique'd
				implode( "\n", array_unique( $listcontent ) ),
				// Trailing new line for consistency
				"\n"
			],
			LOCK_EX
		) ) {
			print "Unable to write to $path.\n";
			exit( 1 );
		}
	}

	private function generateDbSectionMapping(): void {
		$sections = var_export(
			array_merge( ...array_map( static function ( string $s ) {
				return array_fill_keys( WmfConfig::readDbListFile( $s ), $s );
			}, [ 's1', 's2', 's4', 's5', 's6', 's7', 's8' ] ) ),
			true
		);

		$sections = preg_replace( '/\s+/', '', $sections );
		$sections = str_replace( 'array(', "[\n\t", $sections );
		$sections = str_replace( ',', ",\n\t", $sections );
		$sections = str_replace( '=>', ' => ', $sections );
		$sections = str_replace( "\t)", "];\n", $sections );

		file_put_contents( 'wmf-config/db-sections.php',
			"<?php\n"
			. '# NOTE: This file is automatically generated. Do not edit it directly, run '
			. "'composer manage-dblist' instead to add/remove wikis from sections.\n"
			. '$wgLBFactoryConf[\'sectionsByDB\'] = '
			. $sections
		);
	}

	/**
	 * @param string $head Comment
	 * @param string $path Path to PHP-file to create or replace
	 * @param array $data
	 */
	private function writeStaticArrayFile( string $head, string $path, array $data ) {
		$code = "<?php\n"
			. "// " . implode( "\n// ", explode( "\n", $head ) ) . "\n"
			. "return [\n";
		foreach ( $data as $key => $value ) {
			$code .= var_export( $key, true ) . " => [ "
				. implode( ", ", array_map( static function ( $sub ) {
					return var_export( $sub, true );
				}, $value ) )
				. " ],\n";
		}
		$code .= "];\n";

		if ( !file_put_contents( $path, $code, LOCK_EX ) ) {
			print "Unable to write to $path.\n";
			exit( 1 );
		}
	}

	/**
	 * @param array<string,string[]> $dblists
	 */
	private function writeDblistsIndex( array $dblists ): void {
		$indexByDbname = [];
		foreach ( WmfConfig::DB_LISTS as $listname ) {
			$contents = $dblists[$listname];
			foreach ( $contents as $dbname ) {
				$indexByDbname[$dbname][] = $listname;
			}
		}
		ksort( $indexByDbname );

		$path = __DIR__ . '/../../dblists-index.php';
		$this->writeStaticArrayFile(
			'NOTE: Automatically generated from the /dblists directory' . "\n"
				. 'Do not edit it directly, run "composer manage-dblist update" instead.',
			$path,
			$indexByDbname
		);
	}

	private function error( string $msg ): void {
		print "\n" . $msg . "\n\n";
		$this->help();
		exit( 1 );
	}
}

$cli = new WmfManageDblistCli( $argv );
$cli->run();
