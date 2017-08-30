<?php
namespace Common\Model;
use Common\Model\CommonModel;
use Common\Model\Extra\Commonable;
use Think\Model\RelationModel;
use Think\Page;

class UsersModel extends RelationModel
{
    use Commonable;
	
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
// 		array('user_login', 'require', '用户名称不能为空！', 0, 'regex', CommonModel:: MODEL_INSERT  ),
		array('mobile', 'require', '手机不能为空！', 1, 'regex', CommonModel:: MODEL_INSERT  ),
		array('user_pass', 'require', '密码不能为空！', 1, 'regex', CommonModel:: MODEL_INSERT ),
		array('user_login', 'require', '用户名称不能为空！', 0, 'regex', CommonModel:: MODEL_UPDATE  ),
		array('user_pass', 'require', '密码不能为空！', 0, 'regex', CommonModel:: MODEL_UPDATE  ),
		array('user_login','','用户名已经存在！',0,'unique',CommonModel:: MODEL_BOTH ), // 验证user_login字段是否唯一
	    array('mobile','','手机号已经存在！',0,'unique',CommonModel:: MODEL_BOTH ), // 验证mobile字段是否唯一
		//array('user_email','require','邮箱不能为空！',0,'regex',CommonModel:: MODEL_BOTH ), // 验证user_email字段是否唯一
		array('user_email','unique','邮箱帐号已经存在！',2,'unique',CommonModel:: MODEL_INSERT ), // 验证user_email字段是否唯一
		array('user_email','email','邮箱格式不正确！',2,'',CommonModel:: MODEL_BOTH ), // 验证user_email字段格式是否正确
	);
	
	protected $_auto = array(
	    //array('create_time','mGetDate',CommonModel:: MODEL_INSERT,'callback'),
		array('create_time','time',1,'function'),
		array('update_time','time',3,'function'),
		array('create_user_id','confirmUserID',1,'callback'),
		array('update_user_id','confirmUserID',3,'callback'),
	    array('birthday','',CommonModel::MODEL_UPDATE,'ignore')
	);
	
    protected $_link = array(
        'Companys' => self::HAS_ONE
    );
	
	//用于获取时间，格式为2012-02-03 12:12:12,注意,方法不能为private
	function mGetDate() {
		return date('Y-m-d H:i:s');
	}
	
	//当前台user_id存在时保存，不然保存后台admin_id
	function confirmUserID(){
		$user_id = sp_get_current_userid()?sp_get_current_userid():sp_get_current_admin_id();
		return $user_id;
	}
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
		
		if(!empty($data['user_pass']) && strlen($data['user_pass'])<25){
			$data['user_pass']=sp_password($data['user_pass']);
		}
	}

    public function user_select($param)
    {
        //user_login,user_nickname,user_email
        $group_or['user_login'] = array('like', '%'.$param.'%');
        $group_or['user_nicename'] = array('like', '%'.$param.'%');
        $group_or['user_email'] = array('like', '%'.$param.'%');
        $group_or['_logic'] = 'OR';
        $map['_complex'] = $group_or;
        $map['user_status'] = array('egt', 1);

        $total_count = $this->where($map)->count();
        $page = new Page($total_count);
        $result = $this->where($map)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->field('id,user_login,user_nicename,user_email,user_url,avatar,sex,birthday,mobile,signature')->select();

        return compact('total_count', 'result');
	}
	
	public function user_list($where_param, $limit_param=NULL, $order_param=NULL){
		if(is_array($where_param)){
            $total_count = $this->getQuery($where_param)->count();
            $query = $this->getQuery($where_param);
            if (isset($limit_param['limit']) && isset($limit_param['offset'])) {
                $query->limit($limit_param['offset'].','.$limit_param['limit']);
            }
            $result = $query->order($order_param)->select();
            return compact('total_count', 'result');
		}
	}

    public function getQuery($where_param)
    {
    	$company_id = $where_param['company_id']?$where_param['company_id']:0;
    	$community_id = $where_param['community_id']?$where_param['community_id']:0;
    	$query = $this->alias("u");
//     		->join('__ROLE_USER__ g on u.id = g.user_id')
//     		->join('__ROLE__ h on h.id = g.role_id and status>0')
//     		->group('u.id');
    	if($where_param['login_type']){
    		$query = $query->field("u.*");
    		if($where_param['login_type'] == 1){
    			$query = $query->where("u.id in (select distinct ru.user_id from hc_role r inner join hc_role_user ru on r.id = ru.role_id where r.status = ".$where_param['status']." AND r.company_id=".$company_id." AND r.type IN (".$where_param['type']."))");
    		}elseif ($where_param['login_type'] == 2){
    			$query = $query->where("u.id in (select distinct ru.user_id from hc_role r inner join hc_role_user ru on r.id = ru.role_id where r.status = ".$where_param['status']." AND r.community_id=".$community_id." AND r.type IN (".$where_param['type']."))");
    		}
    	}else {
    		if($where_param['genre'] == 1){
    			$query = $query
    			->field("u.*,exists(select 1 from hc_companys where user_id=u.id) as role_type")
    			->where("u.id in (select distinct ru.user_id from hc_role r inner join hc_role_user ru on r.id = ru.role_id where r.status = ".$where_param['status']." AND r.type IN (".$where_param['type'].") AND ru.company_id>0 and ru.community_id=0)");
    		}elseif ($where_param['genre'] == 2){
    			$query = $query
    			->field("u.*,exists(select 1 from hc_communities where user_id=u.id) as role_type")
    			->where("u.id in (select distinct ru.user_id from hc_role r inner join hc_role_user ru on r.id = ru.role_id where r.status = ".$where_param['status']." AND r.type IN (".$where_param['type'].") AND ru.company_id>0 and ru.community_id>0)");
    		}else {
    			$query = $query->where("u.id in (select distinct ru.user_id from hc_role r inner join hc_role_user ru on r.id = ru.role_id where r.status = ".$where_param['status']." AND r.type IN (".$where_param['type']."))");
    		}
    		
    	}
        
        $tmp = [];
        foreach ($where_param as $key => $value) {
            switch ($key) {
                case 'user_login':
                case 'user_email':
                    $tmp['u.'.$key] = $value;
                    break;
                
            }
        }
        $query->where($tmp);
        return $query;
	}

    public function user_role($user_id)
    {
        $roles = M('Role')->alias('a')
            ->join('__ROLE_USER__ b on a.id = b.role_id')
            ->join('__USERS__ c on b.user_id = c.id')
            ->where(['c.id' => $user_id])
            ->field('a.*')->select();

        $tmp = [];
        foreach ($roles as $key => $role) {
            $tmp[] = $role['id'];
        }

        return $tmp;
	}

    public function user_company($user_id)
    {
        $roles = M('Role')->alias('a')
            ->join('__ROLE_USER__ b on a.id = b.role_id')
            ->where(['b.user_id' => $user_id])
            ->field('a.*')->select();

        $tmp = [];
        foreach ($roles as $key => $role) {
            if ($role['company_id'] == 0) continue;
            $company_id = $role['compay_id'];
            $tmp[] = D('Companys')->getById($company_id);
        }
        return $tmp;
	}

    public function user_community($user_id)
    {
        $roles = M('Role')->alias('a')
            ->join('__ROLE_USER__ b on a.id = b.role_id')
            ->where(['b.user_id' => $user_id])
            ->field('a.*')->select();
        $tmp = [];
        foreach ($roles as $key => $role) {
            if ($role['community_id'] == 0) continue;
            $community_id = $role['community_id'];
            $tmp[] = D('Communities')->where(['community_id'=>$community_id])->find();
        }
        return $tmp;
	}
	
	public function get_user_nicename($user_id){
		if((int)$user_id>0){
			$data=$this->where('id='.$user_id)->find();
			if(!empty($data)){
				return $data['user_nicename'];
			}
		}
		return '';
	}

    public function history($source)
    {
        
	}
}

