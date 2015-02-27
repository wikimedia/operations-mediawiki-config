<?php

class Tree
{
    private $hosts    = array();
    private $qps      = array();
    private $versions = array();
    private $replag   = array();
    private $replink  = array();

    // <fqdn>:<port>
    private function node_instance($host_id)
    {
        $host = $this->hosts[$host_id];
        return sprintf('%s:%d', escape($host['host']), $host['port']);
    }

    // <hostname>
    private function node_describe($host_id)
    {
        $host = $this->hosts[$host_id];
        list ($name) = explode('.', $host['host']);
        return escape($name);
    }

    private function node_detail($host_id)
    {
        $host = $this->hosts[$host_id];

        $version = expect($this->versions, $host_id, 'str', '-');

        if (preg_match_all('/^([0-9]+.[0-9]+.[0-9]+)/', $version, $matches))
        {
            $version = $matches[1][0];
        }

        $lag = expect($this->replag, $host_id, 'int', 0);
        $qps = expect($this->qps,    $host_id, 'int', 0);

        $link_host = sprintf('https://tendril.wikimedia.org/host/view/%s/%d', $host['host'], $host['port']);

        $html = tag('a', array(
            'href'  => $link_host,
            'html'  => $this->node_describe($host_id),
            'title' => $this->node_instance($host_id),
        ))
        .tag('div', array(
            'class' => 'lag '.($lag > 60 ? 'lagging': 'insync'),
            'html'  => sprintf('Lag %d', $lag),
        ))
        .tag('div', array(
            'class' => 'qps',
            'html'  => sprintf('QPS %d', $qps),
        ))
        .tag('div', array(
            'class' => 'ver',
            'html'  => escape($version),
        ));

        return $html;
    }

    private function tree_recurse($master_id, $master_name, &$cluster)
    {
        if (isset($this->replink[$master_id]))
        {
            foreach ($this->replink[$master_id] as $slave_id)
            {
                $cluster[] = array(
                    array(
                        'v' => $this->node_instance($slave_id),
                        'f' => $this->node_detail($slave_id),
                    ),
                    $master_name,
                );
                $this->tree_recurse($slave_id, $this->node_instance($slave_id), $cluster);
            }
        }
    }

    public function generate()
    {
        $this->hosts = sql::query('tendril.servers')
            ->where_not_regexp('host', '^(dbstore|labsdb|db1069)')
            ->fetch_all();

        $this->qps = sql::query('tendril.global_status_log gsl')
            ->fields(array(
                'srv.id',
                'floor((max(value)-min(value))/(unix_timestamp(max(stamp))-unix_timestamp(min(stamp)))) as qps',
            ))
            ->join('tendril.strings str', 'gsl.name_id = str.id')
            ->join('tendril.servers srv', 'gsl.server_id = srv.id')
            ->where_in('srv.id', keys($this->hosts))
            ->where_eq('str.string', 'questions')
            ->where('gsl.stamp > now() - interval 10 minute')
            ->group('server_id')
            ->fetch_pair('id', 'qps');

        $this->versions = sql::query('tendril.global_variables')
            ->fields('server_id, variable_value')
            ->where_in('server_id', keys($this->hosts))
            ->where_eq('variable_name', 'version')
            ->fetch_pair('server_id', 'variable_value');

        $this->replag = sql::query('tendril.slave_status a')
            ->join('tendril.slave_status b', 'a.server_id = b.server_id')
            ->fields('a.server_id, a.variable_value')
            ->where_in('a.server_id', keys($this->hosts))
            ->where_eq('a.variable_name', 'seconds_behind_master')
            ->where_eq('b.variable_name', 'slave_sql_running')
            ->where_eq('b.variable_value', 'Yes')
            ->fetch_pair('server_id', 'variable_value');

        $roots = sql::query('tendril.shards')
            ->where_eq('display', 1)
            ->order('place')
            ->fetch_pair('name', 'master_id');

        $this->replink = sql::query('tendril.servers m')
            ->join('tendril.replication r', 'm.id = r.master_id')
            ->join('tendril.servers s', 'r.server_id = s.id')
            ->fields(array(
                'm.id as master_id',
                'count(*) as size',
                'group_concat(s.id order by s.host) as slave_ids'
            ))
            ->where_in('m.id', keys($this->hosts))
            ->where_in('r.server_id', keys($this->hosts))
            ->group('m.id')
            ->order('size', 'desc')
            ->order('m.host', 'asc')
            ->fetch_pair('master_id', 'slave_ids');

        foreach ($this->replink as $master_id => $slave_ids)
        {
            $this->replink[$master_id] = expect($this->replink, $master_id, 'csv', array());
        }

        $clusters = array();

        foreach ($roots as $shard => $master_id)
        {
            $cluster = array(
                array(
                    array(
                        'v' => $this->node_instance($master_id),
                        'f' => $this->node_detail($master_id),
                    ),
                    $shard,
                )
            );

            $this->tree_recurse($master_id, $this->node_instance($master_id), $cluster);

            $clusters[] = $cluster;
        }

        return array( $clusters );
    }
}