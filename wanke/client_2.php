<?php
header("Content-type:text/html;charset=utf-8");
require_once("./lib/nusoap.php");
@include('../data/config/config.ini.php');
$client = new soapclient($config['base_site_url'].'/wanke/server.php');

$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = false;
$client->xml_encoding = 'UTF-8';

//参数转为数组形式传递
//$paras=array('name'=>'Bruce Lee');
//$result=$client->call('sayHello',$paras);
$orderSn=$_GET['orderSn'];
$orderState=$_GET['orderState'];
$paras=array('orderSn'=>$orderSn,'orderState'=>$orderState,'key'=>'wanke1234564');
$result=$client->call('updateOrderStatusForCTSys',$paras);//目标方法没有参数时，可省略后面的参数
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