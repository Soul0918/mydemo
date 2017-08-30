<?php

namespace Common\Jobs;

use Common\Lib\Wxapi;
use Common\Model\NoticesModel;

class SendWxTemplateJob
{
    /**
     * @var array
     */
    public $wx_public;

    /**
     * @var Wxapi
     */
    public $wxapi;

    /**
     * @var string
     */
    public $company_id;

    /**
     * @var string
     */
    public $community_id;

    /**
     * @return mixed
     */
    public function getWxPublic()
    {
        return $this->wx_public;
    }

    /**
     * @param mixed $wx_public
     */
    public function setWxPublic($wx_public)
    {
        $this->wx_public = $wx_public;
    }

    /**
     * @return Wxapi
     */
    public function getWxapi()
    {
        return $this->wxapi;
    }

    /**
     * @param mixed $wxapi
     */
    public function setWxapi($wxapi)
    {
        $this->wxapi = $wxapi;
    }

    /**
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->company_id;
    }

    /**
     * @param mixed $company_id
     */
    public function setCompanyId($company_id)
    {
        $this->company_id = $company_id;
    }

    /**
     * @return string
     */
    public function getCommunityId()
    {
        return $this->community_id;
    }

    /**
     * @param string $community_id
     */
    public function setCommunityId($community_id)
    {
        $this->community_id = $community_id;
    }

    public function perform()
    {
        \Think\Log::write(__CLASS__ . ':' . date('Y-m-d H:i:s') . '开始运行:---------------------------------------------------');

        $recipient = $this->args['recipient'];
        $host      = $this->args['host'];
        if (false === isset($recipient)) {
            return;
        }
        $source = $recipient['source_code'];
        //发模板消息
        $wx_publics_model = D('WxPublics');
        $notices_model    = D('Notices');

        $user_wx_mapping_model = D('UserWxMapping');

        $recipients  = $recipient['list'];
        $notices     = [];
        $communities = [];
        $options     = [];
        foreach ($recipients as $key => $value) {
            $recipient_type      = $value['recipient_type'];
            $order_id            = $recipient['order_id'];
            $other               = isset($recipient['other']) ? $recipient['other'] : false;
            $options['other']    = $other;
            $options['order_id'] = $order_id;
            if ($recipient['cat'] == '1') {
                if (false === isset($notices[$order_id])) {
                    $notices[$order_id] = $notices_model->where(['notice_id' => $order_id])->find();
                }
                $notice = $notices[$order_id];
                if ($notice['state'] != '3') {
                    continue;
                }
                if ($notice['community_id']) $this->setCommunityId($notice['community_id']);
                $content = json_decode(htmlspecialchars_decode($notice['content_dynamic']), true);
                $recipient['sub_cat'] = D('NoticeCategory')->where(['cat_id' => $recipient['sub_cat']])->getField('wx_template_sub_cat');
//                $content['url'] = 'http://www.baidu.com';
            } else if ($recipient['cat'] == '3' || $recipient['cat'] == '4' || $recipient['cat'] == '5' || $recipient['cat'] == '6' || $recipient['cat'] == '7' || $recipient['cat'] == '8' || $recipient['cat'] == '9' || $recipient['cat'] == '10') {
                $content = $this->args['content'];
            }
            $content['template_id'] = D('WxTemplate')->where(['cat' => $recipient['cat'] == '9' ? 1 : $recipient['cat'], 'sub_cat' => $recipient['sub_cat'], 'state' => 1])->getField('template_id');
            $template_id            = $content['template_id'];
            if (!$template_id) break;

            switch (intval($recipient_type)) {
                case NoticesModel::TYPE_COMPANY:
                    $company_id = $value['target_id'];
                    $this->setTemplate($company_id, $content, $options, function ($content, $user_ids) use ($order_id, $template_id, $user_wx_mapping_model, $source) {
                        $user_wx_mapping = $user_wx_mapping_model->where(['wx_id' => $this->getWxPublic()['wx_id']])->field('mapping_id,user_id,openid')->select();
                        foreach ($user_wx_mapping as $value) {
                            $content['touser'] = $value['openid'];
                            if ($this->filterRead($value['user_id'], $user_ids)) continue;
                            try {
                                $result = $this->getWxapi()->sendTemplateMessage($content);
                                $this->wxTemplateHist($value, $order_id, $content, $template_id, $result, $source);
                            } catch (\Exception $e) {
                            }
                        }
                    });
                    break;
                case NoticesModel::TYPE_COMMUNITY:
                    $community_id = $value['target_id'];
                    $this->setCommunityId($community_id);
                    if (false === isset($communities[$community_id])) {
                        $communities[$community_id] = D('Communities')->where(['community_id' => $community_id])->find();
                    }
                    $community = $communities[$community_id];

                    $company_id = $community['company_id'];

                    $this->setTemplate($company_id, $content, $options, function ($content, $user_ids) use ($order_id, $template_id, $community_id, $user_wx_mapping_model, $source) {
                        $cert = D('CommunityRoomCert')->alias('a')->join('__COMMUNITY_ROOMS__ b on a.room_id = b.room_id')
                            ->where(['b.community_id' => $community_id, 'a.state' => 1, 'b.state' => ['neq', -1], 'a.type' => ['in', '1,2,3']])->field('a.*')->select();
                        $tmp = [];
                        if (!empty($cert)) {
                            foreach ($cert as $item) {
                                if ($this->filterRead($item['user_id'], $user_ids)) continue;
                                $tmp[] = $item['user_id'];
                            }
                            $tmp = array_unique($tmp);
                            $this->sendTemplateMessage($tmp, $content, ['order_id' => $order_id, 'template_id' => $template_id, 'source' => $source, 'template_id' => $template_id]);
                        }
                        $map['a.community_id'] = $community_id;
                        $map['_logic']         = 'or';
                        $map['_complex']       = [
                            'a.type'         => 5,
                            'a.community_id' => 0
                        ];
                        $user_wx_mapping       = $user_wx_mapping_model->where(['wx_id' => $this->getWxPublic()['wx_id'], 'last_login_community' => $community_id])->field('mapping_id,user_id,openid')->select();
                        foreach ($user_wx_mapping as $value) {
                            $content['touser'] = $value['openid'];
                            if (in_array($value['user_id'], $tmp, true)) continue;
                            if ($this->filterRead($value['user_id'], $user_ids)) continue;
                            try {
                                $result = $this->getWxapi()->sendTemplateMessage($content);
                                $this->wxTemplateHist($value, $order_id, $content, $template_id, $result, $source);
                            } catch (\Exception $e) {
                                D('log')->add(['cat' => 'wx_template', 'log' => (string)$e]);
                            }
                        }
                    });

                    break;
                case NoticesModel::TYPE_UNIT:
                    $unit_id = $value['target_id'];
                    $unit    = D('CommunityUnits')->where(['unit_id' => $unit_id])->find();
                    if (empty($unit)) {
                        return;
                    }
                    $company_id = $unit['company_id'];
                    $this->setTemplate($company_id, $content, $options, function ($content, $user_ids) use ($order_id, $template_id, $unit_id, $user_wx_mapping_model, $source) {
                        $unit_ids = D('CommunityUnits')->where(['pid' => $unit_id, 'is_end' => 1])->getField('unit_id', true) ? : [];
                        array_push($unit_ids, $unit_id);
                        $cert = D('CommunityRoomCert')->alias('a')->join('__COMMUNITY_ROOMS__ b on a.room_id = b.room_id')
                            ->where(['b.unit_id' => ['in', $unit_ids], 'a.state' => 1, 'b.state' => ['neq', -1], 'a.type' => ['in', '1,2,3']])->field('a.*')->select();
                        if (!empty($cert)) {
                            $tmp = [];
                            foreach ($cert as $item) {
                                if ($this->filterRead($item['user_id'], $user_ids)) continue;
                                $tmp[] = $item['user_id'];
                            }
                            $this->sendTemplateMessage($tmp, $content, ['order_id' => $order_id, 'template_id' => $template_id, 'source' => $source, 'template_id' => $template_id]);
                        }
                    });
                    break;
                case NoticesModel::TYPE_ROOM:
                    $room_id = $value['target_id'];
                    $room    = D('CommunityRooms')->where(['room_id' => $room_id])->find();
                    if (empty($room)) {
                        return;
                    }
                    $company_id = $room['company_id'];

                    $this->setTemplate($company_id, $content, $options, function ($content, $user_ids) use ($order_id, $template_id, $room_id, $user_wx_mapping_model, $source) {

                        $cert = D('CommunityRoomCert')->alias('a')->join('__COMMUNITY_ROOMS__ b on a.room_id = b.room_id')
                            ->where(['a.room_id' => $room_id, 'a.state' => 1, 'b.state' => ['neq', -1], 'a.type' => ['in', '1,2,3']])->field('a.*')->select();

                        $tmp = [];
                        foreach ($cert as $item) {
                            if ($this->filterRead($item['user_id'], $user_ids)) continue;
                            $tmp[] = $item['user_id'];
                        }
                        $this->sendTemplateMessage($tmp, $content, ['order_id' => $order_id, 'template_id' => $template_id, 'source' => $source, 'template_id' => $template_id]);
                    });

                    break;
                case 5:
                    $user_id      = $value['target_id'];
                    $company_id   = $value['company_id'];
                    $community_id = $value['community_id'];
                    $this->setCommunityId($community_id);
                    $this->setTemplate($company_id, $content, $options, function ($content) use ($order_id, $template_id, $user_id, $user_wx_mapping_model, $source) {
                        sleep(0.5);
                        $this->sendTemplateMessage([$user_id], $content, ['order_id' => $order_id, 'template_id' => $template_id, 'source' => $source, 'template_id' => $template_id]);
                    });
                    break;
                default :
                    break;
            }
        }

        \Think\Log::write('结束运行:---------------------------------------------------');
    }

    private function setTemplate($company_id, &$content, $array = [], callable $callback)
    {
        $this->setCompanyId($company_id);
        $wx_publics_model = D('WxPublics');
        $wx_publics       = [];
        if (false === isset($wx_publics[$company_id])) {
            $wx_publics[$company_id] = $wx_publics_model->alias('wx')->join('__COMPANYS__ a on wx.token = a.token')->where(['a.id' => $company_id])->field('wx.*')->find();
        }
        $this->setWxPublic($wx_publics[$company_id]);
        $wx_public = $this->getWxPublic();
        $options   = [
            'app_id' => $wx_public['appid'],
            'secret' => $wx_public['appsecret'],
            'token'  => $wx_public['token'],
            'debug'  => false
        ];
        $this->setWxapi(new \Common\Lib\Wxapi($options));
        $this->getTemplateContent($content);
        $recipient    = $this->args['recipient'];
        $host         = $this->args['host'];
        $source       = $recipient['source_code'];
        $mod          = '';
        $community_id = $this->getCommunityId();
        array_key_exists('other', $array) && $array['other'] && $mod = 'me';
        $user_ids = [];
        switch (strtolower($source)) {
            case 'nt': //公告
                if ($recipient['filter'] == 'read') {
                    $user_ids = D('UserRead')->where(['source_code' => 'NT', 'order_id' => $array['order_id']])->getField('user_id', true);
                }
                $tmp = '';
                if (!empty($community_id)) {
                    $tmp = '&community=' . $community_id;
                }
                $content['url'] = $host . '/wap/index/forward/wx/' . $wx_public['wx_id'] . '?redirect=' . ($mod != '' ? urlencode('/me/notices/detail?id=' . $array['order_id']) : urlencode('/notice/detail?id=' . $array['order_id'])) . $tmp;
                break;
            case 'ls':
                $content['url'] = $host . '/wap/index/forward/wx/' . $wx_public['wx_id'] . '?redirect=' . urlencode('/activity/detail?id=' . $array['order_id']);
                break;
            case 'ex':
                $content['url'] = $host . '/wap/index/forward/wx/' . $wx_public['wx_id'] . '?redirect=' . urlencode('/express/detail?id=' . $array['order_id']);
                break;
            case 'cr':
                $content['url'] = $host . '/wap/index/forward/wx/' . $wx_public['wx_id'] . '?redirect=' . urlencode('/me/property');
                break;
            case 're':
                if (!empty($community_id)) {
                    $tmp = '&community=' . $community_id;
                }
                $content['url'] = $host . '/wap/index/forward/wx/' . $wx_public['wx_id'] . '?redirect=' . urlencode('/repair/detail?id=' . $array['order_id'] . '&mod=' . $mod) . $tmp;
                break;
            case 'co':
                $content['url'] = $host . '/wap/index/forward/wx/' . $wx_public['wx_id'] . '?redirect=' . ($mod != '' ? urlencode('/me/complaint/detail?id=' . $array['order_id']) : urlencode('/suggest/details?id=' . $array['order_id']));
                break;
            case 'gu':
                $content['url'] = $host . '/wap/index/forward/wx/' . $wx_public['wx_id'] . '?redirect=' . ($mod != '' ? urlencode('/me/guides/detail?id=' . $array['order_id']) : urlencode('/instructions/detail?id=' . $array['order_id']));
                break;
            case 'cert':
                $content['url'] = $host . '/wap/index/forward/wx/' . $wx_public['wx_id'] . '?redirect=' . urlencode('/me/ownermanage/detail?id=' . $array['order_id']);
                break;
            case 'bi':
                $tmp = '';
                if (!empty($community_id)) {
                    $tmp = '&community=' . $community_id;
                }
                if ($mod == '') {
                    if ($array['order_id'])
                        $content['url'] = $host . '/wap/index/forward/wx/' . $wx_public['wx_id'] . '?redirect=' . urlencode('/bills/detail?id=' . $array['order_id']) . $tmp;
                    else
                        $content['url'] = $host . '/wap/index/forward/wx/' . $wx_public['wx_id'] . '?redirect=' . urlencode('/bills') . $tmp;
                }
                break;
            case 'comsg':
                $content['url'] = $host . '/wap/index/forward/wx/' . $wx_public['wx_id'] . '?redirect=' . ($mod != '' ? urlencode('/me/complaint/detail?id=' . $array['order_id']) : urlencode('/suggest/details?id=' . $array['order_id']));
                break;
            default:
                break;
        }
        if (is_callable($callback)) {
            $callback($content, $user_ids);
        }
    }

    private function wxTemplateHist($mapping, $order_id, $content, $template_id, $result, $source)
    {
        $data = [
            'template_id'    => $template_id,
            'content'        => json_encode($content),
            'result'         => json_encode($result),
            'state'          => $result['errcode'],
            'source'         => 0,
            'order_id'       => $order_id,
            'user_id'        => $mapping['user_id'],
            'mapping_id'     => $mapping['mapping_id'],
            'create_time'    => time(),
            'create_user_id' => 0,
            'source_code'    => $source
        ];

        D('WxTemplateHist')->add($data);
    }

    private function getTemplateContent(&$content)
    {
        $company_id                = $this->getCompanyId();
        $wxapi                     = $this->getWxapi();
        $wx_public                 = $this->getWxPublic();
        $company_wx_template_model = D('CompanyWxTemplate');
        $wx_template_model         = D('WxTemplate');
        $template_id               = $content['template_id'];
        try {
            $wx_templates = $wxapi->getPrivateTemplates()->toArray()['template_list'];
        } catch (\Exception $e) {
            $code = (int)$e->getCode();
            if ($code == 40001) {
                $token        = $wxapi->getApp()->access_token->getToken(true);
                $wx_templates = $wxapi->getApp()->notice->setAccessToken($token)->getPrivateTemplates();
            } else {
                D('log')->add(['cat' => __FUNCTION__, 'log' => (string)$e]);
            }
        }
        $company_wx_template = $company_wx_template_model->where(['template_id' => $template_id, 'company_id' => $company_id, 'wx_id' => $wx_public['wx_id']])->find();
        if (!empty($wx_templates)) {
            $tmp = [];
            foreach ($wx_templates as $template) {
                array_push($tmp, $template['template_id']);
            }
            if (!empty($company_wx_template)) {
                if (!in_array($company_wx_template['code'], $tmp, true)) {
                    D('CompanyWxTemplate')->where(['template_id' => $template_id, 'company_id' => $this->getCompanyId(), 'wx_id' => $this->getWxPublic()['wx_id']])->delete();
                }
            }
        }
        $company_wx_template = $company_wx_template_model->where(['template_id' => $template_id, 'company_id' => $company_id, 'wx_id' => $wx_public['wx_id']])->find();
        //获取模板id
        if (empty($company_wx_template)) {
            $wx_template = $wx_template_model->where(['template_id' => $template_id])->find();
            $result      = $wxapi->addTemplate($wx_template['code']);
            if ((int)$result['errcode'] < 0) {
                D('log')->add(['cat' => 'SendWxTemplate', 'log' => json_encode($result)]);
            }
            if (isset($result['template_id'])) {
                $content['template_id'] = $result['template_id'];
                $company_wx_template_model->add([
                    'template_id' => $template_id,
                    'company_id'  => $company_id,
                    'wx_id'       => $wx_public['wx_id'],
                    'code'        => $content['template_id']
                ]);
            }
        } else {
            $content['template_id'] = $company_wx_template['code'];
        }
    }

    /**
     * @param $user_ids 用户id数组
     * @param $content 模板内容
     * @param array $hist 历史记录数组
     */
    private function sendTemplateMessage($user_ids, $content, $hist = [])
    {
        $wx_public       = $this->wx_public;
        $wxapi           = $this->wxapi;
        if (empty($user_ids)) return;
        $user_wx_mapping = D('UserWxMapping')->where(['wx_id' => $wx_public['wx_id'], 'user_id' => ['in', implode(',', $user_ids)]])->field('mapping_id,user_id,openid')->select();
        foreach ($user_wx_mapping as $value) {
            $content['touser'] = $value['openid'];
            try {
                $result = $wxapi->sendTemplateMessage($content);
                $this->wxTemplateHist($value, $hist['order_id'], $content, $hist['template_id'], $result, $hist['source']);
            } catch (\Exception $e) {
                D('log')->add(['cat' => 'wx_template', 'log' => (string)$e]);
            }
        }
    }

    private function filterRead($user_id, $user_ids)
    {
        $recipient = $this->args['recipient'];
        if ($recipient['filter'] == 'read') {
            return in_array($user_id, $user_ids, true);
        }

        return false;
    }
}