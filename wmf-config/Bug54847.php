<?php

// Context: #mediawiki-security, 5 AM UTC, 2-Oct-2013

// Eloquence: greg-g, in my email I asked Chris if he can take the lead on the MediaWiki side to force a password change on login, and he seemed to imply in earlier correspondence that he already had some thoughts on how to best do it. but if someone else wants to take a crack at it that would probably make Chris happy :)
// Eloquence: ori-l, there's Special:ChangePassword -- if we could kill any active sessions and force users through that loop before they can log in, would that be sufficient?
// TimStarling: I would be more inclined to just deny the login in a UserLoadFromSession hook
// TimStarling: then also hook the login form to redirect the subsequent manual login to the reset page
// TimStarling: btw, note that the user_password field is not removed when a user attaches their account to CentralAuth
// TimStarling: but there's no way to change that password after it is attached
// TimStarling: it will show up as compromised for this notification email, but the password that is compromised might not be current, and might not be functional
// ori-l: TimStarling: I didn't follow, probably because I don't know CentralAuth well. Are you flagging a concern, or suggesting that the normal behavior of CentralAuth could be tricked into implementing a faux forced password reset?
// greg-g: well, that the hash that was exposed might not actually still be the hash that applies to the user. it could be an old password that was changed post-CentralAuth linkage. So, not much to do either way without checking explicitly.
// greg-g: (my maybe misunderstood understanding)
// ori-l: no, I think you're right
// greg-g: but, isn't the salt or something diff between the two hashes? so a simple diff won't work...
// TimStarling: yes, the salt will be different
// TimStarling: it would be possible to require a password change, and refuse the password change if the new password matches the leaked hash
// TimStarling: but where the leaked hash is obscured by CA, it should just be removed with a maintenance script
// TimStarling: removed from user_password at least

function efUserIsAffected ( $user, &$loggedout = null, &$isGlobal = false ) {

	// Returns true if
	// - user is in list of affected global users
	// - user's password hasn't changed since bug

	// Assuming something like this was added to the centralauth database:
	// CREATE TABLE bug_54847_password_resets (
	// `r_wiki` varchar(255) binary not null, /* INDEX */
	// `r_username` varchar(255) binary not null,
	// `r_email` varchar(255) binary,
	// `r_logged_out` tinyint not null default 0, /* set to 1 when session killed */
	// `r_reset` varchar(14) binary default NULL /* datestamp of password reset */
	// );
	// CREATE INDEX reset_users ON bug_54847_password_resets (r_username);

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
	if ( efUserIsAffected( $user ) ) {
		wfDebugLog( "Bug54847", "Aborting login until they reset password: " . $user->getName() );
		$msg = 'bug-54847-password-reset-prompt';
		$retval = LoginForm::RESET_PASS;
		return false;
	}
};

// Reject attempts to set an existing password as the new password.
$wgHooks['AbortChangePassword'][] = function ( $user, $password, $newpassword, &$errorMsg ) {

	$passwordOK = false;
	$loggedout = false;
	$isGlobal = false;

	if ( !efUserIsAffected( $user, $loggedout, $isGlobal ) ) {
		return true;
	}

	// Ensure that the user is not attempting to set their existing password as
	// the new password.

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
				$result = 'password-recycled';
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
			$result = 'password-recycled';
			return false;
		}
	}

	wfDebugLog( "Bug54847", "User seems to be setting a sane password, update our db for: " . $user->getName() );

	// Everything looks ok, so lets assume the reset is going to go ok, and don't flag this user in the future
	$dbw = CentralAuthUser::getCentralDB();

	$resetWikis = wfWikiID();
	if ( $isGlobal ) {
		$resetWikis = $centralUser->listAttached();
	}

	$dbw->update(
		'bug_54847_password_resets',
		array( 'r_reset' => wfTimestampNow() ),
		array( 'r_username' => $user->getName(),
			'r_wiki' => $resetWikis
		),
		__METHOD__
	);
};
