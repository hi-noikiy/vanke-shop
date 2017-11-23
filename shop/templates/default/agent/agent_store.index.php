<?php ?>


<div class="alert alert-block mt10">
    <ul class="mt5">
        <li>1、说明。</li>

    </ul>
</div>

<table class="ncsc-default-table">
    <thead>
    <tr>
        <th class="w30"></th>
        <th class="w180 tl">店铺名称</th>
        <th class="w180 tl">开店时间</th>
        <th class="w180 tl">店主信息</th>
        <th class="tl">地址信息</th>
    </tr>
    </thead>

    <tbody>
    <?php if (is_array($output['order_list']) and !empty($output['order_list'])) { ?>
        <?php foreach($output['order_list'] as $order_id => $order) { ?>
            <tr class="bd-line">
                <td> </td>
                <td class="tl"><?php echo $order['store_name']; ?></td>
                <td class="tl"><em class="goods-time"><?php echo date("Y-m-d H:i:s",$order['store_time']); ?></em></td>
                <td class="tl"><?php echo $order['store_company_name']; ?></td>
                <td class="tl"><?php echo $order['area_info']; ?></td>
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
