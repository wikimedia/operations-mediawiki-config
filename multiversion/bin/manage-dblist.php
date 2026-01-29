#!/usr/bin/env php
<?php
declare( strict_types = 1 );

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
	/** @var string[] */
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
			match ( $this->command ) {
				'add' => $this->doAdd( ...$this->params ),
				'list' => $this->doList( ...$this->params ),
				'del', 'remove' => $this->doDel( ...$this->params ),
				'update' => $this->doUpdate( ...$this->params ),
				'prepare' => $this->doPrepare( ...$this->params ),
				'activate' => $this->doActivate( ...$this->params ),
				'init-labs' => $this->doInitLabs( ...$this->params ),
				'close' => $this->doClose( ...$this->params ),
				'', 'help' => $this->help(),
				default => $this->error( 'Unknown command' )
			};
		} catch ( Throwable $e ) {
			$this->error( (string)$e );
		}
	}

	private function doList( string $dbname ): void {
		$dblists = array_keys( array_filter(
			WmfConfig::getAllDbListsForCLI(),
			static fn ( array $list ): bool => in_array( $dbname, $list, true )
		) );
		sort( $dblists );
		foreach ( $dblists as $dblist ) {
			print "* $dblist\n";
		}
	}

	private function doAdd( string $dbname, string $listname ): void {
		if ( !in_array( $listname, [ 'all', 'all-labs', 'preinstall' ], true ) ) {
			$validDbnames = strpos( $listname, 'labs' ) !== false
				? WmfConfig::readDbListFile( 'all-labs' )
				: WmfConfig::evalDbExpressionForCli( 'all + preinstall' );
			if ( !in_array( $dbname, $validDbnames, true ) ) {
				$this->error( "Unknown wiki: $dbname" );
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
		$prodWikis = WmfConfig::evalDbExpressionForCli( 'all + preinstall' );
		$labsOnlyWikis = WmfConfig::readDbListFile( 'all-labs' );
		$knownDBLists = WmfConfig::getAllDbListsForCLI();

		// There is only one set of dblists for all realms combined.
		// This means it is important that a labs-only wiki never be included in
		// production dblists like "all", "closed", "fishbowl", "echo", etc.
		//
		// This caveat is validated by DblistTest::testDblistAllContainsEverything().
		//
		$labsOnlyTags = [ 'all-labs', 'closed-labs', 'flow_only_labs', 'flow-labs' ];
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
					if ( !in_array( $dbname, $validDbnames, true ) ) {
						unset( $content[$i] );
					}
				}
				$this->writeDblist( $listname, $content, $sources[$listname] ?? null );
			}

		}

		$this->writeDblistsIndex( $knownDBLists );
		$this->generateDbSectionMapping();
	}

	private function doPrepare( string $dbName, string $lang, string $family, string $visibility ): void {
		$visibility = strtolower( $visibility );

		if ( !in_array( $visibility, [ 'public', 'fishbowl', 'private' ], true ) ) {
			$this->error( 'Unknown visibility' );
		}

		// Add wiki to wikiversion.json
		$versions = MWWikiVersions::readWikiVersionsFile( 'wikiversions.json' );
		$key = match ( $family ) {
			'wikipedia' => 'enwiki',
			'wikimania' => 'wikimaniawiki',
			'wikimedia' => 'vewikimedia',
			'wikibooks' => 'enwikibooks',
			'wikinews' => 'enwikinews',
			'wikiquote' => 'enwikiquote',
			'wikisource' => 'enwikisource',
			'wikiversity' => 'enwikiversity',
			'wikivoyage' => 'enwikivoyage',
			'wiktionary' => 'enwiktionary',
			'special' => 'apiportalwiki',
			default => $this->error( 'Unknown family' )
		};
		$versions[$dbName] = $versions[$key];
		ksort( $versions );
		MWWikiVersions::writeWikiVersionsFile( 'wikiversions.json', $versions );

		$this->doAdd( $dbName, 'preinstall' );
		$this->doAdd( $dbName, $family );
		// All wikis are currently created in s5
		$this->doAdd( $dbName, 's5' );
		$this->doAdd( $dbName, 'small' );

		// (T376827) All new wikis are created with parsoidrendered enabled, except Wikisources
		if ( $family !== 'wikisource' ) {
			$this->doAdd( $dbName, 'parsoidrendered' );
		}

		$data = file( 'langlist', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		if ( !in_array( $lang, $data, true ) ) {
			$data[] = $lang;
			asort( $data );
			file_put_contents( 'langlist', implode( "\n", $data ) . "\n" );
		}

		switch ( $visibility ) {
			case 'public':
				if ( $family !== 'wikimedia' ) {
					$this->doAdd( $dbName, 'wikidataclient' );
				}
				$this->doAdd( $dbName, 'commonsuploads' );
				$this->doAdd( $dbName, 'securepollglobal' );
				$this->doAdd( $dbName, 'sul' );
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

	private function doActivate( string $dbName ): void {
		$preinstallWikis = WmfConfig::readDbListFile( 'preinstall' );
		if ( !in_array( $dbName, $preinstallWikis, true ) ) {
			$this->error( "Wiki not in preinstall.dblist: $dbName" );
		}
		$this->doAdd( $dbName, 'all' );
		$this->doDel( $dbName, 'preinstall' );
	}

	private function doInitLabs( string $dbName, string $lang ): void {
		// Bail if the wiki doesn't exist in production. Standard configuration structure
		// assumes the wiki receives settings from production dblists such as the wiki family.
		// Adding a wiki that doesn't exist in production to the Beta Cluster is possible but
		// more complicated and not supported by this tool.
		$validDbNames = WmfConfig::readDbListFile( 'all' );
		if ( !in_array( $dbName, $validDbNames, true ) ) {
			$this->error( "Wiki not in production config: $dbName" );
		}

		$this->doAdd( $dbName, 'all-labs' );

		// Add wiki to wikiversion-labs.json
		$versions = MWWikiVersions::readWikiVersionsFile( 'wikiversions-labs.json' );
		if ( isset( $versions[$dbName] ) ) {
			$this->error( "Wiki already in Beta Cluster: $dbName" );
		}
		$versions[$dbName] = 'php-master';
		ksort( $versions );
		MWWikiVersions::writeWikiVersionsFile( 'wikiversions-labs.json', $versions );

		$data = file( 'langlist-labs', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		if ( !in_array( $lang, $data, true ) ) {
			$data[] = $lang;
			asort( $data );
			file_put_contents( 'langlist-labs', implode( "\n", $data ) . "\n" );
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
	private function writeDblist( string $listname, array $listcontent, ?string $source = null ): void {
		$path = __DIR__ . '/../../dblists/' . $listname . '.dblist';

		// Alpha-sort the contents of the list by array value for consistency
		asort( $listcontent );

		$from = ( $source === 'dbexpr' ? " from {$listname}.dbexpr" : '' );

		$content = "# NOTE: This file is automatically generated{$from}."
			. " Do not edit it directly, run 'composer manage-dblist' instead.\n";

		if ( $listcontent ) {
			$content .= implode( "\n", array_unique( $listcontent ) ) . "\n";
		}

		if ( !file_put_contents( $path, $content, LOCK_EX ) ) {
			$this->error( "Unable to write to $path." );
		}
	}

	private function generateDbSectionMapping(): void {
		// Note: 's3' is intentionally omitted as it's the default section
		// that databases will fall back to if they don't match any other section
		$sections = [ 's1', 's2', 's4', 's5', 's6', 's7', 's8' ];
		$sectionData = array_combine(
			$sections,
			array_map( [ WmfConfig::class, 'readDbListFile' ], $sections )
		);

		$maxLength = max( array_map( 'strlen', array_merge( ...array_values( $sectionData ) ) ) );
		$output = "<?php\n"
			. "// NOTE: This file is automatically generated. Do not edit it directly.\n"
			. "// Run 'composer manage-dblist' to add or remove wikis from sections.\n\n"
			. "\$wgLBFactoryConf['sectionsByDB'] = [\n"
			. implode( "\n", array_map(
				static fn ( $section, $dbs ): string => implode( "", array_map(
					static fn ( $db ): string => "\t" . str_pad( "'$db'", $maxLength + 2 ) . " => '$section',\n",
					$dbs
				) ),
				$sections,
				$sectionData
			) )
			. "];\n";

		file_put_contents( __DIR__ . '/../../wmf-config/db-sections.php', $output );
	}

	/**
	 * @param string $head Comment
	 * @param string $path Path to PHP-file to create or replace
	 * @param array $data
	 */
	private function writeStaticArrayFile( string $head, string $path, array $data ): void {
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
			$this->error( "Unable to write to $path." );
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

	private function error( string $msg ): never {
		print "\n$msg\n\n";
		print "Run 'composer manage-dblist help' for more information.\n";
		exit( 1 );
	}
}

$cli = new WmfManageDblistCli( $argv );
$cli->run();
