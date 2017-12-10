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
    <span class="arrow">></span> <span>联系人管理</span>
</div>
<style>
    .list-item:hover {
        background: rgba(255,245,204,0.25)
    }
</style>
<div class="main" style="margin-top: 30px;">
    <div class="layui-form" action="">
        <div class="layui-form-item" style="background-color: #f2f2f2;">
            <label class="layui-form-label" style="width: 240px;text-align:center;padding:9px 0px;">城市公司</label>
            <label class="layui-form-label" style="width: 240px;text-align:center;padding:9px 0px;">联系人姓名</label>
            <label class="layui-form-label" style="width: 240px;text-align:center;padding:9px 0px;">联系人电话</label>
            <label class="layui-form-label" style="width: 240px;text-align:center;padding:9px 0px;">操作</label>
        </div>

        <?php if(!empty($output['all_list']) && is_array($output['all_list'])){?>
            <?php foreach ($output['all_list'] as $list){?>
                <div class="layui-form-item list-item">
                    <label class="layui-form-label" style="width: 240px;text-align:center;padding:9px 0px;"><?php echo $list['city_name'];?></label>
                    <label class="layui-form-label" style="width: 240px;text-align:center;padding:9px 0px;"><?php echo $list['city_contacts_name'];?></label>
                    <label class="layui-form-label" style="width: 240px;text-align:center;padding:9px 0px;"><?php echo $list['city_contacts_phone'];?></label>
                    <label class="layui-form-label" style="width: 240px;text-align:center;padding:9px 0px;">
                        <a href="javascript:void(0);" class="look-up" style="color:#27A9E3;text-decoration:none;cursor: pointer;">查看</a>
                        |
                        <a href="javascript:void(0);" class="modify" style="color:#27A9E3;text-decoration:none;cursor: pointer;">修改</a>
                    </label>
                </div>
        <?php }}?>
    </div>
</div>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/supplier/supplier_index.css" media="all">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script>
    $(".look-up").click(function(){
        alert('1');
    });

    $(".modify").click(function(){
        alert('2');
    });

</script>
