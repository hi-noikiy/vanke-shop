<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['order_manage'];?></h3>
      <ul class="tab-base">
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="index.php?act=order_in&op=order_classAll"><span>报表统计</span></a></li>
        <li><a href="index.php?act=order_in&op=order_gys_All"><span>供应商统计</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" action="index.php" name="formSearch" id="formSearch">
    <input type="hidden" name="act" value="order_in" />
    <input type="hidden" name="op" value="order_class" />
    <table class="tb-type1 noborder search">
      <tbody>
        <tr>
         <th><label><?php echo $lang['order_number'];?></label></th>
         <td><input class="txt2" type="text" name="order_sn" value="<?php echo $_GET['order_sn'];?>" /></td>
         <th><?php echo $lang['store_name'];?></th>
         <td><input class="txt-short" type="text" name="store_name" value="<?php echo $_GET['store_name'];?>" /></td>
         <!--项目筛选先隐藏-->
<!--          <th><?php echo $lang['ktext'];?></th>  
             <td><input class="txt-short" type="text" name="ktext" value="<?php echo $_GET['ktext'];?>" /></td>-->
         <th><label><?php echo $lang['order_state'];?></label></th>
          <td colspan="4"><select name="order_state" class="querySelect">
              <option value=""><?php echo $lang['nc_please_choose'];?></option>
              <option value="<?php echo ORDER_STATUS_SEND_ONE;?>" <?php if($_GET['order_state'] == ORDER_STATUS_SEND_ONE){?>selected<?php }?>><?php echo $lang['order_audit'];?></option>
              <option value="<?php echo ORDER_STATUS_SEND_TWO;?>" <?php if($_GET['order_state'] == ORDER_STATUS_SEND_TWO){?>selected<?php }?>><?php echo $lang['pending_audit'];?></option>
              <option value="<?php echo ORDER_STATUS_SUCCESS;?>" <?php if($_GET['order_state'] == ORDER_STATUS_SUCCESS){?>selected<?php }?>><?php echo $lang['through_audit'];?></option>
              <option value="<?php echo ORDER_STATUS_ERROR;?>" <?php if($_GET['order_state'] == ORDER_STATUS_ERROR){?>selected<?php }?>><?php echo $lang['audit_return'];?></option>
              <option value="<?php echo ORDER_STATUS_OUT;?>" <?php if($_GET['order_state'] == ORDER_STATUS_OUT){?>selected<?php }?>><?php echo $lang['audit_reject'];?></option>
              <option value="<?php echo ORDER_STATUS_CUS_RECEIVED;?>" <?php if($_GET['order_state'] == ORDER_STATUS_CUS_RECEIVED){?>selected<?php }?>><?php echo $lang['audit_issend'];?></option>
              <option value="<?php echo ORDER_STATUS_SEND_HET;?>" <?php if($_GET['order_state'] == ORDER_STATUS_SEND_HET){?>selected<?php }?>><?php echo $lang['audit_instat'];?></option>
              <option value="<?php echo ORDER_STATE_SEND;?>" <?php if($_GET['order_state'] == ORDER_STATE_SEND){?>selected<?php }?>><?php echo $lang['order_state_send'];?></option>
              <option value="<?php echo ORDER_STATE_SUCCESS;?>" <?php if($_GET['order_state'] == ORDER_STATE_SUCCESS){?>selected<?php }?>><?php echo $lang['audit_over'];?></option>
              <option value="<?php echo ORDER_STATE_CANCEL;?>" <?php if(isset($_GET['order_state']) && $_GET['order_state']!= '' ) {if($_GET['order_state'] == ORDER_STATE_CANCEL){?>selected<?php }}?>><?php echo $lang['order_state_cancel'];?></option>
             </select></td> 
        </tr>
        <tr>
          <th><label for="query_start_time"><?php echo $lang['order_time_from'];?></label></th>
          <td><input class="txt date" type="text" value="<?php echo $_GET['query_start_time'];?>" id="query_start_time" name="query_start_time">
            <label for="query_start_time">~</label>
            <input class="txt date" type="text" value="<?php echo $_GET['query_end_time'];?>" id="query_end_time" name="query_end_time"/>
            <span></span>
          </td>
         <th><?php echo $lang['buyer_name'];?></th>
         <td><input class="txt-short" type="text" name="buyer_name" value="<?php echo $_GET['buyer_name'];?>" /></td>
          <th><?php echo $lang['member_truename'];?></th>
          <td><input class="txt-short" type="text" name="member_truename" value="<?php echo $_GET['member_truename'];?>" /></td>
         <!--城市中心-->
            <th><?php echo $lang['buyer_city_name'];?></th>
                <td colspan="4">
                     <select name="city_id" class="querySelect">
                          <option value=""><?php echo $lang['nc_please_choose'];?></option>
                         <?php if(count($output['city_centreList'])>0){?>            
                         <?php foreach($output['city_centreList'] as $city_centre){?>                                        
                         <option value ="<?php echo $city_centre['id'];?>" <?php if($_GET['city_id'] == $city_centre['id']) echo 'selected'; ?>  ><?php echo $city_centre['city_name'];?></option>                
                         <?php } }?>
                        </select>
                 </td>
         <!--放大镜-->
          <td><a href="javascript:void(0);" id="ncsubmit" class="btn-search " title="<?php echo $lang['nc_query'];?>">&nbsp;</a>
            
            </td>
        </tr>
      </tbody>
    </table>
  </form>
  <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th colspan="12"><div class="title"><h5><?php echo $lang['nc_prompts'];?></h5><span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td>
        <ul>
            <li><?php echo $lang['order_help1'];?></li>
            <li><?php echo $lang['order_help2'];?></li>
            <li><?php echo $lang['order_help3'];?></li>
          </ul></td>
      </tr>
      
    </tbody>
  </table>
  <!--<div style="text-align:right;"><a class="btns" target="_blank" href="index.php?<?php echo $_SERVER['QUERY_STRING'];?>&op=export_step1"><span><?php echo $lang['nc_export'];?>Excel</span></a></div>-->
   <div style="text-align:right;"><a class="btns" href="javascript:void(0);" id="ncexport"><span><?php echo $lang['nc_export'];?>Excel</span></a></div>
  <table class="table tb-type2 nobdb">
    <thead>
      <tr class="thead">
        <th><?php echo $lang['order_number'];?></th>
        <th class="align-center"><?php echo $lang['store_name'];?></th>
        <th class="align-center"><?php echo $lang['buyer_name'];?></th>
        <th class="align-center">项目名称</th>
        <th class="align-center">买家名称</th>
        <th class="align-center"><?php echo $lang['buyer_city_name'];?></th>
        <th class="align-center"><?php echo $lang['order_time'];?></th>
        <th class="align-center"><?php echo $lang['order_total_price'];?></th>
<!-- 支付方式注释        <th class="align-center"><?php echo $lang['payment'];?></th>-->
        <th class="align-center"><?php echo $lang['order_state'];?></th>
        <th class="align-center"><?php echo $lang['nc_handle'];?></th>
      </tr>
    </thead>
    <tbody>
      <?php if(count($output['order_list'])>0){?>
      <?php foreach($output['order_list'] as $order){?>
      <tr class="hover">
        <td><?php echo $order['order_sn'];?></td>
        <td class="align-center"><?php echo $order['store_name'];?></td>
        <td class="align-center"><?php echo $order['buyer_name'];?></td>
        <td class="align-center"><?php echo $order['ktext'];?></td>
        <td class="align-center"><?php echo $order['member_truename'];?></td>
        <td class="align-center"><?php echo $order['city_name'];?></td><!--输出城市中心名称-->
        <td class="nowrap align-center"><?php echo date('Y-m-d H:i:s',$order['add_time']);?></td>
        <td class="align-center"><?php echo $order['order_amount'];?></td>
<!-- 支付方式注释       <td class="align-center"><?php echo orderPaymentName($order['payment_code']);?></td>-->
        <td class="align-center">
        <?php if($order['order_state'] == ORDER_STATUS_SEND_TWO){
            echo $lang['pending_audit'];
        }else if($order['order_state'] == ORDER_STATUS_SEND_ONE){
            echo $lang['order_audit'];
        }else if($order['order_state'] == ORDER_STATUS_SUCCESS){
            echo $lang['through_audit']; 
        }else if($order['order_state'] == ORDER_STATUS_ERROR){
            echo $lang['audit_return']; 
        }else if($order['order_state'] == ORDER_STATUS_OUT){
            echo $lang['audit_reject']; 
        }else if($order['order_state'] == ORDER_STATUS_CUS_RECEIVED){
            echo $lang['audit_issend']; 
        }else if($order['order_state'] == ORDER_STATE_SUCCESS){
            echo $lang['audit_over']; 
        }else if($order['order_state'] == ORDER_STATUS_SEND_HET){
            echo $lang['audit_instat']; 
        }else if($order['order_state'] == ORDER_STATE_SEND){
            echo $lang['order_state_send'];
        }else if($order['order_state'] == ORDER_STATE_CANCEL){
            echo $lang['order_state_cancel'];
        }?>
        </td>
        <td class="w144 align-center"><a href="index.php?act=order_in&op=show_order&order_id=<?php echo $order['order_id'];?>"><?php echo $lang['nc_view'];?></a>
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
    $('#query_start_time').datepicker({dateFormat: 'yy-mm-dd'});
    $('#query_end_time').datepicker({dateFormat: 'yy-mm-dd'});
    $('#ncsubmit').click(function(){
        
        var statre_time = $('#query_start_time').val();      
        var end_time = $('#query_end_time').val();    
        if(statre_time > end_time){
              alert("开始时间不能晚于结束时间");
           return false;
        }
    	$('input[name="op"]').val('order_class');$('#formSearch').submit();
    });
});
</script> 
<script type="text/javascript">
$(function(){
    $('#ncexport').click(function(){
    $('input[name="op"]').val('Purchaseexport_step1');
    $('#formSearch').submit();
    });
});


</script>
