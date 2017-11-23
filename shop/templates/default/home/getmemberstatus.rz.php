<?php ?>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<div class="breadcrumb"><span class="icon-home"></span><span><a href="<?php echo SHOP_SITE_URL;?>">首页</a>
    </span>
    <span class="arrow">
        ></span> <span>物业采购员认证申请</span> </div>
<div class="main">
  <div class="sidebar">
    <div class="title">
      <h3>物业采购认证申请</h3>
    </div>
    <div class="content">
      <dl>
        <dt class="<?php echo $output['step'] == '1' ? 'current' : '';?>"> <i class="hide"></i>物业认证</dt>
      </dl>
<!--      <dl>
        <dt> <i class="hide"></i>店铺开通</dt>
      </dl>-->
    </div>
    <div class="title">
      <h3>平台联系方式</h3>
    </div>
    <div class="content">
      <ul>
        <?php
			if(is_array($output['phone_array']) && !empty($output['phone_array'])) {
				foreach($output['phone_array'] as $key => $val) {
			?>
        <li><?php echo '电话'.($key+1).$lang['nc_colon'];?><?php echo $val;?></li>
        <?php
				}
			}
			 ?>
        <li><?php echo '邮箱'.$lang['nc_colon'];?><?php echo C('site_email');?></li>
      </ul>
    </div>
  </div>
  <div class="right-layout">
    <div class="joinin-concrete">
      <?php require('get_memberstatus.'.$output['sub_step'].'.php'); ?>
    </div>
  </div>
</div>
