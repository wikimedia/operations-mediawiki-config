<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

use MediaWiki\Logger\LoggerFactory;
use MediaWiki\MediaWikiServices;
use MediaWiki\Session\SessionManager;
use Monolog\Handler\StreamHandler;

wfLoadExtension( 'LdapAuthentication' );
$wgAuthManagerAutoConfig['primaryauth'] += [
	LdapPrimaryAuthenticationProvider::class => [
		'class' => LdapPrimaryAuthenticationProvider::class,
		'args' => [ [
			// don't allow local non-LDAP accounts
			'authoritative' => true,
		] ],
		// must be smaller than local pw provider
		'sort' => 50,
	],
];
$wgLDAPDomainNames = [ 'labs' ];
switch ( $wgDBname ) {
	case 'labswiki':
		$wgLDAPServerNames = [ 'labs' => "ldap-rw.{$wmgDatacenter}.wikimedia.org" ];
		break;
	case 'labtestwiki':
		$wgLDAPServerNames = [ 'labs' => 'cloudservices2004-dev.codfw.wmnet' ];
		break;
}
// T165795: require exact case matching of username via :caseExactMatch:
$wgLDAPSearchAttributes = [ 'labs' => 'cn:caseExactMatch:' ];
$wgLDAPBaseDNs = [ 'labs' => 'dc=wikimedia,dc=org' ];
$wgLDAPUserBaseDNs = [ 'labs' => 'ou=people,dc=wikimedia,dc=org' ];
$wgLDAPEncryptionType = [ 'labs' => 'tls' ];
$wgLDAPWriteLocation = [ 'labs' => 'ou=people,dc=wikimedia,dc=org' ];
$wgLDAPAddLDAPUsers = [ 'labs' => true ];
$wgLDAPUpdateLDAP = [ 'labs' => true ];
$wgLDAPPasswordHash = [ 'labs' => 'clear' ];
// 'invaliddomain' is set to true so that mail password options
// will be available on user creation and password mailing
// Force strict mode. T218589
// $wgLDAPMailPassword = [ 'labs' => true, 'invaliddomain' => true ];
$wgLDAPPreferences = [ 'labs' => [ "email" => "mail" ] ];
$wgLDAPLowerCaseUsername = [ 'labs' => false, 'invaliddomain' => false ];
// Only enable UseLocal if you need to promote an LDAP user
// $wgLDAPUseLocal = true;
// T168692: Attempt to lock LDAP accounts when blocked
$wgLDAPLockOnBlock = true;
$wgLDAPLockPasswordPolicy = 'cn=disabled,ou=ppolicies,dc=wikimedia,dc=org';

// Local debug logging for troubleshooting LDAP issues
// phpcs:disable Generic.CodeAnalysis.UnconditionalIfStatement.Found
if ( false ) {
	$wgLDAPDebug = 5;
	$monolog = LoggerFactory::getProvider();
	$monolog->mergeConfig( [
		'loggers' => [
			'ldap' => [
				'handlers' => [ 'wikitech-ldap' ],
				'processors' => array_keys( $wmgMonologProcessors ),
			],
		],
		'handlers' => [
			'wikitech-ldap' => [
				'class' => StreamHandler::class,
				'args' => [ '/tmp/ldap-s-1-debug.log' ],
				'formatter' => 'line',
			],
		],
	] );
}

// Dummy setting for conduit api token to be used by the BlockIpComplete hook
// that tries to disable Phabricator accounts. Real value should be provided
// by /etc/mediawiki/WikitechPrivateSettings.php
$wmgPhabricatorApiToken = false;

// Dummy settings for Gerrit api access to be used by the BlockIpComplete hook
// that tries to disable Gerrit accounts. Real values should be provided by
// /etc/mediawiki/WikitechPrivateSettings.php
$wmgGerritApiUser = false;
$wmgGerritApiPassword = false;

// Dummy setting for GitLab api access to be used by the BlockIpComplete
// hook that tries to disable GitLab accounts. Real value should be provided
// by /etc/mediawiki/WikitechPrivateSettings.php
$wmgGitLabApiToken = false;

# This must be loaded AFTER OSM, to overwrite it's defaults
# Except when we're not an OSM host and we're running like a maintenance script.
if ( file_exists( '/etc/mediawiki/WikitechPrivateSettings.php' ) ) {
	require_once '/etc/mediawiki/WikitechPrivateSettings.php';
}

# wgCdnReboundPurgeDelay is set to 11 in reverse-proxy.php but
# since we aren't using the shared jobqueue, we don't support delays
$wgCdnReboundPurgeDelay = 0;

/**
 * Make arbitrary Conduit requests to the Wikimedia Phabricator
 *
 * @param string $apiToken
 * @param string $path
 * @param array $query
 * @return mixed|false Result of the Conduit request or false on error
 */
function wmfPhabClient( string $apiToken, string $path, $query ) {
	$query['__conduit__'] = [ 'token' => $apiToken ];
	$post = [
		'params' => json_encode( $query ),
		'output' => 'json',
	];
	$phabUrl = 'https://phabricator.wikimedia.org';
	$ch = curl_init( "{$phabUrl}/api/{$path}" );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
	$ret = curl_exec( $ch );
	curl_close( $ch );
	if ( $ret ) {
		$resp = json_decode( $ret, true );
		if ( !$resp['error_code'] ) {
			return $resp['result'];
		}
	}
	wfDebugLog(
		'WikitechPhabBan',
		"Phab {$path} error " . var_export( $ret, true )
	);
	return false;
}

// Attempt to disable related accounts when a developer account is
// permablocked.
$wgHooks['BlockIpComplete'][] = static function ( $block, $user, $prior ) use ( $wmgPhabricatorApiToken ) {
	if ( !$wmgPhabricatorApiToken
		// 1 is the value of Block::TYPE_USER
		|| $block->getType() !== 1
		|| $block->getExpiry() !== 'infinity'
		|| !$block->isSitewide()
	) {
		// Nothing to do if we don't have config or if the block is not
		// a site-wide indefinite block of a named user.
		return;
	}

	try {
		$username = $block->getTargetName();
		$resp = wmfPhabClient( $wmgPhabricatorApiToken, 'user.ldapquery', [
			'ldapnames' => [ $username ],
			'offset' => 0,
			'limit' => 1,
		] );

		if ( $resp ) {
			$phid = $resp[0]['phid'];
			wmfPhabClient( $wmgPhabricatorApiToken, 'user.edit', [
				'transactions' => [
					[
						'type' => 'disabled',
						'value' => true,
					],
				],
				'objectIdentifier' => $phid,
			] );
		}
	} catch ( Throwable $t ) {
		wfDebugLog(
			'WikitechPhabBan',
			"Unhandled error blocking Phabricator user: {$t}"
		);
	}
};
$wgHooks['UnblockUserComplete'][] = static function ( $block, $user ) use ( $wmgPhabricatorApiToken ) {
	if ( !$wmgPhabricatorApiToken
		// 1 is the value of Block::TYPE_USER
		|| $block->getType() !== 1
		|| $block->getExpiry() !== 'infinity'
		|| !$block->isSitewide()
	) {
		// Nothing to do if we don't have config or if the block is not
		// a site-wide indefinite block of a named user.
		return;
	}

	try {
		$username = $block->getTargetName();
		$resp = wmfPhabClient( $wmgPhabricatorApiToken, 'user.ldapquery', [
			'ldapnames' => [ $username ],
			'offset' => 0,
			'limit' => 1,
		] );

		if ( $resp ) {
			$phid = $resp[0]['phid'];
			wmfPhabClient( $wmgPhabricatorApiToken, 'user.edit', [
				'transactions' => [
					[
						'type' => 'disabled',
						'value' => false,
					],
				],
				'objectIdentifier' => $phid,
			] );
		}
	} catch ( Throwable $t ) {
		wfDebugLog(
			'WikitechPhabBan',
			"Unhandled error unblocking Phabricator user: {$t}"
		);
	}
};

/**
 * Lookup a Gerrit account ID.
 *
 * @param string $gerritUsername
 * @param string $gerritPassword
 * @param string $uid Developer account shellname (LDAP `uid` attribute)
 * @return int|null null on failure, or numeric account ID on success
 */
function wmfGerritFindAccountId(
	string $gerritUsername,
	string $gerritPassword,
	string $uid
) {
	$gerritUrl = 'https://gerrit.wikimedia.org';
	$ch = curl_init(
		// By default Gerrit only matches active accounts (by automatically
		// adding `is:active`. Adding both ensure we get the account regardless
		// of its state.
		// https://phabricator.wikimedia.org/T307558#9625736
		"{$gerritUrl}/r/a/accounts/?n=1&q=(is:inactive+OR+is:active)+username:" . urlencode( $uid )
	);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_USERPWD, "{$gerritUsername}:{$gerritPassword}" );

	$gerritId = null;
	$ret = curl_exec( $ch );
	if ( $ret === false ) {
		wfDebugLog(
			'WikitechGerritBan',
			"Gerrit user lookup of username:{$uid} failed: " . curl_error( $ch )
		);
	} else {
		// Gerrit responds with JSON, sort of...
		$jsonBody = ltrim( $ret, ")]}'" );
		$json = json_decode( $jsonBody, true );
		if ( $json ) {
			$gerritId = $json[0]['_account_id'];
		}
	}
	curl_close( $ch );

	return $gerritId;
}

/**
 * Changes the Gerrit active status of the specified user using
 * the specified HTTP method (PUT to enable and DELETE to disable)
 *
 * @param string $gerritUsername
 * @param string $gerritPassword
 * @param string $username
 * @param string $httpMethod
 * @return int|null null on failure, or HTTP response code on success
 */
function wmfGerritSetActive(
	string $gerritUsername,
	string $gerritPassword,
	string $username,
	string $httpMethod
) {
	// Disable gerrit user tied to developer account
	$gerritUrl = 'https://gerrit.wikimedia.org';
	$ch = curl_init(
		"{$gerritUrl}/r/a/accounts/" . urlencode( $username ) . '/active'
	);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $httpMethod );
	curl_setopt(
		$ch, CURLOPT_USERPWD,
		"{$gerritUsername}:{$gerritPassword}"
	);

	if ( curl_exec( $ch ) === false ) {
		wfDebugLog(
			'WikitechGerritBan',
			"Gerrit user active status change ({$httpMethod}) of {$username} failed: " . curl_error( $ch )
		);
		$status = null;
	} else {
		$status = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	}

	curl_close( $ch );

	return $status;
}

/**
 * @param string $username Developer account username (LDAP `cn` attribute)
 * @return string Developer account shellname (LDAP `uid` attribute)
 */
function wmfGetDeveloperAccountUid( string $username ): string {
	$ldap = LdapAuthenticationPlugin::getInstance();
	$ldap->connect();
	$ldap->getUserDN( $username );
	$uid = $ldap->userInfo[0]['uid'][0];
	if ( !$uid ) {
		throw new RuntimeException( "Did not find UID for user '$username'" );
	}
	return $uid;
}

$wgHooks['BlockIpComplete'][] = static function ( $block, $user, $prior ) use ( $wmgGerritApiUser, $wmgGerritApiPassword ) {
	if ( !$wmgGerritApiUser
		|| !$wmgGerritApiPassword
		// 1 is the value of Block::TYPE_USER
		|| $block->getType() !== 1
		|| $block->getExpiry() !== 'infinity'
		|| !$block->isSitewide()
	) {
		// Nothing to do if we don't have config or if the block is not
		// a site-wide indefinite block of a named user.
		return;
	}
	try {
		$userIdent = $block->getTargetUserIdentity();
		if ( !$userIdent ) {
			return;
		}
		$username = $userIdent->getName();
		$gerritId = wmfGerritFindAccountId(
			$wmgGerritApiUser,
			$wmgGerritApiPassword,
			wmfGetDeveloperAccountUid( $username )
		);
		if ( $gerritId === null ) {
			return;
		}

		$status = wmfGerritSetActive(
			$wmgGerritApiUser,
			$wmgGerritApiPassword,
			$gerritId,
			'DELETE'
		);

		// https://gerrit.wikimedia.org/r/Documentation/rest-api-accounts.html#delete-active

		$userMsg = "{$username} (id: {$gerritId})";

		switch ( $status ) {
			case 204:
				wfDebugLog( 'WikitechGerritBan', "{$userMsg} is now blocked in Gerrit" );
				break;
			case 409:
				wfDebugLog( 'WikitechGerritBan', "{$userMsg} is already blocked in Gerrit" );
				break;
			default:
				wfDebugLog( 'WikitechGerritBan', "Gerrit block of {$userMsg} failed with status {$status}" );
		}

	} catch ( Throwable $t ) {
		wfDebugLog(
			'WikitechGerritBan',
			"Unhandled error blocking Gerrit user: {$t}"
		);
	}
};
$wgHooks['UnblockUserComplete'][] = static function ( $block, $user ) use ( $wmgGerritApiUser, $wmgGerritApiPassword ) {
	if ( !$wmgGerritApiUser
		|| !$wmgGerritApiPassword
		// 1 is the value of Block::TYPE_USER
		|| $block->getType() !== 1
		|| $block->getExpiry() !== 'infinity'
		|| !$block->isSitewide()
	) {
		// Nothing to do if we don't have config or if the block is not
		// a site-wide indefinite block of a named user.
		return;
	}
	try {
		$userIdent = $block->getTargetUserIdentity();
		if ( !$userIdent ) {
			return;
		}
		$username = $userIdent->getName();
		$gerritId = wmfGerritFindAccountId(
			$wmgGerritApiUser,
			$wmgGerritApiPassword,
			wmfGetDeveloperAccountUid( $username )
		);
		if ( $gerritId === null ) {
			return;
		}

		$status = wmfGerritSetActive(
			$wmgGerritApiUser,
			$wmgGerritApiPassword,
			$gerritId,
			'PUT'
		);

		// https://gerrit.wikimedia.org/r/Documentation/rest-api-accounts.html#set-active

		$userMsg = "{$username} (id: {$gerritId})";

		switch ( $status ) {
			case 201:
				wfDebugLog( 'WikitechGerritBan', "{$userMsg} is now unblocked in Gerrit" );
				break;
			case 200:
				wfDebugLog( 'WikitechGerritBan', "{$userMsg} was not blocked in Gerrit" );
				break;
			default:
				wfDebugLog( 'WikitechGerritBan', "Gerrit unblock of {$userMsg} failed with status {$status}" );
		}

	} catch ( Throwable $t ) {
		wfDebugLog(
			'WikitechGerritBan',
			"Unhandled error unblocking Gerrit user: {$t}"
		);
	}
};

/**
 * Invalidates sessions of a blocked user and therefore logs them out.
 */
$wgHooks['BlockIpComplete'][] = static function ( $block, $user, $prior ) {
	// 1 is the value of Block::TYPE_USER
	if ( $block->getType() !== 1
		|| $block->getExpiry() !== 'infinity'
		|| !$block->isSitewide()
	) {
		// Nothing to do if we don't have config or if the block is not
		// a site-wide indefinite block of a named user.
		return;
	}
	if ( !$block->getTargetUserIdentity() ) {
		return;
	}
	$userObj = MediaWikiServices::getInstance()
		->getUserFactory()
		->newFromUserIdentity( $block->getTargetUserIdentity() );
	SessionManager::singleton()->invalidateSessionsForUser( $userObj );
};

// T360883 - This is provided by Bitu now.
// phpcs:ignore MediaWiki.Files.ClassMatchesFilename.NotMatch
class DisallowLdapChangesSecondaryAuthenticationProvider extends MediaWiki\Auth\AbstractSecondaryAuthenticationProvider {
	public function getAuthenticationRequests( $action, array $options ) {
		return [];
	}

	public function beginSecondaryAuthentication( $user, array $reqs ) {
		return MediaWiki\Auth\AuthenticationResponse::newAbstain();
	}

	public function beginSecondaryAccountCreation( $user, $creator, array $reqs ) {
		return MediaWiki\Auth\AuthenticationResponse::newAbstain();
	}

	public function providerAllowsAuthenticationDataChange( \MediaWiki\Auth\AuthenticationRequest $req, $checkData = true ) {
		if ( $req instanceof MediaWiki\Auth\PasswordAuthenticationRequest ) {
			return StatusValue::newFatal( 'hookaborted' );
		}

		return parent::providerAllowsAuthenticationDataChange( $req, $checkData );
	}

	public function providerAllowsPropertyChange( $property ) {
		if ( $property === 'emailaddress' ) {
			return false;
		}

		return parent::providerAllowsPropertyChange( $property );
	}
}
$wgAuthManagerAutoConfig['secondaryauth'][DisallowLdapChangesSecondaryAuthenticationProvider::class] = [
	'class' => DisallowLdapChangesSecondaryAuthenticationProvider::class,
];

// Also, hide the email confirmation timestamp from Special:Preferences. LDAPAuthentication
// trusts the emails it reads from LDAP and the timestamp in the MW database is not accurate.
$wgHooks['GetPreferences'][] = static function ( $user, &$preferences ) {
	unset( $preferences['emailauthentication'] );
};

/**
 * Make arbitrary API requests to the Wikimedia GitLab instance
 *
 * @param string $apiToken
 * @param string $path
 * @param array|null $query
 * @param bool $postVerb
 * @return mixed|false Result of the API request or false on error
 */
function wmfGitLabClient(
	string $apiToken,
	string $path,
	$query = null,
	$postVerb = false
) {
	$headers = [
		"PRIVATE-TOKEN: {$apiToken}",
		'Content-Type: application/json',
	];
	$gitlabUrl = 'https://gitlab.wikimedia.org';
	$url = "{$gitlabUrl}/api/v4/{$path}";

	if ( $query !== null ) {
		$url = $url . '?' . http_build_query( $query );
	}

	$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_POST, $postVerb );
	$ret = curl_exec( $ch );
	$status = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	curl_close( $ch );
	if ( $ret ) {
		$resp = json_decode( $ret, true );
		if ( $status >= 200 && $status <= 300 ) {
			return $resp;
		}
	}
	wfDebugLog(
		'WikitechGitLabBan',
		"GitLab {$path} error " . var_export( $ret, true )
	);
	return false;
}

/**
 * Lookup a GitLab account ID.
 *
 * @param string $apiToken
 * @param string $cn Developer account username (LDAP `cn` attribute)
 * @return int|null null on failure, or numeric account ID on success
 */
function wmfGitLabFindAccountId( string $apiToken, string $cn ) {
	$resp = wmfGitLabClient(
		$apiToken,
		'users',
		[ 'provider' => 'openid_connect', 'extern_uid' => $cn ]
	);
	if ( $resp ) {
		return $resp[ 0 ][ 'id' ];
	}
	return null;
}

$wgHooks['BlockIpComplete'][] = static function ( $block, $user, $prior ) use ( $wmgGitLabApiToken ) {
	if ( !$wmgGitLabApiToken
		// 1 is the value of Block::TYPE_USER
		|| $block->getType() !== 1
		|| $block->getExpiry() !== 'infinity'
		|| !$block->isSitewide()
	) {
		// Nothing to do if we don't have config or if the block is not
		// a site-wide indefinite block of a named user.
		return;
	}
	try {
		$userIdent = $block->getTargetUserIdentity();
		if ( !$userIdent ) {
			return;
		}
		$username = $userIdent->getName();
		$gitlabId = wmfGitLabFindAccountId( $wmgGitLabApiToken, $username );
		if ( $gitlabId === null ) {
			return;
		}

		$resp = wmfGitLabClient(
			$wmgGitLabApiToken,
			"users/{$gitlabId}/block",
			null,
			true
		);
		if ( $resp !== false ) {
			wfDebugLog(
				'WikitechGitLabBan',
				"GitLab block of {$username} failed"
			);
		}
	} catch ( Throwable $t ) {
		wfDebugLog(
			'WikitechGitLabBan',
			"Unhandled error blocking GitLab user: {$t}"
		);
	}
};
$wgHooks['UnblockUserComplete'][] = static function ( $block, $user ) use ( $wmgGitLabApiToken ) {
	if ( !$wmgGitLabApiToken
		// 1 is the value of Block::TYPE_USER
		|| $block->getType() !== 1
		|| $block->getExpiry() !== 'infinity'
		|| !$block->isSitewide()
	) {
		// Nothing to do if we don't have config or if the block is not
		// a site-wide indefinite block of a named user.
		return;
	}
	try {
		$userIdent = $block->getTargetUserIdentity();
		if ( !$userIdent ) {
			return;
		}
		$username = $userIdent->getName();
		$gitlabId = wmfGitLabFindAccountId( $wmgGitLabApiToken, $username );
		if ( $gitlabId === null ) {
			return;
		}

		$resp = wmfGitLabClient(
			$wmgGitLabApiToken,
			"users/{$gitlabId}/unblock",
			null,
			true
		);
		if ( $resp !== false ) {
			wfDebugLog(
				'WikitechGitLabBan',
				"GitLab block of {$username} failed"
			);
		}
	} catch ( Throwable $t ) {
		wfDebugLog(
			'WikitechGitLabBan',
			"Unhandled error unblocking GitLab user: {$t}"
		);
	}
};
