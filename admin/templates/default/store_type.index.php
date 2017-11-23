<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['store_type'];?></h3>
      <ul class="tab-base">
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="index.php?act=store_type&op=store_type_add" ><span><?php echo $lang['nc_new'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th colspan="12" class="nobg"><div class="title">
            <h5><?php echo $lang['nc_prompts'];?></h5>
            <span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td><ul>
            <li><?php echo $lang['store_type_help1'];?></li>
            <li><?php echo $lang['store_type_help2'];?></li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <form method='post'>
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2 nobdb">
      <thead>
        <tr class="thead">
          <th><input type="checkbox" class="checkall" id="checkall_1"></th>
          <th><?php echo $lang['nc_sort'];?></th>
          <th><?php echo $lang['store_type_name'];?></th>
          <th><?php echo $lang['nc_handle'];?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['class_list']) && is_array($output['class_list'])){ ?>
        <?php foreach($output['class_list'] as $k => $v){ ?>
        <tr class="hover edit">
          <td class="w36"><input type="checkbox" name='check_st_id[]' value="<?php echo $v['st_id'];?>" class="checkitem"></td>
          <td class="w48 sort"><span title="<?php echo $lang['can_edit'];?>" ajax_branch='store_type_sort' datatype="number" fieldid="<?php echo $v['st_id'];?>" fieldname="st_sort" nc_type="inline_edit" class="editable"><?php echo $v['st_sort'];?></span></td>
          <td class="name">
          	<span title="<?php echo $lang['store_type_name'];?>" required="1" fieldid="<?php echo $v['st_id'];?>" ajax_branch='store_type_name' fieldname="st_name" nc_type="inline_edit" class="node_name editable"><?php echo $v['st_name'];?></span>
          </td>
          <td class="w84"><span><a href="index.php?act=store_type&op=store_type_edit&st_id=<?php echo $v['st_id'];?>"><?php echo $lang['nc_edit'];?></a> | <a href="javascript:if(confirm('<?php echo $lang['del_store_type'];?>'))window.location = 'index.php?act=store_type&op=store_type_del&st_id=<?php echo $v['st_id'];?>';"><?php echo $lang['nc_del'];?></a> </span></td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr class="no_data">
          <td colspan="10"><?php echo $lang['nc_no_record'];?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <?php if(!empty($output['class_list']) && is_array($output['class_list'])){ ?>
        <tr id="batchAction" >
          <td><input type="checkbox" class="checkall" id="checkallBottom"></td>
          <td colspan="16" id="dataFuncs"><label for="checkallBottom"><?php echo $lang['nc_select_all']; ?></label>
            &nbsp;&nbsp;<a href="JavaScript:void(0);" class="btn" onclick="if(confirm('<?php echo $lang['del_store_type'];?>')){$('form:first').submit();}"><span><?php echo $lang['nc_del'];?></span></a>
            <div class="pagination"> <?php echo $output['page'];?> </div></td>
            </td>
        </tr>
        <?php } ?>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.edit.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.store_type.js" charset="utf-8"></script> 