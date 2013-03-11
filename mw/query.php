<?php
header("HTTP/1.x 500 Gone");
?><html>
<head>
<title>query.php is dead</title>
</head>
<body>
<p>Sorry, the old <tt>query.php</tt> interface has been shut down
as of 25 August, 2008 in favor of MediaWiki's
<a href="api.php">native machine API</a>.</p>

<p>Please update your scripts to use the current interfaces, which
are actively maintained..</p>

<p>If you're interested in creating an adaptor interface to translate
old queries to the new backend, please contact the folks on the
<a href="https://lists.wikimedia.org/mailman/listinfo/mediawiki-api">MediaWiki-API</a>
mailing list.</p>

<?php

echo "<!-- filler for IE " .
  str_repeat( "*", 1024 ) .
  " -->";
?>
</body>
</html>
