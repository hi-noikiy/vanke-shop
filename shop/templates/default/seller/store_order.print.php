<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php ?>

<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/seller_center.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
body { background: #FFF none;
}
</style>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.printarea.js" charset="utf-8"></script>
<title><?php echo $lang['member_printorder_print'];?>--<?php echo $output['store_info']['store_name'];?><?php echo $lang['member_printorder_title'];?></title>
</head>

<body>
<?php if (!empty($output['order_info'])){?>
<div class="print-layout">
  <div class="print-btn" id="printbtn" title="<?php echo $lang['member_printorder_print_tip'];?>"><i></i><a href="javascript:void(0);"><?php echo $lang['member_printorder_print'];?></a></div>
  <div class="a5-size"></div>
  <dl class="a5-tip">
    <dt>
      <h1>A5</h1>
      <em>Size: 210mm x 148mm</em></dt>
    <dd><?php echo $lang['member_printorder_print_tip_A5'];?></dd>
  </dl>
  <div class="a4-size"></div>
  <dl class="a4-tip">
    <dt>
      <h1>A4</h1>
      <em>Size: 210mm x 297mm</em></dt>
    <dd><?php echo $lang['member_printorder_print_tip_A4'];?></dd>
  </dl>
  <div class="print-page">
    <div id="printarea">
      <?php foreach ($output['goods_list'] as $item_k =>$item_v){?>
      <div class="orderprint">
        <div class="top">
          <?php if (empty($output['store_info']['store_label'])){?>
          <div class="full-title"><?php echo $output['store_info']['store_name'];?> <?php echo $lang['member_printorder_title'];?></div>
          <?php }else {?>
          <div class="logo" ><img src="<?php echo $output['store_info']['store_label']; ?>"/></div>
          <div class="logo-title"><?php echo $output['store_info']['store_name'];?><?php echo $lang['member_printorder_title'];?></div>
          <?php }?>
        </div>
        <table class="buyer-info">
          <tr>
            <td class="w200"><?php echo $lang['member_printorder_truename'].$lang['nc_colon']; ?><?php echo $output['order_info']['extend_order_common']['reciver_name'];?></td>
            <td><?php echo '电话'.$lang['nc_colon']; ?><?php echo @$output['order_info']['extend_order_common']['reciver_info']['phone'];?></td>
            <td></td>
          </tr>
          <tr>
            <td colspan="3"><?php echo $lang['member_printorder_address'].$lang['nc_colon']; ?><?php echo @$output['order_info']['extend_order_common']['reciver_info']['address'];?></td>
          </tr>
          <tr>
            <td><?php echo $lang['member_printorder_orderno'].$lang['nc_colon'];?><?php echo $output['order_info']['order_sn'];?></td>
            <td><?php echo $lang['member_printorder_orderadddate'].$lang['nc_colon'];?><?php echo @date('Y-m-d',$output['order_info']['add_time']);?></td>
            <td><?php if ($output['order_info']['shippin_code']){?>
              <span><?php echo $lang['member_printorder_shippingcode'].$lang['nc_colon']; ?><?php echo $output['order_info']['shipping_code'];?></span>
              <?php }?></td>
          </tr>
        </table>
        <table class="order-info">
          <thead>
          <tr>
              <th class="w40"><?php echo $lang['member_printorder_shop'].$lang['nc_colon'];?></th>
              <th class="tl" colspan="4"><?php echo $output['store_info']['store_name'];?></th>
          </tr>
            <tr>
              <th style="border-bottom:none" class="w40"><?php echo $lang['member_printorder_serialnumber'];?></th>
              <th style="border-bottom:none" class="tl"><?php echo $lang['member_printorder_goodsname'];?></th>
              <th style="border-bottom:none" class="w70 tl"><?php echo $lang['member_printorder_goodsprice'];?>(<?php echo $lang['currency_zh'];?>)</th>
              <th style="border-bottom:none" class="w50"><?php echo $lang['member_printorder_goodsnum'];?></th>
              <th style="border-bottom:none;width: 100px;"><?php echo $lang['member_printorder_subtotal'];?>(<?php echo $lang['currency_zh'];?>)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($item_v as $k=>$v){?>
            <tr>
              <td style="border:none"><?php echo $k;?></td>
              <td style="border:none" class="tl"><?php echo $v['goods_name'];?></td>
              <td style="border:none" class="tl"><?php echo $lang['currency'].$v['goods_price'];?></td>
              <td style="border:none"><?php echo $v['goods_num'];?></td>
              <td style="text-align: right !important;border:none">
                  <?php echo $lang['currency'].'&nbsp;'.$v['goods_all_price'];?>
                  &nbsp;&nbsp;&nbsp;&nbsp;
              </td>
            </tr>
            <?php }?>
            <tr>
                <th style="border:none"></th>
                <th colspan="2" style="border:none">商品总价：</th>
                <th style="border:none"><?php echo $output['goods_all_num'];?></th>
                <th style="text-align: right !important;border:none">
                    <?php echo $lang['currency'].'&nbsp;'.$output['goods_total_price'];?>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                </th>
            </tr>
            <tr>
                <th style="border:none"></th>
                <th colspan="2" style="border:none">运&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;费：</th>
                <th colspan="2" style="text-align: right !important;border:none">
                    <?php echo $lang['currency'].'&nbsp;'.$output['shipping_fee'];?>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                </th>
            </tr>
            <tr>
                <th></th>
                <th colspan="2" style="font-size:14px;" align="right">
                    <?php echo $lang['member_printorder_amountto'];?>
                </th>
                <th colspan="2" style="font-size:14px;text-align: right !important;">
                    <?php echo $lang['currency'].'&nbsp;'.$output['order_money'];?>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                </th>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="10">
                  <span>
                      买家留言：
                      <?php echo $output['order_info']['extend_order_common']['order_message']; ?>
                  </span>
              </th>
            </tr>
          </tfoot>
        </table>

        <?php if (empty($output['store_info']['store_stamp'])){?>
            <div class="explain">
                <?php echo $output['store_info']['store_printdesc'];?>
            </div>
        <?php }else {?>
            <!--订单备注信息-->
            <div class="explain">
                <?php echo $output['store_info']['store_printdesc'];?>
            </div>
            <!--公司印章-->
            <div class="seal" style="top: 50px;">
                <img style="width: 120px;height: 120px;" src="<?php echo $output['store_info']['store_stamp'];?>"
                     onload="javascript:DrawImage(this,120,120);"/>
            </div>
        <?php }?>

          <div class="tc page"><?php echo $lang['member_printorder_pagetext_1']; ?><?php echo $item_k;?><?php echo $lang['member_printorder_pagetext_2']; ?>/<?php echo $lang['member_printorder_pagetext_3']; ?><?php echo count($output['goods_list']);?><?php echo $lang['member_printorder_pagetext_2']; ?></div>
     
              <!--发票信息-->
          <div class="ncsc-order-details" style="width:100%">
              <div class="title">发票信息：</div>
              <div class="content">
                  <dl>
                      <dt style="width: 10%"></dt>
                      <dd>
                          <table>
                              <?php if(!empty($output['order_info']['extend_order_common']['invoice_info']) && is_array($output['order_info']['extend_order_common']['invoice_info'])){?>
                                  <?php foreach ($output['order_info']['extend_order_common']['invoice_info'] as $key=>$val){?>
                                      <tr>
                                          <td align="right"><?php echo $key;?>：</td>
                                          <td align="left"><strong><?php echo $val;?></strong></td>
                                      </tr>
                                  <?php }}?>
                          </table>
                      </dd>
                  </dl>
              </div>
          </div>
         <br/><br/>
     <!--发票信息-->      
          
          
        <?php if (empty($output['store_info']['store_stamp'])){?>
        <div >
        	<?php echo $output['store_info']['store_printdesc'];?>
        </div>
        <?php }else {?>
<!--        <div class="explain">
        	<?php echo $output['store_info']['store_printdesc'];?>
        </div>-->
  
        <?php }?>
        
        <div class="tc page"><?php echo $lang['member_printorder_pagetext_1']; ?><?php echo $item_k;?><?php echo $lang['member_printorder_pagetext_2']; ?>/<?php echo $lang['member_printorder_pagetext_3']; ?><?php echo count($output['goods_list']);?><?php echo $lang['member_printorder_pagetext_2']; ?></div>   
      </div>
      <?php }?>
    </div>
    <?php }?>
  </div>
</div>
</body>
<script>
$(function(){
	$("#printbtn").click(function(){
	$("#printarea").printArea();
	});
});

//打印提示
$('#printbtn').poshytip({
	className: 'tip-yellowsimple',
	showTimeout: 1,
	alignTo: 'target',
	alignX: 'center',
	alignY: 'bottom',
	offsetY: 5,
	allowTipHover: false
});
</script>
</html>