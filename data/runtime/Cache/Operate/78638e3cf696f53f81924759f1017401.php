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
            
    <?php if($user_status == 1): ?><div class="layui-input-inline" group="edit">
            <button class="layui-btn layui-btn-normal layui-btn-small" lay-filter="*" lay-submit=""><?php echo L('SAVE');?></button>
            <div class="layui-btn layui-btn-danger layui-btn-small" onclick="cancelDetail();"><?php echo L('CANCEL');?></div>
        </div>
        <?php if($canEdit == 1): ?><div class="layui-input-inline" group="view">
            <div class="layui-btn layui-btn-normal layui-btn-small" onclick="editDetail();"><?php echo L('EDIT');?></div>
            <div class="layui-btn layui-btn-normal layui-btn-small" onclick="ban()">拉黑</div>
            <div class="layui-btn layui-btn-danger layui-btn-small" onclick="Delete()"><?php echo L('DELETE');?></div>
        </div><?php endif; ?>
        <?php elseif($user_status == 0): ?>
        <div class="layui-btn layui-btn-normal layui-btn-small" onclick="cancelban();">启用</div><?php endif; ?>


            <i class="layui-icon" onclick="closeDetail();" style="font-size: 25px; cursor: pointer; position: fixed; right: 15px; top: 7px;z-index:99;">&#x1006;</i>
        </div>
        <div id="divInput" style="width:100%; overflow-x:hidden; overflow-y:auto; min-width:500px; margin-top:40px;">
            <div class="detailpage-nav">
                
    <?php echo L('ADMIN_RBAC_INDEX');?><b class="arrow fa fa-angle-right normal"></b><?php echo L('ADMIN_RBAC_ROLEADD');?>

            </div>
            
    <input type="hidden" name="id" value="<?php echo ($id); ?>" />
    <div class="layui-form-item">
        <!-- <label class="layui-form-label"><?php echo L('PASSWORD');?></label> -->
        <div class="layui-input-inline" >
            <input type="hidden" name="user_pass" value=""  placeholder="******" group="edit">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('USERCOMPANY_NAME');?></label>
        <div class="layui-input-inline" >
            <label class="layui-form-label layui-form-label-view" group="view" ><?php echo ($usernameval); ?></label>
            <input type="text" name="user_name" value="<?php echo ($usernameval); ?>"  placeholder="请输入用户昵称" group="edit">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('USERNICENAME');?></label>
        <div class="layui-input-inline" >
            <label class="layui-form-label layui-form-label-view" group="view" ><?php echo ($user_nicename); ?></label>
            <input type="text" name="user_nicename" value="<?php echo ($user_nicename); ?>"  placeholder="请输入用户昵称" group="edit">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('USERCOMPANY_PHONE');?></label>
        <div class="layui-input-inline">
            <label class="layui-form-label layui-form-label-view" group="view"><?php echo ($mobile); ?></label>
            <input type="text" name="mobile" value="<?php echo ($mobile); ?>"  placeholder="请输入用户电话" group="edit" required lay-verify="required|phone">
        </div>
    </div>


    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('USERCOMPANY_EMAIL');?></label>
        <div class="layui-input-inline" >
            <input type="text" class="layui-input" name="user_email" data-origin="<?php echo ($user_email); ?>" value="<?php echo ($user_email); ?>" group="edit" placeholder="<?php echo L('USERCOMPANY_EMAIL_INPUT');?>" autocomplete="off">
            <label class="layui-form-label layui-form-label-view" group="view"><?php echo ($user_email); ?></label>
        </div>
    </div>
    <?php if($genre == 2): if(!empty($communities)): ?><div class="layui-form-item">
                <label class="layui-form-label"><?php echo L('USERCOMPANY_COMMUNITY');?></label>
                <div class="layui-input-inline" >
                    <label class="layui-form-label layui-form-label-view"><?php echo ($community["name"]); ?></label>
                </div>
            </div><?php endif; endif; ?>
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('USERCOMPANY_ROLE');?></label>
        <?php if($is_com_admin['state'] > 0): ?><div class="layui-input-inline" >
                <label class="layui-form-label layui-form-label-view"><?php echo ($is_com_admin["type"]); ?></label>
            </div>	
            <?php else: ?>
            <div class="layui-input-inline" group="edit">
                <?php if(is_array($roles)): foreach($roles as $key=>$vo): ?><div class="layui-input-inline">
                        <?php $role_id_selected = in_array($vo['id'],$role_ids, true)?"checked":""; ?>
                        <input value="<?php echo ($vo["id"]); ?>"  type="checkbox" lay-skin="primary" name="role_id[]" title="<?php echo ($vo["name"]); ?>" <?php echo ($role_id_selected); ?>>
                    </div><?php endforeach; endif; ?>
            </div>
            <div class="layui-input-inline .layui-form-checkbox" group="view">
                <?php if(is_array($roles)): foreach($roles as $key=>$co): $role_name=in_array($co['id'],$role_ids)? $co['name']:""; ?>
                    <?php if ($role_name) { ?>
                    <label class="layui-form-label layui-form-label-view"><?php echo ($role_name); ?></label>
                    <?php } endforeach; endif; ?>
            </div><?php endif; ?>
    </div>

    <input type="hidden" name="community" value="<?php echo I('get.community');?>">

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

    <script src="/demo-master/public/simpleboot/layui/chosen/chosen.js"></script>
    <script>
            window.onload = function () {
                $('#selectbox').find('.layui-form-select').remove();
            };
            $('.test-select').chosen({
                width: '100%',
                no_results_text: "没有匹配结果"
            });
            $('[group="edit"]').hide();

            function editDetail() {
                $('[group="view"]').hide();
                $('[group="edit"]').show();
//            initEdit();
                layui.use('form', function () {
                    form = layui.form(); //获取form组件
                    form.render();
                });
            }

            function cancelDetail() {
                $('[group="view"]').show();
                $('[group="edit"]').hide();
            }

            function getFormJson(form) {
                var o = {};
                var a = $(form).serializeArray();
                $.each(a, function () {
                    if (o[this.name] !== undefined) {
                        if (!o[this.name].push) {
                            o[this.name] = [o[this.name]];
                        }
                        o[this.name].push(this.value || '');
                    } else {
                        o[this.name] = this.value || '';
                    }
                });
                return o;
            }


            layui.use('form', function () {

                form = layui.form(); //获取form组件


//监听提交
                form.on('submit(*)', function (data) {
                    var fromdata = getFormJson('.layui-form');
                    /*   layer.alert(JSON.stringify(fromdata)); */

                    postData({
                        url: "<?php echo U('edit_post');?>/genre/<?php echo ($genre); ?>",
                        params: fromdata,
                        success: function (result) {
                            if (result.status == 1) {
                                $('#tabList').bootstrapTable('refresh');
                                showDetail("<?php echo U('edit',['edit'=>$canEdit]);?>?id=<?php echo ($id); ?>&genre=<?php echo ($genre); ?>&community=<?php echo ($community["community_id"]); ?>");
                                layer.msg('编辑成功', {
                                    time: 2000
                                }, function () {
                                    /*  closeDetail(); */
                                });
                            } else {
                                layer.alert(result.info);
                            }
                        }
                    });

                    return false;
                });
                form.render();
            });

            function Delete() {
                var DeleteUrl = "<?php echo U('delete',array('id'=>$id,'genre'=>$genre));?>";
                deleteDetail(0, DeleteUrl, 'tabList');
            }

            function ban() {
                var url = "<?php echo U('ban',array('id'=>$id));?>";
                restoreDetail('	您确定要拉黑此用户吗？', url, 'tabList');
            }
            function cancelban() {
                var url = "<?php echo U('cancelban',array('id'=>$id));?>";
                restoreDetail('	您确定要启用此用户吗？', url, 'tabList');
            }
    </script>