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
            <li class="col-xs-4 on">
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
    <div style="width: 750px;margin:0 auto;margin-top: 100px">
        <?php if($output['email_type'] == '2'){?>

            <form class="layui-form" action="">

                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:100%;text-align:center">邮件已发送至您的邮箱</label>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:100%;text-align:center">请登陆您的邮箱</label>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:100%;text-align:center">请点邮件上的地址以便我们完成最后的认证</label>
                </div>

            </form>
        <?php }else{?>
            <form class="layui-form" action="">
                <div class="layui-form-item">
                    <label id="log_msg" class="layui-form-label" style="width:100%;text-align:center">恭喜您，提交资料成功,请点击发送邮箱以便我们完成最后的认证</label>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block" style="margin-left: 315px;margin-top: 20px;margin-bottom: 30px;">
                        <a href="javascript:void(0);" onclick="sendEmail()" class="layui-btn layui-btn-lg layui-btn-normal" style="width: 150px">发送邮件</a>
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block" style="margin-left: 315px;margin-top: 20px;margin-bottom: 30px;">
                        <a class="layui-btn layui-btn-primary" style="width: 150px"
                           href="/shop/index.php?act=supplier_join&op=break" >返回修改资料</a>
                    </div>
                </div>
            </form>
        <?php }?>
    </div>
</div>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui-supplier.css" media="all">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/form.js"></script>
<script>
    function sendEmail(){
        $.ajax({
            type:"POST",
            //提交的网址
            url:"/shop/index.php?act=supplier_join&op=sendEmail",
            data:{},
            datatype: "json",
            beforeSend: function () {
                layui.use('layer', function(){
                    loads = layer.load(1, {
                        shade: [0.5,'#000'] //0.1透明度的白色背景
                    });
                })
            },
            success:function(result){
                layui.use('layer', function(){
                    var layer = layui.layer;
                    layer.close(loads);
                })
                var result = JSON.parse(result);
                if(result.code == '1'){
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert('邮件发送成功，请及时认证！', {closeBtn: 0,title: '温馨提示',}, function(index){
                            parent.location.reload();
                            layer.close(layer.index);
                        });
                    })
                }else{
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert(result.msg, {closeBtn: 0,title: '温馨提示',}, function(index){
                            layer.close(layer.index);
                        });
                    })
                }
            }
        });
        return false;
    }
</script>

