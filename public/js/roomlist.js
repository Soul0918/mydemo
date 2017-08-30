/**
 * roomlist页面js
 */
var _open = '';//open赋值
var _adddiv = '';//操作div id
function room_return_data(id, name) {
	
	try {
		//检测是否已经存在该用户
		var _allroom = $('.roomclass').find('input').prevObject;
		
		var _room_ids=new Array()
		for(var i=0; i<_allroom.length; i++){
			_room_ids[i] = parseInt(_allroom[i].value);
		}
		if($.inArray(id,_room_ids)>-1){
			layer.msg('该房间已经选择');
		}else{
			layer.close(_open);
			$('#'+_adddiv).val(name);
			$('#'+_adddiv).next().val(id);
		}
	} catch (err) {
		layer.close(_open);
		$('#'+_adddiv).val("请重新选择");
	}
	$('#'+_adddiv).removeAttr("id");
}