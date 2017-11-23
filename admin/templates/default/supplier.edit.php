<?php ?>
<style type="text/css">
.d_inline {
      display:inline;
}
</style>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['store'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=store&op=store"><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="index.php?act=store&op=store_joinin"><span><?php echo $lang['pending'];?></span></a></li>
        <li><a href="index.php?act=store&op=reopen_list" ><span>续签申请</span></a></li>
<!--        <li><a href="index.php?act=store&op=store_bind_class_applay_list" ><span>经营类目申请</span></a></li>-->
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['nc_edit'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <div class="homepage-focus" nctype="editStoreContent">

  <form id="joinin_form" enctype="multipart/form-data" method="post" action="index.php?act=store&op=edit_save_joinin" >
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="member_id" value="<?php echo $output['joinin_detail']['member_id'];?>" />
      <input type="hidden" name="city_id" value="<?php echo $output['city_id'];?>" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">公司及联系人信息</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150"><label class="validation" for="company_name">公司名称：</label></th>
        <td colspan="20"><?php echo $output['joinin_detail']['company_name'];?></td>
      </tr>
      <tr>
        <th><label class="validation" for="company_address">公司所在地：</label></th>
        <td colspan="20">
          <input type="hidden" name="company_address" id="company_address" value="<?php echo $output['joinin_detail']['company_address'];?>">
        </td>
      </tr>
      <tr>
        <th class="required"><label class="validation" for="company_address_detail">公司详细地址：</label></th>
        <td colspan="20"><input type="text" class="txt w300" name="company_address_detail" value="<?php echo $output['joinin_detail']['company_address_detail'];?>"></td>
      </tr>
      <tr>
        <th>公司电话：</th>
        <td><input type="text" class="txt" name="company_phone" value="<?php echo $output['joinin_detail']['company_phone'];?>"></td>
        <th>员工总数：</th>
        <td><input type="text" class="txt w72" name="company_employee_count" value="<?php echo $output['joinin_detail']['company_employee_count'];?>">&nbsp;人</td>
        <th>注册资金：</th>
        <td><input type="text" class="txt w72" name="company_registered_capital" value="<?php echo $output['joinin_detail']['company_registered_capital'];?>">&nbsp;万元 </td>
      </tr>
      <tr>
        <th><label class="validation" for="contacts_name">联系人姓名：</label></th>
        <td><input type="text" class="txt" name="contacts_name" value="<?php echo $output['joinin_detail']['contacts_name'];?>"></td>
        <th><label class="validation" for="contacts_phone">联系人电话：</label></th>
        <td><input type="text" class="txt" name="contacts_phone" value="<?php echo $output['joinin_detail']['contacts_phone'];?>"></td>
        <th>电子邮箱：</th>
        <td><input type="text" class="txt" name="contacts_email" value="<?php echo $output['joinin_detail']['contacts_email'];?>"></td>
      </tr>
    </tbody>
  </table>
  <?php if($output['cy_type'] != 1){?>
      <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
          <thead>
          <tr>
              <th colspan="20">城市公司及联系人信息</th>
          </tr>
          </thead>
          <tbody>
          <tr>
              <th style="width: 150px;"><label class="validation" for="contacts_name">城市公司联系人姓名：</label></th>
              <td><input type="text" class="txt" name="city_contacts_name" value="<?php echo $output['information']['city_contacts_name'];?>"></td>
              <th style="width: 150px;"><label class="validation" for="contacts_phone">城市公司联系人电话：</label></th>
              <td><input type="text" class="txt" name="city_contacts_phone" value="<?php echo $output['information']['city_contacts_phone'];?>"></td>
          </tr>
          </tbody>
      </table>
  <?php }?>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">营业执照信息（副本）</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">营业执照号：</th>
        <td><?php echo $output['joinin_detail']['business_licence_number'];?></td>
     <tr>
      
        <th>营业执照所在地：</th>
        <td><input type="hidden" name="business_licence_address" id="business_licence_address" value="<?php echo $output['joinin_detail']['business_licence_address'];?>"></td></tr><tr>
      
        <th>营业执照有效期：</th>
        <td><input type="text" class="txt" name="business_licence_start" id="business_licence_start" value="<?php echo $output['joinin_detail']['business_licence_start'];?>"> - <input type="text" class="txt" name="business_licence_end" id="business_licence_end" value="<?php echo $output['joinin_detail']['business_licence_end'];?>"></td>
      </tr>
      <tr>
        <th>法定经营范围：</th>
        <td colspan="20"><input type="text" class="txt w300" name="business_sphere" value="<?php echo $output['joinin_detail']['business_sphere'];?>"></td>
      </tr>
      <tr>
        <th>营业执照<br />
电子版：</th>
        <td colspan="20">
          <a nctype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['business_licence_number_electronic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['business_licence_number_electronic']);?>" alt="" /> </a>
          
        </td>
      </tr>
    </tbody>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">组织机构代码证</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>组织机构代码：</th>
        <td colspan="20"><?php echo $output['joinin_detail']['organization_code'];?></td>
      </tr>
      <tr>
        <th>组织机构代码证<br/>          电子版：</th>
        <td colspan="20">
          <a nctype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['organization_code_electronic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['organization_code_electronic']);?>" alt="" /> </a>
        </td>
      </tr>
    </tbody>
  </table>

  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">开户银行信息：</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">银行开户名：</th>
        <td><input type="text" class="txt w300" name="bank_account_name" value="<?php echo $output['joinin_detail']['bank_account_name'];?>"></td>
      </tr><tr>
        <th>公司银行账号：</th>
        <td><input type="text" class="txt w300" name="bank_account_number" value="<?php echo $output['joinin_detail']['bank_account_number'];?>"></td></tr>
      <tr>
        <th>开户银行支行名称：</th>
        <td><input type="text" class="txt w300" name="bank_name" value="<?php echo $output['joinin_detail']['bank_name'];?>"></td>
      </tr>
      <tr>
        <th>支行联行号：</th>
        <td><input type="text" class="txt w300" name="bank_code" value="<?php echo $output['joinin_detail']['bank_code'];?>"></td>
      </tr><tr>
        <th>开户银行所在地：</th>
        <td colspan="20"><input type="hidden" name="bank_address" id="bank_address" value="<?php echo $output['joinin_detail']['bank_address'];?>"></td>
      </tr>
      <tr>
        <th>开户银行许可证<br/>电子版：</th>
        <td colspan="20">
          <a nctype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['bank_licence_electronic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['bank_licence_electronic']);?>" alt="" /> </a>
          <input type="file" name="bank_licence_electronic">
        </td>
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
        <th class="w150" ><label class="validation" for="settlement_bank_account_name">银行开户名：</label></th>
        <td><input type="text" class="txt w300" name="settlement_bank_account_name" value="<?php echo $output['joinin_detail']['settlement_bank_account_name'];?>"></td>
      </tr>
      <tr>
        <th ><label class="validation" for="settlement_bank_account_number">公司银行账号：</label></th>
        <td><input type="text" class="txt w300" name="settlement_bank_account_number" value="<?php echo $output['joinin_detail']['settlement_bank_account_number'];?>"></td>
      </tr>
      <tr>
        <th ><label class="validation" for="settlement_bank_name">支行名称：</label></th>
        <td><input type="text" class="txt w300" name="settlement_bank_name" value="<?php echo $output['joinin_detail']['settlement_bank_name'];?>"></td>
      </tr>
      <tr>
        <th>支行联行号：</th>
        <td><input type="text" class="txt w300" name="settlement_bank_code" value="<?php echo $output['joinin_detail']['settlement_bank_code'];?>"></td>
      </tr>
      <tr>
        <th>开户银行所在地：</th>
        <td><input type="hidden" name="settlement_bank_address" id="settlement_bank_address" value="<?php echo $output['joinin_detail']['settlement_bank_address'];?>"></td>
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
        <th>是否一般纳税人：</th>
        <td><input id="is_taxpayer" name="is_taxpayer" value="1" <?php if($output['joinin_detail']['is_taxpayer'] == 1){echo "checked=checked";}?> type="checkbox" /></td>
      </tr>
      <tr>
        <th class="w150">税务登记证号：</th>
        <td><input type="text" class="txt w300" name="tax_registration_certificate" value="<?php echo $output['joinin_detail']['tax_registration_certificate'];?>"></td>
      </tr>
      <tr>
        <th>纳税人识别号：</th>
        <td><input type="text" class="txt w300" name="taxpayer_id" value="<?php echo $output['joinin_detail']['taxpayer_id'];?>"></td>
      </tr>
      <tr>
        <th>税务登记证号<br />
电子版：</th>
        <td>
          <a nctype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['tax_registration_certificate_electronic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['tax_registration_certificate_electronic']);?>" alt="" /> </a>
          <input type="file" name="tax_registration_certificate_electronic">
        </td>
      </tr>
    </tbody>
  </table>
  <div><a id="submitBtn" class="btn" href="JavaScript:void(0);"><span><?php echo $lang['nc_submit'];?></span></a></div>
</form>
</div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL."/js/jquery-ui/i18n/zh-CN.js";?>" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/custom.min.js" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/styles/nyroModal.css" rel="stylesheet" type="text/css" id="cssfile2" />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript">
var SHOP_SITE_URL = '<?php echo SHOP_SITE_URL;?>';

$(function(){
    $("#company_address").nc_region();
    $("#business_licence_address").nc_region();
    $("#bank_address").nc_region();
    $("#settlement_bank_address").nc_region();
    $('#end_time').datepicker();
    $('#business_licence_start').datepicker();
    $('#business_licence_end').datepicker();
    $('a[nctype="nyroModal"]').nyroModal();
    $('input[name=store_state][value=<?php echo $output['store_array']['store_state'];?>]').trigger('click');

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
    
    //按钮先执行验证再提交表单
    $("#submitBtn").click(function(){
        if($("#joinin_form").valid()){
            $("#joinin_form").submit();
        }
    });


    $('#st_id').on('change', function() {
        if($(this).val() > 0) {
            $('#st_name').val($(this).find('option:selected').text());
        } else {
            $('#st_name').val('');
        }
    });

    $('#sc_id').on('change', function() {
        if($(this).val() > 0) {
            var selected = $(this).find('option:selected');
            $('#gc_bind').val(selected.data('bind'));
        } else {
            $('#gc_bind').val('0');
        }
    });


    $('#joinin_form').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.parent('td'));
        },
		ignore:"#end_time",
        rules : {
            company_address: {
                required : true
            },
            company_address_detail: {
                required : true
            },
            contacts_name: {
                required : true
            },
            contacts_phone: {
                required : true
            },
            settlement_bank_account_name: {
                required : true
            },
            settlement_bank_account_number: {
                required : true
            },
            settlement_bank_name: {
                required : true
            }
        },
        messages : {
            company_address: {
                required: '请输入公司所在地'
            },
            company_address_detail: {
                required: '请输入公司详细地址'
            },
            contacts_name: {
                required: '请输入联系人姓名'
            },
            contacts_phone: {
                required: '请输入联系人电话'
            },
            settlement_bank_account_name: {
                required: '请输入银行开户名'
            },
            settlement_bank_account_number: {
                required: '请输入公司银行账号'
            },
            settlement_bank_name: {
                required: '请输入支行名称'
            }
        }
    });

    $('div[nctype="editStoreContent"] > ul').find('li').click(function(){
        $(this).addClass('current').siblings().removeClass('current');
        var _index = $(this).index();
        var _form = $('div[nctype="editStoreContent"]').find('form');
        _form.hide();
        _form.eq(_index).show();
    });
});
</script>