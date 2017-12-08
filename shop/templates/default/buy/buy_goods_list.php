<?php ?>
<style>
.ncc-table-style tbody tr.item_disabled td {
	background: none repeat scroll 0 0 #F9F9F9;
	height: 30px;
	padding: 10px 0;
	text-align: center;
}
.money-data-tr{border-bottom:none;}
.money-data-td{border-bottom:none;}
</style>
<div class="ncc-receipt-info">
  <div class="ncc-receipt-info-title">
    <h3>商品清单</h3>
    <?php if(!empty($output['ifcart'])){?>
    <a href="index.php?act=cart"><?php echo $lang['cart_step1_back_to_cart'];?></a>
    <?php }?>
  </div>
  <table class="ncc-table-style">
    <thead>
      <tr>
        <th class="w20"></th>
        <th colspan="3"><?php echo $lang['cart_index_store_goods'];?></th>
        <th style="width: 120px;"><?php echo $lang['cart_index_price'].'('.$lang['currency_zh'].')';?></th>
        <th style="width: 120px;"><?php echo $lang['cart_index_amount'];?></th>
        <th style="width: 120px;"><?php echo $lang['cart_index_sum'].'('.$lang['currency_zh'].')';?></th>
      </tr>
    </thead>
    <tbody style="margin-bottom: 30px;">
    <?php if(!empty($output['list']['goodList']) && is_array($output['list']['goodList'])){?>
        <?php foreach ($output['list']['goodList'] as $v){?>
        <tr>
            <th colspan="20">
                <strong>店铺：
                    <a href="<?php echo urlShop('show_store','index',array('store_id'=>$v['store_id']));?>">
                        <?php echo $v['store_name']; ?>
                    </a>
                </strong>
            </th>
        </tr>
        <?php if(!empty($v['goodList']) && is_array($v['goodList'])){?>
        <?php foreach ($v['goodList'] as $vl){?>
            <input type="hidden" name="cart[<?php echo $v['store_id']; ?>][cart_id][<?php echo $vl['cart_id']; ?>]" value="<?php echo $vl['cart_id']; ?>|<?php echo $vl['goods_num']; ?>"/>
            <tr>
                <td></td>
                <td>
                    <div style="height: 80px;width:80px">
                        <a href="<?php echo urlShop('goods','index',array('goods_id'=>$vl['goods_id']));?>" target="_blank" class="ncc-goods-thumb" title="<?php echo $vl['goods_name']; ?>">
                            <img src="<?php echo thumb($vl,100);?>" alt="<?php echo $vl['goods_name']; ?>" style="max-width:80px;max-height:80px;"/>
                        </a>
                    </div>
                </td>
                <td style="width:250px;">
                    <a href="<?php echo urlShop('goods','index',array('goods_id'=>$vl['goods_id']));?>" target="_blank" title="<?php echo $vl['goods_name']; ?>">
                        <em><?php echo $vl['goods_name']; ?></em>
                    </a>
                </td>
                <td style="width:239px;"><em style="margin-left: 15px;color: #AAA;">
                            <?php if(!empty($vl['spce'])){echo $vl['spce'];}else{echo "标准";}?>
                        </em></td>
                <td><em><?php echo $vl['goods_price']; ?></em></td>
                <td><em><?php echo $vl['goods_num']; ?></em></td>
                <td><em><?php echo number_format($vl['goods_price']*$vl['goods_num'],4); ?></em></td>
            </tr>
        <?php }} ?>
            <tr>
                <td colspan="5">
                    <span style="float: left;margin-left: 20px;">买家留言：</span>
                    <span style="float: left;height: 60px;width: 500px;">
                    <textarea placeholder="选填：对本次交易的说明（建议填写已经和商家达成一致的说明）"
                              name="cart[<?php echo $v['store_id']; ?>][mark]" class="layui-textarea" style="height: 60px;min-height:60px;"></textarea>
                </span>
                </td>
                <td colspan="2">
                    <table class="money-data">
                        <tr class="money-data-tr">
                            <td style="border-bottom:none;padding:0px;width:140px;" align="right">
                                <p style="font: normal 12px/28px Verdana, Arial;float: right">运费：</p>
                            </td>
                            <td style="border-bottom:none;padding:0px;width:100px;" align="right">
                                <p style="font: normal 12px/28px Verdana, Arial;float: right;margin-right: 20px;">
                                    <?php echo $v['freight'];?>&nbsp;元
                                </p>
                            </td>
                        </tr>
                        <tr class="money-data-tr">
                            <td style="border-bottom:none;padding:0px;" align="right">
                                <p style="font: normal 12px/28px Verdana, Arial;float: right">商品金额：</p>
                            </td>
                            <td style="border-bottom:none;padding:0px;" align="right">
                                <p style="font: normal 12px/28px Verdana, Arial;float: right;margin-right: 20px;">
                                    <?php echo $v['goodMoney'];?>&nbsp;元
                                </p>
                            </td>
                        </tr>
                        <tr class="money-data-tr">
                            <td style="border-bottom:none;padding:0px;" align="right">
                                <p style="font: normal 12px/28px Verdana, Arial;float: right">本店合计：</p>
                            </td>
                            <td style="border-bottom:none;padding:0px;" align="right">
                                <p style="font: normal 12px/28px Verdana, Arial;float: right;margin-right: 20px;">
                                    <?php echo $v['allMoney'];?>&nbsp;元
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
    <?php }}?>
    </tbody>
  </table>
</div>

