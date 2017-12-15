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
            <label class="layui-form-label" style="width: 180px">开户银行信息：</label>
            <div class="layui-input-inline" style="width: 350px;">
                <select name="bank_id" lay-search="" lay-verify="required" lay-filter="account">
                    <option value="">请选择开户银行信息</option>
                    <?php if(!empty($output['account_bank_list']) && is_array($output['account_bank_list'])){?>
                        <?php foreach ($output['account_bank_list'] as $account_bank){?>
                            <option value="<?php echo $account_bank['id'];?>"><?php echo $account_bank['bank_name']."（".$account_bank['account_number']."）";?></option>
                        <?php }}?>
                </select>
            </div>
        </div>
        <input name="type" value="account" type="hidden">
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


        form.on('select(account)', function(data){
            if(data.value == ''){
                $("#account_info").remove();$("#account_info_err").remove();
            }else{
                $.ajax({
                    type: "get",
                    url: "/shop/index.php?act=base_list&op=getAccountBank",
                    data: {"id": data.value},
                    dataType: "json",
                    beforeSend: function () {
                        $("#account_info").remove();
                    },
                    success: function(rest) {
                        if(rest.code == 1){
                            add_account(rest.list);
                        }else{
                            add_account_err();
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

    function add_account(list){
        var str = "<div class='layui-form-item' id='account_info'>";
        str+="<div class='layui-input-inline' style='width: 500px;margin-top: 20px;margin-left: 100px;'>";
        str+="<table style='background: #f2f2f2;border-collapse:separate; border-spacing:0px 10px;'>";
        str+="<tr><td style='width: 150px;' align='right'>银行开户名：</td>";
        str+="<td style='width: 350px;' align='left'>"+list.account_name+"</td></tr>";
        str+="<tr><td style='width: 150px;' align='right'>公司银行账号：</td>";
        str+="<td style='width: 350px;' align='left'>"+list.account_number+"</td></tr>";
        str+="<tr><td style='width: 150px;' align='right'>开户银行名称：</td>";
        str+="<td style='width: 350px;' align='left'>"+list.bank_name+"</td></tr>";
        str+="<tr><td style='width: 150px;' align='right'>开户银行支行名称：</td>";
        str+="<td style='width: 350px;' align='left'>"+list.bank_branch_name+"</td></tr>";
        str+="<tr><td style='width: 150px;' align='right'>支行联行号：</td>";
        str+="<td style='width: 350px;' align='left'>"+list.bank_branch_code+"</td></tr>";
        str+="<tr><td style='width: 150px;' align='right'>开户行所在地：</td>";
        str+="<td style='width: 350px;' align='left'>"+list.bank_address+"</td></tr>";
        str+="</table></div></div>";
        $("#account_data").append(str);
    }

    function add_account_err(){
        var str = "<div class='layui-form-item' id='account_info_err'>";
        str+="<label class='layui-form-label' style='width: 180px'></label>";
        str+="<div class='layui-form-mid layui-word-aux' style='width: 500px;'>";
        str+="加载开户银行详细信息失败";
        str+="</div></div>";
        $("#account_data").append(str);
    }


</script>
