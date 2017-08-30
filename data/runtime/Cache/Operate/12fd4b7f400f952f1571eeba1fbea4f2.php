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
            
    <button type="submit" class="layui-btn layui-btn-normal layui-btn-small" lay-filter="*" lay-submit=""><?php echo L('SAVE');?></button>
    <div class="layui-btn layui-btn-normal layui-btn-small" style=" margin-left: 0px;" onclick="cancelDetail();">取消</div>

            <i class="layui-icon" onclick="closeDetail();" style="font-size: 25px; cursor: pointer; position: fixed; right: 15px; top: 7px;z-index:99;">&#x1006;</i>
        </div>
        <div id="divInput" style="width:100%; overflow-x:hidden; overflow-y:auto; min-width:500px; margin-top:40px;">
            <div class="detailpage-nav">
                
    游戏配置<b class="arrow fa fa-angle-right normal"></b>详情

            </div>
            
    <div class="layui-form-item">
        <label class="layui-form-label">客户编码</label>
        <div class="layui-input-inline">
            <input lay-verify="required" type="text" id="number" onblur="myFunction()" name="lifestyle[number]" placeholder="请输入客户编码"  class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">客户名称</label>
        <div class="layui-input-inline">
            <input lay-verify="required" type="text" id="y" name="lifestyle[name]"  class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">收费项目</label>
        <div class="layui-input-inline">
            <input lay-verify="required" type="text" value="xxxx年xx月技术服务费" id="content" name="lifestyle[content]" placeholder="收费项目"  class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">收费金额</label>
        <div class="layui-input-inline">
            <input lay-verify="number" type="text" name="lifestyle[money]" placeholder="收费金额"  class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">账户</label>
        <div class="layui-input-inline">
            <select name="lifestyle[zhanghu]">
                <option value="6224 3927 0000 3779 61 广州市农村商业银行番禺天安支行">6224 3927 0000 3779 61 广州市农村商业银行番禺天安支行</option>
                <option value="6224 3927 0000 3779 79 广州市农村商业银行番禺天安支行">6224 3927 0000 3779 79 广州市农村商业银行番禺天安支行</option>
            </select>

        </div>

    </div>
    <div class="layui-inline">
        <label class="layui-form-label">日期</label>
        <div class="layui-input-inline">
            <input lay-verify="date" type="text" name="lifestyle[time]" class="layui-input" id="test1" value=<?php echo (date('Y-m-d',$time)); ?> >
        </div>
    </div>
    <script>
                layui.use('form', function () {
                    form = layui.form(); //获取form组件
                    //监听提交
                    form.on('submit(*)', function (data) {
                        postData({
                            url: "<?php echo U('add_post');?>",
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
                function myFunction() {
                    var data = document.getElementById("number");
                    var number = data.value
                    $.ajax({
                        type: "GET",
                        url: "<?php echo U('get_name');?>",
                        data: {
                            number: number,
                        },
                        success: function (msg) {
                            y.value= msg;
                        }
                    })
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