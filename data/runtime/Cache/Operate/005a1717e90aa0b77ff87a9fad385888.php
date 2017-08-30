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
            
    <div class="layui-btn layui-btn-normal layui-btn-small" onclick="editDetail();" group="view"><?php echo L('EDIT');?></div>
    <div class="layui-btn layui-btn-danger layui-btn-small" onclick="del();" group="view"><?php echo L('DELETE');?></div>
    <div class="layui-input-inline" group="edit">
        <button class="layui-btn layui-btn-normal layui-btn-small" lay-filter="*" lay-submit=""><?php echo L('SAVE');?></button>
        <div class="layui-btn layui-btn-danger layui-btn-small" onclick="cancelDetail();"><?php echo L('CANCEL');?></div>
    </div>

            <i class="layui-icon" onclick="closeDetail();" style="font-size: 25px; cursor: pointer; position: fixed; right: 15px; top: 7px;z-index:99;">&#x1006;</i>
        </div>
        <div id="divInput" style="width:100%; overflow-x:hidden; overflow-y:auto; min-width:500px; margin-top:40px;">
            <div class="detailpage-nav">
                
    收据管理<b class="arrow fa fa-angle-right normal"></b>详情

            </div>
            
    <input type="hidden" name="lifestyle[lifestyle_id]"  value="<?php echo ($lifestyle["lifestyle_id"]); ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">客户编码</label>
        <div class="layui-input-inline">
            <label class="layui-form-label layui-form-label-view" group="view"><?php echo ($lifestyle["number"]); ?></label>
            <input type="text" name="lifestyle[number]" placeholder="请输入标题"  class="layui-input" group="edit" value="<?php echo ($lifestyle["number"]); ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">客户名称</label>
        <div class="layui-input-inline">
            <label class="layui-form-label layui-form-label-view" group="view"><?php echo ($lifestyle["name"]); ?></label>
            <input type="text" name="lifestyle[name]" placeholder="请输入标题"  class="layui-input" group="edit" value="<?php echo ($lifestyle["name"]); ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">收费项目</label>
        <div class="layui-input-inline">
            <label class="layui-form-label layui-form-label-view" group="view"><?php echo ($lifestyle["content"]); ?></label>
            <input type="text" name="lifestyle[content]" placeholder="请输入标题"  class="layui-input" group="edit" value="<?php echo ($lifestyle["content"]); ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">收费金额</label>
        <div class="layui-input-inline">
            <label class="layui-form-label layui-form-label-view" group="view"><?php echo ($lifestyle["money"]); ?></label>
            <input type="text" name="lifestyle[money]" placeholder="请输入标题"  class="layui-input" group="edit" value="<?php echo ($lifestyle["money"]); ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">户名</label>
        <div class="layui-input-inline">
            <label class="layui-form-label layui-form-label-view" group="view"><?php echo ($lifestyle["zhanghu_name"]); ?></label>
            <label class="layui-form-label layui-form-label-view" group="edit"><?php echo ($lifestyle["zhanghu_name"]); ?></label>
            <!--<input type="text" name="lifestyle[huming]" placeholder="请输入标题"  class="layui-input" group="edit" value="<?php echo ($lifestyle["huming"]); ?>">-->
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">账户</label>
        <label class="layui-form-label layui-form-label-view" group="view"><?php echo ($lifestyle["zhanghu"]); ?></label>
        <div class="layui-input-inline" group="edit">
            <select name="lifestyle[zhanghu]" lay-verify="">
                <option value="6224 3927 0000 3779 61 广州市农村商业银行番禺天安支行">6224 3927 0000 3779 61 广州市农村商业银行番禺天安支行</option>
                <option value="6224 3927 0000 3779 79 广州市农村商业银行番禺天安支行">6224 3927 0000 3779 79 广州市农村商业银行番禺天安支行</option>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">日期</label>
        <div class="layui-input-inline">
            <label class="layui-form-label layui-form-label-view" group="view"><?php echo ($lifestyle["time"]); ?></label>
            <input type="text" name="lifestyle[time]" placeholder="yyyy-MM-dd" class="layui-input" group="edit" value="<?php echo ($lifestyle["time"]); ?>">
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

    <script type="text/javascript">
                    $('[group="edit"]').hide();

                    function editDetail() {
                        $('[group="view"]').hide();
                        $('[group="edit"]').show();
                    }

                    function del() {
                        var url = "<?php echo U('delete',array('id'=>$lifestyle['lifestyle_id']));?>";
                        restoreDetail('确认要删除该记录吗？', url, 'tabList');
                    }

                    function cancelDetail() {
                        $('[group="view"]').show();
                        $('[group="edit"]').hide();
                    }
                    layui.use('form', function () {
                        form = layui.form(); //获取form组件
                        //监听提交
                        form.on('submit(*)', function (data) {
                            /*if(ue.getContent().length == 0){
                                layer.alert('请填写内容');
                                return false;
                            }*/
                            postData({
                                url: "<?php echo U('edit_post');?>",
                                params: data.field,
                                success: function (result) {
                                    if (result.status == 1) {
                                        $('#tabList').bootstrapTable('refresh');
                                        layer.msg(result.info, {
                                            time: 2000
                                        }, function () {
                                            showDetail(result.url);
                                        });
                                    } else {
                                        layer.alert(result.info);
                                    }
                                }
                            });
                            return false;
                        });
                    });
    </script>