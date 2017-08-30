<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/23
 * Time: 17:00
 */

namespace Common\Controller;

class OperatebaseController extends AppframeController 
{
    protected $app_menu = array('Admin','Api','Asset','Comment','Common','Company','Demo','Install','Portal','User','Wechat','Operate');
    protected $menus;
    protected $no_need_check_rules = [];

    public function __construct()
    {
        hook('admin_begin');
        $admintpl_path=C("SP_ADMIN_TMPL_PATH").C("SP_ADMIN_DEFAULT_THEME")."/";
        C("TMPL_ACTION_SUCCESS",$admintpl_path.C("SP_ADMIN_TMPL_ACTION_SUCCESS"));
        C("TMPL_ACTION_ERROR",$admintpl_path.C("SP_ADMIN_TMPL_ACTION_ERROR"));
        parent::__construct();
        $time=time();
        $this->assign("js_debug",APP_DEBUG?"?v=$time":"");

        C('APP_MENU', $this->app_menu);
         //缓存菜单 
        $temp_menu='menus_'.session('ADMIN_ID');
        if(empty(S($temp_menu))){
            $this->menus = D("Common/Menu")->menu_json();
            S($temp_menu,$this->menus,3000);
        }else{
            $this->menus = S($temp_menu) ;
        }
        $this->assign("menus",$this->menus);
    }

    function _initialize()
    {
        parent::_initialize();
        define("TMPL_PATH", C("SP_MANAGMENT_TMPL_PATH"));
        $this->no_need_check_rules= array_merge($this->no_need_check_rules,["OperatePublicLogo",'OperateIndexWelcome']);
        
        //暂时取消后台多语言
        $this->load_app_admin_menu_lang();

        $session_admin_id=session('ADMIN_ID');
        if(!empty($session_admin_id) && empty(session('LOGIN_TYPE'))){
            $users_obj= M("Users");
            $user=$users_obj->where(array('id'=>$session_admin_id))->find();
            if(!$this->check_access($session_admin_id)){
                $this->error("您没有访问权限！");
            }
            $this->assign("admin",$user);
        }else{

            if(IS_AJAX){
                $this->error("您还没有登录！",U("admin/public/login"));
            }else{
                header("Location:".U("Operate/public/login"));
                exit();
            }
        }
        
    }

    /**
     * 初始化后台菜单
     */
    public function initMenu()
    {
        $Menu = F("Menu");
        if (!$Menu) {
            $Menu=D("Common/Menu")->menu_cache();
        }
        return $Menu;
    }

    /**
     * 消息提示
     * @param string $message
     * @param string $jumpUrl
     * @param bool $ajax
     */
    public function success($message = '', $jumpUrl = '', $ajax = false)
    {
        parent::success($message, $jumpUrl, $ajax);
    }

    /**
     * 模板显示
     * @param string $templateFile
     * @param string $charset
     * @param string $contentType
     * @param string $content
     * @param string $prefix
     * 此方法作用在于实现后台模板直接存放在各自项目目录下。例如Admin项目的后台模板，直接存放在Admin/Tpl/目录下
     */
    public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '')
    {
        parent::display($this->parseTemplate($templateFile), $charset, $contentType,$content,$prefix);
    }

    /**
     * 获取输出页面内容
     * 调用内置的模板引擎fetch方法，
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $content 模板输出内容
     * @param string $prefix 模板缓存前缀*
     * @return string
     */
    public function fetch($templateFile='',$content='',$prefix='')
    {
        $templateFile = empty($content)?$this->parseTemplate($templateFile):'';
        return parent::fetch($templateFile,$content,$prefix);
    }

    /**
     * 自动定位模板文件
     * @access protected
     * @param string $template 模板文件规则
     * @return string
     */
    public function parseTemplate($template='')
    {
        $tmpl_path=C("SP_MANAGMENT_TMPL_PATH");
        define("SP_TMPL_PATH", $tmpl_path);
        if($this->theme) { // 指定模板主题
            $theme = $this->theme;
        }else{
            // 获取当前主题名称
            $theme      =    C('SP_ADMIN_DEFAULT_THEME');
        }

        if(is_file($template)) {
            // 获取当前主题的模版路径
            define('THEME_PATH',   $tmpl_path.$theme."/");
            return $template;
        }
        $depr       =   C('TMPL_FILE_DEPR');
        $template   =   str_replace(':', $depr, $template);

        // 获取当前模块
        $module   =  MODULE_NAME."/";
        if(strpos($template,'@')){ // 跨模块调用模版文件
            list($module,$template)  =   explode('@',$template);
        }

        $module =$module."/";

        // 获取当前主题的模版路径
        define('THEME_PATH',   $tmpl_path.$theme."/");

        // 分析模板文件规则
        if('' == $template) {
            // 如果模板文件名为空 按照默认规则定位
            $template = CONTROLLER_NAME . $depr . ACTION_NAME;
        }elseif(false === strpos($template, '/')){
            $template = CONTROLLER_NAME . $depr . $template;
        }

        $cdn_settings=sp_get_option('cdn_settings');
        if(!empty($cdn_settings['cdn_static_root'])){
            $cdn_static_root=rtrim($cdn_settings['cdn_static_root'],'/');
            C("TMPL_PARSE_STRING.__TMPL__",$cdn_static_root."/".THEME_PATH);
            C("TMPL_PARSE_STRING.__PUBLIC__",$cdn_static_root."/public");
            C("TMPL_PARSE_STRING.__WEB_ROOT__",$cdn_static_root);
        }else{
            C("TMPL_PARSE_STRING.__TMPL__",__ROOT__."/".THEME_PATH);
        }


        C('SP_VIEW_PATH',$tmpl_path);
        C('DEFAULT_THEME',$theme);
        define("SP_CURRENT_THEME", $theme);

        $file = sp_add_template_file_suffix(THEME_PATH.$module.$template);
        $file= str_replace("//",'/',$file);
        if(!file_exists_case($file)) E(L('_TEMPLATE_NOT_EXIST_').':'.$file);
        return $file;
    }
    
    /**
     * 获取当前登录的默认查询条件
     *
     * @param unknown $map        	
     * @return mixed
     */
	public function getCurrentLoginQuery($map) {
		if ($this->login_type == 1) {
			$map ["company_id"] = sp_get_current_company_id ();
		} elseif ($this->login_type == 2) {
			$map ["community_id"] = sp_get_current_community_id ();
		}
		return $map;
	}

    /**
     * 排序 排序字段为listorders数组 POST 排序字段为：listorder或者自定义字段
     * @param $model 需要排序的模型类
     * @param string $custom_field 自定义排序字段 默认为listorder,可以改为自己的排序字段
     * @return bool
     */
    protected function _listorders($model,$custom_field='')
    {
        if (!is_object($model)) {
            return false;
        }
        $field=empty($custom_field)&&is_string($custom_field)?'listorder':$custom_field;
        $pk = $model->getPk(); //获取主键名称
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data[$field] = $r;
            $model->where(array($pk => $key))->save($data);
        }
        return true;
    }

    /**
     *
     * {@inheritDoc}
     * @see \Common\Controller\AppframeController::page()
     */
    protected function page($total_size = 1, $page_size = 0, $current_page = 1, $listRows = 6, $pageParam = '', $pageLink = '', $static = false)
    {
        if ($page_size == 0) {
            $page_size = C("PAGE_LISTROWS");
        }

        if (empty($pageParam)) {
            $pageParam = C("VAR_PAGE");
        }

        $page = new \Page($total_size, $page_size, $current_page, $listRows, $pageParam, $pageLink, $static);
        $page->SetPager('Admin', '{first}{prev}&nbsp;{liststart}{list}&nbsp;{next}{last}<span>共{recordcount}条数据</span>', array("listlong" => "4", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
        return $page;
    }

    /**
     *  检查后台用户访问权限
     * @param int $uid 后台用户id
     * @return boolean 检查通过返回true
     */
    private function check_access($uid)
    {
        //如果用户角色是1，则无需判断
        if($uid == 1){
            return true;
        }

        $rule= strtolower(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME);
        if (!empty($this->no_need_check_rules)) {
            $tmp = [];
            foreach ($this->no_need_check_rules as $item) {
                $tmp[] = strtolower($item);
            }
            $this->no_need_check_rules = $tmp;
        }
        if( !in_array($rule,$this->no_need_check_rules) ){
            return sp_auth_check($uid);
        }else{
            return true;
        }
    }

    private function _check_access()
    {
        $session_admin_id=session('ADMIN_ID');
        if(!empty($session_admin_id) && !empty($this->login_type)){
            $users_obj= M("Users");
            $user=$users_obj->where(array('id'=>$session_admin_id))->find();
            $user['setting'] = json_decode($user['setting'], true);
            $is_access = $this->check_access($session_admin_id);
            if (!$is_access) {
                $this->error("您没有访问权限！");
            }
            $this->assign("admin",$user);
        }else{
            if(IS_AJAX){
                $this->error("您还没有登录！",U("managment/public/login"));
            }else{
                header("Location:".U("managment/public/login"));
                exit();
            }
        }
    }

    /**
     * 加载后台用户语言包
     */
    private function load_app_admin_menu_lang()
    {
        $default_lang=C('DEFAULT_LANG');
        $langSet=C('ADMIN_LANG_SWITCH_ON',null,false)?LANG_SET:$default_lang;
//        if($default_lang!=$langSet){
//            $admin_menu_lang_file=SPAPP.MODULE_NAME."/Lang/".$langSet."/admin_menu.php";
//        }else{
//            $admin_menu_lang_file=SITE_PATH."data/lang/".MODULE_NAME."/Lang/$langSet/admin_menu.php";
//            if(!file_exists_case($admin_menu_lang_file)){
//                $admin_menu_lang_file=SPAPP.MODULE_NAME."/Lang/".$langSet."/admin_menu.php";
//            }
//        }
        $admin_menu_lang_file=SPAPP.MODULE_NAME."/Lang/".$langSet."/admin_menu.php";
        if(is_file($admin_menu_lang_file)){
            $lang=include $admin_menu_lang_file;
            L($lang);
        }
    }

    private function _check_is_admin()
    {
        $this->is_admin = false;
        if (!is_bool(session('is_admin'))) {
            if ($this->login_type) {
                if ($this->login_type == 1) {
                    $company_id = sp_get_current_company_id();
                    $company = D('Companys')->field('user_id')->find($company_id);
                    $this->is_admin = $company['user_id'] == sp_get_current_admin_id();
                } elseif ($this->login_type == 2) {
                    $community_id = sp_get_current_community_id();
                    $community = D('Communities')->field('user_id')->find($community_id);
                    $this->is_admin = $community['user_id'] == sp_get_current_admin_id();
                }
            }
            session('is_admin', $this->is_admin);
        }
        $this->is_admin = session('is_admin');
        $this->assign('is_admin', $this->is_admin);
    }

    private function load_menu_lang()
    {
        $default_lang=C('DEFAULT_LANG');

        $langSet=C('ADMIN_LANG_SWITCH_ON',null,false)?LANG_SET:$default_lang;

        $apps=sp_scan_dir(SPAPP."*",GLOB_ONLYDIR);
        $error_menus=array();
        foreach ($apps as $app){
            if(is_dir(SPAPP.$app)){
                if($default_lang!=$langSet){
                    $admin_menu_lang_file=SPAPP.$app."/Lang/".$langSet."/admin_menu.php";
                }else{
                    $admin_menu_lang_file=SITE_PATH."data/lang/$app/Lang/".$langSet."/admin_menu.php";
                    if(!file_exists_case($admin_menu_lang_file)){
                        $admin_menu_lang_file=SPAPP.$app."/Lang/".$langSet."/admin_menu.php";
                    }
                }

                if(is_file($admin_menu_lang_file)){
                    $lang=include $admin_menu_lang_file;
                    L($lang);
                }
            }
        }
    }
}