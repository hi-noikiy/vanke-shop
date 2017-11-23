<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>代理首页</h3>
      <ul class="tab-base">
        <li><a href="index.php?act=agent_joinin&op=setting"  class="current"><span><?php echo '设置';?></span></a></li>
        <li><a href="index.php?act=agent_joinin&op=edit_info"><span><?php echo '前台加盟页';?></span></a></li>
        <li><a href="index.php?act=agent_joinin&op=help_list"><span><?php echo '流程说明';?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" name="settingForm" id="settingForm">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <tbody>
      <!-- 开启代理 -->
      <tr class="noborder">
        <td colspan="2" class="required"><label>开启代理:</label></td>
      </tr>
      <tr class="noborder">
        <td class="vatop rowform onoff"><label for="agent_isuse_1" class="cb-enable <?php if($output['list_setting']['agent_isuse'] == '1'){ ?>selected<?php } ?>" title="<?php echo $lang['open'];?>"><span><?php echo $lang['open'];?></span></label>
          <label for="agent_isuse_0" class="cb-disable <?php if($output['list_setting']['agent_isuse'] == '0'){ ?>selected<?php } ?>" title="<?php echo $lang['close'];?>"><span><?php echo $lang['close'];?></span></label>
          <input type="radio" id="agent_isuse_1" name="agent_isuse" value="1" <?php echo $output['list_setting']['agent_isuse'] ==1?'checked=checked':''; ?>>
          <input type="radio" id="agent_isuse_0" name="agent_isuse" value="0" <?php echo $output['list_setting']['agent_isuse'] ==0?'checked=checked':''; ?>>
        <td class="vatop tips">开启代理，店铺分润功能</td>
      </tr>

      <tr>
        <td class="" colspan="2">
          <table class="table tb-type2 nomargin">
            <thead>
            <tr class="thead">
              <th colspan="2">店铺推广分润</th>
            </tr>
            </thead>
            <tbody>
            <tr class="hover">
              <td class="w200">店铺推广分润比例</td>
              <td><input id="points_store_invite" name="points_store_invite" value="<?php echo $output['list_setting']['points_store_invite'];?>" class="txt" type="text" style="width:60px;">
                例:设置为10，表明店铺可以享受推广会员的订单利润的10%的佣金返还</td>
            </tr>

            </tbody>
            <tfoot>
            <tr class="tfoot">
              <td colspan="2" ><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'];?></span></a></td>
            </tr>
            </tfoot>
          </table>
  </form>
</div>
<script>

  $(function(){$("#submitBtn").click(function(){
    if($("#settingForm").valid()){
      $("#settingForm").submit();
    }
  });
  });
  //
  $(document).ready(function(){
    $("#settingForm").validate({
      errorPlacement: function(error, element){
        error.appendTo(element.parent().parent().prev().find('td:first'));
      },
      rules : {
      },
      messages : {
      }
    });
  });
</script>
