/**
 * 上传图片后显示图片
 */

	function jsReadImgs(files, isdeleteimg) {
         if (files.length) {
        	 
             var reader = new FileReader();
             for(var i=0; i<files.length; i++){
            	 var ctype = files[i].type;
            	 if(ctype.indexOf('image')>-1){
            		 var imgHtml='';
                	 imgHtml+='<div style="display:initial;float: left;  width: 110px;  padding: 0 15px;">';
                	 if(isdeleteimg){
                		 imgHtml+='<img class="delete"  style="width:20px;float: right;" src="'+fileImgUrl+'/suffix/del"/></br>';
                	 }
                	 
                	 imgHtml+='<img class="imgtype" height="90" width="90" style="height:90px;padding-left: 10px;" src="'+files[i].url+'">';
                	 imgHtml+='<span style="width: 110px; word-wrap: break-word; word-break: break-all;display: inline-block;">'+files[i].name+'</span>';
                	 imgHtml+='<canvas id="logo"  context="2d" style="display:none; border:1px solid #000000;"></canvas>';
                     
                     imgHtml+='</div>';
                     $('#imgdiv').append(imgHtml);
                     if(ctype.indexOf('image')>-1){
//                    	 debugger;
                    	 var img =$('.uploadfile :last')[i];
                         drawOnCanvas(img.files[i], 'imgtype', 1);
                         
                     }
            	 } 
             	
             }
         }
     }
	
	$('.delete').off('click');
	$(document).on('click','.delete',function(){ 
		$(this).parent().prev().remove();
		$(this).parent().remove();
	});