<?php
/**
 * wx互联登录
*/

class connectwxControl extends mobileHomeControl{
	public function __construct(){
		parent::__construct();
		//Language::read("home_login_register,home_login_index,home_qqconnect");
		/**
		 * 判断qq互联功能是否开启
		 */

		/**
		 * 初始化测试数据
		 */

		 if (!$_SESSION['openid']){
			output_error('登陆失败01');
		 exit;
			//showMessage('获取参数错误','index.php','html','error');//'系统错误'
		}
		if (!$_SESSION['unionid']){
			output_error('登陆失败02');
			exit;
			//showMessage('获取参数错误','index.php','html','error');//'系统错误'
		}
		/*Tpl::output('hidden_nctoolbar', 1); */
	}
	/**
	 * 首页
	 */
	public function indexOp(){
		/**
		 * 检查登录状态
		 */
		/* if($_SESSION['is_login'] == '1') {
			//wx绑定
			$this->bindwxOp();
		}else*/
		/*
		{
			$this->autologin();
			$this->registerOp();
		}*/

		// old function
		//$this->autologin();
		//$this->registerOp();
		// e fun


		$this->bindorlogin();
	}
	/**
	 * wx绑定新用户
	 */
	public function registerOp(){
		//实例化模型
		$model_member	= Model('member');
		if (chksubmit()){
			/* $update_info	= array();
			$update_info['member_passwd']= md5(trim($_POST["password"]));
			if(!empty($_POST["email"])) {
				$update_info['member_email']= $_POST["email"];
				$_SESSION['member_email']= $_POST["email"];
			}
			$model_member->editMember(array('member_id'=>$_SESSION['member_id']),$update_info);
			showMessage(Language::get('nc_common_save_succ'),SHOP_SITE_URL); */
		}else {
			//检查登录状态
			//$model_member->checkloginMember();
			//获取wx账号信息
			require_once (dirname(__FILE__).'/'.'../api/wxlogin/comm/get_user_info.php');
			$qquser_info = get_user_info($_SESSION["appid"], $_SESSION["appkey"], $_SESSION["token"], $_SESSION["secret"], $_SESSION["openid"]);
			//Tpl::output('wxuser_info',$qquser_info);

			//处理wx账号信息
			$qquser_info['nickname'] = trim($qquser_info['nickname']);
			$user_passwd = 'wx'.rand(100000, 999999);
			/**
			 * 会员添加
			 */
			$user_array	= array();
			$user_array['member_name']		= $qquser_info['nickname'];
			$user_array['member_passwd']	= $user_passwd;
			$user_array['member_email']		= '';
			$user_array['member_wxopenid']	= $qquser_info['openid'];//wx openid
			$user_array['weixin_info']	= serialize($qquser_info);//wx 信息
			$user_array['weixin_unionid']	= $qquser_info['unionid'];
			$rand = rand(100, 899);
			if(strlen($user_array['member_name']) < 3) {$user_array['member_name']		= $qquser_info['nickname'].$rand;}
			$check_member_name	= $model_member->getMemberInfo(array('member_name'=>trim($user_array['member_name'])));
			$result	= 0;
			if(empty($check_member_name)) {
				$result	= $model_member->addMember($user_array);
			}else {
				$user_array['member_name'] = trim($qquser_info['nickname']).$rand;
				$check_member_name	= $model_member->getMemberInfo(array('member_name'=>trim($user_array['member_name'])));
				if(empty($check_member_name)) {
					$result	= $model_member->addMember($user_array);
				}else {
					for ($i	= 1;$i < 999999;$i++) {
						$rand = $rand+$i;
						$user_array['member_name'] = trim($qquser_info['nickname']).$rand;
						$check_member_name	= $model_member->getMemberInfo(array('member_name'=>trim($user_array['member_name'])));
						if(empty($check_member_name)) {
							$result	= $model_member->addMember($user_array);
							break;
						}
					}
				}
			}
			if($result) {
				//Tpl::output('user_passwd',$user_passwd);
				$avatar	= @copy($qquser_info['headimgurl'],BASE_UPLOAD_PATH.'/'.ATTACH_AVATAR."/avatar_$result.jpg");
				$update_info	= array();
				if($avatar) {
				    $update_info['member_avatar'] 	= "avatar_$result.jpg";
    				$model_member->editMember(array('member_id'=>$result),$update_info);
    				$user_array['member_avatar'] 	= "avatar_$result.jpg";
				}
				$user_array['member_id']		= $result;
				//$model_member->createSession($user_array);
				$token = $this->_get_token($user_array['member_id'], $user_array['member_name'], 'wap');
				if($token) {
					output_data(array('username' => $member_info['member_name'], 'key' => $token, 'reg' => true ));//cary 注册标识
				} else {
					output_error('登陆失败!');
				}
				//Tpl::showpage('connectwx_register');
			} else {
				output_error('登陆失败1');
				//showMessage(Language::get('login_usersave_regist_fail'),SHOP_SITE_URL.'/index.php?act=login&op=register','html','error');//"会员注册失败"
			}
		}
	}
	/**
	 * 已有用户绑定
	 */
/* 	public function bindwxOp(){
		$model_member	= Model('member');
		//验证账号用户是否已经存在
		$array	= array();
		$array['weixin_unionid']	= $_SESSION['unionid'];
		$member_info = $model_member->getMemberInfo($array);
		if (is_array($member_info) && count($member_info)>0){
			unset($_SESSION['openid']);
			unset($_SESSION['unionid']);			
			$token = $this->_get_token($member_info['member_id'], $member_info['member_name'], 'wap');
			@header('location:/wap/tmpl/member/member.html?act=member');
			//showMessage('该账号已绑定','','html','error');//'该wx账号已经绑定其他商城账号,请使用其他wx账号与本账号绑定'
		}
		//获取wx账号信息
		require_once (dirname(__FILE__).'/'.'../api/weixin/comm/get_user_info.php');
		$qquser_info = get_user_info($_SESSION["appid"], $_SESSION["appkey"], $_SESSION["token"], $_SESSION["secret"], $_SESSION["openid"]);
		$edit_state		= $model_member->editMember(array('member_id'=>$_SESSION['member_id']), array('weixin_unionid'=>$_SESSION['unionid'],'member_wxopenid'=>$_SESSION['openid'], 'member_wxinfo'=>serialize($qquser_info)));
		if ($edit_state){
			
			$token = $this->_get_token($member_info['member_id'], $member_info['member_name'], 'wap');
			@header('location:/wap/tmpl/member/member.html?act=member');
			//showMessage('绑定微信账号成功!','index.php?act=member_connect');
		}else {
			@header('location:/wap/index.html');
			//showMessage('绑定微信账号失败','index.php?act=member_connect','html','error');//'绑定wx失败'
		}
	} */
	/**
	 * 绑定wx后自动登录
	 */
	public function autologin(){
		//查询是否已经绑定该wx,已经绑定则直接跳转
		$model_member	= Model('member');
		$array	= array();
		$array['weixin_unionid']	= $_SESSION['unionid'];
		$member_info = $model_member->getMemberInfo($array);
		if (is_array($member_info) && count($member_info)>0){
			if(!$member_info['member_state']){//1为启用 0 为禁用
				//showMessage(Language::get('nc_notallowed_login'),'','html','error');
				output_error('登陆失败3!');
			}
			//$model_member->createSession($member_info);
			$token = $this->_get_token($member_info['member_id'], $member_info['member_name'], 'wap');
			if($token) {
					output_data(array('username' => $member_info['member_name'], 'key' => $token, 'reg' => false));//cary 增加新注册状态
					exit;
				} else {
					output_error('登陆失败2!');
				}
			//$success_message = Language::get('login_index_login_success');
			//showMessage($success_message,SHOP_SITE_URL);
		}
	}
	
    /**
     * 登录生成token
     */
    private function _get_token($member_id, $member_name, $client) {
        $model_mb_user_token = Model('mb_user_token');

        //重新登录后以前的令牌失效
        //暂时停用
        //$condition = array();
        //$condition['member_id'] = $member_id;
        //$condition['client_type'] = $_POST['client'];
        //$model_mb_user_token->delMbUserToken($condition);

        //生成新的token
        $mb_user_token_info = array();
        $token = md5($member_name . strval(TIMESTAMP) . strval(rand(0,999999)));
        $mb_user_token_info['member_id'] = $member_id;
        $mb_user_token_info['member_name'] = $member_name;
        $mb_user_token_info['token'] = $token;
        $mb_user_token_info['login_time'] = TIMESTAMP;
        $mb_user_token_info['client_type'] = 'wap';

        $result = $model_mb_user_token->addMbUserToken($mb_user_token_info);

        if($result) {
            return $token;
        } else {
            return null;
        }

    }


	private function bindorlogin(){
		//查询是否已经绑定该wx,已经绑定则直接跳转
		$model_member	= Model('member');
		$array	= array();
		$array['weixin_unionid']	= $_SESSION['unionid'];
		$member_info = $model_member->getMemberInfo($array);
$_SESSION['step'] .= '1,'.time().';';
		if (is_array($member_info) && count($member_info)>0){
$_SESSION['step'] .= '2,'.time().';';
			if(!$member_info['member_state']){//1为启用 0 为禁用
				//showMessage(Language::get('nc_notallowed_login'),'','html','error');
				output_error('登陆失败3!');
			}
			//$model_member->createSession($member_info);
			$token = $this->_get_token($member_info['member_id'], $member_info['member_name'], 'wap');
			if($token) {
$_SESSION['step'] .= '9,'.time().';';
$_SESSION['member_id'] = $member_info['member_id'];   // add cary ,buy_logic需要用到session
				output_data(array('username' => $member_info['member_name'], 'key' => $token, 'reg' => $_SESSION['is_reg'],'step'=> $_SESSION['step']));//cary 增加新注册状态
				exit;
			} else {
				output_error('登陆失败2!');
			}
			//$success_message = Language::get('login_index_login_success');
			//showMessage($success_message,SHOP_SITE_URL);
		}

		///////////////////////////////新用户
$_SESSION['step'] .= '3,'.time().';';
		//检查登录状态
		//$model_member->checkloginMember();
		//获取wx账号信息
		require_once (dirname(__FILE__).'/'.'../api/wxlogin/comm/get_user_info.php');
		$qquser_info = get_user_info($_SESSION["appid"], $_SESSION["appkey"], $_SESSION["token"], $_SESSION["secret"], $_SESSION["openid"]);
		//Tpl::output('wxuser_info',$qquser_info);
		//处理wx账号信息
		$qquser_info['nickname'] = trim($qquser_info['nickname']);
		$user_passwd = 'wx'.rand(100000, 999999);
		/**
		 * 会员添加
		 */
		$user_array	= array();
		$user_array['member_name']		= $qquser_info['nickname'];
		$user_array['member_passwd']	= $user_passwd;
		$user_array['member_email']		= '';
		$user_array['member_wxopenid']	= $qquser_info['openid'];//wx openid
		$user_array['weixin_info']	= serialize($qquser_info);//wx 信息
		$user_array['weixin_unionid']	= $qquser_info['unionid'];
		$rand = rand(100, 899);
		if(strlen($user_array['member_name']) < 3) {$user_array['member_name']		= $qquser_info['nickname'].$rand;}
		$check_member_name	= $model_member->getMemberInfo(array('member_name'=>trim($user_array['member_name'])));
		$result	= 0;
$_SESSION['step'] .= '4,'.time().';';
		if(empty($check_member_name)) {
$_SESSION['is_reg'] = true ;
			$result	= $model_member->addMember($user_array);
		}else {
$_SESSION['step'] .= '5,'.time().';';
			$user_array['member_name'] = trim($qquser_info['nickname']).$rand;
			$check_member_name	= $model_member->getMemberInfo(array('member_name'=>trim($user_array['member_name'])));
			if(empty($check_member_name)) {
				$result	= $model_member->addMember($user_array);
			}else {
				for ($i	= 1;$i < 999999;$i++) {
					$rand = $rand+$i;
					$user_array['member_name'] = trim($qquser_info['nickname']).$rand;
					$check_member_name	= $model_member->getMemberInfo(array('member_name'=>trim($user_array['member_name'])));
					if(empty($check_member_name)) {
						$result	= $model_member->addMember($user_array);
						break;
					}
				}
			}
		}
		if($result) {
$_SESSION['step'] .= '6,'.time().' img:'.$qquser_info['headimgurl'].';';
			//Tpl::output('user_passwd',$user_passwd);
			$thumb = $qquser_info['headimgurl'];
			$thumb = substr($thumb,0,-1).'64';
			$avatar	= @copy($thumb,BASE_UPLOAD_PATH.'/'.ATTACH_AVATAR."/avatar_$result.jpg");
			$update_info	= array();
			if($avatar) {
				$update_info['member_avatar'] 	= "avatar_$result.jpg";
				$model_member->editMember(array('member_id'=>$result),$update_info);
				$user_array['member_avatar'] 	= "avatar_$result.jpg";
			}
$_SESSION['step'] .= '7,'.time().';';
			$user_array['member_id']		= $result;
			//$model_member->createSession($user_array);
			$token = $this->_get_token($user_array['member_id'], $user_array['member_name'], 'wap');
			if($token) {
$_SESSION['step'] .= '8,'.time().';';
$_SESSION['member_id'] = $user_array['member_id'];   // add cary ,buy_logic需要用到session
				output_data(array('username' => $member_info['member_name'], 'key' => $token, 'reg' => $_SESSION['is_reg'],'step'=> $_SESSION['step']));//cary 注册标识

			} else {
				output_error('登陆失败!');
			}
			//Tpl::showpage('connectwx_register');
		} else {
			output_error('登陆失败1');
			//showMessage(Language::get('login_usersave_regist_fail'),SHOP_SITE_URL.'/index.php?act=login&op=register','html','error');//"会员注册失败"
		}



	}
}
