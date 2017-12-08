<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<div class="ncm-order-info" style="border:none;margin-top: -10px;font: 14px Helvetica Neue,Helvetica,PingFang SC,微软雅黑,Tahoma,Arial,sans-serif;">
    <div class="layui-tab layui-tab-card">
        <ul class="layui-tab-title">
            <li class="layui-this">订单详情</li>
            <li>收货人地址信息</li>
            <li>发票信息</li>
            <li>商家信息</li>
            <li>物流信息</li>
            <li>发货人信息</li>
        </ul>
        <div class="layui-tab-content" style="height: 300px;">
            <div class="layui-tab-item layui-show">
                <?php include template('orders/order_info');?>
            </div>
            <div class="layui-tab-item">
                <?php include template('orders/order_address');?>
            </div>
            <div class="layui-tab-item">
                <?php include template('orders/order_inv');?>
            </div>
            <div class="layui-tab-item">
                <?php include template('orders/order_store');?>
            </div>
            <div class="layui-tab-item">
                <?php include template('orders/order_logistics');?>
            </div>
            <div class="layui-tab-item">
                <?php include template('orders/order_deliver');?>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/form.js"></script>

<script>
    layui.use('element', function(){
        var element = layui.element;

        //…
    });
</script>

