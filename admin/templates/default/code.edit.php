<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>物料管理 -- <?php echo $output['code']['local_description'];?></h3>
    </div>
  </div>
  <div class="fixed-empty"></div>
      <form id="admin_form" method="post" action='index.php?act=codemanages&op=edit&id=<?php echo base64_encode($output['code']['product_id']);?>'>
   <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <tbody>
        <tr>
          <td  class="required" style="display: <?php if ($output['code']['english_description']=="0")echo none ?>"><font color="red"><?php echo $output['code']['english_description']=="0" ? "" : '采购系统推送失败，该物料编号不可使用请重新编辑';?></font></td>
        </tr>
        <tr class="noborder">
            <td class="required" style="display: <?php if ($output['code']['sales_description']=="0")echo none ?>"><font color="red"><?php echo $output['code']['sales_description']=="0" ? "" : '合同系统推送失败，该物料编号不可使用请重新编辑';?></font></td>
        </tr>
          <tr>
          <td colspan="2" class="required">商品分类: </td>
        </tr>
          <tr class="noborder">
           <td class="vatop rowform" >
               <input type="hidden" value="0" name="class_id" class="mls_id">
            <input type="hidden" value="<?php echo $output['code']['gc_classname'];?>" name="brand_class" class="mls_name">
            <span class="mr10"><?php echo $output['code']['gc_classname'];?></span>
           </td>
        </tr>
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">物料编号:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="product_code"  <?php if($_GET['op'] == 'edit'){echo 'readonly';} ?> class="txt" value="<?php echo $output['code']['product_id'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
<!--        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">外部物料编号:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="product_code" class="txt" value="<?php echo $output['code']['product_code'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>-->
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">物料名称:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="local_description" class="txt" value="<?php echo $output['code']['local_description'];?>" type="text"></td>
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
          <td colspan="2" class="required"><labe for="new_pw2">是否内部物料:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform">
            <input name="product_level" value="<?php echo $output['code']['product_level']=="0" ? "否" : "是"?>" type="text" readonly="true">
            </td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr style="display: <?php echo $output['code']['product_level']=="1" ? "none" : ""?>">
          <td colspan="2" class="required"><labe for="new_pw2">挂靠内部物料:</label></td>
        </tr>
        <tr class="noborder" style="display: <?php echo $output['code']['product_level']=="1" ? "none" : ""?>">
            <td class="vatop rowform">
            <input name="to_product_id" id="to_product_id" value="<?php echo $output['code']['to_product_id']?>" type="text" readonly="true">
            </td>
          <td class="vatop tips"><a href="javascript:void(0);" id="select" >选择</a><a href="javascript:void(0);" id="delete_product" >清除内部物料编号</a></td>
        </tr>

        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">库存单位:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="unit_of_measure_inventory_id" class="txt" value="<?php echo $output['code']['unit_of_measure_inventory_id'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">采购单位:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="unit_of_measure_purchase_id" class="txt" value="<?php echo $output['code']['unit_of_measure_purchase_id'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">最小库存:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="minimum_sales_quantity" class="txt" value="<?php echo $output['code']['minimum_sales_quantity'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">最小采购数量:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="minimum_purchase_quantity" class="txt" value="<?php echo $output['code']['minimum_purchase_quantity'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
<!--        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">内部物料编号:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="to_product_id" class="txt" value="<?php echo $output['code']['to_product_id'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>-->
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">品牌:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="brand" class="txt" value="<?php echo $output['code']['brand'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">规格:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="product_spec" class="txt" value="<?php echo $output['code']['product_spec'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">单位比例:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="unit_scale" class="txt" value="<?php echo $output['code']['unit_scale'] ? $output['code']['unit_scale'] : '0.00' ;?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
<!--        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">会员价:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="vs_price" class="txt" value="<?php echo $output['code']['vs_price'] ? $output['code']['vs_price'] : '0.00';?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">协议价:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="contract_price" class="txt" value="<?php echo $output['code']['contract_price'] ? $output['code']['contract_price'] : '0.00';?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">第三方价:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="member_price" class="txt" value="<?php echo $output['code']['member_price'] ? $output['code']['member_price'] : '0.00';?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">参考价:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="reference_price" class="txt" value="<?php echo $output['code']['reference_price'] ? $output['code']['reference_price'] : '0.00';?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>-->

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
          <td colspan="2" ><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'].推送;?></span></a></td>
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
    $("#submitBtn").click(function(){
    if($("#admin_form").valid()){
     $("#admin_form").submit();
	}
        
	});
        
        $('.class-select').change(function(){
        var classid = $('.mls_id').val();
        $.post(
                  '<?php echo ADMIN_SITE_URL;?>/index.php?act=codemanages&op=getclasscode',
                {
                    'classid':classid,
                },
                function(data){
                    $('#new_pw2').val(data);
                }
        );
        
    })
});

$("#select").click(function(){ 
    
    _uri_nbb = "<?php echo ADMIN_SITE_URL;?>/index.php?act=codemanages&op=getToProductId";
    CUR_DIALOG = ajax_form('product_edit', '获取物料编号', _uri_nbb, 530);
});
$("#delete_product").click(function(){
    $("#to_product_id").val("");
});

gcategoryInit('gcategory');
</script>