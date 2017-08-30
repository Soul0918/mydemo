<?php

namespace Common\Controller;


use Common\Lib\Wxapi;

class WechatbaseController extends HomebaseController
{

    /**
     * @var Wxapi
     */
    protected $wx;

    protected $token;

    protected $options;

    protected $wx_public;

    protected $no_need_oauth = ['wapindexindex','wapindexoauthcallback','wapindextest','wapindexauthsuccess', 'wapindexshare'];

    public function __construct()
    {
        parent::__construct();
    }

    function _initialize()
    {
        parent::_initialize();

        $this->token = I('request.token', false);
        $wx_public_id = I('request.wx');
        if ($this->token OR array_key_exists('token', $_REQUEST)) {
            if (is_null($this->wx_public)) {
                $map['token'] = $this->token;
                $map['wx_id'] = $this->token;
                $map['_logic'] = 'OR';
                $this->wx_public = D('WxPublics')->where($map)->find();
            }
        }

        if ($wx_public_id OR array_key_exists('wx', $_REQUEST)) {
            if (is_null($this->wx_public)) {
                $map['token'] = $wx_public_id;
                $map['wx_id'] = $wx_public_id;
                $map['_logic'] = 'OR';
                $this->wx_public = D('WxPublics')->where($map)->find();
                $this->token = $this->wx_public['token'];
            }
        }

        if (is_null($this->wx_public)) {
            $this->wx_public= D('WxPublics')->where(['is_default' => 1])->find();
            $this->token = $this->wx_public['token'];
        }

        $this->options = [
            'debug' => false,
            'wx_id' => $this->wx_public['wx_id'],
            'app_id' => $this->wx_public['appid'],
            'secret' => $this->wx_public['appsecret'],
            'token' => $this->token,
            'oauth' => [
                'scopes' => ['snsapi_userinfo'],
                'callback' => U('Wap/Index/OauthCallback',['wx'=>$this->wx_public['wx_id']])
            ],
        ];

        $this->wx = new Wxapi($this->options);

        if (!in_array(strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME), $this->no_need_oauth, true)) {
            $app = $this->wx->getApp();
            $oauth = $app->oauth;
            $http = is_ssl() ? 'https://' : 'http://';
            $url = $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            api_wxid($this->wx_public['wx_id']);
            $type = I('get.type');
            if (!empty($type)) {
                session('target_url', U('wap/index/'.$type,$_GET,true,true));
            } else {
                session('target_url', U('wap/index/authsuccess','',true,true));
            }
//            print_r(session());
//            print_r($_GET);
//            if (ACTION_NAME == 'share') {
//                session('target_url', $url);
//            } else {
//                session('target_url', U('wap/index/authsuccess','',true,true));
//            }
            return $oauth->redirect()->send();
        }
    }

    /**
     * @return Wxapi
     */
    public function getWx()
    {
        if (is_null($this->wx)) {
            $this->wx = new Wxapi();
        }

        return $this->wx;
    }

    /**
     * @param mixed $wx
     */
    public function setWx($wx)
    {
        $this->wx = $wx;
    }
}