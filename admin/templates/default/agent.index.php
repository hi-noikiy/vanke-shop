<?php ?>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['store'];?></h3>
      <ul class="tab-base">
        <li><a href="JavaScript:void(0);"  class="current"><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="index.php?act=agent&op=agent_joinin"><span><?php echo $lang['pending'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" name="formSearch">
    <input type="hidden" value="agent" name="act">
    <input type="hidden" value="agent" name="op">
    <table class="tb-type1 noborder search">
      <tbody>
        <tr>
          <th><label for="agent_id">代理账号</label></th>
          <td><input type="text" value="<?php echo $_GET['agent_id']; ?>" name="agent_id" id="agent_id" class="txt"></td>
          <th><label for="agent_name">登录账号</label></th>
          <td><input type="text" value="<?php echo $_GET['agent_name']; ?>" name="agent_name" id="agent_name" class="txt"></td>
          <th><label><?php echo $lang['belongs_level'];?></label></th>
          <td><select name="grade_id">
              <option value=""><?php echo $lang['nc_please_choose'];?>...</option>
              <?php if(!empty($output['grade_list']) && is_array($output['grade_list'])){ ?>
              <?php foreach($output['grade_list'] as $k => $v){ ?>
              <option value="<?php echo $v['sg_id'];?>" <?php if($v['sg_id']==$_GET['grade_id']) { echo 'selected'; }?>><?php echo $v['sg_name'];?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
            <td><a href="javascript:document.formSearch.submit();" class="btn-search " title="<?php echo $lang['nc_query'];?>">&nbsp;</a>
                <?php if($output['agent_id'] != '' or $output['agent_name'] != '' or $output['grade_id'] != ''){?>
                <a href="index.php?act=agent&op=agent" class="btns " title="<?php echo $lang['nc_cancel_search'];?>"><span><?php echo $lang['nc_cancel_search'];?></span></a>
                <?php }?></td>
        </tr>
        </tbody>
    </table>
</form>
<!--<table class="table tb-type2" id="prompt">-->
<!--    <tbody>-->
<!--      <tr class="space odd">-->
<!--        <th colspan="12"><div class="title">-->
<!--            <h5>--><?php //echo $lang['nc_prompts'];?><!--</h5>-->
<!--            <span class="arrow"></span></div></th>-->
<!--      </tr>-->
<!--      <tr>-->
<!--        <td><ul>-->
<!--            <li>点击审核按钮可以对开店申请进行审核，点击查看按钮可以查看开店信息</li>-->
<!--          </ul></td>-->
<!--      </tr>-->
<!--    </tbody>-->
<!--  </table>-->
  <form method="post" id="store_form" name="store_form">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="type" id="type" value="" />
    <table class="table tb-type2">
      <thead>
        <tr class="thead">
          <th>代理编号</th>
          <th>代理账号</th>
          <th><?php echo $lang['location'];?></th>
          <th class="align-center"><?php echo $lang['belongs_level'];?></th>
          <th class="align-center"><?php echo $lang['operation'];?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['agent_list']) && is_array($output['agent_list'])){ ?>
        <?php foreach($output['agent_list'] as $k => $v){ ?>
        <tr class="hover edit">
          <td><?php echo $v['seller_id'];?></td>
          <td><?php echo $v['seller_name'];?></td>
          <td class="w150"><?php echo $v['company_address'];?></td>
          <td class="align-center"><?php echo $v['sg_name'];?></td>
          <td class="w72 align-center"><a href="index.php?act=agent&op=agent_joinin_detail&member_id=<?php echo $v['member_id'];?>">注册资料</a></td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr class="no_data">
          <td colspan="5"><?php echo $lang['nc_no_record'];?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td></td>
          <td colspan="5">
              <?php if(!empty($output['agent_list']) && is_array($output['agent_list'])){ ?>
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