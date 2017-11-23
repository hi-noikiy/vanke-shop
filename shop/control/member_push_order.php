<?php
/**
 * 订单列表审批推送订单
 *
 *
 *
 ***/



class member_push_orderControl extends BaseMemberControl{
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
    public function transOrderToYMAOp(){

        $order_sn  = htmlspecialchars($_GET['order_num']);
        //订单推送页面使用 防止2次推送产生不友好log信息 如订单不为待审核 控制不
            $model = Model();
            $order_state = $model->table('order')->field("order_state")->where(array("order_sn"=>$order_sn))->find();
            if($order_state['order_state']=="14"){
                $locked = array(
                    "resultCode" => "0",
                    "resultMsg"  => "重复调用推送功能",
                );
                echo json_encode($locked);exit;
            }
        $order_logic = Logic("trans_order");
        $return =$order_logic->transOrderToYMA($order_sn);
        echo json_encode($return);
    }
}