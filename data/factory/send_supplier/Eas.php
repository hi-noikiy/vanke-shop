<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/13
 * Time: 上午11:43
 */
include_once 'Send.php';
class Eas implements Send{

    //供应商信息数据
    private $supplier = array();
    //用户信息
    private $member = array();
    //城市中心信息
    private $city_data = array();

    private $send_url = EAS_API_URL;

    private $city;

    public function __construct($member, $supplier, $city){
        $this->model = Model();
        $this->member = $member;
        $this->supplier = $supplier;
        $this->city = $city;
    }

    public function sendStart(){
        //判定是否需要向EAS推送
        if($this->city == $this->supplier['first_city_id']){
            $rest_data = array(
                'code'  =>'-1',
                'msg'   =>'',
            );
            if(!empty($this->supplier['contract_code'])){
                $rest = $this->getData();
                $rest_data['msg'] = $rest['msg'];
                $rest_data['code'] = $rest['code'];
            }else{
                $rest_data['msg'] = '合同编码不能为空';
            }
            $join_data['eas_type'] = $rest_data['code'] == '-1' ? "2":"1";
            $join_data['eas_type'] = $rest_data['code'] == '-1' ? $rest_data['msg']:"SUCCESS";
            $join_data['eas_type'] = time();
            $this->model->table("supplier")->where("member_id = '".$this->member['member_id']."'")->update($join_data);
            return $rest_data;
        }else{
            return array('code'=>'1','msg'=>'');
        }
    }


    private function getData(){
        $data = array(
            "supplierInfo"      =>array(
                "Number"        =>$this->supplier['contract_code'],      //供应商组织机构编码
                "Name"          =>$this->supplier['company_name'],                    //供应商名称
                "standardNum"   =>"supplierGroupStandard",  //默认
                "groupNumber"   => "WY01",        //默认
                "orgNumber"     =>  "WK"            //默认
            ),
            "requestPubProfile" => array(
                "requestInfo"=>array(
                    "requestID"     => $this->getEasCode(),       //时间YYYYMMDD+系统编号03004+流水号 流水号取自采购系统存储过程
                    "correlationID" => $this->getEasCode(),  //时间YYYYMMDD+系统编号03004+流水号
                    "version"       => "1.0",//默认
                )
            ),
            "batchType"         => array(
                "batchInfo" => array(
                    "dataName"  =>"batchAddSupplierWY",   //默认
                    "dataCount" => "1"           //默认
                ),
            ),
            "Systems"           => array(
                "system"=>"08001",  //默认
                "source"=>"03004"   //默认
            ),
        );
        $url = $this->send_url;
        log::record4inter(array("requestDataRQ"=> $data), log::MOBILE_MESSAGE);
        //推送EAS
        $eas_return = WebServiceUtil::getEasBySupply($url, "batchAddSupplierWY", array("requestDataRQ"=> $data));
        log::record4inter("日志查看发送给EAS的json:".json_encode(array("requestDataRQ"=> $data)), log::MOBILE_MESSAGE);
        log::record4inter(json_encode($eas_return), log::MOBILE_MESSAGE);
        if($eas_return['rtnInfo']['errorCode']){
            $rest['code'] = '1';//000表示成功
            $rest['msg']  = 'success';//返回信息
            $eas_code_data = array(
                'eas_code' => $eas_return['rtnInfo']['supplierNumber']
            );
            $this->model->table("supplier")->where("member_id = '".$this->member['member_id']."'")->update($eas_code_data);

            $supply_eas_code['supply_eas_code'] = $eas_return['rtnInfo']['supplierNumber']; //供应商组织机构编码
            $this->model->table("member")->where("member_id = '".$this->member['member_id']."'")->update($supply_eas_code);
        }else{
            $rest['code'] = '-1';//000表示成功
            $rest['msg']  = $eas_return['rtnInfo']['errorInfo'];//返回信息
        }
        return array("requestDataRQ"=> $data);
    }


    private function getEasCode(){
        if($this->is_https()){
            $sql = "select vs_purchase2.getEasSeq() as code";
        }else{
            $sql = "select vs_purchase_t2.getEasSeq()  as code";
        }
        $Eas_Seq_array = Model()->query($sql);
        $code = $Eas_Seq_array[0]['code'];
        switch ($code){
            case $code<10:
                $code_num = "00000".$code;
                break;
            case $code<100:
                $code_num = "0000".$code;
                break;
            case $code <1000:
                $code_num = "000".$code;
                break;
            case $code <10000:
                $code_num = "00".$code;
                break;
            case $code <100000:
                $code_num = "0".$code;
                break;
        }
        $Eas_seq = date("Ymd",time())."03004".$code_num;
        log::record4inter("获取到的EAS流水号：".$Eas_seq, log::INFO);
        return $Eas_seq;
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