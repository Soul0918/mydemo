<?php


namespace Common\Lib\Sms;

/**
 *
 * @package Common\Lib\Sms
 * @example Sms::setMobile(13267698663) Sms::register()
 * @version v0.01
 */
class Sms
{
    const SMS_NOTICES = 1;
    const SMS_REPAIRS = 2;
    const SMS_COMPLAINTS = 3;
    const SMS_PAY = 4;
    const SMS_O2O = 5;
    const SMS_REGISTER_WX = 6;
    const SMS_RESIGTER_APP = 7;

    /**
     * @var SmsSingleSender
     */
    protected static $singleSender;

    /**
     * @var SmsMark
     */
    protected static $mark = '';

    /**
     * @var string
     */
    protected static $nationcode;

    /**
     * @var string
     */
    protected static $mobile;

    /**
     * @return mixed
     */
    public static function getNationcode()
    {
        if (is_null(self::$nationcode)) {
            self::$nationcode = '86';
        }

        return self::$nationcode;
    }

    /**
     * @param mixed $nationcode
     */
    public static function setNationcode($nationcode)
    {
        self::$nationcode = $nationcode;
    }

    /**
     * @return mixed
     */
    public static function getMobile()
    {
        return self::$mobile;
    }

    /**
     * @param mixed $mobile
     */
    public static function setMobile($mobile)
    {
        self::$mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public static function getMark()
    {
        return self::$mark;
    }

    /**
     * @param mixed $mark
     */
    public static function setMark($mark)
    {
        self::$mark = $mark;
    }

    /**
     * @return mixed|SmsSingleSender
     */
    public static function getSingleSender()
    {
        if (is_null(self::$singleSender)) {
            $options = get_site_options();
            if (isset($options['sms_app_id']) && isset($options['sms_app_key'])) {
                $appid              = $options['sms_app_id'];
                $appkey             = $options['sms_app_key'];
                $singleSender       = new SmsSingleSender($appid, $appkey);
                self::$singleSender = $singleSender;
            }
        }

        return self::$singleSender;
    }

    /**
     * 发送注册验证码
     * @return array|bool|mixed
     */
    public static function register()
    {
        if (sp_is_weixin()) {
            $type = self::SMS_REGISTER_WX;
        } else {
            $type = self::SMS_RESIGTER_APP;
        }

        $code          = sp_random_code();
        $time          = '10'; //分钟
        $rsp           = self::SendSingleSender(9464, [$code, $time], $type, '0', '0', function ($result) use ($code) {
            $mobile = self::getMobile();
            if (intval($result['result']) == 0) {
                session(md5($mobile), ['code' => $code, 'expire' => time() + 10 * 60]);
            }
        });
        $rsp['errmsg'] = error_msg($rsp['result']);

        return $rsp;
    }

    /**
     * 发送成功成为小区管理员的密码
     * @return array|bool|mixed
     */
    public static function userpassword($name, $pw)
    {
        if (sp_is_weixin()) {
            $type = self::SMS_REGISTER_WX;
        } else {
            $type = self::SMS_RESIGTER_APP;
        }

//     	$code = $pw;
//     	$time = '10'; //分钟
        $rsp = self::SendSingleSender(14925, [$name, $pw], $type, '0', '0', function ($result) {
//    		$mobile = self::getMobile();
//     		if (intval($result['result']) == 0) {
//     			session(md5($mobile), ['code' => $code, 'expire' => time() + 10 * 60]);
//     		}
        });

        return $rsp;
    }

    public static function userregister($callback = null)
    {
        if (sp_is_weixin()) {
            $type = self::SMS_REGISTER_WX;
        } else {
            $type = self::SMS_RESIGTER_APP;
        }
        $code = sp_random_code();
        $time = '10'; //分钟
        $rsp  = self::SendSingleSender(9687, [$code, $time], $type, '0', '0');
        if (intval($rsp['result']) == 0) {
            if (is_callable($callback)) {
                $callback($code, self::getMobile(), $time);
            }
        } else {
            return false;
        }
    }

    /**
     * 发送业主认证验证码
     * @return array|bool|mixed
     */
    public static function certify()
    {
        if (sp_is_weixin()) {
            $type = self::SMS_REGISTER_WX;
        } else {
            $type = self::SMS_RESIGTER_APP;
        }

        $code = sp_random_code();
        $time = '10'; //分钟
        $rsp  = self::SendSingleSender(11811, [$code, $time], $type, '0', '0', function ($result) use ($code) {
            $mobile = self::getMobile();
            $mark   = self::getMark();
            if (intval($result['result']) == 0) {
                session(md5($mobile . $mark), ['code' => $code, 'expire' => time() + 10 * 60]);
            }
        });

        return $rsp;
    }
    
    /**
     * 发送投诉建议申诉认证验证码
     * @return array|bool|mixed
     */
    public static function appealcertify()
    {
    	if (sp_is_weixin()) {
    		$type = self::SMS_REGISTER_WX;
    	} else {
    		$type = self::SMS_RESIGTER_APP;
    	}
    
    	$code = sp_random_code();
    	$time = '10'; //分钟
    	$rsp  = self::SendSingleSender(25260, [$code, $time], $type, '0', '0', function ($result) use ($code) {
    		$mobile = self::getMobile();
    		$mark   = self::getMark();
    		if (intval($result['result']) == 0) {
    			session(md5($mobile . $mark), ['code' => $code, 'expire' => time() + 10 * 60]);
    		}
    	});
    
    		return $rsp;
    }

    /**
     * @param $temp_id
     * @param $params
     * @param $type
     * @param $order_id
     * @param $user_id
     * @param null $callback
     * @return array|bool|mixed
     */
    public static function SendSingleSender($temp_id, $params, $type, $order_id, $user_id, $callback = null)
    {
        $mobile = self::getMobile();
//        if(empty($mobile) || !isMobile($mobile)) {
//            return false;
//        }

        try {
            $singleSender = self::getSingleSender();
            $result       = $singleSender->sendWithParam(self::getNationcode(), $mobile, $temp_id, $params, "企鹅圈社区", "", "");
            $content      = $singleSender->data;
            $rsp          = json_decode($result, true);
            if (is_callable($callback)) {
                $callback($rsp);
            }
        } catch (\Exception $e) {
            $rsp     = [
                'result' => -1,
                'errmsg' => 'Orz发生未知错误'
            ];
            $content = [];
        }

        //插入历史
        $data = [
            'source'         => $type,
            'order_id'       => $order_id,
            'content'        => json_encode($content),
            'user_id'        => $user_id,
            'mobile'         => $mobile,
            'result'         => json_encode($rsp),
            'errmsg'         => $rsp['errmsg'],
            'ext'            => true === isset($rsp['ext']) ? $rsp['ext'] : '',
            'sid'            => true === isset($rsp['sid']) ? $rsp['sid'] : '',
            'fee'            => true === isset($rsp['fee']) ? $rsp['fee'] : '',
            'create_time'    => time(),
            'create_user_id' => empty(sp_get_current_admin_id()) ? 0 : sp_get_current_admin_id()
        ];

        M('SmsHist')->add($data);

        if ((int)$rsp['result'] == 1022) {
            $rsp = [
                'result' => -1022,
                'errmsg' => '业务短信日下发条数超过设定的上限'
            ];
        } else if ((int)$rsp['result'] == 1025) {
            $rsp = [
                'result' => -1025,
                'errmsg' => '单个手机号日下发短信条数超过设定的上限'
            ];
        } else if ((int)$rsp['result'] == 1026) {
            $rsp = [
                'result' => -1026,
                'errmsg' => '单个手机号下发相同内容超过设定的上限'
            ];
        }

        return $rsp;
    }

    /**
     * 判断是否相同
     * @param $code
     * @return bool
     */
    public static function equal($code)
    {
        $mobile = self::getMobile();
        $mark   = self::getMark();
        if (empty($mobile) || !isMobile($mobile) || !session(md5($mobile . $mark))) {
            return false;
        }

        $array = session(md5($mobile . $mark));

        if ($array['expire'] <= time()) {
            return false;
        }

        return $array['code'] == $code;
    }
}

function error_msg($code)
{
    switch ((int)$code) {
        case 1001:
            $string = 'sig校验失败';
            break;
        case 1002:
            $string = '短信/语音内容中含有敏感词';
            break;
        case 1003:
            $string = '请求包体没有sig字段或sig为空';
            break;
        case 1004:
            $string = '请求包解析失败，通常情况下是由于没有遵守API接口说明规范导致的';
            break;
        case 1006:
            $string = '请求没有权限，比如没有扩展码权限等';
            break;
        case 1007:
            $string = '其他错误';
            break;
        case 1008:
            $string = '请求下发短信/语音超时';
            break;
        case 1009:
            $string = '请求ip不在白名单中';
            break;
        case 1011:
            $string = '不存在该REST API接口';
            break;
        case 1012:
            $string = '签名格式错误或者签名未审批';
            break;
        case 1013:
            $string = '下发短信/语音命中了频率限制策略';
            break;
        case 1014:
            $string = '模版未审批或请求的内容与审核通过的模版内容不匹配';
            break;
        case 1015:
            $string = '手机号在黑名单库中,通常是用户退订或者命中运营商黑名单导致的';
            break;
        case 1016:
            $string = '手机号格式错误';
            break;
        case 1017:
            $string = '请求的短信内容太长';
            break;
        case 1018:
            $string = '语音验证码格式错误';
            break;
        case 1019:
            $string = 'sdkappid不存在';
            break;
        case 1020:
            $string = 'sdkappid已禁用';
            break;
        case 1021:
            $string = '请求发起时间不正常，通常是由于您的服务器时间与腾讯云服务器时间差异超过10分钟导致的';
            break;
        case 1022:
            $string = '业务短信日下发条数超过设定的上限';
            break;
        case 1023:
            $string = '单个手机号30秒内下发短信条数超过设定的上限';
            break;
        case 1024:
            $string = '单个手机号1小时内下发短信条数超过设定的上限';
            break;
        case 1025:
            $string = '单个手机号日下发短信条数超过设定的上限';
            break;
        case 1026:
            $string = '单个手机号下发相同内容超过设定的上限';
            break;
        default :
            $string = 'success';
            break;
    }

    return $string;
}

function random_code()
{
    $numbers = range(0, 9);
    shuffle($numbers);
    $num    = 4;
    $result = array_slice($numbers, 0, $num);

    return implode('', $result);
}