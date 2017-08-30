<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/28
 * Time: 10:42
 */

namespace Common\Lib\Cos;

function my_curl_reset($handler) {
    curl_setopt($handler, CURLOPT_URL, '');
    curl_setopt($handler, CURLOPT_HTTPHEADER, array());
    curl_setopt($handler, CURLOPT_POSTFIELDS, array());
    curl_setopt($handler, CURLOPT_TIMEOUT, 0);
    curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, 0);
}

class Http
{
    public static $_httpInfo = '';

    public static $_curlHandler;

    /**
     * send http request
     * @param  array $rq http请求信息
     *                   url        : 请求的url地址
     *                   method     : 请求方法，'get', 'post', 'put', 'delete', 'head'
     *                   data       : 请求数据，如有设置，则method为post
     *                   header     : 需要设置的http头部
     *                   host       : 请求头部host
     *                   timeout    : 请求超时时间
     *                   cert       : ca文件路径
     *                   ssl_version: SSL版本号
     * @return string    http请求响应
     */
    public static function sendRequest($rq)
    {
        if(self::$_curlHandler)
        {
            if(function_exists('curl_reset'))
            {
                curl_reset(self::$_curlHandler);
            }
            else {
                my_curl_reset(self::$_curlHandler);
            }
        }
        else
        {
            self::$_curlHandler = curl_init();
        }

        curl_setopt(self::$_curlHandler, CURLOPT_URL, $rq['url']);
        switch (true) {
            case isset($rq['method']) && in_array(strtolower($rq['method']), array('get', 'post', 'put', 'delete', 'head')):
                $method = strtoupper($rq['method']);
                break;
            case isset($rq['data']):
                $method = 'POST';
                break;
            default:
                $method = 'GET';
        }
        $header = isset($rq['header']) ? $rq['header'] : array();
        $header[] = 'Method:'.$method;
        $header[] = 'User-Agent:'.self::getUserAgent();
        isset($rq['host']) && $header[] = 'Host:'.$rq['host'];
        //curl_setopt(self::$_curlHandler, CURLOPT_PROXY, '127.0.0.1:8888');
        curl_setopt(self::$_curlHandler, CURLOPT_HTTPHEADER, $header);
        curl_setopt(self::$_curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(self::$_curlHandler, CURLOPT_CUSTOMREQUEST, $method);
        if (defined('CURLOPT_SAFE_UPLOAD')) {
            curl_setopt(self::$_curlHandler, CURLOPT_SAFE_UPLOAD, false);
        }

        isset($rq['timeout']) && curl_setopt(self::$_curlHandler, CURLOPT_TIMEOUT, $rq['timeout']);
        isset($rq['data']) && in_array($method, array('POST', 'PUT')) && curl_setopt(self::$_curlHandler, CURLOPT_POSTFIELDS, $rq['data']);
        $ssl = substr($rq['url'], 0, 8) == "https://" ? true : false;
        if( isset($rq['cert'])){
            curl_setopt(self::$_curlHandler, CURLOPT_SSL_VERIFYPEER,true);
            curl_setopt(self::$_curlHandler, CURLOPT_CAINFO, $rq['cert']);
            curl_setopt(self::$_curlHandler, CURLOPT_SSL_VERIFYHOST,2);
            if (isset($rq['ssl_version'])) {
                curl_setopt(self::$_curlHandler, CURLOPT_SSLVERSION, $rq['ssl_version']);
            } else {
                curl_setopt(self::$_curlHandler, CURLOPT_SSLVERSION, 4);
            }
        }else if( $ssl ){
            curl_setopt(self::$_curlHandler, CURLOPT_SSL_VERIFYPEER,false);   //true any ca
            curl_setopt(self::$_curlHandler, CURLOPT_SSL_VERIFYHOST,1);       //check only host
            if (isset($rq['ssl_version'])) {
                curl_setopt(self::$_curlHandler, CURLOPT_SSLVERSION, $rq['ssl_version']);
            } else {
                curl_setopt(self::$_curlHandler, CURLOPT_SSLVERSION, 4);
            }
        }
        $ret = curl_exec(self::$_curlHandler);
        self::$_httpInfo = curl_getinfo(self::$_curlHandler);
        // curl_close(self::$_curlHandler);
        return $ret;
    }

    public static function info()
    {
        return self::$_httpInfo;
    }

    public static function buildCustomPostFields($fields, $files = null) {
        // invalid characters for "name" and "filename"
        static $disallow = array("\0", "\"", "\r", "\n");

        // initialize body
        $body = array();

        // build normal parameters
        foreach ($fields as $key => $value) {
            $key = str_replace($disallow, "_", $key);
            $body[] = implode("\r\n", array(
                "Content-Disposition: form-data; name=\"{$key}\"",
                '',
                filter_var($value),
            ));
        }

        if (!empty($files)) {
            foreach ($files as $file) {
                $tmp = [];
                $name = str_replace($disallow, "_", $file['name']);
                if (isset($file['filename'])) {
                    $filename = $file['filename'];
                    array_push($tmp, "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$filename}\"");
                } else {
                    array_push($tmp, "Content-Disposition: form-data; name=\"{$name}\"");
                }
                if (isset($file['headers'])) {
                    foreach ($file['headers'] as $header) {
                        array_push($tmp, $header);
                    }
                }
                array_push($tmp, '');
                array_push($tmp, filter_var($file['contents']));

                array_push($body, implode("\r\n", $tmp));
            }
        }

        // generate safe boundary
        do {
            $boundary = "---------------------" . md5(mt_rand() . microtime());
        } while (preg_grep("/{$boundary}/", $body));

        // add boundary for each parameters
        foreach ($body as &$part) {
            $part = "--{$boundary}\r\n{$part}";
        }
        unset($part);

        // add final boundary
        $body[] = "--{$boundary}--";
        $body[] = '';

        return array($boundary, implode("\r\n", $body));
    }

    protected static function getUserAgent()
    {
        return 'cos-sdk-'.Cosapi::VERSION.'('.php_uname().')';
    }
}