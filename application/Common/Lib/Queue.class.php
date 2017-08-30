<?php

namespace Common\Lib;


class Queue
{

    protected static $host = [
        'test'    => 'gd-hc.net',
        'release' => 'gd-hc.com.cn',
        'uat'     => 'uat.gd-hc.com.cn',
        'default' => 'localhost/hcpmsv2'
    ];

    /**
     * 入栈
     * @param $class
     * @param array $args
     * @param null $callback
     * @return string|void
     */
    public static function push($class, $args = [], $callback = null)
    {
        $hist_model = M('JobHistory');
        $config = C('QUEUE');
        if (!$config) {
            return;
        }

        $class = '\\Common\\Jobs\\' . $class;

        $http = is_ssl() ? 'https://' : 'http://';
        $url  = str_replace('www.', '', $_SERVER['HTTP_HOST']) ? : self::$host[C('ENV')];
        $args = array_merge($args, ['host' => $http . $url]);

        $jobid = \Resque::enqueue('default', $class, $args, true);
        $hist_model->add([
            'job_id' => $jobid,
            'state' => 1,
            'class_name' => $class,
            'args' => json_encode($args),
            'last_execution_time' => time(),
            'create_time' => time(),
            'create_user_id' => sp_get_current_admin_id(),
            'update_time' => time(),
            'update_user_id' => sp_get_current_admin_id()
        ]);

        if (is_callable($callback)) {
            $callback($jobid);
        } else {
            return $jobid;
        }
    }

    /**
     * 出栈
     * @param $class
     * @param array $args
     */
    public static function pop($class, $args = [])
    {
        $config = C('QUEUE');
        if (!$config) {
            return;
        }

        $class = '\\Common\\Jobs\\' . $class;
        \Resque::dequeue('default', [$class => $args]);
    }
}