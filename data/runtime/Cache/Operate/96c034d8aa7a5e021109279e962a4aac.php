<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh_CN">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="This is page-header (.page-header &gt; h1)">
    <link rel="shortcut icon" type="image/x-icon" href="/demo-master/public/images/favicon.ico">
    <script>
        //全局变量
        var GV = {
            HOST: "<?php echo ($_SERVER['HTTP_HOST']); ?>",
            ROOT: "/demo-master/",
            WEB_ROOT: "/demo-master/",
            JS_ROOT: "public/js/",
            PUBLIC: "/demo-master/public"
        };
    </script>
    <script src="/demo-master/public/js/wind.js"></script>
    <!--异步加载css-->
    <script>
        Wind.css("/demo-master/public/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css");
        Wind.css("/demo-master/public/simpleboot/font-awesome/4.4.0/css/font-awesome.min.css?page=index");
        Wind.css("/demo-master/public/simpleboot/layui/css/layui.css");
        Wind.css("/demo-master/public/simpleboot/layui/css/global.css");
        Wind.css("/demo-master/public/js/artDialog/skins/default.css");
        Wind.css("/demo-master/public/bootstrap-table-master/dist/bootstrap-table.min.css");
    </script>
    <!--<script src="/demo-master/public/js/jquery.js"></script>-->
    <script src="http://apps.bdimg.com/libs/jquery/1.11.3/jquery.min.js"></script>
    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!--[if IE 7]>
    <link href="/demo-master/public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css" rel="stylesheet">
    <![endif]-->
    <!--[if lte IE 8]>
    <link href="/demo-master/public/simpleboot/css/simplebootadminindex-ie.css?" rel="stylesheet"/>
    <![endif]-->
    <style>
        .menu-main { color: #aeb9c2; text-shadow: none !important; background-color: #42485b; }
        .menu-sub-icon { width: 18px; height: auto; font-size: 12px; font-weight: normal; line-height: 12px; text-align: center; }
        .layui-nav-child > li > dd > a:hover { color: #fff !important; background-color: #5b6275 !important; }
        .item-selected { background-color: #2196F3 !important; color: #fff !important; }
        .brand { font-family: "Microsoft YaHei", "Helvetica Neue", "Luxi Sans", "DejaVu Sans", Tahoma, "Hiragino Sans GB", STHeiti; padding: 0 37px 0 49px; margin: 0; height: 60px; display: inline-block; font-size: 13px; line-height: 60px; background-image: url('/demo-master/public/images/logo.png'); background-repeat: no-repeat; background-size: 29px; background-position: 14px; color: #fff; }
        .footer { height: 50px; bottom: 0; color: #a5a5a5; border-top: 1px solid #d2d2d2; text-align: center; font-size: 13px; line-height: 50px; padding: 0; background-color: initial !important; }
        h2 { color: white; }
        dd { line-height: 38px; }
        a:link { text-decoration: none; }
        a:visited { text-decoration: none; }
        a:hover { text-decoration: none; }
        a:active { text-decoration: none; }
        .btn:focus { background-position: 0 30px !important; }
        body { overflow: hidden; }
        .rowselected { background-color: #ccc; }
        .layui-btn-normal { background-color: #2196f3; }
        .layui-form-selected dl { z-index: 9999; }
        input[type="text"], input[type="password"] { height: 38px; line-height: 38px; }
        input[type=radio] { display: none; }
        /*顶部搜索框的样式*/
        .search-form { font-size: 14px; display: inline-block; padding-top: 10px; padding-left: 10px; }
            .search-form .layui-form-item { margin-bottom: 0px; }
            .search-form .search-input { width: 300px; position: relative; }
        #txtSearch { padding-right: 25px; }
        .search-form .layui-icon { position: absolute; right: 10px; top: 13px; cursor: pointer; color: #999; }
        /*************/

        .table-bordered td { word-break: break-all; word-wrap: break-word; }

        .table_btn { color: #2196f3; text-decoration: underline !important; }
    </style>

    
    <style>
        .btn-choose {
            border: none;
            background-color: #fff;
            text-decoration: underline;
            color: #2196f3;
        }
    </style>

</head>
<body>
    <div class="layui-layout layui-layout-admin">
        <div>
            <div id="divTitle" class="layui-tab-title site-demo-title" style="top: 0px; height: auto; border-bottom: 1px solid #2196f3; z-index:999;">
                <div id="divBtn">
                    
    <div class="layui-form search-form" style="margin:10px;">
        <div class="layui-input-inline search-input">
            <input class="layui-input" id="txtSearch" autocomplete="off" value="" type="text" placeholder="<?php echo L('USERNAME');?> / <?php echo L('NICENAME');?> / <?php echo L('PHONE_NUMBER');?>">
            <i id="search" class="layui-icon" onclick="choose();">&#xe615;</i>
        </div>
    </div>

                </div>
            </div>
            <div id="divBody" class="layui-body layui-tab-content site-demo site-demo-body">
                <div class="layui-tab-item layui-show">
                    <div class="layui-main" style="margin: 0 15px;">
                        
    <table id="userList" class="table table-bordered">
        <thead>
            <tr>
                <th data-field="id" data-formatter="actionFormatter" data-width="50" data-align="center"><?php echo L('ACTIONS');?></th>
                <th data-field="user_name" data-sortable="true"><?php echo L('USERNAME');?></th>
                <th data-field="user_nicename" data-sortable="true"><?php echo L('NICENAME');?></th>
                <th data-field="user_email" data-sortable="true"><?php echo L('EMAIL');?></th>
                <th data-field="mobile" data-sortable="true"><?php echo L('PHONE_NUMBER');?></th>
                <th data-field="user_status" data-formatter="stateFormatter" data-width="100" data-sortable="true"><?php echo L('STATUS');?></th>
            </tr>
        </thead>
    </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/demo-master/public/js/common.js"></script>
    <script src="/demo-master/public/simpleboot/bootstrap/js/bootstrap.min.js"></script>
    <script src="/demo-master/public/simpleboot/layui/layui.js"></script>
    <script src="/demo-master/public/bootstrap-table-master/src/bootstrap-table.js"></script>
    <script src="/demo-master/public/bootstrap-table-master/dist/locale/bootstrap-table-zh-CN.js"></script>
    <script src="/demo-master/public/js/hcjs/jquery.table.js"></script>
    <script src="/demo-master/public/js/hcjs/jquery.detail.js"></script>

    <script>
        setTimeout(function () {
            var iHeight = $(window.parent.document).find('.layui-layer-content').find('iframe').height();
            iHeight = iHeight - $('#divTitle').height();
            $('#divBody').height(iHeight);
        }, 1000);
    </script>

    
    <script>


        load();
        function load(param) {
            if (param == undefined) {
                param = ''
            }
            $('#userList').bootstrapTable({
                method: 'get',
                url: "<?php echo U('getusers',['id'=>1]);?>?" + param,
                pageSize: 5,
                idField: "id", //标识哪个字段为id主键
                onLoadError: function (data) {
                    $('#userList').bootstrapTable('removeAll');
                },

            });
        }

        //搜索框触发事件
        function choose() {
            var search = $("#txtSearch").val();
            $('#userList').bootstrapTable('destroy');
            load("search=" + search);
        }
        $('#txtSearch').keyup(function (event) {
            if (event.keyCode == 13) {
                choose();
            }
        });

        function stateFormatter(value, row) {
            switch (value) {
                case '-1':
                    return '已删除';
                    break;
                case '1':
                    return '正常';
                    break;
                case '2':
                    return '未验证';
                    break;

            }

        }

        function actionFormatter(value, row) {
            var name = "'" + row.user_nicename + "'";
            var choosebtn = '<button class="btn-choose" type="button" onclick="chooseafter('+ row.id + ',\'' + row.user_name
                 + '\',\'' + row.user_nicename + '\',\'' + row.mobile + '\');"><?php echo L('CHOOSE');?></button>';
            return choosebtn;
        }

        function chooseafter(id,user_name,user_nicename,mobile) {
            user = {
                user_name:user_name,
                user_nicename:user_nicename,
                id:id,
                mobile:mobile
            };
            window.parent.addSelectUser(user);
        }

    </script>


</body>
</html>