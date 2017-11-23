<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['agent_grade'];?></h3>
      <ul class="tab-base">
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="index.php?act=agent_grade&op=agent_grade_add" ><span><?php echo $lang['nc_new'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>

  <table class="table tb-type2" id="prompt">
    <tbody>
    <tr class="space odd">
      <th colspan="12"><div class="title">
          <h5><?php echo $lang['nc_prompts'];?></h5>
          <span class="arrow"></span></div></th>
    </tr>
    <tr>
      <td><ul>
          <li>删除代理等级后，对应的用户等级会自动改为默认</li>
        </ul></td>
    </tr>
    </tbody>
  </table>
  <form id="form_grade" method='post' name="">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <thead>
        <tr class="thead">
          <th class="w24">&nbsp;</th>
          <th><?php echo $lang['grade_sortname']; ?></th>
          <th><?php echo $lang['agent_grade_name'];?></th>
          <th class="align-center">佣金比例</th>
          <th class="align-center">加盟费用</th>
          <th><?php echo $lang['nc_handle'];?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['grade_list']) && is_array($output['grade_list'])){ ?>
        <?php foreach($output['grade_list'] as $k => $v){ ?>
        <tr class="hover">
          <td><?php if($v['sg_id'] > 1){ ?>
            <input type="checkbox" name='check_sg_id[]' value="<?php echo $v['sg_id'];?>" class="checkitem">
            <?php } ?></td>
          <td class="w36"><?php echo $v['sg_sort'];?></td>
          <td><?php echo $v['sg_name'];?></td>
          <td class="align-center"><?php echo $v['ag_rate'];?></td>
          <td class="align-center"><?php echo $v['sg_price'];?> </td>
          <td class="w270"><a href="index.php?act=agent_grade&op=agent_grade_edit&sg_id=<?php echo $v['sg_id'];?>"><?php echo $lang['nc_edit'];?></a> |
            <?php if($v['sg_id'] == '1'){ ?>
            <?php echo $lang['default_agent_grade_no_del'];?>
            <?php }else { ?>
            <a href="javascript:if(confirm('<?php echo $lang['problem_del'];?>'))window.location = 'index.php?act=agent_grade&op=agent_grade_del&sg_id=<?php echo $v['sg_id'];?>';"><?php echo $lang['nc_del'];?></a>
            <?php } ?>
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
          <td><input type="checkbox" class="checkall" id="checkallBottom"></td>
          <td colspan="15"><label for="checkallBottom"><?php echo $lang['nc_select_all']; ?></label>
            &nbsp;&nbsp;<a href="JavaScript:void(0);" class="btn" onclick="if(confirm('<?php echo $lang['problem_del'];?>')){$('#form_grade').submit();}"><span><?php echo $lang['nc_del'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
