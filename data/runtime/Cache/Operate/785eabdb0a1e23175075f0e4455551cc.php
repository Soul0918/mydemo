<?php if (!defined('THINK_PATH')) exit();?>﻿<style>
    .detailpage-nav { width: 100%; height: 30px; border-bottom: 1px solid #ddd; margin-bottom: 15px; text-indent: 10px; line-height: 30px; color: #ccc; }
    .detailpage-nav b { display: inline-block; width: 20px; text-align: center; }
    #divInput .layui-input, #divInput .layui-form-select { width: 400px; height: 38px !important; line-height: 38px !important; margin-bottom: 0px !important; }
    #divInput .layui-input-inline { width: 400px !important; }
    #divInput .layui-form-label { cursor: default !important; width:100px; }
    #divInput .layui-form-label-view { text-align: left; width: auto; }
    #divDetailBtn .layui-btn + .layui-btn { margin-left: 0px; }
</style>


<form class="layui-form" method="post" enctype="multipart/form-data">
    <div style="width: 100%; height: 100%; position: relative; overflow: hidden;">
        <div id="divDetailBtn" style="background-color: #fafafa; padding: 5px; border-bottom: solid 1px #ccc; position: fixed; width: 100%; height: 29px; z-index: 99">
            
    <div class="layui-input-inline" group="edit">
        <button class="layui-btn layui-btn-normal layui-btn-small" lay-filter="*" lay-submit=""><?php echo L('SAVE');?></button>
        <div class="layui-btn layui-btn-danger layui-btn-small" onclick="back();"><?php echo L("CANCEL");?></div>
        <div class="layui-btn layui-btn-normal layui-btn-small" onclick="openchoose(this)">新增用户</div>
    </div>

            <i class="layui-icon" onclick="closeDetail();" style="font-size: 25px; cursor: pointer; position: fixed; right: 15px; top: 7px;z-index:99;">&#x1006;</i>
        </div>
        <div id="divInput" style="width:100%; overflow-x:hidden; overflow-y:auto; min-width:500px; margin-top:40px;">
            <div class="detailpage-nav">
                
    <?php echo L('ADMIN_RBAC_INDEX');?><b class="arrow fa fa-angle-right normal"></b><?php echo L('ROLE_ALLOT');?>

            </div>
            
    <!-- 角色名称 -->
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('ROLE_NAME');?></label>
        <div class="layui-input-inline">
            <!--  <input type="text" name="name" value="<?php echo ($data["name"]); ?>" autocomplete="off" class="layui-input"  maxlength="20" readonly> -->
            <label class="layui-form-label layui-form-label-view" ><?php echo ($data["name"]); ?></label>
        </div>
    </div>

    <!-- 角色描述 -->
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('ROLE_DESCRIPTION');?></label>
        <div class="layui-input-inline">
            <!--  <textarea name="remark" rows="2" cols="20" class="layui-textarea" onkeydown="if (value.length > 250)
                         value = value.substr(0, 250)" readonly><?php echo ($data["remark"]); ?></textarea> -->
            <label class="layui-form-label layui-form-label-view"><?php echo ($data["remark"]); ?></label>
        </div>
    </div>

    <!-- 类型 -->
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('CHOOSE_TYPE');?></label>
        <div class="layui-input-inline">
            <?php if($data['type'] == 0){ $typeval =L('OPERATOR'); }elseif($data['type'] == 1){ $typeval =L('SYSTEM'); }elseif($data['type'] == 2){ $typeval =L('CUSTOM'); }elseif($data['type'] == 3){ $typeval =L('SUPER_ADMIN'); }elseif($data['type'] == 4){ $typeval =L('COMPANY_SUPER_ADMIN'); }elseif($data['type'] == 5){ $typeval =L('VILLAGE'); } ?>
            <!-- <input type="text" name="type" value="<?php echo ($typeval); ?>" autocomplete="off" class="layui-input"  maxlength="20" readonly> -->
            <label class="layui-form-label layui-form-label-view"><?php echo ($typeval); ?></label>
        </div>
    </div>

    <?php if($genre != 0): ?><div class="control-group" id="companyGroup">
            <label class="control-label"><?php echo L('COMPANY_GROUP');?></label>
            <div class="controls">
                <div class="layui-input-inline" style="width: 220px;">

                    <select id="company_id" name="company_id" class="test-select" data-placeholder="请选择公司" disabled>
                        <option value=""></option>
                        <?php if(is_array($companys)): $i = 0; $__LIST__ = $companys;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; $company_select=$vo['id']==$data['company_id']?"selected":""; ?>
                            <option value="<?php echo ($vo["id"]); ?>" <?php echo ($company_select); ?>><?php echo ($vo["company_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <?php if($genre == 2): ?><div class="layui-input-inline" style="width: 220px;">
                        <select id="xiaoquid" class="test-select" name="community_id" data-placeholder="请选择小区" disabled>
                            <option value=""></option>
                        </select>
                    </div><?php endif; ?>
            </div>
        </div><?php endif; ?>


    <!-- 选择用户 -->
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('USER');?></label>
        <div class="layui-input-inline">
            <input type="hidden" name="id" value="<?php echo ($data["id"]); ?>">
            
            <span style="display: none;">
            <?php if(is_array($user_data)): foreach($user_data as $key=>$item): $checked = in_array($item['id'], $select_user_id, true) ? 'checked' : ''; ?>
                <?php $username = $item['user_name'] ? $item['user_name'] : $item['user_nicename']; ?>
                <?php if(!empty($username)): ?><div  style="height:30px;min-height:30px;">
                        <input type="checkbox" lay-skin="primary" name="user_id[<?php echo ($key); ?>]" value='<?php echo ($item["id"]); ?>' title="<?php echo ($username); ?>" <?php echo ($checked); ?>>
                    </div><?php endif; endforeach; endif; ?>
            </span>
        </div>
    </div>

    <!-- 角色分配表格 -->
    <div class="layui-form-item" style="margin: 10px 1rem;">
        <div class="controls">
            <table class="layui-table user-table" >
                <colgroup>
                    <col width="150">
                    <col width="200">
                    <col width="150">
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo L('USERNAME');?></th>
                        <th><?php echo L('NICENAME');?></th>
                        <th><?php echo L('MOBILE');?></th>
                        <th><?php echo L('ACTIONS');?></th>
                    </tr> 
                </thead>
                <tbody>
                <?php if(is_array($role_user)): foreach($role_user as $key=>$vo): ?><tr>
                    <?php $username = $vo['user_name']?$vo['user_name']:$vo['user_nicename']; ?>
                    <td><?php echo ($vo["user_name"]); ?></td>
                    <td><?php echo ($vo["user_nicename"]); ?></td>
                    <td><?php echo ($vo["mobile"]); ?><input type="hidden" data="<?php echo ($vo["id"]); ?>" value="<?php echo ($vo["id"]); ?>"></td>
                    <td><a onclick='Delete("<?php echo U("deleteRole",array("rid"=>$vo["role_id"],"uid"=>$vo["user_id"]));?>")'><?php echo L('DELETE');?></a></td>
                    </tr><?php endforeach; endif; ?>
                </tbody>
            </table>

        </div>
    </div>
    <script src="/demo-master/public/js/common.js?123"></script>
    <block name="script">
        <script>
                        var layer;
                        layui.use('form', function (data) {
                            form = layui.form(); //获取form组件
                            layer = layui.layer
                            //监听提交
                            form.on('submit(*)', function (data) {
                                /*       layer.alert(JSON.stringify(data.field)); */
                                postData({
                                    url: "<?php echo U('rbac/roledistribute_post');?>",
                                    params: data.field,
                                    success: function (result) {
                                        if (result.status == 1) {
                                            $('#tabList').bootstrapTable('refresh');
                                            showDetail(result.url);
                                            layer.msg('保存成功', {
                                                time: 2000
                                            });
                                        } else {
                                            layer.alert(result.info);
                                        }
                                    }
                                });
                                return false;
                            });
                        });
                        //选择按钮的触发事件
                        function openchoose(obj) {
                            _adddiv = 'selectnow';
                            $('#' + _adddiv).removeAttr('id');
                            $(obj).prev().prev().children('.layui-input').attr('id', _adddiv);
                            _open = layer.open({
                                type: 2,
                                title: '选择用户',
                                content: "<?php echo U('userlist');?>",
                                area: ['700px', '550px'],
                            });
                        }
                        function back() {
                            showDetail("<?php echo U('roleedit',array('id'=>$data['id']));?>");
                        }
                        function Delete(url) {
                            layui.use('layer', function () {
                                layer.msg('确认要删除该记录吗？', {
                                    icon: 3,
                                    time: 20000, //20s后自动关闭
                                    shade: [0.2, '#222'],
                                    btn: ['确认', '取消'],
                                    yes: function (index, layero) {
                                        layer.close(index);
                                        postData({
                                            url: url,
                                            success: function (result) {
                                                switch (result.status) {
                                                    case 1:
                                                        showDetail("<?php echo U('roledistribute',array('id'=>$data['id']));?>");
                                                        break;
                                                    default:
                                                        layer.msg(result.info, {
                                                            icon: 5
                                                        });
                                                        break;
                                                }
                                            }
                                        });
                                    }
                                });
                            });
                        }
                        
                        function addSelectUser(user){
                            var t = $('[data='+user.id+']').val();
                            if(t){
                                layer.msg('该用户已经选择');
                            }else{
                                var html = '<tr><td>'+user.user_name+'</td><td>'+user.user_nicename+'</td><td>'+user.mobile+'</td><td>';
                                html += '<a onclick="remove(this)"><?php echo L('DELETE');?></a><input type="hidden" data="'+user.id+'" name="user_id['+user.id+']" value="'+user.id+'"></td></tr>';
                                $(".user-table").append(html);
                                layer.closeAll('iframe');
                            }
                        }
                        
                        function remove(obj){
                            $(obj).parent().parent().remove();
                        } 
        </script>
    
        </div>
    </div>
</form>


<script>
    $('#divInput').height($('#detail_form').height() - 40);

    function resultHandle(result, msg, id) {
        switch (result.status) {
            case 0:
                $('#tabList').bootstrapTable('refresh');
                layer.msg(msg, {
                    time: 2000
                });
                showDetail("<?php echo U('detail');?>/id/" + id);
                break;
            case - 99:
                noDetail();
                break;
            default:
                layer.msg(result.content, {
                    icon: 5
                });
                break;
        }
    }
</script>