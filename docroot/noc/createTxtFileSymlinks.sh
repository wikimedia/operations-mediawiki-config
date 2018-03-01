#!/bin/bash
cd $(dirname $0)/conf

# Create .txt symlink from mediawiki-config
mwconfig_files_txt=(
	CommonSettings-labs.php
	CommonSettings.php
	InitialiseSettings-labs.php
	InitialiseSettings.php
	ProductionServices.php
	PoolCounterSettings.php
	StartProfiler.php
	abusefilter.php
	CirrusSearch-common.php
	CirrusSearch-labs.php
	CirrusSearch-production.php
	db-codfw.php
	db-eqiad.php
	db-labs.php
	etcd.php
	FeaturedFeedsWMF.php
	filebackend.php
	flaggedrevs.php
	HHVMRequestInit.php
	import.php
	interwiki.php
	jobqueue.php
	jobqueue-labs.php
	MetaContactPages.php
	liquidthreads.php
	logging.php
	logging-labs.php
	mc.php
	mc-labs.php
	missing.php
	mobile-labs.php
	mobile.php
	proofreadpage.php
	redis.php
	throttle.php
	wgConf.php
	reverse-proxy.php
	reverse-proxy-staging.php
	session.php
	session-labs.php
	timeline.php
	trusted-xff.php
	Wikibase.php
	Wikibase-labs.php
	Wikibase-production.php
	wikitech.php
)

# Create non-txt symlink from mediawiki-config
# raw views should use txt for consistent behaviour in browsers
# (not triggering a download instead of a view, and rendering as plain text).
mwconfig_files=(
	debug.json
	fc-list
	langlist
	wikiversions.json
	wikiversions-labs.json
	wmf-config/extension-list
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

ln -s ../../../dblists dblists
