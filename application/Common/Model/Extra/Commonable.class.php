<?php

namespace Common\Model\Extra;

trait Commonable
{
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
            $fields[$v['field']] = $v['type'];
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
	
	// 查询成功的回调方法
	protected function _after_find(&$result, $options) {
        parent::_after_find($result, $options);
        
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