#!/bin/bash
cd $(dirname $0)/conf

# Create .txt symlink from mediawiki-config
mwconfig_files_txt=(
	CommonSettings-beta.php
	CommonSettings.php
	InitialiseSettings-beta.php
	InitialiseSettings.php
	ProductionServices.php
	PoolCounterSettings.php
	StartProfiler.php
	abusefilter.php
	CirrusSearch-common.php
	CirrusSearch-beta.php
	CirrusSearch-production.php
	db-eqiad.php
	db-beta.php
	FeaturedFeedsWMF.php
	filebackend-beta.php
	filebackend-production.php
	flaggedrevs.php
	HHVMRequestInit.php
	import.php
	interwiki.php
	jobqueue.php
	jobqueue-beta.php
	MetaContactPages.php
	liquidthreads.php
	logging.php
	logging-beta.php
	mc.php
	mc-beta.php
	missing.php
	mobile-beta.php
	mobile.php
	proofreadpage.php
	redis.php
	throttle.php
	wgConf.php
	squid.php
	squid-beta.php
	session.php
	session-beta.php
	trusted-xff.php
	Wikibase.php
	Wikibase-beta.php
	Wikibase-production.php
	wikitech.php
)

# Create non-txt symlink from mediawiki-config
# raw views should use txt for consistent behaviour in browsers
# (not triggering a download instead of a view, and rendering as plain text).
mwconfig_files=(
	fc-list
	langlist
	wikiversions.json
	wikiversions-beta.json
	wmf-config/extension-list
	wmf-config/extension-list-beta
	wmf-config/extension-list-wikitech
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

for i in ../../../dblists/*
do
	ln -s "$i" "./$(basename $i)"
done
