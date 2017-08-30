<?php


namespace Common\Controller;


use Api\Lib\ErrorCode;
use Common\Controller\Extra\AccessToken;
use EasyWeChat\Core\Exception;
use Lcobucci\JWT\Token;
use Think\Controller\RestController;


abstract class ApiRestController extends RestController
{
    use AccessToken;

    const TYPE_WX = 'wx'; // 微信

    const TYPE_APP = 'app'; // App

    protected $allowMethod = ['get', 'post', 'put', 'delete', 'resource'];

    protected $allowType = ['xml', 'json'];

    protected $check_key = false;

    protected $key;

    protected $check_token = true;

    protected $sbdata = [];

    public function __construct()
    {
        parent::__construct();
        $storage_setting = sp_get_cmf_settings('storage');
        C('FILE_UPLOAD_TYPE', $storage_setting['type']);
        C('UPLOAD_TYPE_CONFIG', true === isset($storage_setting[$storage_setting['type']]) ? $storage_setting[$storage_setting['type']] : []);
    }

    function _initialize()
    {
//        define('DOMAIN', '.gd-hc.com.cn');
//        ini_set('session.cookie_path', '/');
//        ini_set('session.cookie_domain', DOMAIN);
        if (!empty($_REQUEST['PHPSESSID'])) {
            session(['id' => $_REQUEST['PHPSESSID'], 'path' => '/', 'domain' => '.gd-hc.com.cn', 'expire' => 3600]);
            $tmp          = array_merge($_GET, $_POST, $_REQUEST, session());
            $this->sbdata = $tmp;
        }
        $this->_type = I('request.format', 'json');
        $this->key   = I('request.key', false);
        if ($this->check_token) {
            $this->checkAccessToken(I('request.access_token'));
        }
        if ($this->check_key) {
            if (!$this->key) {
                $this->res([], -3, 'invaild key param');
            }
        }
    }

    /**
     * 获取当前物业公司id
     * @return bool|mixed
     */
    public function getCompanyId()
    {
        try {
            $companyid = $this->access_token->getClaim('companyid');
        } catch (\Exception $e) {
            $companyid = false;
        }
        if (!$companyid) {
            $communityid = $this->getCommunityId();
            $companyid   = D('Communities')->where(['community_id' => $communityid])->getField('company_id');
        }

        return $companyid;
    }

    /**
     * 获取用户openid
     * @return bool|mixed
     */
    public function getOpenId()
    {
        if (is_null($this->access_token)) {
            return false;
        }
        try {
            $openid = $this->access_token->getClaim('openid');
        } catch (\Exception $e) {
            $openid = false;
        }

//        return !empty(session('user_wx_openid')) ? session('user_wx_openid') : false; //旧的
        return $openid;
    }

    /**
     * 设置小区
     * @param $communityId
     */
    public function setCommunityId($communityId)
    {
//        $old_community_id = session('last_community_id');
        $user_id = $this->getUserId();
        if (!empty($user_id)) {
            D('Users')->where(['id' => $user_id])->save(['mobile_last_login_community' => $communityId]);
        }
        $user_wx_openid = $this->getOpenId();
        if (!empty($user_wx_openid)) {
            D('UserWxMapping')->where(['openid' => $user_wx_openid])->save(['last_login_community' => $communityId, 'update_time' => time()]);
        }
        $imei = $this->getImei();
        if (!empty($imei)) {
            D('UserAppInfo')->where(['imei' => $imei])->save(['last_login_community' => $communityId]);
        }
        session('last_community_id', $communityId);
    }

    /**
     * 获取小区id
     * @return bool|mixed
     */
    public function getCommunityId()
    {
        if (!empty(session('last_community_id'))) {
            return session('last_community_id');
        } else {
            $user_id = $this->getUserId();
            $type    = $this->getType();
            if ($user_id && $type) {
                if ($type == self::TYPE_WX) {
                    $wx   = $this->getWxid();
                    $data = D('UserWxMapping')->where(['wx_id' => $wx, 'user_id' => $user_id])->find();

                    return $data['last_login_community'];
                }

                if ($type == self::TYPE_APP) {
                    $data = D('UserAppInfo')->where(['user_id' => $user_id])->find();

                    return $data['last_login_community'];
                }
            } else {
                $imei = $this->getImei();
                if ($imei) {
                    $data = D('UserAppInfo')->where(['imei' => $imei])->find();

                    return $data['last_login_community'];
                }
            }

            return false;
        }

        return !empty(session('last_community_id')) ? session('last_community_id') : false;
    }

    public function getImei()
    {
        if (is_null($this->access_token)) {
            return false;
        }
        try {
            $imei = $this->access_token->getClaim('imei', false);
        } catch (\Exception $e) {
            $imei = false;
        }

//        return !empty(session('user_wx_openid')) ? session('user_wx_openid') : false; //旧的
        return $imei;
    }

    /**
     * 获取保存再token的数据
     * @param Token $token 权限token
     * @param array $data 扩展数据
     * @return array
     */
    public function getTokenData($token, $data = [])
    {
        try {
            $userId = $token->getClaim('userId', false);
        } catch (\Exception $e) {
            $userId = false;
        }
        try {
            $companyid = $token->getClaim('companyid', false);
        } catch (\Exception $e) {
            $companyid = false;
        }
        try {
            $openid = $token->getClaim('openid', false);
        } catch (\Exception $e) {
            $openid = false;
        }
        try {
            $type = $token->getClaim('type', false);
        } catch (\Exception $e) {
            $type = false;
        }
        try {
            $wx = $token->getClaim('wx', false);
        } catch (\Exception $e) {
            $wx = false;
        }
        try {
            $wxappid = $token->getClaim('wxappid', false);
        } catch (\Exception $e) {
            $wxappid = false;
        }
        try {
            $imei = $token->getClaim('imei', false);
        } catch (\Exception $e) {
            $imei = false;
        }

        return array_merge(compact('userId', 'companyid', 'openid', 'type', 'wx', 'wxappid', 'imei'), $data);
    }

    public function getWxappid()
    {
        try {
            $appid = $this->access_token->getClaim('wxappid', false);
        } catch (\Exception $e) {
            $appid = false;
        }

        return $appid;
    }

    /**
     * 获取当前微信公众号
     * @return bool|mixed
     */
    public function getWxid()
    {
        try {
            $wxid = $this->access_token->getClaim('wx', false);
        } catch (\Exception $e) {
            $wxid = false;
        }

        return $wxid;
    }

    public function res($data = null, $code = 0, $msg = 'success', $statusCode = 200)
    {
//        header('Access-Control-Allow-Origin:*');
        header("Access-Control-Allow-Origin:" . $_SERVER['HTTP_ORIGIN']);
        header("Access-Control-Allow-Headers:Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        header('Access-Control-Allow-Credentials: true');
        $result = ['code' => $code, 'msg' => $msg, 'data' => $data];
        if (empty($data)) unset($result['data']);
        $this->response($result, $this->_type, $statusCode);
    }

    public function getuserinfo($user)
    {
        return [
            'userId'                      => $user['id'],
            'headImg'                     => empty($user['avatar']) ? api_wx_user()['headimgurl'] : sp_get_image_url($user['avatar']),
            'nickName'                    => $user['user_nicename'],
            'mobile_last_login_community' => $user['mobile_last_login_community'],
            'mobile'                      => $user['mobile'],
            'jf'                          => (int)$user['credits']
        ];
    }

    public function getMobile()
    {
        if ($this->getUserId()) {
            $user = D('Users')->where(['id' => $this->getUserId()])->find();

            return $user['mobile'];
        }
        
        return false;
    }

    public function is_admin()
    {
        if ($this->getUserId() && $this->getCompanyId() && $this->getCommunityId()) {
            return is_admin($this->getCompanyId(), $this->getCommunityId(), $this->getUserId());
        }

        return false;
    }

    public function getInfoId()
    {
        $imei = $this->getImei();
        if (!$imei) return false;
        $info_id = D('UserAppInfo')->where(['imei' => $imei])->getField('info_id');

        return $info_id ?: false;
    }

    public function getMappingId()
    {
        $openid = $this->getOpenId();
        if (!$openid) return false;
        $mapping_id = D('UserWxMapping')->where(['openid' => $openid])->getField('mapping_id');

        return $mapping_id;
    }

    /**
     * 模板显示
     * @param type $templateFile 指定要调用的模板文件
     * @param type $charset 输出编码
     * @param type $contentType 输出类型
     * @param string $content 输出内容
     * 此方法作用在于实现后台模板直接存放在各自项目目录下。例如Admin项目的后台模板，直接存放在Admin/Tpl/目录下
     */
    public function display_managment($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '')
    {
        parent::display($this->parseTemplate_managment($templateFile), $charset, $contentType, $content, $prefix);
    }

    /**
     * 自动定位模板文件
     * @access protected
     * @param string $template 模板文件规则
     * @return string
     */
    public function parseTemplate_managment($template = '')
    {
        $tmpl_path = C("SP_MANAGMENT_TMPL_PATH");
        define("SP_TMPL_PATH", $tmpl_path);
        if ($this->theme) { // 指定模板主题
            $theme = $this->theme;
        } else {
            // 获取当前主题名称
            $theme = C('SP_ADMIN_DEFAULT_THEME');
        }

        if (is_file($template)) {
            // 获取当前主题的模版路径
            define('THEME_PATH', $tmpl_path . $theme . "/");

            return $template;
        }
        $depr     = C('TMPL_FILE_DEPR');
        $template = str_replace(':', $depr, $template);

        // 获取当前模块
        $module = MODULE_NAME . "/";
        if (strpos($template, '@')) { // 跨模块调用模版文件
            list($module, $template) = explode('@', $template);
        }

        $module = $module . "/";

        // 获取当前主题的模版路径
        define('THEME_PATH', $tmpl_path . $theme . "/");

        // 分析模板文件规则
        if ('' == $template) {
            // 如果模板文件名为空 按照默认规则定位
            $template = CONTROLLER_NAME . $depr . ACTION_NAME;
        } else if (false === strpos($template, '/')) {
            $template = CONTROLLER_NAME . $depr . $template;
        }

        $cdn_settings = sp_get_option('cdn_settings');
        if (!empty($cdn_settings['cdn_static_root'])) {
            $cdn_static_root = rtrim($cdn_settings['cdn_static_root'], '/');
            C("TMPL_PARSE_STRING.__TMPL__", $cdn_static_root . "/" . THEME_PATH);
            C("TMPL_PARSE_STRING.__PUBLIC__", $cdn_static_root . "/public");
            C("TMPL_PARSE_STRING.__WEB_ROOT__", $cdn_static_root);
        } else {
            C("TMPL_PARSE_STRING.__TMPL__", __ROOT__ . "/" . THEME_PATH);
        }


        C('SP_VIEW_PATH', $tmpl_path);
        C('DEFAULT_THEME', $theme);
        define("SP_CURRENT_THEME", $theme);

        $file = sp_add_template_file_suffix(THEME_PATH . $module . $template);
        $file = str_replace("//", '/', $file);
        if (!file_exists_case($file)) E(L('_TEMPLATE_NOT_EXIST_') . ':' . $file);

        return $file;
    }

    /**
     * 获取用户平均分数
     * @param array $option
     * @return boolean
     */
    public function getGradeAge($option = [])
    {
        if (is_array($option) || $option = []) {
            if ($option['user_id'] == 0) {
                $option['user_id'] = $this->getUserId();
            }
            if ($option['company_id'] == 0) {
                $option['company_id'] = $this->getCompanyId();
            }
            if ($option['community_id'] == 0) {
                $option['community_id'] = $this->getCommunityId();
            }

            $appraise = D('UserAppraise')->where($option)->find();
            if ($appraise) {
            	$grade_age = $appraise['grade_age']<=0 || $appraise['grade_age']>5?5.0:$appraise['grade_age'];
                return $grade_age;
            } else {
                return 5.0;
            }
        } else {
            return false;
        }

    }

    /**
     * 获取用户积分排名
     */
    public function getRank($user_id)
    {
        if ($user_id == 0) {
            $user_id = $this->getUserId();
        }
        $my_community_rank_query = function () {
            return M()->table('(
                    SELECT a.*, @rank:=@rank+1 AS pm
                    FROM (
                    SELECT DISTINCT c.user_id,u.user_name,u.credits,u.user_nicename
                    FROM hc_community_room_cert c
                    INNER JOIN hc_users u ON c.user_id = u.id AND u.user_status>0
                    WHERE c.type = 1 AND c.state = 1 AND c.community_id = \'' . $this->getCommunityId() . '\'
                    ORDER BY u.credits DESC) AS a, (
                    SELECT @rank:=0) b) AS tmp');
        };
        $my_rank                 = 0;
        $my_community_rank       = $my_community_rank_query()->where('tmp.user_id = ' . $user_id)->find();
        if (!empty($my_community_rank)) {
            $my_rank = $my_community_rank['pm'];
        }

        return $my_rank;
    }

    /**
     * 获取用户积分
     */
    public function getCredits($user_id = 0)
    {
        if ($user_id == 0) {
            $user_id = $this->getUserId();
        }
        $credit = 0;
        $user   = D('Users')->where(['id' => $user_id])->find();
        $credit = $user['credits'];

        return $credit;
    }

}