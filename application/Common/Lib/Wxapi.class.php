<?php
namespace Common\Lib;
use Admin\Controller\EquipmentController;
use Common\Lib\Robot\Robot;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Raw;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Transfer;
use Think\Exception;
use EasyWeChat\Message\Image;

class Wxapi
{
	public $user_id;

	public $user_model;

	public $wx_public_model;

	public $wx_tokensmodel;

    /**
     * EasyWeChat Application
     * @var \EasyWeChat\Foundation\Application
     */
	protected $app;

    /**
     * EasyWeChat 配置
     * @var array
     */
    protected $options = [
        'debug' => true,
        'app_id' => 'wx5d9d0f47fb95f264',
        'secret' => '6d2d46e2c66ff5593c89b23831e7bdb1',
        'aes_key' => '',
        'log' => [
            'level' => 'debug',
            'file'  => '/tmp/easywechat.log'
        ]
    ];

    public function __construct($options = [])
    {
		$this->user_model = D("Common/Users");
		$this->wx_public_model = D("Common/WxPublics");
		$this->wx_tokensmodel = D("Common/WxTokens");

		$this->options = array_merge($this->options, $options);
		//设置默认的日志输出路径
        if (false === isset($this->options['log']['file'])) {
            //            $this->options['log']['file'] = LOG_PATH.'Wechat/'.'wechat-'.date('Y-m-d').'.log';
        }
        $wx_id = $this->options['wx_id'];
        $this->setApp(new Application($this->options));
        $this->getAccessToken($wx_id);
        //        $app = $this->getApp();
        //        D('WxTokens')->add(['wx_id'=>])
	}

    /**
     * 蓝牙设备授权
     * @param $object
     * @param $deviceID
     * @param $deviceMac
     * @param null $cProductID
     * @param null $cWxID
     * @param null $isAddData
     * @return array
     */
	public function bluetoothBind($object, $deviceID, $deviceMac, $cProductID = NULL, $cWxID = NULL, $isAddData = NULL)
    {
		if (! isset ( $cProductID ) || $cProductID == "") {
			$cProductID = "8081";
		}
		if (( int ) $cWxID <= 0) {
			// 			$cWxID = $this->user_id;
		}
		if (isset ( $deviceID ) && ! empty ( $deviceID ) && isset ( $deviceMac ) && ! empty ( $deviceMac )) {
			$data = array (
					"device_num" => "1",
					"device_list" => array (
							0 => array (
									"id" => $deviceID,
									"mac" => $deviceMac,
									"connect_protocol" => "3",
									"auth_key" => "",
									"close_strategy" => "1",
									"conn_strategy" => "16",
									"crypt_method" => "0",
									"auth_ver" => "0",
									"manu_mac_pos" => "-1",
									"ser_mac_pos" => "-2"
							)
					),
					"op_type" => "0",
					"product_id" => $cProductID
			);
			$postJosnData = json_encode ( $data );
			$result = $this->httpPost ( "https://api.weixin.qq.com/device/authorize_device?access_token=" . $this->getAccessToken ( $cWxID ), $postJosnData );
			$jsonResult = json_decode ( $result, true );
			if (array_key_exists ( "resp", $jsonResult )) {
				$equiment = new EquipmentController();
				if ($jsonResult ["resp"] [0] ["errcode"] == 0) {
					if($isAddData){
						if(!$equiment->addDevices($isAddData)){
							return array(
									'errcode'=>-100,
									'errmsg'=>"添加失败"
							);
							exit();
						}
					}
					return array(
							'errcode'=>0,
							'errmsg'=>"绑定成功"
					);
				} elseif (( int ) $jsonResult ["resp"] [0] ["errcode"] == 100002) {
					if($isAddData){
                        if(!$equiment->addDevices($isAddData)){
							return array(
									'errcode'=>-100,
									'errmsg'=>"添加失败"
							);
							exit();
						}
					}
                    
					$data = array (
							"device_num" => "1",
							"device_list" => array (
									0 => array (
											"id" => $deviceID,
											"mac" => $deviceMac,
											"connect_protocol" => "3",
											"auth_key" => "",
											"close_strategy" => "1",
											"conn_strategy" => "16",
											"crypt_method" => "0",
											"auth_ver" => "0",
											"manu_mac_pos" => "-1",
											"ser_mac_pos" => "-2"
									)
							),
							"op_type" => "1"
					);
					$postJosnData = json_encode ( $data );
					$result = $this->httpPost ( "https://api.weixin.qq.com/device/authorize_device?access_token=" . $this->getAccessToken (), $postJosnData );
					return array(
							'errcode'=>-100,
							'errmsg'=>"设备已经存在"
					);
				} else {
					return array(
							'errcode'=>-100,
							'errmsg'=>"绑定失败_" . $jsonResult ["resp"] [0] ["errcode"] . "_" . $jsonResult ["resp"] [0] ["errmsg"]
					);
				}
			} else {
				return array(
						'errcode'=>-100,
						'errmsg'=>"绑定失败_" . $jsonResult->errcode . "_" . $jsonResult->errmsg
				);
			}
		}
		return array(
				'errcode'=>-100,
				'errmsg'=>"参数错误"
		);
	}
	
	/**
     * Summary of bluetoothBind2
     * @param mixed $deviceID 
     * @param mixed $deviceMac 
     * @param mixed $productID 
     * @param mixed $wxID 
     * @return mixed
     */
	public function bluetoothBind2($deviceID, $deviceMac, $productID, $wxID){
		$arrReturn = array(
				"errcode" => 0,
				"errmsg" => ''
		);
		if(isset($deviceID) && ! empty($deviceID) && isset($deviceMac) && ! empty($deviceMac)) {
			$token = $this->getAccessToken($wxID);
            $data = array(
                    "device_num" => "1",
                    "device_list" => array(
                            0 => array(
                                    "id" => $deviceID,
                                    "mac" => $deviceMac,
                                    "connect_protocol" => "3",
                                    "auth_key" => "",
                                    "close_strategy" => "1",
                                    "conn_strategy" => "16",
                                    "crypt_method" => "0",
                                    "auth_ver" => "0",
                                    "manu_mac_pos" => "-1",
                                    "ser_mac_pos" => "-2"
                            )
                    ),
                    "op_type" => "0",
                    "product_id" => $productID
            );
            
            $postJosnData = json_encode($data);
            $result = $this->httpPost("https://api.weixin.qq.com/device/authorize_device?access_token=" . $token, $postJosnData);
            $jsonResult = json_decode($result, true);
            
            if(array_key_exists("resp", $jsonResult)) {
                if($jsonResult["resp"][0]["errcode"] == 0) {
                    D('WxProducts')->update_residue($wxID,$productID);
                    $arrReturn["errmsg"] = "绑定成功";
                } elseif((int)$jsonResult["resp"][0]["errcode"] == 100002) {
                    $data = array(
                            "device_num" => "1",
                            "device_list" => array(
                                    0 => array(
                                            "id" => $deviceID,
                                            "mac" => $deviceMac,
                                            "connect_protocol" => "3",
                                            "auth_key" => "",
                                            "close_strategy" => "1",
                                            "conn_strategy" => "16",
                                            "crypt_method" => "0",
                                            "auth_ver" => "0",
                                            "manu_mac_pos" => "-1",
                                            "ser_mac_pos" => "-2"
                                    )
                            ),
                            "op_type" => "1"
                    );
                    $postJosnData = json_encode($data);
                    $result = $this->httpPost("https://api.weixin.qq.com/device/authorize_device?access_token=" . $token, $postJosnData);
                    
                    $arrReturn["errmsg"] = "设备已经存在";
                } else {
                    $arrReturn["errmsg"] = $jsonResult["resp"][0]["errmsg"];
                    $arrReturn["errcode"] = $jsonResult["resp"][0]["errcode"];
                }
            } else {
                $arrReturn["errmsg"] = $jsonResult->errmsg;
                $arrReturn["errcode"] = $jsonResult->errcode;
            }
		} else {
			$arrReturn["errcode"] = - 1;
			$arrReturn["errmsg"] = "参数错误";
		}
		return $arrReturn;
	}
	
	
	/**
     *  获取AccessToken
     *  @param $cWxID int 微信公众号ID
     *  @return string
     */
	public function getAccessToken($cWxID = NULL) {
        
        //		$arrData = $this->wx_tokensmodel->getDataWithToken ( $cWxID );
        //		if (count ( $arrData ) > 0) {
        //			if (( int ) $arrData['token_id'] <= 0 || strtotime ( $arrData['update_time'] ) < time()) {
        //				$arrData = $this->getNewAccessToken ( $arrData );
        //				$arrData = $this->wx_tokensmodel->getDataWithToken ( $cWxID );
        //			}
        //		} else {
        //			$arrData = $this->wx_tokensmodel->getDataWithToken ();
        //			if (( int ) $arrData['token_id'] <= 0 || strtotime ( $arrData['update_time'] ) < time()) {
        //				$arrData = $this->getNewAccessToken ( $arrData );
        //				$arrData = $this->wx_tokensmodel->getDataWithToken ( $cWxID );
        //			}
        //		}
        //
        //		return $arrData['token'];
        if (empty($cWxID)) {
            return false;
        }
        $wx = $this->wx_public_model->find($cWxID);
        $app = new Application([
            'debug' => false,
            'app_id' => $wx['appid'],
            'secret' => $wx['appsecret'],
            'aes_key' => ''
        ]);
        $token = $app->access_token->getToken();
        $hist = $this->wx_tokensmodel->where(['wx_id' => $cWxID])->find();
        if ($hist) {
            $this->wx_tokensmodel->where(['wx_id' => $cWxID])->save(['token'=>$token,'update_time'=>time()]);
        } else {
            $this->wx_tokensmodel->add(['wx_id'=>$cWxID, 'token'=>$token, 'type'=>1,'update_time'=>time()]);
        }
        return $token;
	}

    /**
     * 获取所有模板列表
     * @return \EasyWeChat\Support\Collection
     */
    public function getPrivateTemplates()
    {
        return $this->app->notice->getPrivateTemplates();
    }

    /**
     * 添加模板
     * @param $short_id
     * @return array|\EasyWeChat\Support\Collection
     */
    public function addTemplate($short_id)
    {
        try{
            if (empty($short_id))
                throw new Exception('缺少模板编号');
            $result = $this->getApp()->notice->addTemplate($short_id);
        }
        catch (\Exception $e) {
            $result = [
                'errcode' => - 100,
                'errmsg' => "获取模板ID失败"
            ];
        }
        return $result;
    }

    /**
     * 删除模板消息
     * @param $template_id
     * @return bool|\EasyWeChat\Support\Collection
     */
    public function deletePrivateTemplate($template_id)
    {
        if (empty($template_id)) return false;
        $result = $this->app->notice->deletePrivateTemplate($template_id);
        return $result;
    }

    /**
     * 发送模板消息
     *  $message = [
     *      'touser' => 'touser',
     *      'template_id' => 'template_id',
     *      'topcolor' => '#d3d3d3',
     *      'data' => [
     *          'first' => 'Test',
     *          'keyword1' => ['123', '#dddddd'],
     *          'keyword2' => '123',
     *          'keyword3' => '123123',
     *          'keyword4' => '123123123213',
     *          'keyword5' => '123132131321321313131313112',
     *          'remark' => 'Test,Test'
     *      ]
     *  ]
     * @param $message 信息数组
     * @return \EasyWeChat\Support\Collection|void
     */
    public function sendTemplateMessage($message)
    {
        $result = $this->app->notice->send($message);
        return $result;
    }

    /**
     * 生成菜单
     * @param $buttons
     * @param array $matchRule
     * @return bool
     */
    public function generateMenu($buttons, $matchRule = [])
    {
        if (empty($buttons)) return false;
        $result = $this->app->menu->add($buttons,$matchRule);
        return $result;
    }

    /**
     * 删除菜单
     * @param int $menuId
     * @return bool
     */
    public function destroyMenu($menuId = 0)
    {
        $menu = $this->app->menu;
        $result = $menu->destroy($menuId);
        return $result;
    }

    /**
     * 获取公众号所有用户openid
     * @return array
     */
    public function openids()
    {
        $open_ids = [];
        $next_openid = null;

        do{
            $lists = $this->app->user->lists($next_openid);
            $openids = $lists['data']['openid'];
            if (isset($lists['data']))
                $open_ids = array_merge($open_ids, $openids);

            if ((!isset($lists['total']) AND !isset($lists['count'])) OR $lists['total'] == $lists['count'])
                $next_openid = false;
            else
                $next_openid = isset($lists['next_openid']) && $lists['next_openid'] != '' ? $lists['next_openid'] : false;

        }while($next_openid);

        return $open_ids;
    }

    /**
     * 获取所有用户信息
     * @return \EasyWeChat\Support\Collection
     */
    public function userInfos()
    {
        $openids = $this->openids();
        $users = $this->app->user->batchGet($openids);
        return $users;
    }

    /**
     * 获取单个用户信息
     * @param $openid
     * @return array
     */
    public function oneUserInfo($openid)
    {
        $userinfo = $this->getApp()->user->get($openid);
        return $userinfo;
    }
    
    /**
     * 随机生成数
     * @param int $len 长度
     * @param string $format 格式
     * @return string
     */
    public function randpw($len=8,$format='ALL'){
    	$is_abc = $is_numer = 0;
    	$password = $tmp ='';
    	switch($format){
    		case 'ALL':
    			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    			break;
    		case 'CHAR':
    			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    			break;
    		case 'NUMBER':
    			$chars='23';//'0123456789';
    			break;
    		default :
    			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    			break;
    	} 
    	mt_srand((double)microtime()*1000000*getmypid());
    	while(strlen($password)<$len){
    		$tmp =substr($chars,(mt_rand()%strlen($chars)),1);
    		if(($is_numer <> 1 && is_numeric($tmp) && $tmp > 0 )|| $format == 'CHAR'){
    			$is_numer = 1;
    		}
    		if(($is_abc <> 1 && preg_match('/[a-zA-Z]/',$tmp)) || $format == 'NUMBER'){
    			$is_abc = 1;
    		}
    		$password.= $tmp;
    	}
    	if($is_numer <> 1 || $is_abc <> 1 || empty($password) ){
    		$password = randpw($len,$format);
    	}
    	return $password;
    }

    /**
     * 绑定设备
     * @param $openid
     * @param $ticket
     * @param $deviceid
     * @return array
     */
    public function bindDevice($openid,$ticket,$deviceid)
    {
        return $this->getApp()->device->bind($openid,$deviceid,$ticket)->toArray();
    }
    


	// 获取新的AccessToken
	protected function getNewAccessToken($cArrData) {
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $cArrData['appid'] . "&secret=" . $cArrData['appsecret'];
		$res = json_decode ( $this->httpGet ( $url ) );
        
		$cArrData['token'] = (isset($res->access_token) && $res->access_token != '') ? $res->access_token : '';
		$this->saveToken ( ( int ) $cArrData['token_id'], ( int ) $cArrData['wx_id'], $cArrData['token'], 1 );
        
		return $cArrData;
	}
	
	/**
     * 保存token资料
     */ 
	protected function saveToken($cTokenID, $cWxID, $cToken, $cType) {
		$arrData = array (
				"wx_id" => $cWxID,
				"type" => $cType,
				"token" => $cToken,
				"update_time" => strtotime ( '+1000 second' )
		);
        
		if (( int ) $cTokenID > 0) {
			$this->wx_tokensmodel->data( $arrData)->where(array ( "token_id" => $cTokenID ))->save();
		} else {
			$this->wx_tokensmodel->data( $arrData )->add();
		}
	}
	
    /* 上传多媒体文件 */
    public function uploadFile($file, $type = 'image', $wx_id) {
    	//  $post_data ['type'] = $type; // 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
//     	$post_data ['media']  = '@'.$file;
//     	$post_data ['media']  = new \CURLFile($file);
    	$url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=".$this->getAccessToken($wx_id)."&type=".$type;
//     	$output=$this->httpPost($url, $post_data);
		$output = $this->uploadPost($file, $url);
    	D('Log')->data(['cat'=>'wx_uploadfile', 'log'=>$output])->add();
    	return $output;
    }
    
    public function uploadPost($media_file,$url){
    	if (!empty($media_file)) {
    		$data = [];
    		foreach ($media_file as $file) {
    			$data[] = $file;
    		}
    	
    		list($name, $file_type, $tmp_name) = $data;
    		$post_data = [
    				'media' => curl_file_create($tmp_name, $file_type, $name),
    		];
    	
    		$curl   = new \Curl();
    		$result = $curl->post($url, $post_data);
    		$result = json_decode($result, true);
    	
    		return $result;
    	}
    }

	// 发送请求(post)
	public function httpPost($url, $data) {
        
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, false);
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        
		$tmpInfo = curl_exec ( $ch );
        
		if (curl_errno ( $ch )) {
			return curl_error ( $ch );
		}
        
		curl_close ( $ch );
		return $tmpInfo;
	}
	
	// 发送请求(get)
	protected function httpGet($url) {
        // 		$this->logger ( $url, "wx_httpGet_url" );
		$ch = curl_init ();
		$header [] = "Accept-Charset: utf-8";
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "GET" );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$temp = curl_exec ( $ch );
        // 		$this->logger ( $temp, "wx_httpGet_result" );
		return $temp;
	}
	
	/**
     * xml格式转为数组
     * @param xml $xml
     */
	function xml_to_array($xml) {
		$reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
		if (preg_match_all ( $reg, $xml, $matches )) {
			$count = count ( $matches [0] );
			$arr = array ();
			for($i = 0; $i < $count; $i ++) {
				$key = $matches [1] [$i];
				$val = $this->xml_to_array ( $matches [2] [$i] ); // 递归
				if (array_key_exists ( $key, $arr )) {
					if (is_array ( $arr [$key] )) {
						if (! array_key_exists ( 0, $arr [$key] )) {
							$arr [$key] = array (
									$arr [$key]
							);
						}
					} else {
						$arr [$key] = array (
								$arr [$key]
						);
					}
					$arr [$key] [] = $val;
				} else {
					$arr [$key] = $val;
				}
			}
			return $arr;
		} else {
			return $xml;
		}
	}
	
	/**
     * 组合数据
     * @param array $urlObj 访问接口的参数数组
     */
	public function ToUrlParams($urlObj) {
		$buff = "";
		foreach ( $urlObj as $k => $v ) {
			if ($k != "sign") {
				$buff .= $k . "=" . $v . "&";
			}
		}
        
		$buff = trim ( $buff, "&" );
		return $buff;
	}

    /**
     * @return Application
     */
    public function getApp()
    {
        if (is_null($this->app)) {
            $this->app = new Application($this->options);
        }

        return $this->app;
    }

    /**
     * @param Application $app
     * @return $this
     */
    public function setApp(Application $app)
    {
        $this->app = $app;

        return $this;
    }

    public function receiveEvent($message)
    {
        $event = $message->Event;
        switch ($event) {

        }
    }

    /**
     * 接受文本消息
     * @param $message
     * @return array|News|Raw|Text|Transfer|null
     */
    public function receiveText($message, $wx_public)
    {
        F('wx_text_key', 100000);
        //        $wx_original_id = $message->ToUserName;
        //        $wx_public = $this->wx_public_model->where(['wx_original_id' => $wx_original_id])->find();

        $keyword = trim ( $message->Content );
        if (strstr ( $keyword, "请问在吗" ) || strstr ( $keyword, "在线客服" )) {
            return new Transfer();
        }

        $object = null;
        //        if (strstr ( $keyword, "文本" )) {
        //            $object = new Text();
        //            $object->content = '这是个文本消息';
        //        } else if (strstr ( $keyword, "表情" )) {
        //            $object = new Text();
        //            $object->content = "中国：" . $this->bytes_to_emoji ( 0x1F1E8 ) . $this->bytes_to_emoji ( 0x1F1F3 ) . "\n仙人掌：" . $this->bytes_to_emoji ( 0x1F335 );
        //        } else if (strstr ( $keyword, "单图文" )) {
        //            $object = new News([
        //                "title" => "单图文标题",
        //                "description" => "单图文内容",
        //                "image" => "http://discuz.comli.com/weixin/weather/icon/cartoon.jpg",
        //                "url" => "http://m.cnblogs.com/?u=txw1958"
        //            ]);
        //        } else if (strstr ( $keyword, "图文" ) || strstr ( $keyword, "多图文" )) {
        //            $new1 = new News([
        //                "title" => "多图文1标题",
        //                "description" => "",
        //                "image" => "http://discuz.comli.com/weixin/weather/icon/cartoon.jpg",
        //                "url" => "http://m.cnblogs.com/?u=txw1958"
        //            ]);
        //            $new2 = new News([
        //                "title" => "多图文2标题",
        //                "description" => "",
        //                "image" => "http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg",
        //                "url" => "http://m.cnblogs.com/?u=txw1958"
        //            ]);
        //            $new3 = new News([
        //                "title" => "多图文3标题",
        //                "description" => "",
        //                "image" => "http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg",
        //                "url" => "http://m.cnblogs.com/?u=txw1958"
        //            ]);
        //            $object = [$new1, $new2, $new3];
        //        } else if (strstr ( $keyword, "音乐" )) {
        //            $content = [
        //                "Title" => "最炫民族风",
        //                "Description" => "歌手：凤凰传奇",
        //                "MusicUrl" => "http://121.199.4.61/music/zxmzf.mp3",
        //                "HQMusicUrl" => "http://121.199.4.61/music/zxmzf.mp3"
        //            ];
        //            $object = new Raw($this->transmitMusic($message, $content));
        //        } else if (strstr($keyword, '1024')) {
        //            F('wx_text_key', 102400);
        //            $object = new Text();
        //            $object->content = '小姐姐为你服务'.F('wx_text_key');
        //        } else {
        //            // $content = date ( "Y-m-d H:i:s", time () ) . "\nOpenID：" . $object->FromUserName . "\n技术支持 江门智慧社区";
        //            $object = new Text();
        //            $object->content = date ( "Y-m-d H:i:s", time () );
        //        }
        $wx_response = D('WxResponse')->where(['wx_id'=>$wx_public['wx_id'],'state'=>1,'key'=>['like','%,'.$keyword.',%']])->find();
        //        return json_encode($wx_response);
        if (!empty($wx_response)) {
            $type = (int)$wx_response['type'];
            switch ($type) {
                case 1:
                	$object = $wx_response['response'];
                	break;
                case 2:
                	$response = json_decode($wx_response['response'],true);
                    $object = new Image(['media_id' => $response['media_id']]);
                    break;
                case 3:
                    $object = new News(json_decode($wx_response['response'], true));
                    break;
                default:
                    break;
            }
        } else {
            if ($keyword == 'get_openid') {
                $object = $message->FromUserName;
            }else{
            	$object = $wx_public['response_auto'];
            }
        }

        return $object;
    }

    public function receiveRobot($message)
    {
        $wx_original_id = $message->ToUserName;
        $keyword = trim ( $message->Content );
        $result = Robot::Go($keyword, $wx_original_id);
        switch ((int)$result['code']) {
            case Robot::TEXT:
                $object = new Text();
                $object->content = $result['text'];
                break;
            case Robot::LINK:
                $object = new News([
                    "title" => $result['text'],
                    "description" => "",
                    "image" => '',
                    "url" => $result['url']
                ]);
                break;
            case Robot::NEWS:
                //                $object = [];
                //                foreach ($result['list'] as $list) {
                //                    $object[] = new News([
                //                        "title" => $list['article'],
                //                        "description" => $list['article'],
                //                        "image" => $list['icon'],
                //                        "url" => $list['detailurl']
                //                    ]);
                //                }
                $object = new Text();
                $object->content = 123;
                break;
            case Robot::COOK:
                $object = 'haha';
                break;
            default:
                $object = new Text();
                $object->content = '老娘不知道你说什么!';
                break;
        }

        return $object;
    }

    protected function bytes_to_emoji($cp)
    {
        if ($cp > 0x10000) { // 4 bytes
            return chr ( 0xF0 | (($cp & 0x1C0000) >> 18) ) . chr ( 0x80 | (($cp & 0x3F000) >> 12) ) . chr ( 0x80 | (($cp & 0xFC0) >> 6) ) . chr ( 0x80 | ($cp & 0x3F) );
        } else if ($cp > 0x800) { // 3 bytes
            return chr ( 0xE0 | (($cp & 0xF000) >> 12) ) . chr ( 0x80 | (($cp & 0xFC0) >> 6) ) . chr ( 0x80 | ($cp & 0x3F) );
        } else if ($cp > 0x80) { // 2 bytes
            return chr ( 0xC0 | (($cp & 0x7C0) >> 6) ) . chr ( 0x80 | ($cp & 0x3F) );
        } else { // 1 byte
            return chr ( $cp );
        }
    }

    protected function startWith($str, $needle)
    {
        return strpos ( $str, $needle ) === 0;
    }

    /**
     * JSSDK签名包
     */
    public function getSignPackage()
    {
        return $this->app->js->getConfigArray();
    }

    public function transmitMusic($object, $musicArray)
    {
        if (! is_array ( $musicArray )) {
            return "";
        }
        $itemTpl = "<Music>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <MusicUrl><![CDATA[%s]]></MusicUrl>
        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
    </Music>";

        $item_str = sprintf ( $itemTpl, $musicArray ['Title'], $musicArray ['Description'], $musicArray ['MusicUrl'], $musicArray ['HQMusicUrl'] );

        $xmlTpl = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[music]]></MsgType>
		$item_str
		</xml>";

        $result = sprintf ( $xmlTpl, $object->FromUserName, $object->ToUserName, time () );
        return $result;
    }
    
    // 发红包
    public function sendredpack($data){
        return $this->app->lucky_money->sendNormal($data);
    }
}