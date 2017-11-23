<?php ?>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/styles/nyroModal.css" rel="stylesheet" type="text/css" id="cssfile2" />
<script type="text/javascript">
    $(document).ready(function(){
        
        $('#btn_fail').on('click', function() {
            if(confirm('确认拒绝申请？')) {
                $('#verify_type').val('fail');
                $('#form_store_verify').submit();
            }
        });
        
        $('#btn_pass').on('click', function() {
            var valid = true;
            $('[nctype="commis_rate"]').each(function(commis_rate) {
                rate = $(this).val();
                if(rate == '') {
                    valid = false;
                    return false;
                }

                var rate = Number($(this).val());
                if(isNaN(rate) || rate < 0 || rate >= 100) {
                    valid = false;
                    return false;
                }
            });
            if(valid) {
                $('#validation_message').hide();
                if(confirm('确认通过申请？')) {
                    $('#verify_type').val('pass');
                    $('#form_store_verify').submit();
                }
            } else {
                $('#validation_message').text('请正确填写分佣比例');
                $('#validation_message').show();
            }
        });
    });
</script>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>其他物业采购员认证</h3>
      <ul class="tab-base">
        <li><a href="index.php?act=third_member&op=index" ><span>列表</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">基本信息</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">公司名称：</th>
        <td colspan="20"><?php echo $output['news']['company_name'];?></td>
      </tr>
      <tr>
        <th>姓名：</th>
        <td><?php echo $output['news']['name'];?></td>
        <th>项目（小区）名称：</th>
        <td colspan="20"><?php echo $output['news']['product_name'];?></td>
      </tr>
      <tr>
        <th>职务：</th>
        <td><?php echo $output['news']['job_name'];?></td>
        <th>会员名称：</th>
        <td><?php echo $output['news']['member_name'];?></td>
      </tr>
      
      <tr>
        <th>营业执照<br />电子版：</th>
        <td colspan="20"><?php if($output['news']['business_licence_number_electronic']){?><a target="_bank" nctype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['news']['business_licence_number_electronic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['news']['business_licence_number_electronic']);?>" alt="" /> </a><?php }?></td>
      </tr>
    </tbody>
  </table>


  <form id="form_store_verify" action="index.php?act=third_member&op=save" method="post" enctype="multipart/form-data" >
      <input id="verify_type" name="verify_type" type="hidden"  />
    <input name="member_id" type="hidden" value="<?php echo $output['news']['member_id'];?>" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
      <thead>
        <tr>
          <th colspan="20">认证信息</th>
        </tr>
      </thead>
      <tbody>

        <tr>
            <th>城市公司：</th>
            <td style="color: red;"><?php echo $output['city']['city_name'];?></td>
        </tr>
        <tr>
            <th>审核状态：</th>
            <td style="color: red;"><?php if($output['news']['rz_status'] == 1){echo '认证中';}else if($output['news']['rz_status'] == 2){echo '认证成功';}else{echo '认证拒绝';}?></td>
        </tr>
    </tbody>
    </table>
    <?php if($output['news']['rz_status'] == 1){?>
  <div id="validation_message" style="color:red;display:none;"></div>
    <div>
            <a id="btn_fail" class="btn" href="JavaScript:void(0);"><span>拒绝</span></a> 
            <a id="btn_pass" class="btn" href="JavaScript:void(0);"><span>通过</span></a>
    </div>
    <?php }?>
  </form>

</div>
