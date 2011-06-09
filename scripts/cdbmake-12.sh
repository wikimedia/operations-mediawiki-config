#!/bin/sh
# This File is from the following cdb package
# http://cr.yp.to/cdb.html
# Version:	cdb-0.75 (2000.02.19, "beta")
# Usage: cdbmake-12.sh wikiversions.db wikiversions.tmp < wikiversions.dat
# See: wikiversions.dat.sample
awk '
  /^[^#]/ {
    print "+" length($1) "," length($2) ":" $1 "->" $2
  }
  END {
    print ""
  }
' | cdbmake "$@"
