<?php ?>

<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/home_point.css" rel="stylesheet" type="text/css">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/home_login.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.charCount.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/member.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/home.js" charset="utf-8"></script>
<!--
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/sns.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/sns_store.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/smilies/smilies_data.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/smilies/smilies.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.caretInsert.js" charset="utf-8"></script>
-->
<script>
    var MAX_RECORDNUM = <?php echo $output['max_recordnum'];?>;
</script>

<div class="cms-sns">
    <div class="cms-sns-left">
        <div class="cms-sns-tabmene">
            <ul>
                <li><a  class="selected">优惠券<i></i></a></li>
            </ul>
        </div>
        <div class="cms-sns-content">

            <?php if (!empty($output['voucherlist'])){?>
                <ul class="ncp-voucher-list">
                    <?php foreach ($output['voucherlist'] as $k=>$v){?>
                        <li>
                            <div class="ncp-voucher">
                                <div class="cut"></div>
                                <div class="info"><a href="<?php echo urlShop('show_store', 'index', array('store_id'=>$v['voucher_t_store_id']));?>" class="store"><?php echo $v['voucher_t_storename'];?></a>
                                    <p class="store-classify"><?php echo $v['voucher_t_sc_name'];?></p>
                                    <div class="pic"><img src="<?php echo $v['voucher_t_customimg'];?>" onerror="this.src='<?php echo UPLOAD_SITE_URL.DS.defaultGoodsImage(240);?>'"/></div>
                                </div>
                                <dl class="value">
                                    <dt><?php echo $lang['currency'];?><em><?php echo $v['voucher_t_price'];?></em></dt>
                                    <dd>购物满<?php echo $v['voucher_t_limit'];?>元可用</dd>
                                    <dd class="time">有效期至<?php echo @date('Y-m-d',$v['voucher_t_end_date']);?></dd>
                                </dl>
                                <div class="point">
                                    <p class="required">需<em><?php echo $v['voucher_t_points'];?></em>积分</p>
                                    <p><em><?php echo $v['voucher_t_giveout'];?></em>人兑换</p>
                                </div>
                                <div class="button"><a target="_blank" href="###" nc_type="exchangebtn" data-param='{"vid":"<?php echo $v['voucher_t_id'];?>"}' class="ncp-btn ncp-btn-red">立即兑换</a></div>
                            </div>
                        </li>
                    <?php }?>
                </ul>
                <div class="tc mt20 mb20">
                    <div class="pagination"><?php echo $output['show_page'];?></div>
                </div>
            <?php }else{?>
                <div class="norecord"><?php echo $lang['home_voucher_list_null'];?></div>
            <?php }?>






        </div>
        <!-- 表情弹出层 -->
        <div id="smilies_div" class="smilies-module"></div>
    </div>

    <div class="cms-sns-right">
        <?php if ($_SESSION['is_login'] == '1'){ ?>

            <div class="cms-sns-right-container">
                <div class="cms-store-pic"><a><img src="<?php if ($output['member_info']['member_avatar']!='') { echo UPLOAD_SITE_URL.'/'.ATTACH_AVATAR.DS.$output['member_info']['member_avatar']; } else { echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON. DS.C('default_user_portrait'); } ?>"/></a></div>
                <dl class="cms-store-info">
                    <dt><?php echo $_SESSION['member_name'];?></dt>
                    <dd>当前等级：<em nctype="store_collect"><?php echo $output['member_info']['level_name'];?></em></dd>
                    <dd>当前经验：<em nctype="store_collect"><?php echo $output['member_info']['member_exppoints'];?></em></dd>
                </dl>

            </div>

        <div class="ncp-member-point">
            <dl style="border-left: none 0;">
                <a href="index.php?act=member_points" target="_blank">
                    <dt><strong><?php echo $output['member_info']['member_points'];?></strong>分</dt>
                    <dd>我的积分</dd>
                </a>
            </dl>
            <?php if (C('voucher_allow')==1){ ?>
                <dl>
                    <a href="index.php?act=member_voucher&op=index" target="_blank">
                        <dt><strong><?php echo $output['vouchercount']; ?></strong>张</dt>
                        <dd>可用代金券</dd>
                    </a>
                </dl>
            <?php } ?>
            <?php if (C('pointprod_isuse')==1){?>
                <dl>
                    <a href="index.php?act=member_pointorder&op=orderlist" target="_blank">
                        <dt><strong><?php echo $output['pointordercount'];?></strong>个</dt>
                        <dd>已兑换礼品</dd>
                    </a>
                </dl>
            <?php }?>
        </div>

        <?php } ?>

    </div>
</div>
