<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['order_manage'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=order_in&op=order_class" ><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span>报表统计</span></a></li>
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
                                <!--验收时间-->
         <th><label for="ys_query_start_time">完成时间</label></th>
          <td>
            <input class="txt date" type="text" value="<?php echo $_GET['ys_query_start_time'];?>" id="ys_query_start_time" name="ys_query_start_time">
            <label for="ys_query_start_time">~</label>
            <input class="txt date" type="text" value="<?php echo $_GET['ys_query_end_time'];?>" id="ys_query_end_time" name="ys_query_end_time"/>
          </td> 
          <th>内部物料类别</th>
          <td class="vatop rowform" id="ngcategory">
             <input type="hidden" value="" name="n_brand_class" class="mls_name">
            <select class="class-select" name ="n_class_select">
              <option value="0">请选择...</option>
              <?php if(!empty($output['gc_list'])){ ?>
              <?php foreach($output['gc_list'] as $k => $v){ ?>
              <?php if ($v['gc_parent_id'] == 0) {?>
              <option value="<?php echo $v['gc_id'];?>"><?php echo $v['gc_name'];?></option>
              <?php } ?>
              <?php } ?>
              <?php } ?>
            </select>
             <input type="hidden" value="" name="n_class_id" class="mls_id">
          </td>  
<!--          <th>项目</th>
         <td><input class="txt-short" type="text" name="ktext" value="<?php echo $_GET['ktext'];?>" /></td>-->
         <th>供应商名称</th>
         <td><input class="txt-short" type="text" name="store_company_name" value="<?php echo $_GET['store_company_name'];?>" /></td>
        </tr>
        <tr>
          <th><label for="xd_query_start_time">下单时间</label></th>
          <td>
            <input class="txt date" type="text" value="<?php echo $_GET['xd_query_start_time'];?>" id="xd_query_start_time" name="xd_query_start_time">
            <label for="xd_query_start_time">~</label>
            <input class="txt date" type="text" value="<?php echo $_GET['xd_query_end_time'];?>" id="xd_query_end_time" name="xd_query_end_time"/>
            <span></span>
          </td>
                    <th>外部物料类别</th>
          <td class="vatop rowform" id="wgcategory">
             <input type="hidden" value="" name="brand_class" class="mls_name">
            <select class="class-select" name ="class_select">
              <option value="0"><?php echo $lang['nc_please_choose'];?>...</option>
              <?php if(!empty($output['gc_list'])){ ?>
              <?php foreach($output['gc_list'] as $k => $v){ ?>
              <?php if ($v['gc_parent_id'] == 0) {?>
              <option value="<?php echo $v['gc_id'];?>"><?php echo $v['gc_name'];?></option>
              <?php } ?>
              <?php } ?>
              <?php } ?>
            </select>
             <input type="hidden" value="" name="w_class_id" class="mls_id">
          </td>
           <th><label><?php echo $lang['order_state'];?></label></th>
          <td ><select name="order_state" class="txt-short">
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
         <!--城市中心-->
<!--            <th>中心城市</th>
                <td colspan="4">
                     <select name="city_name" class="querySelect">
                          <option value=""><?php echo $lang['nc_please_choose'];?></option>
                         <?php if(count($output['city_centreList'])>0){?>            
                         <?php foreach($output['city_centreList'] as $city_centre){?>                                        
                         <option value ="<?php echo $city_centre['city_name'];?>" <?php if($_GET['city_id'] == $city_centre['id']) echo 'selected'; ?>  ><?php echo $city_centre['city_name'];?></option>                
                         <?php } }?>
                        </select>
                 </td>-->
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
   <div style="text-align:right;"><a class="btns" href="javascript:void(0);" id="ncexport"><span><?php echo $lang['nc_export'];?>Excel</span></a></div>
  <table class="table tb-type2 nobdb">
    <thead>
      <tr class="thead">
        <th>订单编号</th>
        <th class="align-center">订单状态</th>
<!--        <th class="align-center">公司编码</th>
        <th class="align-center">项目名称</th>
        <th class="align-center">项目编码</th>
        <th class="align-center">城市公司</th>-->
        <th class="align-center">下单日期</th>
        <th class="align-center">完成日期</th>
        <th class="align-center">供应商名称</th>
        <th class="align-center">店铺名称</th>
        <th class="align-center">供应商账号</th>
        <th class="align-center">外部物料编号</th>
        <th class="align-center">外部物料大类</th>
        <th class="align-center">外部物料中类</th>
        <th class="align-center">外部物料小类</th>
        <th class="align-center">外部物料物料名称</th>
        <th class="align-center">外部物料品牌</th>
        <th class="align-center">外部物料规格</th>
        <th class="align-center">内部物料编码</th>
        <th class="align-center">内部物料大类</th>
        <th class="align-center">内部物料中类</th>
        <th class="align-center">内部物料小类</th>
        <th class="align-center">内部物料物料名称</th>
        <th class="align-center">内部物料品牌</th>
        <th class="align-center">内部物料规格</th>       
        <th class="align-center">采购单价</th>
        <th class="align-center">数量</th>
        <th class="align-center">金额</th>
      </tr>
    </thead>
    <tbody>
      <?php if(count($output['order_list'])>0){?>
      <?php foreach($output['order_list'] as $order){?>
      <tr class="hover">
        <td><?php echo $order['order_sn'];?></td> <!--订单号-->
        <td class="align-center"><!--状态-->
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
<!--        <td class="align-center"><?php echo $order['ktext'];?></td>
        <td class="align-center"><?php echo $order['kostl'];?></td>
        <td class="align-center"><?php echo $order['butxt'];?></td>-->
        <td class="nowrap align-center"><?php echo date('Y-m-d H:i:s',$order['add_time']);?></td><!--下单时间-->
        <td class="nowrap align-center"><?php echo date('Y-m-d H:i:s',$order['finnshed_time']);?></td><!--完成时间-->
        <td class="align-center"><?php echo $order['store_company_name'];?></td><!--公司名称-->
        <td class="align-center"><?php echo $order['store_name'];?></td><!--店铺名称-->
        <td class="align-center"><?php echo $order['member_name'];?></td><!--账号--> 
        <td class="align-center"><?php echo $order['materiel_code'];?></td>
        <td class="align-center"><?php echo $order['w_gc_id_1'];?></td>
        <td class="align-center"><?php echo $order['w_gc_id_2'];?></td>
        <td class="align-center"><?php echo $order['w_gc_id_3'];?></td>
        <td class="align-center"><?php echo $order['w_local_description'];?></td>
        <td class="align-center"><?php echo $order['brand'];?></td>
        <td class="align-center"><?php echo $order['product_spec'];?></td>
        <td class="align-center"><?php echo $order['to_product_id'];?></td>
        <td class="align-center"><?php echo $order['n_gc_id_1'];?></td>
        <td class="align-center"><?php echo $order['n_gc_id_2'];?></td>
        <td class="align-center"><?php echo $order['n_gc_id_3'];?></td>
        <td class="align-center"><?php echo $order['n_local_description'];?></td>
        <td class="align-center"><?php echo $order['n_brand'];?></td>
        <td class="align-center"><?php echo $order['n_product_spec'];?></td>
        <td class="align-center"><?php echo $order['goods_price'];?></td>
        <td class="align-center"><?php echo $order['goods_num'];?></td>
        <td class="align-center"><?php echo $order['goods_pay_price'];?></td>
        
        
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
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript">
$(function(){
    //验收时间
    $('#ys_query_start_time').datepicker({dateFormat: 'yy-mm-dd'});
    $('#ys_query_end_time').datepicker({dateFormat: 'yy-mm-dd'});
    //下单时间
    $('#xd_query_start_time').datepicker({dateFormat: 'yy-mm-dd'});
    $('#xd_query_end_time').datepicker({dateFormat: 'yy-mm-dd'});
    
    $('#ncsubmit').click(function(){
        var statre_time = $('#ys_query_start_time').val();      
        var end_time = $('#ys_query_end_time').val();    
        if(statre_time > end_time){
              alert("开始时间不能晚于结束时间");
           return false;
        }
        

        var statre_time = $('#xd_query_start_time').val();      
        var end_time = $('#xd_query_end_time').val();    
        if(statre_time > end_time){
              alert("开始时间不能晚于结束时间");
           return false;
        }   
        
        
    	$('input[name="op"]').val('order_classAll');$('#formSearch').submit();
    });
});

    $("#wgcategory").change(function(){
        var removeui = $("#wgcategory").parent().prev().find('td:first>label:last').is('.error');
        if(removeui){
              $("#wgcategory").parent().prev().find('td:first>label:last').hide();
        }
      
    });
gcategoryInit('wgcategory');

    $("#ngcategory").change(function(){
        var removeui = $("#ngcategory").parent().prev().find('td:first>label:last').is('.error');
        if(removeui){
              $("#ngcategory").parent().prev().find('td:first>label:last').hide();
        }
      
    });
gcategoryInit('ngcategory');
</script> 
<script type="text/javascript">
$(function(){
    $('#ncexport').click(function(){
    $('input[name="op"]').val('dcgoodsList');
    $('#formSearch').submit();
    });
    
});


</script>
