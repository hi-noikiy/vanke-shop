<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/12/6
 * Time: 上午10:52
 */
?>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/line/gloab.css" media="all">
<style>.step ul li{width: 16%;float: left;}</style>
<div class="breadcrumb">
    <span class="icon-home"></span>
    <span>首页</span>
    <span class="arrow">></span> <span>供应商认证申请</span>
</div>
<div class="main" style="margin-top: 30px">
    <div class="step" style="margin-bottom: 10px">
        <ul>
            <li class="col-xs-4 on">
                <span class="num"><em class="f-r5"></em><i>1</i></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">签订认证协议</p>
            </li>
            <li class="col-xs-4 on">
                <span class="num"><em class="f-r5"></em><i>2</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">公司基本信息</p>
            </li>
            <li class="col-xs-4">
                <span class="num"><em class="f-r5"></em><i>3</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">公司营业信息</p>
            </li>
            <li class="col-xs-4">
                <span class="num"><em class="f-r5"></em><i>4</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">公司银行信息</p>
            </li>
            <li class="col-xs-4">
                <span class="num"><em class="f-r5"></em><i>5</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">等待验证邮箱</p>
            </li>
            <li class="col-xs-4">
                <span class="num"><em class="f-r5"></em><i>6</i></span>
                <span class="line_bg lbg-l"></span>
                <p class="lbg-txt">等待资质审核</p>
            </li>
        </ul>
    </div>
    <div style="width: 750px;margin:0 auto">
        <form class="layui-form" action="">
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                <legend>公司基本信息</legend>
            </fieldset>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width:110px;">公司名称：</label>
                <div class="layui-input-block" style="width:540px;margin-left: 140px;">
                    <input name="company_name" lay-verify="company_name" autocomplete="off"
                           value="<?php echo $output['supplier']['company_name'];?>"
                           placeholder="请输入公司名称" class="layui-input" type="text">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label" style="width:110px;">公司所在地：</label>
                <div class="layui-input-inline" style="width:173px;">
                    <select name="province" id="province" lay-filter="province" lay-verify="required"></select>
                </div>
                <div class="layui-input-inline" style="width:173px;">
                    <select name="city" id="city" lay-filter="city" lay-verify="required"></select>
                </div>
                <div class="layui-input-inline" style="width:173px;">
                    <select name="county" id="county"></select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label" style="width:110px;">公司详细地址：</label>
                <div class="layui-input-block" style="width:540px;margin-left: 140px;">
                    <input name="address" lay-verify="required" placeholder="请输入公司详细地址"
                           value="<?php echo $output['supplier']['company_address_detail'];?>"
                           autocomplete="off" class="layui-input" type="text">
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label" style="width:110px;">公司电话：</label>
                    <div class="layui-input-inline" style="width: 100px;">
                        <input name="area_code" placeholder="区号" autocomplete="off" class="layui-input" type="text"
                               value="<?php echo $output['company_phone']['area_code'];?>"
                               onkeyup="if(this.value.length>4){this.value=this.value.substr(0,4)};this.value=this.value.replace(/\D/g,'')"
                               onafterpaste="if(this.value.length>4){this.value=this.value.substr(0,4)};this.value=this.value.replace(/\D/g,'')">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 180px;">
                        <input name="tell_num" placeholder="电话" autocomplete="off" class="layui-input" type="text"
                               value="<?php echo $output['company_phone']['tell_num'];?>" lay-verify="required"
                               onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 70px;">
                        <input name="extension" placeholder="分机" autocomplete="off" class="layui-input"
                               value="<?php echo $output['company_phone']['extension'];?>" type="text"
                               onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label" style="width:110px;">员工总数：</label>
                    <div class="layui-input-inline" style="width: 130px;">
                        <input name="employee_count" placeholder="员工总数" autocomplete="off" class="layui-input"
                               value="<?php echo $output['supplier']['company_employee_count'];?>" type="text" lay-verify="required"
                               onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
                    </div>
                    <div class="layui-form-mid">人</div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label" style="width:110px;">注册资金：</label>
                    <div class="layui-input-inline" style="width: 130px;">
                        <input name="registered_capital" placeholder="注册资金" autocomplete="off" class="layui-input"
                               value="<?php echo $output['supplier']['company_registered_capital'];?>" type="text" lay-verify="required"
                               onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
                    </div>
                    <div class="layui-form-mid">万元</div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label" style="width:110px;">公司法人：</label>
                    <div class="layui-input-inline" style="width: 130px;">
                        <input name="legal_person" placeholder="公司法人" autocomplete="off" class="layui-input"
                               value="<?php echo $output['supplier']['legal_person'];?>" type="text">
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label" style="width:110px;">城市公司：</label>
                <div class="layui-input-inline" style="width:300px;">
                    <select name="city_center" id="city_center" lay-filter="city_center" lay-verify="required"></select>
                </div>
            </div>

            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                <legend>公司联系人信息</legend>
            </fieldset>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label" style="width:110px;">联系人姓名：</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input name="contacts_name" placeholder="请输入联系人姓名"
                               value="<?php echo $output['supplier']['contacts_name'];?>"
                               lay-verify="required" autocomplete="off" class="layui-input" type="text" >
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label" style="width:110px;">联系人手机：</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input name="contacts_phone" placeholder="请输入联系人手机"
                               value="<?php echo $output['supplier']['contacts_phone'];?>"
                               onkeyup="if(this.value.length>11){this.value=this.value.substr(0,4)};this.value=this.value.replace(/\D/g,'')"
                               onafterpaste="if(this.value.length>11){this.value=this.value.substr(0,4)};this.value=this.value.replace(/\D/g,'')"
                               lay-verify="required|phone" autocomplete="off" class="layui-input" type="text" >
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label" style="width:110px;">联系人邮箱：</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input name="contacts_email" id="contacts_email" placeholder="请输入联系人邮箱"
                               value="<?php echo $output['supplier']['contacts_email'];?>"
                               lay-verify="required|email" autocomplete="off" class="layui-input" type="text" >
                    </div>
                </div>
            </div>

            <input type="hidden" name="step_str" value="<?php echo $output['step_str'];?>">
            <input type="hidden" name="step_key" value="<?php echo $output['step_key'];?>">

            <div class="layui-form-item">
                <div class="layui-input-block" style="margin-left: 270px;margin-top: 20px;margin-bottom: 30px;">
                    <?php if($output['join_type'] == STORE_JOIN_STATE_CALLBACK){?>
                        <a href="/shop/index.php?act=supplier_join&step=agreement" class="layui-btn layui-btn-primary">上一步</a>
                    <?php }?>
                    <button class="layui-btn" lay-submit="" lay-filter="demo1">提交保存</button>
                    <?php if($output['join_type'] == STORE_JOIN_STATE_CALLBACK){?>
                        <a href="/shop/index.php?act=supplier_join&step=business" class="layui-btn layui-btn-primary">下一步</a>
                    <?php }?>
                </div>
            </div>
        </form>
    </div>
</div>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
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
            company_name: function(value){
                if(value.length < 5){
                    return '公司名称至少得5个字符啊';
                }
            }
            ,phone: [/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(19[7]))\d{8}$/, '请输入正确的手机号码']
        });

        //监听下拉选择事件
        form.on('select(province)', function(data){
            $.ajax({
                type: "get",
                url: "/shop/index.php?act=base_list&op=cityList", // type =2表示查询市
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
                url: "/shop/index.php?act=base_list&op=cityList", // type =2表示查询市
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
                url:"/shop/index.php?act=supplier_join&op=checkEmail",
                data:{'email':$("#contacts_email").val()},
                datatype: "json",
                success:function(result){
                    var result = JSON.parse(result);
                    if(result.code == '1'){
                        $.ajax({
                            type:"POST",
                            //提交的网址
                            url:"/shop/index.php?act=supplier_join&op=company",
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
                                    window.location.href="/shop/index.php?act=supplier_join&step=business";
                                }else{
                                    layer.alert('提交保存数据失败！请联系管理员', {closeBtn: 0,title: '温馨提示',}, function(index){
                                        layer.close(index);
                                    });
                                }
                            }
                        });
                    }else{
                        layer.alert('邮箱已被占用！', {closeBtn: 0,title: '温馨提示',}, function(index){
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
        var province = '<?php echo $output['city_list']['province'];?>';
        var city = '<?php echo $output['city_list']['city'];?>';
        var county = '<?php echo $output['city_list']['county'];?>';
        $.ajax({
            type: "get",
            url: "/shop/index.php?act=base_list&op=cityList", // type=1表示查询省份
            data: {"parent_id": "0", "type": "1"},
            dataType: "json",
            success: function(data) {
                $("#province").html("<option value=''>请选择省份</option>");
                $.each(data, function(i, item) {
                    if(item.area_id == province){
                        $("#province").append("<option value='" + item.area_id + "' selected >" + item.area_name + "</option>");
                    }else{
                        $("#province").append("<option value='" + item.area_id + "'>" + item.area_name + "</option>");
                    }
                });
                layui.use(['form'], function(){
                    var form = layui.form;
                    form.render('select');
                });
            }
        });

        <?php if(!empty($output['city_list']['province'])){?>
            $.ajax({
                type: "get",
                url: "/shop/index.php?act=base_list&op=cityList", // type =2表示查询市
                data: {"parent_id": province, "type": "2"},
                dataType: "json",
                success: function(list) {
                    $("#city").html("<option value=''>请选择市</option>");
                    $.each(list, function(i, item) {
                        if(item.area_id == city){
                            $("#city").append("<option value='" + item.area_id + "' selected >" + item.area_name + "</option>");
                        }else{
                            $("#city").append("<option value='" + item.area_id + "' >" + item.area_name + "</option>");
                        }
                    });
                    layui.use(['form'], function(){
                        var form = layui.form;
                        form.render('select');
                    });
                }
            });
        <?php }?>


        <?php if(!empty($output['city_list']['city'])){?>
            $.ajax({
                type: "get",
                url: "/shop/index.php?act=base_list&op=cityList", // type =2表示查询市
                data: {"parent_id": city, "type": "3"},
                dataType: "json",
                success: function(list) {
                    $("#county").html("<option value=''>请选择县</option>");
                    $.each(list, function(i, item) {
                        if(item.area_id == county){
                            $("#county").append("<option value='" + item.area_id + "' selected >" + item.area_name + "</option>");
                        }else{
                            $("#county").append("<option value='" + item.area_id + "' >" + item.area_name + "</option>");
                        }
                    });
                    layui.use(['form'], function(){
                        var form = layui.form;
                        form.render('select');
                    });
                }
            });
        <?php }?>

        var city_center = '<?php echo $output['city_id'];?>';
        $.ajax({
            type: "get",
            url: "/shop/index.php?act=base_list&op=cityCenter", // type=1表示查询省份
            data: {},
            dataType: "json",
            success: function(data) {
                $("#city_center").html("<option value=''>请选城市公司所在地</option>");
                $.each(data, function(i, item) {
                    if(item.id == city_center){
                        $("#city_center").append("<option value='" + item.id + "' selected >" + item.city_name + "</option>");
                    }else{
                        $("#city_center").append("<option value='" + item.id + "'>" + item.city_name + "</option>");
                    }
                });
                layui.use(['form'], function(){
                    var form = layui.form;
                    form.render('select');
                });
            }
        });
    });


</script>

