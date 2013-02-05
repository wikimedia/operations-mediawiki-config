<?php

if( PHP_SAPI !== 'cli' ) die(":<");

$subtitles = array(
	'ar' => 'العربية',
	'ca' => 'Català',
	'cs' => 'Česky',
	'de' => 'Deutsch',
	'el' => 'Ελληνικά',
	'en' => 'English',
	'eo' => 'Esperanto',
	'es' => 'Español',
	'fr' => 'Français',
	'he' => 'עברית',
	'hu' => 'Magyar',
	'id' => 'Bahasa Indonesia',
	'it' => 'Italiano',
	'ja' => '日本語',
	'nl' => 'Nederlands',
	'pt' => 'Português',
	'pl' => 'Polski',
	'ro' => 'Română',
	'ru' => 'Русский',
	'sr' => 'Српски',
	'th' => 'ไทย',
	'tl' => 'Tagalog',
	'zh' => '中文',
	'zh-yue' => '粵語',
);

file_put_contents("index.html", getPage('', $subtitles));
foreach( $subtitles as $code => $name ) {
	file_put_contents("subtitled-$code.html", getPage($code, $subtitles));
}

function getPage( $lang, $subtitles ) {
	$key = ($lang) ? "-$lang" : "";
	
	$selected = ($lang) ? ' selected="selected"' : '';
	$list = "<option value=\"\"$selected>(none)</option>";
	foreach( $subtitles as $code => $name ) {
		if( $lang == $code ) {
			$item = "<b>$name</b>";
		} else {
			$item = "<a href=\"subtitled-$code.html\">$name</a>";
		}
		$selected = ($code == $lang) ? ' selected="selected"' : '';
		$list .= "<option value=\"$code\"$selected>$code - $name</option>";
	}
	/*
	foreach( $subtitles as $code => $name ) {
		if( $lang == $code ) {
			$item = "<b>$name</b>";
		} else {
			$item = "<a href=\"subtitled-$code.html\">$name</a>";
		}
		$list .= "$item &nbsp; ";
	}
	*/
	
	$text = <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="shortcut icon" href="/favicon.ico" />
		<title>Wikimedia Foundation - Watch Jimmy's video...</title>

<style type="text/css">
body {
	font-family: sans-serif;
	font-size: 90%;
	padding: 24px;
	padding-top: 0;
	background: #f8f8f8;
}
.wrapper {
	width: 480px;
	padding: 16px;
	padding-top: 4px;
	margin-left: auto;
	margin-right: auto;
	border: solid 1px #ddd;
	background: white;
}
a img {
	border: 0;
}
table {
	border: solid 1px #888;
	width: 400px;
}
th {
	background: #efefef;
}
td {
	background: white;
}
th, td {
	padding: 8px;
}
td {
	padding-left: 0px; /* for alignment extra */
}
td {
	text-align: right;
}
h1 {
	margin-top: 1em;
	font-size: 1.75em;
}
h2 {
	font-size: 1.25em;
	color: #444;
}
hr {
	width: 312px;
	text-align: left;
	margin-left: 42px;
	margin-top: 1em;
}
.video {
	width: 400px;
	padding-top: 1em;
	margin-left: auto;
	margin-right: auto;
}

.subs {
	text-align: right;
	padding: 4px;
/*
	width: 282px;
	margin: 24px 42px;
	padding: 16px 16px;
	background: #eef0f2;
	border: solid 1px #dde0e2;
*/
}

</style>

<!-- Subtitle selection -->
<script type="text/javascript">
function selectLang(lang) {
	//console.log("lang: " + lang);
	if (lang) {
		var fileName='subtitled-'+lang+'.html';
	} else {
		var fileName='index.html';
	}
	document.location = (document.location+"").replace(/[^\/]*$/, fileName);
}
</script>

<!-- Ogg player setup... -->
<script type="text/javascript" src="OggPlayer.js?5z"></script>
<script type="text/javascript">
wgOggPlayer.msg = {"ogg-play": "Play", "ogg-pause": "Pause", "ogg-stop": "Stop", "ogg-no-player": "Sorry, your system does not appear to have any supported player software. Please \x3ca href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\"\x3edownload a player\x3c/a\x3e.", "ogg-player-videoElement": "\x3cvideo\x3e element", "ogg-player-oggPlugin": "Ogg plugin", "ogg-player-gnash": "Gnash", "ogg-player-flash": "Flash", "ogg-player-cortado": "Cortado (Java)", "ogg-player-vlc-mozilla": "VLC", "ogg-player-vlc-activex": "VLC (ActiveX)", "ogg-player-quicktime-mozilla": "QuickTime", "ogg-player-quicktime-activex": "QuickTime (ActiveX)", "ogg-player-thumbnail": "Still image only", "ogg-player-selected": "(selected)", "ogg-use-player": "Use player: ", "ogg-more": "Player options...", "ogg-download": "Download file", "ogg-desc-link": "About this file", "ogg-dismiss": "Close", "ogg-player-soundthumb": "No player", "ogg-no-xiphqt": "You do not appear to have the XiphQT component for QuickTime. QuickTime cannot play Ogg files without this component. Please \x3ca href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\"\x3edownload XiphQT\x3c/a\x3e or choose another player."};
wgOggPlayer.cortadoUrl = "http://upload.wikimedia.org/fundraising/2007/psa/cortado-patched-height.jar";
wgOggPlayer.extPathUrl = "http://commons.wikimedia.org/w/extensions/OggHandler";
</script>
<style type="text/css">
.ogg-player-options {
	border: solid 1px #ccc;
	padding: 2pt;
	text-align: left;
	font-size: 10pt;
}
</style>

</head>
<body>

<div class="wrapper">

<h1>“Free Access To All Human Knowledge”</h1>
<h2>A Video Appeal From Wikipedia Founder Jimmy Wales</h2>

<div class="video">

	<p>Help us spread knowledge worldwide. <b><a href="http://donate.wikimedia.org/">Make a donation to the Wikimedia Foundation!</a></b></p>

	<div id="ogg_player_1" style="width: 400px;">
		<div>
			<a href="http://upload.wikimedia.org/fundraising/2007/psa/PSA-Web-400-15fps$key.ogg" onclick="document.getElementById('playButton').click();return false"><img src="http://upload.wikimedia.org/fundraising/2007/psa/thumb-384.jpg" width="400" height="224" /></a></div>
<div><button id="playButton" onclick="wgOggPlayer.init(false, {&quot;id&quot;: &quot;ogg_player_1&quot;, &quot;videoUrl&quot;: &quot;http://upload.wikimedia.org/fundraising/2007/psa/PSA-Web-400-15fps$key.ogg&quot;, &quot;width&quot;: 400, &quot;height&quot;: 224, &quot;length&quot;: 191, &quot;linkUrl&quot;: false, &quot;isVideo&quot;: true});" style="width: 400px; text-align: center" title="Play video"><img src="http://commons.wikimedia.org/w/extensions/OggHandler/play.png" width="22" height="22" alt="Play video" /></button></div>
</div>

<div class="subs">
	<form action="sub.php" method="get">
		<b>Subtitle language:</b>
		<select name="lang" onchange="selectLang(this[selectedIndex].value)">
		$list
		</select>
		<noscript>
			<input type="submit" value="Go" />
		</noscript>
	</form>
</div>

<hr />

<h3>Mirrors</h3>

<p>Having troubles viewing the video here? Watch it at one of our mirrors:</p>

<ul>
<li><b><a href="http://wikimediafoundation.blip.tv/" target="_blank">Wikimedia Foundation's official page on blip.tv</a></b></li>
<li>Unofficial <a href="http://www.youtube.com/watch?v=y6mCO5lXsSU" target="_blank">mirror on YouTube</a></li>
<li>Additional subtitles are available at <a href="http://dotsub.com/films/wikimedia2007/index.php" target="_blank">our unofficial mirror at dotsub</a>.</li>
</ul>

<hr />

<h3>Download</h3>

<p>We recommend cross-platform, free <a href="http://www.videolan.org/vlc/">VLC</a> for playing downloaded video files.</p>

<table border="1">
<tr>
	<th>.ogg <small>Theora</small></th>
	<th>.mp4 <small>H.264</small></th>
	<th>.mpg <small>MPEG-1</small></th>
</tr>
<tr>
	<td><a href="http://upload.wikimedia.org/fundraising/2007/psa/PSA-Web-320.ogg">320x180</a> (18M)</td>
	<td><a href="http://upload.wikimedia.org/fundraising/2007/psa/PSA-Web-320.mp4">320x180</a> (17M)</td>
	<td><a href="http://upload.wikimedia.org/fundraising/2007/psa/PSA-Web-320.mpg">320x180</a> (19M)</td>
</tr>
<tr>
	<td><a href="http://upload.wikimedia.org/fundraising/2007/psa/PSA-Web-640.ogg">640x360</a> (31M)</td>
	<td><a href="http://upload.wikimedia.org/fundraising/2007/psa/PSA-Web-640.mp4">640x360</a> (30M)</td>
	<td><a href="http://upload.wikimedia.org/fundraising/2007/psa/PSA-Web-640.mpg">640x360</a> (30M)</td>
</tr>
<tr>
	<td><a href="http://upload.wikimedia.org/fundraising/2007/psa/PSA-Web-1280.ogg">1280x720</a> (50M)</td>
	<td><a href="http://upload.wikimedia.org/fundraising/2007/psa/PSA-Web-1280.mp4">1280x720</a> (55M)</td>
</tr>
</table>

<hr />

<p>› <a href="http://donate.wikimedia.org/">Return to Wikimedia Fundraising</a></p>

</div><!-- video -->

</div><!-- wrapper -->

</body>
</html>
END;
	return $text;
}
