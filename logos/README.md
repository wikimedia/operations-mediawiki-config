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
thumbnails will be downloaded from there. Run:

`tox -e logos -- update {wikiname}`

This will download new PNGs from Commons, compress them and update logos.php.

### Logo variants (usually temporary)
Sometimes a wiki wants to use a temporary logo for a celebration or needs an
alternative logo for language converter purposes.

Add a `variants` block:

```yaml
xxwiki:
  commons: File:Normal_logo.svg
  selected: xxwiki-birthday
  variants:
    xxwiki-birthday: File:Birthday_logo.svg
```

Then run:

`tox -e logos -- update xxwiki --variant xxwiki-birthday`

The `selected` key controls which logo will be used in logos.php. If you merely
want a variant available on the server it can be omitted.

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

If no 1.5x or 2x versions are available, set the `no_1_5x` and `no_2x` keys.

For special occasions, a different logo can be specified with the `selected` key.

For logo variants, the key is the name of the variant (must start with the
wiki's name) and the value is the Commons filename.

### Labs overrides
When changing these logos, please ensure that all labs overrides in
InitialiseSettings-labs.php work correctly to comply with the instructions at
[VPS Terms of Use](https://wikitech.wikimedia.org/wiki/Wikitech:Cloud_Services_Terms_of_use).
