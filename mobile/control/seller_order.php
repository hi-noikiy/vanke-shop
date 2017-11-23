<?php
/**
 * 商家订单
 *
 *
 *
 *
 */





class seller_orderControl extends mobileSellerControl {

    public function __construct(){
        parent::__construct();
    }

    public function order_listOp() {
        $model_order = Model('order');

        $order_list = $model_order->getStoreOrderList(
            $this->store_info['store_id'],
            $_POST['order_sn'],
            $_POST['buyer_name'],
            $_POST['state_type'],
            $_POST['query_start_date'],
            $_POST['query_end_date'],
            $_POST['skip_off'],
            '*',
            array('order_goods')
        );

        $page_count = $model_order->gettotalpage();
        output_data(array('order_group_list' => $order_list), mobile_page($page_count));
    }

    /**
     * 取消订单
     */
    public function order_cancelOp() {
        $order_id = intval($_POST['order_id']);
        $reason = $_POST['reason'];
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_info['store_id'];
        $order_info = $model_order->getOrderInfo($condition);

        $if_allow = $model_order->getOrderOperateState('store_cancel',$order_info);
        if (!$if_allow) {
            output_error('无权操作');
        }

        if (TIMESTAMP - 86400 < $order_info['api_pay_time']) {
            $_hour = ceil(($order_info['api_pay_time']+86400-TIMESTAMP)/3600);
            output_error('该订单曾尝试使用第三方支付平台支付，须在'.$_hour.'小时以后才可取消');
        }

        if ($order_info['order_type'] == 2) {
            //预定订单
            $result = Logic('order_book')->changeOrderStateCancel($order_info,'seller',$this->seller_info['seller_name'], $reason);
        } else {
            $cancel_condition = array();
            if ($order_info['payment_code'] != 'offline') {
                $cancel_condition['order_state'] = ORDER_STATE_NEW;
            }
            $result = Logic('order')->changeOrderStateCancel($order_info,'seller',$this->seller_info['seller_name'], $reason, true, $cancel_condition);
        }

        if (!$result['state']) {
            output_error($result['msg']);
        }
        output_data('1');
    }
    
    /**
     * 修改运费
     */
    public function order_ship_priceOp() {
        $order_id = intval($_POST['order_id']);
        $shipping_fee = ncPriceFormat($_POST['shipping_fee']);
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_info['store_id'];
        $order_info = $model_order->getOrderInfo($condition);

        $if_allow = $model_order->getOrderOperateState('modify_price',$order_info);
        if (!$if_allow) {
            output_error('无权操作');
        }
        $result = Logic('order')->changeOrderShipPrice($order_info, 'seller', $this->seller_info['seller_name'], $shipping_fee);

        if (!$result['state']) {
            output_error($result['msg']);
        }
        output_data('1');
    }

    /**
     * 发货
     */
    public function order_deliver_sendOp() {
        $order_id = intval($_POST['order_id']);
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_info['store_id'];
        $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
        $if_allow_send = intval($order_info['lock_state']) || !in_array($order_info['order_state'],array(ORDER_STATE_PAY,ORDER_STATE_SEND));
        if ($if_allow_send) {
            output_error('无权操作');
        }

        $_POST['reciver_info'] = $this->_get_reciver_info(); //cary add,  order logic 中有调用
        $result = Logic('order')->changeOrderSend($order_info, 'seller', $this->seller_info['seller_name'], $_POST);
        if (!$result['state']) {
            output_error($result['msg']);
        }
        output_data('1');
    }



    /**
     * 订单详情
     */
    public function order_infoOp(){
        $order_id = intval($_GET['order_id']);
        if ($order_id <= 0) {
            output_error('订单不存在');
        }
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_info['store_id'];
        $order_info = $model_order->getOrderInfo($condition,array('order_goods','order_common','store'));

        if (empty($order_info) || $order_info['delete_state'] == ORDER_DEL_STATE_DROP) {
            output_error('订单不存在');
        }

        $model_refund_return = Model('refund_return');
        $order_list = array();
        $order_list[$order_id] = $order_info;
        $order_list = $model_refund_return->getGoodsRefundList($order_list,1);//订单商品的退款退货显示
        $order_info = $order_list[$order_id];
        $refund_all = $order_info['refund_list'][0];
        if (!empty($refund_all) && $refund_all['seller_state'] < 3) {//订单全部退款商家审核状态:1为待审核,2为同意,3为不同意
            output_error($refund_all);
        }


        $order_info['store_member_id'] = $order_info['extend_store']['member_id'];
        $order_info['store_phone'] = $order_info['extend_store']['store_phone'];


        if($order_info['payment_time']){
            $order_info['payment_time'] = date('Y-m-d H:i:s',$order_info['payment_time']);
        }else{
            $order_info['payment_time'] = '';
        }
        if($order_info['finnshed_time']){
            $order_info['finnshed_time'] = date('Y-m-d H:i:s',$order_info['finnshed_time']);
        }else{
            $order_info['finnshed_time'] = '';
        }
        if($order_info['add_time']){
            $order_info['add_time'] = date('Y-m-d H:i:s',$order_info['add_time']);
        }else{
            $order_info['add_time'] = '';
        }

        if($order_info['extend_order_common']['order_message']){
            $order_info['order_message'] = $order_info['extend_order_common']['order_message'];
        }
        $order_info['invoice'] = $order_info['extend_order_common']['invoice_info']['类型'].$order_info['extend_order_common']['invoice_info']['抬头'].$order_info['extend_order_common']['invoice_info']['内容'];


        $order_info['reciver_phone'] = $order_info['extend_order_common']['reciver_info']['phone'];
        $order_info['reciver_name'] = $order_info['extend_order_common']['reciver_name'];
        $order_info['reciver_addr'] = $order_info['extend_order_common']['reciver_info']['address'];

        $order_info['promotion'] = array();
        //显示锁定中
        $order_info['if_lock'] = $model_order->getOrderOperateState('lock',$order_info);

        //显示取消订单
        $order_info['if_buyer_cancel'] = $model_order->getOrderOperateState('buyer_cancel',$order_info);

        //显示退款取消订单
        $order_info['if_refund_cancel'] = $model_order->getOrderOperateState('refund_cancel',$order_info);

        //显示投诉
        $order_info['if_complain'] = $model_order->getOrderOperateState('complain',$order_info);

        //显示收货
        $order_info['if_receive'] = $model_order->getOrderOperateState('receive',$order_info);

        //显示物流跟踪
        $order_info['if_deliver'] = $model_order->getOrderOperateState('deliver',$order_info);




        //显示评价
        $order_info['if_evaluation'] = $model_order->getOrderOperateState('evaluation',$order_info);

        //显示分享
        $order_info['if_share'] = $model_order->getOrderOperateState('share',$order_info);

        $order_info['ownshop'] = $model_order->getOrderOperateState('share',$order_info);

        //显示系统自动取消订单日期
        if ($order_info['order_state'] == ORDER_STATE_NEW) {
            $order_info['order_cancel_day'] = $order_info['add_time'] + ORDER_AUTO_CANCEL_TIME * 3600;
        }
        $order_info['if_deliver'] = false;
        //显示快递信息
        if ($order_info['shipping_code'] != '') {
            $order_info['if_deliver'] = true;
            $express = rkcache('express',true);
            $order_info['express_info']['e_code'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_code'];
            $order_info['express_info']['e_name'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_name'];
            $order_info['express_info']['e_url'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_url'];
        }


        //显示系统自动收获时间
        if ($order_info['order_state'] == ORDER_STATE_SEND) {
            $order_info['order_confirm_day'] = $order_info['delay_time'] + ORDER_AUTO_RECEIVE_DAY * 24 * 3600;
        }

        //如果订单已取消，取得取消原因、时间，操作人
        if ($order_info['order_state'] == ORDER_STATE_CANCEL) {
            $close_info = $model_order->getOrderLogInfo(array('order_id'=>$order_info['order_id']),'log_id desc');
            $order_info['close_info'] = $close_info;
            $order_info['state_desc'] = $close_info['log_orderstate'];
            $order_info['order_tips'] = $close_info['log_msg'];
        }
        //查询消费者保障服务
        if (C('contract_allow') == 1) {
            $contract_item = Model('contract')->getContractItemByCache();
        }
        foreach ($order_info['extend_order_goods'] as $value) {
            $value['image_60_url'] = cthumb($value['goods_image'], 60, $value['store_id']);
            $value['image_url'] = cthumb($value['goods_image'], 240, $value['store_id']);
            $value['goods_type_cn'] = orderGoodsType($value['goods_type']);
            $value['goods_url'] = urlShop('goods','index',array('goods_id'=>$value['goods_id']));
            //处理消费者保障服务
            if (trim($value['goods_contractid']) && $contract_item) {
                $goods_contractid_arr = explode(',',$value['goods_contractid']);
                foreach ((array)$goods_contractid_arr as $gcti_v) {
                    $value['contractlist'][] = $contract_item[$gcti_v];
                }
            }
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

        $order_info['real_pay_amount'] = $order_info['order_amount']+$order_info['shipping_fee'];
        //取得其它订单类型的信息000--------------------------------

        //$model_order->getOrderExtendInfo($order_info);


        $order_info['zengpin_list']=array();
        if (is_array($order_info['extend_order_goods'])) {
            foreach ($order_info['extend_order_goods'] as $val) {
                if ($val['goods_type'] == 5) {
                    $order_info['zengpin_list'][] = $val;
                }
            }
        }

        //物流设置
        $express_list  = rkcache('express',true);
        $my_express_list = Model()->table('store_extend')->getfby_store_id($this->store_info['store_id'],'express');
        if (!empty($my_express_list)){
            $my_express_list = explode(',',$my_express_list);
        }

//        print_r($my_express_list);exit;
        $order_info['my_express_list']=	$my_express_list;
        $order_info['express_list']=	$express_list;

        output_data(array('order_info'=>$order_info));


        //卖家发货信息
        if (!empty($order_info['extend_order_common']['daddress_id'])) {
            $daddress_info = Model('daddress')->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
            Tpl::output('daddress_info',$daddress_info);
        }


        $data = '{"code":200,"datas":{"order_info":{"order_id":"202","order_sn":"7000000000018101","store_id":"6","store_name":"\u7231\u5bb6\u4e50\u751f\u6d3b\u5bb6\u5c45\u4e13\u8425\u5e97","add_time":"2015-10-29 06:41:30","payment_time":"","shipping_time":"","finnshed_time":"","order_amount":"74.00","shipping_fee":"13.00","real_pay_amount":"74.00","state_desc":"\u5f85\u4ed8\u6b3e","payment_name":"\u5728\u7ebf\u4ed8\u6b3e","order_message":"","reciver_phone":"15950031003","reciver_name":"\u8def\u4eba\u4e01","reciver_addr":"\u5c71\u897f \u9633\u6cc9\u5e02 \u76c2\u53bf \u4eba\u6c11\u8def","store_member_id":"7","store_phone":null,"order_tips":"\u8bf7\u4e8e1\u5c0f\u65f6\u5185\u5b8c\u6210\u4ed8\u6b3e\uff0c\u903e\u671f\u672a\u4ed8\u8ba2\u5355\u81ea\u52a8\u5173\u95ed","promotion":[],"invoice":"","if_deliver":false,"if_buyer_cancel":true,"if_refund_cancel":false,"if_receive":false,"if_evaluation":false,"if_lock":false,"goods_list":[],"zengpin_list":[],"ownshop":false}}}';
        //exit($data);
        $order_id   = intval($_GET['order_id']);
        if ($order_id <= 0) {
            output_error('订单不存在100');
        }

        $model_order    = Model('order');
        $condition['order_id'] = $order_id;
//        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['store_id'] = $this->store_info['store_id'];
        $order_info = $model_order->getOrderInfo($condition,array('order_goods'),'order_id','order_sn','store_id','store_name','add_time','payment_time','shipping_time','finnshed_time','order_amount','shipping_fee','real_pay_amount','state_desc','payment_name','order_message','reciver_phone','reciver_name','reciver_addr','store_member_id','store_phone','order_tips');

        $order_info['promotion']=array();
        $order_info['if_deliver']=	false;
        $order_info['if_buyer_cancel']=	false;
        $order_info['if_refund_cancel']=	false;
        $order_info['if_receive']=	false;
        $order_info['if_evaluation']=	false;
        $order_info['if_lock']=	false;

        $order_info['goods_list']=array();
        $order_info['zengpin_list']=array();
        $order_info['ownshop']=	false;


        output_data(array('order_info'=>$order_info));

    }


    /*
     * 取得店铺公司物流信息
     */
    public function get_express_companyOp(){
        $expresscom_info =array();
        $express_list  = rkcache('express',true);
        $my_express_list = Model()->table('store_extend')->getfby_store_id($this->store_info['store_id'],'express');
        if (!empty($my_express_list)){
            $my_express_list = explode(',',$my_express_list);
        }

        $expresscom_info['my_express_list']=	$my_express_list;
        $expresscom_info['express_list']=	$express_list;
//print_r($expresscom_info);
        output_data(array('express_company'=>$expresscom_info));
    }





    /**
     * 订单详情
     */
    public function get_current_deliverOp(){
        $order_id   = intval($_POST['order_id']);
        if ($order_id <= 0) {
            output_error('订单不存在');
        }

        $model_order    = Model('order');
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_info['store_id'];
        $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
        if (empty($order_info) || !in_array($order_info['order_state'],array(ORDER_STATE_SEND,ORDER_STATE_SUCCESS))) {
            output_error('订单不存在');
        }

        $express = rkcache('express',true);
        $e_code = $express[$order_info['extend_order_common']['shipping_express_id']]['e_code'];
        $e_name = $express[$order_info['extend_order_common']['shipping_express_id']]['e_name'];
        $deliver_info = $this->_get_express($e_code, $order_info['shipping_code']);

        $data = array();
        $data['deliver_info']['context'] = $e_name;
        $data['deliver_info']['time'] = $deliver_info['0'];
        output_data($data);
    }

    /**
     * 从第三方取快递信息
     *
     */
    public function _get_express($e_code, $shipping_code){
        $url = 'http://www.kuaidi100.com/query?type='.$e_code.'&postid='.$shipping_code.'&id=1&valicode=&temp='.random(4).'&sessionid=&tmp='.random(4);
        import('function.ftp');
        $content = dfsockopen($url);
        $content = json_decode($content,true);

        if ($content['status'] != 200) {
            output_error('物流信息查询失败');
        }
        $content['data'] = array_reverse($content['data']);
        $output = array();
        if (is_array($content['data'])){
            foreach ($content['data'] as $k=>$v) {
                if ($v['time'] == '') continue;
                $output[]= $v['time'].'&nbsp;&nbsp;'.$v['context'];
            }
        }
        if (empty($output)) exit(json_encode(false));
        if (strtoupper(CHARSET) == 'GBK'){
            $output = Language::getUTF8($output);//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
        }

        return $output;
    }

    /**
     * 物流跟踪
     */
    public function search_deliverOp(){
        $order_id	= intval($_POST['order_id']);
        if ($order_id <= 0) {
            output_error('订单不存在');
        }

        $model_order	= Model('order');
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_info['store_id'];
        $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
        if (empty($order_info) || !in_array($order_info['order_state'],array(ORDER_STATE_SEND,ORDER_STATE_SUCCESS))) {
            output_error('订单不存在');
        }

        $express = rkcache('express',true);
        $e_code = $express[$order_info['extend_order_common']['shipping_express_id']]['e_code'];
        $e_name = $express[$order_info['extend_order_common']['shipping_express_id']]['e_name'];

        $deliver_info = $this->_get_express($e_code, $order_info['shipping_code']);
        output_data(array('express_name' => $e_name, 'shipping_code' => $order_info['shipping_code'], 'deliver_info' => $deliver_info));
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
}
