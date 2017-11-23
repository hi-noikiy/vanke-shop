<?php
header("Content-Type: text/html; charset=UTF-8");
/**
 * 交易管理
 *
 *
 *
 ***/


class order_inControl extends SystemControl{
    /**
     * 每次导出订单数量
     * @var int
     */
	const EXPORT_SIZE = 5000;

	public function __construct(){
		parent::__construct();
		Language::read('trade');
	}

	
        public function order_classOp(){
            $model_city_centre = Model();
            $city_centreList=$model_city_centre->table("city_centre")->field("id,city_name")->select(); 
            Tpl::output('city_centreList',$city_centreList);            
            $model_order = Model('order');
            
            $condition	= array();
            if($_GET['member_truename']) {
                  $condition['member_truename'] = $_GET['member_truename'];
            }
//            if($_GET['ktext']) { //验证城市中心
//                  $condition['sap_employeeinfo.ktext'] = $_GET['ktext'];
//            }
            if($_GET['city_id']) {
                  $condition['id'] = $_GET['city_id'];
            }
            if($_GET['order_sn']) {
                 $condition['order_sn'] =array('like', '%' . trim($_GET['order_sn']) . '%');
            }
            if($_GET['store_name']) {
                $condition['store_name'] =array('like', '%' . trim($_GET['store_name']) . '%');
            }
            if(isset($_GET['order_state']) && $_GET['order_state'] != '' ){
		if(in_array($_GET['order_state'],array(ORDER_STATUS_SEND_ONE,ORDER_STATUS_SEND_HET,ORDER_STATE_SUCCESS,ORDER_STATUS_SEND_TWO,ORDER_STATUS_SUCCESS,ORDER_STATUS_ERROR,ORDER_STATUS_OUT,ORDER_STATUS_CUS_RECEIVED,ORDER_STATE_SEND,ORDER_STATE_CANCEL))){
			$condition['order_state'] = $_GET['order_state'];
				}
		}
            if($_GET['payment_code']) {
                $condition['payment_code'] = $_GET['payment_code'];
            }    
            if($_GET['buyer_name']) {
                 $condition['buyer_name'] =array('like', '%' . trim($_GET['buyer_name']) . '%');
            }
        
            $if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_time']);
            $if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_time']);
            $start_unixtime = $if_start_time ? strtotime($_GET['query_start_time']) : null;
            $end_unixtime = $if_end_time ? strtotime($_GET['query_end_time']): null;
            if ($start_unixtime || $end_unixtime) {
                $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
            }
            $condition['order_lei'] = 2;
//            $order_list = $model_city_centre->table('order,member,city_centre')->join('left join')
//               ->on('order.buyer_id=member.member_id,member.belong_city_id=city_centre.id')
//                  ->where($condition)->page(30)->field("`order`.*,member.member_id,city_centre.id as city_id,city_centre.city_name")->select();    
//           
	    $field = '`order`.*, member.member_id,member.member_truename as member_truename,city_centre.id as city_id,city_centre.city_name,';
	    $field.= '(select ktext from sap_employeeinfo where sap_employeeinfo.pernr=member.member_name) as ktext';
	    $on = '`order`.buyer_id=member.member_id,member.belong_city_id=city_centre.id';
	    $model_city_centre->table('order,member,city_centre')->field($field);
	    $order_list = $model_city_centre->join('left,left')->on($on)->where($condition)->page(30)->select();
            
            foreach ($order_list as $order_id => $order_info) {
                //显示取消订单
                $order_list[$order_id]['if_cancel'] = $model_order->getOrderOperateState('system_cancel',$order_info);
                //显示收到货款
                $order_list[$order_id]['if_system_receive_pay'] = $model_order->getOrderOperateState('system_receive_pay',$order_info);
            }
            //显示支付接口列表(搜索)
            $payment_list = Model('payment')->getPaymentOpenList();
            Tpl::output('payment_list',$payment_list);
            Tpl::output('order_list',$order_list);
            Tpl::output('show_page',$model_order->showpage());
            Tpl::showpage('order.index.in');
        }

        
	/**
	 * 平台订单状态操作
	 *
	 */
	public function change_stateOp() {
        $order_id = intval($_GET['order_id']);
        if($order_id <= 0){
            showMessage(L('miss_order_number'),$_POST['ref_url'],'html','error');
        }
        $model_order = Model('order');

        //获取订单详细
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info	= $model_order->getOrderInfo($condition);

        if ($_GET['state_type'] == 'cancel') {
            $result = $this->_order_cancel($order_info);
        } elseif ($_GET['state_type'] == 'receive_pay') {
            $result = $this->_order_receive_pay($order_info,$_POST);
        }
        if (!$result['state']) {
            showMessage($result['msg'],$_POST['ref_url'],'html','error');
        } else {
            showMessage($result['msg'],$_POST['ref_url']);
        }
	}

	/**
	 * 系统取消订单
	 */
	private function _order_cancel($order_info) {
	    $order_id = $order_info['order_id'];
	    $model_order = Model('order');
	    $logic_order = Logic('order');
	    $if_allow = $model_order->getOrderOperateState('system_cancel',$order_info);
	    if (!$if_allow) {
	        return callback(false,'无权操作');
	    }
	    $result =  $logic_order->changeOrderStateCancel($order_info,'system', $this->admin_info['name']);
        if ($result['state']) {
            $this->log(L('order_log_cancel').','.L('order_number').':'.$order_info['order_sn'],1);
        }
        return $result;
	}

	/**
	 * 系统收到货款
	 * @throws Exception
	 */
	private function _order_receive_pay($order_info, $post) {
	    $order_id = $order_info['order_id'];
	    $model_order = Model('order');
	    $logic_order = Logic('order');
	    $if_allow = $model_order->getOrderOperateState('system_receive_pay',$order_info);
	    if (!$if_allow) {
	        return callback(false,'无权操作');
	    }

	    if (!chksubmit()) {
	        Tpl::output('order_info',$order_info);
	        //显示支付接口列表
	        $payment_list = Model('payment')->getPaymentOpenList();
	        //去掉预存款和货到付款
	        foreach ($payment_list as $key => $value){
	            if ($value['payment_code'] == 'predeposit' || $value['payment_code'] == 'offline') {
	               unset($payment_list[$key]);
	            }
	        }
	        Tpl::output('payment_list',$payment_list);
	        Tpl::showpage('order.receive_pay');
	        exit();
	    }
	    $order_list	= $model_order->getOrderList(array('pay_sn'=>$order_info['pay_sn'],'order_state'=>ORDER_STATE_NEW));
	    $result = $logic_order->changeOrderReceivePay($order_list,'system',$this->admin_info['name'],$post);
        if ($result['state']) {
            $this->log('将订单改为已收款状态,'.L('order_number').':'.$order_info['order_sn'],1);
        }
	    return $result;
	}

	/**
	 * 查看订单
	 *
	 */
	public function show_orderOp(){
	    $order_id = intval($_GET['order_id']);
	    if($order_id <= 0 ){
	        showMessage(L('miss_order_number'));
	    }
        $model_order	= Model('order');
        $order_info	= $model_order->getOrderInfo(array('order_id'=>$order_id),array('order_goods','order_common','store'));

        //订单变更日志
		$log_list	= $model_order->getOrderLogList(array('order_id'=>$order_info['order_id']));
		Tpl::output('order_log',$log_list);
		//退款退货信息
        $model_refund = Model('refund_return');
        $condition = array();
        $condition['order_id'] = $order_info['order_id'];
        $condition['seller_state'] = 2;
        $condition['admin_time'] = array('gt',0);
        $return_list = $model_refund->getReturnList($condition);
        Tpl::output('return_list',$return_list);

        //退款信息
        $refund_list = $model_refund->getRefundList($condition);
        Tpl::output('refund_list',$refund_list);

		//卖家发货信息
		if (!empty($order_info['extend_order_common']['daddress_id'])) {
		    $daddress_info = Model('daddress')->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
		    Tpl::output('daddress_info',$daddress_info);
		}

		Tpl::output('order_info',$order_info);
        Tpl::showpage('order.view');
	}

	/**
	 * 导出
	 *
	 */
	public function export_step1Op(){
		$lang	= Language::getLangContent();

	    $model_order = Model('order');
        $condition	= array();
        if($_GET['order_sn']) {
        	$condition['order_sn'] = $_GET['order_sn'];
        }
        if($_GET['store_name']) {
            $condition['store_name'] = $_GET['store_name'];
        }
        if(in_array($_GET['order_state'],array('0','10','20','30','40'))){
        	$condition['order_state'] = $_GET['order_state'];
        }
        if($_GET['payment_code']) {
            $condition['payment_code'] = $_GET['payment_code'];
        }
        if($_GET['buyer_name']) {
            $condition['buyer_name'] = $_GET['buyer_name'];
        }
        $if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_time']);
        $if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_time']);
        $start_unixtime = $if_start_time ? strtotime($_GET['query_start_time']) : null;
        $end_unixtime = $if_end_time ? strtotime($_GET['query_end_time']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }

		if (!is_numeric($_GET['curpage'])){
			$count = $model_order->getOrderCount($condition);
			$array = array();
			if ($count > self::EXPORT_SIZE ){	//显示下载链接
				$page = ceil($count/self::EXPORT_SIZE);
				for ($i=1;$i<=$page;$i++){
					$limit1 = ($i-1)*self::EXPORT_SIZE + 1;
					$limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
					$array[$i] = $limit1.' ~ '.$limit2 ;
				}
				Tpl::output('list',$array);
				Tpl::output('murl','index.php?act=order&op=index');
				Tpl::showpage('export.excel');
			}else{	//如果数量小，直接下载
				$data = $model_order->getOrderList($condition,'','*','order_id desc',self::EXPORT_SIZE);
				$this->createExcel($data);
			}
		}else{	//下载
			$limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
			$limit2 = self::EXPORT_SIZE;
			$data = $model_order->getOrderList($condition,'','*','order_id desc',"{$limit1},{$limit2}");
			$this->createExcel($data);
		}
	}

	/**
	 * 生成excel
	 *
	 * @param array $data
	 */
	private function createExcel($data = array()){
		Language::read('export');
		import('libraries.excel');
		$excel_obj = new Excel();
		$excel_data = array();
		//设置样式
		$excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
		//header
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_no'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_store'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_buyer'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_xtimd'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_count'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_yfei'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_paytype'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_state'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_storeid'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_buyerid'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_bemail'));
		//data
		foreach ((array)$data as $k=>$v){
			$tmp = array();
			$tmp[] = array('data'=>'NC'.$v['order_sn']);
			$tmp[] = array('data'=>$v['store_name']);
			$tmp[] = array('data'=>$v['buyer_name']);
			$tmp[] = array('data'=>date('Y-m-d H:i:s',$v['add_time']));
			$tmp[] = array('format'=>'Number','data'=>ncPriceFormat($v['order_amount']));
			$tmp[] = array('format'=>'Number','data'=>ncPriceFormat($v['shipping_fee']));
			$tmp[] = array('data'=>orderPaymentName($v['payment_code']));
			$tmp[] = array('data'=>orderState($v));
			$tmp[] = array('data'=>$v['store_id']);
			$tmp[] = array('data'=>$v['buyer_id']);
			$tmp[] = array('data'=>$v['buyer_email']);
			$excel_data[] = $tmp;
		}
		$excel_data = $excel_obj->charset($excel_data,CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
		$excel_obj->generateXML($excel_obj->charset(L('exp_od_order'),CHARSET).$_GET['curpage'].'-'.date('Y-m-d-H',time()));
	}
        
        
        
	/**
	 * 导出采购订单
	 *
	 */
	public function Purchaseexport_step1Op(){
            
  
            
        $lang	= Language::getLangContent();
        $model_city_centre = Model(); 
        $model_order = Model('order');
        
        $condition	= array();
           $condition['order_lei'] = 2;
        if($_GET['member_truename']) {
                  $condition['member_truename'] = $_GET['member_truename'];
            }
        if($_GET['city_id']) {
                  $condition['id'] = $_GET['city_id'];
            }
        if($_GET['city_name']) {
                $condition['city_name'] = $_GET['city_name'];
        }
        if($_GET['order_sn']) {
        	 $condition['order_sn'] =array('like', '%' . trim($_GET['order_sn']) . '%');
        }
        if($_GET['store_name']) {
           $condition['store_name'] =array('like', '%' . trim($_GET['store_name']) . '%');
        }       
         if(isset($_GET['order_state']) && $_GET['order_state'] != '' ){
				if(in_array($_GET['order_state'],array(ORDER_STATUS_SEND_ONE,ORDER_STATUS_SEND_HET,ORDER_STATE_SUCCESS,ORDER_STATUS_SEND_TWO,ORDER_STATUS_SUCCESS,ORDER_STATUS_ERROR,ORDER_STATUS_OUT,ORDER_STATUS_CUS_RECEIVED,ORDER_STATE_SEND,ORDER_STATE_CANCEL))){
					$condition['order_state'] = $_GET['order_state'];
				}
			}
//        if(in_array($_GET['order_state'],array('0','10','20','30','40'))){
//        	$condition['order_state'] = $_GET['order_state'];
//        }
        
//        if($_GET['payment_code']) {
//            $condition['payment_code'] = $_GET['payment_code'];
//        }
        if($_GET['buyer_name']) {
            $condition['buyer_name'] = $_GET['buyer_name'];
        }
        $if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_time']);
        $if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_time']);
        $start_unixtime = $if_start_time ? strtotime($_GET['query_start_time']) : null;
        $end_unixtime = $if_end_time ? strtotime($_GET['query_end_time']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }
		if (!is_numeric($_GET['curpage'])){
                    //先统计order的表总数进行判断
                    $count = $model_order->getOrderCount($condition);
			$array = array();
			if ($count > self::EXPORT_SIZE ){	//显示下载链接
				$page = ceil($count/self::EXPORT_SIZE);
				for ($i=1;$i<=$page;$i++){
					$limit1 = ($i-1)*self::EXPORT_SIZE + 1;
					$limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
					$array[$i] = $limit1.' ~ '.$limit2 ;
				}
				Tpl::output('list',$array);
				Tpl::output('murl','index.php?act=order&op=index');
				Tpl::showpage('export.excel');
			}else{	//如果数量小，直接下载
				//$data = $model_order->getOrderList($condition,'','*','order_id desc',self::EXPORT_SIZE);
                            	    $field = '`order`.*, member.member_id,member.member_truename as member_truename,city_centre.id as city_id,city_centre.city_name,';
                                    $field.= '(select ktext from sap_employeeinfo where sap_employeeinfo.pernr=member.member_name) as ktext';
	                            $on = '`order`.buyer_id=member.member_id,member.belong_city_id=city_centre.id';
                                    $model_city_centre->table('order,member,city_centre')->field($field);
                                    $order_list = $model_city_centre->join('left,left')->on($on)->where($condition)->limit(self::EXPORT_SIZE)->select();                                                      
//                                $order_list = $model_city_centre->table('order,member,city_centre')->join('left join')
//                                  ->on('order.buyer_id=member.member_id,member.belong_city_id=city_centre.id')
//                                  ->where($condition)->limit(self::EXPORT_SIZE)->field("`order`.*,member.member_id,city_centre.id as city_id,city_centre.city_name")->select();                         
                                   $this->PurchasecreateExcel($order_list);
			}
		}else{	//下载
			$limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
			$limit2 = self::EXPORT_SIZE;
                        $field = '`order`.*, member.member_id,member.member_truename as member_truename,city_centre.id as city_id,city_centre.city_name,';
                                    $field.= '(select ktext from sap_employeeinfo where sap_employeeinfo.pernr=member.member_name) as ktext';
	                            $on = '`order`.buyer_id=member.member_id,member.belong_city_id=city_centre.id';
                                    $model_city_centre->table('order,member,city_centre')->field($field);
                                    $order_list = $model_city_centre->join('left,left')->on($on)->where($condition)->order('order_id desc')->limit("{$limit1},{$limit2}")->select();                                                      
//			$data = $model_order->getOrderList($condition,'','*','order_id desc',"{$limit1},{$limit2}");
			$this->PurchasecreateExcel($order_list);
		}
	}

	/**
	 * 生成采购订单excel
	 *
	 * @param array $data
	 */
	private function PurchasecreateExcel($data = array()){
           
		Language::read('export');
		import('libraries.excel');
		$excel_obj = new Excel();
		$excel_data = array();
		//设置样式
		$excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
		//header
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_no'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_store'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_buyer'));
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_ktext'));//项目名称
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_member_truename'));//买家姓名
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_city_name')); 
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_xtimd'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_count'));
//		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_yfei'));    //运费                       
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_state'));
//		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_storeid')); //店铺ID
//		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_buyerid'));  //买家ID
//		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_od_bemail')); //买家Email
               
		foreach ((array)$data as $k=>$v){
			$tmp = array();
			$tmp[] = array('data'=>$v['order_sn']);
			$tmp[] = array('data'=>$v['store_name']);
			$tmp[] = array('data'=>$v['buyer_name']);
                        $tmp[] = array('data'=>$v['ktext']);
                        $tmp[] = array('data'=>$v['member_truename']);
                        $tmp[] = array('data'=>$v['city_name']); 
			$tmp[] = array('data'=>date('Y-m-d H:i:s',$v['add_time']));
			$tmp[] = array('format'=>'Number','data'=>ncPriceFormat($v['order_amount']));
//			$tmp[] = array('format'=>'Number','data'=>ncPriceFormat($v['shipping_fee']));  //运费
			$tmp[] = array('data'=>orderState($v));
//			$tmp[] = array('data'=>$v['store_id']);
//			$tmp[] = array('data'=>$v['buyer_id']);
//			$tmp[] = array('data'=>$v['buyer_email']);
			$excel_data[] = $tmp;
		}    
               
		$excel_data = $excel_obj->charset($excel_data,CHARSET);
                
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
		$excel_obj->generateXML($excel_obj->charset(L('exp_od_order'),CHARSET).$_GET['curpage'].'-'.date('Y-m-d-H',time()));
	}
 
        
        
        //刷新商品表中的可销售区域  
        public function exitGoodsOp(){
            $model_order = Model();
	    $goodsList=$model_order->table('store')->field("store_id , store_city_id ")->where("1=1")->group('store_id')->select();	
	    foreach($goodsList as $v){
                $model_order->beginTransaction();
                if(!empty($v['store_city_id'])){
                    $c1 = substr($v['store_city_id'],-1);
                    $c2 = substr($v['store_city_id'],0,strlen($v['store_city_id'])-1);
                    if( $c1=="," && !empty($c2) ){
                        $city = substr($v['store_city_id'],0,strlen($v['store_city_id'])-1);
                    }else{
                        $city = $v['store_city_id'];
                    }
                    $ra = $model_order->table('goods_common')->where("store_id = '".$v['store_id']."' and sales_area = ''")->update(array("sales_area"=>$city));
                    $rb = $model_order->table('goods')->where("store_id = '".$v['store_id']."' and sales_area is null")->update(array("sales_area"=>$city));
                }
                    if($ra && $rb){
                        $model_order->commit();  //提交事务  
                        var_dump("成功1");
                    }else{
                        $model_order->rollBack(); //事物回滚
                         var_dump("失败2");
                }               
	    };          
        }
        
                //刷新商品表中的可销售区域  
        public function exitGoods2Op(){
            $model_order = Model();
	    $goodsList=$model_order->table('store')->field("store_id , store_city_id ")->where("1=1")->group('store_id')->select();	
            $model_order->beginTransaction();
	    foreach($goodsList as $v){
	        $ra = $model_order->table('goods_common')->where("store_id = '".$v['store_id']."' and sales_area is null ")->update(array("sales_area"=>substr($v['store_city_id'],0,strlen($v['store_city_id'])-1)));
	        $rb = $model_order->table('goods')->where("store_id = '".$v['store_id']."' and sales_area = ''")->update(array("sales_area"=>substr($v['store_city_id'],0,strlen($v['store_city_id'])-1)));
	    };
            if($ra && $rb){
                $model_order->commit();  //提交事务  
                var_dump("成功1");
            }else{
                $model_order->rollBack(); //事物回滚
                 var_dump("失败2");
            }
        }
        
        
                //刷新商品表中的可销售区域  
        public function exitGoods3Op(){
            $model_order = Model();
	    $goodsList=$model_order->table('store')->field("store_id , store_city_id ")->where("1=1")->group('store_id')->select();	
            $model_order->beginTransaction();
	    foreach($goodsList as $v){
	        $ra = $model_order->table('goods_common')->where("store_id = '".$v['store_id']."' and sales_area = '' ")->update(array("sales_area"=>substr($v['store_city_id'],0,strlen($v['store_city_id'])-1)));
	        $rb = $model_order->table('goods')->where("store_id = '".$v['store_id']."' and sales_area is null")->update(array("sales_area"=>substr($v['store_city_id'],0,strlen($v['store_city_id'])-1)));
	    };
            if($ra && $rb){
                $model_order->commit();  //提交事务  
                var_dump("成功1");
            }else{
                $model_order->rollBack(); //事物回滚
                 var_dump("失败2");
            }
        }
        
        
        
        /**
         * 报表
         */
        
        
          public function order_classAll2Op(){

            $model_city_centre = Model();
            $city_centreList=$model_city_centre->table("city_centre")->field("id,city_name")->select(); //输出中心城市
            $gc_list = Model('goods_class')->getGoodsClassListByParentId(0);// 一级商品分类
            
           // $model_city_centre->query("create temporary table sac_emp select ep.butxt,ep.kostl,ep.ktext from sc_order as od left join sap_employeeinfo as ep on od.buyer_name = ep.pernr");
           
            $sql="  1=1 " ;
            if(isset($_GET['order_state']) && $_GET['order_state'] != '' ){
		if(in_array($_GET['order_state'],array(ORDER_STATUS_SEND_ONE,ORDER_STATUS_SEND_HET,ORDER_STATE_SUCCESS,ORDER_STATUS_SEND_TWO,ORDER_STATUS_SUCCESS,ORDER_STATUS_ERROR,ORDER_STATUS_OUT,ORDER_STATUS_CUS_RECEIVED,ORDER_STATE_SEND,ORDER_STATE_CANCEL))){
                        $sql=$sql." and (`order`.order_state = '".$_GET['order_state']."') ";
				}
		}
             
            if($_GET['city_name']) {
                 $sql=$sql." and (butxt = '".$_GET['city_name']."') ";
            }
            //项目名称
            if($_GET['ktext']) {
                 $sql=$sql." and (ktext like '%".$_GET['ktext']."%') ";
            }
            //供应商名称
            if($_GET['store_company_name']) {
                 $sql=$sql." and (`store`.store_company_name like '%".$_GET['store_company_name']."%') ";
            }
            //验收时间
            $ys_if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['ys_query_start_time']);
            $ys_if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['ys_query_end_time']);
            $ys_start_unixtime = $ys_if_start_time ? strtotime($_GET['ys_query_start_time']) : null;
            $ys_end_unixtime = $ys_if_end_time ? strtotime($_GET['ys_query_end_time']): null;
                if ($ys_start_unixtime || $ys_end_unixtime) {
                $sql=$sql." and (`order`.finnshed_time  BETWEEN '".$ys_start_unixtime."' AND '".$ys_end_unixtime."') ";
                }
                //下单时间
            $xd_if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['xd_query_start_time']);
            $xd_if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['xd_query_end_time']);
            $xd_start_unixtime = $xd_if_start_time ? strtotime($_GET['xd_query_start_time']) : null;
            $xd_end_unixtime = $xd_if_end_time ? strtotime($_GET['xd_query_end_time']): null;
                if ($xd_start_unixtime || $xd_end_unixtime) {
                  $sql=$sql." and (`order`.add_time  BETWEEN '".$xd_start_unixtime."' AND '".$xd_end_unixtime."') ";  
                }
            //内部物料
               $n_gc_id = $_GET['n_class_id'];   
              //查询商品分类是否有父级ID
               if($n_gc_id){
                    $gc_class = $model_city_centre->table('goods_class')->where('gc_id='.$n_gc_id)->field('gc_parent_id')->find();
                    //如果没有有上级ID 则是一级分类
                if($gc_class['gc_parent_id'] == 0){
                        $sql=$sql." and (`product`.gc_id_1 = '".$n_gc_id."') ";
                        $sql=$sql." and (`product`.gc_id_2 = '0') ";
                        $sql=$sql." and (`product`.gc_id_3 = '0') ";
                    }else{
                        //有上级 查询这个上级ID
                        $gc_class_two = $model_city_centre->table('goods_class')->where('gc_id='.$gc_class['gc_parent_id'])->field('gc_parent_id')->find();

                        if($gc_class_two['gc_parent_id'] > 0){
                            $sql=$sql." and (`product`.gc_id_1 = '".$gc_class_two['gc_parent_id']."') ";
                            $sql=$sql." and (`product`.gc_id_2 = '".$gc_class['gc_parent_id']."') ";
                            $sql=$sql." and (`product`.gc_id_3 = '".$n_gc_id."') ";
                        }else{
                            $sql=$sql." and (`product`.gc_id_1 = '".$gc_class['gc_parent_id']."') ";
                            $sql=$sql." and (`product`.gc_id_2 = '".$n_gc_id."') ";
                            $sql=$sql." and (`product`.gc_id_3 = '0') ";
                        }

                    }   
                };
            //外部物料  
                $w_gc_id = $_GET['w_class_id'];   
             if($w_gc_id){   
              //查询商品分类是否有父级ID
                $w_gc_class = $model_city_centre->table('goods_class')->where('gc_id='.$w_gc_id)->field('gc_parent_id')->find();
                //如果没有有上级ID 则是一级分类
            if($w_gc_class['gc_parent_id'] == 0){
                    $sql=$sql." and (`product`.gc_id_1 = '".$w_gc_id."') ";
                    $sql=$sql." and (`product`.gc_id_2 = '0') ";
                    $sql=$sql." and (`product`.gc_id_3 = '0') ";
                    $sql=$sql." and (`product`.product_level = '0') ";
                }else{
                    //有上级 查询这个上级ID
                    $w_gc_class_two = $model_city_centre->table('goods_class')->where('gc_id='.$w_gc_class['gc_parent_id'])->field('gc_parent_id')->find();
                    
                    if($w_gc_class_two['gc_parent_id'] > 0){
                        $sql=$sql." and (`product`.gc_id_1 = '".$w_gc_class_two['gc_parent_id']."') ";
                        $sql=$sql." and (`product`.gc_id_2 = '".$w_gc_class['gc_parent_id']."') ";
                        $sql=$sql." and (`product`.gc_id_3 = '".$w_gc_id."') ";
                        $sql=$sql." and (`product`.product_level = '0') ";
                    }else{
                        $sql=$sql." and (`product`.gc_id_1 = '".$w_gc_class['gc_parent_id']."') ";
                        $sql=$sql." and (`product`.gc_id_2 = '".$w_gc_id."') ";
                        $sql=$sql." and (`product`.gc_id_3 = '0') ";
                        $sql=$sql." and (`product`.product_level = '0') ";
                    }
                    
                }   
             };   
                
            $field='`order`.'; 
            
            
            
//            $on ='`order_goods`.order_id = `order`.order_id';
//            $model_city_centre->table('order,order_goods')->field($field);
//            $order_list = $model_city_centre->join('left,left')->on($on)->where($sql)->page(30)->select();
              
//            $field =' `order_goods`.goods_price as goods_price ,`order_goods`.goods_num as goods_num ,`order_goods`.goods_pay_price as goods_pay_price  ,' ;//order_goods 单价 数量 成交价
//            $field.=' `order`.order_sn as order_sn , `order`.order_state ,`order`.add_time, `order`.finnshed_time ,' ;//order表 
//            $field.='`goods`.materiel_code as materiel_code ,';//goods表 外部物料编号
//            $field.='(select gc_name from sc_goods_class where `product`.gc_id_1 = sc_goods_class.gc_id) as w_gc_id_1 ,'; //外部物料大类
//            $field.='(select gc_name from sc_goods_class where `product`.gc_id_2 = sc_goods_class.gc_id) as w_gc_id_2 ,';//中类
//            $field.='(select gc_name from sc_goods_class where `product`.gc_id_3 = sc_goods_class.gc_id) as w_gc_id_3 ,';//小类
//            $field.='`product`.local_description as w_local_description ,`product`.brand as brand, `product`.product_spec as product_spec ,`product`.to_product_id as to_product_id,`product`.gc_id_1 ,`product`.gc_id_2 ,`product`.gc_id_3 ,`product`.product_level ,';//外部物料物料名称 ，品牌 ,规格,内部物料
//            $field.='(select gc_name from sc_goods_class where (select p2.gc_id_1 from sc_product  as p2 where p2.product_id=`product`.to_product_id ) = sc_goods_class.gc_id) as n_gc_id_1 ,'; //内部物料大类
//            $field.='(select gc_name from sc_goods_class where (select p2.gc_id_2 from sc_product  as p2 where p2.product_id=`product`.to_product_id ) = sc_goods_class.gc_id) as n_gc_id_2 ,'; //内部物料中类
//            $field.='(select gc_name from sc_goods_class where (select p2.gc_id_3 from sc_product  as p2 where p2.product_id=`product`.to_product_id ) = sc_goods_class.gc_id) as n_gc_id_3 ,'; //内部物料小类
//            $field.='(select p3.local_description from sc_product as p3 where p3.product_id=`product`.to_product_id ) as n_local_description ,';
//            $field.='(select p4.product_spec from sc_product as p4 where p4.product_id=`product`.to_product_id ) as n_product_spec ,';
//            $field.='(select p5.brand from sc_product as p5 where p5.product_id=`product`.to_product_id ) as n_brand ,'; //内部物料品牌
//            $field.='`store`.member_name as member_name ,`store`.store_name as store_name , `store`.store_company_name as store_company_name ,' ;  //账号  店铺名称  公司名称 
//            $field.='`city_centre`.city_name  as butxt ,';//所属城市
//            $field.='(select emp.kostl  from sap_employeeinfo as emp where emp.pernr=`order`.buyer_name)  as kostl ,'; 
//            $field.='(select emp.ktext  from sap_employeeinfo as emp where emp.pernr=`order`.buyer_name)  as ktext '; 
//            
//            
//            
//            $on ='`order_goods`.order_id = `order`.order_id,'
//                    . '`order_goods`.goods_id = `goods`.goods_id ,'
//                    . '`goods`.materiel_code=`product`.product_id ,'
//                    . '`goods`.store_id=`store`.store_id ,'
//                    . '`order`.buyer_id = `member`.member_name , `member`.belong_city_id =`city_centre`.id';
//            $model_city_centre->table('order_goods,order,goods,product,store,member,city_centre')->field($field);
//            $order_list = $model_city_centre->join('left,left')->on($on)->where($sql)->page(30)->select();
            Tpl::output('gc_list', $gc_list);
            Tpl::output('order_list',$order_list);
            Tpl::output('city_centreList',$city_centreList);    
            Tpl::output('show_page',$model_city_centre->showpage());
            Tpl::showpage('order.index.all');
        }
        /**
         * 供应商报表
         */
          public function order_gys_AllOp(){
            $model_store_joinin = Model();
            $city_centreList=$model_store_joinin->table("city_centre")->field("id,city_name")->select(); //输出城市中心
            $area_List=$model_store_joinin->table("area")->field("area_id,area_name")->where(array("area_parent_id"=>"'0'"))->select(); //输出地区
            
            
            $sql="  1=1 " ;
            if(!empty($_GET['city_id'])) {
                $sql=$sql." and (`city_centre`.id = '".$_GET['city_id']."') ";
            }
            if(!empty($_GET['supplier_code'])) {
                $sql=$sql." and (`store_joinin`.member_name = '".$_GET['supplier_code']."') ";
            }
            if(!empty($_GET['supplier_name'])) {
                $sql=$sql." and (`supplier`.company_name like '%".$_GET['supplier_name']."%') ";
            }
            if(!empty($_GET['area_list_id'])) {
                $sql=$sql." and (`supplier`.company_province_id = '".$_GET['area_list_id']."') ";
            }
            if(!empty($_GET['supply_level'])) {
                $sql=$sql." and (`supplier`.level = '".$_GET['supply_level']."') ";
            }
            if(!empty($_GET['role_id'])) {
                $sql=$sql." and (`member`.role_id = '".$_GET['role_id']."') ";
            }
            $field = "`store_joinin`.member_name,"
                    . "`supplier`.company_name,"
                    . "`member`.member_time,"
                    . "`supplier`.add_time as supply_end_time ,"
                    . "`supplier`.end_time as  supply_type_json ,"
                    . "`supplier`.level as supply_level,"
                    . "`supplier`.contacts_email as contacts_email ,"
                    . "`store_joinin`.store_name as store_name ,"
                    . "`city_centre`.city_name as city_name,"
                    . "`supplier_information`.city_contacts_name as city_contacts_name ,"
                    . "`supplier_information`.city_contacts_phone as city_contacts_phone,"
                    . "`store_joinin`.sc_name as sc_name,"
                    . "(select count(`goods_common`.goods_commonid) from sc_goods_common as `goods_common` where `goods_common`.store_id = `supplier`.store_id and `goods_common`.goods_state = '1' and `goods_common`.goods_verify = '1') as line_num,"
                    . "(select count(`goods_common`.goods_commonid) from sc_goods_common as `goods_common` where `goods_common`.store_id = `supplier`.store_id and `goods_common`.goods_verify = '1') as tg_num,"
                    . "(select sum(order_amount) from sc_order as `order` "
                    . "left join sc_member as `member` on `order`.buyer_id = `member`.member_id and `member`.role_id in('01','04') "
                    . "where `order`.store_id = `supplier`.store_id and `member`.belong_city_id = `store_joinin`.city_center) as all_money"
            ;


            $on = "`store_joinin`.member_id = `member`.member_id,"
                    . "`member`.member_id = `supplier`.member_id,"
                    . "`store_joinin`.city_center = `supplier_information`.join_city and `member`.member_id = `supplier_information`.member_id,"
                    . "`store_joinin`.city_center = `city_centre`.id"
            ;
	    
            $store_joinin_list = $model_store_joinin->
                    table('store_joinin,member,supplier,supplier_information,city_centre')->join('left,left,left,left')
                    ->on($on)->where($sql)->field($field)->page(30)->select(); 
            
            Tpl::output('area_List',$area_List);   
            Tpl::output('city_centreList',$city_centreList);    
            Tpl::output('store_joinin_list',$store_joinin_list);
            Tpl::output('show_page',$model_store_joinin->showpage());
            Tpl::showpage('order.index.gysList');
        }
        
        
      
	/**
	 * 导出供应商表
	 *
	 */
	public function Store_joininOp(){

        $lang	= Language::getLangContent();
        $model_store_joinin = Model();
         $sql="  1=1 " ;
            if($_GET['city_id']) {
                $sql=$sql." and (`city_centre`.id = '".$_GET['city_id']."') ";
            }
            if($_GET['area_list_id']) {
                $sql=$sql." and (`supplier`.company_province_id = '".$_GET['area_list_id']."') ";
            }
            if($_GET['supply_level']) {
                $sql=$sql." and (`supplier`.supply_level = '".$_GET['supply_level']."') ";
            }
            if($_GET['role_id']) {
                $sql=$sql." and (`member`.role_id = '".$_GET['role_id']."') ";
            }
		if (!is_numeric($_GET['curpage'])){
                     $field = '`supplier`.member_name,'
                    . '`supplier`.type_json,'
                    . '`supplier`.company_name,'
                    . '`supplier`.company_province_id,'
                    . '`supplier`.add_time as supply_end_time ,'
                    . '`supplier`.end_time as  supply_type_json ,'
                    . '`supplier`.level as supply_level,'
                    . '`supplier`.contacts_email as contacts_email ,'
                    . '`store_joinin`.store_name as store_name ,'
                    . '`city_centre`.city_name as city_name,'
                    . '`supplier_information`.city_contacts_name as city_contacts_name ,'
                    . '`supplier_information`.city_contacts_phone as city_contacts_phone';
                     
                    $on = ' `supplier`.member_id=`store_joinin`.member_id,'
                    . '`supplier`.member_id=`supplier_information`.member_id,'
                    . '`store_joinin`.city_center=`city_centre`.id ,`supplier`.member_id=`member`.member_id';
                    
                    $store_joinin_list = $model_store_joinin->
                    table('supplier,store_joinin,supplier_information,city_centre,member')->join('left,left')
                    ->on($on)->where($sql)->field($field)->limit("0,100000")->select();
                   $this->Store_joininExcel($store_joinin_list);
		}
	}

	/**
	 * 生成供应商excel
	 *
	 * @param array $data
	 */
	private function Store_joininExcel($data = array()){
                $model = Model();
		Language::read('export');
		import('libraries.excel');
		$excel_obj = new Excel();
		$excel_data = array();
		//设置样式
		$excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
                
               
		//header
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_jb_member_name'));//供应商账号
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_jb_company_name'));//供应商名称
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_jb_store_name'));//店铺名称
//                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_jb_cit_name'));//所在地
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_jb_city_name'));//城市公司
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_jb_contacts_name')); //联系人
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_jb_contacts_phone'));//联系电话
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_jb_contacts_email'));  // 邮箱地址             
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_jb_member_time'));   //   注册时间 
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_jb_supply_end_time'));//有效截止时间
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_jb_supply_type_json'));   //    类别          
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_jb_supply_level')); //  供应商等级
		foreach ((array)$data as $k=>$v){
			$tmp = array();
			$tmp[] = array('data'=>$v['member_name']);
			$tmp[] = array('data'=>$v['company_name']);
			$tmp[] = array('data'=>$v['store_name']);
//                        $tmp[] = array('data'=>$v['cit_name']);
                        $tmp[] = array('data'=>$v['city_name']);
                        $tmp[] = array('data'=>$v['city_contacts_name']); 
                        $tmp[] = array('data'=>$v['city_contacts_phone']); 
                        $tmp[] = array('data'=>$v['contacts_email']);                        
			$tmp[] = array('data'=>date('Y-m-d H:i:s',$v['member_time']));
                        $tmp[] = array('data'=>date('Y-m-d H:i:s',$v['supply_end_time']));
                        //级别的处理 得到级别json串转换
                        $member_supplier_type = json_decode($v['type_json'],true);
                        $supplier_type_father = array();//一级
                        $supplier_type_sun = array();//二级
                        $type_name="";
                        if(!empty($member_supplier_type) && is_array($member_supplier_type)){
                            foreach ($member_supplier_type as $key=>$v){
                                //根据取的key当做参数进行查询级别
                                $type_namea = $model->table('supplier_type')->field('type_name')->where(array("id"=>$key))->find();
                                $type_name.=$type_namea['type_name'].",";
                                foreach ($v as $v_sun){
                                    $type_namea = $model->table('supplier_type')->field('type_name')->where(array("id"=>$v_sun))->find();
                                    $type_name.=$type_namea['type_name'].",";
                                }
                            }
                        }
                        $tmp[] = array('data'=>$type_name); 
                        if($v['supply_level']==1){
                            $tmp[] = array('data'=>"优选"); 
                        }else if($v['supply_level']==2){
                            $tmp[] = array('data'=>"合格"); 
                        }else if($v['supply_level']==3){
                            $tmp[] = array('data'=>"淘汰"); 
                        }else{
                            $tmp[] = array('data'=>"无"); 
                        }
                        
			$excel_data[] = $tmp;
		}    
               
		$excel_data = $excel_obj->charset($excel_data,CHARSET);
                
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
		$excel_obj->generateXML($excel_obj->charset(L('exp_od_order'),CHARSET).$_GET['curpage'].'-'.date('Y-m-d-H',time()));
	}
    
       
        /**
         * 报表
         */
        
        
          public function order_classAllOp(){
            $model_city_centre = Model();
            $city_centreList=$model_city_centre->table("city_centre")->field("id,city_name")->select(); //输出中心城市
            $gc_list = Model('goods_class')->getGoodsClassListByParentId(0);// 一级商品分类

            $sql="  1=1 " ;
            if(isset($_GET['order_state']) && $_GET['order_state'] != '' ){
		if(in_array($_GET['order_state'],array(ORDER_STATUS_SEND_ONE,ORDER_STATUS_SEND_HET,ORDER_STATE_SUCCESS,ORDER_STATUS_SEND_TWO,ORDER_STATUS_SUCCESS,ORDER_STATUS_ERROR,ORDER_STATUS_OUT,ORDER_STATUS_CUS_RECEIVED,ORDER_STATE_SEND,ORDER_STATE_CANCEL))){
                        $sql=$sql." and (`order`.order_state = '".$_GET['order_state']."') ";
				}
		}
             
            if($_GET['city_name']) {
                 $sql=$sql." and (butxt = '".$_GET['city_name']."') ";
            }
//            //项目名称
//            if($_GET['ktext']) {
//                 $sql=$sql." and (ktext like '%".$_GET['ktext']."%') ";
//            }
            //供应商名称
            if($_GET['store_company_name']) {
                 $sql=$sql." and (`store`.store_company_name like '%".$_GET['store_company_name']."%') ";
            }
            //验收时间
            $ys_if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['ys_query_start_time']);
            $ys_if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['ys_query_end_time']);
            $ys_start_unixtime = $ys_if_start_time ? strtotime($_GET['ys_query_start_time']) : null;
            $ys_end_unixtime = $ys_if_end_time ? strtotime($_GET['ys_query_end_time']): null;
                if ($ys_start_unixtime || $ys_end_unixtime) {
                $sql=$sql." and (`order`.finnshed_time  BETWEEN '".$ys_start_unixtime."' AND '".$ys_end_unixtime."') ";
                }
                //下单时间
            $xd_if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['xd_query_start_time']);
            $xd_if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['xd_query_end_time']);
            $xd_start_unixtime = $xd_if_start_time ? strtotime($_GET['xd_query_start_time']) : null;
            $xd_end_unixtime = $xd_if_end_time ? strtotime($_GET['xd_query_end_time']): null;
                if ($xd_start_unixtime || $xd_end_unixtime) {
                  $sql=$sql." and (`order`.add_time  BETWEEN '".$xd_start_unixtime."' AND '".$xd_end_unixtime."') ";  
                }
            //内部物料
               $n_gc_id = $_GET['n_class_id'];   
              //查询商品分类是否有父级ID
               if($n_gc_id){
                    $gc_class = $model_city_centre->table('goods_class')->where('gc_id='.$n_gc_id)->field('gc_parent_id')->find();
                    //如果没有有上级ID 则是一级分类
                if($gc_class['gc_parent_id'] == 0){
                        $sql=$sql." and (`product`.gc_id_1 = '".$n_gc_id."') ";
                       // $sql=$sql." and (`product`.gc_id_2 = '0') ";
                      //  $sql=$sql." and (`product`.gc_id_3 = '0') ";
                    }else{
                        //有上级 查询这个上级ID
                        $gc_class_two = $model_city_centre->table('goods_class')->where('gc_id='.$gc_class['gc_parent_id'])->field('gc_parent_id')->find();

                        if($gc_class_two['gc_parent_id'] > 0){
                            $sql=$sql." and (`product`.gc_id_1 = '".$gc_class_two['gc_parent_id']."') ";
                            $sql=$sql." and (`product`.gc_id_2 = '".$gc_class['gc_parent_id']."') ";
                            $sql=$sql." and (`product`.gc_id_3 = '".$n_gc_id."') ";
                        }else{
                            $sql=$sql." and (`product`.gc_id_1 = '".$gc_class['gc_parent_id']."') ";
                            $sql=$sql." and (`product`.gc_id_2 = '".$n_gc_id."') ";
                           // $sql=$sql." and (`product`.gc_id_3 = '0') ";
                        }

                    }   
                };
            //外部物料  
                $w_gc_id = $_GET['w_class_id'];   
             if($w_gc_id){   
              //查询商品分类是否有父级ID
                $w_gc_class = $model_city_centre->table('goods_class')->where('gc_id='.$w_gc_id)->field('gc_parent_id')->find();
                //如果没有有上级ID 则是一级分类
            if($w_gc_class['gc_parent_id'] == 0){
                    $sql=$sql." and (`product`.gc_id_1 = '".$w_gc_id."') ";
                    //$sql=$sql." and (`product`.gc_id_2 = '0') ";
                   // $sql=$sql." and (`product`.gc_id_3 = '0') ";
                    $sql=$sql." and (`product`.product_level = '0') ";
                }else{
                    //有上级 查询这个上级ID
                    $w_gc_class_two = $model_city_centre->table('goods_class')->where('gc_id='.$w_gc_class['gc_parent_id'])->field('gc_parent_id')->find();
                    if($w_gc_class_two['gc_parent_id'] > 0){
                        $sql=$sql." and (`product`.gc_id_1 = '".$w_gc_class_two['gc_parent_id']."') ";
                        $sql=$sql." and (`product`.gc_id_2 = '".$w_gc_class['gc_parent_id']."') ";
                        $sql=$sql." and (`product`.gc_id_3 = '".$w_gc_id."') ";
                        $sql=$sql." and (`product`.product_level = '0') ";
                    }else{
                        $sql=$sql." and (`product`.gc_id_1 = '".$w_gc_class['gc_parent_id']."') ";
                        $sql=$sql." and (`product`.gc_id_2 = '".$w_gc_id."') ";
                        //$sql=$sql." and (`product`.gc_id_3 = '0') ";
                        $sql=$sql." and (`product`.product_level = '0') ";
                    }
                    
                }   
             };   
            
             //order表中的参数
            $field='`order`.order_sn as order_sn , '; //订单编号
            $field.='`order`.order_state as order_state , '; //订单状态
            $field.='`order`.add_time as add_time , '; //下单时间
            $field.='`order`.finnshed_time as finnshed_time , '; //订单完成时间
            $field.='`order`.store_name as store_name  ,'; //店铺名称
            $field.='`order`.store_name as store_name  ,'; //店铺名称
            //order_goods表中的参数
            $field.='`order_goods`.goods_price as goods_price  ,'; //单品价格
            $field.='`order_goods`.goods_num as goods_num  ,'; //数量
            $field.='`order_goods`.goods_pay_price as goods_pay_price ,'; //成交价格
            //store 表中的 member_name
            $field.='`store`.store_company_name as store_company_name , '; //供应商名称
            $field.='`store`.member_name as member_name ,'; //供应商名称
            //goods 中的外部物料编号
            $field.='`goods`.materiel_code as materiel_code ,'; //外部物料编号
            //goods_class
            $field.='(select gc_name from sc_goods_class where `goods`.gc_id_1 = sc_goods_class.gc_id) as w_gc_id_1 ,'; //外部物料大类
            $field.='(select gc_name from sc_goods_class where `goods`.gc_id_2 = sc_goods_class.gc_id) as w_gc_id_2 ,';//中类
            $field.='(select gc_name from sc_goods_class where `goods`.gc_id_3 = sc_goods_class.gc_id) as w_gc_id_3 ,';//小类            
            //materiel_code 物料表
            $field.='`product`.local_description as w_local_description , '; //物料名称
            $field.='`product`.brand as brand , '; //物料品牌
            $field.='`product`.product_spec as product_spec , '; //物料规格
            $field.='`product`.to_product_id as to_product_id  ,'; //物料规格
            //内部物料
            $field.='(select gc_name from sc_goods_class where (select p2.gc_id_1 from sc_product  as p2 where p2.product_id=`product`.to_product_id ) = sc_goods_class.gc_id) as n_gc_id_1 ,'; //内部物料大类
            $field.='(select gc_name from sc_goods_class where (select p2.gc_id_2 from sc_product  as p2 where p2.product_id=`product`.to_product_id ) = sc_goods_class.gc_id) as n_gc_id_2 ,'; //内部物料中类
            $field.='(select gc_name from sc_goods_class where (select p2.gc_id_3 from sc_product  as p2 where p2.product_id=`product`.to_product_id ) = sc_goods_class.gc_id) as n_gc_id_3 ,'; //内部物料小类
            $field.='(select p3.local_description from sc_product as p3 where p3.product_id=`product`.to_product_id ) as n_local_description ,';
            $field.='(select p4.product_spec from sc_product as p4 where p4.product_id=`product`.to_product_id ) as n_product_spec ,';
            $field.='(select p5.brand from sc_product as p5 where p5.product_id=`product`.to_product_id ) as n_brand '; //内部物料品牌            
            //临时表中的字段
            //$field.='`emp`.butxt as butxt , '; //物料品牌
            //$field.='`emp`.kostl as kostl , '; //物料规格
            //$field.='`emp`.ktext as ktext '; //物料规格           
            
            $on ='`order_goods`.order_id = `order`.order_id ,';
            $on .='`store`.store_id = `order`.store_id ,';
            $on .='`order_goods`.goods_id = `goods`.goods_id ,';
            $on .='`product`.product_id = `goods`.materiel_code ';
            //$on .='`emp`.order_sn = `order`.order_sn ';
            $model_city_centre->table('order,order_goods,store,goods,product')->field($field);
            $order_list = $model_city_centre->join('left,left')->on($on)->where($sql)->page(30)->select(); 
//            $new_list = array();
//            if(!empty($order_list) && is_array($order_list)){
//                foreach($order_list as $va){
//                    $emp_data = $model_city_centre->query("select * from sc_emp where sc_emp.order_sn = '".$va['order_sn']."'");
//                    $va['butxt'] = $emp_data['butxt'];
//                    $va['kostl'] = $emp_data['kostl'];
//                    $va['ktext'] = $emp_data['ktext'];
//                    $new_list[] = $va;
//                }
//            }
            Tpl::output('gc_list', $gc_list);
            Tpl::output('order_list',$order_list);
            Tpl::output('city_centreList',$city_centreList);    
            Tpl::output('show_page',$model_city_centre->showpage());
            Tpl::showpage('order.index.all');
        }    
      /**
	 * 导出商品表
	 *
	 */
	public function dcgoodsListOp(){

        $lang	= Language::getLangContent();
        $model_store_joinin = Model();
        $model_city_centre = Model();

        $sql="  1=1 " ;
            if(isset($_GET['order_state']) && $_GET['order_state'] != '' ){
		if(in_array($_GET['order_state'],array(ORDER_STATUS_SEND_ONE,ORDER_STATUS_SEND_HET,ORDER_STATE_SUCCESS,ORDER_STATUS_SEND_TWO,ORDER_STATUS_SUCCESS,ORDER_STATUS_ERROR,ORDER_STATUS_OUT,ORDER_STATUS_CUS_RECEIVED,ORDER_STATE_SEND,ORDER_STATE_CANCEL))){
                        $sql=$sql." and (`order`.order_state = '".$_GET['order_state']."') ";
				}
		}
             
            if($_GET['city_name']) {
                 $sql=$sql." and (butxt = '".$_GET['city_name']."') ";
            }
            //供应商名称
            if($_GET['store_company_name']) {
                 $sql=$sql." and (`store`.store_company_name like '%".$_GET['store_company_name']."%') ";
            }
            //验收时间
            $ys_if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['ys_query_start_time']);
            $ys_if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['ys_query_end_time']);
            $ys_start_unixtime = $ys_if_start_time ? strtotime($_GET['ys_query_start_time']) : null;
            $ys_end_unixtime = $ys_if_end_time ? strtotime($_GET['ys_query_end_time']): null;
                if ($ys_start_unixtime || $ys_end_unixtime) {
                $sql=$sql." and (`order`.finnshed_time  BETWEEN '".$ys_start_unixtime."' AND '".$ys_end_unixtime."') ";
                }
                //下单时间
            $xd_if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['xd_query_start_time']);
            $xd_if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['xd_query_end_time']);
            $xd_start_unixtime = $xd_if_start_time ? strtotime($_GET['xd_query_start_time']) : null;
            $xd_end_unixtime = $xd_if_end_time ? strtotime($_GET['xd_query_end_time']): null;
                if ($xd_start_unixtime || $xd_end_unixtime) {
                  $sql=$sql." and (`order`.add_time  BETWEEN '".$xd_start_unixtime."' AND '".$xd_end_unixtime."') ";  
                }
            //内部物料
               $n_gc_id = $_GET['n_class_id'];   
              //查询商品分类是否有父级ID
               if($n_gc_id){
                    $gc_class = $model_city_centre->table('goods_class')->where('gc_id='.$n_gc_id)->field('gc_parent_id')->find();
                    //如果没有有上级ID 则是一级分类
                if($gc_class['gc_parent_id'] == 0){
                        $sql=$sql." and (`product`.gc_id_1 = '".$n_gc_id."') ";
                        $sql=$sql." and (`product`.gc_id_2 = '0') ";
                        $sql=$sql." and (`product`.gc_id_3 = '0') ";
                    }else{
                        //有上级 查询这个上级ID
                        $gc_class_two = $model_city_centre->table('goods_class')->where('gc_id='.$gc_class['gc_parent_id'])->field('gc_parent_id')->find();

                        if($gc_class_two['gc_parent_id'] > 0){
                            $sql=$sql." and (`product`.gc_id_1 = '".$gc_class_two['gc_parent_id']."') ";
                            $sql=$sql." and (`product`.gc_id_2 = '".$gc_class['gc_parent_id']."') ";
                            $sql=$sql." and (`product`.gc_id_3 = '".$n_gc_id."') ";
                        }else{
                            $sql=$sql." and (`product`.gc_id_1 = '".$gc_class['gc_parent_id']."') ";
                            $sql=$sql." and (`product`.gc_id_2 = '".$n_gc_id."') ";
                            $sql=$sql." and (`product`.gc_id_3 = '0') ";
                        }

                    }   
                };
            //外部物料  
                $w_gc_id = $_GET['w_class_id'];   
             if($w_gc_id){   
              //查询商品分类是否有父级ID
                $w_gc_class = $model_city_centre->table('goods_class')->where('gc_id='.$w_gc_id)->field('gc_parent_id')->find();
                //如果没有有上级ID 则是一级分类
            if($w_gc_class['gc_parent_id'] == 0){
                    $sql=$sql." and (`product`.gc_id_1 = '".$w_gc_id."') ";
                    $sql=$sql." and (`product`.gc_id_2 = '0') ";
                    $sql=$sql." and (`product`.gc_id_3 = '0') ";
                    $sql=$sql." and (`product`.product_level = '0') ";
                }else{
                    //有上级 查询这个上级ID
                    $w_gc_class_two = $model_city_centre->table('goods_class')->where('gc_id='.$w_gc_class['gc_parent_id'])->field('gc_parent_id')->find();
                    
                    if($w_gc_class_two['gc_parent_id'] > 0){
                        $sql=$sql." and (`product`.gc_id_1 = '".$w_gc_class_two['gc_parent_id']."') ";
                        $sql=$sql." and (`product`.gc_id_2 = '".$w_gc_class['gc_parent_id']."') ";
                        $sql=$sql." and (`product`.gc_id_3 = '".$w_gc_id."') ";
                        $sql=$sql." and (`product`.product_level = '0') ";
                    }else{
                        $sql=$sql." and (`product`.gc_id_1 = '".$w_gc_class['gc_parent_id']."') ";
                        $sql=$sql." and (`product`.gc_id_2 = '".$w_gc_id."') ";
                        $sql=$sql." and (`product`.gc_id_3 = '0') ";
                        $sql=$sql." and (`product`.product_level = '0') ";
                    }
                    
                }   
             };   
            $field='`order`.order_sn as order_sn , '; //订单编号
            $field.='`order`.order_state as order_state , '; //订单状态
            $field.='`order`.add_time as add_time , '; //下单时间
            $field.='`order`.finnshed_time as finnshed_time , '; //订单完成时间
            $field.='`order`.store_name as store_name  ,'; //店铺名称
            $field.='`order`.store_name as store_name  ,'; //店铺名称
            //order_goods表中的参数
            $field.='`order_goods`.goods_price as goods_price  ,'; //单品价格
            $field.='`order_goods`.goods_num as goods_num  ,'; //数量
            $field.='`order_goods`.goods_pay_price as goods_pay_price ,'; //成交价格
            //store 表中的 member_name
            $field.='`store`.store_company_name as store_company_name , '; //供应商名称
            $field.='`store`.member_name as member_name ,'; //供应商名称
            //goods 中的外部物料编号
            $field.='`goods`.materiel_code as materiel_code ,'; //外部物料编号
            //goods_class
            $field.='(select gc_name from sc_goods_class where `goods`.gc_id_1 = sc_goods_class.gc_id) as w_gc_id_1 ,'; //外部物料大类
            $field.='(select gc_name from sc_goods_class where `goods`.gc_id_2 = sc_goods_class.gc_id) as w_gc_id_2 ,';//中类
            $field.='(select gc_name from sc_goods_class where `goods`.gc_id_3 = sc_goods_class.gc_id) as w_gc_id_3 ,';//小类            
            //materiel_code 物料表
            $field.='`product`.local_description as w_local_description , '; //物料名称
            $field.='`product`.brand as brand , '; //物料品牌
            $field.='`product`.product_spec as product_spec , '; //物料规格
            $field.='`product`.to_product_id as to_product_id  ,'; //物料规格
            //内部物料
            $field.='(select gc_name from sc_goods_class where (select p2.gc_id_1 from sc_product  as p2 where p2.product_id=`product`.to_product_id ) = sc_goods_class.gc_id) as n_gc_id_1 ,'; //内部物料大类
            $field.='(select gc_name from sc_goods_class where (select p2.gc_id_2 from sc_product  as p2 where p2.product_id=`product`.to_product_id ) = sc_goods_class.gc_id) as n_gc_id_2 ,'; //内部物料中类
            $field.='(select gc_name from sc_goods_class where (select p2.gc_id_3 from sc_product  as p2 where p2.product_id=`product`.to_product_id ) = sc_goods_class.gc_id) as n_gc_id_3 ,'; //内部物料小类
            $field.='(select p3.local_description from sc_product as p3 where p3.product_id=`product`.to_product_id ) as n_local_description ,';
            $field.='(select p4.product_spec from sc_product as p4 where p4.product_id=`product`.to_product_id ) as n_product_spec ,';
            $field.='(select p5.brand from sc_product as p5 where p5.product_id=`product`.to_product_id ) as n_brand '; //内部物料品牌            
            //临时表中的字段
            //$field.='`emp`.butxt as butxt , '; //物料品牌
            //$field.='`emp`.kostl as kostl , '; //物料规格
            //$field.='`emp`.ktext as ktext '; //物料规格           
            
            $on ='`order_goods`.order_id = `order`.order_id ,';
            $on .='`store`.store_id = `order`.store_id ,';
            $on .='`order_goods`.goods_id = `goods`.goods_id ,';
            $on .='`product`.product_id = `goods`.materiel_code ';
            //$on .='`emp`.order_sn = `order`.order_sn ';
            $model_city_centre->table('order,order_goods,store,goods,product')->field($field);
//            $order_list = $model_city_centre->join('left,left')->on($on)->where($sql)->page(30)->select();  
             
             
             
             
            if (!is_numeric($_GET['curpage'])){
                    //先统计order的表总数进行判断
                    $model_city_centre->table('order,order_goods,store,goods,product')->field($field);
                    $count = $model_city_centre->join('left,left')->on($on)->where($sql)->count();
                
			$array = array();
			if ($count > self::EXPORT_SIZE ){	//显示下载链接
				$page = ceil($count/self::EXPORT_SIZE);
				for ($i=1;$i<=$page;$i++){
					$limit1 = ($i-1)*self::EXPORT_SIZE + 1;
					$limit2 = $i*self::EXPORT_SIZE > $count ? $count : $i*self::EXPORT_SIZE;
					$array[$i] = $limit1.' ~ '.$limit2 ;
				}
				Tpl::output('list',$array);
				Tpl::output('murl','index.php?act=order&op=index');
				Tpl::showpage('export.excel');
			}else{	//如果数量小，直接下载
                                $model_city_centre->table('order,order_goods,store,goods,product')->field($field);
                                $order_list = $model_city_centre->join('left,left')->on($on)->where($sql)->limit(self::EXPORT_SIZE)->select();                     
                                $this->scgoodsExcel($order_list);
			}
		}else{	//下载
			$limit1 = ($_GET['curpage']-1) * self::EXPORT_SIZE;
			$limit2 = self::EXPORT_SIZE;
                        $model_city_centre->table('order,order_goods,store,goods,product')->field($field);
                        $order_list = $model_city_centre->join('left,left')->on($on)->where($sql)->order('`order`.order_sn desc')->limit("{$limit1},{$limit2}")->select();
			$this->scgoodsExcel($order_list);
		}
		
	}

	/**
	 * 生成商品excel
	 *
	 * @param array $data
	 */
	private function scgoodsExcel($data = array()){
                $model = Model();
		Language::read('export');
		import('libraries.excel');
		$excel_obj = new Excel();
		$excel_data = array();
		//设置样式
		$excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
                
               
		//header
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_order_sn'));//订单编号
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_order_state'));//订单状态
//		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_ktext'));//项目名称	
//                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_kostl'));//项目编码	
//                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_butxt'));//城市公司
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_add_time'));   //   下单日期 
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_finnshed_time'));   //   完成日期
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_store_company_name')); //供应商名称
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_store_name'));//店铺名称	
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_member_name'));  //供应商账号             
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_materiel_code'));   //外部物料编号          
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_w_gc_id_1')); //  外部物料大类
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_w_gc_id_2')); //  外部物料中类	
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_sw_gc_id_3')); //  	外部物料小类
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_w_local_description')); //  外部物料物料名称
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_brand')); //  外部物料品牌
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_product_spec')); // 	外部物料规格
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_to_product_id')); //  内部物料编码
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_n_gc_id_1')); //  内部物料大类
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_n_gc_id_2')); //  	内部物料中类
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_n_gc_id_3')); // 内部物料小类
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bbn_local_description')); //  内部物料物料名称	
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_n_brand')); // 内部物料品牌
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_n_product_spec')); //  	内部物料规格
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_goods_price')); //  采购单价
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_goods_num')); //  数量
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_bb_goods_pay_price')); //  金额

		foreach ((array)$data as $k=>$v){
			$tmp = array();
			$tmp[] = array('data'=>$v['order_sn']);
                        //订单状态转换
                        if($v['order_state']==ORDER_STATUS_SEND_ONE){
                            $order_stat='待审核';
                        };
                        if($v['order_state']==ORDER_STATUS_SEND_TWO){
                            $order_stat='审核中';
                        };
                        if($v['order_state']==ORDER_STATUS_SUCCESS){
                            $order_stat='通过审核';
                        };
                        if($v['order_state']==ORDER_STATUS_ERROR){
                            $order_stat='审核回退';
                        };
                        if($v['order_state']==ORDER_STATUS_OUT){
                            $order_stat='审核拒绝';
                        };
                        if($v['order_state']==ORDER_STATUS_CUS_RECEIVED){
                            $order_stat='已检收';
                        };
                        if($v['order_state']==ORDER_STATUS_SEND_HET){
                            $order_stat='待检收';
                        };
                        if($v['order_state']==ORDER_STATE_SEND){
                            $order_stat='待收货';
                        };
                        if($v['order_state']==ORDER_STATE_SUCCESS){
                            $order_stat='交易完成';
                        };
			$tmp[] = array('data'=>$order_stat);
//			$tmp[] = array('data'=>$v['ktext']);
//                        $tmp[] = array('data'=>$v['kostl']);
//                        $tmp[] = array('data'=>$v['butxt']);                      
			$tmp[] = array('data'=>date('Y-m-d H:i:s',$v['add_time']));
                        $tmp[] = array('data'=>date('Y-m-d H:i:s',$v['finnshed_time']));
                        $tmp[] = array('data'=>$v['store_company_name']);
			$tmp[] = array('data'=>$v['store_name']);
			$tmp[] = array('data'=>$v['member_name']);
                        $tmp[] = array('data'=>$v['materiel_code']);
                        $tmp[] = array('data'=>$v['w_gc_id_1']);
                        $tmp[] = array('data'=>$v['w_gc_id_2']); 
                        $tmp[] = array('data'=>$v['w_gc_id_3']); 
                        $tmp[] = array('data'=>$v['w_local_description']);  
                        $tmp[] = array('data'=>$v['brand']);
			$tmp[] = array('data'=>$v['product_spec']);
			$tmp[] = array('data'=>$v['to_product_id']);
                        $tmp[] = array('data'=>$v['n_gc_id_1']);
                        $tmp[] = array('data'=>$v['n_gc_id_2']);
                        $tmp[] = array('data'=>$v['n_gc_id_3']); 
                        $tmp[] = array('data'=>$v['n_local_description']); 
                        $tmp[] = array('data'=>$v['n_brand']);  
                        $tmp[] = array('data'=>$v['n_product_spec']);  
                        $tmp[] = array('data'=>$v['goods_price']); 
                        $tmp[] = array('data'=>$v['goods_num']); 
                        $tmp[] = array('data'=>$v['goods_pay_price']);  
			$excel_data[] = $tmp;
		}    
               
		$excel_data = $excel_obj->charset($excel_data,CHARSET);
                
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset(L('exp_od_order'),CHARSET));
		$excel_obj->generateXML($excel_obj->charset(L('exp_od_order'),CHARSET).$_GET['curpage'].'-'.date('Y-m-d-H',time()));
	}  
        
        
        
        
        
        public function testemailOp(){
//            $email =new MyPHPMailer();
             $model = Model('stat');
        //查询最后统计的记录
        $latest_record = $model->getOneStatmember(array(), '', 'statm_id desc');
        $stime = 0;
        if ($latest_record){
            $start_time = strtotime(date('Y-m-d',$latest_record['statm_updatetime']));
        } else {
            $start_time = strtotime(date('Y-m-d',strtotime(C('setup_date'))));//从系统的安装时间开始统计
        }
        $j = 1;
        for ($stime = $start_time; $stime < time(); $stime = $stime+86400){
            //数据库更新数据数组
            $insert_arr = array();
            $update_arr = array();
    
            $etime = $stime + 86400 - 1;
            //避免重复统计，开始时间必须大于最后一条记录的记录时间
            $search_stime = $latest_record['statm_updatetime'] > $stime?$latest_record['statm_updatetime']:$stime;
            //统计一天的数据，如果结束时间大于当前时间，则结束时间为当前时间，避免因为查询时间的延迟造成数据遗落
            $search_etime = ($t = ($stime + 86400 - 1)) > time() ? time() : ($stime + 86400 - 1);
    
            //统计订单下单量和下单金额
            $field = ' order.order_id,add_time,buyer_id,buyer_name,order_amount';
            $where = array();
            $where['order.order_state'] = array('neq',ORDER_STATE_NEW);//去除未支付订单
            $where['order.refund_state'] = array('exp',"!(order.order_state = '".ORDER_STATE_CANCEL."' and order.refund_state = 0)");//没有参与退款的取消订单，不记录到统计中
            $where['order_log.log_time'] = array('between',array($search_stime,$search_etime));//按照订单付款的操作时间统计
            //货到付款当交易成功进入统计，非货到付款当付款后进入统计
            $where['payment_code'] = array('exp',"(order.payment_code='offline' and order_log.log_orderstate = '".ORDER_STATE_SUCCESS."') or (order.payment_code<>'offline' and order_log.log_orderstate = '".ORDER_STATE_PAY."' )");
            $orderlist_tmp = $model->statByOrderLog($where, $field, 0, 0, 'order_id');//此处由于底层的限制，仅能查询1000条，如果日下单量大于1000，则需要limit的支持
    
            $order_list = array();
            $orderid_list = array();
            foreach ((array)$orderlist_tmp as $k=>$v){
                $addtime = strtotime(date('Y-m-d',$v['add_time']));
                if ($addtime != $stime){//订单如果隔天支付的话，需要进行统计数据更新
                    $update_arr[$addtime][$v['buyer_id']]['statm_membername'] = $v['buyer_name'];
                    $update_arr[$addtime][$v['buyer_id']]['statm_ordernum'] = intval($update_arr[$addtime][$v['buyer_id']]['statm_ordernum'])+1;
                    $update_arr[$addtime][$v['buyer_id']]['statm_orderamount'] = floatval($update_arr[$addtime][$v['buyer_id']]['statm_orderamount']) + (($t = floatval($v['order_amount'])) > 0?$t:0);
                } else {
                    $order_list[$v['buyer_id']]['buyer_name'] = $v['buyer_name'];
                    $order_list[$v['buyer_id']]['ordernum'] = intval($order_list[$v['buyer_id']]['ordernum']) + 1;
                    $order_list[$v['buyer_id']]['orderamount'] = floatval($order_list[$v['buyer_id']]['orderamount']) + (($t = floatval($v['order_amount'])) > 0?$t:0);
                }
                //记录订单ID数组
                $orderid_list[] = $v['order_id'];
            }
    
            //统计下单商品件数
            if ($orderid_list){
                $field = ' add_time,order.buyer_id,order.buyer_name,goods_num ';
                $where = array();
                $where['order.order_id'] = array('in',$orderid_list);
                $ordergoods_tmp = $model->statByOrderGoods($where, $field, 0, 0, 'order.order_id');
                $ordergoods_list = array();
                foreach ((array)$ordergoods_tmp as $k=>$v){
                    $addtime = strtotime(date('Y-m-d',$v['add_time']));
                    if ($addtime != $stime){//订单如果隔天支付的话，需要进行统计数据更新
                        $update_arr[$addtime][$v['buyer_id']]['statm_goodsnum'] = intval($update_arr[$addtime][$v['buyer_id']]['statm_goodsnum']) + (($t = floatval($v['goods_num'])) > 0?$t:0);
                    } else {
                        $ordergoods_list[$v['buyer_id']]['goodsnum'] = $ordergoods_list[$v['buyer_id']]['goodsnum'] + (($t = floatval($v['goods_num'])) > 0?$t:0);
                    }
                }
            }
    
            //统计的预存款记录
            $field = ' lg_member_id,lg_member_name,SUM(IF(lg_av_amount>=0,lg_av_amount,0)) as predincrease, SUM(IF(lg_av_amount<=0,lg_av_amount,0)) as predreduce ';
            $where = array();
            $where['lg_add_time'] = array('between',array($stime,$etime));
            $predeposit_tmp = $model->getPredepositInfo($where, $field, 0, 'lg_member_id', 0, 'lg_member_id');
            $predeposit_list = array();
            foreach ((array)$predeposit_tmp as $k=>$v){
                $predeposit_list[$v['lg_member_id']] = $v;
            }
    
            //统计的积分记录
            $field = ' pl_memberid,pl_membername,SUM(IF(pl_points>=0,pl_points,0)) as pointsincrease, SUM(IF(pl_points<=0,pl_points,0)) as pointsreduce ';
            $where = array();
            $where['pl_addtime'] = array('between',array($stime,$etime));
            $points_tmp = $model->statByPointslog($where, $field, 0, 0, '', 'pl_memberid');
            $points_list = array();
            foreach ((array)$points_tmp as $k=>$v){
                $points_list[$v['pl_memberid']] = $v;
            }
    
            //处理需要更新的数据
            foreach ((array)$update_arr as $k=>$v){
                foreach ($v as $m_k=>$m_v){
                    //查询记录是否存在
                    $statmember_info = $model->getOneStatmember(array('statm_time'=>$k,'statm_memberid'=>$m_k));
                    if ($statmember_info){
                        $m_v['statm_ordernum'] = intval($statmember_info['statm_ordernum']) + $m_v['statm_ordernum'];
                        $m_v['statm_orderamount'] = floatval($statmember_info['statm_ordernum']) + $m_v['statm_orderamount'];
                        $m_v['statm_updatetime'] = $search_etime;
                        $model->updateStatmember(array('statm_time'=>$k,'statm_memberid'=>$m_k),$m_v);
                    } else {
                        $tmp = array();
                        $tmp['statm_memberid'] = $m_k;
                        $tmp['statm_membername'] = $m_v['statm_membername'];
                        $tmp['statm_time'] = $k;
                        $tmp['statm_updatetime'] = $search_etime;
                        $tmp['statm_ordernum'] = ($t = intval($m_v['statm_ordernum'])) > 0?$t:0;
                        $tmp['statm_orderamount'] = ($t = floatval($m_v['statm_orderamount']))>0?$t:0;
                        $tmp['statm_goodsnum'] = ($t = intval($m_v['statm_goodsnum']))?$t:0;
                        $tmp['statm_predincrease'] = 0;
                        $tmp['statm_predreduce'] = 0;
                        $tmp['statm_pointsincrease'] = 0;
                        $tmp['statm_pointsreduce'] = 0;
                        $insert_arr[] = $tmp;
                    }
                    unset($statmember_info);
                }
            }
    
            //处理获得所有会员ID数组
            $memberidarr_order = $order_list?array_keys($order_list):array();
            $memberidarr_ordergoods = $ordergoods_list?array_keys($ordergoods_list):array();
            $memberidarr_predeposit = $predeposit_list?array_keys($predeposit_list):array();
            $memberidarr_points = $points_list?array_keys($points_list):array();
            $memberid_arr = array_merge($memberidarr_order,$memberidarr_ordergoods,$memberidarr_predeposit,$memberidarr_points);
            //查询会员信息
            $memberid_list = Model('member')->getMemberList(array('member_id'=>array('in',$memberid_arr)), '', 0);
            //查询记录是否存在
            $statmemberlist_tmp = $model->statByStatmember(array('statm_time'=>$stime));
            $statmemberlist = array();
            foreach ((array)$statmemberlist_tmp as $k=>$v){
                $statmemberlist[$v['statm_memberid']] = $v;
            }
            foreach ((array)$memberid_list as $k=>$v){
                $tmp = array();
                $tmp['statm_memberid'] = $v['member_id'];
                $tmp['statm_membername'] = $v['member_name'];
                $tmp['statm_time'] = $stime;
                $tmp['statm_updatetime'] = $search_etime;
                //因为记录可能已经存在，所以加上之前的统计记录
                $tmp['statm_ordernum'] = intval($statmemberlist[$tmp['statm_memberid']]['statm_ordernum']) + (($t = intval($order_list[$tmp['statm_memberid']]['ordernum'])) > 0?$t:0);
                $tmp['statm_orderamount'] = floatval($statmemberlist[$tmp['statm_memberid']]['statm_orderamount']) + (($t = floatval($order_list[$tmp['statm_memberid']]['orderamount']))>0?$t:0);
                $tmp['statm_goodsnum'] = intval($statmemberlist[$tmp['statm_memberid']]['statm_goodsnum']) + (($t = intval($ordergoods_list[$tmp['statm_memberid']]['goodsnum']))?$t:0);
                $tmp['statm_predincrease'] = (($t = floatval($predeposit_list[$tmp['statm_memberid']]['predincrease']))?$t:0);
                $tmp['statm_predreduce'] = (($t = floatval($predeposit_list[$tmp['statm_memberid']]['predreduce']))?$t:0);
                $tmp['statm_pointsincrease'] = (($t = intval($points_list[$tmp['statm_memberid']]['pointsincrease']))?$t:0);
                $tmp['statm_pointsreduce'] = (($t = intval($points_list[$tmp['statm_memberid']]['pointsreduce']))?$t:0);
                $insert_arr[] = $tmp;
            }
            //删除旧的统计数据
            $model->delByStatmember(array('statm_time'=>$stime));
            $model->table('stat_member')->insertAll($insert_arr);
        }
        }
        
        
        public function testemail1Op(){

include '../core/framework/libraries/mail/PHPMailerAutoload.php';
include '../core/framework/libraries/mail/class.phpmailer.php';
include '../core/framework/libraries/mail/class.pop3.php';
include '../core/framework/libraries/mail/class.smtp.php';


$mail = new PHPMailer;
$mail->SMTPDebug = 3;                               // Enable verbose debug output
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'appmail.vanke.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = "s-vkwycgpt";             // SMTP username
$mail->Password = "26npGYPU";                       // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 25;                                    // TCP port to connect to
$mail->setFrom('s-vkwycgpt@vanke.com', 's-vkwycgpt');
$mail->addAddress('669333576@qq.com');     // Add a recipient
$mail->addReplyTo('s-vkwycgpt@vanke.com', 's-vkwycgpt');
$mail->Subject = '主题';
$mail->Body    = '消息';
$mail->CharSet = "UTF-8"; 
$mail->Encoding = "base64";
if(!$mail->send()) {
    echo '失败';
} else {
    echo '成功';
}
        }
}
