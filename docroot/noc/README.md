These are the files for the [noc.wikimedia.org](https://noc.wikimedia.org/) microsite.

* [conf/index.php](./conf/index.php): Config file browser, at <https://noc.wikimedia.org/conf/>.
* [conf/highlight.php](./conf/highlight.php): html visualization of file contents, at <https://noc.wikimedia.org/conf/highlight.php?file=..>
* [db.php](./db.php): Database config, at <https://noc.wikimedia.org/db.php>.
* [wiki.php](./wiki.php): Wiki config, at <https://noc.wikimedia.org/wiki.php>.

In production, noc is served from `mwwmaint*` hosts.

To preview this locally:

```
docroot/noc$ php -S localhost:4000
```

Then open <http://localhost:4000/>.
