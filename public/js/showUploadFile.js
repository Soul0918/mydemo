/**
 * 上传图片后显示图片和文件名
 */

     function jsReadFiles(files) {
         if (files.length) {
             var reader = new FileReader();
             for(var i=0; i<files.length; i++){
            	 var ctype = files[i].type;

            		 var imgHtml='';
                	 imgHtml+='<div style="display:initial;float: left;  width: 110px;  padding: 0 15px;">';
                	 imgHtml+='<img class="delete"  style="width:20px;float: right;" src="'+fileImgUrl+'/suffix/del"/></br>';
                	 if(ctype.indexOf('text')>-1){
                		 	imgHtml+='<img height="90" width="90" style="height:90px;padding-left: 10px;" src="'+fileImgUrl+'/suffix/txt">';
                		 	imgHtml+='<span style="width: 110px; word-wrap: break-word; word-break: break-all;display: inline-block;">'+files[i].name+'</span>';
                	 	
                     }
                	 if(ctype.indexOf('image')>-1){
                		 	imgHtml+='<img class="imgtype" height="90" width="90" style="height:90px;padding-left: 10px;" src="'+files[i].url+'">';
                		 	imgHtml+='<span style="width: 110px; word-wrap: break-word; word-break: break-all;display: inline-block;">'+files[i].name+'</span>';
                		 	imgHtml+='<canvas id="logo"  context="2d" style="display:none; border:1px solid #000000;"></canvas>';
                     }
                	 if(ctype.indexOf('pdf')>-1){
                		 	imgHtml+='<img height="90" width="90" style="height:90px;padding-left: 10px;" src="'+fileImgUrl+'/suffix/pdf">';
                		 	imgHtml+='<span style="width: 110px; word-wrap: break-word; word-break: break-all;display: inline-block;">'+files[i].name+'</span>';
                     }
                     if(ctype==""){
                    	 c_name = files[i].name;
                    	 ctype = c_name.split(".")[1];
                    	 if(ctype.indexOf('docx')>-1){
                    		 	imgHtml+='<img height="90" width="90" style="height:90px;padding-left: 10px;" src="'+fileImgUrl+'/suffix/doc">';
                    		 	imgHtml+='<span style="width: 110px; word-wrap: break-word; word-break: break-all;display: inline-block;">'+files[i].name+'</span>';
                         }
                    	 if(ctype.indexOf('xlsx')>-1){
                    		 	imgHtml+='<img height="90" width="90" style="height:90px;padding-left: 10px;" src="'+fileImgUrl+'/suffix/xlsx">';
                    		 	imgHtml+='<span style="width: 110px; word-wrap: break-word; word-break: break-all;display: inline-block;">'+files[i].name+'</span>';
                         }
                         if(ctype.indexOf('chm')>-1){
                    		 	imgHtml+='<img height="90" width="90" style="height:90px;padding-left: 10px;" src="'+fileImgUrl+'/suffix/chm">';
                    		 	imgHtml+='<span style="width: 110px; word-wrap: break-word; word-break: break-all;display: inline-block;">'+files[i].name+'</span>';
                         }
                     }
                     imgHtml+='</div>';
                     $('#imgdiv').append(imgHtml);
                     if(ctype.indexOf('image')>-1){
//                    	 debugger;
                    	 var img =$('.uploadfile :last')[i];
                         drawOnCanvas(img.files[0], 'imgtype', 1);
                         
                     }
                     
            	 
             }
         }
     }