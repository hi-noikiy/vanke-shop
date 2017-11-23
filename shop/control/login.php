<?php
/**
 * 前台登录 退出操作
 *
 *
 *
 ***/




class loginControl extends BaseHomeControl {

	public function __construct(){
		parent::__construct();
		Tpl::output('hidden_nctoolbar', 1);
		Tpl::setLayout('login_layout');
	}

	/**
	 * 登录操作
	 *
	 */
	public function indexOp(){
		Language::read("home_login_index,home_login_register");
		$lang	= Language::getLangContent();
		$model_member	= Model('member');
        if($this->is_https()){
            $item_url = 'https://mall.vankeservice.com/';
            $idm_url = 'https://siam.vankeservice.com/';
        }else{
            $item_url = 'http://120.77.38.59/';
            $idm_url = 'https://siamtest.vankeservice.com/';
        }
        Tpl::output('item_url',$item_url);
        Tpl::output('idm_url',$idm_url);
		//检查登录状态
		$model_member->checkloginMember();
		if ($_GET['inajax'] == 1 && C('captcha_status_login') == '1'){
		    $script = "document.getElementById('codeimage').src='".APP_SITE_URL."/index.php?act=seccode&op=makecode&nchash=".getNchash()."&t=' + Math.random();";
		}
		$result = chksubmit(true,C('captcha_status_login'),'num');
		if ($result !== false){
			if ($result === -11){
				showDialog($lang['login_index_login_illegal'],'','error',$script);
			}elseif ($result === -12){
				showDialog($lang['login_index_wrong_checkcode'],'','error',$script);
			}
			if (process::islock('login')) {
				showDialog($lang['nc_common_op_repeat'],SHOP_SITE_URL,'','error',$script);
			}
			$obj_validate = new Validate();
            $user_name = $_POST['user_name'];
            $password = $_POST['password'];
            $obj_validate->validateparam = array(
                array("input"=>$user_name,     "require"=>"true", "message"=>$lang['login_index_username_isnull']),
                array("input"=>$password,      "require"=>"true", "message"=>$lang['login_index_password_isnull']),
            );
			$error = $obj_validate->validate();
			if ($error != ''){
			    showDialog($error,SHOP_SITE_URL,'error',$script);
			}
            $condition = array();
            $condition['member_name'] = $user_name;
            $condition['member_passwd'] = md5($password);
            $member_info = $model_member->getMemberInfo($condition);
//            if(empty($member_info) && preg_match('/^0?(13|15|17|18|14)[0-9]{9}$/i', $user_name)) {//根据会员名没找到时查手机号
//                $condition = array();
//                $condition['member_mobile'] = $user_name;
//                $condition['member_passwd'] = md5($password);
//                $member_info = $model_member->getMemberInfo($condition);
//            }
            if(empty($member_info) && (strpos($user_name, '@') > 0)) {//按邮箱和密码查询会员
                $condition = array();
                $condition['member_email'] = $user_name;
                $condition['member_passwd'] = md5($password);
                $member_info = $model_member->getMemberInfo($condition);
            }
            //处理供应商账号到期问题
            /* @Aletta 2017.06.01*/
            if($member_info['role_id']=='02' || $member_info['role_id']=='03'){
                $model = Model();
                $supply_end_time = $model->table('supplier')->field('end_time')->where("member_id = '".$member_info['member_id']."' and supplier_state = 2")->find();
                if(empty($supply_end_time['end_time'])){
                    $end_time = $member_info['member_time'] + (SUPPLY_TIME_LONG * 24 * 3600);
                    $model->table('supplier')->where('member_id='.$member_info['member_id'])->update(array('end_time'=>$end_time));
                    if($end_time < TIMESTAMP){
                        $this->cloced_member_store($member_info['member_id']);
                        showDialog($lang['login_index_supply_end'],'','error',$script);
                    }
                }else{
                    if($supply_end_time['end_time'] < TIMESTAMP){
                        $this->cloced_member_store($member_info['member_id']);
                        showDialog($lang['login_index_supply_end'],'','error',$script);
                    }
                }
            }
            if(is_array($member_info) && !empty($member_info)) {
                if(!$member_info['member_state']){
                    showDialog($lang['login_index_account_stop'],'','error',$script);
                }
            }else{
                process::addprocess('login');
                showDialog($lang['login_index_login_fail'],'','error',$script);
            }
            $_SESSION['role_id'] = $member_info['role_id'];
            // 自动登录
            $member_info['auto_login'] = $_POST['auto_login'];
            $model_member->createSession($member_info);
            //判断如果有开店则 自动登录商家中心
             $model_seller = Model('seller');
            $seller_info = $model_seller->getSellerInfo(array('member_id' => $_SESSION['member_id']));
            if($seller_info){
                  $data = file_get_contents(SHOP_SITE_URL.'/index.php?act=seller_login&op=login_user&name='.$seller_info['seller_name'].'&pw='.  md5($password).'&post_return=ismemberlogin');

                  $r_data  = json_decode($data,true);
                  if($r_data['code'] == 2){
                      foreach($r_data['success'] as $key=>$rows){
                          $_SESSION[$key] = $rows;
                      }
                  }
            }
            process::clear('login');
            // cookie中的cart存入数据库
            Model('cart')->mergecart($member_info,$_SESSION['store_id']);

            // cookie中的浏览记录存入数据库
            Model('goods_browse')->mergebrowse($_SESSION['member_id'],$_SESSION['store_id']);
            if ($_GET['inajax'] == 1){
                showDialog('',$_POST['ref_url'] == '' ? 'reload' : $_POST['ref_url'],'js');
            } else {
				$ref_url = $_COOKIE['ref_url'];
				if(empty($ref_url)){
					$_SESSION['ref_url_iframe']=$_POST['ref_url_iframe'];
					redirect($_POST['ref_url']);
				}else{
					setcookie("ref_url", null,null,"/");
					redirect($ref_url);
				}

            }
        }else{

			//登录表单页面
			$_pic = @unserialize(C('login_pic'));
			if ($_pic[0] != ''){
				Tpl::output('lpic',UPLOAD_SITE_URL.'/'.ATTACH_LOGIN.'/'.$_pic[array_rand($_pic)]);
			}else{
				Tpl::output('lpic',UPLOAD_SITE_URL.'/'.ATTACH_LOGIN.'/'.rand(1,4).'.jpg');
			}

			if(empty($_GET['ref_url'])) {
			    $ref_url = getReferer();
			    if (!preg_match('/act=login&op=logout/', $ref_url)) {
			     $_GET['ref_url'] = $ref_url;
			    }
			}
			Tpl::output('html_title',C('site_name').' - '.$lang['login_index_login']);
			if ($_GET['inajax'] == 1){
				Tpl::showpage('login_inajax','null_layout');
			}else{
				Tpl::showpage('login');
			}
		}
	}


	
	
	/**
	 * 店铺到期关闭处理商品以及店铺数据
	 **/
	private function cloced_member_store($member_id){
	    if(!empty($member_id)){
	        $model = Model();
	        $sorte_id = $model->table('store')->field('store_id')->where("member_id = '".$member_id."'")->find();
	        if(!empty($sorte_id['store_id'])){
	            //根据店铺状态修改该店铺所有商品状态
	            $model_goods = Model('goods');
	            $model_goods->editProducesOffline_s(array('store_id' => $sorte_id['store_id']));
	            //修改店铺状态
//	            $model_store = Model('store');
//	            $update_array = array(
//	                'store_state'      =>0,
//	                'store_close_info' =>'商户账户到期'
//	            );
//	            $model_store->editStore($update_array, array('store_id' => $sorte_id['store_id']));
	        }
	    }
	}
	
	
	
	
        function file_get_contents_post($url, $post) {  
               $options = array(  
                   'http' => array(  
                       'method' => 'POST',  
                       // 'content' => 'name=caiknife&email=caiknife@gmail.com',  
                       'content' => http_build_query($post),  
                   ),  
               );  

               $result = file_get_contents($url, false, stream_context_create($options));  

               return $result;  
           }
	/**
	 * 退出操作
	 *
	 * @param int $id 记录ID
	 * @return array $rs_row 返回数组形式的查询结果
	 */
	public function logoutOp(){
		Language::read("home_login_index");
		$lang	= Language::getLangContent();
	        // 清理COOKIE
	        setNcCookie('msgnewnum'.$_SESSION['member_id'],'',-3600);
	        setNcCookie('auto_login', '', -3600);
	        setNcCookie('cart_goods_num','',-3600);
	        session_unset();
	        session_destroy();
		if(empty($_GET['ref_url'])){
			$ref_url = getReferer();
		}else {
			$ref_url = $_GET['ref_url'];
		}
		redirect('index.php?act=login');
	}

	/**
	 * 会员注册页面
	 *
	 * @param
	 * @return
	 */
	public function registerOp() {
		Language::read("home_login_register");
		$lang	= Language::getLangContent();
		$model_member	= Model('member');
		$model_member->checkloginMember();
		Tpl::output('html_title',C('site_name').' - '.$lang['login_register_join_us']);
		Tpl::showpage('register');
	}

	/**
	 * 会员添加操作
	 *
	 * @param
	 * @return
	 */
	public function usersaveOp() {
		//重复注册验证
		if (process::islock('reg')){
			showDialog(Language::get('nc_common_op_repeat'));
		}
		Language::read("home_login_register");
		$lang	= Language::getLangContent();
		$model_member	= Model('member');
		$model_member->checkloginMember();
		$result = chksubmit(true,C('captcha_status_register'),'num');
		if ($result){
			if ($result === -11){
				showDialog($lang['invalid_request'],'','error');
			}elseif ($result === -12){
				showDialog($lang['login_usersave_wrong_code'],'','error');
			}
		} else {
		    showDialog($lang['invalid_request'],'','error');
		}
        $register_info = array();
        $register_info['username'] = $_POST['user_name'];
        $register_info['password'] = $_POST['password'];
        $register_info['password_confirm'] = $_POST['password_confirm'];
        $register_info['email'] = $_POST['email'];
		//添加奖励积分ID BY abc.com V3
		//$register_info['inviter_id'] = intval($_COOKIE['uid'])/1;
		$register_info['inviter_id'] = intval(base64_decode($_COOKIE['uid']))/1;
		//cary_add 店铺邀请注册
		$register_info['inviter_store'] = intval(base64_decode($_COOKIE['stid']))/1;

        $member_info = $model_member->register($register_info);
        if(!isset($member_info['error'])) {
            $model_member->createSession($member_info,true);
			process::addprocess('reg');

			// cookie中的cart存入数据库
			Model('cart')->mergecart($member_info,$_SESSION['store_id']);

			// cookie中的浏览记录存入数据库
			Model('goods_browse')->mergebrowse($_SESSION['member_id'],$_SESSION['store_id']);

			$_POST['ref_url']   = (strstr($_POST['ref_url'],'logout')=== false && !empty($_POST['ref_url']) ? $_POST['ref_url'] : 'index.php?act=show_joinin&op=index');
                        $_POST['ref_url'] = urldecode($_POST['ref_url']);
	            if ($_GET['inajax'] == 1){
	                showDialog('',$_POST['ref_url'] == '' ? 'reload' : $_POST['ref_url'],'js');
	            } else {
	                redirect($_POST['ref_url']);
	            }
	        } else {
	            showDialog($member_info['error']);
	        }
	    }
	/**
	 * 会员名称检测
	 *
	 * @param
	 * @return
	 */
	public function check_memberOp() {
			/**
		 	* 实例化模型
		 	*/
			$model_member	= Model('member');

			$check_member_name	= $model_member->getMemberInfo(array('member_name'=>$_GET['user_name']));
			if(is_array($check_member_name) && count($check_member_name)>0) {
				echo 'false';
			} else {
				echo 'true';
			}
	}

	/**
	 * 电子邮箱检测
	 *
	 * @param
	 * @return
	 */
	public function check_emailOp() {
		$model_member = Model('member');
		$check_member_email	= $model_member->getMemberInfo(array('member_email'=>$_GET['email']));
		if(is_array($check_member_email) && count($check_member_email)>0) {
			echo 'false';
		} else {
			echo 'true';
		}
	}

	/**
	 * 忘记密码页面
	 */
	public function forget_passwordOp(){
		/**
		 * 读取语言包
		 */
		Language::read('home_login_register');
		$_pic = @unserialize(C('login_pic'));
		if ($_pic[0] != ''){
			Tpl::output('lpic',UPLOAD_SITE_URL.'/'.ATTACH_LOGIN.'/'.$_pic[array_rand($_pic)]);
		}else{
			Tpl::output('lpic',UPLOAD_SITE_URL.'/'.ATTACH_LOGIN.'/'.rand(1,4).'.jpg');
		}
		Tpl::output('html_title',C('site_name').' - '.Language::get('login_index_find_password'));
		Tpl::showpage('find_password');
	}

	/**
	 * 找回密码的发邮件处理
	 */
	public function find_passwordOp(){
		Language::read('home_login_register');
		$lang	= Language::getLangContent();

		$result = chksubmit(true,true,'num');
		if ($result !== false){
		    if ($result === -11){
		        showDialog('非法提交');
		    }elseif ($result === -12){
		        showDialog('验证码错误');
		    }
		}

		if(empty($_POST['username'])){
			showDialog($lang['login_password_input_username']);
		}

		if (process::islock('forget')) {
		    showDialog($lang['nc_common_op_repeat'],'reload');
		}

		$member_model	= Model('member');
		$member	= $member_model->getMemberInfo(array('member_name'=>$_POST['username']));
		if(empty($member) or !is_array($member)){
		    process::addprocess('forget');
			showDialog($lang['login_password_username_not_exists'],'reload');
		}

		if(empty($_POST['email'])){
			showDialog($lang['login_password_input_email'],'reload');
		}

		if(strtoupper($_POST['email'])!=strtoupper($member['member_email'])){
		    process::addprocess('forget');
			showDialog($lang['login_password_email_not_exists'],'reload');
		}
		process::clear('forget');
		//产生密码
		$new_password	= random(15);
		if(!($member_model->editMember(array('member_id'=>$member['member_id']),array('member_passwd'=>md5($new_password))))){
			showDialog($lang['login_password_email_fail'],'reload');
		}

		$model_tpl = Model('mail_templates');
		$tpl_info = $model_tpl->getTplInfo(array('code'=>'reset_pwd'));
		$param = array();
		$param['site_name']	= C('site_name');
		$param['user_name'] = $_POST['username'];
		$param['new_password'] = $new_password;
		$param['site_url'] = SHOP_SITE_URL;
		$subject	= ncReplaceText($tpl_info['title'],$param);
		$message	= ncReplaceText($tpl_info['content'],$param);

//		$email	= new Email();
                $email = new MySendMail();
		$result	= $email->send_sys_email($_POST["email"],$subject,$message);
		showDialog('新密码已经发送至您的邮箱，请尽快登录并更改密码！','','succ','',5);
	}

	/**
	 * 邮箱绑定验证
	 */
	public function bind_emailOp() {
	   $model_member = Model('member');
	   $uid = @base64_decode($_GET['uid']);
	   $uid = decrypt($uid,'');
	   list($member_id,$member_email) = explode(' ', $uid);

	   if (!is_numeric($member_id)) {
	       showMessage('验证失败',SHOP_SITE_URL,'html','error');
	   }

	   $member_info = $model_member->getMemberInfo(array('member_id'=>$member_id),'member_email,role_id');
	   if ($member_info['member_email'] != $member_email) {
	       showMessage('验证失败',SHOP_SITE_URL,'html','error');
	   }

	   $member_common_info = $model_member->getMemberCommonInfo(array('member_id'=>$member_id));
	   if (empty($member_common_info) || !is_array($member_common_info)) {
	       showMessage('验证失败',SHOP_SITE_URL,'html','error');
	   }
	   if (md5($member_common_info['auth_code']) != $_GET['hash'] || TIMESTAMP - $member_common_info['send_acode_time'] > 24*3600) {
	       showMessage('验证失败',SHOP_SITE_URL,'html','error');
	   }

	   $update = $model_member->editMember(array('member_id'=>$member_id),array('member_email_bind'=>1));
	   if (!$update) {
	       showMessage('系统发生错误，如有疑问请与管理员联系',SHOP_SITE_URL,'html','error');
	   }

	   $data = array();
	   $data['auth_code'] = '';
	   $data['send_acode_time'] = 0;
	   $update = $model_member->editMemberCommon($data,array('member_id'=>$_SESSION['member_id']));
	   if (!$update) {
	       showDialog('系统发生错误，如有疑问请与管理员联系');
	   }
           //修改绑定邮箱后，同时修改注册认证供应商的邮箱
           if($member_info['role_id']==MEMBER_IDENTITY_THREE||$member_info['role_id']==MEMBER_IDENTITY_FOUR){
              $email_address = array();
              $email_address['contacts_email']= $member_email;
              Model()->table('supplier')->where(array("member_id"=>$member_id))->update($email_address);
           }
        showMessage('邮箱设置成功','index.php?act=member_security&op=index');

	}
}
