<?php
/**
 * 代理店铺的实物订单管理
 *
 *
 *
 ***/



class agent_orderControl extends BaseAgentControl {
    public function __construct() {
        parent::__construct();
        Language::read('member_store_index');
    }

	/**
	 * 订单列表
	 */
	public function indexOp() {
        $model_order = Model('order');
        $model_agent = Model('agent');

        $condition = array();
        $condition['seller_id'] = $_SESSION['agent_id'];

        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_date ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_date ? strtotime($_GET['query_end_date']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['start_time'] = $start_unixtime;
            $condition['end_time'] = $end_unixtime;
        }

        $order_list = $model_agent->getAgentOrder($condition );
//        $order_list = $model_order->getOrderList($condition, 20, '*', 'order_id desc','', array('order_goods','order_common','member'));

        Tpl::output('order_list',$order_list);
        Tpl::output('show_page',$model_order->showpage());
        self::profile_menu('list','agent_order');

        Tpl::showpage('agent_order.index');
	}

    /**
     * vr订单列表
     */
    public function vr_indexOp() {
        $model_order = Model('order');
        $model_agent = Model('agent');

        $condition = array();
        $condition['seller_id'] = $_SESSION['agent_id'];

        $if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_date']);
        $if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_date']);
        $start_unixtime = $if_start_date ? strtotime($_GET['query_start_date']) : null;
        $end_unixtime = $if_end_date ? strtotime($_GET['query_end_date']): null;
        if ($start_unixtime || $end_unixtime) {
            $condition['start_time'] = $start_unixtime;
            $condition['end_time'] = $end_unixtime;
        }

        $order_list = $model_agent->getAgentVROrder($condition );
//        $order_list = $model_order->getOrderList($condition, 20, '*', 'order_id desc','', array('order_goods','order_common','member'));

        Tpl::output('order_list',$order_list);
        Tpl::output('show_page',$model_order->showpage());
        self::profile_menu('list','agent_vr_order');

        Tpl::showpage('agent_vr_order.index');
    }



    private function profile_menu($menu_type='',$menu_key='') {
        Language::read('member_layout');
        switch ($menu_type) {
        	case 'list':
            $menu_array = array(
                array('menu_key'=>'agent_order',		'menu_name'=>'真实交易订单',	'menu_url'=>'index.php?act=agent_order&op=index'),
                array('menu_key'=>'agent_vr_order',		'menu_name'=>'虚拟交易订单',	'menu_url'=>'index.php?act=agent_order&op=vr_index'),
            );
            break;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
