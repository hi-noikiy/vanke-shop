<?php
header("Content-type:text/html;charset=utf-8");
require_once("./lib/nusoap_eas.php");

$client = new soapclient_eas('http://osbtest.vanke.net.cn:8011/vankeESBPlatform/proxyservices/EAS/09001081000042_ps?wsdl');


$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = false;
$client->xml_encoding = 'UTF-8';
$paras=array('number'=>'3736','name'=>'山河建设集团有限公司','standardNum'=>'supplierGroupStandard','groupNumber'=>'WK01','orgNumber'=>'027DC14');
$supplierInfo = array(
    "Number"=>"3736",
    "Name"  =>"山河建设集团有限公司",
    "standardNum" =>"supplierGroupStandard",
    "groupNumber" => "WK01",
    "orgNumber" =>  "027DC14"
 );
$requestPubProfile=array(
    "requestInfo"=>array(
        "requestID" => "?",
        "correlationID" => "?",
        "version" => "?",
    )
);
$batchType = array(
    "batchInfo" => array(
        "dataName" =>"?",
        "dataCount" => "1"
    )
);
$Systems = array(
    "system"=>"?",
    "source"=>"?"
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

$result=$client->call('batchAddSupplierWY',$requestDataRQ);
var_dump($result);exit;
if (!$err=$client->getError()) {
    echo "返回结果：";
    foreach($result as $key=>$value){
        echo $key."=>".$value."\n";
    }
} else {
    echo "调用出错：",$err;
    echo '<h2>Request</h2>';
    echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
    echo '<h2>Response</h2>';
    echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
}
?>