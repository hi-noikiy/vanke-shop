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
    <span class="arrow">></span> <span>城市公司管理</span>
    <span class="arrow">></span> <span>开户银行管理</span>
</div>
<style>
    .list-item:hover {
        background: rgba(255,245,204,0.25)
    }
</style>
<div class="main" style="margin-top: 30px;">
    <div class="layui-form" action="">
        <div class="layui-form-item" style="background-color: #f2f2f2;">
            <label class="layui-form-label" style="width: 140px;text-align:center;padding:9px 0px;">城市公司</label>
            <label class="layui-form-label" style="width: 220px;text-align:center;padding:9px 0px;">银行账户</label>
            <label class="layui-form-label" style="width: 170px;text-align:center;padding:9px 0px;">银行名称</label>
            <label class="layui-form-label" style="width: 270px;text-align:center;padding:9px 0px;">支行名称</label>
            <label class="layui-form-label" style="width: 170px;text-align:center;padding:9px 0px;">操作</label>
        </div>

        <?php if(!empty($output['all_list']) && is_array($output['all_list'])){?>
            <?php foreach ($output['all_list'] as $list){?>
                <div class="layui-form-item list-item">
                    <label class="layui-form-label" style="width: 140px;text-align:center;padding:9px 0px;"><?php echo $list['city_name'];?></label>
                    <?php if($list['account_type'] == '4'){?>
                        <?php if($list['joinin_state'] == '44'){?>
                            <label class="layui-form-label" style="width: 660px;text-align:center;padding:9px 0px;">该城市公司尚未绑定开户银行信息</label>
                        <?php }else{?>
                            <label class="layui-form-label" style="width: 660px;text-align:center;padding:9px 0px;">该城市公司正在审核中</label>
                        <?php }?>
                    <?php }else if($list['account_type'] == '1'){?>
                        <label class="layui-form-label" style="width: 660px;text-align:center;padding:9px 0px;">开户银行信息审核中</label>
                    <?php }else{?>
                        <?php $account_bank_info = unserialize($list['account'])?>
                        <label class="layui-form-label" style="width: 220px;text-align:center;padding:9px 0px;"><?php echo $account_bank_info['account_number'];?></label>
                        <label class="layui-form-label" style="width: 170px;text-align:center;padding:9px 0px;"><?php echo $account_bank_info['bank_name'];?></label>
                        <label class="layui-form-label" style="width: 270px;text-align:center;padding:9px 0px;"><?php echo $account_bank_info['bank_branch_name'];?></label>
                    <?php }?>
                    <label class="layui-form-label" style="width: 170px;text-align:center;padding:9px 0px;">
                        <?php if($list['joinin_state'] == '44'){?>
                        <?php if($list['account_type'] != '4'){?>
                            <a href="javascript:void(0);" data-city="<?php echo $list['join_city'];?>" class="look-up" style="color:#27A9E3;text-decoration:none;cursor: pointer;">查看</a>
                            <?php if($list['account_type'] == '2' || $list['account_type'] == '3' ){?>
                                |
                                <a href="javascript:void(0);" data-city="<?php echo $list['join_city'];?>" class="modify" style="color:#27A9E3;text-decoration:none;cursor: pointer;">修改</a>
                            <?php }?>
                        <?php }else{?>
                            <a href="javascript:void(0);" data-city="<?php echo $list['join_city'];?>" class="binding" style="color:#27A9E3;text-decoration:none;cursor: pointer;">绑定</a>
                        <?php }}?>
                    </label>
                </div>
        <?php }}?>
    </div>
</div>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/supplier/supplier_index.css" media="all">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/city_select.js"></script>
<script>
    $(".look-up").click(function(){
        var city = $(this).attr('data-city');
        var member = "<?php echo $_SESSION['member_id']?>";
        var url = '/shop/index.php?act=supplier_member&op=lookBank&city='+city+'&type=account';
        open_window(member,'开户银行信息',url,'860','600');
    });

    $(".binding").click(function(){
        var city = $(this).attr('data-city');
        var member = "<?php echo $_SESSION['member_id']?>";
        var url = '/shop/index.php?act=supplier_member&op=binding&city='+city+'&type=account';
        open_window(member,'开户银行信息',url,'860','600');
    });

</script>
