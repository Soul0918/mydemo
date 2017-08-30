<?php

namespace Operate\Controller;

use Common\Controller\OperatebaseController;

class RbacController extends OperatebaseController {

    protected $role_model, $auth_access_model, $genre;

    public function _initialize() {
        parent::_initialize();
        $this->role_model = D("Common/Role");
        $this->genre = I("param.genre");
        $this->assign("genre", $this->genre);
    }

    // 角色管理列表
    public function index() {
        $this->display();
    }

    // 物业公司管理角色管理列表
    public function index_com() {
        $this->assign("companys", $this->getcompanys());
        $this->display("index");
    }

    // 小区管理角色管理列表
    public function index_xq() {
        $this->assign("companys", $this->getcompanys());
        $this->display("index");
    }

    public function table_data() {
        $cParam = I("param.");
        $offset = $cParam ['offset'];
        $limit = $cParam ['limit'];
        $query = function () {
            $search = I('get.search');
            $company_id = I('get.company_id');
            $community_id = I('get.community_id');
            $query = $this->role_model->alias('a');
            if ($this->genre == 0) {
                $condition ['a.type'] = array(
                    'in',
                    '0,3'
                );
                $query->where($condition);
            } elseif ($this->genre == 1) {
                $condition = '(a.type = 1) or (a.type = 2 and a.community_id = 0)';
                $query = $query->field('a.*,b.company_name')->join('LEFT join __COMPANYS__ b on a.company_id = b.id')->where($condition);
            } elseif ($this->genre == 2) {
                $condition = '(a.type = 6) or (a.type = 2 and a.community_id > 0)';
                $query = $query->field('a.*,b.name as community_name,c.company_name')->join('LEFT join __COMPANYS__ c on a.company_id = c.id')
                        ->join('LEFT join __COMMUNITIES__ b on a.community_id = b.community_id')->where($condition);
            } else {
                $condition ['a.type'] = array(
                    'in',
                    '-1'
                );
                $query->where($condition);
            }
            if(!empty($company_id)){
                $condition = [];
                $condition['a.company_id']=$company_id;
                $query->where($condition);
            }
            if(!empty($community_id)){
                $condition = [];
                $condition['a.community_id']=$community_id;
                $query->where($condition);
            }
            if (!empty($search)) {
                $keyword = $search;
                $keyword_complex = array();
                $keyword_complex['a.name'] = array('like', "%$keyword%");
                $keyword_complex['a.remark'] = array('like', "%$keyword%");
                $keyword_complex['_logic'] = 'or';
                $condition=[];
                $condition['_logic'] = 'and';
                $condition['_complex'] = $keyword_complex;
                $query->where($condition);
            }
            return $query;
        };


        if (array_key_exists("a.sort", $cParam) && array_key_exists("a.order", $cParam)) {
            $order_condition [$cParam ['sort']] = $cParam ['order'];
        } else {
            $order_condition ['a.id'] = "DESC";
            $order_condition ['a.listorder'] = "ASC";
        }
        $data = $query()->limit($offset . "," . $limit)->order($order_condition)->select();
        $counts = $query()->count();
        echo json_encode(array(
            'rows' => $data,
            'total' => $counts
        ));
    }

    // 添加角色
    public function roleadd() {
        $this->assign('companys', $this->getcompanys());
        $this->display();
    }

    // 添加角色提交
    public function roleadd_post() {
        if (IS_POST) {
            if (I('post.type') != 2) {
                $_POST['company_id'] = 0;
            }
            if ($this->role_model->create() !== false) {
                $res = $this->role_model->add();
                if ($res !== false) {
                    $this->success("添加角色成功", U("rbac/roleedit", array('genre' => $_POST['genre'], 'id' => $res)));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->role_model->getError());
            }
        }
    }

    // 删除角色
    public function roledelete() {
        $id = I("get.id", 0, 'intval');
        if ($id == 1) {
            $this->error("超级管理员角色不能被删除！");
        }
        $role_user_model = M("RoleUser");
        $count = $role_user_model->where(array(
                    'role_id' => $id
                ))->count();
        if ($count > 0) {
            $this->error("该角色已经有用户！");
        } else {
            $status = $this->role_model->delete($id);
            if ($status !== false) {
                $this->success("删除成功！", U('Rbac/index', array('genre' => I('param.genre'))));
            } else {
                $this->error("删除失败！");
            }
        }
    }

    // 编辑角色
    public function roleedit() {
        $id = I("get.id", 0, 'intval');
        if ($id == 1) {
            $this->error("超级管理员角色不能被修改！");
        }
        $data = $this->role_model->where(array("id" => $id))->find();
        if ($data && $data['type'] > 0) {
            $db_companys = "hc_companys";
            $data = $this->role_model->join("as r INNER JOIN " . $db_companys . " as c on r.company_id = c.id")->join('LEFT join __COMMUNITIES__ b on r.community_id = b.community_id')->where(array(
                        "r.id" => $id
                    ))->field("r.*, c.id as company_id, c.company_name,b.name as community_name")->find();
        }
        if (!$data) {
            $this->error("该角色不存在！");
        }
        $this->assign("data", $data);
        $this->assign("companys", $this->getcompanys());
        $this->display();
    }

    // 编辑角色提交
    public function roleedit_post() {
        $id = I("request.id", 0, 'intval');
        if ($id == 1) {
            $this->error("超级管理员角色不能被修改！");
        }
        if (IS_POST) {
//			if(I('post.type') != 2 && I('post.type') != 5){
//				$_POST['company_id'] = 0;
//			}
//			if(I('post.type') != 5){
//				$_POST['community_id'] = 0;
//			}
            if ($this->role_model->create() !== false) {
                if ($this->role_model->save() !== false) {
                    $this->success("修改成功！", U('Rbac/roleedit', array('genre' => $_POST['genre'], 'id' => $id)));
                } else {
                    $this->error("修改失败！");
                }
            } else {
                $this->error($this->role_model->getError());
            }
        }
    }

    // 角色授权
    public function authorize() {
        $this->auth_access_model = D("Common/AuthAccess");
        // 角色ID
        $roleid = I("get.id", 0, 'intval');
        $genre = I("get.genre", 0, 'intval');
        if (empty($roleid)) {
            $this->error("参数错误！");
        }
        import("Tree");
        $menu = new \Tree ();
        $menu->icon = array(
            '│ ',
            '├─ ',
            '└─ '
        );
        $menu->nbsp = '&nbsp;&nbsp;&nbsp;';
        $where_result = empty($genre) ? ['app' => 'Operate'] : ['app' => 'Managment'];
        $result = D("Common/Menu")->where($where_result)->select();
//        var_dump($result);exit;
        //
        $db_companys = "hc_companys";
        $role = D("Common/Role")->join("as r INNER JOIN " . $db_companys . " as c on r.company_id = c.id")->where(array(
                    "r.id" => $roleid
                ))->select();
        if (count($role, 0) > 0) {
            $result = unserialize(htmlspecialchars_decode($role [0] ["auth_access"]));
        }

        //
        $newmenus = array();
        $priv_data = $this->auth_access_model->where(array(
                    "role_id" => $roleid
                ))->getField("rule_name", true); // 获取权限表数据
        foreach ($result as $m) {
            $newmenus [$m ['id']] = $m;
        }

        foreach ($result as $n => $t) {
            $result [$n] ['checked'] = ($this->_is_checked($t, $roleid, $priv_data)) ? ' checked' : '';
            $result [$n] ['level'] = $this->_get_level($t ['id'], $newmenus);
            $result [$n] ['style'] = empty($t ['parentid']) ? '' : 'display:none;';
            $result [$n] ['parentid_node'] = ($t ['parentid']) ? ' class="child-of-node-' . $t ['parentid'] . '"' : '';
        }
        $str = "<tr id='node-\$id' \$parentid_node  style='\$style'>
                   <td style='padding-left:30px;'>\$spacer<input type='checkbox' name='menuid[]' value='\$id' level='\$level' \$checked onclick='javascript:checknode(this);'> \$name</td>
    			</tr>";
        $menu->init($result);
        $categorys = $menu->get_tree(0, $str);
        
        //已选的用户
        $role_user_data = M("RoleUser")->alias("ru")->join("LEFT JOIN __USERS__ as u on ru.user_id=u.id")->where(['role_id' => $roleid])->field("ru.*,u.*")->select();
        //没有分配的用户
        $user_ids = [1];
        foreach ($role_user_data as $key => $val) {
            $user_ids[] = $val['user_id'];
        }
        $this->assign("role_user", $role_user_data);
        $this->assign("categorys", $categorys);
        $this->assign("roleid", $roleid);
        $this->display();
    }

    public function authorize_module() {
        $roleid = I("get.id", 0, 'intval');
        $genre = I('get.genre', 0, 'intval');
        if (empty($roleid)) {
            $this->error("参数错误！");
        }


//        if ($genre == 2) {
//            $role = D ( "Common/Role" )->getById($roleid);
//            $community = D('Communities')->relation(true)->where(['community_id'=>$role['community_id']])->find();
//            $modules = $community['modules'];
//        } else {
//
//        }
        $role = D("Common/Role")->join("as r INNER JOIN __COMPANYS__ as c on r.company_id = c.id")->where(array(
                    "r.id" => $roleid
                ))->find();
        $result = $role ? unserialize(htmlspecialchars_decode($role["auth_access"])) : $this->initMenu();
        $module_ids = [];
        foreach ($result as $value) {
            if ($value['module_id'] != 0) {
                $module_ids[$value['module_id']] = $value['module_id'];
            }
        }
        $modules_model = M('Modules');
        $modules = [];
        if ($module_ids) {
            $modules = $modules_model->where(['module_id' => ['in', $module_ids]])->select();
        }

        $auth_access = D('RoleModule')->where(['role_id' => $roleid])->getField('module_id', true);

        $this->assign('auth_access', $auth_access);
        $this->assign('roleid', $roleid);
        $this->assign('modules', $modules);
        $this->display();
    }

    public function authorize_module_post() {
        if (IS_POST) {
            $roleid = I("post.roleid", 0, 'intval');
            $genre = I('post.genre', 0, 'intval');
            if (!$roleid) {
                $this->error("需要授权的角色不存在！");
            }
            $moduleid = $_POST ['moduleid'];
            $role_module_model = M('RoleModule');
            if (is_array($moduleid) && count($moduleid) > 0) {
                // 生成后台菜单id
                $menu_model = M("Menu");
//                if ($genre == 2) {
//                    $role = $this->role_model->getById($roleid);
//                    $communities_model = D('Communities');
//                    $companys_model = D('Companys');
//                    $community = $communities_model->relation('modules')->where(['community_id'=>$role['community_id']])->find();
//                    $company = $companys_model->getById($community['company_id']);
//                    $auth_access = unserialize(html_entity_decode($company['auth_access']));
//                    //计算小区菜单
//                    foreach ($auth_access as $value) {
//                        if (in_array((string)$value['module_id'], $moduleid, true)) {
//                            $menuid[] = $value['id'];
//                        }
//                    }
//                } else {
//                }
                $menuid = $menu_model->where(['module_id' => ['in', $moduleid]])->getField('id', true);
                $role_module_model->where(['role_id' => $roleid])->delete();
                foreach ($moduleid as $key => $item) {
                    unset($moduleid[$key]);
                    $moduleid[$key] = ['role_id' => $roleid, 'module_id' => intval($item)];
                }

                $role_module_model->addAll($moduleid);

                // 之前的菜单授权
                $_POST['menuid'] = $menuid;
            } else {
                $role_module_model->where(['role_id' => $roleid])->delete();
            }
            $this->authorize_post();
        }
    }

    // 角色授权提交
    public function authorize_post() {
        $this->auth_access_model = D("Common/AuthAccess");
        if (IS_POST) {
            $roleid = I("post.roleid", 0, 'intval');
            if (!$roleid) {
                $this->error("需要授权的角色不存在！");
            }
            if (is_array($_POST ['menuid']) && count($_POST ['menuid']) > 0) {

                $menu_model = M("Menu");
                $auth_rule_model = M("AuthRule");
                $this->auth_access_model->where(array(
                    "role_id" => $roleid,
                    'type' => 'admin_url'
                ))->delete();
                foreach ($_POST ['menuid'] as $menuid) {
                    $menu = $menu_model->where(array(
                                "id" => $menuid
                            ))->field("app,model,action")->find();
                    if ($menu) {
                        $app = $menu ['app'];
                        $model = $menu ['model'];
                        $action = $menu ['action'];
                        $name = strtolower("$app/$model/$action");
                        $this->auth_access_model->add(array(
                            "role_id" => $roleid,
                            "rule_name" => $name,
                            'type' => 'admin_url'
                        ));
                    }
                }
                sp_clear_cache();
                $this->success("授权成功！", U("Rbac/roleedit", array('genre' => $_POST['genre'], 'id' => $roleid)));
            } else {
                // 当没有数据时，清除当前角色授权
                $this->auth_access_model->where(array(
                    "role_id" => $roleid
                ))->delete();
                $this->error("没有接收到数据，执行清除授权成功！");
            }
        }
    }

    /**
     * 角色分配给用户
     */
    public function roledistribute() {
        $id = I("get.id", 0, 'intval');
        if ($id == 1) {
            $this->error("超级管理员角色不能被分配！");
        }
        $data = $this->role_model->where(array("id" => $id))->find();
        if ($data && $data['type'] == 2) {
            $db_companys = "hc_companys";
            $data = $this->role_model->join("as r INNER JOIN " . $db_companys . " as c on r.company_id = c.id")->where(array(
                        "r.id" => $id
                    ))->field("r.*, c.id as company_id, c.company_name")->find();
        }
        if (!$data) {
            $this->error("该角色不存在！");
        }
        //已选的用户
        $role_user_data = M("RoleUser")->alias("ru")->join("LEFT JOIN __USERS__ as u on ru.user_id=u.id")->where([
                    'role_id' => $data ['id']
                ])->field("ru.*,u.*")->select();
        //没有分配的用户
        $user_ids = [1];
        foreach ($role_user_data as $key => $val) {
            $user_ids[] = $val['user_id'];
        }
        //可选择的用户（超级管理员以及公司超级管理员不可显示）
        $roles_data = $this->role_model->where(['type' => ['not in', '3,4']])->order(array(
                    "listorder" => "ASC",
                    "id" => "DESC"
                ))->select();
        $this->assign("roles", $roles_data);

        $user_data = D("Users")->where(['id' => ['not in', $user_ids]])->select();
        $this->assign("user_data", $user_data);
        $this->assign("data", $data);
        $this->assign("companys", $this->getcompanys());
        $this->assign("role_user", $role_user_data);

        $this->display();
    }

    public function roledistribute_post() {
        if (IS_POST) {
            $id = $_POST['id'];
            $user_ids = $_POST['user_id'];
            if ($id && $user_ids) {
                $count = 0;
                foreach ($user_ids as $key => $val) {
                    $data = ['role_id' => $id, 'user_id' => $val, 'company_id' => I('post.company_id', 0, 'intval')];
                    if (M("RoleUser")->data($data)->add()) {
                        $count ++;
                    }
                }
                if ($count > 0) {
                    $this->success("添加成功！", U("roleedit", array("id" => $id, "genre" => $this->genre)));
                }
            }
        } else {
            $this->error('保存失败');
        }
    }

    /**
     * 角色分配中删除角色
     * @param int rid 角色ID
     * @param int uid 用户ID
     */
    public function deleteRole() {
        $rid = $_GET['rid'];
        $uid = $_GET['uid'];
        if ($rid && $uid) {
            M("RoleUser")->where(['role_id' => $rid, 'user_id' => $uid])->delete();
            $this->success("删除成功！", U("roledistribute", array("id" => $rid, "genre" => $this->genre)));
        }
    }

    /**
     * 检查指定菜单是否有权限
     *
     * @param array $menu
     *        	menu表中数组
     * @param int $roleid
     *        	需要检查的角色ID
     */
    private function _is_checked($menu, $roleid, $priv_data) {
        $app = $menu ['app'];
        $model = $menu ['model'];
        $action = $menu ['action'];
        $name = strtolower("$app/$model/$action");
        if ($priv_data) {
            if (in_array($name, $priv_data)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 获取菜单深度
     * @param $id
     * @param array $array
     * @param int $i
     * @return int
     */
    protected function _get_level($id, $array = array(), $i = 0) {
        if ($array [$id] ['parentid'] == 0 || empty($array [$array [$id] ['parentid']]) || $array [$id] ['parentid'] == $id) {
            return $i;
        } else {
            $i ++;
            return $this->_get_level($array [$id] ['parentid'], $array, $i);
        }
    }

    // 角色成员管理
    public function member() {
        // TODO 添加角色成员管理
    }

    // 获取公司的数据
    public function getcompanys() {
        $companys = D("Common/Companys")->field(true)->select();
        return $companys;
    }

    // 获取公司小区
    public function getCommunities() {

        $company_id = I("param.company_id");
        if ($company_id > 0) {
            $community_data = D("Common/Communities")->where(array("company_id" => $company_id, "state" => 1))->select();
            echo json_encode(array(
                'rows' => $community_data,
                'total' => count($community_data, 0)
            ));
        }
    }

    public function userlist()
    {
        $this->display();
    }
    
    public function getusers()
    {
        $id = I("get.id", 0, 'intval');
        empty($id) && $this->error('参数有误');
        $offset = I('get.offset', 0);
        $limit  = I('get.limit', 5);
        $search  = I('get.search');
        //已选的用户
        $role_user_data = M("RoleUser")->alias("ru")->join("LEFT JOIN __USERS__ as u on ru.user_id=u.id")->where([
                    'role_id' => $id,
                ])->field("ru.*,u.*")->select();
        //没有分配的用户
        $user_ids = [1];
        foreach ($role_user_data as $key => $val) {
            $user_ids[] = $val['user_id'];
        }
        $where = ['id' => ['not in', $user_ids]];
        if(!empty($search)){
            $w_s['user_name'] = ['like',"%$search%"];
            $w_s['user_nicename'] = ['like',"%$search%"];
            $w_s['mobile'] = ['like',"%$search%"];
            $w_s['_logic'] = 'or';
            $where['_complex'] = $w_s;
        }
        $users = D("Users")->where($where)->limit($offset, $limit)->select();
        $count = D("Users")->where($where)->count();

        $this->ajaxReturn([
            'total'  => $count,
            'rows'   => $users,
            'status' => 1
        ]);
    }
}
