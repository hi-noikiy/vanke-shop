<?php
/**
 * 买家 我的实物订单
 *
 */




class member_unlineControl extends BaseMemberControl {

    public function __construct() {
        parent::__construct();
        Language::read('member_member_index');
    }

	/**
     * 线下订单
     *
     */
    public function indexOp() {
        Tpl::setLayout('supplier_member_layout');
        $model_order = Model('order');
        //搜索
        $condition = array();
        $condition['buyer_id'] = $_SESSION['member_id'];
        if ($_GET['ordersn'] != '') {
            $condition['orderNo'] = $_GET['ordersn'];

        }
        if($_GET['state_type'] > 0){
            $condition['orderStatus'] = $_GET['state_type'];
            
        }elseif (empty($_GET['state_type'])){
            $condition['orderStatus'] = '14';
        }
        if($_GET['query_start_date'] != ''){
            $condition['orderDateFrom'] = $_GET['query_start_date'];
        }
        if($_GET['query_end_date'] != ''){
            $condition['orderDateTo'] = $_GET['query_end_date'];
        }
        $member_model = Model();
        $member_supplycode = $member_model->table('member')->where('member_id='.$_SESSION['member_id'])->field('supply_code')->find();
        if(!empty($member_supplycode['supply_code'])){
          $condition['supplyCode'] = $member_supplycode['supply_code'];
        }
        if($_GET['page']>0){
            $condition['rowNoFrom'] = $_GET['page'];
        }else{
            $condition['rowNoFrom'] = 1;
        }
        $url = "?act=member_unline&op=index&page={page}";  
        $condition['pageSize'] = 10;
        $order_group_list = $this->get_unline_order($condition);
       if($order_group_list){
       		$totalNum = $order_group_list['totalNum'];
       		$page = new pages($totalNum, $condition['pageSize'], $condition['rowNoFrom'], $url, 2);
       		Tpl::output('show_page',$page->myde_write());
       		Tpl::output('order_group_list',$order_group_list['purchase_order_json']);
            Tpl::output('page_num',  ceil($order_group_list['totalNum']/$condition['pageSize']));
       }
        Tpl::output('page',$_GET['page']);
        Tpl::output('order_pay_list',$order_pay_list);
        Tpl::showpage('member_order.unline');
    }

    public function indexsOp() {
        Tpl::setLayout('supplier_member_layout');
        $model_order = Model('order');
        //搜索
        $condition = array();
        $condition['buyer_id'] = $_SESSION['member_id'];
        if ($_GET['ordersn'] != '') {
            $condition['orderNo'] = $_GET['ordersn'];

        }
        if($_GET['state_type'] > 0){
            $condition['orderStatus'] = $_GET['state_type'];

        }elseif (empty($_GET['state_type'])){
            $condition['orderStatus'] = '14';
        }
        if($_GET['query_start_date'] != ''){
            $condition['orderDateFrom'] = $_GET['query_start_date'];
        }
        if($_GET['query_end_date'] != ''){
            $condition['orderDateTo'] = $_GET['query_end_date'];
        }
        $member_model = Model();
        $member_supplycode = $member_model->table('member')->where('member_id='.$_SESSION['member_id'])->field('supply_code')->find();
        if(!empty($member_supplycode['supply_code'])){
            $condition['supplyCode'] = $member_supplycode['supply_code'];
        }
        if($_GET['page']>0){
            $condition['rowNoFrom'] = $_GET['page'];
        }else{
            $condition['rowNoFrom'] = 1;
        }
        $url = "?act=member_unline&op=index&page={page}";
        $condition['pageSize'] = 10;
        $order_group_list = $this->get_unline_order($condition);
        if($order_group_list){
            $totalNum = $order_group_list['totalNum'];
            $page = new pages($totalNum, $condition['pageSize'], $condition['rowNoFrom'], $url, 2);
            Tpl::output('show_page',$page->myde_write());
            Tpl::output('order_group_list',$order_group_list['purchase_order_json']);
            Tpl::output('page_num',  ceil($order_group_list['totalNum']/$condition['pageSize']));
        }
        Tpl::output('page',$_GET['page']);
        Tpl::output('order_pay_list',$order_pay_list);
        Tpl::showpage('member_order.unline（old）');
    }
    

    /**
     * 用户中心右边，小导航
     *
     * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return
	 */
	private function profile_menu($menu_key='') {
	    Language::read('member_layout');
	    $menu_array = array(
	        array('menu_key'=>'member_order','menu_name'=>Language::get('nc_member_path_order_list'), 'menu_url'=>'index.php?act=member_inorder&op=inside_order')
	    );
	    Tpl::output('member_menu',$menu_array);
	    Tpl::output('menu_key',$menu_key);
	}
        
        public function send_order_sendstatusOp(){
            try{
                $data['subOrderNo'] = htmlspecialchars($_POST['ordersn']);
                $data['order_state'] = htmlspecialchars($_POST['orderstate']);
                $logic=Logic('deliver_goods');
                $return = $logic->delicer_goods2YMH($data['subOrderNo'],$data['order_state']);
                if($return==0){
                    echo 1;
                }
            }catch (Exception $exc ){
               log::record4inter('推送系统异常', log::ERR);
            }
        }
        
            /*
             * 6.28为满足订单合并新建方法
             * statelist: 1单个商品发货 0是整个订单发货
             */
    
        public function send_order_sendstatus2Op(){
 
            try{
                $data['order_no'] = htmlspecialchars($_POST['order_no']);
                $data['sub_order_no'] = htmlspecialchars($_POST['sub_order_no']);
                $data['order_state'] = htmlspecialchars($_POST['orderstate']);
                $data['state_flag'] = htmlspecialchars($_POST['state_flag']);
                $logic=Logic('deliver_goods');
                $return = $logic->delicer_goods_list2YMH($data['order_no'],$data['sub_order_no'],$data['order_state'],$data['state_flag']);
                if($return==0){
                    echo 1;
                }
            }catch (Exception $exc ){
               log::record4inter('推送系统异常', log::ERR);
            }
        }
        /**
         * 仅认证未开店的供应商查看下线订单
         * @param type $condition  {"orderStatudelicer_goods_list2YMHs":"14","orderDateFrom":"","orderDateTo":"","orderNo":"","supplyCode":"162","rowNoFrom":"1","pageSize":"50"}
         * @return string          1页面展示数据数组  2展示数据总数 
         */
        public function get_unline_order($condition){
            $purchase_order_json = array();
            $purchase_order_json['orderStatus'] = $condition['orderStatus'] ? $condition['orderStatus'] : '';
            $orderDateFrom = $condition['orderDateFrom'] ? $condition['orderDateFrom'] : '';
            $orderDateTo  = $condition['orderDateTo'] ? $condition['orderDateTo'] : '';
            if($orderDateFrom!=""){
                $orderDateFrom=str_replace("-","",$orderDateFrom);
            }
            if($orderDateTo!=""){
                $orderDateTo=str_replace("-","",$orderDateTo);
            }
            $purchase_order_json['orderDateFrom']=$orderDateFrom;
            $purchase_order_json['orderDateTo']=$orderDateTo;
            $purchase_order_json['orderNo']=$condition['orderNo'] ? $condition['orderNo'] : '';
            $purchase_order_json['supplyCode']=$condition['supplyCode'];
            $purchase_order_json['rowNoFrom']=$condition['rowNoFrom'];
            $purchase_order_json['pageSize']=$condition['pageSize'];
        $send_json = json_encode($purchase_order_json);
        $url = YMA_WEBSERVICE_RETRIVE_PURCHASE_ORDER;
        $return_json = WebServiceUtil::getDataByCurl($url, $send_json, 1);
        $return = json_decode($return_json,true);
        if($return['resultCode']=="0"){
            return $return['resultData']; 
        }else{
            return "";
        }
        
        
        
       
        }


    /**
     * 招标询价
     */
    public function tenderInquiryOp(){
        /**
         * 读取语言包
         */
        Language::read('home_article_index');
        $lang	= Language::getLangContent();
        $default_url="";
        /**
         * 分类导航
         */
        if($_GET['mtype']=='tender'){
            $nav_link = array(
                array('title'=>$lang['homepage'], 'link'=>SHOP_SITE_URL),
                array('title'=>'我的商城', 'link' => urlShop('member', 'home')),
                array('title'=>'招标信息',)
            );
        }else{
            $nav_link = array(
                array('title'=>$lang['homepage'], 'link'=>SHOP_SITE_URL),
                array('title'=>'我的商城', 'link' => urlShop('member', 'home')),
                array('title'=>'询价信息',)
            );
        }
        Tpl::output('nav_link_list',$nav_link);

        if( $_SESSION['ref_url_iframe']=='' ){
            if($_GET['mtype']=='tender'){
                $default_url = IFRAME_TENDER_DEFAULT;//招标模块默认的页面
            }else{
                $default_url = IFRAME_INQUIRY_DEFAULT;//询价模块默认的页面
            }
            if(stripos("?",$default_url)>=0){
                $default_url.='&'.Embedpage::getCommonParams().'&'.'usePosit=mc';
            }else{
                $default_url.='?'.Embedpage::getCommonParams().'&'.'usePosit=mc';//拼接上要传给采购系统的固定的参数
            }
        }else{		//需要跳转到采购系统指定的url时
            $default_url= $_SESSION['ref_url_iframe'] ;
            if(stripos("?",$_SESSION['ref_url_iframe'])>=0){
                $default_url.='&'.Embedpage::getCommonParams().'&'.'usePosit=mc';
            }else{
                $default_url.='?'.Embedpage::getCommonParams().'&'.'usePosit=mc';
            }
            $_SESSION['ref_url_iframe']="";
        }
        Tpl::output('default_url',$default_url);

        Model('seo')->type('article')->param(array('article_class'=>'招标信息'))->show();
        if($_GET['up']=='mc'){
            Tpl::showpage('tender.inquiry');
        }else{
            Tpl::showpage('parentIframe');
        }
    }
}
