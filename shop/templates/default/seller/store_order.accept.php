<?php ?>
<div class="eject_con">
  <div id="warning"></div>
  <form method="post" id="order_cancel_form" onsubmit="ajaxpost('order_cancel_form', '', '', 'onerror');return false;" action="index.php?act=store_order&op=change_state&state_type=accept&order_id=<?php echo $output['order_id']; ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <dl>
      <dt><?php echo $lang['store_order_order_sn'].$lang['nc_colon'];?></dt>
      <dd><span class="num"><?php echo trim($_GET['order_sn']); ?></span></dd>
    </dl>
    <dl>
      <dt>订单金额：</dt>
      <dd><?php echo $output['order_info']['order_amount'];?></dd>
    </dl>
    <dl>
      <dt>下单时间：</dt>
      <dd><?php echo date("Y-m-d H:i:s",$output['order_info']['add_time']);?></dd>
    </dl>
    <dl class="bottom">
      <dt>&nbsp;</dt>
      <dd>
        <input type="submit" class="submit" id="confirm_button" value="<?php echo $lang['nc_ok'];?>" />
      </dd>
    </dl>
  </form>
</div>
<script type="text/javascript">
$(function(){
        $('#cancel_button').click(function(){
            DialogManager.close('seller_order_cancel_order');
         });
       $("input[name='state_info']").click(function(){
        if ($(this).attr('flag') == 'other_reason')
        {
            $('#other_reason').show();
        }
        else
        {
            $('#other_reason').hide();
        }
    });
});
</script> 
