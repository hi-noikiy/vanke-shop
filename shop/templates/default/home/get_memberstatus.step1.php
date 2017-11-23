<?php ?>
<!-- 公司信息 v3-10 简化 -->

<div id="apply_company_info" class="apply-company-info">
  <div class="alert">
    <h4>注意事项：</h4>
    以下所需要上传的电子版资质文件仅支持JPG\GIF\PNG格式图片，大小请控制在1M之内。</div>
  <form id="form_company_info" action="index.php?act=getmemberstatus&op=rz_save" method="post" enctype="multipart/form-data" >
    <table border="0" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
          <th colspan="2">公司及联系人信息</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th><i>*</i>姓名：</th>
          <td><input name="name" type="text" value="<?php echo $output['data']['name'];?>" class="w200"/>
              <?php if($output['data']['name']){?>
              <input name="news_zx" type="hidden" value="1" class="w200"/>
              <?php }?>
            <span></span></td>
        </tr>
        <tr>
          <th><i>*</i>职务：</th>
          <td><input name="job_name" value="<?php echo $output['data']['job_name'];?>" type="text" class="w200">
            <span></span></td>
        </tr>
	
        <tr>
          <th><i>*</i>公司名称：</th>
          <td><input name="company_name" type="text" value="<?php echo $output['data']['company_name'];?>" class="w200">
            <span></span></td>
        </tr>
        <tr>
          <th><i>*</i>项目（小区）名称：</th>
          <td><input name="product_name" type="text" value="<?php echo $output['data']['product_name'];?>" class="w200">
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
          <th><i>*</i>所在城市：</th>
          <td  id="prov_2">
               
             <select id="city_centre_2" name="city_centre">
                 <option>请选择</option>
                <?php foreach($output['city'] as $rows){ ?>
                 <?php if($rows['id'] == 1){?>
                 <option <?php if($rows['id'] == 1){echo 'selected=selected';}?> value="<?php echo $rows['id'];?>" <?php if($output['data']['city_centre'] == $rows['id']){echo "selected='selected'";}?>><?php echo $rows['city_name']?></option>
                <?php } } ?>
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
            <th colspan="20">营业执照信息（副本）<!--<em>注：如果是三证合一，则不需要上传一般纳税人证明！</em>--></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th><i>*</i>营业执照电子版（或所在小区管理处正门照片）：</th>
          <td><input name="business_licence_number_electronic"  id= "electronic" type="file" class="w200" />
              <?php if($output['data']['business_licence_number_electronic']){?>
              <a  target="_bank" href="<?php echo getStoreJoininImageUrl($output['data']['business_licence_number_electronic']);?>" >查看图片</a>
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
      
  </form>
  <div class="bottom" ><a style="margin: 0;" id="btn_apply_company_next" href="javascript:;" class="btn">提交申请</a></div>
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
        if (ua.indexOf("MSIE") >= 1 || !!window.ActiveXObject || "ActiveXObject" in window) {
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
            filesize = element.files[0].size;
//            var obj_img = document.getElementById('tempimg');
//            obj_img.dynsrc = element.value;
//            filesize = obj_img.fileSize;
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
            name: {
                required: true,
                maxlength: 50 
            },
            job_name: {
                required: true,
                maxlength: 10 
            },
            product_name: {
                required: true,
                maxlength: 10 
            },
             <?php if(!$output['data']['business_licence_number_electronic']){?>
            business_licence_number_electronic: {
                required: true,
                accept: "jpg|jpeg|png|gif",
                imgsize:true
            },
             <?php }?>
        },
        messages : {
            company_name: {
                required: '请输入公司名称',
                maxlength: jQuery.validator.format("最多{0}个字")
            },
            name: {
                required: '请输入姓名',
                maxlength: jQuery.validator.format("最多{0}个字")
            },
            job_name: {
                required: '请输入职务'
            },
            product_name: {
                required: '请输入项目（小区）名称'
            },
            <?php if(!$output['data']['business_licence_number_electronic']){?>
            business_licence_number_electronic: {
                required: '请选择上传营业执照电子版文件',
                accept: '请选择正确的格式',
                imgsize: '图片大小请控制在1M之内'
            }
            <?php } ?>
        }
    });

    $('#btn_apply_company_next').on('click', function() {
        var file = document.getElementById('electronic'); 
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
