<?php
namespace Common\Model;

class WxPublicsModel extends CommonModel
{
    protected $pk = 'wx_id';

    protected $_validate = [
        ['appid', 'require', 'APP ID不能为空'],
        ['appsecret', 'require', 'App Secret不能为空'],
        ['wx_original_id', 'require', '微信原始ID不能为空'],
//         ['mch_id', 'require', '微信支付分配的商户号不能为空 '],
//         ['api_key', 'require', '微信商户平台API密钥不能为空']
    ];

    protected $_auto = [
        ['create_time', 'time', 1, 'function'],
        ['update_time', 'time', 3, 'function'],
        ['create_user_id', 'sp_get_current_admin_id', 1, 'function'],
        ['update_user_id', 'sp_get_current_admin_id', 3, 'function']
    ];
}