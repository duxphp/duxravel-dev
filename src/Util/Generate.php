<?php

namespace Modules\Dev\Util;


class Generate
{

    private $data = [];


    public function __construct($className)
    {
        $this->data[] = '$' . $className;
    }

    public function method($fun, $params = [], $source = false)
    {
        $arr = [];
        foreach ($params as $vo) {
            if (!is_array($vo)) {
                $arr[] = $source ? $vo : "'" . $vo . "'";
            } else {
                $options = [];
                foreach ($vo as $k => $v) {
                    $options[] = (is_string($k) ? "'$k'" : $k) . " => '$v'";
                }
                $options = implode(', ', $options);
                $arr[] = "[$options]";
            }
        }
        $this->data[] = $fun . '(' . implode(', ', $arr) . ')';
        return $this;
    }

    public function make()
    {
        return implode('->', $this->data) . ';';
    }

}
