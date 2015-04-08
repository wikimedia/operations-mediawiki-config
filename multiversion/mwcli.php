<?php

class MwCli {
    protected $buffer = array();
    protected $methods = array(

    );

    protected $commands;
    protected $args = array();
    protected $colors = array(
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37'
    );

    function __construct() {
        $methods = array();
        $this->methods['color'] = array_keys($this->colors);
        foreach ($this->methods as $method=>$aliases) {
            foreach($aliases as $alias) {
                $methods[$alias]=$method;
            }
        }
        $this->methods = $methods;
    }

    function cmd($args) {
        $this->args = $args;
        if (isset($args[1])) {
            $cmd_method = "cmd_".$args[1];
            if (method_exists($this, $cmd_method)) {
                $method = new ReflectionMethod(get_class($this), $cmd_method);
                if ($method->isPublic()) {
                    $args = array_slice($args,2);
                    $method->invokeArgs($this, $args);
                }
            }
        }
        return $this;
    }

    function txt($msg) {
        $this->buffer[] = $msg;
        return $this;
    }

    function color($color, $msg="") {
        $this->buffer[] = "\033[".$this->colors[$color]."m";
        if ($msg) {
            $this->txt($msg)->endColor();;
        }
        return $this;
    }

    function endColor() {
        $this->buffer[] = "\033[0m";
        return $this;
    }

    function nl() {
        $this->buffer[] = "\n";
        return $this;
    }

    function error($msg) {
        return $this->color('red',$msg)->nl();
    }

    function __call($name, $args) {
        if (isset($this->methods[$name])) {
            $method = $this->methods[$name];
            array_unshift($args, $name);
            return call_user_func_array(array($this, $method), $args);
        }
    }

    function output() {
        foreach($this->buffer as $element) {
            echo $element;
        }
        $this->buffer = array();
        return $this;
    }
    function record($rec, $pad=" ") {
        $this->txt(join($pad, $rec));
        return $this;
    }
    function __toString() {
        $out = join("",$this->buffer);
        $this->buffer = array();
        return $out;
    }
}
