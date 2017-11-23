<?php
/**
 * 供应商开店
 *
 *
 *
 ***/



class show_joininControl extends BaseHomeControl {
    public function __construct() {
        parent::__construct();
    }
	/**
	 * 店铺开店页
	 *
	 */
    public function indexOp() {
        Language::read("home_login_index");
        $code_info = C('store_joinin_pic');
        $info['pic'] = array();
	    if(!empty($code_info)) {
	        $info = unserialize($code_info);
	    }
        Tpl::output('pic_list',$info['pic']);//首页图片
        Tpl::output('show_txt',$info['show_txt']);//贴心提示
        $model_help = Model('help');
        $condition['type_id'] = '1';//入驻指南
        $help_list = $model_help->getHelpList($condition,'',4);//显示4个
        Tpl::output('help_list',$help_list);
        Tpl::output('article_list','');//底部不显示文章分类
        Tpl::output('show_sign','joinin');
        Tpl::output('html_title',C('site_name').' - '.'商家入驻');
        Tpl::setLayout('store_joinin_layout');
        Tpl::showpage('store_joinin');
    }
    
    /**
	 *商家认证页
	 *
	 */
    public function index2Op() {
        Language::read("home_login_index");
        $code_info = C('store_joinin_pic');
        $info['pic'] = array();
	    if(!empty($code_info)) {
	        $info = unserialize($code_info);
	    }
        Tpl::output('pic_list',$info['pic']);//首页图片
        Tpl::output('show_txt',$info['show_txt']);//贴心提示
        $model_help = Model('help');
        $condition['type_id'] = '1';//入驻指南
        $help_list = $model_help->getHelpList($condition,'',4);//显示4个
        Tpl::output('help_list',$help_list);
        Tpl::output('article_list','');//底部不显示文章分类
        Tpl::output('show_sign','joinin');
        Tpl::output('html_title',C('site_name').' - '.'商家认证');
        Tpl::setLayout('store_joinin_layout');
        Tpl::showpage('store_joinin2');
    }


    /*
     * 代理商入驻
     * */
    public function agentOp() {
        Language::read("home_login_index");
        $code_info = C('agent_joinin_pic');
        $info['pic'] = array();
        if(!empty($code_info)) {
            $info = unserialize($code_info);
        }
        Tpl::output('pic_list',$info['pic']);//首页图片
        Tpl::output('show_txt',$info['show_txt']);//贴心提示
        $model_help = Model('help');
        $condition['type_id'] = '20';//入驻指南
        $help_list = $model_help->getHelpList($condition,'',4);//显示4个
        Tpl::output('help_list',$help_list);
        Tpl::output('article_list','');//底部不显示文章分类
        Tpl::output('show_sign','joinin');
        Tpl::output('html_title',C('site_name').' - '.'代理入驻');
        Tpl::setLayout('agent_joinin_layout');
        Tpl::showpage('agent_joinin');
    }
    
    public function check_emailOp(){
        if($_SESSION['member_id'] < 0 ){
            showDialog("非法操作！");exit;
        }
        $model = Model();
        $where['city_center'] = htmlspecialchars($_POST['city']);
        $where['member_id'] = $_SESSION['member_id'];
        $city = $model->table('store_joinin')->where($where)->find();
        if($_POST['city'] <= 0 && empty($city)){
            $email_where['contacts_email'] = htmlspecialchars($_POST['email']);
            $email_where['member_id'] = array('neq',$_SESSION['member_id']);

            $email = $model->table('supplier')->field('contacts_email')->where($email_where)->find();
            if(empty($email)){
                echo 1;
            }else{
                echo 2;
            }
        }else{
            echo 1;
        }
        
    }
     public function check_numberOp(){
        if($_SESSION['member_id'] < 0 ){
            showDialog("非法操作！");exit;
        }
        $model = Model();
        $email_where['business_licence_number'] = htmlspecialchars($_POST['number']);
        $email_where['member_id'] = array('neq',$_SESSION['member_id']);
        $email = $model->table('supplier')->field('business_licence_number')->where($email_where)->find();
        if(empty($email)){
            echo 1;
        }else{
            echo 2;
        }
        
    }



}
