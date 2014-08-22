<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.

/**
 * This file is for overriding the default InitialiseSettings.php with our own
 * stuff. Prefixing a setting key with '-' to override all values from
 * InitialiseSettings.php
 *
 * Please wrap your code in functions to avoid tainting the global namespace.
 */

/**
 * Main entry point to override production settings. Supports key beginning with
 * a dash to completely override a setting.
 */
function WikitechOverrideSettings() {

	/* From Settings.php on old wikitech*/

	$wgEmailConfirmToEdit = true;
	$wgUseTeX = false;
	$wgEnableCreativeCommonsRdf = true;
	$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";
	$wgCacheDirectory = "$IP/cache";
	$wgMainCacheType    = CACHE_MEMCACHED;
	$wgMessageCacheType = CACHE_MEMCACHED;
	$wgSessionsInMemcached = true;

	# Anons can't edit
	$wgGroupPermissions['*']['edit'] = false;

	# Shellmanager is a Custom role for wikitech
	$wgGroupPermissions['shellmanagers']['userrights'] = false;
	$wgAddGroups['shellmanagers'] = array( 'shell' );

	$wgGroupPermissions['contentadmin']['protect'] = true;
	$wgGroupPermissions['contentadmin']['editprotected'] = true;
	$wgGroupPermissions['contentadmin']['bigdelete'] = true;
	$wgGroupPermissions['contentadmin']['delete'] = true;
	$wgGroupPermissions['contentadmin']['undelete'] = true;
	$wgGroupPermissions['contentadmin']['block'] = true;
	$wgGroupPermissions['contentadmin']['blockemail'] = true;
	$wgGroupPermissions['contentadmin']['patrol'] = true;
	$wgGroupPermissions['contentadmin']['autopatrol'] = true;
	$wgGroupPermissions['contentadmin']['import'] = true;
	$wgGroupPermissions['contentadmin']['importupload'] = true;
	$wgGroupPermissions['contentadmin']['upload_by_url'] = true;
	$wgGroupPermissions['contentadmin']['movefile'] = true;
	$wgGroupPermissions['contentadmin']['suppressredirect'] = true;
	$wgGroupPermissions['contentadmin']['rollback'] = true;
	$wgGroupPermissions['contentadmin']['browsearchive'] = true;
	$wgGroupPermissions['contentadmin']['deletedhistory'] = true;
	$wgGroupPermissions['contentadmin']['deletedtext'] = true;
	$wgGroupPermissions['contentadmin']['autoconfirmed'] = true;


	$wgNamespacesWithSubpages[NS_MAIN] = true;

	$wgExtraNamespaces[110] = 'Obsolete';
	$wgExtraNamespaces[111] = 'Obsolete_talk';
	$wgNamespacesWithSubpages[110] = true;
	$wgExtraNamespaces[112] = 'Ops';
	$wgExtraNamespaces[113] = 'Ops_talk';
	$wgNamespacesWithSubpages[112] = true;
	$wgNamespacesWithSubpages[113] = true;
	$wgContentNamespaces[] = 112;
	$wgNamespacesToBeSearchedDefault[112] = true;

	$wgGroupPermissions['bots']['skipcaptcha'] = true;
	$wgCaptchaTriggers['addurl']        = false;

	require_once( "$IP/extensions/DynamicSidebar/DynamicSidebar.php" );
	include_once( "$IP/extensions/Validator/Validator.php" );
	include_once( "$IP/extensions/SemanticMediaWiki/SemanticMediaWiki.php" );
	include_once( "$IP/extensions/SemanticForms/SemanticForms.php" );

	# SemanticResultFormats, an extra set of printers for SMW
	require_once( "$IP/extensions/SemanticResultFormats/SemanticResultFormats.php" );

	require_once( "Private.php" );
	require_once( "Debug.php" );

	/* from Local.php on old Wikitech */
	$wgDBserver           = "virt1000.wikimedia.org";
	$wgDBname             = "labswiki";

	$wgServer             = "https://wikitech.wikimedia.org";
	$wgSitename           = "Wikitech";
	$wgPasswordSenderName = "Wikitech Mail";

	$wgCookieDomain       = "wikitech.wikimedia.org";

	$wgLogo               = "https://wikitech.wikimedia.org/w/images/thumb/6/60/Wikimedia_labs_logo.svg/120px-Wikimedia_labs_logo.svg.png";

	# Only sysops can create new accounts.
	$wgGroupPermissions['*']['createaccount'] = true;

	$wgGroupPermissions['cloudadmin']['listall'] = true;
	$wgGroupPermissions['cloudadmin']['manageproject'] = true;
	$wgGroupPermissions['cloudadmin']['userrights'] = true;
	$wgGroupPermissions['cloudadmin']['managednsdomain'] = true;
	$wgGroupPermissions['cloudadmin']['manageglobalpuppet'] = true;
	$wgGroupPermissions['cloudadmin']['accessrestrictedregions'] = true;
	$wgGroupPermissions['shell']['loginviashell'] = true;

	$wgImportSources[] = "mw";

	enableSemantics('wikitech');

	require_once ( "$IP/extensions/CheckUser/CheckUser.php" );

	require_once( "$IP/extensions/LdapAuthentication/LdapAuthentication.php" );
	$wgAuth = new LdapAuthenticationPlugin();
	$wgLDAPDomainNames = array( 'labs');
	$wgLDAPServerNames = array( 'labs' => 'virt1000.wikimedia.org' );
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
	$wgMinimalPasswordLength = 1;

	require_once( "$IP/extensions/OATHAuth/OATHAuth.php" );

	require_once( "$IP/extensions/OpenStackManager/OpenStackManager.php" );
	$wgOpenStackManagerNovaKeypairStorage = 'ldap';
	$wgOpenStackManagerNovaIdentityURI = 'http://virt1000.wikimedia.org:35357/v2.0';
	$wgOpenStackManagerLDAPDomain = 'labs';
	$wgOpenStackManagerLDAPProjectBaseDN = 'ou=projects,dc=wikimedia,dc=org';
	$wgOpenStackManagerLDAPProjectGroupBaseDN = "ou=groups,dc=wikimedia,dc=org";
	$wgOpenStackManagerLDAPInstanceBaseDN = 'ou=hosts,dc=wikimedia,dc=org';
	$wgOpenStackManagerLDAPServiceGroupBaseDN = 'ou=servicegroups,dc=wikimedia,dc=org';
	$wgOpenStackManagerLDAPDefaultGid = '500';
	$wgOpenStackManagerLDAPDefaultShell = '/usr/local/bin/sillyshell';
	$wgOpenStackManagerLDAPUseUidAsNamingAttribute = true;
	$wgOpenStackManagerDNSOptions = array(
		'enabled' => true,
		'servers' => array( 'primary' => 'virt1000.wikimedia.org' ),
		'soa'     => array( 'hostmaster' => 'hostmaster.wikimedia.org', 'refresh' => '1800', 'retry' => '3600', 'expiry' => '86400', 'minimum' => '7200' ),
	);
	$wgOpenStackManagerPuppetOptions = array(
		'enabled' => true,
		'defaultclasses' => array( 'base', 'role::labs::instance', 'sudo::labs_project' ),
		'defaultvariables' => array( 'realm' => 'labs' ),
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
	$wgNamespacesWithSubpages[NS_NOVA_RESOURCE] = true;
	#$wgNamespacesToBeSearchedDefault[NS_NOVA_RESOURCE] = true;
	$wgNamespacesToBeSearchedDefault[NS_HELP] = true;

	#require_once("$IP/extensions/OpenID/OpenID.php");
	$wgOpenIDClientOnly = false;
	$wgHideOpenIDLoginLink = true;
	$wgOpenIDConsumerAllow = '';
	$wgOpenIDConsumerDenyByDefault = true;
}
