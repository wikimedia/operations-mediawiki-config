<?php
header("HTTP/1.0 302 Moved");
header("Status: 302 Moved");

switch ($_REQUEST['go']) {
  case "Consulter":
    header("Location: http://fr.wikipedia.org/wiki/Special:Search?".$_SERVER['QUERY_STRING']);
    break;
  case "Cerca":
    header("Location: http://it.wikipedia.org/wiki/Speciale:Search?".$_SERVER['QUERY_STRING']);
    break;
  case "Search":
    header("Location: http://en.wikipedia.org/wiki/Special:Search?".$_SERVER['QUERY_STRING']);
    break;
  case "Nachschlagen":
  default:
    header("Location: http://de.wikipedia.org/wiki/Spezial:Search?".$_SERVER['QUERY_STRING']);
}
exit;
?>


