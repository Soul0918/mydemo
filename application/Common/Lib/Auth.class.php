<?php
namespace Common\Lib;

class Auth
{

    public static function check($uid, $name, $relation = 'or')
    {
//        $uid = sp_get_current_admin_id();
        $login_type = session('LOGIN_TYPE');
        $company_id = sp_get_current_company_id();
        $community_id = sp_get_current_community_id();

        if ($uid == 1) return true;

        if (is_string($name)) {
            $name = strtolower($name);
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = array($name);
            }
        }

        $list = [];
        $communities_model = D('Communities');
        $companys_model = D('Companys');
        $role = self::getRoles($uid);

        if ($login_type == '1') {
            $company = $companys_model->getById($company_id);
            //物业公司超级管理员 in_array('5', $role, true)
            if ($company['user_id'] == $uid) {
                $role_company_model = M('RoleCompany');
                $groups_company = $role_company_model
                    ->alias('a')
                    ->join('__COMPANYS__ b on a.company_id = b.id')
                    ->where(array('b.id'=>$company_id, 'b.company_status'=>1))
                    ->getField('company_role_id', true);

                if (empty($groups_company))
                    return false;

//                $company_auth_access_model = M('CompanyAuthAccess');
//                $rules = $company_auth_access_model
//                    ->alias('a')
//                    ->join('__AUTH_RULE__ b on a.rule_name = b.name')
//                    ->where(array('a.company_role_id'=>array('in',$groups_company), array('b.name'=>array('in',$name))))
//                    ->select();

                $auth_access = unserialize(html_entity_decode($company['auth_access']));
                foreach ($auth_access as $key => $value) {
                    $rule = strtolower($value['app']).'/'.strtolower($value['model']).'/'.strtolower($value['action']);
                    if (in_array($rule, $name)) {
                        $list[] = $rule;
                    }
                }
            } else {
                $list = self::auth_list($uid, $name);
            }
        } else if ($login_type == '2') {
            $community = $communities_model->relation('modules')->where(['community_id'=>$community_id])->find();
            if (empty($community)) return false;
            if ($community['user_id'] == $uid) {
                //计算小区权限 in_array('28', $role, true)
                $company = $companys_model->getById($community['company_id']);
                if (empty($company)) return false;
                $modules = [];
                foreach ($community['modules'] as $module) {
                    $modules[] = $module['module_id'];
                }
                $auth_access = unserialize(html_entity_decode($company['auth_access']));
                foreach ($auth_access as $key => $value) {
                    $rule = strtolower($value['app']).'/'.strtolower($value['model']).'/'.strtolower($value['action']);
                    if (in_array((string)$value['module_id'], $modules, true) && in_array($rule, $name)) {
                        $list[] = $rule;
                    }
                }
            } else {
                $list = self::auth_list($uid, $name);
            }
        } else {
            $list = self::auth_list($uid, $name);
        }

        if ($relation == 'or' and !empty($list)) {
            return true;
        }

        $diff = array_diff($name, $list);

        if ($relation == 'and' and empty($diff)) {
            return true;
        }

        return false;
    }

    /**
     * 获取用户角色ID
     * @param $uid
     * @return mixed
     */
    public static function getRoles($uid)
    {
        $roles_model = D('Role');
        $role = $roles_model->alias('a')
            ->join('__ROLE_USER__ b on a.id = b.role_id')
            ->where(['b.user_id'=>$uid])->getField('role_id', true);
        return $role;
    }

    public static function getUserInfo($uid) {
        static $userinfo=array();
        if(!isset($userinfo[$uid])){
            $userinfo[$uid]=M("Users")->where(array('id'=>$uid))->find();
        }
        return $userinfo[$uid];
    }

    public static function auth_list($uid, $name)
    {
        $list = [];
        $role_user_model=M("RoleUser");
        $role_user_join = '__ROLE__ as b on a.role_id =b.id';
        $login_type = session('LOGIN_TYPE');
        $company_id = sp_get_current_company_id();
        $community_id = sp_get_current_community_id();
        $groups=$role_user_model->alias("a")->join($role_user_join)->where(array("user_id"=>$uid,"status"=>1));
        if ($login_type == 1) {
            $groups = $groups->where(['a.company_id' => $company_id]);
        } elseif ($login_type == 2) {
            $groups = $groups->where(['a.community_id' => $community_id]);
        }
        $groups = $groups->getField("role_id",true);
//        var_dump($groups);exit();
        if(in_array(1, $groups)){
            return true;
        }
        if(empty($groups)){
            return false;
        }
        $auth_access_model=M("AuthAccess");
        $join = '__AUTH_RULE__ as b on a.rule_name =b.name';
        $rules=$auth_access_model->alias("a")->join($join)->where(array("a.role_id"=>array("in",$groups),"b.name"=>array("in",$name)))->select();
        foreach ($rules as $rule){
            if (!empty($rule['condition'])) { //根据condition进行验证
                $user = self::getUserInfo($uid);//获取用户信息,一维数组
                $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                //dump($command);//debug
                @(eval('$condition=(' . $command . ');'));
                if ($condition) {
                    $list[] = strtolower($rule['name']);
                }
            }else{
                $list[] = strtolower($rule['name']);
            }
        }
        return $list;
    }
}