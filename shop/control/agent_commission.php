<?php
/**
 * 代理佣金管理
 *
 *
 *
 ***/



class agent_commissionControl extends BaseAgentControl {
    public function __construct() {
        parent::__construct();
        Language::read('member_store_index');
    }

	/**
	 * 订单列表
	 *
	 */
	public function indexOp() {
        $model_oc = Model('commission');

        $condition = array();
        $condition['oc_agent_id'] = $_SESSION['agent_id'];

        $order_list = $model_oc->getOrderBillList($condition, $fields = '*', $pagesize = null, $order = '', $limit = null);
//        $order_list = $model_order->getOrderList($condition, 20, '*', 'order_id desc','', array('order_goods','order_common','member'));

        Tpl::output('order_list',$order_list);
        Tpl::output('show_page',$model_oc->showpage());


        Tpl::showpage('agent_commission.index');
	}

}
