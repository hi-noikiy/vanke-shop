<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/9
 * Time: 13:14
 */
?>
<div class="breadcrumb">
    <i class="layui-icon">&#xe68e;</i>
    <a href="/shop/index.php?act=supplier_member">
        <span>我的商户中心</span>
    </a>
    <span class="arrow">></span> <span>认证管理</span>
    <span class="arrow">></span> <span>城市公司认证申请</span>
</div>
<div class="main" style="margin-top: 30px;">
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>城市公司认证申请</legend>
    </fieldset>
    <?php if(empty($output['join_type'])){?>
    <form class="layui-form" id="store_data">

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px">联系人信息：</label>
            <div class="layui-input-inline">
                <select name="contacts" lay-search="" lay-filter="contacts">
                    <option value="">新增联系人信息</option>
                    <?php if(!empty($output['contacts_list']) && is_array($output['contacts_list'])){?>
                        <?php foreach ($output['contacts_list'] as $contacts){?>
                            <option value="<?php echo $contacts['city_contacts_phone'];?>"><?php echo $contacts['city_contacts_name'];?></option>
                    <?php }}?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px"></label>
            <div class="layui-form-mid layui-word-aux">点击输入联系人姓名可进行搜索查询</div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px">联系人姓名<i class="bj">*</i>：</label>
            <div class="layui-input-inline">
                <input type="text" name="contacts_name" lay-verify="required" placeholder="联系人姓名" autocomplete="off" class="layui-input">
            </div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px">联系人电话<i class="bj">*</i>：</label>
            <div class="layui-input-inline">
                <input type="text" name="contacts_phone" lay-verify="required" placeholder="请输入联系人电话" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item" id="account_data">
            <label class="layui-form-label" style="width: 180px">开户银行信息<i class="bj">*</i>：</label>
            <div class="layui-input-inline" style="width: 350px;">
                <select name="account_bank" lay-search="" lay-verify="required" lay-filter="account">
                    <option value="">请选择开户银行信息</option>
                    <?php if(!empty($output['account_bank_list']) && is_array($output['account_bank_list'])){?>
                        <?php foreach ($output['account_bank_list'] as $account_bank){?>
                            <option value="<?php echo $account_bank['id'];?>"><?php echo $account_bank['bank_name']."（".$account_bank['account_number']."）";?></option>
                        <?php }}?>
                </select>
            </div>
        </div>

        <div class="layui-form-item" id="settlement_data">
            <label class="layui-form-label" style="width: 180px">结算银行信息<i class="bj">*</i>：</label>
            <div class="layui-input-inline" style="width: 350px;">
                <select name="settlement_bank" lay-search="" lay-verify="required" lay-filter="settlement">
                    <option value="">请选择结算银行信息</option>
                    <?php if(!empty($output['settlement_bank_list']) && is_array($output['settlement_bank_list'])){?>
                        <?php foreach ($output['settlement_bank_list'] as $settlement_bank){?>
                            <option value="<?php echo $settlement_bank['id'];?>"><?php echo $settlement_bank['bank_name']."（".$settlement_bank['settlement_number']."）";?></option>
                        <?php }}?>
                </select>
            </div>
        </div>

        <?php if(!empty($output['city_list']) && is_array($output['city_list'])){?>
            <div class="layui-form-item">
                <label class="layui-form-label"  style="width: 180px">可认证城市公司<i class="bj">*</i>：</label>
                <div class="layui-input-block">
                    <div style="width: 430px;margin-left: 100px;">
                        <?php foreach ($output['city_list'] as $city_data){?>
                            <input type="radio" name="city" value="<?php echo $city_data['id'];?>" title="<?php echo $city_data['city_name'];?>">
                        <?php }?>
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block" name="store-data">
                    <a class="layui-btn" lay-submit lay-filter="city-data" style="float: right;margin-right: 400px;margin-top: 50px">提交认证申请</a>
                </div>
            </div>

        <?php }else{?>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: 180px"></label>
                <div class="layui-form-mid layui-word-aux">已无可认证的城市公司</div>
            </div>

        <?php }?>
    </form>
    <?php }else{?>
    <div class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px"></label>
            <div class="layui-form-mid layui-word-aux"></div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px"></label>
            <div class="layui-form-mid layui-word-aux">认证申请已经提交，正在审核中，请耐心等待</div>
        </div>
    </div>
    <?php }?>
</div>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script>
    layui.use(['form', 'layer'], function(){
        var form = layui.form,
            layer = layui.layer;

        form.on('select(contacts)', function(data){
            if(data.value == ''){
                $("input[name='contacts_name']").val('');$("input[name='contacts_name']").attr("disabled",false);
                $("input[name='contacts_phone']").val('');$("input[name='contacts_phone']").attr("disabled",false);
            }else{
                $("input[name='contacts_name']").val(data.elem[data.elem.selectedIndex].text);$("input[name='contacts_name']").attr("disabled",true);
                $("input[name='contacts_phone']").val(data.value);$("input[name='contacts_phone']").attr("disabled",true);
            }
        });

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

    function add_account(list){
        var str = "<div class='layui-form-item' id='account_info'>";
        str+="<label class='layui-form-label' style='width: 180px'></label>";
        str+="<div class='layui-input-inline' style='width: 500px;'>";
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
