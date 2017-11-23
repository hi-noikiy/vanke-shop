<?php
/**
 * 
 *  推送供应商信息接口
 *  2次认证
 *
 */

class member_storeLogic {
     public function __construct() {
       
    }
  
    /**
     * 向不同系统推送认证供应商信息
     * @param type $member_id   认证供应商商城会员id
     * @param type $role        推送系统角色 如 YMH采购系统
     * @param type $state       会员状态值  默认1  (新增或修改) 0 (失效或删除)
     * @return type
     */
    public function transSupplyInfo($member_id,$city_center,$first_city_start,$state="1"){

        $to_pur_result = array();
        if(empty($member_id)) {
            $to_pur_result['resultCode']=-1;
            return $to_pur_result['resultMsg']="会员id为空";
        }

        //组织数据 
        $supplyinfo = $this->getSupplyInfoByKey($member_id, $state,$city_center);
        if(!empty($supplyinfo['error'])){
            $to_pur_result['resultCode']=-1;
            return $to_pur_result['resultMsg']=$supplyinfo['error'];
        }
        
        //获得EAS编码结果  
        $to_eas_result = $supplyinfo['eas_supply_info'];
        //获取合同结果
        $to_ct_result=$supplyinfo['ct_supply_result'];
        //推送到采购后台
        $TO_PUR_URL = YMA_WEBSERVICE_UPDATE_OR_SAVE_SUPPLIER;
        $supplyinfo_json = json_encode($supplyinfo['ymh_supply_info']);
        $to_pur_result_json = WebServiceUtil::getDataByCurl($TO_PUR_URL, $supplyinfo_json, 0);
        $to_pur_result = json_decode($to_pur_result_json,true);
        CommonUtil::insertData2PushLog($to_pur_result, '', $supplyinfo_json, $TO_PUR_URL, 5);
        
        /*------------------------------------------------------*/
        //处理推送数据    Aletta 
        //1:判定采购
        $model = Model();
        if($first_city_start == '1') {
            $send_data = array(
                //1:判定采购
                'purchase_type' => $to_pur_result['resultCode'] == -1 ? 2 : 1,
                'purchase_res' => $to_pur_result['resultCode'] == -1 ? '推送采购后台系统时错误信息：' . $to_pur_result['resultMsg'] : 'SUCCESS',
                'purchase_time' => time(),
                //2:判定EAS
                'eas_type' => $to_eas_result['resultCode'] != "0000" ? 2 : 1,
                'eas_res' => $to_eas_result['resultCode'] != "0000" ? '获取EAS编码时错误信息：' . $to_eas_result['resultMsg'] : 'SUCCESS',
                'eas_time' => time(),
                //3：判定合同
                'contract_type' => ($to_ct_result['resultCode'] == 201 || $to_ct_result['resultCode']) ? 2 : 1,
                'contract_res' => ($to_ct_result['resultCode'] == 201 || $to_ct_result['resultCode']) ? '推送合同系统时错误信息：' . $to_ct_result['resultMsg'] : 'SUCCESS',
                'contract_time' => time(),
            );
            $model->table('supplier')->where('member_id=' . $member_id)->update($send_data);
        }
        $send_data_frist = array(
            'purchase_type' =>$to_pur_result['resultCode']==-1 ? 2:1,
            'purchase_res'  =>$to_pur_result['resultCode']==-1 ? '推送采购后台系统时错误信息：'.$to_pur_result['resultMsg']:'SUCCESS',
            'purchase_time' =>time(),
            'contract_type' =>($to_ct_result['resultCode']==201 || $to_ct_result['resultCode']) ? 2:1,
            'contract_res'  =>($to_ct_result['resultCode']==201 || $to_ct_result['resultCode']) ? '推送合同系统时错误信息：'.$to_ct_result['resultMsg']:'SUCCESS',
            'contract_time' =>time(),
        );
        $model->table('store_joinin')->where("member_id='".$member_id."' and city_center = '".$city_center."'")->update($send_data_frist);
        /*------------------------------------------------------*/
        
        
        if($to_pur_result['resultCode']==0 &&
            $to_ct_result['resultCode']==200 &&   //合同的返回值
            $to_eas_result['resultCode']=="0000"){//成功
            return $to_pur_result;
        }else if($to_eas_result['resultCode']!="0000"){  //EAS获取出错
            $to_pur_result['resultCode']=-1;
            $to_pur_result['resultMsg']='\n获取EAS编码时错误信息'.$to_eas_result['resultMsg'];
            return $to_pur_result;
        }else if($to_pur_result['resultCode']==0 && ($to_ct_result['resultCode']==201 || $to_ct_result['resultCode']) ){//采购员后台成功，合同失败
            $to_pur_result['resultCode']=-1;
            $to_pur_result['resultMsg']='\n推送合同系统时错误信息:'.$to_ct_result['resultMsg'];
            return $to_pur_result;
        }else if($to_pur_result['resultCode']==-1 && $to_ct_result['resultCode']==200){//采购员后台失败，合同成功
            $to_pur_result['resultMsg']='\n推送采购后台系统时错误信息:'.$to_pur_result['resultMsg'];
            return $to_pur_result;
        }else if($to_pur_result['resultCode']==-1 && ($to_ct_result['resultCode']==201 || $to_ct_result['resultCode']) ){//采购员后台失败，合同失败
            $to_pur_result['resultMsg']='\n推送采购后台系统时错误信息:'.$to_pur_result['resultMsg'].'\n推送合同系统时错误信息:'.$to_ct_result['resultMsg'];
            return $to_pur_result;
        }
        
    }
      /**
     * 通过供应商商城会员id获取供应商全部信息，用于向其他平台系统推送供应商数据
     * (/EAS/eas_code-update-supplier/supply_eas_code-update-member/合同返回的企业编码/contract_code-update-supplier/supply_ht_code-update-member)
     * @param type $key         供应商商城会员id 
     * @param type $state       供应商会员状态 默认1 (新增or修改)  0  (失效or删除)
     * @return type $supplyinfo 返回供应商信息数据  $supplyinfo['error']表示错误信息
     */
    public function getSupplyInfoByKey($key,$state='1',$city_centers){   
        $supplyinfo = array();
        $supplyinfoForYHM = array();
        $supplyinfoForCT = array();
        $supplyinfoForEAS = array();
        $model = new Model();
        $where = array("member_id"=>$key);
        
        $storejoininfo = $model->table("supplier")->where($where)->find();
        
        //获取商户信息数据
//        $supplier_data = $model->table("supplier")->where("member_id = '".$key."'")->find();
        
        $citywhere = array("id"=>$city_centers);
        $cityinfo =  $model->table("city_centre")->where($citywhere)->find(); 
         if(empty($storejoininfo)){
             $supplyinfo['error']= "该会员身份不是供应商";
            return $supplyinfo;
        }
        $supply_org = $this->getCityInfoByMemberId($key,",","bukrs",$_POST['city_id']);
        
        $memberinfo = $model->table("member")->where($where)->find();
        if(empty($memberinfo)||$memberinfo==0){
            $supplyinfo['error']= "找不到该会员信息";
            return $supplyinfo;
        }
        //组织给合同系统的供应商数据
        $areaArray = explode(" ",$storejoininfo['company_address']);
        $supply_province = empty($storejoininfo['company_address'])?" ":$areaArray[0];//省
        $p_org_id=$cityinfo['zt_city_code'];//城市公司名称-->更改为城市公司战图编码
        
        //先固定一个战图编码
        //$p_org_id="W000001";
        //通过战途编码到采购系统获取到城市公司下的所有分公司

        //处理事业本部问题
        if($this->is_https()){
            $dbName = "vs_purchase2";
        }else{
            $dbName = "vs_purchase_t2";
        }
        if($p_org_id == 'W000001'){
            $p_org_id_list = array();
            $p_org_id_data = $model->table("city_centre")->select();
            if(!empty($p_org_id_data) && is_array($p_org_id_data)){
                foreach ($p_org_id_data as $vl){
                    $p_org_id_list[] = "'".$vl['zt_city_code']."'";
                }
                $Eas_Seq_array = Model()->query("select distinct vanke_pj_contract.contract_city_code,vanke_pj_contract.contract_city_name  from ".$dbName.".vanke_pj_contract where vanke_pj_contract.city_code in(".implode(',',$p_org_id_list).")");
            }
        }else{
            $Eas_Seq_array = Model()->query("select distinct vanke_pj_contract.contract_city_code,vanke_pj_contract.contract_city_name  from ".$dbName.".vanke_pj_contract where vanke_pj_contract.city_code= '".$p_org_id."'");   //生产环境地址 ：vs_purchase2 测试环境地址 vs_purchase_t2
        }
        $tmp = array();
        if(!empty($Eas_Seq_array) && is_array($Eas_Seq_array)){
            foreach ($Eas_Seq_array as $vls){
                $tmp[] = array(
                    'p_org_id'=>empty($vls['contract_city_code']) ? 'W000001':$vls['contract_city_code'],
                    'vendor_site_code'=>mb_substr($vls['contract_city_name'],0,5)
                );
            }
        }else{
            $supplyinfo['error']= "城市信息数据有误！";
            return $supplyinfo;
        }
        $supplyinfoForCT['p_org_site']=$tmp;
        $supplyinfoForCT['p_vendor_name']=$storejoininfo['company_name'];//供应商名称
        
        $supplyinfoForCT['p_vendor_number']=empty($storejoininfo['eas_code']) ? $storejoininfo['code']:$storejoininfo['eas_code'];//供应商编码 若不存在eas编码则传供应商编码
        //$supplyinfoForCT['p_vendor_number']=empty($memberinfo['supply_eas_code'])?$memberinfo['supply_code']:$memberinfo['supply_eas_code'];//供应商编码 若不存在eas编码则传供应商编码
        $supplyinfoForCT['p_country']='中国';//国家
        $supplyinfoForCT['p_province']=$supply_province;//省
        $supplyinfoForCT['p_loc_address']=$storejoininfo['company_address'].$storejoininfo['company_address_detail'];//详细地址
        $supplyinfoForCT['p_person_name']=empty($storejoininfo['contacts_name']) ? $memberinfo['member_name']:$storejoininfo['contacts_name'];//联系人
        $supplyinfoForCT['p_tel_number']=empty($storejoininfo['contacts_phone'] ) ? $memberinfo['member_name']:$storejoininfo['contacts_phone'];//联系人电话
        $supplyinfoForCT['p_bank_name']=empty($storejoininfo['bank_name']) ? "":$storejoininfo['bank_name'];//银行名称
        $supplyinfoForCT['p_bank_branch_name']=empty($storejoininfo['bank_name']) ? "":$storejoininfo['bank_name'];//分行
        $supplyinfoForCT['p_bank_account_number']=empty($storejoininfo['bank_account_number']) ? "":preg_replace('# #','',$storejoininfo['bank_account_number']);//银行帐号 格式：正数。如：29394848
        $supplyinfoForCT['p_bank_account_name']=empty($storejoininfo['bank_account_name']) ? "":$storejoininfo['bank_account_name'];//开户姓名：
        $supplyinfo['ct_supply_info'] = $supplyinfoForCT;  
        //推送到合同系统 ,移动到getSupplyInfoByKey中将返回值和eas一样放到$supplyinfo
        $TO_CT_URL = CONTRACT_WS_INSERT_SUPPLIER;
        $supplyinfo_json = json_encode($supplyinfo['ct_supply_info']); 
        $to_ct_result_json = WebServiceUtil::getDataByCurl($TO_CT_URL, $supplyinfo_json, 0);
        $to_ct_result = json_decode($to_ct_result_json,true);
        CommonUtil::insertData2PushLog($to_ct_result, '', $supplyinfo_json, $TO_CT_URL, 15);    
        log::record4inter("日志查看发送给合同的json:".$supplyinfo_json."返回参数".$to_ct_result_json, log::MOBILE_MESSAGE);
        //将会同的返回值放到结果集中
        $supplyinfoForCTS['resultCode'] = $to_ct_result['resultCode'];
        $supplyinfoForCTS['resultMsg']  = $to_ct_result['resultMsg'];//返回信息
        $supplyinfoForCTS['supplierNum']  = $to_ct_result['supplierNum'];//返回信息
        $supplyinfo['ct_supply_result'] =$supplyinfoForCTS;         
        /*----现将合同返回的企业编码存值商户表中    Aletta----*/
        $contract_code_data = array(
            'contract_code' => empty($to_ct_result['supplierNum']) ? $storejoininfo['code']:$to_ct_result['supplierNum']
        );
        $model->table("supplier")->where("member_id = '".$key."'")->update($contract_code_data);
        /*---------------------------------------*/
        //将合同返回的企业编码存到member表中
        $member_data = array(
            'supply_ht_code' =>empty($to_ct_result['supplierNum'])? $memberinfo['supply_code']: $to_ct_result['supplierNum'] );
        $model->table("member")->where($where)->update($member_data);
        //组织给后台采购系统的数据
        $supplyinfoForYHM['supply_code']=$storejoininfo['code'];
        //$supplyinfoForYHM['supply_code']=$memberinfo['supply_code'];
        $supplyinfoForYHM['supply_account']=$memberinfo['member_name'];
        $supplyinfoForYHM['supply_eas_code']= empty($storejoininfo['eas_code']) ? $storejoininfo['code']:$storejoininfo['eas_code'];
        //$supplyinfoForYHM['supply_eas_code']=empty($memberinfo['supply_eas_code'])?"":$memberinfo['supply_eas_code'];
        $supplyinfoForYHM['supply_name']=$storejoininfo['company_name'];
        $supplyinfoForYHM['supply_type']=empty($storejoininfo['supply_type']) ? "":$storejoininfo['supply_type'];
        //$supplyinfoForYHM['supply_type']=empty($memberinfo['supply_type'])?"":$memberinfo['supply_type'];
        $supplyinfoForYHM['supply_org']=$supply_org;
        $supplyinfoForYHM['supply_mobile']=empty($storejoininfo['contacts_phone']) ? "":$storejoininfo['contacts_phone'];
        $supplyinfoForYHM['supply_mail']=empty($storejoininfo['contacts_email']) ? "":$storejoininfo['contacts_email'];
        $supplyinfoForYHM['supply_address']=$storejoininfo['company_address'].$storejoininfo['company_address_detail'];
        $supplyinfoForYHM['glass_state']=$state;
        $supplyinfoForYHM['supplierNum']=empty($to_ct_result['supplierNum']) ? $memberinfo['supply_code']:$to_ct_result['resultCode'];//如果公司名称重复返回企业编号推送eas和采购,为空就传企业证据号码冗余
        $supplyinfo['ymh_supply_info'] = $supplyinfoForYHM;
        //获取供应商EAS编号
        $url = EAS_API_URL;
        $requestDataRQ = $this->getEASparamArray2($storejoininfo['company_name'],$key,$to_ct_result['supplierNum']);
        log::record4inter($requestDataRQ, log::MOBILE_MESSAGE);
        //推送EAS
        $eas_return = WebServiceUtil::getEasBySupply($url, "batchAddSupplierWY", $requestDataRQ);
        log::record4inter("日志查看发送给EAS的json:".$requestDataRQ, log::MOBILE_MESSAGE);
        log::record4inter($requestDataRQ, log::MOBILE_MESSAGE);
        log::record4inter($eas_return, log::MOBILE_MESSAGE);
        $supplyinfoForEAS['resultCode'] = $eas_return['rtnInfo']['errorCode'];//000表示成功
        $supplyinfoForEAS['resultMsg']  = $eas_return['rtnInfo']['errorInfo'];//返回信息
        $supplyinfo['eas_supply_info'] =$supplyinfoForEAS;
      
        /*----现将EAS返回的企业编码存值商户表中    Aletta----*/
        if($eas_return['rtnInfo']['errorCode']=="0000"){
            $eas_code_data = array(
                'eas_code' => $eas_return['rtnInfo']['supplierNumber']
            );
            $model->table("supplier")->where("member_id = '".$key."'")->update($eas_code_data);
        }
        /*---------------------------------------*/
         if($eas_return['rtnInfo']['errorCode']=="0000"){
            $supply_eas_code['supply_eas_code'] = $eas_return['rtnInfo']['supplierNumber']; //供应商组织机构编码
             Model()->table("member")->where($where)->update($supply_eas_code);
        } 
        return $supplyinfo;
    }
        /*
    ------------------------------------------------------
    参数：
    $str_cut    需要截断的字符串
    $length     允许字符串显示的最大长度

    程序功能：截取全角和半角（汉字和英文）混合的字符串以避免乱码
    ------------------------------------------------------
    */
    function substr_cut($str_cut,$length)
    {
        if (strlen($str_cut) > $length)
        {
            for($i=0; $i < $length; $i++)
                if (ord($str_cut[$i]) > 128)    $i++;
            $str_cut = substr($str_cut,0,$i)."..";
        }
        return $str_cut;
    }
    /**
     * 通过供应商会员id获取城市中心名称
     * 一个供应商可能有多个城市中心id 通过每个id获取城市中心名称 以$split分隔符分割
     * @param type $member_id 供应商商城会员id
     * @param type $split     返回的字符串分割符
     * @return string         城市中心名称字符串  多个名称已$split分割
     */
    public function getCityInfoByMemberId($member_id,$split,$field="bukrs",$city_id){
        $where=array();
	$where['member_id']=$member_id;
        $where['city_center']=$city_id;
        $model = new  Model(); 
        $city_idArray = $model->table("store_joinin")->field("city_center")->where($where)->select();
        $city_name = $model->table("city_centre")->field($field)->where(array("id"=>$city_idArray[0]['city_center']))->find();  
            $return   = $city_name[$field];
            for($i = 1;$i<sizeof($city_idArray);$i++){
                $city_name = $model->table("city_centre")->field($field)->where(array("id"=>$city_idArray[$i]['city_center']))->find();
                $return =  $return.$split.$city_name[$field];
            }
         return $return;
    }
    
    public function getEASparamArray($company_name,$member_id){
        $model = Model();
        //获取流水号拼接成带有时间戳的流水号
        $Eas_Seq_array = Model()->query('select  vs_purchase2.getEasSeq()');   //生产环境地址 ：vs_purchase2 测试环境地址 vs_purchase_t2
        $Eas_Seq = $Eas_Seq_array[0]['vs_purchase2.getEasSeq()'];
        
        switch ($Eas_Seq){
            case $Eas_Seq<10:
                $Eas_Seq = "00000".$Eas_Seq;
                break;
            case $Eas_Seq<100:
                $Eas_Seq = "0000".$Eas_Seq;
                break;
            case $Eas_Seq <1000:
                $Eas_Seq = "000".$Eas_Seq;
                break;
            case $Eas_Seq <10000:
                $Eas_Seq = "00".$Eas_Seq;
                break;
            case $Eas_Seq <100000:
                $Eas_Seq = "0".$Eas_Seq;
                break;
        }
        $Eas_seq = date("Ymd",time())."03004".$Eas_Seq;
        log::record4inter("获取到的EAS流水号：".$Eas_seq, log::INFO);
        $supply_code = $model->table("member")->field("supply_code")->where(array("member_id"=>$member_id))->find();
        $supplierInfo = array(
                        "Number"=>$supply_code['supply_code'],      //供应商组织机构编码
                        "Name"  =>$company_name,                    //供应商名称
                        "standardNum" =>"supplierGroupStandard",  //默认
                        "groupNumber" => "WY01",        //默认
                        "orgNumber" =>  "WK"            //默认
        );
        $requestPubProfile=array(
                           "requestInfo"=>array(
                                           "requestID" => "$Eas_seq",       //时间YYYYMMDD+系统编号03004+流水号 流水号取自采购系统存储过程
                                           "correlationID" => "$Eas_seq",  //时间YYYYMMDD+系统编号03004+流水号
                                           "version" => "1.0",//默认
                            )
        
        );
        $batchType = array(
                    "batchInfo" => array(
                                   "dataName" =>"batchAddSupplierWY",   //默认
                                   "dataCount" => "1"           //默认
                    )
        );
        $Systems = array(
                   "system"=>"08001",  //默认
                   "source"=>"03004"   //默认
        );
        $requestDataRQ_1 = array(
                            "supplierInfo" => $supplierInfo,
                            "requestPubProfile" => $requestPubProfile,
                            "batchType" => $batchType,
                            "Systems" => $Systems
        );
        $requestDataRQ =array(
                        "requestDataRQ"=> $requestDataRQ_1
        );
        return $requestDataRQ;
    }

    
     public function getEASparamArray2($company_name,$member_id,$supplierNum){


        $model = Model();
        //获取流水号拼接成带有时间戳的流水号
        $Eas_Seq_array = Model()->query('select  vs_purchase2.getEasSeq()');   //生产环境地址 ：vs_purchase2 测试环境地址 vs_purchase_t2
        $Eas_Seq = $Eas_Seq_array[0]['vs_purchase2.getEasSeq()'];
        
        switch ($Eas_Seq){
            case $Eas_Seq<10:
                $Eas_Seq = "00000".$Eas_Seq;
                break;
            case $Eas_Seq<100:
                $Eas_Seq = "0000".$Eas_Seq;
                break;
            case $Eas_Seq <1000:
                $Eas_Seq = "000".$Eas_Seq;
                break;
            case $Eas_Seq <10000:
                $Eas_Seq = "00".$Eas_Seq;
                break;
            case $Eas_Seq <100000:
                $Eas_Seq = "0".$Eas_Seq;
                break;
        }
        $Eas_seq = date("Ymd",time())."03004".$Eas_Seq;
        log::record4inter("获取到的EAS流水号：".$Eas_seq, log::INFO);
        $supply_code = $model->table("member")->field("supply_code")->where(array("member_id"=>$member_id))->find();
        $supplierInfo = array(
                        "Number"=>empty($supplierNum)?$supply_code['supply_code']:$supplierNum,      //供应商组织机构编码
                        "Name"  =>$company_name,                    //供应商名称
                        "standardNum" =>"supplierGroupStandard",  //默认
                        "groupNumber" => "WY01",        //默认
                        "orgNumber" =>  "WK"            //默认
        );
        $requestPubProfile=array(
                           "requestInfo"=>array(
                                           "requestID" => "$Eas_seq",       //时间YYYYMMDD+系统编号03004+流水号 流水号取自采购系统存储过程
                                           "correlationID" => "$Eas_seq",  //时间YYYYMMDD+系统编号03004+流水号
                                           "version" => "1.0",//默认
                            )
        
        );
        $batchType = array(
                    "batchInfo" => array(
                                   "dataName" =>"batchAddSupplierWY",   //默认
                                   "dataCount" => "1"           //默认
                    )
        );
        $Systems = array(
                   "system"=>"08001",  //默认
                   "source"=>"03004"   //默认
        );
        $requestDataRQ_1 = array(
                            "supplierInfo" => $supplierInfo,
                            "requestPubProfile" => $requestPubProfile,
                            "batchType" => $batchType,
                            "Systems" => $Systems
        );
        $requestDataRQ =array(
                        "requestDataRQ"=> $requestDataRQ_1
        );
        return $requestDataRQ;
    }


    public function is_https(){
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
