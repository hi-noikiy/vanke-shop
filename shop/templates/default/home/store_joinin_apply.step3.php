<?php ?>

<!-- 店铺信息 -->
    <style>
.demo--label{margin:20px 20px 0 0;display:inline-block}
.demo--radio{display:none}
.demo--radioInput{background-color:#fff;border:1px solid rgba(0,0,0,0.15);border-radius:100%;display:inline-block;height:16px;margin-right:10px;margin-top:-1px;vertical-align:middle;width:16px;line-height:1}
.demo--radio:checked + .demo--radioInput:after{background-color:#27A9E3;border-radius:100%;content:"";display:inline-block;height:12px;margin:2px;width:12px}
.demo--checkbox.demo--radioInput,.demo--radio:checked + .demo--checkbox.demo--radioInput:after{border-radius:0}
</style>
<div id="apply_store_info" class="apply-store-info">
  <div class="alert">
    <h4>注意事项：</h4>
    店铺经营类目为商城商品分类，请根据实际运营情况添加一个。
  </div>
  <form id="form_store_info" action="index.php?act=store_join&op=ecrz" method="post" >
    <table border="0" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
          <th colspan="20">店铺经营信息</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150"><i>*</i>商家账号：</th>
          <td><?php echo $output['store_info']['seller_name'];?>
            <span></span>
          </td>
        </tr>
        <?php if($output['join_t'] == 1){?>
        <tr>
          <th class="w150"><i>*</i>店铺名称：</th>
          <td><input type="text" disabled value="<?php echo $output['store_info']['store_name'];?>" class="w200"/>
            <span></span>
          </td>
        </tr>
        <?php }else{?>
            <tr>
                <th class="w150"><i>*</i>店铺名称：</th>
                <td><input type="text" value="" name="store_name" class="w200"/>
                    <span></span>
                    <p style="margin-top: 15px;" class="emphasis">店铺名称注册后不可修改，请认真填写。</p>
                </td>
            </tr>
        <?php }?>
        <tr>
          <th><i>*</i>店铺分类：</th>
          <td id="sc-id"><!-- <select name="sc_id" id="sc_id">
              <option value="">请选择</option>
              <?php if(!empty($output['store_class']) && is_array($output['store_class'])){ ?>
              <?php foreach($output['store_class'] as $k => $v){ ?>
              <option value="<?php echo $v['sc_id'];?>" data-bind="<?php echo $v['gc_bind'];?>"><?php echo $v['sc_name'];?> (保证金：<?php echo $v['sc_bail'];?> 元)</option>
              <?php } ?>
              <?php } ?>
            </select> -->
            <?php if(!empty($output['store_class']) && is_array($output['store_class'])){ ?>
              <?php foreach($output['store_class'] as $k => $v){ ?>
              <label class="demo--label" style="width:220px;">
          		<input class="demo--radio" type="checkbox" data-type="class_id" name="store_class_ids[]" 
          		<?php if($v['is_stare'] == 1){?>checked disabled="disabled"<?php }?> value="<?php echo $v['sc_id'];?>">
        		<span class="demo--checkbox demo--radioInput" style="margin-top: 0px;"></span><?php echo $v['sc_name'];?> (保证金：<?php echo $v['sc_bail'];?> 元)
    		</label>
            <?php }}?>
            <span ></span>
            <p style="margin-top: 15px;" class="emphasis">请根据您所经营的内容认真选择店铺分类，注册后商家不可自行修改。</p></td>
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
        <tr id="cy">
          <th><i>*</i>城市公司所在地：</th>
          <td  id="prov_2">

             <!--<select id="city_centre_2" name="city_centre">-->
              <?php if(!empty($output['city']) && is_array($output['city'])){?>
              <?php foreach($output['city'] as $rows){ ?>
                  <label class="demo--label" style="width:220px;">
                      <input class="demo--radio" type="checkbox" data-type="city_id" name="city_centre[]"
                             value="<?php echo $rows['id'];?>">
                      <span class="demo--checkbox demo--radioInput" style="margin-top: 0px;"></span><?php echo $rows['city_name'];?>
                  </label>
              <?php }}?>
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

  </form>

  <div class="bottom"><a id="btn_apply_store_next" href="javascript:;" class="btn">提交申请</a>
  </div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script> 
<script type="text/javascript">
$(document).ready(function(){
        <?php if($output['join_t'] != 1){?>
    <?php if(!C('store_class_bind_isuse')){ ?>
    gcategoryInit("gcategory");
    <?php } ?>

    jQuery.validator.addMethod("seller_name_exist", function(value, element, params) { 
        var result = true;
        $.ajax({  
            type:"GET",  
            url:'<?php echo urlShop('store_joinin', 'check_seller_name_exist');?>',  
            async:false,  
            data:{seller_name: $('#seller_name').val()},  
            success: function(data){  
                if(data == 'true') {
                    $.validator.messages.seller_name_exist = "卖家账号已存在";
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
            store_name: {
                required: true,
                maxlength: 50,
                remote : '<?php echo urlShop('store_joinin', 'checkname');?>'
            },
//            sg_id: {
//                required: true
//            },
//            st_id: {
//                required: true
//            },
//            sc_id: {
//                required: true
//            },
//            store_class: {
//                required: true,
//                min: 1
//            }
        },
        messages : {
            seller_name: {
                required: '请填写卖家用户名',
                maxlength: jQuery.validator.format("最多{0}个字")
            },
            store_name: {
                required: '请填写店铺名称',
                maxlength: jQuery.validator.format("最多{0}个字"),
                remote : '店铺名称已存在'
            },
//            sg_id: {
//                required: '请选择店铺等级'
//            },
//            st_id: {
//                required: '请选择店铺类型'
//            },
/*            sc_id: {
                required: '请选择店铺分类'
            },
            store_class: {
                required: '请选择经营类目',
                min: '请选择经营类目'
            }*/
        }
    });

    $('#btn_select_category').on('click', function() {
        $('#gcategory').show();
        $('#btn_select_category').hide();
        $('#gcategory_class1').val(0).nextAll("select").remove();
    });

    $('#btn_add_category').on('click', function() {
        var tr_category = '<tr class="store-class-item">';
        var category_id = '';
        var category_name = '';
        var class_count = 0;
        var validation = true;
        var i = 1;
        $('#gcategory').find('select').each(function() {
            if(parseInt($(this).val(), 10) > 0) {
                var name = $(this).find('option:selected').text();
                tr_category += '<td>';
                tr_category += name;
                if ($('#gcategory > select').size() == i) {
                    //最后一级显示分佣比例
                    tr_category += ' (分佣比例：' + $(this).find('option:selected').attr('data-explain') + '%)';
                }
                tr_category += '</td>';
                category_id += $(this).val() + ',';
                category_name += name + ',';
                class_count++;
            } else {
                validation = false;
		$('#gc_classtip').html('请选择最后一级分类');
            }
            i++;
        });
        if(validation) {
            for(; class_count < 3; class_count++) {
                tr_category += '<td></td>';
            }
            tr_category += '<td><a nctype="btn_drop_category" href="javascript:;">删除</a></td>';
            tr_category += '<input name="store_class_ids[]" type="hidden" value="' + category_id + '" />';
            tr_category += '<input name="store_class_names[]" type="hidden" value="' + category_name + '" />';
            tr_category += '</tr>';
            $('#table_category').append(tr_category);
            $('#gcategory').hide();
            $('#btn_select_category').show();
            select_store_class_count();
        } else {
            showError('请选择分类');
        }
    });

    $('#table_category').on('click', '[nctype="btn_drop_category"]', function() {
        $(this).parent('td').parent('tr').remove();
        select_store_class_count();
    });

    // 统计已经选择的经营类目
    function select_store_class_count() {
        var store_class_count = $('#table_category').find('.store-class-item').length;
        $('#store_class').val(store_class_count);
    }

    $('#btn_cancel_category').on('click', function() {
        $('#gcategory').hide();
        $('#btn_select_category').show();
    });
//注释不用信息 如店铺等级 店铺类型 开店时长
//    $('#sg_id').on('change', function() {
//        if($(this).val() > 0) {
//            $('#grade_explain').text($(this).find('option:selected').attr('data-explain'));
//            $('#sg_name').val($(this).find('option:selected').text());
//        } else {
//            $('#sg_name').val('');
//        }
//    });
//
//    $('#st_id').on('change', function() {
//        if($(this).val() > 0) {
//            $('#st_name').val($(this).find('option:selected').text());
//        } else {
//            $('#st_name').val('');
//        }
//    });


    $('#sc_id').on('change', function() {
        $("#jylm_str").html('正在获取经营类目...');
        if($(this).val() > 0) {
            $('#sc_name').val($(this).find('option:selected').text());
            <?php if(C('store_class_bind_isuse')){ ?>
            //开启分类绑定
            var selected = $(this).find('option:selected');
            get_gc_info(selected.data('bind'));
            <?php } ?>
        } else {
            $('#sc_name').val('');
        }
    });

    function get_gc_info(id){
        if (id){
            var gc_id = id;
            var url = SITEURL + '/index.php?act=index&op=josn_classinfo&callback=?';
            
            $.getJSON(url, {'gc_id':gc_id}, function(data){
                if (data){
                    if (data.length > 0){
                        $('#store_class_ids_0').val(id+',');
                        $('#store_class_names_0').val(data[0].gc_name+',');
                        $('#store_class').val(id);
                        $("#jylm_str").html('您的经营类目为：'+ data[0].gc_name);
                    }
                }
            });
        }else{
            $('#store_class_ids_0').val('');
            $('#store_class_names_0').val('');
            $('#store_class').val('');
            $("#jylm_str").html('没有获取到经营类目。');
        }
    }
        <?php } ?>
    $('#btn_apply_store_next').on('click', function() {
        var ar = $("#city_centre_2").find("option:selected").text();
        if(ar =="请选择"){
            $('#city_name2').css('display','block');return false;
        }
        if ($("#sc-id input:checkbox[data-type='class_id']:checked").length == 0) {
        	alert('请选择店铺分类');return false;
        }
        if ($("#cy input:checkbox[data-type='city_id']:checked").length == 0) {
            alert('请选择城市公司');return false;
        }
        $('#form_store_info').submit();
    });
});
</script> 
