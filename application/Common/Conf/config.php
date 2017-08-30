<?php
if (file_exists("data/conf/db.php")) {
    $db = include "data/conf/db.php";
} else {
    $db = [];
}
if (file_exists("data/conf/config.php")) {
    $runtime_config = include "data/conf/config.php";
} else {
    $runtime_config = [];
}

if (file_exists("data/conf/route.php")) {
    $routes = include 'data/conf/route.php';
} else {
    $routes = [];
}

$configs = [
//    'ENV' => 'uat',
    "LOAD_EXT_FILE" => "extend",
    'UPLOADPATH' => 'data/upload/',
//        'SHOW_ERROR_MSG'        =>  true,    // 显示错误信息
    'SHOW_PAGE_TRACE' => false,
    'TMPL_STRIP_SPACE' => true,// 是否去除模板文件里面的html空格与换行
    'THIRD_UDER_ACCESS' => false, //第三方用户是否有全部权限，没有则需绑定本地账号
    /* 标签库 */
    'TAGLIB_BUILD_IN' => THINKCMF_CORE_TAGLIBS,
    'MODULE_ALLOW_LIST' => ['Admin', 'Portal', 'Asset', 'Api', 'User', 'Wx', 'Comment', 'Qiushi', 'Tpl', 'Topic', 'Install', 'Bug', 'Better', 'Pay', 'Cas', 'Company', 'Managment', 'Wap', 'Operate'],
    'TMPL_DETECT_THEME' => false,       // 自动侦测模板主题
    'TMPL_TEMPLATE_SUFFIX' => '.html',     // 默认模板文件后缀
    'DEFAULT_MODULE' => 'Portal',  // 默认模块
    'DEFAULT_CONTROLLER' => 'CompanyIndex', // 默认控制器名称
    'DEFAULT_ACTION' => 'index', // 默认操作名称
    'DEFAULT_M_LAYER' => 'Model', // 默认的模型层名称
    'DEFAULT_C_LAYER' => 'Controller', // 默认的控制器层名称

    'DEFAULT_FILTER' => 'htmlspecialchars', // 默认参数过滤方法 用于I函数...htmlspecialchars

    'LANG_SWITCH_ON' => true,   // 开启语言包功能
    'DEFAULT_LANG' => 'zh-cn', // 默认语言
    //'LANG_LIST'            => 'zh-cn,en-us,zh-tw',
    'LANG_LIST' => 'zh-cn',
    'LANG_AUTO_DETECT' => true,
    'ADMIN_LANG_SWITCH_ON' => false,   // 后台开启语言包功能

    'VAR_MODULE' => 'g',     // 默认模块获取变量
    'VAR_CONTROLLER' => 'm',    // 默认控制器获取变量
    'VAR_ACTION' => 'a',    // 默认操作获取变量

    'APP_USE_NAMESPACE' => true, // 关闭应用的命名空间定义
    'APP_AUTOLOAD_LAYER' => 'Controller,Model', // 模块自动加载的类库后缀

    'SP_TMPL_PATH' => 'themes/',       // 前台模板文件根目录
    'SP_DEFAULT_THEME' => 'simplebootx',       // 前台模板文件
    'SP_TMPL_ACTION_ERROR' => 'error', // 默认错误跳转对应的模板文件,注：相对于前台模板路径
    'SP_TMPL_ACTION_SUCCESS' => 'success', // 默认成功跳转对应的模板文件,注：相对于前台模板路径
    'SP_ADMIN_STYLE' => 'flat',
    'SP_ADMIN_TMPL_PATH' => 'admin/themes/',       // 各个项目后台模板文件根目录
    'SP_ADMIN_DEFAULT_THEME' => 'simplebootx',       // 各个项目后台模板文件
    'SP_MANAGMENT_TMPL_PATH' => 'themes/',
    'SP_MANAGMENT_DEFAULT_THEME' => 'simplebootx',
    'SP_ADMIN_TMPL_ACTION_ERROR' => 'Admin/error.html', // 默认错误跳转对应的模板文件,注：相对于后台模板路径
    'SP_ADMIN_TMPL_ACTION_SUCCESS' => 'Admin/success.html', // 默认成功跳转对应的模板文件,注：相对于后台模板路径
    'TMPL_EXCEPTION_FILE' => SITE_PATH . 'public/exception.html',

    'AUTOLOAD_NAMESPACE' => ['plugins' => './plugins/'], //扩展模块列表

    'ERROR_PAGE' => '',//不要设置，否则会让404变302

    'VAR_SESSION_ID' => 'session_id',

    "UCENTER_ENABLED" => 0, //UCenter 开启1, 关闭0
    "COMMENT_NEED_CHECK" => 0, //评论是否需审核 审核1，不审核0
    "COMMENT_TIME_INTERVAL" => 60, //评论时间间隔 单位s

    /* URL设置 */
    'URL_CASE_INSENSITIVE' => true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL' => 2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式，提供最好的用户体验和SEO支持
    'URL_PATHINFO_DEPR' => '/',    // PATHINFO模式下，各参数之间的分割符号
    'URL_HTML_SUFFIX' => '',  // URL伪静态后缀设置

    'VAR_PAGE' => "p",

    'URL_ROUTER_ON' => true,
    'URL_ROUTE_RULES' => array_merge($routes, [
        ['api/login', 'Api/User/login', '', ['method' => 'post']],
        ['api/register', 'Api/User/register', '', ['method' => 'post']],
        ['api/userinfo', 'Api/User/userinfo', '', ['method' => 'post']],
        ['api/avatar2', 'Api/User/avatar', '', ['method' => 'post']],
        ['api/home', 'Api/Home/index', '', ['method' => 'post']],
        ['api/communities', 'Api/Home/communities', '', ['method' => 'post']],
        ['api/appcommunities', 'Api/Home/appcommunities', '', ['method' => 'post']],
        ['api/change_community', 'Api/Home/change_community', '', ['method' => 'post']],
        ['api/sms', 'Api/User/sms', '', ['method' => 'post']],
        ['api/access_token', 'Api/Home/access_token', '', ['method' => 'get']],
        ['api/daydaysign', 'Api/Home/daydaysign', '', ['method' => 'post']],
        ['api/update_user', 'Api/User/update_user', '', ['method' => 'post']],
        ['api/test', 'Api/User/test', '', ['method' => 'get']],
        ['api/getwxuser', 'Api/User/getwxuser', ['method' => 'get']],
        ['api/andriodcheckupdate', 'Api/Home/app_andriodcheckupdate', ['method' => 'post']],
        ['api/getdeviceauth', 'Api/Home/app_getdeviceauth', ['method' => 'post']],
        ['api/notices', 'Api/Home/notices', ['method' => 'get']],
        ['api/logout', 'Api/User/logout', ['method' => 'post']],
        ['api/signpackage', 'Api/Home/signpackage', ['method' => 'post']],
        ['api/binddevice', 'Api/Home/binddevice', ['method' => 'post']],
        ['api/aboutus', 'Api/Home/aboutus', ['method' => 'post']],
        ['api/app/config', 'Api/Home/config', ['method' => 'get']],
        ['api/opendoorintegral', 'Api/Home/opendoorintegral', ['method' => 'post']],
        ['api/opendoorintegral2', 'Api/Home/opendoorintegral2', ['method' => 'post']],
        ['api/oauthcallback', 'Api/User/oauthcallback', ['method' => 'post']],
        ['api/app/download', 'Api/Home/download', ['method' => 'post']],
        ['api/wxzzcode', 'Api/Home/wxzzcode', ['method' => 'get']],
        ['api/startads', 'Api/Home/startads', ['method' => 'post']],
        ['Managment/Bills/index/:id', 'Managment/Bills/index', ['method' => 'get']],
        ['api/myrooms', 'Api/Home/rooms', ['method' => 'post']],
        ['api/doorhist', 'Api/Home/doorhist', ['method' => 'post']],
        ['api/nearbycommunity', 'Api/Home/nearbycommunity', ['method' => 'post']]
    ]),

    /*性能优化*/
    'OUTPUT_ENCODE' => true,// 页面压缩输出

    'HTML_CACHE_ON' => false, // 开启静态缓存
    'HTML_CACHE_TIME' => 60,   // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX' => '.html', // 设置静态缓存文件后缀

    'TMPL_PARSE_STRING' => [
        '__UPLOAD__' => __ROOT__ . '/data/upload/',
        '__STATICS__' => __ROOT__ . '/statics/',
        '__WEB_ROOT__' => __ROOT__
    ],

//    'QUEUE' => [
//        'type' => 'redis',
//        'host'   => '127.0.0.1',
//        //'host' => '10.66.243.114',
//        //'auth' => 'hckj3236052',
//
//        'port' => '6379',
//        'prefix' => 'queue'
//    ],
//        'SESSION_TYPE' => 'Db'
];

return array_merge($configs, $db, $runtime_config);
