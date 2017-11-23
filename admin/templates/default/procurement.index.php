<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['procurement_index_purchase_release'];?></h3>
      <ul class="tab-base">
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['nc_manage'];?></span></a></li>
        <li><a href="index.php?act=procurement&op=add" class="current"><span><?php echo $lang['procurement_index_add'] ;?></span></a></li>
        
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
    <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th class="nobg" colspan="12"><div class="title"><h5><?php echo $lang['nc_prompts'];?></h5><span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td>
        <ul>
            <li><?php echo $lang['procurement_index_help1'];?></li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <table class="table tb-type2 nobdb">
    <thead>
      <tr class="thead">
        <th><?php echo $lang['procurement_index_title'];?></th>
        <th>发布部门</th>
        <th class="align-center"><?php echo $lang['procurement_edit_time'];?></th>
        <th><?php echo $lang['nc_handle'];?></th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($output['pur_list']) && is_array($output['pur_list'])){ ?>
      <?php foreach($output['pur_list'] as $k => $v){ ?>
      <tr class="hover">
        <td><?php echo $v['title']; ?></td>
        <td><?php echo $v['publish_department']; ?></td>
        <td class="w25pre align-center"><?php echo date('Y-m-d H:i:s',$v['publish_date']); ?></td>
        <td class="w96"><a href="index.php?act=procurement&op=edit&purchase_rule_id=<?php echo $v['purchase_rule_id']; ?>"><?php echo $lang['nc_edit'];?></a>
           <?php if(!empty($output['pur_list'][$k]['attachment'])){ ?>
                        <a href="index.php?act=procurement&op=download&purchase_rule_id=<?php echo $v['purchase_rule_id']; ?>"><?php echo "下载";?></a>
           <?php } ?>
        </td>
      <?php } ?>
      <?php }else { ?>
      <tr class="no_data">
        <td colspan="15"><?php echo $lang['nc_no_record'];?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
