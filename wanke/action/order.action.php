<?php
/**
 * User: win7
 * Date: 2016/7/1
 * Time: 16:04
 */
/** 注册需要被客户端访问的程序，类型对应值：bool->"xsd:boolean"   string->"xsd:string"int->"xsd:int"    float->"xsd:float"*/
$server->register( 'updateOrderStatus',
    array(
        "orderSn"=>"xsd:string",
        "orderState"=>"xsd:string",
        "key"=>"xsd:string",
    ),
    array(
        "resultCode"=>"xsd:string",
        "resultMsg"=>"xsd:string",
        "resultData"=>"xsd:string",
    )
);
$server->register( 'updateOrderStatusForCTSys',
    array(
        "orderSn"=>"xsd:string",
        "orderState"=>"xsd:string",
        "key"=>"xsd:string",
    ),
    array(
        "resultCode"=>"xsd:string",
        "resultMsg"=>"xsd:string",
        "resultData"=>"xsd:string",
    )
);
$server->register( 'addOrderfaster',
    array(
        "member_id"=>"xsd:string",
        "store_id"=>"xsd:string",
        "goods_json"=>"xsd:string",
        "key"=>"xsd:string",
    ),
    array(
        "resultCode"=>"xsd:string",
        "resultMsg"=>"xsd:string",
        "resultData"=>"xsd:string",
    )
);
/**
 * 修改订单状态
 * @param $orderSn
 * @param $key
 * @return string
 */
function updateOrderStatus($orderSn,$orderState,$key){
    if( WebServiceInterceptor::check($key) ){
        $resultArray =array("resultCode"=>"-1", "resultMsg"=>"更新失败", "resultData"=>$orderSn,);
        //订单状态校验
        if(CommonUtil::orderStatuscheck($orderState)){
            $serviceFactory = new ServiceFactory();
            $orderService =  $serviceFactory->Service("order");
            $order = array(
                "order_sn"=>$orderSn,
                "order_state"=>$orderState,
            );
            if($orderService->updateOrderStatus($order)){
                $resultArray['resultCode']="0";
                $resultArray['resultMsg']="更新成功";
                return $resultArray;
            }else{
                return   $resultArray;
            }
        }else{
            $resultArray['resultMsg']="orderState不合法";
            return $resultArray;
        }

    }
}

/**
 * 合同系统修改订单状态，并把订单推送到采购系统
 * @param $orderSn
 * @param $key
 * @return string
 */
function updateOrderStatusForCTSys($orderSn,$orderState,$key){
    if( WebServiceInterceptor::check($key) ){
        $resultArray =array("resultCode"=>"-1", "resultMsg"=>"更新失败", "resultData"=>$orderSn,);
        //订单状态校验
        if(CommonUtil::orderStatuscheck($orderState)){
            $serviceFactory = new ServiceFactory();
            $orderService =  $serviceFactory->Service("order");
            $order = array(
                "order_sn"=>$orderSn,
                "order_state"=>$orderState,
            );
            if($orderService->updateOrderStatusForCTSys($order)){
                $resultArray['resultCode']="0";
                $resultArray['resultMsg']="更新成功";
                return $resultArray;
            }else{
                return   $resultArray;
            }
        }else{
            $resultArray['resultMsg']="orderState不合法";
            return $resultArray;
        }
    }
}
function addOrderfaster($member_id,$store_id,$goods_json,$key){
   if( WebServiceInterceptor::check($key) ){
        $serviceFactory = new ServiceFactory();
        $orderService =  $serviceFactory->Service("order");
        
        try {
            $state = $orderService->addOrderFaster($member_id,$store_id,$goods_json);
            if($state){
                return   array(
                    "resultCode"=>"0",
                    "resultMsg"=>"订单添加成功",
                    "resultData"=>"",
                );
            }
        } catch (Exception $e) {
            return   array(
                    "resultCode"=>"-1",
                    "resultMsg"=>"订单添加失败，".$e->getMessage(),
                    "resultData"=>"",
                );
        }
    }
}
?>