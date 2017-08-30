<?php
namespace Common\Model;
use Think\Model\RelationModel;

class RoleUserModel extends RelationModel
{
	//关联
	protected $_link = [
			'Role' => array(
					'mapping_type' => self::BELONGS_TO,
					'class_name' => 'Role',
					'foreign_key' => 'role_id',
					'relation_foreign_key' => 'id',
					'mapping_fields' => 'type,name,status',
					'as_fields' => 'type:type,name:name,status:status'
			)
	];
}