<?php
/**
 * Insert  stat_order,stat_ordergoods表统计数据
 * @date:2017-11
 */
class pushurlControl extends BaseCronControl{
	public function indexOp() {
		$this->statistics_orders();
	}
	private function statistics_orders(){
		set_time_limit(0);
		$model = new Model();
		//查询order表最后的订单时间记录
		$order_add_time = $model->table('order')->field("add_time")->order("order_id desc")->find();		
		//查询stat_ordergoods表最后统计的记录
		$ordergoods_count = $model->table('stat_ordergoods')->field("order_add_time")->order("rec_id desc")->find();
		$unm = 0;
		$orderid = 0;
		if ($ordergoods_count['order_add_time'] === $order_add_time['add_time'] ) {
			exit;
		}
		//获取订单表信息
		$field_orders = 'order_id,order_sn,store_id,buyer_id,buyer_name,add_time,payment_code,order_amount,shipping_fee,evaluation_state,order_state,refund_state,refund_amount,order_from';
		if (empty($ordergoods_count)) {
			$order_list_rows = $model->table('order')->field($field_orders)->limit('200')->select();
	
		}else{
			//查询订单表最后统计的记录
			$where_count = $ordergoods_count['order_add_time'];
			$order_list_rows = $model->table('order')->field($field_orders)->where("add_time > $where_count")->limit('200')->select();
		}
		foreach ($order_list_rows as $k=>$v){
			$orderid++;
			//获取订单商品表信息
			$where_goods = array("order_id"=>$v['order_id']);
			$field_goods = array();
			$order_goods_row = $model->table('order_goods')->where($where_goods)->select();
	
			//店铺表信息
			$field_store = 'store_id,store_name,grade_id,sc_id';
			$where_store = array('store_id'=>$v['store_id']);
			$store_list_tmp = $model->table('store')->field($field_store)->where($where_store)->find();
	
			//订单扩展表
			$field_store = 'reciver_province_id,order_id';
			$order_common_list_one = $model->table('order_common')->field($field_store)->where($where_goods)->find();
	
			foreach ($order_goods_row as $order=>$goods){
				$unm++;
				//订单扩展表
				$field_order_common = 'order_id,reciver_province_id';
				$where_order_common = array('order_id'=>$goods['order_id']);
				$order_common_list_tmp = $model->table('order_common')->field($field_order_common)->where()->find();
	
				//商品表信息
				$field_goods_list = 'goods_id,goods_commonid,goods_price,goods_serial,gc_id,gc_id_1,gc_id_2,gc_id_3,goods_image,goods_promotion_type';
				$where_goods = array("goods_id"=>$goods['goods_id']);
				$goods_list_tmp = $model->table('goods')->field($field_goods_list)->where($where_goods)->find();
	
				//商品扩展表信息
				$field_goods_common = 'goods_commonid,goods_name,brand_id,brand_name,goods_commis_rate';
				$where_goods_common = array('goods_commonid'=>$goods_list_tmp['goods_commonid']);
				$goods_common_list_tmp = $model->table('goods_common')->field($field_goods_common)->where($where_goods_common)->find();
	
				//获取stat_ordergoods表数据，插入stat_ordergoods表
				$tmp = array();
				$tmp['rec_id'] = $goods['rec_id'];
	
				//查询统计表信息
				$field_stat_ordergoods = "rec_id";
				$where_stat_ordergoods = array("rec_id"=>$goods['rec_id']);
				$stat_ordergoods_getone = $model->table('stat_ordergoods')->field($field_stat_ordergoods)->where($where_stat_ordergoods)->find();
				$is_insert_ordergoods = true;
	
				if ($stat_ordergoods_getone['rec_id']) {$is_insert_ordergoods = false;}//判断重复插入
	
				$tmp['stat_updatetime'] = time();
				$tmp['order_id'] = $v['order_id'];
				$tmp['order_sn'] = $v['order_sn'];
				$tmp['order_add_time'] = $v['add_time'];//strtotime(date('Y-m-d H:i:s',strtotime('-1 day')));//
				$tmp['payment_code'] = $v['payment_code'];
				$tmp['order_amount'] = $v['order_amount'];
				$tmp['shipping_fee'] = $v['shipping_fee'];//运费
				$tmp['evaluation_state'] = $v['evaluation_state'];//评价
				$tmp['order_state'] = $v['order_state'];//订单状态：0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已收货;
				$tmp['refund_state'] = $v['refund_state'];//退款状态:0是无退款,1是部分退款,2是全部退款
				$tmp['refund_amount'] = $v['refund_amount']?$v['refund_amount']:'0.00';//退款金额
				$tmp['order_from'] = $v['order_from'];//1WEB2mobile
				if ($v['order_state'] == 40 or $v['order_state'] == 30 or $v['order_state'] == 31 or $v['order_state'] == 33 or $v['order_state'] == 50) {
					//订单状态：0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已收货;(改为12：待审核 13：审核中，14：审核通过，18：审批退回，81：审核拒绝,31:已发货，33已检收，50：付款完成) 。
					$tmp['order_isvalid'] = 1;//是否为有效统计订单;
				}else {
					$tmp['order_isvalid'] = 0;//是否为有效统计订单
				}
				$tmp['reciver_province_id'] = $order_common_list_tmp['reciver_province_id'];
				$tmp['store_id'] = $v['store_id'];
				$tmp['store_name'] = $store_list_tmp['store_name'];
				$tmp['grade_id'] = $store_list_tmp['grade_id'];
				$tmp['sc_id'] = $store_list_tmp['sc_id'];
				$tmp['buyer_id'] = $v['buyer_id'];//买家id
				$tmp['buyer_name'] = $v['buyer_name'];
				$tmp['goods_id'] = $goods['goods_id'];//商品ID
				$tmp['goods_name'] = $goods['goods_name'];
				$tmp['goods_commonid'] = intval($goods_common_list_tmp['goods_commonid']);
				$tmp['goods_commonname'] = $goods_common_list_tmp['goods_name']?$goods_common_list_tmp['goods_name']:'';
				$tmp['gc_id'] = intval($goods_list_tmp['gc_id']);
				$tmp['gc_parentid_1'] = intval($goods_list_tmp['gc_id_1']);
				$tmp['gc_parentid_2'] = intval($goods_list_tmp['gc_id_2']);
				$tmp['gc_parentid_3'] = intval($goods_list_tmp['gc_id_3']);
				$tmp['brand_id'] = intval($goods_common_list_tmp['brand_id']);
				$tmp['brand_name'] = $goods_common_list_tmp['brand_name']?$goods_common_list_tmp['brand_name']:'';
				$tmp['goods_serial'] = $goods_common_list_tmp['goods_serial']?$goods_common_list_tmp['goods_serial']:'';
				$tmp['goods_price'] = $goods['goods_price'];//商品价格
				$tmp['goods_num'] = $goods['goods_num'];//商品数量
				$tmp['goods_image'] = $goods['goods_image'];
				$tmp['goods_pay_price'] = $goods['goods_price'];//商品实际成交价----会员价
				$tmp['goods_type'] = $goods['goods_type'];//1默认2团购商品3限时折扣商品4组合套装5赠品
				$tmp['promotions_id'] = $goods['promotions_id'];//促销活动ID（团购ID/限时折扣ID/优惠套装ID）与goods_type搭配使用
				$tmp['commis_rate'] = $goods_common_list_tmp['goods_commis_rate'];//佣金比例						echo $a;
				$ordergoods_insert_arr[] = $tmp;
	
				if ($is_insert_ordergoods == false) unset($ordergoods_insert_arr[$unm-1]);
				
			}
			//获取stat_order表数据，插入stat_order表
			$is_insert_order = true;
			$field_stat_order = "order_id";
			$where_stat_order = array("order_id"=>$v['order_id']);
			$stat_order_getone = $model->table('stat_order')->field($field_stat_order)->where($where_stat_order)->find();
			 
			if ($stat_order_getone['order_id']) $is_insert_order = false;
			 
			$tmp_order = array();
			$tmp_order['order_id'] = $v['order_id'];
			$tmp_order['order_sn'] = $v['order_sn'];
			$tmp_order['order_add_time'] = $v['add_time'];
			$tmp_order['payment_code'] = $v['payment_code'];
			$tmp_order['order_amount'] = $v['order_amount'];
			$tmp_order['shipping_fee'] = $v['shipping_fee'];
			$tmp_order['evaluation_state'] = $v['evaluation_state'];
			$tmp_order['order_state'] = $v['order_state'];
			$tmp_order['refund_state'] = $v['refund_state'];
			$tmp_order['refund_amount'] = $v['refund_amount'];
			$tmp_order['order_from'] = $v['order_from'];
			$tmp_order['order_isvalid'] = $tmp['order_isvalid'];
			$tmp_order['reciver_province_id'] = $order_common_list_one['reciver_province_id'];
			$tmp_order['store_id'] = $v['store_id'];
			$tmp_order['store_name'] = $store_list_tmp['store_name'];
			$tmp_order['grade_id'] = $store_list_tmp['grade_id'];
			$tmp_order['sc_id'] = $store_list_tmp['sc_id'];
			$tmp_order['buyer_id'] = $v['buyer_id'];
			$tmp_order['buyer_name'] = $v['buyer_name'];
			$tmp_order_s[] = $tmp_order;
			 
			if ($is_insert_order == false) unset($tmp_order_s[$orderid-1]);
		}
		if (empty($ordergoods_insert_arr) or empty($tmp_order_s)) {
			file_put_contents("./statistics_log.txt",'执行时间:'.date('Y-m-d H:i:s',time()).'-->'.'stat_ordergoods=>'.$tmp['order_id'].','.'stat_order=>'.$tmp_order['order_id'].PHP_EOL,FILE_APPEND);					
		}else {
			array_multisort($ordergoods_insert_arr);
			$result_stat_ordergoods = $model->table('stat_ordergoods')->insertAll($ordergoods_insert_arr);
			if ($result_stat_ordergoods == false) continue;
			array_multisort($tmp_order_s);
			$result_stat_order = $model->table('stat_order')->insertAll($tmp_order_s);
			file_put_contents("./statistics_log.txt",'执行时间:'.date('Y-m-d H:i:s',time()).'-->'.'order_id=>'.$v['order_id'].','.'ordergoods_addtime=>'.$v['add_time'].','.'order_addtime=>'.$v['add_time'].PHP_EOL,FILE_APPEND);				
		}
	}
}