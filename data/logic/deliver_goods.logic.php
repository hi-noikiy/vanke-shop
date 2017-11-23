<?php
/**
 * 向雅马哈采购系统推送发货信息
 * 传入都为母单(原始单号)
 */
class deliver_goodsLogic{
    public function __construct() {
       
    }
    /**
     * 发货推送，
     * @param type $order_sn        订单号
     * @param type $order_state     订单状态   31为已发货 其他为原始单现状态
     * @param type $product_json    如果拆单发货则此参数必填  格式：0=> 物料编码：AA01W00001  数量 ：5 (母单该商品剩余数量) 
     *                                                             1=> 物料编码：AA03W00004  数量 ：5（母单该商品剩余数量）
     * @return type $return['result_code']  0表示推送成功   1表示推送失败
     */
    public function delicer_goods2YMH($order_sn,$order_state,$product_json=array()){
        $send = array();
        $send['order_no']=$order_sn;
        $send['order_state'] = $order_state;
        $send['product_json'] = $product_json;
        $send['state_flag'] = "1";
        $send['sub_order_no']="";
        $send_json = json_encode($send);
        $url=YMA_WEBSERVICE_DELIVERY_ORDER;
        $return_json = WebServiceUtil::getDataByCurl($url, $send_json, 0);
        $return = json_decode($return_json,true);
        CommonUtil::insertData2PushLog($return, $order_sn, $send_json, $url, 4);
        return $return['resultCode'];
    }
    /*
     * 6.28为满足订单合并新建方法
     */
    
        public function delicer_goods_list2YMH($order_no,$subOrderNo,$order_state,$statelist,$product_json=array()){
        $send = array();
        $send['order_no']=$order_no;
        $send['sub_order_no']=$subOrderNo;
        $send['order_state'] = $order_state;
        $send['state_flag'] = $statelist;
        $send['product_json'] = $product_json;
        $send_json = json_encode($send);
        log::record4inter("判断是否进入:".$operate, log::MOBILE_MESSAGE);
        $url=YMA_WEBSERVICE_DELIVERY_ORDER;
        $return_json = WebServiceUtil::getDataByCurl($url, $send_json, 0);
        $return = json_decode($return_json,true);
        CommonUtil::insertData2PushLog($return, $order_sn, $send_json, $url, 4);
        return $return['resultCode'];
    }
    
}

