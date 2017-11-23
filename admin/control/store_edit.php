<?php
/**
 * 店铺管理界面
 *
 ***/



class store_editControl extends SystemControl{
	const EXPORT_SIZE = 1000;
	public function __construct(){
		parent::__construct();
		Language::read('store,store_grade');
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
	 * 认证 待审核列表
	 */
	public function indexOp(){
            $model_city_centre = Model();
            $city_centreList=$model_city_centre->table("city_centre")->field("id,city_name")->select(); 
            Tpl::output('city_centreList',$city_centreList);    
            
                if($_GET['city_id']) {
                         $condition['city_center'] = $_GET['city_id'];
                }
		//店铺列表
		if(!empty($_GET['owner_and_name'])) {
			$condition['member_name'] = array('like','%'.$_GET['owner_and_name'].'%');
		}
		if(!empty($_GET['store_name'])) {
			$condition['company_name'] = array('like','%'.$_GET['store_name'].'%');
		}
		if(!empty($_GET['grade_id']) && intval($_GET['grade_id']) > 0) {
			$condition['sg_id'] = $_GET['grade_id'];
		}
		if(!empty($_GET['joinin_state']) && intval($_GET['joinin_state']) > 0) {
			$condition['joinin_state'] = $_GET['joinin_state'] ;
		} else {
			$num = "30,43,44,45";
			$condition['joinin_state'] = array('in',$num);
		}
		//获取当前登录后台管理员 城市中心地区
		$admininfo = $this->getAdminInfo();
		if($admininfo['cityid'] > 0){
			$condition['city_center'] = intval($admininfo['cityid']);
		}
		$model_store_joinin = Model('store_joinin_edit');
		$store_list = $model_store_joinin->where($condition)->page(10)->order('joinin_state asc')->select();
		
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
		Tpl::showpage('store_edit_joinin');
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
                $joinin_state_array[STORE_JOIN_STATE_VERIFY_FAIL] = '审核失败';
                $joinin_state_array[STORE_JOIN_STATE_FINAL] = '开店成功';
                $joinin_state_array[STORE_JOIN_STATE_RZHKD] = '开店新申请';
                break;
            case "auth":
                $joinin_state_array[STORE_JOIN_STATE_RZ] = '认证申请';
                $joinin_state_array[STORE_JOIN_STATE_RZSUCCESS] = '认证成功';
                $joinin_state_array[STORE_JOIN_STATE_FNO] = '认证拒绝';
                break;
        }

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
		$model_store_joinin = Model('store_joinin_edit');
                if(empty($_GET['city'])){
                    showMessage('城市公司参数错误','index.php?act=store&op=store_joinin2');exit;
                }
        $joinin_detail_where['member_id'] = $_GET['member_id'];
        $joinin_detail_where['city_center'] = $_GET['city'];
        $joinin_detail = $model_store_joinin->where($joinin_detail_where)->find();
        //获取城市中心
        $model = Model();
        $city = $model->table('city_centre')->field('city_name')->where('id='.$_GET['city'])->find();
        $joinin_detail['city_name'] = $city['city_name'];
        $joinin_detail_title = '查看';
        if(in_array(intval($joinin_detail['joinin_state']), array(STORE_JOIN_STATE_NEW, STORE_JOIN_STATE_PAY))) {
            $joinin_detail_title = '审核';
        }
        if (!empty($joinin_detail['sg_info'])) {
            $store_grade_info = Model('store_grade')->getOneGrade($joinin_detail['sg_id']);
            $joinin_detail['sg_price'] = $store_grade_info['sg_price'];
        } else {
            $joinin_detail['sg_info'] = @unserialize($joinin_detail['sg_info']);
            if (is_array($joinin_detail['sg_info'])) {
                $joinin_detail['sg_price'] = $joinin_detail['sg_info']['sg_price'];
            }
        }
        //判断是否可以回退
        $where_is_rz['joinin_state'] = 44;
        $where_is_rz['member_id'] = $_GET['member_id'];
        $is_rz_one = $model->table('store_joinin')->field('joinin_state')->where($where_is_rz)->find();

        if($is_rz_one['joinin_state'] == 44){
            Tpl::output('is_rz_one',1);
        }else{
            Tpl::output('is_rz_one',2);
        }
        Tpl::output('joinin_detail_title', $joinin_detail_title);
		Tpl::output('joinin_detail', $joinin_detail);
		Tpl::showpage('store_edit_joinin.detail');
	}

	/**
	 * 审核
	 */
	public function store_joinin_verifyOp() {
        $model_store_joinin = Model();
        $joinin_detail_where['member_id'] = $_POST['member_id'];
        $joinin_detail = $model_store_joinin->table('store_joinin_edit')->where($joinin_detail_where)->find();
        switch (intval($joinin_detail['joinin_state'])) {
            case STORE_JOIN_STATE_NEW:
                $this->store_joinin_verify_pass($joinin_detail);
                break;
            case STORE_JOIN_STATE_RZ:
                $this->store_joinin_verify_pass($joinin_detail);
                break;
            default:
                showMessage('参数错误','');
                break;
        }
        
	}

    private function store_joinin_verify_pass($joinin_detail) {
//        if(empty($_FILES['rz_evaluation_audit']['name'])){
//            showMessage('请先上传审核评估','');exit;
//        }
        $update = array();
        $update['joinin_state'] = $_POST['verify_type'] === 'pass' ? STORE_JOIN_STATE_RZSUCCESS : STORE_JOIN_STATE_VERIFY_FAIL;
        //必须在审核通过的时候才做次操作
        $model = Model();
        $model->beginTransaction();
        if($_POST['verify_type'] === 'pass'){
            //如果审核通过修改之前所有审核记录信息
            //查询当前修改的记录
            $data_edit = $model->table('store_joinin_edit')->where('member_id='.$_POST['member_id'])->find();
            $param = array();
            $param['company_province_id']                       = intval($data_edit['province_id']);
            $param['company_address']                           = $data_edit['company_address'];
            $param['company_address_detail']                    = $data_edit['company_address_detail'];
            $param['company_phone']                             = $data_edit['company_phone'];
            $param['company_employee_count']                    = intval($data_edit['company_employee_count']);
            $param['company_registered_capital']                = intval($data_edit['company_registered_capital']);
            $param['contacts_name']                             = $data_edit['contacts_name'];
            $param['contacts_phone']                            = $data_edit['contacts_phone'];
            $param['contacts_email']                            = $data_edit['contacts_email'];

            $update_store = $model->table('supplier')->where('member_id='.$_POST['member_id'])->update($param);
        }

        //修改审核状态
        $joinin_detail_where['member_id'] = $_POST['member_id'];
        $is_ture = $model->table('store_joinin_edit')->where($joinin_detail_where)->update($update);

        if ($_POST['verify_type'] === 'pass' && $update_store !=  false) {
            $sup_data = $model->table("supplier")->where('member_id='.$_POST['member_id'])->find();
            $city_list = $model->table('city_centre')->where('id ='.$sup_data['first_city_id'])->find();
            //将新的数据进行推送
            $send_data = array(
                "supply_code"       => $sup_data['business_licence_number'],
                "supplierNum"       => $sup_data['contract_code'],
                "supply_account"    => $sup_data['member_name'],
                "supply_eas_code"   => $sup_data['eas_code'],
                "supply_name"       => $sup_data['company_name'],
                "supply_type"       => "1",
                "supply_org"        => $city_list['bukrs'],
                "supply_mobile"     => $sup_data['contacts_phone'],
                "supply_mail"       => $sup_data['contacts_email'],
                "supply_address"    => $sup_data['company_address'],
                "glass_state"       => "1"
            );
            $TO_PUR_URL = YMA_WEBSERVICE_UPDATE_OR_SAVE_SUPPLIER;
            $supplyinfo_json = json_encode($send_data);
            $to_pur_result_json = WebServiceUtil::getDataByCurl($TO_PUR_URL, $supplyinfo_json, 0);
            $to_pur_result = json_decode($to_pur_result_json,true);
            CommonUtil::insertData2PushLog($to_pur_result, '', $supplyinfo_json, $TO_PUR_URL, 5);
            if($to_pur_result['resultCode']==-1){
                $model->rollback();
                showMessage('推送采购后台系统时错误信息：'.$to_pur_result['resultMsg'],'index.php?act=store_edit&op=index');
            }else{
                $model->commit();
                showMessage('供应商认证申请审核完成','index.php?act=store_edit&op=index');
            }
        } else{
            showMessage('供应商认证拒绝','index.php?act=store_edit&op=index');
        }
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
  
    
}
