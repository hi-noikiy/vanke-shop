<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['order_manage'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=order_in&op=order_class" ><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="index.php?act=order_in&op=order_classAll"><span>报表统计</span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span>供应商统计</span></a></li>
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
            <th>供应商账号</th>
            <td colspan="4">
                <input name="supplier_code" id="supplier_code" value="<?php echo $_GET['supplier_code'];?>" type="text">
            </td>
            <th>供应商明名称</th>
            <td colspan="4">
                <input name="supplier_name" id="supplier_name" value="<?php echo $_GET['supplier_name'];?>" type="text">
            </td>
         <!--城市中心-->
            <th>所属城市中心</th>
                <td colspan="4">
                     <select name="city_id" class="querySelect">
                          <option value=""><?php echo $lang['nc_please_choose'];?></option>
                         <?php if(count($output['city_centreList'])>0){?>            
                         <?php foreach($output['city_centreList'] as $city_centre){?>                                        
                         <option value ="<?php echo $city_centre['id'];?>" <?php if($_GET['city_id'] == $city_centre['id']) echo 'selected'; ?>  ><?php echo $city_centre['city_name'];?></option>                
                         <?php } }?>
                        </select>
                 </td>
            <th>供应商等级</th>
                <td colspan="4">
                     <select name="supply_level" class="querySelect">
                        <option value="">选择</option>
                        <option value="1" <?php if($_GET['supply_level'] == 1) echo "selected"; ?>>优选</option>
                        <option value="2" <?php if($_GET['supply_level'] == 2) echo "selected"; ?>>合格</option>
                        <option value="3" <?php if($_GET['supply_level'] == 3) echo "selected"; ?>>淘汰</option>
                        </select>
                 </td>   
                <!--城市公司-->
<!--            <th>地区</th>
                <td colspan="4">
                     <select name="area_list_id" class="querySelect">
                          <option value="">请选择</option>
                         <?php if(count($output['area_List'])>0){?>            
                         <?php foreach($output['area_List'] as $area_List){?>                                        
                         <option value ="<?php echo $area_List['area_id'];?>" <?php if($_GET['area_list_id'] == $area_List['area_id']) echo 'selected'; ?>  ><?php echo $area_List['area_name'];?></option>                
                         <?php } }?>
                        </select>
                 </td>-->
            <th>是否开店</th>
                <td colspan="4">
                    <select name="role_id" class="querySelect">
                        <option value="">选择</option>
                        <option value="02" <?php if($_GET['role_id'] == "02") echo "selected"; ?>>否</option>
                        <option value="03" <?php if($_GET['role_id'] == "03") echo "selected"; ?>>是</option>
                     </select>
                 </td>  
         <!--放大镜-->
          <td><a href="javascript:void(0);" id="ncsubmit" class="btn-search " title="<?php echo $lang['nc_query'];?>">&nbsp;</a>
            
            </td>
        </tr>
      </tbody>
    </table>
  </form>
  <!--操作提示-->
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
        <th>供应商账号</th>
        <th class="align-center">供应商名称</th>
        <th class="align-center">店铺名称</th>
        <!--<th class="align-center">所在地</th>-->
        <th class="align-center">城市公司</th>
        <th class="align-center">联系人</th>
        <th class="align-center">联系电话</th>
        <th class="align-center">邮箱地址</th>
        <th class="align-center">注册时间</th>
        <th class="align-center">有效截止时间</th>
<!--        <th class="align-center">类别</th>-->
        <th class="align-center">供应商等级</th>
<!--        <th class="align-center">最后评估时间</th>-->
        <th class="align-center">供应商店铺分类</th>
          <th class="align-center">上架商品数</th>
          <th class="align-center">已审核商品数</th>
          <th class="align-center">销售金额</th>
      </tr>
    </thead>
    <tbody>
      <?php if(count($output['store_joinin_list'])>0){?>
      <?php foreach($output['store_joinin_list'] as $order){?>
      <tr class="hover">
        <td><?php echo $order['member_name'];?></td>
        <td class="nowrap align-center"><?php echo $order['company_name'];?></td>
        <td class="nowrap align-center"><?php echo $order['store_name'];?></td>
        <!--<td class="align-center"><?php echo $order['cit_name'];?></td>-->
        <td class="align-center"><?php echo $order['city_name'];?></td>
        <td class="align-center"><?php echo $order['city_contacts_name'];?></td>
        <td class="nowrap align-center"><?php echo $order['city_contacts_phone'];?></td>
        <td class="align-center"><?php echo $order['contacts_email'];?></td>
        <td class="nowrap align-center"><?php echo date('Y-m-d H:i:s',$order['member_time']);?></td>
        <td class="nowrap align-center"><?php echo date('Y-m-d H:i:s',$order['supply_end_time']);?></td>
        <!--供应商类别-->
<!--        <td class="w144 align-center"><?php echo $order['supply_type_json'];?></td>-->
        <td class="align-center">
        <?php if($order['supply_level'] == "1"){
            echo "优选";
        }else if($order['supply_level'] == "2"){
            echo "合格";
        }else if($order['supply_level'] == "3"){
            echo "淘汰"; 
        }?>
        </td>
          <td class="align-center"><?php echo $order['sc_name'];?></td>
          <td class="align-center"><?php echo $order['line_num'];?></td>
          <td class="align-center"><?php echo $order['tg_num'];?></td>
          <td class="align-center"><?php echo empty($order['all_money']) ? 0.0000:$order['all_money'];?></td>
      </tr>
      <?php }?>
      <?php }else{?>
      <tr class="no_data">
          <!--没有符合条件的记录-->
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
    	$('input[name="op"]').val('order_gys_All');$('#formSearch').submit();
    });
});
</script> 
<script type="text/javascript">
$(function(){
    $('#ncexport').click(function(){
    $('input[name="op"]').val('Store_joinin');
    $('#formSearch').submit();
    });
});


</script>
