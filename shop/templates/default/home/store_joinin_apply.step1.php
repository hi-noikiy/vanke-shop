<?php ?>

<!-- 公司信息 v3-10 简化 -->

<div id="apply_company_info" class="apply-company-info">
  <div class="alert">
    <h4>注意事项：</h4>
    以下所需要上传的电子版资质文件仅支持JPG\GIF\PNG格式图片，大小请控制在1M之内。<br>
    <b>建议使用163邮箱，如使用QQ或其它邮箱无法正常接收邮件，请在垃圾邮件内查看是否有相应邮件，或将wycgpt@aliyun.com加入邮箱白名单，具体设置方法请登录相应邮箱进行设置。</b></div>
  <form id="form_company_info" action="index.php?act=store_joinin&op=step2" method="post" enctype="multipart/form-data" >
    <table border="0" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
          <th colspan="2">公司及联系人信息</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th><i>*</i>公司名称：</th>
          <td><input name="company_name" type="text" value="<?php echo $output['data_rz']['company_name'];?>" class="w200"/>
            <span></span></td>
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
          <td><input name="contacts_email" value="<?php echo $output['data_rz']['contacts_email'];?>" type="text" id="email_check" class="w200" placeholder="请仔细阅读注意事项"/>
            <span></span></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
      
      <table border="0" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
          <th colspan="2">城市公司选择</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th><i>*</i>城市公司所在地：</th>
          <td  id="prov_2">
               
             <select id="city_centre_2" name="city_centre">
                 <option>请选择</option>
                <?php foreach($output['city'] as $rows){ ?>
                 <option value="<?php echo $rows['id'];?>" <?php if($output['data_rz']['city_center'] == $rows['id']){echo "selected='selected'";}?>><?php echo $rows['city_name']?></option>
                <?php } ?>
             </select>
              <span></span>
             <label for="city_name" id="city_name2" style="display: none;" class="error">请选择城市公司</label>
           </td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
      
      
    <table border="0" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
            <th colspan="20">营业执照信息（副本）<em>注：如果营业执照是无限期的有效期选择最大值</em></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th><i>*</i>营业执照号：</th>
          <td><input name="business_licence_number" value="<?php echo $output['data_rz']['business_licence_number'];?>" type="text" class="w200" />
            <span></span></td>
        </tr>
        <tr>
          <th><i>*</i>营业执照所在地：</th>
          <td><input id="business_licence_address" value="<?php echo $output['data_rz']['business_licence_address'];?>" name="business_licence_address" type="hidden" />
            <span></span></td>
        </tr>
        <tr>
          <th><i>*</i>营业执照有效期：</th>
          <td><input id="business_licence_start" value="<?php echo $output['data_rz']['business_licence_start'];?>" name="business_licence_start" type="text" class="w90" />
            <span></span>-
            <input id="business_licence_end" value="<?php echo $output['data_rz']['business_licence_end'];?>" name="business_licence_end" type="text" class="w90" />
            <span></span></td>
        </tr>
        <tr>
          <th>经营范围：</th>
          <td><textarea name="business_sphere" rows="3" class="w200"><?php echo $output['data_rz']['business_sphere'];?></textarea>
            <span></span></td>
        </tr>
        <tr>
          <th><i>*</i>营业执照电子版：</th>
          <td><input name="business_licence_number_electronic"  id= "electronic" type="file" class="w200" />
              <?php if($output['data_rz']['business_licence_number_electronic']){?>
              <a  target="_bank" href="<?php echo getStoreJoininImageUrl($output['data_rz']['business_licence_number_electronic']);?>" >查看图片</a>
              <?php }?>
              <span class="block">图片大小请控制在1M之内，请确保图片清晰，文字可辨并有清晰的红色公章。</span></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
      
      <table border="0" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
          <th colspan="20">是否是三证合一</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>三证合一：</th>
          <td><input id="is_therea" name="is_therea" value="1" checked='checked' <?php if($output['data_rz']['is_therea'] == 1){echo "checked=checked";}?> type="checkbox" />
            <span></span></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
      
      <table border="0" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
          <th colspan="20">是否是一般纳税人</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>一般纳税人：</th>
          <td><input id="is_taxpayer" name="is_taxpayer" value="1" checked='checked' <?php if($output['data_rz']['is_taxpayer'] == 1){echo "checked=checked";}?> type="checkbox" />
            <span></span></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
      
      
    <table border="0" id="organization_table" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
          <th colspan="20">组织机构代码证</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>组织机构代码：</th>
          <td><input name="organization_code" value="<?php echo $output['data_rz']['organization_code'];?>" type="text" class="w200"/>
            <span></span></td>
        </tr>
        <tr>
          <th>组织机构代码证电子版：</th>
          <td><input name="organization_code_electronic" id="electronic" type="file" />
               <?php if($output['data_rz']['organization_code_electronic']){?>
              <a  target="_bank" href="<?php echo getStoreJoininImageUrl($output['data_rz']['organization_code_electronic']);?>" >查看图片</a>
              <?php }?>
            <span class="block">请确保图片清晰，文字可辨并有清晰的红色公章。</span></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
      
      <!--
    <table border="0" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
          <th colspan="20">一般纳税人证明<em>注：所属企业具有一般纳税人证明时，此项为必填。</em></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150">一般纳税人证明：</th>
          <td><input name="general_taxpayer" type="file" />
              <?php if($output['data_rz']['general_taxpayer']){?>
              <a  target="_bank" href="<?php echo getStoreJoininImageUrl($output['data_rz']['general_taxpayer']);?>" >查看图片</a>
              <?php }?>
            <span class="block">请确保图片清晰，文字可辨并有清晰的红色公章。</span></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
      -->
      <table border="0" id="registration_certificate_table" cellpadding="0" cellspacing="0" class="all" style="display: block;">
      <thead>
        <tr>
          <th colspan="20">税务登记证</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150">税务登记证号：</th>
          <td><input name="tax_registration_certificate" value="<?php echo $output['data_rz']['tax_registration_certificate'];?>" type="text" class="w200"/>
              <span></span></td>
        </tr>
	<!-- v3-10 简化-->
        <tr>
          <th><i>*</i>纳税人识别号：</th>
          <td><input name="taxpayer_id" value="<?php echo $output['data_rz']['taxpayer_id'];?>" type="text" class="w200"/>
            <span></span></td>
        </tr>
        <tr>
          <th>税务登记证号电子版：</th>
          <td><input name="tax_registration_certificate_electronic" type="file" />
               <?php if($output['data_rz']['tax_registration_certificate_electronic']){?>
              <a  target="_bank" href="<?php echo getStoreJoininImageUrl($output['data_rz']['tax_registration_certificate_electronic']);?>" >查看图片</a>
              <?php }?>
            <span class="block">请确保图片清晰，文字可辨并有清晰的红色公章。</span></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
      
  </form>
  <div class="bottom"><a id="btn_apply_company_next" href="javascript:;" class="btn">下一步，提交财务资质信息</a></div>
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
    //营业执照编号验证
    jQuery.validator.addMethod("isLicense", function(value, element) {
        var length = value.length;
        var license = /^([a-zA-Z0-9]+)$/;
        return this.optional(element) || ( license.test(value));
    }, "营业执照只能输入数字或者字母");
    $('#form_company_info').validate({
        errorPlacement: function(error, element){
            element.nextAll('span').first().after(error);
        },
        rules : {
            company_name: {
                required: true,
                maxlength: 50 
            },
            company_address: {
                required: true,
                maxlength: 50 
            },
            company_address_detail: {
                required: true,
                maxlength: 50 
            },
//            company_phone: {
//                required: true,
//                maxlength: 20 
//            }, 
           company_employee_count: {
                required: true,
                digits: true 
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
            business_licence_number: {
                required: true,
                maxlength: 25,
                minlength: 13,
                isLicense: true
            },
            business_licence_address: {
                required: true,
                maxlength: 50
            },
            business_licence_start: {
                required: true
            },
            business_licence_end: {
                required: true
            },
	    // 简化
            /* business_sphere: {
                required: true,
                maxlength: 500
            },*/
        /*
            business_licence_number_electronic: {
                required: true,
                accept: "jpg|jpeg|png|gif",
                imgsize:true
            },
         /*organization_code: {
                required: true,
                maxlength: 20
            }, 
	    organization_code_electronic: {
                required: true
            } */
        },
        messages : {
            company_name: {
                required: '请输入公司名称',
                maxlength: jQuery.validator.format("最多{0}个字")
            },
            company_address: {
                required: '请选择区域地址',
                maxlength: jQuery.validator.format("最多{0}个字")
            },
            company_address_detail: {
                required: '请输入公司详细地址',
                maxlength: jQuery.validator.format("最多{0}个字")
            },
//            company_phone: {
//                required: '请输入公司电话',
//                maxlength: jQuery.validator.format("最多{0}个字")
//            }, 
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
            business_licence_number: {
                required: '请输入营业执照号',
                maxlength: jQuery.validator.format("最多{0}个字")
            },
            business_licence_address: {
                required: '请选择营业执照所在地',
                maxlength: jQuery.validator.format("最多{0}个字")
            },
            business_licence_start: {
                required: '请选择生效日期'
            },
            business_licence_end: {
                required: '请选择结束日期'
            },
	     // 简化
            /*business_sphere: {
                required: '请填写营业执照法定经营范围',
                maxlength: jQuery.validator.format("最多{0}个字")
           },*
            /*
            business_licence_number_electronic: {
                required: '请选择上传营业执照电子版文件',
                accept: '请选择正确的格式',
                imgsize: '图片大小请控制在1M之内'
            },**/
            /*organization_code: {
                required: '请填写组织机构代码',
                maxlength: jQuery.validator.format("最多{0}个字")
            }, 
            organization_code_electronic: {
                required: '请选择上传组织机构代码证电子版文件'
            } */
        }
    });
    $("#electronic").change(function(){
        var name_arr = ['jpg','jepg','png','bmp'];
        var filename = $("#electronic").val();
        var array_name = filename.split(".");
        var name  = array_name[array_name.length-1].toLowerCase();
        var exist = $.inArray(name, name_arr);
        if(exist<0){
            alert("仅支持上传的格式：jpg,jepg,png,bmp");
            $("#electronic").val("");
        }
    });
    $('#btn_apply_company_next').on('click', function() {
        var file = document.getElementById('electronic'); 
        var statre_time = $('#business_licence_start').val();
        var end_time = $('#business_licence_end').val();
        if(statre_time > end_time){
           p_html = '<label for="business_licence_end" class="error">开始时间不能晚于结束时间</label>';
           var p_html = $('#business_licence_end').next().html(p_html);
           return false;
        }
        if (file.value == "") { 
            <?php if(empty($output['data_rz']['business_licence_number_electronic'])){?>
                $('#electronic span').css('display','block'); return false;
            <?php }?>
        }
        var name_arr = ['jpg','jepg','png','bmp'];
         var filename = $("#electronic").val();
        var array_name = filename.split(".");
        var name  = array_name[array_name.length-1].toLowerCase();
        var exist = $.inArray(name, name_arr);
        if(exist<0){
            alert("仅支持上传的格式：jpg,jepg,png,bmp");
            return false;
        }
        var ar = $("#city_centre_2").find("option:selected").text();
        if(ar =="请选择"){
            $('#city_name2').css('display','block');return false;
        }
        if($('#form_company_info').valid()) {
        	//$('#company_address').next().attr('name','province_id');
            $("#prov select").first().attr('name','province_id');
            var if_true = 1;
            var email = $('#email_check').val();
             var number = $('input[name="business_licence_number"]').val();
            $.post(
           SITEURL + '/index.php?act=show_joinin&op=check_email',
                {
                    'email':email,
                    <?php if($output['data_rz']['city_center'] > 0){?>
                    'city':<?php echo $output['data_rz']['city_center'];?>
                    <?php }?>
                },
              function(data){
                  if(data == 1){
                      //验证营业执照唯一性
                        $.post(
                       SITEURL + '/index.php?act=show_joinin&op=check_number',
                            {
                                'number':number,
                                <?php if($output['data_rz']['city_center'] > 0){?>
                                'city':<?php echo $output['data_rz']['city_center'];?>
                                <?php }?>
                            },
                          function(data){
                              if(data == 1){
                                    $('#form_company_info').submit();
                              }else{
                                  alert('当前营业执照已经被注册！');
                              }
                          }
                        )
                  }else{
                      alert('当前邮箱已被认证！');
                  }
              }
            )
         
        }
    });
    $('#email_check').blur(function(){
       var email = $('#email_check').val();
       if(email != ''){
           $.post(
           SITEURL + '/index.php?act=show_joinin&op=check_email',
                {
                    'email':email,
                    <?php if($output['data_rz']['city_center'] > 0){?>
                    'city':<?php echo $output['data_rz']['city_center'];?>
                    <?php }?>

                },
              function(data){
                  if(data == 2){
                      alert('当前邮箱已被认证！');
                  }
              }
            )
       }
           
    })
    $('input[name="business_licence_number"]').blur(function(){
       var number = $('input[name="business_licence_number"]').val();
       if(number != ''){
           $.post(
           SITEURL + '/index.php?act=show_joinin&op=check_number',
                {
                    'number':number,
                    <?php if($output['data_rz']['city_center'] > 0){?>
                    'city':<?php echo $output['data_rz']['city_center'];?>
                    <?php }?>

                },
              function(data){
                  if(data == 2){
                      alert('当前营业执照已经被注册！');
                  }
              }
            )
       }
           
    })
    
    
    $("#is_therea").on("click", function() {
        if($(this).prop("checked")) { 
            $("#organization_table").hide();
            $("#registration_certificate_table").hide();
        } else { 
            $("#organization_table").show();
            $("#registration_certificate_table").show();
        }
    });
    $("#organization_table").hide();
            $("#registration_certificate_table").hide();
    
});
</script> 
