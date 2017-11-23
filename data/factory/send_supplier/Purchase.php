<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/13
 * Time: 上午9:34
 */
include_once 'Send.php';
class Purchase implements Send{

    //状态
    private $state;
    //供应商信息数据
    private $supplier = array();
    //用户信息
    private $member = array();
    //城市中心信息
    private $city_data = array();

    private $send_url = '/impac/restapi/insert/updateOrSaveSupplier';

    public function __construct($member, $supplier, $city, $state='1'){
        $this->model = Model();
        $this->member = $member;
        $this->supplier = $supplier;
        $this->state = $state;
        //获取城市向光数据信息、
        $this->city_data = $this->model->table("city_centre")->where("id = '".$city."'")->find();
    }


    public function sendStart(){
        $rest_data = array(
            'code'  =>'-1',
            'msg'   =>'',
        );
        if(!empty($this->supplier['contract_code'])){
            if(!empty($this->supplier['eas_code'])){
                $rest = $this->sendData();
                $rest_data['msg'] = $rest['msg'];
                $rest_data['code'] = $rest['code'];
            }else{
                $rest_data['msg'] = 'EAS编码不能为空';
            }
        }else{
            $rest_data['msg'] = '合同编码不能为空';
        }
        $join_data['purchase_type'] = $rest_data['code'] == '-1' ? "2":"1";
        $join_data['purchase_type'] = $rest_data['code'] == '-1' ? $rest_data['msg']:"SUCCESS";
        $join_data['purchase_ype'] = time();
        $this->model->table("store_joinin")->where("member_id = '".$this->member['member_id']."' and city_center = '".$this->city."'")->update($join_data);
        return $rest_data;
    }

    //组装推送采购数据
    private function sendData(){
        $data = array(
            'supply_code'       =>$this->supplier['business_licence_number'],//供应商编号(认证时的组织机构代码)
            'supply_account'    =>$this->member['member_name'],//供应商姓名
            'supply_eas_code'   =>empty($this->supplier['eas_code']) ? $this->supplier['code']:$this->supplier['eas_code'],//供应商eas编码
            'supply_name'       =>$this->supplier['company_name'],//	供应商公司名称
            'supply_type'       =>empty($this->supplier['supply_type']) ? "1":$this->supplier['supply_type'],//	供应商类型(1:注册认证，0：后台添加)
            'supply_org'        =>$this->city_data['bukrs'],//	供应商所属城市公司名称
            'supply_mobile'     =>empty($this->supplier['contacts_phone']) ? $this->member['member_name']:$this->supplier['contacts_phone'],//	供应行手机号
            'supply_mail'       =>empty($this->supplier['contacts_email']) ? "":$this->supplier['contacts_email'],//	供应商邮箱
            'supply_address'    =>$this->supplier['company_address'].$this->supplier['company_address_detail'],//	供应商地址
            'glass_state'       =>$this->state,//	状态(1:新增修改，0：删除失效)
            'supplierNum'       =>empty($this->supplier['contract_code']) ? '':$this->supplier['contract_code'],
        );
        //推送到采购后台
        $TO_PUR_URL = YMA_WEBSERVICE_URL_HEAD.$this->send_url;
        $supplyinfo_json = json_encode($data);
        $to_pur_result_json = WebServiceUtil::getDataByCurl($TO_PUR_URL, $supplyinfo_json, 0);
        $to_pur_result = json_decode($to_pur_result_json,true);
        CommonUtil::insertData2PushLog($to_pur_result, '', $supplyinfo_json, $TO_PUR_URL, 5);
        return array(
            'code'=>$to_pur_result['resultCode'] == '0' ? "1":"-1",
            'msg'=>$to_pur_result['resultMsg'],
        );
    }
}