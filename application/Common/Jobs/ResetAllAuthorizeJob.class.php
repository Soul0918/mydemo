<?php

namespace Common\Jobs;

/**
 * 重新更新设备缓存
 *
 */
class ResetAllAuthorizeJob {
	public function perform() {

		C("TOKEN_ON", false);
		$cert_model = D('CommunityRoomCert');
		$auth_model = D('DeviceAuth');
		$cache_model = D('DeviceAuthCache');
		$log_model = D('Log');
		
		//需要更新缓存的权限数据
		$auths = $auth_model->where(['state'=>['gt', 0]])->select();
		//$log_model->add(['cat'=>'cache_auths', 'log'=>json_encode($auths)]);
		foreach ($auths as $key=>$val){
			//删除老缓存
			$cache_model->where(['auth_id'=>$val['auth_id']])->delete();
			//新增缓存
			if ($val['owner_type'] == 1) {
				$data = [
						'user_id' => $val['owner_id'],
						'device_id' => $val['device_id'],
						'auth_id' => $val['auth_id']
				];
				if ($create = $cache_model->create ( $data )) {
					$cache_model->add ( $create );
				}
			} elseif ($val['owner_type'] == 6) {
				$data = [
						'user_id' => -1,
						'device_id' => $val['device_id'],
						'auth_id' => $val['auth_id']
				];
				if ($create = $cache_model->create ( $data )) {
					$cache_model->add ( $create );
				}
			} else {
				// 				if (is_array ( $condition[$key] ) && $condition[$key]) {
				if($val['owner_type'] == 3){
					$where['u.unit_id']  = $val['owner_id'];
					$where['u.pid_list']  = ['like','%'.$val['owner_id'].'%'];
					$where['_logic'] = 'or';
					$map['_complex'] = $where;
					$map['a.state']  = 1;
					$certs = $cert_model->alias("a")
					->join("__COMMUNITY_UNITS__ u on a.unit_id = u.unit_id and u.state>0")
					->where($map)->select();
				}else {
					switch ($val['owner_type']){
						case 2:
							$key_val = 'room_id';
							break;
						case 4:
							$key_val = 'community_id';
							break;
						case 5:
							$key_val = 'company_id';
							break;
					}
					$where_cert = [
							$key_val => $val['owner_id'],
							'state' => 1
					];
					$certs = $cert_model->where ( $where_cert )->select ();
				}
		
				if ($certs) {
						
					$user_ids = array_unique(array_column($certs,'user_id'));
					var_dump($user_ids);
					foreach ( $user_ids as $k => $value ) {
						$data = [
								'user_id' => $value,
								'device_id' => $val['device_id'],
								'auth_id' => $val['auth_id']
						];
						if ($val ['owner_type'] == 4) {
							$data ['start_at'] = $val ['start_at'];
							$data ['expiration_at'] = $val ['expiration_at'];
						}
						$caches = $cache_model->where($data)->select();
						if(!$caches){
							if ($create = $cache_model->create ( $data )) {
								$cache_model->add ( $create );
							}
						}
		
					}
				}
				// 				}
			}
				
		}
	}
}