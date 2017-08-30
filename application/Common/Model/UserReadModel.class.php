<?php


namespace Common\Model;


class UserReadModel extends CommonModel
{
    public function createForIds($options, $data)
    {
        $info_id    = $options['info_id'];
        $mapping_id = $options['mapping_id'];
        $user_id    = $options['user_id'];
        $is_noread  = false;
        $string = [];
        $new_data  = [];
        if ($info_id) {
        	$new_data = array_merge($data, ['info_id' => $info_id]);
            $string[] = 'info_id = ' . $info_id . '';
            $read = D('UserRead')->where(array_merge($data, ['info_id' => $info_id]))->find();
//             if (empty($read)) 
            	D('UserRead')->add(array_merge($data, ['info_id' => $info_id, 'create_time' => time()]));
        } else {
            if ($mapping_id) {
            	$new_data = array_merge($data, ['mapping_id' => $mapping_id]);
                $string[] = 'mapping_id = ' . $mapping_id . '';
                $read = D('UserRead')->where(array_merge($data, ['mapping_id' => $mapping_id]))->find();
//                 if (empty($read)) 
                	D('UserRead')->add(array_merge($data, ['mapping_id' => $mapping_id, 'create_time' => time()]));
            }
        }
        if ($user_id) {
            $read   = D('UserRead')->where(array_merge($data, ['user_id' => $user_id]))->find();
            foreach ($data as $key => $item) {
                $string[] = $key . ' = \'' . $item . '\'';
            }
            D('UserRead')->where(join(' AND ', $string))->save(['user_id' => $user_id]);
//             D('UserRead')->add(array_merge($new_data, ['user_id' => $user_id, 'create_time' => time()]));
            if (empty($read)) {
                $is_noread = true;
            }
        }

        return $is_noread;
    }
}