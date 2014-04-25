#!/bin/sh

WMF_REALM=production
WMF_DATACENTER=pmtpa

if [ -f /etc/wikimedia-realm ]
then
	WMF_REALM=`cat /etc/wikimedia-realm`
fi
if [ -f /etc/wikimedia-site ]
then
	WMF_DATACENTER=`cat /etc/wikimedia-site`
fi

# Validate settings
case "$WMF_REALM" in
	"production"|"labs")
		case "$WMF_DATACENTER" in
			"eqiad")
				;;
			*)
				WMF_DATACENTER=eqiad
				;;
		esac
		;;

	*)
		# Assume something vaguely resembling a default
		WMF_REALM=production
		WMF_DATACENTER=eqiad
		;;
esac

# Function to get the filename for the current realm/datacenter, falling back
# to the "base" file if not found.
#
# Files checked are:
#   base-realm-datacenter.ext
#   base-realm.ext
#   base.ext
#
# @note The full path to the file is returned, not just the filename
#
# @param $filename Full path to file. Must contain an extension
# @output string Full path to file to be used
getRealmSpecificFilename () {
	BASE=${1%.*}
	EXT=${1##*.}
	RET=$1

	if [ -f "$BASE-$WMF_REALM-$WMF_DATACENTER.$EXT" ]
	then
		RET="$BASE-$WMF_REALM-$WMF_DATACENTER.$EXT"
	elif [ -f "$BASE-$WMF_REALM.$EXT" ]
	then
		RET="$BASE-$WMF_REALM.$EXT"
	elif [ -f "$BASE-$WMF_DATACENTER.$EXT" ]
	then
		RET="$BASE-$WMF_DATACENTER.$EXT"
	else
		RET="$1"
	fi

	printf %s "$RET"
}
