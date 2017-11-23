<?php
/**
 * 商家入驻
 *
 *
 *
 ***/




class store_joinControl extends BaseHomeControl {

    private $joinin_detail = NULL;

	public function __construct() {
		parent::__construct();

		Tpl::setLayout('store_joinin_layout');

        $this->checkLogin();
//        $model_seller = Model('seller');
//        $seller_info = $model_seller->getSellerInfo(array('member_id' => $_SESSION['member_id']));
//        if(!empty($seller_info)) {
//            Tpl::output('rzsuccess',6);
//            Tpl::output('show_sign','joinin');
//            $this->show_join_message('开店成功您可以选择　<a href="/shop/index.php?act=store_join&op=ecrz">申请其他城市开店</a>', FALSE, '4');
//        }

        if($_GET['op'] != 'check_seller_name_exist' && $_GET['op'] != 'checkname' && $_GET['op'] != "ecrz") {
            $this->check_joinin_state();
        }
        $phone_array = explode(',',C('site_phone'));
        Tpl::output('phone_array',$phone_array);
        $model_help = Model('help');
        $condition = array();
        $condition['type_id'] = '99';//默认显示入驻流程;
        $list = $model_help->getShowStoreHelpList($condition);
        Tpl::output('list',$list);//左侧帮助类型及帮助
        //
        //获取当前用户已经绑定认证的资料，并进行更新提交如果未有认证机制，则提示用户 必须先认证
        $model = Model();
        $store_member_where['member_id']  = $_SESSION['member_id'];
        $rz_store = $model->table('store_joinin')->where($store_member_where)->find();
        if(!$rz_store){
            //如果有提交认证资料则继续，如果没有则提交给认证审核
            //如果有数据则显示商家提交数据
            $this->show_join_message('您还没有认证请先提交认证',FALSE,0);exit;
        }
        Tpl::output('show_sign','join');
        Tpl::output('html_title',C('site_name').' - '.'商家入驻');
        Tpl::output('article_list','');//底部不显示文章分类
	}

    private function check_joinin_state() {
        //获取用户是否已经认证过如果认证通过则进入选择 用户是继续认证还是开店申请
        $open = 0;
        $model = Model();
        $where_join = "city_center = first_city_id and member_id = '".$_SESSION['member_id']."'";
        $joinin_detail = $model->table('store_joinin')->where($where_join)->find();
        $joinin_list = $model->table('store_joinin')->where("member_id = '".$_SESSION['member_id']."'")->select();
        /*$model_store_joinin = Model('store_joinin');
        $joinin_detail_where['member_id'] = $_SESSION['member_id'];
        $joinin_array = $model_store_joinin->where($joinin_detail_where)->select();
        //判断用户是否已经提交认证
        if(count($joinin_array) >= 2){
            //这是多次提交认证
            //foreach 查看当前用户是否有认证通过并且过开店的店铺
            foreach($joinin_array as $key=>$rows){
                //判断是否已经开过店铺
                if($rows['store_state'] == 40){
                    $open =1;
                }
                if($rows['store_state'] == 34){
                    $joinin_detail = $joinin_array[$key];
                    break;
                }else if($rows['store_state'] == 41){
                   $joinin_detail = $joinin_array[$key];
                   break;
                }else{
                    if(!$joinin_detail){
                        $joinin_detail = $joinin_array[$key];
                    }
                }
            }
        }else{
             $joinin_detail = $joinin_array[0];
        }
        unset($joinin_array);*/
        if(!empty($joinin_detail) && !empty($joinin_list)){
            //判定是单条记录还是多条记录
            if(count($joinin_list) > 1){
                //如果多条记录，判定是否还有已认证城市尚未开店
                $where_no = "member_id = '".$_SESSION['member_id']."' and store_state = '0'";
                $not_join = $model->table('store_joinin')->where($where_no)->count();
                if($not_join > 0){
                    $this->show_join_message('<a href="'.SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=ckrz">查看开店记录</a></br>
                        <a href="'.BASE_SITE_URL.'/shop/index.php?act=store_join&op=ecrz">申请其他城市开店</a>', FALSE, '4');
                }else{
                    $this->show_join_message('<a href="'.SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=ckrz">查看开店记录</a>', FALSE, '4');
                }
            }else{
                $this->joinin_detail = $joinin_detail;
                switch (intval($joinin_detail['joinin_state'])) {
                    case STORE_JOIN_STATE_RZ:
                        $this->show_join_message('认证审核中！请等待审核通过！', FALSE,'3');
                        break;
                }
                
                if($joinin_detail['store_state'] == STORE_JOIN_STATE_RZHKD){
                    $this->show_join_message('已经提交，请等待管理员核对后为您开通店铺</br>
                        <a href="'.SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=ckrz">查看开店记录</a>', FALSE, '3');
                }
                if($joinin_detail['store_state'] == STORE_JOIN_STATE_KDJJ){
                    Tpl::output('rzsuccess',6);
                    Tpl::output('show_sign','join');
                    $this->show_join_message('您的审核未通过.您可以选择　<a href="'.BASE_SITE_URL.'/shop/index.php?act=store_join&op=ecrz">申请其他城市开店</a>', FALSE, '4');
                }
                if($joinin_detail['store_state'] == STORE_JOIN_STATE_FINAL || $open == 1){
                    Tpl::output('rzsuccess',6);
                    Tpl::output('show_sign','join');
                    $this->show_join_message('开店成功您可以选择　<a href="'.BASE_SITE_URL.'/shop/index.php?act=store_join&op=ecrz">申请其他城市开店</a>', FALSE, '4');
                }
            }
        }
    }

	public function indexOp() {
            $this->step3Op();
	}

    public function step3Op(){
            //商品分类
        $gc	= Model('goods_class');
        $gc_list	= $gc->getGoodsClassListByParentId(0);
        Tpl::output('gc_list',$gc_list);
        
        
        //获取可以开店城市中心地址
        $model = Model();
        
        //当前用户被拒绝开店的认证不允许继续认证
        $cityid = '';
        $city_where_join['member_id'] = $_SESSION['member_id'];
        $city_where_join['joinin_state'] = 44;
        $city_where_join['store_state'] = array('not in','40,41');
        $city = $model->table('store_joinin')->where($city_where_join)->field('city_center')->select();
        foreach($city as $rows){
            $cityid .= $rows['city_center'].",";
        }
        $city_center_where['id'] = array('in',$cityid);
        $city_center_where['city_state'] = 1;
        $city_center =  $model->table('city_centre')->where($city_center_where)->select();
        Tpl::output('city',$city_center);
        if(empty($city_center)){
            $this->show_join_message('请先认证城市公司　<a href="'.BASE_SITE_URL.'/shop/index.php?act=store_joinin&op=index">去认证</a>', FALSE, '4');
        }
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
		//获取供应商数据
        $store_join = $model->table("store_joinin")->where("member_id = '".$_SESSION['member_id']."' and city_center = first_city_id")->find();
		Tpl::output('grade_list', $grade_list);
        Tpl::output('store_info',$store_join);
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
        Tpl::showpage('store_joinin_apply2');
        exit;
    }
    
    public function step4Op() {
        $model = Model();
        $store_class_ids = array();
        $store_class_names = array();
        if(!empty($_POST['store_class_ids']) && is_array($_POST['store_class_ids'])) {
            foreach ($_POST['store_class_ids'] as $value) {
                $store_class_ids[] = $value.',';
                $class_data = $model->table("goods_class")->field('gc_name')->where("gc_id = '".$value."'")->find();
                $store_class_names[] = $class_data['gc_name'].',';
            }
        }
        /* if(!empty($_POST['store_class_names'])) {
            foreach ($_POST['store_class_names'] as $value) {
                $store_class_names[] = $value;
            }
        } */
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
        $param = array();
        $param['seller_name'] = $_POST['seller_name'];
        $param['store_name'] = $_POST['store_name'];
        $param['store_class_ids'] = serialize($store_class_ids);
        $param['store_class_names'] = serialize($store_class_names);
        $param['joinin_year'] = 100;//intval($_POST['joinin_year']);
        if(is_array($store_class_commis_rates)){
            $param['store_class_commis_rates'] = implode(',', $store_class_commis_rates);
        }else{
            $param['store_class_commis_rates'] =  $store_class_commis_rates;
        }
        

        //取店铺等级信息
        $grade_list = rkcache('store_grade',true);
        $_POST['sg_id'] = "1";
        $_POST['sg_name'] = "系统默认";
        if (!empty($grade_list[$_POST['sg_id']])) {
            $param['sg_id'] = $_POST['sg_id'];
            $param['sg_name'] = $grade_list[$_POST['sg_id']]['sg_name'];
            $param['sg_info'] = serialize(array('sg_price' => $grade_list[$_POST['sg_id']]['sg_price']));
        }
        if (!empty($grade_list[$_POST['sg_id']])) {
            $param['sg_id'] = $_POST['sg_id'];
            $param['sg_name'] = $grade_list[$_POST['sg_id']]['sg_name'];
            $param['sg_info'] = serialize(array('sg_price' => $grade_list[$_POST['sg_id']]['sg_price']));
        }
        
        if(!empty($_POST['store_class_ids']) && is_array($_POST['store_class_ids'])) {
            $sc_id_data = $sc_name_data = $sc_bail_data =array();
            foreach ($_POST['store_class_ids'] as $v){
                $store_class_info = Model('store_class')->getStoreClassInfo(array('sc_id'=>intval($v)));
                if ($store_class_info) {
                    $sc_id_data[] = $store_class_info['sc_id'];
                    $sc_name_data[] = $store_class_info['sc_name'];
                    $sc_bail_data[] = $store_class_info['sc_bail'];
                }
            }
            $param['sc_id'] = implode(',',$sc_id_data);
            $param['sc_name'] = implode(',',$sc_name_data);
            $param['sc_bail'] = implode(',',$sc_bail_data);
        }

        //取最新店铺分类信息
/*         $store_class_info = Model('store_class')->getStoreClassInfo(array('sc_id'=>intval($_POST['sc_id'])));
        if ($store_class_info) {
            $param['sc_id'] = $store_class_info['sc_id'];
            $param['sc_name'] = $store_class_info['sc_name'];
            $param['sc_bail'] = $store_class_info['sc_bail'];
        } */

        //cary_add   添加代理ID
        if(!empty($_POST['agent_id'])){
            $param['agent_id'] = $_POST['agent_id'];
        }
        //增加店铺类型
        $param['store_type_id'] = "8";//$_POST['st_id'];
        $param['store_type_name'] = "专营店";//$_POST['st_name'];
        //店铺认证状态
        $param['store_state'] = STORE_JOIN_STATE_RZHKD;
        //店铺应付款
        $param['paying_amount'] = floatval($grade_list[$_POST['sg_id']]['sg_price'])*$param['joinin_year']+floatval($param['sc_bail']);
        
        if($_POST['is_rz'] != 1 ){
            $this->step4_save_valid($param);
        }
        $model_store_joinin = Model('store_joinin');
        $join_store_step4_where['city_center'] = $_POST['city_centre'];
        $join_store_step4_where['member_id'] = $_SESSION['member_id'];
        $is_up = $model_store_joinin->where($join_store_step4_where)->update($param);
        
        @header('location: index.php?act=store_join');

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
        Tpl::showpage('store_joinin_apply2');
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
        Tpl::showpage('store_joinin_apply2');
        exit;
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
	 * 二次开店
	 *
	 * @param
	 * @return
	 */
	public function ecrzOp() {
        //二次开店不需要填写商店数据
        //需要查找一条用户认证审核通过并且开店的申请记录
        $model = Model();
        $where_join = "store_state in('".STORE_JOIN_STATE_RZHKD."','".STORE_JOIN_STATE_FINAL."') and member_id = '".$_SESSION['member_id']."'";
        $store_join = $model->table('store_joinin')->where($where_join)->find();
        $where_no = "member_id = '".$_SESSION['member_id']."' and store_state = '0' and joinin_state = '44'";
        $not_join = $model->table('store_joinin')->where($where_no)->count();
        if($not_join > 0){
            //查找到了输入当前信息，且查找城市中心，只查询认证成功并且还未申请开店的数据
            //获取可以开店城市中心地址
            $city_where_join['member_id'] = $_SESSION['member_id'];
            $city_where_join['joinin_state'] = 44;
            $city_where_join['store_state'] = array('eq',0);
            $city = $model->table('store_joinin')->where($city_where_join)->field('city_center')->select();
            $cityid = '';
            foreach($city as $rows){
                $cityid.= $rows['city_center'].",";
            }
            $city_center_where['id'] = array('in',$cityid);
            $city_center_where['city_state'] = 1;
            $city_center =  $model->table('city_centre')->where($city_center_where)->select();
            if(empty($city_center)){
                $this->show_join_message('请先认证城市公司　<a href="'.BASE_SITE_URL.'/shop/index.php?act=store_joinin&op=index">去认证</a>', FALSE, '4');
            }
            Tpl::output('city',$city_center);
            Tpl::output('join_t',empty($store_join) ? 2:1);

            if(empty($store_join['seller_name'])){
                $store_data = $model->table('supplier')->field('member_name')->where("member_id = '".$_SESSION['member_id']."'")->find();
                $store_join['seller_name'] = $store_data['member_name'];
            }

            //店铺分类
            $model_store = Model('store_class');
            $store_class = $model_store->getStoreClassList(array(),'',false);
            if(!empty($store_class) && is_array($store_class)){
                $frist_class_id = unserialize($store_join['store_class_ids']);
                $new_store_class = array();
                foreach ($store_class as $val){
                    if(is_array($frist_class_id) && in_array($val['sc_id'].",", $frist_class_id)){
                        $val['is_stare'] = 1;
                    }else{
                        $val['is_stare'] = 2;
                    }
                    $new_store_class[] = $val;
                }
            }
            Tpl::output('store_class', $new_store_class);
                
                
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

            if(!empty($_POST)){
                $upda_join_store_where['member_id'] = $_SESSION['member_id'];
                //处理多个城市同时开店
                if(!empty($_POST['city_centre']) && is_array($_POST['city_centre'])){
                    $city_where_string = implode(',',$_POST['city_centre']);
                    $upda_join_store_where['city_center'] = array('in',$city_where_string);
                }
                $upda_join_store = $this->get_store_join($_POST,$_SESSION['member_id']);
                if(!empty($upda_join_store)){
                    $store_join = $model->table('store_joinin')->where($upda_join_store_where)->update($upda_join_store);
                }else{
                    $store_join = $model->table('store_joinin')->where($upda_join_store_where)->update(array('store_state'=>STORE_JOIN_STATE_RZHKD));
                }
                if($store_join){
                    if(!empty($upda_join_store)){
                        //修改其它所有店铺认证的信息
                        $all_up_data = array(
                            'store_class_ids'   =>$upda_join_store['store_class_ids'],
                            'store_class_names' =>$upda_join_store['store_class_names'],
                            'sc_id'             =>$upda_join_store['sc_id'],
                            'sc_name'           =>$upda_join_store['sc_name'],
                            'sc_bail'           =>$upda_join_store['sc_bail'],
                            'store_class_commis_rates'=>$upda_join_store['store_class_commis_rates'],
                        );
                        $all_up_data_where = "member_id = '".$_SESSION['member_id']."'";
                        $model->table('store_joinin')->where($all_up_data_where)->update($all_up_data);
                    }
                    @header('location: index.php?act=store_join&op=index');
                }else{
                    $this->show_join_message('系统繁忙，请稍后提交', FALSE, '4');
                }
            }
            Tpl::output('store_info',$store_join);
            Tpl::output('step', '3');
            Tpl::output('sub_step', 'step3');
            Tpl::showpage('store_joinin_apply2');

        }else{
            $this->show_join_message('您还未开店成功！请先提交开店数据</br><a href="'.SHOP_SITE_URL.DS.'index.php?act=store_joinin&op=ckrz">查看开店进度</a>', FALSE, '4');
        }
	}



	/**
     * 针对开店申请进行数据处理
     * $data 前端POST提交的数据
	 **/
	private function get_store_join($data=array(),$member_id)
    {
        if (!empty($data['store_class_ids']) && !empty($data['city_centre'])) {
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
            //获取是否需要更新商户名
            $store_data = $model->table('supplier')->field('member_name')->where("member_id = '".$_SESSION['member_id']."'")->find();
            $param['seller_name'] = $store_data['member_name'];
            if (empty($store_join['store_name'])) {
                $param['store_name'] = $data['store_name'];
            }else{
                $param['store_name'] = $store_join['store_name'];
            }
            //序列化class数据
            $param['store_class_ids'] = serialize($store_class_ids);
            $param['store_class_names'] = serialize($store_class_names);
            $param['joinin_year'] = 100;//intval($_POST['joinin_year']);

            if (is_array($store_class_commis_rates)) {
                $param['store_class_commis_rates'] = implode(',', $store_class_commis_rates);
            } else {
                $param['store_class_commis_rates'] = $store_class_commis_rates;
            }

            //取店铺等级信息
            $grade_list = rkcache('store_grade', true);
            $data['sg_id'] = "1";
            $data['sg_name'] = "系统默认";
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
            //增加店铺类型
            $param['store_type_id'] = "8";//$_POST['st_id'];
            $param['store_type_name'] = "专营店";//$_POST['st_name'];
            //店铺认证状态
            $param['store_state'] = STORE_JOIN_STATE_RZHKD;
            //店铺应付款
            $param['paying_amount'] = floatval($grade_list[$data['sg_id']]['sg_price']) * $param['joinin_year'] + floatval($param['sc_bail']);
            return $param;
        }
    }
}
