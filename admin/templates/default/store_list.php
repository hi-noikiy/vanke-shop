<?php ?>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>供应商清单</h3>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" name="formSearch">
    <input type="hidden" value="store_list" name="act">
    <input type="hidden" value="store_list" name="op">
    <table class="tb-type1 noborder search">
      <tbody>
        <tr>
          <th><label for="store_name"><?php echo $lang['store_name'];?></label></th>
          <td><input type="text" value="<?php echo $output['store_name'];?>" name="store_name" id="store_name" class="txt"></td>
          <th><label for="owner_and_name">供应商账号</label></th>
          <td><input type="text" value="<?php echo $output['owner_and_name'];?>" name="owner_and_name" id="owner_and_name" class="txt"></td>
          
            <td><a href="javascript:document.formSearch.submit();" class="btn-search " title="<?php echo $lang['nc_query'];?>">&nbsp;</a>

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
            <li>点击审核按钮可以对开店申请进行审核，点击查看按钮可以查看开店信息</li>
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
          <th><?php echo $lang['store_name'];?></th>
          <th><?php echo $lang['store_user_name'];?></th>
          <th><?php echo $lang['location'];?></th>
          <!-- 所属等级 -->
<!--          <th class="align-center"><?php echo $lang['belongs_level'];?></th>-->
          <th class="align-center">城市公司</th>
          <th class="align-center"><?php echo $lang['operation'];?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['store_list']) && is_array($output['store_list'])){ ?>
        <?php foreach($output['store_list'] as $k => $v){ ?>
        <tr class="hover edit">
          <td><?php echo $v['company_name'];?></td>
          <td><?php echo $v['member_name'];?></td>
          <td class="w150"><?php echo $v['company_address'];?></td>
          <!-- 所属等级 -->
<!--          <td class="align-center"><?php echo $v['sg_name'];?></td>-->
          <td class="align-center"><?php echo $v['city_center_name'];?></td>
          <td class="w72 align-center">
              <a href="index.php?act=store_list&op=store_joinin_detail&member_id=<?php echo $v['member_id'];?>&city=<?php echo $v['city_center'];?>">查看</a>
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
              <?php if(!empty($output['store_list']) && is_array($output['store_list'])){ ?>
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
