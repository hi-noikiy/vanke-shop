<?php
/**
 * 入口文件
 *
 * 统一入口，进行初始化信息
 */
error_reporting(E_ALL & ~E_NOTICE);
define('BASE_ROOT_PATH',str_replace('\\','/',dirname(__FILE__)));
define('BASE_CORE_PATH',BASE_ROOT_PATH.'/core');
define('BASE_DATA_PATH',BASE_ROOT_PATH.'/data');
define('BASE_WEBSERVICE_PATH',BASE_ROOT_PATH.'/wanke');
define('DS','/');
define('StartTime',microtime(true));
define('TIMESTAMP',time());
define('DIR_SHOP','shop');
define('DIR_ADMIN','admin');
define('DIR_API','api');
define('DIR_MOBILE','mobile');
define('DIR_WAP','wap');
define('DIR_RESOURCE','data/resource');
define('DIR_UPLOAD','data/upload');
define('ATTACH_PATH','shop');
define('ATTACH_COMMON',ATTACH_PATH.'/common');
define('ATTACH_AVATAR',ATTACH_PATH.'/avatar');
define('ATTACH_EDITOR',ATTACH_PATH.'/editor');
define('ATTACH_MEMBERTAG',ATTACH_PATH.'/membertag');
define('ATTACH_STORE',ATTACH_PATH.'/store');
define('ATTACH_GOODS',ATTACH_PATH.'/store/goods');
define('ATTACH_STORE_DECORATION',ATTACH_PATH.'/store/decoration');
define('ATTACH_LOGIN',ATTACH_PATH.'/login');
define('ATTACH_WAYBILL',ATTACH_PATH.'/waybill');
define('ATTACH_ARTICLE',ATTACH_PATH.'/article');
define('ATTACH_BRAND',ATTACH_PATH.'/brand');
define('ATTACH_GOODS_CLASS','shop/goods_class');
define('ATTACH_DELIVERY','/delivery');
define('ATTACH_ADV',ATTACH_PATH.'/adv');
define('ATTACH_ACTIVITY',ATTACH_PATH.'/activity');
define('ATTACH_WATERMARK',ATTACH_PATH.'/watermark');
define('ATTACH_POINTPROD',ATTACH_PATH.'/pointprod');
define('ATTACH_GROUPBUY',ATTACH_PATH.'/groupbuy');
define('ATTACH_LIVE_GROUPBUY',ATTACH_PATH.'/livegroupbuy');
define('ATTACH_SLIDE',ATTACH_PATH.'/store/slide');
define('ATTACH_VOUCHER',ATTACH_PATH.'/voucher');
define('ATTACH_STORE_JOININ',ATTACH_PATH.'/store_joinin');
define('ATTACH_REC_POSITION',ATTACH_PATH.'/rec_position');
define('ATTACH_MOBILE','mobile');
define('ATTACH_CIRCLE','circle');
define('ATTACH_CMS','cms');
define('ATTACH_LIVE','live');
define('ATTACH_MALBUM',ATTACH_PATH.'/member');
define('ATTACH_MICROSHOP','microshop');
define('TPL_SHOP_NAME','default');
define('TPL_CIRCLE_NAME', 'default');
define('TPL_MICROSHOP_NAME', 'default');
define('TPL_CMS_NAME', 'default');
define('TPL_ADMIN_NAME', 'default');
define('TPL_ADMIN_NAME', 'default');
define('TPL_DELIVERY_NAME', 'default');
define('TPL_MEMBER_NAME', 'default');
define('ADMIN_PWD_WHEN_LONG', 180);//管理员密码时长（天）
define('DEFAULT_CONNECT_SMS_TIME', 60);//倒计时时间



/*
 * 商家入驻状态定义
 */
//供应商默认时长
define('SUPPLY_TIME_LONG',365);//默认时长，单位（/天）
//登录时长
define('LOGIN_SESSION_TIME',36000);
//新申请
define('STORE_JOIN_STATE_NEW', 10);
//完成付款
define('STORE_JOIN_STATE_PAY', 11);
//初审成功
define('STORE_JOIN_STATE_VERIFY_SUCCESS', 20);
//初审失败
define('STORE_JOIN_STATE_VERIFY_FAIL', 30);
//付款审核失败
define('STORE_JOIN_STATE_PAY_FAIL', 31);

//回退修改状态
define('STORE_JOIN_STATE_CALLBACK', 32);

//未认证邮箱
define('STORE_JOIN_STATE_EMAIL', 42);

//开店成功
define('STORE_JOIN_STATE_FINAL', 40);

//认证申请
define('STORE_JOIN_STATE_RZ', 43);
//认证成功
define('STORE_JOIN_STATE_RZSUCCESS', 44);

define('STORE_JOIN_STATE_FNO', 45);
//开店申请
define('STORE_JOIN_STATE_RZHKD', 34);
//开店拒绝
define('STORE_JOIN_STATE_KDJJ', 41);

//默认颜色规格id(前台显示图片的规格)
define('DEFAULT_SPEC_COLOR_ID', 1);


/**
 * 商品图片
 */
define('GOODS_IMAGES_WIDTH', '60,240,360,1280');
define('GOODS_IMAGES_HEIGHT', '60,240,360,12800');
define('GOODS_IMAGES_EXT', '_60,_240,_360,_1280');

/**
 *  订单状态
 */
//已取消
define('ORDER_STATE_CANCEL', 0);
//待取消
define('ORDER_WAIT_CANCEL', 1);
//已受理
define('ACCEPTANCE_ALREADY',2);
//已产生但未支付
define('ORDER_STATE_NEW', 10);
//已支付
define('ORDER_STATE_PAY', 20);
//已发货
define('ORDER_STATE_SEND', 30);
//已发货,商家管理中心已发货状态
define('ORDER_STATE_DELEVER_SEND', 50);
//已收货，交易成功
define('ORDER_STATE_SUCCESS', 40);
//未付款订单，自动取消的天数
define('ORDER_AUTO_CANCEL_DAY', 3);
//已发货订单，自动确认收货的天数
define('ORDER_AUTO_RECEIVE_DAY', 7);
//兑换码支持过期退款，可退款的期限，默认为7天
define('CODE_INVALID_REFUND', 7);
//默认未删除
define('ORDER_DEL_STATE_DEFAULT', 0);
//已删除
define('ORDER_DEL_STATE_DELETE', 1);
//彻底删除
define('ORDER_DEL_STATE_DROP', 2);
//订单结束后可评论时间，15天，60*60*24*15
define('ORDER_EVALUATE_TIME', 1296000);
//抢购订单状态
define('OFFLINE_ORDER_CANCEL_TIME', 3);//单位为天

//采购员订单所有状态
define('ORDER_STATUS_STR', ',0,1,2,10,20,30,40,12,13,14,18,81,33,31,32');

//采购员订单审核状态
define('ORDER_STATUS_SEND_ONE', 12);

//采购员订单审核中
define('ORDER_STATUS_SEND_TWO', 13);

//订单已经审核 
define('ORDER_STATUS_SUCCESS',14);

//订单回退 
define('ORDER_STATUS_ERROR',18);

//订单审核拒绝 
define('ORDER_STATUS_OUT',81);

//订单待发货
define('ORDER_DELIVER_GOODS',30);

//订单发货订单状态 31已发货
define('ORDER_STATUS_SEND_HET', 31);

//订单发货订单状态 已收货
define('ORDER_STATUS_RECEIVED', 40);

//订单发货订单状态 采购系统已签收33
define('ORDER_STATUS_CUS_RECEIVED',33 );

//session identity
//会员身份 00普通会员 01采购员 02认证供应商 03开店供应商  04 第三方物业采购员//
define('MEMBER_IDENTITY_ONE','00' );
define('MEMBER_IDENTITY_TWO','01' );
define('MEMBER_IDENTITY_THREE','02' );
define('MEMBER_IDENTITY_FOUR','03' );
define('MEMBER_IDENTITY_FIVE','04' );

//登录action名称
define('DEBUG_SYS_ACTNAME', '414,login,getmemberstatus,josn_classinfo,store_joinin,show_joinin,check_rzemail,store_join,store_joinin,');

//采购学堂分类ID
define('PURCHASE_TYPE',8 );

define('MEMBER_SUPPLY_TYPE_1','1' );
define('MEMBER_SUPPLY_TYPE_2','2' );
$SUPPLY_TYPE_LIST = array( MEMBER_SUPPLY_TYPE_1=>"采购供应商",MEMBER_SUPPLY_TYPE_2=>"非采购供应商");
define('MEMBER_SUPPLY_TYPE_LIST',json_encode($SUPPLY_TYPE_LIST) );//存在sc_member中的supply_type

define('STORE_CITY_FIRST_CITY', 2900);

//基本信息申请
define('ATTRIBUTE_STATE_SUBMIT', 1);
define('ATTRIBUTE_STATE_PROCESSED', 2);
define('ATTRIBUTE_STATE_REFUSE', 3);


