# MinusX release history #

## 0.3.1 / 2018-02-17 ##
* Add .gitattributes with export-ignore (Umherirrender)

## 0.3.0 / 2018-01-10 ##
* Loosen symfony/console dependency (Kunal Mehta)
* Support ignoring entire directories (Kunal Mehta)

## 0.2.1 / 2017-12-03 ##
* Percent-encode URLs in `README.md` to work around bad parsers. (MZMcBride)
* Use env instead of /usr/bin/php directly (Sam Wilson)

## 0.2.0 / 2017-10-30 ##
* Don't use SplFileObject::fread() for PHP < 5.5.11 support (Kunal Mehta)
* Drop .php extension from minus-x command (Kunal Mehta)
* Whitelist `application/x-dosexec` when run on Windows (Kunal Mehta)

## 0.1.0 / 2017-09-12 ##

* Initial release (Kunal Mehta)
