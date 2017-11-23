<?php ?>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['store'];?></h3>
	<ul class="tab-base">
        <li><a <?php if($_GET['op'] == "store_joinin2"){?> href="JavaScript:void(0);" class="current" <?php }else{?>  href="index.php?act=store&op=store_joinin2" <?php } ?> ><span>认证申请审核</span></a></li>
        <li><a <?php if($_GET['op'] == "store_joinin"){?> href="JavaScript:void(0);" class="current" <?php }else{?>  href="index.php?act=store&op=store_joinin" <?php } ?> ><span><?php echo $lang['pending'];?>审核</span></a></li>
        <li><a href="index.php?act=store_edit&op=index" ><span>修改供应商审核</span></a></li>
        <li><a href="index.php?act=store&op=newshop_add" ><span>新增供应商</span></a></li>
        <li><a <?php if($_GET['op'] == "newtemporary_add"){?> href="JavaScript:void(0);" class="current" <?php }else{?>   href="index.php?act=store&op=newtemporary_add" <?php } ?> ><span>新增临时供应商</span></a></li> 
        <li><a <?php if($_GET['op'] == "type_level_list"){?> href="JavaScript:void(0);" class="current" <?php }else{?>   href="index.php?act=store&op=type_level_list" <?php } ?> ><span>供应商类型级别修改</span></a></li>
        <li><a href="index.php?act=store&op=store_type_edit" class="current"><span>供应商店铺类型修改</span></a></li>
        <li><a href="index.php?act=store&op=store_push_list" ><span>手动推送合同</span></a></li>        
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" name="formSearch">
    <input type="hidden" value="store" name="act">
    <input type="hidden" value="store_type_edit" name="op">
    <table class="tb-type1 noborder search">
      <tbody>
        <tr>
          <th><label for="owner_and_name">供应商账号</label></th>
          <td><input type="text" value="<?php echo empty($_GET['owner_and_name']) ? "":$_GET['owner_and_name'];?>" name="owner_and_name" id="owner_and_name" class="txt"></td>
            <th><label for="owner_and_store">店铺名称</label></th>
            <td><input type="text" value="<?php echo empty($_GET['owner_and_store']) ? "":$_GET['owner_and_store'];?>" name="owner_and_store" id="owner_and_store" class="txt"></td>
            <td><a href="javascript:document.formSearch.submit();" class="btn-search " title="<?php echo $lang['nc_query'];?>">&nbsp;</a>
                <?php if($output['owner_and_name'] != '' or $output['grade_id'] != ''){?>
                <a href="index.php?act=store&op=store_type_edit" class="btns " title="<?php echo $lang['nc_cancel_search'];?>"><span><?php echo $lang['nc_cancel_search'];?></span></a>
                <?php }?></td>
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
            <li></li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <form method="post" id="store_form" name="store_form">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="type" id="type" value="" />
    <table class="table tb-type2">
      <thead>
        <tr class="thead">
          <th class="align-center"><?php echo $lang['store_user_name'];?></th>
          <th class="align-center">店铺名称</th>
          <th class="align-center">店铺类型</th>
          <th class="align-center"><?php echo $lang['operation'];?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['supply_list']) && is_array($output['supply_list'])){ ?>
        <?php foreach($output['supply_list'] as $k => $v){ ?>
        <tr class="hover edit">
          <td class="align-center"><?php echo $v['member_name'];?></td>
          <td class="align-center"><?php echo $v['store_name'];?></td>
          <td class="align-center"><?php echo $v['sc_name'];?></td>
          <td class="w72 align-center">
          	<a href="index.php?act=store&op=store_type_detail&member_id=<?php echo $v['member_id']?>">编辑</a>
          </td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr class="no_data">
          <td colspan="10"><?php echo $lang['nc_no_record'];?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td></td>
          <td colspan="15">
              <?php if(!empty($output['supply_list']) && is_array($output['supply_list'])){ ?>
              <div class="pagination"><?php echo $output['page'];?></div>
              <?php } ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.edit.js" charset="utf-8"></script>
<script>
function audit_submit(type){
	$('#type').val(type);
	$("#store_form").submit();
	return true;
}
</script>
