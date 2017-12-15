<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/27
 * Time: 上午10:11
 */
class buyFactory {

    //买家信息数据
    private $buyers;

    private $restCode = '1';


    public function __construct(){
        $this->buyers = $_SESSION['member_id'];
    }

    /**
     * 获取购物车数据信息
     * User: zhengguiyun
     * Date: 2017/11/27
     * Time: 上午10:23
     * $cart  array    购物车数据
     * return   array
     */
    public function getCartList($cart = array()){
        if(!empty($cart) && is_array($cart)){
            //获取购物车数据的ID列表
            $cartIdList = array();
            foreach ($cart['cart_id'] as $vl){
                list($catrId,$nums) = explode("|",$vl);
                $cartIdList[] = $catrId;
            }
            //获取购物车店铺的数据
            $cartStoreList = Model()->table("cart")->field("store_id,store_name")->where("cart_id in('".implode("','",$cartIdList)."')")->group("store_id")->select();
            $list = array();
            //常规购物车购买商品
            if(!empty($cartStoreList) && is_array($cartStoreList)){
                $goodMoney = 0.0000;
                foreach ($cartStoreList as $storeVal){
                    $cartGoodData = $this->getStroeGoodList($storeVal,$cartIdList);
                    $list['goodList'][] = $cartGoodData;
                    $goodMoney += $cartGoodData['allMoney'];
                }
                $list['goodMoney'] = $goodMoney;
            }else{
                //直接购买，不走购物车流程
                list($catrId,$nums) = explode("|",$cart['cart_id'][0]);
                $good_data = $this->getOneGoodList($catrId,$nums);
                $list['goodList'][] = $good_data;
                $list['goodMoney'] = $good_data['allMoney'];
            }
            return $list;
        }
    }


    /**
     * 获取买家默认收货地址
     * User: zhengguiyun
     * Date: 2017/11/27
     * Time: 上午14:23
     * return   array
     */
    public function getDefaultAddress(){
        $list = Model()->table('address')->where("member_id = '".$this->buyers."' and is_default = '1'")->find();
        return $list;
    }

    /**
     * 获取买家地址数据列表
     * User: zhengguiyun
     * Date: 2017/11/27
     * Time: 上午14:30
     * return   array
     */
    public function getAddressList(){
        $list = Model()->table('address')->where("member_id = '".$this->buyers."'")->order("is_default desc")->select();
        return $list;
    }

    /**
     * 获取发票数据列表
     * User: zhengguiyun
     * Date: 2017/11/27
     * Time: 上午14:30
     * return   array
     */
    public function getInvoiceList(){
        $list = Model()->table('invoice')->where("member_id = '".$this->buyers."'")->select();
        $invoice_list = array();
        if(!empty($list) && is_array($list)){
            foreach ($list as $val){
                $invoice_list[] = array(
                    'inv_id'        => $val['inv_id'],
                    'inv_state'     => $val['inv_state'],
                    'inv_title'     => $val['inv_state']=='1' ? $val['inv_title']:$val['inv_company'],
                    'inv_content'   => $val['inv_state']=='1' ? $val['inv_content']:$val['inv_code']."&nbsp;&nbsp;".$val['inv_rec_name'],
                );
            }
        }
        return $invoice_list;
    }

    private function getOneGoodList($good,$num){
        //获取商品数据
        $goodData = Model()->table('goods')->field("store_id,goods_name,goods_price,goods_image,goods_state,goods_storage,goods_spec")->where("goods_id = '".$good."'")->find();
        $spce = unserialize($goodData['goods_spec']);
        if(!empty($spce) && is_array($spce)){
            $spceStr = explode("  ",$spce);
        }
        $store = Model()->table('store')->field('store_id,store_name,store_free_price,freight')->where("store_id = '".$goodData['store_id']."'")->find();
        $list = array(
            array(
                'cart_id'       =>$good,
                'goods_id'      =>$good,
                'goods_name'    =>$goodData['goods_name'],
                'goods_price'   =>$goodData['goods_price'],
                'goods_num'     =>$num,
                'goods_image'   =>$goodData['goods_image'],
                'is_line'       =>$goodData['goods_state'] == '1' ? "1":"2",
                'is_storage'    =>$goodData['goods_storage'] >= $num == '1' ? "1":"2",
                'spce'          =>$spceStr,
            ),
        );
        $goodSum = number_format($goodData['goods_price']*$num,4,".","");
        $freight = 0.0000;
        if( $store['store_free_price'] > 0 ){
            if($goodSum < $store['store_free_price']){
                $freight+=$store['freight'];
            }
        }
        return array(
            'store_id'  =>$store['store_id'],
            'store_name'=>$store['store_name'],
            'goodList'  =>$list,
            'goodMoney' =>$goodSum,
            'freight'   =>number_format($freight,4,".",""),
            'allMoney'  =>number_format($goodSum+$freight,4,".",""),
        );
    }


    private function getStroeGoodList($store,$cartList){
        //获取商品数据
        $where = "store_id = '".$store['store_id']."' and cart_id in('".implode("','",$cartList)."')";
        $goodList = Model()->table("cart")->where($where)->select();
        //检查商品是否已经被下架以及库存
        $newList = array();
        if(!empty($goodList) && is_array($goodList)){
            foreach ($goodList as $val){
                $spceStr = '';
                //检查是否有效 unserialize
                $goodData = Model()->table('goods')->field("goods_state,goods_storage,goods_spec")->where("goods_id = '".$val['goods_id']."'")->find();
                $spce = unserialize($goodData['goods_spec']);
                $val['is_line'] = $goodData['goods_state'] == '1' ? "1":"2";
                $val['is_storage'] = $goodData['goods_storage'] >= $val['goods_num'] == '1' ? "1":"2";
                if(!empty($spce) && is_array($spce)){
                    $spceStr = explode("  ",$spce);
                }
                $val['spce'] = $spceStr;
                $newList[] = $val;
            }
        }
        $goodSum = Model()->table("cart")->field("sum(goods_price*goods_num) as goodNum")->where($where)->find();
        $storeFreight = Model()->table("store")->field("store_free_price,freight")->where("store_id = '".$store['store_id']."'")->find();
        $freight = 0.0000;
        if( $storeFreight['store_free_price'] > 0 ){
            if($goodSum['goodNum'] < $storeFreight['store_free_price']){
                $freight+=$storeFreight['freight'];
            }
        }
        return array(
            'store_id'  =>$store['store_id'],
            'store_name'=>$store['store_name'],
            'goodList'  =>$newList,
            'goodMoney' =>$goodSum['goodNum'],
            'freight'   =>number_format($freight,4,".",""),
            'allMoney'  =>number_format($goodSum['goodNum']+$freight,4,".",""),
        );
    }

    /**
     * 提交购物车数据并作出保存
     * User: zhengguiyun
     * Date: 2017/11/28
     * Time: 上午18:30
     * return   array
     */
    public function saveCartGoods($data = array()){
        if(!empty($data) && is_array($data)){
            $checkData = $this->checkCartData($data);
            if($checkData['code'] == '1'){

            }
        }
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



}