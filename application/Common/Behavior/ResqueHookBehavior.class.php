<?php

namespace Common\Behavior;

use Think\Behavior;

class ResqueHookBehavior extends Behavior
{
    public function run(&$params)
    {
        $config = C('QUEUE');
        if ($config) {
            vendor('php-resque.autoload');
            // 初始化队列服务,使用database(1)
            \Resque::setBackend(['redis' => $config], 0);
            // 初始化缓存前缀
            if(isset($config['prefix']) && !empty($config['prefix']))
                \Resque_Redis::prefix($config['prefix']);
        }
    }
}