      <dl nc_type="spec_dl" class="spec-bg" style="overflow: visible;">
        <dd class="spec-dd" style="width:100%">
          <table border="0" cellpadding="0" cellspacing="0" class="spec_table" style="margin:0 0;width:100%;display:block;overflow:auto;height:500px;border:none;margin: 0 0;width:100%;box-shadow:none">
            <thead style="position:absolute;width: 98.5%;">
              <?php if(is_array($output['sk_data']) && !empty($output['sk_data'])){?>
              <?php foreach ($output['sk_data'] as $val){?>
            	<th style="text-align:center;" class="w90"><?php echo $val;?></th>
              <?php }?>
              <?php }?>
            <th style="text-align:center;" class="w90">协议价(元)</th>
              <th style="text-align:center;" class="w90">高级会员价(元)</th>
              <th style="text-align:center;" class="w90">普通会员价(元)</th>
              <th style="text-align:center;" class="w90">市场价(元)</th>
              <th style="text-align:center;" class="w60"><?php echo $lang['store_goods_index_stock'];?></th>
              <th style="text-align:center;" class="w70">预警值</th>
			  <th style="text-align:center;" class="w70">最小购买</th>
			  <th style="text-align:center;" class="w70">最大购买</th>

                </thead>
              <thead style="height: 5px;">
              <?php if(is_array($output['sk_data']) && !empty($output['sk_data'])){?>
              <?php foreach ($output['sk_data'] as $val){?>
            	<th style="text-align:center;height: 50px;" class="w90">&nbsp;</th>
              <?php }?>
              <?php }?>
            <th style="text-align:center;height: 50px;" class="w90">&nbsp;</th>
              <th style="text-align:center;height: 50px;" class="w90">&nbsp;</th>
              <th style="text-align:center;height: 50px;" class="w90">&nbsp;</th>
              <th style="text-align:center;height: 50px;" class="w90">&nbsp;</th>
              <th style="text-align:center;height: 50px;" class="w60">&nbsp;</th>
              <th style="text-align:center;height: 50px;" class="w70">&nbsp;</th>
			  <th style="text-align:center;height: 50px;" class="w70">&nbsp;</th>
			  <th style="text-align:center;height: 50px;" class="w70">&nbsp;</th>

                </thead>
           <tbody id="specification_stock" nc_type="spec_table">
            <?php if(!empty($output['list']) && is_array($output['list'])){?>
            	<?php foreach ($output['list'] as $vals){?>
            	<tr>
            	<?php if(!empty($vals['goods_spec']) && is_array(unserialize($vals['goods_spec']))){foreach (unserialize($vals['goods_spec']) as $sp){?>
            		<td align="center"><?php echo $sp;?></td>
            	<?php }}?>																						
            		<td align="center"><?php echo $vals['goods_marketprice'];?></td>
            		<td align="center"><?php echo $vals['goods_price'];?></td>
            		<td align="center"><?php echo $vals['goods_third_price'];?></td>
            		<td align="center"><?php echo $vals['g_costprice'];?></td>
            		<td align="center"><?php echo $vals['goods_storage'];?></td>
            		<td align="center"><?php echo $vals['goods_storage_alarm'];?></td>
            		<td align="center"><?php echo $vals['min_num'];?></td>
            		<td align="center"><?php echo $vals['max_num'];?></td>
            	</tr>
            <?php }}?>
            </tbody>
          </table>
        </dd>
      </dl>
<script>
$(function(){
    $('#jingle_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
        submitHandler:function(form){
            ajaxpost('jingle_form', '', '', 'onerror');
        },
        rules : {
            g_jingle : {
                maxlength: 50
            }
        },
        messages : {
            g_jingle : {
                maxlength: '<i class="icon-exclamation-sign"></i>不能超过50个字符'
            }
        }
    });
});
</script> 
