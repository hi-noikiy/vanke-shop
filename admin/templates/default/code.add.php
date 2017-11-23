<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>新增<?php if($_GET['type']=="W")echo "外部"?><?php if($_GET['type']=="N")echo "内部"?>物料</h3>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="admin_form" method="post" action='index.php?act=codemanages&op=add'>
   <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <tbody>
          <tr>
              <td colspan="2" class="required"><label class="validation" for="brand_class"></label>商品分类: </td>
          </tr>
          <tr class="noborder">
          <td class="vatop rowform" id="gcategory">
             <input type="hidden" value="" name="brand_class" class="mls_name brand_class">
            <select class="class-select" name ="class_select">
              <option value="0"><?php echo $lang['nc_please_choose'];?>...</option>
              <?php if(!empty($output['gc_list'])){ ?>
              <?php foreach($output['gc_list'] as $k => $v){ ?>
              <?php if ($v['gc_parent_id'] == 0) {?>
              <option value="<?php echo $v['gc_id'];?>"><?php echo $v['gc_name'];?></option>
              <?php } ?>
              <?php } ?>
              <?php } ?>
            </select>
             <input type="hidden" value="" name="class_id" class="mls_id">
          </td>
            
          <td class="vatop tips">必选！选择分类，可关联大分类或更具体的下级分类。</td>
        </tr>
        <tr class="noborder" style="display:none">
            <td class="vatop rowform"><input id="new_pw2" name="product_dd" class="txt" value="" type="text"></td>
            <td class="vatop rowform"><input id="new_pw2" name="product_type" class="txt" value="<?php echo $_GET['type']?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2"><label class="validation" for="local_description">物料名称:</label></label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform">
                <input id="new_pw2"  name="local_description" class="txt local_description" value="<?php echo $output['code']['local_description'];?>" type="text">
            </td>
                
          <td class="vatop tips"></td>
        </tr>
        
         <tr>
          <td colspan="2" class="required"><labe for="new_pw2">品牌:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="" name="brand" class="txt brand" value="<?php echo $output['code']['brand'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">规格:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="" name="product_spec" class="txt product_spec" value="<?php echo $output['code']['product_spec'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr> 

        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">是否入库:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform">
                <select name="product_classification_id">
                    <option <?php if($output['code']['product_classification_id'] == '1'){echo 'selected="selected"';}?> value="1">是</option>
                    <option <?php if($output['code']['product_classification_id'] == '0'){echo 'selected="selected"';}?> value="0">否</option>
                </select>
            </td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">是否有效:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform">
                <select name="deleted_flag">
                    <option <?php if($output['code']['deleted_flag'] == '1'){echo 'selected="selected"';}?> value="1">是</option>
                    <option <?php if($output['code']['deleted_flag'] == '0'){echo 'selected="selected"';}?> value="0">否</option>
                </select>
            </td>
            <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">物料名称是否可以修改:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform">
                <select name="serialized_item_flag">
                    <option <?php if($output['code']['serialized_item_flag'] == '1'){echo 'selected="selected"';}?> value="1">是</option>
                    <option <?php if($output['code']['serialized_item_flag'] == '0'){echo 'selected="selected"';}?> value="0">否</option>
                </select>
            </td>
            <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">库存单位:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="" name="unit_of_measure_inventory_id" class="txt" value="<?php echo $output['code']['unit_of_measure_inventory_id']=="" ? "个":$output['code']['unit_of_measure_inventory_id'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">采购单位:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="" name="unit_of_measure_purchase_id" class="txt" value="<?php echo $output['code']['unit_of_measure_purchase_id']=="" ? "个":$output['code']['unit_of_measure_purchase_id'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        <?php if($_GET['type']=="W"){?>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">是否挂靠内部物料编号:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform" id="choose_radio">
                <label>是<input id="" name="is_to_product_id" checked="checked" value="1" type="radio"></label>
                <label>否<input id="" name="is_to_product_id"  value="0" type="radio"></label>
            </td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr name="fortrue">
          <td colspan="2" class="required"><labe for="new_pw2">内部物料编号:</label></td>
        </tr>
        <tr class="noborder" name="fortrue">
            <td class="vatop rowform"><input id="to_product_id" name="to_product_id" class="txt" value="<?php echo $output['code']['to_product_id'];?>" type="text"></td>
            <td class="vatop tips"><a href="javascript:void(0);" id="select" >自动匹配</a></td>
        </tr>
        <tr name="fortrue">
             <td colspan="2" class="required"><labe for="new_pw2"><label class="validation" for="to_product_name">内部物料名称:</label></label></td>
        </tr>
        <tr class="noborder" name="fortrue">
            <td class="vatop rowform">
                <input id="to_product_name"  name="to_product_name" class="txt" value="" type="text">
            </td>
            <td class="vatop tips"  ><span id="view_to_product_name" style="color:red"></span></td>
        </tr>
        <?php }?>
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">单位比例:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="" name="unit_scale" class="txt" value="<?php echo $output['code']['unit_scale'] ? $output['code']['unit_scale'] : '0.00' ;?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr style="display: block">
            <td style="display: block">
                <input  name="vs_price" class="txt" value="<?php echo $output['code']['vs_price'] ? $output['code']['vs_price'] : '0.00';?>" type="hidden">
            <input  name="contract_price" class="txt" value="<?php echo $output['code']['contract_price'] ? $output['code']['contract_price'] : '0.00';?>" type="hidden">
            <input  name="member_price" class="txt" value="<?php echo $output['code']['member_price'] ? $output['code']['member_price'] : '0.00';?>" type="hidden">
            <input  name="reference_price" class="txt" value="<?php echo $output['code']['reference_price'] ? $output['code']['reference_price'] : '0.00';?>" type="hidden">
            </td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="2" ><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/dialog/dialog.js" id="dialog_js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.mousewheel.js"></script>
<script>
//按钮先执行验证再提交表单
$(function(){
    //控制三级联动中去掉对应的提示框 解决选择分类后无法更改UI的功能
    $("#gcategory").change(function(){
        var removeui = $("#gcategory").parent().prev().find('td:first>label:last').is('.error');
        if(removeui){
              $("#gcategory").parent().prev().find('td:first>label:last').hide();
        }
      
    });
    //改变单选框时，不需要挂靠内部物料则隐藏内部物料
    $("#choose_radio").change(function(){
        var is_true  = $('#choose_radio input[name="is_to_product_id"]:checked ').val();
        if(is_true==0){
            $("tr[name='fortrue']").hide();
            $("#to_product_name").val("0");
            $("#to_product_id").val("");
        }else{
            $("#to_product_name").val("");
            $("tr[name='fortrue']").show();
        }
    });
    //当选择不入库时，给最小库存赋值为0
    $("select[name='product_classification_id']").change(function(){
        var is_in = $("select[name='product_classification_id']").val();
        if(is_in=="0"){
            $("input[name='minimum_sales_quantity']").val("0")
        }else{
            $("input[name='minimum_sales_quantity']").val("")
        }
    });
    //内部物料编号输入框失去焦点的时候要去判定
    $("#to_product_id").change(function(){
        $("#view_to_product_name").html("");
        var is_null = $("#to_product_id").val();
        if(is_null!=''){
            var to_product_id = $("#to_product_id").val();
            $.getJSON("<?php echo ADMIN_SITE_URL;?>/index.php?act=codemanages&op=checkToProductId",{to_product_id:to_product_id}, function (data) {
                if(data.code=="-1"){
                    alert(data.local_description);
                    $("#to_product_name").val("");
                }else{
                    $("#to_product_name").removeAttr("readonly");
                    $("#to_product_name").val(data.local_description);
                    $("#to_product_name").attr({ readonly: 'true' });
                }
            });
        }else{
            $("#to_product_name").removeAttr("readonly");
            $("#to_product_name").val("");
            $("#view_to_product_name").html("内部物料编号留空，则必须输入物料名称");
        }
    });
    //当内部物料名称获得焦点的时候，如果内部物料编号有值，则不允许用户输入
        $("#to_product_name").focus(function(){
        var is_id_null = $("#to_product_id").val();
        if(is_id_null!=""){
            $("#to_product_name").removeAttr("readonly");
            $("#to_product_name").attr({ readonly: 'true' });
            $("#to_product_name").focus();
        }
        });
    //提交按钮
    $("#submitBtn").click(function(){
        $("#admin_form").validate({
        errorPlacement:function(error, element) {
           error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules:{
            class_id:{
                required:true,
                min:66
            },
            local_description:{
                 required:true
            },
            minimum_sales_quantity:{
                required:true,
                number:true
            },
            minimum_purchase_quantity:{
                required:true,
                number:true
            },
            unit_of_measure_inventory_id:{
                maxlength:40
            },
            unit_of_measure_purchase_id:{
                maxlength:40
            },
//            member_price:{
//                number:true
//            },
//            contract_price:{
//                number:true
//            },
//            vs_price:{
//                number:true
//            },
            <?php if($_GET['type']=="W"){ ?>
            to_product_name:{
                 required:true
            },
            <?php } ?>
           
        },
        messages:{
            class_id:{
                 required:"请选择分类",
                 min :"请选择至三级分类"
            },
            local_description:{
                 required:"请输入物料名称"
            },
            minimum_sales_quantity:{
                required:"请输入最小库存",
                number:"请输入数字"
            },
            minimum_purchase_quantity:{
                required:"请输入最小采购数量",
                number:"请输入数字"
            },
            unit_of_measure_inventory_id:{
                maxlength:"长度不能超过40个字符"
            },
            unit_of_measure_purchase_id:{
                maxlength:"长度不能超过40个字符"
            },
             member_price:{
                number:"请输入数字"
            },
            contract_price:{
               number:"请输入数字"
            },
            vs_price:{
                number:"请输入数字"
            },
            <?php if($_GET['type']=="W"){ ?>
            to_product_name:{
                 required:"请输入内部物料名称"
            },
            <?php } ?>
        },  
    });
     $("#admin_form").submit();
    });      
});
$("#select").click(function(){
            var class_id = $('.mls_id').val();
            var local_description = $('.local_description').val();
            var brand = $('.brand').val();
            var product_spec = $('.product_spec').val();
           if(local_description == ""){
               alert("物料名称不能为空");return false;
           } 
           if(class_id == ""){
               alert("分类不能为空");return false;
           } 
            //自动匹配
            _uri_nbb = "<?php echo ADMIN_SITE_URL;?>/index.php?act=codemanages&op=getToProductId3";
           $.post(_uri_nbb,
           {class_id: class_id,
            local_description:local_description,
            brand:brand,
            product_spec:product_spec,
           },function(data){
            if (data == 0){
                //没有查询到 情况val值并且判断选择
            $('#to_product_id').val("");
            $('#to_product_name').val("");
            queren();
            }else{
            $('#to_product_id').val(data.product_code);
                //有查询到 自动获取to_product_name
            $('#to_product_name').val(data.local_description);
            }
        },"json");
});

    //判断选择
        function queren()
        {
        var se=confirm("未查询到匹配项，请确认是否自动生成，取消为手动选择！");
        if (se==true)
          {
          automatic();
          }
        else
          {
            _uri_nbb = "<?php echo ADMIN_SITE_URL;?>/index.php?act=codemanages&op=getToProductId";
            CUR_DIALOG = ajax_form('product_add', '获取物料编号', _uri_nbb, 530);   
          }
        }
        
        //自动生成
         function automatic(){
            var brand_class = $('.brand_class').val();
            var class_id = $('.mls_id').val();
            var local_description = $('.local_description').val();
            var brand = $('.brand').val();
            var product_spec = $('.product_spec').val();
           _uri_nbb = "<?php echo ADMIN_SITE_URL;?>/index.php?act=codemanages&op=automatic";
           $.post(_uri_nbb,
           {class_id: class_id,
            local_description:local_description,
            brand:brand,
            product_spec:product_spec,
            brand_class:brand_class,
           },function(data){
            if (data == 0){
            alert("自动创建失败，请手动选择！");
            _uri_nbb = "<?php echo ADMIN_SITE_URL;?>/index.php?act=codemanages&op=getToProductId";
            CUR_DIALOG = ajax_form('product_add', '获取物料编号', _uri_nbb, 530);  
            }else{
            $('#to_product_id').val(data.product_id);
            $('#to_product_name').val(data.local_description);
            }
        },"json");

            }
gcategoryInit('gcategory');
</script>