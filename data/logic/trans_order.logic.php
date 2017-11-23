<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
  class trans_orderLogic{
    public function __construct() {
       
    }
     /**
     * 订单列表页面，采购员点击审批按钮，触发此方法，
     * 先判定传过来的订单号是否被锁住，若没有被锁，则说明此订单需要推送给采购系统
     * 通过订单号取得需要的参数后推送给采购系统。当返回为推送成功时，锁定订单
     * @return array(
     *      resultCode =  0 表示推送成功 审批按钮不能打开K2页面   ；-1 表示推送失败 审批按钮可以打开k2页面
     *      resultMessage = "success" 表示推送成功  
     *      resultData = ""  推送成功没有返回数据 
     * )
     */
    
    /**
     * 订单列表页面，采购员点击审批按钮，触发此方法，
     * 先判定传过来的订单号是否被锁住，若没有被锁，则说明此订单需要推送给采购系统
     * 通过订单号取得需要的参数后推送给采购系统。当返回为推送成功时，锁定订单
     * 当有传入状态值时 表示推送拆分的子订单 此时不需要锁单也不用判定订单状态是否为待审核
     * @param type $order_sn   订单编号
     * @param type $type       订单类型  0表示 正常推送（此时订单状态为待审核）   1表示拆单推送（此时订单状态为已审核）       
     * @return array(
     *      resultCode =  0 表示推送成功 审批按钮不能打开K2页面   ；-1 表示推送失败 审批按钮可以打开k2页面
     *      resultMessage = "success" 表示推送成功  
     *      resultData = ""  推送成功没有返回数据 
     * )
     */
    public function transOrderToYMA($order_sn,$type="0"){
        $model = Model();
        if($order_sn==""){
            log::record4inter('推送订单接口：订单号为空，推送失败', log::INFO);
            $return  = array(
                "resultCode"=>"-2",
                "resultMsg"=>"订单号为空",
                "resultData"=>"",
            );
           return $return;
        }
        //获得订单锁定状态
        $is_lock = $model->table("order")->field("lock_state")->where(array('order_sn'=>$order_sn))->find();
        $is_lock = $is_lock['lock_state'];
            
        if($is_lock==0){
            //推送订单
            //向订单表获取订单id 订单编号，购买者id 订单总价和订单状态  预算科目
            $order_db = $model->table('order')->field('order_id,order_sn,buyer_id,order_amount,order_state,store_id,mother_orderid,order_num_sequence,code_combination_id,shipping_fee')->where(array('order_sn'=>$order_sn))->find();
            if($type=="0"){
                if($order_db['order_state']!=ORDER_STATUS_SEND_ONE){
                    log::record4inter('订单号：'.$order_sn.'.状态不为待审核，推送失败', log::INFO);
                    $return  = array(
                        "resultCode"=>"-2",
                        "resultMsg"=>"订单状态不为待审核",
                        "resultData"=>"",
                    );
                    return $return;
                }
            }   
            //制作向雅马哈推送消息的数据数组
            $order_info = array();
            $order_info['freight']=$order_db['shipping_fee'];
            $order_info['order_sn']=$order_db['order_sn'];
            $order_info['order_type_id']='';
            $order_info['order_state']=$order_db['order_state'];
            //$order_select = Model('store')->table('store')->field('member_id')->where(array('store_id'=>$order_db['store_id']))->find();
            $member_id = $model->table('store')->field('member_id')->where(array("store_id"=>$order_db['store_id']))->find();
            $supply_code = $model->table('member')->field("supply_code")->where(array("member_id"=>$member_id['member_id']))->find();
            $order_info['supplier_id']= $supply_code['supply_code'];
            $purchase_pic = $model->table('member')->field('pernr_id')->where(array('member_id'=>$order_db['buyer_id']))->find();
           
            $order_info['purchase_pic']=$purchase_pic['pernr_id'];
            
            //母单和子单被拆数量
             $order_info['parent_order_sn']=$order_db['mother_orderid'] != "" ? $order_db['mother_orderid'] : "";
             $order_info['order_seq']=$order_db['order_num_sequence'] != "" ? $order_db['order_num_sequence'] : "";
             
            //预算科目
             $order_info['code_combination']=$order_db['code_combination_id']==""||null ? "" :$order_db['code_combination_id'] ;
             
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
//                $aa=$order_info['order_items'][$i]['product_id'];
                if(empty($order_info['order_items'][$i]['product_id'])){
                    log::record4inter('订单号：'.$order_sn.'中商品号：'.$order_info_string['materiel_code'].'没有物料编号，推送失败', log::INFO);
                    $return  = array(
                    "resultCode"=>"-2",
                    "resultMsg"=>"该商品没有物料编码",
                    "resultData"=>"",
                    );
                    return $return;
                }
            }

            
            $url = YMA_WEBSERVICE_INSERT_ORDER;
            $order_info_json = json_encode($order_info);
              
            //向远程服务器请求数据
            $result = WebServiceUtil::getDataByCurl($url, $order_info_json, 0);
            $resultArray = json_decode($result,true);
            //推送数据给采购系统的表记录
            CommonUtil::insertData2PushLog($resultArray, $order_sn, $order_info_json, $url, 0);
            if($resultArray['resultCode']=='0'){
                if($type=="0"){
                    //锁定订单
                    $data_update['lock_state'] = 1;
                    $model->table('order')->where(array('order_id'=>$order_db['order_id']))->update($data_update);
                }
                $return  = array(
                "resultCode"=>"0",
                "resultMsg"=>"订单：".$order_sn."已推送",
                "resultData"=>"",
                );
                
                return $return;
            }
            return $resultArray;
            
        }else {
            $return  = array(
                "resultCode"=>"0",
                "resultMsg"=>"订单：".$order_sn."已推送",
                "resultData"=>"",
            );
            return $return;
        }  
    }
  }
