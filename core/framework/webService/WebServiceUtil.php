<?php

/**
 * Created by PhpStorm.
 * Date: 2016/7/7
 * Time: 19:44
 */
require_once(BASE_ROOT_PATH."/wanke/lib/nusoap_eas.php");
class WebServiceUtil
{   /**
 * 向远程服务器请求数据
 * 数据格式要求根据远程服务端的需求来制作json
 * 当成功执行的时候返回得到的json
 * @param type $url  请求的url
 * @param type $data 需要发送的数据(格式化之后)json
 * @param type $type 0表示 POST 1 表示GET
 * @return josn :resultCode 0表示成功 -1 表示失败   resultMsg   resultData  返回的数据
 */
    public  static function getDataByCurl($url,$data,$type){
        try {
            log::record4inter("远程服务器的url：".$url, log::INFO);
            log::record4inter("向远程服务器发送的json数据：".$data, log::INFO);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            if($type=='0'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array( 
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
            );          
            $returndata = curl_exec($ch);//运行curl
            log::record4inter("远程服务器返回的json数据：".$returndata."\n\n\n", log::INFO);
            if(empty($returndata)){
                $return = array(
                        'resultCode'=>'-1',
                        'resultMsg'=>$url.'请求无响应',
                        'resultData'=>''
                );
                log::record4inter("远程服务器的url：".$url.".\n"."向远程服务器发送的json数据：".$data.".\n"."远程服务器返回为空,请检查url是否正确",log::ERR);
                return json_encode($return);
            }
        }catch (Exception $e){
            $return = array(
                        'resultCode'=>'-1',
                        'resultMsg'=>'请求发送失败',
                        'resultData'=>''
            );
            log::record4inter("调用服务器异常：".$e->getMessage(),log::ERR);
            return json_encode($return);
        }
        return $returndata;
        
    }
    public static  function getEasBySupply($url,$functionName,$requestDataRQ){  
        header("Content-type:text/html;charset=utf-8");
        $client = new soapclient_eas($url);
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = false;
        $client->xml_encoding = 'UTF-8';
        log::record4inter("请求远程服务器的url：".$url, log::INFO);
        $requestDataRQ_json = json_encode($requestDataRQ);
        log::record4inter("发送给远程服务器数据：".$requestDataRQ_json, log::INFO);
        $result=$client->call($functionName,$requestDataRQ);
        log::record4inter("请求远程服务器数据：  返回代码：".$result['rtnInfo']['errorCode'].",返回信息：".$result['rtnInfo']['errorInfo']."\n\n\n", log::INFO);
        return $result;
    }
}
?>