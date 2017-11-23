<?php ?>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <a href="javascript:void(0)" class="ncsc-btn ncsc-btn-green" nc_type="dialog" dialog_title="基本信息申请" dialog_id="my_goods_brand_apply" dialog_width="480" uri="index.php?act=attribute_add&op=brand_add">基本信息申请</a></div>
<table class="search-form">

</table>
<table class="ncsc-default-table">
  <thead>
    <tr>
      <th class="w150">申请类型</th>
      <th class="w150">申请标题</th>
      <th>申请内容</th>
      <th>申请时间</th>
      <th class="w100">申请状态</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($output['brand_list'])) { ?>
    <?php foreach($output['brand_list'] as $val) { ?>
    <tr class="bd-line">
      <td><?php echo $val['att_type']?></td>
      <td><?php echo $val['att_title']?></td>
      <td><?php echo $val['att_content'] ?></td>
      <td><?php echo $val['att_date'] ?></td>
      <td> <?php if($val['att_state'] == '2'){echo "申请通过";}else if($val['att_state'] == '1'){echo "待审核";}else{echo "审核失败";}?></td> </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <?php if (!empty($output['brand_list'])) { ?>
    <tr>
      <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
