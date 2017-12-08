<?php ?>
<form class="layui-form" method="post" id="cart-data" action="">
<div class="ncc-main">
  <div class="ncc-title">
    <h3><?php echo $lang['cart_index_ensure_info'];?></h3>
    <h5>请仔细核对填写收货、发票等信息，以确保物流快递及时准确投递。</h5>
  </div>
    <!--收货人地址信息-->
    <?php include template('buy/buy_address');?>
    <!--发票信息-->
    <?php include template('buy/buy_invoice');?>
    <?php include template('buy/buy_budget');?>
    <?php include template('buy/buy_goods_list');?>
</div>
    <div style="background: #F9F9F9;height: 90px;margin-bottom: 30px;">
        <div class="ncc-all-account" style="text-align:right;line-height:50px; height: 50px">订单总金额：
            <em><?php echo $output['list']['goodMoney'];?></em><?php echo $lang['currency_zh'];?>
        </div>
        <div class="ncc-bottom" style="padding:0px">
            <button class="layui-btn layui-btn-lg layui-btn-normal" lay-filter="buy-next" lay-submit=""
                    style="float: right;margin-right: 20px;width: 200px;margin-bottom: 20px">提交提单</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    layui.use(['form', 'layer', 'layedit', 'laydate'], function() {
        var form = layui.form
            , layer = layui.layer
            , layedit = layui.layedit
            , laydate = layui.laydate;

        form.on('submit(buy-next)', function(data){
            var invoice_code = $("#invoice_code").val();
            var add_code = $("#add_code").val();
            if(invoice_code == ''){
                layer.alert('请选择确认发票信息', {closeBtn: 0,title: '温馨提示',}, function(index){
                    layer.closeAll();
                });
                return false;
            }
            if(add_code == ''){
                layer.alert('请选择确认收货人地址信息', {closeBtn: 0,title: '温馨提示',}, function(index){
                    layer.closeAll();
                });
                return false;
            }
            $.ajax({
                type:"POST",
                //提交的网址
                url:"/shop/index.php?act=buy&op=cart",
                data:data.field,
                datatype: "json",
                beforeSend: function () {
                    loads = layer.load(1, {
                        shade: [0.5,'#000'] //0.1透明度的白色背景
                    });
                },
                success:function(result){
                    layer.close(loads);
                    var result = JSON.parse(result);
                    if(result.code == '1'){
                        layer.alert('提交订单成功', {closeBtn: 0,title: '温馨提示',}, function(index){
                            window.location.href="/shop/index.php?act=member_inorder&op=inside_order";
                            layer.close(index);
                        });
                    }else if(result.code == '2'){
                        layer.alert('提交部分订单成功'+result.msg, {closeBtn: 0,title: '温馨提示',}, function(index){
                            window.location.href="/shop/index.php?act=member_inorder&op=inside_order";
                            layer.close(index);
                        });
                    }else{
                        layer.alert(result.msg, {closeBtn: 0,title: '温馨提示',}, function(index){
                            window.location.href="/shop/index.php?act=cart";
                            layer.close(index);
                        });
                    }
                }
            });
            return false;
        });
    });
</script>

