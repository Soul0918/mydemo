/**
 * 选择小区（可多选）
 */
var _open = '';//open赋值
var _community_name = '';
var _community_id ='';
function retuan_communities(community_ids,community_names){
	try {
		layer.close(_open);
//		$('input[name=community]').empty();
		if(_community_name){
			$('input[name=community]').val(_community_name+','+community_names);
			$('input[name=community_ids]').val(_community_id+','+community_ids);
		}else{
			$('input[name=community]').val(community_names);
			$('input[name=community_ids]').val(community_ids);
		}
		
	} catch (err) {
		
	}
	
}

function exit(){
	layer.close(_open);
}