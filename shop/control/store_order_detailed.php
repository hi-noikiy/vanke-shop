<?php
header("Content-Type:text/html;charset=utf-8");
/**
 * 订单打印
 ***/




class store_order_detailedControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
		Language::read('member_printorder');
	}

	/**
	 * 查看订单
	 */
	public function indexOp() {
		$order_id	= intval($_GET['order_id']);
		if ($order_id <= 0){
			showMessage(Language::get('wrong_argument'),'','html','error');
		}
		$order_model = Model('order');
		$condition['order_id'] = $order_id;
		$condition['store_id'] = $_SESSION['store_id'];
		$order_info = $order_model->getstore_order_detailed($condition,array('order_common','order_goods'));
		if (empty($order_info)){
			showMessage(Language::get('member_printorder_ordererror'),'','html','error');
		}
		Tpl::output('order_info',$order_info);
		//卖家信息
		$model_store	= Model('store');
		$store_info		= $model_store->getStoreInfoByID($order_info['store_id']);
		if (!empty($store_info['store_label'])){
			if (file_exists(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$store_info['store_label'])){
				$store_info['store_label'] = UPLOAD_SITE_URL.DS.ATTACH_STORE.DS.$store_info['store_label'];
			}else {
				$store_info['store_label'] = '';
			}
		}
		if (!empty($store_info['store_stamp'])){
			if (file_exists(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$store_info['store_stamp'])){
				$store_info['store_stamp'] = UPLOAD_SITE_URL.DS.ATTACH_STORE.DS.$store_info['store_stamp'];
			}else {
				$store_info['store_stamp'] = '';
			}
		}
		Tpl::output('store_info',$store_info);
        //项目名称
        Tpl::output('buyer_name',$this->getOrderProject($order_info['buyer_id'],$order_info['project_code']));
		//订单商品
		$model_order = Model('order');
		$condition = array();
		$condition['order_id'] = $order_id;
		$condition['store_id'] = $_SESSION['store_id'];
		$goods_new_list = array();
		$goods_all_num = 0;
		$goods_total_price = 0;
		if (!empty($order_info['extend_order_goods'])){
			$goods_count = count($order_info['extend_order_goods']);
			$i = 1;
			foreach ($order_info['extend_order_goods'] as $k => $v){
				$v['goods_name'] = str_cut($v['goods_name'],100);
				$goods_all_num += $v['goods_num'];
				$v['goods_all_price'] = ncPriceFormat($v['goods_num'] * $v['goods_price']);
				$goods_total_price += $v['goods_all_price'];
				$goods_new_list[ceil($i/15)][$i] = $v;
				$i++;
			}
		}
		Tpl::output('order_money',$order_info['order_amount']);
		Tpl::output('goods_all_num',$goods_all_num);
		Tpl::output('goods_total_price',ncPriceFormat($goods_total_price));
        Tpl::output('shipping_fee',$order_info['shipping_fee']);
		Tpl::output('goods_list',$goods_new_list);
		Tpl::showpage('store_order.detailed',"null_layout");
	}


	//获取订单项目名称
    private function getOrderProject($buy_id,$project_code){
        if($this->is_https()){
            $dbName = "vs_purchase2";
        }else{
            $dbName = "vs_purchase_t2";
        }
        $sql = "SELECT * FROM ".$dbName.".vanke_pj_code where project_code = '".$project_code."' limit 1";
        $project = Model()->query($sql);
        $buy_info = Model()->table('member')->field('member_truename,member_mobile,belong_city_id')->where("member_id = '".$buy_id."'")->find();
        $city_info = Model()->table('city_centre')->field('city_name')->where("id = '".$buy_info['belong_city_id']."'")->find();
        $data = array(
            'project_name'  =>$project[0]['project_name'],
            'buy_name'      =>$buy_info['member_truename'],
            'buy_tel'       =>$buy_info['member_mobile'],
            'project_city'  =>$city_info['city_name'],
        );
        return $data;
    }

    private function is_https(){
        if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
            return true;
        } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }
}
