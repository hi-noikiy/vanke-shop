<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/9
 * Time: 13:14
 */
?>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<div class="breadcrumb">
    <i class="layui-icon">&#xe68e;</i>
    <a href="/shop/index.php?act=supplier_member">
        <span>我的商户中心</span>
    </a>
    <span class="arrow">></span> <span>认证管理</span>
    <span class="arrow">></span> <span>城市公司认证记录</span>
</div>
<div class="main" style="margin-top: 30px;">
    <div class="layui-collapse" lay-filter="test">
        <div class="layui-colla-item">
            <h2 class="layui-colla-title">
                <?php echo $output['first_list']['city_name'];?>
                <span class="layui-badge">首次认证</span>
                <?php if( $output['first_list']['joinin_state'] == STORE_JOIN_STATE_RZ){?><span class="layui-badge layui-bg-blue">认证等待审核中</span><?php }?>
                <?php if( $output['first_list']['joinin_state'] == STORE_JOIN_STATE_FNO){?><span class="layui-badge layui-bg-green">认证审核拒绝</span><?php }?>

                <?php if( $output['first_list']['store_state'] == STORE_JOIN_STATE_RZHKD){?><span class="layui-badge layui-bg-blue">开店申请审核中</span><?php }?>
                <?php if( $output['first_list']['store_state'] == STORE_JOIN_STATE_KDJJ){?><span class="layui-badge layui-bg-green">开店申请拒绝</span><?php }?>

                <?php if( $output['first_list']['store_state'] == STORE_JOIN_STATE_FINAL){?>
                    <span class="layui-badge layui-bg-orange">开店成功</span>
                <?php }?>
            </h2>
            <div class="layui-colla-content layui-show">
                <div class="layui-form">

                    <div class="layui-form-item">
                        <label class="layui-form-label" style="width: 130px;">认证状态：</label>
                        <div class="layui-form-mid layui-word-aux" style="width: 200px;">
                            <?php if( $output['first_list']['joinin_state'] == STORE_JOIN_STATE_RZ){?>认证等待审核中<?php }?>
                            <?php if( $output['first_list']['joinin_state'] == STORE_JOIN_STATE_FNO){?>认证审核拒绝<?php }?>
                            <?php if( $output['first_list']['joinin_state'] == STORE_JOIN_STATE_RZSUCCESS){?>认证通过<?php }?>
                        </div>

                        <label class="layui-form-label" style="width: 130px;">开店状态：</label>
                        <div class="layui-form-mid layui-word-aux" style="width: 200px;">
                            <?php if( $output['first_list']['store_state'] == '0'){?>等待开店<?php }?>
                            <?php if( $output['first_list']['store_state'] == STORE_JOIN_STATE_RZHKD){?>开店申请审核中<?php }?>
                            <?php if( $output['first_list']['store_state'] == STORE_JOIN_STATE_KDJJ){?>开店申请拒绝<?php }?>
                            <?php if( $output['first_list']['store_state'] == STORE_JOIN_STATE_FINAL){?>开店成功<?php }?>
                        </div>
                    </div>

                    <?php if($output['first_list']['joinin_state'] == STORE_JOIN_STATE_FNO){?>
                        <div class="layui-form-item">
                            <label class="layui-form-label" style="width: 150px;">认证拒绝原因：</label>
                            <div class="layui-form-mid layui-word-aux">
                                <?php echo $output['first_list']['joinin_message'];?>
                            </div>
                        </div>
                    <?php }?>

                    <?php if($output['first_list']['store_state'] == STORE_JOIN_STATE_KDJJ){?>
                        <div class="layui-form-item">
                            <label class="layui-form-label" style="width: 150px;">开店拒绝原因：</label>
                            <div class="layui-form-mid layui-word-aux">
                                <?php echo $output['first_list']['joinin_message_open'];?>
                            </div>
                        </div>
                    <?php }?>


                    <!--城市公司联系人信息-->
                    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                        <legend>城市公司联系人信息</legend>
                        <?php /*if( $output['first_list']['joinin_state'] == STORE_JOIN_STATE_RZSUCCESS){*/?><!--
                            <div class="layui-btn-group" style="margin-top: -25px;float: right;">
                                <?php /*if(empty($output['first_list']['city_contacts_name']) && empty($output['first_list']['city_contacts_phone'])){*/?>
                                    <a class="layui-btn" style="height: 25px;line-height: 25px;text-decoration:none"><i class="layui-icon"></i>增加</a>
                                <?php /*}else{*/?>
                                    <a class="layui-btn" style="height: 25px;line-height: 25px;text-decoration:none"><i class="layui-icon"></i>修改</a>
                                <?php /*}*/?>
                            </div>
                        --><?php /*}*/?>
                    </fieldset>
                    <?php if(empty($output['first_list']['city_contacts_name']) && empty($output['first_list']['city_contacts_phone'])){?>
                        <div class="layui-form-item">
                            <label class="layui-form-label" style="width: 130px;"></label>
                            <div class="layui-form-mid layui-word-aux">暂无联系人信息，请添加</div>
                        </div>
                    <?php }else{?>
                        <div class="layui-form-item">
                            <label class="layui-form-label" style="width: 130px;">联系人姓名：</label>
                            <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $output['first_list']['city_contacts_name'];?></div>

                            <label class="layui-form-label" style="width: 130px;">联系人手机：</label>
                            <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $output['first_list']['city_contacts_phone'];?></div>
                        </div>
                    <?php }?>
                    <!--end-->

                    <!--城市公司开户银行信息-->
                    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                        <legend>城市公司开户银行信息</legend>
                    </fieldset>
                    <?php if(empty($output['first_list']['account_bank_info'])){?>
                        <div class="layui-form-item">
                            <label class="layui-form-label" style="width: 130px;"></label>
                            <div class="layui-form-mid layui-word-aux">尚未绑定开户银行信息，请先绑定</div>
                        </div>
                    <?php }else{?>

                    <?php }?>
                    <!--end-->


                    <!--城市公司结算银行信息-->
                    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                        <legend>城市公司结算银行信息</legend>
                    </fieldset>
                    <?php if(empty($output['first_list']['settlement_bank_info'])){?>
                        <div class="layui-form-item">
                            <label class="layui-form-label" style="width: 130px;"></label>
                            <div class="layui-form-mid layui-word-aux">尚未绑定结算银行信息，请先绑定</div>
                        </div>
                    <?php }else{?>
                        <a class="layui-btn" style="height: 25px;line-height: 25px;text-decoration:none"><i class="layui-icon"></i>修改绑定银行</a>
                        <a class="layui-btn" style="height: 25px;line-height: 25px;"><i class="layui-icon"></i>解除绑定银行</a>
                    <?php }?>
                    <!--end-->
                </div>
            </div>
        </div>

        <?php if(!empty($output['all_list']) && is_array($output['all_list'])){?>
            <?php foreach ($output['all_list'] as $val){?>
                <div class="layui-colla-item">
                    <h2 class="layui-colla-title">
                        <?php echo $val['city_name'];?>
                        <?php if( $val['joinin_state'] == STORE_JOIN_STATE_RZ){?><span class="layui-badge layui-bg-blue">认证等待审核中</span><?php }?>
                        <?php if( $val['joinin_state'] == STORE_JOIN_STATE_FNO){?><span class="layui-badge layui-bg-green">认证审核拒绝</span><?php }?>

                        <?php if( $val['store_state'] == STORE_JOIN_STATE_RZHKD){?><span class="layui-badge layui-bg-blue">开店申请审核中</span><?php }?>
                        <?php if( $val['store_state'] == STORE_JOIN_STATE_KDJJ){?><span class="layui-badge layui-bg-green">开店申请拒绝</span><?php }?>

                        <?php if( $val['store_state'] == STORE_JOIN_STATE_FINAL){?>
                            <span class="layui-badge layui-bg-orange">开店成功</span>
                        <?php }?>
                    </h2>
                    <div class="layui-colla-content">
                        <div class="layui-form">

                            <div class="layui-form-item">
                                <label class="layui-form-label" style="width: 130px;">认证状态：</label>
                                <div class="layui-form-mid layui-word-aux" style="width: 200px;">
                                    <?php if( $val['joinin_state'] == STORE_JOIN_STATE_RZ){?>认证等待审核中<?php }?>
                                    <?php if( $val['joinin_state'] == STORE_JOIN_STATE_FNO){?>认证审核拒绝<?php }?>
                                    <?php if( $val['joinin_state'] == STORE_JOIN_STATE_RZSUCCESS){?>认证通过<?php }?>
                                </div>

                                <label class="layui-form-label" style="width: 130px;">开店状态：</label>
                                <div class="layui-form-mid layui-word-aux" style="width: 200px;">
                                    <?php if( $val['store_state'] == '0'){?>等待开店<?php }?>
                                    <?php if( $val['store_state'] == STORE_JOIN_STATE_RZHKD){?>开店申请审核中<?php }?>
                                    <?php if( $val['store_state'] == STORE_JOIN_STATE_KDJJ){?>开店申请拒绝<?php }?>
                                    <?php if( $val['store_state'] == STORE_JOIN_STATE_FINAL){?>开店成功<?php }?>
                                </div>
                            </div>

                            <?php if($val['joinin_state'] == STORE_JOIN_STATE_FNO){?>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 130px;">认证拒绝原因：</label>
                                    <div class="layui-form-mid layui-word-aux">
                                        <?php echo $val['joinin_message'];?>
                                    </div>
                                </div>
                            <?php }?>

                            <?php if($val['store_state'] == STORE_JOIN_STATE_KDJJ){?>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 130px;">开店拒绝原因：</label>
                                    <div class="layui-form-mid layui-word-aux">
                                        <?php echo $val['joinin_message_open'];?>
                                    </div>
                                </div>
                            <?php }?>

                            <!--城市公司联系人信息-->
                            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                                <legend>城市公司联系人信息</legend>
                            </fieldset>
                            <?php if(empty($val['city_contacts_name']) && empty($val['city_contacts_phone'])){?>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 130px;"></label>
                                    <div class="layui-form-mid layui-word-aux">暂无联系人信息，请添加</div>
                                </div>
                            <?php }else{?>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 130px;">联系人姓名：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['city_contacts_name'];?></div>

                                    <label class="layui-form-label" style="width: 130px;">联系人手机：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['city_contacts_phone'];?></div>
                                </div>
                            <?php }?>
                            <!--end-->

                            <!--城市公司开户银行信息-->
                            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                                <legend>城市公司开户银行信息</legend>
                            </fieldset>
                            <?php if(empty($val['account_bank_info'])){?>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 130px;"></label>
                                    <div class="layui-form-mid layui-word-aux">尚未绑定开户银行信息，请先绑定</div>
                                </div>
                            <?php }else{?>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 130px;">银行开户名：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['account_bank_info']['account_name'];?></div>

                                    <label class="layui-form-label" style="width: 130px;">公司银行账号：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['account_bank_info']['account_number'];?></div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 130px;">开户银行名称：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['account_bank_info']['bank_name'];?></div>

                                    <label class="layui-form-label" style="width: 130px;">开户银行支行名称：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['account_bank_info']['bank_branch_name'];?></div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 130px;">支行联行号：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['account_bank_info']['bank_branch_code'];?></div>

                                    <label class="layui-form-label" style="width: 130px;">开户行所在地：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['account_bank_info']['bank_address'];?></div>
                                </div>
                            <?php }?>
                            <!--end-->


                            <!--城市公司结算银行信息-->
                            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                                <legend>城市公司结算银行信息</legend>
                            </fieldset>
                            <?php if(empty($val['settlement_bank_info'])){?>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 130px;"></label>
                                    <div class="layui-form-mid layui-word-aux">尚未绑定结算银行信息，请先绑定</div>
                                </div>
                            <?php }else{?>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 130px;">结算银行开户名：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['settlement_bank_info']['settlement_name'];?></div>

                                    <label class="layui-form-label" style="width: 130px;">结算银行账号：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['settlement_bank_info']['settlement_number'];?></div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 130px;">结算银行名称：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['settlement_bank_info']['bank_name'];?></div>

                                    <label class="layui-form-label" style="width: 130px;">结算银行支行名称：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['settlement_bank_info']['bank_branch_name'];?></div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="width: 130px;">结算支行联行号：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['settlement_bank_info']['bank_branch_code'];?></div>

                                    <label class="layui-form-label" style="width: 130px;">结算行所在地：</label>
                                    <div class="layui-form-mid layui-word-aux" style="width: 200px;"><?php echo $val['settlement_bank_info']['bank_address'];?></div>
                                </div>
                            <?php }?>
                            <!--end-->
                        </div>
                    </div>
                </div>
        <?php }}?>
    </div>
</div>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/supplier/supplier_index.css" media="all">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script>
    layui.use(['element', 'layer'], function(){
        var element = layui.element;
        var layer = layui.layer;
    });
</script>
