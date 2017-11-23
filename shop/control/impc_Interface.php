<?php
/**
 * 接口测试页面
 ***/



class impc_InterfaceControl extends BaseMemberControl{
    public function __construct(){
        parent::__construct() ;
    }
    /**
     * 接口首页
     */
    public function indexOp(){
        Tpl::output('max_recordnum','aaaa');
        Tpl::showpage('impc_Interface.index');
    }
    
    
    /**
     * 接口首页
     */
    public function callWebServiceOp(){      
       $soap = "http://www.webxml.com.cn/WebServices/WeatherWS.asmx?wsdl";
       $client = new SoapClient($soap,array ('trace'=>0,'uri'=>' http://WebXml.com.cn/'));
       $param["theRegionCode"]='000001';
       $rs = $client->__call('getSupportCityString', array($param));
       $result = serialize($rs);
       Tpl::output('max_recordnum',$result);
       Tpl::showpage('impc_Interface.index');
    }

    /**
     * 接口首页
     */
    public function callWebService2Op(){
        require_once("../data/api/emay/nusoaplib/nusoap.php");
//        $client = new soapclient('http://localhost/shopSys/shop/WebServiceServer.php?wsdl');
        $client = new soapclient('http://10.191.6.16/shop/WebServiceServer.php?wsdl');
        $pagram = array('fbbin', 'fbbin@foxmail.com');
        $string = $client->call('getUserInfo', $pagram);
        print_r($string);exit;
    }

    public function callWebService3Op(){
        $soap = "http://localhost/shopSys/shop/WebServiceServer.php?wsdl";
        $client = new SoapClient($soap,array ('trace'=>0,'uri'=>' http://localhost/soap/nusoasp'));
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = false;
        $client->xml_encoding = 'UTF-8';
        $pagram = array('fbbin', 'fbbin@foxmail.com');
        $rs = $client->__call('getUserInfo', $pagram);
        $result = serialize($rs);
        Tpl::output('max_recordnum',$result);
        Tpl::showpage('impc_Interface.index');
    }
    /**
     * 邮件测试
     */
    public function send_emailOp(){ 
//       $email_send = new Email();
       $email_send = new MySendMail();
       $result	= $email_send->send_sys_email("lai_hanzhang@hyrus.com.cn","123","qaz");
       Tpl::output('max_recordnum',$result);
       Tpl::showpage('impc_Interface.index');
    }
    
    
    
    
    

}
