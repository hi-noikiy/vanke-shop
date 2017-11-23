<?php ?>

<!-- 公司信息 v3-10 简化 -->

<div id="apply_company_info" class="apply-company-info">
  <div class="alert">
    <h4>注意事项：</h4>
    以下所需要上传的电子版资质文件仅支持JPG\GIF\PNG格式图片，大小请控制在1M之内。</div>
    <form id="form_company_info" action="index.php?act=storejoininlog&op=logedit2&id=<?php echo $output['data_rz']['city_center'];?>" method="post" enctype="multipart/form-data" >
    <table border="0" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
          <th colspan="2">公司及联系人信息</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th><i>*</i>公司名称：</th>
          <td><?php echo $output['data_rz']['company_name'];?></td>
        </tr>
        <tr>
          <th><i>*</i>公司所在地：</th>
          <td  id="prov"><input id="company_address" value="<?php echo $output['data_rz']['company_address'];?>" name="company_address" type="hidden" />
            <span></span></td>
        </tr>
        <tr>
          <th><i>*</i>公司详细地址：</th>
          <td><input name="company_address_detail" value="<?php echo $output['data_rz']['company_address_detail'];?>" type="text" class="w200">
            <span></span></td>
        </tr>
	
        <tr>
          <th><i>*</i>公司电话：</th>
          <td><input name="company_phone" type="text" value="<?php echo $output['data_rz']['company_phone'];?>" class="w100">
            <span></span></td>
        </tr>
        <tr>
          <th><i>*</i>员工总数：</th>
          <td><input name="company_employee_count" value="<?php echo $output['data_rz']['company_employee_count'];?>" type="text" class="w50"/>
            &nbsp;人 <span></span></td>
        </tr>
        <tr>
          <th><i>*</i>注册资金：</th>
          <td><input name="company_registered_capital" value="<?php echo $output['data_rz']['company_registered_capital'];?>" type="text" class="w50">
            &nbsp;万元<span></span></td>
        </tr>
        <tr>
          <th><i>*</i>联系人姓名：</th>
          <td><input name="contacts_name" value="<?php echo $output['data_rz']['contacts_name'];?>" type="text" class="w100" />
            <span></span></td>
        </tr>
        <tr>
          <th><i>*</i>联系人手机：</th>
          <td><input name="contacts_phone" value="<?php echo $output['data_rz']['contacts_phone'];?>" type="text" class="w100" />
            <span></span></td>
        </tr>
        <tr>
          <th><i>*</i>电子邮箱：</th>
          <td><input name="contacts_email" value="<?php echo $output['data_rz']['contacts_email'];?>" type="text" id="email_check" class="w200" />
            <span></span></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>


      
  </form>
  <div class="bottom"><a id="btn_apply_company_next" href="javascript:;" class="btn">保存修改</a></div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    
    $('#company_address').nc_region();
    $('#business_licence_address').nc_region();
    
    $('#business_licence_start').datepicker();
    $('#business_licence_end').datepicker();

    $('#btn_apply_agreement_next').on('click', function() {
        if($('#input_apply_agreement').prop('checked')) {
            $('#apply_agreement').hide();
            $('#apply_company_info').show();
        } else {
            alert('请阅读并同意协议');
        }
    });

    // 图片格式验证
    jQuery.validator.addMethod("imgsize",
    function(value, element) {
        var browserCfg = {};
        var ua = window.navigator.userAgent;
        if (ua.indexOf("MSIE") >= 1) {
            browserCfg.ie = true;
        } else if (ua.indexOf("Firefox") >= 1) {
            browserCfg.firefox = true;
        } else if (ua.indexOf("Chrome") >= 1) {
            browserCfg.chrome = true;
        }
        var filesize = 0;
        if (browserCfg.firefox || browserCfg.chrome) {
            filesize = element.files[0].size;
        } else if (browserCfg.ie) {
            var obj_img = document.getElementById('tempimg');
            obj_img.dynsrc = element.value;
            filesize = obj_img.fileSize;
        } else {
            return false;
        }
        if (filesize == -1) {
            return false;
        } else if (filesize > (1024*1024)) {
            return false;
        } else {
            return true;
        }
    },"图片格式错误");

    // 手机号码验证
    jQuery.validator.addMethod("isMobile", function(value, element) {
        var length = value.length;
        var mobile = /^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/;
        return this.optional(element) || (length == 11 && mobile.test(value));
    }, "请正确填写您的手机号码");
    $('#form_company_info').validate({
        errorPlacement: function(error, element){
            element.nextAll('span').first().after(error);
        },
        rules : {
            company_address: {
                required: true,
                maxlength: 50 
            },
            company_address_detail: {
                required: true,
                maxlength: 50 
            },
            company_registered_capital: {
                required: true,
                digits: true 
            },
            contacts_name: {
                required: true,
                maxlength: 20 
            },
            contacts_phone: {
                required: true,
                isMobile: true
            },
            contacts_email: {
                required: true,
                email: true 
            },
        },
        messages : {
            company_address: {
                required: '请选择区域地址',
                maxlength: jQuery.validator.format("最多{0}个字")
            },
            company_address_detail: {
                required: '请输入公司详细地址',
                maxlength: jQuery.validator.format("最多{0}个字")
            },
	     // v3-10 简化
            /**/company_phone: {
                required: '请输入公司电话',
                maxlength: jQuery.validator.format("最多{0}个字")
            }, 
             company_employee_count: {
                required: '请输入员工总数',
                digits: '必须为数字'
            }, 
            company_registered_capital: {
                required: '请输入注册资金',
                digits: '必须为数字'
            },
            contacts_name: {
                required: '请输入联系人姓名',
                maxlength: jQuery.validator.format("最多{0}个字")
            },
            contacts_phone: {
                required: '请输入联系人电话'
            },
            contacts_email: {
                required: '请输入常用邮箱地址',
                email: '请填写正确的邮箱地址'
            },
        }
    });

    $('#btn_apply_company_next').on('click', function() {
        if($('#form_company_info').valid()) {
            $("#prov select").first().attr('name','province_id');
            var email = $('#email_check').val();
            $.post(
           SITEURL + '/index.php?act=storejoininlog&op=check_email',
                {
                    'email':email,
                },
              function(data){
                  if(data == 1){
                      $('#form_company_info').submit();
                  }else{
                      alert('当前邮箱已被认证！');
                  }
              }
            )
            
        }
    });
    var h = $('#is_therea').val();
    if(h == 1){
        $("#organization_table").hide();
        $("#registration_certificate_table").hide();
    }
    $("#is_therea").on("click", function() {
        if($(this).prop("checked")) { 
            $("#organization_table").hide();
            $("#registration_certificate_table").hide();
        } else { 
            $("#organization_table").show();
            $("#registration_certificate_table").show();
        }
    });
    
    
});
</script> 
