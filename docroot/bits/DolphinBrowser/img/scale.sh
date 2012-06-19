cd orig
for fname in option*.png
do
    convert -resize 96x96 $fname ../xhdpi/$fname
    convert -resize 72x72 $fname ../hdpi/$fname
    convert -resize 48x48 $fname ../mdpi/$fname
    convert -resize 32x32 $fname ../ldpi/$fname
done
