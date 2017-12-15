<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/12/6
 * Time: 上午10:52
 */
?>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/line/gloab.css" media="all">
<style>.step ul li{width: 16%;float: left;}
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
<div class="main" style="margin-top: 30px;">
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
            <li class="col-xs-4 on">
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
                <legend>开户银行信息</legend>
            </fieldset>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width:150px;">银行开户名<i class="bj">*</i>：</label>
                <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                    <input name="account_names" lay-verify="required" autocomplete="off" placeholder="请填写公司在银行的开户名"
                           value="<?php echo $output['account_bank']['account_name']?>" class="layui-input" type="text">
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label" style="width:150px;">公司银行账号<i class="bj">*</i>：</label>
                <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                    <input name="account_number" lay-verify="required" autocomplete="off"
                           value="<?php echo $output['account_bank']['account_number']?>"
                           onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"
                           placeholder="请填写公司在银行的开户账号" class="layui-input" type="text">
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label" style="width:150px;">开户银行名称<i class="bj">*</i>：</label>
                <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                    <input name="account_bank_name" lay-verify="required" autocomplete="off" placeholder="请输入开户银行名称"
                           value="<?php echo $output['account_bank']['bank_name']?>" class="layui-input" type="text">
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
                           value="<?php echo $output['account_bank']['bank_branch_name']?>" class="layui-input" type="text">
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
                           value="<?php echo $output['account_bank']['bank_branch_code']?>" class="layui-input" type="text">
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
                        <?php if(!empty($output['account_bank']['account_path'])){?>display: none<?php }?>">
                        <button type="button" class="layui-btn upimg"
                                lay-data="{url: '/shop/index.php?act=supplier_join&op=upLoadFirld&type=account'}"
                                style="height:35px;line-height:35px;background-color: #71b704">
                            <i class="layui-icon"></i>
                            银行许可证电子版
                        </button>
                    </div>
                    <div id="account-none" class="layui-form-mid layui-word-aux" style="width:540px;
                    <?php if(empty($output['account_bank']['account_path'])){?>display: none<?php }?>">
                        <div style="width:500px;float: left">
                            <p id="account-name" style="float: left"><?php echo $output['account_bank']['bank_licence_electronic'];?></p>
                            <a href="javascript:void(0)" onclick="del_path('account')">
                                <i title="删除文件" class="layui-icon" style="font-size: 20px;margin-left: 15px;color: #5cb85c">&#xe640;</i>
                            </a>
                            <a href="javascript:void(0)" onclick="look_path('account')">
                                <i title="查看文件" class="layui-icon" style="font-size: 20px;margin-left: 15px;color: #5cb85c">&#xe615;</i>
                            </a>
                        </div>
                    </div>
                    <input type="hidden" id="account_path" name="account_path" value="<?php echo $output['account_bank']['account_path'];?>" lay-verify="required">
                    <input type="hidden" id="account_name" name="account_name" value="<?php echo $output['account_bank']['bank_licence_electronic'];?>" lay-verify="required">
                    <input type="hidden" id="account_new" name="account_new" value="<?php echo $output['account_bank']['bank_licence_electronic'];?>" lay-verify="required">
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
                           <?php if($output['account_bank']['is_settlement'] == '1' || empty($output['account_bank']['is_settlement'])){?>checked=""<?php }?> type="checkbox">
                </div>
            </div>


            <!--结算银行账号-->
            <div id="settlement-item" <?php if($output['account_bank']['is_settlement'] == '1' || empty($output['account_bank']['is_settlement'])){?>style="display: none"<?php }?> >
                <fieldset name="settlement-list"  class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                    <legend>结算银行账号</legend>
                </fieldset>

                <div name="settlement-list" class="layui-form-item">
                    <label class="layui-form-label" style="width:150px;">银行开户名<i class="bj">*</i>：</label>
                    <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                        <input name="settlement_name" autocomplete="off"
                               value="<?php echo $output['settlement_bank']['settlement_name']?>"
                               <?php if($output['account_bank']['is_settlement'] == '2'){?>lay-verify="required"<?php }?>
                               placeholder="请填写结算银行开户名" class="layui-input settlement-ipt" type="text">
                    </div>
                </div>

                <div name="settlement-list" class="layui-form-item">
                    <label class="layui-form-label" style="width:150px;">公司银行账号<i class="bj">*</i>：</label>
                    <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                        <input name="settlement_number" autocomplete="off"
                               value="<?php echo $output['settlement_bank']['settlement_number']?>"
                               <?php if($output['account_bank']['is_settlement'] == '2'){?>lay-verify="required"<?php }?>
                               onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"
                               placeholder="请填写公司结算银行账号" class="layui-input settlement-ipt" type="text">
                    </div>
                </div>

                <div name="settlement-list" class="layui-form-item">
                    <label class="layui-form-label" style="width:150px;">结算银行所在地<i class="bj">*</i>：</label>
                    <div class="layui-input-inline" style="width:155px;">
                        <select name="settlement_province" id="settlement_province" class="settlement-ipt"
                                <?php if($output['account_bank']['is_settlement'] == '2'){?>lay-verify="required"<?php }?>
                                lay-filter="settlement_province"></select>
                    </div>
                    <div class="layui-input-inline" style="width:173px;">
                        <select name="settlement_city" id="settlement_city" class="settlement-ipt"
                                <?php if($output['account_bank']['is_settlement'] == '2'){?>lay-verify="required"<?php }?>
                                lay-filter="settlement_city"></select>
                    </div>
                    <div class="layui-input-inline" style="width:173px;">
                        <select name="settlement_county" id="settlement_county" class="settlement-ipt"
                                <?php if($output['account_bank']['is_settlement'] == '2'){?>lay-verify="required"<?php }?>
                                lay-verify="settlement_county"></select>
                    </div>
                </div>

                <div name="settlement-list" class="layui-form-item">
                    <label class="layui-form-label" style="width:150px;">结算银行名称<i class="bj">*</i>：</label>
                    <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                        <input name="settlement_bank_name" autocomplete="off"
                               value="<?php echo $output['settlement_bank']['bank_name']?>"
                               <?php if($output['account_bank']['is_settlement'] == '2'){?>lay-verify="required"<?php }?>
                               placeholder="请填写结算银行名称" class="layui-input settlement-ipt" type="text">
                    </div>
                </div>

                <div name="settlement-list" class="layui-form-item">
                    <label class="layui-form-label" style="width:150px;">结算支行名称<i class="bj">*</i>：</label>
                    <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                        <input name="settlement_branch_name" autocomplete="off"
                               value="<?php echo $output['settlement_bank']['bank_branch_name']?>"
                               <?php if($output['account_bank']['is_settlement'] == '2'){?>lay-verify="required"<?php }?>
                               placeholder="请填写结算银行支行名称" class="layui-input settlement-ipt" type="text">
                    </div>
                </div>

                <div name="settlement-list" class="layui-form-item">
                    <label class="layui-form-label" style="width:150px;">结算支行联行号<i class="bj">*</i>：</label>
                    <div class="layui-input-block" style="width:520px;margin-left: 180px;">
                        <input name="settlement_branch_code" autocomplete="off"
                               value="<?php echo $output['settlement_bank']['bank_branch_code']?>"
                               <?php if($output['account_bank']['is_settlement'] == '2'){?>lay-verify="required"<?php }?>
                               placeholder="请填写结算支行联行号" class="layui-input settlement-ipt" type="text">
                    </div>
                </div>
            </div>



            <div class="layui-form-item">
                <label class="layui-form-label" style="width:150px;"></label>
                <div class="layui-input-block" style="width:520px;margin-left: 180px;"> </div>
            </div>


            <input type="hidden" name="step_str" value="<?php echo $output['step_str'];?>">
            <input type="hidden" name="step_key" value="<?php echo $output['step_key'];?>">

            <div class="layui-form-item">
                <div class="layui-input-block" style="margin-left: 270px;margin-top: 20px;margin-bottom: 30px;">
                    <?php if($output['join_type'] == STORE_JOIN_STATE_CALLBACK){?>
                        <a href="/shop/index.php?act=supplier_join&step=business" class="layui-btn layui-btn-primary">上一步</a>
                    <?php }?>
                    <button class="layui-btn" lay-submit="" lay-filter="demo1">提交保存</button>
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
        });

        //监听下拉选择事件
        form.on('select(account_province)', function(data){
            get_city(data.value,"account_city",'');
        });

        form.on('select(account_city)', function(data){
            get_county(data.value,"account_county",'');
        });

        form.on('select(settlement_province)', function(data){
            get_city(data.value,"settlement_city",'');
        });

        form.on('select(settlement_city)', function(data){
            get_county(data.value,"settlement_county",'');
        });

        form.on('checkbox(is_settlement)', function(data){
            if(data.elem.checked){
                $("#settlement-item").hide();
                $("div[name='settlement-list']").each(function(){
                    $(this).find(".settlement-ipt").attr('lay-verify','');
                });
            }else{
                $("#settlement-item").show();
                $("div[name='settlement-list']").each(function(){
                    $(this).find(".settlement-ipt").attr('lay-verify','required');
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
                url:"/shop/index.php?act=supplier_join&op=bank",
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
                        window.location.href="/shop/index.php?act=supplier_join&step=email";
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
        var account_province = '<?php echo $output['account_bank']['account_province'];?>';
        var account_city = '<?php echo $output['account_bank']['account_city'];?>';
        var account_county = '<?php echo $output['account_bank']['account_county'];?>';

        get_province("account_province",account_province);

        var settlement_province = '<?php echo $output['settlement_bank']['settlement_province'];?>';
        var settlement_city = '<?php echo $output['settlement_bank']['settlement_city'];?>';
        var settlement_county = '<?php echo $output['settlement_bank']['settlement_county'];?>';

        get_province("settlement_province",settlement_province);


        <?php if($output['account_bank']['is_settlement'] == '2'){?>

            <?php if(!empty($output['account_bank']['account_province'])){?>
                get_city(account_province,"account_city",account_city);
            <?php }?>


            <?php if(!empty($output['account_bank']['account_city'])){?>
                get_county(account_city,"account_county",account_county);
            <?php }?>

            <?php if(!empty($output['settlement_bank']['settlement_province'])){?>
                get_city(settlement_province,"settlement_city",settlement_city);
            <?php }?>

            <?php if(!empty($output['settlement_bank']['settlement_city'])){?>
                get_county(settlement_city,"settlement_county",settlement_county);
            <?php }?>

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

