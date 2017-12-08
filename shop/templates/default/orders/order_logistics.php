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
<?php if(!empty($output['daddress_info'])){?>
<div style="padding: 10px 50px;">
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">物流公司：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <a target="_blank" href="<?php echo $output['order_info']['express_info']['e_url'];?>">
                <?php echo $output['order_info']['express_info']['e_name'];?>
            </a>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">物流单号：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['order_info']['shipping_code'];?>
        </div>
    </div>
</div>
<?php }else{?>
    <div>
        <div id="wrapper">
            <div id="main">
                <header id="header">
                    <h1><span class="sub">卖家尚未发货</span></h1>
                </header>
            </div>
        </div>
    </div>
<?php }?>