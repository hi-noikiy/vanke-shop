<?php ?>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/styles/nyroModal.css" rel="stylesheet" type="text/css" id="cssfile2" />
<link href="<?php echo RESOURCE_SITE_URL;?>/js/jquery.viewer/viewer.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.viewer/viewer.min.js" charset="utf-8"></script>
<script type="text/javascript">

    $(document).ready(function(){
        $('a[nctype="nyroModal"]').nyroModal();

        var options = {url: 'src', title: false};
        $('img[nctype="viewer"]').viewer(options);

        $('#btn_pass').on('click', function() {
            
        	var type_num = 0;
            $("#supplier_type tr td input[data-type='supplier_type']").each(function () {
          		 var val = $(this).val();
           		 var len = $("#supplier_list_"+val+" td table tr input[data-type='supplier_list']:checkbox:checked").length; 
            	 type_num = parseInt(type_num) + parseInt(len);
            });
			if(type_num == 0){
				 $('#validation_message').text('请选择供应商类型');
	             $('#validation_message').show();
	             return false;
			} else {
                $('#validation_message').hide();
            }

			var type_level = $('#supplier_level option:selected').val();
			if(type_level == 0){
				 $('#validation_message').text('请选择供应商级别');
	             $('#validation_message').show();
	             return false;
			} else {
              $('#validation_message').hide();
            }

			if(confirm('确认提交？')) {
                $('#verify_type').val('pass');
                $('#form_store_verify').submit();
            }
        });
    });
</script>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['store'];?>类型级别修改</h3>
    </div>
  </div>
  <div class="fixed-empty"></div>

  <form id="form_store_verify" action="index.php?act=store&op=store_type_level_verify" method="post" enctype="multipart/form-data" >
    <input id="verify_type" name="verify_type" type="hidden" />
    <?php if($_GET['is_rz'] == "1"){ ?>
    <input id="verify_type" name="pass_store" type="hidden" value="1"/>
    <?php }?>
    <input name="member_id" type="hidden" value="<?php echo $output['member_id'];?>" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
      <thead>
        <tr>
          <th colspan="20">供应商信息类型</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150">卖家账号：</th>
          <td><?php echo $output['supplier_name'];?></td>
        </tr>
        
        <!-- 新增供应商类型选择操作 @Aletta -->
        <style>
        #supplier_type,.supplier_list{
        border-collapse: collapse;
        border: 0px solid #ccc;
        }
        #supplier_type .type_td {
        border-top: 0;
        border-right: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        border-left: 0;
        }
        #supplier_type tr.lastrow td {
        border-bottom: 0;
        }
        #supplier_type tr td.lastCol {
        border-right: 0;
        } 
        </style>
        <?php if(empty($output['supplier_type_data'])){?>
        <tr>
        	<th><span style="color:red;">* </span>供应商类型：</th>
        	<td>
        		<table id="supplier_type" style="width:100%">
        		<?php foreach ($output['supplier_type'] as $key=>$type_val){?>
        		<tr>
        			<td class="type_td lastCol">
        				<label><input name="supplier_type[<?php echo $type_val['id'];?>]" type="checkbox" value="<?php echo $type_val['id'];?>" data-type="supplier_type"/><?php echo $type_val['type_name'];?></label>
                	</td>
                </tr>
                <tr <?php if(end($output['supplier_type']) == $type_val){?>class="lastrow"<?php }?> id="supplier_list_<?php echo $type_val['id'];?>" style="display: none">
                	<td class="type_td lastCol"><table class="supplier_list">
                	<?php if(is_array($output['supplier_list'][$type_val['id']])){foreach ($output['supplier_list'][$type_val['id']] as $key_a=>$type_val_a){?>
                		<?php if($key_a%4 == 0){?><tr><?php }?>
                		<td <?php if($key_a%4 == 3){?>class="lastCol"<?php }?> style="border-top: 0;">
                		<label><input name="supplier_type[<?php echo $type_val['id'];?>][]" 
                			type="checkbox" value="<?php echo $type_val_a['id'];?>" data-type="supplier_list"/><?php echo $type_val_a['type_name'];?></label>
                		</td>
                		<?php if($key_a%4 == 3){?></tr><?php }?>
                	<?php }}?>
                	</table></td>
                </tr>
                <?php }?>
            	</table>
        	</td>
        </tr>
        <tr>
        
        </tr>
        	<th><span style="color:red;">* </span>供应商级别：</th>
        	<td>
        		<select name="supply_level" id="supplier_level">
                  <option value ="0">请选择供应商级别</option>
                  <option value ="1">优选供应商</option>
                  <option value="2">合格供应商</option>
                  <option value="3">淘汰供应商</option>
                </select>
        	</td>
        <tr>
        	<th><span style="color:red;">* </span>供应商到期时间：</th>
        	<td colspan="2"><?php echo date("Y/m/d",$output['supplier_time_data']['member_time']);?>(开始)<label style="margin-left: 10px;margin-right:5px;">~</label>
        	<input class="txt date" type="text" value="<?php if(empty($output['supplier_time_data']['supply_end_time'])){ 
        	       echo date("Y/m/d",($output['supplier_time_data']['member_time']+(SUPPLY_TIME_LONG * 24 * 3600)));
        	   }else{ echo date("Y/m/d",$output['supplier_time_data']['supply_end_time']);}?>" id="supply_end_time" name="supply_end_time">
        	(到期)</td>
        </tr>
        <?php }else{?>
       <tr>
        <th><span style="color:red;">* </span>供应商类型：</th>
        <td>
           <style>
        #supplier_type,.supplier_list{
        border-collapse: collapse;
        border: 0px solid #ccc;
        }
        #supplier_type .type_td {
        border-top: 0;
        border-right: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        border-left: 0;
        }
        #supplier_type tr.lastrow td {
        border-bottom: 0;
        }
        #supplier_type tr td.lastCol {
        border-right: 0;
        } 
        </style>
        	<table id="supplier_type" style="width:100%">
        		<?php foreach ($output['supplier_type'] as $key=>$type_val){?>
        		<tr>
        			<td class="type_td lastCol">
        				<label><input name="supplier_type[<?php echo $type_val['id'];?>]" type="checkbox" value="<?php echo $type_val['id'];?>" 
        				<?php if(in_array($type_val['id'], $output['supplier_type_father'])){?>checked="checked"<?php }?> data-type="supplier_type"/><?php echo $type_val['type_name'];?></label>
                	</td>
                </tr>
                <tr <?php if(end($output['supplier_type']) == $type_val){?>class="lastrow"<?php }?> id="supplier_list_<?php echo $type_val['id'];?>" <?php if(!in_array($type_val['id'], $output['supplier_type_father'])){?>style="display: none"<?php }?>>
                	<td class="type_td lastCol"><table class="supplier_list">
                	<?php if(is_array($output['supplier_list'][$type_val['id']])){foreach ($output['supplier_list'][$type_val['id']] as $key_a=>$type_val_a){?>
                		<?php if($key_a%4 == 0){?><tr><?php }?>
                		<td <?php if($key_a%4 == 3){?>class="lastCol"<?php }?> style="border-top: 0;">
                		<label><input name="supplier_type[<?php echo $type_val['id'];?>][]" <?php if(in_array($type_val_a['id'], $output['supplier_type_sun'])){?>checked="checked"<?php }?>
                			type="checkbox" value="<?php echo $type_val_a['id'];?>" data-type="supplier_list"/><?php echo $type_val_a['type_name'];?></label>
                		</td>
                		<?php if($key_a%4 == 3){?></tr><?php }?>
                	<?php }}?>
                	</table></td>
                </tr>
                <?php }?>
            </table>
        </td>
      </tr>
      <tr>
        <th><span style="color:red;">* </span>供应商级别：</th>
        <td>
        	<select name="supply_level" id="supplier_level">
                  <option value ="0">请选择供应商级别</option>
                  <option <?php if($output['member_supplier_level'] == '1'){?>selected<?php }?> value ="1">优选供应商</option>
                  <option <?php if($output['member_supplier_level'] == '2'){?>selected<?php }?> value="2">合格供应商</option>
                  <option <?php if($output['member_supplier_level'] == '3'){?>selected<?php }?> value="3">淘汰供应商</option>
            </select>
        </td>
      </tr>
      <tr>
        	<th><span style="color:red;">* </span>供应商到期时间：</th>
        	<td colspan="2"><?php echo date("Y/m/d",$output['supplier_time_data']['member_time']);?>(开始)<label style="margin-left: 10px;margin-right:5px;">~</label>
        	<input class="txt date" type="text" value="<?php if(empty($output['supplier_time_data']['supply_end_time'])){ 
        	       echo date("Y/m/d",($output['supplier_time_data']['member_time']+(SUPPLY_TIME_LONG * 24 * 3600)));
        	   }else{ echo date("Y/m/d",$output['supplier_time_data']['supply_end_time']);}?>" id="supply_end_time" name="supply_end_time">
        	(到期)</td>
        </tr>
        <?php }?>
    </tbody>
    </table>
    <div id="validation_message" style="color:red;display:block;"></div>
    <div>
    <a id="btn_pass" class="btn" href="JavaScript:void(0);"><span>提交</span></a>
    </div>
  </form>

</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/jquery.ui.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript">
$(function () {

	 $('#supply_end_time').datepicker({dateFormat: 'yy-mm-dd',minDate: new Date()});
	 
	 $("#supplier_type tr td input[data-type='supplier_type']").bind("click", function () {
		 var val = $(this).val();
		 if ($(this).is(":checked")) {
			 $("#supplier_list_"+val).show();
         }else{
        	 $("#supplier_list_"+val).hide();
        	 $("#supplier_list_"+val+" td table tr input[data-type='supplier_list']").each(function () {
        		 if ($(this).is(":checked")) {
        			 $(this).attr("checked", false);
            	 }
             });
         }
     });
});
</script>