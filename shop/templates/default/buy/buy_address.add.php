<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/23
 * Time: 下午1:52
 * 添加新增地址信息数据
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新增地址</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
</head>
<body>

<div style="width:760px;margin-top: 30px;margin-left: 40px;">
<form class="layui-form" action="">
    <div class="layui-form-item">
        <label class="layui-form-label">收货人：<i style="color: #E83737;font-size:18px;">*</i></label>
        <div class="layui-input-block" style="width:400px;">
            <input name="send_name" autocomplete="off" lay-verify="required" placeholder="请输入收货人姓名" class="layui-input" type="text">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">手机号码：<i style="color: #E83737;font-size:18px;">*</i></label>
        <div class="layui-input-block" style="width:400px;">
            <input name="phone"  placeholder="请输入收货人联系手机号码" autocomplete="off" lay-verify="required|phone"
                   onkeyup="if(this.value.length>11){this.value=this.value.substr(0,4)};this.value=this.value.replace(/\D/g,'')"
                   onafterpaste="if(this.value.length>11){this.value=this.value.substr(0,4)};this.value=this.value.replace(/\D/g,'')"
                   class="layui-input" type="text">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">电话号码：</label>
            <div class="layui-input-inline" style="width: 100px;">
                <input name="area_code" placeholder="区号" autocomplete="off" class="layui-input" type="text"
                       onkeyup="if(this.value.length>4){this.value=this.value.substr(0,4)};this.value=this.value.replace(/\D/g,'')"
                       onafterpaste="if(this.value.length>4){this.value=this.value.substr(0,4)};this.value=this.value.replace(/\D/g,'')">
            </div>
            <div class="layui-form-mid">-</div>
            <div class="layui-input-inline" style="width: 180px;">
                <input name="tell_num" placeholder="电话" autocomplete="off" class="layui-input" type="text"
                       onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
            </div>
            <div class="layui-form-mid">-</div>
            <div class="layui-input-inline" style="width: 70px;">
                <input name="extension" placeholder="分机" autocomplete="off" class="layui-input" type="text"
                       onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">联系地址：<i style="color: #E83737;font-size:18px;">*</i></label>
        <div class="layui-input-inline">
            <select name="province" id="province" lay-filter="province" lay-verify="required"></select>
        </div>
        <div class="layui-input-inline">
            <select name="city" id="city" lay-filter="city" lay-verify="required"></select>
        </div>
        <div class="layui-input-inline">
            <select name="county" id="county"></select>
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">详细地址：<i style="color: #E83737;font-size:18px;">*</i></label>
        <div class="layui-input-block" style="width: 590px;">
            <input name="address" lay-verify="required" placeholder="请输入联系人详细地址信息" autocomplete="off" class="layui-input" type="text">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>
</div>

<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/form.js"></script>
<script>
    layui.use(['form', 'layedit', 'laydate'], function(){
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,laydate = layui.laydate;

        //自定义验证规则
        form.verify({
            phone: [/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(19[7]))\d{8}$/, '请输入正确的手机号码']
        });

        //监听下拉选择事件
        form.on('select(province)', function(data){
            $.ajax({
                type: "get",
                url: "/shop/index.php?act=member_address&op=getCityList", // type =2表示查询市
                data: {"parent_id": data.value, "type": "2"},
                dataType: "json",
                success: function(list) {
                    $("#city").html("<option value=''>请选择市</option>");
                    $.each(list, function(i, item) {
                        $("#city").append("<option value='" + item.area_id + "'>" + item.area_name + "</option>");
                    });
                    if(data.value == ''){
                        $("#county").html("<option value=''>请选择市</option>");
                    }
                    form.render('select');
                }
            });
        });

        form.on('select(city)', function(data){
            $.ajax({
                type: "get",
                url: "/shop/index.php?act=member_address&op=getCityList", // type =2表示查询市
                data: {"parent_id": data.value, "type": "3"},
                dataType: "json",
                success: function(list) {
                    $("#county").html("<option value=''>请选择区／县</option>");
                    $.each(list, function(i, item) {
                        $("#county").append("<option value='" + item.area_id + "'>" + item.area_name + "</option>");
                    });
                    form.render('select');
                }
            });
        });


        //监听提交
        form.on('submit(demo1)', function(data){
            $.ajax({
                type:"POST",
                //提交的网址
                url:"/shop/index.php?act=member_address&op=newAddress",
                data:data.field,
                datatype: "json",
                success:function(result){
                    var result = JSON.parse(result);
                    if(result.code == '1'){
                        layui.use('layer', function(){
                            var layer = layui.layer;
                            layer.alert('添加成功', {closeBtn: 0,title: '温馨提示',}, function(index){
                                parent.appStr(result.data);
                            });
                        })
                    }else{
                        layui.use('layer', function(){
                            var layer = layui.layer;
                            layer.alert(result,msg, {closeBtn: 0,title: '温馨提示',}, function(index){
                                layer.closeAll();
                            });
                        })
                    }
                }
            });
            return false;
        });


    });


    $(document).ready(function() {
        //  加载所有的省份
        $.ajax({
            type: "get",
            url: "/shop/index.php?act=member_address&op=getCityList", // type=1表示查询省份
            data: {"parent_id": "0", "type": "1"},
            dataType: "json",
            success: function(data) {
                $("#province").html("<option value=''>请选择省份</option>");
                $.each(data, function(i, item) {
                    $("#province").append("<option value='" + item.area_id + "'>" + item.area_name + "</option>");
                });
                layui.use(['form'], function(){
                    var form = layui.form;
                    form.render('select');
                });
            }
        });
    });

</script>

</body>
</html>
