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
    <span class="arrow">></span> <span>银行管理</span>
</div>
<style>
    .list-item:hover {
        background: rgba(255,245,204,0.25)
    }
</style>
<div class="main" style="margin-top: 30px;">
    <div class="layui-tab layui-tab-brief" lay-filter="bank-item">
        <ul class="layui-tab-title">
            <li lay-id="account"class="layui-this" >开户银行</li>
            <li lay-id="settlement" >结算银行</li>
        </ul>
        <div class="layui-tab-content" style="height: 100px;">
            <div class="layui-tab-item layui-show">
                <div class="layui-form" action="">
                    <a class="layui-btn add-account" style="float: right;margin-right: 20px;margin-bottom: 15px;">添加开户银行</a>
                    <div class="layui-form-item" style="background-color: #f2f2f2;">
                        <label class="layui-form-label" style="width: 40px;text-align:center;padding:9px 0px;"></label>
                        <label class="layui-form-label" style="width: 220px;text-align:center;padding:9px 0px;">开户银行账户</label>
                        <label class="layui-form-label" style="width: 170px;text-align:center;padding:9px 0px;">开户银行名称</label>
                        <label class="layui-form-label" style="width: 270px;text-align:center;padding:9px 0px;">开户支行名称</label>
                        <label class="layui-form-label" style="width: 170px;text-align:center;padding:9px 0px;">操作</label>
                    </div>
                    <?php if(!empty($output['list']['account']) && is_array($output['list']['account'])){?>
                        <?php foreach ($output['list']['account'] as $va){?>
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="width: 40px;text-align:center;padding:9px 0px;"></label>
                                <label class="layui-form-label" style="width: 220px;text-align:center;padding:9px 0px;">
                                    <?php echo $va['account_number']?>
                                </label>
                                <label class="layui-form-label" style="width: 170px;text-align:center;padding:9px 0px;">
                                    <?php echo $va['bank_name']?>
                                </label>
                                <label class="layui-form-label" style="width: 270px;text-align:center;padding:9px 0px;">
                                    <?php echo $va['bank_branch_name']?>
                                </label>
                                <label class="layui-form-label" style="width: 170px;text-align:center;padding:9px 0px;">
                                    <a href="javascript:void(0);" data-type="account" data-val="<?php echo $va['id'];?>" class="look-up" style="color:#27A9E3;text-decoration:none;cursor: pointer;">查看</a>
                                    |
                                    <a href="javascript:void(0);" data-type="account" data-val="<?php echo $va['id'];?>" class="modify" style="color:#27A9E3;text-decoration:none;cursor: pointer;">修改</a>
                                </label>
                            </div>
                    <?php }}?>
                </div>
            </div>
            <div class="layui-tab-item">
                <div class="layui-form" action="">
                    <button class="layui-btn add-settlement" style="float: right;margin-right: 20px;margin-bottom: 15px;">添加结算银行</button>
                    <div class="layui-form-item" style="background-color: #f2f2f2;">
                        <label class="layui-form-label" style="width: 40px;text-align:center;padding:9px 0px;"></label>
                        <label class="layui-form-label" style="width: 220px;text-align:center;padding:9px 0px;">结算银行账户</label>
                        <label class="layui-form-label" style="width: 170px;text-align:center;padding:9px 0px;">结算银行名称</label>
                        <label class="layui-form-label" style="width: 270px;text-align:center;padding:9px 0px;">结算支行名称</label>
                        <label class="layui-form-label" style="width: 170px;text-align:center;padding:9px 0px;">操作</label>
                    </div>

                    <?php if(!empty($output['list']['settlement']) && is_array($output['list']['settlement'])){?>
                        <?php foreach ($output['list']['settlement'] as $vb){?>
                            <div class="layui-form-item">
                            <label class="layui-form-label" style="width: 40px;text-align:center;padding:9px 0px;"></label>
                            <label class="layui-form-label" style="width: 220px;text-align:center;padding:9px 0px;">
                                <?php echo $vb['settlement_number']?>
                            </label>
                            <label class="layui-form-label" style="width: 170px;text-align:center;padding:9px 0px;">
                                <?php echo $vb['bank_name']?>
                            </label>
                            <label class="layui-form-label" style="width: 270px;text-align:center;padding:9px 0px;">
                                <?php echo $vb['bank_branch_name']?>
                            </label>
                            <label class="layui-form-label" style="width: 170px;text-align:center;padding:9px 0px;">
                                <a href="javascript:void(0);" data-type="settlement" data-val="<?php echo $vb['id'];?>" class="look-up" style="color:#27A9E3;text-decoration:none;cursor: pointer;">查看</a>
                                |
                                <a href="javascript:void(0);" data-type="settlement" data-val="<?php echo $vb['id'];?>" class="modify" style="color:#27A9E3;text-decoration:none;cursor: pointer;">修改</a>
                            </label>
                        </div>
                    <?php }}?>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/supplier/supplier_index.css" media="all">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/city_select.js"></script>
<script>

    layui.use(['laypage', 'layer', 'element', 'table'], function(){
        var $ = layui.jquery
            ,element = layui.element
            ,laypage = layui.laypage
            ,table = layui.table
            ,layer = layui.layer;


    });


    $(".look-up").click(function(){
        var id = $(this).attr('data-val');
        var type = $(this).attr('data-type');
        var member = "<?php echo $_SESSION['member_id']?>";
        var url = '/shop/index.php?act=supplier_member&op=lookListBank&id='+id+'&type='+type;
        open_window(member,'银行信息',url,'860','600');
    });

    $(".binding").click(function(){
        var city = $(this).attr('data-city');
        var member = "<?php echo $_SESSION['member_id']?>";
        var url = '/shop/index.php?act=supplier_member&op=binding&city='+city+'&type=account';
        open_window(member,'开户银行信息',url,'860','600');
    });

    $(".modify").click(function(){
        var id = $(this).attr('data-val');
        var type = $(this).attr('data-type');
        var member = "<?php echo $_SESSION['member_id']?>";
        if(type == 'settlement'){
            var url = '/shop/index.php?act=supplier_member&op=newSettlementBank&id='+id;
            open_window(member,'修改结算银行信息',url,'900','600');
        }else{
            var url = '/shop/index.php?act=supplier_member&op=newAccountBank&id='+id;
            open_window(member,'修改开户银行信息',url,'900','600');
        }
    });

    $(".add-account").click(function(){
        var member = "<?php echo $_SESSION['member_id']?>";
        var url = '/shop/index.php?act=supplier_member&op=newAccountBank';
        open_window(member,'添加开户银行',url,'900','600');
    });

    $(".add-settlement").click(function(){
        var member = "<?php echo $_SESSION['member_id']?>";
        var url = '/shop/index.php?act=supplier_member&op=newSettlementBank';
        open_window(member,'添加结算银行',url,'900','600');
    });

</script>
