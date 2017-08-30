<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/28
 * Time: 11:52
 */

namespace Common\Lib\Cos\Commands;


abstract class Command
{
    protected $appid;

    protected $sectetid;

    protected $sectetkey;

    protected $bucket;

    protected $timeout;

    public function __construct($config)
    {
        $this->appid = $config['APP_ID'];
        $this->sectetid = $config['SECRET_ID'];
        $this->sectetkey = $config['SECRET_KEY'];
        $this->bucket = $config['BUCKET'];
        $this->timeout = $config['TIMEOUT'];
    }

    public function run(){}

    protected function getPorDetectOptions()
    {
        return [
            'url' => 'http://service.image.myqcloud.com/detection/pornDetect'
        ];
    }

    protected function getImageView2Options()
    {
        return [
            'url' => 'http://'.$this->bucket.'-'.$this->appid.'.image.myqcloud.com'
        ];
    }
}