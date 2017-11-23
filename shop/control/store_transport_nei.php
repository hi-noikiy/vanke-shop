<?php
header("Content-Type:text/html;charset=utf-8");
/**
 * 卖家实物订单管理
 *
 *
 *
 ***/



class store_transport_neiControl extends BaseSellerControl {
    
    const EXPORT_SIZE = 1000;
    
    public function __construct() {
        parent::__construct();
        Language::read('member_store_index');
    }

	/**
	 * 订单列表
	 *
	 */
	public function indexOp() {
        $model_order = Model('order');
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        if ($_GET['order_sn'] != '') {
            $condition['order_sn'] = $_GET['order_sn'];
        }
        if ($_GET['buyer_name'] != '') {
            $condition['buyer_name'] = $_GET['buyer_name'];
        }
        $allow_state_array = array('state_new','state_pay','state_send','state_success','state_cancel');
        if ((int)$_GET['state_type']) {
            if($_GET['state_type'] == 50){
                $condition['order_state'] = array('in','31,33,40');
            }else{
                $condition['order_state'] = htmlspecialchars($_GET['state_type']);
            }
            
        } else {
            $_GET['state_type'] = 'store_order';
        }
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_date ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_date ? strtotime($_GET['query_end_date']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
        }

        if ($_GET['skip_off'] == 1) {
            $condition['order_state'] = array('neq',ORDER_STATE_CANCEL);
        }
        $condition['order_lei'] = 2;
        $order_list = $model_order->getOrderList($condition, 20, '*', 'order_id desc','', array('order_goods','order_common','member'));
      
        //页面中显示那些操作
        foreach ($order_list as $key => $order_info) {

        	//显示取消订单
        	$order_info['if_cancel'] = $model_order->getOrderOperateState('store_cancel',$order_info);

        	//显示调整运费
        	$order_info['if_modify_price'] = $model_order->getOrderOperateState('modify_price',$order_info);
			
		//显示修改价格
        	$order_info['if_spay_price'] = $model_order->getOrderOperateState('spay_price',$order_info);

        	//显示发货
        	$order_info['if_send'] = $model_order->getOrderOperateState('send',$order_info);

        	//显示锁定中
        	$order_info['if_lock'] = $model_order->getOrderOperateState('lock',$order_info);

        	//显示物流跟踪
        	$order_info['if_deliver'] = $model_order->getOrderOperateState('deliver',$order_info);
               
               
               
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
        self::profile_menu('list',$_GET['state_type']);

        Tpl::showpage('store_transport.index');
	}

	/**
	 * 卖家订单详情
	 *
	 */
	public function show_orderOp() {
		Language::read('member_member_index');
	    $order_id = intval($_GET['order_id']);
	    if ($order_id <= 0) {
	        showMessage(Language::get('wrong_argument'),'','html','error');
	    }
	    $model_order = Model('order');
	    $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
	    $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods','member'));
	    if (empty($order_info)) {
	        showMessage(Language::get('store_order_none_exist'),'','html','error');
	    }

        $model_refund_return = Model('refund_return');
        $order_list = array();
        $order_list[$order_id] = $order_info;
        $order_list = $model_refund_return->getGoodsRefundList($order_list,1);//订单商品的退款退货显示
        $order_info = $order_list[$order_id];
        $refund_all = $order_info['refund_list'][0];
        if (!empty($refund_all) && $refund_all['seller_state'] < 3) {//订单全部退款商家审核状态:1为待审核,2为同意,3为不同意
            Tpl::output('refund_all',$refund_all);
        }

        //显示锁定中
        $order_info['if_lock'] = $model_order->getOrderOperateState('lock',$order_info);

    	//显示调整运费
    	$order_info['if_modify_price'] = $model_order->getOrderOperateState('modify_price',$order_info);
		
		//显示调整价格
    	$order_info['if_spay_price'] = $model_order->getOrderOperateState('spay_price',$order_info);

        //显示取消订单
        $order_info['if_cancel'] = $model_order->getOrderOperateState('buyer_cancel',$order_info);

    	//显示发货
    	$order_info['if_send'] = $model_order->getOrderOperateState('send',$order_info);

        //显示物流跟踪
        $order_info['if_deliver'] = $model_order->getOrderOperateState('deliver',$order_info);

        //显示系统自动取消订单日期
        if ($order_info['order_state'] == ORDER_STATE_NEW) {
            //$order_info['order_cancel_day'] = $order_info['add_time'] + ORDER_AUTO_CANCEL_DAY * 24 * 3600;
			// by abc.com
			$order_info['order_cancel_day'] = $order_info['add_time'] + ORDER_AUTO_CANCEL_DAY + 3 * 24 * 3600;
        }

        //显示快递信息
        if ($order_info['shipping_code'] != '') {
            $express = rkcache('express',true);
            $order_info['express_info']['e_code'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_code'];
            $order_info['express_info']['e_name'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_name'];
            $order_info['express_info']['e_url'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_url'];
        }

        //显示系统自动收获时间
        if ($order_info['order_state'] == ORDER_STATUS_SEND_HET) {
            //$order_info['order_confirm_day'] = $order_info['delay_time'] + ORDER_AUTO_RECEIVE_DAY * 24 * 3600;
			//by abc.com
			//获取发出货物的时间
			$order_info['order_confirm_day'] = $order_info['delay_time'] + (ORDER_AUTO_RECEIVE_DAY + 15) * 24 * 3600;
        }

        //如果订单已取消，取得取消原因、时间，操作人
        if ($order_info['order_state'] == ORDER_STATE_CANCEL) {
            $order_info['close_info'] = $model_order->getOrderLogInfo(array('order_id'=>$order_info['order_id']),'log_id desc');
        }
        
        //获取取消订单操作记录
        if($order_info['order_state'] == ORDER_WAIT_CANCEL){
            $order_info['clence_info'] = $model_order->getOrderLogInfo(array('order_id'=>$order_info['order_id'],'log_orderstate'=>ORDER_WAIT_CANCEL),'log_time desc');
        }
        
        //获取受理订单操作记录
        if($order_info['order_state'] >= ORDER_STATE_SEND){
            $order_info['acceptance_info'] = $model_order->getOrderLogInfo(array('order_id'=>$order_info['order_id'],'log_orderstate'=>ORDER_DELIVER_GOODS),'log_time desc');
        }

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
        
        if (empty($order_info['zengpin_list'])) {
            $order_info['goods_count'] = count($order_info['goods_list']);
        } else {
            $order_info['goods_count'] = count($order_info['goods_list']) + 1;
        }
	    Tpl::output('order_info',$order_info);

        //发货信息
        if (!empty($order_info['extend_order_common']['daddress_id'])) {
            $daddress_info = Model('daddress')->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
            Tpl::output('daddress_info',$daddress_info);
        }

		Tpl::showpage('store_inorder.show');
	}

	/**
	 * 卖家订单状态操作
	 *
	 */
	public function change_stateOp() {
          
		$state_type	= $_GET['state_type'];
		$order_id	= intval($_GET['order_id']);

		$model_order = Model('order');
		$condition = array();
		$condition['order_id'] = $order_id;
		//$condition['store_id'] = $_SESSION['store_id'];
		$order_info	= $model_order->getOrderInfo($condition);

		if ($_GET['state_type'] == 'order_cancel') {
		    $result = $this->_order_cancel($order_info,$_POST);
		} elseif ($_GET['state_type'] == 'modify_price') {
		    $result = $this->_order_ship_price($order_info,$_POST);
		} elseif ($_GET['state_type'] == 'spay_price') {
			$result = $this->_order_spay_price($order_info,$_POST);
    	} elseif ($_GET['state_type'] == 'accept') {
			$result = $this->_order_accept($order_info,$_POST);
    	}
        if (!$result['state']) {
            showDialog($result['msg'],'','error',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
        } else {
            showDialog($result['msg'],'reload','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
        }
	}
	
	/**
	 * 订单受理
	 * @param unknown $order_info
	 */
	private function _order_accept($order_info, $post) {
          
	    $model_order = Model('order');
	    $logic_order = Logic('order');
           
	    if(!chksubmit()) {
            Tpl::output('order_info',$order_info);
            Tpl::output('order_id',$order_info['order_id']);
            Tpl::showpage('store_order.accept','null_layout');
            exit();
	     } else {
    	    $if_allow = $model_order->getOrderOperateState('order_accept',$order_info);
    	    if (!$if_allow) {
    	        return callback(false,'无权操作');
    	    }
    	    return $logic_order->changeOrderAccept($order_info,'seller',$_SESSION['member_name']);
	     }
	}

	/**
	 * 取消订单
	 * @param unknown $order_info
	 */
	private function _order_cancel($order_info, $post) {
	    $model_order = Model('order');
	    $logic_order = Logic('order');

	    if(!chksubmit()) {
            Tpl::output('order_info',$order_info);
            Tpl::output('order_id',$order_info['order_id']);
            Tpl::showpage('store_order.cancel','null_layout');
            exit();
	     } else {
	         $if_allow = $model_order->getOrderOperateState('store_cancel',$order_info);
	         if (!$if_allow) {
	             return callback(false,'无权操作');
	         }
	         $msg = $post['state_info1'] != '' ? $post['state_info1'] : $post['state_info'];
	         return $logic_order->changeOrderStateCancel($order_info,'seller',$_SESSION['member_name'], $msg);
	     }
	}
/**
	 * 供应商拒绝取消订单
	 * @param unknown $order_info
	 */
	public function refuseOrderOp() {
            
            $order_id	= empty($_GET['order_id'])? intval($_POST['order_id']) : intval($_GET['order_id']) ;
            $model_order = Model('order');
            $condition = array();
            $condition['order_id'] = $order_id;
            $order_info	= $model_order->getOrderInfo($condition);
            $model_order_login = Model();


	    if(!chksubmit()) {
             $data_login=array(
                   "order_id"=>$order_id,  
                   "log_user"=>$order_info['buyer_name'],
                   "log_role"=>"买家",
                    "log_orderstate"=>1
                 );
            $order_login=$model_order_login->table('order_log')->where($data_login)->find(); 
            Tpl::output('order_login',$order_login['log_msg']);
            Tpl::output('order_info',$order_info);
            Tpl::output('order_id',$order_info['order_id']);
            Tpl::showpage('store_order.cancel_gys','null_layout');
            exit();
	     } else {
                 //恢复状态
                 $data_login=array(
                   "order_id"=>$order_id,  
                   "log_user"=>$order_info['buyer_name'],
                   "log_role"=>"买家",
                   "log_orderstate"=>1
                 );
                 $order_login=$model_order_login->table('order_log')->where($data_login)->find();
                 
                 $original_state=empty($order_login['original_state']) ? 14 :$order_login['original_state'];
                 $results=$model_order_login->table('order')->where(array("order_id"=>$order_id))->update(array("order_state"=>$original_state));
                 //插入日志
                $msg = $_POST['state_info1'] != '' ? $_POST['state_info1'] : $_POST['state_info'];
                $data = array();
                $data['order_id'] = $order_id;
                $data['log_role'] = 'seller';//操作角色
                $data['log_msg'] = '取消了订单';
                $data['log_user'] = $_SESSION['member_name'];//操作人
                if ($msg) {
                    $data['log_msg'] .= ' ( '.$msg.' )';
                }
                $data['log_orderstate'] = ORDER_STATE_CANCEL;
                $result=$model_order->addOrderLog($data);
                
                
               if (empty($results) || empty($result)) {
                     showDialog("操作失败",'','error');
                } else {
                    showDialog("操作成功",'reload','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
                }
                
                
                
                
                
	     }
	}
	/**
	 * 修改运费
	 * @param unknown $order_info
	 */
	private function _order_ship_price($order_info, $post) {
	    $model_order = Model('order');
	    $logic_order = Logic('order');
	    if(!chksubmit()) {
	        Tpl::output('order_info',$order_info);
	        Tpl::output('order_id',$order_info['order_id']);
            Tpl::showpage('store_order.edit_price','null_layout');
            exit();
        } else {
            $if_allow = $model_order->getOrderOperateState('modify_price',$order_info);
            if (!$if_allow) {
                return callback(false,'无权操作');
            }
            return $logic_order->changeOrderShipPrice($order_info,'seller',$_SESSION['member_name'],$post['shipping_fee']);           
        }

	}
	/**
	 * 修改会员价
	 * @param unknown $order_info
	 */
	private function _order_spay_price($order_info, $post) {
        $model_order = Model('order');
	    $logic_order = Logic('order');
	    if(!chksubmit()) {
	        Tpl::output('order_info',$order_info);
	        Tpl::output('order_id',$order_info['order_id']);
            Tpl::showpage('store_order.edit_spay_price','null_layout');
            exit();
        } else {
            $if_allow = $model_order->getOrderOperateState('spay_price',$order_info);
            if (!$if_allow) {
                return callback(false,'无权操作');
            }
            return $logic_order->changeOrderSpayPrice($order_info,'seller',$_SESSION['member_name'],$post['goods_amount']); 
	    }
	}


	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return
     */
    private function profile_menu($menu_type='',$menu_key='') {
        Language::read('member_layout');
        switch ($menu_type) {
        	case 'list':
            $menu_array = array(
            array('menu_key'=>'store_order',		'menu_name'=>Language::get('nc_member_path_all_order'),	'menu_url'=>'index.php?act=store_transport_nei'),
            array('menu_key'=>ORDER_STATUS_SEND_ONE,			'menu_name'=>"待审核",	'menu_url'=>'index.php?act=store_transport_nei&op=index&state_type='.ORDER_STATUS_SEND_ONE),
            array('menu_key'=>ORDER_STATUS_SEND_TWO,	        'menu_name'=>"审核中",	'menu_url'=>'index.php?act=store_transport_nei&op=store_order&state_type='.ORDER_STATUS_SEND_TWO),
            array('menu_key'=>ORDER_STATUS_SUCCESS,		       'menu_name'=>"待受理",	    'menu_url'=>'index.php?act=store_transport_nei&op=index&state_type='.ORDER_STATUS_SUCCESS),
            array('menu_key'=>ORDER_STATUS_OUT,		           'menu_name'=>"审核不通过",	'menu_url'=>'index.php?act=store_transport_nei&op=index&state_type='.ORDER_STATUS_OUT),
            array('menu_key'=>ORDER_DELIVER_GOODS,		       'menu_name'=>"待发货",	    'menu_url'=>'index.php?act=store_transport_nei&op=index&state_type='.ORDER_DELIVER_GOODS),
            array('menu_key'=>ORDER_STATE_DELEVER_SEND,		   'menu_name'=>"已发货",	    'menu_url'=>'index.php?act=store_transport_nei&op=index&state_type='.ORDER_STATE_DELEVER_SEND),
            array('menu_key'=>ORDER_WAIT_CANCEL,		      'menu_name'=>"待取消",	    'menu_url'=>'index.php?act=store_transport_nei&op=index&state_type='.ORDER_WAIT_CANCEL),
            );
            break;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
 
    
    /**
	 * 导出采购订单
	 *
	 */
	public function Purchaseexport_step1Op(){
            
 
            
        $lang	= Language::getLangContent();
        $model_order = Model('order');
        $model_orders=Model();
        $sql="  1=1 " ;
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $sql=$sql." and (`order`.store_id = '".$_SESSION['store_id']."') ";
        if ($_GET['order_sn'] != '') {
            $condition['order_sn'] = $_GET['order_sn'];
            $sql=$sql." and (`order`.order_sn = '".$_GET['order_sn']."') ";
        }
        if ($_GET['buyer_name'] != '') {
            $condition['buyer_name'] = $_GET['buyer_name'];
             $sql=$sql." and (`order`.buyer_name = '".$_GET['buyer_name']."') ";
        }
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_date ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_date ? strtotime($_GET['query_end_date']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
            $sql=$sql." and (( add_time BETWEEN ".$start_unixtime." AND ".$end_unixtime."))";
        }
        $condition['order_lei'] = 2;
        $sql=$sql." and (`order`.order_lei = 2 ) ";
        $order_list = $model_order->table('order')->getOrderList($condition, 20, '*', 'order_id desc','', array('order_goods','order_common','member'));
        if (!is_numeric($_GET['curpage'])){
                
                                    $field = '`order`.*,`member`.member_truename,`order_common`.invoice_info';
	                            $on = '`order`.buyer_name= `member`.member_name ,`order_common`.order_id=`order`.order_id';
                                    $model_orders->table('order,member,order_common')->field($field);
                                    $data = $model_orders->join('left,left')->on($on)->where($sql)->limit(self::EXPORT_SIZE)->select();                                      
                                    $this->PurchasecreateExcel($data);
			
		}
	}

	/**
	 * 生成excel
	 *
	 * @param array $data
	 */
	private function PurchasecreateExcel($data = array()){
          
		Language::read('member_store_index');
		import('libraries.excel');
		$excel_obj = new Excel();
		$excel_data = array();
		//设置样式
		$excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
		//header
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_order_nb'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_store_name'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_order_amount'));
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_buyer_name'));
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_invoice_info'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_order_time'));                 
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_order_state'));

		foreach ((array)$data as $k=>$v){
			$tmp = array();
			$tmp[] = array('data'=>$v['order_sn']);
			$tmp[] = array('data'=>$v['store_name']);
			$tmp[] = array('data'=>$v['order_amount']);
                        $a=str_replace("<br>","",implode(unserialize($v['invoice_info'])));
                        $tmp[] = array('data'=>$v['member_truename']);
                        $tmp[] = array('data'=>$a);
			$tmp[] = array('data'=>date('Y-m-d H:i:s',$v['add_time']));
			$tmp[] = array('data'=>orderState($v));
			$excel_data[] = $tmp;
		}    
		$excel_data = $excel_obj->charset($excel_data,CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset(L('exp_order_name'),CHARSET));
		$excel_obj->generateXML($excel_obj->charset(L('exp_order_name'),CHARSET).'-'.date('Y-m-d-H',time()));
	}
        
        
        
         /**
	 * 导出订单商品
	 *
	 */
	public function goodsOp(){
        $model_orders=Model();
        if (!empty($_GET['order_sn'])){
            $field = '`order_goods`.goods_name,`order_goods`.goods_price,'
                     . '`order_goods`.goods_num,`order_goods`.goods_pay_price ,'
                     . '`order`.order_sn ';
	    $on = '`order`.order_id= `order_goods`.order_id';
            $model_orders->table('order,order_goods')->field($field);
            $data = $model_orders->join('left,left')->on($on)->where(array("order_sn"=>$_GET['order_sn']))->order('goods_name desc')->select();                                      
            $this->goodsExcel($data);
			
		}
	}

	/**
	 * 生成采购订单excel
	 *
	 * @param array $data
	 */
	private function goodsExcel($data = array()){
          
		Language::read('member_store_index');
		import('libraries.excel');
		$excel_obj = new Excel();
		$excel_data = array();
		//设置样式
		$excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
		//header
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_order_sn'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_goods_name'));
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_goods_price'));
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_goods_num'));
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_goods_pay_price'));

		foreach ((array)$data as $k=>$v){
			$tmp = array();
			$tmp[] = array('data'=>$v['order_sn']);
			$tmp[] = array('data'=>$v['goods_name']);
			$tmp[] = array('data'=>$v['goods_price']);
                        $tmp[] = array('data'=>$v['goods_num']);
			$tmp[] = array('data'=>$v['goods_pay_price']);
			$excel_data[] = $tmp;
		}    
		$excel_data = $excel_obj->charset($excel_data,CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset(L('exp_order_name'),CHARSET));
		$excel_obj->generateXML($excel_obj->charset(L('exp_order_name'),CHARSET).'-'.date('Y-m-d-H',time()));
	}
        
        
        
        
        
        /**
	 * 详细订单excel
	 *
	 */
	public function Purchaseexport_step2Op(){
        $lang	= Language::getLangContent();
        $model_order = Model('order');
        $model_orders=Model();
        $sql="  1=1 " ;
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $sql=$sql." and (`order`.store_id = '".$_SESSION['store_id']."') ";
        if ($_GET['order_sn'] != '') {
            $condition['order_sn'] = $_GET['order_sn'];
            $sql=$sql." and (`order`.order_sn = '".$_GET['order_sn']."') ";
        }
        if ($_GET['buyer_name'] != '') {
            $condition['buyer_name'] = $_GET['buyer_name'];
             $sql=$sql." and (`order`.buyer_name = '".$_GET['buyer_name']."') ";
        }
        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_date ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_date ? strtotime($_GET['query_end_date']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['add_time'] = array('time',array($start_unixtime,$end_unixtime));
            $sql=$sql." and (( add_time BETWEEN ".$start_unixtime." AND ".$end_unixtime."))";
        }
        $condition['order_lei'] = 2;
        $sql=$sql." and (`order`.order_lei = 2 ) ";
        
        $a=$_GET['state_type'];
        if (!empty($_GET['state_type']) && $_GET['state_type'] !='store_order' ) {
             $condition['order_state'] = $_GET['state_type'];
              $sql=$sql." and (`order`.order_state = '".$_GET['state_type']."') ";
        } 
        
        $order_list = $model_order->table('order')->getOrderList($condition, 20, '*', 'order_id desc','', array('order_goods','order_common','member'));
        if (!is_numeric($_GET['curpage'])){
                
                                    $field = '`order`.order_sn,`order`.store_name,`order`.order_state,`order`.order_amount,`order`.add_time,`order_goods`.goods_price,'
                                             . '`order_goods`.goods_name,`order_goods`.goods_pay_price,`order_goods`.goods_num,`order_goods`.goods_pay_price,'
                                            . '`member`.member_truename,`member`.member_mobile,'
                                            . '`order_common`.invoice_info,`order_common`.reciver_info,'
                                            . '`city_centre`.city_name';
	                            $on = '`order`.order_id=`order_goods`.order_id ,'
                                            . '`member`.member_name=`order`.buyer_name,'
                                            . '`city_centre`.id=`member`.belong_city_id,'
                                            . '`order_common`.order_id=`order`.order_id';
                                    $model_orders->table('order,order_goods,member,city_centre,order_common')->field($field);
                                    $data = $model_orders->join('left,left')->on($on)->where($sql)->limit(self::EXPORT_SIZE)->select();                                      
                                    $this->PurchasecreateExcel2($data);
			
		}
	}

	/**
	 * 生成excel
	 *
	 * @param array $data
	 */
	private function PurchasecreateExcel2($data = array()){
          
		Language::read('member_store_index');
		import('libraries.excel');
		$excel_obj = new Excel();
		$excel_data = array();
		//设置样式
		$excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
		//header
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_order_nb'));//订单编号
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_store_name'));//店铺名称
                     $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_goods_name'));//商品名称
                     $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_goods_prices'));//商品单价
                     $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_goods_num'));//数量
                     $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_goods_pay_price'));//商品总价
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_order_amounts'));//订单总金额                  
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_buyer_name'));//买家名称                 
                    $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_member_mobile'));//买家手机
                    $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_city_name'));//城市中心
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_invoice_info'));//发票信息
                $excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_reciver_info'));//收货地址
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_order_time'));   //下单时间             
		$excel_data[0][] = array('styleid'=>'s_title','data'=>L('exp_order_state'));//订单状态

		foreach ((array)$data as $k=>$v){
			$tmp = array();
			$tmp[] = array('data'=>$v['order_sn']);//订单编号
			$tmp[] = array('data'=>$v['store_name']);//店铺名称
                            $tmp[] = array('data'=>$v['goods_name']);//商品名称
                            $tmp[] = array('data'=>$v['goods_price']);//商品单价
                            $tmp[] = array('data'=>$v['goods_num']);//数量goods_pay_price
                            $tmp[] = array('data'=>$v['goods_pay_price']);//商品总价
			$tmp[] = array('data'=>$v['order_amount']);//订单金额
                            $a=str_replace("<br>","",implode(unserialize($v['invoice_info'])));
                        $tmp[] = array('data'=>$v['member_truename']);//买家名称
                            $tmp[] = array('data'=>$v['member_mobile']);//买家手机
                            $tmp[] = array('data'=>$v['city_name']);//城市中心
                        $tmp[] = array('data'=>$a);//发票信息
                        $b=unserialize($v['reciver_info']);
                        $tmp[] = array('data'=>$b['address']);//收货地址信息
			$tmp[] = array('data'=>date('Y-m-d H:i:s',$v['add_time'])); //下单时间      
			$tmp[] = array('data'=>orderStates($v));//订单状态
			$excel_data[] = $tmp;
		}    
		$excel_data = $excel_obj->charset($excel_data,CHARSET);
		$excel_obj->addArray($excel_data);
		$excel_obj->addWorksheet($excel_obj->charset(L('exp_order_name'),CHARSET));
		$excel_obj->generateXML($excel_obj->charset(L('exp_order_name'),CHARSET).'-'.date('Y-m-d-H',time()));
	}
        
            
        
}
