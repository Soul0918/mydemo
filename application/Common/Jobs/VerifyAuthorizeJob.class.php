<?php

namespace Common\Jobs;


class VerifyAuthorizeJob {
	/**
	 * @param
	 * int user_id 用户id
	 */
	public function perform() {
		\Think\Log::write ( __CLASS__ . ':' . date ( 'Y-m-d H:i:s' ) . '开始运行:---------------------------------------------------' );
		C("TOKEN_ON", false);
		$cert_model = D('CommunityRoomCert');
		$cache_model = D('DeviceAuthCache');
		$devices_model = D('Devices');
		$auth_model = D('DeviceAuth');
		
		$param = $this->args['param'];
		$user_id = $param['user_id'];
		
		if($user_id){
			//获取用户验证数据，循环查询设备
			$certs = $cert_model->where(['user_id'=>$user_id,'state'=>1])->select();
			if($certs){
				$cache_model->where(['user_id'=>$user_id])->delete();
				foreach ($certs as $key=>$val){
					//获取设备，循环查询权限
					$where[]  = ['community_id'=>$val['community_id'],'unit_id'=>0];
					$where['unit_id']  = $val['unit_id'];
					$where['_logic'] = 'or';
					$map['_complex'] = $where;
					$map['state']  = ['gt',0];
					$devices = $devices_model->where($map)->select();
					if($devices){
						foreach ($devices as $k=>$device){
							//获取权限，验证权限是否匹配，添加到缓存表
							$auths = $auth_model->where(['device_id'=>$device['device_id'],'state'=>1])->select();
							if($auths){
								foreach ($auths as $i=>$auth){
									$add_bol = false;
									switch ($auth['owner_type']){
										case '1':
											if($auth['owner_id'] == $val['user_id']){
												$add_bol = true;
											}
											break;
										case '2':
											if($auth['owner_id'] == $val['room_id']){
												$add_bol = true;
											}
											break;
										case '3':
											if($auth['owner_id'] == $val['unit_id']){
												$add_bol = true;
											}
											break;
										case '4':
											if($auth['owner_id'] == $val['community_id']){
												$add_bol = true;
											}
											break;
										case '5':
											if($auth['owner_id'] == $val['company_id']){
												$add_bol = true;
											}
											break;
									}
									if($add_bol){
										//添加到缓存表
										$data = [
												'user_id'=>	$val['user_id'],
												'device_id'=>$device['device_id'],
												'start_at' => $val['start_at'],
												'expiration_at' => $val['expiration_at'],
												'auth_id' => $auth['auth_id']
										];
										$cache_model->add($data);
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
	
	private function add_cache($user_id,$device_id){
		$auth_model = D('DeviceAuth');
		$cache_model = D('DeviceAuthCache');
		$auths = $auth_model->where(['device_id'=>$device_id,'state'=>1])->select();
		if($auths){
			foreach ($auths as $i=>$auth){
				$data = [
					'user_id'=>	$user_id,
					'device_id'=>$device_id,
					'start_at' => $auth['start_at'],
					'expiration_at' => $auth['expiration_at'],
					'auth_id' => $auth['auth_id']
				];
			}
		}
	}
}