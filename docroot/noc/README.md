These are the files for the [noc.wikimedia.org](https://noc.wikimedia.org/) microsite.

* [db.php](./db.php) generates the database page at <https://noc.wikimedia.org/db.php>.
* [conf/index.php](./conf/index.php) creates the overview at <https://noc.wikimedia.org/conf/>.

In production, noc is served from `mwwmaint*` hosts.

To test this locally:

```
cd docroot/noc$ php -S localhost:9412
```

Then view <http://localhost:9412/>.
