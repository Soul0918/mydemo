<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/11
 * Time: 9:41
 */

namespace Common\Model;


class UserLoginHistModel extends CommonModel
{

    public function history($user_id, $order_id, $source)
    {
        if (empty($source)) {
            return;
        }

        $ipinfo = $this->getIPInfo();
        $data   = [
            'user_id'    => $user_id,
            'source'     => $source,
            'order_id'   => $order_id,
            'login_time' => date('Y-m-d H:i:s'),
            'login_ip'   => get_client_ip(),
            'country'    => $ipinfo['country'] ?: '',
            'province'   => $ipinfo['region'] ?: '',
            'city'       => $ipinfo['city'] ?: '',
            'district'   => $ipinfo['county'] ?: '',
            //'area' => $ipinfo['area'] ? : '',
            'isp'        => $ipinfo['isp'] ?: '',
            'os'         => $this->getOS(),
            'browser'    => $this->getBrowser(),
            'lang'       => $this->getLang()
        ];

        $this->add($data);
        $data['history_id'] = $this->getLastInsID();

        return $data;
    }

    /**
     * 用户登录记录
     * @param $user_id
     * @param int $source
     * @return array
     */
    public function history_user($user_id, $source = 1)
    {
        return $this->history2($user_id, 0, 0, $source);
    }

    /**
     * 微信用户登录记录
     * @param $mapping_id
     * @return array
     */
    public function history_mapping($mapping_id)
    {
        return $this->history2(0, $mapping_id, 0, 1);
    }

    /**
     * 手机用户记录登录
     * @param $info_id
     * @return array
     */
    public function history_info($info_id)
    {
        return $this->history2(0, 0, $info_id, 2);
    }

    private function history2($user_id, $mapping_id, $info_id, $source)
    {
        $ipinfo = $this->getIPInfo();
        $data   = [
            'user_id'    => $user_id,
            'source'     => $source,
            'order_id'   => 0,
            'login_time' => date('Y-m-d H:i:s'),
            'login_ip'   => get_client_ip(),
            'country'    => $ipinfo['country'] ?: '',
            'province'   => $ipinfo['region'] ?: '',
            'city'       => $ipinfo['city'] ?: '',
            'district'   => $ipinfo['county'] ?: '',
            //'area' => $ipinfo['area'] ? : '',
            'isp'        => $ipinfo['isp'] ?: '',
            'os'         => $this->getOS(),
            'browser'    => $this->getBrowser(),
            'lang'       => $this->getLang(),
            'mapping_id' => $mapping_id,
            'info_id'    => $info_id
        ];
        $this->add($data);
        $data['history_id'] = $this->getLastInsID();

        return $data;
    }

    protected function getIPInfo()
    {
        $arrReturn = [
            "country"    => "", // 国家--中国
            "country_id" => "", // 国家编号--CN
            "area"       => "", // 区域--华南
            "area_id"    => "", // 区域ID--800000
            "region"     => "", // 省份--广东省
            "region_id"  => "", // 省份ID--440000
            "city"       => "", // 市--江门市
            "city_id"    => "", // 市ID--440700
            "county"     => "", // 地区--新会区
            "county_id"  => "", // 地区ID--440705
            "isp"        => "", // 运营商--电信
            "isp_id"     => "", // 运营商ID--100017
            "ip"         => ""
        ];

        if ($this->ip()) {
            // 淘宝IP接口，访问频率需小于10qps
//            $curl = new \Curl();
            $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . get_client_ip();
            $res = file_get_contents($url);
            $res = json_decode($res, true);
            if (( int )$res ["code"] == 0) {
                $arrReturn = $res ["data"];
            }
        }

        return $arrReturn;
    }

    private function ip()
    {
        return strpos(get_client_ip(), '0.0.0.0') === false
            && strpos(get_client_ip(), '127.0.0') === false
            && strpos(get_client_ip(), '192.168') === false;
    }

    private function getBrowser()
    {
        $Browser = $_SERVER ['HTTP_USER_AGENT'];
        if (preg_match('/MSIE/i', $Browser)) {
            $Browser = 'MSIE';
        } else if (preg_match('/Firefox/i', $Browser)) {
            $Browser = 'Firefox';
        } else if (preg_match('/Chrome/i', $Browser)) {
            $Browser = 'Chrome';
        } else if (preg_match('/Safari/i', $Browser)) {
            $Browser = 'Safari';
        } else if (preg_match('/Opera/i', $Browser)) {
            $Browser = 'Opera';
        } else {
            $Browser = 'Other';
        }

        return $Browser;
    }

    private function getLang()
    {
        if (!empty ($_SERVER ['HTTP_ACCEPT_LANGUAGE'])) {
            $lang = $_SERVER ['HTTP_ACCEPT_LANGUAGE'];
            $lang = substr($lang, 0, 5);
            if (preg_match("/zh-cn/i", $lang)) {
                $lang = "简体中文";
            } else if (preg_match("/zh/i", $lang)) {
                $lang = "繁体中文";
            } else {
                $lang = "English";
            }

            return $lang;
        } else {
            return "unknow";
        }
    }

    private function getOS()
    {
        $OS = $_SERVER ['HTTP_USER_AGENT'];
        if (preg_match('/win/i', $OS)) {
            $OS = 'Windows';
        } else if (preg_match('/mac/i', $OS)) {
            $OS = 'MAC';
        } else if (preg_match('/linux/i', $OS)) {
            $OS = 'Linux';
        } else if (preg_match('/unix/i', $OS)) {
            $OS = 'Unix';
        } else if (preg_match('/bsd/i', $OS)) {
            $OS = 'BSD';
        } else {
            $OS = 'Other';
        }

        return $OS;
    }
}