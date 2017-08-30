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
        /*Wind.css("/demo-master/public/css/themes_blue.css");*/

        function setBodyHeight() {
            var iTop = $('#divTitle').offset().top + $('#divTitle').height() + 1;
            $('#divBody').css('top', iTop + 'px');
            $('#divBody').height($('#divMenu').height() - $('#divTitle').height() - 60);

            if ($('#' + GV.DETAIL_ID).length > 0) {
                $('#' + GV.DETAIL_ID).parent().height($('#divBody').height());
                $('#' + GV.DETAIL_ID).height($('#divBody').height());
                if ($('#divInput').length > 0) {
                    $('#divInput').height($('#' + GV.DETAIL_ID).height() - 40);
                }
            }
        }
    </script>
    <script src="/demo-master/public/js/jquery.js"></script>
    <!--[if IE 7]>
    <link href="/demo-master/public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css" rel="stylesheet">
    <![endif]-->
    <!--[if lte IE 8]>
    <link href="/demo-master/public/simpleboot/css/simplebootadminindex-ie.css?" rel="stylesheet"/>
    <![endif]-->
    <style>
        .menu-sub-icon { width: 18px; height: auto; font-size: 12px; font-weight: normal; line-height: 12px; text-align: center; }
        .brand { font-family: "Microsoft YaHei", "Helvetica Neue", "Luxi Sans", "DejaVu Sans", Tahoma, "Hiragino Sans GB", STHeiti; padding: 0 37px 0 49px; margin: 0; height: 60px; display: inline-block; font-size: 13px; line-height: 60px; background-image: url('/demo-master/public/images/logo.png'); background-repeat: no-repeat; background-size: 29px; background-position: 14px; color: #fff; }
        .footer { height: 50px; bottom: 0; color: #a5a5a5; border-top: 1px solid #d2d2d2; text-align: center; font-size: 13px; line-height: 50px; padding: 0; background-color: initial !important; }
        h2 { color: white; }
        dd { line-height: 38px; }
        a:link { text-decoration: none; }
        a:visited { text-decoration: none; }
        a:hover { text-decoration: none; }
        a:active { text-decoration: none; }
        a{cursor:pointer;}
        .btn:focus { background-position: 0 30px !important; }
        body { overflow: hidden; }
        .rowselected { background-color: #ccc; }
        /*.layui-btn-normal { background-color: #2196f3; }*/
        .layui-form-selected dl { z-index: 9999; }
        input[type="text"], input[type="password"] { height: 38px; line-height: 38px; }
        input[type=radio] { display: none; }
        /*顶部搜索框的样式*/
        .search-form { font-size: 14px; display: inline-block; float: right; }
            .search-form .layui-form-item { margin-bottom: 0px; }
            .search-form .search-input { width: 300px; position: relative; }
        #txtSearch { padding-right: 25px; }
        .search-form .layui-icon { position: absolute; right: 10px; top: 13px; cursor: pointer; color: #999; }
        /*************/

        .table-bordered td { word-break: break-all; word-wrap: break-word; }

        .table_btn {  text-decoration: underline !important; }
        .layui-nav-last{display:none;}
        .layui-nav-thi-select{display:block;}
        .layui-nav .layui-nav-child .layui-nav-last a{    padding: 0 30px;}
    </style>

    
    <title><?php echo L(strtoupper(MODULE_NAME.'_'.CONTROLLER_NAME.'_'.ACTION_NAME));?>-<?php echo L('ADMIN_CENTER');?></title>

</head>
<body>
    <div class="layui-layout layui-layout-admin">
        <div id="divHeader" class="layui-header header header-demo" style="border-bottom: none;height:60px;">
    <div class="layui-main">
        <?php $login_type = session('LOGIN_TYPE'); ?>
        <ul class="layui-nav" lay-filter="" style="position: relative; right: initial;">
            <li style="display: inline-block; vertical-align: middle;">
                <a href="<?php echo U('Operate/index/welcome');?>" class="brand">
                    <span class="title">
                        企鹅圈运营管理平台
                    </span>
                </a>
            </li>
            <li class="layui-nav-item">
                <!--<button onclick="clearCache()" style="background:white;color:black;padding:5px 10px;border-radius:5px; border:none;" >清理缓存</button>
                <button onclick="updateMenu()" style="background:#e60;color:#fff;padding:5px 10px;border-radius:5px; border:none;" >更新菜单</button>
                <button onclick="updateAuthCache()" style="background:#2e0;color:#fff;padding:5px 10px;border-radius:5px; border:none;">更新门禁设备</button>-->
            <?php if ($login_type == '1' && count($companys) > 0) { ?>
            <dl class="layui-nav-child">
                <?php if(is_array($companys)): foreach($companys as $key=>$company_): if($company_id == $company_['id']): ?><dd class="layui-this" style="padding-left:20px;"><?php echo ($company_["company_name"]); ?></dd>
                        <?php else: ?>
                        <dd><a href="<?php echo U('Managment/public/change_company',['id'=>$company_['id']]);?>"><?php echo ($company_["company_name"]); ?></a></dd><?php endif; endforeach; endif; ?>
            </dl>
            <?php } elseif ($login_type == '2' && count($communities_) > 0){ ?>
            <dl class="layui-nav-child">
                <?php if(is_array($communities_)): foreach($communities_ as $key=>$community_): if($community_id == $community_['community_id']): ?><dd class="layui-this" style="padding-left:20px;"><?php echo ($community_["name"]); ?></dd>
                        <?php else: ?>
                        <dd><a href="<?php echo U('Managment/public/change_community',['id'=>$community_['community_id']]);?>" class="<?php echo ($name==$community_["name?'layui-this':'1'"]); ?>"><?php echo ($community_["name"]); ?></a></dd><?php endif; endforeach; endif; ?>
            </dl>
            <?php } ?>
            </li>
            <li class="layui-nav-item" style="float:right;">
                <a href="javascript:;">
                    <?php if($admin['avatar']): ?><img style="width: 32px; max-height:32px; margin-right: 5px;" class="nav-user-photo" src="<?php echo sp_get_user_avatar_url($admin['avatar']);?>" alt="<?php echo ($admin["user_login"]); ?>" />
                        <?php else: ?>
                        <img style="width: 32px; margin-right: 5px;" src="/demo-master/themes/simplebootx/Operate/Public/assets/images/face.png" alt="<?php echo ($admin["user_login"]); ?>" /><?php endif; ?>
                    <?php echo L('WELCOME_USER',array('username'=>empty($admin['user_name'])?$admin['user_nicename']:$admin['user_name']));?>
                </a>
                <dl class="layui-nav-child">
                    <dd>
                        <a href="<?php echo U('User/userinfo');?>" style="color: black;">
                            <i class="fa fa-user"></i>
                            <?php echo L('ADMIN_USER_USERINFO');?>
                        </a>
                    </dd>
                    <dd>
                        <a href="<?php echo U('Setting/password');?>" style="color: black;">
                            <i class="fa fa-lock"></i>
                            <?php echo L('ADMIN_SETTING_PASSWORD');?>
                        </a>
                    </dd>
                    <dd>
                        <a href="<?php echo U('Operate/public/logout');?>" style="color: black;">
                            <i class="fa fa-sign-out"></i>
                            <?php echo L('LOGOUT');?>
                        </a>
                    </dd>
                </dl>
            </li>
        </ul>
    </div>
</div>
<div id="divMenu" class="layui-side layui-bg-black" style="top:60px;">
    <div class="layui-side-scroll">
        <?php $page_nav=''; ?>
        <?php $page_name=''; ?>
        <ul class="layui-nav layui-nav-tree" lay-filter="menu">
            <?php if(!empty($menus)): if(is_array($menus)): foreach($menus as $key=>$vo): if(($vo["name"] !='个人信息') AND strpos($vo['id'],$Think.MODULE_NAME)): $url=empty($cam_url)?strtoupper($Think.MODULE_NAME.'_'.$Think.CONTROLLER_NAME.'_'.$Think.ACTION_NAME):$cam_url; $select=false; if($vo['items']){ foreach($vo['items'] as &$value){ if($value['lang'] == $url){ $select=true; $page_nav = '<a><cite>'.$vo['name'].'</cite></a>'.'<a><cite>'.$value['name'].'</cite></a>'; $page_name = $value['name']; } if($value['items']){ foreach($value['items'] as $thi_value){ if($thi_value['lang'] == $url){ $page_nav = '<a><cite>'.$vo['name'].'</cite></a>'.'<a><cite>'.$value['name'].'</cite></a>'.'<a><cite>'.$thi_value['name'].'</cite></a>'; $select=true; $value['select_thi'] = true; } } } } }else{ if($url == $vo['lang']){ $select = true; $page_nav = '<a><cite>'.$vo['name'].'</cite></a>'; $page_name = $vo['name']; } } ?>
                    <li class="layui-nav-item <?php echo ($select && $vo['items']?'layui-nav-itemed':''); ?>">
                        <a href="<?php echo ($vo['items']?'javascript:;':$vo['url']); ?>" tip="<?php echo ($vo["name"]); ?>" class="menu-main <?php echo ($select && !$vo['items']?'item-selected':''); ?>">
                            <h2>
                                <?php var_dump(); ?>
                                <i class="fa fa-<?php echo ($vo['icon']?$vo['icon']:'desktop'); ?> normal"></i>
                                <?php echo ($vo["name"]); ?>
                            </h2>
                        </a>
                    <?php if(!empty($vo["items"])): ?><dl class="layui-nav-child">
                            <?php if(is_array($vo["items"])): foreach($vo["items"] as $key=>$ivo): ?><li>
                                <dd><a href="<?php echo ($ivo['items'] ? 'javascript:;':$ivo['url']); ?>" tip="<?php echo ($ivo["name"]); ?>" class="<?php echo ($url==$ivo['lang']?'item-selected':''); ?>" onclick="$('.layui-nav-last',$(this).parent().parent()).toggle();">
                                        <i class="fa fa-circle-thin menu-sub-icon"></i><span class="menu-text"><?php echo ($ivo["name"]); ?></span></a>
                                </dd>
                                <?php if(!empty($ivo["items"])): ?><dl class="layui-nav-last <?php echo ($ivo['select_thi'] ? 'layui-nav-thi-select':''); ?>">
                                        <?php if(is_array($ivo["items"])): foreach($ivo["items"] as $key=>$lvo): ?><li>
                                            <dd><a href="<?php echo ($lvo["url"]); ?>" tip="<?php echo ($lvo["name"]); ?>" class="<?php echo ($url==$lvo['lang']?'item-selected':''); ?>"><i class="fa fa-caret-right menu-sub-icon"></i><span class="menu-text"><?php echo ($lvo["name"]); ?></span></a></dd>
                                            </li><?php endforeach; endif; ?>
                                    </dl><?php endif; ?>
                                </li><?php endforeach; endif; ?>
                        </dl><?php endif; ?>
                    </li><?php endif; endforeach; endif; endif; ?>
        </ul>
    </div>
</div>


        <div>
            <div id="divTitle" class="layui-tab-title site-demo-title" style="top: 60px; height: auto; z-index:999; min-width:1100px;">
                <span class="layui-breadcrumb" style="background-color: #e9f0f9; padding: 0 20px 0; margin: 0; border: none; height: 45px; display: flex; align-items: center; ">
                    <?php echo ($page_nav); ?>
                </span>
                <div id="divBtn">
                    
    <!--<button class="layui-btn layui-btn-normal layui-btn-small" onclick="showDetailForm();"><?php echo L('ROLE_ROLEADD');?></button>-->
    <div class="layui-form search-form">
        <div class="layui-form-item">
            <?php if($genre > 0): ?><div class="layui-input-inline">
                    <select name="companys" name="companys" lay-filter="companys">
                        <option value="0">全部公司</option> 
                        <?php if(is_array($companys)): foreach($companys as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["company_name"]); ?></option><?php endforeach; endif; ?>
                    </select>
                </div><?php endif; ?>
            <?php if($genre > 1): ?><div class="layui-input-inline">
                    <select name="community" name="community" lay-filter="community">
                        <option value="0">全部小区</option> 
                        <?php if(is_array($communities)): foreach($communities as $key=>$vo): ?><option value="<?php echo ($vo["community_id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; ?>
                    </select>
                </div><?php endif; ?>
            <div class="layui-input-inline search-input">
                <input class="layui-input"  autocomplete="off" placeholder="角色名称/角色描述" value="<?php echo I('request.search');?>" type="text" name="search" id="txtSearch">
                <i id="search" class="layui-icon" onclick="searchData();" >&#xe615;</i>
            </div>
        </div>
    </div>

                </div>
                <script>
                    {
                        if ($('#divBtn').children().length > 0) {
                            $('#divBtn').css('padding', '10px');
                        }
                    }
                </script>
            </div>
            <div id="divBody" class="layui-body layui-tab-content site-demo site-demo-body">
                <div class="layui-tab-item layui-show">
                    <div class="layui-main" style="margin: 0 15px;">
                        
    <table id="tabList" class="table table-bordered" data-page-list="[10,20,50]">
        <thead>
            <tr>
                <th data-field="control" data-formatter="controlFormatter" data-valign="middle"  data-action="haha" data-width="50"><?php echo L('ACTIONS');?></th>
                <th data-field="id"  data-sortable="true" data-valign="middle"  data-width="20" data-visible="false">ID</th>
                <th data-field="name" data-sortable="true"  data-valign="middle"  data-width="100"><?php echo L('ROLE_NAME');?></th>
                <th data-field="remark" data-sortable="true" data-valign="middle"  data-width="100"><?php echo L('ROLE_DESCRIPTION');?></th>
        <?php if($genre == 1): ?><th data-field="company_name" data-align="center" data-valign="middle"  data-width="100">所属公司</th><?php endif; ?>
        <?php if($genre == 2): ?><th data-field="company_name" data-align="center" data-valign="middle"  data-width="100">所属公司</th>
            <th data-field="community_name" data-align="center" data-valign="middle"  data-width="100">所属小区</th><?php endif; ?>
        <th data-field="status" data-formatter="statusFormatter" data-sortable="true" data-width="60" data-valign="middle" ><?php echo L('STATUS');?></th>
        </tr>
        </thead>
    </table>

                    </div>
                </div>
            </div>
            
        </div>
        <div id="divFooter" class="layui-footer footer footer-demo">
    <div class="layui-main">
        
            Copyright © 广东华城信息科技有限公司2017, All Rights Reserved. 粤ICP备15104217号-1
        
    </div>
</div>
    </div>
    <script src="/demo-master/public/js/common.js"></script>
    <script src="/demo-master/public/simpleboot/bootstrap/js/bootstrap.min.js"></script>
    <!--<script src="/demo-master/themes/simplebootx/Managment/Public/assets/js/index.js"></script>-->
    <script src="/demo-master/public/simpleboot/layui/layui.js"></script>
    <script src="/demo-master/public/bootstrap-table-master/src/bootstrap-table.js"></script>
    <script src="/demo-master/public/bootstrap-table-master/dist/locale/bootstrap-table-zh-CN.js"></script>
    <script src="/demo-master/public/js/hcjs/jquery.table.js"></script>
    <script src="/demo-master/public/js/hcjs/jquery.detail.js"></script>

    <script>
        layui.use('element');
        setBodyHeight();
        setTimeout(function () {
            setBodyHeight();
        }, 1000);
        $(window).resize(function () {
            setBodyHeight();
        });
        function clearCache(){
            postData({
              url: "<?php echo U('index.php/Operate/index/clear_cache');?>",
              success: function (result) {
                  if (result.status == 0) {
                      layer.info('清空成功', {
                          time: 2000
                      }, function () {
                          closeDetail();
                      });
                  }
                 else {
                      layer.alert(result.info);
                  }
              }
            });
        }
        function updateMenu(){
            postData({
              url: "<?php echo U('index.php/Operate/index/updateMenu');?>",
              success: function (result) {
                  if (result.status == 0) {
                      layer.alert('更新成功', {
                          time: 2000
                      }, function () {
                          closeDetail();
                          location.reload();
                      });
                  }
                 else {
                      layer.alert(result.info);
                  }
              }
            });
        }
        function updateAuthCache(){
            postData({
              url: "<?php echo U('index.php/Operate/index/update_device_auth_cache');?>",
              success: function (result) {
                  if (result.status == 0) {
                      layer.alert('更新成功', {
                          time: 2000
                      }, function () {
                          closeDetail();
                          location.reload();
                      });
                  }
                 else {
                      layer.alert(result.info);
                  }
              }
            });
        }
    </script>

    
    <script>
        var 　_iCommunityid;
        var  form;
        load();

        function load() {
            $('#tabList').bootstrapTable({
                method: 'get',
                url: "<?php echo U('table_data',['genre'=>$genre]);?>",
                idField: "id",
                sortName: 'id',
                sortOrder: 'desc',
                queryParams: getQueryParams,
                onLoadError: function (data) {
                    $('#tabList').bootstrapTable('removeAll');
                },
                onDblClickRow: function (row, e, field) {
                    $('.rowselected').removeClass('rowselected');
                    $(e).addClass('rowselected');
                    if (field != 'control') {
                        showDetail("<?php echo U('roleedit');?>/id/" + row.id + "/genre/<?php echo ($genre); ?>");
                    }

                }
            });
        }

        function getQueryParams(params) {
            params['search'] = $('#txtSearch').val();
            params['company_id'] = $('[name=companys]').val();
            params['community_id'] = $('[name=community]').val();
            return params;
        }
        layui.use('form', function () {
             form = layui.form();
             form.on('select(companys)', function (ele) {
                $('[name=community]').val(0);
                $('[name=companys]').val(ele.value);
                $('#tabList').bootstrapTable('refresh');
                getXiaoqu(ele.value);
            });
            form.on('select(community)', function (ele) {
                $('[name=community]').val(ele.value);
                $('#tabList').bootstrapTable('refresh');
            });
        })


        $('#txtSearch').keyup(function (e) {
            if (e.keyCode == 13) {
                $('#search').trigger('click');
            }
        });
        function searchData() {
            $('#tabList').bootstrapTable('refresh');
        }
        
        function getXiaoqu(id) {
            $.getJSON('<?php echo U("Rbac/getCommunities");?>', {
                company_id: id
            }, function (ci_objData) {
                $('[name=community]').empty();
                var objHtml = [];
                objHtml.push('<option value="0">全部小区</option>');
                if (ci_objData.rows != undefined && ci_objData.rows.length > 0) {
                    $.each(ci_objData.rows, function () {
                        objHtml.push('<option value="' + this.community_id + '" >' + this.name + '</option>');
                    });
                }
                $(objHtml.join('')).appendTo('[name=community]');
                form.render();
            });
        }
    </script>
    <script>

        function communityFormatter(value, row) {
            return row.community_name
        }

        function statusFormatter(value, row) {
            if (parseInt(value) == 1) {
                return '开启';
            } else if (parseInt(value) == 0) {
                return '关闭';
            }
        }

        function controlFormatter(value, row) {
            var $control = [];
            $control.push('<a class="table_btn" href="javascript:void(0)" onclick="showDetailEdit(' + row.id + ')"><?php echo L('DETAIL');?></a>');
            return $control.join(' | ');
        }

        function showDetailForm() {
            showDetail("<?php echo U('roleadd',array('genre'=>$genre));?>");
        }

        function showDetailEdit(id) {
            showDetail("<?php echo U('roleedit',array('genre'=>$genre));?>/id/" + id);
        }
    </script>


    <?php if($admin['setting']): if(!empty($admin['setting']['agreement']) and $admin['setting']['agreement'] == 'no'): ?><script>
                var config = {
                    skin: '',
                    closeBtn: 0,
                    btn: ['同意', '关闭'],
                    yes: function () {
                        if (confirm('是否同意?')) {
                            $.post('<?php echo U('Public/agreement_post');?>');
                        }
                        layer.closeAll();
                    },
                    cancel: function (index) {
                        if (confirm('确定要关闭么?')) {
                            layer.close(index);
                            window.location.href = '<?php echo U('Public/logout');?>';
                        }
                        return false;
                    }
                }
                open_iframe_layer('<?php echo U('Public/agreement');?>', '用户协议', config);
                function controlFormatter(value, row) {
                    var state = row.state;
                    var $control = [];
                    $control.push('<a class="table_btn" href="javascript:void(0);" onclick="myShowDetail(' + row.community_id + ');"><?php echo L('DETAIL');?></a>');
                    return $control;
            	}
            </script><?php endif; endif; ?>
</body>
</html>