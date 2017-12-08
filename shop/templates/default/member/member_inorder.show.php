<?php ?>
<style>
    .goods-total {
        display: inline-block;
        display: block;
        padding: 20px;
    }
    .goods-total ul {
        float: right;
        list-style: none;
    }
    .goods-total li {
        display: inline-block;
        display: block;
        line-height: 28px;
    }
    .goods-total .label {
        float: left;
        width: 500px;
        text-align: right;
    }
    .goods-total .txt {
        float: left;
        width: 130px;
        text-align: right;
        font-family: verdana;
        border: none;
    }
    .ftx-01, .ftx01 {
        color: #e4393c;
    }
    .goods-total .count {
        font-size: 18px;
        font-weight: 700;
    }
</style>
<div class="ncm-oredr-show">

    <?php include template('orders/order_top');?>
  <div class="ncm-order-contnet">
    <table class="ncm-default-table order">
      <thead>
        <tr>
          <th class="w20"></th>
          <th class="w60">商品编号</th>
          <th colspan="2"><?php echo $lang['member_order_goods_name'];?></th>
          <th class="w120 tl"><?php echo $lang['member_order_price'];?>（元）</th>
          <th class="w60"><?php echo $lang['member_order_amount'];?></th>
          <th class="w20"></th>
          <th class="w100">交易操作</th>
        </tr>
      </thead>
      <tbody>
      <?php if(!empty($output['order_info']['goods_list']) && is_array($output['order_info']['goods_list'])){?>
      <?php foreach($output['order_info']['goods_list'] as $k => $goods) {?>
          <tr>
              <td></td>
              <td><?php echo $goods['goods_id']; ?></td>
              <td class="w70">
                  <div class="ncm-goods-thumb">
                      <a target="_blank" href="<?php echo $goods['goods_url']; ?>">
                          <img src="<?php echo $goods['image_60_url']; ?>" onMouseOver="toolTip('<img src=<?php echo $goods['image_240_url']; ?>>')" onMouseOut="toolTip()" />
                      </a>
                  </div>
              </td>
              <td class="tl">
                  <dl class="goods-name">
                      <dt>
                          <a target="_blank" href="<?php echo $goods['goods_url']; ?>" title="<?php echo $goods['goods_name']; ?>">
                              <?php echo $goods['goods_name']; ?>
                          </a>
                      </dt>
                  </dl>
              </td>
              <td class="tl refund"><?php echo $goods['goods_price']; ?></td>
              <td><?php echo $goods['goods_num']; ?></td>
              <td></td>
              <?php if($k < 1){?>
                  <td class="bdl bdr" rowspan="2">
                      交易操作
                  </td>
              <?php }?>
          </tr>
      <?php }}?>
      </tbody>
    </table>
  </div>
    <div class="goods-total" style="background: #F9F9F9;height: 90px;margin-bottom: 30px;">
        <ul>
            <li>
                <span class="label">商品总额：</span>
                <span class="txt">¥<?php echo $output['order_info']['goods_amount'];?></span>
            </li>
            <li>
                <span class="label">运　　费：</span>
                <span class="txt">¥<?php echo $output['order_info']['shipping_fee'];?></span>
            </li>
            <li class="ftx-01">
                <span class="label">订单总额：</span>
                <span class="txt count">¥<?php echo $output['order_info']['order_amount'];?></span>
            </li>
        </ul>
    </div>
</div>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/sns.js" ></script> 
<script type="text/javascript">
</script> 
