<?php


namespace Common\Jobs;


use Common\Lib\Xinge\PushService;
use Common\Lib\Xinge\XingeApp;
use Think\Log;

function log($content) {
    Log::record(__CLASS__. ':' . $content , Log::DEBUG);
}

class PushDeviceAuthJob
{
    public function perform()
    {
        \Common\Jobs\log(date('Y-m-d H:i:s').'开始运行:---------------------------------------------------');
        $auth = $this->args['auth'];
        if (false === isset($auth)) {
            return ;
        }
        $owner_type = $auth['owner_type'];
        $owner_id = $auth['owner_id'];
        $auth_id = $auth['auth_id'];
        $app_info_model = D('UserAppInfo');

        $app_info = [];

        $sql = 'SELECT user_id FROM __DEVICE_AUTH_CACHE__ a WHERE ((a.start_at = 0 AND a.expiration_at < '.time().') OR (a.start_at > '.time().' AND a.expiration_at = 0) OR (a.start_at = 0 and a.expiration_at = 0)) AND a.auth_id = '.$auth_id;

        switch ((int)$owner_type) {
            case 1:
                $user_id = $owner_id;
                break;
            case 2:
            case 3:
            case 4:
            case 5:
                $datas = M()->query($sql);
                $user_id = [];
                foreach ($datas as $data) {
                    $user_id[] = $data['user_id'];
                }
                break;
            case 6:
                $user_id = 'all';
                break;
            default:
                break;
        }

        if (is_null($user_id)) return;

        if (is_array($user_id)) {
            foreach ($user_id as $item) {
                if ($data = $app_info_model->where(['user_id'=>$item])->find()) {
                    $app_info[] = $data;
                }
            }
        } else {
            if ($user_id == 'all') {
                $app_info = $app_info_model->select();
            } else {
                $app_info = $app_info_model->where(['user_id'=>$user_id])->select();
            }
        }

        //发送推送
        foreach ($app_info as $item) {
            $sql = 'SELECT a.* FROM hc_devices a JOIN hc_device_auth_cache b on a.device_id = b.device_id WHERE ((b.start_at = 0 AND b.expiration_at < '.time().') OR (b.start_at > '.time().' AND b.expiration_at = 0) OR (b.start_at = 0 and b.expiration_at = 0)) AND (b.user_id = '.$item['user_id'].' OR b.user_id = -1) GROUP BY a.device_id';
            if (!empty($item['device_token'])) {
                $datas = M()->query($sql);
                if (strtolower($item['os']) == 'ios') {
                    print_r(PushService::PushTokenIosMessage(['device_auth'=>$datas], $item['device_token'], 2));
//                    print_r(PushService::PushTokenIos('Test', $item['device_token'], 2));
                } else if (strtolower($item['os']) == 'andriod') {
//                    print_r(PushService::PushTokenAndroid('Test','test', $item['device_token']));
                }
            }
        }
    }
}