      <dl nc_type="spec_dl" class="spec-bg" style="overflow: visible;">
        <dd class="spec-dd" style="width:100%">
          <form method="post" id="goods_form" action="<?php echo urlShop('store_goods_online', 'goods_kc_eidt');?>">
            <table border="0" cellpadding="0" cellspacing="0" class="spec_table" style="margin:0 0;width:100%;display:block;overflow:auto;height:500px;border:none;margin: 0 0;width:100%;box-shadow:none">
              <thead style="position:absolute;width: 98.5%;">
                <?php if(is_array($output['sk_data']) && !empty($output['sk_data'])){?>
                <?php foreach ($output['sk_data'] as $val){?>
                  <th style="text-align:center;" class="w90"><?php echo $val;?></th>
                <?php }?>
                <?php }?>
               <th class="w90"><span class="red">*</span>库存
<!--                <div class="batch"><i class="icon-edit" title="批量操作"></i>
                  <div class="batch-input" style="display:none;">
                    <h6>批量设置价格：</h6>
                    <a href="javascript:void(0)" class="close">X</a>
                    <input name="" type="text" class="text price" />
                    <a href="javascript:void(0)" class="ncsc-btn-mini" data-type="stock">设置</a><span class="arrow"></span></div>
                </div>-->
              </th>
               <th class="w90"><span class="red">*</span>预警值
<!--                <div class="batch"><i class="icon-edit" title="批量操作"></i>
                  <div class="batch-input" style="display:none;">
                    <h6>批量设置价格：</h6>
                    <a href="javascript:void(0)" class="close">X</a>
                    <input name="" type="text" class="text price" />
                    <a href="javascript:void(0)" class="ncsc-btn-mini" data-type="alarm">设置</a><span class="arrow"></span></div>
                </div>-->
              </th>
                  </thead>
                <thead style="height: 5px;">
                <?php if(is_array($output['sk_data']) && !empty($output['sk_data'])){?>
                <?php foreach ($output['sk_data'] as $val){?>
                  <th style="text-align:center;height: 50px;" class="w90">&nbsp;</th>
                <?php }?>
                <?php }?>
                <th style="text-align:center;height: 50px;" class="w60">&nbsp;</th>
                <th style="text-align:center;height: 50px;" class="w70">&nbsp;</th>
                <th style="text-align:center;height: 50px;" class="w70">&nbsp;</th>
                  </thead>
             <tbody id="specification_stock" nc_type="spec_table">
              <?php if(!empty($output['list']) && is_array($output['list'])){?>
                  <?php foreach ($output['list'] as $vals){?>
                  <tr nc_type="good_data">
                  <?php if(!empty($vals['goods_spec']) && is_array(unserialize($vals['goods_spec']))){foreach (unserialize($vals['goods_spec']) as $sp){?>
                          <td align="center"><?php echo $sp;?></td>
                  <?php }}?>
                          <input type="hidden" name="goods_id[]" value="<?php echo $vals['goods_id'];?>" />
                          <td align="center">
                              <input style="width:35px;" class="text price" type="text" name="goods_storage[<?php echo $vals['goods_id'];?>]" data_type="goods_third_price" nc_type="" value="<?php echo $vals['goods_storage'];?>" />
                          </td>
                           <td align="center">
                              <input style="width:35px;" class="text price" type="text" name="goods_storage_alarm[<?php echo $vals['goods_id'];?>]" data_type="g_costprice" nc_type="" value="<?php echo $vals['goods_storage_alarm'];?>" />
                          </td>
                          <td></td>
                  </tr>
              <?php }}?>
              </tbody>
            </table>
                <div class="bottom tc hr32">
                    <label class="submit-border">
                    <input type="submit" class="submit" value="提交保存" />
                    </label>
                </div>
            </from>
        </dd>
      </dl>
<script>
$("#goods_form").bind("submit", function(){  
		var num_a = 0;
		$("[nc_type='spec_table'] tr[nc_type='good_data']").each(function(){
                var goods_third_price = isNaN(parseInt($(this).find("input[data_type='goods_third_price']").val())) ? 0:parseInt($(this).find("input[data_type='goods_third_price']").val());
        	var cosprice = isNaN(parseInt($(this).find("input[data_type='g_costprice']").val())) ? 0:parseInt($(this).find("input[data_type='g_costprice']").val());    
                if(goods_third_price <= 0){
            	num_a++;
            	$(this).find("input[data_type='goods_third_price']").css('background-color','#FFAEB9');
            }else{
            	$(this).find("input[data_type='goods_third_price']").css('background-color','#fff');
            }
		});
		if(num_a > 0){
			alert("库存不能低于0");
			return false;
		}
	});
</script> 
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/store_goods_add.step2.js"></script>

