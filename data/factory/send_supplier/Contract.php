<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/10
 * Time: 上午11:33
 * 推送供应商数据到合同
 */
include_once 'Send.php';
class Contract implements Send {

    private $member_id;

    private $model;
    //认证城市中心ID
    private $city;
    //城市中心信息
    private $city_data = array();
    //供应商信息数据
    private $supplier = array();
    //用户信息
    private $member = array();
    //获取城市战图编码
    private $city_code = array();
    //请求数据地址
    private $send_url = '/htmapface/PurchaseController/insertSupplier';

    public function __construct($member, $join, $supplier, $city){
        $this->model = Model();
        $this->member = $member;
        $this->supplier = $supplier;
        //获取城市向光数据信息、
        $this->city_data = $this->model->table("city_centre")->where("id = '".$city."'")->find();
        $this->city = $city;
    }

    //实现合同的API推送
    public function sendStart(){
        $rest_data = array(
            'code'  =>'-1',
            'msg'   =>'',
        );
        //校验城市信息
        $this->city_code = $this->getCityZTCode();
        if(!empty($this->city_code)){
            $rest = $this->sendData();
            $rest_data['code'] = $rest['resultCode'];
            $rest_data['msg'] = $rest['resultMsg'];
        }else{
            $rest_data['msg'] = '供应商城市信息数据有误！';
        }
        $join_data['contract_type'] = $rest_data['code'] == '-1' ? "2":"1";
        $join_data['contract_res'] = $rest_data['code'] == '-1' ? $rest_data['msg']:"SUCCESS";
        $join_data['contract_time'] = time();
        $this->model->table("store_joinin")->where("member_id = '".$this->member['member_id']."' and city_center = '".$this->city."'")->update($join_data);
        return $rest_data;
    }


    //组装推送合同数据
    private function sendData(){
        $data = array(
            "p_org_site"        =>$this->city_code,                     //城市公司战图编码
            "p_vendor_name"     =>$this->supplier['company_name'],      //供应商名称
            "p_vendor_number"   =>empty($this->supplier['contract_code']) ? $this->supplier['business_licence_number']:$this->supplier['contract_code'],          //供应商编码
            "p_country"         =>"中国",                               //国家
            "p_province"        =>empty($this->supplier['company_address']) ? " ":preg_replace('# #','',$this->supplier['company_address']),//省
            "p_loc_address"     =>$this->supplier['company_address'].$this->supplier['company_address_detail'],//详细地址
            "p_person_name"     =>empty($this->supplier['contacts_name']) ? $this->member['member_name']:$this->supplier['contacts_name'],//联系人
            "p_tel_number"      =>empty($this->supplier['contacts_phone']) ? $this->member['member_name']:$this->supplier['contacts_phone'],//联系人电话
            "p_bank_name"       =>empty($this->supplier['bank_name']) ? "":$this->supplier['bank_name'],//银行名称
            "p_bank_account_number"=>empty($this->supplier['bank_account_number']) ? "":preg_replace('# #','',$this->supplier['bank_account_number']),//银行帐号
            "p_bank_account_name"=>empty($this->supplier['bank_account_name']) ? "":$this->supplier['bank_account_name'],//开户姓名
            "p_bank_branch_name"=>empty($this->supplier['bank_name']) ? "":$this->supplier['bank_name'],//分行
            "p_e_mail"          =>empty($this->supplier['contacts_email']) ? "":$this->supplier['contacts_email'],//联系人邮箱
        );
        //发起推送数据
        $TO_CT_URL = CONTRACT_WS_URL_HEAD.$this->send_url;
        $to_ct_result_json = WebServiceUtil::getDataByCurl($TO_CT_URL, json_encode($data), 0);
        $to_ct_result = json_decode($to_ct_result_json,true);
        log::record4inter("日志查看发送给合同的json:".json_encode($data)."返回参数".$to_ct_result_json, log::MOBILE_MESSAGE);
        //请求成功处理跟新数据
        $join_data = array();
        if($to_ct_result['resultCode']==200) {
            $contract_code_data = array(
                'contract_code' => empty($to_ct_result['supplierNum']) ? $this->supplier['business_licence_number'] : $to_ct_result['supplierNum'],
            );
            $member_data = array(
                'supply_ht_code' => empty($to_ct_result['supplierNum']) ? $this->member['business_licence_number'] : $to_ct_result['supplierNum']
            );
            $this->model->table("supplier")->where("member_id = '" . $this->member['member_id'] . "'")->update($contract_code_data);
            $this->model->table("member")->where("member_id = '" . $this->member['member_id'] . "'")->update($member_data);
        }
        return array(
            'code'  =>$to_ct_result['resultCode']==200 ? "-1":"1",
            'msg'   =>$to_ct_result['resultCode']==200 ? "SUCCESS":$to_ct_result['resultMsg'],
        );
    }

    //获取城市公司的战图数据信息
    private function getCityZTCode(){
        if($this->is_https()){
            $dbName = "vs_purchase2";
        }else{
            $dbName = "vs_purchase_t2";
        }
        if($this->city_data['zt_city_code'] == 'W000001'){
            $all_list = array();
            $all_city = $this->model->table("city_centre")->select();
            if(!empty($all_city) && is_array($all_city)){
                foreach ($all_city as $vl){
                    $p_org_id_list[] = "'".$vl['zt_city_code']."'";
                }
                $sql = "select distinct vanke_pj_contract.contract_city_code,vanke_pj_contract.contract_city_name  from ".$dbName.".vanke_pj_contract where vanke_pj_contract.city_code in(".implode(',',$p_org_id_list).")";
                $Eas_Seq_array = Model()->query($sql);
            }
        }else{
            $sql = "select distinct vanke_pj_contract.contract_city_code,vanke_pj_contract.contract_city_name  from ".$dbName.".vanke_pj_contract where vanke_pj_contract.city_code= '".$this->city_data['zt_city_code']."'";
            $Eas_Seq_array = Model()->query($sql);   //生产环境地址 ：vs_purchase2 测试环境地址 vs_purchase_t2
        }
        $tmp = array();
        if(!empty($Eas_Seq_array) && is_array($Eas_Seq_array)){
            foreach ($Eas_Seq_array as $vls){
                $tmp[] = array(
                    'p_org_id'=>empty($vls['contract_city_code']) ? 'W000001':$vls['contract_city_code'],
                    'vendor_site_code'=>mb_substr($vls['contract_city_name'],0,5)
                );
            }
        }
        return $tmp;
    }



    private function is_https(){
        if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
            return true;
        } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }

}

