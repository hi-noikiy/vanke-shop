<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/12/13
 * Time: 下午4:24
 */
?>
<style>
    .bj{
        color: #ff2222;
        font-size: 18px;
        margin-left: 3px;
        margin-right: 3px;
    }
</style>
<div class="main" style="margin-top: 30px;margin:0 auto;width: 800px">
    <form class="layui-form" action="">
        <!--结算银行账号-->
        <div>
            <fieldset name="settlement-list"  class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                <legend>结算银行账号</legend>
            </fieldset>

            <div name="settlement-list" class="layui-form-item">
                <label class="layui-form-label" style="width:150px;">银行开户名<i class="bj">*</i>：</label>
                <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                    <input name="settlement_name" autocomplete="off"
                           value="<?php echo $output['list']['settlement_name']?>" lay-verify="required"
                           placeholder="请填写结算银行开户名" class="layui-input settlement-ipt" type="text">
                </div>
            </div>

            <div name="settlement-list" class="layui-form-item">
                <label class="layui-form-label" style="width:150px;">公司银行账号<i class="bj">*</i>：</label>
                <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                    <input name="settlement_number" autocomplete="off"
                           value="<?php echo $output['list']['settlement_number']?>" lay-verify="required"
                           onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"
                           placeholder="请填写公司结算银行账号" class="layui-input settlement-ipt" type="text">
                </div>
            </div>

            <div name="settlement-list" class="layui-form-item">
                <label class="layui-form-label" style="width:150px;">结算银行所在地<i class="bj">*</i>：</label>
                <div class="layui-input-inline" style="width:155px;">
                    <select name="settlement_province" id="settlement_province" class="settlement-ipt" lay-verify="required"
                            lay-filter="settlement_province"></select>
                </div>
                <div class="layui-input-inline" style="width:173px;">
                    <select name="settlement_city" id="settlement_city" class="settlement-ipt" lay-verify="required"
                            lay-filter="settlement_city"></select>
                </div>
                <div class="layui-input-inline" style="width:173px;">
                    <select name="settlement_county" id="settlement_county" class="settlement-ipt" lay-verify="required"
                            lay-verify="settlement_county"></select>
                </div>
            </div>

            <div name="settlement-list" class="layui-form-item">
                <label class="layui-form-label" style="width:150px;">结算银行名称<i class="bj">*</i>：</label>
                <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                    <input name="settlement_bank_name" autocomplete="off"
                           value="<?php echo $output['list']['bank_name']?>" lay-verify="required"
                           placeholder="请填写结算银行名称" class="layui-input settlement-ipt" type="text">
                </div>
            </div>

            <div name="settlement-list" class="layui-form-item">
                <label class="layui-form-label" style="width:150px;">结算支行名称<i class="bj">*</i>：</label>
                <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                    <input name="settlement_branch_name" autocomplete="off"
                           value="<?php echo $output['list']['bank_branch_name']?>" lay-verify="required"
                           placeholder="请填写结算银行支行名称" class="layui-input settlement-ipt" type="text">
                </div>
            </div>

            <div name="settlement-list" class="layui-form-item">
                <label class="layui-form-label" style="width:150px;">结算支行联行号<i class="bj">*</i>：</label>
                <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                    <input name="settlement_branch_code" autocomplete="off"
                           value="<?php echo $output['list']['bank_branch_code']?>" lay-verify="required"
                           placeholder="请填写结算支行联行号" class="layui-input settlement-ipt" type="text">
                </div>
            </div>
        </div>

        <input type="hidden" name="ac_id" value="<?php echo $output['list']['id'];?>">


        <div class="layui-form-item">
            <label class="layui-form-label" style="width:150px;"></label>
            <div class="layui-input-block" style="width:520px;margin-left: 180px;"> </div>
        </div>


        <div class="layui-form-item">
            <div class="layui-input-block" style="margin-left: 270px;margin-top: 20px;margin-bottom: 30px;">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">提交保存</button>
            </div>
        </div>
    </form>
</div>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script>
    layui.use(['form', 'layedit', 'laydate', 'upload', 'table'], function(){
        var form = layui.form,
            layer = layui.layer,
            layedit = layui.layedit,
            table = layui.table,
            upload = layui.upload,
            laydate = layui.laydate;


        //自定义验证规则
        form.verify({
        });

        //监听下拉选择事件
        form.on('select(settlement_province)', function(data){
            $.ajax({
                type: "get",
                url: "/shop/index.php?act=base_list&op=cityList", // type =2表示查询市
                data: {"parent_id": data.value},
                dataType: "json",
                success: function(list) {
                    $("#settlement_city").html("<option value=''>请选择市</option>");
                    $.each(list, function(i, item) {
                        $("#settlement_city").append("<option value='" + item.code + "'>" + item.city_name + "</option>");
                    });
                    $("#settlement_county").html("<option value=''>请选择区／县</option>");
                    form.render('select');
                }
            });
        });

        form.on('select(settlement_city)', function(data){
            $.ajax({
                type: "get",
                url: "/shop/index.php?act=base_list&op=countyList", // type =2表示查询市
                data: {"parent_id": data.value},
                dataType: "json",
                success: function(list) {
                    $("#settlement_county").html("<option value=''>请选择区／县</option>");
                    $.each(list, function(i, item) {
                        $("#settlement_county").append("<option value='" + item.code + "'>" + item.city_name + "</option>");
                    });
                    form.render('select');
                }
            });
        });


        //上传
        upload.render({
            elem: '.upimg',
            method:'post',
            exts: 'jpg|jpeg|gif|png',
            accept: 'file',
            data:{},
            done: function(res){ //上传后的回调
                if(res.code == 0){
                    $("#"+res.data.type+"_path").val(res.data.src);
                    $("#"+res.data.type+"_name").val(res.data.name);
                    $("#"+res.data.type+"_new").val(res.data.paname);
                    //显示数据
                    $("#"+res.data.type+"-show").hide();
                    $("#"+res.data.type+"-none").show();
                    $("#"+res.data.type+"-name").text(res.data.name);
                }
            }
        })

        //监听提交
        form.on('submit(demo1)', function(data){
            $.ajax({
                type:"POST",
                //提交的网址
                url:"/shop/index.php?act=supplier_member&op=newSettlementItem",
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
                        parent.layer.closeAll();
                        parent.location.reload();
                    }else if(result.msg != ''){
                        layer.alert(result.msg, {closeBtn: 0,title: '温馨提示',}, function(index){
                            layer.close(index);
                        });
                    }else{
                        layer.alert('提交保存数据失败！请联系管理员', {closeBtn: 0,title: '温馨提示',}, function(index){
                            layer.close(index);
                        });
                    }
                }
            });
            return false;
        });


    });


    $(document).ready(function() {
        //  加载所有的省份
        var settlement_province = '<?php echo $output['list']['settlement_province'];?>';
        var settlement_city = '<?php echo $output['list']['settlement_city'];?>';
        var settlement_county = '<?php echo $output['list']['settlement_county'];?>';
        $.ajax({
            type: "get",
            url: "/shop/index.php?act=base_list&op=provinceList", // type=1表示查询省份
            data: {},
            dataType: "json",
            success: function(data) {
                $("#settlement_province").html("<option value=''>请选择省份</option>");
                $.each(data, function(i, item) {
                    if(item.code == settlement_province){
                        $("#settlement_province").append("<option value='" + item.code + "' selected >" + item.city_name + "</option>");
                    }else{
                        $("#settlement_province").append("<option value='" + item.code + "'>" + item.city_name + "</option>");
                    }
                });
                layui.use(['form'], function(){
                    var form = layui.form;
                    form.render('select');
                });
            }
        });


        <?php if(!empty($output['list']['settlement_province'])){?>
        $.ajax({
            type: "get",
            url: "/shop/index.php?act=base_list&op=cityList", // type =2表示查询市
            data: {"parent_id": settlement_province},
            dataType: "json",
            success: function(list) {
                $("#settlement_city").html("<option value=''>请选择市</option>");
                $.each(list, function(i, item) {
                    if(item.code == settlement_city){
                        $("#settlement_city").append("<option value='" + item.code + "' selected >" + item.city_name + "</option>");
                    }else{
                        $("#settlement_city").append("<option value='" + item.code + "' >" + item.city_name + "</option>");
                    }
                });
                layui.use(['form'], function(){
                    var form = layui.form;
                    form.render('select');
                });
            }
        });
        <?php }?>


        <?php if(!empty($output['list']['settlement_city'])){?>
        $.ajax({
            type: "get",
            url: "/shop/index.php?act=base_list&op=countyList", // type =2表示查询市
            data: {"parent_id": settlement_city},
            dataType: "json",
            success: function(list) {
                $("#settlement_county").html("<option value=''>请选择县</option>");
                $.each(list, function(i, item) {
                    if(item.code == settlement_county){
                        $("#settlement_county").append("<option value='" + item.code + "' selected >" + item.city_name + "</option>");
                    }else{
                        $("#settlement_county").append("<option value='" + item.code + "' >" + item.city_name + "</option>");
                    }
                });
                layui.use(['form'], function(){
                    var form = layui.form;
                    form.render('select');
                });
            }
        });
        <?php }?>


    });

    function look_path(id){
        url = $("#"+id+"_path").val();
        window.open(url);
    }

    function del_path(id){
        var path_url = $("#"+id+"_path").val();
        $.ajax({
            type:"POST",
            //提交的网址
            url:"/shop/index.php?act=supplier_join&op=delPath",
            data:{path: path_url},
            datatype: "json",
            success:function(result){
                if(result == '1'){
                    $("#"+id+"-show").show();
                    $("#"+id+"-none").hide();
                }else{
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert('删除失败', {closeBtn: 0,title: '温馨提示',}, function(index){
                            layer.close(layer.index);
                        });
                    })
                }
            }
        });
        return false;
    }

</script>