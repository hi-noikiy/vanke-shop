<?php ?>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/styles/nyroModal.css" rel="stylesheet" type="text/css" id="cssfile2" />
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.viewer/viewer.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.viewer/viewer.min.js" charset="utf-8"></script>

<div class="page">
    <div class="fixed-bar">
        <?php include template('supplier/top');?>
    </div>
  <div class="fixed-empty"></div>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
      <thead>
          <tr>
              <th colspan="20">公司及联系人信息</th>
          </tr>
      </thead>
      <tbody>
      <tr>
          <th class="w150">公司名称：</th>
          <td colspan="20"><?php echo $output['list']['company_name'];?></td>
      </tr>
      <tr>
          <th>公司法人：</th>
          <td><?php echo $output['list']['legal_person'];?></td>
          <th>公司所在地：</th>
          <td colspan="20"><?php echo $output['list']['company_address'];?></td>
      </tr>

        <tr>
            <th class="w150">公司详细地址：</th>
            <td colspan="20"><?php echo $output['list']['company_address_detail'];?></td>
        </tr>
      <tr>
        <th>公司电话：</th>
        <td><?php echo $output['list']['company_phone'];?></td>
        <th>员工总数：</th>
        <td><?php echo $output['list']['company_employee_count'];?>&nbsp;人</td>
        <th>注册资金：</th>
        <td><?php echo $output['list']['company_registered_capital'];?>&nbsp;万元 </td>
      </tr>
      <tr>
        <th>联系人姓名：</th>
        <td><?php echo $output['list']['contacts_name'];?></td>
        <th>联系人电话：</th>
        <td><?php echo $output['list']['contacts_phone'];?></td>
        <th>电子邮箱：</th>
        <td><?php echo $output['list']['contacts_email'];?></td>
      </tr>
    </tbody>
  </table>

    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
        <thead>
        <tr>
            <th colspan="20">所属城市公司联系人信息</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>联系人姓名：</th>
            <td><?php echo $output['list']['city_contacts_name'];?></td>
            <th>联系人电话：</th>
            <td colspan="20"><?php echo $output['list']['city_contacts_phone'];?></td>
        </tr>
        </tbody>
    </table>

  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">营业执照信息（副本）</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">营业执照号：</th>
        <td><?php echo $output['list']['business_licence_number'];?></td></tr><tr>
      
        <th>营业执照所在地：</th>
        <td><?php echo $output['list']['business_licence_address'];?></td></tr><tr>
      
        <th>营业执照有效期：</th>
        <td><?php echo $output['list']['business_licence_start'];?> 至 <?php echo $output['list']['business_licence_end'];?></td>
      </tr>
      <tr>
        <th>法定经营范围：</th>
        <td colspan="20"><?php echo $output['list']['business_sphere'];?></td>
      </tr>
      <tr>
        <th>营业执照<br />电子版：</th>
        <td colspan="20"><?php if($output['list']['business_licence_number_electronic']){?><img nctype="viewer" src="<?php echo getStoreJoininImageUrl($output['list']['business_licence_number_electronic']);?>" alt="" /><?php }?></td>
      </tr>
      <tr>
          <th>是否一般纳税人：</th>
          <td colspan="20"><?php echo $output['list']['is_taxpayer'] == '1' ? "是":"否";?></td>
      </tr>
      <tr>
          <th>是否三证合一：</th>
          <td colspan="20"><?php echo $output['list']['is_therea'] == '1' ? "是":"否";?></td>
      </tr>
    </tbody>
  </table>

    <?php if($output['list']['is_therea'] == '2'){?>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">组织机构代码证</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>组织机构代码：</th>
        <td colspan="20"><?php echo $output['list']['organization_code'];?></td>
      </tr>
      <tr>
        <th>组织机构代码证<br/>          电子版：</th>
        <td colspan="20"><?php if($output['list']['organization_code_electronic']){?><a nctype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['list']['organization_code_electronic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['list']['organization_code_electronic']);?>" alt="" /> </a><?php }?></td>
      </tr>
    </tbody>
  </table>

        <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
            <thead>
            <tr>
                <th colspan="20">税务登记证</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th class="w150">税务登记证号：</th>
                <td><?php echo $output['list']['tax_registration_certificate'];?></td>
            </tr>
            <tr>
                <th>纳税人识别号：</th>
                <td><?php echo $output['list']['taxpayer_id'];?></td>
            </tr>
            <tr>
                <th>税务登记证号<br />
                    电子版：</th>
                <td><?php if($output['list']['tax_registration_certificate_electronic']){?><a nctype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['list']['tax_registration_certificate_electronic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['list']['tax_registration_certificate_electronic']);?>" alt="" /> </a><?php }?></td>
            </tr>
            </tbody>
        </table>
    <?php }?>

  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">开户银行信息：</th>
      </tr>
    </thead>
    <tbody>
    <?php $account_data = unserialize($output['list']['account']);?>
      <tr>
        <th class="w150">银行开户名：</th>
        <td><?php echo $account_data['account_name'];?></td>
      </tr><tr>
        <th>公司银行账号：</th>
        <td><?php echo $account_data['account_number'];?></td></tr>
    <tr>
        <th>开户银行名称：</th>
        <td><?php echo $account_data['bank_name'];?></td>
    </tr>
      <tr>
        <th>开户银行支行名称：</th>
        <td><?php echo $account_data['bank_branch_name'];?></td>
      </tr>
      <tr>
        <th>支行联行号：</th>
        <td><?php echo $account_data['bank_branch_code'];?></td>
      </tr><tr>
        <th>开户银行所在地：</th>
        <td colspan="20"><?php echo $account_data['bank_address'];?></td>
      </tr>
      <tr>
        <th>开户银行许可证<br/>电子版：</th>
        <td colspan="20"><?php if($account_data['bank_licence_electronic']){?><img nctype="viewer" src="<?php echo getStoreJoininImageUrl($account_data['bank_licence_electronic']);?>" alt="" /><?php }?></td>
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
    <?php $settlement_data = unserialize($output['list']['settlement']);?>
      <tr>
        <th class="w150">银行开户名：</th>
        <td><?php echo $settlement_data['settlement_name'];?></td>
      </tr>
      <tr>
        <th>公司银行账号：</th>
          <td><?php echo $settlement_data['settlement_number'];?></td>
      </tr>
    <tr>
        <th>开户银行名称：</th>
        <td><?php echo $settlement_data['bank_name'];?></td>
    </tr>
      <tr>
        <th>开户银行支行名称：</th>
          <td><?php echo $settlement_data['bank_branch_name'];?></td>
      </tr>
      <tr>
        <th>支行联行号：</th>
          <td><?php echo $settlement_data['bank_branch_code'];?></td>
      </tr>
      <tr>
        <th>开户银行所在地：</th>
          <td><?php echo $settlement_data['bank_address'];?></td>
      </tr>
    </tbody>
    
  </table>


    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
        <thead>
        <tr>
            <th colspan="20">供应商信息：</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th class="w150">供应商类型：</th>
            <td>
                <?php if(!empty($output['supplier_type_data']) && is_array($output['supplier_type_data'])){?>
                    <style>
                        #supplier_type_data,.supplier_list{
                            border-collapse: collapse;
                            border: 0px solid #ccc;
                        }
                        #supplier_type_data .type_td {
                            border-top: 0;
                            border-right: 1px solid #ccc;
                            border-bottom: 1px solid #ccc;
                            border-left: 0;
                        }
                        #supplier_type_data tr.lastrow td {
                            border-bottom: 0;
                        }
                        #supplier_type_data tr td.lastCol {
                            border-right: 0;
                        }
                        #supplier_type_data .supplier_list label{
                            margin-left: 20px;
                            cursor:default;
                        }
                    </style>
                    <table id="supplier_type_data" style="width:100%">
                        <?php foreach ($output['supplier_type_data'] as $key=>$type_val){?>
                            <tr>
                                <td class="type_td lastCol">
                                    <label><b><?php echo $type_val['name']?></b></label>
                                </td>
                            </tr>
                            <tr <?php if(end($output['supplier_type_data']) == $type_val){?>class="lastrow"<?php }?>>
                                <td class="type_td lastCol"><table class="supplier_list">
                                        <?php if(is_array($type_val['son'])){foreach ($type_val['son'] as $key_a=>$type_val_a){?>
                                            <?php if($key_a%4 == 0){?><tr><?php }?>
                                            <td <?php if($key_a%4 == 3){?>class="lastCol"<?php }?> style="border-top: 0;">
                                                <label><?php echo $type_val_a['son_name'];?></label>
                                            </td>
                                            <?php if($key_a%4 == 3){?></tr><?php }?>
                                        <?php }}?>
                                    </table></td>
                            </tr>
                        <?php }?>
                    </table>
                <?php }else{?>
                    尚未选择
                <?php }?>
            </td>
        </tr>
        <tr>
            <th>供应商级别：</th>
            <td><?php echo empty($output['supplier_level']) ? "尚未选择":$output['supplier_level'];?></td>
        </tr>
        <tr>
            <th>供应商到期时间：</th>
            <td>
                <?php echo date("Y/m/d",$output['supplier_time_data']['member_time']);?>(开始)<label style="margin-left: 10px;margin-right:5px;">~</label>
                <?php if(empty($output['supplier_time_data']['supply_end_time'])){
                    echo date("Y/m/d",($output['supplier_time_data']['member_time']+(SUPPLY_TIME_LONG * 24 * 3600)));
                }else{ echo date("Y/m/d",$output['supplier_time_data']['supply_end_time']);}?>
                (到期)
            </td>
        </tr>
        <tr>
            <th>认证城市公司：</th>
            <td><?php echo $output['list']['city_name'];?></td>
        </tr>
        </tbody>
    </table>
    <form id="form_store_verify" action="index.php?act=supplier&op=store_verify" method="post" enctype="multipart/form-data" >
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
        <thead>
        <tr>
            <th colspan="20">审核信息：</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th><span style="color:red;">* </span>认证审核意见：</th>
            <td colspan="2"><textarea id="joinin_message" class='rz_joinin_message' name="joinin_message"></textarea></td>
        </tr>
        <tr>
            <th><span style="color:red;">* </span>认证审核评估：</th>
            <td><input name="rz_evaluation_audit" type="file" class="w200" />
                <span class="block">大小请控制在2M之内。</span></td>
        </tr>
        </tbody>
        <input name="member_id" type="hidden" value="<?php echo $output['list']['member_id'];?>" />
        <input name="city_id" type="hidden" value="<?php echo $output['list']['city_center'];?>" />
        <input name="verify_type" id="verify_type" type="hidden" value="" />
    </table>
        <div id="validation_message" style="color:red;display:block;"></div>
    </form>

    <div>
        <a id="btn_no" class="btn" href="JavaScript:void(0);"><span>拒绝</span></a>
        <a id="btn_pass" class="btn" href="JavaScript:void(0);"><span>通过</span></a>
    </div>


</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript">
$(function () {

	 $('#supply_end_time').datepicker({dateFormat: 'yy-mm-dd',minDate: new Date()});
	 
	 $("#supplier_type tr td input[data-type='supplier_type']").bind("click", function () {
		 var val = $(this).val();
		 if ($(this).is(":checked")) {
			 $("#supplier_list_"+val).show();
         }else{
        	 $("#supplier_list_"+val).hide();
        	 $("#supplier_list_"+val+" td table tr input[data-type='supplier_list']").each(function () {
        		 if ($(this).is(":checked")) {
        			 $(this).attr("checked", false);
            	 }
             });
         }
     });


    $('#btn_no').on('click', function() {
        if($('#joinin_message').val() == '') {
            $('#validation_message').text('请输入审核意见');
            $('#validation_message').show();
            return false;
        } else {
            $('#validation_message').hide();
        }
        if(confirm('确认回退申请？')) {
            $('#verify_type').val('fail');
            $('#form_store_verify').submit();
        }
    });

    $('#btn_pass').on('click', function() {
        if($('#joinin_message').val() == '') {
            $('#validation_message').text('请输入审核意见');
            $('#validation_message').show();
            return false;
        } else {
            $('#validation_message').hide();
        }


        var valid = true;
        if(valid) {
            $('#validation_message').hide();
            if(confirm('确认通过申请？')) {
                $('#verify_type').val('pass');
                $('#form_store_verify').submit();
            }
        }
    });
});

</script>