<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>物业后台登录</title>
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel='icon' href='/public/images/favorite.ico' type=‘image/x-ico’ />

    <link href="/demo-master/public/simpleboot/layui/css/layui.css" rel="stylesheet">
    <script type="text/javascript">
        //全局变量
        var GV = {
            ROOT: "/demo-master/",
            WEB_ROOT: "/demo-master/",
            JS_ROOT: "public/js/",
            APP: '<?php echo (MODULE_NAME); ?>'/*当前应用名*/
        };
    </script>
    <script src="/demo-master/public/js/jquery-1.11.3.min.js"></script>
    <script src="/demo-master/public/simpleboot/layui/layui.js"></script>
    <style>
        body { background: #2BC0E4; /* fallback for old browsers */ background: linear-gradient(45deg, #02b2c9, #0dbbb6 40%, #2ac896 60%, #7ada5d); background: -webkit-linear-gradient(to top right, #02b2c9, #17c0ab 50%, #7ada5d); overflow: hidden; }

        .login-div { width: 380px; height: 415px; box-shadow: 5px 8px 15px rgba(80, 80, 80, 0.2); background: #ffffff; position: absolute; left: 50%; margin-left: -190px; margin-top: 160px; border-radius: 6px; box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; -ms-box-sizing: border-box; }

        .login-header { margin: 0px; font-size: 20px; text-align: center; line-height: 90px; border-bottom: 1px solid #efefef; }

         .login-header img { height: 44px; }

        .tips_error { position: absolute; right: 38px; color: #ff2c2c; line-height: 44px; background: #fff; background: -webkit-linear-gradient(left, rgba(255, 255, 255, 0), #fff 8px); background: linear-gradient(to right, rgba(255, 255, 255, 0), #fff 8px); }

        .user, .pw { margin: 20px 0 20px; }

        .username, .password { margin: 0 20px; background: url("/demo-master/public/images/username.svg") no-repeat; background-size: 30px; background-position: 12px 50%; border-radius: 4px; border: 1px solid #e6e6e6; }

        .password { background: url("/demo-master/public/images/password.svg") no-repeat; background-size: 30px; background-position: 12px 50%; }

        .logininput { width: 260px; padding: 5px 13px; line-height: 50px; height: 34px; background: #FFF !important; border: 0; white-space: nowrap; text-overflow: ellipsis; margin: 0 0 0 45px; }

        .login-type { height: 46px; line-height: 46px; margin: 20px 0 20px; }

        .login-button { background: #1dd892; color: #FFF; line-height: 46px; border: 0; font-size: 16px; letter-spacing: 3px; cursor: pointer; border-radius: 4px; width: 340px; margin: 0 auto; display: block; height: 46px; box-sizing: border-box; transition: background-color 0.4s; -moz-transition: background-color 0.4s; -webkit-transition: background-color 0.4s; -o-transition: background-color 0.4s;}

            .login-button:hover { background-color: #46e4a9; }

        .input-focus { border-color: #1dd892; outline: none; box-shadow: 0 0 5px #1dd892; }

        .layui-form-item > span { font-size: 14px; }

        .layui-form-item { margin: 0; text-align: center; }

        input:-webkit-autofill { background: #fff !important; }
         .verify{ margin: 0 0 0 20px; height: 40px; width: 200px; border-radius: 4px; border: 1px solid #e6e6e6; padding-left: 15px}
         .ver{margin-bottom: 30px} 
		 .layui-layer-shade{opacity: 0.3 !important;}
    </style>
</head>
<body>
    <div id="gradient">
        <div class="login-div">
            <div class="login-header">
                <img src="/demo-master/public/images/1024.png">
                <span>企鹅圈运营管理平台</span>
            </div>
            <form class="layui-ajax-form" action="<?php echo U('dologin');?>" method="post">
                <div class="user">
                    <div class="username">
                        <input type="text" name="login-name" lay-verify="username" value="<?php echo cookie('admin_username');?>"
                               autocomplete="off" autofocus="autofocus" placeholder="手机号码/邮箱" class="logininput"
                               required="required">
                    </div>
                </div>
                <div class="pw">
                    <div class="password">
                        <input type="password" name="login-pass" lay-verify="password" autocomplete="off"
                               placeholder="密码" class="logininput" required="required">
                    </div>
                </div>
                <div class="ver">
                    <input type="text" name="verify" placeholder="<?php echo L('ENTER_VERIFY_CODE');?>" class="verify"/>
                    <i class="image"><?php echo sp_verifycode_img('length=1&font_size=16&width=120&height=30&use_noise=0&use_curve=0&charset=123456789','style="cursor:
                    pointer;" title="点击获取"');?></i>
                </div>
                <div>
                    <input type="submit" lay-submit="" class="login-button" lay-filter="login" value="登录">
                </div>
                <div style="margin-top: 24px;margin-bottom: 24px;text-align: center;">
                </div>
            </form>
        </div>
    </div>
    <!--背景-->
    <canvas id="canvas"></canvas>
    <script>
        //定义画布宽高和生成点的个数
        var WIDTH = window.innerWidth, HEIGHT = window.innerHeight, POINT = 25;

        var canvas = document.getElementById('canvas');
        canvas.width = WIDTH,
            canvas.height = HEIGHT;
        var context = canvas.getContext('2d');
        context.strokeStyle = 'rgba(1,169,195,0.2)',
            context.strokeWidth = 1,
            context.fillStyle = 'rgba(4,180,200,0.5)';
        var circleArr = [];

        //线条：开始xy坐标，结束xy坐标，线条透明度
        function Line(x, y, _x, _y, o) {
            this.beginX = x,
                this.beginY = y,
                this.closeX = _x,
                this.closeY = _y,
                this.o = o;
        }
        //点：圆心xy坐标，半径，每帧移动xy的距离
        function Circle(x, y, r, moveX, moveY) {
            this.x = x,
                this.y = y,
                this.r = r,
                this.moveX = moveX,
                this.moveY = moveY;
        }
        //生成max和min之间的随机数
        function num(max, _min) {
            var min = arguments[1] || 0;
            return Math.floor(Math.random() * (max - min + 1) + min);
        }
        // 绘制原点
        function drawCricle(cxt, x, y, r, moveX, moveY) {
            var circle = new Circle(x, y, r, moveX, moveY)
            cxt.beginPath()
            cxt.arc(circle.x, circle.y, circle.r, 0, 2 * Math.PI)
            cxt.closePath()
            cxt.fill();
            return circle;
        }
        //绘制线条
        function drawLine(cxt, x, y, _x, _y, o) {
            var line = new Line(x, y, _x, _y, o)
            cxt.beginPath()
            cxt.strokeStyle = 'rgba(0,0,0,' + o + ')'
            cxt.moveTo(line.beginX, line.beginY)
            cxt.lineTo(line.closeX, line.closeY)
            cxt.closePath()
            cxt.stroke();

        }
        //初始化生成原点
        function init() {
            circleArr = [];
            for (var i = 0; i < POINT; i++) {
                circleArr.push(drawCricle(context, num(WIDTH), num(HEIGHT), num(15, 2), num(10, -10) / 40, num(10, -10) / 40));
            }
            draw();
        }

        //每帧绘制
        function draw() {
            context.clearRect(0, 0, canvas.width, canvas.height);
            for (var i = 0; i < POINT; i++) {
                drawCricle(context, circleArr[i].x, circleArr[i].y, circleArr[i].r);
            }
            for (var i = 0; i < POINT; i++) {
                for (var j = 0; j < POINT; j++) {
                    if (i + j < POINT) {
                        var A = Math.abs(circleArr[i + j].x - circleArr[i].x),
                            B = Math.abs(circleArr[i + j].y - circleArr[i].y);
                        var lineLength = Math.sqrt(A * A + B * B);
                        var C = 1 / lineLength * 7 - 0.009;
                        var lineOpacity = C > 0.03 ? 0.03 : C;
                        if (lineOpacity > 0) {
                            drawLine(context, circleArr[i].x, circleArr[i].y, circleArr[i + j].x, circleArr[i + j].y, lineOpacity);
                        }
                    }
                }
            }
        }

        //调用执行
        window.onload = function () {
            init();
            setInterval(function () {
                for (var i = 0; i < POINT; i++) {
                    var cir = circleArr[i];
                    cir.x += cir.moveX;
                    cir.y += cir.moveY;
                    if (cir.x > WIDTH) cir.x = 0;
                    else if (cir.x < 0) cir.x = WIDTH;
                    if (cir.y > HEIGHT) cir.y = 0;
                    else if (cir.y < 0) cir.y = HEIGHT;

                }
                draw();
            }, 16);
        }

    </script>

    <script src="/demo-master/public/js/common.js"></script>
    <script src="/demo-master/public/js/wind.js"></script>
    <script>
        layui.use(['form', 'jquery'], function () {
            var form = layui.form()
                , layer = layui.layer
                , $ = layer.jquery;

            //自定义验证规则
            form.verify({
                username: function (value) {
                    if (value == '') return '手机号码不能为空';
                }
                , password: [/(.+){6,12}$/, '密码必须6到12位']
            });

            //监听提交
            form.on('submit(login)', function (data) {
                return login_form(this)
            });
        });
        // 选中样式
        $('.username input,.password input').focus(
            function () {
                var item = $(this).parent();
                var tips_error = $(".tips_error");
                item.addClass('input-focus');
                tips_error.fadeOut('slow').delay(2000);
            }
        );
        $('.username input,.password input').blur(
            function () {
                var item = $(this).parent();
                item.removeClass('input-focus');
            }
        )

        //验证
        function valitate() {
            var login_name = $('input[name=login-name]').fieldValue();
            var login_pass = $('input[name=login-pass]').fieldValue();
            if (!login_name[0]) {
                $('<label class="tips_error">' + "请输入用户名" + '</label>').appendTo($(".username")).fadeIn('slow').delay(1000);
                return false;
            }
            if (!login_pass[0]) {
                $('<label class="tips_error">' + "请输入密码" + '</label>').appendTo($(".password")).fadeIn('slow').delay(1000);
                return false;
            }
            else{
            	layer.load(2);
            }
        }

        //登录
        function login_form(btn) {
            var $btn = $(btn);
            var $form = $btn.parents('form.layui-ajax-form');
            if ($form.length > 0) {
                Wind.use('ajaxForm', function () {
                    $form.ajaxSubmit({
                        url: $btn.data('action') ? $btn.data('action') : $form.attr('action'),
                        dataType: 'json',
                        beforeSubmit: valitate,
                        success: function (data, statusText, xhr, $form) {
                            var text = $btn.text();
                            //按钮文案、状态修改
                            $btn.removeClass('disabled').prop('disabled', false).text(text.replace('中...', '')).parent().find('.tips_error').remove();
                            if (data.state === 'success') {
                                //                            登录成功
                                //                            $('<span class="tips_success">' + data.info + '</span>').appendTo($btn.parent()).fadeIn('slow').delay(1000).fadeOut(function () {
                                //                            });
                            } else if (data.state === 'fail') {
                                // 刷新验证码
                                var $verify_img = $form.find(".verify_img");
                                if ($verify_img.length) {
                                    $verify_img.attr("src", $verify_img.attr("src") + "&refresh=" + Math.random());
                                }

                                var $verify_input = $form.find("[name='verify']");
                                $verify_input.val("");

                                if (data.token) {
                                    var $token_input = $form.find("[name='" + data.token[0] + "']");
                                    $token_input.val(data.token[1]);
                                }
                                $('<label class="tips_error">' + data.info + '</label>').appendTo($(".password")).fadeIn('slow').delay(1000);
                                $btn.removeProp('disabled').removeClass('disabled');
                                setTimeout(function(){
              					  layer.closeAll('loading');
              					}, 200);
                            }

                            if (data.referer) {
                                //返回带跳转地址
                                window.location.href = data.referer;
                            } else {
                                if (data.state === 'success') {
                                    //刷新当前页
                                    reloadPage(window);
                                }
                            }

                        },
                        error: function (xhr, e, statusText) {
                            layer.alert('Orz...发生了一个致命错误，请联系管理员:(', {
                                title: '错误',
                                end: function () {
                                    reloadPage(window);
                                }
                            })
                        },
                        complete: function () {
                            $btn.data("loading", false);
                        }
                    });
                });
                return false;
            }
            return true;
        }
    </script>
</body>
</html>