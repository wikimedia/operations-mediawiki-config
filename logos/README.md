Site logo management
====================

### Setup

You need to install:
* Python 3.7+
* [tox](https://tox.readthedocs.io/en/latest/index.html)
* [pngquant](https://github.com/kornelski/pngquant) (usually packaged)
* [zopflipng](https://github.com/google/zopfli) (usually packaged)
* [svgo](https://github.com/svg/svgo)
* [rsvg-convert](https://wiki.gnome.org/Projects/LibRsvg) (part of the `librsvg` package)

Note that you need the pngquant and zopflipng OS packages, not the Python packages.

### Adding a new logo
Add or update the existing section in config.yaml for that wiki. Ensure a
`commons` setting is present that points to the SVG on Wikimedia Commons. The
thumbnails will be downloaded from there. Run:

`tox -e logos -- update {wikiname}`

This will download new PNGs from Commons, compress them and update logos.php.

When adding or modifying logos, it is highly recommended to add a `comment`
setting (normally Phabricator task ID) in config.yaml to explain the change.
You can also add it in `comment_1_5x` and `comment_2x` fields if necessary.

### Adding a new wordmark/tagline
Similar to adding a new logo, but you need to add a `commons_wordmark`
(or `commons_tagline`) field to the config.yaml file. This script will download
the SVG from Wikimedia Commons and compress it. If the SVG file has a width
larger than 140px, it will also be resized.

For some cases, if you want to manually set the width/height of the SVG, please
add `width_wordmark` and `height_wordmark` (for tagline, please replace
"wordmark" with "tagline" for the name of the field) to the config.yaml file.

There is no separate command to update the wordmark/tagline, it will be updated
when you run:

`tox -e logos -- update {wikiname}`

to update the site logo.

If you want to use the wordmark/tagline from another wiki for your site,
you can add a `selected_wordmark` field to the config.yaml file. For example,
if `yywiki` want to use the wordmark/tagline from `xwiki`:

```yaml
yywiki:
  selected_wordmark: xxwiki
  selected_tagline: xxwiki
```

If you need to add wordmarks/taglines without uploading files
to Wikimedia Commons, you can add a `local_wordmark`/`local_tagline` field to
the config.yaml. In future when the logos are stable you should upload the files
to Wikimedia Commons so they can be managed by their respective communities. A
script is proposed for managing this in future, more details at
https://phabricator.wikimedia.org/T345775.

When adding or modifying wordmarks/taglines, it is highly recommended to add
a `comment_wordmark` (or `comment_tagline`) setting (normally Phabricator
task ID) in config.yaml to explain the change.

### Adding a new icon
Add or update the existing section in config.yaml for that wiki. Ensure a
`commons_icon` setting is present that points to the SVG on Wikimedia Commons.
Similar to adding wordmarks/taglines, you can add a `local_icon` field to
directly use the local icon file.

When adding or modifying logos, it is highly recommended to add a `comment_icon`
setting (normally Phabricator task ID) in config.yaml to explain the change.

### Renaming files in static folder

''IMPORTANT'': When renaming files, it is important to use symbolic links to
point to the old version of static assets for cached HTML. It is also important
to verify that no symbolic links are broken.

```bash
find static -xtype l # unix
find static | xargs file | grep -i "broken symbolic link" # osx
```

### Logo variants (usually temporary)
Sometimes a wiki wants to use a temporary logo for a celebration or needs an
alternative logo for language converter purposes.

Add a `variants` block:

```yaml
xxwiki:
  commons: File:Normal_logo.svg
  selected: xxwiki-birthday
  variants:
    xxwiki-birthday:
      commons: File:Birthday_logo.svg
      commons_icon: File:Birthday_icon.svg
  commons_wordmark: File:Normal_wordmark.svg
  commons_tagline: File:Normal_tagline.svg
  commons_icon: File:Normal_icon.svg
  comment: T123456, T999999
  comment_wordmark: T123456
  comment_tagline: T123456
  comment_icon: T123456, T999999
```

Then run:

`tox -e logos -- update xxwiki --variant xxwiki-birthday`

The `selected` key controls which logo will be used in logos.php. If you merely
want a variant available on the server it can be omitted.

### Updating logos.php

If you just need to update logos.php after tweaking config.yaml, run:

`tox -e logos -- generate && composer buildLogoHTML`.

### Schema
config.yaml contains the definitions for the 1x, 1.5x, and 2x versions of wiki
logos (`$wgLogos`), and the wordmark/tagline definitions. It contains a Python
script to generate the PHP MediaWiki configuration in `../wmf-config/logos.php`.

By default, the name of the logos is expected to follow the format of:
* `{name}.png`
* `{name}-1.5x.png`
* `{name}-2x.png`

If no 1.5x or 2x versions are available, set the `no_1_5x` and `no_2x` keys.

For special occasions, a different logo can be specified with the `selected` key.

For logo variants, the key is the name of the variant (must start with the
wiki's name) and the value is the Commons filename.

For wordmark/tagline, the name of them is expected to follow the format of:
* `{wikiproject}-wordmark-{langcode}.svg`
* `{wikiproject}-tagline-{langcode}.svg`

For icon, the name of it is expected to be the same of the project name.

### Labs overrides
When changing these logos, please ensure that all labs override in
InitialiseSettings-labs.php work correctly to comply with the instructions at
[VPS Terms of Use](https://wikitech.wikimedia.org/wiki/Wikitech:Cloud_Services_Terms_of_use).
