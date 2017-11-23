<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/10
 * Time: 上午11:36
 */
include_once 'Contract.php';
include_once 'Purchase.php';
include_once 'Eas.php';
interface SendFactory {
    public function sendToApi();
}


/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/10
 * Time: 上午11:36
 * 具体抽象合同工厂实现
 */
//推送合同
class ContractFactory {

    function sendToApi($member, $join, $supplier, $city){
        return new Contract($member, $join, $supplier, $city);
    }
}

//推送采购
class PurchaseFactory {
    function sendToApi($member, $supplier, $city){
        return new Purchase($member, $supplier, $city);
    }
}


//推送采购
class EasFactory {
    function sendToApi($member, $supplier, $city){
        return new Eas($member, $supplier, $city);
    }
}
