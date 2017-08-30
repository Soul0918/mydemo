<?php

namespace Common\Controller\Extra;


trait UserExtend
{
    public function get_user_json()
    {
        $q = I('get.q');
        $page = I('get.p', 1, 'intval');
        $user_model = D('Users');
        $data = array();
        if ($q) {
            $data = $user_model->user_select($q, $page);
        }
        $this->ajaxReturn($data);
    }
}