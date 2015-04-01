<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.

/* From Settings.php on old wikitech */

$wgEmailConfirmToEdit = true;
$wgUseTeX = false;
$wgEnableCreativeCommonsRdf = true;

$wgUseInstantCommons  = true;

$wgCaptchaTriggers['addurl']        = false;

/* from Local.php on old Wikitech */
$wgPasswordSenderName = "Wikitech Mail";

$wgCookieDomain       = "wikitech.wikimedia.org";

require_once( "$IP/extensions/Validator/Validator.php" );
require_once( "$IP/extensions/SemanticMediaWiki/SemanticMediaWiki.php" );
require_once( "$IP/extensions/SemanticForms/SemanticForms.php" );
require_once( "$IP/extensions/SemanticResultFormats/SemanticResultFormats.php" );
enableSemantics('wikitech');

require_once( "$IP/extensions/LdapAuthentication/LdapAuthentication.php" );
$wgAuth = new LdapAuthenticationPlugin();
$wgLDAPDomainNames = array( 'labs');
$wgLDAPServerNames = array( 'labs' => 'ldap-eqiad.wikimedia.org' );
$wgLDAPSearchAttributes = array( 'labs' => 'cn');
$wgLDAPBaseDNs = array( 'labs' => 'dc=wikimedia,dc=org' );
$wgLDAPUserBaseDNs = array( 'labs' => 'ou=people,dc=wikimedia,dc=org' );
$wgLDAPEncryptionType = array( 'labs' => 'tls');
$wgLDAPWriteLocation = array( 'labs' => 'ou=people,dc=wikimedia,dc=org' );
$wgLDAPAddLDAPUsers = array( 'labs' => true );
$wgLDAPUpdateLDAP = array( 'labs' => true );
$wgLDAPPasswordHash = array( 'labs' => 'clear' );
// 'invaliddomain' is set to true so that mail password options
// will be available on user creation and password mailing
$wgLDAPMailPassword = array( 'labs' => true, 'invaliddomain' => true );
$wgLDAPPreferences = array( 'labs' => array( "email"=>"mail" ) );
$wgLDAPUseFetchedUsername = array( 'labs' => true );
$wgLDAPLowerCaseUsernameScheme = array( 'labs' => false, 'invaliddomain' => false );
$wgLDAPLowerCaseUsername = array( 'labs' => false, 'invaliddomain' => false );
// Only enable UseLocal if you need to promote an LDAP user
#$wgLDAPUseLocal = true;

require_once( "$IP/extensions/OpenStackManager/OpenStackManager.php" );
$wgOpenStackManagerNovaKeypairStorage = 'ldap';
$wgOpenStackManagerNovaIdentityURI = 'http://virt1000.wikimedia.org:35357/v2.0';
$wgOpenStackManagerLDAPDomain = 'labs';
$wgOpenStackManagerLDAPProjectBaseDN = 'ou=projects,dc=wikimedia,dc=org';
$wgOpenStackManagerLDAPProjectGroupBaseDN = "ou=groups,dc=wikimedia,dc=org";
$wgOpenStackManagerLDAPInstanceBaseDN = 'ou=hosts,dc=wikimedia,dc=org';
$wgOpenStackManagerLDAPServiceGroupBaseDN = 'ou=servicegroups,dc=wikimedia,dc=org';
$wgOpenStackManagerLDAPDefaultGid = '500';
$wgOpenStackManagerLDAPDefaultShell = '/bin/bash';
$wgOpenStackManagerLDAPUseUidAsNamingAttribute = true;
$wgOpenStackManagerDNSOptions = array(
	'enabled' => true,
	'servers' => array( 'primary' => 'virt1000.wikimedia.org' ),
	'soa'     => array( 'hostmaster' => 'hostmaster.wikimedia.org', 'refresh' => '1800', 'retry' => '3600', 'expiry' => '86400', 'minimum' => '7200' ),
);
$wgOpenStackManagerPuppetOptions = array(
	'enabled' => true,
	'defaultclasses' => array( 'base', 'role::labs::instance' ),
	'defaultvariables' => array( 'realm' => 'labs', 'use_dnsmasq' => 'true' ),
);
$wgOpenStackManagerInstanceUserData = array(
	'cloud-config' => array(
		#'puppet' => array( 'conf' => array( 'puppetd' => array( 'server' => 'wikitech.wikimedia.org', 'certname' => '%i' ) ) ),
		#'apt_upgrade' => 'true',
		'apt_update' => 'false', // Puppet will cause this
		#'apt_mirror' => 'http://ubuntu.wikimedia.org/ubuntu/',
	),
	'scripts' => array(
		# Used for new images
		'runpuppet.sh' => '/srv/org/wikimedia/controller/scripts/runpuppet.sh',
		# Used for pre-configured images
		'runpuppet-new.sh' => '/srv/org/wikimedia/controller/scripts/runpuppet-new.sh',
	),
	'upstarts' => array(
		'ttyS0.conf' => '/srv/org/wikimedia/controller/upstarts/ttyS0.conf',
		'ttyS1.conf' => '/srv/org/wikimedia/controller/upstarts/ttyS1.conf',
	),
);
$wgOpenStackManagerDefaultSecurityGroupRules = array(
	# Allow all traffic within the project
	array( 'group' => 'default' ),
	# Allow ping from everywhere
	array( 'fromport' => '-1',
		'toport' => '-1',
		'protocol' => 'icmp',
		'range' => '0.0.0.0/0' ),
	# Allow ssh from all projects
	array( 'fromport' => '22',
		'toport' => '22',
		'protocol' => 'tcp',
		'range' => '10.0.0.0/8' ),
	# Allow nrpe access from all projects (access is limited in config)
	array( 'fromport' => '5666',
		'toport' => '5666',
		'protocol' => 'tcp',
		'range' => '10.0.0.0/8' ),
);
$wgOpenStackManagerInstanceBannedInstanceTypes = array(
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
);

# Enable doc links on the 'configure instance' page
$wgOpenStackManagerPuppetDocBase = 'http://doc.wikimedia.org/puppet/classes/__site__/';

$wgOpenStackManagerProxyGateways = array('pmtpa' => '208.80.153.214', 'eqiad' => '208.80.155.156');

# Restrict eqiad to a group
$wgOpenStackManagerRestrictedRegions = array();
$wgOpenStackManagerReadOnlyRegions = array();

$smwgNamespacesWithSemanticLinks[112] = true;
$smwgNamespacesWithSemanticLinks[NS_NOVA_RESOURCE] = true;
#$wgNamespacesToBeSearchedDefault[NS_NOVA_RESOURCE] = true;

# TODO:  Re-enable OpenID
#require_once("$IP/extensions/OpenID/OpenID.php");
#$wgOpenIDClientOnly = false;
#$wgHideOpenIDLoginLink = true;
#$wgOpenIDConsumerAllow = '';
#$wgOpenIDConsumerDenyByDefault = true;

require_once( "$IP/extensions/OATHAuth/OATHAuth.php" );
require_once( "$IP/extensions/DynamicSidebar/DynamicSidebar.php" );

$wgMainCacheType = 'memcached-pecl';
$wgMessageCacheType = 'memcached-pecl';

$wgSessionsInObjectCache = true;
$wgSessionCacheType = 'memcached-pecl';

// No cluster for us!
$wgDefaultExternalStore = false;

require_once( '/srv/mediawiki/private/WikitechPrivateLdapSettings.php' );

#$wgPasswordReminderResendTime = 0;
#$wgPasswordAttemptThrottle = false;
#$wgShowExceptionDetails = true;
#$wgLDAPDebug = 5;
#$wgDebugLogGroups["ldap"] = "/tmp/ldap-s-1-debug.log";
