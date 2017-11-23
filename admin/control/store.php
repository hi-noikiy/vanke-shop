<?php
/**
 * 店铺管理界面
 *
 ***/



class storeControl extends SystemControl{
	const EXPORT_SIZE = 1000;
	public function __construct(){
		parent::__construct();
		Language::read('store,store_grade');
	}

	/**
	 * 店铺
	 */
	public function storeOp(){
		$lang = Language::getLangContent();

		$model_store = Model('store');

		if(trim($_GET['owner_and_name']) != ''){
			$condition['member_name']	= array('like', '%'.$_GET['owner_and_name'].'%');
			Tpl::output('owner_and_name',$_GET['owner_and_name']);
		}
		if(trim($_GET['store_name']) != ''){
			$condition['store_name']	= array('like', '%'.trim($_GET['store_name']).'%');
			Tpl::output('store_name',$_GET['store_name']);
		}
		if(intval($_GET['grade_id']) > 0){
			$condition['grade_id']		= intval($_GET['grade_id']);
			Tpl::output('grade_id',intval($_GET['grade_id']));
		}
                //获取当前登录后台管理员 城市中心地区
                $admininfo = $this->getAdminInfo();
                if($admininfo['cityid'] > 0){
                    $condition['first_city_id']		= intval($admininfo['cityid']);
                }
        switch ($_GET['store_type']) {
            case 'close':
                $condition['store_state'] = 0;
                break;
            case 'open':
                $condition['store_state'] = 1;
                break;
            case 'expired':
                $condition['store_end_time'] = array('between', array(1, TIMESTAMP));
                $condition['store_state'] = 1;
                break;
            case 'expire':
                $condition['store_end_time'] = array('between', array(TIMESTAMP, TIMESTAMP + 864000));
                $condition['store_state'] = 1;
                break;
        }

        // 默认店铺管理不包含自营店铺
        $condition['is_own_shop'] = 0;

		//店铺列表
		$store_list = $model_store->getStoreList($condition, 10,'store_id desc');
		//店铺等级
		$model_grade = Model('store_grade');
		$grade_list = $model_grade->getGradeList($condition);
		if (!empty($grade_list)){
			$search_grade_list = array();
			foreach ($grade_list as $k => $v){
				$search_grade_list[$v['sg_id']] = $v['sg_name'];
			}
		}
        Tpl::output('search_grade_list', $search_grade_list);

		Tpl::output('grade_list',$grade_list);
		Tpl::output('store_list',$store_list);
        Tpl::output('store_type', $this->_get_store_type_array());
		Tpl::output('page',$model_store->showpage('2'));
		Tpl::showpage('store.index');
	}
	
    private function _get_store_type_array() {
        return array(
            'open' => '开启',
            'close' => '关闭',
            'expire' => '即将到期',
            'expired' => '已到期'
        );
    }
	/**
	 * 店铺编辑
	 */
	public function store_editOp(){
		$lang = Language::getLangContent();

		$model_store = Model('store');
		//保存
		if (chksubmit()){
			//取店铺等级的审核
			$model_grade = Model('store_grade');
			$grade_array = $model_grade->getOneGrade(intval($_POST['grade_id']));
			if (empty($grade_array)){
				showMessage($lang['please_input_store_level']);
			}
			//结束时间
			$time	= '';
			if(trim($_POST['end_time']) != ''){
				$time = strtotime($_POST['end_time']);
			}
			$update_array = array();
			$update_array['store_name'] = trim($_POST['store_name']);
			$update_array['sc_id'] = intval($_POST['sc_id']);
			$update_array['grade_id'] = intval($_POST['grade_id']);

            //店铺类型
            if($_POST['st_id'] != $_POST['old_st_id']){
                $update_array['store_type_id'] = intval($_POST['st_id']);
                $update_array['store_type_name'] = trim($_POST['st_name']);
            }

			$update_array['store_end_time'] = $time;
			$update_array['store_state'] = intval($_POST['store_state']);
			$update_array['store_baozh'] = trim($_POST['store_baozh']);//保障服务开关
			$update_array['store_baozhopen'] = trim($_POST['store_baozhopen']);//保证金显示开关
			$update_array['store_baozhrmb'] = trim($_POST['store_baozhrmb']);//新加保证金-金额
			$update_array['store_qtian'] = trim($_POST['store_qtian']);//保障服务-七天退换
			$update_array['store_zhping'] = trim($_POST['store_zhping']);//保障服务-正品保证
			$update_array['store_erxiaoshi'] = trim($_POST['store_erxiaoshi']);//保障服务-两小时发货
			$update_array['store_tuihuo'] = trim($_POST['store_tuihuo']);//保障服务-退货承诺
			$update_array['store_shiyong'] = trim($_POST['store_shiyong']);//保障服务-试用
			$update_array['store_xiaoxie'] = trim($_POST['store_xiaoxie']);//保障服务-消协
			$update_array['store_huodaofk'] = trim($_POST['store_huodaofk']);//保障服务-货到付款
			$update_array['store_shiti'] = trim($_POST['store_shiti']);//保障服务-实体店铺
			if ($update_array['store_state'] == 0){
				//根据店铺状态修改该店铺所有商品状态
				$model_goods = Model('goods');
				$model_goods->editProducesOffline(array('store_id' => $_POST['store_id']));
				$update_array['store_close_info'] = trim($_POST['store_close_info']);
				$update_array['store_recommend'] = 0;
			}else {
				//店铺开启后商品不在自动上架，需要手动操作
				$update_array['store_close_info'] = '';
				$update_array['store_recommend'] = intval($_POST['store_recommend']);
			}
            $result = $model_store->editStore($update_array, array('store_id' => $_POST['store_id']));
			if ($result){

            //如果修改店铺分类
                            
            if($_POST['sc_id'] != $_POST['old_sc_id']){
                if( intval($_POST['gc_bind']) > 0 ){
                    $is_return = Model('store_bind_class')->editStoreBindClass(array('class_1'=> $_POST['gc_bind']), array('store_id'=> $_POST['store_id']));
                    //修改店铺分类的同时修改,店铺绑定分类,表(store_bind_class)
                    $model = Model();
                    $is_in_bind_class = $model->table('store_bind_class')->where('store_id='.$_POST['store_id'])->find();
                    
                    if(empty($is_in_bind_class)){
                        //判断当前店铺是否绑定过,如果没有这新增,如果有.则更新
                         //插入店铺绑定分类表
                        $store_bind_class_array = array();
                        //插寻当前店铺storejoin
                        //获取当前class分类的二级分类有多少个插入多少条\ 
                        $store_id=$_POST['store_id'];
                        $insert_data['store_id'] = $store_id;
                        $insert_data['commis_rate'] = 0;
                        $insert_data['class_1'] = $_POST['gc_bind'];
                        $insert_data['class_2'] = 0;
                        $insert_data['class_3'] = 0;
                        $insert_data['state'] = 1;
                        $model->table('store_bind_class')->insert($insert_data);
                      
                    }
                }
            }


			//店铺名称修改处理
			$store_id=$_POST['store_id'];
			$store_name=trim($_POST['store_name']);
			$store_info = $model_store->getStoreInfoByID($store_id);
			if(!empty($store_name))
			{
				$where=array();
				$where['store_id']=$store_id;
				$update=array();
				$update['store_name']=$store_name;
				$bllGoods=Model()->table('goods_common')->where($where)->update($update);
				$bllGoods=Model()->table('goods')->where($where)->update($update);
			}
			
			
				$url = array(
				array(
				'url'=>'index.php?act=store&op=store',
				'msg'=>$lang['back_store_list'],
				),
				array(
				'url'=>'index.php?act=store&op=store_edit&store_id='.intval($_POST['store_id']),
				'msg'=>$lang['countinue_add_store'],
				),
				);
				$this->log(L('nc_edit,store').'['.$_POST['store_name'].']',1);
				showMessage($lang['nc_common_save_succ'],$url);
			}else {
				$this->log(L('nc_edit,store').'['.$_POST['store_name'].']',1);
				showMessage($lang['nc_common_save_fail']);
			}
		}
		//取店铺信息
		$store_array = $model_store->getStoreInfoByID($_GET['store_id']);
		if (empty($store_array)){
			showMessage($lang['store_no_exist']);
		}
		//整理店铺内容
		$store_array['store_end_time'] = $store_array['store_end_time']?date('Y-m-d',$store_array['store_end_time']):'';
		//店铺分类
		$model_store_class = Model('store_class');
		$parent_list = $model_store_class->getStoreClassList(array(),'',false);
		//店铺等级
		$model_grade = Model('store_grade');
		$grade_list = $model_grade->getGradeList();

        //店铺类型
        $store_type_list = Model('store_type')->getStoreClassList(array(),'',false);
        Tpl::output('store_type_list', $store_type_list);

		Tpl::output('grade_list',$grade_list);
		Tpl::output('class_list',$parent_list);
		Tpl::output('store_array',$store_array);

		$joinin_detail = Model('store_joinin')->getOne(array('member_id'=>$store_array['member_id']));
        Tpl::output('joinin_detail', $joinin_detail);
		Tpl::showpage('store.edit');
	}

    /**
     * 供应商编辑
     */
    public function supplier_editOp(){
        $lang = Language::getLangContent();
        $joinin_detail = Model('store_joinin')->getOne("store_joinin.member_id = '".$_GET['member_id']."' and city_center = '".$_GET['city']."'");
        if(!empty($joinin_detail)) {
            $member_model = Model('member');
            $model = Model();
            $field = "supplier.member_id,member.member_name,member.member_time,supplier.level,supplier.type_json,supplier.end_time";
            $on = 'supplier.member_id = member.member_id';
            $supply_list = $model->table("supplier,member")->field($field)->join('left')->on($on)->where("supplier.member_id = '" . $_GET['member_id'] . "' and supplier_state = 2")->find();
            //$member_info = $member_model->getMemberInfo(array('member_id'=>$_GET['member_id']),'supply_type_json,supply_level');
            //获取供应商类型数据
            $supplier_type_data = Model("supplier_type")->field('id,type_name')->where(array("level" => 1, "parent_id" => 0))->select();
            $member_supplier_type = json_decode($supply_list['type_json'], true);
            $supplier_type_father = array();
            $supplier_type_sun = array();
            if (!empty($member_supplier_type) && is_array($member_supplier_type)) {
                foreach ($member_supplier_type as $key => $v) {
                    $supplier_type_father[] = $key;
                    foreach ($v as $v_sun) {
                        $supplier_type_sun[] = $v_sun;
                    }
                }
            }
            $supplier_type_list = array();
            if (!empty($supplier_type_data) && is_array($supplier_type_data)) {
                foreach ($supplier_type_data as $val) {
                    $supplier_type_list_data = Model("supplier_type")->field('id,type_name')->where(array("level" => 2, "parent_id" => $val['id']))->select();
                    $supplier_type_list[$val['id']] = $supplier_type_list_data;
                }
            }
            $cy_type = $_GET['city'] == $joinin_detail['first_city_id'] ? 1:2;
            Tpl::output('cy_type', $cy_type);
            //对商户差异信息作出处理
            $supplier_information = $model->table("supplier_information")->where("member_id = '" . $_GET['member_id'] . "' and join_city = '" . $_GET['city'] . "'")->find();
            Tpl::output('information', $supplier_information);
            Tpl::output('city_id', $_GET['city']);
            Tpl::output('supplier_type_father', $supplier_type_father);
            Tpl::output('supplier_type_sun', $supplier_type_sun);
            Tpl::output('supplier_type', $supplier_type_data);
            Tpl::output('supplier_list', $supplier_type_list);
            Tpl::output('supplier_level', $supply_list['level']);
            Tpl::output('joinin_detail', $joinin_detail);
            Tpl::output('supplier_time_data', array('member_time' => $supply_list['member_time'], 'supply_end_time' => $supply_list['end_time']));
            Tpl::showpage('supplier.edit');
        }
    }

    /**
     * 编辑保存注册信息
     */
    public function edit_save_joininOp() {
        if (chksubmit()) {
            $member_id = $_POST['member_id'];
            if ($member_id <= 0) {
                showMessage(L('param_error'));
            }
            $model = Model();
            $param = array();
            //$param['company_name'] = $_POST['company_name'];
            $param['company_province_id'] = intval($_POST['province_id']);
            $param['company_address'] = $_POST['company_address'];
            $param['company_address_detail'] = $_POST['company_address_detail'];
            $param['company_phone'] = $_POST['company_phone'];
            $param['company_employee_count'] = intval($_POST['company_employee_count']);
            $param['company_registered_capital'] = intval($_POST['company_registered_capital']);
            $param['contacts_name'] = $_POST['contacts_name'];
            $param['contacts_phone'] = $_POST['contacts_phone'];
            $param['contacts_email'] = $_POST['contacts_email'];
            //$param['business_licence_number'] = $_POST['business_licence_number'];
            $param['business_licence_address'] = $_POST['business_licence_address'];
            $param['business_licence_start'] = $_POST['business_licence_start'];
            $param['business_licence_end'] = $_POST['business_licence_end'];
            $param['business_sphere'] = $_POST['business_sphere'];
            if ($_FILES['business_licence_number_electronic']['name'] != '') {
                $param['business_licence_number_electronic'] = $this->upload_image('business_licence_number_electronic');
            }
            //$param['organization_code'] = $_POST['organization_code'];组织机构代码
            //组织机构代码证书地址
            //if ($_FILES['organization_code_electronic']['name'] != '') {
            //    $param['organization_code_electronic'] = $this->upload_image('organization_code_electronic');
            //}
            if ($_FILES['general_taxpayer']['name'] != '') {
                $param['general_taxpayer'] = $this->upload_image('general_taxpayer');
            }
            $param['bank_account_name'] = $_POST['bank_account_name'];
            $param['bank_account_number'] = $_POST['bank_account_number'];
            $param['bank_name'] = $_POST['bank_name'];
            $param['bank_code'] = $_POST['bank_code'];
            $param['bank_address'] = $_POST['bank_address'];
            if ($_FILES['bank_licence_electronic']['name'] != '') {
                $param['bank_licence_electronic'] = $this->upload_image('bank_licence_electronic');
            }
            $param['settlement_bank_account_name'] = $_POST['settlement_bank_account_name'];
            $param['settlement_bank_account_number'] = $_POST['settlement_bank_account_number'];
            $param['settlement_bank_name'] = $_POST['settlement_bank_name'];
            $param['settlement_bank_code'] = $_POST['settlement_bank_code'];
            $param['settlement_bank_address'] = $_POST['settlement_bank_address'];
            $param['tax_registration_certificate'] = $_POST['tax_registration_certificate'];
            $param['taxpayer_id'] = $_POST['taxpayer_id'];
            if ($_FILES['tax_registration_certificate_electronic']['name'] != '') {
                $param['tax_registration_certificate_electronic'] = $this->upload_image('tax_registration_certificate_electronic');
            }
            if($_POST['is_taxpayer'] == 1 ){
                $param['is_taxpayer'] = $_POST['is_taxpayer'];
            }else{
                $param['is_taxpayer'] = 2;
            }
            //$result = Model('store_joinin')->editStoreJoinin(array('member_id' => $member_id), $param);
            //操作用户数据表跟新相应的供应商类型，级别信息
            /* @Aletta 2017.06.01*/
/*            $param['type_json'] = empty($_POST['supplier_type']) ? '':json_encode($_POST['supplier_type']);
            $param['level']     = $_POST['supply_level'];
            $param['end_time']  = strtotime($_POST['supply_end_time'])+24*3600;*/
            $upmember = Model('supplier')->where("member_id = '".$member_id."' and supplier_state = 2")->update($param);
            /*------------------END--------------------*/
            if ($upmember) {
                $supp_data = Model('supplier')->where("member_id = '".$member_id."' and supplier_state = 2")->find();
                //处理供应商差异数据的更新
                if($_POST['city_id'] == $supp_data['first_city_id']){
                    $cy_data = array(
                        'city_contacts_name'    => $param['contacts_name'],
                        'city_contacts_phone'   => $param['contacts_phone'],
                    );
                }else{
                    $cy_data = array(
                        'city_contacts_name'    =>$_POST['city_contacts_name'],
                        'city_contacts_phone'   =>$_POST['city_contacts_phone'],
                    );
                }
                Model('supplier_information')->where("member_id = '".$member_id."' and join_city = '".$_POST['city_id']."'")->update($cy_data);
                //推送采购信息
                $send_data_frist = $send_data = array(
                    "supply_code"       => $supp_data['business_licence_number'],
                    "supplierNum"       => $supp_data['contract_code'],
                    "supply_account"    => $supp_data['member_name'],
                    "supply_eas_code"   => $supp_data['eas_code'],
                    "supply_name"       => $supp_data['company_name'],
                    "supply_type"       => "1",
                    "supply_mobile"     => $supp_data['contacts_phone'],
                    "supply_address"    => $supp_data['company_address'],
                    "glass_state"       => "1"
                );
                $TO_PUR_URL = YMA_WEBSERVICE_UPDATE_OR_SAVE_SUPPLIER;
                if($_POST['city_id'] == $supp_data['first_city_id']){
                    //一次推送
                    $city_list = $model->table('city_centre')->where('id ='.$_POST['city_id'])->find();
                    $send_data['supply_org'] = $city_list['bukrs'];
                    $send_data['supply_mobile'] = $supp_data['contacts_phone'];
                    $send_data['supply_address'] = $supp_data['company_address'];
                    $send_data['supply_mail'] = $supp_data['supply_mail'];
                    $supplyinfo_json = json_encode($send_data);
                    $to_pur_result_json = WebServiceUtil::getDataByCurl($TO_PUR_URL, $supplyinfo_json, 0);
                    $to_pur_result = json_decode($to_pur_result_json,true);
                    CommonUtil::insertData2PushLog($to_pur_result, '', $supplyinfo_json, $TO_PUR_URL, 5);
                }else{
                    //两次推送
                    $city_list_p = $model->table('city_centre')->where('id ='.$_POST['city_id'])->find();
                    $send_data['supply_org'] = $city_list_p['bukrs'];
                    $send_data['supply_mobile'] = $supp_data['contacts_phone'];
                    $send_data['supply_address'] = $supp_data['company_address'];
                    $send_data['supply_mail'] = $supp_data['supply_mail'];
                    $supplyinfo_json_p = json_encode($send_data);
                    $to_pur_result_json_p = WebServiceUtil::getDataByCurl($TO_PUR_URL, $supplyinfo_json_p, 0);
                    $to_pur_result_p = json_decode($to_pur_result_json_p,true);
                    CommonUtil::insertData2PushLog($to_pur_result_p, '', $supplyinfo_json_p, $TO_PUR_URL, 5);

                    $city_list_f = $model->table('city_centre')->where('id ='.$supp_data['first_city_id'])->find();
                    $send_data_frist['supply_org'] = $city_list_f['bukrs'];
                    $send_data_frist['supply_mobile'] = $_POST['city_contacts_phone'];
                    $send_data_frist['supply_address'] = $supp_data['company_address'];
                    $send_data_frist['supply_mail'] = $supp_data['supply_mail'];
                    $supplyinfo_json_f = json_encode($send_data_frist);
                    $to_pur_result_json_f = WebServiceUtil::getDataByCurl($TO_PUR_URL, $supplyinfo_json_f, 0);
                    $to_pur_result_f = json_decode($to_pur_result_json_f,true);
                    CommonUtil::insertData2PushLog($to_pur_result_f, '', $supplyinfo_json_f, $TO_PUR_URL, 5);

                }
                //处理店铺关闭
                $model = Model();
                if(!empty($supp_data['end_time']) && ($supp_data['end_time'] > time()) && ($supp_data['store_state'] == 0)){
                    $model->table('store')->where("member_id = '".$member_id."'")->update(array('store_state'=>1,'store_close_info'=>NULL));
                }
                $member_storeLogic = Logic('member_store');
                $transResult = $member_storeLogic->transSupplyInfo($member_id);
                
                /*$send_json = json_encode($this->processing_supply_data($_POST['supplier_type'],$member_id));
                $url=YMA_WEBSERVICE_SUPPLY_TYPE;
                $return_json = WebServiceUtil::getDataByCurl($url, $send_json, 0);
                $return = json_decode($return_json,true);
                $this->log('供应商类型推送结果：供应商ID：' . $member_id . '，结果：' . $return['resultCode'] . '原因：' . $return['resultMsg'], 1);*/
                //print_r($transResult);exit;
		        //商城系统 更新店铺信息
	    	    $store_update = array();
                $store_update['store_company_name']=$param['company_name'];
                $store_update['area_info']=$param['company_address'];
                $store_update['store_address']=$param['company_address_detail'];
                $model_store = Model('store');
                $store_info = $model_store->getStoreInfo(array('member_id'=>$member_id));
                if(!empty($store_info)) {
                    $r=$model_store->editStore($store_update, array('member_id'=>$member_id));
                    $this->log('编辑店铺信息' . '[ID:' . $r. ']', 1);
                }
                if($transResult['resultCode'] == 0 ){
                    showMessage(L('nc_common_op_succ'), 'index.php?act=store&op=store_joinin2');
                }else{
                    showMessage(L('nc_common_op_succ').$transResult['resultMsg'], 'index.php?act=store&op=store_joinin2','javascript');
                }

            } else {
                showMessage(L('nc_common_op_fail'));
            }
        }
    }
    
    private function upload_file($file) {
        $pic_name = '';
        $upload = new UploadFile();
        $uploaddir = ATTACH_PATH.DS.'store_joinin'.DS;
        $upload->set('default_dir',$uploaddir);
        $upload->set('allow_type',array('jpg','jpeg','gif','png','word','pdf','docx','xlsx','html'));
        if (!empty($_FILES[$file]['name'])){
            $result = $upload->upfile_exe($file);
            if ($result){
                $pic_name = $upload->file_name;
                $upload->file_name = '';
            }
        }
        return $pic_name;
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
     * 店铺经营类目管理
     */
    public function store_bind_classOp() {
        $store_id = intval($_GET['store_id']);

        $model_store = Model('store');
        $model_store_bind_class = Model('store_bind_class');
        $model_goods_class = Model('goods_class');

//        $gc_list = $model_goods_class->getGoodsClassListByParentId(0);
//        Tpl::output('gc_list',$gc_list);

        $store_info = $model_store->getStoreInfoByID($store_id);
        if(empty($store_info)) {
            showMessage(L('param_error'),'','','error');
        }
        Tpl::output('store_info', $store_info);

        //绑定商城分类
        $get_goods_class = Model('store_class')->getStoreClassInfo(array('sc_id' => $store_info['sc_id']));
        if($get_goods_class){
            $top_goods_class = $get_goods_class['gc_bind'];
        }else{
            $top_goods_class = 0;
        }

        //$gc_list = $model_goods_class->getGoodsClassListByParentId($top_goods_class);
        $gc_list = $model_goods_class->getGoodsClassList(array('gc_id'=> $top_goods_class));
        Tpl::output('gc_list',$gc_list);
        $store_bind_class_list = $model_store_bind_class->getStoreBindClassList(array('store_id'=>$store_id,'state'=>array('in',array(1,2))), null);
        $goods_class = Model('goods_class')->getGoodsClassIndexedListAll();
        for($i = 0, $j = count($store_bind_class_list); $i < $j; $i++) {
            $store_bind_class_list[$i]['class_1_name'] = $goods_class[$store_bind_class_list[$i]['class_1']]['gc_name'];
            $store_bind_class_list[$i]['class_2_name'] = $goods_class[$store_bind_class_list[$i]['class_2']]['gc_name'];
            $store_bind_class_list[$i]['class_3_name'] = $goods_class[$store_bind_class_list[$i]['class_3']]['gc_name'];
        }
        Tpl::output('store_bind_class_list', $store_bind_class_list);

        Tpl::showpage('store_bind_class');
    }

    /**
     * 添加经营类目
     */
    public function store_bind_class_addOp() {
        $store_id = intval($_POST['store_id']);
        $commis_rate = intval($_POST['commis_rate']);
        if($commis_rate < 0 || $commis_rate > 100) {
            showMessage(L('param_error'), '');
        }
        list($class_1, $class_2, $class_3) = explode(',', $_POST['goods_class']);

        $model_store_bind_class = Model('store_bind_class');

        $param = array();
        $param['store_id'] = $store_id;
        $param['class_1'] = $class_1;
        $param['state'] = 1;
        if(!empty($class_2)) {
            $param['class_2'] = $class_2;
        }
        if(!empty($class_3)) {
            $param['class_3'] = $class_3;
        }

        // 检查类目是否已经存在
        $store_bind_class_info = $model_store_bind_class->getStoreBindClassInfo($param);
        if(!empty($store_bind_class_info)) {
            showMessage('该类目已经存在','','','error');
        }

        $param['commis_rate'] = $commis_rate;
        $result = $model_store_bind_class->addStoreBindClass($param);

        if($result) {
            $this->log('删除店铺经营类目，类目编号:'.$result.',店铺编号:'.$store_id);
            showMessage(L('nc_common_save_succ'), '');
        } else {
            showMessage(L('nc_common_save_fail'), '');
        }
    }

    /**
     * 删除经营类目
     */
    public function store_bind_class_delOp() {
        $bid = intval($_POST['bid']);

        $data = array();
        $data['result'] = true;

        $model_store_bind_class = Model('store_bind_class');
        $model_goods = Model('goods');

        $store_bind_class_info = $model_store_bind_class->getStoreBindClassInfo(array('bid' => $bid));
        if(empty($store_bind_class_info)) {
            $data['result'] = false;
            $data['message'] = '经营类目删除失败';
            echo json_encode($data);die;
        }

        // 商品下架
        $condition = array();
        $condition['store_id'] = $store_bind_class_info['store_id'];
        $gc_id = $store_bind_class_info['class_1'].','.$store_bind_class_info['class_2'].','.$store_bind_class_info['class_3'];
        $update = array();
        $update['goods_stateremark'] = '管理员删除经营类目';
        $condition['gc_id'] = array('in', rtrim($gc_id, ','));
        $model_goods->editProducesLockUp($update, $condition);

        $result = $model_store_bind_class->delStoreBindClass(array('bid'=>$bid));

        if(!$result) {
            $data['result'] = false;
            $data['message'] = '经营类目删除失败';
        }
        $this->log('删除店铺经营类目，类目编号:'.$bid.',店铺编号:'.$store_bind_class_info['store_id']);
        echo json_encode($data);die;
    }

    public function store_bind_class_updateOp() {
        $bid = intval($_GET['id']);
        if($bid <= 0) {
            echo json_encode(array('result'=>FALSE,'message'=>Language::get('param_error')));
            die;
        }
        $new_commis_rate = intval($_GET['value']);
        if ($new_commis_rate < 0 || $new_commis_rate >= 100) {
            echo json_encode(array('result'=>FALSE,'message'=>Language::get('param_error')));
            die;
        } else {
            $update = array('commis_rate' => $new_commis_rate);
            $condition = array('bid' => $bid);
            $model_store_bind_class = Model('store_bind_class');
            $result = $model_store_bind_class->editStoreBindClass($update, $condition);
            if($result) {
                $this->log('更新店铺经营类目，类目编号:'.$bid);
                echo json_encode(array('result'=>TRUE));
                die;
            } else {
                echo json_encode(array('result'=>FALSE,'message'=>L('nc_common_op_fail')));
                die;
            }
        }
    }
    /**
     * 手动合同推送列表
     */
    public  function store_push_listOp(){
    	$model_store = Model('store');
    	$model = new Model();
    	$supplier_name = htmlspecialchars(trim($_GET['supplier_name']?$_GET['supplier_name']:''));
    	$supplier_id = htmlspecialchars(trim($_GET['supplier_id']?$_GET['supplier_id']:''));
    	$push_state = $_GET['push_state']?$_GET['push_state']:'';
    	$supplier_cityid = $_GET['supplier_cityid']?$_GET['supplier_cityid']:'';
    	if (!empty($supplier_name)) {
    		$where.= " and supplier.company_name like '%".$supplier_name."%'";
    	}
    	if (!empty($supplier_id)){
    		$where.= " and member.member_name = '".$supplier_id."'";
    	}
    	if (!empty($push_state)){
    		$where.= " and store_joinin.contract_type = '".$push_state."'";
    	}
    	if (!empty($supplier_cityid)){
    		$where.= " and store_joinin.city_center = '".$supplier_cityid."'";
    	}
    	if (!empty($supplier_cityid) || !empty($push_state) || !empty($supplier_id) || !empty($supplier_name)) {
    		$where.= " and store_joinin.contract_type != 1 and supplier.supplier_state = 2 and store_joinin.store_name != ''";
    		
    	}
    	$show_where = array(
    			'supplier_name'=>$supplier_name,
    			'supplier_id'=>$supplier_id,
    			'push_state'=>$push_state,
    			'supplier_cityid'=>$supplier_cityid,
    	);
    	$push_result = $model_store->push_attestation($where,$page=10);
    	$citys_result = $model->table('city_centre')->select();
    	foreach ($push_result as $k=>$v){
    		$citys = $model_store->store_result(array('id'=>$v['city_center']));
    		$citys_name[]=$citys;
    	}   
    	Tpl::output('citys_list',$citys_result);
    	if (is_array($push_result) && $push_result != '') {
    		Tpl::output('show_page',$model_store->showpage('2'));
    		Tpl::output('citys_name',$citys_name);
    		Tpl::output('show_where',$show_where);
    		Tpl::output('push_result_list',$push_result);
    		Tpl::showpage('store_push');
    	}
    }
    /*
     *手动对接推送合同工厂
     */
    public function store_push_contractOp(){
    	include BASE_DATA_PATH.'/factory/push.factory.php';
    	$member = $_GET['member_id']?intval($_GET['member_id']):'';
    	$city = $_GET['city_center']?intval($_GET['city_center']):'';
    	$factory = new pushFactory();
    	$return_state = $factory->sendContract($member, $city);
    	if ($return_state['code'] != -1 ) {
    		showMessage('推送合同成功！', '', 'html', 'succ');
    	}else {
    		showMessage($return_state['msg'], '', 'html', 'error');
    	}
    }
	/**
	 * 店铺 待审核列表
	 */
	public function store_joininOp(){
        $model_city_centre = Model();
        $city_centreList=$model_city_centre->table("city_centre")->field("id,city_name")->select();
        Tpl::output('city_centreList',$city_centreList);
        $condition = ' 1 = 1 ';
        $condition.= !empty($_GET['city_id']) ? " and store_joinin.city_center = '".$_GET['city_id']."'":"";
		//店铺列表
        $condition.= !empty($_GET['owner_and_name']) ? " and store_joinin.member_name like '%".$_GET['owner_and_name']."%'":"";
        $condition.= !empty($_GET['store_name']) ? " and supplier.company_name like '%".$_GET['store_name']."%'":"";
		if(!empty($_GET['grade_id']) && intval($_GET['grade_id']) > 0) {
			//$condition['sg_id'] = $_GET['grade_id'];
            $condition.= " and sg_id = '".$_GET['grade_id']."'";
		}
		if(!empty($_GET['joinin_state']) ){
            if( intval($_GET['joinin_state']) > 0) {
                //$condition['store_state'] = $_GET['joinin_state'];
                $condition.= " and store_state = '".$_GET['joinin_state']."'";
            }
        } else {
            //$condition['store_state'] = array('gt',0);
            $condition.= " and store_state > '0'";
        }
        //获取当前登录后台管理员 城市中心地区
        $admininfo = $this->getAdminInfo();
        if($admininfo['cityid'] > 0){
            //$condition['city_center'] = intval($admininfo['cityid']);
            $condition.= " and store_joinin.city_center = '".intval($admininfo['cityid'])."'";
        }
		$model_store_joinin = Model('store_joinin');

		$store_list = $model_store_joinin->getList($condition, 10, 'store_state asc');
                
                $model = Model();
                $city = $model->table('city_centre')->where('city_state=1')->select();
                foreach($store_list as $key=>$rows){
                    foreach($city as $c){
                        if($rows['city_center'] == $c['id']){
                            $store_list[$key]['city_center_name'] = $c['city_name'];
                        }
                    }
                }
		Tpl::output('store_list', $store_list);
                
                Tpl::output('joinin_state_array', $this->get_store_joinin_state("open"));

		//店铺等级
		$model_grade = Model('store_grade');
		$grade_list = $model_grade->getGradeList();
		Tpl::output('grade_list', $grade_list);

		Tpl::output('page',$model_store_joinin->showpage('2'));
		Tpl::showpage('store_joinin');
	}
        
        /**
	 * 认证 待审核列表
	 */
	public function store_joinin2Op(){
            $model_city_centre = Model();
            $city_centreList=$model_city_centre->table("city_centre")->field("id,city_name")->select(); 
            Tpl::output('city_centreList',$city_centreList);    
        $where = " 1 = 1 ";   
        if($_GET['city_id']) {
            //$condition['city_center'] = $_GET['city_id'];
            $where.= " and city_center = '".$_GET['city_id']."'";
        }
		//店铺列表
		if(!empty($_GET['owner_and_name'])) {
			//$condition['store_joinin.member_name'] = array('like','%'.$_GET['owner_and_name'].'%');
		    $where.= " and store_joinin.member_name like '%".$_GET['owner_and_name']."%'";
            Tpl::output('owner_and_name', $_GET['owner_and_name']);
		}
		if(!empty($_GET['store_name'])) {
			//$condition['company_name'] = array('like','%'.$_GET['store_name'].'%');
			$where.= " and company_name like '%".$_GET['store_name']."%'";
            Tpl::output('store_name', $_GET['store_name']);
		}
		if(!empty($_GET['grade_id']) && intval($_GET['grade_id']) > 0) {
			//$condition['sg_id'] = $_GET['grade_id'];
			$where.= " and sg_id = '".$_GET['grade_id']."'";
            Tpl::output('grade_id', $_GET['grade_id']);
		}
		if(!empty($_GET['joinin_state']) && intval($_GET['joinin_state']) > 0) {
           //$condition['joinin_state'] = $_GET['joinin_state'] ;
		    $where.= " and joinin_state = '".$_GET['joinin_state']."'";
            Tpl::output('joinin_state', $_GET['joinin_state']);
        } else {
            //$num = "30,43,44,45";
            //$condition['joinin_state'] = array('in',$num);
            $where.= " and joinin_state in(30,43,44,45)";
        }
        //获取当前登录后台管理员 城市中心地区
        $admininfo = $this->getAdminInfo();
        if($admininfo['cityid'] > 0){
            //$condition['city_center'] = intval($admininfo['cityid']);
            $where.= " and city_center = '".intval($admininfo['cityid'])."'";
        }
		$model_store_joinin = Model('store_joinin');
		$field = "store_joinin.member_id,company_name,store_joinin.member_name,company_address,store_joinin.city_center,joinin_state";
		$store_list = $model_store_joinin->getList($where, 10, 'joinin_state asc',$field);
                $model = Model();
                $city = $model->table('city_centre')->where('city_state=1')->select();
                foreach($store_list as $key=>$rows){
                    foreach($city as $c){
                        if($rows['city_center'] == $c['id']){
                            $store_list[$key]['city_center_name'] = $c['city_name'];
                        }
                    }
                }
		Tpl::output('store_list', $store_list);
                 Tpl::output('joinin_state_array', $this->get_store_joinin_state("auth"));

		//店铺等级
		$model_grade = Model('store_grade');
		$grade_list = $model_grade->getGradeList();
		Tpl::output('grade_list', $grade_list);

		Tpl::output('page',$model_store_joinin->showpage('2'));
		Tpl::showpage('store_joinin');
	}

	/**
	 * 经营类目申请列表
	 */
	public function store_bind_class_applay_listOp(){
	    $condition = array();

        // 不显示自营店铺绑定的类目
        if ($_GET['state'] != '') {
            $condition['state'] = intval($_GET['state']);
            if (!in_array($condition['state'], array('0', '1', )))
                unset($condition['state']);
        } else {
            $condition['state'] = array('in', array('0', '1', ));
        }

	    if(intval($_GET['store_id'])) {
	        $condition['store_id'] = intval($_GET['store_id']);
	    }

        $model_store_bind_class = Model('store_bind_class');
        $store_bind_class_list = $model_store_bind_class->getStoreBindClassList($condition, 15,'state asc,bid desc');
        $goods_class = Model('goods_class')->getGoodsClassIndexedListAll();
        $store_ids = array();
        for($i = 0, $j = count($store_bind_class_list); $i < $j; $i++) {
            $store_bind_class_list[$i]['class_1_name'] = $goods_class[$store_bind_class_list[$i]['class_1']]['gc_name'];
            $store_bind_class_list[$i]['class_2_name'] = $goods_class[$store_bind_class_list[$i]['class_2']]['gc_name'];
            $store_bind_class_list[$i]['class_3_name'] = $goods_class[$store_bind_class_list[$i]['class_3']]['gc_name'];
            $store_ids[] = $store_bind_class_list[$i]['store_id'];
        }
        //取店铺信息
        $model_store = Model('store');
        $store_list = $model_store->getStoreList(array('store_id'=>array('in',$store_ids)),null);
        $bind_store_list = array();
        if (!empty($store_list) && is_array($store_list)) {
            foreach ($store_list as $k => $v) {
                $bind_store_list[$v['store_id']]['store_name'] = $v['store_name'];
                $bind_store_list[$v['store_id']]['seller_name'] = $v['seller_name'];
            }
        }

        Tpl::output('bind_list', $store_bind_class_list);
        Tpl::output('bind_store_list',$bind_store_list);

	    Tpl::output('page',$model_store_bind_class->showpage('2'));
	    Tpl::showpage('store_bind_class_applay.list');
	}

	/**
	 * 审核经营类目申请
	 */
	public function store_bind_class_applay_checkOp() {
	    $model_store_bind_class = Model('store_bind_class');
	    $condition = array();
	    $condition['bid'] = intval($_GET['bid']);
	    $condition['state'] = 0;
	    $update = $model_store_bind_class->editStoreBindClass(array('state'=>1),$condition);
	    if ($update) {
	        $this->log('审核新经营类目申请，店铺ID：'.$_GET['store_id'],1);
	        showMessage('审核成功',getReferer());
	    } else {
	        showMessage('审核失败',getReferer(),'html','error');
	    }
	}

	/**
	 * 删除经营类目申请
	 */
	public function store_bind_class_applay_delOp() {
	    $model_store_bind_class = Model('store_bind_class');
	    $condition = array();
	    $condition['bid'] = intval($_GET['bid']);
	    $del = $model_store_bind_class->delStoreBindClass($condition);
	    if ($del) {
	        $this->log('删除经营类目，店铺ID：'.$_GET['store_id'],1);
	        showMessage('删除成功',getReferer());
	    } else {
	        showMessage('删除失败',getReferer(),'html','error');
	    }
	}

    private function get_store_joinin_state($type) {
        $joinin_state_array = array();
        switch($type){
            case "open":
                $joinin_state_array[STORE_JOIN_STATE_KDJJ] = '开店拒绝';
                $joinin_state_array[STORE_JOIN_STATE_FINAL] = '开店成功';
                $joinin_state_array[STORE_JOIN_STATE_RZHKD] = '开店新申请';
                break;
            case "auth":
                $joinin_state_array[STORE_JOIN_STATE_RZ] = '认证申请';
                $joinin_state_array[STORE_JOIN_STATE_RZSUCCESS] = '认证成功';
                $joinin_state_array[STORE_JOIN_STATE_FNO] = '认证拒绝';
                break;
        }
       /* $joinin_state_array = array(
            STORE_JOIN_STATE_NEW => '新申请',
            STORE_JOIN_STATE_PAY => '已付款',
            STORE_JOIN_STATE_VERIFY_SUCCESS => '待付款',
            STORE_JOIN_STATE_VERIFY_FAIL => '审核失败',
            STORE_JOIN_STATE_PAY_FAIL => '付款审核失败',
            STORE_JOIN_STATE_FINAL => '开店成功',
            STORE_JOIN_STATE_RZ => '认证申请',
            STORE_JOIN_STATE_RZHKD => '开店新申请',
            STORE_JOIN_STATE_RZSUCCESS => '认证成功',
            STORE_JOIN_STATE_FNO => '认证拒绝',
        );*/
        return $joinin_state_array;
    }

    /**
     * 店铺续签申请列表
     */
    public function reopen_listOp(){
        $condition = array();
        if(intval($_GET['store_id'])) {
            $condition['re_store_id'] = intval($_GET['store_id']);
        }
        if(!empty($_GET['store_name'])) {
            $condition['re_store_name'] = $_GET['store_name'];
        }
        if ($_GET['re_state'] != '') {
            $condition['re_state'] = intval($_GET['re_state']);
        }
        $model_store_reopen = Model('store_reopen');
        $reopen_list = $model_store_reopen->getStoreReopenList($condition, 15);

        Tpl::output('reopen_list', $reopen_list);

        Tpl::output('page',$model_store_reopen->showpage('2'));
        Tpl::showpage('store_reopen.list');
    }

    /**
     * 审核店铺续签申请
     */
    public function reopen_checkOp() {
        if (intval($_GET['re_id']) <= 0) exit();
        $model_store_reopen = Model('store_reopen');
        $condition = array();
        $condition['re_id'] = intval($_GET['re_id']);
        $condition['re_state'] = 1;
        //取当前申请信息
        $reopen_info = $model_store_reopen->getStoreReopenInfo($condition);

        //取目前店铺有效截止日期
        $store_info = Model('store')->getStoreInfoByID($reopen_info['re_store_id']);
        $data = array();
        $data['re_start_time'] = strtotime(date('Y-m-d 0:0:0',$store_info['store_end_time']))+24*3600;
        $data['re_end_time'] = strtotime(date('Y-m-d 23:59:59', $data['re_start_time'])." +".intval($reopen_info['re_year'])." year");
        $data['re_state'] = 2;
        $update = $model_store_reopen->editStoreReopen($data,$condition);
        if ($update) {
            //更新店铺有效期
            Model('store')->editStore(array('store_end_time'=>$data['re_end_time']),array('store_id'=>$reopen_info['re_store_id']));
            $msg = '审核通过店铺续签申请，店铺ID：'.$reopen_info['re_store_id'].'，续签时间段：'.date('Y-m-d',$data['re_start_time']).' - '.date('Y-m-d',$data['re_end_time']);
            $this->log($msg,1);
            showMessage('续签成功，店铺有效成功延续到了'.date('Y-m-d',$data['re_end_time']).'日',getReferer());
        } else {
            showMessage('审核失败',getReferer(),'html','error');
        }
    }

    /**
     * 删除店铺续签申请
     */
    public function reopen_delOp() {
        $model_store_reopen = Model('store_reopen');
        $condition = array();
        $condition['re_id'] = intval($_GET['re_id']);
        $condition['re_state'] = array('in',array(0,1));

        //取当前申请信息
        $reopen_info = $model_store_reopen->getStoreReopenInfo($condition);
        $cert_file = BASE_UPLOAD_PATH.DS.ATTACH_STORE_JOININ.DS.$reopen_info['re_pay_cert'];
        $del = $model_store_reopen->delStoreReopen($condition);
        if ($del) {
            if (is_file($cert_file)) {
                unlink($cert_file);
            }
            $this->log('删除店铺续签目申请，店铺ID：'.$_GET['re_store_id'],1);
            showMessage('删除成功',getReferer());
        } else {
            showMessage('删除失败',getReferer(),'html','error');
        }
    }

	/**
	 * 审核详细页
	 */
	public function store_joinin_detailOp(){
		$model_store_joinin = Model('store_joinin');
                if(empty($_GET['city'])){
                    showMessage('城市公司参数错误','index.php?act=store&op=store_joinin2');exit;
                }
        $member_id = $_GET['member_id'];
        $joinin_detail_where = "store_joinin.member_id = '".$member_id."' and store_joinin.city_center = '".$_GET['city']."'";
        $joinin_detail = $model_store_joinin->getOne($joinin_detail_where);
        //获取城市中心
        $model = Model();
        $city = $model->table('city_centre')->field('city_name')->where('id='.$_GET['city'])->find();
        $joinin_detail['city_name'] = $city['city_name'];
        $joinin_detail_title = '查看';
        if(in_array(intval($joinin_detail['joinin_state']), array(STORE_JOIN_STATE_NEW, STORE_JOIN_STATE_PAY))) {
            $joinin_detail_title = '审核';
        }
        //检查店铺信息是否存在
        if(empty($joinin_detail['seller_name']) || empty($joinin_detail['store_name']) || empty($joinin_detail['sg_id']) ){
            $where_list = "member_id = '".$member_id."' and store_state in('34','40')";
            $list_data = $model->table('store_joinin')->where($where_list)->find();
            $joinin_detail['seller_name'] = $list_data['seller_name'];
            $joinin_detail['store_name'] = $list_data['store_name'];
            $joinin_detail['sg_id'] = $list_data['sg_id'];
        }

        if (empty($joinin_detail['sg_info'])) {
            $store_grade_info = Model('store_grade')->getOneGrade($joinin_detail['sg_id']);
            $joinin_detail['sg_price'] = $store_grade_info['sg_price'];
        } else {
            $joinin_detail['sg_info'] = @unserialize($joinin_detail['sg_info']);
            if (is_array($joinin_detail['sg_info'])) {
                $joinin_detail['sg_price'] = $joinin_detail['sg_info']['sg_price'];
            }
        }
        //查询是否存在差异信息，如果存在，则将信息替换
        $data_info = $model->table("supplier_information")->where("member_id = '".$member_id."' and join_city = '".$_GET['city']."'")->find();
        if(!empty($data_info)){
            $joinin_detail['contacts_name'] = $data_info['city_contacts_name'];
            $joinin_detail['contacts_phone'] = $data_info['city_contacts_phone'];
        }
        //判断是否可以回退
        $where_is_rz['joinin_state'] = 44;
        $where_is_rz['member_id'] = $_GET['member_id'];
        $is_rz_one = $model->table('store_joinin')->field('joinin_state')->where($where_is_rz)->find();
        
        //获取供应商类型数据
        $supplier_type_data = $model->table('supplier_type')->field('id,type_name')->where(array("level"=>1,"parent_id"=>0))->select();
        $supplier_type_list = array();
        if(!empty($supplier_type_data) && is_array($supplier_type_data)){
            foreach ($supplier_type_data as $val){
                $supplier_type_list_data = $model->table('supplier_type')->field('id,type_name')->where(array("level"=>2,"parent_id"=>$val['id']))->select();
                $supplier_type_list[$val['id']] = $supplier_type_list_data;
            }
        }

        $join_show_data = $model->table('store_joinin')->where("member_id = '".$member_id."' and city_center = first_city_id and joinin_state = '44'")->find();
        $sup_show = empty($join_show_data) ? '1':'2';
        if($is_rz_one['joinin_state'] == 44){
            Tpl::output('is_rz_one',1);
        }else{
            Tpl::output('is_rz_one',2);
        }

        $num = $model->table('store_joinin')->where(array("member_id"=>$member_id))->count();
        $member_model = Model('member');
        $member_time = $model->table('member')->field('member_time')->where(array('member_id'=>$member_id))->find();
        $member_time_end = $model->table('supplier')->field('end_time')->where(array('member_id'=>$member_id,'supplier_state'=>2))->find();
        Tpl::output('supplier_type_data',$member_model->get_supply_type($member_id));
        Tpl::output('supplier_level',$member_model->get_supply_level($member_id));
        Tpl::output('store_joinin_num', $num);
        Tpl::output('supplier_time_data',array('member_time'=>$member_time['member_time'],'supply_end_time'=>$member_time_end['end_time']));
        Tpl::output('joinin_detail_title', $joinin_detail_title);
		Tpl::output('joinin_detail', $joinin_detail);
		Tpl::output('supplier_type', $supplier_type_data);
		Tpl::output('supplier_list', $supplier_type_list);
        Tpl::output('supplier_type_show',$sup_show);
		Tpl::showpage('store_joinin.detail');
	}
	
	
	/**
	 * 获取供应商联动数据
	 * @Aletta 2017.05.24 10:09
	 **/
	public function get_supplier_typeOp(){
	    $model = Model();
	    $condition['parent_id'] = $_POST['parent'];
	    $condition['level'] = 2;
	    $type = $model->table('supplier_type')->where($condition)->select();
	    echo json_encode($type);
	}
	

	/**
	 * 审核
	 */
	public function store_joinin_verifyOp() {
        $model_store_joinin = Model('store_joinin');
        $joinin_detail_where = "store_joinin.member_id = '".$_POST['member_id']."' and store_joinin.city_center = '".$_POST['city_id']."'";
        $joinin_detail = $model_store_joinin->getOne($joinin_detail_where);
        switch (intval($joinin_detail['joinin_state'])) {
            case STORE_JOIN_STATE_NEW:
                $this->store_joinin_verify_pass($joinin_detail);
                break;
            case STORE_JOIN_STATE_RZ://43
                $this->store_joinin_verify_pass($joinin_detail);
                break;
            case STORE_JOIN_STATE_PAY:
                $this->store_joinin_verify_open($joinin_detail);
                break;
            case STORE_JOIN_STATE_RZSUCCESS:
                $this->store_joinin_verify_open($joinin_detail);
                break;
            default:
                showMessage('参数错误','');
                break;
        } 
        
	}

    private function store_joinin_verify_pass($joinin_detail){
        if ($_POST['verify_type'] == "pass") {
            if (empty($_FILES['rz_evaluation_audit']['name'])) {
                showMessage('请先上传审核评估', '');
                exit;
            }
        }
        $param = array();
        if ($_POST['verify_type'] == 'fno') {
            $param['joinin_state'] = 45;
        } else {
            $param['joinin_state'] = $_POST['verify_type'] === 'pass' ? STORE_JOIN_STATE_RZSUCCESS : STORE_JOIN_STATE_VERIFY_FAIL;
        }
        //必须在审核通过的时候才做次操作
        $model = Model();
        $up_supplier_data['supplier_state'] = $_POST['verify_type'] === 'pass' ? 2 : 4;
        $up_information_data['state_type'] = $_POST['verify_type'] === 'pass' ? 2 : 3;
        if ($_POST['verify_type'] === 'pass') {
            //获取供应商的认证数据信息
            $join_data = $model->table('store_joinin')->where("member_id='" . $_POST['member_id'] . "' and city_center = '" . $_POST['city_id'] . "'")->find();
            //跟新join认证记录表数据
            $param['joinin_message'] = $_POST['joinin_message'];
            $param['joinin_message_open'] = $_POST['joinin_message_open'];
            $param['rz_evaluation_audit'] = $this->upload_file('rz_evaluation_audit');
            $param['paying_amount'] = abs(floatval($_POST['paying_amount']));
            if (is_array($_POST['commis_rate'])) {
                $param['store_class_commis_rates'] = implode(',', $_POST['commis_rate']);
            }
            //updata
            $model_store_joinin = Model('store_joinin');
            $joinin_detail_where['member_id'] = $_POST['member_id'];
            $joinin_detail_where['city_center'] = $_POST['city_id'];
            $is_ture = $model->table('store_joinin')->where($joinin_detail_where)->update($param);
            if ($is_ture) {
                $up_member_data['supply_code'] = $joinin_detail['business_licence_number'];
                if ($join_data['first_city_id'] == '0') {
                    //首次认证
                    //跟新member数据
                    $up_member_data['first_city_id'] = $_POST['city_id'];
                    $up_member_data['role_id'] = MEMBER_IDENTITY_THREE;
                    $up_member_data['city_id'] = '0,';
                    $model->table('member')->where('member_id=' . $_POST['member_id'])->update($up_member_data);
                    //首次认证跟新供应商数据
                    $up_supplier_data['type_json'] = empty($_POST['supplier_type']) ? '' : json_encode($_POST['supplier_type']);
                    $up_supplier_data['level'] = $_POST['supply_level'];
                    $up_supplier_data['end_time'] = strtotime($_POST['supply_end_time']) + 24 * 3600;
                    $up_supplier_data['first_city_id'] = $_POST['city_id'];

                    //跟新首次认证城市信息
                    $model->table('store_joinin')->where($joinin_detail_where)->update(array('first_city_id'=>$_POST['city_id']));
                }
                //获取到所有已经认证成功的城市公司数据
                $city_list = $model->table('store_joinin')->field('city_center')->where("member_id = '" . $_POST['member_id'] . "' and joinin_state = '44'")->select();
                $city_data = array();
                if (!empty($city_list) && is_array($city_list)) {
                    foreach ($city_list as $v) {
                        $city_data[] = $v['city_center'];
                    }
                }
                $up_supplier_data['city_center_list'] = empty($city_data) ? "" : implode(',', $city_data);
                $model->table('member')->where('member_id=' . $_POST['member_id'])->update($up_member_data);
                $model->table('supplier')->where('member_id=' . $_POST['member_id'])->update($up_supplier_data);
                //推送其他平台
                $rest_msg = '';
                //合同
                $restContract = Factory('push')->sendContract($_POST['member_id'], $_POST['city_id']);
                $rest_msg.= $restContract['code'] == '-1' ? "/" . $restContract['msg'] : "";
                //EAS
                $restEas = Factory('push')->sendEas($_POST['member_id'], $_POST['city_id']);
                $rest_msg.= $restEas['code'] == '-1' ? "/n" . $restEas['msg'] : "";
                //采购
                $restPurchase = Factory('push')->sendPurchase($_POST['member_id'], $_POST['city_id']);
                $rest_msg.= $restPurchase['code'] == '-1' ? "/" . $restPurchase['msg'] : "";
                //&& ($restEas['code'] == '1')
                if (($restContract['code'] == '1')  && ($restPurchase['code'] == '1') && ($restEas['code'] == '1') ){
                    showMessage('供应商认证申请审核完成', 'index.php?act=store&op=store_joinin2');
                } else {
                    showMessage('供应商认证申请审核完成。' . $rest_msg, 'index.php?act=store&op=store_joinin2', 'javascript');
                }
            } else {
                showMessage('供应商认证失败', 'index.php?act=store&op=store_joinin2');
            }
        } else {
            showMessage('供应商认证拒绝', 'index.php?act=store&op=store_joinin2');
        }
    }

    
    private function processing_supply_data($supply_data,$member_id) {
        $model	= Model();
        $store_joinin_info = $model->table("supplier")->field('business_licence_number')->where(array("member_id"=>$member_id))->find();
        if(!empty($store_joinin_info['business_licence_number'])){
            if(!empty($supply_data) && is_array($supply_data)){
                $new_data = array();
                foreach ($supply_data as $vl){
                    foreach ($vl as $v){
                        $new_data[] = $v;
                    }
                }
            }
            return array("supply_code"=>$store_joinin_info['business_licence_number'],"supplier_types"=>implode(",",$new_data));
        }
    }

    private function store_joinin_verify_open($joinin_detail) {
        $model_store_joinin = Model('store_joinin');
        $model_store	= Model('store');
        $model_seller = Model('seller');
        $model = Model();
        //判断当前用户是否开过店如果开过店铺则不创建店铺并且不走验证卖家用户名操作
        $store = $model_store->where('member_id='.$_POST['member_id'])->find();
        if(empty($store)){
            //验证卖家用户名是否已经存在
            if($model_seller->isSellerExist(array('seller_name' => $joinin_detail['seller_name']))) {
                showMessage('卖家用户名已存在','');
            }
        }
        //查询当前更新的城市中心名称
        $city_name = $model->table('city_centre')->where('id='.$joinin_detail['city_center'])->find();
        $param = array();
        $param['joinin_message_open'] = $_POST['joinin_message_open'];
        
        if($_POST['verify_type'] === 'pass'){
            //不通过则修改店铺认证状态
            $param['store_state'] = 40;
        }else{
            $param['store_state'] = 41;
        }
        $model_store_joinin_where['member_id']  = $_POST['member_id'];
        $model_store_joinin_where['city_center']  = $_POST['city_id'];
        
        $is=$model->table('store_joinin')->where($model_store_joinin_where)->update($param);
        
        //发送短信通知
        $this->sendMsg4Review($param, $model_store_joinin_where, "入驻", true);
        
        
        if($_POST['verify_type'] === 'pass') {
            $data_u_up['role_id'] = MEMBER_IDENTITY_FOUR;
            $data_u_up['city_id'] = '0,';
            $member_mode = Model('member');
            $upmember = $member_mode->where('member_id='.$_POST['member_id'])->update($data_u_up);

            //获取所有已经开店的城市id
            $city_list = $model->table('store_joinin')->field('city_center')->where("member_id = '".$_POST['member_id']."' and store_state = '40'")->select();
            $city_data = array();
            if(!empty($city_list) && is_array($city_list)){
                foreach ($city_list as $v){
                    $city_data[] = $v['city_center'];
                }
            }
            $store_city_id = empty($city_data) ? "":implode(',',$city_data).',';
            //如果没有就开过店铺的则走开店流程 else 直接添加店铺城市中心id
            if(empty($store)){
                //开店
                $shop_array		= array();
                $shop_array['member_id']	= $joinin_detail['member_id'];
                $shop_array['member_name']	= $joinin_detail['member_name'];
                $shop_array['seller_name'] = $joinin_detail['seller_name'];
                $shop_array['grade_id']		= $joinin_detail['sg_id'];
                $shop_array['store_name']	= $joinin_detail['store_name'];
                $shop_array['sc_id']		= $joinin_detail['sc_id'];

                //店铺类型
                $shop_array['store_type_id']		= $joinin_detail['store_type_id'];
                $shop_array['store_type_name']		= $joinin_detail['store_type_name'];

                $shop_array['store_company_name'] = $joinin_detail['company_name'];
                $shop_array['province_id']	= $joinin_detail['company_province_id'];
                $shop_array['area_info']	= $joinin_detail['company_address'];
                $shop_array['store_address']= $joinin_detail['company_address_detail'];
                $shop_array['store_zip']	= '';
                $shop_array['store_zy']		= '';
                $shop_array['store_state']	= 1;
                $shop_array['store_time']	= time();
                $shop_array['store_city_id']	= $store_city_id;
                $shop_array['first_city_id']	= $joinin_detail['first_city_id'];
                $shop_array['store_city_name']	= $city_name['city_name'];
                $shop_array['store_end_time'] = strtotime(date('Y-m-d 23:59:59', strtotime("+".intval($joinin_detail['joinin_year'])." year")))+(3600*24);
                $store_id = $model_store->addStore($shop_array);

                if($store_id) {
                    //更新商户信息数据表
                    $model->table('supplier')->where('member_id='.$joinin_detail['member_id'])->update(array('store_id'=>$store_id));
                    //开店用户  更新用户角色字段
                    $data_u_member['role_id'] = MEMBER_IDENTITY_FOUR;
                    $model->table('member')->where('member_id='.$joinin_detail['member_id'])->update($data_u_member);

                    //写入卖家账号
                    $seller_array = array();
                    $seller_array['seller_name'] = $joinin_detail['seller_name'];
                    $seller_array['member_id'] = $joinin_detail['member_id'];
                    $seller_array['seller_group_id'] = 0;
                    $seller_array['store_id'] = $store_id;
                    $seller_array['is_admin'] = 1;
                    $state = $model_seller->addSeller($seller_array);

                    //cary_add 添加代理关联
                    if(!empty($joinin_detail['agent_id'])){
                        $model_agent_store = Model('agent_store');
                        $agent_store_array = array();
                        $agent_store_array['agent_id'] = $joinin_detail['agent_id'];
                        $agent_store_array['store_id'] = $store_id;
                        $agent_store_array['verify_time'] = TIMESTAMP;
                        $model_agent_store->addStore($agent_store_array);
                    }

                }

                if($state) {
                    // 添加相册默认
                    $album_model = Model('album');
                    $album_arr = array();
                    $album_arr['aclass_name'] = Language::get('store_save_defaultalbumclass_name');
                    $album_arr['store_id'] = $store_id;
                    $album_arr['aclass_des'] = '';
                    $album_arr['aclass_sort'] = '255';
                    $album_arr['aclass_cover'] = '';
                    $album_arr['upload_time'] = time();
                    $album_arr['is_default'] = '1';
                    $album_model->addClass($album_arr);

                    $model = Model();
                    //插入店铺扩展表
                    $model->table('store_extend')->insert(array('store_id'=>$store_id));
                    $msg = Language::get('store_save_create_success');

                    //插入店铺绑定分类表
                    $store_bind_class_array = array();
                    $store_bind_class = unserialize($joinin_detail['store_class_ids']);
                    $store_bind_commis_rates = explode(',', $joinin_detail['store_class_commis_rates']);
                    for($i=0, $length=count($store_bind_class); $i<$length; $i++) {
                        list($class1, $class2, $class3) = explode(',', $store_bind_class[$i]);
                        $store_bind_class_array[] = array(
                            'store_id' => $store_id,
                            'commis_rate' => $store_bind_commis_rates[$i],
                            'class_1' => $class1,
                            'class_2' => intval($class2),
                            'class_3' => intval($class3),//@cary fix  自动赋0
                            'state' => 1
                        );
                    }
                    $model_store_bind_class = Model('store_bind_class');
                    $model_store_bind_class->addStoreBindClassAll($store_bind_class_array);

                    //批量绑定子分类下的经营项目
                    $sub_class = $this->batch_bind_class_byid($store_id, $class1, 0);
                    $model_store_bind_class->addStoreBindClassAll($sub_class);


                    showMessage('店铺开店成功','index.php?act=store&op=store_joinin');
                } else {
                    showMessage('店铺开店失败','index.php?act=store&op=store_joinin');
                }
            }else{
                //为当前这个用户添加城市中心
                $store_up['store_city_id'] = $store_city_id;
                $store_up['supply_type'] = MEMBER_SUPPLY_TYPE_1;
                $store_up['supply_code'] = $joinin_detail['business_licence_number'];
                $model_store->where('member_id='.$_POST['member_id'])->update($store_up);
                //添加这个城市中心的名称和id
                //更新店铺表 城市id 名称
                $store_up_city['store_city_id'] = $store['store_city_id'].$joinin_detail['city_center'].',';
                //跟新绑定类目
                //插入店铺绑定分类表
                $store_bind_class_array = array();
                $store_bind_class = unserialize($joinin_detail['store_class_ids']);
                $store_bind_commis_rates = explode(',', $joinin_detail['store_class_commis_rates']);
                for($i=0, $length=count($store_bind_class); $i<$length; $i++) {
                    list($class1, $class2, $class3) = explode(',', $store_bind_class[$i]);
                    $store_bind_class_array[] = array(
                        'store_id' => $store['store_id'],
                        'commis_rate' => $store_bind_commis_rates[$i],
                        'class_1' => $class1,
                        'class_2' => intval($class2),
                        'class_3' => intval($class3),//@cary fix  自动赋0
                        'state' => 1
                    );
                }
                $model_store_bind_class = Model('store_bind_class');
                $model_store_bind_class->addStoreBindClassAll($store_bind_class_array);

                //批量绑定子分类下的经营项目
                $sub_class = $this->batch_bind_class_byid($store['store_id'], $class1, 0);
                $model_store_bind_class->addStoreBindClassAll($sub_class);
                //提交更新store表
                $up = $model_store->where('member_id='.$_POST['member_id'])->update($store_up_city);
                showMessage('店铺开店成功','index.php?act=store&op=store_joinin');
            }
            
        } else {
            showMessage('店铺开店拒绝','index.php?act=store&op=store_joinin');
        }
    }

    /**
     * 提醒续费
     */
    public function remind_renewalOp() {
        $store_id = intval($_GET['store_id']);
        $store_info = Model('store')->getStoreInfoByID($store_id);
        if (!empty($store_info) && $store_info['store_end_time'] < (TIMESTAMP + 864000) && cookie('remindRenewal'.$store_id) == null) {
            // 发送商家消息
            $param = array();
            $param['code'] = 'store_expire';
            $param['store_id'] = intval($_GET['store_id']);
            $param['param'] = array();
            QueueClient::push('sendStoreMsg', $param);

            setNcCookie('remindRenewal'.$store_id, 1, 86400 * 10);  // 十天
            showMessage('消息发送成功');
        }
            showMessage('消息发送失败');
    }
	    public function delOp()
    {
        $storeId = (int) $_GET['id'];
        $storeModel = model('store');

        $storeArray = $storeModel->field('is_own_shop,store_name')->find($storeId);

        if (empty($storeArray)) {
            showMessage('外驻店铺不存在', '', 'html', 'error');
        }

        if ($storeArray['is_own_shop']) {
            showMessage('不能在此删除自营店铺', '', 'html', 'error');
        }

        $condition = array(
            'store_id' => $storeId,
        );

        if ((int) model('goods')->getGoodsCount($condition) > 0)
            showMessage('已经发布商品的外驻店铺不能被删除', '', 'html', 'error');

        // 完全删除店铺
        $storeModel->delStoreEntirely($condition);
		
		//删除入驻相关
		$member_id = (int) $_GET['member_id'];
		$store_joinin = model('store_joinin');
		$condition = array(
	        'member_id' => $member_id,
        	);
		$store_joinin->drop($condition);
		
        $this->log("删除外驻店铺: {$storeArray['store_name']}");
        showMessage('操作成功', getReferer());
    }
	
	
	//删除店铺操作
	  public function del_joinOp()
    {
        $member_id = (int) $_GET['id'];
        $store_joinin = model('store_joinin');
        $condition = array(
            'member_id' => $member_id,
        );
		$mm=$store_joinin->getOne($condition);
		if(empty($mm))
		{
			showMessage('操作失败', getReferer());
		}
		if($mm['joinin_state']=='20')
		{
		}
		$store_name=$mm['store_name'];
		$storeModel = model('store');
		$scount=$storeModel->getStoreCount($condition);
		if($scount>0)
		{
		   showMessage('操作失败已有店铺在运营', getReferer());
		}
        // 完全删除店铺入驻
        $condition['city_center']    = htmlspecialchars($_GET['city']);  
        $store_joinin->drop($condition);
        $this->log("删除店铺入驻:".$store_name);
        showMessage('操作成功', getReferer());
    }

    /**
     * 如果页面没有勾选了同时生成店铺。则数据不保存到stor表中。
     */
    public function newshop_addOp()
    {
        if (chksubmit())
        {
            $user = unserialize(decrypt(cookie('sys_key'),MD5_KEY));
            $is_create_store = $_POST['is_create_store'];//是否创建供应商的同时也开店

            $city_id_str='';
            $postCityList = $_POST['cityArray'];
            $cityListLen = sizeof($postCityList);
            if ($cityListLen == 0 ) {
                showMessage('请选择中心城市', '', 'html', 'error');
            }
            $isContainCurrentUserCity = false;//中心城市列表中是否包含当前用户的城市中心
            if($cityListLen  > 0){
                for($index = 0 ; $index < $cityListLen ;$index++ ){//拼接认证的城市id列表
                    if($postCityList[$index] == $user['cityid']){
                        $isContainCurrentUserCity = true;
                    }
                    $city_id_str .=$postCityList[$index];
                    if($index < $cityListLen-1){$city_id_str .=',';}
                }
            }
            $memberName = $_POST['member_name'];
            $memberPasswd = (string) $_POST['member_passwd'];
            $supply_type = $_POST['supply_type'];
            $supply_code = $_POST['supply_code'];
            if (strlen($memberName) < 3 || strlen($memberName) > 15)
                showMessage('账号名称必须是3~15位', '', 'html', 'error');
            if (strlen($memberPasswd) < 6)
                showMessage('登录密码不能短于6位', '', 'html', 'error');
            if (!$this->checkMemberName($memberName))
                showMessage('店主账号已被占用', '', 'html', 'error');
            if( sizeof(  model('member')->getMemberInfo(array('supply_code' => $supply_code ))) > 0  ){
                showMessage('营业执照号或组织机构代码'.$supply_code.'已被占用', '', 'html', 'error');
            };
            try
            {
                if($is_create_store =='1'){//创建供应商的同时也开店
                    if (strlen($_POST['seller_name']) < 3 || strlen($_POST['seller_name']) > 15) {
                        showMessage('账号名称必须是3~15位', '', 'html', 'error');
                    }
                    if (!$this->checkSellerName($_POST['seller_name'])) {
                        showMessage('店主卖家账号名称已被其它店铺占用', '', 'html', 'error');
                    }
                    $role_id = MEMBER_IDENTITY_FOUR;
                }else{
                    $role_id = MEMBER_IDENTITY_THREE;
                }
                $model = Model();
                $model->beginTransaction();
                $memberId = model('member')->addMemberForAdmin(array(
                    'member_name' => $memberName,
                    'member_passwd' => $memberPasswd,
                    'member_email' => '',
                    'supply_type' => $supply_type,
                    'supply_code' => $supply_code,
                    'role_id' => $role_id,
                    'first_city_id' =>  $user['cityid'],
                ));
                if($is_create_store =='1'){//同时店铺数据
                    $store_state = STORE_JOIN_STATE_FINAL;
                    $storeModel = model('store');
                    $saveArray = array();
                    $saveArray['store_name'] = $_POST['store_name'];
                    $saveArray['member_id'] = $memberId;
                    $saveArray['member_name'] = $memberName;
                    $saveArray['seller_name'] = $_POST['seller_name'];
                    $saveArray['bind_all_gc'] = 1;
                    $saveArray['store_state'] = 1;
                    $saveArray['store_time'] = time();
                    $saveArray['is_own_shop'] = 0;
                    $saveArray['first_city_id'] = $user['cityid'];
                    if( (!$isContainCurrentUserCity) && $cityListLen >0 && $user['cityid'] > 0){
                        $city_id_str .=','.$user['cityid'];
                    }
                    $saveArray['store_city_id'] = $city_id_str;
                    $storeId = $storeModel->addStore($saveArray);
                    // 添加相册默认
                    $album_model = Model('album');
                    $album_arr = array();
                    $album_arr['aclass_name'] = '默认相册';
                    $album_arr['store_id'] = $storeId;
                    $album_arr['aclass_des'] = '';
                    $album_arr['aclass_sort'] = '255';
                    $album_arr['aclass_cover'] = '';
                    $album_arr['upload_time'] = time();
                    $album_arr['is_default'] = '1';
                    $album_model->addClass($album_arr);

                    //插入店铺扩展表
                    $model = Model();
                    $model->table('store_extend')->insert(array('store_id'=>$storeId));
                }else{
                    $store_state = 0 ;
                }
                model('seller')->addSeller(array(
                    'seller_name' => $_POST['seller_name'],
                    'member_id' => $memberId,
                    'store_id' => $storeId,
                    'seller_group_id' => 0,
                    'is_admin' => 1,
                ));
                $addStoreJoininRecord = array(
                    'seller_name' => $_POST['seller_name'],
                    'store_name'  => $_POST['store_name'],
                    'company_name'  => $_POST['company_name'],
                    'member_name' => $memberName,
                    'member_id' => $memberId,
                    'joinin_state' => STORE_JOIN_STATE_RZSUCCESS,
                    'city_center' => 0,
                    'first_city_id' =>0,
                    'store_state' => $store_state,
                    'company_province_id' => 0,
                    'sc_bail' => 0,
                    'joinin_year' => 1,
                );
                $first_city_id=0;//首次认证的城市
                for($index = 0 ; $index < $cityListLen ;$index++ ){
                        //if($isContainCurrentUserCity && $first_city_id == $user['cityid'] ){
                            $first_city_id = $user['cityid'];
                        /*}else{
                            $first_city_id =0;
                        }*/
                        $addStoreJoininRecord['city_center'] = $postCityList[$index];
                        $addStoreJoininRecord['first_city_id'] = $first_city_id;
                        model('store_joinin')->save($addStoreJoininRecord);
                }
                if( (!$isContainCurrentUserCity) && $cityListLen >0 && $user['cityid'] > 0){//如果新增加的供应商没有选择跟当前用户的城市中心绑定。则新增
                    $addStoreJoininRecord['city_center'] = $user['cityid'];
                    $addStoreJoininRecord['first_city_id'] = $user['cityid'];
                    model('store_joinin')->save($addStoreJoininRecord);
                }

                // 删除自营店id缓存
                Model('store')->dropCachedOwnShopIds();
                $model->commit();

            }
            catch (Exception $ex)
            {
                $model->rollback();
                showMessage('店主账号新增失败', '', 'html', 'error');
            }
            $this->log("新增外驻店铺: {$saveArray['store_name']}");
            showMessage('操作成功', urlAdmin('store', 'store_joinin2'));
            return;
        }
        $user = unserialize(decrypt(cookie('sys_key'),MD5_KEY));
        //取得中心城市列表
        $cityLogic = Logic('city');
        $cityList = $cityLogic->getNewCityList($user['cityid']);
        Tpl::output("twoDimencityList",$cityList);
        Tpl::output("supply_type_list",json_decode(MEMBER_SUPPLY_TYPE_LIST,true));
        Tpl::showpage('store.newshop.add');
    }
    

    public function check_seller_nameOp()
    {
        echo json_encode($this->checkSellerName($_GET['seller_name'], $_GET['id']));
        exit;
    }

    private function checkSellerName($sellerName, $storeId = 0)
    {
        // 判断store_joinin是否存在记录
        $count = (int) Model('store_joinin')->getStoreJoininCount(array(
            'seller_name' => $sellerName,
        ));
        if ($count > 0)
            return false;

        $seller = Model('seller')->getSellerInfo(array(
            'seller_name' => $sellerName,
        ));

        if (empty($seller))
            return true;

        if (!$storeId)
            return false;

        if ($storeId == $seller['store_id'] && $seller['seller_group_id'] == 0 && $seller['is_admin'] == 1)
            return true;

        return false;
    }

    public function check_member_nameOp()
    {
        echo json_encode($this->checkMemberName($_GET['member_name']));
        exit;
    }

    private function checkMemberName($memberName)
    {
        // 判断store_joinin是否存在记录
        $count = (int) Model('store_joinin')->getStoreJoininCount(array(
            'member_name' => $memberName,
        ));
        if ($count > 0)
            return false;

        return ! Model('member')->getMemberCount(array(
            'member_name' => $memberName,
        ));
    }
    /**
     * 验证店铺名称是否存在
     */
    public function ckeck_store_nameOp() {
        /**
         * 实例化卖家模型
         */
        $where = array();
        $where['store_name'] = $_GET['store_name'];
        $where['store_id'] = array('neq', $_GET['store_id']);
        $store_info = Model('store')->getStoreInfo($where);
        if(!empty($store_info['store_name'])) {
            echo 'false';
        } else {
            echo 'true';
        }
    }
	    /**
     * 验证店铺名称是否存在
     */
    private function ckeckStoreName($store_name) {
    	/**
    	 * 实例化卖家模型
    	 */
    	$where = array();
    	$where['store_name'] = $store_name;
    	$store_info = Model('store')->getStoreInfo($where);
    	if(!empty($store_info['store_name'])) {
    		return false;
    	} else {
    		return true;
    	}
    }

    public function store_importOp(){
		Tpl::showpage('store_import');    
    }
    
    public function store_import_csvOp(){
    	if (isset($_POST['import'])) {
    		$file = $_FILES['csv_stores'];
    		$file_type = substr(strstr($file['name'], '.'), 1);
    		
			//上传文件存在判断
			if(empty($file['name'])){
    			showMessage('请选择要上传csv的文件!','','html','error');
			}
    		
    		// 检查文件格式
    		if ($file_type != 'csv') {
    			showMessage('文件格式不对,请重新上传!','','html','error');
    			exit;
    		}

    		$handle = fopen($file['tmp_name'], "r");
		    $result = $this->input_csv($handle); //解析csv 
		    $rows = count($result); 
		    if($rows == 0){ 
    			showMessage('没有任何数据!','','html','error');
		        exit; 
		    } 

		    $scounter = 0;
		    
		    $storeModel = model('store');
		    
    		for ($i = 1; $i < $rows; $i++) { 
    			//循环获取各字段值 
    			$store_name = iconv('gb2312', 'utf-8', $result[$i][0]);
    			$member_name = iconv('gb2312', 'utf-8', $result[$i][1]);
    			$seller_name = iconv('gb2312', 'utf-8', $result[$i][2]);
    			$password = iconv('gb2312', 'utf-8', $result[$i][3]);
    			$store_company_name = iconv('gb2312', 'utf-8', $result[$i][4]);
    			$company_name = iconv('gb2312', 'utf-8', $result[$i][5]);
    			$company_address = iconv('gb2312', 'utf-8', $result[$i][6]);
    			$store_address = iconv('gb2312', 'utf-8', $result[$i][7]);
    			$store_zip = iconv('gb2312', 'utf-8', $result[$i][8]);
    			$store_qq = iconv('gb2312', 'utf-8', $result[$i][9]);
    			
    			$store_ww = iconv('gb2312', 'utf-8', $result[$i][10]);    			
    			$store_phone = iconv('gb2312', 'utf-8', $result[$i][11]);
    			$company_employee_count = iconv('gb2312', 'utf-8', $result[$i][12]);
    			$company_registered_capital = iconv('gb2312', 'utf-8', $result[$i][13]);
    			$contacts_name = iconv('gb2312', 'utf-8', $result[$i][14]);
    			$contacts_phone = iconv('gb2312', 'utf-8', $result[$i][15]);
    			$contacts_email = iconv('gb2312', 'utf-8', $result[$i][16]);
    			$business_licence_number = iconv('gb2312', 'utf-8', $result[$i][17]);
    			$business_licence_address = iconv('gb2312', 'utf-8', $result[$i][18]);
    			$business_licence_start = iconv('gb2312', 'utf-8', $result[$i][19]);
    			
    			$business_licence_end = iconv('gb2312', 'utf-8', $result[$i][20]);    			
    			$business_sphere = iconv('gb2312', 'utf-8', $result[$i][21]);
    			$organization_code = iconv('gb2312', 'utf-8', $result[$i][22]);
    			$bank_account_name = iconv('gb2312', 'utf-8', $result[$i][23]);
    			$bank_account_number = iconv('gb2312', 'utf-8', $result[$i][24]);
    			$bank_name = iconv('gb2312', 'utf-8', $result[$i][25]);
    			$bank_code = iconv('gb2312', 'utf-8', $result[$i][26]);
    			$bank_address = iconv('gb2312', 'utf-8', $result[$i][27]);
    			$is_settlement_account = iconv('gb2312', 'utf-8', $result[$i][28]);
    			$settlement_bank_account_name = iconv('gb2312', 'utf-8', $result[$i][29]);
    			
    			$settlement_bank_account_number = iconv('gb2312', 'utf-8', $result[$i][30]);
    			$settlement_bank_name = iconv('gb2312', 'utf-8', $result[$i][31]);
    			$settlement_bank_code = iconv('gb2312', 'utf-8', $result[$i][32]);
    			$settlement_bank_address = iconv('gb2312', 'utf-8', $result[$i][33]);
    			$tax_registration_certificate = iconv('gb2312', 'utf-8', $result[$i][34]);
    			$taxpayer_id = iconv('gb2312', 'utf-8', $result[$i][35]);
    			$joinin_year = iconv('gb2312', 'utf-8', $result[$i][36]);

    			if(!$this->ckeckStoreName($store_name))
    			{
    				continue;
    			}
    			if(!$this->checkMemberName($member_name))
    			{
    				continue;    				
    			}
    			if(!$this->checkSellerName($seller_name))
    			{
    				continue;
    			}    			

    			try
    			{
    				$memberId = model('member')->addMember(array(
    						'member_name' => $member_name,
    						'member_passwd' => $password,
    						'member_email' => '',
    				));
    			}
    			catch (Exception $ex)
    			{
    				showMessage('店主账号新增失败', '', 'html', 'error');
    			}
    			
    			$storeModel = model('store');
    			
    			$saveArray = array();
    			$saveArray['store_name'] = $store_name;
    			$saveArray['grade_id'] = 1;
    			$saveArray['member_id'] = $memberId;
    			$saveArray['member_name'] = $member_name;
    			$saveArray['seller_name'] = $seller_name;
    			$saveArray['bind_all_gc'] = 0;
    			$saveArray['store_state'] = 1;
    			$saveArray['store_time'] = time();
    			$saveArray['store_company_name'] = $store_company_name;
    			$saveArray['store_address'] = $store_address;
    			$saveArray['store_zip'] = $store_zip;
    			$saveArray['store_qq'] = $store_qq;
    			$saveArray['store_ww'] = $store_ww;
    			$saveArray['store_phone'] = $store_phone;
    			
    			$storeId = $storeModel->addStore($saveArray);

	            model('seller')->addSeller(array(
	                'seller_name' => $seller_name,
	                'member_id' => $memberId,
	                'store_id' => $storeId,
	                'seller_group_id' => 0,
	                'is_admin' => 1,
	            ));

	            $store_joinModel = model('store_joinin');
    			$save_joinArray = array();
    			$save_joinArray['member_id'] = $memberId;
    			$save_joinArray['member_name'] = $member_name;
    			$save_joinArray['company_name'] = $company_name;
    			$save_joinArray['company_address'] = $company_address;
    			$save_joinArray['company_address_detail'] = $store_address;
    			$save_joinArray['company_phone'] = $store_phone;
    			$save_joinArray['company_employee_count'] = $company_employee_count;
    			$save_joinArray['company_registered_capital'] = $company_registered_capita;
    			$save_joinArray['contacts_name'] = $contacts_name;
    			$save_joinArray['contacts_phone'] = $contacts_phone;
    			$save_joinArray['contacts_email'] = $contacts_email;
    			$save_joinArray['business_licence_number'] = $business_licence_number;
    			$save_joinArray['business_licence_address'] = $business_licence_address;
    			$save_joinArray['business_licence_start'] = $business_licence_start;
    			$save_joinArray['business_licence_end'] = $business_licence_end;
    			$save_joinArray['business_sphere'] = $business_sphere;
    			$save_joinArray['organization_code'] = $organization_code;
    			$save_joinArray['general_taxpayer'] = $general_taxpayer;
    			$save_joinArray['bank_account_name'] = $bank_account_name;
    			$save_joinArray['bank_account_number'] = $bank_account_number;
    			$save_joinArray['bank_name'] = $bank_name;
    			$save_joinArray['bank_code'] = $bank_code;
    			$save_joinArray['bank_address'] = $bank_address;
    			$save_joinArray['is_settlement_account'] = $is_settlement_account;
    			if($is_settlement_account == '是')
    			{
    				//2独立
    				$save_joinArray['is_settlement_account'] = 2;
    				$save_joinArray['settlement_bank_account_name'] = $settlement_bank_account_name;
    				$save_joinArray['settlement_bank_account_number'] = $settlement_bank_account_number;
    				$save_joinArray['settlement_bank_name'] = $settlement_bank_name;
    				$save_joinArray['settlement_bank_code'] = $settlement_bank_code;    	
    				$save_joinArray['settlement_bank_address'] = $settlement_bank_address;   			
    			}
    			else 
    			{ 				
    				//1非独立
    				$save_joinArray['is_settlement_account'] = 1;
    				$save_joinArray['settlement_bank_account_name'] = $bank_account_name;
    				$save_joinArray['settlement_bank_account_number'] = $bank_account_number;
    				$save_joinArray['settlement_bank_name'] = $bank_name;
    				$save_joinArray['settlement_bank_code'] = $bank_code;    	
    				$save_joinArray['settlement_bank_address'] = $bank_address;   
    			}
    			$save_joinArray['tax_registration_certificate'] = $tax_registration_certificate;
    			$save_joinArray['taxpayer_id'] = $taxpayer_id;
    			$save_joinArray['seller_name'] = $seller_name;
    			$save_joinArray['store_name'] = $store_name;
    			$save_joinArray['joinin_state'] = 40;
    			$save_joinArray['joinin_year'] = $joinin_year;
    			$save_joinArray['company_name'] = $company_name;
    			$save_joinArray['company_name'] = $company_name;
    			
    			
    			$store_joinModel->save($save_joinArray);
	            
	            // 添加相册默认
	            $album_model = Model('album');
	            $album_arr = array();
	            $album_arr['aclass_name'] = '默认相册';
	            $album_arr['store_id'] = $storeId;
	            $album_arr['aclass_des'] = '';
	            $album_arr['aclass_sort'] = '255';
	            $album_arr['aclass_cover'] = '';
	            $album_arr['upload_time'] = time();
	            $album_arr['is_default'] = '1';
	            $album_model->addClass($album_arr);

	            //插入店铺扩展表
	            $model = Model();
	            $model->table('store_extend')->insert(array('store_id'=>$storeId));
	            
	            $scounter++;
	            
    		} 
    		//$data_values = substr($data_values,0,-1); //去掉最后一个逗号 
    		fclose($handle); //关闭指针 

            showMessage('操作成功,成功导入 '.strval($scounter).' 条数据' , urlAdmin('store', 'store'));
            return;
    		
		    /*
    		$row = 0;    		
    		while ($data = fgetcsv($handle, 10000)) {
    			$row++;
    			if ($row == 1) continue;
    			$num = count($data);
    			for ($i = 0; $i < $num; $i++) {
    				$t=iconv('gb2312', 'utf-8', $data[$i]); 
    				echo $t.
    				"<br>";
    			}
    		}
    		fclose($handle);
    		*/
    	}
    }
    
    /*
     * 解析csv
     */
    private function input_csv($handle) {
    	$out = array ();
    	$n = 0;
    	while ($data = fgetcsv($handle, 10000)) {
    		$num = count($data);
    		for ($i = 0; $i < $num; $i++) {
    			$out[$n][$i] = $data[$i];
    		}
    		$n++;
    	}
    	return $out;
    }




    /*
     * 批量绑定店铺分类, 排除第一层分类 $class1_id 为第一层分类
     */
    private function batch_bind_class_byid($store_id,$class1_id, $commis_rate){
        $out = array();

        $commis_rate = intval($commis_rate);
        if(intval($class1_id) > 0){
            $model_goods_class = Model('goods_class');
            $goods_class1 = $model_goods_class->getChildClass($class1_id);//getChildClass

            foreach($goods_class1 as $v){
                if($v['gc_id'] != $class1_id){//排除第一层
                    if($v['gc_parent_id'] == $class1_id){ //第二层分类
                        $out[] = array('store_id'=> $store_id, 'commis_rate' => $commis_rate, 'class_1'=> $class1_id , 'class_2'=> $v['gc_id'], 'class_3'=>0 , 'state'=>1);
                    }else{//第三层分类
                        $out[] = array('store_id'=> $store_id, 'commis_rate' => $commis_rate, 'class_1'=> $class1_id , 'class_2'=> $v['gc_parent_id'], 'class_3'=>$v['gc_id'], 'state'=>1 );
                    }
                }
            }
        }
        return $out;
    }



    public function store_bind_initOp() {
        $store_id = intval($_POST['store_id']);
        $commis_rate = intval($_POST['new_commis_rate']);
        if($commis_rate < 0 || $commis_rate > 100) {
            showMessage(L('param_error'), '');
        }

        $model_store	= Model('store');
        $store_info = $model_store->getStoreInfoByID($store_id);
        if(empty($store_info)) {
            showMessage(L('param_error'),'','','error');
        }

        //绑定商城分类
        $get_goods_class = Model('store_class')->getStoreClassInfo(array('sc_id' => $store_info['sc_id']));
        if($get_goods_class){
            $top_goods_class = $get_goods_class['gc_bind'];
        }else{
            $top_goods_class = 0;
        }

        $model_store_bind_class = Model('store_bind_class');


        //删除旧分类
        $model_store_bind_class->delStoreBindClass(array('store_id' => $store_id));


        //批量绑定子分类下的经营项目
        $sub_class = $this->batch_bind_class_byid($store_id, $top_goods_class , $commis_rate);
        $result = $model_store_bind_class->addStoreBindClassAll($sub_class);

        if($result) {
            $this->log('更新店铺经营类目，店铺编号:'.$store_id);
            $data['result'] = true;
            $data['message'] = '经营类目删除失败';
        } else {
            $data['result'] = false;
            $data['message'] = '操作失败';
        }
        echo json_encode($data);die;
    }
    
    
    /**
     * 供应商级别类型修改列表
     * @author Aletta 
     * @time 2017.06.05
     */
    public function type_level_listOp(){
        $model = Model();
        //获取当前登录后台管理员 城市中心地区
        $admininfo = $this->getAdminInfo();
        //获取供应商信息数据
        $new_list = array();
        $where = $admininfo['cityid'] > 0 ? "supplier.first_city_id = '".intval($admininfo['cityid'])."' and ":" ";
        $where.= "store_joinin.city_center = store_joinin.first_city_id and store_joinin.joinin_state = '44'";
        $where.= empty($_GET['owner_and_name']) ? "":" and supplier.member_name = '".$_GET['owner_and_name']."'";
		$where.= empty($_GET['joinin_state']) ? "":"and  supply_level = '".$_GET['joinin_state']."'";
        $where.= empty($_GET['company_name']) ? "":"and  supplier.company_name like '%".$_GET['company_name']."%'";
		$field = "supplier.company_name,supplier.member_id,member.member_name,member.member_time,supplier.level,supplier.type_json,supplier.end_time";
		$on =  'supplier.member_id = member.member_id,supplier.member_id = store_joinin.member_id';
        $supply_list = $model->table("supplier,member,store_joinin")->field($field)->join('left')->on($on)->where($where)->page(10)->select();
        if(!empty($supply_list) && is_array($supply_list)){
            foreach ($supply_list as $v){
                $type_data = json_decode($v['type_json'],true);
                $level_one = array();
                $type_string = '尚未选择';
                if(!empty($type_data) && is_array($type_data)){
                    $level_one = array();
                    foreach ($type_data as $k_type=>$v_type){
                        $type_data = $model->table("supplier_type")->field("type_name")->where(array("id"=>$k_type))->find();
                        $level_one[] = $type_data['type_name'];
                    }
                    $type_string = implode(',',$level_one);
                }
                $v['type_string'] = $type_string;
                $new_list[] = $v;
            }
        }
        Tpl::output('supply_list',$new_list);
		Tpl::output('page',$model->showpage('2'));
        Tpl::showpage('store_type_level');
    }
    
    
    /**
     * 审核详细页
     * @author Aletta 
     * @time 2017.06.05
     */
    public function store_type_level_detailOp(){
        $member_id = $_GET['member_id'];
        $model = Model();
        $field = "supplier.member_id,member.member_name,member.member_time,supplier.level,supplier.type_json,supplier.end_time";
        $on =  'supplier.member_id = member.member_id';
        $supply_list = $model->table("supplier,member")->field($field)->join('left')->on($on)->where("supplier.member_id = '".$member_id."'")->find();
        if(empty($supply_list)){
            showMessage('供应商参数错误','index.php?act=store&op=type_level_list');exit;
        }
        
        $member_supplier_type = json_decode($supply_list['type_json'],true);
        //获取供应商类型数据
        $supplier_type_data = $model->table('supplier_type')->field('id,type_name')->where(array("level"=>1,"parent_id"=>0))->select();
        $supplier_type_father = array();
        $supplier_type_sun = array();
        if(!empty($member_supplier_type) && is_array($member_supplier_type)){
            foreach ($member_supplier_type as $key=>$v){
                $supplier_type_father[] = $key;
                foreach ($v as $v_sun){
                    $supplier_type_sun[] = $v_sun;
                }
            }
        }
        $supplier_type_list = array();
        if(!empty($supplier_type_data) && is_array($supplier_type_data)){
            foreach ($supplier_type_data as $val){
                $supplier_type_list_data = Model("supplier_type")->field('id,type_name')->where(array("level"=>2,"parent_id"=>$val['id']))->select();
                $supplier_type_list[$val['id']] = $supplier_type_list_data;
            }
        }
        Tpl::output('supplier_type_father', $supplier_type_father);
        Tpl::output('supplier_type_sun', $supplier_type_sun);
        $member_model = Model('member');
        Tpl::output('supplier_type_data',$member_model->get_supply_type($member_id));
        Tpl::output('supplier_level',$member_model->get_supply_level($member_id));
        Tpl::output('supplier_time_data',array('member_time'=>$supply_list['member_time'],'supply_end_time'=>$supply_list['end_time']));
        Tpl::output('store_joinin_num', $model->table('store_joinin')->where(array("member_id"=>$member_id))->count());
        Tpl::output('supplier_type', $supplier_type_data);
        Tpl::output('supplier_name', $supply_list['member_name']);
        Tpl::output('member_supplier_level',$supply_list['level']);
        Tpl::output('member_id', $member_id);
        Tpl::output('supplier_list', $supplier_type_list);
        Tpl::showpage('store_type_level.detail');
    }
    
    
    /**
     * 审核数据保存
     * @author Aletta
     * @time 2017.06.05
     */
    public function store_type_level_verifyOp() {
        //必须在审核通过的时候才做次操作
        $model = Model();
        $where = "supplier.member_id='".$_POST['member_id']."' and store_joinin.city_center = store_joinin.first_city_id and store_joinin.joinin_state = '44'";
        $on =  'supplier.member_id = store_joinin.member_id';
        $member_info = $model->table("supplier,store_joinin")->join('left')->on($on)->where($where)->find();
        if(!empty($member_info)){
            $data_u['type_json'] = empty($_POST['supplier_type']) ? '':json_encode($_POST['supplier_type']);
            $data_u['level'] = $_POST['supply_level'];
            if($_POST['supply_level'] == '3'){
                $data_u['end_time'] = time()-(24*3600-1);
            }else{
                $data_u['end_time'] = strtotime($_POST['supply_end_time'])+24*3600-1;
            }
            $upmember = $model->table('supplier')->where("member_id='".$_POST['member_id']."'")->update($data_u);
            if($upmember){
                //处理供应商淘汰下架所有商品
                if($_POST['supply_level'] == '3'){
                    $sorte_id = $model->table('store')->field('store_id')->where("member_id = '".$_POST['member_id']."'")->find();
                    if(!empty($sorte_id['store_id'])) {
                        //根据店铺状态修改该店铺所有商品状态
                        $model_goods = Model('goods');
                        $model_goods->editProducesOffline_s(array('store_id' => $sorte_id['store_id']));
                    }
                }
                $send_json = json_encode($this->processing_supply_data($_POST['supplier_type'],$_POST['member_id']));
                $url=YMA_WEBSERVICE_SUPPLY_TYPE;
                $return_json = WebServiceUtil::getDataByCurl($url, $send_json, 0);
                $return = json_decode($return_json,true);
                $this->log('供应商类型推送结果：供应商ID：' . $_POST['member_id'] . '，结果：' . $return['resultCode'] . '原因：' . $return['resultMsg'], 1);
                showMessage('供应商类型级别修改成功','index.php?act=store&op=type_level_list');
            }
        }else{
            showMessage('供应商参数错误','index.php?act=store&op=type_level_list');
        }
    }
    /*
     * 新增临时供应商推送给合同和采购系统，商城系统没有用到
     * 
     */
    
    public function newtemporary_addOp(){
            $supplyinfo = array();
            $supplyinfoForYHM = array();
            $supplyinfoForCT = array();
            $model = Model();
//            $city_center = $model->table('city_centre')->select();
//            Tpl::output('city', $city_center);
            //生成企业编号传到到前端
            $supply_code="lsgys".time();
            Tpl::output('supply_code', $supply_code);
            Tpl::output('member_name', time());
        if (chksubmit()){
            $admininfo = $this->getAdminInfo();
            if($supplier_member_name=$model->table('supplier')->where(array("member_name"=>$_POST['member_name']))->select()){
                  showMessage('店主账号已被占用', '', 'html', 'error');
            }
            if($supplier_member_name=$model->table('supplier')->where(array("company_name"=>$_POST['company_name']))->select()){
                  showMessage('供应商名称已被占用', '', 'html', 'error');
            }
            if($supplier_code=$model->table('supplier')->where(array("code"=>$_POST['supply_code']))->select()){          
                 showMessage('营业执照号或组织机构代码'.$supply_code.'已被占用', '', 'html', 'error');
            }

            $admincity = $admininfo['cityid'] == '0' ? '1':$admininfo['cityid'];

            $data = array(
                'company_name'=>empty($_POST['company_name']) ? " " : $_POST['company_name'],//公司名称
                'code'=>empty($_POST['supply_code']) ? " ": $_POST['supply_code'],//企业号码
                'contacts_name'=>empty($_POST['contacts_name']) ? " ": $_POST['contacts_name'],//联系人姓名
                'contacts_phone'=>empty($_POST['contacts_phone']) ? " " : $_POST['contacts_phone'],//电话
                'contacts_email'=>empty($_POST['contacts_email']) ? " " : $_POST['contacts_email'],//邮箱
                'bank_account_name'=>empty($_POST['bank_account_name']) ? " " :  $_POST['bank_account_name'],//银行开户名
                'bank_account_number'=>empty($_POST['bank_account_number']) ? " " : $_POST['bank_account_number'],//公司银行账号
                'settlement_bank_address'=>empty($_POST['settlement_bank_address']) ? " " : $_POST['settlement_bank_address'],//结算开户银行所在地
                'city_center_list'=>$admincity,//城市公司
                'member_name'=>$_POST['member_name'],//供应商账号
                'supply_type'=>3, //默认固定为临时供应商
                'first_city_id'=>$admincity,//首次城市公司
                'add_time'=>time(),//添加时间
                'add_user'=>$admininfo['name'],  //添加用户
                'bank_name'=>$_POST['bank_name']  //开户银行支行名称  
            );
            //插入供应商表
            $rs=$model->table("supplier")->insert($data);
                
                
            if($rs){
                //组织给合同系统的供应商数据
                $areaArray = explode(" ",$_POST['settlement_bank_address']);
                $supply_province = empty($_POST['settlement_bank_address'])?" ":$areaArray[0];//省

                $cityinfo =  $model->table("city_centre")->where(array("id"=>$admincity))->find(); 
                $p_org_id=$cityinfo['zt_city_code'];//城市公司名称-->更改为城市公司战图编码

                //通过战途编码到采购系统获取到城市公司下的所有分公司
                //处理事业本部问题
                if(preg_match("/\x20*https?\:\/\/.*/i","",$_SERVER['SERVER_NAME'])){
                    $dbName = "vs_purchase2";
                }else{
                    $dbName = "vs_purchase_t2";
                }
                if($p_org_id == 'W000001'){
                    $p_org_id_list = array();
                    $p_org_id_data = $model->table("city_centre")->select();
                    if(!empty($p_org_id_data) && is_array($p_org_id_data)){
                        foreach ($p_org_id_data as $vl){
                            $p_org_id_list[] = "'".$vl['zt_city_code']."'";
                        }
                        $Eas_Seq_array = Model()->query("select distinct vanke_pj_contract.contract_city_code,vanke_pj_contract.contract_city_name  from ".$dbName.".vanke_pj_contract where vanke_pj_contract.city_code in(".implode(',',$p_org_id_list).")");
                    }
                }else{
                    $Eas_Seq_array = Model()->query("select distinct vanke_pj_contract.contract_city_code,vanke_pj_contract.contract_city_name  from ".$dbName.".vanke_pj_contract where vanke_pj_contract.city_code= '".$p_org_id."'");   //生产环境地址 ：vs_purchase2 测试环境地址 vs_purchase_t2
                }
                $tmp = array();
                if(!empty($Eas_Seq_array) && is_array($Eas_Seq_array)){
                    foreach ($Eas_Seq_array as $vls){
                        $tmp[] = array(
                            'p_org_id'=>empty($vls['contract_city_code']) ? 'W000001':$vls['contract_city_code'],
                            'vendor_site_code'=>mb_substr($vls['contract_city_name'],0,5)
                        );
                    }
                }else{
                    $supplyinfo['error']= "城市信息数据有误！";
                    return $supplyinfo;
                }
                $supplyinfoForCT['p_vendor_name']=empty($_POST['company_name']) ? " " : $_POST['company_name'];//供应商名称 
                $supplyinfoForCT['p_vendor_number']=empty($_POST['supply_code']) ? " ":$_POST['supply_code'] ;//企业营业号码
                $supplyinfoForCT['p_person_name']=empty($_POST['contacts_name']) ? " ": $_POST['contacts_name'];//联系人
                $supplyinfoForCT['p_tel_number']=empty($_POST['contacts_phone']) ? " " : $_POST['contacts_phone'] ;//联系人电话
                $supplyinfoForCT['p_e_mail']=  empty($_POST['contacts_email']) ? " " : $_POST['contacts_email'];//联系人邮箱
                $supplyinfoForCT['p_bank_account_name']=empty($_POST['bank_account_name']) ? " " :  $_POST['bank_account_name'];//开户行名称
                $supplyinfoForCT['p_bank_account_number']=empty($_POST['bank_account_number']) ? " " : preg_replace('# #','',$_POST['bank_account_number']);//银行帐号 格式：正数。如：29394848
                $supplyinfoForCT['p_province']=$supply_province;//省
                $supplyinfoForCT['p_loc_address']=empty($_POST['settlement_bank_address']) ? " " : $_POST['settlement_bank_address'];//详细地址
                $supplyinfoForCT['p_org_site']=$tmp; //城市公司下是分公司
                
                
                $supplyinfoForCT['p_country']='中国';//国家
                $supplyinfoForCT['p_bank_branch_name']=empty($_POST['bank_name']) ? " " :  $_POST['bank_name'];//分行
                $supplyinfoForCT['p_bank_name']=empty($_POST['bank_name']) ? " " :  $_POST['bank_name'];//银行名称
                
                $supplyinfo['ct_supply_info'] = $supplyinfoForCT;     
                $TO_CT_URL = CONTRACT_WS_INSERT_SUPPLIER;
                $supplyinfo_json = json_encode($supplyinfo['ct_supply_info']); 
                $to_ct_result_json = WebServiceUtil::getDataByCurl($TO_CT_URL, $supplyinfo_json, 0);
                $to_ct_result = json_decode($to_ct_result_json,true);
                CommonUtil::insertData2PushLog($to_ct_result, '', $supplyinfo_json, $TO_CT_URL, 15);    
                //将会同的返回值放到结果集中
                $supplyinfoForCTS['resultCode'] = $to_ct_result['resultCode'];
                $supplyinfoForCTS['resultMsg']  = $to_ct_result['resultMsg'];//返回信息
                $supplyinfoForCTS['supplierNum']  = $to_ct_result['supplierNum'];//返回信息
                $supplyinfo['ct_supply_result'] =$supplyinfoForCTS;  
                
                if(!empty($supplyinfoForCTS)){
                    $contract_type_ht= array(
                         'contract_type'=> $to_ct_result['resultCode']==200 ? 1 : 2,//推送合同状态（1：成功，2：失败，3：未推送）
                         'contract_res'=>$to_ct_result['resultMsg'],//推送结果
                         'contract_time'=>time(),
                         'contract_code'=>  empty($to_ct_result['supplierNum']) ? $_POST['supply_code']: $to_ct_result['supplierNum']
                    );
                    $model->table("supplier")->where(array("member_name"=>$_POST['member_name']))->update($contract_type_ht);
                }
                
                $supply_org = $this->getCityInfoByMemberId(",","bukrs",$admincity);
                //推送采购后台
                $supplyinfoForYHM['supply_code']=empty($_POST['supply_code']) ? " ":$_POST['supply_code'];//供应商编号
                $supplyinfoForYHM['supply_account']=empty($_POST['member_name']) ? " ": $_POST['member_name'];//供应商姓名=账号
                $supplyinfoForYHM['supply_eas_code']= empty($_POST['supply_code']) ? " ":$_POST['supply_code'];//供应商eas编码
                $supplyinfoForYHM['supply_name']=empty($_POST['company_name']) ? " " : $_POST['company_name'];//供应商公司名称
                $supplyinfoForYHM['supply_type']=3; //供应商类型(1:注册认证，0：后台添加)
                $supplyinfoForYHM['supply_org']=$supply_org;
                $supplyinfoForYHM['supply_mobile']=empty($_POST['contacts_phone']) ? " ": $_POST['contacts_phone'];//供应商手机号
                $supplyinfoForYHM['supply_mail']=empty($_POST['contacts_email']) ? " " : $_POST['contacts_email'];//供应商邮箱
                $supplyinfoForYHM['supply_address']=empty($_POST['settlement_bank_address']) ? " " : $_POST['settlement_bank_address'];//供应商地址
                $supplyinfoForYHM['glass_state']=1;//状态(1:新增修改，0：删除失效)
                $supplyinfoForYHM['supplierNum']=empty($to_ct_result['supplierNum']) ? $_POST['supply_code']: $to_ct_result['supplierNum'];//如果公司名称重复返回企业编号推送eas和采购,为空就传企业证据号码冗余

                $supplyinfo['ymh_supply_info'] = $supplyinfoForYHM;
                $TO_PUR_URL = YMA_WEBSERVICE_UPDATE_OR_SAVE_SUPPLIER;
                $supplyinfo_json = json_encode($supplyinfo['ymh_supply_info']);

                $to_pur_result_json = WebServiceUtil::getDataByCurl($TO_PUR_URL, $supplyinfo_json, 0);
                $to_pur_result = json_decode($to_pur_result_json,true);
                CommonUtil::insertData2PushLog($to_pur_result, '', $supplyinfo_json, $TO_PUR_URL, 5);

                 if(!empty($to_pur_result_json)){
                    $contract_type_cg= array(
                         'purchase_type'=> $to_pur_result['resultCode']==0 ? 1 : 2,//推送合同状态（1：成功，2：失败，3：未推送）
                         'purchase_res'=>$to_pur_result['resultMsg'],//推送结果
                         'purchase_time'=>time()
                    );
                    $model->table("supplier")->where(array("member_name"=>$_POST['member_name']))->update($contract_type_cg);
                    showMessage('添加成功', '', 'html', 'succ');
                }

                }else{
                     showMessage('供应商添加失败', '', 'html', 'error');
                }
                    
                    
                }
                
                Tpl::showpage('newtemporary_add');
    }

     public function getCityInfoByMemberId($split,$field="bukrs",$city_id){
        $model = new  Model(); 
        $city_name = $model->table("city_centre")->field($field)->where(array("id"=>$city_id))->find();  
        return $city_name[$field];

    }


    public function store_type_editOp(){
        $model = Model();
        $admininfo = $this->getAdminInfo();
        //获取供应商信息数据
        $new_list = array();
        $where = $admininfo['cityid'] > 1 ? "supplier.first_city_id = '".intval($admininfo['cityid'])."' and ":" ";
        $where.= "store_joinin.city_center = store_joinin.first_city_id and store_joinin.joinin_state = '44'";
        $where.= empty($_GET['owner_and_name']) ? "":" and supplier.member_name = '".$_GET['owner_and_name']."'";
        $where.= empty($_GET['owner_and_store']) ? "":" and store.store_name = '".$_GET['owner_and_store']."'";
        $field = "supplier.member_id,supplier.member_name,store.store_name,sc_name";
        $on =  'supplier.member_id = store_joinin.member_id,supplier.member_id = store.member_id';
        $supply_list = $model->table("supplier,store_joinin,store")->field($field)->join('left')->on($on)->where($where)->page(10)->select();
        Tpl::output('supply_list',$supply_list);
        Tpl::output('page',$model->showpage('2'));
        Tpl::showpage('store_type_edit');
    }


    public function store_type_detailOp(){
        $member_id = $_GET['member_id'];
        $model = Model();
        $where = "supplier.member_id = '".$member_id."'";
        $field = "supplier.member_id,supplier.member_name,store.store_name,sc_name,store_class_names,store_class_commis_rates";
        $on =  'supplier.member_id = store_joinin.member_id,supplier.member_id = store.member_id';
        $supply_list = $model->table("supplier,store_joinin,store")->field($field)->join('left')->on($on)->where($where)->find();
        if(empty($supply_list)){
            showMessage('供应商参数错误','index.php?act=store&op=store_type_edit');exit;
        }
        $class_data = $model->table('goods_class')->where("gc_parent_id = '0'")->select();
        $store_class_names = unserialize($supply_list['store_class_names']);
        if(!empty($class_data) && is_array($class_data)){
            $new_class = array();
            foreach ($class_data as $vl){
                if(in_array($vl['gc_name'].',',$store_class_names)){
                    $vl['is_type'] = 1;
                }else{
                    $vl['is_type'] = 2;
                }
                $new_class[] = $vl;
            }
        }
        Tpl::output('class_list',$new_class);
        Tpl::output('list',$supply_list);
        Tpl::showpage('store_type_edit.detail');
    }

    public function store_type_verifyOp(){
        if(!empty($_POST)){
            $data = array('store_class_ids'=>$_POST['class_id']);
            $upda_join_store = $this->get_store_join($data,$_POST['member_id']);
            if(!empty($upda_join_store)){
                $model = Model();
                $join_res = $model->table('store_joinin')->where("member_id = '".$_POST['member_id']."'")->update($upda_join_store);
                if($join_res){
                    $model_store_joinin = Model('store_joinin');
                    $joinin_detail_where = "store_joinin.member_id = '".$_POST['member_id']."' and store_joinin.city_center = store_joinin.first_city_id";
                    $joinin_detail = $model_store_joinin->getOne($joinin_detail_where);
                    $store_data = $model->table('store')->where("member_id = '".$_POST['member_id']."'")->find();
                    $store_bind_class_array = array();
                    $store_bind_class = unserialize($joinin_detail['store_class_ids']);
                    $store_bind_commis_rates = explode(',', $joinin_detail['store_class_commis_rates']);
                    for($i=0, $length=count($store_bind_class); $i<$length; $i++) {
                        list($class1, $class2, $class3) = explode(',', $store_bind_class[$i]);
                        $store_bind_class_array[] = array(
                            'store_id' => $store_data['store_id'],
                            'commis_rate' => $store_bind_commis_rates[$i],
                            'class_1' => $class1,
                            'class_2' => intval($class2),
                            'class_3' => intval($class3),//@cary fix  自动赋0
                            'state' => 1
                        );
                    }
                    $model_store_bind_class = Model('store_bind_class');
                    $model_store_bind_class->addStoreBindClassAll($store_bind_class_array);

                    //批量绑定子分类下的经营项目
                    $sub_class = $this->batch_bind_class_byid($store_data['store_id'], $class1, 0);
                    $model_store_bind_class->addStoreBindClassAll($sub_class);
                    showMessage('修改成功', 'index.php?act=store&op=store_type_edit', 'html', 'succ');
                }
            }else{
                showMessage('供应商店铺数据没有任何变化修改，请确认', '', 'html', 'error');
            }
        }
    }


    private function get_store_join($data=array(),$member_id){
        if (!empty($data['store_class_ids'])) {
            $model = Model();
            $where_join = "store_state in('" . STORE_JOIN_STATE_RZHKD . "','" . STORE_JOIN_STATE_FINAL . "') and member_id = '" . $member_id . "'";
            $store_join = $model->table('store_joinin')->where($where_join)->order("store_state desc")->find();
            //获取原始数据
            if(!empty($store_join)){
                if(!empty($data['store_class_ids'])){
                    $new_class_id = array_unique(array_merge($data['store_class_ids'],explode(',',$store_join['sc_id'])));
                }else{
                    $new_class_id = explode(',',$store_join['sc_id']);
                }
            }else{
                $new_class_id = $data['store_class_ids'];
            }
            //定义初始化class数组
            $store_class_ids = array();
            $store_class_names = array();
            if (is_array($new_class_id)) {
                foreach ($new_class_id as $value) {
                    $store_class_ids[] = $value . ',';
                    $class_data = $model->table("goods_class")->field('gc_name')->where("gc_id = '" . $value . "'")->find();
                    $store_class_names[] = $class_data['gc_name'] . ',';
                }
            }
            //取最小级分类最新分佣比例
            $sc_ids = array();
            foreach ($store_class_ids as $v) {
                $v = explode(',', trim($v, ','));
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
            $param = array();
            //序列化class数据
            $param['store_class_ids'] = serialize($store_class_ids);
            $param['store_class_names'] = serialize($store_class_names);

            if (is_array($store_class_commis_rates)) {
                $param['store_class_commis_rates'] = implode(',', $store_class_commis_rates);
            } else {
                $param['store_class_commis_rates'] = $store_class_commis_rates;
            }

            //取店铺等级信息
            $grade_list = rkcache('store_grade', true);
            if (!empty($grade_list[$data['sg_id']])) {
                $param['sg_id'] = $data['sg_id'];
                $param['sg_name'] = $grade_list[$data['sg_id']]['sg_name'];
                $param['sg_info'] = serialize(array('sg_price' => $grade_list[$data['sg_id']]['sg_price']));
            }
            if (!empty($grade_list[$data['sg_id']])) {
                $param['sg_id'] = $data['sg_id'];
                $param['sg_name'] = $grade_list[$data['sg_id']]['sg_name'];
                $param['sg_info'] = serialize(array('sg_price' => $grade_list[$data['sg_id']]['sg_price']));
            }

            if (!empty($new_class_id) && is_array($new_class_id)) {
                $sc_id_data = $sc_name_data = $sc_bail_data = array();
                foreach ($new_class_id as $v) {
                    $store_class_info = Model('store_class')->getStoreClassInfo(array('sc_id' => intval($v)));
                    if ($store_class_info) {
                        $sc_id_data[] = $store_class_info['sc_id'];
                        $sc_name_data[] = $store_class_info['sc_name'];
                        $sc_bail_data[] = $store_class_info['sc_bail'];
                    }
                }
                $param['sc_id'] = implode(',', $sc_id_data);
                $param['sc_name'] = implode(',', $sc_name_data);
                $param['sc_bail'] = implode(',', $sc_bail_data);
            }

            return $param;
        }
    }


    /**
     * 获取最精一个月内即将到期供应商数据
     * User: zhengguiyun
     * Date: 2017/10/20
     * Time: 下午4:16
     */
    public function getSupplierTimeEndOp(){
        Tpl::showpage('supplier.time.end.list');
    }

    /**
     * 获取最精一个月内即将到期供应商数据
     * User: zhengguiyun
     * Date: 2017/10/20
     * Time: 下午4:16
     */
    public function getSupplierTimeEndDataOp(){
        $page = empty($_GET['page']) ? '1':$_GET['page'];
        $model_admin = Model('admin');
        $admin_info = $this->getAdminInfo();
        $model = Model();
        $nums = '10';
        $limit = $page*$nums-$nums.','.$nums;
        if($admin_info['city_id'] == '0'){
            $where = "(end_time-unix_timestamp(now())) < (3600*24*30)";
        }else{
            $where = "(end_time-unix_timestamp(now())) < (3600*24*30) and concat(city_center_list,',') like '%".intval($admin_info['cityid'])."%'";
        }
        $where.= " and level != '3'";
        $field = "id,member_name,company_name,contacts_phone,contacts_email,FROM_UNIXTIME(end_time, '%Y-%m-%d') as js_time,( (end_time-unix_timestamp(now())) div (24*3600)) as days";
        $list_num = $model->table('supplier')->where($where)->count();
        $list = array(
            'code'  => 0,
            'msg'   => '',
            'count' => $model->table('supplier')->where($where)->count(),
            'data'  => $model->table('supplier')->field($field)->where($where)->order('days asc')->limit($limit)->select(),
        );
        echo json_encode($list);
    }

}
