<?php
error_reporting( E_ALL );
require_once( __DIR__ . '/defines.php' );

/**
 * Automatically git clone a MediaWiki version and do some basic wmf setup.
 * LocalSettings.php will be created (which loads CommonSettings.php) and various
 * symlinks will also be created.
 *
 * The first argument is the git branch (relative to wmf).
 * This is typically a version of the format "X.XXwmfX" ("e.g. 1.17wmf1").
 * The second argument is the target path (relative to MEDIAWIKI_STAGING_DIR)
 * to store local copy of the git checkout. This is typically of the format "php-X.XX".
 *
 * This script assumes that the user running this script has an git account
 * with the SSH agent/key available.
 *
 * @return void
 */
function checkoutMediaWiki() {
	global $argv;

	$argsValid = false;
	if ( count( $argv ) >= 3 ) {
		$gitVersion = $argv[1]; // e.g. "X.XXwmfX"
		$dstVersion = $argv[2]; // e.g. "php-X.XXwmfX"
		if ( preg_match( '/^php-(\d+\.\d+wmf\d+|master)$/', $dstVersion, $m ) ) {
			$dstVersionNum = $m[1]; // everything after 'php-'
			$argsValid = true;
		}
	}

	if ( !$argsValid ) {
		print "Usage: checkoutMediaWiki X.XXwmfX php-X.XXwmfX\n";
		exit( 1 );
	}

	# MW install path
	$destIP = MEDIAWIKI_STAGING_DIR . "/$dstVersion";

	if ( !file_exists( $destIP ) ) {
		passthru( 'git clone -n ' .
			'https://gerrit.wikimedia.org/r/p/mediawiki/core.git ' .
			escapeshellarg( $destIP ),
			$ret );
		if ( $ret ) {
			print "Error cloning mediawiki\n";
			exit( 1 );
		}
		chmod( "$destIP", 0775 );
		if ( !chdir( $destIP ) ) {
			print "Error changing directory\n";
			exit( 1 );
		}

		passthru( 'git config branch.autosetuprebase always', $ret );
		if ( $ret ) {
			# Don't exit, this isn't a show-stopper
			print "Error running setting autosetuprebase\n";
		}

		$checkoutVersion = $gitVersion == 'master' ? $gitVersion : "wmf/$gitVersion";
		passthru( 'git checkout ' . escapeshellarg( $checkoutVersion ), $ret );
		if ( $ret ) {
			print "Error checking out branch\n";
			exit( 1 );
		}

		# If using master, we don't have submodules. But we want extensions
		if ( $dstVersionNum == 'master' ) {
			chdir( "$destIP/extensions" );
			passthru( 'git init' );
			passthru( 'git remote add origin https://gerrit.wikimedia.org/r/p/mediawiki/extensions' );
			passthru( 'git fetch' );
			passthru( 'git checkout -f -t origin/master' );
			passthru( 'git pull' );
			passthru( 'git submodule update --init --recursive' );
			passthru( 'git clone https://gerrit.wikimedia.org/r/p/mediawiki/vendor vendor', $ret );
			chdir( $destIP );
		} else {
			passthru( 'git submodule update --init --recursive', $ret );
			if ( $ret ) {
				print "Error updating submodules\n";
				exit( 1 );
			}
		}

		$submodules = array();
		exec( 'git submodule status | cut -d" " -f3', $submodules, $ret );
		if ( $ret ) {
			print "Error finding list of submodules\n";
		} else {
			foreach ( $submodules as $moduleName ) {
				passthru( "git config submodule.\"{$moduleName}\".update rebase", $ret );
				if ( $ret ) {
					print "Failed to set submodule \"{$moduleName}\" to rebase on update.\n";
				}
			}
		}
	} else {
		echo "MediaWiki already checked out at $destIP\n";
	}

	$localSettingsCode = <<<PHP
<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.
include_once( "/srv/mediawiki/wmf-config/CommonSettings.php" );
PHP;

	# Create LocalSettings.php stub...
	$path = "$destIP/LocalSettings.php";
	if ( !file_exists( $path ) ) {
		if ( file_put_contents( $path, $localSettingsCode ) ) {
			print "Created LocalSettings.php file.\n";
		}
	} else {
		print "File already exists: $path\n";
	}

	# Create symlink to wmf-config/StartProfiler.php...
	$path = "$destIP/StartProfiler.php";
	$link = "../wmf-config/StartProfiler.php";
	createSymlink( $path, $link, "Created StartProfiler.php symlink." );

	# Create static- symlinks for bits.wikimedia.org...
	$bitsStaticDir = MEDIAWIKI_STAGING_DIR . "/docroot/bits/static-$dstVersionNum";
	if ( !file_exists( $bitsStaticDir ) ) {
		mkdir( $bitsStaticDir, 0775 );
	}
	$path = MEDIAWIKI_STAGING_DIR . "/docroot/bits/static-$dstVersionNum/skins";
	$link = MEDIAWIKI_DEPLOYMENT_DIR . "/php-$dstVersionNum/skins/";
	createSymlink( $path, $link, "Created bits/static-$dstVersionNum/skins symlink." );

	$path = MEDIAWIKI_STAGING_DIR . "/docroot/bits/static-$dstVersionNum/extensions";
	$link = MEDIAWIKI_DEPLOYMENT_DIR . "/php-$dstVersionNum/extensions";
	createSymlink( $path, $link, "Created bits/static-$dstVersionNum/extensions symlink." );

	$path = MEDIAWIKI_STAGING_DIR . "/docroot/bits/static-$dstVersionNum/resources";
	$link = MEDIAWIKI_DEPLOYMENT_DIR . "/php-$dstVersionNum/resources";
	createSymlink( $path, $link, "Created bits/static-$dstVersionNum/resources symlink." );

	# Create static- symlinks for /w...
	$liveStaticDir = MEDIAWIKI_STAGING_DIR . "/w/static-$dstVersionNum";
	if ( !file_exists( $liveStaticDir ) ) {
		mkdir( $liveStaticDir, 0775 );
	}
	$path = MEDIAWIKI_STAGING_DIR . "/w/static-$dstVersionNum/skins";
	$link = MEDIAWIKI_DEPLOYMENT_DIR . "/php-$dstVersionNum/skins";
	createSymlink( $path, $link, "Created /w/static-$dstVersionNum/skins symlink." );

	$path = MEDIAWIKI_STAGING_DIR . "/w/static-$dstVersionNum/extensions";
	$link = MEDIAWIKI_DEPLOYMENT_DIR . "/php-$dstVersionNum/extensions";
	createSymlink( $path, $link, "Created /w/static-$dstVersionNum/extensions symlink." );

	$path = MEDIAWIKI_STAGING_DIR . "/w/static-$dstVersionNum/resources";
	$link = MEDIAWIKI_DEPLOYMENT_DIR . "/php-$dstVersionNum/resources";
	createSymlink( $path, $link, "Created /w/static-$dstVersionNum/resources symlink." );

	# Create l10n cache dir
	$l10nDir = "$destIP/cache/l10n";
	if ( !file_exists( $l10nDir ) ) {
		chmod( "$destIP/cache", 0777 );
		exec( "sudo -u l10nupdate -- mkdir $destIP/cache/l10n",  $output, $status );
		chmod( "$destIP/cache", 0775 );
		if ( $status ) {
			print "Failed to create $destIP/cache/l10n\n";
			exit( 1 );
		}
	} else {
		print "Directory already exists: $l10nDir\n";
	}

	print "\nMediaWiki $dstVersionNum, from $gitVersion, successfully checked out.\n";
}

function createSymlink( $path, $link, $createdMsg ) {
	if ( !file_exists( $path ) ) {
		if ( symlink( $link, $path ) ) {
			print "$createdMsg\n";
		}
	} else {
		print "Symlink file already exists: $path\n";
	}
}

