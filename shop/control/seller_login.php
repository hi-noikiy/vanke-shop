<?php
/**
 * 店铺卖家登录
 *
 *
 *
 ***/




class seller_loginControl extends BaseSellerControl {

	public function __construct() {
		parent::__construct();
        if (!empty($_SESSION['seller_id'])) {
            @header('location: index.php?act=seller_center');die;
        }
	}

    public function indexOp() {
        $this->show_loginOp();
    }

    public function show_loginOp() {
        Tpl::output('nchash', getNchash());
		Tpl::setLayout('null_layout');
        Tpl::showpage('login');
    }

    public function loginOp() {
        $result = chksubmit(true,true,'num');
        if ($result){
            if ($result === -11){
                showDialog('用户名或密码错误','','error');
            } elseif ($result === -12){
                showDialog('验证码错误','','error');
            }
        } else {
            showDialog('非法提交','','error');
        }

        $model_seller = Model('seller');
        $seller_info = $model_seller->getSellerInfo(array('seller_name' => $_POST['seller_name']));
        if($seller_info) {

            //处理供应商账号到期问题
            /* @Aletta 2017.06.01*/
            $model = Model();
            $member_info = $model->table('member')->where("member_id = '".$seller_info['member_id']."'")->find();
            if($member_info['role_id']=='02' || $member_info['role_id']=='03'){
                $model = Model();
                $supply_end_time = $model->table('supplier')->field('end_time')->where("member_id = '".$member_info['member_id']."' and supplier_state = 2")->find();
                if(empty($supply_end_time['end_time'])){
                    $end_time = $member_info['member_time'] + (SUPPLY_TIME_LONG * 24 * 3600);
                    $model->table('supplier')->where('member_id='.$member_info['member_id'])->update(array('end_time'=>$end_time));
                    if($end_time < TIMESTAMP){
                        $this->cloced_member_store($member_info['member_id']);
                        showDialog('账号已到期，请联系管理员','','error');
                    }
                }else{
                    if($supply_end_time['end_time'] < TIMESTAMP){
                        $this->cloced_member_store($member_info['member_id']);
                        showDialog('账号已到期，请联系管理员','','error');
                    }
                }
            }


            $model_member = Model('member');
            $member_info = $model_member->getMemberInfo(
                array(
                    'member_id' => $seller_info['member_id'],
                    'member_passwd' => md5($_POST['password'])
                )
            );
            if($member_info) {
                $_SESSION['role_id'] = $member_info['role_id'];

                // 更新卖家登陆时间
                $model_seller->editSeller(array('last_login_time' => TIMESTAMP), array('seller_id' => $seller_info['seller_id']));

                $model_seller_group = Model('seller_group');
                $seller_group_info = $model_seller_group->getSellerGroupInfo(array('group_id' => $seller_info['seller_group_id']));

                $model_store = Model('store');
                $store_info = $model_store->getStoreInfoByID($seller_info['store_id']);

                $_SESSION['is_login'] = '1';
                $_SESSION['identity'] = $member_info['role_id'];
                $_SESSION['member_id'] = $member_info['member_id'];
                $_SESSION['member_name'] = $member_info['member_name'];
    			$_SESSION['member_email'] = $member_info['member_email'];
    			$_SESSION['is_buy']	= $member_info['is_buy'];
    			$_SESSION['avatar']	= $member_info['member_avatar'];

                $_SESSION['grade_id'] = $store_info['grade_id'];
                $_SESSION['seller_id'] = $seller_info['seller_id'];
                $_SESSION['seller_name'] = $seller_info['seller_name'];
                $_SESSION['seller_is_admin'] = intval($seller_info['is_admin']);
                $_SESSION['store_id'] = intval($seller_info['store_id']);
                $_SESSION['store_name']	= $store_info['store_name'];
                $_SESSION['is_own_shop'] = (bool) $store_info['is_own_shop'];
                $_SESSION['bind_all_gc'] = (bool) $store_info['bind_all_gc'];
                $_SESSION['seller_limits'] = explode(',', $seller_group_info['limits']);
                if($seller_info['is_admin']) {
                    $_SESSION['seller_group_name'] = '管理员';
                    $_SESSION['seller_smt_limits'] = false;
                } else {
                    $_SESSION['seller_group_name'] = $seller_group_info['group_name'];
                    $_SESSION['seller_smt_limits'] = explode(',', $seller_group_info['smt_limits']);
                }
                if(!$seller_info['last_login_time']) {
                    $seller_info['last_login_time'] = TIMESTAMP;
                }
                $_SESSION['seller_last_login_time'] = date('Y-m-d H:i', $seller_info['last_login_time']);
                $seller_menu = $this->getSellerMenuList($seller_info['is_admin'], explode(',', $seller_group_info['limits']));
                $_SESSION['seller_menu'] = $seller_menu['seller_menu'];
                $_SESSION['seller_function_list'] = $seller_menu['seller_function_list'];
                if(!empty($seller_info['seller_quicklink'])) {
                    $quicklink_array = explode(',', $seller_info['seller_quicklink']);
                    foreach ($quicklink_array as $value) {
                        $_SESSION['seller_quicklink'][$value] = $value ;
                    }
                }
                //写入session有效期   @Aletta @time 2017.05.23 10:40
                $_SESSION['expiretime'] = TIMESTAMP + LOGIN_SESSION_TIME;
                /*@Aletta end*/
                
                $this->recordSellerLog('登录成功');
                redirect('index.php?act=seller_center');
            } else {
                showMessage('用户名密码错误', '', '', 'error');
            }
        } else {
            showMessage('用户名密码错误', '', '', 'error');
        }
    }
    
   //会员中心登录
    public function login_userOp() {
        
        if(!$_GET['name'] || !$_GET['pw'] ){
            showDialog('账号密码不正确','','error');
        }
        
        if($_GET['post_return'] != 'ismemberlogin'){
            showDialog('非法提交','','error');
        }
        $seller_name = htmlspecialchars($_GET['name']);
        $paswword = htmlspecialchars($_GET['password']);
        
        $model_seller = Model('seller');
        $seller_info = $model_seller->getSellerInfo(array('seller_name' =>$seller_name));
        if($seller_info) {

            $model_member = Model('member');
            $member_info = $model_member->getMemberInfo(
                array(
                    'member_id' => $seller_info['member_id'],
                )
            );
            if($member_info) {
                // 更新卖家登陆时间
                $model_seller->editSeller(array('last_login_time' => TIMESTAMP), array('seller_id' => $seller_info['seller_id']));

                $model_seller_group = Model('seller_group');
                $seller_group_info = $model_seller_group->getSellerGroupInfo(array('group_id' => $seller_info['seller_group_id']));

                $model_store = Model('store');
                $store_info = $model_store->getStoreInfoByID($seller_info['store_id']);

                $data['is_login'] = '1';
                $data['identity'] = $member_info['role_id'];
                $data['member_id'] = $member_info['member_id'];
                $data['member_name'] = $member_info['member_name'];
                $data['member_email'] = $member_info['member_email'];
                $data['is_buy']	= $member_info['is_buy'];
                $data['avatar']	= $member_info['member_avatar'];

                $data['grade_id'] = $store_info['grade_id'];
                $data['seller_id'] = $seller_info['seller_id'];
                $data['seller_name'] = $seller_info['seller_name'];
                $data['seller_is_admin'] = intval($seller_info['is_admin']);
                $data['store_id'] = intval($seller_info['store_id']);
                $data['store_name']	= $store_info['store_name'];
                $data['is_own_shop'] = (bool) $store_info['is_own_shop'];
                $data['bind_all_gc'] = (bool) $store_info['bind_all_gc'];
                $data['seller_limits'] = explode(',', $seller_group_info['limits']);
                if($seller_info['is_admin']) {
                    $data['seller_group_name'] = '管理员';
                    $data['seller_smt_limits'] = false;
                } else {
                    $data['seller_group_name'] = $seller_group_info['group_name'];
                    $data['seller_smt_limits'] = explode(',', $seller_group_info['smt_limits']);
                }
                if(!$seller_info['last_login_time']) {
                    $seller_info['last_login_time'] = TIMESTAMP;
                }
                $data['seller_last_login_time'] = date('Y-m-d H:i', $seller_info['last_login_time']);
                $seller_menu = $this->getSellerMenuList($seller_info['is_admin'], explode(',', $seller_group_info['limits']));
                $data['seller_menu'] = $seller_menu['seller_menu'];
                $data['seller_function_list'] = $seller_menu['seller_function_list'];
                if(!empty($seller_info['seller_quicklink'])) {
                    $quicklink_array = explode(',', $seller_info['seller_quicklink']);
                    foreach ($quicklink_array as $value) {
                        $data['seller_quicklink'][$value] = $value ;
                    }
                }
                $this->recordSellerLog('登录成功');
                $re_data['success'] = $data;
                $re_data['code'] = 2;
                echo json_encode($re_data);exit;
            }else{
                $data['error'] = '账号密码错误';
                $data['code']  = 1;
                echo json_encode($data);exit;
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
}
