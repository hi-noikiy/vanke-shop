<div class="breadcrumb">
    <i class="layui-icon">&#xe68e;</i>
    <a href="/shop/index.php?act=supplier_member">
        <span>我的商户中心</span>
    </a>
    <span class="arrow">></span> <span>交易管理</span>
    <span class="arrow">></span> <span>线下订单</span>
</div>
<div class="main" style="margin-top: 30px;">
    <div>
        <form class="layui-form" id="data-list" name="data-list" method="post" action="">

            <div class="layui-form-item">
                <div class="layui-input-inline" style="margin-left: 20px;">
                    <input type="text" name="contacts_phone" placeholder="请输入订单编号" autocomplete="off" class="layui-input">
                </div>

                <label class="layui-form-label" style="margin-left: -45px;width: 95px;">下单时间</label>
                <div class="layui-input-inline" style="width: 300px;">
                    <input type="text" id="start" style="float: left;height:30px;width:120px">
                    <i class="layui-icon" style="font-size: 30px;float: left;margin-left: -35px;">&#xe637;</i>
                    <span style="font-size: 30px;float: left;margin-left: 5px;margin-right: 5px;">-</span>
                    <input type="text" id="end" style="float: left;height:30px;width:120px">
                    <i class="layui-icon" style="font-size: 30px;float: left;margin-left: -35px;">&#xe637;</i>
                </div>

                <label class="layui-form-label" style="margin-left: -45px;width: 95px;">订单状态</label>
                <div class="layui-input-inline" style="width: 100px;">
                    <select name="contacts" lay-search="" lay-filter="contacts">
                        <option value="">状态</option>
                        <?php if(!empty($output['contacts_list']) && is_array($output['contacts_list'])){?>
                            <?php foreach ($output['contacts_list'] as $contacts){?>
                                <option value="<?php echo $contacts['city_contacts_phone'];?>"><?php echo $contacts['city_contacts_name'];?></option>
                            <?php }}?>
                    </select>
                </div>

                <div class="layui-input-inline" style="width: 100px;">
                    <a href="javascript:void(0);" onclick="get_data()" style="text-decoration:none;height: 35px;font-size:15px;width: 75px;margin-left: 20px;"
                       class="layui-btn layui-btn-primary layui-btn-small">
                        <span style="margin-left: -3px;">搜索</span>
                        <i class="layui-icon" style="color: #71b704;">&#xe615;</i>
                    </a>
                </div>
            </div>
        </form>
    </div>
    <div style="margin-top: 20px;">
        <table style="border-collapse:separate;border-spacing:0px 10px;width: 100%">
            <tr style="background-color: #f2f2f2;font-size: 16px;height: 40px">
                <td align="center" style="width: 18%">订单号</td>
                <td align="center" style="width: 25%">项目名称</td>
                <td align="center" style="width: 10%">收货人</td>
                <td align="center" style="width: 50%">收货地址</td>
                <td align="center" style="width: 3%"></td>
            </tr>
            <?php if (!empty($output['order_group_list']) && is_array($output['order_group_list'])) { ?>
            <?php foreach($output['order_group_list'] as $order_id2 => $order_info2) {?>
                <tr>
                    <td class="item-data" align="center" style="width: 18%" data-val="1">
                        <i class="layui-icon one" style="font-size: 20px;cursor:pointer;">&#xe625;</i>
                        <i class="layui-icon all" style="font-size: 20px;display: none;cursor:pointer;">&#xe619;</i>
                        <?php echo $order_info2['orderNo'];?>
                    </td>
                    <td align="center" style="width: 25%"><?php echo $order_info2['butxt'];?></td>
                    <td align="center" style="width: 10%"><?php echo $order_info2['deliveryPerson'];?></td>
                    <td align="center" style="width: 47%"><?php echo $order_info2['deliveryAddress'];?></td>
                </tr>
                <tr id="list-1" style="display: none">
                    <td colspan="10">
                        <table style="width: 95%;background-color: #f2f2f2;margin-left: 20px">
                            <tr>
                                <td align="center" style="width: 15%">订单号</td>
                                <td align="center" style="width: 25%">商品</td>
                                <td align="center" style="width: 10%">单价（元）</td>
                                <td align="center" style="width: 10%">数量</td>
                                <td align="center" style="width: 10%">订单金额</td>
                                <td align="center" style="width: 15%">下单时间</td>
                                <td align="center" style="width: 8%">交易状态</td>
                                <td align="center" style="width: 7%">操作</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            <?php }}?>
        </table>
    </div>
</div>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script>
    layui.use(['form', 'layer'], function(){
        var form = layui.form,
            layer = layui.layer;



        //监听提交
        form.on('submit(city-data)', function(data){
            var val=$('input:radio[name="city"]:checked').val();
            if(val==null){
                layer.alert('请选择需要认证的城市公司', {closeBtn: 0,title: '温馨提示',}, function(index){
                    layer.close(index);
                });
                return false;
            }else{
                $.ajax({
                    type:"POST",
                    //提交的网址
                    url:"/shop/index.php?act=supplier_member&op=city_add",
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
                            window.location.href="/shop/index.php?act=supplier_member&op=join_log";
                        }else{
                            if(result.msg == ''){
                                layer.alert('提交保存数据失败！请联系管理员', {closeBtn: 0,title: '温馨提示',}, function(index){
                                    layer.close(index);
                                });
                            }else{
                                layer.alert(result.msg, {closeBtn: 0,title: '温馨提示',}, function(index){
                                    layer.close(index);
                                });
                            }
                        }
                    }
                });
                return false;
            }
        });

    });

    $(".item-data").on("click", ".one", function() {
        var key = $(this).parent().attr('data-val');
        $(this).hide();
        $(this).parent().find(".all").show();
        $("#list-"+key).show();
    });

    $(".item-data").on("click", ".all", function() {
        var key = $(this).parent().attr('data-val');
        $(this).hide();
        $(this).parent().find(".one").show();
        $("#list-"+key).hide();
    });


</script>