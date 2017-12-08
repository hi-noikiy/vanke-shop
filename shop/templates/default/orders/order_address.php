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
        <label class="layui-form-label" style="width: 170px;">收货人姓名：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['order_info']['extend_order_common']['reciver_name'];?>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">收货人手机号码：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['order_info']['extend_order_common']['reciver_info']['mob_phone'];?>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">收货人电话号码：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['order_info']['extend_order_common']['reciver_info']['tel_phone'];?>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">收货人地址：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['order_info']['extend_order_common']['reciver_info']['tel_phone'];?>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">收货人详细地址：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['order_info']['extend_order_common']['reciver_info']['street'];?>
        </div>
    </div>
</div>
