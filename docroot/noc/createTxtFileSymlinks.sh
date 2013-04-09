#!/bin/bash
cd $(dirname $0)/conf

files_array=(
	# wmf-config/*.php
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

	wikiversions.dat
	langlist
)

# if -e returns false if there is a symlink but target does not exist locally
if ls ./*.txt >/dev/null 2>&1
then
	rm ./*.txt
fi

for i in "${files_array[@]}"
do
   ln -s ../../../wmf-config/$i ./$i.txt
done
