#!/usr/bin/env bash
cd $(dirname $0)/conf

# Create .txt symlink from mediawiki-config
mwconfig_files_txt=(
	CommonSettings-labs.php
	CommonSettings.php
	InitialiseSettings-labs.php
	InitialiseSettings.php
	ProductionServices.php
	PoolCounterSettings.php
	abusefilter.php
	CirrusSearch-common.php
	CirrusSearch-labs.php
	CirrusSearch-production.php
	db-production.php
	db-labs.php
	etcd.php
	FeaturedFeedsWMF.php
	filebackend.php
	flaggedrevs.php
	import.php
	interwiki.php
	interwiki-labs.php
	MetaContactPages.php
	LabsServices.php
	liquidthreads.php
	logging.php
	logos.php
	mc.php
	mc-labs.php
	missing.php
	redis.php
	throttle.php
	throttle-analyze.php
	reverse-proxy.php
	reverse-proxy-staging.php
	Wikibase.php
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

# Use a different public name because "config.yaml" is very generic
ln -s ../../../logos/config.yaml logos-config.yaml

ln -s ../../../dblists dblists
