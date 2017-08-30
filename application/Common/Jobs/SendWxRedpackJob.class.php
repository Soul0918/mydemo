<?php

namespace Common\Jobs;

use Common\Lib\Wxapi;

class SendWxRedpackJob {

    protected $wx_public;
    protected $wxapi;

    public function perform() {
        \Think\Log::write(__CLASS__ . ':' . date('Y-m-d H:i:s') . '开始运行:---------------------------------------------------');
        \Think\Log::record(__CLASS__ . ':' . date('Y-m-d H:i:s') . '开始运行:---------------------------------------------------');
        sleep('5');
        $options = $this->args['options'];
        $data_redpack = $this->args['data_redpack'];
        $info_data = $this->args['info_data'];
        $this->wxapi = new Wxapi($options);
        $res = $this->wxapi->sendredpack($data_redpack);
        //录入红包记录
        $state = $res['return_code'] == 'SUCCESS' && $res['result_code'] == 'SUCCESS' ? 0 : 1;
        $wx_redpack_hist_data = [
            'state' => $state,
            'wx_id' => $info_data['wx_id'],
            'game_id' => $info_data['game_id'],
            'winner_id'=>$info_data['winner_id'],
            'total_amount'=>$data_redpack['total_amount'],
            'user_id'=>$info_data['user_id'],
            'mapping_id'=>$info_data['mapping_id'],
            'request'=> json_encode($data_redpack),
            'result'=> json_encode($res),
            'create_time'=>time()
        ];
        $history_id = D('wx_redpack_hist')->add($wx_redpack_hist_data);
        //更新奖品状态
        if( $res['return_code'] == 'SUCCESS' && $res['result_code'] == 'SUCCESS'){
            D('game_winners')->where(['winner_id'=>$info_data['winner_id'],'state'=>1])->save(['state'=>2]);
        }else{
            D('game_winners')->where(['winner_id'=>$info_data['winner_id'],'state'=>1])->save(['state'=>5]);
        }
        \Think\Log::record('结束运行:---------------------------------------------------');
        \Think\Log::write('结束运行:---------------------------------------------------');
    }

}
