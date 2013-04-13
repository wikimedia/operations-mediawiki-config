#!/bin/bash
cd $(dirname $0)/conf

# Create .txt symlink from mediawiki-config
mwconfig_files_txt=(
	langlist
	wikiversions.dat
	wmf-config/CommonSettings-labs.php
	wmf-config/CommonSettings.php
	wmf-config/InitialiseSettings-labs.php
	wmf-config/InitialiseSettings.php
	wmf-config/PoolCounterSettings-eqiad.php
	wmf-config/PoolCounterSettings-pmtpa.php
	wmf-config/StartProfiler.php
	wmf-config/abusefilter.php
	wmf-config/codereview.php
	wmf-config/db-eqiad.php
	wmf-config/db-labs.php
	wmf-config/db-pmtpa.php
	wmf-config/db-secondary.php
	wmf-config/ext-labs.php
	wmf-config/ext-production.php
	wmf-config/filebackend-labs.php
	wmf-config/filebackend.php
	wmf-config/flaggedrevs.php
	wmf-config/jobqueue-eqiad.php
	wmf-config/jobqueue-pmtpa.php
	wmf-config/liquidthreads.php
	wmf-config/logging-labs.php
	wmf-config/lucene-common.php
	wmf-config/lucene-labs.php
	wmf-config/lucene-production.php
	wmf-config/mc-eqiad.php
	wmf-config/mc-labs.php
	wmf-config/mc-pmtpa.php
	wmf-config/missing.php
	wmf-config/mobile-labs.php
	wmf-config/mobile.php
	wmf-config/proofreadpage.php
	wmf-config/throttle.php
	wmf-config/wgConf.php
)

# Create non-txt symlink from mediawiki-config
# Mostly for backwards compatibility.
# Raw views should use txt for consistent behaviour in browsers.
mwconfig_files=(
	all.dblist
	closed.dblist
	deleted.dblist
	fc-list
	fishbowl.dblist
	flaggedrevs.dblist
	langlist
	large.dblist
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
	trusted-xff.cdb
	wikibooks.dblist
	wikidataclient.dblist
	wikimania.dblist
	wikimedia.conf
	wikimedia.dblist
	wikinews.dblist
	wikipedia.dblist
	wikiquote.dblist
	wikisource.dblist
	wikiversions.dat
	wikiversity.dblist
	wikivoyage.dblist
	wiktionary.dblist
	wmf-config/interwiki.cdb
)

# Create non-txt symlink from /home/wikipedia/conf
other_config_file=(
	httpd/en2.conf
	httpd/foundation.conf
	httpd/ganglia.conf
	lucene/lsearch-global-2.1.conf
	httpd/main.conf
	httpd/nagios.conf
	httpd/nonexistent.conf
	httpd/postrewrites.conf
	httpd/redirects.conf
	httpd/remnant.conf
	httpd/www.wikipedia.conf
)

for i in ./*
do
	if [[ $i != "./index.php" && $i != "./highlight.php" && $i != "./images" ]]
	then
		rm $i
	fi
done

for i in "${mwconfig_files_txt[@]}"
do
	ln -s ../../../$i "./$(basename $i).txt"
done

for i in "${mwconfig_files[@]}"
do
	ln -s ../../../$i "./$(basename $i)"
done

for i in "${other_config_file[@]}"
do
	ln -s /home/wikipedia/conf/$i "./$(basename $i)"
done
