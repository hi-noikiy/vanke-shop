<?php
/**
 * 入口文件
 *
 * 统一入口，进行初始化信息
 */

//--------后台采购系统--------
define('YMA_WEBSERVICE_URL_HEAD', 'http://120.77.89.93:8080');//后台采购系统服务器接口模块的地址头
define('YMA_WEBSERVICE_MODULE_PRE', '/impac/restapi');//后台采购系统服务器地址头
define('YMA_WEBSERVICE_RETRIEVE_PRODUCT_STOCK', YMA_WEBSERVICE_URL_HEAD.YMA_WEBSERVICE_MODULE_PRE.'/retrieve/retrieveProductStock');//库存查询
define('YMA_WEBSERVICE_INSERT_AND_UPDATE_PRODUCT', YMA_WEBSERVICE_URL_HEAD.YMA_WEBSERVICE_MODULE_PRE.'/insertAndUpdate/insertAndUpdateProduct');//物料数据给采购
define('YMA_WEBSERVICE_INSERT_AND_UPDATE_PRODUCT_CATEGORY', YMA_WEBSERVICE_URL_HEAD.YMA_WEBSERVICE_MODULE_PRE.'/insertAndUpdate/insertAndUpdateProductCategory');//推送所有商品分类到后台的采购系统
define('YMA_WEBSERVICE_UPDATE_DELIVERY_CHANGE_DATE_AND_STATUS', YMA_WEBSERVICE_URL_HEAD.YMA_WEBSERVICE_MODULE_PRE.'/update/updateDeliveryChangeDateAndStatus');//推送订单状态
define('YMA_WEBSERVICE_INSERT_ORDER', YMA_WEBSERVICE_URL_HEAD.YMA_WEBSERVICE_MODULE_PRE.'/insert/insertOrder');//推送订单
define('YMA_WEBSERVICE_RETRIVE_PURCHASE_ORDER', YMA_WEBSERVICE_URL_HEAD.YMA_WEBSERVICE_MODULE_PRE.'/retrieve/retrievePurchaseOrder');//线下订单获取

define('YMA_WEBSERVICE_DELIVERY_ORDER', YMA_WEBSERVICE_URL_HEAD.YMA_WEBSERVICE_MODULE_PRE.'/update/deliveryOrder');//订单发货
define('YMA_WEBSERVICE_CANCEL_ORDER', YMA_WEBSERVICE_URL_HEAD.YMA_WEBSERVICE_MODULE_PRE.'/update/cancelOrderFromShop');//订单取消
define('YMA_WEBSERVICE_UPDATE_OR_SAVE_SUPPLIER', YMA_WEBSERVICE_URL_HEAD.YMA_WEBSERVICE_MODULE_PRE.'/insert/updateOrSaveSupplier');//认证供应商推送
define('YMA_WEBSERVICE_RETRIEVE_TENDER_INQUERY', YMA_WEBSERVICE_URL_HEAD.YMA_WEBSERVICE_MODULE_PRE.'/retrieve/retrieveTenderInquery');//招投标询价信息获取接口
define('YMA_EMAILRECEPTION', YMA_WEBSERVICE_URL_HEAD.YMA_WEBSERVICE_MODULE_PRE.'/email/sendEmailMallReception');//发送邮件接口
define('YMA_WEBSERVICE_URL_ORDER', 'http://wyworkflow/workflowInterface/k2/Load.aspx?BSID=CG-ORDER&ProcID=17130');//审核地址
define('YMA_WEBSERVICE_SUPPLY_TYPE', YMA_WEBSERVICE_URL_HEAD.YMA_WEBSERVICE_MODULE_PRE.'/update/modifySupplier');//供应商类型修改

define('YMA_IFRAME_URL_HEAD', 'http://120.77.89.93:8080');//后台采购系统嵌入页面地址头
define('IFRAME_TENDER_DEFAULT', YMA_IFRAME_URL_HEAD.'/impac/loginFromUrl.do?forwardUrl=/xl04/ptm/ptm0003_01/PTM0003_01Initial.do&validateFlg=true');//嵌入商城招标默认页面
define('IFRAME_INQUIRY_DEFAULT', YMA_IFRAME_URL_HEAD.'/impac/loginFromUrl.do?forwardUrl=/xl04/spm/spm3001_01/SPM3001_01Initial.do&validateFlg=true');//嵌入商城询价默认页面
define('SYSTEM_USER_NAME','shop1234' );
define('SYSTEM_USER_NAME_DES_KEY','vanke234' );//固定8位字符des key
define('SYSTEM_SITE_DOMAIN','mall.vankeservice.com' );//域名或ip

//--------合同系统--------
define('CONTRACT_WS_URL_HEAD', 'http://10.0.72.19:8080');//合同系统服务器地址头
define('CONTRACT_WS_MODULE_PRE', '/htmapface');//合同系统服务器地址头
define('CONTRACT_WS_INSERT_SUPPLIER', CONTRACT_WS_URL_HEAD.CONTRACT_WS_MODULE_PRE.'/PurchaseController/insertSupplier');//合同系统供应商推送接口URL
define('CONTRACT_WS_INSERT_INVITEM', CONTRACT_WS_URL_HEAD.CONTRACT_WS_MODULE_PRE.'/PurchaseController/insertInvItem');//合同系统物料信息
define('CONTRACT_WS_VERIFY_BUDGET', CONTRACT_WS_URL_HEAD.CONTRACT_WS_MODULE_PRE.'/PurchaseController/verifyBudget');//预算查询


//预算科目库配置

define('DB_TWO_OBJ_ADDRESS', 'rm-wz98r4676h1b44b1f.mysql.rds.aliyuncs.com');//地址 rds5f3gzemdw53bpsnowl.mysql.rds.aliyuncs.com
define('DB_TWO_ROOT_DBNAME', 'purchase');//用户名 purtest
define('DB_TWO_ROOT_PASSWORD', '3fQ7yguRf');//密码 tX1fm5Mk3
define('DB_TWO_ROOT_DB_C', 'vs_purchase_t2');//库 vs_purchase_t2
define('DB_TWO_ROOT_DB_SPORT', '3306');//端口

//助这儿
define('ZZE_API_URL', 'https://vsapp.4009515151.com/');//接口地址
define('ZZE_CLIENT_ID', '0491c0bbfe4e32320e64c2bec4f70c73');//CLIENT_ID
define('ZZE_CLIENT_SECRET', 'ae0ea45eeebf5fddd1a6151ef49ba937');//CLIENT_SECRET
define('ZZE_REDIRECT_URI', 'http://mall.vankeservice.com/');//接口回调地址头部

//EAS
define('EAS_API_URL', 'http://osb.vanke.net.cn:8011/vankeESBPlatform/proxyservices/EAS/09001081000042_ps?wsdl');//接口地址
?>
