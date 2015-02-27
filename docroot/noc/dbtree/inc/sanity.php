<?php

function backtrace()
{
    ob_start();
    debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    e(ob_get_clean());
}

function map()
{
    return call_user_func_array('array_map', func_get_args());
}

function reduce($func, $array, $init=null)
{
    return array_reduce($array, $func, $init);
}

function filter($func, $array)
{
    return array_filter($array, $func);
}

function str($arg, $j='')
{
    if (is_array($arg))
        $arg = join($j, $arg);
    return strval($arg);
}

function bool($v)
{
    return $v ? true: false;
}

function keys($a)
{
    return is_array($a) ? array_keys($a): array( str($a) );
}

function vals($a)
{
    return is_array($a) ? array_values($a): array( str($a) );
}

function all($a)
{
    return reduce(function($a, $b) { return $a && $b; }, $a, true);
}

function any($a)
{
    return reduce(function($a, $b) { return $a || $b; }, $a, false);
}

function any_null($a)
{
    return reduce(function($a, $b) { return $a || is_null($b); }, $a, false);
}

function any_empty($a)
{
    return reduce(function($a, $b) { return $a || empty($b); }, $a, false);
}

// Retrieve a field from an array (say, $_REQUEST) and cast it to a certain type,
// or fall back on the default value.
function expect($arr, $key, $type='string', $def=null)
{
    if (is_array($arr) && array_key_exists($key, $arr))
    {
        // type can be an array of types
        if (is_array($type))
        {
            $index = array_search($arr[$key], $type);
            return ($index !== false) ? $type[$index]: $def;
        }
        // type can be a string of types | separated
        $types = explode('|', strtolower($type));
        foreach ($types as $type)
        {
            // anything PHP thinks is true becomes proper true.
            if ($type == 'bool' || $type == 'boolean')
                return ($arr[$key]) ? true: false;
            if (is_numeric($arr[$key]))
            {
                if ($type == 'number')
                    return $arr[$key];
                if ($type == 'int' || $type == 'integer' || $type == 'signed')
                    return (int)$arr[$key];
                if ($type == 'float' || $type == 'decimal')
                    return (float)$arr[$key];
                if (($type == 'uint' || $type == 'unsigned') && intval($arr[$key]) >= 0)
                    return (int)$arr[$key];
                if (($type == 'pint' || $type == 'positive') && intval($arr[$key]) > 0)
                    return (int)$arr[$key];
            }
            if ($type == 'hex' && is_scalar($arr[$key]) && preg_match('/^(0x)?[0-9a-fA-F]+$/', $arr[$key]))
                return hexdec($arr[$key]);
            if (($type == 'str' || $type == 'string') && is_scalar($arr[$key]))
                return trim(sprintf("%s", $arr[$key]));
            if ($type == 'array' && is_array($arr[$key]))
                return $arr[$key];
            if ($type == 'object' && is_object($arr[$key]))
                return $arr[$key];
            if ($type == 'csv' && is_scalar($arr[$key]))
                return strlen($arr[$key]) ? preg_split('/\s*,\s*/', trim($arr[$key])) : array();
            if ($type == 'json' && is_scalar($arr[$key]) && strlen($arr[$key]))
                return json_decode($arr[$key], true);
            // type can be a callback function to filter a value
            if (is_string($type) && function_exists($type))
                return $type($arr[$key], $def);
        }
    }
    return $def;
}

// Robust htmlentities
function escape($str)
{
    $charsets = array(
        'UTF-8',
        'ISO-8859-1',
        'ISO-8859-15',
        'GB2312',
        'BIG5',
        'BIG5-HKSCS',
        'Shift_JIS',
        'EUC-JP',
        'KOI8-R',
        'ISO-8859-5',
        'cp1251',
        'cp1252',
        'MacRoman',
    );

    $test = false;
    foreach ($charsets as $charset)
    {
        if ($test === false) $test = @iconv($charset, 'UTF-8//TRANSLIT', $str);
        if ($test !== false) { $str = $test; break; }
    }

    $flags = ENT_QUOTES;
    if (defined('ENT_SUBSTITUTE')) $flags |= ENT_SUBSTITUTE; // php 5.4
    if (defined('ENT_HTML5'))      $flags |= ENT_HTML5;      // php 5.4

    return htmlentities($str, $flags, 'UTF-8');
}

// Intelligently build a <tag></tag>
function tag($tag, $txt='', $vars= array())
{
    if (is_array($txt))
    {
        $vars = $txt;
        $txt = '';
    }

    if (is_string($vars))
    {
        $vars = array( 'class' => $vars );
    }

    if (isset($vars['html']))
    {
        $txt = str($vars['html']);
        unset($vars['html']);
    }

    if (expect($vars, 'class', 'array'))
    {
        $vars['class'] = join(' ', $vars['class']);
    }

    if (expect($vars, 'style', 'array'))
    {
        $vars['style'] = map(
            function() {
                return sprintf('%s: %s;', $a, $b);
            },
            keys($vars['style']),
            vals($vars['style'])
        );
    }

    $attr = join(' ', map(
        function($key, $val) {
            return is_null($val) ? $key: sprintf('%s="%s"', $key, $val);
        },
        keys($vars),
        vals($vars)
    ));

    if (preg_match('/^(br|hr|input|link|meta|img)$/', $tag))
        return sprintf('<%s %s/>', $tag, $attr);

    $eol = preg_match('/^(div|p|h[12345]|section|article|header|footer)$/', $tag)
        ? "\n": '';

    $txt = str($txt);

    return sprintf('<%s %s>%s%s%s</%s>%s', $tag, $attr, $eol, $txt, $eol, $tag, $eol);
}

class cache
{
    protected static $cache = array();

    public static function get($key, $type='string', $def=null)
    {
        if (function_exists('mc') && mc() && ($value = @mc()->get($key)))
        {
            $tmp[$key] = $value;
            return expect($tmp, $key, $type, $def);
        }
        return expect(self::$cache, $key, $type, $def);
    }

    public static function set($key, $value, $expire=0)
    {
        if (function_exists('mc') && mc())
        {
            if (class_exists('Memcache') && mc() instanceof Memcache)
                @mc()->set($key, $value, 0, $expire);
            if (class_exists('Memcached') && mc() instanceof Memcached)
                @mc()->set($key, $value, $expire);
            return;
        }
        self::$cache[$key] = $value;
    }
}

class sql
{
    protected $db     = null;
    protected $table  = null;
    protected $alias  = null;
    protected $fields = array();
    protected $where  = array();
    protected $having = array();
    protected $limit  = null;
    protected $order  = array();
    protected $group  = array();
    protected $join   = array();

    protected $cache  = 0;
    protected $expire = 0;
    // shared result set cache
    protected static $_result_cache = array();

    protected $set = null;
    protected $multiset = array();

    const NOCACHE = 0;
    const CACHE = 1;
    const MEMCACHE = 2;

    // mysql result resource after each query
    protected $rs = null;
    protected $rs_fields = array();
    protected $rs_sql = null;

    protected $error = null;
    protected $error_msg = null;

    // flags
    protected $sql_no_cache = false;

    // for logging
    protected $db_host = null;
    protected $db_vers = null;
    protected $db_user = null;

    public function __construct($table=null, $db=null)
    {
        if (!is_null($table)) $this->from($table);
        $this->db(is_null($db) && function_exists('db') ? db(): $db);
    }

    // quote and escape a value
    public static function quote($val)
    {
        if (is_numeric($val) && preg_match('/^[-]{0,1}[0-9]+$/', $val)) return $val;
        if (is_scalar($val)) return sprintf("'%s'", mysql_real_escape_string($val));
        if (is_array($val))
        {
            $out = array(); foreach ($val as $v) $out[] = self::quote($v);
            return sprintf('(%s)', join(',', $out));
        }
        // expr() returns a stdObject with ->content to idetify something already escaped
        if (is_object($val) && get_class($val) == 'stdClass' && isset($val->content)) return $val->content;
        if (is_callable($val)) return $val();
        return 'null';
    }

    public function table() { return $this->table; }
    public function alias() { return $this->alias; }

    // quote a field or table name if safe to do so
    public static function quote_name($key)
    {
        return preg_match('/^[a-zA-Z]+[a-zA-Z0-9_]*$/', $key) ? sprintf('`%s`', trim($key)): trim($key);
    }

    // quote and escape key/value pairs
    public static function prepare($pairs)
    {
        $out = array();
        if (is_array($pairs)) foreach ($pairs as $key => $val)
        {
            $key = self::quote_name($key);
            $out[$key] = self::quote($val);
        }
        return $out;
    }

    // return a prepared set as an SQL key = val string
    public static function prepare_set($pairs)
    {
        $out = array();
        foreach (self::prepare($pairs) as $key => $val)
            $out[] = sprintf('%s = %s', $key, $val);
        return join(', ', $out);
    }

    // determine the name of a table's primary key
    public static function primary_key($table) {
        $schema = 'database()';
        if (preg_match_all('/^(.+)\.(.+)$/', $table, $matches))
        {
            $schema = "'".$matches[1][0]."'";
            $table  = $matches[2][0];
        }
        return self::query('information_schema.COLUMNS', sql::MEMCACHE)
            ->fields('COLUMN_NAME')
            ->where('TABLE_NAME', $table)->where('COLUMN_KEY', 'PRI')
            ->where("TABLE_SCHEMA = $schema")->fetch_value();
    }

    // grab a table's field types and defaults
    public static function table_fields($table) {
        $schema = 'database()';
        if (preg_match_all('/^(.+)\.(.+)$/', $table, $matches))
        {
            $schema = "'".$matches[1][0]."'";
            $table  = $matches[2][0];
        }
        return self::query('information_schema.COLUMNS', sql::MEMCACHE)
            ->fields('COLUMN_NAME,COLUMN_DEFAULT,IS_NULLABLE,DATA_TYPE,COLUMN_KEY,EXTRA,CHARACTER_MAXIMUM_LENGTH')
            ->where('TABLE_NAME', $table)->where("TABLE_SCHEMA = $schema")->fetch_all();
    }

    // generate an arbitrary sql fragment with ? fields
    public static function parse($pattern)
    {
        $parts = explode('?', $pattern);
        $out = array_shift($parts);
        $i = 1; while (count($parts))
        {
            $arg = func_get_arg($i);
            if ($arg === false) $arg = null;
            $out .= self::quote($arg);
            $out .= array_shift($parts);
            $i++;
        }
        return $out;
    }

    // generate an arbitrary sql fragment with ? fields, as an object for quote()
    public static function expr()
    {
        $out = call_user_func_array('self::parse', func_get_args());
        $obj = new stdClass(); $obj->content = $out;
        return $obj;
    }

    // allow client side caching of result
    public function cache($f=sql::CACHE, $expire=0)
    {
        $this->cache = $f;
        $this->expire = $expire;
        return $this;
    }

    // disable all caching, including query cache
    public function nocache()
    {
        $this->cache(sql::NOCACHE, 0);
        $this->sql_no_cache = true;
        return $this;
    }

    public function db($db)
    {
        $this->db = $db;
    }

    // add a field to be retrieved
    public function field($field)
    {
        $this->fields[] = self::quote_name($field);
        return $this;
    }
    // set an array of field to be retrieved
    public function fields($fields)
    {
        if (!is_array($fields))
            $fields = preg_split('/\s*,\s*/', $fields);

        $this->fields = array();
        foreach ($fields as $f) $this->field($f);

        return $this;
    }
    // set primary table name
    public function from($table, $alias=null)
    {
        if (strpos($table, ' ') && is_null($alias))
            list ($table, $alias) = preg_split('/\s+/', trim($table));

        $this->table = $table;
        $this->alias = $alias;

        if (empty($this->fields))
            $this->field(($alias ? $alias: $table).'.*');

        return $this;
    }
    // create an SQL clause for WHERE or JOIN conditions
    public static function clause($name)
    {
        $name = trim($name);
        $argc = func_num_args();
        $value = $argc > 1 ? func_get_arg(1): null;
        // ? mark expression with variable number of args
        if (strpos($name, '?') !== false)
            return call_user_func_array('self::parse', func_get_args());
        else
        // single field name without operator, so default to =
        if (strpos($name, ' ') === false && $argc > 1)
            return sprintf('%s = %s', self::quote_name($name), self::quote($value));
        else
        // single field with trailing operator and separate value
        if (strpos($name, ' ') !== false && $argc > 1)
        {
            list ($field, $op) = preg_split('/\s+/', $name);
            return sprintf('%s %s %s', self::quote_name($field), $op, self::quote($value));
        }
        else
        // only one arg and may be raw SQL, don't touch it
            return $name;
    }
    // add a where clause, defaulting to = when >1 argument
    public function where($sql)
    {
        $this->where[] = call_user_func_array('self::clause', func_get_args());
        return $this;
    }
    public function where_eq($name, $val)
    {
        return $this->where($name.' =', $val);
    }
    public function where_ne($name, $val)
    {
        return $this->where($name.' <>', $val);
    }
    public function where_lt($name, $val)
    {
        return $this->where($name.' <', $val);
    }
    public function where_let($name, $val)
    {
        return $this->where($name.' <=', $val);
    }
    public function where_gt($name, $val)
    {
        return $this->where($name.' >', $val);
    }
    public function where_gte($name, $val)
    {
        return $this->where($name.' >=', $val);
    }
    public function where_like($name, $val, $mode=true)
    {
        $this->where[] = sprintf('%s %s %s', $name, $mode ? 'like': 'not like', self::quote($val));
        return $this;
    }
    public function where_not_like($name, $val)
    {
        return $this->where_like($name, $val, false);
    }
    public function where_regexp($name, $val, $mode=true)
    {
        $this->where[] = sprintf('%s %s %s', $name, $mode ? 'regexp': 'not regexp', self::quote($val));
        return $this;
    }
    public function where_not_regexp($name, $val)
    {
        return $this->where_regexp($name, $val, false);
    }
    public function having($sql)
    {
        $this->having[] = call_user_func_array('self::clause', func_get_args());
        return $this;
    }
    // add a field IN(...values...)
    public function where_in($name, $vals)
    {
        if (is_scalar($vals)) $vals = preg_split('/\s*,\s*/', $vals);
        $this->where[] = sprintf('%s in (%s)', self::quote_name($name), join(',', self::prepare($vals)));
        return $this;
    }
    // add a field IN(...values...)
    public function where_not_in($name, $vals)
    {
        if (is_scalar($vals)) $vals = preg_split('/\s*,\s*/', $vals);
        $this->where[] = sprintf('%s not in (%s)', self::quote_name($name), join(',', self::prepare($vals)));
        return $this;
    }
    public function where_in_if($name, $vals)
    {
        if (!empty($vals)) $this->where_in($name, $vals);
        return $this;
    }
    public function where_not_in_if($name, $vals)
    {
        if (!empty($vals)) $this->where_not_in($name, $vals);
        return $this;
    }
    // add a field IS NULL
    public function where_null($name, $state=true)
    {
        $this->where[] = sprintf('%s %s null', self::quote_name($name), $state ? 'is': 'is not');
        return $this;
    }
    // add a field IS NULL
    public function where_not_null($name)
    {
        return $this->where_null($name, false);
    }
    // add a field between x AND y
    public function where_between($name, $lower, $upper)
    {
        $this->where(self::quote_name($name) .' between ? and ?', $lower, $upper);
        return $this;
    }
    // bulk add where clauses to be ANDed togetehr
    public function where_and($pairs)
    {
        foreach ($pairs as $key => $val)
        {
            if (is_array($val)) $this->where_in($key, $val);
            else $this->where($key, $val);
        }
        return $this;
    }
    // bulk add where clauses to be ORed together
    public function where_or($pairs)
    {
        $tmp = $this->where;
        $this->where = array();
        foreach ($pairs as $key => $val)
        {
            if (is_array($val)) $this->where_in($key, $val);
            else $this->where($key, $val);
        }
        $tmp[] = sprintf('(%s)', join(' or ', $this->where));
        $this->where = $tmp;
        return $this;
    }
    // limit the returned rows
    public function limit($offset, $limit=null)
    {
        if (is_null($limit)) $this->limit = $offset;
        else $this->limit = $offset.', '.$limit;
        return $this;
    }
    // order by one or more fields
    public function order($name, $dir='asc')
    {
        $name = trim($name);
        // single CSV string of fields (and possibly directions)
        if (strpos($name, ','))
            foreach (preg_split('/\s*,\s*/', $name) as $field) $this->order($field);
        else
        // single field string with name and direction
        if (preg_match('/\s+(asc|desc)$/i', $name))
        {
            list ($name, $dir) = preg_split('/\s+/', $name);
            $this->order[] = self::quote_name($name).' '.$dir;
        }
        else
        // proper field and separate direction
            $this->order[] = self::quote_name($name).' '.$dir;
        return $this;
    }
    // group by one or more fields
    public function group($vals)
    {
        if (is_scalar($vals)) $vals = preg_split('/\s*,\s*/', $vals);
        foreach ($vals as $val) $this->group[] = self::quote_name($val);
        $this->group = array_unique($this->group);
        return $this;
    }
    // join a table
    public function join($table, $on=null)
    {
        $alias = null;

        if (is_string($table) && strpos($table, ' ') && !preg_match('/^\s*select/i', $table))
        {
            list ($table, $alias) = preg_split('/\s+/', trim($table));
            $table = self::quote_name($table);
        }

        if ($on)
        {
            $args = func_get_args(); array_shift($args);
            $clause = call_user_func_array('self::clause', $args);
            $this->join[] = sprintf('join '. $table .($alias ? ' '.$alias:'') . ' on ' . $clause);
        }
        else
        {
            $this->join[] = sprintf('join '. $table .($alias ? ' '.$alias:''));
        }

        return $this;
    }
    // left outer join a table
    public function left_join($table, $on=null)
    {
        $alias = null;

        if (is_string($table) && strpos($table, ' ') && !preg_match('/^\s*\(/i', $table))
        {
            list ($table, $alias) = preg_split('/\s+/', trim($table));
            $table = self::quote_name($table);
        }

        $args = func_get_args(); array_shift($args);
        $clause = call_user_func_array('self::clause', $args);

        $this->join[] = sprintf('left join '. $table .($alias ? ' '.$alias:'') . ' on ' . $clause);

        return $this;
    }
    // right outer join a table
    public function right_join($table, $on=null)
    {
        $alias = null;

        if (is_string($table) && strpos($table, ' ') && !preg_match('/^\s*\(/i', $table))
        {
            list ($table, $alias) = preg_split('/\s+/', trim($table));
            $table = self::quote_name($table);
        }

        $args = func_get_args(); array_shift($args);
        $clause = call_user_func_array('self::clause', $args);

        $this->join[] = sprintf('right join '. $table .($alias ? ' '.$alias:'') . ' on ' . $clause);

        return $this;
    }    // set key/val pair to be written in undate or single row insert
    public function set($pairs, $val=null)
    {
        if (is_scalar($pairs))
        {
            if (!is_array($this->set))
                $this->set = array();
            $this->set[$pairs] = $val;
        }
        else
        foreach ($pairs as $key => $val)
            $this->set($key, $val);
        return $this;
    }
    // retrieve from set
    public function get($key=null, $def=null)
    {
        if (is_null($key)) return $this->set;
        return isset($this->set[$key]) ? $this->set[$key]: $def;
    }
    // set multiple rows to be written in a bulk insert
    public function rows($rows)
    {
        $this->multiset = $rows;
        return $this;
    }
    // add another set to be written in a bulk insert
    public function add_row($row)
    {
        $this->multiset[] = $row;
        return $this;
    }
    // methods for building SQL fragments
    private function get_from() { return 'from '.self::quote_name($this->table) .($this->alias ? ' '.$this->alias:''); }
    private function get_join() { return $this->join ? join(' ', $this->join): ''; }
    private function get_where() { return $this->where ? 'where '.join(' and ', $this->where) : ''; }
    private function get_having() { return $this->having ? 'having '.join(' and ', $this->having) : ''; }
    private function get_limit() { return $this->limit ? 'limit '.$this->limit: ''; }
    private function get_order() { return $this->order ? 'order by '.join(', ', $this->order): ''; }
    private function get_group() { return $this->group ? 'group by '.join(', ', $this->group): ''; }

    // generate a SELECT query
    public function get_select()
    {
        $flags  = $this->sql_no_cache ? 'SQL_NO_CACHE': '';
        $fields = $this->fields ? join(', ', $this->fields): '*';
        $from   = $this->get_from();
        $join   = $this->get_join();
        $where  = $this->get_where();
        $having = $this->get_having();
        $order  = $this->get_order();
        $group  = $this->get_group();
        $limit  = $this->get_limit();
        // MySQL runs a needless filesort when grouping without an order clause. disable it.
        if ($group && !$order) $order = 'order by null';
        return "select $flags $fields $from $join $where $group $having $order $limit";
    }

    // generate a DELETE query
    public function get_delete()
    {
        $del = $this->alias;
        $from  = $this->get_from();
        $join  = $this->get_join();
        $where = $this->get_where();
        $order = $this->get_order();
        $group = $this->get_group();
        $limit = $this->get_limit();
        return "delete $del $from $join $where $group $order $limit";
    }

    // generate an INSERT query
    public function get_insert()
    {
        $pairs = !empty($this->multiset) ? $this->multiset: array($this->set);

        $vals = array();
        $keys = null;
        foreach ($pairs as $row)
        {
            $keys = array_keys($row);
            $vals[] = join(', ', self::prepare($row));
        }
        $keys = join(', ', $keys);
        $vals = join('), (', $vals);
        $table = self::quote_name($this->table);
        return "insert into $table ($keys) values ($vals)";
    }

    // generate a REPLACE query
    public function get_replace()
    {
        return preg_replace('/^insert/', 'replace', $this->get_insert());
    }

    // generate an UPDATE query
    public function get_update()
    {
        $table = self::quote_name($this->table);
        $alias = $this->alias ? $this->alias: '';
        $where = $this->get_where();
        $limit = $this->get_limit();
        $set = self::prepare_set($this->set);
        return "update $table $alias set $set $where $limit";
    }

    // execute the current state as a SELECT
    public function execute($sql=null)
    {
        if (is_null($sql)) $sql = $this->get_select();

        $this->error = null;
        $this->error_msg = null;

        $host = preg_replace('/^([^\s]+).*$/', '\1', mysql_get_host_info($this->db));
        e('sql '.$host.': '.rtrim($sql)."\n");

        $this->rs = mysql_query($sql, $this->db);
        $this->rs_sql = $sql;

        if (!$this->rs)
        {
            $this->error = mysql_errno();
            $this->error_msg = mysql_error();
            e('sql error: '.$this->error.' '.$this->error_msg."\n");
        }

        $this->rs_fields = array();

        if (is_resource($this->rs))
        {
            $i = 0;
            $l = mysql_num_fields($this->rs);
            while ($i < $l)
            {
                $this->rs_fields[$i] = mysql_fetch_field($this->rs, $i);
                $i++;
            }
        }

        return $this;
    }

    public function error()
    {
        return ($this->error) ? array($this->error, $this->error_msg) : null;
    }

    public function recache($rows)
    {
        $sql = $this->rs_sql ? $this->rs_sql: $this->get_select();
        $md5 = md5($sql);

        if ($this->cache === sql::MEMCACHE)
            cache::set($md5, $rows, $this->expire);

        if ($this->cache === sql::CACHE)
            self::$_result_cache[$md5] = gzcompress(serialize($rows));
    }

    // retrieve all available rows
    public function fetch_all($index=null)
    {
        $host = preg_replace('/^([^\s]+).*$/', '\1', mysql_get_host_info($this->db));

        $sql = $this->rs_sql ? $this->rs_sql: $this->get_select();
        $md5 = md5($sql);

        if (!$this->rs)
        {
            if ($this->cache && isset(self::$_result_cache[$md5]))
            {
                e("sql $host (result cache): ".rtrim($sql)."\n");
                return unserialize(gzuncompress(self::$_result_cache[$md5]));
            }

            if (($data_obj = cache::get($md5, 'array')) && is_array($data_obj))
            {
                e("sql $host (memcached): ".rtrim($sql)."\n");
                return $data_obj;
            }

            $this->execute($sql);
        }

        $rows = array();
        while (($row = mysql_fetch_array($this->rs, MYSQL_NUM)) && $row)
        {
            $j = count($rows);
            $res = array();
            $pri = array();
            foreach ($row as $i => $value)
            {
                $field = $this->rs_fields[$i];
                $res[$field->name] = $value;

                if ($field->type == 'int')
                    $res[$field->name] = intval($value);

                if ($field->primary_key)
                    $pri[] = $value;
            }
            if ($pri) $j = join(':', $pri);
            $rows[$index ? $res[$index]: $j] = $res;
        }

        $this->recache($rows);
        return $rows;
    }

    // retrieve all rows as numerically indexed array
    public function fetch_all_numeric()
    {
        $rows = $this->fetch_all();
        foreach ($rows as &$row) $row = array_values($row);
        return $rows;
    }

    // retrieve a single field from all available rows
    public function fetch_field($name=null)
    {
        $out = array();
        foreach ($this->fetch_all() as $row)
            $out[] = is_null($name) ? array_shift($row): $row[$name];
        return $out;
    }

    // retrieve two fields from all available rows, as a key/val array (key must be UNIQUE or PRIMARY)
    public function fetch_pair($key, $val)
    {
        $out = array();
        foreach ($this->fetch_all() as $row)
            $out[$row[$key]] = $row[$val];
        return $out;
    }

    // retrieve a single row
    public function fetch_one()
    {
        if (!$this->limit) $this->limit(1);
        $rows = $this->fetch_all();
        return $rows ? array_shift($rows): null;
    }

    // retrieve a single row numerically indexed
    public function fetch_one_numeric()
    {
        $row = $this->fetch_one();
        return $row ? array_values($row): $row;
    }

    // retrieve a single value
    public function fetch_value($name=null)
    {
        $row = $this->fetch_one();
        if ($row) {
            if ($name && isset($row[$name])) return $row[$name];
            return array_shift($row);
        }
        return null;
    }

    // retrieve all rows and group by $name
    public function fetch_all_grouped($name)
    {
        $rows = $this->fetch_all();
        $res  = array();
        foreach ($rows as $key => $row)
            $res[$row[$name]][] = $row;
        return $res;
    }

    // wrappers to execute the current state as different queries
    public function select($fields=null) { if ($fields) $this->fields($fields); return $this->execute($this->get_select()); }
    public function delete() { return $this->execute($this->get_delete()); }
    public function insert($set=null, $val=null) { if ($set) $this->set($set, $val); return $this->execute($this->get_insert()); }
    public function update($set=null, $val=null) { if ($set) $this->set($set, $val); return $this->execute($this->get_update()); }
    public function replace($set=null, $val=null) { if ($set) $this->set($set, $val); return $this->execute($this->get_replace()); }
    public function insert_id($set=null, $val=null) { $this->insert($set, $val); return mysql_insert_id(); }

    public function truncate() { return $this->execute('truncate table '.self::quote_name($this->table)); }

    // initializer
    public static function query($table=null, $cache=sql::NOCACHE, $db=null)
    {
        $s = new static($table);
        if ($cache) $s->cache($cache);
        if (!is_null($db)) $s->db($db);
        return $s;
    }

    // initializer
    public static function rawquery($sql=null, $cache=sql::NOCACHE, $db=null)
    {
        $s = new static();
        if ($cache) $s->cache($cache);
        if (!is_null($db)) $s->db($db);
        $s->execute($sql);
        return $s;
    }

    // initializer
    public static function command($command, $db=null)
    {
        $s = new static();
        if ($db) $s->db($db);
        $s->execute($command);
        return $s;
    }
}
