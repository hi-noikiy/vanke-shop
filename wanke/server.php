<?php
require_once("./lib/nusoap.php");
define('BASE_PATH',str_replace('\\','/',dirname(__FILE__)));
if (!@include(dirname(dirname(__FILE__)).'/global.php')) exit('global.php isn\'t exists!');
if (!@include(dirname(dirname(__FILE__)).'/webservice.conf.php')) exit('webservice.conf.php isn\'t exists!');
if (!@include(BASE_CORE_PATH.'/base.php')) exit('base.php isn\'t exists!');
Base::runForWebService();
require_once("./webService.ini.php");
require_once("./WebServiceInterceptor.php");
require_once("./util/CommonUtil.php");
require_once("./service/ServiceFactory.php");
#require_once("../core/framework/webService/WebServiceUtil.php");
$server = new soap_server;
$server->soap_defencoding = 'UTF-8';//避免乱码
$server->decode_utf8 = false;
$server->xml_encoding = 'UTF-8';
$server->configureWSDL('sayHello');//打开wsdl支持
$server->configureWSDL('updateOrderStatus');


require_once("./action/sayHello.action.php");
require_once("./action/order.action.php");

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);//service 处理客户端输入的数据

?>
