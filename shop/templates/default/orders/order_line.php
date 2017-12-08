<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/30
 * Time: 下午2:35
 */
?>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/line/gloab.css" media="all">
<style>.step ul{margin-left:15px;}</style>
<?php if ($output['order_info']['order_state'] != ORDER_STATE_CANCEL) { ?>
<div class="reg-box-pan display-inline" style="width: 100%">
    <div class="step" style="margin-bottom: 20px">
    <!--审核拒绝-->
    <?php if($output['order_info']['order_state'] == ORDER_STATUS_OUT){?>
        <style>.step ul li{width: 29%;float: left;}</style>
        <ul>
            <li class="col-xs-4 on">
                <span class="num"><em class="f-r5"></em><i>1</i></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">等待审核</p>
            </li>
            <li class="col-xs-4 on">
                <span class="num"><em class="f-r5"></em><i>2</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">K2审核中</p>
            </li>
            <li class="col-xs-4 on">
                <span class="num"><em class="f-r5"></em><i>7</i></span>
                <span class="line_bg lbg-l"></span>
                <p class="lbg-txt">K2审核拒绝</p>
            </li>
        </ul>
    <?php }elseif($output['order_info']['order_state'] == ORDER_WAIT_CANCEL){?>
    <!--受理拒绝-->
        <style>.step ul li{width: 20%;float: left;}</style>
        <ul>
            <li class="col-xs-4 on">
                <span class="num"><em class="f-r5"></em><i>1</i></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">等待审核</p>
            </li>
            <li class="col-xs-4 on">
                <span class="num"><em class="f-r5"></em><i>2</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">K2审核中</p>
            </li>
            <li class="col-xs-4 on">
                <span class="num"><em class="f-r5"></em><i>3</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">商家待受理</p>
            </li>
            <li class="col-xs-4 on">
                <span class="num"><em class="f-r5"></em><i>3</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">买家取消订单</p>
            </li>
            <li class="col-xs-4">
                <span class="num"><em class="f-r5"></em><i>7</i></span>
                <span class="line_bg lbg-l"></span>
                <p class="lbg-txt">等待商家受理取消订单</p>
            </li>
        </ul>
    <?php }else{?>
    <!--正常流程-->
        <style>.step ul li{width: 12%;float: left;}</style>
        <ul>
            <li class="col-xs-4 on">
                <span class="num"><em class="f-r5"></em><i>1</i></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">等待审核</p>
            </li>
            <li class="col-xs-4 <?php if ($output['order_info']['order_state'] >= ORDER_STATUS_SEND_TWO){?>on<?php }?>">
                <span class="num"><em class="f-r5"></em><i>2</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">K2审核中</p>
            </li>
            <li class="col-xs-4 <?php if ($output['order_info']['order_state'] >= ORDER_STATUS_SUCCESS){?>on<?php }?>">
                <span class="num"><em class="f-r5"></em><i>3</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">商家待受理</p>
            </li>
            <li class="col-xs-4 <?php if ($output['order_info']['order_state'] >= ORDER_DELIVER_GOODS){?>on<?php }?>">
                <span class="num"><em class="f-r5"></em><i>4</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">商家待发货</p>
            </li>
            <li class="col-xs-4 <?php if ($output['order_info']['order_state'] >= ORDER_STATUS_SEND_HET){?>on<?php }?>">
                <span class="num"><em class="f-r5"></em><i>5</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">商家已发货</p>
            </li>
            <li class="col-xs-4 <?php if ($output['order_info']['order_state'] >= ORDER_STATUS_CUS_RECEIVED){?>on<?php }?>">
                <span class="num"><em class="f-r5"></em><i>6</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">订单待检收</p>
            </li>
            <li class="col-xs-4">
                <span class="num"><em class="f-r5"></em><i>7</i></span>
                <span class="line_bg lbg-l"></span>
                <span class="line_bg lbg-r"></span>
                <p class="lbg-txt">订单待付款</p>
            </li>
            <li class="col-xs-4">
                <span class="num"><em class="f-r5"></em><i>8</i></span>
                <span class="line_bg lbg-l"></span>
                <p class="lbg-txt">订单完成</p>
            </li>
        </ul>
    <?php }?>
    </div>
</div>
<?php }?>
