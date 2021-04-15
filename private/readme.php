<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This is the Documentation for PrivateSettings.php. It is also used to
# satisfy static analysis and for unit tests to ensure these variables
# exist.
#
# The real file is stored in a private Git repository on the
# deployment host that overlaps this directory.


/************************************************************************
 * @name Settings for MediaWiki core and extension (wg-)
 * @{
 */

// FIXME: Is this still needed?
// It appears unused since MediaWiki 1.5.
$wgDBsqlpassword = null;

// FIXME: Is this still needed?
// It appears unused since MediaWiki 1.24.
$wgProxyKey = null;

/**
 *
 * Usage (incomplete):
 *
 * - MediaWiki core:
 *   - WebInstaller (not used in production).
 *   - CryptHKDF, secret entropy for random hashes.
 *   - SpecialRunJobs (not used in production).
 *
 * - EventBus extension:
 *   - Used to create signature that authorise submission and RPC
 *     execution of JobQueue jobs.
 *
 * - OAuth extension:
 *   - The default value of $wgOAuthSecretKey, used with HMAC to store consumer shared
 *     data in the database.
 *
 * - FlaggedRevs extension:
 *   - Validation key in RevisionReviewForm::validationKey()
 *
 *
 * @see https://doc.wikimedia.org/mediawiki-core/master/php/DefaultSettings_8php.html#a38155f5bd16275bf8cd15745b899b19d
 */
$wgSecretKey = null;

/**
 * MySQL database credentials for the current process,
 * differentiates between web and CLI.
 */
$wgDBuser = null;
$wgDBpassword = null;

/**
 * MySQL database credentials with elevated access,
 * if appropriate for the current process.
 */
if ( false === true ) {
	$wgDBadminuser = null;
	$wgDBadminpassword = null;
}

/**
 * Included in the hash behind user session IDs.
 *
 * Changing this will invalidate all active sessions, and thus logs out all users.
 *
 * @see https://doc.wikimedia.org/mediawiki-core/master/php/DefaultSettings_8php.html#a4c5339d5e9e8d2602389149ac3e32f4c
 */
$wgAuthenticationTokenVersion = null;

/** @} */
/************************************************************************
 * @name Settings for use within wmf-config (wmg- and wmf-)
 * @{
 */

/**
 * Used for the hashes of captcha answers.
 *
 * When a captcha is created and selected, it publicly identified with
 * a hash of this secret and the expected answer. Upon submission their
 * answer is hashed the same way to validate it.
 *
 * @see wmf-config/CommonSettings.php, $wgCaptchaSecret
 * @see mediawiki/extensions/ConfirmEdit (FancyCaptcha)
 */
// TODO: Why not set $wgCaptchaSecret directly?
$wmgCaptchaSecret = null;

// FIXME: Is this still used?
$wmgCaptchaPassword = null;

/**
 * @see wmf-config/filebackend.php
 */
$wmfSwiftConfig = [];
$wmfSwiftConfig['eqiad'] =
$wmfSwiftConfig['codfw'] = [
	'cirrusAuthUrl' => null,
	'cirrusUser' => null,
	'cirrusKey' => null,
	'thumborUser' => null,
	'thumborPrivateUser' => null,
	'thumborUrl' => null,
	'thumborSecret' => null,
	'user' => null,
	'key' => null,
	'tempUrlKey' => null,
];

/**
 * Credentials for RedisBagOStuff and RedisLockManager backends.
 *
 * @see wmf-config/redis.php, $wgObjectCaches
 * @see wmf-config/filebackend.php, $wgLockManagers
 */
$wmgRedisPassword = null;

// FIXME: Is this still used?
$wmgZeroPortalApiUserName = null;
$wmgZeroPortalApiPassword = null;

/**
 * @see wmf-config/CommonSettings.php, $wgVERPsecret
 * @see mediawiki/extensions/BounceHandler
 */
// TODO: Why not set $wgVERPsecret directly?
$wmgVERPsecret = null;

// FIXME: Is this still used?
$wmgLogstashPassword = null;

// FIXME: Is this still used?
$wmgForceProfilePassword = null;

/**
 * The 'key' option of $wgContentTranslationCXServerAuth.
 *
 * @see wmf-config/CommonSettings.php, $wgContentTranslationCXServerAuth
 * @see mediawiki/extensions/ContentTranslation
 */
$wmgContentTranslationCXServerAuthKey = null;

// FIXME: Is this still used?
$wmgContributionTrackingDBpassword = null;

/**
 * @see wmf-config/CommonSettings.php, $wgVirtualRestConfig
 * @see wmf-config/ProductionServices.php, 'electron'
 * @see mediawiki/extensions/Collection
 */
$wmgElectronSecret = null;

/**
 * The 'hmac_key' option of the RESTBagOStuff for Kask.
 *
 * @see wmf-config/CommonSettings.php, $wgObjectCaches['kask-session']
 */
$wmgSessionStoreHMACKey = null;

/**
 * XHGui database credentials.
 *
 * @see wmf-config/PhpAutoPrepend.php
 * @see wmf-config/profiler.php
 * @see https://phabricator.wikimedia.org/T254795
 */
$wmgXhguiDBuser = null;
$wmgXhguiDBpassword = null;

/**
 * MediaModeration extension private API configuration details
 *
 * @see mediawiki/extensions/MediaModeration
 */
$wgMediaModerationPhotoDNASubscriptionKey = 'subscription-key';
$wgMediaModerationRecipientList = [];

/**
 * Used as a salt in VectorPrefDiffInstrumentation to hash the user id.
 * @see mediawiki/extensions/WikimediaEvents
 */
$wgWMEVectorPrefDiffSalt = null;
