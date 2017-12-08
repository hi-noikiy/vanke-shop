<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/12/6
 * Time: 下午1:58
 */
class base_listControl extends Control {

    public function __construct(){
        Tpl::setLayout('null_layout');
    }

    /**
     * 获取城市数据信息
     * @Author  : Aletta
     * @Time    : 2017-11-23 PM 14:02
     */
    public function cityCenterOp(){
        $list = Model()->table("city_centre")->select();
        $city_json = json_encode($list);
        exit($city_json);
    }

    /**
     * 获取城市数据信息
     * @Author  : Aletta
     * @Time    : 2017-11-23 PM 14:02
     */
    public function cityListOp(){
        $type = isset($_GET["type"]) ? $_GET["type"] : "1";
        $parent_id = isset($_GET["parent_id"]) ? $_GET["parent_id"] : "0";
        $where = "area_parent_id = '".$parent_id."' and area_deep = '".$type."'";
        $list = Model()->table("area")->where($where)->select();
        $provinces_json = json_encode($list);
        exit($provinces_json);
    }
}