<?php
/**
 * 任务计划 - 月执行的任务
 *
 * 
 *
 *
 *
 */


class commissionControl extends BaseCronControl {

    public function indexOp(){
        $model = Model('commission');

        //代理 分润结算
        try {
            $model->beginTransaction();
            $this->_agent_commission();
            $model->commit();
        } catch (Exception $e) {
            $this->log('代理分润:'.$e->getMessage());
        }

        //店铺推广 分润结算
        try {
            $model->beginTransaction();
            $this->_store_commission();
            $model->commit();
        } catch (Exception $e) {
            $this->log('店铺推广分润:'.$e->getMessage());
        }

        //生成佣金统计
        try {
            $model->beginTransaction();
            $this->_commission_statis();
            $model->commit();
        } catch (Exception $e) {
            $this->log('分润统计:'.$e->getMessage());
        }

    }



    /**
     * 生成代理 上月分润
     */
    private function _agent_commission() {
        $model_oc = Model('commission');
        $model_order = Model('order');

        $order_statis_max_info = $model_oc->getOrderStatisInfo(array(),'os_end_date','os_month desc');
        //计算起始时间点，自动生成以月份为单位的空结算记录
        if (!$order_statis_max_info){
            $order_min_info = $model_order->getOrderInfo(array('order_lei'=>array('neq',2)),array(),'min(add_time) as add_time');
            $start_unixtime = is_numeric($order_min_info['add_time']) ? $order_min_info['add_time'] : TIMESTAMP;
        } else {
            $start_unixtime = $order_statis_max_info['os_end_date'];
        }
        $data = array();
        $i = 1;
        $start_unixtime = strtotime(date('Y-m-01 00:00:00', $start_unixtime));   //2015/11/1 0:0:0
        $current_time = strtotime(date('Y-m-01 00:00:01',TIMESTAMP));  //2015/12/1 0:0:1

        while (($time = strtotime('-'.$i.' month',$current_time)) >= $start_unixtime) {
            if (date('Ym',$start_unixtime) == date('Ym',$time)) {
                //如果两个月份相等检查库是里否存在
                $order_statis = $model_oc->getOrderStatisInfo(array('os_month'=>date('Ym',$start_unixtime)));
                if ($order_statis) {
                    break;
                }
            }
            $first_day_unixtime = strtotime(date('Y-m-01 00:00:00', $time));	//该月第一天0时unix时间戳
            $last_day_unixtime = strtotime(date('Y-m-01 23:59:59', $time)." +1 month -1 day"); //该月最后一天最后一秒时unix时间戳
            $key = count($data);
            $os_month = date('Ym',$first_day_unixtime);
            $data[$key]['os_month'] = $os_month;
            $data[$key]['os_year'] = date('Y',$first_day_unixtime);
            $data[$key]['os_start_date'] = $first_day_unixtime;
            $data[$key]['os_end_date'] = $last_day_unixtime;

            //生成所有代理月分润出账单
            $this->_create_agent_commission($data[$key]);
            $i++;
        }
    }


    /**
     * 生成所有代理的月 分润单
     *
     * @param int $data
     */
    private function _create_agent_commission($data){
        $model_agent = Model('agent');
        $model_oc = Model('commission'); //分润模块

        $grade_list = rkcache('agent_grade',true); //缓存中读取代理级别
        //取agent表数量(因为可能存在无订单，但有店铺活动费用，所以不再从订单表取店铺数量)
        $agent_count = $model_agent->getCount(array());

        //分批生成该月份的店铺空结算表，每批生成300个店铺
        $insert = false;
        for ($i=0;$i<=$agent_count;$i=$i+300){
            $agent_list = $model_agent->getAgentList(array(),null,'','seller_id,seller_name,grade_id,member_id',"{$i},300");

            if ($agent_list){
                //自动生成以月份为单位的空结算记录
                $data_bill = array();
                foreach($agent_list as $store_info){

                    $data_bill['oc_no'] = '1'.$data['os_month'].$store_info['seller_id']; // 代理订单编号, 编号前缀 1
                    $data_bill['oc_start_date'] = $data['os_start_date'];
                    $data_bill['oc_end_date'] = $data['os_end_date'];
                    $data_bill['oc_month'] = $data['os_month'];
                    $data_bill['oc_state'] = 0;
                    $data_bill['oc_agent_id'] = $store_info['seller_id'];
                    $data_bill['oc_agent_name'] = $store_info['seller_name'];

                    $data_bill['oc_type'] = '1'; // 佣金类型:  1 代理    2  店铺推广
                    if (!empty($grade_list[$store_info['grade_id']])) {
                        $data_bill['commission_rate'] = $grade_list[$store_info['grade_id']]['ag_rate'];
                    }else{
                        $data_bill['commission_rate'] = '0';
                    }

                    if (!$model_oc->getOrderBillInfo(array('oc_no'=>$data_bill['oc_no']))) {
                        $insert = $model_oc->addOrderBill($data_bill);
                        if (!$insert) {
                            throw new Exception('生成账单['.$data_bill['oc_no'].']失败');
                        }
                        //对已生成空账单进行销量、退单、佣金统计
                        $update = $this->_calc_agent_commission($data_bill);
                        if (!$update){
                            throw new Exception('更新账单['.$data_bill['oc_no'].']失败');
                        }

                        // 给代理发送用户消息
                        $param = array();
                        $param['code'] = 'agent_commission_affirm';
                        $param['member_id'] = $store_info['member_id'];
                        $param['param'] = array(
                            'state_time' => date('Y-m-d H:i:s', $data_bill['oc_start_date']),
                            'end_time' => date('Y-m-d H:i:s', $data_bill['oc_end_date']),
                            'bill_no' => $data_bill['oc_no']
                        );
                        QueueClient::push('sendMemberMsg', $param);

                    }
                }
            }
        }
    }


    /**
     * 计算某月内，某代理的佣金
     *
     * @param array $data_bill
     */
    private function _calc_agent_commission($data_bill){
        $model_agent = Model('agent');
        $model_oc = Model('commission'); //分润模块

        //统计店铺利润表
        $agent_commission = $model_agent->getAgentCommission(array('seller_id'=>$data_bill['oc_agent_id'],'os_month'=>$data_bill['oc_month']) );
        $agent_commissionvr = $model_agent->getAgentCommissionVR(array('seller_id'=>$data_bill['oc_agent_id'],'os_month'=>$data_bill['oc_month']) );

        $update = array();
        $update['oc_commis_totals'] = $agent_commission[0]['oc_commis_totals'];
        $update['oc_commis_return_totals'] = $agent_commission[0]['oc_commis_return_totals'];
        $update['oc_shipping_totals'] = $agent_commission[0]['oc_shipping_totals'];
        $update['oc_vr_commis_totals'] = $agent_commissionvr[0]['oc_vr_commis_totals']; //虚拟订单

        $update['oc_result_totals'] = ($update['oc_commis_totals'] - $update['oc_commis_return_totals'] + $update['oc_vr_commis_totals'])* $data_bill['commission_rate']/100;  //(真实佣金 + 虚拟佣金 - 佣金退还) * 佣金提点/100
        $update['oc_create_date'] = TIMESTAMP;
        $update['oc_state'] = 1;

        return $model_oc->editOrderBill($update,array('oc_no'=>$data_bill['oc_no']));
    }



    /**
     * 生成店铺 上月分润
     */
    private function _store_commission() {
        $model_oc = Model('commission');
        $model_order = Model('order');

        $order_statis_max_info = $model_oc->getOrderStatisInfo(array(),'os_end_date','os_month desc');
        //计算起始时间点，自动生成以月份为单位的空结算记录
        if (!$order_statis_max_info){
            $order_min_info = $model_order->getOrderInfo(array('order_lei'=>array('neq',2)),array(),'min(add_time) as add_time');
            $start_unixtime = is_numeric($order_min_info['add_time']) ? $order_min_info['add_time'] : TIMESTAMP;
        } else {
            $start_unixtime = $order_statis_max_info['os_end_date'];
        }
        $data = array();
        $i = 1;
        $start_unixtime = strtotime(date('Y-m-01 00:00:00', $start_unixtime));   //2015/11/1 0:0:0
        $current_time = strtotime(date('Y-m-01 00:00:01',TIMESTAMP));  //2015/12/1 0:0:1

        while (($time = strtotime('-'.$i.' month',$current_time)) >= $start_unixtime) {
            if (date('Ym',$start_unixtime) == date('Ym',$time)) {
                //如果两个月份相等检查库是里否存在
                $order_statis = $model_oc->getOrderStatisInfo(array('os_month'=>date('Ym',$start_unixtime)));
                if ($order_statis) {
//                    break;
                }
            }
            $first_day_unixtime = strtotime(date('Y-m-01 00:00:00', $time));	//该月第一天0时unix时间戳
            $last_day_unixtime = strtotime(date('Y-m-01 23:59:59', $time)." +1 month -1 day"); //该月最后一天最后一秒时unix时间戳
            $key = count($data);
            $os_month = date('Ym',$first_day_unixtime);
            $data[$key]['os_month'] = $os_month;
            $data[$key]['os_year'] = date('Y',$first_day_unixtime);
            $data[$key]['os_start_date'] = $first_day_unixtime;
            $data[$key]['os_end_date'] = $last_day_unixtime;

            $this->_create_store_commission($data[$key]);            //生成所有代理月分润出账单
            $i++;
        }
    }



    /**
     * 生成所有店铺推广的月 分润单
     *
     * @param int $data
     */
    private function _create_store_commission($data){
        
        $model_order = Model('order');
        $model_oc = Model('commission'); //分润模块
        $store_register_rate = C('points_store_invite');  //店铺推广分润比例, 店铺会员佣金,全平台统一

         $condition = array();
         $condition['order_state'] = ORDER_STATE_SUCCESS;
         $condition['order_lei'] = array('neq',2);
         $condition['finnshed_time'] = array(array('egt',$data['os_start_date']),array('elt',$data['os_end_date']),'and');
         
         $store_count =  $model_order->getOrderInfo($condition,array(),'count(DISTINCT inviter_store) as c');
         
         $store_count = $store_count['c'];

        $insert = false;
        for ($i=0;$i<=$store_count;$i=$i+300){
            $model = Model();
            $store_list = $model->table('order')->field('DISTINCT inviter_store')->where($condition)->order('inviter_store asc')->limit("{$i},300")->page(null)->select();

            if ($store_list){
                //自动生成以月份为单位的空结算记录
                $data_bill = array();
                foreach($store_list as $store_info) {
                    if(!empty($store_info['inviter_store'])){
                        $data_bill['oc_no'] = '2' . $data['os_month'] . $store_info['inviter_store']; // 店铺订单编号, 编号前缀 2
                        $data_bill['oc_start_date'] = $data['os_start_date'];
                        $data_bill['oc_end_date'] = $data['os_end_date'];
                        $data_bill['oc_month'] = $data['os_month'];
                        $data_bill['oc_state'] = 0;
                        $data_bill['oc_store_id'] = $store_info['inviter_store'];
                        $data_bill['oc_type'] = '2'; // 佣金类型:  1 代理    2  店铺推广
                        $data_bill['commission_rate'] = $store_register_rate;

                        if (!$model_oc->getOrderBillInfo(array('oc_no' => $data_bill['oc_no']))) {
                            $insert = $model_oc->addOrderBill($data_bill);
                            if (!$insert) {
                                throw new Exception('生成店铺分润[' . $data_bill['oc_no'] . ']失败');
                            }
                            //对已生成空账单进行销量、退单、佣金统计
                            $update = $this->_calc_store_commission($data_bill);
                            if (!$update) {
                                throw new Exception('更新店铺分润[' . $data_bill['oc_no'] . ']失败');
                            }

                            // 发送店铺消息
                            $param = array();
                            $param['code'] = 'store_commission_affirm';// cary_add 添加佣金发放模板
                            $param['store_id'] = $store_info['inviter_store'];
                            $param['param'] = array(
                                'state_time' => date('Y-m-d H:i:s', $data_bill['oc_start_date']),
                                'end_time' => date('Y-m-d H:i:s', $data_bill['oc_end_date']),
                                'bill_no' => $data_bill['oc_no']
                            );
                            QueueClient::push('sendStoreMsg', $param);
                        }
                    }
                }
            }
        }
    }


    /**
     * 计算某月内，某店铺推广的佣金
     *
     * @param array $data_bill
     */
    private function _calc_store_commission($data_bill){
        $model_oc = Model('commission'); //分润模块

        $model_order = Model('order');
        $model_store = Model('store');
        $model_vr_order = Model('vr_order');

        $update = array();

        //店铺名字
        $store_info = $model_store->getStoreInfoByID($data_bill['oc_store_id']);
        $update['oc_store_name'] = $store_info['store_name'];

        // == 真实交易 ==
        $order_condition = array();
        $order_condition['order_state'] = ORDER_STATE_SUCCESS;
        $order_condition['order_lei'] = array('neq',2);
        $order_condition['inviter_store'] = $data_bill['oc_store_id'];
        $order_condition['finnshed_time'] = array('between',"{$data_bill['oc_start_date']},{$data_bill['oc_end_date']}");

        //订单金额
        $fields = 'sum(order_amount) as order_amount,sum(shipping_fee) as shipping_amount,store_name';
        $order_info =  $model_order->getOrderInfo($order_condition,array(),$fields);
        $update['oc_totals'] = floatval($order_info['order_amount']); //真实交易订单 总金额
        $update['oc_shipping_totals'] = floatval($order_info['shipping_amount']); //运费 总金额

        //佣金金额
        $order_info =  $model_order->getOrderInfo($order_condition,array(),'count(DISTINCT order_id) as count');
        $order_count = $order_info['count'];
        $commis_rate_totals_array = array();
        //分批计算佣金，最后取总和
        for ($i = 0; $i <= $order_count; $i = $i + 300){
            $order_list = $model_order->getOrderList($order_condition,'','order_id','',"{$i},300");
            $order_id_array = array();
            foreach ($order_list as $order_info) {
                $order_id_array[] = $order_info['order_id'];
            }
            if (!empty($order_id_array)){
                $order_goods_condition = array();
                $order_goods_condition['order_id'] = array('in',$order_id_array);
                $field = 'SUM(ROUND(goods_pay_price*commis_rate/100,2)) as commis_amount';
                $order_goods_info = $model_order->getOrderGoodsInfo($order_goods_condition,$field);
                $commis_rate_totals_array[] = $order_goods_info['commis_amount'];
            }else{
                $commis_rate_totals_array[] = 0;
            }
        }
        $update['oc_commis_totals'] = floatval(array_sum($commis_rate_totals_array));  // 真实交易佣金总额

        //退款总额
        $model_refund = Model('refund_return');
        $refund_condition = array();
        $refund_condition['seller_state'] = 2;
        $refund_condition['inviter_store'] = $data_bill['oc_store_id'];
        $refund_condition['goods_id'] = array('gt',0);
        $refund_condition['admin_time'] = array(array('egt',$data_bill['oc_start_date']),array('elt',$data_bill['oc_end_date']),'and');
        $refund_info = $model_refund->getRefundReturnInfo($refund_condition,'sum(refund_amount) as amount');
        $update['oc_return_totals'] = floatval($refund_info['amount']); //退款总额

        //退款佣金
        $refund  =  $model_refund->getRefundReturnInfo($refund_condition,'sum(ROUND(refund_amount*commis_rate/100,2)) as amount');
        if ($refund) {
            $update['oc_commis_return_totals'] = floatval($refund['amount']); //退款佣金金额
        } else {
            $update['oc_commis_return_totals'] = 0;
        }

        // ==VR交易==
        $vrorder_condition = array();
        $vrorder_condition['vr_state'] = 1;
        $vrorder_condition['store_id'] = $data_bill['ob_store_id'];
        $vrorder_condition['vr_usetime'] = array('between',"{$data_bill['ob_start_date']},{$data_bill['ob_end_date']}");

        //订单金额
        $fields = 'sum(pay_price) as order_amount,SUM(ROUND(pay_price*commis_rate/100,2)) as commis_amount';
        $vr_order_info =  $model_vr_order->getOrderCodeInfo($vrorder_condition, $fields);
        $update['oc_vr_totals'] = floatval($vr_order_info['order_amount']);  //虚拟订单总额

        //虚拟交易 佣金金额
        $update['oc_vr_commis_totals'] = $vr_order_info['commis_amount'];  //虚拟订单佣金总额

        // 计算总结算金额
        $update['oc_result_totals'] = ($update['oc_commis_totals'] + $update['oc_vr_commis_totals'] - $update['oc_commis_return_totals'] ) * $data_bill['commission_rate']/100;  //(真实佣金 + 虚拟佣金 - 佣金退还) * 佣金提点/100
        $update['oc_create_date'] = TIMESTAMP;
        $update['oc_state'] = 1;

        return $model_oc->editOrderBill($update,array('oc_no'=>$data_bill['oc_no']));
    }


    /**
     * 生成 上月佣金统计
     */
    private function _commission_statis() {
        $model_oc = Model('commission');
        $model_order = Model('order');

        $order_statis_max_info = $model_oc->getOrderStatisInfo(array(),'os_end_date','os_month desc');
        //计算起始时间点，自动生成以月份为单位的空结算记录
        if (!$order_statis_max_info){
            $order_min_info = $model_order->getOrderInfo(array('order_lei'=>array('neq',2)),array(),'min(add_time) as add_time');
            $start_unixtime = is_numeric($order_min_info['add_time']) ? $order_min_info['add_time'] : TIMESTAMP;
        } else {
            $start_unixtime = $order_statis_max_info['os_end_date'];
        }
        $data = array();
        $i = 1;
        $start_unixtime = strtotime(date('Y-m-01 00:00:00', $start_unixtime));   //2015/11/1 0:0:0
        $current_time = strtotime(date('Y-m-01 00:00:01',TIMESTAMP));  //2015/12/1 0:0:1


        while (($time = strtotime('-'.$i.' month',$current_time)) >= $start_unixtime) {
            if (date('Ym',$start_unixtime) == date('Ym',$time)) {
                //如果两个月份相等检查库是里否存在
                $order_statis = $model_oc->getOrderStatisInfo(array('os_month'=>date('Ym',$start_unixtime)));
                if ($order_statis) {
                    break;
                }
            }

            $first_day_unixtime = strtotime(date('Y-m-01 00:00:00', $time));	//该月第一天0时unix时间戳
            $last_day_unixtime = strtotime(date('Y-m-01 23:59:59', $time)." +1 month -1 day"); //该月最后一天最后一秒时unix时间戳
            $key = count($data);
            $os_month = date('Ym',$first_day_unixtime);
            $data[$key]['os_month'] = $os_month;
            $data[$key]['os_year'] = date('Y',$first_day_unixtime);
            $data[$key]['os_start_date'] = $first_day_unixtime;
            $data[$key]['os_end_date'] = $last_day_unixtime;
            $data[$key]['os_create_date'] = TIMESTAMP;

            $fileds = 'sum(oc_totals) as oc_totals,sum(oc_return_totals) as oc_return_totals,
            sum(oc_commis_totals) as oc_commis_totals,sum(oc_commis_return_totals) as oc_commis_return_totals,
            sum(oc_result_totals) as oc_result_totals,
            sum(oc_vr_totals) as oc_vr_totals,sum(oc_shipping_totals) as oc_shipping_totals,
            sum(oc_vr_commis_totals) as oc_vr_commis_totals';
            $order_bill_info = $model_oc->getOrderBillInfo(array('oc_month'=>$os_month),$fileds);

            $data[$key]['os_order_totals'] = floatval($order_bill_info['oc_totals']); //真实交易订单总额
            $data[$key]['os_shipping_totals'] = floatval($order_bill_info['oc_shipping_totals']);//运费
            $data[$key]['os_order_return_totals'] = floatval($order_bill_info['oc_return_totals']);//真实退单金额
            $data[$key]['os_commis_totals'] = floatval($order_bill_info['oc_commis_totals']); //真实佣金金额
            $data[$key]['os_commis_return_totals'] = floatval($order_bill_info['oc_commis_return_totals']);   //真实退还佣金
            $data[$key]['os_store_cost_totals'] = 0;   //店铺促销活动费用
            $data[$key]['os_result_totals'] = floatval($order_bill_info['oc_result_totals']);   //本期应结
            $data[$key]['os_order_totals_vr'] = floatval($order_bill_info['oc_vr_totals']);  //虚拟交易订单金额
            $data[$key]['os_commis_totals_vr'] = floatval($order_bill_info['oc_vr_commis_totals']); //虚拟交易佣金金额

            $i++;
        }
        krsort($data);

        foreach ($data as $v) {
            $insert = $model_oc->addOrderStatis($v);
            if (!$insert) {
                throw new Exception('生成平台月出账单['.$v['os_month'].']失败');
            }
        }
    }

}