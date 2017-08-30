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
                //if ($('#divInput').length > 0) {
                //    $('#divInput').height($('#' + GV.DETAIL_ID).height() - 40);
                //}
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
        .btn:focus { background-position: 0 30px !important; }
        body { overflow: hidden; }
        .rowselected { background-color: #ccc; }
        /*.layui-btn-normal { background-color: #2196f3; }*/
        .layui-form-selected dl { z-index: 9999; }
        .fly-searchbox { position: relative; margin-right: 20px; display: inline-block; vertical-align: top; float: right; width: 396px; height: 48px; }
            .fly-searchbox .icon-sousuo { position: absolute; right: 10px; top: 11px; color: #999; cursor: pointer; font-size: 17px; z-index: 2; }
            .fly-searchbox input { padding-right: 60px; height: 38px; width: 300px; position: absolute; right: 0; }
        .fly-select { height: 38px; width: 95px; font-size: 14px; }
        input[type="text"], input[type="password"] { height: 38px; line-height: 38px; }
        input[type=radio] { display: none; }
        #divInput .layui-input, #divInput .layui-form-select { width: 400px; height: 38px !important; line-height: 38px !important; margin-bottom: 0px !important; }
        #divInput .layui-input-inline { width: 400px !important; }
        #divInput .layui-form-label { cursor: default !important; width: 100px; }
        #divInput .layui-form-label-view { text-align: left; width: auto; }
        #divDetailBtn .layui-btn + .layui-btn { margin-left: 0px; }
    </style>
    
    <title><?php echo L(strtoupper(MODULE_NAME.'_'.CONTROLLER_NAME.'_'.ACTION_NAME));?>-<?php echo L('ADMIN_CENTER');?></title>
    <style>
        input[type="text"] { width: 250px; }
        .layui-form-item .layui-input-inline {width: 250px;}
    </style>

</head>
<body>
    <div class="layui-layout layui-layout-admin">
        <div id="divHeader" class="layui-header header header-demo" style="border-bottom: none;height:60px;">
    <div class="layui-main">
        <?php ?>
        <?php $login_type = session('LOGIN_TYPE'); ?>
        <?php $company_id = session('COMPANY_ID'); ?>
        <?php $community_id = session('COMMUNITY_ID'); ?>
        <ul class="layui-nav" lay-filter="" style="position: relative; right: initial;">
            <li style="display: inline-block; vertical-align: middle;">
                <a href="<?php echo U('managment/index/index');?>" class="brand">
                    <span class="title">
                        企鹅圈运营管理平台
                    </span>
                </a>
            </li>
            <li class="layui-nav-item">
                <button onclick="clearCache()" style="background:#f91;color:#fff;padding:5px 10px;border-radius:5px; border:none;" >清理缓存</button>
                <button onclick="updateMenu()" style="background:#e60;color:#fff;padding:5px 10px;border-radius:5px; border:none;" >更新菜单</button>
                <button onclick="updateAuthCache()" style="background:#2e0;color:#fff;padding:5px 10px;border-radius:5px; border:none;">更新门禁设备</button>
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


        <div class="layui-tab layui-tab-brief" lay-filter="demoTitle">
            <form class="layui-form" method="post" enctype="multipart/form-data">
                <div id="divTitle" class="layui-tab-title site-demo-title" style="top: 60px; height: auto; ">
                    <span class="layui-breadcrumb" style="background-color: #ececec; padding: 0 20px 0; margin: 0; border: none; height: 45px; display: flex; align-items: center; ">
                        <?php echo ($page_nav); ?>
                    </span>
                    <div id="divBtn">
                        
    <button class="layui-btn layui-btn-normal layui-btn-small" id="submit" type="submit" lay-filter="*" lay-submit=""><?php echo L('SAVE');?></button>

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
                        <div id="divInput" class="layui-main" style="margin: 0 15px;margin-top:10px;">
                            
    <!-- <form class="layui-form" method="post" action="<?php echo U('User/userinfo_post');?>"> -->
    <div id="userinfo" style="margin-top:20px;">
            <fieldset>
                <div class="layui-form-item">
                        <label class="layui-form-label"><?php echo L('REALNAME');?></label>
                        <div class="layui-input-inline">
                            <input type="text" name="user_name" required  lay-verify="required" autocomplete="off" placeholder="<?php echo L('INPUT_REALNAME');?>" class="layui-input" value="<?php echo ($user_name); ?>">
                        </div>
                        <div class="layui-form-mid layui-word-aux" style="color:red;" group="edit">*</div>
                </div>
                <div class="layui-form-item">
                        <label class="layui-form-label"><?php echo L('NICKNAME');?></label>
                        <div class="layui-input-inline">
                            <input type="text" name="user_nicename" required  lay-verify="required" autocomplete="off" placeholder="<?php echo L('INPUT_NICKNAME');?>" class="layui-input" value="<?php echo ($user_nicename); ?>">
                        </div>
                        <div class="layui-form-mid layui-word-aux" style="color:red;" group="edit">*</div>
                </div>

                <div class="layui-form-item"></div>

                <div class="layui-form-item">
                    <!--电话-->
                        <label class="layui-form-label" for="input-user_url"><?php echo L('PHONENUM');?></label>
                        <div class=layui-input-inline>
                            <input type="text" id="input-user_url" name="mobile" lay-verify="required" placeholder="<?php echo L('INPUT_PHONENUM');?>" autocomplete="off" class="layui-input" value="<?php echo ($mobile); ?>">
                        </div>
                        <div class="layui-form-mid layui-word-aux" style="color:red;" group="edit">*</div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label"><?php echo L('GENDER');?></label>
                    <div class="layui-input-inline sex-radio">
                        <input type="radio" name="sex" value="0" title="<?php echo L('GENDER_SECRECY');?>">
                        <input type="radio" name="sex" value="1" title="<?php echo L('MALE');?>">
                        <input type="radio" name="sex" value="2" title="<?php echo L('FEMALE');?>">
                    </div>

                </div>
                <div class="layui-form-item">
                    <!--邮箱-->
                        <label class="layui-form-label" for="input-user_url"><?php echo L('EMAIL');?></label>
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input" id="input-user_url" placeholder="http://thinkcmf.com" name="user_email" value="<?php echo ($user_email); ?>">
                        </div>
                </div>


                <!--按钮-->
                <!--
                <div class="form-actions">
                    <button type="submit" class="layui-btn layui-btn-normal"><?php echo L('SAVE');?></button>
                </div> -->
            </fieldset>
    </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div id="divFooter" class="layui-footer footer footer-demo">
    <div class="layui-main">
        
            Copyright © 广东华城信息科技有限公司2017, All Rights Reserved. 粤ICP备15104217号-1
        
    </div>
</div>
    </div>
    <script src="/demo-master/public/js/common.js"></script>
    <script src="/demo-master/public/simpleboot/bootstrap/js/bootstrap.min.js"></script>
    <script src="/demo-master/public/simpleboot/layui/layui.js"></script>
    <script src="/demo-master/public/bootstrap-table-master/src/bootstrap-table.js"></script>
    <script src="/demo-master/public/bootstrap-table-master/dist/locale/bootstrap-table-zh-CN.js"></script>
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
    </script>

    
    <script type="text/javascript">
        var sex = '<?php echo ($sex); ?>';
        $(".sex-radio input").each(function () {
            var this_sex = $(this).val();
            if (this_sex == sex) {
                $(this).attr("checked", 'checked');
            }
        });


//         //提交
//         $('#submit').click(function () {
//             $.ajax({
//                 type: "POST",
//                 url: "<?php echo U('User/userinfo_post');?>",
//                 data: $("#userinfo").serialize(),
//                 datatype: "html",
//                 success: function (data) {
//                     alert(data.info);
//                     //$("#msg").html(decodeURI(data));
//                 },
//                 error: function () {
//                 }
//             });
//         });
        layui.use('form', function () {
            var form = layui.form();

          //监听提交
     	   form.on('submit(*)', function (data) {
//      	    	var fromdata = getFormJson('.layui-form');
     			/*layer.alert(JSON.stringify(fromdata));*/ 
     	        postData({
     	            url: "<?php echo U('userinfo_post');?>",
     	            params: $("form").serialize(),
     	            success: function (result) {
     	                if (result.status == 1) {
//      	                	$('#tabList').bootstrapTable('refresh');
//      	                	showDetail("<?php echo U('edit');?>?id=<?php echo ($device["device_id"]); ?>");
//      	                    layer.msg('保存成功', {
//      	                        time: 2000
//      	                    });
     	                	layer.msg('您的信息已经修改成功！', {
                                icon:1,
                      		  time: 20000, //20s后自动关闭
                      		  btn: ['知道了'],
                      		  yes: function(index){
                      		    layer.close(index);
                      		  }
                      		});
     	                }
     	               else {
     	                    layer.alert(result.info);
     	                }
     	            }
     	        });
     	        return false;
     	    });
            form.render('radio');
        });
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
            </script><?php endif; endif; ?>
</body>
</html>