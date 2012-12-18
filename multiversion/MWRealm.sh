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
			"pmtpa"|"eqiad")
				;;
			*)
				WMF_DATACENTER=pmtpa
				;;
		esac
		;;

	*)
		# Assume something vaguely resembling a default
		WMF_REALM=production
		WMF_DATACENTER=pmtpa
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
# @param $filename string Base filename, must contain an extension
# @output string Filename to be used
getRealmSpecificFilename () (
	BASE=${1%.*}
	EXT=${1##*.}
	RET=$1

	if [ -f "$BASE-$WMF_REALM-$WMF_DATACENTER.$EXT" ]
	then
		RET="$BASE-$WMF_REALM-$WMF_DATACENTER.$EXT"
	elif [ -f "$BASE-$WMF_REALM.$EXT" ]
	then
		RET="$BASE-$WMF_REALM.$EXT"
	else
		RET="$1"
	fi

	printf %s "$RET"
)
