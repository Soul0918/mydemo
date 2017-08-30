<?php

namespace Operate\Controller;

use Common\Controller\OperatebaseController;

class UserController extends OperatebaseController {

    protected $users_model, $role_model, $genre;

    public function _initialize() {
        parent::_initialize();
        $this->users_model = D('Common/Users');
        $this->role_model = D('Common/Role');
        $this->genre = I('param.genre');
        $this->assign('genre', $this->genre);
    }

    // 管理员列表
    public function index() {
        $data_out['companys'] = $this->getcompanys();
        $data_out['canEdit'] = 1;
        $this->assign($data_out);
        $this->display();
    }

    // 物业公司管理员列表
    public function index_com() {
        $this->assign("companys", $this->getcompanys());
        $this->display('index');
    }

    // 物业公司管理员列表
    public function index_xq() {
        $this->assign("companys", $this->getcompanys());
        $this->display('index');
    }

    // 管理员添加
    public function add() {

        $companys = D('Companys')->where(['company_status' => ['neq', 0]])->select();
        $this->assign('companys', $companys);
        $condition['status'] = 1;
        if ($this->genre == 0) {
            $condition['type'] = 0;
        } elseif ($this->genre == 1) {
            $condition = [
                'type' => 2,
                'company_id' => sp_get_current_company_id(),
                'community_id' => 0
            ];
        } elseif ($this->genre == 2) {
            $condition = [
                'type' => 2,
                'company_id' => sp_get_current_company_id(),
                'community_id' => sp_get_current_community_id()
            ];
        }
        $roles = $this->role_model->where($condition)->order("id DESC")->select();
        $this->assign("roles", $roles);
        $this->display();
    }

    // 管理员添加提交
    public function add_post() {
        if (IS_POST) {
            C("TOKEN_ON", false);
            if (!empty($_POST['role_id']) && is_array($_POST['role_id'])) {
                $role_ids = $_POST['role_id'];
                unset($_POST['role_id']);
                $user = $this->users_model->where(['mobile' => $_POST['mobile'], 'user_status' => ['gt', 0]])->find();
// 				$user = $this->_list();
                if ($user) {
                    foreach ($role_ids as $role_id) {
                        if (sp_get_current_admin_id() != 1 && $role_id == 1) {
                            $this->error("为了网站的安全，非网站创建者不可创建超级管理员！");
                        }
                        $role = D('Role')->where(['id' => $role_id])->find();
                        $add_data = [
                            'user_id' => $user['id'],
                            'role_id' => $role_id,
                            'company_id' => $role['company_id'],
                            'community_id' => $role['community_id']
                        ];
                        $check_role = D('RoleUser')->where($add_data)->find();
                        if (!$check_role) {
                            D('RoleUser')->add($add_data);
                        }
                    }
                    $this->success("添加成功！", U("edit", array('genre' => I('param.genre'), 'id' => $user['id'])));
                } else {
                    if ($this->users_model->create() !== false) {
                        $result = $this->users_model->add();
                        if ($result !== false) {
                            // 						$role_user_model=M("RoleUser");
                            foreach ($role_ids as $role_id) {
                                if (sp_get_current_admin_id() != 1 && $role_id == 1) {
                                    $this->error("为了网站的安全，非网站创建者不可创建超级管理员！");
                                }
                                // 							if($create = D('RoleUser')->create()){
                                $role = D('Role')->where(['id' => $role_id])->find();
                                // 								$create['user_id'] = $result;
                                // 								$create['role_id'] = $role_id;
                                $add_data = [
                                    'user_id' => $result,
                                    'role_id' => $role_id,
                                    'company_id' => $role['company_id'],
                                    'community_id' => $role['community_id']
                                ];
                                if ($role['community_id']) {
                                    $community = D('Communities')->where(['community_id' => $role['community_id']])->find();
                                    $add_data['company_id'] = $community['company_id'];
                                }
                                D('RoleUser')->add($add_data);
                                // 							}
                            }
                            $this->success("添加成功！", U("edit", array('genre' => I('param.genre'), 'id' => $result)));
                        } else {
                            $this->error("添加失败！");
                        }
                    } else {
                        $this->error($this->users_model->getError());
                    }
                }
            } else {
                $this->error("请为此用户指定角色！");
            }
        }
    }

    // 管理员编辑
    public function edit() {
        $id = I('get.id', 0, 'intval');
        $canEdit = I('get.edit', 0, 'intval');
        $user = $this->users_model->where(array("id" => $id))->find();
        $role_users = D("RoleUser")->where(array("user_id" => $id))->select();
        $role_ids = array_column($role_users, 'role_id');
        $this->assign("role_ids", $role_ids);
        $company_ids = array_column($role_users, 'company_id');
        $community_ids = array_column($role_users, 'community_id');

        $condition['status'] = 1;
        if ($this->genre == 0) {
            $condition['type'] = 0;
            $condition['company_id'] = 0;
            $condition['community_id'] = 0;
        } elseif ($this->genre == 1) {
            $condition['type'] = 2;
            $condition['company_id'] = ['in', $company_ids];
            $condition['community_id'] = 0;
        } elseif ($this->genre == 2) {
            $condition['type'] = 2;
            $condition['company_id'] = ['in', $company_ids];
            $condition['community_id'] = ['in', $community_ids];
        }
        $roles = $this->role_model->where($condition)->select();
        $this->assign("roles", $roles);
        if ($user) {
            $user['usernameval'] = $user['user_name'];
            unset($user['user_name']);
        }
        $user['canEdit'] = $canEdit;
        $this->assign($user);
        $this->display();
    }

    // 管理员编辑提交
    public function edit_post() {
        if (IS_POST) {
            if (!empty($_POST['role_id']) && is_array($_POST['role_id'])) {
                if (empty($_POST['user_pass'])) {
                    unset($_POST['user_pass']);
                }
                $role_ids = I('post.role_id/a');
                unset($_POST['role_id']);
                if ($this->users_model->create() !== false) {
                    $result = $this->users_model->save();
                    if ($result !== false) {
                        $uid = I('post.id', 0, 'intval');
                        $role_user_model = M("RoleUser");

                        foreach ($role_ids as $role_id) {
                            if (sp_get_current_admin_id() != 1 && $role_id == 1) {
                                $this->error("为了网站的安全，非网站创建者不可创建超级管理员！");
                            }
                            $role_user_model->where(['user_id' => $uid, 'role_id' => $role_id])->delete();
                            $role = $this->role_model->where(['id' => $role_id])->find();
                            $role_user_model->add([
                                'role_id' => $role_id,
                                'user_id' => $uid,
                                'company_id' => $role['company_id'],
                                'community_id' => $role['community_id']
                            ]);
                        }
                        $this->success('保存成功！');
                    } else {
                        $this->error('保存失败！');
                    }
                } else {
                    $this->error($this->users_model->getError());
                }
            } else {
                $this->error('请为此用户指定角色！');
            }
        }
    }

    // 管理员删除
    public function delete() {
        $id = I('get.id', 0, 'intval');
        if ($id == 1) {
            $this->error('最高管理员不能删除！');
        }

        if ($this->users_model->delete($id) !== false) {
            M('RoleUser')->where(array('user_id' => $id))->delete();
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    // 管理员个人信息修改
    public function userinfo() {
        $id = sp_get_current_admin_id();
        $user = $this->users_model->where(array('id' => $id))->find();
        $this->assign($user);
        $this->display();
    }

    // 管理员个人信息修改提交
    public function userinfo_post() {
        if (IS_POST) {
            $_POST['id'] = sp_get_current_admin_id();
            $create_result = $this->users_model
                    ->field('id,user_name,user_nicename,sex,mobile,user_email')
                    ->create();
            if ($create_result !== false) {
                if ($this->users_model->save() !== false) {
                    $this->success('保存成功！');
                } else {
                    $this->error('保存失败！');
                }
            } else {
                $this->error($this->users_model->getError());
            }
        }
    }

    // 停用管理员
    public function ban() {
        $id = I('get.id', 0, 'intval');
        if (!empty($id)) {
            $result = $this->users_model->where(array('id' => $id, 'user_type' => 1))->setField('user_status', '0');
            if ($result !== false) {
                $this->success('管理员停用成功！', U('user/index', array('genre' => $this->genre)));
            } else {
                $this->error('管理员停用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    // 启用管理员
    public function cancelban() {
        $id = I('get.id', 0, 'intval');
        if (!empty($id)) {
            $result = $this->users_model->where(array('id' => $id, 'user_type' => 1))->setField('user_status', '1');
            if ($result !== false) {
                $this->success('管理员启用成功！', U('user/index', array('genre' => $this->genre)));
            } else {
                $this->error('管理员启用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    //获取列表数据
    public function table_data_bak() {
        $cParam = I('param.');
        $limit_condition['offset'] = $cParam ['offset'];
        $limit_condition['limit'] = $cParam ['limit'];

        $where_condition['status'] = 1;
        $where_condition['genre'] = $this->genre;
        if ($this->genre == 0) {
            $where_condition['type'] = ('0,3');
        } elseif ($this->genre == 1) {
            $where_condition['type'] = ('2,4');
        } elseif ($this->genre == 2) {
            $where_condition['type'] = ('2,5');
        } elseif ($this->genre == 3) {  //运维类型
            $where_condition['type'] = ('2,6');
        }

        if (array_key_exists('sort', $cParam) && array_key_exists('order', $cParam)) {
            $order_condition [$cParam ['sort']] = $cParam ['order'];
        } else {
            $order_condition ['id'] = 'ASC';
        }

        $search = trim(I('param.search'));
        if ($search) {
            $where_condition['user_login'] = array('like', '%' . $search . '%');
        }
        $return_data = $this->users_model->user_list($where_condition, $limit_condition, $order_condition);
        $this->ajaxReturn(array(
            'status' => 1,
            'rows' => $return_data['result'],
            'total' => $return_data['total_count']
        ));
    }

    //获取列表数据
    public function table_data() {
        $offset = I('offset', 0, 'intval');
        $limit = I('limit', 10, 'intval');
        $query = function () {
            $sort = I('sort', 'id');
            $order = I('order', 'asc');
            $condition = [];
            $map_condition = [];
            if ($this->genre == 1) {
                $condition['type'] = ['in', [1, 2]];
                $condition['company_id'] = ['gt', 0];
                $condition['community_id'] = 0;
                $map_condition['type'] = 4;
                $map_condition['_logic'] = 'or';
                $map_condition[1] = $condition;
            } else if ($this->genre == 2) {
                $condition['type'] = ['in', [2]];
                $condition['community_id'] = ['gt', 0];
                $map_condition['type'] = 5;
                $map_condition['_logic'] = 'or';
                $map_condition[1] = $condition;
            } else {
                $map_condition['type'] = 0;
            }
            $query = D('RoleUser')->alias('a')
                    ->join('(' . D('Role')->where($map_condition)->field('id')->select(false) . ') b on a.role_id = b.id')
                    ->join('__USERS__ c on a.user_id = c.id');
            if ($this->genre == 1) {
                $query = $query->join('__COMPANYS__ p on a.company_id = p.id and p.company_status>0')->group('c.id,p.id')
                        ->field('c.*,p.company_name,p.user_id admin_user');
            } elseif ($this->genre == 2) {
                $query = $query->join('__COMMUNITIES__ x on a.community_id = x.community_id and x.state>0')->group('c.id,x.community_id')
                        ->field('c.*,x.name community_name,x.community_id,x.user_id admin_user');
            }
            if ($sort == 'community_id') {
                return $query->order('a.' . $sort . ' ' . $order);
            } else {
                return $query->order('c.' . $sort . ' ' . $order);
            }
        };
        $search = I('get.search');
        $condition = [];
        if (!empty($search)) {
            $keyword = $search;
            $keyword_complex = array();
            $keyword_complex['c.user_login'] = array('like', "%$keyword%");
            $keyword_complex['c.user_nicename'] = array('like', "%$keyword%");
            $keyword_complex['c.user_email'] = array('like', "%$keyword%");
            $keyword_complex['c.user_name'] = array('like', "%$keyword%");
            $keyword_complex['c.mobile'] = array('like', "%$keyword%");
            $keyword_complex['_logic'] = 'or';
            $condition['_complex'] = $keyword_complex;
        }
        $company_id = I('get.company_id');
        $community_id = I('get.community_id');
        empty($company_id) ? '' : $condition['a.company_id'] = $company_id;
        empty($community_id) ? '' : $condition['a.community_id'] = $community_id;
        $datas = $query()->where($condition)->limit($offset, $limit)->group('c.id')->select();
        $count = $query()->where($condition)->field('COUNT(DISTINCT c.id) AS tp_count')->find();
        foreach ($datas as $key => $data) {
            $user_activation_key = unserialize($data['user_activation_key']);
            if ($user_activation_key) {
                $expired = intval(($user_activation_key['expired'] - time()) / 60);
                //                var_dump($expired);
                $user_activation_key['expired'] = $expired;
            }
            $datas[$key]['user_activation_key'] = json_encode($user_activation_key);
            $role_type = 0;
            if ($data['id'] == $data['admin_user']) {
                $role_type = 1;
            }
            $datas[$key]['role_type'] = $role_type;
            if ($this->genre == 2) {
                if ($data['community_id']) {
                    $community = D('Communities')->relation(true)->where(['community_id' => $data['community_id']])->find();
                    $datas[$key]['company_name'] = $community['company_name'];
                }
            }
        }
        //        var_dump($datas);exit();
        $this->ajaxReturn([
            'total' => $count['tp_count'],
            'rows' => $datas,
            'status' => 1
        ]);
    }

    /**
     * 获取角色
     */
    public function getRoles() {

        $condition['status'] = 1;
        if ($_GET['company_id']) {
            $condition['company_id'] = $_GET['company_id'];
            $condition['community_id'] = 0;
        }
        if ($_GET['community_id']) {
            $condition['community_id'] = $_GET['community_id'];
        }
        $genre = I('param.genre');
        if ($genre == 0) {
            $condition = [
                'status' => 1,
                'type' => 0,
                'community_id' => 0,
                'company_id' => 0
            ];
        } elseif ($genre == 1) {
            $condition['type'] = 2;
        } elseif ($genre == 2) {
            $condition['type'] = 2;
        }
        $roles = $this->role_model->where($condition)->order('id DESC')->select();
        echo json_encode(array(
            'rows' => $roles,
            'total' => count($roles, 0)
        ));
    }

    /**
     * 获取公司小区
     */
    public function getCommunities() {

        $company_id = I('param.company_id');
        if ($company_id > 0) {
            $community_data = D('Common/Communities')->where(array('company_id' => $company_id, 'state' => 1))->select();
            echo json_encode(array(
                'rows' => $community_data,
                'total' => count($community_data, 0)
            ));
        }
    }

    // 获取公司的数据
    public function getcompanys() {
        $companys = D("Common/Companys")->field(true)->select();
        return $companys;
    }

}
