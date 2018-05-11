<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

use MediaWiki\Logger\LoggerFactory;

require_once "$IP/extensions/LdapAuthentication/LdapAuthentication.php";
$wgAuthManagerAutoConfig['primaryauth'] += [
	LdapPrimaryAuthenticationProvider::class => [
		'class' => LdapPrimaryAuthenticationProvider::class,
		'args' => [ [
			'authoritative' => true, // don't allow local non-LDAP accounts
		] ],
		'sort' => 50, // must be smaller than local pw provider
	],
];
$wgLDAPDomainNames = [ 'labs' ];
switch ( $wgDBname ) {
case 'labswiki' :
	$wgLDAPServerNames = [ 'labs' => 'ldap-labs.eqiad.wikimedia.org' ];
		break;
case 'labtestwiki' :
	$wgLDAPServerNames = [ 'labs' => 'labtestservices2001.wikimedia.org' ];
		break;
}
$wgLDAPSearchAttributes = [ 'labs' => 'cn' ];
$wgLDAPBaseDNs = [ 'labs' => 'dc=wikimedia,dc=org' ];
$wgLDAPUserBaseDNs = [ 'labs' => 'ou=people,dc=wikimedia,dc=org' ];
$wgLDAPEncryptionType = [ 'labs' => 'tls' ];
$wgLDAPWriteLocation = [ 'labs' => 'ou=people,dc=wikimedia,dc=org' ];
$wgLDAPAddLDAPUsers = [ 'labs' => true ];
$wgLDAPUpdateLDAP = [ 'labs' => true ];
$wgLDAPPasswordHash = [ 'labs' => 'clear' ];
// 'invaliddomain' is set to true so that mail password options
// will be available on user creation and password mailing
$wgLDAPMailPassword = [ 'labs' => true, 'invaliddomain' => true ];
$wgLDAPPreferences = [ 'labs' => [ "email" => "mail" ] ];
$wgLDAPUseFetchedUsername = [ 'labs' => true ];
$wgLDAPLowerCaseUsernameScheme = [ 'labs' => false, 'invaliddomain' => false ];
$wgLDAPLowerCaseUsername = [ 'labs' => false, 'invaliddomain' => false ];
// Only enable UseLocal if you need to promote an LDAP user
# $wgLDAPUseLocal = true;

$wgLDAPDebug = 5; // Maximally verbose logs for Andrew Bogott, 8-Dec-2015

// Local debug logging for troubleshooting LDAP issues
// @codingStandardsIgnoreStart
if ( false ) {
	// @codingStandardsIgnoreEnd
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
				'class' => '\\Monolog\\Handler\\StreamHandler',
				'args' => [ '/tmp/ldap-s-1-debug.log' ],
				'formatter' => 'line',
			],
		],
	] );
}


# This must be loaded AFTER OSM, to overwrite it's defaults
# Except when we're not an OSM host and we're running like a maintenance script.
if ( file_exists( '/etc/mediawiki/WikitechPrivateSettings.php' ) ) {
	require_once '/etc/mediawiki/WikitechPrivateSettings.php';
}

# wgCdnReboundPurgeDelay is set to 11 in reverse-proxy.php but
# since we aren't using the shared jobqueue, we don't support delays
$wgCdnReboundPurgeDelay = 0;

# Wikitech on labweb is behind the misc-web varnishes so we need a different
# multicast IP for cache invalidation.  This file is loaded
# after the standard MW equivalent (in reverse-proxy.php)
# so we can just override it here.
$wgHTCPRouting = [
	'' => [
		'host' => '239.128.0.115',
		'port' => 4827
	]
];
