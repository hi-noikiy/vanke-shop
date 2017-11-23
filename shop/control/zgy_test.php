<?php
header("Content-type: text/html; charset=utf-8");
class zgy_testControl extends BaseHomeControl {
    private $times;

    private $end_time;

    private $stat_time;

    private $model;

    public function __construct(){
        $this->model = Model();
        $this->times = time();
        $this->stat_time = strtotime(date('Y-m-d',time())) - 86400;
        $this->end_time = strtotime(date('Y-m-d',time())) - 1;
    }

    public function indexOp(){
        $member_storeLogic = 'a:6:{s:5:"phone";s:11:"18896541112";s:9:"mob_phone";s:11:"18896541112";s:9:"tel_phone";s:0:"";s:7:"address";s:103:"江苏	苏州市	苏州工业园区 工业园区星汉街108号湖左岸花园幸福驿站2楼办公室";s:4:"area";s:35:"江苏	苏州市	苏州工业园区";s:6:"street";s:67:"工业园区星汉街108号湖左岸花园幸福驿站2楼办公室";}';
        var_dump(unserialize($member_storeLogic));
    }

    public function getCityInfoByMemberId($member_id,$split,$field="bukrs",$city_id){
        $where=array();
        $where['member_id']=$member_id;
        $where['city_center']=$city_id;
        $model = new  Model();
        $city_idArray = $model->table("store_joinin")->field("city_center")->where($where)->select();
        $city_name = $model->table("city_centre")->field($field)->where(array("id"=>$city_idArray[0]['city_center']))->find();
        var_dump($city_idArray);
        $return   = $city_name[$field];
        for($i = 1;$i<sizeof($city_idArray);$i++){
            $city_name = $model->table("city_centre")->field($field)->where(array("id"=>$city_idArray[$i]['city_center']))->find();
            $return =  $return.$split.$city_name[$field];
        }
        return $return;
    }


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
        //$supply_org = $this->getCityInfoByMemberId($key,",","bukrs",$_POST['city_id']);

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
        if($p_org_id == 'W000001'){
            $p_org_id_list = array();
            $p_org_id_data = $model->table("city_centre")->select();
            if(!empty($p_org_id_data) && is_array($p_org_id_data)){
                foreach ($p_org_id_data as $vl){
                    $p_org_id_list[] = "'".$vl['zt_city_code']."'";
                }
                $Eas_Seq_array = Model()->query("select distinct vanke_pj_contract.contract_city_code,vanke_pj_contract.contract_city_name  from vs_purchase2.vanke_pj_contract where vanke_pj_contract.city_code in(".implode(',',$p_org_id_list).")");
            }
        }else{
            $Eas_Seq_array = Model()->query("select distinct vanke_pj_contract.contract_city_code,vanke_pj_contract.contract_city_name  from vs_purchase2.vanke_pj_contract where vanke_pj_contract.city_code= '".$p_org_id."'");   //生产环境地址 ：vs_purchase2 测试环境地址 vs_purchase_t2
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
        $TO_CT_URL = "10.0.57.108:8080/htmapface/PurchaseController/insertSupplier";
        $supplyinfo_json = json_encode($supplyinfo['ct_supply_info']);
        echo json_encode($supplyinfo['ct_supply_info']);
        $to_ct_result_json = WebServiceUtil::getDataByCurl($TO_CT_URL, $supplyinfo_json, 0);
        var_dump($to_ct_result_json);
        $to_ct_result = json_decode($to_ct_result_json,true);
        return $to_ct_result;
    }


    private function getTime(){
        $list = $this->model->table('statistics_order')->order("add_time desc")->find();
        return empty($list) ? '':$list['add_time'];
    }


    private function getWhere(){
        if(empty($this->getTime())){
            $where = "add_time <= '".$this->end_time."'";
        }else{
            $where = "add_time <= '".$this->end_time."' and add_time >= '".$this->getTime()."'";
        }
    }

    private function getOrderList(){
        if($this->check_order()){
            //
        }else{

        }

    }
	
}


class Timer{
    private $startTime = 0; //保存脚本开始执行时的时间（以微秒的形式保存）
    private $stopTime = 0; //保存脚本结束执行时的时间（以微秒的形式保存）

    //在脚本开始处调用获取脚本开始时间的微秒值
    function start()
    {
        $this->startTime = microtime(true); //将获取的时间赋值给成员属性$startTime
    }

    //脚本结束处嗲用脚本结束的时间微秒值
    function stop()
    {
        $this->stopTime = microtime(true); //将获取的时间赋给成员属性$stopTime
    }

    //返回同一脚本中两次获取时间的差值
    function spent()
    {
        //计算后4舍5入保留4位返回
        return round(($this->stopTime - $this->startTime), 4);
    }


/*$timer= new Timer();
$timer->start();
usleep(1000);
$timer->stop();
echo "执行本次操作用时<b>".$timer->spent()."</b>秒";*/
}
?>