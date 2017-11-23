<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
     <h3><?php echo $lang['goods_class_index_class'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=assessment&op=store"><span>管理</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" id="store_form">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <thead>
        <tr class="thead">
          <th>供应商卖家账号</th>
          <th>公司名称</th>
          <th class="align-center">评估时间</th>
          <th class="align-center">操作</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['list']) && is_array($output['list'])){ ?>
        <?php foreach($output['list'] as $k => $v){ ?>
        <tr class="hover edit">
          <td>
              <a href="index.php?act=assessment&op=looked&member_id=<?php echo $v['member_id'];?>&time=<?php echo $v['addtime'];?>" >
                <?php echo $v['store_name'];?>
            </a>
          </td>
          <td><?php echo $v['store_company_name'];?></td>
          <td><?php echo date('Y-m-d H:i:s',$v['addtime']);;?></td>
        <td class="align-center w200">
             <a href="index.php?act=assessment&op=looked&member_id=<?php echo $v['member_id'];?>&time=<?php echo $v['addtime'];?>">查看评估</a>   
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
    	$('input[name="op"]').val('store');$('#formSearch').submit();
    });
});
</script>
