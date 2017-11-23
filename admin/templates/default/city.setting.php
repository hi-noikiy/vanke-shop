<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['city_setting'];?></h3>
      <h3><a href="index.php?act=city_manges&op=add">新增城市中心</a></h3>
      <?php echo $output['top_link'];?>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" id='form_admin'>
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <thead>
        <tr class="space">
          <th colspan="15" class="nobg"><?php echo $lang['nc_list'];?></th>
        </tr>
        <tr class="thead">
            <th><?php echo $lang['city_id'];?></th>
          <th><?php echo $lang['city_name'];?></th>
          <th class="align-center"><?php echo $lang['city_state'];?></th>
          <th class="align-center"><?php echo $lang['city_bukrs'];?></th>
          <th class="align-center"><?php echo $lang['city_comtxt'];?></th>
          <th class="align-center">操作</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['city']) && is_array($output['city'])){ ?>
        <?php foreach($output['city'] as $k => $v){ ?>
        <tr class="hover">
            <td><?php echo $v['id'];?></td>
          <td><?php echo $v['city_name'];?></td>
          <td class="align-center"><?php echo $v['city_state'] ? '正常' : '关闭'; ?></td>
          <td class="align-center"><?php echo $v['bukrs']; ?></td>
          <td class="w150 align-center"><?php echo $v['comtxt']; ?></td>
          <td class="w150 align-center">
              <a href="<?php echo ADMIN_SITE_URL;?>/index.php?act=city_manges&op=edit&id=<?php echo $v['id'];?>" >
                  <?php echo $lang['city_edit'];?>
              </a>
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
        <?php if(!empty($output['city']) && is_array($output['city'])){ ?>
        <tr class="tfoot">
          <td colspan="16">
             <div class="pagination"> <?php echo $output['page'];?> </div></td>
        </tr>
        <?php } ?>
      </tfoot>
    </table>
  </form>
</div>
