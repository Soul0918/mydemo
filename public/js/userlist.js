/**
 * userlist页面js
 */
var _open = '';//open赋值
var _adddiv = '';//操作div id
function user_return_data(id, name) {
	
	try {
		//检测是否已经存在该用户
		var _alluser = $('.userclass').find('input').prevObject;

		var _user_ids=new Array()
		for(var i=0; i<_alluser.length; i++){
			_user_ids[i] = parseInt(_alluser[i].value);
		}
		if($.inArray(id,_user_ids)>-1){
			layer.msg('该用户已经选择');
		}else{
			layer.close(_open);
			$('#'+_adddiv).val('');
			$('#'+_adddiv).val(name);
			$('#'+_adddiv).next().val(id);
		}
	} catch (err) {
		layer.close(_open);
		$('#'+_adddiv).val("请重新选择");
	}
	$('#'+_adddiv).removeAttr("id");
}