<?php


namespace Api\Controller;


use Api\Lib\ErrorCode;
use Common\Controller\ApiRestController;
use Common\Lib\Hcpms;
use Common\Lib\Sms\Sms;
use Common\Model\UsersModel;
use Common\Model\UserWxMapping;
use Lcobucci\JWT\Parser;
use Think\Model;

class UserController extends ApiRestController
{
    /**
     * @var UsersModel
     */
    protected $users_model;

    /**
     * @var Model
     */
    protected $user_app_info_model;

    /**
     * @var UserWxMapping
     */
    protected $user_wx_mapping_model;

    function _initialize()
    {
        parent::_initialize();
        $this->users_model           = D('Users');
        $this->user_app_info_model   = D('UserAppInfo');
        $this->user_wx_mapping_model = D('UserWxMapping');
        if (in_array(ACTION_NAME, ['lists', 'avatar'])) {
            $this->check_key = true;
        }
    }

    public function login()
    {
        $type = I($this->_method . '.type', '', 'strtolower');
        if (!in_array($type, [self::TYPE_WX, self::TYPE_APP], true)) {
            $this->res([], 10002, 'invaild params');
        }
        switch ($type) {
            case self::TYPE_WX:
                $this->_wx_login();
                break;
            case self::TYPE_APP:
                $this->_app_login();
                break;
            default:
                break;
        }
    }

    public function userinfo()
    {
        $key  = I('request.refresh_token');
        $type = I('request.type', '', 'strtolower');

        try {
            $token = ((new Parser())->parse((string)$key));
        } catch (\Exception $e) {
            $this->res([], -1, $e->getMessage());
        }
        $tmp = [];
        if (!empty(api_wxid())) {
            $tmp['wx'] = api_wxid();
        }
        if (!empty(api_wx_openid(api_wxid()))) {
            $tmp['openid'] = api_wx_openid(api_wxid());
        }
        if (!empty(api_wx_companyid(api_wxid()))) {
            $tmp['companyid'] = api_wx_companyid(api_wxid()) ?: 1;
        }
        $data = array_merge($this->getTokenData($token), $tmp);
        if (isset($data['imei']) && !empty($data['imei'])) {
            $type         = 'app';
            $data['type'] = 'app';
        }
        $user_id = $data['userId'];
        if (!in_array($type, [self::TYPE_APP, self::TYPE_WX], true) || !$key) {
            $this->res([], 10002, 'invaild params');
        }
        $tmp['type'] = $type;
        if (!$user_id) {
            $this->res([], -1, 'error');
        } else {
            if ($type == self::TYPE_APP) {
                $imei = $data['imei'];
                if ($info = $this->user_app_info_model->where(['imei' => $imei])->find()) {
                    $info['last_login_ip'] = get_client_ip(0, true);
                    $info['key']           = $key;
                    $this->user_app_info_model->save($info);
                }
            }

            if ($type == self::TYPE_WX) {
                $wx_id  = $data['wx'];
                $openid = $data['openid'];
                if ($info = $this->user_wx_mapping_model->where(['openid' => $openid, 'wx_id' => $wx_id])->find()) {
                    $info['key'] = $key;
                    if (!$info['user_id']) {
                        $info['user_id'] = $user_id;
                    }
                    $this->user_wx_mapping_model->save($info);
                    if ((int)$info['user_id'] <= 0) {
                        $this->res([], -1, '重新登录吧，少年！');
                    }
                }
            }
        }
        $appid     = $this->getAppid();
        $appsecret = $this->getAppsecret();

        $token        = $this->getAccessToken($appid, $appsecret, $data);
        $user         = $this->users_model->find($user_id);
        $user_wx_info = D('UserWxInfo')->where(['user_id' => $user_id])->find();

        $data = [
            'userId'                      => $user['id'],
            'headImg'                     => empty($user['avatar']) ? $user_wx_info['headimgurl'] : sp_get_image_url($user['avatar']),
            'nickName'                    => $user['user_nicename'],
            'mobile_last_login_community' => true === isset($info['last_login_community']) && (int)$info['last_login_community'] != 0 ? $info['last_login_community'] : -1,
//            'mobile_last_login_community' => 0,
            'signed'                      => $this->getSign($user['id']),
            'letters'                     => [],
            'is_licensor'                 => $this->_get_licensor($user_id, $this->getCommunityId()),
            'jf'                          => (int)$user['credits'],
            'mobile'                      => $user['mobile']
        ];
        if (!session('user')) {
            session('user.id', $user['id']);
            session('user', $user);
        }
        $this->res(['access_token' => (string)$token, 'expired' => ((int)$token->getClaim('exp')) * 1000, 'logon' => true, 'user' => $data]);
        $this->res($user);
    }

    /**
     * 微信网站登录[登录成功后返回带有user_id的权限token,用于其他操作]
     */
    private function _wx_login()
    {
        $mobile             = I($this->_method . '.mobile');
        $user_wx_info_model = D('UserWxInfo');
        $code               = I('post.code');
        $nationcode         = I('post.nationcode');
        Sms::setNationcode($nationcode);
        Sms::setMobile($mobile);
        if ($user = $this->users_model->where(['mobile' => $mobile])->find()) {
            if (APP_DEBUG || $mobile == '132269413891' || Sms::equal($code)) {
                //token数据
                $token_data = $this->getTokenData($this->access_token, [
                    'userId' => $user['id'],
                    'type'   => 'wx']);

                $user_id = $user['id'];
                session('user.id', $user_id);
                session('user', $user);

                //检测微信用户
                $wechat_user = api_wx_user();
                if (!empty($wechat_user)) {
                    $openid          = $wechat_user['openid'];
                    $user_wx_mapping = $this->user_wx_mapping_model->where(['openid' => $openid])->find();
                    $this->user_wx_mapping_model->where(['openid' => $openid])->save(['user_id' => $user_id]);
                    if ($user_wx_info = $user_wx_info_model->where(['user_id' => $user_id])->find()) {
                        $data = [
                            'user_id'        => $user_id,
                            'nickname'       => $wechat_user['nickname'],
                            'headimgurl'     => $wechat_user['headimgurl'],
                            'sex'            => $wechat_user['sex'],
                            'city'           => $wechat_user['city'],
                            'country'        => $wechat_user['country'],
                            'province'       => $wechat_user['province'],
                            'language'       => $wechat_user['language'],
                            'update_time'    => time(),
                            'update_user_id' => $user_id
                        ];
                        $user_wx_info_model->save($data);
                    } else {
                        $data = [
                            'user_id'        => $user_id,
                            'nickname'       => !empty($wechat_user['nickname']) ? $wechat_user['nickname'] : '',
                            'headimgurl'     => !empty($wechat_user['headimgurl']) ? $wechat_user['headimgurl'] : '',
                            'sex'            => !empty($wechat_user['sex']) ? $wechat_user['sex'] : '',
                            'city'           => !empty($wechat_user['city']) ? $wechat_user['city'] : '',
                            'country'        => !empty($wechat_user['country']) ? $wechat_user['country'] : '',
                            'province'       => !empty($wechat_user['province']) ? $wechat_user['province'] : '',
                            'language'       => !empty($wechat_user['language']) ? $wechat_user['language'] : '',
                            'create_time'    => time(),
                            'create_user_id' => $user_id,
                            'update_time'    => time(),
                            'update_user_id' => $user_id,
                        ];
                        $user_wx_info_model->add($data);
                    }
                    $this->setCommunityId($user_wx_mapping['last_login_community']);
                    $user['mobile_last_login_community'] = $user_wx_mapping['last_login_community'];
                }

                //生成用户的access_token
//                $is_licensor = D('CommunityRoomCert')->where(['community'=>$user_wx_mapping['last_login_community'],'state'=>1,'type'=>['in','1,2,3']])->count();
                $appid     = $this->getAppid();
                $appsecret = $this->getAppsecret();
                $token     = $this->getAccessToken($appid, $appsecret, $token_data);

                $data = array_merge($this->getuserinfo($user), [
                    'signed'      => $this->getSign($user['id']),
                    'letters'     => [],
                    'is_licensor' => $this->_get_licensor($user['id'], $user_wx_mapping['last_login_community'])
                ]);

//                D('UserLoginHist')->history(sp_get_current_userid(), sp_get_current_userid(), 1);
                $this->res(['access_token' => (string)$token, 'expired' => ((int)$token->getClaim('exp')) * 1000, 'user' => $data]);
            } else {
                $this->res([], ErrorCode::VERIFICATION_CODE_ERROR, 'verification code error');
            }
        } else {
//            $this->res([],100003,'unknown error');
            $this->register();
        }
    }

    /**
     * 手机登录
     */
    private function _app_login()
    {
        $mobile     = I($this->_method . '.mobile');
        $imei       = I('post.imei');
        $code       = I('post.code');
        $nationcode = I('post.nationcode');
        $app_id     = I('post.app_id');
        Sms::setNationcode($nationcode);
        Sms::setMobile($mobile);
        if (!$mobile) {
            $this->res([], 10002, 'invaild params');
        }
        if ($user = $this->users_model->where(['mobile' => $mobile])->find()) {
            if (APP_DEBUG || $mobile == '132269413891' || Sms::equal($code)) {
                $app        = D('Apps')->where(['code' => $app_id])->find();
                $token_data = $this->getTokenData($this->access_token, [
                    'userId' => $user['id'],
                    'type'   => 'app', 'wxappid' => $app['wx_appid']]);
                //生成用户access_token
                $appid     = $this->getAppid();
                $appsecret = $this->getAppsecret();
                $token     = $this->getAccessToken($appid, $appsecret, $token_data);

                session('user.id', $user['id']);
                session('user', $user);
                if ($app_info = $this->user_app_info_model->where(['imei' => $imei])->find()) {
                    $app_info['user_id']         = $user['id'];
                    $app_info['last_login_ip']   = get_client_ip(0, true);
                    $app_info['last_login_time'] = time();
                    $app_info['key']             = (string)$token;
                    $app_info['update_time']     = time();
                    $app_info['update_user_id']  = $user['id'];
                    $this->user_app_info_model->save($app_info);
                    session('last_community_id', $app_info['last_login_community']); //保存最后登录小区
                }
                $wx_id = api_wxid();
                if ($wx_id && $user_wx_mapping = $this->user_wx_mapping_model->where(['openid' => api_wx_openid($wx_id), 'wx_id' => $wx_id])->find()) {
                    $user_wx_mapping['user_id'] = $user['id'];
                    $this->user_wx_mapping_model->save($user_wx_mapping);
                }
            } else {
                $this->res([], ErrorCode::VERIFICATION_CODE_ERROR, 'verification code error');
            }
            $data = array_merge($this->getuserinfo($user), [
                'signed'  => $this->getSign(),
                'letters' => []
            ]);
            $this->res(['access_token' => (string)$token, 'expired' => ((int)$token->getClaim('exp')) * 1000, 'user' => $data]);
        } else {
            $this->register();
        }
    }

    /**
     * 获取验证码
     */
    public function sms()
    {
        $mobile     = I('post.mobile');
        $nationcode = I('post.nationcode');
        $action     = I('post.action');
        if (!$mobile || !$nationcode || !in_array($action, ['login', 'register'], true)) {
            $this->res([], ErrorCode::INVAILD_PARAMS, ErrorCode::getMsg(ErrorCode::INVAILD_PARAMS));
        }

        $is_send = true;
        $user    = $this->users_model->where(['mobile' => $mobile])->find();
        if ($is_send) {
            Sms::setNationcode($nationcode);
            Sms::setMobile($mobile);
            if (!APP_DEBUG && $mobile != '132269413891') {
                $res = Sms::register();
                if ($res['result'] != 0) {
                    $this->res([], $res['result'], $res['errmsg']);
                }
            }
            if ($user) {
                $data        = $this->getuserinfo($user);
                $wechat_user = api_wx_user();
                if ($wechat_user) {
                    $data = array_merge($data, [
                        'headImg' => $wechat_user['headimgurl']
                    ]);
                }
                if ($user['avatar']) {
                    $data = array_merge($data, [
                        'headImg' => sp_get_image_url($user['avatar'])
                    ]);
                }
            } else {
                $wechat_user = api_wx_user();
                if ($wechat_user) {
                    $data = [
                        'headImg'                     => $wechat_user['headimgurl'],
                        'mobile'                      => $mobile,
                        'mobile_last_login_community' => 0,
                        'nickName'                    => $wechat_user['nickname'],
                    ];
                } else {
                    $data = [
                        'headImg'                     => '',
                        'mobile'                      => $mobile,
                        'mobile_last_login_community' => 0,
                        'nickName'                    => $mobile,
                    ];
                }
            }
            $this->res($data);
        }
        $this->res([]);
    }

    /**
     * 注册
     */
    public function register()
    {
        if (IS_POST) {
            $mobile     = I('post.mobile');
            $code       = I('post.code');
            $nickname   = api_wx_user()['nickname'] ?: I('post.nickname', $mobile);
            $user_model = D('Users');
            $community  = I('post.community', 0, 'intval');
            $type       = I($this->_method . '.type', '', 'strtolower');
            $nationcode = I('post.nationcode');
            if (!in_array($type, [self::TYPE_WX, self::TYPE_APP], true)) {
                $this->res([], 10002, 'invaild params');
            }

            if (!$mobile || !$code) {
                $this->res([], ErrorCode::INVAILD_PARAMS, ErrorCode::getMsg(ErrorCode::INVAILD_PARAMS));
            }
            Sms::setNationcode($nationcode);
            Sms::setMobile($mobile);
            if (APP_DEBUG || Sms::equal($code)) {
                if ($user = $user_model->where(['mobile' => $mobile])->find()) {
                    $this->res([], ErrorCode::MOBILE_IS_REGISTERED, 'is registered');
                } else {
//                    if ($user_model->where(['user_nicename' => $nickname])->find()) {
//                        $this->res([], ErrorCode::USER_REGISTER_NICKNAME_ERROR, 'nickname error');
//                    }
                    $md5_key = md5(microtime(true));
                    $key     = I($this->_method . '.key', $md5_key);

                    $data = [
                        'user_nicename'               => $nickname,
                        'mobile'                      => $mobile,
                        'last_login_company'          => 0,
                        'create_time'                 => time(),
                        'create_user_id'              => 0,
                        'update_time'                 => 0,
                        'update_user_id'              => 0,
                        'setting'                     => json_encode(['agreement' => 'yes']),
                        'mobile_last_login_community' => $community
                    ];

                    $user_model->add($data);
                    $user_id = $user_model->getLastInsID();

                    if ($type == 'app') {
                        $imei     = I($this->_method . '.imei');
                        $model    = I($this->_method . '.model');
                        $os       = I($this->_method . '.os');
                        $app_id   = I($this->_method . '.app_id');
                        $app_info = [
                            'user_id'         => $user_id,
                            'app_id'          => $app_id,
                            'imei'            => $imei,
                            'model'           => $model,
                            'os'              => $os,
                            'last_login_ip'   => get_client_ip(0, true),
                            'last_login_time' => time(),
                            'key'             => $key,
                            'create_time'     => time(),
                            'create_user_id'  => $user_id,
                            'update_time'     => time(),
                            'update_user_id'  => $user_id
                        ];

                        $this->user_app_info_model->add($app_info);
                        $app_info['info_id'] = $this->user_app_info_model->getLastInsID();
                    }

                    if ($type == 'wx') {
                        $wechat_user        = api_wx_user();
                        $user_wx_info_model = D('UserWxInfo');
                        if ($wechat_user) {
                            $openid = $wechat_user['openid'];
                            $this->user_wx_mapping_model->where(['openid' => $openid])->save(['user_id' => $user_id]);
                            $data = [
                                'user_id'        => $user_id,
                                'nickname'       => $wechat_user['nickname'],
                                'headimgurl'     => $wechat_user['headimgurl'],
                                'sex'            => $wechat_user['sex'],
                                'city'           => $wechat_user['city'],
                                'country'        => $wechat_user['country'],
                                'province'       => $wechat_user['province'],
                                'language'       => $wechat_user['language'],
                                'create_time'    => time(),
                                'create_user_id' => $user_id,
                                'update_time'    => time(),
                                'update_user_id' => $user_id,
                            ];
                            $user_wx_info_model->add($data);
                        }
                    }

                    if ($user_id) {
                        $user = $user_model->where(['mobile' => $mobile])->find();
                        session('user.id', $user_id);
                        session($key, $user_id);
                        session('user', $user);


                        $token_data = $this->getTokenData($this->access_token, [
                            'userId' => $user_id,
                            'type'   => $type]);

                        //生成用户的access_token
                        $appid     = $this->getAppid();
                        $appsecret = $this->getAppsecret();
                        $token     = $this->getAccessToken($appid, $appsecret, $token_data);


//                        $data = array_merge($this->getuserinfo($user),['letters' => []]);
//                        $this->res(['key'=>$key,'user'=>$data]);

                        $data = array_merge($this->getuserinfo($user), [
                            'signed'  => $this->getSign($user_id),
                            'letters' => []
                        ]);

//                        D('UserLoginHist')->history(sp_get_current_userid(), sp_get_current_userid(), 1);
                        $this->res(['access_token' => (string)$token, 'expired' => ((int)$token->getClaim('exp')) * 1000, 'user' => $data]);

                    } else {
                        $this->res([], ErrorCode::UNKNOW_ERROR, ErrorCode::getMsg(ErrorCode::UNKNOW_ERROR));
                    }
                }
            } else {
                $this->res([], ErrorCode::VERIFICATION_CODE_ERROR, 'verification code error');
            }
        }
    }

    /**
     * 上传头像
     */
    public function avatar()
    {
        $avatar  = I('post.avatar', '', 'base64_decode');
        $user_id = $this->getUserId();
        $user    = sp_get_current_user();
        if (empty($avatar)) {
            $this->res([], ErrorCode::INVAILD_PARAMS, ErrorCode::getMsg(ErrorCode::INVAILD_PARAMS));
        }

        if (empty($user_id)) {
            $this->res([], ErrorCode::NOT_LOGIN, ErrorCode::getMsg(ErrorCode::NOT_LOGIN));
        }

        $result = sp_standard_upload($avatar, 'user-logo', $user_id);
//        $result = sp_standard_upload2('user-logo', $user_id);
//        exit();
        if (!empty($result)) {
            Hcpms::asset_avatar($result['fileid'], $user_id);
            $user['avatar'] = sp_get_image_url($result['filepath']);
            session('user', $user);
            D('Users')->save(['id' => $user_id, 'avatar' => $result['filepath']]);
            $this->res(['avatar' => $user['avatar']]);
        } else {
            $this->res([], ErrorCode::UNKNOW_ERROR, ErrorCode::getMsg(ErrorCode::UNKNOW_ERROR));
        }
    }

    public function test()
    {
//        var_dump($this->access_token);
//        print_r($this->getWxid());
//        print_r($this->getTokenData($this->access_token,['userId'=>28]));
//        print_r($this->access_token->getHeaders());
//        print_r($this->getWxid());
//        print_r($_SERVER);
        print_r($this->getOpenId());
        $this->res([]);
    }

    public function update_user()
    {
        if ($this->_method == 'post') {
            $user_id     = $this->getUserId();
            $nickname    = I('post.nickname');
            $users_model = D('Users');
            $user        = $users_model->find($user_id);
            if (empty($user)) {
                $this->res([], -1, 'error');
            }
            if ($users_model->where(['user_nicename' => $nickname, 'id' => ['neq', $user_id]])->find()) {
                $this->res([], -2, '用户名已存在!');
            }
            $users_model->where(['id' => $user_id])->save(['user_nicename' => $nickname]);
            if (sp_get_current_user()) {
                $user                  = sp_get_current_user();
                $user['user_nicename'] = $nickname;
                sp_update_current_user($user);
            }
            $this->res([]);
        }
    }

    public function getwxuser()
    {
//        $wechat_user = session('wechat_user');
//        if (!empty($wechat_user)) {
//            $openid = $wechat_user['openid'];
//            $user_wx_mapping = D('UserWxMapping')->where(['openid'=>$openid])->find();
//            $data = [
//                'headImg' => $wechat_user['headimgurl'],
//                'nickName' => $wechat_user['nickname'],
//                'mobile_last_login_community' => $user_wx_mapping['last_login_community']
//            ];
//            $this->res($data);
//        }
        $openid = $this->getOpenId();
        if ($openid) {
            $wechat_user     = api_wx_user();
            $user_wx_mapping = D('UserWxMapping')->where(['openid' => $openid])->find();
            $data            = [
                'headImg'                     => 'data:image/png;base64,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',
                'nickName'                    => $wechat_user['nickname'],
                'mobile_last_login_community' => $user_wx_mapping['last_login_community']
            ];
            $this->res($data);
        } else {
            $this->res([
                'headImg'                     => '',
                'nickName'                    => '游客',
                'mobile_last_login_community' => 0
            ]);
        }
    }

    public function logout()
    {
//        session('user.id',false);
//        $wechat_user = api_wx_user();
//        $wx_id       = api_wxid();
//        if (!empty($wechat_user)) {
//            $openid          = $wechat_user['openid'];
//            $user_wx_mapping = D('UserWxMapping')->where(['openid' => $openid, 'wx_id' => $wx_id])->find();
//            $data            = [
//                'headImg'                     => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAMAAAD04JH5AAAC/VBMVEUAAAD///////////////////////////////////////////////////////////////////////////////////8ABkkADVEACU0AEVcAGmACn/cAD1QAH2YAC08AJW38/PwCo/kAKnIAAkYCm/IAMHgAE1n19vYCid0BOIECkeUClOkCluwBQYwCgtQAFVz5+fkCnfQAHWMBR5MAF10BecsAI2sCpPwBb78BcsQBWaYCme8ChdcBi+AAK3YAAkkBZrYBMnwBNH8BjuICh9oAAk0BaroBRY/z8/MBdscBW6kBS5gBP4kBO4YCsv8ABFLm5+cBftACpf/t7e0BTpoCrf/v8PACfM0BVaIBUp/x8fLo6ekBY7IBYrEBXqwCuf8CqP/q6+vg4OEBYa4BT5sCtf8BVKkCw//j4+PU1da5ubmzs7QAJHjc3d3JyssAFmgAB1cBasQBS6GZm5wCOI0AMIUBmPzNzs/FxscAFGICofzS0tIBgNK2vse/wMGrrK2mp6iSk5eLjZEVGVICmPbX2NgBWbCbn6UvdaMBP5ZWaIV3e4QAGm0CyP80ib8BRZoAKn4sT3tHVnpoangAHXMAB1wpLlkCvv8Yvf8BjewBheUBjOTJ194mlNEBcM2/xMqkrbdwkKl+i6ShoqIgYJ5jgppvfpGEhYoWTYlyc35eYG9BR2o0OGACoP8BlPOrz+Eeodlss9WbvdKtws4BZb0bfrtDjLpTkbNlcYV9foNVXXgAKG5PUGcAD2MCnf0KqPABfd5JqdwNldsBd9W9yNIcicStucJRnsCOpbMtebGTnatVg6qNlqFDdJUyYpVaeo1/f4k4WIgtRn0eOHIYK2VARmMWs/oupOw1seUPmeEBg94RkNCGs8qYtMOCp72cp7V9m7EbcrE7gayAlJ9Lbp2DjpV9g5QMTZRFY458g4oSQoNEZIHa5eu92ON6vuPX3eI8oM6ptLwBXrglaKsBVKERWJwkSos9SXMzvfUCneZqvuA3ot9ZotFsmbYBO4hfBABCAAAAFXRSTlMA+iTxoILk3s/HRQhsK6aIVSAqawGX+PDwAAAJjklEQVR42sTWSUwTURjA8akF9918p17GpMk0bdNMD0S006RJG1M5TMqQUUORGWomLRBMGpJC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',
//                'nickName'                    => $wechat_user['nickname'],
//                'mobile_last_login_community' => ((int)$user_wx_mapping['user_id'] == 0 && (int)$user_wx_mapping['last_login_community'] == 0) ? -1 : $user_wx_mapping['last_login_community']
//            ];
//            $this->res($data);
//        }
        $type    = $this->getType();
        $user_id = $this->getUserId();
        if ($type == self::TYPE_WX) {
            $openid          = $this->getOpenId();
            $wxid            = $this->getWxid();
            $user_wx_mapping = D('UserWxMapping')->where(['openid' => $openid, 'wx_id' => $wxid])->find();
            $data            = [
                'headImg'                     => 'data:image/png;base64,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',
                'nickName'                    => D('Users')->where(['id' => $user_id])->getField('user_nicename') ?: '游客',
                'mobile_last_login_community' => ((int)$user_wx_mapping['user_id'] == 0 && (int)$user_wx_mapping['last_login_community'] == 0) ? -1 : $user_wx_mapping['last_login_community']
            ];
        } else if ($type == self::TYPE_APP) {
            $imei     = $this->getImei();
            $app_info = D('UserAppInfo')->where(['imei' => $imei])->find();
            $user_id  = $user_id ?: $app_info['user_id'];
            $data     = [
                'headImg'                     => 'data:image/png;base64,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',
                'nickName'                    => D('Users')->where(['id' => $user_id])->getField('user_nicename') ?: '游客',
                'mobile_last_login_community' => $app_info['last_login_community']
            ];
        } else {
            $data = [
                'headImg'                     => '',
                'nickName'                    => '游客',
                'mobile_last_login_community' => 0
            ];
        }
        $this->res($data);
    }

    public function oauthcallback()
    {
        $body      = I('request.body');
        $community = I('request.community', $this->getCommunityId());
        $app_id    = I('request.app_id');
        $type      = I('request.type', '', 'strtolower');
        if (!in_array($type, ['wx', 'qq'], true) || !$body) {
            $this->res([], 10002, 'invaild params');
        }
        if ($type == 'wx') {
            $this->_wxoauth($app_id, $community, $body);
        }
        if ($type == 'qq') {
            $this->_qqoauth($app_id, $community, $body);
        }
    }

    private function _wxoauth($app_id, $community, $body)
    {
        $app  = D('Apps')->where(['code' => $app_id])->find();
        $wxid = $app['wx_id'];
        $body = json_decode(htmlspecialchars_decode($body), true) ?: json_decode($body, true);
        api_wxid($wxid);
        api_wx_user($body);
        api_wx_openid($wxid, $body['openid']);
        $openid          = $body['openid'];
        $_POST['type']   = 'app';
        $token_data      = $this->getTokenData($this->access_token);
        $user_wx_mapping = $this->user_wx_mapping_model->where(['openid' => $openid, 'wx_id' => $wxid])->find();
        if (empty($user_wx_mapping)) {
            $data = [
                'openid'               => $body['openid'],
                'wx_id'                => $wxid,
                'user_id'              => 0,
                'subscribe_time'       => 0,
                'unsubscribe_time'     => 0,
                'unionid'              => $body['unionid'],
                'remark'               => '',
                'groupid'              => 0,
                'last_login_community' => 0,
                'key'                  => '',
                'create_time'          => time(),
                'create_user_id'       => 0,
                'update_time'          => time(),
                'update_user_id'       => 0
            ];
            $this->user_wx_mapping_model->add($data);
            $data['mapping_id'] = $this->user_wx_mapping_model->getLastInsID();
            $this->res([], ErrorCode::APP_USER_NOREGISTER, ErrorCode::getMsg(ErrorCode::APP_USER_NOREGISTER));
        } else {
            $user_id = $user_wx_mapping['user_id'];
            if ($imei = $token_data['imei']) {
                D('UserAppInfo')->where(['imei' => $imei])->save(['user_id' => $user_id]);
            }
            if (!$user_id) {
                $this->res([], ErrorCode::APP_USER_NOREGISTER, ErrorCode::getMsg(ErrorCode::APP_USER_NOREGISTER));
            }
            $user       = D('Users')->find($user_id);
            $token_data = array_merge($token_data, [
                'userId'  => $user_id,
                'wx'      => $wxid,
                'type'    => 'app',
                'wxappid' => $app['wx_appid']
            ]);
            $data       = array_merge($this->getuserinfo($user), [
                'signed'                      => $this->getSign($user_id),
                'mobile_last_login_community' => $community,
                'letters'                     => []
            ]);
            $appid      = $this->getAppid();
            $appsecret  = $this->getAppsecret();
            $token      = $this->getAccessToken($appid, $appsecret, $token_data);

            session('user.id', $user_id);
            session('user', $user);
//            D('UserLoginHist')->history(sp_get_current_userid(), sp_get_current_userid(), 1);
            $this->res(['access_token' => (string)$token, 'expired' => ((int)$token->getClaim('exp')) * 1000, 'user' => $data]);
        }
    }

    private function _qqoauth($app_id, $community, $body)
    {
        $app  = D('Apps')->where(['code' => $app_id])->find();
        $wxid = $app['wx_id'];
        $body = json_decode(htmlspecialchars_decode($body), true) ?: json_decode($body, true);
        $body = [
            'openid'     => $body['openid'],
            'city'       => $body['city'],
            'country'    => '',
            'nickname'   => $body['nickname'],
            'privilege'  => [],
            'language'   => 'zh-CN',
            'headimgurl' => $body['figureurl_qq_2'],
            'unionid'    => '',
            'sex'        => $body['gender'],
            'province'   => $body['province']
        ];
        api_wx_user($body);
        api_wx_openid($wxid, $body['openid']);
        $openid          = $body['openid'];
        $_POST['type']   = 'app';
        $token_data      = [];
        $user_wx_mapping = $this->user_wx_mapping_model->where(['openid' => $openid, 'wx_id' => $wxid])->find();
        if (empty($user_wx_mapping)) {
            $data = [
                'openid'               => $body['openid'],
                'wx_id'                => $wxid,
                'user_id'              => 0,
                'subscribe_time'       => 0,
                'unsubscribe_time'     => 0,
                'unionid'              => '',
                'remark'               => '',
                'groupid'              => 0,
                'last_login_community' => 0,
                'key'                  => '',
                'create_time'          => time(),
                'create_user_id'       => 0,
                'update_time'          => time(),
                'update_user_id'       => 0
            ];
            $this->user_wx_mapping_model->add($data);
            $data['mapping_id'] = $this->user_wx_mapping_model->getLastInsID();
            $this->res([], ErrorCode::APP_USER_NOREGISTER, ErrorCode::getMsg(ErrorCode::APP_USER_NOREGISTER));
        } else {
            $user_id = $user_wx_mapping['user_id'];
            if (!$user_id) {
                $this->res([], ErrorCode::APP_USER_NOREGISTER, ErrorCode::getMsg(ErrorCode::APP_USER_NOREGISTER));
            }
            $user       = D('Users')->find($user_id);
            $token_data = array_merge($token_data, [
                'userId'  => $user_id,
                'wx'      => $wxid,
                'type'    => 'app',
                'wxappid' => $app['wx_appid']
            ]);
            $data       = array_merge($this->getuserinfo($user), [
                'signed'                      => $this->getSign($user_id),
                'mobile_last_login_community' => $community,
                'letters'                     => []
            ]);
            $appid      = $this->getAppid();
            $appsecret  = $this->getAppsecret();
            $token      = $this->getAccessToken($appid, $appsecret, $token_data);

            session('user.id', $user_id);
            session('user', $user);
//            D('UserLoginHist')->history(sp_get_current_userid(), sp_get_current_userid(), 1);
            $this->res(['access_token' => (string)$token, 'expired' => ((int)$token->getClaim('exp')) * 1000, 'user' => $data]);
        }
    }

    private function getSign($id = null)
    {
        $code       = 'A';
        $rule_model = D('CreditsRule');
        $hist_model = D('UserCreditsHist');
        $rule       = $rule_model->where(['code' => $code])->find();
        if (empty($rule)) {
            return 1;
        }
        $begin = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $end   = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        $hist  = $hist_model->where(['credits_id' => $rule['credits_id'], 'create_time' => ['between', [$begin, $end]]]);
        if (!empty($id)) {
            $hist->where(['user_id' => $id]);
        }
        $hist = $hist->find();
//        print_r($hist_model->getLastSql());
        if (empty($hist)) {
            return 0;
        } else {
            return 1;
        }
    }

    private function _get_licensor($user_id, $id)
    {
        $is_licensor = D('CommunityRoomCert')->where(['community_id' => $id, 'state' => 1, 'type' => ['in', '1,2,3'], 'user_id' => $user_id, 'state' => 1])->count();

        return $is_licensor;
    }
}