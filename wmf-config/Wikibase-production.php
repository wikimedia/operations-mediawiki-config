<?php

// already wrapped in ( $wmgUseWikibaseRepo || $wmgUseWikibaseClient )
// in CommonSettings.php

require_once( "$IP/extensions/DataValues/DataValues.php" );
require_once( "$IP/extensions/DataTypes/DataTypes.php" );
require_once( "$IP/extensions/Diff/Diff.php" );
require_once( "$IP/extensions/WikibaseDataModel/WikibaseDataModel.php" );
require_once( "$IP/extensions/Wikibase/lib/WikibaseLib.php" );

if ( $wmgUseWikibaseRepo ) {
	require_once( "$IP/extensions/Wikibase/repo/Wikibase.php" );
}

if ( $wmgUseWikibaseClient ) {
	require_once( "$IP/extensions/Wikibase/client/WikibaseClient.php" );
}
