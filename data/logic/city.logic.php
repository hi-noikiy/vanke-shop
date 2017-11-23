<?php
/**
 * 中心城市
 */

class cityLogic {


    public function __construct() {

    }

    public  function  getCityList(){
        $model = Model('citycentre');
        $cityList = $model->getList(array());

        //每一行有6个中心城市
        $resultCityList = array();
        $tempArray = array();
        $listLength = count($cityList);
        for($temp = 0;$temp < $listLength;$temp++){
            if($temp%4==0 && $temp != 0 ){
                array_push($resultCityList,$tempArray);
                $tempArray = array();
            }
            array_push($tempArray,$cityList[$temp]);//数组尾添加元素
        }
        array_push($resultCityList,$tempArray);
        return $resultCityList;
    }
    
    public  function  getNewCityList($id){
        $model  = Model();
        $cityList=$model->query("SELECT * FROM sc_city_centre where id='$id'");

        //每一行有6个中心城市
        $resultCityList = array();
        $tempArray = array();
        $listLength = count($cityList);
        for($temp = 0;$temp < $listLength;$temp++){
            if($temp%4==0 && $temp != 0 ){
                array_push($resultCityList,$tempArray);
                $tempArray = array();
            }
            array_push($tempArray,$cityList[$temp]);//数组尾添加元素
        }
        array_push($resultCityList,$tempArray);
        return $resultCityList;
    }

    /**
     * 预算查询
     * $cityInfo属性说明
     * p_org_code	必填	城市公司编码
     * p_dept_code	必填	项目编码
     * p_code_combination	必填	预算科目
     */
    public  function  verifyBudget($cityInfo){
        $result = array();
        if(empty($cityInfo['p_org_code'])){
            $result['resultCode']=-1;
            $result['resultMsg']="城市公司编码不能为空";
            return $result;
        }
        if(empty($cityInfo['p_dept_code'])){
            $result['resultCode']=-1;
            $result['resultMsg']="项目编码不能为空";
            return $result;
        }
        if(empty($cityInfo['p_code_combination'])){
            $result['resultCode']=-1;
            $result['resultMsg']="预算科目不能为空";
            return $result;
        }
        $URL = CONTRACT_WS_VERIFY_BUDGET;
        $cityInfo_json = json_encode($cityInfo);
        $result_json = WebServiceUtil::getDataByCurl($URL, $cityInfo_json, 0);

            $result_json='{"resultCode":200,"budgetBalance":999999999}';

        $result = json_decode($result_json,true);
        return $result;
    }
}
