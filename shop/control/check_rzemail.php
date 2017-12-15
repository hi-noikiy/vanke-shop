<?php
/**
 * 前台登录 退出操作
 *
 *
 *
 ***/




class check_rzemailControl extends BaseHomeControl {

	public function __construct(){
		parent::__construct();
		Tpl::output('hidden_nctoolbar', 1);
		Tpl::setLayout('login_layout');
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
           $model = Model();
           $email_where['member_id'] = $member_id;
	   $email = $model->table('supplier')->where($email_where)->field('contacts_email')->find();
           
	   if ($member_email != $email['contacts_email']) {
	       showMessage('验证失败',SHOP_SITE_URL,'html','error');
	   }
           
	   $member_common_info = $model_member->getMemberCommonInfo(array('member_id'=>$member_id));
           
	   if (md5($member_common_info['auth_code']) != $_GET['hash'] || TIMESTAMP - $member_common_info['send_acode_time'] > 24*3600) {
	       showMessage('验证失败',SHOP_SITE_URL,'html','error');
	   }

           //执行更新操作
           $up_where['city_center'] = $_GET['city'];
           $up_where['member_id'] = $member_id;
           
           $up_data['joinin_state'] = 43;
	   $update = $model->table('store_joinin')->where($up_where)->update($up_data);
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
           //验证邮箱后直接绑定邮箱
           $email_array=array();
           $email_array['member_email_bind']=1;
           $email_array['member_email']=$member_email;
           $model_member->table('member')->where(array('member_id'=>$member_id))->update($email_array);
	   showMessage('邮箱验证成功','index.php?act=store_joinin&op=index');

	}
}
