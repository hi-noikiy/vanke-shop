<?php
/**
 * 商城板块初始化文件
 *
 *
 * *  */

define('BASE_PATH',str_replace('\\','/',dirname(__FILE__)));
if (!@include(dirname(dirname(__FILE__)).'/global.php')) exit('global.php isn\'t exists!');
if (!@include(dirname(dirname(__FILE__)).'/webservice.conf.php')) exit('webservice.conf.php isn\'t exists!');
if (!@include(BASE_CORE_PATH.'/base.php')) exit('base.php isn\'t exists!');
if (!@include(BASE_CORE_PATH.'/framework/webService/WebServiceUtil.php')) exit('WebServiceUtil.php isn\'t exists!');
if (!@include(BASE_CORE_PATH.'/framework/libraries/MySendMail.php')) exit('MySendMail.php isn\'t exists!');
if (!@include(dirname(dirname(__FILE__)).'/wanke/util/CommonUtil.php')) exit('CommonUtil.php isn\'t exists!');
define('TPL_NAME',TPL_ADMIN_NAME);
define('ADMIN_TEMPLATES_URL',ADMIN_SITE_URL.'/templates/'.TPL_NAME);
define('BASE_TPL_PATH',BASE_PATH.'/templates/'.TPL_NAME);

if (!@include(BASE_PATH.'/control/control.php')) exit('control.php isn\'t exists!');

Base::run();
?>