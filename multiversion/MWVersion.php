<?php
/**
 * @see MWMultiVersion::getMediaWiki()
 */
function getMediaWiki( $file, $wiki = null ) {
	require_once __DIR__ . '/MWMultiVersion.php';
	return MWMultiVersion::getMediaWiki( $file, $wiki );
}
