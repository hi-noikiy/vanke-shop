<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/9
 * Time: 13:14
 */
?>
<div class="main" style="margin-top: 30px;margin:0 auto;width: 700px">
    <form class="layui-form" id="store_data">

        <div class="layui-form-item" style="margin-top: 40px;">
            <label class="layui-form-label" style="width: 180px">城市公司：</label>
            <div class="layui-form-mid layui-word-aux"><?php echo $output['city']['city_name']?></div>
            <input name="city" value="<?php echo $output['city']['id']?>" type="hidden">
        </div>


        <div class="layui-form-item" id="account_data">
            <label class="layui-form-label" style="width: 180px">结算银行信息：</label>
            <div class="layui-input-inline" style="width: 350px;">
                <select name="bank_id" lay-search="" lay-verify="required" lay-filter="settlement">
                    <option value="">请选择结算银行信息</option>
                    <?php if(!empty($output['settlement_bank_list']) && is_array($output['settlement_bank_list'])){?>
                        <?php foreach ($output['settlement_bank_list'] as $settlement_bank){?>
                            <option value="<?php echo $settlement_bank['id'];?>"><?php echo $settlement_bank['bank_name']."（".$settlement_bank['settlement_number']."）";?></option>
                        <?php }}?>
                </select>
            </div>
        </div>
        <input name="type" value="settlement" type="hidden">
        <div class="layui-form-item">
            <div class="layui-input-block" name="store-data">
                <a class="layui-btn" lay-submit lay-filter="city-data" style="float: right;margin-right: 330px;margin-top: 50px">提交</a>
            </div>
        </div>
    </form>
</div>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script>
    layui.use(['form', 'layer'], function(){
        var form = layui.form,
            layer = layui.layer;


        form.on('select(settlement)', function(data){
            if(data.value == ''){
                $("#settlement_info").remove();$("#settlement_info_err").remove();
            }else{
                $.ajax({
                    type: "get",
                    url: "/shop/index.php?act=base_list&op=getSettlementBank",
                    data: {"id": data.value},
                    dataType: "json",
                    beforeSend: function () {
                        $("#settlement_info").remove();
                    },
                    success: function(rest) {
                        if(rest.code == 1){
                            add_settlement(rest.list);
                        }else{
                            add_settlement_err();
                        }
                    }
                });
            }
        });


        //监听提交
        form.on('submit(city-data)', function(data){
            $.ajax({
                type:"POST",
                //提交的网址
                url:"/shop/index.php?act=supplier_member&op=bingBank",
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
                        parent.location.reload();
                        parent.layer.closeAll();
                    }else{
                        if(result.msg == ''){
                            layer.alert('提交保存数据失败！请联系管理员', {closeBtn: 0,title: '温馨提示',}, function(index){
                                parent.layer.closeAll();
                            });
                        }else{
                            layer.alert(result.msg, {closeBtn: 0,title: '温馨提示',}, function(index){
                                parent.layer.closeAll();
                            });
                        }
                    }
                }
            });
            return false;
        });

    });

    function add_settlement(list){
        var str = "<div class='layui-form-item' id='account_info'>";
        str+="<label class='layui-form-label' style='width: 180px'></label>";
        str+="<div class='layui-input-inline' style='width: 500px;'>";
        str+="<table style='background: #f2f2f2;border-collapse:separate; border-spacing:0px 10px;'>";
        str+="<tr><td style='width: 150px;' align='right'>结算银行开户名：</td>";
        str+="<td style='width: 350px;' align='left'>"+list.settlement_name+"</td></tr>";
        str+="<tr><td style='width: 150px;' align='right'>结算银行账号：</td>";
        str+="<td style='width: 350px;' align='left'>"+list.settlement_number+"</td></tr>";
        str+="<tr><td style='width: 150px;' align='right'>结算银行名称：</td>";
        str+="<td style='width: 350px;' align='left'>"+list.bank_name+"</td></tr>";
        str+="<tr><td style='width: 150px;' align='right'>结算银行支行名称：</td>";
        str+="<td style='width: 350px;' align='left'>"+list.bank_branch_name+"</td></tr>";
        str+="<tr><td style='width: 150px;' align='right'>结算支行联行号：</td>";
        str+="<td style='width: 350px;' align='left'>"+list.bank_branch_code+"</td></tr>";
        str+="<tr><td style='width: 150px;' align='right'>结算行所在地：</td>";
        str+="<td style='width: 350px;' align='left'>"+list.bank_address+"</td></tr>";
        str+="</table></div></div>";
        $("#settlement_data").append(str);
    }

    function add_settlement_err(){
        var str = "<div class='layui-form-item' id='account_info_err'>";
        str+="<label class='layui-form-label' style='width: 180px'></label>";
        str+="<div class='layui-form-mid layui-word-aux' style='width: 500px;'>";
        str+="加载开户银行详细信息失败";
        str+="</div></div>";
        $("#settlement_data").append(str);
    }

</script>
