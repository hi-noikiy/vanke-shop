<?php
/**
 * 发货
 *
 *
 *
 ***/




class store_deliverControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
		Language::read('member_store_index,deliver');
	}

	/**
	 * 发货列表
	 *
	 */
	public function indexOp() {
	    $model_order = Model('order');
		if (!in_array($_GET['state'],array('deliverno','delivering','delivered'))) $_GET['state'] = 'deliverno';
//		$order_state = str_replace(array('deliverno','delivering','delivered'),
//		        array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS),$_GET['state']);
                $order_state = str_replace(array('deliverno','delivering','delivered'),
		        array(ORDER_STATUS_SUCCESS.','.ORDER_STATE_PAY,ORDER_STATE_SEND.','.ORDER_STATUS_SEND_HET,ORDER_STATE_SUCCESS),$_GET['state']);
		$condition = array();
		$condition['store_id'] = $_SESSION['store_id'];
		$condition['order_state'] = array('in', $order_state);;
		if ($_GET['buyer_name'] != '') {
		    $condition['buyer_name'] = $_GET['buyer_name'];
		}
		if ($_GET['order_sn'] != '') {
		    $condition['order_sn'] = $_GET['order_sn'];
		}
		$if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
		$if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
		$start_unixtime = $if_start_date ? strtotime($_GET['query_start_date']) : null;
		$end_unixtime = $if_end_date ? strtotime($_GET['query_end_date']): null;
		if ($start_unixtime || $end_unixtime) {
		    $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
		}
		$order_list = $model_order->getOrderList($condition,5,'*','order_id desc','',array('order_goods','order_common','member'));
                foreach ($order_list as $key => $order_info) {
                    if(is_array($order_info['extend_order_goods'])){
                        foreach ($order_info['extend_order_goods'] as $value) {
                            $value['image_60_url'] = cthumb($value['goods_image'], 60, $value['store_id']);
                            $value['image_240_url'] = cthumb($value['goods_image'], 240, $value['store_id']);
                            $value['goods_type_cn'] = orderGoodsType($value['goods_type']);
                            $value['goods_url'] = urlShop('goods','index',array('goods_id'=>$value['goods_id']));
                            if ($value['goods_type'] == 5) {
                                $order_info['zengpin_list'][] = $value;
                            } else {
                                $order_info['goods_list'][] = $value;
                            }
                        }
                    }
		    

		    if (empty($order_info['zengpin_list'])) {
		        $order_info['goods_count'] = count($order_info['goods_list']);
		    } else {
		        $order_info['goods_count'] = count($order_info['goods_list']) + 1;
		    }
		    $order_list[$key] = $order_info;
		}
		Tpl::output('order_list',$order_list);
		Tpl::output('show_page',$model_order->showpage());
		self::profile_menu('deliver',$_GET['state']);
		Tpl::showpage('store_order.deliver');
	}

	/**
	 * 发货
	 */
	public function sendOp(){
        $order_id = intval($_GET['order_id']);
		if ($order_id <= 0){
			showMessage(Language::get('wrong_argument'),'','html','error');
		}
		$model_order = Model('order');
		$condition = array();
		$condition['order_id'] = $order_id;
		$condition['store_id'] = $_SESSION['store_id'];
		$order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
                if($order_info['order_lei']==2){
                    $if_allow_send = intval($order_info['lock_state']) || in_array($order_info['order_state'],array(70));
                }else{
                    $if_allow_send = intval($order_info['lock_state']) || !in_array($order_info['order_state'],array(ORDER_STATE_PAY,ORDER_STATE_SEND));
                }
                if ($if_allow_send) {
		    showMessage(Language::get('wrong_argument'),'','html','error');
		}

		if (chksubmit()){
		    $logic_order = Logic('order');
		    $_POST['reciver_info'] = $this->_get_reciver_info();
		    $result = $logic_order->changeOrderSend($order_info, 'seller', $_SESSION['member_name'], $_POST);
                    if (!$result['state']) {
			    showMessage($result['msg'],'','html','error');
			} else {
                            $data['order_sn'] = $order_info['order_sn'];
                            $data['order_state'] = ORDER_STATUS_SEND_HET;
                            $this->send_order_sendstatus($data);
			    showDialog($result['msg'],$_POST['ref_url'],'succ');
			}
		}
        Tpl::output('order_info',$order_info);
		//取发货地址
		$model_daddress = Model('daddress');
		if ($order_info['extend_order_common']['daddress_id'] > 0 ){
			$daddress_info = $model_daddress->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
		}else{
		    //取默认地址
			$daddress_info = $model_daddress->getAddressList(array('store_id'=>$_SESSION['store_id']),'*','is_default desc',1);
			$daddress_info = $daddress_info[0];

            //写入发货地址编号
            $this->_edit_order_daddress($daddress_info['address_id'], $order_id);
		}
		Tpl::output('daddress_info',$daddress_info);

		$express_list  = rkcache('express',true);
		//如果是自提订单，只保留自提快递公司
		/*if (isset($order_info['extend_order_common']['reciver_info']['dlyp'])) {
		    foreach ($express_list as $k => $v) {
		        if ($v['e_zt_state'] == '0') unset($express_list[$k]);
		    }
		    $my_express_list = array_keys($express_list);

		} else {
		    //快递公司
		    $my_express_list = Model()->table('store_extend')->getfby_store_id($_SESSION['store_id'],'express');
		    if (!empty($my_express_list)){
		        $my_express_list = explode(',',$my_express_list);
		    }
		}*/
        $my_express_list = Model()->table('store_extend')->getfby_store_id($_SESSION['store_id'],'express');
        if (!empty($my_express_list)){
            $my_express_list = explode(',',$my_express_list);
        }
		Tpl::output('my_express_list',$my_express_list);
		Tpl::output('express_list',$express_list);
		Tpl::showpage('store_deliver.send');
	}

	/**
	 * 编辑收货地址
	 * @return boolean
	 */
	public function buyer_address_editOp() {
	    $order_id = intval($_GET['order_id']);
	    if ($order_id <= 0) return false;
	    $model_order = Model('order');
		$condition = array();
		$condition['order_id'] = $order_id;
		$condition['store_id'] = $_SESSION['store_id'];
		$order_common_info = $model_order->getOrderCommonInfo($condition);
        if (!$order_common_info) return false;
        $order_common_info['reciver_info'] = @unserialize($order_common_info['reciver_info']);
		Tpl::output('address_info',$order_common_info);

		Tpl::showpage('store_deliver.buyer_address.edit','null_layout');
	}

    /**
     * 收货地址保存
     */
    public function buyer_address_saveOp() {
        $model_order = Model('order');
        $data = array();
        $data['reciver_name'] = $_POST['reciver_name'];
        $data['reciver_info'] = $this->_get_reciver_info();
        $condition = array();
        $condition['order_id'] = intval($_POST['order_id']);
        $condition['store_id'] = $_SESSION['store_id'];
        $result = $model_order->editOrderCommon($data, $condition);
        if($result) {
            echo 'true';
        } else {
            echo 'flase';
        }
    }

    /**
     * 组合reciver_info
     */
    private function _get_reciver_info() {
        $reciver_info = array(
            'address' => $_POST['reciver_area'] . ' ' . $_POST['reciver_street'],
            'phone' => trim($_POST['reciver_mob_phone'] . ',' . $_POST['reciver_tel_phone'],','),
            'area' => $_POST['reciver_area'],
            'street' => $_POST['reciver_street'],
            'mob_phone' => $_POST['reciver_mob_phone'],
            'tel_phone' => $_POST['reciver_tel_phone'],
            'dlyp' => $_POST['reciver_dlyp']
        );
        return serialize($reciver_info);
    }

	/**
	 * 选择发货地址
	 * @return boolean
	 */
/*	public function send_address_selectOp() {
	    Language::read('deliver');
	    $address_list = Model('daddress')->getAddressList(array('store_id'=>$_SESSION['store_id']));
	    Tpl::output('address_list',$address_list);
	    Tpl::output('order_id', $_GET['order_id']);
	    Tpl::showpage('store_deliver.daddress.select','null_layout');
	}*/
    public function send_address_selectOp() {
        Language::read('deliver');
        Tpl::output('order_id', $_GET['order_id']);
        Tpl::showpage('store_deliver.daddress.select','null_layout');
    }

    public function send_address_listOp() {
        //计算分页数据
        $page = empty($_GET['page']) ? 1:$_GET['page'];
        $nums = empty($_GET['nums']) ? 10:$_GET['nums'];
        $model = Model();
        $limit = $page*$nums-$nums.','.$nums;
        $where = "store_id = '".$_SESSION['store_id']."'";
        if(!empty($_GET['member'])){
            $where.= " and seller_name = like '%".$_GET['member']."%'";
        }
        if(!empty($_GET['address'])){
            $where.= " and (area_info like '%".$_GET['address']."%' or address like '%".$_GET['address']."%')";
        }
        if(!empty($_GET['phone'])){
            $where.= " and telphone like '%".$_GET['phone']."%'";
        }
        $address_list = $model->table('daddress')->where($where)->limit($limit)->select();
        $list = array(
            'code'  => 0,
            'msg'   => '',
            'count' => $model->table('daddress')->where($where)->count(),
            'data'  => $address_list,
        );
        echo json_encode($list);
    }



    /**
     * 保存发货地址修改
     */
    public function send_address_saveOp() {
        $result = $this->_edit_order_daddress($_POST['daddress_id'], $_POST['order_id']);
        if($result) {
            echo 'true';
        } else {
            echo 'flase';
        }
    }

    /**
     * 修改发货地址
     */
    private function _edit_order_daddress($daddress_id, $order_id) {
        $model_order = Model('order');
        $data = array();
        $data['daddress_id'] = intval($daddress_id);
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        return $model_order->editOrderCommon($data, $condition);
    }

    /**
	 * 物流跟踪
	 */
	public function search_deliverOp(){
		Language::read('member_member_index');
		$lang	= Language::getLangContent();

		$order_sn	= $_GET['order_sn'];
		if (!is_numeric($order_sn)) showMessage(Language::get('wrong_argument'),'','html','error');
		$model_order	= Model('order');
		$condition['order_sn'] = $order_sn;
		$condition['store_id'] = $_SESSION['store_id'];
		$order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
		if (empty($order_info) || $order_info['shipping_code'] == '') {
		    showMessage('未找到信息','','html','error');
		}
		$order_info['state_info'] = orderState($order_info);
		Tpl::output('order_info',$order_info);
		//卖家发货信息
		$daddress_info = Model('daddress')->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
		Tpl::output('daddress_info',$daddress_info);

		//取得配送公司代码
		$express = rkcache('express',true);
		Tpl::output('e_code',$express[$order_info['extend_order_common']['shipping_express_id']]['e_code']);
		Tpl::output('e_name',$express[$order_info['extend_order_common']['shipping_express_id']]['e_name']);
		Tpl::output('e_url',$express[$order_info['extend_order_common']['shipping_express_id']]['e_url']);
		Tpl::output('shipping_code',$order_info['shipping_code']);

		self::profile_menu('search','search');
		Tpl::showpage('store_deliver.detail');
	}

	/**
	 * 延迟收货
	 */
	public function delay_receiveOp(){
	    $order_id = intval($_GET['order_id']);
	    $model_trade = Model('trade');
	    $model_order = Model('order');
	    $condition = array();
	    $condition['order_id'] = $order_id;
	    $condition['store_id'] = $_SESSION['store_id'];
	    $condition['lock_state'] = 0;
	    $order_info = $model_order->getOrderInfo($condition);

	    //取目前系统最晚收货时间
	    $delay_time = $order_info['delay_time'] + ORDER_AUTO_RECEIVE_DAY * 3600 * 24;
	    if (chksubmit()) {
	        $delay_date = intval($_POST['delay_date']);
	        if (!in_array($delay_date,array(5,10,15))) {
	            showDialog(Language::get('wrong_argument'),'','error',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
	        }
	        $update = $model_order->editOrder(array('delay_time'=>array('exp','delay_time+'.$delay_date*3600*24)),$condition);
	        if ($update) {
	            //新的最晚收货时间
	            $dalay_date = date('Y-m-d H:i:s',$delay_time+$delay_date*3600*24);
	            showDialog("成功将最晚收货期限延迟到了".$dalay_date.'&emsp;','','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();',4);
	        } else {
	            showDialog('延迟失败','','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
	        }
        } else {
            $order_info['delay_time'] = $delay_time;
            Tpl::output('order_info',$order_info);
            Tpl::showpage('store_deliver.delay_receive','null_layout');
            exit();
        }
	}

	/**
	 * 从第三方取快递信息
	 *
	 */
	public function get_expressOp(){
        $url = 'http://www.kuaidi100.com/query?type='.$_GET['e_code'].'&postid='.$_GET['shipping_code'].'&id=1&valicode=&temp='.random(4).'&sessionid=&tmp='.random(4);
        import('function.ftp');
        $content = dfsockopen($url);
        $content = json_decode($content,true);
        if ($content['status'] != 200) exit(json_encode(false));
        $content['data'] = array_reverse($content['data']);
        $output = '';
        if (is_array($content['data'])){
            foreach ($content['data'] as $k=>$v) {
                if ($v['time'] == '') continue;
                $output .= '<li>'.$v['time'].'&nbsp;&nbsp;'.$v['context'].'</li>';
            }
        }
        if ($output == '') exit(json_encode(false));
        if (strtoupper(CHARSET) == 'GBK'){
            $output = Language::getUTF8($output);//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
        }
        echo json_encode($output);
	}

    /**
     * 运单打印
     */
    public function waybill_printOp() {
        $order_id = intval($_GET['order_id']);
        if($order_id <= 0) {
            showMessage(L('param_error'));
        }

        $model_order = Model('order');
        $model_store_waybill = Model('store_waybill');
        $model_waybill = Model('waybill');

        $order_info = $model_order->getOrderInfo(array('order_id' => intval($_GET['order_id'])), array('order_common'));

        $store_waybill_list = $model_store_waybill->getStoreWaybillList(array('store_id' => $order_info['store_id']), 'is_default desc');

        $store_waybill_info = $this->_getCurrentWaybill($store_waybill_list, $_GET['store_waybill_id']);
        if(empty($store_waybill_info)) {
            showMessage('请首先绑定打印模板', urlShop('store_waybill', 'waybill_manage'), '', 'error');
        }

        $waybill_info = $model_waybill->getWaybillInfo(array('waybill_id' => $store_waybill_info['waybill_id']));
        if(empty($waybill_info)) {
            showMessage('请首先绑定打印模板', urlShop('store_waybill', 'waybill_manage'), '', 'error');
        }

        //根据订单内容获取打印数据
        $print_info = $model_waybill->getPrintInfoByOrderInfo($order_info);

        //整理打印模板
        $store_waybill_data = unserialize($store_waybill_info['store_waybill_data']);
        foreach ($waybill_info['waybill_data'] as $key => $value) {
            $waybill_info['waybill_data'][$key]['show'] = $store_waybill_data[$key]['show'];
            $waybill_info['waybill_data'][$key]['content'] = $print_info[$key];
        }

        //使用商家自定义的偏移尺寸
        $waybill_info['waybill_pixel_top'] = $store_waybill_info['waybill_pixel_top'];
        $waybill_info['waybill_pixel_left'] = $store_waybill_info['waybill_pixel_left'];

        Tpl::output('waybill_info', $waybill_info);
        Tpl::output('store_waybill_list', $store_waybill_list);
        Tpl::showpage('waybill.print', 'null_layout');
    }

    /**
     * 获取当前打印模板
     */
    private function _getCurrentWaybill($store_waybill_list, $store_waybill_id) {
        if(empty($store_waybill_list)) {
            return false;
        }

        $store_waybill_id = intval($store_waybill_id);

        $store_waybill_info = null;

        //如果指定模板使用指定的模板，未指定使用默认模板
        if($store_waybill_id > 0) {
            foreach ($store_waybill_list as $key => $value) {
                if($store_waybill_id == $value['store_waybill_id']) {
                    $store_waybill_info = $store_waybill_list[$key];
                    break;
                }
            }
        }

        if(empty($store_waybill_info)) {
            $store_waybill_info = $store_waybill_list[0];
        }

        return $store_waybill_info;
    }

	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return
	 */
	private function profile_menu($menu_type,$menu_key='') {
		Language::read('member_layout');
		$menu_array		= array();
		switch ($menu_type) {
			case 'deliver':
				$menu_array = array(
				array('menu_key'=>'deliverno',			'menu_name'=>Language::get('nc_member_path_deliverno'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=deliverno'),
				array('menu_key'=>'delivering',			'menu_name'=>Language::get('nc_member_path_delivering'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=delivering'),
				array('menu_key'=>'delivered',		'menu_name'=>Language::get('nc_member_path_delivered'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=delivered'),
				);
				break;
			case 'search':
				$menu_array = array(
				1=>array('menu_key'=>'nodeliver',			'menu_name'=>Language::get('nc_member_path_deliverno'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=nodeliver'),
				2=>array('menu_key'=>'delivering',			'menu_name'=>Language::get('nc_member_path_delivering'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=delivering'),
				3=>array('menu_key'=>'delivered',		'menu_name'=>Language::get('nc_member_path_delivered'),	'menu_url'=>'index.php?act=store_deliver&op=index&state=delivered'),
				4=>array('menu_key'=>'search',		'menu_name'=>Language::get('nc_member_path_deliver_info'),	'menu_url'=>'###'),
				);
				break;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
        
        public function send_order_sendstatus($data){
            try{
                /*
		$model = Model();
		$data_json = json_encode($data);
		//$url = YMA_WEBSERVICE_URL_HEAD."/impac/services/update/updateDeliveryChangeDateAndStatus";
		//$url = 'http://192.168.172.208:8080/xl04_war/services/update/updateOrderStatus';
                $url = YMA_WEBSERVICE_UPDATE_DELIVERY_CHANGE_DATE_AND_STATUS;
		$return = WebServiceUtil::getDataByCurl($url, $data_json, 0);
		$returnArray = json_decode($return,true);
		CommonUtil::insertData2PushLog($returnArray, $data['order_sn'], $data_json, $url, 1);
                 */
                if(!isset($data['goods']))$data['goods']=array();
                $logic=Logic('deliver_goods');
                $return = $logic->delicer_goods2YMH($data['order_sn'],$data['order_state'],$data['goods']);
            }catch (Exception $exc ){
               log::record4inter('推送系统异常', log::ERR);
            }
        }
        public function send_order_openOp(){
            
            $model = Model();
            $list_where['order_id'] = htmlspecialchars($_GET['order_id']);
            $list = $model->table('order_goods')->where($list_where)->select();
            $express_list  = rkcache('express',true);
            //快递公司
            $my_express_list = Model()->table('store_extend')->getfby_store_id($_SESSION['store_id'],'express');
            if (!empty($my_express_list)){
                $my_express_list = explode(',',$my_express_list);
            }
            Tpl::output('express_list',$express_list);
            Tpl::output('my_express_list',$my_express_list);
            if(chksubmit()){
                if($_SESSION['set_opentime']){
                    if(time()  - $_SESSION['set_opentime'] < 10){
                        showDialog('请不要频繁操作！');
                    }else{
                        $_SESSION['set_opentime']  = time();
                    }
                }else{
                    $_SESSION['set_opentime']  = time();
                }
                if($_POST){
                    $model = Model();
                    $list_where['order_id'] = htmlspecialchars($_POST['order_id']);
                    $list = $model->table('order_goods')->where($list_where)->select();
            
                    $model_order = Model('order');
                    //组合post 传送数据
                    $data = array();
                    $i = 0;
                    foreach($_POST['re_id'] as $r_id){
                        $data[$i]['rec_id'] = $r_id;
                        $i++;
                    }
                    $inum = 0;
                    foreach($_POST['open_num'] as $o_num){
                        $data[$inum]['goods_num'] = $o_num;
                        $inum++;
                    }
//                    var_Dump($data);exit;
//                  生成新的一笔订单数据
//                  
                try{
                    $model->beginTransaction();
                    //把原来商品订单插入商品订单数据插入到old_order 新表中
                    $order_old = $model->table('order')->where('order_id='.htmlspecialchars($_POST['order_id']))->find();
                    $order_old_sn=$order_old['order_sn'];
                    $old_mother_order_sn = $order_old['order_sn'];
                    if($order_old['mother_orderid'] || $order_old['order_num_sequence']){
                        showDialog('订单拆分失败，子单不能拆单！');
                        $model->rollback();
                        exit;
                    }
                    //判断当前订单是否是母单
                    $insert_oldorder = array();
                    if($order_old && $order_old['mother_orderid'] == 0){
                        //循环拼凑插入数据
                        foreach($order_old as $key=>$order_data){
                            $insert_oldorder[$key]  = $order_data;
                        }         
                        //保存数据如old_order
                        if($insert_oldorder){
                            
                            unset($insert_oldorder['mother_orderid']);
                            $order_insert_old =  $model_order->insert_oldorder($insert_oldorder);
                        }
                    }
                    //创建新的订单
                    //生成支付订单ID
                    $data_pay_id['pay_sn'] = $this->makePaySn($order_old['buyer_id']);
                    $data_pay_id['buyer_id']  = $order_old['buyer_id'];
                    $up_pay_id = $model->table('order_pay')->insert($data_pay_id);
                    $order_old['order_sn'] = $this->makeOrderSn($up_pay_id);
                    //插入订单物流信息
                   
                    
                    //查询是否子单拆到第几单了
                    $order_seq = $model->table('order')->field('mother_orderid,order_num_sequence')->where('mother_orderid = '.$old_mother_order_sn)->order('order_num_sequence DESC')->find();
                    $order_old['mother_orderid'] = $old_mother_order_sn;
                    $order_old['order_num_sequence'] = $order_seq['order_num_sequence']+1;
                    $shiping_id = $_POST['shiping_id'];
                    if($shiping_id != '-1'){
                        $order_old['shipping_code'] = $_POST['shipping_code'][$shiping_id]['code'];
                    }
                    
                    unset($order_old['order_id']);
                    $insert_news_order = $model_order->insert_newsorder($order_old);
                    
                     //取发货地址
                    $condition_info_order['sotre_id'] = $order_old['store_id'];
                    $condition_info_order['order_id'] = $insert_news_order;
                    $order_info = $model_order->getOrderInfo($condition_info_order,array('order_common','order_goods'));
                    
                    $model_daddress = Model('daddress');
                    if ($order_info['extend_order_common']['daddress_id'] > 0 ){
                            $daddress_info = $model_daddress->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
                    
                    }else{
                        //取默认地址
                            $daddress_info = $model_daddress->getAddressList(array('store_id'=>$_SESSION['store_id']),'*','is_default desc',1);
                            $daddress_info = $daddress_info[0];
                        //写入发货地址编号
                        $this->_edit_order_daddress($daddress_info['address_id'], $insert_news_order);
                    }
                    //复制一份收货地址信息
                    $address = $model->table('order_common')->where('order_id='.$_POST['order_id'])->find();
                    $news_address = array();
                    foreach($address as $key=>$address_rows){
                        $news_address[$key] = $address_rows;
                    }
                    //插入新的订单收货信息
                    $news_address['order_id'] = $insert_news_order;
                    $news_address['shipping_time'] = time();
                    $news_address['deliver_explain'] = $_POST['deliver_explain'];
                    if($shiping_id != '-1'){
                        $news_address['shipping_express_id'] = $shiping_id;
                    }
                    $model->table('order_common')->insert($news_address);
                    //创建订单成功 执行更新订单商品数据
                    $price= 0;
                    if($insert_news_order != false){
                        //比对商品订单更新商品订单
                        foreach($list as $key=>$rows){
                            //循环商品数量更新商品数量
                            foreach($data as $n_rows){
                                if($rows['rec_id'] == $n_rows['rec_id']){
                                    //判断当前减少的商品数量是不是最大值，如果是 删原订单商品数据
                                    if($rows['goods_num'] - $n_rows['goods_num'] > 0){
                                         //执行SQL更新语句
                                        //判断当前减少的商品是否是最大值 如果超出最大值。 返回失败
                                        $faind_listorder = $model->table('order_goods')->where('rec_id='.$rows['rec_id'])->field('goods_num')->find();
                                        
                                        $goods_up['goods_num'] = array('exp','goods_num -'.$n_rows['goods_num']);
                                        //更新旧数据
                                        $is_up = $model->table('order_goods')->where('rec_id='.$rows['rec_id'])->update($goods_up);
                                        //同时插入新的数据创建新的订单
                                        if($is_up != false && $n_rows['goods_num'] > 0){
                                           
                                            $list[$key]['goods_num'] = $n_rows['goods_num'];
                                            $list[$key]['order_id'] = $insert_news_order;
                                            unset($list[$key]['rec_id']);
                                            $insert_ordergoods = $model->table('order_goods')->insert($list[$key]);
                                            if($insert_ordergoods != false){
                                                //计算拆分出来的订单商品价格
                                                $price += $rows['goods_price'] * $n_rows['goods_num'];
                                                
                                            }
                                        }
                                    }else{
                                        //删除当前商品数据，插入新的订单表中
                                       $del = $model->table('order_goods')->where('rec_id='.$rows['rec_id'])->delete();
                                       //插入新的数据
                                       if($del != false && $n_rows['goods_num'] > 0){
                                           if($rows['goods_num'] - $n_rows['goods_num'] <= 0){
                                               $model->rollback();
                                               showDialog('当前拆分的订单商品数量不能大于或等于原订单商品数量！');exit;
                                           }
                                            $list[$key]['goods_num'] = $n_rows['goods_num'];
                                            $list[$key]['order_id'] = $insert_news_order;
                                            unset($list[$key]['rec_id']);
                                            $insert_ordergoods = $model->table('order_goods')->insert($list[$key]);
                                            if($insert_ordergoods != false){
                                                //计算拆分出来的订单商品价格
                                                $price += $rows['goods_price'] * $n_rows['goods_num'];
                                            }
                                       }
                                    }
                                }
                            }
                        }
                        
                        //循环得出 拆分商品订单价格为$price  按照比例计算出订单价格 更新新创建订单数据
                        $update_newsorder['goods_amount'] = $price;
                        $update_newsorder['order_amount'] = $price / $order_old['goods_amount'] * $order_old['order_amount'];
                        $update_newsorder['order_state'] = ORDER_STATUS_SEND_HET;                            
                        $upda_newsorder =  $model->table('order')->where('order_id='.$insert_news_order)->update($update_newsorder);
                        //更新 旧的订单价格
                        $updaoldorder['goods_amount'] = array('exp','goods_amount -'.$price);
                        $updaoldorder['order_amount'] = array('exp','order_amount - '.$update_newsorder['order_amount']);
                        $upda_oldsorder =  $model->table('order')->where('order_id='.  htmlspecialchars($_POST['order_id']))->update($updaoldorder);
                        if($upda_newsorder != false && $upda_oldsorder != false){
                            $model->commit();
                            $data['order_sn'] = $order_old_sn;
                            $data['order_state'] = ORDER_STATUS_SUCCESS;
                            //母单发货，获取母单商品信息
                            //$goods_data = $model->table('order_goods,goods')->join('left')->on('order_goods.goods_id=goods.goods_id')->field('goods.materiel_code,order_goods.goods_num')->where('order_goods.order_id='.$list_where['order_id'])->select();
                            $goods_data = $model->table('order_goods,goods')->join('left')->on('order_goods.goods_id=goods.goods_id')->field('order_goods.goods_num as order_quantity,goods.materiel_code as product_code')->where('order_goods.order_id='.$list_where['order_id'])->select();
                            $data['goods']=$goods_data;
                            $this->send_order_sendstatus($data);
                            //推送新订单
                            $logic=Logic('trans_order');
                            $logic->transOrderToYMA($order_old['order_sn'],1);
                            showDialog("订单拆分成功！",'index.php?act=store_deliver&op=send&order_id='.$_POST['order_id'],'succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
                        }else{
                            showDialog('订单拆分失败，请稍后尝试！');
                        }
                    }
                } catch (Exception $ex) {
                    $model->rollback();
                }    
                }
            }
            Tpl::output('list',$list);
            Tpl::showpage('store_deliver_open','null_layout');
        }
        
        /**
	 * 订单编号生成规则，n(n>=1)个订单表对应一个支付表，
	 * 生成订单编号(年取1位 + $pay_id取13位 + 第N个子订单取2位)
	 * 1000个会员同一微秒提订单，重复机率为1/100
	 * @param $pay_id 支付表自增ID
	 * @return string
	 */
	public function makeOrderSn($pay_id) {
	    //记录生成子订单的个数，如果生成多个子订单，该值会累加
	    static $num;
	    if (empty($num)) {
	        $num = 1;
	    } else {
	        $num ++;
	    }
		return (date('y',time()) % 9+1) . sprintf('%013d', $pay_id) . sprintf('%02d', $num);
	}
        
        /**
	 * 生成支付单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)，该值会传给第三方支付接口
	 * 长度 =2位 + 10位 + 3位 + 3位  = 18位
	 * 1000个会员同一微秒提订单，重复机率为1/100
	 * @return string
	 */
	public function makePaySn($member_id) {
		return mt_rand(10,99)
		      . sprintf('%010d',time() - 946656000)
		      . sprintf('%03d', (float) microtime() * 1000)
		      . sprintf('%03d', (int) $member_id % 1000);
	}
        
}
