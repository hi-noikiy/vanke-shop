<?php
/**
 * password="tX1fm5Mk3"
type="javax.sql.DataSource"
url="jdbc:p6spy:mysql://rds5f3gzemdw53bpsnowl.mysql.rds.aliyuncs.com:3306/vs_purchase_t2"
username="purtest"/>
 **/

$config = array();
$config['base_site_url'] 		= 'http://www.wkshop.com';
$config['member_site_url'] 		= 'http://www.wkshop.com';
$config['shop_site_url'] 		= 'http://www.wkshop.com/shop';
$config['admin_site_url'] 		= 'http://www.wkshop.com/admin';
$config['mobile_site_url'] 		= 'http://www.wkshop.com/mobile';
$config['wap_site_url'] 		= 'http://www.wkshop.com/wap';
$config['chat_site_url'] 		= 'http://www.wkshop.com/chat';
$config['node_site_url'] 		= 'http://www.wkshop.com:8090';
$config['upload_site_url']		= 'http://www.wkshop.com/data/upload';
$config['resource_site_url']	= 'http://www.wkshop.com/data/resource';
$config['version'] 		= '201510121201';
$config['setup_date'] 	= '2015-11-19 15:02:07';
$config['gip'] 			= 0;
$config['dbdriver'] 	= 'mysqli';
$config['tablepre']		= 'sc_';
$config['db']['1']['dbhost']       = '127.0.0.1';//'rds5f3gzemdw53bpsnowl.mysql.rds.aliyuncs.com';//'10.39.35.16';
$config['db']['1']['dbport']       = '3306';
$config['db']['1']['dbuser']       = 'root';//'purtest';
$config['db']['1']['dbpwd']        = 'zgyyxm1314';//'tX1fm5Mk3';
$config['db']['1']['dbname']       = 'wk_caigou';//'vs_purchase_t1';//'vs_purchase_t1';//'wk_line_c';
$config['db']['1']['dbcharset']    = 'UTF-8';
$config['db']['slave']                  = $config['db']['master'];
$config['session_expire'] 	= 3600;
$config['lang_type'] 		= 'zh_cn';
$config['cookie_pre'] 		= '0F47_';
$config['thumb']['cut_type'] = 'gd';
$config['thumb']['impath'] = '';
$config['cache']['type'] 			= 'file';
//$config['redis']['prefix']      	= 'nc_';
//$config['redis']['master']['port']     	= 6379;
//$config['redis']['master']['host']     	= '127.0.0.1';
//$config['redis']['master']['pconnect'] 	= 0;
//$config['redis']['slave']      	    = array();
//$config['fullindexer']['open']      = false;
//$config['fullindexer']['appname']   = 'abc';
$config['debug'] 			= false;
$config['default_store_id'] = '1';
$config['url_model'] = true;
$config['subdomain_suffix'] = '';
//$config['session_type'] = 'redis';
//$config['session_save_path'] = 'tcp://127.0.0.1:6379';
$config['node_chat'] = true;
//流量记录表数量，为1~10之间的数字，默认为3，数字设置完成后请不要轻易修改，否则可能造成流量统计功能数据错误
$config['flowstat_tablenum'] = 3;
$config['sms']['gwUrl'] = 'https://sdkhttp.eucp.b2m.cn/sdk/SDKService';
$config['sms']['serialNumber'] = '';
$config['sms']['password'] = '';
$config['sms']['sessionKey'] = '';
$config['queue']['open'] = false;
$config['queue']['host'] = '127.0.0.1';
$config['queue']['port'] = 6379;
$config['cache_open'] = false;
$config['delivery_site_url']    = 'http://www.wkshop.com/delivery';
$config['cms_site_url'] 		= 'http://www.wkshop.com/cms';
$config['microshop_site_url'] 	= 'http://www.wkshop.com/microshop';
$config['circle_site_url'] 		= 'http://www.wkshop.com/circle';
return $config;
