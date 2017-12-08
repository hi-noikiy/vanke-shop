<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/27
 * Time: 上午10:11
 * 78034，78035，78036
 */
class orderFactory {

    //买家信息数据
    private $buyers;

    //付款方式，默认都是在线
    private $payCode = 'online';

    private $_buyData = array();

    //订单来源(1:PC)
    private $_orderFrom = '1';


    public function __construct(){
        $this->buyers = $_SESSION['member_id'];
        $this->_buyData = Model()->table('member')->where("member_id = '".$this->buyers."'")->find();
    }


    public function newOrder($data){
        if(!empty($data) && is_array($data) && !empty($this->buyers)){
            Model()->beginTransaction();
            $rest = array();
            $tock_null = '';
            foreach ($data as $val){
                //检查库存情况
                $restStock = $this->checkStock($val['cart_id']);
                if(!in_array('2',$restStock)){
                    $rest[] = $this->addOrder($val);
                }else{
                    $rest[] = '2';
                    $tock_null.= $val['store_name'].",";
                }
            }
            if(!in_array('-1',$rest)){
                Model()->commit();
                if(in_array('2',$rest)){
                    return array('code'=>'2','msg'=>$tock_null);
                }else{
                    return array('code'=>'1','msg'=>'');
                }
            }else{
                Model()->rollback();
                return array('code'=>'-1','msg'=>'');
            }
        }
    }




    /**
     * 写入订单数据
     * User: zhengguiyun
     * Date: 2017/11/29
     * Time: 上午09:30
     * return   array
     */
    private function addOrder($data){
        $paySn = $this->makePaySn();
        $money = $this->getAllMoney($data['store_id'],$data['store_free_price'],$data['freight'],$data['cart_id']);
        $orderData = array(
            'order_sn'      =>$this->makeOrderSn($this->addPayData($paySn)),//订单编号
            'pay_sn'        =>$paySn,
            'store_id'      =>$data['store_id'],
            'store_name'    =>$data['store_name'],
            'buyer_id'      =>$this->buyers,
            'buyer_name'    =>$this->_buyData['member_name'],
            'buyer_email'   =>$this->_buyData['member_email'],
            'code_combination_id'=>$data['budegt_code'],
            'add_time'      =>TIMESTAMP,
            'payment_code'  =>$this->payCode,//付款方式
            'order_lei'     =>2,
            'order_state'   =>ORDER_STATUS_SEND_ONE,
            'order_amount'  =>number_format($money['freight']+$money['money'],4),//订单总价
            'shipping_fee'  =>$money['freight'],//运费
            'goods_amount'  =>$money['money'],//商品总价
            'order_from'    =>$this->_orderFrom,//订单来源
            'project_code'  =>$this->_buyData['project_id'],
            'city_centre'   =>$this->_buyData['belong_city_id'],
            'buy_type'      =>$data['buy_type'],
        );
        $order_id = Model()->table('order')->insert($orderData);
        if($order_id){
            //处理订单附表
            $com_data = $this->addOrderCommon($order_id,$data['store_id'],$data['message'],$data['add_code'],$data['invoice_code']);
            $order_com = Model()->table('order_common')->insert($com_data);
            if(!empty($data['cart_id']) && is_array($data['cart_id'])){
                $cartIdList = array();
                foreach ($data['cart_id'] as $good_val){
                    list($id,$nums) = explode("|",$good_val);
                    $cartIdList[] = $id;
                    //查询购物车数据表
                    $cartDdata = Model()->table('cart')->where("buyer_id = '".$this->buyers."' and cart_id = '".$id."' and store_id = '".$data['store_id']."'")->find();
                    if(!empty($cartDdata)){
                        $goodData = Model()->table('goods')->field('gc_id_3,goods_salenum,goods_storage')->where("goods_id = '".$cartDdata['goods_id']."'")->find();
                        $order_good_data[] = array(
                            'order_id'      =>$order_id,
                            'store_id'      =>$data['store_id'],
                            'buyer_id'      =>$this->buyers,
                            'goods_id'      =>$cartDdata['goods_id'],
                            'goods_name'    =>$cartDdata['goods_name'],
                            'goods_price'   =>$cartDdata['goods_price'],
                            'goods_num'     =>$cartDdata['goods_num'],
                            'goods_image'   =>$cartDdata['goods_image'],
                            'goods_pay_price'=>number_format($cartDdata['goods_num']*$cartDdata['goods_price'],4),
                            'gc_id'         =>$goodData['gc_id_3'],
                        );
                        //调整库存销量等数据
                        $goodUp = array(
                            'goods_salenum'=>$goodData['goods_salenum'] + $cartDdata['goods_num'],
                            'goods_storage'=>$goodData['goods_storage'] - $cartDdata['goods_num'],
                        );
                    }else{
                        $goodData = Model()->table('goods')->field('goods_id,store_id,goods_name,goods_price,goods_image,gc_id_3,goods_salenum,goods_storage')->where("goods_id = '".$id."'")->find();
                        $order_good_data[] = array(
                            'order_id'      =>$order_id,
                            'store_id'      =>$data['store_id'],
                            'buyer_id'      =>$this->buyers,
                            'goods_id'      =>$goodData['goods_id'],
                            'goods_name'    =>$goodData['goods_name'],
                            'goods_price'   =>$goodData['goods_price'],
                            'goods_num'     =>$nums,
                            'goods_image'   =>$goodData['goods_image'],
                            'goods_pay_price'=>number_format($nums*$goodData['goods_price'],4),
                            'gc_id'         =>$goodData['gc_id_3'],
                        );
                        //调整库存销量等数据
                        $goodUp = array(
                            'goods_salenum'=>$goodData['goods_salenum'] + $nums,
                            'goods_storage'=>$goodData['goods_storage'] - $nums,
                        );
                    }
                    Model()->table('goods')->where("goods_id = '".$cartDdata['goods_id']."'")->update($goodUp);
                }
                Model()->table('order_goods')->insertAll($order_good_data);
            }
            //清理购物车数据
            $this->clearCart($cartIdList);
            return $order_com ? '1':'-1';
        }
        return "-1";
    }

    private function getAllMoney($store_id,$store_free_price,$freights,$cartList){
        $idList = array();
        if(!empty($cartList) && is_array($cartList)){
            foreach ($cartList as $vl){
                list($id,$nums) = explode("|",$vl);
                $idList[] = $id;
            }
        }
        $where = "store_id = '".$store_id."' and cart_id in('".implode("','",$cartList)."')";
        $cartDataNums = Model()->table("cart")->where($where)->count();
        if($cartDataNums > 0){
            $goodSumCart = Model()->table("cart")->field("sum(goods_price*goods_num) as goodNum")->where($where)->find();
            $goodSum = $goodSumCart['goodNum'];
        }else{
            list($good_id,$buy_nums) = explode("|",$cartList[0]);
            $goodData = Model()->table('goods')->field('goods_price')->where("goods_id = '".$good_id."'")->find();
            $goodSum = $goodData['goods_price']*$buy_nums;
        }
        $freight = 0.0000;
        if( $store_free_price > 0 ){
            if($goodSum < $store_free_price){
                $freight+=$freights;
            }
        }
        return array('money'=>number_format($goodSum,4),'freight'=>number_format($freight,4));
    }

    //清理购物车数据
    private function clearCart($cartList){
        $cart_id_str = implode(',',$cartList);
        if (preg_match('/^[\d,]+$/',$cart_id_str)) {
            QueueClient::push('delCart', array('buyer_id'=>$this->buyers,'cart_ids'=>$cartList));
        }
    }


    //添加订单附加信息
    private function addOrderCommon($order_id,$store_id,$message,$addID,$invID){
        $add_data = $this->getSendMember($addID);
        $data = array(
            'order_id'              =>$order_id,
            'store_id'              =>$store_id,
            'order_message'         =>$message,//订单留言
            'reciver_province_id'   =>$add_data['reciver_province_id'],
            'reciver_city_id'       =>$add_data['reciver_city_id'],
            'reciver_county_id'     =>$add_data['reciver_county_id'],
            'reciver_name'          =>$add_data['reciver_name'],
            'reciver_info'          =>$add_data['reciver_info'],
            'invoice_info'          =>$this->createInvoiceData($invID),
        );
        return $data;
    }

    //获取收货人数据信息
    private function getSendMember($add_code){
        $data = Model()->table('address')->where("member_id = '".$this->buyers."' and address_id = '".$add_code."'")->find();
        //获取到省份ID编码
        $city_data = Model()->table("area")->where("area_id = '".$data['city_id']."'")->find();
        $province_data = Model()->table("area")->where("area_id = '".$city_data['area_parent_id']."'")->find();
        $info = array(
            'address'   =>$data['area_info'] .'  '. $data['address'],
            'area'      =>$data['area_info'],
            'street'    =>$data['address'],
            'mob_phone' =>$data['mob_phone'],
            'tel_phone' =>$data['tel_phone'],
        );
        $add_data = array(
            'reciver_province_id'   =>$data['area_id'],
            'reciver_city_id'       =>$data['city_id'],
            'reciver_county_id'     =>$province_data['area_id'],
            'reciver_name'          =>$data['true_name'],
            'reciver_info'          =>serialize($info),
        );
        return $add_data;
    }


    /**
     * 整理发票信息
     * @param array $invoice_info 发票信息数组
     * @return string
     */
    private function createInvoiceData($invoice_id){
        $invoice_info = Model()->table('invoice')->where("member_id = '".$this->buyers."' and inv_id = '".$invoice_id."'")->find();
        //发票信息
        $inv = array();
        if ($invoice_info['inv_state'] == 1) {
            $inv['类型'] = '普通发票';
            $inv['抬头'] = $invoice_info['inv_title_select'] == 'person' ? '个人': $invoice_info['inv_title'];
            $retus = $invoice_info['inv_title'] == '个人' ?  true : false;
            if(!$retus){
                $inv['纳税人识别号'] =$invoice_info['inv_code'];
            }
            $inv['内容'] = $invoice_info['inv_content'];
            $inv['收票人姓名'] = $invoice_info['inv_rec_name'];
            $inv['收票人手机号'] = $invoice_info['inv_rec_mobphone'];
            $inv['收票人省份'] = $invoice_info['inv_rec_province'];
            $inv['收票地址'] = $invoice_info['inv_goto_addr'];
        } elseif (!empty($invoice_info)) {
            $inv['单位名称'] = $invoice_info['inv_company'];
            $inv['纳税人识别号'] = $invoice_info['inv_code'];
            $inv['注册地址'] = $invoice_info['inv_reg_addr'];
            $inv['注册电话'] = $invoice_info['inv_reg_phone'];
            $inv['开户银行'] = $invoice_info['inv_reg_bname'];
            $inv['银行账户'] = $invoice_info['inv_reg_baccount'];
            $inv['收票人姓名'] = $invoice_info['inv_rec_name'];
            $inv['收票人手机号'] = $invoice_info['inv_rec_mobphone'];
            $inv['收票人省份'] = $invoice_info['inv_rec_province'];
            $inv['收票地址'] = $invoice_info['inv_goto_addr'];
        }
        return !empty($inv) ? serialize($inv) : serialize(array());
    }

    //插入订单支付表数据
    private function addPayData($paySn){
        return Model()->table('order_pay')->insert(array('pay_sn'=>$paySn,'buyer_id'=>$this->buyers));
    }


    /**
     * 校验数据的准确
     * User: zhengguiyun
     * Date: 2017/11/28
     * Time: 上午18:30
     * return   array
     */
    private function checkCartData($data = array()){
        //校验收货人的地址信息
        if(!empty($data['add_code'])){
            $list = Model()->table('address')->where("member_id = '".$this->buyers."' and address_id = '".$data['add_code']."'")->find();
            if(empty($list)){
                return array('code'=>'-1','msg'=>'收货人地址信息有误，请核对信息！');
            }
        }
        //校验发票数据信息
        if(!empty($data['invoice_code'])){
            $list = Model()->table('invoice')->where("member_id = '".$this->buyers."' and inv_id = '".$data['invoice_code']."'")->find();
            if(empty($list)){
                return array('code'=>'-1','msg'=>'收货人地址信息有误，请核对信息！');
            }
        }
        return array('code'=>'1','msg'=>'success');
    }

    //检查库存
    private function checkStock($cart = array()){
        if(!empty($cart) && is_array($cart)){
            $data = array();
            foreach ($cart as $val){
                //查询购物车数据表
                list($id,$nums) = explode("|",$val);
                $cartDdata = Model()->table('cart')->where("buyer_id = '".$this->buyers."' and cart_id = '".$id."'")->find();
                if(!empty($cartDdata)){
                    $goodData = Model()->table('goods')->field('goods_storage')->where("goods_id = '".$cartDdata['goods_id']."'")->find();
                }else{
                    $goodData = Model()->table('goods')->field('goods_storage')->where("goods_id = '".$id."'")->find();
                }
                $data[] = $goodData['goods_storage'] < $cartDdata['goods_num'] ? "2":"1";
                break;
            }
            return $data;
        }
    }


    /**
     * 生成支付单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)，该值会传给第三方支付接口
     * 长度 =2位 + 10位 + 3位 + 3位  = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @return string
     */
    private function makePaySn() {
        return mt_rand(10,99)
            . sprintf('%010d',time() - 946656000)
            . sprintf('%03d', (float) microtime() * 1000)
            . sprintf('%03d', (int) $this->buyers % 1000);
    }

    /**
     * 订单编号生成规则，n(n>=1)个订单表对应一个支付表，
     * 生成订单编号(年取1位 + $pay_id取13位 + 第N个子订单取2位)
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @param $pay_id 支付表自增ID
     * @return string
     */
    private function makeOrderSn($pay_id) {
        //记录生成子订单的个数，如果生成多个子订单，该值会累加
        static $num;
        if (empty($num)) {
            $num = 1;
        } else {
            $num ++;
        }
        return (date('y',time()) % 9+1) . sprintf('%013d', $pay_id) . sprintf('%02d', $num);
    }


}