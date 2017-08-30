/**
 * 图片压缩处理
 * @param file
 * @param canvasId 画布ID
 * @param isclass 传入的画布ID是否是class
 * @param inputval 隐藏input的id
 */

function drawOnCanvas(file,canvasId, isclass, inputId) {
        var blob = URL.createObjectURL(file);                                               //创建一个URL
        
        var reader = new FileReader();
        reader.onload = function (blob) {
                     var dataURL = blob.target.result,
                    canvas = document.querySelector('canvas'),
                    ctx = canvas.getContext('2d'),
                    img = new Image();

            img.onload = function() {
                var square = 320;                                                           //定义所需尺寸
                canvas.width = square;                                                         //画布大小
                canvas.height = square;
                var context = canvas.getContext('2d');
                context.clearRect(0, 0, square, square);                                     //清空画布
                var imageWidth;
                var imageHeight;
                var offsetX = 0;
                var offsetY = 0;


                if (this.width > this.height){
                    imageHeight = Math.round(this.height/(this.width/square));
                    imageWidth = square;
                }else if(this.height>this.width)
                {
                	imageWidth = Math.round(this.width/(this.height/square));
                    imageHeight = square;
                }else if(this.height=this.width&&(this.width>square||this.height>square)){
                    imageWidth = square;
                    imageHeight = square;

                }

                canvas.width = imageWidth;                                                         //画布大小
                canvas.height = imageHeight;
                context.drawImage(this, offsetX, offsetY, imageWidth, imageHeight);  //画图
                
                var base64 = canvas.toDataURL('image/jpeg',0.5);                        //生成base64码
                if(isclass == 1){
                	var classname="."+canvasId;
                	$(classname+" :last").attr("src",base64);
                }else{
                	$("#"+canvasId).attr("src",base64);
                	if(inputId){
                		$("#"+inputId).attr("value",base64);
                	}
                }
                
//                $("#"+canvasId).attr("width",imageWidth);
//                $("#"+canvasId).attr("height",imageHeight);
                return base64;
            };
            img.src = dataURL;
        };
       var a = reader.readAsDataURL(file);
    }