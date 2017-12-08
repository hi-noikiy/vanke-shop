<?php
/**
 * 购买流程
 ***/



class buyControl extends BaseBuyControl {

    public function __construct() {

        parent::__construct();
        Language::read('home_cart_index');
        if (!$_SESSION['member_id']){
            redirect('index.php?act=login&ref_url='.urlencode(request_uri()));
        }
        //验证该会员是否禁止购买
        if(!$_SESSION['is_buy']){
            showMessage(Language::get('cart_buy_noallow'),'','html','error');
        }
        //验证判断当前会员是否是采购员
//        $model = Model();
//
//        if($_SESSION['city_id'] > 0){
//            //如果是有城市中心id的，则是采购员 判断当前店铺是否有当前采购员的城市id
//            $logic_buy = Logic('buy');
//            $result = $logic_buy->buyStep1($_POST['cart_id'], $_POST['ifcart'], $_SESSION['member_id'], $_SESSION['store_id']);
//            //foreach 判断当前提交过来的商品信息是否在当前采购员可购买的城市中心中
//            foreach($result['data']['store_cart_list'] as $key=>$rows){
//                $store_id .= $key.",";
//            }
//            //判断当前会员采购ID有无不在当前商品的
//            $city_inid = explode(',', $store_id);
//            foreach($city_inid as $key=>$city){
//                if($city != $_SESSION['city_id']){
//                    $city_no_id[] = $city;
//                }
//            }
//            if(count($city_no_id) >= 2){
//                //便利输入提示当前用户某个商品不在购买范围内
//                foreach($result['data']['store_cart_list'] as $key=>$re){
//                    foreach($city_no_id as $cityno){
//                        if($cityno == $key){
//                            //存在不能购买的商品则添加提示消息
//                            foreach($result['data']['store_cart_list'][$key] as $msg){
//                                $re_msg .= $msg['goods_name']."</br>";
//                            }
//                        }
//                    }
//                }
//            }
//            if($re_msg){
//                $re_msg_re = "以下商品</br>《".$re_msg."》不在您购买的地区范围内！";
//                showMessage($re_msg_re, 'index.php?act=cart', 'html', 'error');
//            }
//        }
        Tpl::output('hidden_rtoolbar_cart', 1);
    }

    /**
     * 实物商品 购物车、直接购买第一步:选择收获地址和配送方式
     * 调用采购数据2库
     */
    public function buy_step1Op() {

        //查询另外一个库的另外一张表。
        $localhost = DB_TWO_OBJ_ADDRESS.':'.DB_TWO_ROOT_DB_SPORT;
        $db_name = DB_TWO_ROOT_DBNAME;
        $db_password = DB_TWO_ROOT_PASSWORD;
        $mysql_database = DB_TWO_ROOT_DB_C;
        @$con = mysql_connect($localhost,$db_name,$db_password);
        $strsql = 'select * from vanke_budget order by description ASC';
        @mysql_select_db($mysql_database,$con);
        @mysql_query("set names 'utf8'");
        @$result=mysql_query($strsql);
        // 获取查询结果
        $array = array();
        $i = 0;
        while ($row=@mysql_fetch_row($result)){
            $array[$i]['id'] .= $row[0];
            $array[$i]['desc'] .= $row[1];
            $i++;
        }
        // 释放资源
        @mysql_free_result($result);
        // 关闭连接
        @mysql_close(@$con);
        Tpl::output('myrows',$array);
        if($_SESSION['city_id'] > 0){

        }
        //虚拟商品购买分流
        $this->_buy_branch($_POST);

        //得到购买数据
        $logic_buy = Logic('buy');
        $result = $logic_buy->buyStep1($_POST['cart_id'], $_POST['ifcart'], $_SESSION['member_id'], $_SESSION['store_id']);
        if(!$result['state']) {
            showMessage($result['msg'], '', 'html', 'error');
        } else {
            $result = $result['data'];
        }
        //商品金额计算(分别对每个商品/优惠套装小计、每个店铺小计)
        Tpl::output('store_cart_list', $result['store_cart_list']);
        Tpl::output('store_goods_total', $result['store_goods_total']);

        //取得店铺优惠 - 满即送(赠品列表，店铺满送规则列表)
        Tpl::output('store_premiums_list', $result['store_premiums_list']);
        Tpl::output('store_mansong_rule_list', $result['store_mansong_rule_list']);

        //返回店铺可用的代金券
        Tpl::output('store_voucher_list', $result['store_voucher_list']);

        //返回需要计算运费的店铺ID数组 和 不需要计算运费(满免运费活动的)店铺ID及描述
        Tpl::output('need_calc_sid_list', $result['need_calc_sid_list']);
        Tpl::output('cancel_calc_sid_list', $result['cancel_calc_sid_list']);

        //将商品ID、数量、售卖区域、运费序列化，加密，输出到模板，选择地区AJAX计算运费时作为参数使用
        Tpl::output('freight_hash', $result['freight_list']);

        //输出用户默认收货地址
        Tpl::output('address_info', $result['address_info']);

        //输出有货到付款时，在线支付和货到付款及每种支付下商品数量和详细列表
        Tpl::output('pay_goods_list', $result['pay_goods_list']);
        Tpl::output('ifshow_offpay', $result['ifshow_offpay']);
        Tpl::output('deny_edit_payment', $result['deny_edit_payment']);

        //不提供增值税发票时抛出true(模板使用)
        Tpl::output('vat_deny', $result['vat_deny']);

        //增值税发票哈希值(php验证使用)
        Tpl::output('vat_hash', $result['vat_hash']);

        //输出默认使用的发票信息
        Tpl::output('inv_info', $result['inv_info']);

        //显示预存款、支付密码、充值卡
        Tpl::output('available_pd_amount', $result['available_predeposit']);
        Tpl::output('member_paypwd', $result['member_paypwd']);
        Tpl::output('available_rcb_amount', $result['available_rc_balance']);

        //删除购物车无效商品
        $logic_buy->delCart($_POST['ifcart'], $_SESSION['member_id'], $_POST['invalid_cart']);

        //标识购买流程执行步骤
        Tpl::output('buy_step','step2');

        Tpl::output('ifcart', $_POST['ifcart']);

        //店铺信息
        $store_list = Model('store')->getStoreMemberIDList(array_keys($result['store_cart_list']));
        Tpl::output('store_list',$store_list);

        Tpl::showpage('buy_step1');
    }

    /**
     * 生成订单
     *
     */
    public function buy_step2Op() {
        if(empty($_POST['invoice_id'])){
            showMessage("发票信息不能为空！",'index.php');
        }
        //得到购买数据
        $date_goods = $_POST['goods_data'];
        $logic_buy = logic('buy');
        $_POST['order_lei'] = 2;
        $result = $logic_buy->buyStep2($_POST, $_SESSION['member_id'], $_SESSION['member_name'], $_SESSION['member_email']);
        if(!$result['state']) {
            showMessage($result['msg'], 'index.php?act=cart', 'html', 'error');
        }
        if($_SESSION['city_id']){
            //转向会员中心页面
            $key_array = array_keys($result['data']['order_list']);
            $key = $key_array[0];
            $order_sn = $result['data']['order_list'][$key]['order_sn'];
            showMessage("提交订单成功！",'index.php?act=member_inorder&op=inside_order&member_num='.$order_sn);
        }else{
            //转向到商城支付页面
            redirect('index.php?act=buy&op=pay&pay_sn='.$result['data']['pay_sn']);
        }
    }

    /**
     * 下单时支付页面
     */
    public function payOp() {

        $pay_sn	= $_GET['pay_sn'];
        if (!preg_match('/^\d{18}$/',$pay_sn)){
            showMessage(Language::get('cart_order_pay_not_exists'),'index.php?act=member_order','html','error');
        }

        //查询支付单信息
        $model_order= Model('order');
        $pay_info = $model_order->getOrderPayInfo(array('pay_sn'=>$pay_sn,'buyer_id'=>$_SESSION['member_id']),true);
        if(empty($pay_info)){
            showMessage(Language::get('cart_order_pay_not_exists'),'index.php?act=member_order','html','error');
        }
        Tpl::output('pay_info',$pay_info);

        //取子订单列表
        $condition = array();
        $condition['pay_sn'] = $pay_sn;
        $condition['order_state'] = array('in',array(ORDER_STATE_NEW,ORDER_STATE_PAY));
        $order_list = $model_order->getOrderList($condition,'','order_id,order_state,payment_code,order_amount,rcb_amount,pd_amount,order_sn','','',array(),true);
        if (empty($order_list)) {
            showMessage('未找到需要支付的订单','index.php?act=member_order','html','error');
        }

        //重新计算在线支付金额
        $pay_amount_online = 0;
        $pay_amount_offline = 0;
        //订单总支付金额(不包含货到付款)
        $pay_amount = 0;

        foreach ($order_list as $key => $order_info) {

            $payed_amount = floatval($order_info['rcb_amount'])+floatval($order_info['pd_amount']);
            //计算相关支付金额
            if ($order_info['payment_code'] != 'offline') {
                if ($order_info['order_state'] == ORDER_STATE_NEW) {
                    $pay_amount_online += ncPriceFormat(floatval($order_info['order_amount'])-$payed_amount);
                }
                $pay_amount += floatval($order_info['order_amount']);
            } else {
                $pay_amount_offline += floatval($order_info['order_amount']);
            }

            //显示支付方式与支付结果
            if ($order_info['payment_code'] == 'offline') {
                $order_list[$key]['payment_state'] = '货到付款';
            } else {
                $order_list[$key]['payment_state'] = '在线支付';
                if ($payed_amount > 0) {
                    $payed_tips = '';
                    if (floatval($order_info['rcb_amount']) > 0) {
                        $payed_tips = '充值卡已支付：￥'.$order_info['rcb_amount'];
                    }
                    if (floatval($order_info['pd_amount']) > 0) {
                        $payed_tips .= ' 预存款已支付：￥'.$order_info['pd_amount'];
                    }
                    $order_list[$key]['order_amount'] .= " ( {$payed_tips} )";
                }
            }
        }
        Tpl::output('order_list',$order_list);

        //如果线上线下支付金额都为0，转到支付成功页
        if (empty($pay_amount_online) && empty($pay_amount_offline)) {
            redirect('index.php?act=buy&op=pay_ok&pay_sn='.$pay_sn.'&pay_amount='.ncPriceFormat($pay_amount));
        }

        //输出订单描述
        if (empty($pay_amount_online)) {
            $order_remind = '下单成功，我们会尽快为您发货，请保持电话畅通！';
        } elseif (empty($pay_amount_offline)) {
            $order_remind = '请您及时付款，以便订单尽快处理！';
        } else {
            $order_remind = '部分商品需要在线支付，请尽快付款！';
        }
        Tpl::output('order_remind',$order_remind);
        Tpl::output('pay_amount_online',ncPriceFormat($pay_amount_online));
        Tpl::output('pd_amount',ncPriceFormat($pd_amount));

        //显示支付接口列表
        if ($pay_amount_online > 0) {
            $model_payment = Model('payment');
            $condition = array();
            $payment_list = $model_payment->getPaymentOpenList($condition);
            if (!empty($payment_list)) {
                unset($payment_list['predeposit']);
                unset($payment_list['offline']);
            }
            if (empty($payment_list)) {
                showMessage('暂未找到合适的支付方式','index.php?act=member_order','html','error');
            }
            Tpl::output('payment_list',$payment_list);
        }

        //标识 购买流程执行第几步
        Tpl::output('buy_step','step3');
        Tpl::showpage('buy_step2');
    }

    /**
     * 预存款充值下单时支付页面
     */
    public function pd_payOp() {
        $pay_sn	= $_GET['pay_sn'];
        if (!preg_match('/^\d{18}$/',$pay_sn)){
            showMessage(Language::get('para_error'),'index.php?act=predeposit','html','error');
        }

        //查询支付单信息
        $model_order= Model('predeposit');
        $pd_info = $model_order->getPdRechargeInfo(array('pdr_sn'=>$pay_sn,'pdr_member_id'=>$_SESSION['member_id']));
        if(empty($pd_info)){
            showMessage(Language::get('para_error'),'','html','error');
        }
        if (intval($pd_info['pdr_payment_state'])) {
            showMessage('您的订单已经支付，请勿重复支付','index.php?act=predeposit','html','error');
        }
        Tpl::output('pdr_info',$pd_info);

        //显示支付接口列表
        $model_payment = Model('payment');
        $condition = array();
        $condition['payment_code'] = array('not in',array('offline','predeposit'));
        $condition['payment_state'] = 1;
        $payment_list = $model_payment->getPaymentList($condition);
        if (empty($payment_list)) {
            showMessage('暂未找到合适的支付方式','index.php?act=predeposit&op=index','html','error');
        }
        Tpl::output('payment_list',$payment_list);

        //标识 购买流程执行第几步
        Tpl::output('buy_step','step3');
        Tpl::showpage('predeposit_pay');
    }

    /**
     * 支付成功页面
     */
    public function pay_okOp() {
        $pay_sn	= $_GET['pay_sn'];
        if (!preg_match('/^\d{18}$/',$pay_sn)){
            showMessage(Language::get('cart_order_pay_not_exists'),'index.php?act=member_order','html','error');
        }

        //查询支付单信息
        $model_order= Model('order');
        $pay_info = $model_order->getOrderPayInfo(array('pay_sn'=>$pay_sn,'buyer_id'=>$_SESSION['member_id']));
        if(empty($pay_info)){
            showMessage(Language::get('cart_order_pay_not_exists'),'index.php?act=member_order','html','error');
        }
        Tpl::output('pay_info',$pay_info);

        Tpl::output('buy_step','step4');
        Tpl::showpage('buy_step3');
    }

    /**
     * 加载买家收货地址
     *
     */
    public function load_addrOp() {
        $model_addr = Model('address');
        //如果传入ID，先删除再查询
        if (!empty($_GET['id']) && intval($_GET['id']) > 0) {
            $model_addr->delAddress(array('address_id'=>intval($_GET['id']),'member_id'=>$_SESSION['member_id']));
        }
        $condition = array();
        $condition['member_id'] = $_SESSION['member_id'];
        if (!C('delivery_isuse')) {
            $condition['dlyp_id'] = 0;
            $order = 'dlyp_id asc,address_id desc';
        }
        $list = $model_addr->getAddressList($condition,$order);
        Tpl::output('address_list',$list);
        Tpl::showpage('buy_address.load','null_layout');
    }


    /**
     * 选择不同地区时，异步处理并返回每个店铺总运费以及本地区是否能使用货到付款
     * 如果店铺统一设置了满免运费规则，则售卖区域无效
     * 如果店铺未设置满免规则，且使用售卖区域，按售卖区域计算，如果其中有商品使用相同的售卖区域，则两种商品数量相加后再应用该售卖区域计算（即作为一种商品算运费）
     * 如果未找到售卖区域，按免运费处理
     * 如果没有使用售卖区域，商品运费按快递价格计算，运费不随购买数量增加
     */
    public function change_addrOp() {
        $logic_buy = Logic('buy');
        $model_addr = Model('address');
        $data = $logic_buy->changeAddr($_POST['freight_hash'], $_POST['city_id'], $_POST['area_id'], $_SESSION['member_id']);
        //修改默认选择地址
        $where_data = array(
            'member_id' => $_SESSION['member_id'],
            'area_id'   => $_POST['area_id'],
            'city_id'   => $_POST['city_id'],
        );
        $model_addr->editAddress(array('is_default'=>1),$where_data);
        $where_data_other = "member_id = '".$_SESSION['member_id']."' and area_id != '".$_POST['area_id']."' and city_id != '".$_POST['city_id']."'";
        $model_addr->editAddress(array('is_default'=>0),$where_data_other);
        if(!empty($data)) {
            exit(json_encode($data));
        } else {
            exit();
        }
    }

    /**
     * 添加新的收货地址
     *
     */
    public function add_addrOp(){
        $model_addr = Model('address');
        if (chksubmit()){
            //验证表单信息
            $obj_validate = new Validate();
            $obj_validate->validateparam = array(
                array("input"=>$_POST["true_name"],"require"=>"true","message"=>Language::get('cart_step1_input_receiver')),
                array("input"=>$_POST["area_id"],"require"=>"true","validator"=>"Number","message"=>Language::get('cart_step1_choose_area')),
                array("input"=>$_POST["address"],"require"=>"true","message"=>Language::get('cart_step1_input_address'))
            );
            $error = $obj_validate->validate();
            if ($error != ''){
                $error = strtoupper(CHARSET) == 'GBK' ? Language::getUTF8($error) : $error;
                exit(json_encode(array('state'=>false,'msg'=>$error)));
            }
            $data = array();
            $data['member_id'] = $_SESSION['member_id'];
            $data['true_name'] = $_POST['true_name'];
            $data['area_id'] = intval($_POST['area_id']);
            $data['city_id'] = intval($_POST['city_id']);
            $data['area_info'] = $_POST['area_info'];
            $data['address'] = $_POST['address'];
            $data['tel_phone'] = $_POST['tel_phone'];
            $data['mob_phone'] = $_POST['mob_phone'];
            $data['is_default'] = 1;
            //转码
            $data = strtoupper(CHARSET) == 'GBK' ? Language::getGBK($data) : $data;
            $insert_id = $model_addr->addAddress($data);
            if ($insert_id){
                //跟新其它收货地址
                $model_addr->editAddress(array('is_default'=>0),array('member_id'=>$_SESSION['member_id'],'address_id'=>array('not in',$insert_id)));
                exit(json_encode(array('state'=>true,'addr_id'=>$insert_id)));
            }else {
                exit(json_encode(array('state'=>false,'msg'=>Language::get('cart_step1_addaddress_fail','UTF-8'))));
            }
        } else {
            Tpl::showpage('buy_address.add','null_layout');
        }
    }

    /**
     * 加载买家发票列表，最多显示10条
     *
     */
    public function load_invOp() {
        $logic_buy = Logic('buy');

        $condition = array();
        if ($logic_buy->buyDecrypt($_GET['vat_hash'], $_SESSION['member_id']) == 'allow_vat') {
        } else {
            Tpl::output('vat_deny',false);
            $condition['inv_state'] = 1;
        }
        $condition['member_id'] = $_SESSION['member_id'];

        $model_inv = Model('invoice');
        //如果传入ID，先删除再查询
        if (intval($_GET['del_id']) > 0) {
            $model_inv->delInv(array('inv_id'=>intval($_GET['del_id']),'member_id'=>$_SESSION['member_id']));
        }
        $list = $model_inv->getInvList($condition,10);
        if (!empty($list)) {
            foreach ($list as $key => $value) {
                if ($value['inv_state'] == 1) {
                    $list[$key]['content'] = '普通发票'.' '.$value['inv_title'].' '.$value['inv_content'];
                } else {
                    $list[$key]['content'] = '增值税发票'.' '.$value['inv_company'].' '.$value['inv_code'].' '.$value['inv_reg_addr'];
                }
            }
        }
        Tpl::output('inv_list',$list);
        Tpl::showpage('buy_invoice.load','null_layout');
    }

    /**
     * 新增发票信息
     *
     */
    public function add_invOp(){
        $model_inv = Model('invoice');
        if (chksubmit()){
            //如果是增值税发票验证表单信息
            if ($_POST['invoice_type'] == 2) {
                if (empty($_POST['inv_company']) || empty($_POST['inv_code']) || empty($_POST['inv_reg_addr'])) {
                    exit(json_encode(array('state'=>false,'msg'=>Language::get('nc_common_save_fail','UTF-8'))));
                }
            }
            $data = array();
            if ($_POST['invoice_type'] == 1) {
                $data['inv_state'] = 1;
                $data['inv_title'] = $_POST['inv_title_select'] == 'person' ? '个人' : $_POST['inv_title'];
                $data['inv_content'] = $_POST['inv_content'];
                $a=$data['inv_rec_name'] = $_POST['inv_rec_name1'];
                $a=$data['inv_rec_mobphone'] = $_POST['inv_rec_mobphone1'];
                $a=$data['inv_rec_province'] = $_POST['area_info1'];
                $a=$data['inv_goto_addr'] = $_POST['inv_goto_addr1'];
                $a=$data['inv_code'] = $_POST['inv_code1'];
            } else {
                $data['inv_state'] = 2;
                $data['inv_company'] = $_POST['inv_company'];
                $data['inv_code'] = $_POST['inv_code'];
                $data['inv_reg_addr'] = $_POST['inv_reg_addr'];
                $data['inv_reg_phone'] = $_POST['inv_reg_phone'];
                $data['inv_reg_bname'] = $_POST['inv_reg_bname'];
                $data['inv_reg_baccount'] = $_POST['inv_reg_baccount'];
                $data['inv_rec_name'] = $_POST['inv_rec_name'];
                $data['inv_rec_mobphone'] = $_POST['inv_rec_mobphone'];
                $data['inv_rec_province'] = $_POST['area_info'];
                $data['inv_goto_addr'] = $_POST['inv_goto_addr'];
            }
            $data['member_id'] = $_SESSION['member_id'];
            //转码
            $data = strtoupper(CHARSET) == 'GBK' ? Language::getGBK($data) : $data;
            $insert_id = $model_inv->addInv($data);
            if ($insert_id) {
                exit(json_encode(array('state'=>'success','id'=>$insert_id)));
            } else {
                exit(json_encode(array('state'=>'fail','msg'=>Language::get('nc_common_save_fail','UTF-8'))));
            }
        } else {
            Tpl::showpage('buy_address.add','null_layout');
        }
    }

    /**
     * AJAX验证支付密码
     */
    public function check_pd_pwdOp(){
        if (empty($_GET['password'])) exit('0');
        $buyer_info	= Model('member')->getMemberInfoByID($_SESSION['member_id'],'member_paypwd');
        echo ($buyer_info['member_paypwd'] != '' && $buyer_info['member_paypwd'] === md5($_GET['password'])) ? '1' : '0';
    }

    /**
     * F码验证
     */
    public function check_fcodeOp() {
        $result = logic('buy')->checkFcode($_GET['goods_commonid'], $_GET['fcode']);
        echo $result['state'] ? '1' : '0';
        exit;
    }

    /**
     * 得到所购买的id和数量
     *
     */
    private function _parseItems($cart_id) {
        //存放所购商品ID和数量组成的键值对
        $buy_items = array();
        if (is_array($cart_id)) {
            foreach ($cart_id as $value) {
                if (preg_match_all('/^(\d{1,10})\|(\d{1,6})$/', $value, $match)) {
                    $buy_items[$match[1][0]] = $match[2][0];
                }
            }
        }
        return $buy_items;
    }

    /**
     * 购买分流
     */
    private function _buy_branch($post) {
        if (!$post['ifcart']) {
            //取得购买商品信息
            $buy_items = $this->_parseItems($post['cart_id']);
            $goods_id = key($buy_items);
            $quantity = current($buy_items);

            $goods_info = Model('goods')->getGoodsOnlineInfoAndPromotionById($goods_id);
            if ($goods_info['is_virtual']) {
                redirect('index.php?act=buy_virtual&op=buy_step1&goods_id='.$goods_id.'&quantity='.$quantity);
            }
        }
    }

    public function buy_checknumOp(){
        //获取goods_id组装goodsid
        $goods_data = explode(',', htmlspecialchars($_POST['goods_id']));
        foreach($goods_data as $rows){
            $goods[] = explode('_', $rows);
        }
        $model = Model();
        if(is_array($goods)){
            $re_msg = array();
            //当接收到的是多个的商品ID时执行
            $i = 0;
            foreach($goods as $g_goods){
                $where['goods_id'] = $g_goods[0];
                $goods_num = $model->table('goods')->where($where)->field('min_num,max_num')->find();
                if($goods_num){
                    $re_msg[$i]['goods_id'] = $g_goods[0].'_'.$g_goods[1];
                    $re_msg[$i]['state'] = 2;
                    if($g_goods[1] < $goods_num['min_num']){
                        //当前商品小于最小购买数量
                        $re_msg[$i]['state'] = 1;
                        $re_msg[$i]['goods_min'] = $goods_num['min_num'];
                    }
                    if($g_goods[1] > $goods_num['max_num']){
                        //当前商品大于最大购买数量
                        $re_msg[$i]['state'] = 1;
                        $re_msg[$i]['goods_max'] = $goods_num['max_num'];
                    }
                }
                $i++;
            }
        }
        echo json_encode($re_msg);
    }
    public function getobjmoneyOp(){

        $city_log = Logic('city');
        $city_info = array();
        //查询城市公司编码
        $model  = Model();
        /*
        $member_city_code_where['member_id'] = $_SESSION['member_id'];
        $member_city_code = $model->table('member,city_centre')->join('member.belong_city_id = city_centre.id')
                            ->where($member_city_code_where)
                            ->field('city_centre.zt_city_code,member.project_id')
                            ->find();*/
        $member_city_code=$model->query("SELECT sc_city_centre.zt_city_code,sc_member.project_id FROM sc_member left join sc_city_centre on sc_member.belong_city_id=sc_city_centre.id WHERE member_id='".$_SESSION['member_id']."'");
        $city_info['p_org_code']  = $member_city_code[0]['zt_city_code'];
        $city_info['p_dept_code'] = $member_city_code[0]['project_id'];
        $city_info['p_code_combination'] = htmlspecialchars($_POST['val']);
        $re_data = $city_log->verifyBudget($city_info);

        if($re_data['resultCode'] == 200){
            echo sprintf("%.2f", $re_data['budgetBalance']/100);
        }else{
            echo $re_data['resultMsg'];
        }
    }
}
