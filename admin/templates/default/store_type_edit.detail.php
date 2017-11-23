<?php ?>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/styles/nyroModal.css" rel="stylesheet" type="text/css" id="cssfile2" />
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.viewer/viewer.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.viewer/viewer.min.js" charset="utf-8"></script>
<script type="text/javascript">

    $(document).ready(function(){
        $('a[nctype="nyroModal"]').nyroModal();

        var options = {url: 'src', title: false};
        $('img[nctype="viewer"]').viewer(options);

        $('#btn_pass').on('click', function() {

			if(confirm('确认提交？')) {
                $('#verify_type').val('pass');
                $('#form_store_verify').submit();
            }
        });
    });
</script>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['store'];?>店铺类型修改</h3>
    </div>
  </div>
  <div class="fixed-empty"></div>

  <form id="form_store_verify" action="index.php?act=store&op=store_type_verify" method="post" enctype="multipart/form-data" >
    <input id="verify_type" name="verify_type" type="hidden" />
    <?php if($_GET['is_rz'] == "1"){ ?>
    <input id="verify_type" name="pass_store" type="hidden" value="1"/>
    <?php }?>
    <input name="member_id" type="hidden" value="<?php echo $output['list']['member_id'];?>" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
      <thead>
        <tr>
          <th colspan="20">供应商信息</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150">供应商账号：</th>
          <td><?php echo $output['list']['member_name'];?></td>
        </tr>

        <tr>
            <th class="w150">店铺名称：</th>
            <td><?php echo $output['list']['store_name'];?></td>
        </tr>

        <tr>
            <th>经营类目：</th>
            <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" id="table_category" class="type">
                    <thead>
                    <tr>
                        <th>分类1</th>
                        <th>比例</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $store_class_names = unserialize($output['list']['store_class_names']);?>
                    <?php if(!empty($store_class_names) && is_array($store_class_names)) {?>
                        <?php $store_class_commis_rates = explode(',', $output['list']['store_class_commis_rates']);?>
                        <?php for($i=0, $length = count($store_class_names); $i < $length; $i++) {?>
                            <?php list($class1, $class2, $class3) = explode(',', $store_class_names[$i]);?>
                            <tr>
                                <td><input name="class_id" type="checkbox" value="" checked="checked" disabled="false"/>
                                    <?php echo $class1;?></td>
                                <td><?php echo $store_class_commis_rates[$i];?> %</td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    <?php if(!empty($output['class_list']) && is_array($output['class_list'])) {?>
                        <?php foreach ($output['class_list'] as $class){ ?>
                            <?php if($class['is_type'] == '2'){?>
                                <tr>
                                    <td><input name="class_id[]" type="checkbox" value="<?php echo $class['gc_id'];?>" /><?php echo $class['gc_name'];?></td>
                                    <td></td>
                                </tr>
                            <?php }?>
                        <?php } ?>
                    <?php }?>
                    </tbody>
                </table></td>
        </tr>
    </tbody>
    </table>
    <div id="validation_message" style="color:red;display:block;"></div>
    <div>
    <a id="btn_pass" class="btn" href="JavaScript:void(0);"><span>提交</span></a>
    </div>
  </form>

</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />