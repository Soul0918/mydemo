<?php
namespace Common\Lib;

class Commonuse{
	
	/**
	 * 设备访问接口，
	 * 更新ip、登录时间
	 * @param mac 地址
	 * @return date 时间 ，格式dhis，不足补零
	 */
	public function updateDevice(){
		$mac = $_GET['mac'];
		if($mac){
			if( D("Devices")->where(['mac' => $mac])->find() ){
				$update_condition = 
				[
					'last_login_ip'	=> get_client_ip(),
					'last_login_time' => time(),
					'update_time' => time(),
					'update_user_id' => sp_get_current_admin_id()
				];
// 				if(D("Devices")->where(['mac' => $mac])->create()){
					if(D("Devices")->where(['mac' => $mac])->save($update_condition)){
						return date('dhis', time());
					}else {
						return '更新失败';
					}
// 				}else{
// 					return D("Devices")->getError();
// 				}
			}else{
				return '数据不存在';
			}
		}else {
			return '缺少mac';
		}
	}
}