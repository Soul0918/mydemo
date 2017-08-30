<?php

namespace Common\Jobs;

use Managment\Controller\EquipmentController;

class EquipmentAuthorizeJob {
	public function perform() {
		\Think\Log::write ( __CLASS__ . ':' . date ( 'Y-m-d H:i:s' ) . '开始运行:---------------------------------------------------' );
		C("TOKEN_ON", false);
		$cert_model = D('CommunityRoomCert');
		$cache_model = D('DeviceAuthCache');
		
		$param = $this->args['param'];
		$device_id = $param['device_id'];
		$arr_auth_ids = $param['arr_auth_ids'];
		
		if($device_id && $arr_auth_ids){
			$owner_type = $param ['owner_type'];
			$owner_id = $param ['owner_id'];
			$condition = $param ['condition'];
			
			foreach ($owner_type as $k=>$value){
					if ($value == 1) {
						$data = [
								'user_id' => $owner_id[$k],
								'device_id' => $device_id,
								'auth_id' => $arr_auth_ids[$k]
						];
						if ($create = $cache_model->create ( $data )) {
							$cache_model->add ( $create );
						}
					} elseif ($value == 6) {
						$data = [
								'user_id' => -1,
								'device_id' => $device_id,
								'auth_id' => $arr_auth_ids[$k]
						];
						if ($create = $cache_model->create ( $data )) {
							$cache_model->add ( $create );
						}
					} else {
						if (is_array ( $condition[$k] ) && $condition[$k]) {
							if($value == 3){
								$where['u.unit_id']  = $condition[$k] ['val'];
								$where['u.pid_list']  = ['like','%'.$condition[$k] ['val'].'%'];
								$where['_logic'] = 'or';
								$map['_complex'] = $where;
								$map['a.state']  = 1;
								$certs = $cert_model->alias("a")
									->join("__COMMUNITY_UNITS__ u on a.unit_id = u.unit_id and u.state>0")
									->where($map)->select();
							}else {
								$where_cert = [
										$condition[$k] ['key'] => $condition[$k] ['val'],
										'state' => 1
								];
								$certs = $cert_model->where ( $where_cert )->select ();
							}
							
							if ($certs) {
								
								$user_ids = array_column($certs,'user_id');
								foreach ( $certs as $key => $val ) {
									$data = [
											'user_id' => $val ['user_id'],
											'device_id' => $device_id,
											'auth_id' => $arr_auth_ids[$k]
									];
									if ($val ['type'] == 4) {
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
						}
					}
				}
		}
		\Think\Log::write ( '结束运行:---------------------------------------------------' );
	}
}