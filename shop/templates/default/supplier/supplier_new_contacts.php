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
    <form class="layui-form" id="store_data">
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px"></label>
            <div class="layui-form-mid layui-word-aux"></div>
        </div>

        <?php if(!empty($output['contacts_list']) && is_array($output['contacts_list'])){?>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px">联系人信息：</label>
            <div class="layui-input-inline">
                <select name="contacts" lay-search="" lay-filter="contacts">
                    <option value="">新增联系人信息</option>
                        <?php foreach ($output['contacts_list'] as $contacts){?>
                            <option value="<?php echo $contacts['city_contacts_phone'];?>"><?php echo $contacts['city_contacts_name'];?></option>
                        <?php }?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px"></label>
            <div class="layui-form-mid layui-word-aux">点击输入联系人姓名可进行搜索查询</div>
        </div>
        <?php }?>

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px">联系人姓名<i class="bj">*</i>：</label>
            <div class="layui-input-inline">
                <input type="text" name="contacts_name" id="" lay-verify="required"
                       value="<?php echo $output['list']['city_contacts_name']?>"
                       placeholder="联系人姓名" autocomplete="off" class="layui-input">
            </div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px">联系人电话<i class="bj">*</i>：</label>
            <div class="layui-input-inline">
                <input type="text" name="contacts_phone" id="contacts_phone"
                       value="<?php echo $output['list']['city_contacts_phone']?>"
                       lay-verify="required" placeholder="请输入联系人电话" autocomplete="off" class="layui-input">
            </div>
        </div>

        <input type="hidden" id="city" name="city" value="<?php echo $output['city'];?>" >


        <div class="layui-form-item">
            <div class="layui-input-block" name="store-data">
                <a class="layui-btn" lay-submit lay-filter="city-data" style="float: right;margin-right: 400px;margin-top: 50px">提交</a>
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

        form.on('select(contacts)', function(data){
            if(data.value == ''){
                $("input[name='contacts_name']").val('');$("input[name='contacts_name']").attr("disabled",false);
                $("input[name='contacts_phone']").val('');$("input[name='contacts_phone']").attr("disabled",false);
            }else{
                $("input[name='contacts_name']").val(data.elem[data.elem.selectedIndex].text);
                $("input[name='contacts_name']").attr("disabled",true);
                $("input[name='contacts_phone']").val(data.value);
                $("input[name='contacts_phone']").attr("disabled",true);
            }
        });


        //监听提交
        form.on('submit(city-data)', function(data){
            $.ajax({
                type:"POST",
                //提交的网址
                url:"/shop/index.php?act=supplier_member&op=newContactsItem",
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
        });

    });


</script>
