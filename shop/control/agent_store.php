<?php
/**
 * 卖家实物订单管理
 *
 *
 *
 ***/



class agent_storeControl extends BaseAgentControl {
    public function __construct() {
        parent::__construct();
        Language::read('member_store_index');
    }

	/**
	 * 订单列表
	 *
	 */
	public function indexOp() {
        $model_order = Model('store');
        $model_agent = Model('agent');

        $condition = array();
        $condition['seller_id'] = $_SESSION['agent_id'];

        $order_list = $model_agent->getAgentStore($condition );
//        $order_list = $model_order->getOrderList($condition, 20, '*', 'order_id desc','', array('order_goods','order_common','member'));

        Tpl::output('order_list',$order_list);
        Tpl::output('show_page',$model_order->showpage());


        Tpl::showpage('agent_store.index');
	}

}
