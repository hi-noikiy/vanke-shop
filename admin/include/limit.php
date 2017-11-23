<?php
/**
 * 载入权限
 *
 *
 */

$_limit =  array(
	array('name'=>$lang['nc_config'], 'child'=>array(
		array('name'=>$lang['nc_web_set'], 'op'=>null, 'act'=>'setting'),
		array('name'=>$lang['nc_web_account_syn'], 'op'=>null, 'act'=>'account'),
		array('name'=>$lang['nc_upload_set'], 'op'=>null, 'act'=>'upload'),
		array('name'=>$lang['nc_seo_set'], 'op'=>'seo', 'act'=>'setting'),
		array('name'=>$lang['nc_pay_method'], 'op'=>null, 'act'=>'payment'),
		array('name'=>$lang['nc_message_set'], 'op'=>null, 'act'=>'message'),
		array('name'=>$lang['nc_admin_express_set'], 'op'=>null, 'act'=>'express'),
		array('name'=>'运单模板', 'op'=>null, 'act'=>'waybill'),
		//
		array('name'=>$lang['nc_admin_area_manage'], 'op'=>null, 'act'=>'area'),
		array('name'=>$lang['nc_admin_offpay_area_set'], 'op'=>null, 'act'=>'offpay_area'),
	    array('name'=>$lang['nc_admin_clear_cache'], 'op'=>null, 'act'=>'cache'),
	    array('name'=>$lang['nc_admin_perform_opt'], 'op'=>null, 'act'=>'perform'),
	    array('name'=>$lang['nc_admin_search_set'], 'op'=>null, 'act'=>'search'),
	    array('name'=>$lang['nc_admin_log'], 'op'=>null, 'act'=>'admin_log'),
	    array('name'=>'城市中心', 'op'=>null, 'act'=>'city_manges'),
		)),
	array('name'=>$lang['nc_goods'], 'child'=>array(
		array('name'=>$lang['nc_goods_manage'], 'op'=>null, 'act'=>'goods'),
		array('name'=>$lang['nc_class_manage'], 'op'=>null, 'act'=>'goods_class'),
		array('name'=>$lang['nc_brand_manage'], 'op'=>null, 'act'=>'brand'),
		array('name'=>$lang['nc_type_manage'], 'op'=>null, 'act'=>'type'),
		array('name'=>$lang['nc_spec_manage'], 'op'=>null, 'act'=>'spec'),
		array('name'=>$lang['nc_album_manage'], 'op'=>null, 'act'=>'goods_album'),
            array('name'=>'基本信息申请', 'op'=>null, 'act'=>'attribut'),
		array('name'=>'物料管理', 'op'=>null, 'act'=>'codemanages'),
		)),
	array('name'=>$lang['nc_store'], 'child'=>array(
		array('name'=>$lang['nc_store_manage'], 'op'=>null, 'act'=>'store'),
		//array('name'=>$lang['nc_store_grade'], 'op'=>null, 'act'=>'store_grade'),
                array('name'=>'供应商清单', 'op'=>null, 'act'=>'store_list'),
		array('name'=>$lang['nc_store_class'], 'op'=>null, 'act'=>'store_class'),
		array('name'=>$lang['nc_domain_manage'], 'op'=>null, 'act'=>'domain'),
		array('name'=>$lang['nc_s_snstrace'], 'op'=>null, 'act'=>'sns_strace'),
		array('name'=>'店铺帮助', 'op'=>null, 'act'=>'help_store'),
		array('name'=>'开店首页', 'op'=>null, 'act'=>'store_joinin'),
		array('name'=>'自营店铺', 'op'=>null, 'act'=>'ownshop'),
		array('name'=>'城市供应商评估', 'op'=>null, 'act'=>'assessment'),
                array('name'=>'供应商评级', 'op'=>null, 'act'=>'assessment_city'),
                array('name'=>'评估模板', 'op'=>null, 'act'=>'assessmentmb'),
		)),
	array('name'=>$lang['nc_member'], 'child'=>array(
		array('name'=>$lang['nc_member_manage'], 'op'=>null, 'act'=>'member'),
	    array('name'=>'会员级别', 'op'=>null, 'act'=>'member_grade'),
	    array('name'=>$lang['nc_exppoints_manage'], 'op'=>null, 'act'=>'exppoints'),
		array('name'=>$lang['nc_member_notice'], 'op'=>null, 'act'=>'notice'),
		array('name'=>$lang['nc_member_pointsmanage'], 'op'=>null, 'act'=>'points'),
		array('name'=>$lang['nc_binding_manage'], 'op'=>null, 'act'=>'sns_sharesetting'),
		array('name'=>$lang['nc_member_album_manage'], 'op'=>null, 'act'=>'sns_malbum'),
	    array('name'=>$lang['nc_snstrace'], 'op'=>null, 'act'=>'snstrace'),
		array('name'=>$lang['nc_member_tag'], 'op'=>null, 'act'=>'sns_member'),
		array('name'=>$lang['nc_member_predepositmanage'], 'op'=>null, 'act'=>'predeposit'),
		array('name'=>'聊天记录', 'op'=>null, 'act'=>'chat_log'),
            array('name'=>'第三方采购认证', 'op'=>null, 'act'=>'third_member'),
		)),
	array('name'=>$lang['nc_trade'], 'child'=>array(
            array('name'=>'采购订单', 'op'=>null, 'act'=>'order_in'),
		array('name'=>$lang['nc_order_manage'], 'op'=>null, 'act'=>'order'),
//	    array('name'=>'虚拟订单', 'op'=>null, 'act'=>'vr_order'),
		array('name'=>'退款管理', 'op'=>null, 'act'=>'refund'),
		array('name'=>'退货管理', 'op'=>null, 'act'=>'return'),
		array('name'=>'虚拟订单退款', 'op'=>null, 'act'=>'vr_refund'),
		array('name'=>$lang['nc_consult_manage'], 'op'=>null, 'act'=>'consulting'),
		array('name'=>$lang['nc_inform_config'], 'op'=>null, 'act'=>'inform'),
		array('name'=>$lang['nc_goods_evaluate'], 'op'=>null, 'act'=>'evaluate'),
		array('name'=>$lang['nc_complain_config'], 'op'=>null, 'act'=>'complain'),
		)),
	array('name'=>$lang['nc_website'], 'child'=>array(
		array('name'=>$lang['nc_article_class'], 'op'=>null, 'act'=>'article_class'),
		array('name'=>$lang['nc_article_manage'], 'op'=>null, 'act'=>'article'),
		array('name'=>$lang['nc_document'], 'op'=>null, 'act'=>'document'),
		array('name'=>$lang['nc_navigation'], 'op'=>null, 'act'=>'navigation'),
		array('name'=>$lang['nc_adv_manage'], 'op'=>null, 'act'=>'adv'),
		array('name'=>$lang['nc_web_index'], 'op'=>null, 'act'=>'web_config|web_api'),
		array('name'=>$lang['nc_admin_res_position'], 'op'=>null, 'act'=>'rec_position'),
		array('name'=>$lang['nc_cms_special_manage'], 'op'=>null, 'act'=>'web_special'),
                array('name'=>"采购制度", 'op'=>null, 'act'=>'procurement'),
		)),
	array('name'=>$lang['nc_operation'], 'child'=>array(
		array('name'=>$lang['nc_operation_set'], 'op'=>null, 'act'=>'operation'),
		array('name'=>$lang['nc_groupbuy_manage'], 'op'=>null, 'act'=>'groupbuy'),
        array('name'=>'虚拟抢购设置', 'op'=>null, 'act'=>'vr_groupbuy'),
		array('name'=>$lang['nc_activity_manage'], 'op'=>null, 'act'=>'activity'),
		array('name'=>$lang['nc_promotion_xianshi'], 'op'=>null, 'act'=>'promotion_xianshi'),
		array('name'=>$lang['nc_promotion_mansong'], 	'op'=>null, 'act'=>'promotion_mansong'),
		array('name'=>$lang['nc_promotion_bundling'], 'op'=>null, 'act'=>'promotion_bundling'),
		array('name'=>'推荐展位', 'op'=>null, 'act'=>'promotion_bundling'),
		array('name'=>$lang['nc_pointprod'], 'op'=>null, 'act'=>'pointprod|pointorder'),
		array('name'=>$lang['nc_voucher_price_manage'], 	'op'=>null, 'act'=>'voucher'),
	    array('name'=>$lang['nc_bill_manage'], 'op'=>null, 'act'=>'bill'),
	    array('name'=>'虚拟订单结算', 'op'=>null, 'act'=>'vr_bill'),
	    array('name'=>'平台客服', 'op'=>null, 'act'=>'mall_consult'),
        array('name'=>'平台充值卡', 'op'=>null, 'act'=>'rechargecard'),
	    array('name'=>'物流自提服务站', 'op'=>null, 'act'=>'delivery')
		)),
	array('name'=>$lang['nc_stat'], 'child'=>array(
	    array('name'=>$lang['nc_statgeneral'], 'op'=>null, 'act'=>'stat_general'),
	    array('name'=>$lang['nc_statindustry'], 'op'=>null, 'act'=>'stat_industry'),
		array('name'=>$lang['nc_statmember'], 'op'=>null, 'act'=>'stat_member'),
		array('name'=>$lang['nc_statstore'], 'op'=>null, 'act'=>'stat_store'),
		array('name'=>$lang['nc_stattrade'], 'op'=>null, 'act'=>'stat_trade'),
		array('name'=>$lang['nc_statgoods'], 'op'=>null, 'act'=>'stat_goods'),
		array('name'=>$lang['nc_statmarketing'], 'op'=>null, 'act'=>'stat_marketing'),
		array('name'=>$lang['nc_stataftersale'], 	'op'=>null, 'act'=>'stat_aftersale'),
		)),
);

if (C('mobile_isuse') !== NULL){
	$_limit[] = array('name'=>$lang['nc_mobile'], 'child'=>array(
		array('name'=>'首页设置', 'op'=>NULL, 'act'=>'mb_special'),
		array('name'=>'专题设置', 'op'=>NULL, 'act'=>'mb_special'),
		array('name'=>$lang['nc_mobile_catepic'], 'op'=>NULL, 'act'=>'mb_category'),
		array('name'=>'下载设置', 'op'=>NULL, 'act'=>'mb_app'),
		array('name'=>$lang['nc_mobile_feedback'], 'op'=>NULL, 'act'=>'mb_feedback'),
		array('name'=>'手机支付', 'op'=>NULL, 'act'=>'mb_payment'),
		));
}




return $_limit;
