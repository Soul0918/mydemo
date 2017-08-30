<?php


namespace Common\Model;


use Common\Model\Extra\Commonable;
use Think\Model\RelationModel;

class LifestylesModel extends RelationModel
{
    use Commonable;

    protected $pk = 'lifestyle_id';

    protected $_link = [
        [
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'Users',
            'foreign_key' => 'create_user_id',
            'mapping_name' => 'author',
            'as_fields' => 'user_nicename'
        ],
    ];
}