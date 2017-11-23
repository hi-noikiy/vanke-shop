<?php ?>
<style type="text/css">    
      p{margin:0}    
      #page{ 
      	     background-color: #FFFFFF;
      	     border-bottom: 0px solid;
            }    
      #page a{    
                display:block;    
                float:left;    
                margin-right:10px;    
                padding:2px 12px;    
                height:24px;    
                border:1px #cccccc solid;    
                background:#fff;    
                text-decoration:none;    
                color:#808080;    
                font-size:12px;    
                line-height:24px;    
            }    
       #page a:hover{    
                color:#077ee3;    
                border:1px #077ee3 solid;    
            }    
       #page a.cur{    
                border:none;    
                background:#20b2aa;    
                color:#fff;    
            }    
       #page p{    
                float:left;    
                padding:2px 12px;    
                font-size:12px;    
                height:24px;    
                line-height:24px;    
                color:#bbb;    
                border:1px #ccc solid;    
                background:#fcfcfc;    
                margin-right:8px;    
    
            }    
       #page p.pageRemark{    
                border-style:none;    
                background:none;    
                margin-right:0px;    
                padding:4px 0px;    
                color:#666;    
            }    
       #page p.pageRemark b{    
                color:red;    
            }    
       #page p.pageEllipsis{    
                border-style:none;    
                background:none;    
                padding:4px 0px;    
                color:#808080;    
            }    
   	 .dates li {font-size: 14px;margin:20px 0}    
     .dates li span{float:right}    
</style> 
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/inordermenu');?>
  </div>
  <form method="get" action="index.php" target="_self">
    <table class="ncm-search-table">
      <input type="hidden" name="act" value="member_unline" />
	  <input type="hidden" name="op" value="index" />
      <tr>
        <th><?php echo $lang['member_order_state'];?></th>
        <td class="w100">
        	<select name="state_type">
	            <option value="<?php echo ORDER_STATUS_SUCCESS;?>" <?php if($_GET['state_type']=='14'){echo 'selected';} ?>>待发货</option>
	            <option value="<?php echo ORDER_STATUS_SEND_HET;?>" <?php if($_GET['state_type']=='31'){echo 'selected';} ?>><?php echo $lang['order_show_send'];?></option>
	            <option value="<?php echo ORDER_STATUS_CUS_RECEIVED;?>" <?php if($_GET['state_type']=='33'){echo 'selected';} ?>>检收完成</option>
           </select>
        </td>
        <th><?php echo $lang['member_order_time'];?></th>
        <td class="w240"><input type="text" class="text w70" name="query_start_date" id="query_start_date" value="<?php echo $_GET['query_start_date']; ?>"/><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;<input type="text" class="text w70" name="query_end_date" id="query_end_date" value="<?php echo $_GET['query_end_date']; ?>"/><label class="add-on"><i class="icon-calendar"></i></label></td>
        <th><?php echo $lang['member_order_sn'];?></th>
        <td ><input type="text" class="text w150" name="ordersn" id="ordersn" value="<?php echo $_GET['ordersn']; ?>"></td>
        <td >
        <label class="submit-border">
            <input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>"/>
          </label></td>
          <td>
           <label class="submit-border">
            <input type="submit" class="submit" style="width:60px;height:28px;background-color: #F5F5F5;" onclick="clear_input()" value="撤销<?php echo $lang['nc_search'];?>"/>
          </label>
          </td>
      </tr>
    </table>
  </form>
  <table class="ncm-default-table order">
    <thead>
      <tr>
        <th class="w200"></th>
        <th class="w300">订单号</th>
        <th class="w500">商品</th>
        <th class="w300">单价（元）</th>
        <th class="w300">数量</th>
        <th class="w120">订单金额</th>
        <th class="w100">下单时间</th>
        <th class="w100">交易状态</th>
        <th class="w150">交易操作</th>
      </tr>
    </thead>
    <?php if ($output['order_group_list']) { ?> 
             <?php foreach($output['order_group_list'] as $order_id2 => $order_info2) {?>
      <thead>
        <tr data-type="orderNo_list">
        <td class="w200">项目名称：<?php echo $order_info2['butxt'];?></td>
        <td class="w300" data-type="data_orderNo" data-value="<?php echo $order_info2['orderNo'];?>">订单号：<?php echo $order_info2['orderNo'];?></td>
        <td class="w500" style="text-align:left;">收货地址：<?php echo $order_info2['deliveryAddress'];?></td>
        <td class="w300">收货人：<?php echo $order_info2['deliveryPerson'];?></td>
        <td class="w300" style="text-align:left;">
        <?php if($_GET['state_type']=='14') {?>
            <a  href="javascript:void(0)" onclick="fn(this)" class="ncm-btn ncm-btn-red send_order_statelist"   dialog_title="批量发货"  id="orderList" lang='<?php echo $order_info2['orderNo'];?>'>批量发货</a>
       <?php }?>
        </td>
        </tr>
         <?php foreach($order_info2 as $order_id1 => $order_info1) {?>
        <?php if($order_id1=="sub_order_json"){?>
            <?php foreach($order_info1 as $order_id => $order_info) {?>
                <tr data-type="order_list">               <!-- 商品列表 -->
                  <td class="bdl"></td>
                  <td data-type="data_subOrderNo" data-value="<?php echo $order_info['subOrderNo'];?>"><?php echo $order_info['subOrderNo'];?></td>
                  <td class="tl">
                      <dl class="goods-name">
                            <dt><?php echo $order_info['orderName']; ?></dt>
                       </dl>
                  </td>
                  <td><?php echo $order_info['orderPrice'];?></td>
                  <td><?php echo $order_info['orderQty']; ?></td>
                  <td><?php echo $order_info['orderAmt'];?></td>
                  <td><?php echo $order_info['orderDate'];?></td>
                  <td data-type="data_orderStatus" data-value="<?php echo $order_info['orderStatus'];?>">
                      <?php if($order_info['orderStatus'] == ORDER_STATUS_SUCCESS){?>
                          待发货
                      <?php }else if($order_info['orderStatus'] == ORDER_STATUS_SEND_HET){?>
                          已发货
                      <?php }else if($order_info['orderStatus'] == ORDER_STATUS_CUS_RECEIVED){?>
                          检收完成
                      <?php }?>
                  </td>
                  <?php if($order_info['orderStatus'] != ORDER_STATUS_SEND_HET  && $order_info['orderStatus'] == ORDER_STATUS_SUCCESS){ ?>
                  <td>
                      <a href="javascript:void(0)" class="ncm-btn ncm-btn-green send_order_state order_<?php echo $order_info['subOrderNo']; ?>" lang='<?php echo $order_info['subOrderNo']; ?>'  dialog_title="发货"  id="order264_action_cance"> 快速发货</a>
                  </td>
                  <?php }?>
                </tr><?php }?>
        </thead>
            <?php }?> <!-- E 商品列表 -->
        <?php }?>   
      <?php } ?>
    <?php } else { ?>
    <tbody>
      <tr>
        <td colspan="20" class="norecord"><div class="warning-option"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></div></td>
      </tr>
    </tbody>
     <?php } ?>   
    <?php if($output['page_num'] >= 1){
                if(!$output['page']){$output['page'] = 1;} ?>
      <tr>
        <td colspan="19">
            <div class="pagination"> 
               <?php echo $output['show_page'];?>
            </div>
        </td>  
      </tr>
    <?php }?>  
  </table>
</div>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/sns.js" ></script>
<script type="text/javascript">
function clear_input(){
	$("#query_start_date").val(""); 
	$("#query_end_date").val(""); 
	$("#ordersn").val(""); 
}
$(function(){
    $('.send_order_state').click(function(){
        var sub_order_no = $(this).attr('lang');
        $.post(
            'index.php?act=member_unline&op=send_order_sendstatus2',
                {
                    sub_order_no:sub_order_no,
                    orderstate:'<?php echo ORDER_STATUS_SEND_HET;?>  ',
                    state_flag:"0",
                },
                function(data){
                    if(data == 1){
                        $('.order_'+sub_order_no).html('已发货');
                        $('.order_'+sub_order_no).unbind("click");
                        $('.order_'+sub_order_no).removeClass();
                    }else{
                        alert('发货失败！请稍后尝试！');
                    }
                });
    }) 
    $('#query_start_date').datepicker({dateFormat: 'yy-mm-dd'});
    $('#query_end_date').datepicker({dateFormat: 'yy-mm-dd'});
});
function fn(obj){
//取到总订单的编号
    var subOrderNo = $(obj).parent().parent().parent().find('tr[data-type="orderNo_list"]').find('td[data-type="data_orderNo"]').attr('data-value');
    var num =0;
     //取到子订单的状态 
     $(obj).parent().parent().parent().find('tr[data-type="order_list"]').each(function(){
       var dataorder= $(this).find('td[data-type="data_orderStatus"]').attr('data-value');
       if(dataorder == 12 || dataorder == 13){
          num++;
       }
     });
    if( num==0){
         $.post(
            'index.php?act=member_unline&op=send_order_sendstatus2',
                {
                    order_no:subOrderNo,
                    orderstate:'<?php echo ORDER_STATUS_SEND_HET;?>  ',
                    state_flag:"1",
                },
                function(data){
                    if(data == 1){
                             //取到子订单的编号
                    $(obj).parent().parent().parent().find('tr[data-type="order_list"]').each(function(){
                       var sub_order_no=$(this).find('td[data-type="data_subOrderNo"]').attr('data-value');
                        $('.order_'+sub_order_no).html('已发货');
                        $('.order_'+sub_order_no).unbind("click");
                        $('.order_'+sub_order_no).removeClass();
                             });
                    }else{
                        alert('发货失败！请稍后尝试！');
                    }
                });
                }else {
                     alert("订单状态不允许提交");
                       return false;
                }
}
</script>
