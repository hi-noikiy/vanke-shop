<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php /*echo $lang['member_name'];*/?>基本信息申请</h3>
      <ul class="tab-base">
        
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" name="formSearch" id="formSearch">
  <input type="hidden" value="attribut" name="act">
  <input type="hidden" value="Index" name="op">
  <table class="tb-type1 noborder search">
  <tbody>
    <tr style="float: left;">
      <th><label for="member_name"><?php /*echo $lang['member_name'];*/?>申请人账号</label></th>
      <td><input type="text"  name="member_id" id="member_id" class="txt" value="<?php echo $output['member_id'];?>"  ></td>
      </tr>

    <tr style="float: left;margin-left: 20px;">
      <th><label for="att_content"><?php /*echo $lang['att_content'];*/?>申请内容</label></th>
      <td><input type="text" value="<?php echo $output['att_content'];?>" name="att_content" id="att_content" class="txt"></td>

    </tr>
    <tr style="float: left;margin-left: 20px;">
      <th><label for="member_name"><?php /*echo $lang['member_name'];*/?>申请状态</label></th>
      <td class="w100"><select name="att_state">
          <option value="" <?php echo $_GET['att_state']==''?'selected':''; ?>>请选择...</option>
          <option value="<?php echo ATTRIBUTE_STATE_SUBMIT;?>" <?php echo $_GET['att_state']==ATTRIBUTE_STATE_SUBMIT ?'selected':''; ?>>待审核</option>
          <option value="<?php echo ATTRIBUTE_STATE_PROCESSED;?>" <?php echo $_GET['att_state']==ATTRIBUTE_STATE_PROCESSED ?'selected':''; ?>>审核通过</option>
          <option value="<?php echo ATTRIBUTE_STATE_REFUSE;?>" <?php echo $_GET['att_state']==ATTRIBUTE_STATE_REFUSE ?'selected':''; ?>>审核拒绝</option>
        </select></td>
      <td><a href="javascript:void(0);" id="ncsubmit" class="btn-search " title="<?php echo $lang['nc_query'];?>">&nbsp;</a>
        <?php if($output['owner_and_name'] != '' or $output['att_content'] != '' or $output['grade_id'] != ''){?>
          <!--注释撤销检索，这个撤销会导致报错-->
<!--          <a href="index.php?act=store&op=attribut" class="btns " title="<?php echo $lang['nc_cancel_search'];?>"><span><?php echo $lang['nc_cancel_search'];?></span></a>-->
        <?php }?>
      </td>
    </tr>

  </tbody>
  </table>
  </form>
<!--   <table class="table tb-type2" id="prompt">-->
<!--    <tbody>-->
<!--      <tr class="space odd">-->
<!--        <th colspan="12"><div class="title">-->
<!--            <h5>--><?php //echo $lang['nc_prompts'];?><!--</h5>-->
<!--            <span class="arrow"></span></div></th>-->
<!--      </tr>-->
<!--      <tr>-->
<!--        <td><ul>-->
<!--            <li>--><?php //echo $lang['store_help1'];?><!--</li>-->
<!--          </ul></td>-->
<!--      </tr>-->
<!--    </tbody>-->
<!--  </table>-->
  <form method="post" id="store_form">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <thead>
        <tr class="thead">
          <th>申请人账号</th>
          <th>申请人名称</th>
          <th class="align-center">申请类型</th>
          <th class="align-center">申请标题</th>
          <th class="align-center">申请内容</th>
          <th class="align-center">申请时间</th>
          <th class="align-center">申请状态</th>
          <th class="align-center">操作</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['att_list']) && is_array($output['att_list'])){ ?>
        <?php foreach($output['att_list'] as $k => $v){ ?>
        <tr class="hover edit <?php echo getStoreStateClassName($v);?>">
          <td>
              <?php echo  $v['member_id'] ? $v['member_id'] : $v['member_id'];?>
          </td>
          <td>
          <?php if($v['att_from'] == 0){?>
            <?php echo $v['store_name'];?>
          <?php }else{?>
               <?php echo $v['member_name'];?>
          <?php }?>
          </td>
          <td class="align-center"><?php echo $v['att_type'];?></td>
          <td class="align-center"><?php echo $v['att_title'];?></td>
          <td class="align-center"><?php echo $v['att_content'];?></td>
          <td class="align-center"><?php echo $v['att_date'];?></td>
          <td class="align-center">
              <?php if($v['att_state'] == 1){echo "待审核";}else if($v['att_state'] == 2){echo "申请通过";}else{echo '申请拒绝';}?>
          </td>
          <td class="align-center">
               <?php if($v['att_state'] == 1){?>
              <a href="/admin/index.php?act=attribut&op=update&type=1&id=<?php echo $v['id']?>">申请通过</a>
              <a href="/admin/index.php?act=attribut&op=update&type=2&id=<?php echo $v['id']?>">审核拒绝</a>
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
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.edit.js" charset="utf-8"></script>
<script>
$(function(){
    $('#ncsubmit').click(function(){
    	$('input[name="op"]').val('attribut');$('#formSearch').submit();
    });
});
</script>
