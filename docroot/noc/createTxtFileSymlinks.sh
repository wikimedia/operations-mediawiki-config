#!/bin/bash
cd $(dirname $0)/conf

# [mediawiki-config]/wmf-config/
wmf_config_files=(
	CommonSettings-labs.php
	CommonSettings.php
	InitialiseSettings-labs.php
	InitialiseSettings.php
	PoolCounterSettings-eqiad.php
	PoolCounterSettings-pmtpa.php
	StartProfiler.php
	abusefilter.php
	codereview.php
	db-eqiad.php
	db-labs.php
	db-pmtpa.php
	db-secondary.php
	ext-labs.php
	ext-production.php
	filebackend-labs.php
	filebackend.php
	flaggedrevs.php
	jobqueue-eqiad.php
	jobqueue-pmtpa.php
	liquidthreads.php
	logging-labs.php
	lucene-common.php
	lucene-labs.php
	lucene-production.php
	mc-eqiad.php
	mc-labs.php
	mc-pmtpa.php
	missing.php
	mobile-labs.php
	mobile.php
	proofreadpage.php
	throttle.php
	wgConf.php
)

# [mediawiki-config]/
misc_files=(
	wikiversions.dat
	langlist
)

if ls ./*.txt >/dev/null 2>&1
then
	rm ./*.txt
fi

for i in "${wmf_config_files[@]}"
do
	ln -s ../../../wmf-config/$i ./$i.txt
done

for i in "${misc_files[@]}"
do
	ln -s ../../../$i ./$i.txt

	# backwards compatibity: Though non-txt sometimes triggers
	# a download in browsers (so we use .txt for everything now)
	# some users (RT-4927) still have links to /langlist or
	# /wikiversions.dat
	if [[ -e ./$i || -L ./$i ]]
	then
		rm ./$i
	fi
	ln -s ../../../$i ./$i
done
