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
        Tpl::output('hidden_rtoolbar_cart', 1);
    }

    /**
     * 实物商品 购物车、直接购买第一步:选择收获地址和配送方式
     * 调用采购数据2库
     */
    public function buy_step1Op() {
        //标识购买流程执行步骤
        Tpl::output('buy_step','step2');
        Tpl::output('address_info', Factory("buy")->getDefaultAddress());
        Tpl::output('address_list', Factory("buy")->getAddressList());
        Tpl::output('invoice_list', Factory("buy")->getInvoiceList());
        Tpl::output("list",Factory("buy")->getCartList($_POST));
        Tpl::showpage('buy_step1');
    }

    /**
     * 提交购物车，生成订单
     *
     */
    public function cartOp(){
        $data = $_POST;
        $rest_data = array(
            'code'=>'-1',
            'msg'=>'非法请求',
        );
        if(!empty($data) && is_array($data) && !empty($_SESSION['member_id'])){
            //整理数据
            if(!empty($data['cart']) && is_array($data['cart'])){
                $cartData = array();
                foreach ($data['cart'] as $key=>$val){
                    $store = Model()->table('store')->field('store_name,store_free_price,freight')->where("store_id = '".$key."'")->find();
                    $cartData[] = array(
                        'store_id'          =>$key,
                        'store_name'        =>$store['store_name'],
                        'store_free_price'  =>$store['store_free_price'],
                        'freight'           =>$store['freight'],
                        'cart_id'           =>$val['cart_id'],
                        'message'           =>$val['mark'],
                        'add_code'          =>$data['add_code'],
                        'invoice_code'      =>$data['invoice_code'],
                        'budegt_code'       =>$data['obj_list'],
                        'buy_type'          =>$data['buy_type'],
                    );
                }
                $rest = Factory('order')->newOrder($cartData);
                $rest_data['code'] = $rest['code'];
                if($rest_data['code'] == '2'){
                    $rest_data['msg'] = "商家：".$rest['msg'].'部分商品存在库存不足，请联系商户处理';
                }else{
                    $rest_data['msg'] = $rest['code'] == '1' ? "":"提交失败";
                }
            }
        }
        echo json_encode($rest_data);

    }


     /**
      * 新增发票信息
      *
      */
     public function addInvOp(){
         Tpl::showpage('buy_invoice.add','null_layout');
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

    /**
     * 新增收货地址
     * @Author  : Aletta
     * @Time    : 2017-11-23 PM 14:02
     */
    public function addRessItemOp(){
        if(!empty($_SESSION['member_id'])){
            if(!empty($_GET['id'])){
                $where = "address_id = '".$_GET['id']."' and member_id = '".$_SESSION['member_id']."'";
                $data = Model()->table('address')->where($where)->find();
                //获取省份数据
                $city_data = Model()->table("area")->where("area_id = '".$data['city_id']."'")->find();
                $list = Model()->table("area")->where("area_id = '".$city_data['area_parent_id']."'")->find();
                $data['province'] = $list['area_id'];
                //解析处理电话号码
                if(!empty($data['tel_phone'])) {
                    $tell_data = explode("-",$data['tel_phone']);
                    $data['area_code'] = strlen($tell_data[0]) <= 4 ? $tell_data[0]:"";
                    $data['tell_num'] = strlen($tell_data[0]) <= 4 ? $tell_data[1]:$tell_data[0];
                    $data['extension'] = empty($tell_data[2]) ? '':$tell_data[2];
                }
                Tpl::output('show_id',$_GET['sid']);
                Tpl::output('address',$data);
                Tpl::showpage('buy_address.updata','null_layout');
            }else{
                Tpl::showpage('buy_address.add','null_layout');
            }
        }
    }


    //新增发票页面
    public function newInvoiceOp(){
        if(!empty($_SESSION['member_id'])){
            if(!empty($_GET['id'])){
                $where = "address_id = '".$_GET['id']."' and member_id = '".$_SESSION['member_id']."'";
                $data = Model()->table('address')->where($where)->find();
                //获取省份数据
                $city_data = Model()->table("area")->where("area_id = '".$data['city_id']."'")->find();
                $list = Model()->table("area")->where("area_id = '".$city_data['area_parent_id']."'")->find();
                $data['province'] = $list['area_id'];
                //解析处理电话号码
                if(!empty($data['tel_phone'])) {
                    $tell_data = explode("-",$data['tel_phone']);
                    $data['area_code'] = strlen($tell_data[0]) <= 4 ? $tell_data[0]:"";
                    $data['tell_num'] = strlen($tell_data[0]) <= 4 ? $tell_data[1]:$tell_data[0];
                    $data['extension'] = empty($tell_data[2]) ? '':$tell_data[2];
                }
                Tpl::output('show_id',$_GET['sid']);
                Tpl::output('address',$data);
                Tpl::showpage('buy_address.updata','null_layout');
            }else{
                Tpl::showpage('buy_invoice.add','null_layout');
            }
        }
    }



    /**
     * 获取预算数据信息列表
     * @Author  : Aletta
     * @Time    : 2017-11-27 PM 10：00
     */
    public function getBudgetListOp(){
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
        $array_json = json_encode($array);
        exit($array_json);
        //return $array;
    }


}
