<?php
namespace Common\Jobs;

class ActivitiesUpdateJob{
	public function perform() {
		\Think\Log::write ( __CLASS__ . ':' . date ( 'Y-m-d H:i:s' ) . '开始运行:---------------------------------------------------' );
// 		D('Log')->add(['cat'=>'activity', 'log'=>'test']);
		C("TOKEN_ON", false);
		$activity_model = D('Activities');
		
		$activities = $activity_model->where(['state'=>['gt',0]])->select();
		foreach ($activities as $key=>$val){
			$activity_date = $val['activity_date']?$val['activity_date'].' 23:59:59':0;
			$activity_date = strtotime($activity_date);
			if($activity_date<time()){
				if($create = $activity_model->create(['activity_id'=>$val['activity_id'], 'state'=>4])){
					$activity_model->save($create);
					echo $val['activity_id'].'-';
				}
			}
		}
		
		\Think\Log::write ( '结束运行:---------------------------------------------------' );
	}
}