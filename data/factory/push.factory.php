<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/10
 * Time: 上午11:23
 * 分别向三个系统推送数据，为了保证数据的准确，推送的顺序依次为合同-》EAS-》采购，也可以单独进行推送
 */
include 'send_supplier/SendFactory.php';
class pushFactory {


    private $model;

    private $member;

    private $join;

    private $supplier;

    public function __construct(){
        $this->model = Model();
    }

    /**
     * 向合同系统推送供应商数据信息
     * User: zhengguiyun
     * Date: 2017/11/10
     * Time: 上午11:23
     * $member  int     供应商的用户ID
     * $city    int     供应商需要认证的城市公司
     * return   array   'code'  1：成功，-1：失败
     *                  'msg'   错误信息，success：成功
     */
    public function sendContract($member,$city){
        $err = $this->getData($member,$city);
        if($err['code'] == '1'){
            $Factory =  new  ContractFactory;
            $toContract = $Factory->sendToApi($this->member,$this->join,$this->supplier,$city);
            return $toContract->sendStart();
        }else{
            return $err;
        }
    }


    /**
     * 向采购系统推送供应商数据信息
     * User: zhengguiyun
     * Date: 2017/11/10
     * Time: 上午11:23
     * $member  int     供应商的用户ID
     * $city    int     供应商需要认证的城市公司
     * return   array   'code'  1：成功，-1：失败
     *                  'msg'   错误信息，success：成功
     */
    public function sendPurchase($member, $city){
        $rest = array(
            'code' =>'-1',
            'msg' =>'',
        );
        if(!empty($member)){
            $this->member = $this->getMember($member);
            if(!empty($this->member)){
                $this->supplier = $this->getSupplier($member);
                if(!empty($this->supplier)){
                    $Factory =  new  PurchaseFactory;
                    $toPurchase = $Factory->sendToApi($this->member,$this->supplier, $city);
                    return $toPurchase->sendStart();
                }else{
                    $rest['msg'] = "供应商信息异常";
                }
            }else{
                $rest['msg'] = "供应商用户的用户信息异常";
            }
        }else{
            $rest['msg'] = "参数信息异常";
        }
        return $rest;
    }

    /**
     * 向EAS系统推送供应商数据信息
     * User: zhengguiyun
     * Date: 2017/11/10
     * Time: 上午11:23
     * $member  int     供应商的用户ID
     * return   array   'code'  1：成功，-1：失败
     *                  'msg'   错误信息，success：成功
     */
    public function sendEas($member, $city){
        $rest = array(
            'code' =>'-1',
            'msg' =>'',
        );
        if(!empty($member)){
            $this->member = $this->getMember($member);
            if(!empty($this->member)){
                $this->supplier = $this->getSupplier($member);
                if(!empty($this->supplier)){
                    $Factory =  new  EasFactory;
                    $toPurchase = $Factory->sendToApi($this->member,$this->supplier, $city);
                    return $toPurchase->sendStart();
                }else{
                    $rest['msg'] = "供应商信息异常";
                }
            }else{
                $rest['msg'] = "供应商用户的用户信息异常";
            }
        }else{
            $rest['msg'] = "参数信息异常";
        }
        return $rest;
    }

    //将用户的信息进行处理
    private function getData($member,$city){
        $rest = array(
            'code' =>'-1',
            'msg' =>'',
        );
        if(!empty($member) && !empty($city)){
            //查询用户数据信息，获取用户的基本信息
            $this->member = $this->getMember($member);
            if(!empty($this->member)){
                if(!empty($this->member)){
                    $this->join = $this->getSupplierJoin($member,$city);
                    if(!empty($this->join)){
                        $this->supplier = $this->getSupplier($member);
                        if(!empty($this->supplier)){
                            $rest['code'] = '1';
                        }else{
                            $rest['msg'] = "供应商信息异常";
                        }
                    }else{
                        $rest['msg'] = "供应商认证信息异常";
                    }
                }else{
                    $rest['msg'] = "供应商用户的用户信息异常";
                }
            }else{
                $rest['msg'] = "供应商用户的用户信息异常";
            }
        }else{
            $rest['msg'] = "参数信息异常";
        }
        return $rest;
    }

    private function getMember($member){
        return $this->model->table('member')->where("member_id = '".$member."'")->find();
    }


    private function getSupplierJoin($member,$city){
        $where = "member_id = '".$member."' ";
        $where.= "and city_center = '".$city."'";
        $where.= "and joinin_state in('".STORE_JOIN_STATE_RZ."','".STORE_JOIN_STATE_VERIFY_FAIL."','".STORE_JOIN_STATE_RZSUCCESS."')";
        return $this->model->table('store_joinin')->where($where)->find();
    }

    private function getSupplier($member){
        return $this->model->table('supplier')->where("member_id = '".$member."'")->find();
    }



}
