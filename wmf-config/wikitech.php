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

require_once "$IP/extensions/OpenStackManager/OpenStackManager.php";
switch ( $wgDBname ) {
case 'labswiki' :
	$wgOpenStackManagerNovaIdentityURI = 'http://labcontrol1001.wikimedia.org:35357/v2.0';
	$wgOpenStackManagerNovaIdentityV3URI = 'http://labcontrol1001.wikimedia.org:35357/v3';
	$wgOpenStackManagerDNSOptions = [
		'enabled' => true,
		'servers' => [ 'primary' => 'labcontrol1001.wikimedia.org' ],
		'soa'     => [ 'hostmaster' => 'hostmaster.wikimedia.org', 'refresh' => '1800', 'retry' => '3600', 'expiry' => '86400', 'minimum' => '7200' ],
	];
		break;
case 'labtestwiki' :
	$wgOpenStackManagerNovaIdentityURI = 'http://labtestcontrol2001.wikimedia.org:35357/v2.0';
	$wgOpenStackManagerNovaIdentityV3URI = 'http://labtestcontrol2001.wikimedia.org:35357/v3';
	$wgOpenStackManagerDNSOptions = [
		'enabled' => true,
		'servers' => [ 'primary' => 'labtestcontrol2001.wikimedia.org' ],
		'soa'     => [ 'hostmaster' => 'hostmaster.wikimedia.org', 'refresh' => '1800', 'retry' => '3600', 'expiry' => '86400', 'minimum' => '7200' ],
	];
		break;
}
$wgOpenStackManagerNovaKeypairStorage = 'ldap';
$wgOpenStackManagerLDAPDomain = 'labs';
$wgOpenStackManagerLDAPProjectBaseDN = 'ou=projects,dc=wikimedia,dc=org';
$wgOpenStackManagerLDAPProjectGroupBaseDN = "ou=groups,dc=wikimedia,dc=org";
$wgOpenStackManagerLDAPInstanceBaseDN = 'ou=hosts,dc=wikimedia,dc=org';
$wgOpenStackManagerLDAPServiceGroupBaseDN = 'ou=servicegroups,dc=wikimedia,dc=org';
$wgOpenStackManagerLDAPDefaultGid = '500';
$wgOpenStackManagerLDAPDefaultShell = '/bin/bash';
$wgOpenStackManagerLDAPUseUidAsNamingAttribute = true;
$wgOpenStackManagerPuppetOptions = [
	'enabled' => true,
	'defaultclasses' => [],
	'defaultvariables' => [],
];
$wgOpenStackManagerInstanceUserData = [
	'cloud-config' => [
		# 'puppet' => array( 'conf' => array( 'puppetd' => array( 'server' => 'wikitech.wikimedia.org', 'certname' => '%i' ) ) ),
		# 'apt_upgrade' => 'true',
		'apt_update' => 'false', // Puppet will cause this
		# 'apt_mirror' => 'http://ubuntu.wikimedia.org/ubuntu/',
	],
	'scripts' => [
		# Used for new images
		'runpuppet.sh' => '/srv/org/wikimedia/controller/scripts/runpuppet.sh',
		# Used for pre-configured images
		'runpuppet-new.sh' => '/srv/org/wikimedia/controller/scripts/runpuppet-new.sh',
	],
	'upstarts' => [
		'ttyS0.conf' => '/srv/org/wikimedia/controller/upstarts/ttyS0.conf',
		'ttyS1.conf' => '/srv/org/wikimedia/controller/upstarts/ttyS1.conf',
	],
];
$wgOpenStackManagerDefaultSecurityGroupRules = [
	# Allow all traffic within the project
	[ 'group' => 'default' ],
	# Allow ping from everywhere
	[ 'fromport' => '-1',
		'toport' => '-1',
		'protocol' => 'icmp',
		'range' => '0.0.0.0/0' ],
	# Allow ssh from all projects
	[ 'fromport' => '22',
		'toport' => '22',
		'protocol' => 'tcp',
		'range' => '10.0.0.0/8' ],
	# Allow nrpe access from all projects (access is limited in config)
	[ 'fromport' => '5666',
		'toport' => '5666',
		'protocol' => 'tcp',
		'range' => '10.0.0.0/8' ],
];
$wgOpenStackManagerInstanceBannedInstanceTypes = [
	"m1.tiny",
	"s1.tiny",
	"s1.small",
	"s1.medium",
	"s1.large",
	"s1.xlarge",
	"pmtpa-1",
	"pmtpa-2",
	"pmtpa-3",
	"pmtpa-4",
	"pmtpa-5",
	"pmtpa-6",
	"pmtpa-7",
	"pmtpa-8",
	"pmtpa-9",
	"pmtpa-10",
	"pmtpa-11"
];

# Enable doc links on the 'configure instance' page
$wgOpenStackManagerPuppetDocBase = 'http://doc.wikimedia.org/puppet/classes/__site__/';

$wgOpenStackManagerProxyGateways = [ 'eqiad' => '208.80.155.156' ];

# This must be loaded AFTER OSM, to overwrite it's defaults
# Except when we're not an OSM host and we're running like a maintenance script.
if ( file_exists( '/etc/mediawiki/WikitechPrivateSettings.php' ) ) {
	require_once '/etc/mediawiki/WikitechPrivateSettings.php';
}

# This next conditional bit is meant to select wikitech on labweb but
# not on silver.  After Silver is switched off the conditional
# can be removed as well as the first two lines here, in favor
# of just setting wmgUseClusterSquid to true on wikitech.
if ( defined( 'HHVM_VERSION' ) ) {
	$wgUseSquid = true;
	require "$wmfConfigDir/reverse-proxy.php";

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
}
