<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/30
 * Time: 下午2:37
 */
?>
<style>
</style>
<div style="padding: 10px 50px;">
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">店铺名称：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['order_info']['extend_store']['store_name'];?>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">公司名称：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['order_info']['extend_store']['store_company_name'];?>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">联系方式：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['order_info']['extend_store']['store_phone'];?>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">所在城市：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['order_info']['extend_store']['area_info'];?>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">详细地址：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['order_info']['extend_store']['store_address'];?>
        </div>
    </div>
</div>
