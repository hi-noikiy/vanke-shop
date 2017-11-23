<?php ?> 
<style type="text/css">    
      p{margin:0}    
      #page{    
                height:40px;    
                padding:20px 0px;    
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
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>销量分析</h3>
      <?php echo $output['top_link'];?> </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" action="index.php" name="formSearch" id="formSearch">
    <input type="hidden" name="act" value="stat_trade" />
    <input type="hidden" name="op" value="income" />
    <input type="hidden" name="" value="" />
    <div class="w100pre" style="width: 100%;">
      <table class="tb-type1 noborder search left">
        <tbody>
          <tr>
            <th>年份</th>
            <td><select name="search_year" id="search_year" class="querySelect">
                <?php for ($i=2009;$i<=2020;$i++){ ?>
                <option value="<?php echo $i; ?>" <?php echo $_GET['search_year'] == $i?'selected':''; ?>><?php echo $i; ?></option>
                <?php } ?>
              </select></td>
            <th>月份</th>
            <td><select name="search_month" id="search_month" class="querySelect">
                <option value="01" <?php echo $_GET['search_month']=='01'?'selected':''; ?>>1</option>
                <option value="02" <?php echo $_GET['search_month']=='02'?'selected':''; ?>>2</option>
                <option value="03" <?php echo $_GET['search_month']=='03'?'selected':''; ?>>3</option>
                <option value="04" <?php echo $_GET['search_month']=='04'?'selected':''; ?>>4</option>
                <option value="05" <?php echo $_GET['search_month']=='05'?'selected':''; ?>>5</option>
                <option value="06" <?php echo $_GET['search_month']=='06'?'selected':''; ?>>6</option>
                <option value="07" <?php echo $_GET['search_month']=='07'?'selected':''; ?>>7</option>
                <option value="08" <?php echo $_GET['search_month']=='08'?'selected':''; ?>>8</option>
                <option value="09" <?php echo $_GET['search_month']=='09'?'selected':''; ?>>9</option>
                <option value="10" <?php echo $_GET['search_month']=='10'?'selected':''; ?>>10</option>
                <option value="11" <?php echo $_GET['search_month']=='11'?'selected':''; ?>>11</option>
                <option value="12" <?php echo $_GET['search_month']=='12'?'selected':''; ?>>12</option>
              </select></td>
            <td><a href="javascript:void(0);" id="ncsubmit" class="btn-search tooltip" title="<?php echo $lang['nc_query'];?>">&nbsp;</a></td>
          </tr>
        </tbody>
      </table>
      <span class="right" style="margin:12px 0px 6px 4px;"> </span> </div>
  </form>
  <div id="container" class="w100pre close_float" style="height:50px"></div>
  <div style="text-align:right;">
    <input type="hidden" id="export_type" name="export_type" data-param='{"url":"index.php?act=stat_trade&op=income&search_year=<?php echo intval($_GET['search_year']); ?>&search_month=<?php echo trim($_GET['search_month']); ?>&exporttype=excel"}' value="excel"/>
    <a class="btns" href="javascript:void(0);" id="export_btn"><span>导出Excel</span></a> </div>
  <table class="table tb-type2 nobdb">
    <thead>
      <tr class="thead">
        <th class="align-center">店铺名称</th>
        <th class="align-center">卖家账号</th>
        <th class="align-center">订单数量</th>
        <th class="align-center">订单金额</th>
      </tr>
    </thead>
    <tbody id="datatable">
      <?php if(!empty($output['store_list'])){ ?>
      <?php foreach ($output['store_list'] as $k=>$v){ ?>
      <tr class="hover">
        <td class="align-center"><?php echo $v['store_name']; ?></td>
        <td class="align-center"><?php echo $v['member_name']; ?></td>
        <td class="align-center"><?php echo $v['COUNT(stat_order.order_sn)']; ?></td>
        <td class="align-center"><?php echo $v['SUM(order_amount)']; ?></td>
        <td class="align-center"><?php echo $v['ob_order_return_totals']; ?></td>
       <td class="align-center"><a href="index.php?act=stat_trade&op=sale&search_type=month&search_time_month=<?php echo intval($_GET['search_month']); ?>&search_time_year=<?php echo intval($_GET['search_year']); ?>&store_name=<?php echo $v['store_name']; ?>&date=<?php echo $output['date'];?>">详细</a></td>
      </tr>
      <?php } ?>
      <?php }else { ?>
      <tr class="no_data">
        <td colspan="15"><?php echo $lang['nc_no_record'];?></td>
      </tr>
      <?php } ?>
    </tbody>
    <?php if(!empty($output['store_list']) && is_array($output['store_list'])){ ?>
    <tfoot>
      <tr class="tfoot">
        <td colspan="15" id="dataFuncs"><div class="pagination"> <?php echo $output['show_page'];?> </div></td>
      </tr>
    </tfoot>
    <?php } ?>
  </table>
  <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script> 
  <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css" />
  <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/highcharts/highcharts.js"></script> 
  <script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/statistics.js"></script> 
</div>
<script>
$(function () {
	$('#ncsubmit').click(function(){
    	$('#formSearch').submit();
    });

	//导出图表
    $("#export_btn").click(function(){
        var item = $("#export_type");
        var type = $(item).val();
        if(type == 'excel'){
        	download_excel(item);
        }
    });
    
	$('#ncexport').click(function(){
		$("#")
    	$('#formSearch').submit();
    });
});
</script>