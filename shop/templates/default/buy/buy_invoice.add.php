<?php ?>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<style>
</style>
<div class="ncc-receipt-info" style="margin-top: 20px">
    <form class="layui-form" action="">

        <div class="layui-form-item" pane="">
            <label class="layui-form-label" style="width: 150px">发票类型：</label>
            <div class="layui-input-block">
                <input name="inv_type" value="1"  lay-filter="inv_type" title="增值税普通发票" checked="" type="radio">
                <input name="inv_type" value="2"  lay-filter="inv_type" title="增值税专用发票" type="radio">
            </div>
        </div>


        <div class="layui-form-item" id="pt_title">
            <div class="layui-inline">
                <label class="layui-form-label" style="width: 150px">发票抬头：</label>
                <div class="layui-input-inline">
                    <select name="inv_person" lay-filter="inv_person">
                        <option value="1" selected="">单位</option>
                        <option value="2">个人</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline" id="pt_title">
                <div class="layui-input-inline" style="width: 380px;">
                    <input id="pt_company" name="pt_company" lay-verify="company_name" autocomplete="off" placeholder="请输入公司单位名称" class="layui-input" type="text">
                </div>
            </div>
        </div>

        <div class="layui-form-item" id="ze_title" style="display: none">
            <label class="layui-form-label" style="width: 150px">单位名称：</label>
            <div class="layui-input-block" style="width: 590px;margin-left: 180px;">
                <input id="ze_company" name="ze_company" lay-verify="" autocomplete="off" placeholder="请输入公司单位名称" class="layui-input" type="text">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px">发票内容：</label>
            <div class="layui-form-mid layui-word-aux">明细</div>
        </div>

        <div class="layui-form-item" id="taxpayer_item">
            <label class="layui-form-label" style="width: 150px">纳税人识别号：</label>
            <div class="layui-input-block" style="width: 590px;margin-left: 180px;">
                <input id="taxpayer" name="taxpayer" lay-verify="required" autocomplete="off" placeholder="请输入纳税人识别号" class="layui-input" type="text">
            </div>
        </div>
        <!--增值税发票内容-->
        <div class="layui-form-item" name="ze_info" style="display: none">
            <label class="layui-form-label" style="width: 150px">注册地址：</label>
            <div class="layui-input-block" style="width: 590px;margin-left: 180px;">
                <input id="reg_addr" name="reg_addr" lay-verify="" autocomplete="off" placeholder="请输入注册地址" class="layui-input" type="text">
            </div>
        </div>

        <div class="layui-form-item" name="ze_info" style="display: none">
            <label class="layui-form-label" style="width: 150px">注册电话：</label>
            <div class="layui-input-block" style="width: 590px;margin-left: 180px;">
                <input id="reg_phone" name="reg_phone" lay-verify="" autocomplete="off" placeholder="请输入注册电话" class="layui-input" type="text">
            </div>
        </div>

        <div class="layui-form-item" name="ze_info" style="display: none">
            <label class="layui-form-label" style="width: 150px">开户银行：</label>
            <div class="layui-input-block" style="width: 590px;margin-left: 180px;">
                <input id="reg_bname" name="reg_bname" lay-verify="" autocomplete="off" placeholder="请输入开户银行" class="layui-input" type="text">
            </div>
        </div>

        <div class="layui-form-item" name="ze_info" style="display: none">
            <label class="layui-form-label" style="width: 150px">银行账户：</label>
            <div class="layui-input-block" style="width: 590px;margin-left: 180px;">
                <input id="reg_baccount" name="reg_baccount" lay-verify="" autocomplete="off"
                       onkeyup="this.value=this.value.replace(/\D/g,'')"
                       onafterpaste="this.value=this.value.replace(/\D/g,'')"
                       placeholder="请输入银行账户" class="layui-input" type="text">
            </div>
        </div>

        <div class="layui-form-item" name="ze_info" style="display: none">
            <label class="layui-form-label" style="width: 150px"></label>
            <div class="layui-input-block layui-word-aux" style="width: 590px;margin-left: 180px;">
                如您是首次开具增值税专用发票，请您填写纳税人识别号等开票信息，并上传 加盖公章的营业执照副本、税务登记证副本、一般纳税人资格证书及银行开户 许可证扫描件邮寄给我们，收到您的开票资料后，我们会尽快审核。
            </div>
        </div>
        <!--增值税发票内容  end-->

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px">收票人姓名：</label>
            <div class="layui-input-block" style="width: 590px;margin-left: 180px;">
                <input name="rec_name" lay-verify="required" autocomplete="off" placeholder="请输入收票人姓名" class="layui-input" type="text">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px">收票人手机号码：</label>
            <div class="layui-input-block" style="width: 590px;margin-left: 180px;">
                <input name="rec_mobphone" lay-verify="title" autocomplete="off" placeholder="请输入手机号码" lay-verify="required|phone"
                       onkeyup="if(this.value.length>11){this.value=this.value.substr(0,4)};this.value=this.value.replace(/\D/g,'')"
                       onafterpaste="if(this.value.length>11){this.value=this.value.substr(0,4)};this.value=this.value.replace(/\D/g,'')"
                       class="layui-input" type="text">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px">收票人省份：</label>
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

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 150px">收票地址：</label>
            <div class="layui-input-block" style="width: 590px;margin-left: 180px;">
                <input name="rec_address" lay-verify="required" placeholder="请输入收票地址" autocomplete="off" class="layui-input" type="text">
            </div>
        </div>


        <div class="layui-form-item">
            <div class="layui-input-block" style="margin-left: 290px;margin-top: 30px;">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/form.js"></script>
<script type="text/javascript">

    layui.use(['form', 'element', 'layer'], function(){
        var form = layui.form,
            element = layui.element,
            layer = layui.layer;

        //自定义验证规则
        form.verify({
            company_name: function(value){
                if(value.length < 5){
                    return '公司名称至少得5个字符';
                }
            },
            phone: [/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(19[7]))\d{8}$/, '请输入正确的手机号码'],
        });

        form.on('radio(inv_type)', function(data){
            if(data.value == '1'){
                $("#pt_title").show();
                $("#pt_company").attr('lay-verify','company_name');

                $("#ze_title").hide();
                $("#ze_company").attr('lay-verify','');
                $("div[name='ze_info']").each(function(){
                    $(this).hide();
                    $(this).find("input").attr('lay-verify','');
                });
            }else{
                $("#pt_title").hide();
                $("#pt_company").attr('lay-verify','');

                $("#ze_title").show();
                $("#ze_company").attr('lay-verify','company_name');
                $("div[name='ze_info']").each(function(){
                    $(this).show();
                    $(this).find("input").attr('lay-verify','required');
                });
            }
        });


        form.on('select(inv_person)', function(data){
            if(data.value == '1'){
                 $("#pt_company").show();
                 $("#pt_company").attr('lay-verify','company_name');
                 $("#taxpayer_item").show();
                 $("#taxpayer").attr('lay-verify','required');
            }else{
                 $("#pt_company").hide();
                 $("#pt_company").attr('lay-verify','');
                 $("#taxpayer_item").hide();
                 $("#taxpayer").attr('lay-verify','');
            }
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


        //监听提交 add_inv
        form.on('submit(demo1)', function(data){
            $.ajax({
                type:"POST",
                //提交的网址
                url:"/shop/index.php?act=member_invoice&op=add_inv",
                data:data.field,
                datatype: "json",
                success:function(result){
                    var result = JSON.parse(result);
                    if(result.code == '1'){
                        layui.use('layer', function(){
                            var layer = layui.layer;
                            layer.alert('添加成功', {closeBtn: 0,title: '温馨提示',}, function(index){
                                parent.add_inv(result.list);
                            });
                        })
                    }else{
                        layui.use('layer', function(){
                            var layer = layui.layer;
                            layer.alert('添加失败', {closeBtn: 0,title: '温馨提示',}, function(index){
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