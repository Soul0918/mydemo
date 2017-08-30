<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- HTML5 shim for IE8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<![endif]-->

	<link href="/demo-master/public/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet">
	<link href="/demo-master/public/simpleboot/css/simplebootadmin.css" rel="stylesheet">
	<link href="/demo-master/public/js/artDialog/skins/default.css" rel="stylesheet" />
	<link href="/demo-master/public/simpleboot/font-awesome/4.4.0/css/font-awesome.min.css"  rel="stylesheet" type="text/css">
	<link href="/demo-master/public/simpleboot/layui/css/layui.css" rel="stylesheet">
	<script src="/demo-master/public/simpleboot/layui/layui.js"></script>
	<script src="/demo-master/public/simpleboot/jquery1.42.min.js"></script>
	<!--layui组件引用-->
	<script>
		layui.use(['layer', 'form', 'element'], function(){
			var layer = layui.layer
					,form = layui.form()
					,element = layui.element()



		});
	</script>
	<script>
		layui.use(['layer', 'form', 'element'], function(){
			var layer = layui.layer

			laydate = layui.laydate()


		});
	</script>

	<script>
		layui.use('laydate', function(){
			var laydate = layui.laydate;

			var start = {
				min: laydate.now()
				,max: '2099-06-16 23:59:59'
				,istoday: false
				,choose: function(datas){
					end.min = datas; //开始日选好后，重置结束日的最小日期
					end.start = datas //将结束日的初始值设定为开始日
				}
			};

			var end = {
				min: laydate.now()
				,max: '2099-06-16 23:59:59'
				,istoday: false
				,choose: function(datas){
					start.max = datas; //结束日选好后，重置开始日的最大日期
				}
			};



		});
	</script>
	<!--layui组件引用-->
	<style>
		.form .input-order{margin-bottom: 0px;padding:3px;width:40px;}
		.table-actions{margin-top: 5px; margin-bottom: 5px;padding:0px;}
		.table-list{margin-bottom: 0px;}
		.layui-input{height: 38px!important;line-height: 38px!important;}

	</style>
	<!--[if IE 7]>
	<link rel="stylesheet" href="/demo-master/public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css">
	<![endif]-->
	<script type="text/javascript">
		//全局变量
		var GV = {
			ROOT: "/demo-master/",
			WEB_ROOT: "/demo-master/",
			JS_ROOT: "public/js/",
			APP:'<?php echo (MODULE_NAME); ?>',/*当前应用名*/
			PUBLIC: "/demo-master/public"
		};
	</script>
	<script src="/demo-master/public/js/jquery.js"></script>
	<script src="/demo-master/public/js/wind.js"></script>
	<script src="/demo-master/public/simpleboot/bootstrap/js/bootstrap.min.js"></script>
	<script>
		$(function(){
			$("[data-toggle='tooltip']").tooltip();
		});
	</script>
	<?php if(APP_DEBUG): ?><style>
			#think_page_trace_open{
				z-index:9999;
			}
		</style><?php endif; ?>
<style>
li {
	list-style: none;
}
</style>
</head>
<body>
	<div class="wrap">
		<div id="error_tips">
			<h2><?php echo ($msgTitle); ?></h2>
			<div class="error_cont">
				<ul>
					<li><?php echo ($error); ?></li>
				</ul>
				<div class="error_return">
					<a href="<?php echo ($jumpUrl); ?>" class="btn">返回</a>
				</div>
			</div>
		</div>
	</div>
	<script src="/demo-master/public/js/common.js"></script>
	<script>
		setTimeout(function() {
			location.href = '<?php echo ($jumpUrl); ?>';
		}, 3000);
	</script>
</body>
</html>