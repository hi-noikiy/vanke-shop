<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/12/6
 * Time: 上午10:52
 */
?>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/line/gloab.css" media="all">
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
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
            <li class="col-xs-4">
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
    <div>
        <!-- 协议 -->
        <div id="apply_agreement" class="apply-agreement">
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                <legend style="margin-left: 430px;">认证协议</legend>
            </fieldset>
            <div class="apply-agreement-content" style="height: 400px">
                <?php echo $output['agreement'];?>
            </div>
            <div class="apple-agreement">
                <input id="input_apply_agreement" name="input_apply_agreement" type="checkbox" checked />
                <label for="input_apply_agreement">我已阅读并同意以上协议</label>
            </div>
            <div class="bottom" style="margin-bottom: 20px">
                <a href="javascript:;" class="layui-btn layui-btn-normal" style="float: right;margin-right: 40px;">下一步</a>
            </div>
        </div>
    </div>
</div>
<script>
    $('.layui-btn-normal').on('click', function() {
        if($('#input_apply_agreement').prop('checked')) {
            window.location.href = "index.php?act=supplier_join&step=company";
        } else {
            alert('请阅读并同意协议');
        }
    });
</script>

