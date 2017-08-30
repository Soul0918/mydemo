<?php
namespace Api\Lib;

class ErrorCode
{
    const ACCESS_TOKEN_ERROR = -1;
    const ACCESS_TOKEN_EXPIRE = -2;
    const INVAILD_PARAMS = 100002;
    const UNKNOW_ERROR = 100003;
    const NOT_LOGIN = 100004;
    const VERIFICATION_CODE_ERROR = 100005;
    const MOBILE_IS_REGISTERED = 100006;
    const USER_REGISTER_NICKNAME_ERROR = 100007;
    const MOBILE_IS_NOT_REGISTER = 100008;
    const MODEL_ERROR = 100009;//数据库错误
    const NON_OPERATIONAL = 100010;//不可操作
    const DELETE_OR_NOTEXIST = 100011;//数据被删除或不存在
    const APP_USER_NOREGISTER = 200001;

    public static function getMsg($code)
    {
        $array = [
            self::INVAILD_PARAMS => '参数错误',
            self::UNKNOW_ERROR => '未知错误',
            self::NOT_LOGIN => '未登录',
            self::ACCESS_TOKEN_ERROR => 'access_token错误',
            self::ACCESS_TOKEN_EXPIRE => 'access_token过期',
        	self::DELETE_OR_NOTEXIST => '数据已被删除或不存在',
            self::APP_USER_NOREGISTER => '您还需要绑定手机号！'
        ];
        return $array[$code];
    }
}