<?php
header("Content-type:text/html;charset=utf-8");
require_once("./lib/nusoap.php");

$client = new soapclient('http://osbtest.vanke.net.cn:8011/vankeESBPlatform/proxyservices/EAS/09001081000042_ps?wsdl');
$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = false;
$client->xml_encoding = 'UTF-8';

$paras=array('number'=>'3736','name'=>'山河建设集团有限公司','standardNum'=>'supplierGroupStandard','groupNumber'=>'WK01','orgNumber'=>'027DC14');

$result=$client->call('batchAddSupplierWY',$paras);
print_r($result);
exit;
$client = new soapclient('http://10.191.6.16/wanke/server.php');
$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = false;
$client->xml_encoding = 'UTF-8';

//参数转为数组形式传递
//$paras=array('name'=>'Bruce Lee');
//$result=$client->call('sayHello',$paras);
$paras=array('orderSn'=>'8000000000044801','orderState'=>'95','key'=>'wanke1234564');
$result=$client->call('updateOrderStatus',$paras);//目标方法没有参数时，可省略后面的参数
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