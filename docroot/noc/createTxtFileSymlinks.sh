#!/bin/bash
cd $(dirname $0)

files_array=(
	CommonSettings.php
	InitialiseSettings.php
	PoolCounterSettings-eqiad.php
	PoolCounterSettings-pmtpa.php
	StartProfiler.php
	abusefilter.php
	codereview.php
	db-eqiad.php
	db-labs.php
	db-pmtpa.php
	filebackend.php
	flaggedrevs.php
	liquidthreads.php
	lucene-common.php
	lucene-labs.php
	lucene-production.php
	mc-eqiad.php
	mc-labs.php
	mc-pmtpa.php
	mobile.php
	proofreadpage.php
	throttle.php
	wgConf.php
)

# if -e returns false if there is a symlink but target does not exist locally
if ls ./conf/*.txt >/dev/null 2>&1
then
	rm ./conf/*.txt
fi

for i in "${files_array[@]}"
do
   ln -s ../../wmf-config/$i conf/$i.txt
done
