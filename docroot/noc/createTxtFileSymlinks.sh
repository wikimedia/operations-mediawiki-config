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
	AffComContactPages.php
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
	fc-list
	langlist
	wikiversions.json
	wikiversions-labs.json
	wmf-config/extension-list
	wmf-config/extension-list-labs
	wmf-config/extension-list-wikitech
	wmf-config/interwiki.cdb
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

for i in ../../../dblists/*.dblist
do
	ln -s "$i" "./$(basename $i)"
done
