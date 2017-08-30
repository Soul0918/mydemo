/**
 * 表单ajax提交
 */

	$('#btn').click(function(){
		 $.ajax({
	            type:"POST",
	            url:link_url,
	            data:$("#form").serialize(),
	            datatype: "html",
	            success:function(data){
	            	//alert(data.info);
	            	if(data.referer){
	            		window.location.href = data.referer;
	            	}else{
	            		//location.reload();
	            	}
	            	
	            },
	            error: function(){
	            }         
	         });
	});
	
	$('#submit').click(function(){
		$("#detail_form form").attr("action",link_url);
		 $("#detail_form form").submit();
//		 $("#detail_form form").attr("id","form");
//		 $.ajax({
//	            type:"POST",
//	            url:link_url,
//	            data:$("#form").serialize(),
//	            datatype: "html",
//	            success:function(data){
//	            	alert(data.info);
//	            	if(data.referer){
//	            		window.location.href = data.referer;
//	            	}else{
//	            		location.reload();
//	            	}
//	            	
//	            },
//	            error: function(){
//	            }         
//	         });
	});

	 function getFormJson(form) {
         var o = {};
         var a = $(form).serializeArray();
         $.each(a, function () {
             if (o[this.name] !== undefined) {
                 if (!o[this.name].push) {
                     o[this.name] = [o[this.name]];
                 }
                 o[this.name].push(this.value || '');
             } else {
                 o[this.name] = this.value || '';
             }
         });
         return o;
     }
     
     layui.use('form', function(){
    	 var form = layui.form();
    	//监听提交
         form.on('submit(*)', function (data) {
         	var fromdata = getFormJson('.layui-form');
         	/* layer.alert(JSON.stringify(fromdata)); */
             postData({
                 url: link_url,
                 params: fromdata,
                 success: function (result) {
                     if (result.status == 1) {
                         $('#tabList').bootstrapTable('refresh');

                         layer.msg('编辑成功', {
                             time: 2000
                         }, function () {
                             closeDetail();
                         });
                     }
                    /*else {
                         layer.alert(result.info);
                     }*/
                 }
             });
             return false;
         });
     	  
     	});