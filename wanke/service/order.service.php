<?php
/**
 * User: lwl
 * Date: 2016/6/29
 * Time: 17:00
 */
class orderService
{

    public function  updateOrderStatus($update_order){
        $model_order = Model('order');
        try {//获取订单详细
            $model_order->beginTransaction();
            $condition = array();
            $condition['order_sn'] = $update_order['order_sn'];
            $order_info	= $model_order->getOrderInfo($condition);
            if(!empty($order_info)){
                //更新订单状态。采购已签收33转换为40
                /*if($update_order['order_state']==ORDER_STATUS_CUS_RECEIVED){
                    $update_order['order_state']=ORDER_STATUS_RECEIVED;
                }*/
                $update = $model_order->editOrder($update_order,array('order_id'=>$order_info['order_id']));
                if (!$update) {
                    log::record4inter($update_order['order_sn']."订单状态更新异常",log::ERR);
                    throw new Exception('订单状态更新异常');
                }
                if($update_order['order_state']==ORDER_STATUS_SUCCESS){
                    //解锁订单
                    $data_update['lock_state'] = 0;
                    Model()->table('order')->where(array('order_id'=>$order_info['order_id']))->update($data_update);
                    log::record4inter($update_order['order_sn']."订单解锁成功",log::IN_INFO);
                }
                $model_order->commit();
                log::record4inter($update_order['order_sn']."订单更新状态为".$update_order['order_state'],log::IN_INFO);
            }else{
                log::record4inter($update_order['order_sn']."订单不存在",log::IN_INFO);
                return false;
            }
        } catch (Exception $e) {
            $model_order->rollback();
            return false;
        }
        return $update;
    }
    /**
     * 合同系统调用的接口，当成功将订单状态修改为以审核状态时
     * 向雅马哈推送订单消息。并且返回给合同系统调用成功
     * @param type $order 合同系统传输过来的订单 （订单号，和订单状态）
     * @return boolean
     * @throws Exception
     */
    public function  updateOrderStatusForCTSys($order){
        //修改状态
        $update = self::updateOrderStatus($order);
        if($update){
            //审核通过的订单推送订单到采购系统
            if($order['order_state']==ORDER_STATUS_SUCCESS){
                try{
                    self::transOrderToYMA($order);
                } catch (Exception $exc) {
                    log::record4inter('系统异常:'.$exc->getMessage(), log::ERR);
                }
            }
            return true;
        }else{
            return false;
        }
    }
    /**
     * 向雅马哈系统推送消息，当合同系统修改完订单状态为以审核之后
     * 调用此方法，根据参数数组中的订单编号和订单状态取数据库中表
     * sc_order，sc_order_common，sc_order_goods，sc_goods 的数据
     * 推送给雅马哈采购系统
     * @param type $order 订单数组 格式 ['order_sn'] =>8000000000049901 ['order_state']=>70
     * @return boolean
     */
    public function transOrderToYMA($order){
        //首先给订单上锁，当成功返回时给订单解锁
        $model = Model();
        $data_update['lock_state'] = 1;
        $model->table('order')->where(array('order_sn'=>$order['order_sn']))->update($data_update);  
        
        if($order['order_sn']==""){
            log::record4inter('推送订单接口：订单号为空，推送失败', log::INFO);
            return array(
                "resultCode"=>"-1",
                "resultMsg"=>"订单号为空",
                "resultData"=>"",
            );
        }  
        //向订单表获取订单id 订单编号，购买者id 订单总价和订单状态
        $order_db = $model->table('order')->field('order_id,order_sn,buyer_id,order_amount,order_state,store_id')->where(array('order_sn'=>$order['order_sn']))->find();
        if($order_db['order_state']!=ORDER_STATUS_SUCCESS){
            log::record4inter('订单号：'.$order['order_sn'].'\n状态不为审核通过，推送失败', log::INFO);
            return array(
                "resultCode"=>"-1",
                "resultMsg"=>"订单状态不为已审核",
                "resultData"=>"",
            );
        }   
        //制作向雅马哈推送消息的数据数组
        $order_info = array();
        $order_info['freight']=$order_db['shipping_fee'];
        $order_info['order_sn']=$order_db['order_sn'];
        $order_info['order_type_id']='';
        //$order_select = Model('store')->table('store')->field('member_id')->where(array('store_id'=>$order_db['store_id']))->find();
        $order_info['supplier_id']= $order_db['store_id'];
        $purchase_pic = $model->table('member')->field('pernr_id')->where(array('member_id'=>$order_db['buyer_id']))->find();
        $order_info['purchase_pic']=$purchase_pic['pernr_id'];
        //向订单拓展表获取收货人的信息
        $address_serialize = $model->table('order_common')->field('reciver_info')->where(array('order_id'=>$order_db['order_id']))->find();
        $address_unserialize=unserialize($address_serialize['reciver_info']);
        $order_info['delivery_facility']=$address_unserialize['address'];
        $order_info['delivery_date']='';
        $order_info['decided_price']=$order_db['order_amount'];
        //在传输的数据数组中加入order_items 存放从订单商品表中得到的商品编号，单价，数量等数据
        $order_info['order_items']=array();
        $other_info=$model->table('order_goods')->field('goods_id,goods_price,goods_num')->where(array('order_id'=>$order_db['order_id']))->select();
        for($i=0;$i<sizeof($other_info);$i++){
            $order_info['order_items'][$i]['standard_price']=$other_info[$i]['goods_price'];
            $order_info['order_items'][$i]['quantity']=$other_info[$i]['goods_num'];
            $order_info['order_items'][$i]['unit']='';
            $order_info_string = $model->table('goods')->field('materiel_code')->where(array('goods_id'=>$other_info[$i]['goods_id']))->find();
            $order_info['order_items'][$i]['product_id']= $order_info_string['materiel_code'];
            if(empty($order_info['order_items'][$i]['product_id'])){
                log::record4inter('订单号：'.$order['order_sn'].'中商品号：'.$order_info_string['materiel_code'].'没有物料编号，推送失败', log::INFO);
                return array(
                "resultCode"=>"-1",
                "resultMsg"=>"该商品没有物料编码",
                "resultData"=>"",
            );
            }
        }
     
        //$url='http://192.168.172.208:8080/xl04_war/services/insert/insertOrder';
        //$url = YMA_WEBSERVICE_URL_HEAD."/impac/services/insert/insertOrder";
        $url = YMA_WEBSERVICE_INSERT_ORDER;
        $order_info_json = json_encode($order_info);
        //向远程服务器请求数据
        $result = WebServiceUtil::getDataByCurl($url, $order_info_json, 0);
        $resultArray = json_decode($result,true);
        //推送数据给采购系统的表记录
        CommonUtil::insertData2PushLog($resultArray, $order['order_sn'], $order_info_json, $url, 0);
        if($resultArray['resultCode']=='0'){
            //解锁订单
            $data_update['lock_state'] = 0;
            $model->table('order')->where(array('order_id'=>$order_db['order_id']))->update($data_update);  
        }  
        
    }
  /**
   * 快速生成订单
   * @param type $member_id  用户id
   * @param type $store_id   店铺id
   * @param type $goods_json [{"goods_id":"101109","goods_num":"2"},{"goods_id":"101110","goods_num":"3"}]  2维数组 第2维包含商品id 商品数量 必须为同一家店铺下的商品
   * @return type            success:  code=0  msg='订单添加成功' error: code=-1 msg='订单添加失败'.errormsg
   * @throws Exception
   */
    public function  addOrderFaster($member_id,$store_id,$goods_json){
        $model_order = Model('order');
        try {//获取订单详细
            $model_order->beginTransaction();
                //根据接口提供的用户id去查询必要数据并且保存起来。
                $model = Model('member');
                $member_info=array();
                $member_info =$model->getMemberInfoByID($member_id);
                if(empty($member_info)){
                    throw new Exception('用户id信息错误');
                }
                if($member_info['member_provinceid'] == null || $member_info['member_cityid'] == null || $member_info['member_areaid'] == null){
                    throw new Exception('该用户没有填写默认收货地址或者默认收货地址不全');
                }
                //根据接口提供的店铺id去查询必要的数据并且保存起来。
                $model = Model('store');
                $store_info=array();
                $store_info=$model->getStoreInfoByID($store_id);
                if(empty($store_info)){
                    throw new Exception('店铺id信息错误');
                }
                //将传进来的json转换为可用数组
                 $goods_key = json_decode($goods_json,true);
                 if(sizeof($goods_key)==0||sizeof($goods_key[0])==0){
                     throw new Exception('json字符串格式有误');
                 }
                //根据接口提供的商品id区查询必要的数据并且保存起来。
                $model = Model('goods');
                $goods_info=array();
                //设置快速下单运费为0
                $total_free=0;
                for($i=0; $i<sizeof($goods_key); $i++){
                    $goods_info[$i]=$model->getGoodsInfo(array('goods_id'=>$goods_key[$i]['goods_id'],));
                    if(empty($goods_info)){
                       throw new Exception('商品id信息错误');
                    }
                    //根据接口提供的商品数量判断参数是否有问题、
                    if(!is_numeric($goods_key[$i]['goods_num'])||$goods_key[$i]['goods_num']<1){
                        throw new Exception('商品数量参数有误');
                    }
                    if($i==0){
                    $store_info_key = $goods_info[$i]['store_id'];
                    }
                    if($goods_info[$i]['store_id']!=$store_info_key){
                    throw new Exception('非同一家店铺商品不可一起下单');
                    }
                    //计算商品总价
                    $total+=$goods_info[$i]['goods_price'] * $goods_key[$i]['goods_num'];
                    }
                //根据接口提供的用户id查询用户默认收件人信息
                $model = Model('address');
                $address_info = $model->getAddressInfo(array('member_id'=>$member_id,'is_default'=>'1'));
                if(empty($address_info)){
                    throw new Exception('该用户没有默认地址');
                }
                $address = array();
                $address['phone']=trim($address_info['mob_phone'].($address_info['tel_phone'] ? ','.$address_info['tel_phone'] : null),',');
                $address['mob_phone']=$address_info['mob_phone'];
                $address['tel_phone']=$address_info['tel_phone'];
                $address['address']=$address_info['area_info'].' '.$address_info['address'];
                $address['area']=$address_info['area_info'];
                $address['street']=$address_info['address'];
                $reciver_info =serialize($address);        
    //第一步 向sc_order_pay表插入必要数据
                $logic_buy_1 = logic('buy_1');
                $pay_sn = $logic_buy_1->makePaySn($member_id);
                $model = Model('order');
                $order_pay = array();
                $order_pay['pay_sn'] = $pay_sn;
                $order_pay['buyer_id'] = $member_id;
                $order_pay_id = $model->addOrderPay($order_pay);
                if (!$order_pay_id) {
                    throw new Exception('订单保存失败[未生成支付单]');
                }
    //第二步 向sc_order 表插入必要数据
                $order = array();
                $order['order_sn'] = $logic_buy_1->makeOrderSn($order_pay_id);;
                $order['pay_sn'] = $pay_sn;
                $order['store_id'] = $store_id;
                $order['store_name'] = $store_info['store_name'];
                $order['buyer_id'] = $member_id;
                $order['buyer_name'] = $member_info['member_name'];
                $order['buyer_email'] =$member_info['member_email'];
                $order['inviter_store'] = $member_info['inviter_id'];
                $order['add_time'] = TIMESTAMP;
                $order['payment_code'] = '支付宝';
                $order['order_state'] = '10';
                $order['order_amount'] = $total;
                $order['shipping_fee'] = $total_free;
                $order['goods_amount'] = $order['order_amount'] - $order['shipping_fee'];
                $order['order_from'] = '1';
                $order_id = $model->addOrder($order);
                if (!$order_id) {
                    throw new Exception('订单保存失败[未生成定单]');
                };       
    //第三步  向sc_order_common 表插入必要数据
                $order_common = array();
                $order_common['order_id'] = $order_id;
                $order_common['store_id'] = $store_id;
                $order_common['order_message'] = '';                                     //接口提供 若不提供为空
                $order_common['reciver_info']= $reciver_info;                            //接口提供 若不提供默认使用默认收货地址 存储格式用php序列化方式（serialize ）
                $order_common['reciver_name'] = $address_info['true_name'];              //接口提供 若不提供默认使用默认收件人
                $order_common['reciver_province_id'] = $member_info['member_provinceid'];//接口提供 若不提供默认使用默认收件人的省级id
                $order_common['reciver_city_id'] = $member_info['member_cityid'];        //接口提供 若不提供默认使用默认收件人的市级id
                $order_common['invoice_info'] = serialize(array());                      //接口提供 若不提供默认不需要发票
              //$order_common['promotion_info'] = addslashes($store_mansong_rule_list[$store_id]['desc']);  //促销信息
                $order_id = $model->addOrderCommon($order_common);
                if (!$order_id) {
                    throw new Exception('订单保存失败[未生成订单扩展数据]');
                }            
    //第四步  向sc_order_goods 表插入必要数据 完成生成订单流程
                $order_goods= array();
                for($i=0;$i<sizeof($goods_info);$i++){
                $order_goods[$i]['order_id']=$order_id;                                          
                $order_goods[$i]['goods_id']=$goods_info[$i]['goods_id'];
                $order_goods[$i]['goods_name']=$goods_info[$i]['goods_name'];
                $order_goods[$i]['goods_price']=$goods_info[$i]['goods_price'];
                $order_goods[$i]['goods_num']=$goods_key[$i]['goods_num'];                                        //接口提供商品数量
                $order_goods[$i]['goods_image']=$goods_info[$i]['goods_image'];
                $order_goods[$i]['goods_pay_price']= $order_goods[$i]['goods_price'] * $order_goods[$i]['goods_num'];
                $order_goods[$i]['store_id']=$store_id;
                $order_goods[$i]['buyer_id']=$member_id;
                $order_goods[$i]['goods_type']='1';                                              //默认无优惠类型 
                $order_goods[$i]['promotions_id']=0;                                             //促销活动id 默认0
                $order_goods[$i]['commis_rate']=0;                                               //佣金比例  默认无s
                $order_goods[$i]['gc_id']=$goods_info[$i]['gc_id'];
                $model = Model();
                $insert = $model->table('order_goods')->insert($order_goods[$i]);
                if (!$insert) {
                    throw new Exception('订单保存失败[未生成商品数据]');
                }
                }
                 $model_order->commit();
        } catch (Exception $e) {
            $model_order->rollback();
            throw $e;
        }
        return $state=true;
    }
}
?>