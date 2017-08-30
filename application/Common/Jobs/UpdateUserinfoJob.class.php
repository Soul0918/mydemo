<?php


namespace Common\Jobs;


use Common\Lib\Wxapi;

class UpdateUserinfoJob
{
    public function perform()
    {
        $wx_id = $this->args['wx_id'];
        $wx_publics_model      = D('WxPublics');
        $user_wx_info_model    = D('UserWxInfo');
        $user_wx_mapping_model = D('UserWxMapping');

        if (!empty($wx_id)) {
            $wx_publics = $wx_publics_model->where(['wx_id' => $wx_id])->select();
        } else {
            $wx_publics = $wx_publics_model->select();
        }

        foreach ($wx_publics as $wx_public) {
            $insert  = [];
            $openids = [];
            $save    = [];
            if (empty($wx_public['token']) || empty($wx_public['appid']) || empty($wx_public['appsecret'])) {
                continue;
            }
            $options = [
                'debug'  => false,
                'wx_id'  => $wx_public['wx_id'],
                'app_id' => $wx_public['appid'],
                'secret' => $wx_public['appsecret'],
                'token'  => $wx_public['token']
            ];
            try {
                $wx = new Wxapi($options);
                foreach ($wx->openids() as $openid) {
                    $userinfo        = $wx->oneUserInfo($openid);
                    $openids[]       = $openid;
                    $user_wx_mapping = $user_wx_mapping_model->where(['openid' => $openid, 'wx_id' => $wx_public['wx_id']])->find();
                    if ($user_wx_mapping) {
                        $save[] = [
                            'mapping_id'       => $user_wx_mapping['mapping_id'],
                            'subscribe_time'   => $userinfo['subscribe_time'],
                            'update_time'      => time(),
                            'unsubscribe_time' => 0,
                            'unionid'          => $userinfo['unionid'],
                            'remark'           => $userinfo['remark'],
                            'groupid'          => $userinfo['groupid'],
                            'nickname'         => $userinfo['nickname'],
                            'headimgurl'       => $userinfo['headimgurl']
                        ];
                    } else {
                        $insert[] = [
                            'openid'               => $userinfo['openid'],
                            'wx_id'                => $wx_public['wx_id'],
                            'user_id'              => 0,
                            'subscribe_time'       => $userinfo['subscribe_time'],
                            'unsubscribe_time'     => 0,
                            'unionid'              => $userinfo['unionid'],
                            'remark'               => $userinfo['remark'],
                            'groupid'              => $userinfo['groupid'],
                            'last_login_community' => 0,
                            'create_time'          => time(),
                            'update_time'          => time(),
                            'create_user_id'       => 1,
                            'update_user_id'       => 1,
                            'nickname'             => $userinfo['nickname'],
                            'headimgurl'           => $userinfo['headimgurl']
                        ];
                    }
                }
            } catch (\Exception $e) {
                D('log')->add(['cat' => 'update_user_info', 'log' => (string)$e]);
            }

            if (!empty($save)) {
                $user_wx_mapping_model->where('wx_id = '.$wx_public['wx_id'] . ' and (unsubscribe_time = 0 or unsubscribe_time = \'\')')->save(['unsubscribe_time' => time()]);
                foreach ($save as $item) {
                    $user_wx_mapping_model->save($item);
                }
            }

            if (!empty($insert)) {
                $user_wx_mapping_model->addAll($insert);
            }
        }
    }
}