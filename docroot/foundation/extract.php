<?php
header("Content-Type: text/html; charset=utf-8");
$lang = "meta";
define( "MEDIAWIKI", true );
#include_once("CommonSettings.php");
include_once("/home/wikipedia/common/php-new/CommonSettings.php");
include_Once("Setup.php");
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head><title>Wikimedia Foundation</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="robots" content="index,follow">
<link rel="stylesheet" href="/style/foundation.css">
<link rel="shortcut icon" href="/favicon.ico">
</head>

<body class="striped">
<div id="menu">
<a href="http://www.wikimediafoundation.org/"><img 
src="http://meta.wikipedia.org/upload/f/f7/Ncwikimediafound.png" border="0"></A><br>
  <ul>
    <li><a href="http://wikimediafoundation.org">Home</a></li>
    <li><a href="http://www.wikipedia.org/wiki/Wikimedia">About 
      Wikimedia</a> </li>
    <li><a href="http://meta.wikipedia.org/wiki/Wikimedia_News">News</a></li>

    <li><a href="http://wikimedia.org">Projects</a></li>
    <li><a href="http://mail.wikipedia.org">Mailing lists</a></li>
    <li><a href="http://mediawiki.org">MediaWiki software</a></li>
    <li><a href="http://wikimediafoundation.org/fundraising">Donate 
      Now</a></li>
    <li><a href="http://www.wikimediafoundation.org/bylaws.pdf">By-laws<br>(PDF format)</a></li>

  </ul>
</div>

<div id="wikipage">
<h1>Wikimedia Foundation</h1>

<?

$map = array(
	"index" => "Wikimedia Foundation home page",
	"fundraising" => "Wikimedia Fundraising page",
	"GNU_FDL" => "Wikimedia GNU FDL",

);

$title = $_REQUEST["title"];
#str_replace( "/", "_", $title );

if(isset($map[$title])) {
	$wgTitle = Title::newFromText($map[$_REQUEST["title"]]);
	if($wgTitle) {
		$wgArticle = new Article( $wgTitle );
		echo $wgArticle->getContent(false);
	}
	#readfile($title);
} else {
	echo "no page";
}

?>
</div>

<div id="stripe2">&nbsp;</div>

</body>
</html>
