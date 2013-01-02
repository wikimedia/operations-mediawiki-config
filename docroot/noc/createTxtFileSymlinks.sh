#!/bin/bash
files_array=(
	CommonSettings.php
	InitialiseSettings.php
	PoolCounterSettings.php
	StartProfiler.php
	abusefilter.php
	db.php
	flaggedrevs.php
	liquidthreads.php
	lucene.php
	mc.php
	mobile.php
	proofreadpage.php
	secure.php
	wgConf.php
)

cleanup_pattern=/home/wikipedia/htdocs/noc/conf/*.txt

if [ -a cleanup_pattern ]
	then rm cleanup_pattern
fi

for i in "${files_array[@]}"
do
   ln -f -s /home/wikipedia/common/wmf-config/$i /home/wikipedia/htdocs/noc/conf/$i.txt
done
