<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/12/15
 * Time: 上午11:36
 */

class supplierModel extends Model{


    public function __construct(){
        parent::__construct('supplier');
    }

    /**
     * 读取供应商列表认证数据列表
     * @param array $condition
     *
     */
    public function join_list($condition,$page='',$order='',$field='*'){
        $on = "store_joinin.member_id = supplier.member_id,";
        $on.= "city_centre.id = store_joinin.city_center,";
        $result = $this->table('store_joinin,supplier,city_centre')->field($field)->join('left,left')->on($on)->where($condition)->page($page)->order($order)->select();
        return $result;
    }

    /**
     * 读取供应商认证城市公司的详细数据
     * @param array $condition
     *
     */
    public function join_detail($condition,$field='*'){
        $on = "store_joinin.member_id = supplier.member_id,";
        $on.= "city_centre.id = store_joinin.city_center,";
        $on.= "supplier_information.member_id = store_joinin.member_id and supplier_information.join_city = store_joinin.city_center";
        $result = $this->table('store_joinin,supplier,city_centre,supplier_information')->field($field)->join('left,left,left')->on($on)->where($condition)->find();
        return $result;
    }
}