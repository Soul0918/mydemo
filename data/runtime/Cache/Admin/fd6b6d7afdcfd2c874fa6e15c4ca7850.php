<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel='icon' href='/public/images/favicon.ico' type=‘image/x-ico’/>
    <title>管理员登陆</title>
    <style>
        html, body {
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "Microsoft YaHei", "Helvetica Neue", Helvetica, "PingFang SC", 微软雅黑, Tahoma, Arial, sans-serif;
            background: #fff;
            overflow: hidden;
        }

        .login ul li:nth-child(0) {
            list-style-type: none;
        }

        input, button {
            font-family: "Microsoft YaHei", "Helvetica Neue", Helvetica, "PingFang SC", 微软雅黑, Tahoma, Arial, sans-serif;
        }

        .login li input {
            display: block;
            border: 1px solid #ffffff;
            margin: 14px auto;
            width: 290px;
            height: 34px;
            border-radius: 4px;
            text-indent: 5px;
            outline: none;
        }

        .login li {
            list-style-type: none;
        }

        .login-logo img {
            margin: 0 auto;
        }

        .login {
            margin: 120px auto;
            width: 335px;
            background-color: #03A9F4;
            border-radius: 4px;
            box-shadow: 3px 3px 12px #d4d4d4;
            height: 514px;
        }

        .tips_error {
            display: block;
            margin-bottom: 1.5rem;
        }

        .tips_success {
            display: block;
            margin-bottom: 1.5rem;
        }

        .login-background {
            background: radial-gradient(#20a2e2, #1c8bd9);
            background: -webkit-radial-gradient(#20a2e2, #1c8bd9);
            background: -o-radial-gradient(#20a2e2, #1c8bd9);
            background: -moz-radial-gradient(#20a2e2, #1c8bd9);
            position: relative;
            border-radius: 5px;

        }

        #login_btn_wraper button {
            width: 295px;
            background: #0097dc;
            color: #ffffff;
            border: 1px solid #fff;
            border-radius: 4px;
            height: 40px;
            cursor: pointer;
            margin: 0 auto;
            display: block;
            background: transparent;
            -webkit-transition: background-color 0.6s;
            -moz-transition: background-color 0.6s;
            -ms-transition: background-color 0.6s;
            -o-transition: background-color 0.6s;
            transition: background-color 0.6s;
        }

        #login_btn_wraper button:hover {
            background-color: #0eb4ff;
        }

        #canvas {
            display: inline-block;
            overflow: hidden;
            position: absolute;
            top: 0;
            z-index: -100;
        }

        .login-form > form > ul {
            padding: 0;
            margin: 0;
        }

        .login-title {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100px;
            s color: #fff;
            border-bottom: 1px solid #fff;
            padding: 0 10px 0;
        }

        .company-name {
            background-image: url(/public/images/hc_logo_w.png);
            background-repeat: no-repeat;
            background-size: 27px;
            background-position: 10px 50%;
            padding-left: 45px;
            line-height: 45px;
            height: 45px;
            color: #fff;
            font-size: 18px;
        }

        #verify-input {
            padding: 0 20px 0;
            position: relative;
            padding: 0 20px 0;
        }

        .login-form {
            margin: 30px 0 20px;
        }

        .verify_img {
            cursor: pointer;
            position: absolute;
            right: 24px;
            top: 2px;
            border-left: 2px solid #d8d8d8;
            height: 33px;
        }

        .other {
            color: #fff;
            font-size: 14px;
            margin: 0 auto;
            display: block;
            width: 298px;
            text-align: center;
        }

        .other > span {
            display: block;
            width: 100%;
            text-align: center;
            margin: 10px auto 20px;
        }

        .btn-reg {
            color: #ffffff;
            text-decoration: underline;
        }

        .forgot_password {
            text-decoration: none;
            display: block;
            width: 100%;
            padding: 0 22px 0;
            color: #fff;
        }

        .forgot_password > a {
            color: #fff;
            font-size: 14px;
        }

        #login_btn_wraper {
            margin: 31px auto 50px;
        }

        .other_line {
            border: none;
            border-top: 1px solid #fff;
        }
    </style>
</head>
<body>
<div class="login">
    <section class="login-title">
        <span class="company-name">广东华城信息科技有限公司</span>
    </section>
    <section class="login-form">
        <form method="post" name="login" action="<?php echo U('public/dologin');?>" autoComplete="off" class="js-ajax-form">
            <ul>
                <li>
                    <input class="input" id="js-admin-name" name="username" type="text"
                           placeholder="<?php echo L('USERNAME_OR_EMAIL');?>" title="<?php echo L('USERNAME_OR_EMAIL');?>"
                           value="<?php echo cookie('admin_username');?>" data-rule-required="true" data-msg-required=""/>
                </li>
                <li>
                    <input class="input" id="admin_pwd" type="password" name="password" placeholder="<?php echo L('PASSWORD');?>"
                           title="<?php echo L('PASSWORD');?>" data-rule-required="true" data-msg-required=""/>
                </li>
                <li class="verifycode-wrapper">
                </li>
                <li id="verify-input">
                    <input type="text" name="verify" placeholder="<?php echo L('ENTER_VERIFY_CODE');?>"/>
                    <?php echo sp_verifycode_img('length=1&font_size=14&width=100&height=30&use_noise=1&use_curve=0','style="cursor:
                    pointer;" title="点击获取"');?>
                </li>
                <li>
                    <span class="forgot_password"><a href="#">忘记密码？</a></span>
                </li>
            </ul>
            <div id="login_btn_wraper">
                <button type="submit" name="submit" class="btn js-ajax-submit" data-loadingmsg="<?php echo L('LOADING');?>">
                    <?php echo L('LOGIN');?>
                </button>
            </div>
        </form>
    </section>
    <section class="other">
        <fieldset class="other_line">
            <legend align="center">or</legend>
        </fieldset>
        <a title="注册" class="btn-reg">注册</a>
    </section>
</div>
<canvas id="canvas"></canvas>
<script>
    //定义画布宽高和生成点的个数
    var WIDTH = window.innerWidth, HEIGHT = window.innerHeight, POINT = 30;

    var canvas = document.getElementById('canvas');
    canvas.width = WIDTH,
        canvas.height = HEIGHT;
    var context = canvas.getContext('2d');
    context.strokeStyle = 'rgba(3,168,225,0.2)',
        context.strokeWidth = 0.5,
        context.fillStyle = 'rgba(3,169,244,1)';
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

<script>
    var GV = {
        ROOT: "/demo-master/",
        WEB_ROOT: "/demo-master/",
        JS_ROOT: "public/js/"
    };
</script>
<script src="/demo-master/public/js/wind.js"></script>
<script src="/demo-master/public/js/jquery.js"></script>
<script type="text/javascript" src="/demo-master/public/js/common.js"></script>
<script>
    (function () {
        document.getElementById('js-admin-name').focus();
    })();
</script>
</body>
</html>