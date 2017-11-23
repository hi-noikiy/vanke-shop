<?php ?>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<form method="get" action="index.php" target="_self">
  <table class="search-form">
    <input type="hidden" name="act" value="agent_order" />
    <input type="hidden" name="op" value="vr_index" />
    <tr>
      <td>&nbsp;</td>
      <th><?php echo $lang['store_order_add_time'];?></th>
      <td class="w240"><input type="text" class="text w70" name="query_start_date" id="query_start_date" value="<?php echo $_GET['query_start_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;<input id="query_end_date" class="text w70" type="text" name="query_end_date" value="<?php echo $_GET['query_end_date']; ?>" /><label class="add-on"><i class="icon-calendar"></i></label></td>
      <td class="w70 tc"><label class="submit-border">
          <input type="submit" class="submit" value="<?php echo $lang['store_order_search'];?>" />
        </label></td>
    </tr>
  </table>
</form>

<div class="alert alert-block mt10">
    <ul class="mt5">
        <li>1、此处显示为由您所推广的店铺已完成的虚拟交易订单。</li>
    </ul>
</div>

<table class="ncsc-default-table">
    <thead>
    <tr>
        <th class="w30"></th>
        <th class="w180 tl">订单号</th>
        <th class="w180 tl">下单时间</th>
        <th class="tl">店铺名称</th>
        <th class="w100 tl">订单金额</th>
        <th class="w150"></th>
    </tr>
    </thead>

    <tbody>
    <?php if (is_array($output['order_list']) and !empty($output['order_list'])) { ?>
        <?php foreach($output['order_list'] as $order_id => $order) { ?>
            <tr class="bd-line">
                <td></td>
                <td class="tl"><?php echo $order['order_sn']; ?></td>
                <td class="tl"><em class="goods-time"><?php echo date("Y-m-d H:i:s",$order['add_time']); ?></em></td>
                <td class="tl">
                    <?php echo $order['store_name']; ?>
                </td>
                <td class="tl"><?php echo $order['order_amount']; ?></td>
                <td class="nscs-table-handle"></td>
            </tr>
        <?php }}else{ ?>
    <tr>
        <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i>
                <span>没有找到相关订单</span>
            </div></td>
    </tr>
    <?php }?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
    </tfoot>
</table>


<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript">
$(function(){
    $('#query_start_date').datepicker({dateFormat: 'yy-mm-dd'});
    $('#query_end_date').datepicker({dateFormat: 'yy-mm-dd'});
    $('.checkall_s').click(function(){
        var if_check = $(this).attr('checked');
        $('.checkitem').each(function(){
            if(!this.disabled)
            {
                $(this).attr('checked', if_check);
            }
        });
        $('.checkall_s').attr('checked', if_check);
    });
    $('#skip_off').click(function(){
        url = location.href.replace(/&skip_off=\d*/g,'');
        window.location.href = url + '&skip_off=' + ($('#skip_off').attr('checked') ? '1' : '0');
    });
});
</script> 
