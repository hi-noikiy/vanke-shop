<div class="eject_con">
  <div id="warning" class="alert alert-error"></div>
  <form method="post" target="_parent" action="index.php?act=attribute_add&op=<?php if ($output['brand_array']['brand_id']!='') echo 'brand_edit'; else echo 'brand_save'; ?>"enctype="multipart/form-data" id="brand_apply_form">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="brand_id" value="<?php echo $output['brand_array']['brand_id']; ?>" />
    <dl>
      <dt><i class="required">*</i>申请类型</dt>
      <dd>
        <input type="text" style="width:250px;" class="text" name="attribute_type" value="<?php echo $output['brand_array']['attribute_type']; ?>" id="brand_name" />
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i>申请标题</dt>
      <dd>
        <input type="text" style="width:250px;"  class="text" name="attribute_name" value="<?php echo $output['brand_array']['brand_name']; ?>" id="brand_name" />
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i>申请内容<?php echo $lang['nc_colon'];?></dt>
      <dd>
          <textarea style="width:250px;" name="attribute_desc"></textarea>
        </dd>
    </dl>
  
    <div class="bottom">
      <label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_submit'];?>"/></label>
    </div>
  </form>
</div>
<script>
$(function(){
	$.getScript('<?php echo RESOURCE_SITE_URL;?>/js/common_select.js', function(){
		gcategoryInit('gcategory');
	});

    jQuery.validator.addMethod("initial", function(value, element) {
        return /^[A-Za-z0-9]$/i.test(value);
    }, "");
    $('#brand_apply_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	submitHandler:function(form){
    		ajaxpost('brand_apply_form', '', '', 'onerror') 
    	},
        rules : {
            attribute_type : {
                required : true
            },
            attribute_name : {
                required : true
            }
			,
            attribute_desc : {
                required : true
			}
        },
        messages : {
            attribute_type : {
                required : '<i class="icon-exclamation-sign"></i>请填写申请类型'
            },
            attribute_name : {
                required : '<i class="icon-exclamation-sign"></i>请填写申请标题'
            }
			,
            attribute_desc : {
                required : '<i class="icon-exclamation-sign"></i>请填写申请内容'
			}
        }
    });
	$('input[nc_type="logo"]').change(function(){
		var src = getFullPath($(this)[0]);
		$('img[nc_type="logo1"]').attr('src', src);
	});
});

</script> 
