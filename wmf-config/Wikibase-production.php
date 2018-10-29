<?php

if ( $wmgUseWikibaseRepo ) {
	if ( $wgDBname === 'wikidatawiki' ) {
		$wgSpecialPages['ItemDisambiguation'] = 'SpecialBlankpage';

		$wgWBRepoSettings['unitStorage'] = [
			'class' => '\\Wikibase\\Lib\\Units\\JsonUnitStorage',
			'args' => [ __DIR__ . '/unitConversionConfig.json' ]
		];
	}
}
