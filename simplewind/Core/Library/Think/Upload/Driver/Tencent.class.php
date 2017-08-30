<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/27
 * Time: 15:11
 */
namespace Think\Upload\Driver;

use Think\Upload\Driver\Cos\qcloudcos\Auth;
use Think\Upload\Driver\Cos\qcloudcos\Cosapi;
use Think\Upload\Driver\Tencentyun\Libs\Image;
use Think\Upload\Driver\Tencentyun\Libs\ImageV2;

class Tencent
{
    /**
     * 上传跟目录
     * @var string
     */
    private $rootPath;

    /**
     * 错误信息
     * @var string
     */
    private $error;

    /**
     * 腾讯云配置
     * @var array
     */
    private $config = [
        'appid' => '',
        'secretID' => '',
        'secretKey' => '',
        'end_point' => '',
        'timeout' => 300,
        'bucket' => '',
        'region' => 'gz'
    ];

    function __construct($config = array())
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'Cos/include.php';
        $this->config = array_merge($this->config, $config);
        Cosapi::setRegion($this->config['region']);
    }

    /**
     * 检测上传根目录(腾讯云会自动检测生成目录,直接返回)
     * @param $rootpath
     * @return bool
     */
    public function checkRootPath($rootpath)
    {
        $this->rootPath = trim($rootpath, './') . '/';
        return true;
    }

    /**
     * 检测保存目录(腾讯云会自动检测生成目录,直接返回)
     * @param $savepath
     * @return bool
     */
    public function checkSavePath($savepath)
    {
        return true;
    }

    /**
     * 生成目录(腾讯云会自动检测生成目录,直接返回)
     * @param $savepath
     * @return bool
     */
    public function mkdir($savepath)
    {
        return true;
    }

    /**
     * 保存文件
     * @param $file
     * @param bool $replace
     * @return bool
     */
    public function save(&$file,$replace=true)
    {
        $file['name'] = $this->rootPath . $file['savepath'] . $file['savename'];
        $dstPath = $file['name'];
        $srcPath = $file['tmp_name'];
        $insertOnly = $replace ? 1 : 0;
        $bucket = $this->config['bucket'];
        $result = Cosapi::upload($bucket,$srcPath,$dstPath,'',$insertOnly);
        if (!empty($result['message']) && strtolower($result['message']) != 'success') {
            $this->error = "错误代码[{$result['code']}],{$result['message']}";
            return false;
        }
        $file['data'] = $result['data'];
        return true;
    }

    /**
     * 返回最后一次错误提示
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
}