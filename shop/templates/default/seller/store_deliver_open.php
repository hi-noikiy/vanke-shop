<div class="eject_con" style='height:600px;overflow:auto;width:800px;'>
  <div id="warning" class="alert alert-error"></div>
  <form method="post" target="_parent" action="index.php?act=store_deliver&op=send_order_open" enctype="multipart/form-data" id="brand_apply_form">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="order_id" value="<?php echo $_GET['order_id'];?>" />
    <dl style='border-bottom: 1px solid #E6E6E6;'>
      <dt class='w300' style='text-align:center'>商品名称</dt>
      <dt class='w50'>商品数量</dt>
      <dt class='w50'>拆分数量</dt>
    </dl>
    <?php $i=0;foreach($output['list'] as $rows){?>
    <dl style='border-bottom: 1px solid #E6E6E6;'>
        <input type="hidden" name="re_id[]" value="<?php echo $rows['rec_id'];?>">
      <dt class='w300' style='text-align:center'><i class="required">*</i><?php echo $rows['goods_name'];?></dt>
      <dt class='w50'  style='text-align:center'><?php echo $rows['goods_num'];?></dt>
      <dt class='w50' ><input type="text" class="text num_godos_open_<?php echo $i++;?>" style="width: 80px;" name="open_num[]" value="<?php echo $rows['goods_num'] ? $rows['goods_num'] : 0; ?>" id="brand_name" /></dt>
    </dl>
    <?php }?>
    <dl>
            <dt>发货备忘：</dt>
            <dt>
                <textarea name="deliver_explain" cols="100" rows="2" class="w300 tip-t" title="您可以输入一些发货备忘信息（仅卖家自己可见）"></textarea>
            </dt>
            <dt> </dt>
        </dl>
    <dl>
        <dt><h3>物流配送</h3></dt>
    </dl>
    <div class="tabmenu">
      <ul class="tab pngFix">
        <li id="table_first" class="active"><a href="javascript:void(0);" class="shiping_one">物流配送</a></li>
        <li id="table_two" class="normal"><a href="javascript:void(0);" class="shiping_two" >自己配送</a></li>
      </ul>
    </div>
    <table class="ncsc-default-table order" id="texpress_one">
      <tbody>
        <tr>
          <td class="w50">选择</td>
          <td class="bdl w150">公司名称</td>
          <td class="w250">物流单号</td>
        </tr>
        <?php if(is_array($output['my_express_list'])){?>
        <?php foreach($output['my_express_list'] as $shiping){?>
        <tr>          
          <td class="bdl bdr tc"><input type="radio" name="shiping_id" value="<?php echo $shiping;?>" /></td>
          <td class="bdl"><?php echo $output['express_list'][$shiping]['e_name'];?></td>
          <td class="bdl"><input name="shipping_code[<?php echo $shiping?>][code]" type="text" class="text w200 tip-r shiping_code_<?php echo $shiping;?>" title="正确填写物流单号，确保快递跟踪查询信息正确" maxlength="20" nc_type="eb" nc_value="<?php echo $shiping;?>"></td>
        </tr>
        <?php } }?>
                      </tbody>
    </table>
    <table class="ncsc-default-table order" style='display:none;' id="texpress_two">
      <tbody>
        <tr>
          <td colspan="2"></td>
        </tr>
        <tr>
          <td class="bdl w300">如果订单中的商品要自己配送，您可以直接点击单选按钮</td>
          <td class="bdr tl w50">&nbsp;<input type="radio" name="shiping_id" value="-1"></td>
        </tr>
        <tr>
          <td colspan="2"></td>
        </tr>
      </tbody>
    </table>
    <div class="bottom">
      <label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_submit'];?>"/></label>
    </div>
    
  </form>
</div>
<script>
$(function(){
    $('.shiping_one').click(function(){
        $('#table_first').attr('class','active');
        $('#table_two').attr('class','normal');
        $('#shiping_one').show();
        $('#texpress_two').hide();
        $('#texpress_one').show();
    })
    $('.shiping_two').click(function(){
        $('#table_first').attr('class','normal');
        $('#table_two').attr('class','active');
        $('#texpress_one').hide();
        $('#texpress_two').show();
    })
    var numgoods = <?php echo $i;?>;
	$.getScript('<?php echo RESOURCE_SITE_URL;?>/js/common_select.js', function(){
		gcategoryInit('gcategory');
	});
    $('.submit').click(function(){
        //判断是否选择发货
        if(!$('input[name=shiping_id]').is(":checked")){
            alert('请选择发送方式！');return false;
        }else{
            var code_list = $('input[name=shiping_id]:checked').val();
            var code_val = $('.shiping_code_'+code_list).val();
            if(!code_val && code_list != -1){
                alert('请输入物流单号！');
                return false;
            }
        }
        var ture_submit  = 1;
        var code_if_true = 0;
        var code_if_i = 0;
        for(var i = 0;i<numgoods;i++){
            code_if_i += 1;
            var numg = $('.num_godos_open_'+i).val();
            if(numg >= 1){
                var re = /^\d+$/;
                //验证
                if (!re.test(numg))
                {
//                    alert('请正确填写商品拆分数量');return false;
                    //不满足条件执行到此处
                }else{
                    ture_submit = 2;
                }
            }else{
//               alert('请正确填写商品拆分数量');return false;
            }
        }
        if(ture_submit == 1){
            alert('请正确填写商品拆分数量');return false;
        }
    })
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
            brand_name : {
                required : true,
                rangelength: [0,100]
            },
            brand_initial : {
                initial  : true
            }
			<?php if ($output['brand_array']['brand_id']=='') { ?>
			,
            brand_pic : {
                required : true
			}
			<?php } ?>		
        },
        messages : {
            brand_name : {
                required : '<i class="icon-exclamation-sign"></i><?php echo $lang['store_goods_brand_input_name'];?>',
                rangelength: '<i class="icon-exclamation-sign"></i><?php echo $lang['store_goods_brand_name_error'];?>'
            },
            brand_initial : {
                initial : '<i class="icon-exclamation-sign"></i>请填写正确首字母',
            }
			<?php if ($output['brand_array']['brand_id']=='') { ?>
			,
            brand_pic : {
                required : '<i class="icon-exclamation-sign"></i><?php echo $lang['store_goods_brand_icon_null'];?>'
			}
			<?php } ?>
        }
    });
	$('input[nc_type="logo"]').change(function(){
		var src = getFullPath($(this)[0]);
		$('img[nc_type="logo1"]').attr('src', src);
	});
});

</script> 
