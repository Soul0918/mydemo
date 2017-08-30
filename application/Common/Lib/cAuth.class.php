<?php
namespace Common\Lib;

class cAuth
{
//    public function check($uid,$cid,$name,$relation='or')
//    {
//
//        if(empty($cid) || empty($uid)){
//            return false;
//        }
//
//        if (is_string($name)) {
//            $name = strtolower($name);
//            if (strpos($name, ',') !== false) {
//                $name = explode(',', $name);
//            } else {
//                $name = array($name);
//            }
//        }
//
//
//        $list = [];
//        $roles_model = D('Role');
//        $role = $roles_model->alias('a')
//            ->join('__ROLE_USER__ b on a.id = b.role_id')
//            ->where(['b.user_id'=>$uid])->getField('role_id', true);
//
//        if (in_array('5', $role, true)) {
//            $role_company_model = M('RoleCompany');
//            $groups_company = $role_company_model
//                ->alias('a')
//                ->join('__COMPANYS__ b on a.company_id = b.id')
//                ->where(array('b.id'=>$cid, 'b.company_status'=>1))
//                ->getField('company_role_id', true);
//
//            if (empty($groups_company))
//                return false;
//
//            $company_auth_access_model = M('CompanyAuthAccess');
//            $rules = $company_auth_access_model
//                ->alias('a')
//                ->join('__AUTH_RULE__ b on a.rule_name = b.name')
//                ->where(array('a.company_role_id'=>array('in',$groups_company), array('b.name'=>array('in',$name))))
//                ->select();
//            if (empty($rules))
//                return false;
//
//            foreach ($rules as $rule){
//                $list[] = strtolower($rule['name']);
//            }
//        } else {
//            $role_user_model=M("RoleUser");
//            $role_user_join = '__ROLE__ as b on a.role_id =b.id';
//            $groups=$role_user_model->alias("a")->join($role_user_join)->where(array("user_id"=>$uid,"status"=>1))->getField("role_id",true);
//            if(in_array(1, $groups)){
//                return true;
//            }
//            if(empty($groups)){
//                return false;
//            }
//            $auth_access_model=M("AuthAccess");
//            $join = '__AUTH_RULE__ as b on a.rule_name =b.name';
//            $rules=$auth_access_model->alias("a")->join($join)->where(array("a.role_id"=>array("in",$groups),"b.name"=>array("in",$name)))->select();
//            foreach ($rules as $rule){
//                if (!empty($rule['condition'])) { //根据condition进行验证
//                    $user = $this->getUserInfo($uid);//获取用户信息,一维数组
//                    $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
//                    //dump($command);//debug
//                    @(eval('$condition=(' . $command . ');'));
//                    if ($condition) {
//                        $list[] = strtolower($rule['name']);
//                    }
//                }else{
//                    $list[] = strtolower($rule['name']);
//                }
//            }
//        }
//
//
//
//        if ($relation == 'or' and !empty($list)) {
//            return true;
//        }
//        $diff = array_diff($name, $list);
//        if ($relation == 'and' and empty($diff)) {
//            return true;
//        }
//
//        return false;
//    }

    /**
     * 验证是否拥有权限
     * @param $uid 用户id
     * @param $aid 公司id
     * @param $cid 小区id
     * @param $name 验证规则名称
     * @param string $relation
     * @return bool
     */
    public static function check_verify($uid, $aid, $cid, $name, $relation='or')
    {
        if(empty($uid) || empty($aid)){
            return false;
        }
        if (is_string($name)) {
            $name = strtolower($name);
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = array($name);
            }
        }
        $list = array(); //保存验证通过的规则名
        $role_user_model=M("RoleUser");
        $role_user_join = '__ROLE__ as b on a.role_id =b.id';
        $map[] = '1=1';
        $map[] = ' AND a.user_id = '.$uid;
        $map[] = ' AND b.status = 1';
        $map[] = ' AND ( (a.company_id = '.$aid.' AND a.community_id = 0) OR (a.company_id = 0 AND a.community_id = '.$cid.') )';
        $groups = $role_user_model->alias("a")->join($role_user_join)->where(join('', $map))->getField("role_id",true);
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
            $list[] = strtolower($rule['name']);
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

//    public function check_community($uid,$cid,$name,$relation='or')
//    {
//        if(empty($cid) || empty($uid)){
//            return false;
//        }
//
//        if (is_string($name)) {
//            $name = strtolower($name);
//            if (strpos($name, ',') !== false) {
//                $name = explode(',', $name);
//            } else {
//                $name = array($name);
//            }
//        }
//
//
//        $list = [];
//        $communities_model = D('Communities');
//        $companys_model = D('Companys');
//        $roles_model = D('Role');
//        $role = $roles_model->alias('a')
//            ->join('__ROLE_USER__ b on a.id = b.role_id')
//            ->where(['b.user_id'=>$uid])->getField('role_id', true);
//
//        if (in_array('28', $role, true)) {
//            //计算小区权限
//            $community = $communities_model->relation('modules')->where(['community_id'=>$cid])->find();
//            if (empty($community)) return false;
//            $company = $companys_model->getById($community['company_id']);
//            if (empty($company)) return false;
//            $modules = [];
//            foreach ($community['modules'] as $module) {
//                $modules[] = $module['module_id'];
//            }
//            $auth_access = unserialize(html_entity_decode($company['auth_access']));
//            foreach ($auth_access as $key => $value) {
//                $rule = strtolower($value['app']).'/'.strtolower($value['model']).'/'.strtolower($value['action']);
//                if (in_array((string)$value['module_id'], $modules, true) && in_array($rule, $name)) {
//                    $list[] = $rule;
//                }
//            }
//        } else {
//            //计算角色权限
//            $role_user_model=M("RoleUser");
//            $role_user_join = '__ROLE__ as b on a.role_id =b.id';
//            $groups=$role_user_model->alias("a")->join($role_user_join)->where(array("user_id"=>$uid,"status"=>1))->getField("role_id",true);
//            if(in_array(1, $groups)){
//                return true;
//            }
//            if(empty($groups)){
//                return false;
//            }
//            $auth_access_model=M("AuthAccess");
//            $join = '__AUTH_RULE__ as b on a.rule_name =b.name';
//            $rules=$auth_access_model->alias("a")->join($join)->where(array("a.role_id"=>array("in",$groups),"b.name"=>array("in",$name)))->select();
//            foreach ($rules as $rule){
//                if (!empty($rule['condition'])) { //根据condition进行验证
//                    $user = $this->getUserInfo($uid);//获取用户信息,一维数组
//                    $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
//                    //dump($command);//debug
//                    @(eval('$condition=(' . $command . ');'));
//                    if ($condition) {
//                        $list[] = strtolower($rule['name']);
//                    }
//                }else{
//                    $list[] = strtolower($rule['name']);
//                }
//            }
//        }
//
//        if ($relation == 'or' and !empty($list)) {
//            return true;
//        }
//
//        $diff = array_diff($name, $list);
//
//        if ($relation == 'and' and empty($diff)) {
//            return true;
//        }
//
//        return false;
//
//    }
}