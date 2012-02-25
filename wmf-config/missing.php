<?php

/**
 * Missing wiki redirect / 404 page
 * 
 * This file redirects non-existing languages of Wikipedia, Wiktionary, Wikiquote,
 * Wikibooks and Wikinews to the Wikimedia Incubator. Non-existing languages of
 * Wikisource and Wikiversity show static 404 page.
 * 
 * There is a specific extension on Incubator used to make nice "welcome pages"
 * (adapted to each language, project and translatable).
 * 
 * These redirects allow the usage of interwiki links from existing language
 * subdomains to Incubator, e.g. [[xyz:Page]] on en.wikipedia links to
 * http://incubator.wikimedia.org/wiki/Wp/xyz/Page
 * 
 * @copyright Copyright © 2011, Danny B., SPQRobin
 * 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */


/**
 * Obtaining the full self URL
 * @return string Actual URL except for fragment part
 */
function getSelfUrl() {

	// faking https on secure.wikimedia.org - thanks Ryan for hint
	if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
		$_SERVER['HTTPS'] = 'on';
	}

	$protocol = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? 'https' : 'http';
	$port = ( $_SERVER['SERVER_PORT'] == '80' ) ? '' : ( ':' . $_SERVER['SERVER_PORT'] );
	return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
}


$projects = array(
	'wikibooks'   => 'b',
	'wikinews'    => 'n',
	'wikipedia'   => 'p',
	'wikiquote'   => 'q',
	'wikisource'  => 's', // forward compatiblity, unused ATM
	'wikiversity' => 'v', // forward compatiblity, unused ATM
	'wiktionary'  => 't',
);



$url = parse_url( getSelfUrl() );

// FIXME: the code below assumes getSelfUrl() contains /wiki/ , it should verify that
if( $url['host'] == 'secure.wikimedia.org' ) {

	# https://secure.wikimedia.org/$project/$language/wiki/$page
	$tmp = explode( '/', ltrim( $url['path'], '/' ) );
	$project = $tmp[0];
	$language = $tmp[1];
	$base = 'secure.wikimedia.org/wikipedia/incubator/wiki/';
	if( isset( $_SERVER['PATH_INFO'] ) ) {
		$page = implode( array_slice( $tmp, 3 ) ); # /wiki/Page (possibly /wiki/Page?foo=bar)
	} elseif( isset( $_GET['title'] ) && $_GET['title'] ) {
		$page = $_GET['title']; # index.php?title=Page
	} else {
		$page = ''; # no known title, fallback to main page
	}

} else {

	# http(s)://$language.$project.org/wiki/$page
	$tmp = explode( '.', $url['host'] );
	$project = $tmp[1];
	$language = $tmp[0];
	$base = 'incubator.wikimedia.org/wiki/';
	if( isset( $_SERVER['PATH_INFO'] ) ) {
		$page = preg_replace( '/^\/wiki\//', '', $url['path'] ); # /wiki/Page (possibly /wiki/Page?foo=bar)
	} elseif( isset( $_GET['title'] ) && $_GET['title'] ) {
		$page = $_GET['title']; # index.php?title=Page
	} else {
		$page = ''; # no known title, fallback to main page
	}

}

$project = strtolower( $project );
$projectcode = $projects[$project];
$project = ucfirst( $project ); // for 404 pages message

$location = $url['scheme'] . '://' . $base . 'W' . $projectcode . '/' . urlencode( $language );
# Go to the page if specified (look out for slashes), otherwise go to
# the main page Wx/xyz?goto=mainpage (WikimediaIncubator extension takes care of that)
$location .= $page && $page !== '/' ? '/' . $page :
	'?goto=mainpage' . ( isset( $_GET['uselang'] ) ? '&uselang=' . urlencode( $_GET['uselang'] ) : '' );

$redir = false;

switch( $projectcode ) {

	# Wikisource
	case 's':
		$logo = '//upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Wikisource-logo.svg/280px-Wikisource-logo.svg.png';
		$home = '//wikisource.org';
		$name = 'Multilingual Wikisource';
		break;

	# Wikiversity
	case 'v':
		$logo = '//upload.wikimedia.org/wikipedia/commons/thumb/9/91/Wikiversity-logo.svg/300px-Wikiversity-logo.svg.png';
		$home = '//beta.wikiversity.org';
		$name = 'Beta Wikiversity';
		break;

	# Wikipedia, Wiktionary, Wikiquote, Wikibooks and Wikinews
	default:
		$redir = true;

}

# If not Wikisource/Wikiversity and the URL seems valid, redirect to Incubator
if( $redir && parse_url( $location ) !== false ) {

	header( 'Location: ' . $location );
	exit();

}

header( 'HTTP/1.x 404 Not Found' );
header( 'Content-Type: text/html; charset=utf-8' );

$escLanguage = htmlspecialchars( $language );
$escProject = htmlspecialchars( $project );

?><!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<title><?php echo "$escLanguage&nbsp;$escProject"; ?> does not exist</title>
	<meta charset="UTF-8" />
	<link rel="shortcut icon" href="<?php echo $home; ?>/favicon.ico" />
	<style type="text/css">
/* <![CDATA[ */
* {
	font-family: 'Gill Sans', 'Gill Sans MT', sans-serif;
	margin: 0;
	padding: 0;
}

body {
  background: #fff url('//upload.wikimedia.org/wikipedia/commons/9/96/Errorbg.png') repeat-x;
  color: #333;
  margin: 0;
  padding: 0;
}

#page {
  background: url('<?php echo $logo; ?>') center left no-repeat;
  height: 300px;
  left: 50%;
  margin: -150px 0 0 -360px;
  overflow: visible;
  position: absolute;
  top: 50%;
  width: 720px;
}

#message {
	background: url('//upload.wikimedia.org/wikipedia/commons/9/97/Errorline.png') center left no-repeat;
	margin-left: 300px;
	padding-left: 15px;
}

h1, h2, p {
	margin-bottom: 1em;
}

a:link, a:visited {
	color: #005b90;
}

a:hover, a:active {
	color: #900;
}

/* ]]> */
	</style>
</head>
<body>
	<div id="page">
		<div id="message">

			<h1>This wiki does not exist</h1>

			<h2>Welcome to <?php echo $escProject; ?></h2>

			<p>Unfortunately, <?php echo $escProject; ?> in "<?php echo $escLanguage; ?>" does not exist on its own domain yet, or it has been closed.</p>

			<p>You may like to visit <a href="<?php echo $home; ?>"><?php echo $name; ?></a> to start or improve <em><?php echo "$escLanguage&nbsp;$escProject"; ?></em> there.</p>

			<p>If you would like to request that this wiki be created, see the <a href="//meta.wikimedia.org/wiki/Requests_for_new_languages">requests for new languages</a> page on Meta-Wiki.</p>

			<p style="font-size: smaller;">A&nbsp;project of the <a href="//wikimediafoundation.org" title="Wikimedia Foundation">Wikimedia Foundation</a></p>

		</div>
	</div>
</body>
</html>
