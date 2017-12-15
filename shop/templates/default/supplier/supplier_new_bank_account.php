<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/9
 * Time: 13:14
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
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>开户银行信息</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width:150px;">银行开户名<i class="bj">*</i>：</label>
            <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                <input name="account_names" lay-verify="required" autocomplete="off" placeholder="请填写公司在银行的开户名"
                       value="<?php echo $output['list']['account_name']?>" class="layui-input" type="text">
            </div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label" style="width:150px;">公司银行账号<i class="bj">*</i>：</label>
            <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                <input name="account_number" lay-verify="required" autocomplete="off"
                       value="<?php echo $output['list']['account_number']?>"
                       onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"
                       placeholder="请填写公司在银行的开户账号" class="layui-input" type="text">
            </div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label" style="width:150px;">开户银行名称<i class="bj">*</i>：</label>
            <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                <input name="account_bank_name" lay-verify="required" autocomplete="off" placeholder="请输入开户银行名称"
                       value="<?php echo $output['list']['bank_name']?>" class="layui-input" type="text">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width:130px;"></label>
            <div class="layui-input-inline layui-word-aux" style="width:540px;">
                请填写公司开户银行的名称，如：中国人民银行
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label" style="width:150px;">开户支行名称<i class="bj">*</i>：</label>
            <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                <input name="account_branch_name" lay-verify="required" autocomplete="off" placeholder="请输入开户支行名称"
                       value="<?php echo $output['list']['bank_branch_name']?>" class="layui-input" type="text">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width:130px;"></label>
            <div class="layui-input-inline layui-word-aux" style="width:540px;">
                请填写公司开户银行的支行名称，如：中国人民银行XX支行，就填写XX支行即可
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label" style="width:150px;">支行联行号<i class="bj">*</i>：</label>
            <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                <input name="account_branch_code" lay-verify="required" autocomplete="off" placeholder="请输入支行联行号"
                       value="<?php echo $output['list']['bank_branch_code']?>" class="layui-input" type="text">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label" style="width:150px;">开户银行所在地<i class="bj">*</i>：</label>
            <div class="layui-input-inline" style="width:155px;">
                <select name="account_province" id="account_province" lay-filter="account_province" lay-verify="required"></select>
            </div>
            <div class="layui-input-inline" style="width:173px;">
                <select name="account_city" id="account_city" lay-filter="account_city" lay-verify="required"></select>
            </div>
            <div class="layui-input-inline" style="width:173px;">
                <select name="account_county" id="account_county" lay-verify="required"></select>
            </div>
        </div>

        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label" style="width:150px;">银行许可证电子版<i class="bj">*</i>：</label>
            <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                <div id="account-show" class="layui-form-mid layui-word-aux" style="width:540px;
                <?php if(!empty($output['list']['account_path'])){?>display: none<?php }?>">
                    <button type="button" class="layui-btn upimg"
                            lay-data="{url: '/shop/index.php?act=supplier_join&op=upLoadFirld&type=account'}"
                            style="height:35px;line-height:35px;background-color: #71b704">
                        <i class="layui-icon"></i>
                        银行许可证电子版
                    </button>
                </div>
                <div id="account-none" class="layui-form-mid layui-word-aux" style="width:540px;
                <?php if(empty($output['list']['account_path'])){?>display: none<?php }?>">
                    <div style="width:500px;float: left">
                        <p id="account-name" style="float: left"><?php echo $output['list']['bank_licence_electronic'];?></p>
                        <a href="javascript:void(0)" onclick="del_path('account')">
                            <i title="删除文件" class="layui-icon" style="font-size: 20px;margin-left: 15px;color: #5cb85c">&#xe640;</i>
                        </a>
                        <a href="javascript:void(0)" onclick="look_path('account')">
                            <i title="查看文件" class="layui-icon" style="font-size: 20px;margin-left: 15px;color: #5cb85c">&#xe615;</i>
                        </a>
                    </div>
                </div>
                <input type="hidden" id="account_path" name="account_path" value="<?php echo $output['list']['account_path'];?>" lay-verify="required">
                <input type="hidden" id="account_name" name="account_name" value="<?php echo $output['list']['bank_licence_electronic'];?>" lay-verify="required">
                <input type="hidden" id="account_new" name="account_new" value="<?php echo $output['list']['bank_licence_electronic'];?>" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width:130px;"></label>
            <div class="layui-input-inline layui-word-aux" style="width:540px;">
                开户银行许可证电子版资质文件仅支持JPG\GIF\PNG格式图片，大小请控制在1M之内
            </div>
        </div>

        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>此开户账号是否为结算账号</legend>
        </fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width:130px;">是否为结算账号：</label>
            <div class="layui-input-block">
                <input name="is_settlement" value="2" lay-filter="is_settlement" title="是否为结算账号"
                       <?php if($output['list']['is_settlement'] == '1' || empty($output['list']['is_settlement'])){?>checked=""<?php }?> type="checkbox">
            </div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label" style="width:150px;"></label>
            <div class="layui-input-block" style="width:520px;margin-left: 180px;"> </div>
        </div>


        <input type="hidden" name="ac_id" value="<?php echo $output['list']['id'];?>">

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
        form.on('select(account_province)', function(data){
            $.ajax({
                type: "get",
                url: "/shop/index.php?act=base_list&op=cityList", // type =2表示查询市
                data: {"parent_id": data.value},
                dataType: "json",
                success: function(list) {
                    $("#account_city").html("<option value=''>请选择市</option>");
                    $.each(list, function(i, item) {
                        $("#account_city").append("<option value='" + item.code + "'>" + item.city_name + "</option>");
                    });
                    $("#account_county").html("<option value=''>请选择区／县</option>");
                    form.render('select');
                }
            });
        });

        form.on('select(account_city)', function(data){
            $.ajax({
                type: "get",
                url: "/shop/index.php?act=base_list&op=countyList", // type =2表示查询市
                data: {"parent_id": data.value},
                dataType: "json",
                success: function(list) {
                    $("#account_county").html("<option value=''>请选择区／县</option>");
                    $.each(list, function(i, item) {
                        $("#account_county").append("<option value='" + item.code + "'>" + item.city_name + "</option>");
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
                url:"/shop/index.php?act=supplier_member&op=newAccountItem",
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
        var account_province = '<?php echo $output['list']['account_province'];?>';
        var account_city = '<?php echo $output['list']['account_city'];?>';
        var account_county = '<?php echo $output['list']['account_county'];?>';
        $.ajax({
            type: "get",
            url: "/shop/index.php?act=base_list&op=provinceList", // type=1表示查询省份
            data: {},
            dataType: "json",
            success: function(data) {
                $("#account_province").html("<option value=''>请选择省份</option>");
                $.each(data, function(i, item) {
                    if(item.code == account_province){
                        $("#account_province").append("<option value='" + item.code + "' selected >" + item.city_name + "</option>");
                    }else{
                        $("#account_province").append("<option value='" + item.code + "'>" + item.city_name + "</option>");
                    }
                });
                layui.use(['form'], function(){
                    var form = layui.form;
                    form.render('select');
                });
            }
        });


        <?php if(!empty($output['list']['account_province'])){?>
        $.ajax({
            type: "get",
            url: "/shop/index.php?act=base_list&op=cityList", // type =2表示查询市
            data: {"parent_id": account_province},
            dataType: "json",
            success: function(list) {
                $("#account_city").html("<option value=''>请选择市</option>");
                $.each(list, function(i, item) {
                    if(item.code == account_city){
                        $("#account_city").append("<option value='" + item.code + "' selected >" + item.city_name + "</option>");
                    }else{
                        $("#account_city").append("<option value='" + item.code + "' >" + item.city_name + "</option>");
                    }
                });
                layui.use(['form'], function(){
                    var form = layui.form;
                    form.render('select');
                });
            }
        });
        <?php }?>


        <?php if(!empty($output['list']['account_city'])){?>
        $.ajax({
            type: "get",
            url: "/shop/index.php?act=base_list&op=countyList", // type =2表示查询市
            data: {"parent_id": account_city},
            dataType: "json",
            success: function(list) {
                $("#account_county").html("<option value=''>请选择县</option>");
                $.each(list, function(i, item) {
                    if(item.code == account_county){
                        $("#account_county").append("<option value='" + item.code + "' selected >" + item.city_name + "</option>");
                    }else{
                        $("#account_county").append("<option value='" + item.code + "' >" + item.city_name + "</option>");
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
