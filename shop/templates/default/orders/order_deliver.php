<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/30
 * Time: 下午2:37
 */
?>
<style>

    #wrapper {
    }
    #main {
        position: relative;
        width: 600px;
        margin: 0 auto;
        padding-top: 8%;
        animation: main .8s 1;
        animation-fill-mode: forwards;
        -webkit-animation: main .8s 1;
        -webkit-animation-fill-mode: forwards;
        -moz-animation: main .8s 1;
        -moz-animation-fill-mode: forwards;
        -o-animation: main .8s 1;
        -o-animation-fill-mode: forwards;
        -ms-animation: main .8s 1;
        -ms-animation-fill-mode: forwards;
    }
    #main #header h1 {
        position: relative;
        display: block;
        font: 72px 'TeXGyreScholaBold', Arial, sans-serif;
        color: #0061a5;
        text-shadow: 2px 2px #f7f7f7;
        text-align: center;
    }
    #main #header h1 span.sub {
        position: relative;
        font-size: 21px;
        top: -20px;
        padding: 0 10px;
        font-style: italic;
    }
    #main #header h1 span.icon {
        position: relative;
        display: inline-block;
        top: -6px;
        margin: 0 10px 5px 0;
        background: #0061a5;
        width: 50px;
        height: 50px;
        -moz-box-shadow: 1px 2px white;
        -webkit-box-shadow: 1px 2px white;
        box-shadow: 1px 2px white;
        -webkit-border-radius: 50px;
        -moz-border-radius: 50px;
        border-radius: 50px;
        color: #dfdfdf;
        font-size: 46px;
        line-height: 48px;
        font-weight: bold;
        text-align: center;
        text-shadow: 0 0;
    }

</style>
<?php if(!empty($output['daddress_info'])){?>
<div style="padding: 10px 50px;">
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">发  货  人：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['daddress_info']['seller_name']; ?>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">公司名称：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['daddress_info']['company'];?>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">联系方式：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['daddress_info']['telphone'];?>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;">发货地址：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['daddress_info']['area_info'];?>
        </div>
    </div>
    <div class="layui-form-item" style="margin-bottom:0px">
        <label class="layui-form-label" style="width: 170px;"></label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['daddress_info']['address'];?>
        </div>
    </div>
</div>
<?php }else{?>
<div>
    <div id="wrapper">
        <div id="main">
            <header id="header">
                <h1><span class="sub">卖家尚未发货</span></h1>
            </header>
        </div>
    </div>
</div>
<?php }?>
