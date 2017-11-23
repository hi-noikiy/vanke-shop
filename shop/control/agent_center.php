<?php
/**
 * 商户中心
 *
 */




class agent_centerControl extends BaseAgentControl {

    /**
     * 构造方法
     *
     */
    public function __construct() {
        parent::__construct();
//        Language::read('common,store_layout,member_layout');
//        if(!C('site_status')) halt(C('closed_reason'));
//        Tpl::setDir('agent');
//        Tpl::setLayout('agent_layout');
    }

    /**
     * 商户中心首页
     *
     */
    public function indexOp() {
		Language::read('member_home_index');

        // 商家帮助
        $model_help = Model('article');
        $condition	= array();
        $condition['limit'] = '6';//是否显示,0为否,1为是
        $condition['ac_id'] = '8';
        $help_list = $model_help->getArticleList($condition, '');
        Tpl::output('help_list',$help_list);

        $phone_array = explode(',',C('site_phone'));
        Tpl::output('phone_array',$phone_array);

        Tpl::output('menu_sign','index');
        Tpl::showpage('index');
    }



}
