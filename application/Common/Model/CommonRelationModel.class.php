<?php

/* * 
 * 公共模型
 */

namespace Common\Model;

use Think\Model;
use Think\Model\RelationModel;

class CommonRelationModel extends RelationModel {
    protected $readonlyField    =   array();
    protected $_filter          =   array();
    protected $autoCheckFields  =   true;
    
    /**
     * 删除表
     */
    final public function drop_table($tablename) {
        $tablename = C("DB_PREFIX") . $tablename;
        return $this->query("DROP TABLE $tablename");
    }

    /**
     * 读取全部表名
     */
    final public function list_tables() {
        $tables = array();
        $data = $this->query("SHOW TABLES");
        foreach ($data as $k => $v) {
            $tables[] = $v['tables_in_' . strtolower(C("DB_NAME"))];
        }
        return $tables;
    }

    /**
     * 检查表是否存在 
     * $table 不带表前缀
     */
    final public function table_exists($table) {
        $tables = $this->list_tables();
        return in_array(C("DB_PREFIX") . $table, $tables) ? true : false;
    }

    /**
     * 获取表字段 
     * $table 不带表前缀
     */
    final public function get_fields($table) {
        $fields = array();
        $table = C("DB_PREFIX") . $table;
        $data = $this->query("SHOW COLUMNS FROM $table");
        foreach ($data as $v) {
            $fields[$v['Field']] = $v['Type'];
        }
        return $fields;
    }

    /**
     * 检查字段是否存在
     * $table 不带表前缀
     */
    final public function field_exists($table, $field) {
        $fields = $this->get_fields($table);
        return array_key_exists($field, $fields);
    }
    
    protected function _before_write(&$data) {

    }
    
    // 查询成功后的回调方法
	protected function _after_select(&$resultSet, $options) {
        parent::_after_select($resultSet, $options);

        if(!empty($options['link'])){
            if (count ( $resultSet ) > 0) {
                foreach ( $resultSet as $key => $value ) {
                    if(array_key_exists('create_time', $resultSet[$key])){
                        $resultSet[$key] ['create_time'] = date ( 'Y-m-d H:i', $value ['create_time'] );
                    }
                    if(array_key_exists('update_time', $resultSet[$key])){
                        $resultSet[$key] ['update_time'] = date ( 'Y-m-d H:i', $value ['update_time'] );
                    }
                }
            }
        }
	}
	
	// 查询成功的回调方法
	protected function _after_find(&$result, $options) {
        parent::_after_find($result, $options);

        if(!empty($options['link'])){
            if (count ( $result ) > 0) {
                if(array_key_exists('create_time', $result)){
                    $result ['create_time'] = date ( 'Y-m-d H:i', $result ['create_time'] );
                }
                if(array_key_exists('update_time', $result)){
                    $result ['update_time'] = date ( 'Y-m-d H:i', $result ['update_time'] );
                }
            }
        }
	}
    
    // 更新前的回调方法
    protected function _before_update(&$data,$options='') {
        // 检查只读字段
        $data = $this->checkReadonlyField($data);
        // 检查字段过滤
        $data = $this->setFilterFields($data);
    }
    
    /**
     * 检查只读字段
     * @access protected
     * @param array $data 数据
     * @return array
     */
    protected function checkReadonlyField(&$data) {
        if(!empty($this->readonlyField)) {
            foreach ($this->readonlyField as $key=>$field){
                if(isset($data[$field]))
                    unset($data[$field]);
            }
        }
        return $data;
    }
    
    /**
     * 写入数据的时候过滤数据字段
     * @access protected
     * @param mixed $result 查询的数据
     * @return array
     */
    protected function setFilterFields($data) {
        if(!empty($this->_filter)) {
            foreach ($this->_filter as $field=>$filter){
                if(isset($data[$field])) {
                    $fun              =  $filter[0];
                    if(!empty($fun)) {
                        if(isset($filter[2]) && $filter[2]) {
                            // 传递整个数据对象作为参数
                            $data[$field]   =  call_user_func($fun,$data);
                        }else{
                            // 传递字段的值作为参数
                            $data[$field]   =  call_user_func($fun,$data[$field]);
                        }
                    }
                }
            }
        }
        return $data;
    }
    
    /**
     * 自动生成查询条件
     * @param mixed $params 
     * @param mixed $map 
     * @return mixed
     */
    final function getMap($params, $map=null){
        $my_map=array();
        if(!is_null($map)){
            $my_map=$map;
        }
        if (!is_null ( $params )) {
            foreach ( $params as $key => $value ) {
                if(array_key_exists($key, $this->fields['_type'])){
                	if(strstr($this->fields['_type'][$key], 'int')){
                		if(is_array($value)){
                			$my_map [$key]=$value;
                		}else{
                			$my_map [$key] = array (
                					'eq',
                					( int ) $value
                			);
                		}
                	}else{
                		if(is_array($value)){
                			$my_map [$key]=$value;
                		}else{
                			$my_map [$key] = array (
                					'eq',
                					$value
                			);
                		}
                	}
                }
            }
        }
        return $my_map;
    }
}

