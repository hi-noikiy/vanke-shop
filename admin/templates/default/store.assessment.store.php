<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['title'];?></h3>
      <ul class="tab-base">
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" name="formSearch" id="formSearch">
  <input type="hidden" value="assessment" name="act">
  <input type="hidden" value="store" name="op">
  <table class="tb-type1 noborder search">
  <tbody>
    <tr>
      <th><label for="store_name"><?php echo $lang['store_name'];?></label></th>
      <td><input type="text" value="<?php echo $output['store_name'];?>" name="store_name" id="store_name" class="txt"></td>
      <th><label for="store_name">评估状态</label></th>
      <td>
          <select name='store_assessment' >
              <option selected="selected" value >全部供应商</option>
              <option <?php if($output['store_assessment'] == 1){echo 'selected="selected"';}?> value="1" >优秀供应商</option>
              <option <?php if($output['store_assessment'] == 2){echo 'selected="selected"';}?> value="2" >合格供应商</option>
          </select>
      </td>
        
      <td><a href="javascript:void(0);" id="ncsubmit" class="btn-search " title="<?php echo $lang['nc_query'];?>">&nbsp;</a>
        <?php if($output['owner_and_name'] != '' or $output['store_name'] != '' or $output['store_assessment'] != ''){?>
        <a href="index.php?act=assessment&op=store" class="btns " title="<?php echo $lang['nc_cancel_search'];?>"><span><?php echo $lang['nc_cancel_search'];?></span></a>
        <?php }?></td>
    </tr></tbody>
  </table>
  </form>
  <form method="post" id="store_form">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <thead>
        <tr class="thead">
          <th><?php echo $lang['store_name'];?></th>
          <th><?php echo $lang['store_user_name'];?></th>
          <th>公司名称</th>
          <th>供应商卖家账号</th>
          <th class="align-center"><?php echo $lang['edit'];?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['store_list']) && is_array($output['store_list'])){ ?>
        <?php foreach($output['store_list'] as $k => $v){ ?>
        <tr class="hover edit">
          <td><?php echo $v['store_name'];?></td>
          <td><?php echo $v['member_name'];?></td>
          <td><?php echo $v['company_name'];?></td>
          <td><?php echo $v['seller_name'];?></td>
        <td class="align-center w220">
            <a href="index.php?act=assessment&op=index&member_id=<?php echo $v['member_id'];?>">编辑模板</a>	
            <a href="index.php?act=assessment&op=exam&member_id=<?php echo $v['member_id'];?>">评估</a>   
             <a href="index.php?act=assessment&op=look&member_id=<?php echo $v['member_id'];?>">查看评估</a> 
             <?php if($v['city_center'] == $output['admin_city'] || $output['admin_city'] == '0'){?>
             <!--<a class='grade_shan' lang-member='<?php echo $v['member_id']?>' lang="<?php echo $v['city_center'];?>" href="javascript:void(0)">评级</a>	-->
            <?php }else{?>
                <a href="javascript:void(0)">　　</a>	
            <?php }?>
        </td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr class="no_data">
          <td colspan="15"><?php echo $lang['nc_no_record'];?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td></td>
          <td colspan="16">
            <div class="pagination"><?php echo $output['page'];?></div></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/dialog/dialog.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
<script>
$(function(){
    $('#ncsubmit').click(function(){
    	$('input[name="op"]').val('store');$('#formSearch').submit();
    });
    
    $('.grade_shan').click(function(){
        var id= $(this).attr("lang");
        var member_id= $(this).attr("lang-member");
        
        _uri_nbb = "<?php echo ADMIN_SITE_URL;?>/index.php?act=assessment&op=grade_shan&id="+id+"&member_id="+member_id;
        CUR_DIALOG = ajax_form('grade_shan', '供应商评级', _uri_nbb, 480);
    })
});
</script>
