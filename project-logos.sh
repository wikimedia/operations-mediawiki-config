#!/usr/bin/env bash -eu
# Bash 4 or higher required (associative arrays)
declare -A logo_urls logo_15_urls logo_20_urls

# Mapping of wiki dbnames to the image used for for $wgLogo.
# Served from /static/images/project-logos/:dbname.png.
logo_urls=(
	# This is *not* the same as
	# https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/Wikipedia-logo-v2-en.svg/135px-Wikipedia-logo-v2-en.svg.png
	# This PNG was hand-tuned long ago for improved text legibility at the small
	# size of 135px
	["enwiki"]="https://upload.wikimedia.org/wikipedia/commons/d/d6/Wikipedia-logo-v2-en.png"
	["abwiki"]="https://upload.wikimedia.org/wikipedia/commons/5/5a/Wikipedia-logo-v2-ab.png"
)

logo_15_urls=(
)

logo_20_urls=(
)

echo "#### wgLogo"
for dbname in "${!logo_urls[@]}"
do
	echo "$dbname: ${logo_urls[$dbname]}"
done

echo "#### Clean up"
status=0
for path in static/images/project-logos/*.png
do
	filename=$(basename "$path")
	if [[ "$filename" != *"-"*"x.png" ]]; then
		# wgLogo (dbname-variant.png)
		dbname="${filename%.png}"
		if [[ -z "${logo_urls[$dbname]:-}" ]]; then
			# echo "Warning: Untracked $filename (logo_urls[$dbname] not set)"
			status=1
		fi
	else
		# wgLogoHD (dbname-variant-DDPXx.png)
		dbname="${filename%-*.png}"
		base="${filename%.png}"
		dppx="${base##*-}" # Long match to also strip -variant
		if [[ "$dppx" == "1.5x" ]]; then
			if [[ -z "${logo_15_urls[$dbname]:-}" ]]; then
				# echo "Warning: Untracked $filename (logo_15_urls[$dbname] not set)"
				status=1
			fi
		elif [[ "$dppx" == "2x" ]]; then
			if [[ -z "${logo_20_urls[$dbname]:-}" ]]; then
				# echo "Warning: Untracked $filename (logo_20_urls[$dbname] not set)"
				status=1
			fi
		else
			# echo "Warning: Unknown dppx '$dppx' for '$dbname' in '$filename'"
			status=1
		fi
	fi
done

exit $status
