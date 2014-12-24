<?php

function efUserIsAffected ( $user, &$loggedout = null, &$isGlobal = false ) {
	global $wgMemc;

	if ( !$user->getID() ) {
		return false;
	}

	// Query the slave to eliminate the most common case where user is not on our list
	$cacheData = $wgMemc->get( 'centralauth:reset-pass:' . md5( $user->getName() ) );
	if ( !$cacheData ) {
		$dbr = CentralAuthUser::getCentralSlaveDB();
		$res = $dbr->select(
			'bug_54847_password_resets',
			array( 'r_wiki', 'r_username' ),
			array( 'r_username' => $user->getName() ),
			__METHOD__
		);
		if ( $res->numRows() < 1 ) {
			$cacheData = 'no';
		} else {
			$cacheData = 'yes';
		}
		$wgMemc->set( 'centralauth:reset-pass:' . md5( $user->getName() ), $cacheData );
	}
	if ( $cacheData === 'no' ) {
		return false;
	}

	$dbw = CentralAuthUser::getCentralDB();
	$result = $dbw->select(
		'bug_54847_password_resets',
		array( 'r_wiki', 'r_username', 'r_logged_out', 'r_reset' ),
		array( 'r_username' => $user->getName(),
			'r_reset is null'
		),
		__METHOD__
	);

	if ( $result->numRows() < 1 ) {
		return false;
	}


	$centralUser = CentralAuthUser::getInstance( $user );
	if ( $centralUser->exists() && $centralUser->isAttached() ) {
		$isGlobal = true;
	}

	$affectedWikis = array();
	foreach ( $result as $row ) {
		$affectedWikis[] = $row->r_wiki;
		if ( $row->r_logged_out > 0 ) {
			$loggedout = true;
		}
	}

	if ( in_array( wfWikiID(), $affectedWikis ) ) {
		// This user on this wiki affected
		return true;
	}


	if ( $isGlobal ) {
		foreach ( $affectedWikis as $wiki ) {
			if ( $centralUser->attachedOn( $wiki ) ) {
				return true;
			}
		}
	}

	return false;

}

// Abort existing open sessions for affected users. (But not
// repeatedly, only once.)
// Maybe we dont do this and just reset everyone's tokens instead?

$wgHooks['UserLoadAfterLoadFromSession'][] = function ( $user ) {
	if ( efUserIsAffected( $user ) ) {
		$dbw = CentralAuthUser::getCentralDB();
		$dbw->update(
			'bug_54847_password_resets',
			array( 'r_logged_out' => 1 ),
			array( 'r_username' => $user->getName(),
				'r_wiki' => wfWikiID()
			),
			__METHOD__
		);
		$user->logout();
	}
};


$wgHooks['UserLoadFromSession'][] = function ( $user, &$result ) {

	$loggedout = false;
	$isGlobal = false;
	if ( efUserIsAffected( $user, $loggedout, $isGlobal ) ) {
		if ( $loggedout ) {
			// Already logged them out
			return true;
		}

		wfDebugLog( "Bug54847", "Logging out user " . $user->getName() );

		$dbw = CentralAuthUser::getCentralDB();

		$updateWikis = wfWikiID();
		if ( $isGlobal ) {
			$centralUser = CentralAuthUser::getInstance( $user );
			$updateWikis = $centralUser->listAttached();
		}
		$dbw->update(
			'bug_54847_password_resets',
			array( 'r_logged_out' => 1 ),
			array( 'r_username' => $user->getName(),
				'r_wiki' => $updateWikis
			),
			__METHOD__
		);

		$user->logout();
		$result = false;
		return false;
	}
	return true;
};

$wgHooks[ 'AbortLogin' ][] = function ( User $user, $password, &$retval, &$msg ) {
	global $wgOut, $egBug54847;
	if ( empty( $egBug54847 ) && $user->checkPassword( $password ) && efUserIsAffected( $user ) ) {
		wfDebugLog( "Bug54847", "Aborting login until they reset password: " . $user->getName() );
		$msg = 'word-separator';
		$wgOut->addWikiText( wfMessage( 'bug-54847-password-reset-prompt' )->text() );
		$retval = LoginForm::RESET_PASS;
		return false;
	}
};

// Reject attempts to set an existing password as the new password.
$wgHooks['AbortChangePassword'][] = function ( $user, $password, $newpassword, &$errorMsg ) {
	global $egBug54847;

	$passwordOK = false;
	$loggedout = false;
	$isGlobal = false;

	if ( !efUserIsAffected( $user, $loggedout, $isGlobal ) ) {
		return true;
	}

	// Ensure that the user is not attempting to set their existing password as
	// the new password.

	try {
		if ( $isGlobal ) {
			$centralUser = CentralAuthUser::getInstance( $user );
			list( $salt, $crypt ) = $centralUser->getPasswordHash();
			//if ( $centralUser->matchHash( $newpassword, $salt, $crypt ) ) {
			if ( User::comparePasswords( $crypt, $newpassword, $salt ) ) {
				wfDebugLog( "Bug54847", "User attempted to reset with CentralAuth password: " . $user->getName() );
				$errorMsg = 'password-recycled';
				return false;
			}

			// Next, ensure that the user is not attempting to set a password that was on another
			// wiki that had its hash leaked
			$dbw = CentralAuthUser::getCentralDB();
			$result = $dbw->select(
				'bug_54847_password_resets',
				array( 'r_wiki', 'r_reset' ),
				array( 'r_username' => $user->getName(),
					'r_reset is null'
				),
				__METHOD__
			);

			$affectedWikis = array();
			foreach ( $result as $row ) {
				$affectedWikis[] = $row->r_wiki;
			}
			$leakedAttached = array_intersect( $affectedWikis, $centralUser->listAttached() );

			foreach ( $leakedAttached as $leakedAttachedWiki ) {
				$localDB = wfGetLB( $leakedAttachedWiki )->getConnection( DB_SLAVE , array(), $leakedAttachedWiki );
				$res = $localDB->selectRow( 'user',
					array( 'user_password' ),
					array( 'user_name' => $centralUser->mName ),
					__METHOD__
				);
				if ( $res !== false && User::comparePasswords( $res->user_password, $newpassword ) ) {
					$errorMsg = 'password-recycled';
					return false;
				}
			}

		} else {
			$dbr = wfGetDB( DB_SLAVE );
			$res = $dbr->selectRow( 'user',
				array( 'user_password' ),
				array( 'user_name' => $user->getName() ),
				__METHOD__
			);
			if ( $res !== false && User::comparePasswords( $res->user_password, $newpassword ) ) {
				$errorMsg = 'password-recycled';
				return false;
			}
		}
	} catch( PasswordError $ex ) {
		// Just ignore this: Might be that a wiki has some very old or broken hash... not
		// very likely that the user is using that as a password, so just ignoring it is
		// bearable.
		wfDebugLog( "Bug54847", "User has invalid password (" . $ex->getMessage() . "): " . $user->getName() );
	}

	// Everything looks ok, so lets assume the reset is going to go ok, and don't flag this user in the future

	$resetWikis = wfWikiID();
	if ( $isGlobal ) {
		$resetWikis = $centralUser->listAttached();
	}

	$egBug54847 = array(
		'bug_54847_password_resets',
		array( 'r_reset' => wfTimestampNow() ),
		array( 'r_username' => $user->getName(),
			'r_wiki' => $resetWikis
		),
		__METHOD__
	);
};

$wgHooks['PrefsPasswordAudit'][] = function ( $user, $pass, $msg ) {
	global $egBug54847, $wgMemc;

	if ( $msg == 'success' && isset( $egBug54847 ) && is_array( $egBug54847 ) ) {
		$dbw = CentralAuthUser::getCentralDB();
		call_user_func_array(array($dbw, 'update'), $egBug54847 );

		$wgMemc->set( 'centralauth:reset-pass:' . md5( $user->getName() ), 'no' );
	}

	return true;
};
