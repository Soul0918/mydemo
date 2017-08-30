<?php
namespace Operate\Controller;

use Common\Controller\OperatebaseController;

class IndexController extends OperatebaseController {


    public function welcome(){
        $this->display();
    }
    
    public function clear_cache(){
        sp_clear_cache();
        $this->ajaxReturn(['status'=>0,'info'=>'清理成功']);
    }
}
