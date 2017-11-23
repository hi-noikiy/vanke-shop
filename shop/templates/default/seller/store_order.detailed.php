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
      <div class="orderprint">
                      <div style="float:left"><img src="<?php echo UPLOAD_SITE_URL.DS.ATTACH_COMMON.DS; ?>/1logo.png"></div>
        <div class="top" >
            <div class="logo-title" style="text-align:center;float:left;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                万科<?php echo $output['buyer_name']['ktext'];?>采购订单</div>
        </div>
          <tr>
                <td><strong>一,产品清单</strong></td>
           </tr>
        <table class="buyer-info">
          <tr>
            <td>订单号：<?php echo $output['order_info']['order_sn'];?></td>
            <td><?php echo $lang['member_printorder_orderadddate'].$lang['nc_colon'];?><?php echo @date('Y-m-d',$output['order_info']['add_time']);?></td>
            <td>城市中心：<?php echo $output['buyer_name']['butxt'];?></td>
          </tr>
            <tr>
            <td>下单人：<?php echo $output['buyer_name']['sname'];?></td>
            <td>所在部门：<?php echo $output['buyer_name']['ktext'];?></td>   
             <td>联系号码：<?php echo $output['buyer_name']['Telnr'];?></td>    
            </tr>
            <tr>
                
                <form method="get" action="index.php" target="_self" id="formSearch">
                      <table class="search-form">
                        <input type="hidden" name="act" value="store_transport_nei" />
                        <input type="hidden" name="op" value="goods" />
                        <td style="display: none"><input name="order_sn" value ="<?php echo $output['order_info']['order_sn'];?>" ><?php echo $output['order_info']['order_sn'];?></td>   
                      </table>  
                       <td ><a id="ncexport" href="javascript:void(0);">导出商品Excel</a></td>    
                </form>
            </tr>
        </table>         
        <table class="order-info">
          <thead>
            <tr>
              <th class="w40"><?php echo $lang['member_printorder_serialnumber'];?></th>
              <th class="tl"><?php echo $lang['member_printorder_goodsname'];?></th>
              <th class="w70 tl"><?php echo $lang['member_printorder_goodsprice'];?>(<?php echo $lang['currency_zh'];?>)</th>
              <th class="w50"><?php echo $lang['member_printorder_goodsnum'];?></th>
              <th class="w70 tl"><?php echo $lang['member_printorder_subtotal'];?>(<?php echo $lang['currency_zh'];?>)</th>
            </tr>
          </thead>
         <?php foreach ($output['goods_list'] as $item_k =>$item_v){?>        
          <tbody>
            <?php foreach ($item_v as $k=>$v){?>
            <tr>
              <td><?php echo $k;?></td>
              <td class="tl"><?php echo $v['goods_name'];?></td>
              <td class="tl"><?php echo $lang['currency'].$v['goods_price'];?></td>
              <td><?php echo $v['goods_num'];?></td>
              <td class="tl"><?php echo $lang['currency'].$v['goods_all_price'];?></td>
            </tr>
            <?php }?>
          </tbody>
             <?php }?> 
            <tr>
              <th></th>
              <th colspan="2" class="tl"><?php echo $lang['member_printorder_amountto'];?></th>
              <th><?php echo $output['goods_all_num'];?></th>
              <th class="tl"><?php echo $lang['currency'].$output['goods_total_price'];?></th>
            </tr>
          <tfoot>
            <tr>
              <th colspan="10"><span><?php echo $lang['member_printorder_totle'].$lang['nc_colon'];?><?php echo $lang['currency'].$output['goods_total_price'];?></span><span><?php echo $lang['member_printorder_freight'].$lang['nc_colon'];?><?php echo $lang['currency'].$output['order_info']['shipping_fee'];?></span><span><?php echo $lang['member_printorder_privilege'].$lang['nc_colon'];?><?php echo $lang['currency'].$output['promotion_amount'];?></span><span><?php echo $lang['member_printorder_orderamount'].$lang['nc_colon'];?><?php echo $lang['currency'].$output['order_info']['order_amount'];?></span><span><?php echo $lang['member_printorder_shop'].$lang['nc_colon'];?><?php echo $output['store_info']['store_name'];?></span>
                <?php if (!empty($output['store_info']['store_qq'])){?>
                <span>QQ：<?php echo $output['store_info']['store_qq'];?></span>
                <?php }elseif (!empty($output['store_info']['store_ww'])){?>
                <span><?php echo $lang['member_printorder_shopww'].$lang['nc_colon'];?><?php echo $output['store_info']['store_ww'];?></span>
                <?php }?></th>
            </tr>
          </tfoot>
        </table>
            <br/>    <br/>
            <tr>
                <td><strong>二,交货信息</strong></td>
            </tr>
        <table class="buyer-info">
          <tr>
            <td class="w200"><?php echo $lang['member_printorder_truename'].$lang['nc_colon']; ?><?php echo $output['order_info']['extend_order_common']['reciver_name'];?></td><br/>
            <tr>
            <td><?php echo '电话'.$lang['nc_colon']; ?><?php echo @$output['order_info']['extend_order_common']['reciver_info']['phone'];?></td>
             </tr>
            <td></td>
          </tr>
          <tr>
            <td colspan="3"><?php echo $lang['member_printorder_address'].$lang['nc_colon']; ?><?php echo @$output['order_info']['extend_order_common']['reciver_info']['address'];?></td>
          </tr>
        </table>
          
           <tr>
                <td><strong>三,开票信息</strong></td>
            </tr>             <br/>
            <br/>
         <!--发票信息-->
     <div class="ncsc-order-details" style="width:100%">
      <div class="title"><?php echo $lang['store_show_order_info'];?></div>
      <div class="content">
        <dl>
          <dt>发票信息：</dt>
          <dd>
            <?php foreach ((array)$output['order_info']['extend_order_common']['invoice_info'] as $key => $value){?>
            <span><?php echo $key;?> :<strong><?php echo $value;?></strong></span>
            <?php } ?>
          </dd>
        </dl>
        <dl>
          <dt>买家留言：</dt>
          <dd><?php echo $output['order_info']['extend_order_common']['order_message']; ?></dd>
        </dl>
      </div>
    </div>
         <br/><br/>
     <!--发票信息-->      
        <?php if (empty($output['store_info']['store_stamp'])){?>
        <div >
        	<?php echo $output['store_info']['store_printdesc'];?>
        </div>
        <?php }?>
        <div class="tc page"><?php echo $lang['member_printorder_pagetext_1']; ?><?php echo $item_k;?><?php echo $lang['member_printorder_pagetext_2']; ?>/<?php echo $lang['member_printorder_pagetext_3']; ?><?php echo count($output['goods_list']);?><?php echo $lang['member_printorder_pagetext_2']; ?></div>
      </div>
    </div>
    <?php }?>
  </div>
</div>
</body>
<script>
$(function(){
    $('#ncexport').click(function(){
    $('#formSearch').submit();
    });
});

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