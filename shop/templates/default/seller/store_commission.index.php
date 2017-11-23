<?php ?>


<div class="alert alert-block mt10">
    <ul class="mt5">
        <li>1、您的店铺会员在本商城产生的消费订单，我们将给您一定的佣金。</li>
        <li>2、佣金计算公式：  ( 佣金金额 + 虚拟佣金 - 退还佣金 ) * 分润比率 = 我的佣金。</li>
    </ul>
</div>


<table class="ncsc-default-table">
    <thead>
    <tr>
        <th>账单编号</th>
        <th class="align-center">账单周期</th>
        <th class="align-center">出账日期</th>
        <th class="align-center">账单状态</th>
<!--        <th class="align-center">订单金额</th>-->
<!--        <th class="align-center">运费</th>-->
        <th class="align-center">佣金金额</th>
<!--        <th class="align-center">退款金额</th>-->
        <th class="align-center">虚拟佣金</th>
        <th class="align-center">退还佣金</th>
        <th class="align-center">我的佣金</th>



    </tr>
    </thead>

    <tbody>
    <?php if (is_array($output['order_list']) and !empty($output['order_list'])) { ?>
        <?php foreach($output['order_list'] as $order_id => $bill_info) { ?>
            <tr class="bd-line">
                <td><?php echo $bill_info['oc_no'];?></td>
                <td class="nowrap align-center"><?php echo date('Y-m-d',$bill_info['oc_start_date']);?> - <?php echo date('Y-m-d',$bill_info['oc_end_date']);?></td>
                <td class="align-center"><?php echo date('Y-m-d',$bill_info['oc_create_date']);?></td>
                <td class="align-center"><?php echo billState($bill_info['oc_state']);?></td>
<!--                <td class="align-center">--><?php //echo $bill_info['oc_totals'];?><!--</td>-->
<!--                <td class="align-center">--><?php //echo $bill_info['oc_shipping_totals'];?><!--</td>-->
                <td class="align-center"><?php echo $bill_info['oc_commis_totals'];?></td>
<!--                <td class="align-center">--><?php //echo $bill_info['oc_return_totals'];?><!--</td>-->
                <td class="align-center"><?php echo $bill_info['oc_vr_commis_totals'];?></td>
                <td class="align-center"><?php echo $bill_info['oc_commis_return_totals'];?></td>
                <td class="align-center"><strong><?php echo $bill_info['oc_result_totals'];?></strong></td>

            </tr>
        <?php }}else{ ?>
    <tr>
        <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i>
                <span>没有相关店铺</span>
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
