<?php
/**
 * 店铺开店
 *
 *
 *
 ***/



class storejoininlogControl extends BaseHomeControl {
    public function __construct() {
        parent::__construct();
        Tpl::setLayout('store_joinin_layout');
        if($_GET['op'] != 'bind_email'){
            $this->checkLogin();
        }
//        $model_seller = Model('seller');
//        $seller_info = $model_seller->getSellerInfo(array('member_id' => $_SESSION['member_id']));
//		if(!empty($seller_info)) {
//            @header('location: index.php?act=seller_login');
//		}
        if($_GET['op'] != 'loocklog' && $_GET['op'] != 'send_email' && $_GET['op'] != 'bind_email'){
            $this->check_joinin_state();
        }
        
        $phone_array = explode(',',C('site_phone'));
        Tpl::output('phone_array',$phone_array);
        $model_help = Model('help');
        $condition = array();
        $condition['type_id'] = '99';//默认显示入驻流程;
        $list = $model_help->getShowStoreHelpList($condition);
        Tpl::output('list',$list);//左侧帮助类型及帮助
        Tpl::output('show_sign','joinin');
        Tpl::output('html_title',C('site_name').' - '.'商家入驻');
        Tpl::output('article_list','');//底部不显示文章分类
        
    }
    
    private function check_joinin_state() {
        
        Tpl::output('show_sign','joinin');
        $model_store_joinin = Model('store_joinin_edit');
        $joinin_detail_where['member_id'] = $_SESSION['member_id'];
        $joinin_detail = $model_store_joinin->where($joinin_detail_where)->find();
        if(!empty($joinin_detail)) {
            $this->joinin_detail = $joinin_detail;
            switch (intval($joinin_detail['joinin_state'])) {
                case STORE_JOIN_STATE_RZ:
                    $this->show_join_message('入驻申请已经提交，请等待管理员审核', FALSE, '3');
                    break;
                case STORE_JOIN_STATE_EMAIL:
                    Tpl::output('rzsuccess',5);
                    $this->show_join_message('已经提交，请验证您的邮箱！  <a href="'.SHOP_SITE_URL.DS.'index.php?act=storejoininlog&op=send_email">重新发送邮件</a>', FALSE, '2');
                    break;
                default :
                    break;
            }
        }
    }
    
    private function show_join_message($message, $btn_next = FALSE, $step = '2') {
        Tpl::output('joinin_message', $message);
        Tpl::output('btn_next', $btn_next);
        Tpl::output('step', $step);
        Tpl::output('sub_step', 'step4');
        if($_GET['is_rz'] == 1){
            Tpl::showpage('store_joinin_apply2');
        }else{
            Tpl::showpage('store_joinin_apply');
        }
        exit;
    }
	
    public function check_emailOp(){
        if($_SESSION['member_id'] < 0 ){
            showDialog("非法操作！");exit;
        }
        $model = Model();
        $email = $model->table('supplier')->field('contacts_email')->where("member_id = '".$_SESSION['member_id']."'")->find();
        if(!empty($email)){
            //如果查询结果不为空
            $email_where = "member_id != '".$_SESSION['member_id']."'";
            $email_where.= " and contacts_email = '".htmlspecialchars($_POST['email'])."'";
            $email_rest = $model->table('supplier')->field('member_id,contacts_email')->where($email_where)->select();
            if(empty($email_rest)){
                echo 1;
            }else{
                $orther_data = array();
                foreach ($email_rest as $vl){
                    $join_where = "city_center = first_city_id and member_id = '".$vl['member_id']."' and joinin_state in('44','43')";
                    $join_log = $model->table('store_joinin')->where($join_where)->find();
                    $orther_data[] = empty($join_log) ? 1:2;
                }
                echo in_array(2,$orther_data) ? 2:1;
            }
        }else {
            showDialog("非法操作！");
            exit;
        }
    }
    
    public function loocklogOp(){
        if($_SESSION['member_id'] > 0){
            $model = Model();
            //查询申请记录表
            $filed = 'store_joinin.city_center,joinin_state,contacts_name,store_joinin.first_city_id,company_name';
            $on = "store_joinin.member_id = supplier.member_id";
            $condition = "store_joinin.member_id = '".$_SESSION['member_id']."'";
            $rz_log = $model->table('store_joinin,supplier')->field($filed)->join('left')->on($on)->where($condition)->select();
            //$where['member_id'] = $_SESSION['member_id'];
            //$rz_log = $model->table('store_joinin')->where($where)->field($filed)->select();
            
            $cache_news = new CacheFile();
            $cache_info_list = $cache_news->get('city_name');
            foreach($rz_log as $key=>$r_rows){
                foreach($cache_info_list as $c_rows){
                    if($r_rows['city_center'] == $c_rows['id']){
                        $rz_log[$key]['city_name'] = $c_rows['city_name'];
                    }
                }
            }
            Tpl::output('log',$rz_log);
            Tpl::setLayout('store_joinin_layout');
            Tpl::output('html_title',C('site_name').' - '.'商家入驻');
            Tpl::output('article_list','');//底部不显示文章分类
        }else{
            showMessage("请先登录");
        }
        
        Tpl::showpage('loocklog');
    }
    
    public function loglookOp(){
        
        $city_id = intval($_GET['id']);
        if($city_id >0 ){
            $model =  Model();
            $on = "store_joinin.member_id = supplier.member_id";
            $condition = "store_joinin.member_id = '".$_SESSION['member_id']."' and store_joinin.city_center = '".$city_id."'";
            $data_rz = $model->table('store_joinin,supplier')->join('left')->on($on)->where($condition)->find();
            $city = $model->table('city_centre')->where('id='.$city_id)->select();
            
            Tpl::output('city',$city);
            Tpl::output('data_rz',$data_rz);
        }
        
            
        Tpl::output('op','3');
        Tpl::setLayout('store_joinin_layout');
        Tpl::output('html_title',C('site_name').' - '.'商家入驻');
        Tpl::output('article_list','');//底部不显示文章分类
        Tpl::showpage('logedit');
    }
    
    public function logeditOp(){
        $city_id = intval($_GET['id']);
        if(empty($_SESSION['member_id'])) {
            showMessage("请先登录");
        }
        if($city_id > 0){
            $model =  Model();
            //查询申请修改审核记录是否有记录 没有则调用stroe_joninin
            $where['member_id'] = $_SESSION['member_id'];
            $where['city_center'] = $city_id;
            
            $data_rz = $model->table('store_joinin_edit')->where($where)->find();
            if(!$data_rz){
                $on = "store_joinin.member_id = supplier.member_id";
                $condition = "store_joinin.member_id = '".$_SESSION['member_id']."' and store_joinin.city_center = '".$city_id."'";
                $data_rz = $model->table('store_joinin,supplier')->join('left')->on($on)->where($condition)->find();
                //$data_rz = $model->table('store_joinin')->where($where)->find();
            }
            
            if($data_rz['first_city_id'] != $city_id){
                showMessage("对不起，您没有修改该城市资料权限！");
            }
            
            $city = $model->table('city_centre')->where('id='.$city_id)->select();
            
            Tpl::output('city',$city);
            Tpl::output('data_rz',$data_rz);
        }
        Tpl::output('op','1');
        Tpl::setLayout('store_joinin_layout');
        Tpl::output('html_title',C('site_name').' - '.'商家入驻');
        Tpl::output('article_list','');//底部不显示文章分类
        Tpl::showpage('logedit');
    }
    
    public function logedit2Op(){
        $city_id = intval($_GET['id']);
        if(empty($_SESSION['member_id'])){
            showMessage("请先登录");
        }
        $model = Model();
        $supplier_where = "member_id = '".$_SESSION['member_id']."'";
        $supplier = $model->table("supplier")->where($supplier_where)->find();
        $join_info = $model->table("store_joinin")->where("member_id = '".$_SESSION['member_id']."' and city_center = '".$city_id."'")->find();
        if($city_id > 0 && !empty($supplier) && !empty($join_info)){
            if(!empty($_POST)) {
                //获取是否存在修改数据
                $edit_data = $model->table("store_joinin_edit")->where("member_id = '".$_SESSION['member_id']."'")->find();
                $updata = array(
                    'company_province_id'       => intval($_POST['province_id']),
                    'company_address'           => $_POST['company_address'],
                    'company_address_detail'    => $_POST['company_address_detail'],
                    'company_phone'             => $_POST['company_phone'],
                    'company_employee_count'    => intval($_POST['company_employee_count']),
                    'company_registered_capital'=> intval($_POST['company_registered_capital']),
                    'contacts_name'             => $_POST['contacts_name'],
                    'contacts_phone'            => $_POST['contacts_phone'],
                    'contacts_email'            => $_POST['contacts_email'],
                    'joinin_state'              => STORE_JOIN_STATE_RZ,
                );

                if (!empty($edit_data)) {
                    $is_ture = $model->table('store_joinin_edit')->where("member_id = '" . $_SESSION['member_id'] . "'")->update($updata);
                } else {
                    //修改数据
                    $supplier_where = "member_id = '" . $_SESSION['member_id'] . "'";
                    $supplier = $model->table("supplier")->where($supplier_where)->find();
                    $updata['company_name'] = $supplier['company_name'];
                    $updata['member_name'] = $supplier['member_name'];
                    $updata['business_licence_number'] = $supplier['business_licence_number'];
                    $updata['business_licence_address'] = $supplier['business_licence_address'];
                    $updata['business_licence_start'] = $supplier['business_licence_start'];
                    $updata['business_licence_end'] = $supplier['business_licence_end'];
                    $updata['business_sphere'] = $supplier['business_sphere'];
                    $updata['business_licence_number_electronic'] = $supplier['business_licence_number_electronic'];
                    $updata['organization_code'] = $supplier['organization_code'];
                    $updata['organization_code_electronic'] = $supplier['organization_code_electronic'];
                    $updata['general_taxpayer'] = $supplier['general_taxpayer'];
                    $updata['bank_account_name'] = $supplier['bank_account_name'];
                    $updata['bank_account_number'] = $supplier['bank_account_number'];
                    $updata['bank_name'] = $supplier['bank_name'];
                    $updata['bank_code'] = $supplier['bank_code'];
                    $updata['bank_address'] = $supplier['bank_address'];
                    $updata['bank_licence_electronic'] = $supplier['bank_licence_electronic'];
                    $updata['is_settlement_account'] = $supplier['is_settlement_account'];
                    $updata['settlement_bank_account_name'] = $supplier['settlement_bank_account_name'];
                    $updata['settlement_bank_account_number'] = $supplier['settlement_bank_account_number'];
                    $updata['settlement_bank_name'] = $supplier['settlement_bank_name'];
                    $updata['settlement_bank_code'] = $supplier['settlement_bank_code'];
                    $updata['settlement_bank_address'] = $supplier['settlement_bank_address'];
                    $updata['tax_registration_certificate'] = $supplier['tax_registration_certificate'];
                    $updata['taxpayer_id'] = $supplier['taxpayer_id'];
                    $updata['tax_registration_certificate_electronic'] = $supplier['tax_registration_certificate_electronic'];
                    $updata['seller_name'] = $join_info['seller_name'];
                    $updata['store_name'] = $join_info['store_name'];
                    $updata['store_class_ids'] = $join_info['store_class_ids'];
                    $updata['store_class_names'] = $join_info['store_class_names'];
                    $updata['joinin_message'] = $join_info['joinin_message'];
                    $updata['joinin_year'] = $join_info['joinin_year'];
                    $updata['sg_name'] = $join_info['sg_name'];
                    $updata['sg_id'] = $join_info['sg_id'];
                    $updata['sg_info'] = $join_info['sg_info'];
                    $updata['sc_name'] = $join_info['sc_name'];
                    $updata['sc_id'] = $join_info['sc_id'];
                    $updata['sc_bail'] = $join_info['sc_bail'];
                    $updata['store_class_commis_rates'] = $join_info['store_class_commis_rates'];
                    $updata['paying_money_certificate'] = $join_info['paying_money_certificate'];
                    $updata['paying_money_certificate_explain'] = $join_info['paying_money_certificate'];
                    $updata['paying_amount'] = $join_info['paying_amount'];
                    $updata['agent_id'] = $join_info['agent_id'];
                    $updata['store_type_id'] = $join_info['store_type_id'];
                    $updata['store_type_name'] = $join_info['store_type_name'];
                    $updata['city_center'] = $join_info['city_center'];
                    $updata['store_state'] = $join_info['store_state'];
                    $updata['joinin_message_open'] = $join_info['joinin_message_open'];
                    $updata['rz_evaluation_audit'] = $join_info['rz_evaluation_audit'];
                    $updata['is_therea'] = $supplier['is_therea'];
                    $updata['first_city_id'] = $join_info['first_city_id'];
                    $updata['if_edit'] = 1;
                    $updata['member_id'] = $_SESSION['member_id'];
                    $is_ture = $model->table('store_joinin_edit')->insert($updata);
                }
                if ($is_ture) {
                    $where['member_id'] = $_SESSION['member_id'];
                    $where['city_center'] = $city_id;
                    $data_rz = $model->table('store_joinin_edit')->where($where)->find();
                    if ($data_rz['contacts_email'] != $_POST['contacts_email']) {
                        $_SESSION['open_sendemail'] = 1;
                    }
                    showDialog('资料修改成功，请等待管理员的审核！', 'index.php?act=store_joinin&op=ckrz');
                } else {
                    showDialog('资料修改失败，请重新操作！', 'index.php?act=store_joinin&op=ckrz');
                }
            }
        }
    }
    
    public function logedit3Op(){
        
        if(!empty($_POST)) {
            $param = array();
            $param['bank_account_name'] = $_POST['bank_account_name'];
            $param['bank_account_number'] = $_POST['bank_account_number'];
            $param['bank_name'] = $_POST['bank_name'];
            $param['bank_code'] = $_POST['bank_code'];
            $param['bank_address'] = $_POST['bank_address'];
            if($_SESSION['open_sendemail'] == 1){
                $param['joinin_state'] = STORE_JOIN_STATE_EMAIL;
            }else{
                $param['joinin_state'] = STORE_JOIN_STATE_RZ;
            }
            
            if(!empty($_FILES['bank_licence_electronic']['name'])){
                $param['bank_licence_electronic'] = $this->upload_image('bank_licence_electronic');
            }
            
            if(!empty($_POST['is_settlement_account'])) {
                $param['is_settlement_account'] = 1;
                $param['settlement_bank_account_name'] = $_POST['bank_account_name'];
                $param['settlement_bank_account_number'] = $_POST['bank_account_number'];
                $param['settlement_bank_name'] = $_POST['bank_name'];
                $param['settlement_bank_code'] = $_POST['bank_code'];
                $param['settlement_bank_address'] = $_POST['bank_address'];
            } else {
                $param['is_settlement_account'] = 2;
                $param['settlement_bank_account_name'] = $_POST['settlement_bank_account_name'];
                $param['settlement_bank_account_number'] = $_POST['settlement_bank_account_number'];
                $param['settlement_bank_name'] = $_POST['settlement_bank_name'];
                $param['settlement_bank_code'] = $_POST['settlement_bank_code'];
                $param['settlement_bank_address'] = $_POST['settlement_bank_address'];
                

            }

            $this->step3_save_valid($param);
            $model= Model();
            $where_joinin['if_edit'] = 1;
            $where_joinin['member_id'] = $_SESSION['member_id'];
            $up_parm = $model->table('store_joinin_edit')->where($where_joinin)->update($param);
            if($up_parm != false){
                showDialog('提交成功，请等待管理员审核！',BASE_SITE_URL.'/index.php?act=storejoininlog&op=loocklog');
                if($_SESSION['open_sendemail'] == 1){
                    $this->send_emailOp();
                }
            }
            
        }
        
    }
    
    private function step3_save_valid($param) {
        $obj_validate = new Validate();
        $obj_validate->validateparam = array(
            array("input"=>$param['bank_account_name'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>"银行开户名不能为空且必须小于50个字"),
            array("input"=>$param['bank_account_number'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"20","message"=>"银行账号不能为空且必须小于20个字"),
            array("input"=>$param['bank_name'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>"开户银行支行名称不能为空且必须小于50个字"),
            //array("input"=>$param['bank_code'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"20","message"=>"支行联行号不能为空且必须小于20个字"),
            array("input"=>$input['bank_address'], "require"=>"true","开户行所在地不能为空"),
            //array("input"=>$input['bank_licence_electronic'], "require"=>"true","开户银行许可证电子版不能为空"),
            array("input"=>$param['settlement_bank_account_name'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>"银行开户名不能为空且必须小于50个字"),
            array("input"=>$param['settlement_bank_account_number'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"20","message"=>"银行账号不能为空且必须小于20个字"),
            array("input"=>$param['settlement_bank_name'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>"开户银行支行名称不能为空且必须小于50个字"),
            //array("input"=>$param['settlement_bank_code'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"20","message"=>"支行联行号不能为空且必须小于20个字"),
            array("input"=>$input['settlement_bank_address'], "require"=>"true","开户行所在地不能为空"),
//            array("input"=>$param['tax_registration_certificate'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"20","message"=>"税务登记证号不能为空且必须小于20个字"),
            //array("input"=>$param['taxpayer_id'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"20","message"=>"纳税人识别号"),
//            array("input"=>$param['tax_registration_certificate_electronic'], "require"=>"true","message"=>"税务登记证号电子版不能为空"),
        );
        $error = $obj_validate->validate();
        if ($error != ''){
            showMessage($error);
        }
    }
    
    public function send_emailOp(){
            if(!$_SESSION['member_id']){
                $this->show_join_message('请先登录！', SHOP_SITE_URL.DS.'index.php?act=login');
            }
            //发送邮件，验证状态
            $model_store_joinin = Model('store_joinin_edit');
            $where_emaildata['if_edit'] = 1;
            $where_emaildata['member_id'] = $_SESSION['member_id'];
            $email_cont = $model_store_joinin->where($where_emaildata)->field('contacts_email')->find();
            
            $model_member = Model('member');
            $seed = random(6);
            $data = array();
            $data['auth_code'] = $seed;
            $data['send_acode_time'] = TIMESTAMP;
            $update = $model_member->editMemberCommon($data,array('member_id'=>$_SESSION['member_id']));
            if (!$update) {
                showDialog('系统发生错误，如有疑问请与管理员联系');
            }
            $uid = base64_encode(encrypt($_SESSION['member_id'].' '.$email_cont["contacts_email"]));
            $verify_url = SHOP_SITE_URL.'/index.php?act=storejoininlog&op=bind_email&uid='.$uid.'&hash='.md5($seed);

            $model_tpl = Model('mail_templates');
            $tpl_info = $model_tpl->getTplInfo(array('code'=>'bind_email'));
            $param = array();
            $param['site_name']	= C('site_name');
            $param['user_name'] = $_SESSION['member_name'];
            $param['verify_url'] = $verify_url;
            $subject	= "供应商认证验证邮箱";
            $message	= ncReplaceText($tpl_info['content'],$param);

//            $email	= new Email();
            $email = new MySendMail();
            $result	= $email->send_sys_email($email_cont["contacts_email"],$subject,$message);
            if($result != false){
                showDialog('邮件发送成功，请及时认证！',BASE_SITE_URL.'/index.php?act=storejoininlog&op=loocklog');
            }else{
                showDialog('发送失败');
            }
    }
    
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
	   $email = $model->table('store_joinin_edit')->where($email_where)->field('contacts_email')->find();
           
	   if ($member_email != $email['contacts_email']) {
	       showMessage('验证失败',SHOP_SITE_URL,'html','error');
	   }
           
	   $member_common_info = $model_member->getMemberCommonInfo(array('member_id'=>$member_id));
           
	   if (md5($member_common_info['auth_code']) != $_GET['hash'] || TIMESTAMP - $member_common_info['send_acode_time'] > 24*3600) {
	       showMessage('验证失败',SHOP_SITE_URL,'html','error');
	   }

           //执行更新操作
           $up_where['if_edit'] = 1;
           $up_where['member_id'] = $member_id;
           
           $up_data['joinin_state'] = STORE_JOIN_STATE_RZ;
	   $update = $model->table('store_joinin_edit')->where($up_where)->update($up_data);
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
	   showMessage('邮箱验证成功','index.php?act=show_joinin&op=loocklog');

	}
        
        private function upload_image($file) {
        $pic_name = '';
        $upload = new UploadFile();
        $uploaddir = ATTACH_PATH.DS.'store_joinin'.DS;
        $upload->set('default_dir',$uploaddir);
        $upload->set('allow_type',array('jpg','jpeg','gif','png'));
        if (!empty($_FILES[$file]['name'])){
            $result = $upload->upfile($file);
            if ($result){
                $pic_name = $upload->file_name;
                $upload->file_name = '';
            }
        }
        return $pic_name;
    }
        


}
