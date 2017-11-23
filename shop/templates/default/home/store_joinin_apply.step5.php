<?php ?>

<!-- 公司信息 v3-10 简化 -->
<style>
.demo--label{margin:20px 20px 0 0;display:inline-block}
.demo--radio{display:none}
.demo--radioInput{background-color:#fff;border:1px solid rgba(0,0,0,0.15);border-radius:100%;display:inline-block;height:16px;margin-right:10px;margin-top:-1px;vertical-align:middle;width:16px;line-height:1}
.demo--radio:checked + .demo--radioInput:after{background-color:#27A9E3;border-radius:100%;content:"";display:inline-block;height:12px;margin:2px;width:12px}
.demo--checkbox.demo--radioInput,.demo--radio:checked + .demo--checkbox.demo--radioInput:after{border-radius:0}
</style>
<div id="apply_company_info" class="apply-company-info">
  <form id="form_company_info" action="index.php?act=store_joinin&op=ecrz" method="post" enctype="multipart/form-data" >
    
      <table border="0" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
          <th colspan="2">城市公司选择</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td  id="prov_2" colspan="2">
          <?php foreach($output['city'] as $rows){ ?>
          	<label class="demo--label" style="width:150px;">
          		<input class="demo--radio" type="checkbox" name="city_centre[]" value="<?php echo $rows['id'];?>">
        		<span class="demo--checkbox demo--radioInput" style="margin-top: 0px;"></span><?php echo $rows['city_name']?>
    		</label>
          <?php } ?>
             <!--  <select id="city_centre_2" name="city_centre">
                 <option>请选择</option>
                <?php foreach($output['city'] as $rows){ ?>
                 <option value="<?php echo $rows['id'];?>"><?php echo $rows['city_name']?></option>
                <?php } ?>
             </select>-->
              <span></span>
             <label for="city_name" id="city_name2" style="display: none;" class="error">请选择城市公司</label>
           </td>
        </tr>
      </tbody>
          <tr>
              <td colspan="20">&nbsp;</td>
          </tr>
          <thead>
          <tr>
              <th colspan="2">城市公司差异信息填写</th>
          </tr>
          </thead>
          <tbody>
          <tr>
              <td colspan="2">
                  <span ></span>
                  <p style="margin-top: 15px;" class="emphasis">此信息将作用于所选择的认证城市上，如多个城市存在不同信息，请选择一个城市，进行多次认证</br>
                      如不填写信心，则默认为首次认证城市公司信息
                  </p>
              </td>
          </tr>
          <tr>
              <th class="w150">联系人姓名：</th>
              <td><input type="text" value="" name="city_names" class="w200"/>
                  <span></span>
              </td>
          </tr>
          <tr>
              <th class="w150">联系人电话：</th>
              <td><input type="text" value="" name="city_phones" class="w200"/>
                  <span></span>
              </td>
          </tr>
          </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
  </form>
  <div class="bottom"><a id="btn_apply_company_next" href="javascript:;" class="btn">下一步,提交二次认证</a></div>
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
			// v3-10 简化
            /* company_phone: {
                required: true,
                maxlength: 20 
            }, 
           /*  company_employee_count: {
                required: true,
                digits: true 
            }, */
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
                maxlength: 13,
                minlength: 13,
                number: true
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
           },*/
            business_licence_number_electronic: {
                required: '请选择上传营业执照电子版文件',
                accept: '请选择正确的格式',
                imgsize: '图片大小请控制在1M之内'
            },
            /*organization_code: {
                required: '请填写组织机构代码',
                maxlength: jQuery.validator.format("最多{0}个字")
            }, 
            organization_code_electronic: {
                required: '请选择上传组织机构代码证电子版文件'
            } */
        }
    });

    $('#btn_apply_company_next').on('click', function() {
        var ar = $("#city_centre_2").find("option:selected").text();
        if(ar =="请选择"){
            $('#city_name2').css('display','block');return false;
        }
        if($('#form_company_info').valid()) {
        	//$('#company_address').next().attr('name','province_id');
            $("#prov select").first().attr('name','province_id');
            $('#form_company_info').submit();
        }
    });
});
</script> 
