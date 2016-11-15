<?php
header( 'HTTP/1.x 410 Gone' );
?><!DOCTYPE html>
<html>
<head>
<title>query.php is dead</title>
</head>
<body>
<p>Sorry, the old <tt>query.php</tt> interface has been shut down
as of 25 August, 2008 in favor of MediaWiki's
<a href="api.php">native machine API</a>.</p>

<p>Please update your scripts to use the current interfaces, which
are actively maintained..</p>

<?php
echo '<!-- filler for IE ' . str_repeat( '*', 1024 ) . ' -->';
?>
</body>
</html>
