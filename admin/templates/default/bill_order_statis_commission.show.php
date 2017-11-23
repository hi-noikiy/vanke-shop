<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['bill_manage'];?>佣金管理</h3>
		<ul class="tab-base">
		<li><a href="index.php?act=commission"><span>佣金管理</span></a></li>
		<li><a class="current" href="JavaScript:void(0);"><span><?php echo !empty($_GET['os_month']) ? $_GET['os_month'].'期' : null;?> 佣金列表</span></a></li>
		</ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" action="index.php" target="" name="formSearch" id="formSearch">
    <input type="hidden" name="act" value="commission" />
    <input type="hidden" name="op" value="show_statis" />
    <input type="hidden" name="os_month" value="<?php echo $_GET['os_month'];?>">
    <table class="tb-type1 noborder search">
      <tbody>
        <tr>
          <th>店铺ID/名称</th>
          <td><input class="txt-short" type="text" value="<?php echo $_GET['query_store'];?>" name="query_store" id="query_store"/></td>
          <th>账单状态</th>
          <td>
          <select name="bill_state">
          <option><?php echo L('nc_please_choose');?></option>
          <option <?php if ($_GET['bill_state'] == BILL_STATE_CREATE) {?>selected<?php } ?> value="<?php echo BILL_STATE_CREATE;?>">已出账</option>
          <option <?php if ($_GET['bill_state'] == BILL_STATE_STORE_COFIRM) {?>selected<?php } ?> value="<?php echo BILL_STATE_STORE_COFIRM;?>">商家已确认</option>
          <option <?php if ($_GET['bill_state'] == BILL_STATE_SYSTEM_CHECK) {?>selected<?php } ?> value="<?php echo BILL_STATE_SYSTEM_CHECK?>">平台已审核</option>
          <option <?php if ($_GET['bill_state'] == BILL_STATE_SUCCESS) {?>selected<?php } ?> value="<?php echo BILL_STATE_SUCCESS?>">结算完成</option>
          </select>
          </td>
          <td><a href="javascript:void(0);" id="ncsubmit" class="btn-search " title="<?php echo $lang['nc_query'];?>">&nbsp;</a>
          </td>
        </tr>
      </tbody>
    </table>
  </form>
  <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th colspan="12"><div class="title">
            <h5><?php echo $lang['nc_prompts'];?></h5>
            <span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td><ul>
            <li>此处列出了平台的佣金发放对象，包括 <strong>代理</strong> 及 <strong>店铺</strong></li>

            <li>佣金计算公式： <span style="color: blue;">（真实佣金 + 虚拟佣金 - 退还佣金） × 分润比例 = 应结佣金</span></li>
            <li>佣金处理流程为：系统出账 > 商家申请结算 > 系统处理结算 3个环节</li>
            <li>最后一期的佣金处于冻结状态，不能进行结算</li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <div style="text-align:right;"><a class="btns" href="index.php?<?php echo $_SERVER['QUERY_STRING'];?>&op=export_bill"><span><?php echo $lang['nc_export'];?>CSV</span></a></div>
  <table class="table tb-type2 nobdb">
    <thead>
      <tr class="thead">
        <th>账单编号</th>
        <th class="align-center">开始日期</th>
        <th class="align-center">结束日期</th>
        <th class="align-center">订单金额</th>
        <th class="align-center">运费</th>
        <th class="align-center">真实佣金</th>
        <th class="align-center">退款金额</th>
        <th class="align-center">退还佣金</th>
        <th class="align-center">虚拟佣金</th>
        <th class="align-center">应结佣金</th>
        <th class="align-center">出账日期</th>
        <th class="align-center">账单状态</th>
        <th class="align-center">类型</th>
        <th class="align-center">账号</th>
        <th class="align-center"><?php echo $lang['nc_handle'];?></th>
      </tr>
    </thead>
    <tbody>
      <?php if(count($output['bill_list'])>0){?>
      <?php foreach($output['bill_list'] as $bill_info){?>
      <tr class="hover">
        <td><?php echo $bill_info['oc_no'];?></td>
        <td class="nowrap align-center"><?php echo date('Y-m-d',$bill_info['oc_start_date']);?></td>
        <td class="nowrap align-center"><?php echo date('Y-m-d',$bill_info['oc_end_date']);?></td>
        <td class="align-center"><?php echo $bill_info['oc_totals'];?></td>
        <td class="align-center"><?php echo $bill_info['oc_shipping_totals'];?></td>
        <td class="align-center"><?php echo $bill_info['oc_commis_totals'];?></td>
        <td class="align-center"><?php echo $bill_info['oc_return_totals'];?></td>
        <td class="align-center"><?php echo $bill_info['oc_commis_return_totals'];?></td>
        <td class="align-center"><?php echo $bill_info['oc_vr_commis_totals'];?></td>
        <td class="align-center" style="color: red;"><strong><?php echo $bill_info['oc_result_totals'];?></strong></td>
        <td class="align-center"><?php echo date('Y-m-d',$bill_info['oc_create_date']);?></td>
        <td class="align-center"><?php echo billState($bill_info['oc_state']);?></td>
        <td class="align-center"><?php if($bill_info['oc_type'] == '1'){
            echo '<span style="color:red;">代 </span>';
          }else{
            echo '<span style="color:blue;">店 </span>';
          } ?></td>
        <td class="align-center">
<?php if($bill_info['oc_type'] == '1'){
  echo $bill_info['oc_agent_name'].'<br/>id: '.$bill_info['oc_agent_id'];
}else{
  echo $bill_info['oc_store_name'].'<br/>id: '.$bill_info['oc_store_id'];
} ?>
</td>
        <td class="align-center">
<!--        <a href="index.php?act=commission&op=show_bill&ob_no=--><?php //echo $bill_info['oc_no'];?><!--">--><?php //echo $lang['nc_view'];?><!--</a>-->
        </td>
      </tr>
      <?php }?>
      <?php }else{?>
      <tr class="no_data">
        <td colspan="15"><?php echo $lang['nc_no_record'];?></td>
      </tr>
      <?php }?>
    </tbody>
    <tfoot>
      <tr class="tfoot">
        <td colspan="15" id="dataFuncs"><div class="pagination"> <?php echo $output['show_page'];?> </div></td>
      </tr>
    </tfoot>
  </table>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript">
$(function(){
    $('#bill_month').datepicker({dateFormat:'yy-mm'});
    $('#ncsubmit').click(function(){
    	$('#formSearch').attr('target','_self');
    	$('input[name="op"]').val('show_statis');$('#formSearch').submit();
    });
});
</script> 
