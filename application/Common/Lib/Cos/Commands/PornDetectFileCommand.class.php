<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/28
 * Time: 10:39
 */

namespace Common\Lib\Cos\Commands;

use Common\Lib\Cos\Auth;
use Common\Lib\Cos\Http;
use Think\Exception;

/**
 * 图片检黄
 * Class PornDetectFile
 * @package Common\Lib\Cos
 */
class PornDetectFileCommand extends Command
{
    /**
     * 执行函数
     * @param $params 文件数组,格式类似$_FILES
     * @return array|mixed
     */
    public function run($params)
    {
        $sign = Auth::getPornDetectSign($this->appid, $this->sectetid, $this->sectetkey, $this->bucket);
        $options = $this->getPorDetectOptions();

        //如果$params为空，则处理$_FILES数组
        $pornFile = [];
        if (empty($params)) {
            foreach ($_FILES as $file) {
                $pornFile[] = $file;
            }
        } else {
            $pornFile = $params[0];
        }

        if(false === $sign)
        {
            $data = array("code"=>9,
                "message"=>"Secret id or key is empty.",
                "data"=>array());
            return $data;
        }

        $data = ['appid' => $this->appid, 'bucket' => $this->bucket];

        $files = [];

        for($i = 0; $i < count($pornFile); $i++) {
            if(PATH_SEPARATOR==';'){    // WIN OS
                $pornFile[$i]['tmp_name'] = iconv("UTF-8","gb2312",$pornFile[$i]['tmp_name']);
            }
            $srcPath = realpath($pornFile[$i]['tmp_name']);
            if (!file_exists($srcPath)) {
                return array('httpcode' => 0, 'code' => -1, 'message' => 'file ' . $pornFile[$i]['name'] . ' not exists', 'data' => array());
            }

            $files[] = [
                'name' => 'image['.(string)$i.']',
                'filename' => $pornFile[$i]['name'],
                'contents' => file_get_contents($pornFile[$i]['tmp_name']),
                'headers' => ['Content-type: '.$pornFile[$i]['type']]
            ];
        }

        $data = Http::buildCustomPostFields($data, $files);

        $req = [
            'url' => $options['url'],
            'method' => 'post',
            'timeout' => $this->timeout,
            'data' => $data[1],
            'header' => [
                'Authorization: '.$sign,
                'Content-Type: multipart/form-data; boundary=' . $data[0]
            ]
        ];

        $response = json_decode(Http::sendRequest($req), true);
        return $response;
    }
}