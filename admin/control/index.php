<?php
/**
 * 默认展示页面
 *
 * 默认展示页面
 *
 ***/



class indexControl extends SystemControl{
	public function __construct(){
		parent::__construct();
		Language::read('index');
	}
	public function indexOp(){
		//输出管理员信息
		Tpl::output('admin_info',$this->getAdminInfo());
		//输出菜单
		$this->getNav('',$top_nav,$left_nav,$map_nav);
		Tpl::output('top_nav',$top_nav);
		Tpl::output('left_nav',$left_nav);
		Tpl::output('map_nav',$map_nav);
		Tpl::output('expire_time',$this->getExpireTime() > TIMESTAMP ? 1:'index.php?act=index&op=modifypw');

		Tpl::showpage('index','index_layout');
	}

	/**
	 * 退出
	 */
	public function logoutOp(){
		setNcCookie('sys_key','',-1,'',null);
                session_unset();
		@header("Location: index.php");
		exit;
	}
	/**
	 * 修改密码
	 */
	public function modifypwOp(){
		if (chksubmit()){
			if (trim($_POST['new_pw']) !== trim($_POST['new_pw2'])){
				//showMessage('两次输入的密码不一致，请重新输入');
				showMessage(Language::get('index_modifypw_repeat_error'));
			}
			$admininfo = $this->getAdminInfo();
			//查询管理员信息
			$admin_model = Model('admin');
			$admininfo = $admin_model->getOneAdmin($admininfo['id']);
			if (!is_array($admininfo) || count($admininfo)<= 0){
				showMessage(Language::get('index_modifypw_admin_error'));
			}
			//旧密码是否正确
			if ($admininfo['admin_password'] != md5(trim($_POST['old_pw']))){
				showMessage(Language::get('index_modifypw_oldpw_error'));
			}
			$new_pw = md5(trim($_POST['new_pw']));
			$result = $admin_model->updateAdmin(array('admin_password'=>$new_pw,'admin_id'=>$admininfo['admin_id'],'up_pwd_time'=>TIMESTAMP));
			if ($result){
				showMessage(Language::get('index_modifypw_success'),'index.php?act=index&op=logout');
			}else{
				showMessage(Language::get('index_modifypw_fail'));
			}
		}else{
			Language::read('admin');
			Tpl::showpage('admin.modifypw');
		}
	}
}
