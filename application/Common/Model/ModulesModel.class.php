<?php
namespace Common\Model;

class ModulesModel extends CommonRelationModel
{
    protected function _after_insert($data, $options)
    {
        $id = $this->getLastInsID();
        $communities = D('Communities')->field('community_id, '.$id.' as module_id')->where(['state'=>1])->select();
        D('CommunityModule')->addAll($communities);
    }

    protected function _after_update($data, $options)
    {
        parent::_after_update($data, $options);
    }

    protected function _after_delete($data, $options)
    {
        parent::_after_delete($data, $options);
        D('Menu')->where(['module_id'=>$data['module_id']])->save(['module_id'=>0]);
        D('CommunityModule')->where(['module_id'=>$data['module_id']])->delete();
    }
}