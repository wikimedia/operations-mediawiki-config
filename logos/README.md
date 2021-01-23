Site logo management
====================

### Setup

You need to install:
* Python 3.7+
* [tox](https://tox.readthedocs.io/en/latest/index.html)
* [pngquant](https://github.com/kornelski/pngquant) (usually packaged)
* [zopflipng](https://github.com/google/zopfli) (usually packaged)

### Adding a new logo
Add or update the existing section in config.yaml for that wiki. Ensure a
`commons` setting is present that points to the SVG on Wikimedia Commons. The
thumbnails will be downloaded from there.

If this is a special logo, set the `variant` setting to save the files
under a different name. Then run:

`tox -e logos -- update {wikiname}`

This will also update logos.php.

### Updating logos.php

If you just need to update logos.php after tweaking config.yaml, run:

`tox -e logos -- generate`.

### Schema
config.yaml contains the definitions for the 1x, 1.5x and 2x versions of wiki
logos (`$wgLogos`). It contains a Python script to generate the PHP MediaWiki
configuration in `../wmf-config/logos.php`.

By default the name of the logos are expected to follow the format of:
* `{name}.png`
* `{name}-1.5x.png`
* `{name}-2x.png`

For special ocassions, a different name can be specified with the `variant` key.

If no 1.5x or 2x versions are available, set the `no_1_5x` and `no_2x` keys.
