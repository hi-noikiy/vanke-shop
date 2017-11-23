<?php ?>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/styles/nyroModal.css" rel="stylesheet" type="text/css" id="cssfile2" />
<script type="text/javascript">
    $(document).ready(function(){
        $('a[nctype="nyroModal"]').nyroModal();

        $('#btn_fail').on('click', function() {
            if($('#joinin_message').val() == '') {
                $('#validation_message').text('请输入审核意见');
                $('#validation_message').show();
                return false;
            } else {
                $('#validation_message').hide();
            }
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
      <h3><?php echo $lang['store'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=agent&op=agent"><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="index.php?act=agent&op=agent_joinin" ><span><?php echo $lang['pending'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $output['joinin_detail_title'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">联系人信息</th>
      </tr>
    </thead>
    <tbody>

    <tr>
        <th class="w150">联系人姓名：</th>
        <td colspan="20"><?php echo $output['joinin_detail']['contacts_name'];?></td>
    </tr>
    <tr>
        <th class="w150">联系人电话：</th>
        <td colspan="20"><?php echo $output['joinin_detail']['contacts_phone'];?></td>
    </tr>
      <tr>
          <th class="w150">所在地：</th>
          <td colspan="20"><?php echo $output['joinin_detail']['company_address'];?></td>
      </tr>
      <tr>
          <th class="w150">详细地址：</th>
          <td colspan="20"><?php echo $output['joinin_detail']['company_address_detail'];?></td>
      </tr>

      <tr>
          <th class="w150">电子邮箱：</th>
          <td colspan="20"><?php echo $output['joinin_detail']['contacts_email'];?></td>
      </tr>
    </tbody>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">身份信息（副本）</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">身份证号：</th>
        <td><?php echo $output['joinin_detail']['business_licence_number'];?></td></tr>
      <tr>
        <th>身份信息<br />
电子版：</th>
        <td colspan="20"><a nctype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['business_licence_number_electronic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['business_licence_number_electronic']);?>" alt="" /> </a></td>
      </tr>
    </tbody>
  </table>


  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">结算账号信息：</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">银行开户名：</th>
        <td><?php echo $output['joinin_detail']['settlement_bank_account_name'];?></td>
      </tr>
      <tr>
        <th>支付宝账号：</th>
        <td><?php echo $output['joinin_detail']['settlement_bank_account_number'];?></td>
      </tr>
    </tbody>
  </table>

  <form id="form_store_verify" action="index.php?act=agent&op=agent_joinin_verify" method="post">
    <input id="verify_type" name="verify_type" type="hidden" />
    <input name="member_id" type="hidden" value="<?php echo $output['joinin_detail']['member_id'];?>" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
      <thead>
        <tr>
          <th colspan="20">代理信息</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150">代理账号：</th>
          <td><?php echo $output['joinin_detail']['seller_name'];?></td>
        </tr>

        <tr>
          <th>代理等级：</th>
          <td><?php echo $output['joinin_detail']['sg_name'];?>（开店费用：<?php echo $output['joinin_detail']['sg_price'];?> 元/年）</td>
        </tr>
        <tr>
          <th class="w150">时长：</th>
          <td><?php echo $output['joinin_detail']['joinin_year'];?> 年</td>
        </tr>
        <tr>
          <th>应付总金额：</th>
          <td>
          <?php if(intval($output['joinin_detail']['joinin_state']) === 10) {?>
          <input type="text" value="<?php echo $output['joinin_detail']['paying_amount'];?>" name="paying_amount" /> 元
          <?php } else { ?>
          <?php echo $output['joinin_detail']['paying_amount'];?> 元
          <?php } ?>
          </td>
        </tr>

    <?php if(in_array(intval($output['joinin_detail']['joinin_state']), array(STORE_JOIN_STATE_PAY, STORE_JOIN_STATE_FINAL))) {?>
    <tr>
        <th>付款凭证：</th>
        <td><a nctype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['paying_money_certificate']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['paying_money_certificate']);?>" alt="" /> </a></td>
    </tr>
    <tr>
        <th>付款凭证说明：</th>
        <td><?php echo $output['joinin_detail']['paying_money_certificate_explain'];?></td>
    </tr>
    <?php } ?>
   <?php if(in_array(intval($output['joinin_detail']['joinin_state']), array(STORE_JOIN_STATE_NEW, STORE_JOIN_STATE_PAY))) { ?>
    <tr>
        <th>审核意见：</th>
        <td colspan="2"><textarea id="joinin_message" name="joinin_message"></textarea></td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
   <?php if(in_array(intval($output['joinin_detail']['joinin_state']), array(STORE_JOIN_STATE_NEW, STORE_JOIN_STATE_PAY))) { ?>
    <div id="validation_message" style="color:red;display:none;"></div>
    <div><a id="btn_fail" class="btn" href="JavaScript:void(0);"><span>拒绝</span></a> <a id="btn_pass" class="btn" href="JavaScript:void(0);"><span>通过</span></a></div>
    <?php } ?>
  </form>
</div>
