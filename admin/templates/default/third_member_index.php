<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>其他物业认证</h3>
      <ul class="tab-base">
          <li><a class="<?php if($_GET['states'] != 'state'){echo 'current';}?>" href="index.php?act=third_member&op=index" ><span>列表</span></a></li>
        <li><a class="<?php if($_GET['states'] == 'state'){echo 'current';}?>" href="index.php?act=third_member&op=index&states=state" ><span>待审核</span></a></li>
      </ul>
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
          <th><?php echo $lang['member_name'];?></th>
          <th class="align-center"><?php echo $lang['third_member_status'];?></th>
          <th class="align-center">操作</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['member_list']) && is_array($output['member_list'])){ ?>
        <?php foreach($output['member_list'] as $k => $v){ ?>
        <tr class="hover">
            <td><?php echo $v['id'];?></td>
          <td><?php echo $v['member_name'];?></td>
          <td class="align-center"><?php if($v['rz_status'] == 1){echo '新申请';}else if($v['rz_status'] == 2){echo '认证通过';}else{echo '认证失败';}?></td>
          <td class="w150 align-center">
              <?php if($v['rz_status'] == 1){ ?>
                  <a href="<?php echo ADMIN_SITE_URL;?>/index.php?act=third_member&op=detail&id=<?php echo $v['id'];?>" >
                      
                  审核
              </a>
            <?php  }else{?>
                <a href="<?php echo ADMIN_SITE_URL;?>/index.php?act=third_member&op=detail&id=<?php echo $v['id'];?>" >
                      查看
              </a>
            <?php }?>
              
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
          <td colspan="16">
             <div class="pagination"> <?php echo $output['page'];?> </div></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
