<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>物料明细 -- <?php echo $output['code']['local_description'];?></h3>
    </div>
  </div>
  <div class="fixed-empty"></div>
    <form id="admin_form" method="post" action='index.php?act=codemanages&op=detail_push'>
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
            <td class="vatop rowform"><input id="new_pw2" name="product_code"  readonly class="txt" value="<?php echo $output['code']['product_id'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">物料名称:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2"  readonly name="local_description" class="txt" value="<?php echo $output['code']['local_description'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">是否入库:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform" >
                <input id="new_pw2"  readonly name="product_classification_id" class="txt" value="<?php echo ($output['code']['product_classification_id'] == '1') ? "是" : "否" ;?>" type="text">
            </td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">是否有效:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform">
                <input id="new_pw2"  readonly name="deleted_flag" class="txt" value="<?php echo ($output['code']['deleted_flag'] ==1) ? "是" : "否" ;?>" type="text">
            </td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">物料名称是否可以修改:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform">
                <input id="new_pw2"  readonly name="serialized_item_flag" class="txt" value="<?php echo ($output['code']['serialized_item_flag'] == '1') ? "是" : "否" ;?>" type="text">
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
        </tr>

        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">库存单位:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" readonly  name="unit_of_measure_inventory_id" class="txt" value="<?php echo $output['code']['unit_of_measure_inventory_id'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">采购单位:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" readonly name="unit_of_measure_purchase_id" class="txt" value="<?php echo $output['code']['unit_of_measure_purchase_id'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">最小库存:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" readonly name="minimum_sales_quantity" class="txt" value="<?php echo $output['code']['minimum_sales_quantity'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">最小采购数量:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" readonly name="minimum_purchase_quantity" class="txt" value="<?php echo $output['code']['minimum_purchase_quantity'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">品牌:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" readonly name="brand" class="txt" value="<?php echo $output['code']['brand'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">规格:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" readonly name="product_spec" class="txt" value="<?php echo $output['code']['product_spec'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">单位比例:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" readonly name="unit_scale" class="txt" value="<?php echo $output['code']['unit_scale'] ? $output['code']['unit_scale'] : '0.00' ;?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
<!--        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">会员价:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" readonly name="vs_price" class="txt" value="<?php echo $output['code']['vs_price'] ? $output['code']['vs_price'] : '0.00';?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">协议价:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" readonly name="contract_price" class="txt" value="<?php echo $output['code']['contract_price'] ? $output['code']['contract_price'] : '0.00';?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">第三方价:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" readonly name="member_price" class="txt" value="<?php echo $output['code']['member_price'] ? $output['code']['member_price'] : '0.00';?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2">参考价:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" readonly name="reference_price" class="txt" value="<?php echo $output['code']['reference_price'] ? $output['code']['reference_price'] : '0.00';?>" type="text"></td>
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
          <td colspan="2" >
              <a href="<?php echo ADMIN_SITE_URL;?>/index.php?act=codemanages&op=index" class="btn" id="submitBtn"><span>返回列表</span></a>
              <a href="<?php echo ADMIN_SITE_URL;?>/index.php?act=codemanages&op=detail_push&id=<?php echo base64_encode($output['code']['product_id']);?>" class="btn" id="submitBtn"><span>重新推送</span></a>
          </td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.mousewheel.js"></script>
<script>


</script>