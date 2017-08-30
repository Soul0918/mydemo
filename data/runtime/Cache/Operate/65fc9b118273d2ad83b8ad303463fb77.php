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
        <button class="layui-btn layui-btn-normal layui-btn-small"
                lay-filter="*" lay-submit=""><?php echo L('SAVE');?></button>
        <div class="layui-btn layui-btn-danger layui-btn-small" onclick="cancelDetail();"><?php echo L("CANCEL");?></div>
    </div>
    <div class="layui-input-inline" group="view">
        <?php if($data['type'] != 1): ?><div class="layui-btn layui-btn-normal layui-btn-small" onclick="editDetail();"><?php echo L("EDIT");?></div><?php endif; ?>
        <?php if($data['type'] != 1 and $data['status'] == 1): ?><div class="layui-btn layui-btn-normal layui-btn-small" onclick="authorize()"><?php echo L("ROLE_SETTING");?></div><?php endif; ?>
        <?php if($data['status'] == 1): ?><div class="layui-btn layui-btn-normal layui-btn-small" onclick="roleassign()"><?php echo L("ROLE_ALLOT");?></div>
            <div class="layui-btn layui-btn-danger layui-btn-small" onclick="Delete()"><?php echo L("DELETE");?></div><?php endif; ?>
    </div>

            <i class="layui-icon" onclick="closeDetail();" style="font-size: 25px; cursor: pointer; position: fixed; right: 15px; top: 7px;z-index:99;">&#x1006;</i>
        </div>
        <div id="divInput" style="width:100%; overflow-x:hidden; overflow-y:auto; min-width:500px; margin-top:40px;">
            <div class="detailpage-nav">
                
    <?php echo L('ADMIN_RBAC_INDEX');?><b class="arrow fa fa-angle-right normal"></b><?php echo L('ADMIN_RBAC_ROLEEDIT');?>

            </div>
            
    <input type="hidden" name="id" value="<?php echo ($data["id"]); ?>" />
    <input type="hidden" name="genre" value="<?php echo ($genre); ?>" />
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('ROLE_NAME');?></label>
        <div class="layui-input-inline" >
            <input type="text" class="layui-input" name="name" data-origin="<?php echo ($data["name"]); ?>" required lay-verify="required" placeholder="<?php echo L('ROLE_INPUT_NAME');?>" autocomplete="off" group="edit">

            <label class="layui-form-label layui-form-label-view" group="view"><?php echo ($data["name"]); ?></label>
        </div>
        <div class="layui-form-mid layui-word-aux" style="color:red;" group="edit">*</div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('ROLE_DESCRIPTION');?></label>
        <div class="layui-input-inline" >
            <textarea group="edit" name="remark" rows="2" cols="20" placeholder="<?php echo L('ROLE_INPUT_DESCRIPTION');?>" class="layui-textarea"><?php echo ($data["remark"]); ?></textarea>
            <label class="layui-form-label layui-form-label-view" group="view"><?php echo ($data["remark"]); ?></label>                   
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('CHOOSE_TYPE');?></label>
        <div class="layui-input-inline">
           <!--  <select name="type" lay-filter="role" lay-verify="" id="type_select" disabled>
                <?php if($genre == 0): ?><option value="0" name="type" ><?php echo L('OPERATOR');?></option><?php endif; ?>
                <?php if($genre == 1): $system_select=$data['type']==1?"selected":""; ?>
                    <?php $custom_select=$data['type']==2?"selected":""; ?>
                    <option value="2" name="type" <?php echo ($custom_select); ?>><?php echo L('CUSTOM');?></option>
                    <option value="1" name="type" <?php echo ($system_select); ?>><?php echo L('SYSTEM');?></option><?php endif; ?>
                <?php if($genre == 2): ?><option value="2" name="type"><?php echo L('VILLAGE');?></option><?php endif; ?>	
            </select> -->	
            
                  <?php if($genre == 0): ?><label class="layui-form-label layui-form-label-view"><?php echo L('OPERATOR');?></label><?php endif; ?>
                  <?php if($genre == 1): if($data['type']==1){ $select = "system"; }else if($data['type']==2){ $select = "custom"; } ?>
                       <?php if($select == 'custom'): ?><label class="layui-form-label layui-form-label-view" name="type"><?php echo L('CUSTOM');?></label>
                            <?php elseif($select == 'system'): ?>
                               <label class="layui-form-label layui-form-label-view" name="type"><?php echo L('SYSTEM');?></label><?php endif; endif; ?>
                       <?php if($genre == 2): ?><label class="layui-form-label layui-form-label-view" name="type"><?php echo L('VILLAGE');?></label><?php endif; ?>
                       </if>

        </div>
    </div>
    <?php if($genre > 0): ?><div class="layui-form-item">
            <label class="layui-form-label">所属公司</label>
            <div class="layui-input-inline"  group="edit">
                <select lay-filter="abc" name="company_id" class="test-select" data-placeholder="请选择公司" >
                    <option value=""></option>
                    <?php if(is_array($companys)): $i = 0; $__LIST__ = $companys;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['id'] == $data['company_id']): $selected = 'selected'; ?>
                            <?php else: ?>
                            <?php $selected = ''; endif; ?>
                        <option value="<?php echo ($vo["id"]); ?>" <?php echo ($selected); ?>><?php echo ($vo["company_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>

            </div>
            <div class="layui-input-inline"  group="view">
                <label class="layui-form-label layui-form-label-view" group="view"><?php echo ($data["company_name"]); ?></label>
            </div>
        </div>
        <?php if($genre == 2): ?><div class="layui-form-item">
                <label class="layui-form-label">所属小区</label>
                <div class="layui-input-inline"  group="edit">

                    <div class="layui-input-inline" >
                        <select id="xiaoquid" class="test-select" name="community_id" data-placeholder="请选择小区">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="layui-input-inline"  group="view">
                    <label class="layui-form-label layui-form-label-view" group="view"><?php echo ($data["community_name"]); ?></label>
                </div>
            </div><?php endif; endif; ?>

    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('STATUS');?></label>
        <div class="layui-input-inline" group="edit">
            <?php $active_true_checked=($data['status']==1)?"checked":""; ?>
            <input type="radio" name="status" value="1" title="<?php echo L('ROLE_OPEN');?>" id="active_true" <?php echo ($active_true_checked); ?>/>
            <?php $active_false_checked=($data['status']==0)?"checked":""; ?>
            <input type="radio" name="status" value="0" title="<?php echo L('ROLE_CLOSE');?>" id="active_false"<?php echo ($active_false_checked); ?>>
        </div>
        <div class="layui-input-inline" group="view">
            <?php $active_checked_status=($data['status']==1)?L('ROLE_OPEN1'):L('ROLE_CLOSE1'); ?>
            <label class="layui-form-label layui-form-label-view"><?php echo ($active_checked_status); ?></label>
        </div>
    </div>

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
 
    <script>
        $('[group="edit"]').hide();

        function editDetail() {
            $('[group="view"]').hide();
            $('[group="edit"]').show();
            initEdit();
        }

        function cancelDetail() {
            $('[group="view"]').show();
            $('[group="edit"]').hide();
        }

        layui.use('form', function () {
            form = layui.form(); //获取form组件

            //监听提交
            form.on('submit(*)', function (data) {
                postData({
                    url: "<?php echo U('roleedit_post',array('genre'=>$genre));?>",
                    params: data.field,
                    success: function (result) {
                        if (result.status == 1) {
                            $('#tabList').bootstrapTable('refresh');
                            showDetail(result.url);
                            layer.msg(result.info, {
                                time: 2000
                            });
                        } else {
                            layer.alert(result.info);
                        }
                    }
                });
                return false;
            });
            form.on("select(abc)", function (data) {
                //  console.log(data.elem); //得到select原始DOM对象
                // console.log(data.value); //得到被选中的值
                getXiaoqu(data.value);
            });
        });
        function Delete() {
            var DeleteUrl = "<?php echo U('roledelete',array('id'=>$data['id']));?>";
            restoreDetail('确认要删除该记录吗？', DeleteUrl, 'tabList');
        }
        function authorize() {
            showDetail("<?php echo U('authorize',array('id'=>$data['id'],'genre'=>$genre));?>");
        }
        function roleassign() {
            showDetail("<?php echo U('roledistribute',array('id'=>$data['id']));?>");
        }
    </script>
    <script>
        function getXiaoqu(id) {
            $.getJSON('<?php echo U("Rbac/getCommunities");?>', {
                company_id: id
            }, function (ci_objData) {
                $('#xiaoquid').empty();
                var objHtml = [];
                objHtml.push('<option value=""></option>');
                if (ci_objData.rows != undefined && ci_objData.rows.length > 0) {
                    $.each(ci_objData.rows, function () {
                        community_select = this.community_id == <?php echo ($data["community_id"]); ?> ? "selected" : "";
                        objHtml.push('<option value="' + this.community_id + '" ' + community_select + '>' + this.name + '</option>');
                    });
                }
                $(objHtml.join('')).appendTo('#xiaoquid');
                form.render();
            });
        }
        <?php if($data['company_id'] > 0): ?>var company_id = "<?php echo ($data['company_id']); ?>";
            getXiaoqu(company_id);<?php endif; ?>
    </script>