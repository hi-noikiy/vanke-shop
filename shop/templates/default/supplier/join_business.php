<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/12/6
 * Time: 上午10:52
 */
?>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/line/gloab.css" media="all">
<style>
    .step ul li{width: 16%;float: left;}
    .bj{
        color: #ff2222;
        font-size: 18px;
        margin-left: 3px;
        margin-right: 3px;
    }
</style>
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
            <li class="col-xs-4 on">
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
                <legend>公司营业信息</legend>
            </fieldset>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width:130px;">营业执照号<i class="bj">*</i>：</label>
                <div class="layui-input-block" style="width:540px;margin-left: 160px;">
                    <input name="licence_number" lay-verify="required" autocomplete="off"
                           value="<?php echo $output['supplier']['business_licence_number'];?>"
                           placeholder="请输入营业执照号" class="layui-input" type="text">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label" style="width:130px;">营业执照所在地<i class="bj">*</i>：</label>
                <div class="layui-input-inline" style="width:173px;">
                    <select name="licence_province" id="province" lay-filter="province" lay-verify="required"></select>
                </div>
                <div class="layui-input-inline" style="width:173px;">
                    <select name="licence_city" id="city" lay-filter="city" lay-verify="required"></select>
                </div>
                <div class="layui-input-inline" style="width:173px;">
                    <select name="licence_county" id="county" lay-verify="required"></select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label" style="width:130px;">营业执照有效期<i class="bj">*</i>：</label>
                <div class="layui-input-inline" style="width:400px;">
                    <input type="text" id="licence_start" name="licence_start" value="<?php echo $output['supplier']['business_licence_start'];?>"
                           style="float: left;height:30px;width:160px" lay-verify="required">
                    <i class="layui-icon" style="font-size: 30px;float: left;margin-left: -35px;">&#xe637;</i>
                    <span style="font-size: 30px;float: left;margin-left: 5px;margin-right: 5px;">-</span>
                    <input type="text" id="licence_end" name="licence_end" value="<?php echo $output['supplier']['business_licence_end'];?>"
                           style="float: left;height:30px;width:160px" lay-verify="required">
                    <i class="layui-icon" style="font-size: 30px;float: left;margin-left: -35px;">&#xe637;</i>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label" style="width:130px;"></label>
                <div class="layui-input-inline layui-word-aux" style="width:400px;">
                    注：如果营业执照是无限期的有效期选择最大值
                </div>
            </div>

            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label" style="width:130px;">经营范围：</label>
                <div class="layui-input-block" style="width:540px;margin-left: 160px;">
                    <textarea name="licence_sphere" placeholder="请输入经营范围" class="layui-textarea"><?php echo $output['supplier']['business_sphere'];?></textarea>
                </div>
            </div>

            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label" style="width:130px;">营业执照电子版<i class="bj">*</i>：</label>
                <div class="layui-input-block" style="width:540px;margin-left: 160px;">
                    <div id="business-show" class="layui-form-mid layui-word-aux" style="width:540px;
                        <?php if(!empty($output['path']['business'])){?>display: none<?php }?>">
                        <button type="button" class="layui-btn upimg"
                                lay-data="{url: '/shop/index.php?act=supplier_join&op=upLoadFirld&type=business'}"
                                style="height:35px;line-height:35px;background-color: #71b704">
                            <i class="layui-icon"></i>
                            上传营业执照
                        </button>
                    </div>
                    <div id="business-none" class="layui-form-mid layui-word-aux" style="width:540px;
                    <?php if(empty($output['path']['business'])){?>display: none<?php }?>">
                        <div style="width:500px;float: left">
                            <p id="business-name" style="float: left"><?php echo $output['supplier']['business_licence_number_electronic'];?></p>
                            <a href="javascript:void(0)" onclick="del_path('business')">
                                <i title="删除文件" class="layui-icon" style="font-size: 20px;margin-left: 15px;color: #5cb85c">&#xe640;</i>
                            </a>
                            <a href="javascript:void(0)" onclick="look_path('business')">
                                <i title="查看文件" class="layui-icon" style="font-size: 20px;margin-left: 15px;color: #5cb85c">&#xe615;</i>
                            </a>
                        </div>
                    </div>
                    <input type="hidden" id="business_path" name="business_path" value="<?php echo $output['path']['business'];?>" lay-verify="required">
                    <input type="hidden" id="business_name" name="business_name" value="<?php echo $output['supplier']['business_licence_number_electronic'];?>" lay-verify="required">
                    <input type="hidden" id="business_new" name="business_new" value="<?php echo $output['supplier']['business_licence_number_electronic'];?>" lay-verify="required">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width:130px;"></label>
                <div class="layui-input-inline layui-word-aux" style="width:540px;">
                    营业执照电子版资质文件仅支持JPG\GIF\PNG格式图片，大小请控制在1M之内
                </div>
            </div>

            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                <legend>是否是一般纳税人</legend>
            </fieldset>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width:130px;">一般纳税人：</label>
                <div class="layui-input-block">
                    <input name="is_taxpayer" value="2" title="是一般纳税人"
                           <?php if($output['supplier']['is_taxpayer'] == '1' || empty($output['supplier']['is_taxpayer'])){?>checked=""<?php }?>
                            type="checkbox">
                </div>
            </div>

            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                <legend>是否是三证合一</legend>
            </fieldset>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width:130px;">是否是三证合一：</label>
                <div class="layui-input-block">
                    <input name="is_therea" title="三证合一" value="2" lay-filter="merge"
                           <?php if($output['supplier']['is_therea'] == '1' || empty($output['supplier']['is_taxpayer'])){?>checked=""<?php }?>
                            type="checkbox">
                </div>
            </div>

            <!--组织机构代码证-->
            <div id="therea_all" <?php if($output['supplier']['is_therea'] == '1' || empty($output['supplier']['is_taxpayer'])){?>style="display:none;"<?php }?>>
                <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                    <legend>组织机构代码证</legend>
                </fieldset>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:130px;">组织机构代码<i class="bj">*</i>：</label>
                    <div class="layui-input-block" style="width:540px;margin-left: 160px;">
                        <input name="organization_code" autocomplete="off"
                               value="<?php echo $output['supplier']['organization_code'];?>"
                               placeholder="请输入组织机构代码" class="layui-input merge-input" type="text">
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label" style="width:130px;">组织机构电子版<i class="bj">*</i>：</label>
                    <div class="layui-input-block" style="width:540px;margin-left: 160px;">
                        <div id="organization-show" class="layui-form-mid layui-word-aux" style="width:540px;
                        <?php if(!empty($output['path']['organization'])){?>display: none<?php }?>">
                            <button type="button" class="layui-btn upimg"
                                    lay-data="{url: '/shop/index.php?act=supplier_join&op=upLoadFirld&type=organization'}"
                                    style="height:35px;line-height:35px;background-color: #71b704">
                                <i class="layui-icon"></i>
                                组织机构代码证
                            </button>
                        </div>
                        <div id="organization-none" class="layui-form-mid layui-word-aux" style="width:540px;
                        <?php if(empty($output['path']['organization'])){?>display: none<?php }?>">
                            <div style="width:500px;float: left">
                                <p id="organization-name" style="float: left"><?php echo $output['supplier']['organization_code_electronic'];?></p>
                                <a href="javascript:void(0)" onclick="del_path('organization')">
                                    <i title="删除文件" class="layui-icon" style="font-size: 20px;margin-left: 15px;color: #5cb85c">&#xe640;</i>
                                </a>
                                <a href="javascript:void(0)" onclick="look_path('organization')">
                                    <i title="查看文件" class="layui-icon" style="font-size: 20px;margin-left: 15px;color: #5cb85c">&#xe615;</i>
                                </a>
                            </div>
                        </div>
                        <input type="hidden" id="organization_path" name="organization_path"
                               class="merge-input" value="<?php echo $output['path']['organization'];?>" >
                        <input type="hidden" id="organization_name" name="organization_name"
                               class="merge-input" value="<?php echo $output['supplier']['organization_code_electronic'];?>" >
                        <input type="hidden" id="organization_new" name="organization_new"
                               class="merge-input" value="<?php echo $output['supplier']['organization_code_electronic'];?>" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:130px;"></label>
                    <div class="layui-input-inline layui-word-aux" style="width:540px;">
                        组织机构代码证电子版资质文件仅支持JPG\GIF\PNG格式图片，大小请控制在1M之内
                    </div>
                </div>

                <!--税务登记证-->

                <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                    <legend>税务登记证</legend>
                </fieldset>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:130px;">税务登记证号<i class="bj">*</i>：</label>
                    <div class="layui-input-block" style="width:540px;margin-left: 160px;">
                        <input name="registration_code" autocomplete="off" placeholder="请输入税务登记证号"
                               value="<?php echo $output['supplier']['tax_registration_certificate'];?>"
                               class="layui-input merge-input" type="text">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:130px;">纳税人识别号<i class="bj">*</i>：</label>
                    <div class="layui-input-block" style="width:540px;margin-left: 160px;">
                        <input name="taxpayer_code" autocomplete="off" placeholder="请输入纳税人识别号"
                               value="<?php echo $output['supplier']['taxpayer_id'];?>"
                               class="layui-input merge-input" type="text">
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label" style="width:130px;">税务证号电子版<i class="bj">*</i>：</label>
                    <div class="layui-input-block" style="width:540px;margin-left: 160px;">
                        <div id="registration-show" class="layui-form-mid layui-word-aux" style="width:540px;
                        <?php if(!empty($output['path']['registration'])){?>display: none<?php }?>">
                            <button type="button" class="layui-btn upimg"
                                    lay-data="{url: '/shop/index.php?act=supplier_join&op=upLoadFirld&type=registration'}"
                                    style="height:35px;line-height:35px;background-color: #71b704">
                                <i class="layui-icon"></i>
                                上传税务登记证
                            </button>
                        </div>
                        <div id="registration-none" class="layui-form-mid layui-word-aux" style="width:540px;
                        <?php if(empty($output['path']['registration'])){?>display: none<?php }?>">
                            <div style="width:500px;float: left">
                                <p id="registration-name" style="float: left"><?php echo $output['supplier']['tax_registration_certificate_electronic'];?></p>
                                <a href="javascript:void(0)" onclick="del_path('registration')">
                                    <i title="删除文件" class="layui-icon" style="font-size: 20px;margin-left: 15px;color: #5cb85c">&#xe640;</i>
                                </a>
                                <a href="javascript:void(0)" onclick="look_path('registration')">
                                    <i title="查看文件" class="layui-icon" style="font-size: 20px;margin-left: 15px;color: #5cb85c">&#xe615;</i>
                                </a>
                            </div>
                        </div>
                        <input type="hidden" id="registration_path" name="registration_path"
                               class="merge-input" value="<?php echo $output['path']['registration'];?>" >
                        <input type="hidden" id="registration_name" name="registration_name"
                               class="merge-input" value="<?php echo $output['supplier']['tax_registration_certificate_electronic'];?>" >
                        <input type="hidden" id="registration_new" name="registration_new"
                               class="merge-input" value="<?php echo $output['supplier']['tax_registration_certificate_electronic'];?>" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:130px;"></label>
                    <div class="layui-input-inline layui-word-aux" style="width:540px;">
                        税务登记证电子版资质文件仅支持JPG\GIF\PNG格式图片，大小请控制在1M之内
                    </div>
                </div>

            </div>



            <input type="hidden" name="step_str" value="<?php echo $output['step_str'];?>">
            <input type="hidden" name="step_key" value="<?php echo $output['step_key'];?>">

            <div class="layui-form-item">
                <div class="layui-input-block" style="margin-left: 270px;margin-top: 20px;margin-bottom: 30px;">
                    <?php if($output['join_type'] == STORE_JOIN_STATE_CALLBACK){?>
                        <a href="/shop/index.php?act=supplier_join&step=company" class="layui-btn layui-btn-primary">上一步</a>
                    <?php }?>
                    <button class="layui-btn" lay-submit="" lay-filter="demo1">保存提交</button>
                    <?php if($output['join_type'] == STORE_JOIN_STATE_CALLBACK){?>
                        <a href="/shop/index.php?act=supplier_join&step=bank" class="layui-btn layui-btn-primary">下一步</a>
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
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/city_select.js"></script>
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
            company_name: function(value){
                if(value.length < 5){
                    return '公司名称至少得5个字符啊';
                }
            }
            ,phone: [/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8])|(19[7]))\d{8}$/, '请输入正确的手机号码']
        });

        //执行一个laydate实例
        laydate.render({
            elem: '#licence_start', //指定元素
        });

        laydate.render({
            elem: '#licence_end', //指定元素
        });

        //监听下拉选择事件
        form.on('select(province)', function(data){
            get_city(data.value,"city",'');
        });

        form.on('select(city)', function(data){
            get_county(data.value,"county",'');
        });

        form.on('checkbox(merge)', function(data){
            if(data.elem.checked){
                $("#therea_all").hide();
                $("#therea_all").find(".merge-input").each(function(){
                    $(this).attr('lay-verify','');
                });
            }else{
                $("#therea_all").show();
                $("#therea_all").find(".merge-input").each(function(){
                    $(this).attr('lay-verify','required');
                });
            }
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
                url:"/shop/index.php?act=supplier_join&op=business",
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
                        window.location.href="/shop/index.php?act=supplier_join&step=bank";
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
        var province = '<?php echo $output['city_list']['province'];?>';
        var city = '<?php echo $output['city_list']['city'];?>';
        var county = '<?php echo $output['city_list']['county'];?>';

        get_province("province",province);

        <?php if(!empty($output['city_list']['province'])){?>
            get_city(province,"city",city);
        <?php }?>


        <?php if(!empty($output['city_list']['city'])){?>
            get_county(city,"county",county);
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

