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
 * Settings are fetched through wmfLabsSettings() defined below.
 */
function wmfLabsOverrideSettings() {
	global $wmfConfigDir, $wgConf;

	// Override (or add) settings that we need within the labs environment,
	// but not in production.
	$betaSettings = wmfLabsSettings();

	// Set configuration string placeholder 'variant' to 'beta-hhvm'
	// or 'beta' depending on the the runtime executing the code.
	// This is to ensure that *.beta-hhvm.wmflabs.org wikis use
	// loginwiki.wikimedia.beta-hhvm.wmflabs.org as their loginwiki.
	$wgConf->siteParamsCallback = function( $conf, $wiki ) {
		$variant = 'beta';
		return array( 'params' => array( 'variant' => $variant ) );
	};

	foreach ( $betaSettings as $key => $value ) {
		if ( substr( $key, 0, 1 ) == '-' ) {
			// Settings prefixed with - are completely overriden
			$wgConf->settings[substr( $key, 1 )] = $value;
		} elseif ( isset( $wgConf->settings[$key] ) ) {
			$wgConf->settings[$key] = array_merge( $wgConf->settings[$key], $value );
		} else {
			$wgConf->settings[$key] = $value;
		}
	}
}

/**
 * Return settings for wmflabs cluster. This is used by wmfLabsOverride().
 * Keys that start with a hyphen will completely override the regular settings
 * in InitializeSettings.php. Keys that don't start with a hyphen will have
 * their settings combined with the regular settings.
 */
function wmfLabsSettings() {
	global $wmfUdp2logDest;
	return array(
		'wgParserCacheType' => array(
			'default' => CACHE_MEMCACHED,
		),

		'wgSitename' => array(
			'deploymentwiki' => 'Deployment',
			'ee_prototypewiki' => 'Editor Engagement Prototype',
			'wikivoyage'    => 'Wikivoyage',
		),

		'wgServer' => array(
			'default'     => '//$lang.wikipedia.$variant.wmflabs.org',
			'wiktionary'	=> '//$lang.wiktionary.$variant.wmflabs.org',
			'wikipedia'     => '//$lang.wikipedia.$variant.wmflabs.org',
			'wikiversity'	=> '//$lang.wikiversity.$variant.wmflabs.org',
			'wikispecies'	=> '//$lang.wikispecies.$variant.wmflabs.org',
			'wikisource'	=> '//$lang.wikisource.$variant.wmflabs.org',
			'wikiquote'	=> '//$lang.wikiquote.$variant.wmflabs.org',
			'wikinews'	=> '//$lang.wikinews.$variant.wmflabs.org',
			'wikibooks'     => '//$lang.wikibooks.$variant.wmflabs.org',
			'wikivoyage'    => '//$lang.wikivoyage.$variant.wmflabs.org',

			'commonswiki'   => '//commons.wikimedia.$variant.wmflabs.org',
			'deploymentwiki'      => '//deployment.wikimedia.$variant.wmflabs.org',
			'ee_prototypewiki' => '//ee-prototype.wikipedia.$variant.wmflabs.org',
			'loginwiki'     => '//login.wikimedia.$variant.wmflabs.org',
			'metawiki'      => '//meta.wikimedia.$variant.wmflabs.org',
			'testwiki'      => '//test.wikimedia.$variant.wmflabs.org',
			'zerowiki'      => '//zero.wikimedia.$variant.wmflabs.org',
			'wikidatawiki'  => '//wikidata.$variant.wmflabs.org',
		),

		'wgCanonicalServer' => array(
			'default'     => 'http://$lang.wikipedia.$variant.wmflabs.org',
			'wikipedia'     => 'http://$lang.wikipedia.$variant.wmflabs.org',
			'wikibooks'     => 'http://$lang.wikibooks.$variant.wmflabs.org',
			'wikiquote'	=> 'http://$lang.wikiquote.$variant.wmflabs.org',
			'wikinews'	=> 'http://$lang.wikinews.$variant.wmflabs.org',
			'wikisource'	=> 'http://$lang.wikisource.$variant.wmflabs.org',
			'wikiversity'     => 'http://$lang.wikiversity.$variant.wmflabs.org',
			'wiktionary'     => 'http://$lang.wiktionary.$variant.wmflabs.org',
			'wikispecies'     => 'http://$lang.wikispecies.$variant.wmflabs.org',
			'wikivoyage'    => 'http://$lang.wikivoyage.$variant.wmflabs.org',

			'metawiki'      => 'http://meta.wikimedia.$variant.wmflabs.org',
			'ee_prototypewiki' => 'http://ee-prototype.wikipedia.$variant.wmflabs.org',
			'commonswiki'	=> 'http://commons.wikimedia.$variant.wmflabs.org',
			'deploymentwiki'      => 'http://deployment.wikimedia.$variant.wmflabs.org',
			'loginwiki'     => 'http://login.wikimedia.$variant.wmflabs.org',
			'testwiki'      => 'http://test.wikimedia.$variant.wmflabs.org',
			'wikidatawiki'  => 'http://wikidata.$variant.wmflabs.org',
		),

		'wmgUsabilityPrefSwitch' => array(
			'default' => ''
		),

		'wmgUseLiquidThreads' => array(
			'testwiki' => true,
		),

		'-wgUploadDirectory' => array(
			'default'      => '/data/project/upload7/$site/$lang',
			'private'      => '/data/project/upload7/private/$lang',
		),

		/* 'wmgUseOnlineStatusBar' => array( */
		/* 	'default' => false, */
		/* ), */

		'-wgUploadPath' => array(
			'default' => '//upload.$variant.wmflabs.org/$site/$lang',
			'private' => '/w/img_auth.php',
		//	'wikimania2005wiki' => '//upload..org/wikipedia/wikimania', // back compat
			'commonswiki' => '//upload.$variant.wmflabs.org/wikipedia/commons',
			'metawiki' => '//upload.$variant.wmflabs.org/wikipedia/meta',
			'testwiki' => '//upload.$variant.wmflabs.org/wikipedia/test',
		),

		'-wgThumbnailBuckets' => array(
			'default' => array( 256, 512, 1024, 2048, 4096 ),
		),

		'-wgThumbnailMinimumBucketDistance' => array(
			'default' => 32,
		),

		'-wmgMathPath' => array(
			'default' => '//upload.$variant.wmflabs.org/math',
		),

		'wmgNoticeProject' => array(
			'deploymentwiki' => 'meta',
		),

		'-wgDebugLogFile' => array(
			'default' => "udp://{$wmfUdp2logDest}/wfDebug",
		),

		'-wmgDefaultMonologHandler' => array(
			'default' => 'wgDebugLogFile',
		),

		'-wmgLogstashServers' => array(
			'default' => array(
				'10.68.16.134', // deployment-logstash1.eqiad.wmflabs
			),
		),

		// Additional log channels for beta cluster
		'wmgMonologChannels' => array(
			'default' => array(
				'CentralAuthVerbose' => 'debug',
				'dnsblacklist' => 'debug',
				'squid' => 'debug',
			),
		),

		//'-wgDebugLogGroups' => array(),
		'-wgRateLimitLog' => array(),
		'-wgJobLogFile' => array(),

		// Bug T62013, T58758
		'-wmgRC2UDPPrefix' => array(
			'default' => false,
		),

		'wmgUseWebFonts' => array(
			'mywiki' => true,
		),

		'wgLogo' => array(
			'commonswiki' => '$stdlogo',
			'dewiki' => '$stdlogo',
			'wikidatawiki' => '//upload.wikimedia.org/wikipedia/commons/thumb/4/43/Wikidata-logo-en-black.svg/135px-Wikidata-logo-en-black.svg.png',
		),

		'wgFavicon' => array(
			'dewiki' => '//upload.wikimedia.org/wikipedia/commons/1/14/Favicon-beta-wikipedia.png',
		),

		// Editor Engagement stuff
		'-wmfUseArticleCreationWorkflow' => array(
			'default' => false,
		),
		'wmgUseEcho' => array(
			'enwiki' => true,
			'en_rtlwiki' => true,
		),

		'-wmgUsePoolCounter' => array(
			'default' => false, # Bug T38891
		),
		'-wmgUseAPIRequestLog' => array(
			'default' => false,
		),
		'-wmgEnableCaptcha' => array(
			'default' => true,
		),
		'-wmgEchoCluster' => array(
			'default' => false,
		),
		'wmgEchoUseJobQueue' => array(
			'default' => true,
		),
		# FIXME: make that settings to be applied
		'-wgShowExceptionDetails' => array(
			'default' => true,
		),
		'-wgUseContributionTracking' => array(
			'default' => false,
		),
		'-wmgUseContributionReporting' => array(
			'default' => false,
		),

		# To help fight spam, makes rules maintained on deploymentwiki
		# to be available on all beta wikis.
		'-wmgAbuseFilterCentralDB' => array(
			'default' => 'deploymentwiki',
		),
		'-wmgUseGlobalAbuseFilters' => array(
			'default' => true,
		),

		# Bug T39852
		'wmgUseWikimediaShopLink' => array(
			'default'    => false,
			'enwiki'     => true,
			'simplewiki' => true,
		),

		//enable TimedMediaHandler and MwEmbedSupport for testing on commons and enwiki
		'wmgUseMwEmbedSupport' => array(
			'commonswiki'	=> true,
			'enwiki'	=> true,
		),
		// NOTE: TMH *requires* MwEmbedSupport to function
		'wmgUseTimedMediaHandler' => array(
			'commonswiki'	=> true,
			'enwiki'	=> true,
		),
		'wmgMobileUrlTemplate' => array(
			'default' => '%h0.m.%h1.%h2.%h3.%h4',
			'commonswiki' => '',
			'mediawikiwiki' => '',//'m.%h1.%h2',
			'wikidatawiki' => 'm.%h0.%h1.%h2.%h3', // T87440
		),

		'wmgMFPhotoUploadEndpoint' => array(
			'default' => '//commons.wikimedia.$variant.wmflabs.org/w/api.php',
		),
		'wmgMFUseCentralAuthToken' => array(
			'default' => true,
		),
		'wmgMFEnableBetaDiff' => array(
			'default' => true,
		),
		'wmgMFSpecialCaseMainPage' => array(
			'default' => true,
			'enwiki' => false,
		),
		'wmgWikiGrokUIEnable' => array(
			'default' => false,
			'enwiki' => true, // prototype version is for en.wiki only
		),
		'wmgWikiGrokUIEnableOnAllDevices' => array(
			'default' => false,
			'enwiki' => true,
		),
		'wmgWikiGrokUIEnableInSidebar' => array(
			'default' => false,
			'enwiki' => true,
		),
		'wmgMFWikiDataEndpoint' => array(
			'default' => 'http://wikidata.beta.wmflabs.org/w/api.php',
		),
		'wmgWikiBasePropertyConfig' => array(
			'default' => array(
				'instanceOf' => 'P694',
				'bannerImage' => 'P964',
			),
		),
		'wmgMFInfoboxConfig' => array(
			'default' => array(
				// human
				44076 => array(
					'rows' => array(
							// Born
							array( 'id' => 'P476' ),
							// Birthplace
							array( 'id' => 'P965' ),
							// Place of death
							array( 'id' => 'P994' ),
							// Country of citizenship
							array( 'id' => 'P27' ),
							// Alma mater
							array( 'id' => 'P998' ),
					),
				),
				'default' => array(
					'rows' => array(
					),
				),
			),
		),

		'wmgWikiGrokDebug' => array(
			'default' => true,
		),

		'wmgMFIsBrowseEnabled' => array(
			'default' => true,
		),
		'wmgMFBrowseTags' => array(
			'default' => array(
				'San Francisco landmarks' => array(
					'Alcatraz Island',
					'Golden Gate Bridge',
					'Presidio of San Francisco',
					'Lombard Street (San Francisco)',
					'Golden Gate Park',
					'City Lights Bookstore',
					'Coit Tower',
					'San Francisco cable car system',
					'Palace of Fine Arts',
					'Alamo Square, San Francisco',
					'Fort Point, San Francisco',
					'Grace Cathedral, San Francisco',
					'San Francisco Ferry Building',
					'AT&T Park',
					'Yerba Buena Gardens',
					'Castro Theatre',
					'Transamerica Pyramid',
					'San Francisco Museum of Modern Art',
					'Candlestick Park',
					'San Francisco Zoo',
					'Fairmont San Francisco',
					'Fort Mason',
					'Ghirardelli Square',
					'Crissy Field',
					'San Francisco Botanical Garden',
					'Barbary Coast Trail',
					'Duboce Park',
					'Flatiron Building (San Francisco)',
					'Lands End (San Francisco)',
					'San Francisco Mint',
					'Crissy Field',
					'San Francisco Naval Shipyard',
					'Dolores Park',
					'Jack Kerouac Alley',
					'San Francisco Cable Car Museum',
					'Balboa Park, San Francisco',
					'Panhandle (San Francisco)',
					'San Francisco City Hall',
					'Victoria Theatre, San Francisco',
					'Union Square, San Francisco',
					'San Francisco Armory',
					'Mitchell Brothers O\'Farrell Theatre',
					'Seal Rocks (San Francisco, California)',
					'San Francisco–Oakland Bay Bridge',
					'Asian Art Museum of San Francisco',
					'San Francisco Public Library',
					'Levi\'s Plaza',
					'Louise M. Davies Symphony Hall',
					'Moscone Center',
					'San Francisco Federal Building',
				),
				'western Europe' => array(
					'Germany',
					'France',
					'United Kingdom',
					'Italy',
					'Spain',
					'Netherlands',
					'Belgium',
					'Greece',
					'Portugal',
					'Sweden',
					'Austria',
					'Switzerland',
					'Denmark',
					'Finland',
					'Norway',
					'Ireland',
					'Luxembourg',
					'Iceland',
					'Vatican City',
					'Monaco',
					'Principality of Sealand',
					'Western Europe',
					'Northwestern Europe',
				),
				'American politicians of the 20th century' => array(
					'Barack Obama',
					'George W. Bush',
					'Franklin D. Roosevelt',
					'Ronald Reagan',
					'George Washington',
					'Bill Clinton',
					'Theodore Roosevelt',
					'John F. Kennedy',
					'Woodrow Wilson',
					'Richard Nixon',
					'Jimmy Carter',
					'Martin Luther King Jr.',
					'John Kerry',
					'Dwight D. Eisenhower',
					'Lyndon B. Johnson',
					'Harry S. Truman',
					'George H. W. Bush',
					'John McCain',
					'Hillary Rodham Clinton',
					'Al Gore',
					'Nancy Pelosi',
					'Harry Reid',
					'Gerald Ford',
					'Calvin Coolidge',
					'John Boehner',
					'Henry Kissinger',
					'Eric Cantor',
					'Mitch McConnell',
					'Herbert Hoover',
					'Arnold Schwarzenegger',
					'Mitt Romney',
					'Condoleezza Rice',
					'Warren G. Harding',
					'Joe Biden',
					'William McKinley',
					'Colin Powell',
					'Michael Bloomberg',
					'Dick Cheney',
					'William Randolph Hearst',
					'Donald Rumsfeld',
					'Ted Kennedy',
					'Robert F. Kennedy',
					'Robert McNamara',
					'Cordell Hull',
					'John Foster Dulles',
					'James Buchanan',
					'Cordell Hull',
					'Jerry Brown',
					'James A. Garfield',
					'Ralph Nader',
					'Robert Gates',
					'George Marshall',
					'Martin Van Buren',
					'John Kenneth Galbraith',
					'Ralph Nader',
					'Robert Gates',
					'George Marshall',
					'Rudy Giuliani',
					'Madeleine Albright',
					'Ben Bernanke',
					'Barry Goldwater',
					'Nelson Rockefeller',
					'Ron Paul',
					'Jesse Jackson',
					'James Baker',
					'Fiorello H. La Guardia',
					'Newt Gingrich',
					'Sarah Palin',
					'William Cohen',
					'Joseph McCarthy',
					'Ross Perot',
					'Malcolm X',
					'Chuck Schumer',
					'Zbigniew Brzezinski',
					'Dianne Feinstein',
				),
				'modern painters' => array(
					'Pablo Picasso',
					'Willem de Kooning',
					'Marc Chagall',
					'Georgia O\'Keeffe',
					'Salvador Dalí',
					'Marsden Hartley',
					'Henri Matisse',
					'Wassily Kandinsky',
					'Lucian Freud',
					'Max Ernst',
					'Amedeo Modigliani',
					'Piet Mondrian',
					'Joan Miró',
					'Kazimir Malevich',
					'André Derain',
					'Albert Gleizes',
					'Frida Kahlo',
					'Paul Nash (artist)',
					'René Magritte',
					'Emily Carr',
					'John Nash (artist)',
					'Jörg Immendorff',
					'Fernand Léger',
					'Giacomo Balla',
					'Rufino Tamayo',
					'Jean Dubuffet',
					'Jean Metzinger',
					'Robert Rauschenberg',
					'Francis Bacon (artist)',
					'Ernst Ludwig Kirchner',
					'Pierre Bonnard',
					'Kuzma Petrov-Vodkin',
					'Juan O\'Gorman',
					'Fernando Botero',
					'Franz Marc',
					'James Ensor',
					'Stanisław Ignacy Witkiewicz',
					'Edward Hopper',
					'George Grosz',
					'Raoul Dufy',
					'George Bellows',
					'Thomas Hart Benton (painter)',
					'John Dos Passos',
					'Otto Dix',
					'Victor Vasarely',
					'Niki de Saint Phalle',
					'Franz Kline',
					'Julio González (sculptor)',
					'Amadeo de Souza Cardoso',
					'Maria Helena Vieira da Silva',
					'Barnett Newman',
					'Hans Hofmann',
					'Per Kirkeby',
					'Cy Twombly',
					'André Masson',
					'Francis Picabia',
					'Jean-Paul Riopelle',
					'Benito Quinquela Martín',
					'Theo van Doesburg',
				),
				'object-oriented programming languages' => array(
					'Java (programming language)',
					'C++',
					'PHP',
					'Python (programming language)',
					'Fortran',
					'Perl',
					'Ruby (programming language)',
					'COBOL',
					'Visual Basic',
					'Dart (programming language)',
					'Scala (programming language)',
					'Object Pascal',
					'Smalltalk',
					'Visual FoxPro',
					'Common Lisp',
					'SuperCollider',
					'Lua (programming language)',
					'OCaml',
					'FreeBASIC',
					'Gambas',
					'Objective-C',
					'Vala (programming language)',
					'Eiffel (programming language)',
					'Windows PowerShell',
					'Seed7',
					'D (programming language)',
					'Groovy (programming language)',
					'Processing (programming language)',
					'J (programming language)',
					'Self (programming language)',
					'Julia (programming language)',
					'Racket (programming language)',
					'Swift (programming language)',
					'Oxygene (programming language)',
					'E (programming language)',
					'Kotlin (programming language)',
					'Boo (programming language)',
					'Fantom (programming language)',
					'Squirrel (programming language)',
					'Cobra (programming language)',
					'Oberon-2 (programming language)',
					'Blitz BASIC',
					'Flavors (programming language)',
					'Lasso (programming language)',
					'BETA (programming language)',
					'Pike (programming language)',
					'Obix (programming language)',
					'JADE (programming language)',
					'Ateji PX',
					'Nemerle',
					'Emerald (programming language)',
					'Visual Prolog',
					'Chapel (programming language)',
					'ProvideX',
					'Allegro Common Lisp',
					'AgentSheets',
					'Bistro (programming language)',
					'Nu (programming language)',
					'Active Oberon',
					'ActiveVFP',
					'Actor (programming language)',
					'Adenine (programming language)',
					'Aldor',
					'Axum (programming language)',
					'Basic For Qt',
					'Cecil (programming language)',
					'Cel (programming language)',
					'Ciao (programming language)',
					'Claire (programming language)',
					'Clascal',
					'Class implementation file',
					'Compact Application Solution Language',
					'Converge (programming language)',
					'Cool (programming language)',
					'CorbaScript',
					'E Sharp (programming language)',
					'Easytrieve',
					'EusLisp Robot Programming Language',
					'Extensible ML',
					'F-Script (programming language)',
					'Falcon (programming language)',
					'Fancy (programming language)',
					'FPr (programming language)',
					'Free Pascal',
					'Game Oriented Assembly Lisp',
					'Gello Expression Language',
					'Genie (programming language)',
					'Goo (programming language)',
					'Gosu (programming language)',
					'Guido van Robot',
					'Ioke (programming language)',
					'Jasmin (software)',
					'Joule (programming language)',
					'JRuby',
					'Judoscript',
					'Jython',
					'Karel++',
					'Keykit',
					'Lagoona (programming language)',
					'Lightweight Java',
					'List of object-oriented programming languages',
					'Little b (programming language)',
					'Logtalk',
					'MacRuby',
					'MetaQuotes Language MQL4/MQL5',
					'MIMIC',
					'Mirah (programming language)',
					'Mixin',
					'Modula-3',
					'Monkey X',
					'Morfik FX',
					'Neko (programming language)',
					'NetRexx',
					'Newspeak (programming language)',
					'Noop',
					'NS Basic',
					'O:XML',
					'Oak (programming language)',
					'Oaklisp',
					'Object Lisp',
					'Object Oberon',
					'Object REXX',
					'Object-Oriented Turing',
					'Objective-J',
					'ObjectLOGO',
					'ObjVlisp',
					'ObjVProlog',
					'Oblog',
					'OMeta',
					'OpenEdge Advanced Business Language',
					'OptimJ',
					'OTcl',
					'Pauscal',
					'Pnuts',
					'Potion (programming language)',
					'Prograph',
					'Prolog++',
					'Python for S60',
					'QUILL',
					'ROOP (programming language)',
					'RubyMotion',
					'RUR-PLE',
					'S2 (programming language)',
					'SCOOP (software)',
					'Scriptol',
					'Simorgh Programming Language',
					'SMALL',
					'Smalltalk YX',
					'Span (programming language)',
					'Telescript (programming language)',
					'Tibbo BASIC',
					'Timber (programming language)',
					'TOM (object-oriented programming language)',
					'Turbo Pascal',
					'Turbo51',
					'Ubercode',
					'Umple',
					'Urbiscript',
					'Visual Basic',
				),
				'American TV dramas' => array(
					'Lost (TV series)',
					'The West Wing',
					'ER (TV series)',
					'Star Trek: The Next Generation',
					'CSI: Crime Scene Investigation',
					'NCIS (TV series)',
					'The X-Files',
					'Breaking Bad',
					'Star Trek: The Original Series',
					'Grey\'s Anatomy',
					'House (TV series)',
					'24 (TV series)',
					'Star Trek: Deep Space Nine',
					'Buffy the Vampire Slayer',
					'CSI: Miami',
					'Miami Vice',
					'Prison Break',
					'Law & Order',
					'Bones (TV series)',
					'True Blood',
					'Dawson\'s Creek',
					'Law & Order: Special Victims Unit',
					'CSI: NY',
					'Hannibal (TV series)',
					'Star Trek: Voyager',
					'Battlestar Galactica (2004 TV series)',
					'Twin Peaks',
					'Beverly Hills, 90210',
					'Once Upon a Time (TV series)',
					'Criminal Minds',
					'Touched by an Angel',
					'The Wire',
					'Fringe (TV series)',
					'The Mentalist',
					'All My Children',
					'The Americans (2013 TV series)',
					'NYPD Blue',
					'Baywatch',
					'Chicago Hope',
					'Big Love',
					'Chicago Fire (TV series)',
					'Quantum Leap',
					'The Walking Dead (TV series)',
					'Little House on the Prairie (TV series)',
					'JAG (TV series)',
					'The A-Team',
					'Gossip Girl',
					'Mission: Impossible',
					'Kojak',
					'One Life to Live',
					'Homicide: Life on the Street',
					'As the World Turns',
					'Hill Street Blues',
					'Without a Trace',
					'Medium (TV series)',
					'NCIS: Los Angeles',
					'Boardwalk Empire',
					'Burn Notice',
					'L.A. Law',
					'Boston Public',
					'Grimm (TV series)',
					'Hawaii Five-O',
					'Revenge (TV series)',
					'Ghost Whisperer',
					'Veronica Mars',
					'Walker, Texas Ranger',
					'Star Trek: Enterprise',
					'The Fugitive (TV series)',
					'Person of Interest (TV series)',
					'The Practice',
					'Kung Fu (TV series)',
					'The Waltons',
					'Elementary (TV series)',
					'Crossing Jordan',
					'90210 (TV series)',
					'Matlock (TV series)',
					'Nash Bridges',
					'Nashville (2012 TV series)',
					'Nip/Tuck',
					'Angel (1999 TV series)',
					'Ironside (1967 TV series)',
					'Cagney & Lacey',
					'Scandal (TV series)',
					'Starsky & Hutch',
					'Dirty Sexy Money',
					'Joan of Arcadia',
					'Everwood',
					'The Blacklist (TV series)',
					'Six Feet Under (TV series)',
					'Fantasy Island',
					'Adam-12',
					'Suits (TV series)',
					'Police Woman (TV series)',
					'Revolution (TV series)',
					'In the Heat of the Night (TV series)',
					'The Shield',
					'Eli Stone',
					'Private Practice (TV series)',
					'The L Word',
					'Jake and the Fatman',
					'21 Jump Street',
					'7th Heaven (TV series)',
					'Leverage (TV series)',
					'Cop Rock',
					'Zorro (1957 TV series)',
					'Friday Night Lights (TV series)',
					'Highway to Heaven',
					'Perry Mason (TV series)',
					'Homeland (TV series)',
					'NCIS: New Orleans',
					'Third Watch',
					'Empire (2015 TV series)',
					'Under the Dome (TV series)',
					'Party of Five',
					'Arrow (TV series)',
					'Scorpion (TV series)',
					'FlashForward',
					'New York Undercover',
					'Madam Secretary (TV series)',
					'White Collar (TV series)',
					'Life Goes On (TV series)',
					'24: Live Another Day',
					'Promised Land (TV series)',
					'Terra Nova (TV series)',
					'Army Wives',
					'The Closer',
					'Peter Gunn',
					'Rizzoli & Isles',
					'The Legend of Korra',
					'Gotham (TV series)',
					'Wiseguy',
					'American Playhouse',
					'Hell on Wheels (TV series)',
					'Justified (TV series)',
					'Nikita (TV series)',
					'Make It or Break It',
				),
			),
		),

		'wmgGeoDataDebug' => array(
			'default' => true,
		),

		'wmgULSPosition' => array(
			# Beta-specific
			'ee-prototype' => 'personal',
			'deploymentwiki' => 'personal',
		),

		// (Bug T41653) The plan is to enable it for testing on labs first, so add
		// the config hook to be able to do that.
		'wmgUseCodeEditorForCore' => array(
			'default' => true,
		),

		'wmgUseCommonsMetadata' => array(
			'default' => true,
		),
		'wmgCommonsMetadataForceRecalculate' => array(
			'default' => true,
		),

		'wmgUseGWToolset' => array(
			'default' => false,
			'commonswiki' => true,
		),

		// Don't use an http/https proxy
		'-wgCopyUploadProxy' => array(
			'default' => false,
		),

		// ----------- BetaFeatures start ----------
		'wmgUseBetaFeatures' => array(
			'default' => true,
		),

		// Enable all Beta Features in Beta Labs, even if not in production whitelist
		'wmgBetaFeaturesWhitelist' => array(
			'default' => false,
		),

		'wmgUseMultimediaViewer' => array(
			'default' => true,
		),

		'wmgNetworkPerformanceSamplingFactor' => array(
			'default' => 1,
		),

		'wmgUseImageMetrics' => array(
			'default' => true,
		),

		'wmgImageMetricsSamplingFactor' => array(
			'default' => 1,
		),

		'wmgImageMetricsCorsSamplingFactor' => array(
			'default' => 1,
		),

		'wmgRestbaseServer' => array(
			'default' => "http://10.68.17.227:7231" // deployment-restbase01.eqiad.wmflabs
		),

		'wmgUseRestbaseVRS' => array(
			'default' => true,
		),

		'wmgUseVectorBeta' => array(
			'default' => true,
		),
		'wmgVectorBetaFormRefresh' => array(
			'default' => true,
		),

		'wmgVectorBetaPersonalBar' => array(
			'default' => true,
		),

		'wmgVectorBetaWinter' => array(
			'default' => true,
		),

		'wmgVisualEditorExperimental' => array(
			'default' => true,
		),

		'wmgVisualEditorEnableTocWidget' => array(
			'default' => false,
		),
		'wmgVisualEditorAccessRESTbaseDirectly' => array(
			'default' => true,
		),
		// ------------ BetaFeatures end -----------

		'wmgUseRSSExtension' => array(
			'dewiki' => true,
		),

		'wmgRSSUrlWhitelist' => array(
			'dewiki' => array( 'http://de.planet.wikimedia.org/atom.xml' ),
		),

		'wmgUseCampaigns' => array(
			'default' => true,
		),

		'wmgUseEventLogging' => array(
			'default' => true,
		),

		'wmgContentTranslationCluster' => array(
			'default' => false,
		),

		'wmgContentTranslationCampaigns' => array(
			'default' => array( 'newarticle' ),
		),

		'wmgUseNavigationTiming' => array(
			'default' => true,
		),

		'wgSecureLogin' => array(
			// Setting false throughout Labs for now due to untrusted SSL certificate
			// Bug T50501
			'default' => false,
			'loginwiki' => false,
		),

		'wgSearchSuggestCacheExpiry' => array(
			'default' => 300,
		),

		'wmgUseCirrus' => array(
			'default' => true,
			'commonswiki' => true,
			'dewiki' => true,
			'enwiki' => true,
			'eswiki' => true,
			'frwiki' => true,
			'jawiki' => true,
			'nlwiki' => true,
			'plwiki' => true,
			'ruwiki' => true,
			'svwiki' => true,
			'zhwiki' => true,
		),

		'wmgUseFlow' => array(
			'enwiki' => true,
			'en_rtlwiki' => true,
		),
		# Extension:Flow's browsertests use Talk:Flow_QA.
		'wmgFlowOccupyPages' => array(
			'enwiki' => array( 'Talk:Flow QA', 'Talk:Flow' ),
			'en_rtlwiki' => array( 'Talk:Flow' ),
		),
		# No separate Flow DB or cluster (yet) for labs.
		'-wmgFlowDefaultWikiDb' => array(
			'default' => false,
		),
		'-wmgFlowCluster' => array(
			'default' => false,
		),
		'wmgUseGather' => array(
			'default' => true,
		),
		'wmgUseGuidedTour' => array(
			'wikidatawiki' => true,
		),
		// Enable anonymous editor acquisition experiment across labs
		'wmgGettingStartedRunTest' => array(
			'default' => true,
		),
		'+wmgExtraLanguageNames' => array(
			'default' => array(),
			'en_rtlwiki' => array( 'en-rtl' => 'English (rtl)' ),
		),
		'wmgUseContentTranslation' => array(
			'default' => false,
			'wiki' => true,
		),

		'wmgUsePetition' => array(
			'default' => false,
			'metawiki' => true,
		),

		'wmgUseSentry' => array(
			'default' => true,
		),
		'wmgSentryDsn' => array(
			'default' => '//c357be0613e24340a96aeaa28dde08ad@sentry-beta.wmflabs.org/4',
		),

		// Thumbnail prerendering at upload time
		'wgUploadThumbnailRenderMap' => array(
			'default' => array( 320, 640, 800, 1024, 1280, 1920, 2560, 2880 ),
		),

		'wgUploadThumbnailRenderMethod' => array(
			'default' => 'http',
		),

		'wgUploadThumbnailRenderHttpCustomHost' => array(
			'default' => 'upload.beta.wmflabs.org',
		),

		'wgUploadThumbnailRenderHttpCustomDomain' => array(
			'default' => 'deployment-cache-upload02.eqiad.wmflabs',
		),

		'wmgUseApiFeatureUsage' => array(
			'default' => true,
		),

		'wmgUseBounceHandler' => array(
			'default' => true,
		),

		'-wmgScorePath' => array(
			'default' => "//upload.beta.wmflabs.org/score",
		),

		'wgRateLimitsExcludedIPs' => array(
			'default' => array( '198.73.209.0/24' ), // T87841 Office IP
		),

		'wmgUseCapiunto' => array(
			'default' => true,
		),

		'wmgUsePopups' => array(
			'default' => true,
		),

		'wmgUseJosa' => array(
			'default' => false,
			'kowiki' => true, // T15712
		),
		'wmgUseGraph' => array(
			'default' => true,
		),

	);
} # wmflLabsSettings()
