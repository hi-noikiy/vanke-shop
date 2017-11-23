<?php
/**
 * 商家入驻
 *
 *
 *
 ***/




class store_joininControl extends BaseHomeControl {

    private $joinin_detail = NULL;

	public function __construct() {
            
		parent::__construct();

		Tpl::setLayout('store_joinin_layout');

        $this->checkLogin();
//        $model_seller = Model('seller');
//        $seller_info = $model_seller->getSellerInfo(array('member_id' => $_SESSION['member_id']));
//		if(!empty($seller_info)) {
//            @header('location: index.php?act=seller_login');
//		}
        if($_GET['op'] != 'check_seller_name_exist' && $_GET['op'] != 'checkname' && $_GET['op'] != 'ecrz'  && $_GET['op'] != 'send_email'  &&  $_GET['op'] != 'callbackecmail'  &&  $_GET['op'] != 'ckrz' && $_GET['op'] != 'ckinfo' && $_GET['op'] != 'ckedit') {
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
        
        //获取用户是否已经认证过如果认证通过则进入选择 用户是继续认证还是开店申请
        Tpl::output('show_sign','joinin');
        $model_store_joinin = Model('store_joinin');
        $joinin_detail_where['member_id'] = $_SESSION['member_id'];
        $joinin_array = $model_store_joinin->where($joinin_detail_where)->select();
        if(!empty($joinin_array)){
            //当数据为空说明用户未录入数据，则前往协议页面
/*             $this->step0Op();
        }else { */
            //如果当前只存在一条认证记录
            if(count($joinin_array) == 1){
                $joinin_detail = $joinin_array[0];
                //判定邮箱认证状态
                $supplier_data =
                //判定认证的状态
                $this->joinin_detail = $joinin_detail;
                switch (intval($joinin_detail['joinin_state'])) {
                    case STORE_JOIN_STATE_NEW:
                        $this->step4();
                        $this->show_join_message('入驻申请已经提交，请等待管理员审核', FALSE, '3');
                        break;
                    case STORE_JOIN_STATE_RZ:
                        Tpl::output('rzsuccess',5);
                        $this->show_join_message('已经提交，请等待管理员审核您的认证资料', FALSE, '2');
                        break;
                    case STORE_JOIN_STATE_RZSUCCESS:
                        Tpl::output('rzsuccess',6);
                        $this->show_join_message('认证成功</br>
                        <a href="'.SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=ckrz">查看认证</a></br>
                        <a href="'.SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=ecrz">继续认证</a></br>
                        <a href="'.SHOP_SITE_URL.DS.'index.php?act=store_join&op=index">申请开店</a>', FALSE, '4');
                        break;
                    case STORE_JOIN_STATE_EMAIL:
                        Tpl::output('rzsuccess',5);
                        $_SESSION['city'] = $joinin_detail['city_center'];
                        $this->show_join_message('已经提交，请验证您的邮箱！ <a href="'.SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=callbackecmail&city='.$joinin_detail['city_center'].'">回退修改资料</a>  <a href="'.SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=send_email">重新发送邮件</a>', FALSE, '2');
                        break;
                    case STORE_JOIN_STATE_CALLBACK:
                        if(!in_array($_GET['op'], array('step1', 'step2', 'step3', 'step4'))) {
                            Tpl::output('rzsuccess',5);
                            $_SESSION['city'] = $joinin_detail['city_center'];
                            $this->show_join_message('已经提交，请验证您的邮箱！ <a href="'.SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=callbackecmail&city='.$joinin_detail['city_center'].'">回退修改资料</a>  <a href="'.SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=send_email">重新发送邮件</a>', SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=step1', '2');
                        }
                        break;
                    case STORE_JOIN_STATE_VERIFY_FAIL:
                        if(!in_array($_GET['op'], array('step1', 'step2', 'step3', 'step4'))) {
                            $this->show_join_message('审核失败:'.$joinin_detail['joinin_message'], SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=step1');
                        }
                        break;
                    case STORE_JOIN_STATE_FNO:
                        if(!in_array($_GET['op'], array('step1', 'step2', 'step3', 'step4'))) {
                            $this->show_join_message('审核拒绝:'.$joinin_detail['joinin_message'], SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=step1');
                        }
                        break;
                }
            }else{
                Tpl::output('rzsuccess',5);
                $this->show_join_message('已经提交，请等待管理员审核</br>
                 <a href="'.SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=ckrz">查看认证</a></br>
                 <a href="'.SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=ecrz">继续认证</a></br>
                 <a href="'.SHOP_SITE_URL.DS.'index.php?act=store_join&op=index">申请开店</a>', FALSE, '2');
            }
        }
    }
    

	public function indexOp() {
        $this->step0Op();
	}
        

    public function step0Op() {
        $model_document = Model('document');
        $document_info = $model_document->getOneByCode('open_store');
        Tpl::output('agreement', $document_info['doc_content']);
        Tpl::output('step', '0');
        Tpl::output('sub_step', 'step0');
        Tpl::showpage('store_joinin_apply');
        exit;
    }

    public function step1Op() {
        
        $model = Model();
        //如果是认证不通过的则输出内容，如果是新认证的则保持原先动作  (作废)
        //如果是新注册，一定没有认证成功的，如果是二次认证，则去掉第一次认证的城市，如果是被拒绝，则可以继续认证，输出原有信息 (AUTH:Akia)
        $where['member_id'] = $_SESSION['member_id'];
        $id = STORE_JOIN_STATE_RZSUCCESS;
        $where['joinin_state'] = array('not in',$id);
        $store_join_sz = $model->table('store_joinin')->where($where)->find();
        //判断当前是否认证第2个城市公司
        if(empty($store_join_sz)){
            $where_sz['member_id'] = $_SESSION['member_id'];
            $where_sz['joinin_state'] = STORE_JOIN_STATE_RZSUCCESS;
            $store_join_sz = $model->table('store_joinin')->where($where_sz)->find();
        }
        if($store_join_sz){
//           //获取城市中心地址
            //当前用户除了被拒绝的认证不允许继续认证   (作废)
            //如果有信息，则去掉认证了的城市公司，被拒绝的则继续输出  (AUTH:Akia)
            //当前用户被拒绝开店的认证不允许继续认证
            $city_where_join['member_id'] = $_SESSION['member_id'];
            $city_where_join['joinin_state'] = STORE_JOIN_STATE_RZSUCCESS;
            $city = $model->table('store_joinin')->where($city_where_join)->field('city_center')->select();
            if($city){
                $cityid = $city[0]['city_center'];
            }
            
            for($i=1;$i<sizeof($city);$i++){
                $cityid .= ",".$city[$i]['city_center'];
            }
            
            $city_center_where['id'] = array('not in',$cityid);
            $city_center_where['city_state'] = 1;
            $city_center =  $model->table('city_centre')->where($city_center_where)->select();
            
            //获取商户信息数据
            $supplier_data = $model->table('supplier')->where("member_id = '".$_SESSION['member_id']."'")->find();
            Tpl::output('data_rz',$supplier_data);
 
        }else{
            //获取城市中心地址
            //当前用户被拒绝开店的认证不允许继续认证  
            $city_where_join['member_id'] = $_SESSION['member_id'];
            $city_where_join['joinin_state'] = array('neq','NULL');
            $city = $model->table('store_joinin')->where($city_where_join)->field('city_center')->select();
            if($city){
                $cityid = $city[0]['city_center'];
            }
            for($i=1;$i<sizeof($city);$i++){
                $cityid .= ",".$city[$i]['city_center'];
            }
            $city_center_where['id'] = array('not in',$cityid);
            $city_center_where['city_state'] = 1;
            $city_center =  $model->table('city_centre')->order('id desc')->where($city_center_where)->select();
        
        }
        Tpl::output('city', $city_center);
        Tpl::output('step', '1');
        Tpl::output('sub_step', 'step1');
        Tpl::showpage('store_joinin_apply');
        exit;
    }
    

    public function step2Op() {
        
        $model = Model();
        //如果是认证不通过的则输出内容，如果是新认证的则保持原先动作
        $where['member_id'] = $_SESSION['member_id'];
        $id = STORE_JOIN_STATE_RZSUCCESS;
        $where['joinin_state'] = array('not in',$id);
        $store_join_sz = $model->table('store_joinin')->where($where)->find();
        if($store_join_sz){
            //获取商户信息数据
            $supplier_data = $model->table('supplier')->where("member_id = '".$_SESSION['member_id']."'")->find();
            Tpl::output('data_rz',$supplier_data);
        }
        
        if(!empty($_POST)) {
            $param = array();
            $param['member_name'] = $_SESSION['member_name'];
            $param['company_name'] = $_POST['company_name'];
            $param['company_province_id'] = intval($_POST['province_id']);
            $param['company_address'] = $_POST['company_address'];
            $param['company_address_detail'] = $_POST['company_address_detail'];
            $param['company_phone'] = $_POST['company_phone'];
            $param['company_employee_count'] = intval($_POST['company_employee_count']);
            $param['company_registered_capital'] = intval($_POST['company_registered_capital']);
            $param['contacts_name'] = $_POST['contacts_name'];
            $param['contacts_phone'] = $_POST['contacts_phone'];
            $param['contacts_email'] = $_POST['contacts_email'];
            
            $param['business_licence_number'] = $_POST['business_licence_number'];
            $param['business_licence_address'] = $_POST['business_licence_address'];
            $param['business_licence_start'] = $_POST['business_licence_start'];
            $param['business_licence_end'] = $_POST['business_licence_end'];
            $param['business_sphere'] = $_POST['business_sphere'];
            if(!empty($_FILES['tax_registration_certificate_electronic']['name'])){
                $param['tax_registration_certificate_electronic'] = $this->upload_image('tax_registration_certificate_electronic');
            }
            if(!empty($_FILES['business_licence_number_electronic']['name'])){
                $param['business_licence_number_electronic'] = $this->upload_image('business_licence_number_electronic');
            }
            if(!empty($_FILES['organization_code_electronic']['name'])){
                $param['organization_code_electronic'] = $this->upload_image('organization_code_electronic');
            }
            if(!empty($_FILES['general_taxpayer']['name'])){
            $param['general_taxpayer'] = $this->upload_image('general_taxpayer');
            }
            $param['organization_code'] = $_POST['organization_code'];
            $param['tax_registration_certificate'] = $_POST['tax_registration_certificate'];
            
            
            $param['taxpayer_id'] = $_POST['taxpayer_id'];
            
            if($_POST['is_therea'] == 1 ){
                $param['is_therea'] = $_POST['is_therea'];
            }
            if($_POST['is_taxpayer'] == 1 ){
                $param['is_taxpayer'] = $_POST['is_taxpayer'];
            }
            //添加后把城市中心添加到session
            $_SESSION['city']= intval($_POST['city_centre']);

            $model_store_joinin = Model('store_joinin');
            $model = Model();
            $sql = "SELECT * from sc_store_joinin WHERE member_id=".$_SESSION['member_id']." limit 0,1";
            $joinin_info = $model->query($sql);
            if($joinin_info){
                //获取邮箱地址
                $sup_data = $model->table('supplier')->where("member_id = '".$_SESSION['member_id']."'")->find();
                //判断是否需要发送邮件
                if($sup_data['contacts_email'] != $param['contacts_email']){
                    $_SESSION['if_send_email'] = 1;
                }else{
                    $_SESSION['if_send_email'] = 2;
                }
                $is_ture = $model->table("supplier")->where("member_id = '".$_SESSION['member_id']."'")->update($param);
                if($is_ture){
                    $model->table("store_joinin")->where("member_id = '".$_SESSION['member_id']."'")->update(array('city_center'=>intval($_POST['city_centre'])));
                }
            }else{
                $_SESSION['if_send_email'] = 1;
                //$param['member_id'] = $_SESSION['member_id'];
                $param['add_time'] = time();
                $param['add_user'] = $_SESSION['member_id'];
                $insert = $model->table("supplier")->where("member_id = '".$_SESSION['member_id']."'")->update($param);
                if($insert){
                   $join_param = array(
                       'member_id'      =>$_SESSION['member_id'],
                       'member_name'    =>$param['member_name'],
                       'seller_name'    =>$param['member_name'],
                       'city_center'    =>intval($_POST['city_centre']),
                   );
                   $model_store_joinin->save($join_param);
                }
            }
            
        }
        
        
        Tpl::output('step', '2');
        Tpl::output('sub_step', 'step2');
        Tpl::showpage('store_joinin_apply');
        exit;
    }
    

    private function step2_save_valid($param) {
        $obj_validate = new Validate();
        $obj_validate->validateparam = array(
            array("input"=>$param['company_name'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>"公司名称不能为空且必须小于50个字"),
            array("input"=>$param['company_address'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>"公司地址不能为空且必须小于50个字"),
            array("input"=>$param['company_address_detail'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>"公司详细地址不能为空且必须小于50个字"),
            //array("input"=>$param['company_phone'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"20","message"=>"公司电话不能为空"),
            //array("input"=>$input['company_employee_count'], "require"=>"true","validator"=>"Number","员工总数不能为空且必须是数字"),
            array("input"=>$param['company_registered_capital'], "require"=>"true","validator"=>"Number","注册资金不能为空且必须是数字"),
            array("input"=>$param['contacts_name'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"20","message"=>"联系人姓名不能为空且必须小于20个字"),
            array("input"=>$param['contacts_phone'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"20","message"=>"联系人电话不能为空"),
            array("input"=>$param['contacts_email'], "require"=>"true","validator"=>"email","message"=>"电子邮箱不能为空"),
            array("input"=>$param['business_licence_number'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"20","message"=>"营业执照号不能为空且必须小于20个字"),
            array("input"=>$param['business_licence_address'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>"营业执照所在地不能为空且必须小于50个字"),
            array("input"=>$param['business_licence_start'], "require"=>"true","message"=>"营业执照有效期不能为空"),
            array("input"=>$param['business_licence_end'], "require"=>"true","message"=>"营业执照有效期不能为空"),
            //array("input"=>$param['business_sphere'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"500","message"=>"法定经营范围不能为空且必须小于50个字"),
            array("input"=>$param['business_licence_number_electronic'], "require"=>"true","message"=>"营业执照电子版不能为空"),
            //array("input"=>$param['organization_code'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"20","message"=>"组织机构代码不能为空且必须小于20个字"),
           // array("input"=>$param['organization_code_electronic'], "require"=>"true","message"=>"组织机构代码电子版不能为空"),
        );
        $error = $obj_validate->validate();
        if ($error != ''){
            showMessage($error);
        }
    }

    public function step3Op() {
        if(!empty($_POST)) {
            $param = array();
            $param['bank_account_name'] = $_POST['bank_account_name'];
            $param['bank_account_number'] = $_POST['bank_account_number'];
            $param['bank_name'] = $_POST['bank_name'];
            $param['bank_code'] = $_POST['bank_code'];
            $param['bank_address'] = $_POST['bank_address'];
            
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
            $model = Model();

            $this->step3_save_valid($param);

            $model_store_joinin = Model('store_joinin');
            $where_joinin['member_id'] = $_SESSION['member_id'];
            $where_joinin['city_center'] = $_SESSION['city'];
            //跟新商户主表数据
            $res = $model->table("supplier")->where("member_id = '".$_SESSION['member_id']."'")->update($param);
        }

        //商品分类
        $gc	= Model('goods_class');
        $gc_list	= $gc->getGoodsClassListByParentId(0);
        Tpl::output('gc_list',$gc_list);

        //店铺等级
		$grade_list = rkcache('store_grade',true);
		//附加功能
		if(!empty($grade_list) && is_array($grade_list)){
			foreach($grade_list as $key=>$grade){
				$sg_function = explode('|',$grade['sg_function']);
				if (!empty($sg_function[0]) && is_array($sg_function)){
					foreach ($sg_function as $key1=>$value){
						if ($value == 'editor_multimedia'){
							$grade_list[$key]['function_str'] .= '富文本编辑器';
						}
					}
				}else {
					$grade_list[$key]['function_str'] = '无';
				}
			}
		}
		Tpl::output('grade_list', $grade_list);

        //店铺分类
        $model_store = Model('store_class');
        $store_class = $model_store->getStoreClassList(array(),'',false);
        Tpl::output('store_class', $store_class);
        //店铺类型
        $model_store_type = Model('store_type');
        $store_type = $model_store_type->getStoreClassList(array(),'',false);
        Tpl::output('store_type', $store_type);

        Tpl::output('step', '3');
        Tpl::output('sub_step', 'step3');
        Tpl::showpage('store_joinin_apply');
        exit;
    }

    private function step3_save_valid($param) {
        $obj_validate = new Validate();
        $obj_validate->validateparam = array(
            array("input"=>$param['bank_account_name'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>"银行开户名不能为空且必须小于50个字"),
            array("input"=>$param['bank_account_number'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"25","message"=>"银行账号不能为空且必须小于25个字"),
            array("input"=>$param['bank_name'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>"开户银行支行名称不能为空且必须小于50个字"),
            //array("input"=>$param['bank_code'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"20","message"=>"支行联行号不能为空且必须小于20个字"),
            array("input"=>$input['bank_address'], "require"=>"true","开户行所在地不能为空"),
            //array("input"=>$input['bank_licence_electronic'], "require"=>"true","开户银行许可证电子版不能为空"),
            array("input"=>$param['settlement_bank_account_name'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>"银行开户名不能为空且必须小于50个字"),
            array("input"=>$param['settlement_bank_account_number'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"25","message"=>"银行账号不能为空且必须小于25个字"),
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

    public function check_seller_name_existOp() {
        $condition = array();
        $condition['seller_name'] = $_GET['seller_name'];

        $model_seller = Model('seller');
        $result = $model_seller->isSellerExist($condition);

        if($result) {
            echo 'true';
        } else {
            echo 'false';
        }
    }


    public function step4Op() {
	    $model = Model();
        if(!empty($_POST)) {
            $param = array();
            $param['bank_account_name'] = $_POST['bank_account_name'];
            $param['bank_account_number'] = $_POST['bank_account_number'];
            $param['bank_name'] = $_POST['bank_name'];
            $param['bank_code'] = $_POST['bank_code'];
            $param['bank_address'] = $_POST['bank_address'];
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
            $city = $_SESSION['city'];
            $this->step3_save_valid($param);

            $model_store_joinin = Model('store_joinin');
            
            $where_joinin['member_id'] = $_SESSION['member_id'];
            
            $is_callback_where['member_id'] = $_SESSION['member_id'];
            $is_callback_where['joinin_state'] = STORE_JOIN_STATE_CALLBACK;
            //判断是否是回退数据
            $is_callback = $model_store_joinin->field('member_id')->where($is_callback_where)->find();
            if($is_callback){
                $where_joinin['joinin_state'] = STORE_JOIN_STATE_EMAIL;
            }
            $model = Model();
            $up_parm = $model->table("supplier")->where("member_id = '".$_SESSION['member_id']."'")->update($param);
            

        
        $store_class_ids = array();
        $store_class_names = array();
        if(!empty($_POST['store_class_ids'])) {
            foreach ($_POST['store_class_ids'] as $value) {
                $store_class_ids[] = $value;
            }
        }
        if(!empty($_POST['store_class_names'])) {
            foreach ($_POST['store_class_names'] as $value) {
                $store_class_names[] = $value;
            }
        }
        //取最小级分类最新分佣比例
        $sc_ids = array();
        foreach ($store_class_ids as $v) {
            $v = explode(',',trim($v,','));
            if (!empty($v) && is_array($v)) {
                $sc_ids[] = end($v);
            }
        }
        if (!empty($sc_ids)) {
            $store_class_commis_rates = array();
            $goods_class_list = Model('goods_class')->getGoodsClassListByIds($sc_ids);
            if (!empty($goods_class_list) && is_array($goods_class_list)) {
                $sc_ids = array();
                foreach ($goods_class_list as $v) {
                    $store_class_commis_rates[] = $v['commis_rate'];
                }
            }
        }
        $param_join = array();
        //$param_join['seller_name'] = $_POST['seller_name'];
        $param_join['store_name'] = $_POST['store_name'];
        $param_join['store_class_ids'] = serialize($store_class_ids);
        $param_join['store_class_names'] = serialize($store_class_names);
        $param_join['joinin_year'] = intval($_POST['joinin_year']);
        
         //判断是否 回退数据还是新增数据
//        if($_SESSION['rz_callback'] == 1){  
//            $param['joinin_state'] = STORE_JOIN_STATE_CALLBACK;//STORE_JOIN_STATE_RZ
//        }else{
//            $param['joinin_state'] = STORE_JOIN_STATE_EMAIL;
//        }
        //重新提交申请状态改为认证申请
            
        if(is_array($store_class_commis_rates)){
            $param_join['store_class_commis_rates'] = implode(',', $store_class_commis_rates);
        }else{
            $param_join['store_class_commis_rates'] =  $store_class_commis_rates;
        }
        

        //取店铺等级信息
        $grade_list = rkcache('store_grade',true);
        if (!empty($grade_list[$_POST['sg_id']])) {
            $param_join['sg_id'] = $_POST['sg_id'];
            $param_join['sg_name'] = $grade_list[$_POST['sg_id']]['sg_name'];
            $param_join['sg_info'] = serialize(array('sg_price' => $grade_list[$_POST['sg_id']]['sg_price']));
        }

        //取最新店铺分类信息
        $store_class_info = Model('store_class')->getStoreClassInfo(array('sc_id'=>intval($_POST['sc_id'])));
        if ($store_class_info) {
            $param_join['sc_id'] = $store_class_info['sc_id'];
            $param_join['sc_name'] = $store_class_info['sc_name'];
            $param_join['sc_bail'] = $store_class_info['sc_bail'];
        }

        //cary_add   添加代理ID
        if(!empty($_POST['agent_id'])){
            $param_join['agent_id'] = $_POST['agent_id'];
        }
        //增加店铺类型
        $param_join['store_type_id'] = $_POST['st_id'];
        $param_join['store_type_name'] = $_POST['st_name'];

        //店铺认证状态
//        $param['store_state'] = 0;
        //店铺应付款
        $param_join['paying_amount'] = floatval($grade_list[$_POST['sg_id']]['sg_price'])*$param['joinin_year']+floatval($param['sc_bail']);
        
        if($_POST['is_rz'] != 1 ){
            $this->step4_save_valid($param_join);
        }

        $param_join['joinin_state'] = STORE_JOIN_STATE_EMAIL;

        //保存数据
        $update_where_step4['city_center'] = $_SESSION['city'];
        $update_where_step4['member_id'] = $_SESSION['member_id'];
        $if_sumbit = $model->table("store_joinin")->where($update_where_step4)->update($param_join);
        //发送邮箱认证
        if($if_sumbit){
            $this->send_emailOp();
        }
        }
        @header('location: index.php?act=store_joinin');

    }
    
    public function send_emailOp(){
            if(!$_SESSION['member_id']){
                $this->show_join_message('请先登录！', SHOP_SITE_URL.DS.'index.php?act=login');
            }
            //发送邮件，验证状态
            /*$model_store_joinin = Model('store_joinin');
            $where_emaildata['city_center'] = $_SESSION['city'];
            $where_emaildata['member_id'] = $_SESSION['member_id'];
            $email_cont = $model_store_joinin->where($where_emaildata)->field('contacts_email')->find();*/

            $model = Model();
            $where_emaildata = "member_id = '".$_SESSION['member_id']."' and supplier_state = '1'";
            $email_cont = $model->table("supplier")->where($where_emaildata)->field('contacts_email')->find();
            
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
            $verify_url = SHOP_SITE_URL.'/index.php?act=check_rzemail&op=bind_email&uid='.$uid.'&hash='.md5($seed).'&city='.$_SESSION['city'];

            $model_tpl = Model('mail_templates');
            $tpl_info = $model_tpl->getTplInfo(array('code'=>'bind_email'));
            $param = array();
            $param['site_name']	= C('site_name');
            $param['user_name'] = $_SESSION['member_name'];
            $param['verify_url'] = $verify_url;
            $subject	= "供应商认证验证邮箱";
            $message	= ncReplaceText($tpl_info['content'],$param);
            $email = new MySendMail();
            $result	= $email->send_sys_email($email_cont["contacts_email"],$subject,$message);
            if($result != false){
                showDialog('邮件发送成功，请及时认证！','index.php?act=store_joinin');
            }else{
                showDialog('发送失败,请联系管理员','index.php?act=store_joinin');
            }
    }
    private function step4_save_valid($param) {
        $obj_validate = new Validate();
        $obj_validate->validateparam = array(
            array("input"=>$param['store_name'], "require"=>"true","validator"=>"Length","min"=>"1","max"=>"50","message"=>"店铺名称不能为空且必须小于50个字"),
            array("input"=>$param['sg_id'], "require"=>"true","message"=>"店铺等级不能为空"),
            array("input"=>$param['store_type_id'], "require"=>"true","message"=>"店铺类型不能为空"),
            array("input"=>$param['sc_id'], "require"=>"true","message"=>"店铺分类不能为空"),
        );
        $error = $obj_validate->validate();
        if ($error != ''){
            showMessage($error);
        }
    }

    public function payOp() {
        if (!empty($this->joinin_detail['sg_info'])) {
            $store_grade_info = Model('store_grade')->getOneGrade($this->joinin_detail['sg_id']);
            $this->joinin_detail['sg_price'] = $store_grade_info['sg_price'];
        } else {
            $this->joinin_detail['sg_info'] = @unserialize($this->joinin_detail['sg_info']);
            if (is_array($this->joinin_detail['sg_info'])) {
                $this->joinin_detail['sg_price'] = $this->joinin_detail['sg_info']['sg_price'];
            }
        }
        Tpl::output('joinin_detail', $this->joinin_detail);
        Tpl::output('step', '4');
        Tpl::output('sub_step', 'pay');
        Tpl::showpage('store_joinin_apply');
        exit;
    }

    public function pay_saveOp() {
        $param = array();
        $param['paying_money_certificate'] = $this->upload_image('paying_money_certificate');
        $param['paying_money_certificate_explain'] = $_POST['paying_money_certificate_explain'];
        $param['joinin_state'] = STORE_JOIN_STATE_PAY;

        if(empty($param['paying_money_certificate'])) {
            showMessage('请上传付款凭证','','','error');
        }

        $model_store_joinin = Model('store_joinin');
        $model_store_joinin->modify($param, array('member_id'=>$_SESSION['member_id']));

        @header('location: index.php?act=store_joinin');
    }

    private function step4() {
        $model_store_joinin = Model('store_joinin');
        $joinin_detail = $model_store_joinin->getOne(array('member_id'=>$_SESSION['member_id']));
        $joinin_detail['store_class_ids'] = unserialize($joinin_detail['store_class_ids']);
        $joinin_detail['store_class_names'] = unserialize($joinin_detail['store_class_names']);
        $joinin_detail['store_class_commis_rates'] = explode(',', $joinin_detail['store_class_commis_rates']);
        $joinin_detail['sg_info'] = unserialize($joinin_detail['sg_info']);
        Tpl::output('joinin_detail',$joinin_detail);
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
    
    public function callbackecmailOp(){
        //修改审核资料
        $model = Model();
        $city_id = htmlspecialchars($_GET['city']);
        $where['member_id'] = $_SESSION['member_id'];
        $where['city_center'] = $city_id;
        $store_joinin = $model->table('store_joinin')->where($where)->find();
        if($store_joinin['joinin_state'] == STORE_JOIN_STATE_EMAIL){
            //回退修改资料变成可修改状态
            $updata['joinin_state'] = STORE_JOIN_STATE_CALLBACK;
            $if_state = $model->table('store_joinin')->where($where)->update($updata);
            if($if_state != false){
                $_SESSION['rz_callback']  = 1;
                showDialog('回退成功，请及时修改！','index.php?act=store_joinin&op=step1');
            }else{
                showDialog('回退失败，请稍后尝试！');
            }
        }else{
            @header('location: index.php?act=store_joinin');
        }
        
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

	/**
	 * 检查店铺名称是否存在
	 *
	 * @param
	 * @return
	 */
	public function checknameOp() {
		/**
		 * 实例化卖家模型
		 */
		$model_store	= Model('store');
		$store_name = $_GET['store_name'];
		$store_info = $model_store->getStoreInfo(array('store_name'=>$store_name));
		if(!empty($store_info['store_name']) && $store_info['member_id'] != $_SESSION['member_id']) {
			echo 'false';
		} else {
			echo 'true';
		}
	}
        
        /**
	 * 二次认证城市中心
	 *
	 * @param
	 * @return
	 */
	public function ecrzOp() {
            //判定似乎否存在首次认证的城市中心，并且所认证的城市中心需要通过审核
            $model = Model();         
            $where = "member_id = '".$_SESSION['member_id']."' and first_city_id = city_center";
            $store_join_info = $model->table('store_joinin')->where($where)->find();
            if(!empty($store_join_info['joinin_state']) && ($store_join_info['joinin_state'] == 44)){
                //获取商户信息数据
                $supper = $model->table('supplier')->where("member_id = '".$_SESSION['member_id']."'")->find();
                //当前用户被拒绝开店的认证不允许继续认证
                $where_join = "member_id = '".$_SESSION['member_id']."' and joinin_state in('".STORE_JOIN_STATE_RZSUCCESS."','".STORE_JOIN_STATE_RZ."')";
                //$city_where_join['member_id'] = $_SESSION['member_id'];
                //$city_where_join['joinin_state'] = STORE_JOIN_STATE_RZSUCCESS;
                $city = $model->table('store_joinin')->where($where_join)->field('city_center')->select();
                if($city){
                    $cityid = $city[0]['city_center'];
                }
                for($i=1;$i<sizeof($city);$i++){
                    $cityid .= ",".$city[$i]['city_center'];
                }
                
                $city_center_where['id'] = array('not in',$cityid);
                $city_center_where['city_state'] = 1;
                $city_center =  $model->table('city_centre')->where($city_center_where)->select();
                
                Tpl::output('city', $city_center);
                
                if(!empty($_POST['city_centre']) && is_array($_POST['city_centre'])){
                    $model->beginTransaction();
                    $model_store_joinin = Model('store_joinin');
                    //如果用户提交，则进行赋值插入新的二次认证数据
                    $joinin = $model_store_joinin->where('member_id='.$_SESSION['member_id'])->find();
                    $joinin['joinin_state'] = 43;
                    $joinin['store_state']  = 0;
                    $rest_data = $rest_info = array();
                    //差异数据组装构建
                    $supper_info = array(
                        "member_id"             => $_SESSION['member_id'],
                        "city_contacts_name"    => empty($_POST['city_names']) ? $supper['contacts_name']:$_POST['city_names'],
                        "city_contacts_phone"   => empty($_POST['city_phones']) ? $supper['contacts_phone']:$_POST['city_phones'],
                    );
                    foreach ($_POST['city_centre'] as $city_id){
                        $joinin['city_center']  = $city_id;
                        $joinin['contract_type']  = 3;
                        $joinin['contract_res']  = '';
                        $joinin['contract_time']  = '';
                        $joinin['purchase_type']  = 3;
                        $joinin['purchase_res']  = '';
                        $joinin['purchase_time']  = '';
                        //处理认证记录数据
                        $join_list = $model->table("store_joinin")->where("member_id = '".$_SESSION['member_id']."' and city_center = '".$city_id."'")->find();
                        if(empty($join_list)){
                            $insert_into = $model_store_joinin->save($joinin);
                        }else{
                            $insert_into = $model->table("store_joinin")->where("member_id = '".$_SESSION['member_id']."' and city_center = '".$city_id."'")->update(array("joinin_state"=>43));
                        }
                        if($insert_into){
                            $sup_info = $model->table("supplier_information")->where("member_id = '".$_SESSION['member_id']."' and join_city = '".$city_id."'")->find();
                            //处理差异信息记录数据
                            if(empty($sup_info)){
                                $supper_info['join_city'] = $city_id;
                                $model->table("supplier_information")->insert($supper_info);
                            }else{
                                $model->table("supplier_information")->where("id = '".$sup_info['id']."'")->update($supper_info);
                            }
                        }
                        $rest_data[] = $insert_into ? 1:2;
                    }
                    if(!in_array(2, $rest_data)){
                        $model->commit();
                        @header('location: index.php?act=store_joinin');
                    }else{
                        $model->rollback();
                    }
                }
                Tpl::output('step', '5');
                Tpl::output('sub_step', 'step5');
                Tpl::showpage('store_joinin_apply');
            }else{
                @header('location: index.php?act=store_joinin');
            }
	}
	
	

	/**
	 *
	 * 查看认证记录数据
	 **/
	public function ckrzOp(){
	    if($_SESSION['member_id'] > 0){
	        $model = Model();
	        //获取供应商认证信息记录数据
	        $stor_info = $model->table('supplier')->where("member_id = '".$_SESSION['member_id']."'")->field('company_name,contacts_name,contacts_phone')->find();
	        //查询申请记录表
	        $filed = 'city_center,joinin_state,first_city_id,store_state,store_name,join_city,city_contacts_name,city_contacts_phone';
            $on = 'store_joinin.city_center=supplier_information.join_city and store_joinin.member_id = supplier_information.member_id';
	        $where = "store_joinin.member_id = '".$_SESSION['member_id']."'";
            //$where['member_id'] = $_SESSION['member_id'];
	        $rz_log = $model->table('store_joinin,supplier_information')->join('left')->on($on)->field($filed)->where($where)->select();
	        $cache_news = new CacheFile();
	        $cache_info_list = $cache_news->get('city_name');
	        foreach($rz_log as $key=>$r_rows){
	            foreach($cache_info_list as $c_rows){
	                if($r_rows['city_center'] == $c_rows['id']) {
                        $rz_log[$key]['city_name'] = $c_rows['city_name'];
                    }
	            }
	        }
	        $edit_state = $model->table('store_joinin_edit')->field("joinin_state")->where("member_id = '" . $_SESSION['member_id'] . "'")->find();
            Tpl::output('edit_state',$edit_state['joinin_state']);
            Tpl::output('stor_info',$stor_info);
	        Tpl::output('log',$rz_log);
	       Tpl::showpage('store_joinin_ckrz');
	    }else{
	        showMessage("请先登录");
	    }
	}


	/**
     * 查看认证申请记录的详细信息
	 **/
	public function ckinfoOp(){
        if($_SESSION['member_id'] > 0){
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
        }else{
            showMessage("请先登录");
        }
    }


    /**
     * 修改认证申请记录的详细信息
     **/
    public function ckeditOp(){
        if($_SESSION['member_id'] > 0){
            $city_id = intval($_GET['id']);
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
        }else{
            showMessage("请先登录");
        }
    }
	
        
}
