<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/30
 * Time: 下午2:35
 */
?>
<div class="ncm-order-condition" style="width: 90%">
    <dl>
        <dt>订单编号：</dt>
        <dd><?php echo $output['order_info']['order_sn'];?></dd>
        <dt>状态：</dt>
        <dd>
            <?php if($output['order_info']['order_state'] == ORDER_STATUS_SEND_ONE){?>
                订单待审核
            <?php }elseif($output['order_info']['order_state'] == ORDER_STATUS_SEND_TWO){?>
                订单审核中
            <?php }elseif($output['order_info']['order_state'] == ORDER_STATUS_SUCCESS){?>
                商家待受理
            <?php }elseif($output['order_info']['order_state'] == ORDER_DELIVER_GOODS){?>
                订单待发货
            <?php }elseif($output['order_info']['order_state'] == ORDER_STATUS_SEND_HET){?>
                订单已发货
            <?php }elseif($output['order_info']['order_state'] == ORDER_STATUS_RECEIVED){?>
                已收货
            <?php }elseif($output['order_info']['order_state'] == ORDER_STATUS_OUT){?>
                K2审核拒绝
            <?php }elseif($output['order_info']['order_state'] == ORDER_WAIT_CANCEL){?>
                订单取消待受理
            <?php }else{?>
                订单已取消
            <?php }?>
        </dd>
    </dl>
    <?php include template('orders/order_line');?>
    <div class="layui-form-item" style="margin-bottom:20px">
        <label class="layui-form-label" style="width: 110px;">订单备注：</label>
        <div class="layui-input-block layui-word-aux" style="height:38px;line-height:38px;">
            <?php echo $output['order_info']['extend_order_common']['order_message'];?>
        </div>
    </div>
    <ul style="margin-left: 140px;">
        <li>1. 如果收到货后出现问题，您可以联系商家协商解决。</li>
        <li>2. 如果商家没有履行应尽的承诺，您可以申请 <a href="#order-step" class="red">"投诉维权"</a>。</li>
        <?php if ($output['order_info']['if_evaluation']) { ?>
            <li>3. 交易已完成，你可以对购买的商品及商家的服务进行<a href="#order-step" class="ncm-btn-mini ncm-btn-acidblue">评价</a>及晒单。</li>
        <?php } ?>
    </ul>
</div>
