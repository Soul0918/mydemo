<?php

namespace Operate\Controller;

use Common\Controller\OperatebaseController;
use Common\Lib\Hcpms;
use Common\Model\LifestylesModel;

class LifestyleController extends OperatebaseController {

    /**
     * @var LifestylesModel
     */
    protected $lifestyles_model;
    protected $no_need_check_rules = [
        'OperateLifestyleDelete'
    ];
    function _initialize() {
        parent::_initialize();
        $this->lifestyles_model = D('Lifestyles');
    }

    public function index() {
        $this->display();
    }

    public function table_data() {
        $offset = I('get.offset', 0);
        $limit = I('get.limit', 10);
        $search = I("get.search");
        $query = function () {
            $sort = I('get.sort', 'id');
            $order = I('get.order', 'asc');
            $query = $this->lifestyles_model->alias('a')->relation(true)->where(['state' => 1])->order('a.' . $sort . ' ' . $order);
            return $query;
        };
         $condition = [];
        if (!empty($search)) {
            $where_condition = [];
            $where_condition['a.lifestyle_id'] = array('like', "%$search%");
            $where_condition['a.name'] = array('like', "%$search%");
            $where_condition['_logic'] = 'OR';
            $condition['_complex'] = $where_condition;
        }

        $datas = $query()->limit($offset, $limit)->where($condition)->select();
        $count = $query()->where($condition)->count();

        $this->ajaxReturn([
            'total' => $count,
            'rows' => $datas,
            'status' => 1
        ]);
    }

    public function add() {
        $time = time();
        $this->assign('time', $time);
        $this->display();
    }

    public function add_post() {
        if (IS_POST) {
            $data = I('post.lifestyle');
            $fileid = I('post.fileid', 0, 'intval');
            /*if (empty($data['name'])) {
                $this->error('不能为空!');
            }*/
            $data = array_merge($data, [
                'zhanghu_name' => '杨成',
                'state' => '1',
                'create_time' => time(),
                'create_user_id' => sp_get_current_admin_id(),
                'update_user_id' => sp_get_current_admin_id()
            ]);

            if ($this->lifestyles_model->add($data)) {
                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }
        }
    }

    public function edit() {
        $id = I('get.id', 0, 'intval');
        $lifestyle = $this->lifestyles_model->find($id);
        if (empty($lifestyle)) {
            $this->error('该数据已删除或不存在!');
        }
        $this->assign('lifestyle', $lifestyle);
        $this->display();
    }

    public function detail() {
        $id = I('get.id', 0, 'intval');
        $lifestyle = $this->lifestyles_model->find($id);
        $map = [
            'hc_users.id' => $lifestyle[create_user_id],
        ];
        $r = M('Users')->where($map)->select();
        $res = $r[0][user_nicename];
        $lifestyle[username] = $res;
        if (empty($lifestyle) && (int) $lifestyle['state'] == -1) {
            $this->error('该文章已删除或不存在!');
        }
        $test = NumToCNMoney($lifestyle[money]);
        $lifestyle[daxie] = $test;
        $this->assign('lifestyle', $lifestyle);
        $this->display();
        /*$this->ajaxReturn([
            'rows' => $lifestyle,
            'status' => 1
        ]);*/
    }

    public function edit_post() {
        if (IS_POST) {
            $data = I('post.lifestyle');
            $fileid = I('post.fileid', 0, 'intval');
            if (empty($data['content'])) {
                $this->error('内容不能为空!');
            }
/*            $data['content'] = htmlspecialchars_decode($data['content']);
            $data['excerpt'] = nl2br($data['excerpt']);*/
            $data = array_merge($data, [
                'update_user_id' => sp_get_current_admin_id()
            ]);
            if ($this->lifestyles_model->save($data) !== false) {

                $this->success("编辑成功！",U('edit',['id'=>$data['lifestyle_id']]));
            } else {
                $this->error('编辑失败！');
            }
        }
    }

    public function delete() {
        $id = I('get.id', 0, 'intval');
        if ($this->lifestyles_model->save(['lifestyle_id' => $id, 'state' => -1])) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    public function delete_all() {
        if (IS_POST) {
            $ids = I('post.ids');
            if ($this->lifestyles_model->where(['lifestyle_id' => ['in', $ids]])->save(['state' => -1])) {
                $this->success('删除成功！');
            } else {
                $this->error('删除失败！');
            }
        }
    }
    public function get_name(){
        $number =  I('number','intval');
        $res = $lifestyle = $this->lifestyles_model->where(['number' => $number])->field('name')->select();
        $name = $res[0][name];
        /*$this->ajaxReturn([
            'name' => $name,
            'status' => 1
        ]);*/
        echo $name;
    }
}
