<?php

class CommonUtil
{
    /**
     * 对输入订单状态的有效性校验
     * @param $orderStatus 订单状态
     * @return bool
     */
    public static function orderStatuscheck($orderStatus){
        $isExit= stripos(ORDER_STATUS_STR,$orderStatus);
        $result = false;
        if($isExit =='') {
            return $result = false;
        }else if($isExit >= 0 ){
            $result = true;
        }
        return  $result ;
    }
    
     /**
     * 像sc_puch_log表插入推送数据 仅存储错误和失败信息   -1为采购系统接口错误信息  201为合同系统结扩错误信息
     * @param type $result      推送返回的josn数据
     * @param type $order       订单编号  其他类型接口插0或者其他字段 不影响
     * @param type $send_json   推送的json数据
     * @param type $url         推送的url
     * @param type $type        接口类型:0表示订单推送接口，
      *                                  1表示修改订单状态接口,
      *                                  2表示物料数据推送接口(此时订单号为0),
      *                                  3商品分类单条推送
      *                                  4表示发货接口
      *                                  5表示供应商推送接口(此时订单号为供应商code)
      *                                 15表示合同系统供应商推送
      *                                 12表示合同系统物料推送
     * @return boolean
     */
    public static function insertData2PushLog($resultArray,$order,$send_json,$url,$type){
        $model  = Model();
        $insert_info = array();
        $insert_info['order_sn']=$order;
        $insert_info['push_data']=$send_json;
        $insert_info['push_url'] = $url;
              
        $insert_info['action_type']=$type;
        $insert_info['result_code']=$resultArray['resultCode'];
        $insert_info['result_msg']=$resultArray['resultMsg'];
        $insert_info['result_data']=$resultArray['resultData'];
        $insert_info['insert_time']= time();
        try{
            if (empty($resultArray)||$resultArray['resultCode']=='-1'||$resultArray['resultCode']=='201'){
                $insert_info['is_success']='-1'; //1失败
                $model->table('push_log')->insert($insert_info);
                return false;
            }
            //成功情况不插入数据库
//            }else if($resultArray['resultCode']=='0'){
//                $insert_info['is_success']='0';//0成功
//                $model->table('push_log')->insert($insert_info);
//                return true;
//            }
            return true;
        }  catch (Exception $e){
                log::record4inter($e->getMessage(), log::ERR);
                return false;
        }
    }
}
?>
