<?php
/**
 * User: lwl
 * Date: 2016/6/28
 * Time: 17:09
 */

ini_set("soap.wsdl_cache_enabled", 0);
require_once("../data/api/emay/nusoaplib/nusoap.php");
$server = new soap_server;
$server->soap_defencoding = 'UTF-8';
$server->decode_utf8 = false;
$server->xml_encoding = 'UTF-8';
$server->configureWSDL('nusoasp');
$server->register('getUserInfo', array('name'=>"xsd:string", 'email'=>"xsd:string"), array('return'=>"xsd:string"));
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service( $HTTP_RAW_POST_DATA );
function getUserInfo($name, $email)
{
    return "the data you request!";
}

?>