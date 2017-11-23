<?php

/**
 * User: lwl
 * Date: 2016/6/29
 * Time: 14:45
 */
require_once("./lib/nusoap.php");
class WebServiceUtil
{
    public static function registerSoapAction($actName){

    }
}
?>
<?php
require_once("./lib/nusoap.php");
require_once("webService.ini.php");
require_once("WebServiceInterceptor.php");

$server = new soap_server;
$server->soap_defencoding = 'UTF-8';
$server->decode_utf8 = false;
$server->xml_encoding = 'UTF-8';

$server->configureWSDL('sayHello');//wsdl
//$server->configureWSDL('updateOrderStatus');//wsdl

/** bool->"xsd:boolean"   string->"xsd:string"int->"xsd:int"    float->"xsd:float"*/
$server->register( 'sayHello', array("name"=>"xsd:string"), array("return"=>"xsd:string") );
//$server->register( 'updateOrderStatus', array("orderNo"=>"xsd:string","key"=>"xsd:string"), array("return"=>"xsd:string") );
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);//service

function sayHello($name) {
    return "Hello, {$name}!";
}

/**
 * @param $orderNo
 * @param $key
 * @return string
 */
function updateOrderStatus($orderNo,$key){
    if(WebServiceInterceptor::check($key)){
        return "orderNo:$orderNo!";
    }
}
?>