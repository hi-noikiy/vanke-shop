<?php ?>
<!-- 店铺信息 -->

<div id="apply_store_info" class="apply-store-info">
  <div class="alert">
    <h4>注意事项：</h4>
    店铺经营类目为商城商品分类，请根据实际运营情况添加一个或多个经营类目。</div>
    <form id="form_store_info" action="index.php?act=agent_joinin&op=step4" method="post" >
      <table border="0" cellpadding="0" cellspacing="0" class="all">
        <thead>
          <tr>
            <th colspan="20">店铺经营信息</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <th class="w150"><i>*</i>代理账号：</th>
          <td><input id="seller_name" name="seller_name" type="text" class="w200"/>
            <span></span>
            <p class="emphasis">此账号为日后登录代理后台时使用，注册后不可修改，请牢记。</p></td>
        </tr>

        <tr>
            <th><i>*</i>代理级别：</th>
            <td><select name="sg_id" id="sg_id">
                <option value="0">请选择</option>
                <?php if(!empty($output['grade_list']) && is_array($output['grade_list'])){ ?>
                <?php foreach($output['grade_list'] as $k => $v){ ?>
                <?php $goods_limit = empty($v['sg_goods_limit'])?'不限':$v['sg_goods_limit'];?>
                <?php $explain = ' 收费标准：'.$v['sg_price'];?>
                <option value="<?php echo $v['sg_id'];?>" data-explain="<?php echo $explain;?>"><?php echo $v['sg_name'];?></option>
                <?php } ?>
                <?php } ?>
              </select>
              <input id="sg_name" name="sg_name" type="hidden" />
              <span></span>
              <div id="grade_explain" class="grade_explain"></div></td>
          </tr>
        <tr>
          <th><i>*</i>时长：</th>
          <td><select name="joinin_year" id="joinin_year">
                  <option value="0">无限制</option>
              <!--<option value="1">1 年</option>
              <option value="2">2 年</option>-->
            </select></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
  </form>
  <div class="bottom"><a id="btn_apply_store_next" href="javascript:;" class="btn">提交申请</a>
  </div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script>
<script type="text/javascript">
$(document).ready(function(){
	//gcategoryInit("gcategory");

    jQuery.validator.addMethod("seller_name_exist", function(value, element, params) { 
        var result = true;
        $.ajax({  
            type:"GET",  
            url:'<?php echo urlShop('agent_joinin', 'check_seller_name_exist');?>',
            async:false,  
            data:{seller_name: $('#seller_name').val()},  
            success: function(data){  
                if(data == 'true') {
                    $.validator.messages.seller_name_exist = "该账号已存在";
                    result = false;
                }
            }  
        });  
        return result;
    }, '');

    $('#form_store_info').validate({
        errorPlacement: function(error, element){
            element.nextAll('span').first().after(error);
        },
        rules : {
            seller_name: {
                required: true,
                maxlength: 50,
                seller_name_exist: true
            },

            sg_id: {
                required: true
            }
        },
        messages : {
            seller_name: {
                required: '请填写代理账号',
                maxlength: jQuery.validator.format("最多{0}个字")
            },

            sg_id: {
                required: '请选择代理级别'
            }
        }
    });

    $('#sg_id').on('change', function() {
        if($(this).val() > 0) {
            $('#grade_explain').text($(this).find('option:selected').attr('data-explain'));
            $('#sg_name').val($(this).find('option:selected').text());
        } else {
            $('#sg_name').val('');
        }
    });

    $('#btn_apply_store_next').on('click', function() {
        $('#form_store_info').submit();
    });
});
</script>

