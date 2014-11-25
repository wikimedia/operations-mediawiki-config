#!/bin/bash
cd $(dirname $0)/conf

# Create .txt symlink from mediawiki-config
mwconfig_files_txt=(
	CommonSettings-labs.php
	CommonSettings.php
	InitialiseSettings-labs.php
	InitialiseSettings.php
	PoolCounterSettings-eqiad.php
	StartProfiler.php
	abusefilter.php
	CirrusSearch-common.php
	CirrusSearch-labs.php
	db-eqiad.php
	db-labs.php
	db-secondary.php
	ext-labs.php
	ext-production.php
	filebackend-labs.php
	filebackend.php
	flaggedrevs.php
	jobqueue-eqiad.php
	LegalContactPages.php
	liquidthreads.php
	logging.php
	logging-labs.php
	mc.php
	mc-labs.php
	missing.php
	mobile-labs.php
	mobile.php
	proofreadpage.php
	throttle.php
	wgConf.php
	wgConfVHosts.php
	wgConfVHosts-labs.php
	squid.php
	squid-labs.php
	session.php
	session-labs.php
	Wikibase.php
	wikitech.php
)

# Create non-txt symlink from mediawiki-config
# Except for dblists, these are mostly for backwards compatibility as
# raw views should use txt for consistent behaviour in browsers
# (not triggering a download instead of a view, and rendering as plain text).
mwconfig_files=(
	all.dblist
	all-labs.dblist
	nonbetafeatures.dblist
	closed.dblist
	commonsuploads.dblist
	deleted.dblist
	echowikis.dblist
	fc-list
	fishbowl.dblist
	flaggedrevs.dblist
	group0.dblist
	langlist
	large.dblist
	mediaviewer.dblist
	medium.dblist
	private.dblist
	s1.dblist
	s2.dblist
	s3.dblist
	s4.dblist
	s5.dblist
	s6.dblist
	s7.dblist
	small.dblist
	special.dblist
	visualeditor.dblist
	visualeditor-default.dblist
	wikibooks.dblist
	wikidata.dblist
	wikidataclient.dblist
	wikimania.dblist
	wikimedia.dblist
	wikinews.dblist
	wikipedia.dblist
	wikiquote.dblist
	wikisource.dblist
	wikiversions.json
	wikiversions-labs.json
	wikiversity.dblist
	wikivoyage.dblist
	wiktionary.dblist
	wmf-config/extension-list
	wmf-config/extension-list-labs
	wmf-config/extension-list-wikitech
	wmf-config/interwiki.cdb
	wmf-config/interwiki-labs.cdb
	wmf-config/trusted-xff.cdb
)

for i in ./*
do
	if [[ $i != "./index.php" && $i != "./highlight.php" && $i != "./images" && $i != "./activeMWVersions.php" ]]
	then
		rm $i
	fi
done

for i in "${mwconfig_files_txt[@]}"
do
	ln -s ../../../wmf-config/$i "./$(basename $i).txt"
done

for i in "${mwconfig_files[@]}"
do
	ln -s ../../../$i "./$(basename $i)"
done
