<?php if (!defined('THINK_PATH')) exit();?>﻿<style>
    .detailpage-nav { width: 100%; height: 30px; border-bottom: 1px solid #ddd; margin-bottom: 15px; text-indent: 10px; line-height: 30px; color: #ccc; }
    .detailpage-nav b { display: inline-block; width: 20px; text-align: center; }
    #divInput .layui-input, #divInput .layui-form-select { width: 400px; height: 38px !important; line-height: 38px !important; margin-bottom: 0px !important; }
    #divInput .layui-input-inline { width: 400px !important; }
    #divInput .layui-form-label { cursor: default !important; width:100px; }
    #divInput .layui-form-label-view { text-align: left; width: auto; }
    #divDetailBtn .layui-btn + .layui-btn { margin-left: 0px; }
</style>


<form class="layui-form-tree" method="post" enctype="multipart/form-data">
    <div style="width: 100%; height: 100%; position: relative; overflow: hidden;">
        <div id="divDetailBtn" style="background-color: #fafafa; padding: 5px; border-bottom: solid 1px #ccc; position: fixed; width: 100%; height: 29px; z-index: 99">
            
    <div class="layui-btn layui-btn-normal layui-btn-small" lay-filter="*" lay-submit="" ><?php echo L('SAVE');?></div>
    <div class="layui-btn layui-btn-danger layui-btn-small" onclick="back();"><?php echo L("CANCEL");?></div>

            <i class="layui-icon" onclick="closeDetail();" style="font-size: 25px; cursor: pointer; position: fixed; right: 15px; top: 7px;z-index:99;">&#x1006;</i>
        </div>
        <div id="divInput" style="width:100%; overflow-x:hidden; overflow-y:auto; min-width:500px; margin-top:40px;">
            <div class="detailpage-nav">
                
    <?php echo L('ADMIN_RBAC_INDEX');?><b class="arrow fa fa-angle-right normal"></b>权限设置

            </div>
            
    <div class="table_full">
        <table class="table table-bordered" id="authrule-tree">
            <tbody>
                <?php echo ($categorys); ?>
            </tbody>
        </table>
    </div>
    <!-- 角色分配表格 -->
    <!--<div class="layui-form-item" style="margin: 10px 1rem;">
        <div class="controls">
            <table class="layui-table" >
                <colgroup>
                    <col width="150">
                    <col width="200">
                    <col width="150">
                </colgroup>
                <thead>
                    <tr>
                        <th><?php echo L('USERNAME');?></th>
                        <th><?php echo L('MOBILE');?></th>
                        <th><?php echo L('ACTIONS');?></th>
                    </tr> 
                </thead>
                <tbody>
                <?php if(is_array($role_user)): foreach($role_user as $key=>$vo): ?><tr>
                    <?php $username = $vo['user_name']?$vo['user_name']:$vo['user_nicename']; ?>
                    <td><?php echo ($username); ?></td>
                    <td><?php echo ($vo["mobile"]); ?></td>
                    <td><a onclick='Delete("<?php echo U("deleteRole",array("rid"=>$vo["role_id"],"uid"=>$vo["user_id"]));?>")'><?php echo L('DELETE');?></a></td>
                    </tr><?php endforeach; endif; ?>
                </tbody>
            </table>

        </div>
    </div>-->
    <input type="hidden" name="roleid" value="<?php echo ($roleid); ?>" />
    <input type="hidden" name="genre" value="<?php echo ($genre); ?>"/>
    <script type="text/javascript">
        layui.use('form', function () {
            form = layui.form(); //获取form组件
            //监听提交
            form.on('submit(*)', function (data) {
                /*       layer.alert(JSON.stringify(data.field)); */
                postData({
                    url: "<?php echo U('rbac/authorize_post');?>",
                    params: $(".layui-form-tree").serialize(),
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

        $(document).ready(function () {
            Wind.css('treeTable');
            Wind.use('treeTable', function () {
                $("#authrule-tree").treeTable({
                    indent: 20
                });
            });
        });

        function checknode(obj) {
            var chk = $("input[type='checkbox']");
            var count = chk.length;
            var num = chk.index(obj);
            var level_top = level_bottom = chk.eq(num).attr('level');
            for (var i = num; i >= 0; i--) {
                var le = chk.eq(i).attr('level');
                if (le < level_top) {
                    chk.eq(i).prop("checked", true);
                    var level_top = level_top - 1;
                }
            }
            for (var j = num + 1; j < count; j++) {
                var le = chk.eq(j).attr('level');
                if (chk.eq(num).prop("checked")) {
                    if (le > level_bottom) {
                        chk.eq(j).prop("checked", true);
                    } else if (le == level_bottom) {
                        break;
                    }
                } else {
                    if (le > level_bottom) {
                        chk.eq(j).prop("checked", false);
                    } else if (le == level_bottom) {
                        break;
                    }
                }
            }
        }

        function back() {
            showDetail("<?php echo U('roleedit',array('id'=>$roleid));?>");
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