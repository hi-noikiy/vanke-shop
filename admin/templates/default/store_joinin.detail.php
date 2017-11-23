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

        $('#btn_fail').on('click', function() {
            if($('#joinin_message').val() == '') {
                $('#validation_message').text('请输入审核意见');
                $('#validation_message').show();
                return false;
            } else {
                $('#validation_message').hide();
            }
            if(confirm('确认回退申请？')) {
                $('#verify_type').val('fail');
                $('#form_store_verify').submit();
            }
        });
        
        
        $('#btn_no').on('click', function() {
            if($('#joinin_message').val() == '') {
                $('#validation_message').text('请输入审核意见');
                $('#validation_message').show();
                return false;
            } else {
                $('#validation_message').hide();
            }
            if(confirm('确认拒绝申请？')) {
                $('#verify_type').val('fno');
                $('#form_store_verify').submit();
            }
        });
        
        $('#btn_pass').on('click', function() {
            
            if($('#joinin_message').val() == '') {
                $('#validation_message').text('请输入审核意见');
                $('#validation_message').show();
                return false;
            } else {
                $('#validation_message').hide();
            }

            var is_rz_val = '<?php echo empty($_GET['is_rz']) ? '':$_GET['is_rz'];?>';
            var store_joinin_num = '<?php echo empty($output['store_joinin_num']) ? '0':$output['store_joinin_num'];?>';
			if(is_rz_val == '1' && store_joinin_num == '0'){
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
			}
            
            var valid = true;
            $('[nctype="commis_rate"]').each(function(commis_rate) {
                rate = $(this).val();
                if(rate == '') {
                    valid = false;
                    return false;
                }

                var rate = Number($(this).val());
                if(isNaN(rate) || rate < 0 || rate >= 100) {
                    valid = false;
                    return false;
                }
            });
            if(valid) {
                $('#validation_message').hide();
                if(confirm('确认通过申请？')) {
                    $('#verify_type').val('pass');
                    $('#form_store_verify').submit();
                }
            } else {
                $('#validation_message').text('请正确填写分佣比例');
                $('#validation_message').show();
            }
        });
    });
</script>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['store'];?></h3>
      <ul class="tab-base">
        <!--<li><a href="index.php?act=store&op=store"><span><?php /*echo $lang['manage'];*/?></span></a></li>-->

        <!--<li><a href="index.php?act=store&op=reopen_list" ><span>续签申请</span></a></li>-->
        <li><a href="index.php?act=store&op=store_joinin2"><span>认证申请审核</span></a></li>
          <li><a href="index.php?act=store&op=store_joinin" ><span><?php echo $lang['pending'];?>审核</span></a></li>
<!--        <li><a href="index.php?act=store&op=store_bind_class_applay_list" ><span>经营类目申请</span></a></li>-->
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $output['joinin_detail_title'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">公司及联系人信息</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">公司名称：</th>
        <td colspan="20"><?php echo $output['joinin_detail']['company_name'];?></td>
      </tr>
      <tr>
        <th>公司所在地：</th>
        <td><?php echo $output['joinin_detail']['company_address'];?></td>
        <th>公司详细地址：</th>
        <td colspan="20"><?php echo $output['joinin_detail']['company_address_detail'];?></td>
      </tr>
      <tr>
        <th>公司电话：</th>
        <td><?php echo $output['joinin_detail']['company_phone'];?></td>
        <th>员工总数：</th>
        <td><?php echo $output['joinin_detail']['company_employee_count'];?>&nbsp;人</td>
        <th>注册资金：</th>
        <td><?php echo $output['joinin_detail']['company_registered_capital'];?>&nbsp;万元 </td>
      </tr>
      <tr>
        <th>联系人姓名：</th>
        <td><?php echo $output['joinin_detail']['contacts_name'];?></td>
        <th>联系人电话：</th>
        <td><?php echo $output['joinin_detail']['contacts_phone'];?></td>
        <th>电子邮箱：</th>
        <td><?php echo $output['joinin_detail']['contacts_email'];?></td>
      </tr>
    </tbody>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">营业执照信息（副本）</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">营业执照号：</th>
        <td><?php echo $output['joinin_detail']['business_licence_number'];?></td></tr><tr>
      
        <th>营业执照所在地：</th>
        <td><?php echo $output['joinin_detail']['business_licence_address'];?></td></tr><tr>
      
        <th>营业执照有效期：</th>
        <td><?php echo $output['joinin_detail']['business_licence_start'];?> - <?php echo $output['joinin_detail']['business_licence_end'];?></td>
      </tr>
      <tr>
        <th>法定经营范围：</th>
        <td colspan="20"><?php echo $output['joinin_detail']['business_sphere'];?></td>
      </tr>
      <tr>
        <th>营业执照<br />电子版：</th>
        <td colspan="20"><?php if($output['joinin_detail']['business_licence_number_electronic']){?><img nctype="viewer" src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['business_licence_number_electronic']);?>" alt="" /><?php }?></td>
      </tr>
    </tbody>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">组织机构代码证</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>组织机构代码：</th>
        <td colspan="20"><?php echo $output['joinin_detail']['organization_code'];?></td>
      </tr>
      <tr>
        <th>组织机构代码证<br/>          电子版：</th>
        <td colspan="20"><?php if($output['joinin_detail']['organization_code_electronic']){?><a nctype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['organization_code_electronic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['organization_code_electronic']);?>" alt="" /> </a><?php }?></td>
      </tr>
    </tbody>
  </table>
  <!--
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">一般纳税人证明：</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>一般纳税人证明：</th>
        <td colspan="20"><?php if($output['joinin_detail']['general_taxpayer']){?><a nctype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['general_taxpayer']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['general_taxpayer']);?>" alt="" /> </a><?php }?></td>
      </tr>
    </tbody>
  </table>
  -->
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">开户银行信息：</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">银行开户名：</th>
        <td><?php echo $output['joinin_detail']['bank_account_name'];?></td>
      </tr><tr>
        <th>公司银行账号：</th>
        <td><?php echo $output['joinin_detail']['bank_account_number'];?></td></tr>
      <tr>
        <th>开户银行支行名称：</th>
        <td><?php echo $output['joinin_detail']['bank_name'];?></td>
      </tr>
      <tr>
        <th>支行联行号：</th>
        <td><?php echo $output['joinin_detail']['bank_code'];?></td>
      </tr><tr>
        <th>开户银行所在地：</th>
        <td colspan="20"><?php echo $output['joinin_detail']['bank_address'];?></td>
      </tr>
      <tr>
        <th>开户银行许可证<br/>电子版：</th>
        <td colspan="20"><?php if($output['joinin_detail']['bank_licence_electronic']){?><img nctype="viewer" src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['bank_licence_electronic']);?>" alt="" /><?php }?></td>
      </tr>
    </tbody>
    
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">结算账号信息：</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">银行开户名：</th>
        <td><?php echo $output['joinin_detail']['settlement_bank_account_name'];?></td>
      </tr>
      <tr>
        <th>公司银行账号：</th>
        <td><?php echo $output['joinin_detail']['settlement_bank_account_number'];?></td>
      </tr>
      <tr>
        <th>开户银行支行名称：</th>
        <td><?php echo $output['joinin_detail']['settlement_bank_name'];?></td>
      </tr>
      <tr>
        <th>支行联行号：</th>
        <td><?php echo $output['joinin_detail']['settlement_bank_code'];?></td>
      </tr>
      <tr>
        <th>开户银行所在地：</th>
        <td><?php echo $output['joinin_detail']['settlement_bank_address'];?></td>
      </tr>
    </tbody>
    
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">税务登记证</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">税务登记证号：</th>
        <td><?php echo $output['joinin_detail']['tax_registration_certificate'];?></td>
      </tr>
      <tr>
        <th>纳税人识别号：</th>
        <td><?php echo $output['joinin_detail']['taxpayer_id'];?></td>
      </tr>
      <tr>
        <th>税务登记证号<br />
电子版：</th>
        <td><?php if($output['joinin_detail']['tax_registration_certificate_electronic']){?><a nctype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['tax_registration_certificate_electronic']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['tax_registration_certificate_electronic']);?>" alt="" /> </a><?php }?></td>
      </tr>
    </tbody>
  </table>
  <form id="form_store_verify" action="index.php?act=store&op=store_joinin_verify" method="post" enctype="multipart/form-data" >
    <input id="verify_type" name="verify_type" type="hidden" />
    <?php if($_GET['is_rz'] == "1"){ ?>
    <input id="verify_type" name="pass_store" type="hidden" value="1"/>
    <?php }?>
    <input name="member_id" type="hidden" value="<?php echo $output['joinin_detail']['member_id'];?>" />
    <input name="city_id" type="hidden" value="<?php echo $output['joinin_detail']['city_center'];?>" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
      <thead>
        <tr>
          <th colspan="20">店铺经营信息</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150">卖家账号：</th>
          <td><?php echo $output['joinin_detail']['seller_name'];?></td>
        </tr>
        <tr>
          <th class="w150">店铺名称：</th>
          <td><?php echo $output['joinin_detail']['store_name'];?></td>
        </tr>
        <tr>
          <th>店铺等级：</th>
          <td><?php echo $output['joinin_detail']['sg_name'];?>（开店费用：<?php echo $output['joinin_detail']['sg_price'];?> 元/年）</td>
        </tr>
        <tr>
          <th class="w150">开店时长：</th>
          <td><?php echo $output['joinin_detail']['joinin_year'];?> 年</td>
        </tr>
        <tr>
          <th>店铺分类：</th>
          <td><?php echo $output['joinin_detail']['sc_name'];?>（开店保证金：<?php echo $output['joinin_detail']['sc_bail'];?> 元）</td>
        </tr>
        <tr>
          <th>应付总金额：</th>
          <td>
          <?php if(intval($output['joinin_detail']['joinin_state']) === 10) {?>
          <input type="text" value="<?php echo $output['joinin_detail']['paying_amount'];?>" name="paying_amount" /> 元
          <?php } else { ?>
          <?php echo $output['joinin_detail']['paying_amount'];?> 元
          <?php } ?>
          </td>
        </tr>
        <tr>
          <th>经营类目：</th>
          <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" id="table_category" class="type">
              <thead>
                <tr>
                  <th>分类1</th>
                  <th>分类2</th>
                  <th>分类3</th>
                  <th>比例</th>
                </tr>
              </thead>
              <tbody>
                <?php $store_class_names = unserialize($output['joinin_detail']['store_class_names']);?>
                <?php if(!empty($store_class_names) && is_array($store_class_names)) {?>
                <?php $store_class_commis_rates = explode(',', $output['joinin_detail']['store_class_commis_rates']);?>
                <?php for($i=0, $length = count($store_class_names); $i < $length; $i++) {?>
                <?php list($class1, $class2, $class3) = explode(',', $store_class_names[$i]);?>
                <tr>
                  <td><?php echo $class1;?></td>
                  <td><?php echo $class2;?></td>
                  <td><?php echo $class3;?></td>
                  <td>
                <?php if(intval($output['joinin_detail']['joinin_state']) === 10) {?>
                  <input type="text" nctype="commis_rate" value="<?php echo $store_class_commis_rates[$i];?>" name="commis_rate[]" class="w100" /> %
                <?php } else { ?>
                <?php echo $store_class_commis_rates[$i];?> %
                <?php } ?>
                </td>
                </tr>
                <?php } ?>
                <?php } ?>
                </tbody>
        </table></td>
    </tr>
    <?php if(in_array(intval($output['joinin_detail']['joinin_state']), array(STORE_JOIN_STATE_PAY, STORE_JOIN_STATE_FINAL))) {?>
    <tr>
        <th>付款凭证：</th>
        <td><a nctype="nyroModal"  href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['paying_money_certificate']);?>"> <img src="<?php echo getStoreJoininImageUrl($output['joinin_detail']['paying_money_certificate']);?>" alt="" /> </a></td>
    </tr>
    <tr>
        <th>付款凭证说明：</th>
        <td><?php echo $output['joinin_detail']['paying_money_certificate_explain'];?></td>
    </tr>
    <?php } ?>
   <?php if($output['joinin_detail']['store_state'] != 0 && $output['joinin_detail']['store_state'] == 34) { ?>
    <tr>
           <th><span style="color:red;">* </span>开店审核意见：</th>
           <td colspan="2"><textarea id="joinin_message" class='open_joinin_message' name="joinin_message_open"></textarea></td>
       </tr>
    <?php }else if($output['joinin_detail']['store_state'] != 0){ ?>
       <tr>
        <th>开店审核意见：</th>
        <td colspan="2"><?php echo $output['joinin_detail']['joinin_message_open'];?></td>
        </tr>
    <?php } ?>
       <?php if($output['joinin_detail']['joinin_state'] == 43){?>
       <tr>
        <th><span style="color:red;">* </span>认证审核意见：</th>
        <td colspan="2"><textarea id="joinin_message" class='rz_joinin_message' name="joinin_message"></textarea></td>
        </tr>
        <?php if($_GET['is_rz'] == 1){?>
       <tr>
          <th><span style="color:red;">* </span>认证审核评估：</th>
          <td><input name="rz_evaluation_audit" type="file" class="w200" />
            <span class="block">大小请控制在2M之内。</span></td>
        </tr>
        
        <?php } }else{?>
        <tr>
        <th><span style="color:red;">* </span>认证审核意见：</th>
        <td colspan="2"><?php echo $output['joinin_detail']['joinin_message'];?></td>
        </tr>
        <tr>
            <th><span style="color:red;">* </span>供方引入评估：</th>
          <td><a href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['rz_evaluation_audit']);?>">下载附件 </a></td>
        </tr>
        
        <?php }?>
        <!-- 新增供应商类型选择操作 @Aletta -->
        <?php if($output['joinin_detail']['joinin_state'] == 43 && $output['store_joinin_num'] <= '1' && $output['supplier_type_show'] == '1'){?>
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
                </select>
        	</td>
<!--        <tr>
          <th><span style="color:red;">* </span>供应商级别：</th>
          <td><a href="<?php echo getStoreJoininImageUrl($output['joinin_detail']['rz_evaluation_audit']);?>">下载附件 </a></td>
        </tr>-->
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
        <td colspan="2">
        <?php if(!empty($output['supplier_type_data']) && is_array($output['supplier_type_data'])){?>
                <style>
                #supplier_type_data,.supplier_list{
                border-collapse: collapse;
                border: 0px solid #ccc;
                }
                #supplier_type_data .type_td {
                border-top: 0;
                border-right: 1px solid #ccc;
                border-bottom: 1px solid #ccc;
                border-left: 0;
                }
                #supplier_type_data tr.lastrow td {
                border-bottom: 0;
                }
                #supplier_type_data tr td.lastCol {
                border-right: 0;
                } 
                #supplier_type_data .supplier_list label{
	            margin-left: 20px;
                	cursor:default;
                }
                </style>
        	<table id="supplier_type_data" style="width:100%">
        		<?php foreach ($output['supplier_type_data'] as $key=>$type_val){?>
        		<tr>
        			<td class="type_td lastCol">
        				<label><b><?php echo $type_val['name']?></b></label>
                	</td>
                </tr>
                <tr <?php if(end($output['supplier_type_data']) == $type_val){?>class="lastrow"<?php }?>>
                	<td class="type_td lastCol"><table class="supplier_list">
                	<?php if(is_array($type_val['son'])){foreach ($type_val['son'] as $key_a=>$type_val_a){?>
                		<?php if($key_a%4 == 0){?><tr><?php }?>
                		<td <?php if($key_a%4 == 3){?>class="lastCol"<?php }?> style="border-top: 0;">
                		<label><?php echo $type_val_a['son_name'];?></label>
                		</td>
                		<?php if($key_a%4 == 3){?></tr><?php }?>
                	<?php }}?>
                	</table></td>
                </tr>
                <?php }?>
            </table>
         <?php }else{?>
         	尚未选择
         <?php }?>
        </td>
        </tr>
        <tr>
        	<th><span style="color:red;">* </span>供应商级别：</th>
        	<td colspan="2"><?php echo empty($output['supplier_level']) ? "尚未选择":$output['supplier_level'];?></td>
        </tr>
        <tr>
        	<th><span style="color:red;">* </span>供应商到期时间：</th>
        	<td colspan="2"><?php echo date("Y/m/d",$output['supplier_time_data']['member_time']);?>(开始)<label style="margin-left: 10px;margin-right:5px;">~</label>
        	<?php if(empty($output['supplier_time_data']['supply_end_time'])){ 
        	       echo date("Y/m/d",($output['supplier_time_data']['member_time']+(SUPPLY_TIME_LONG * 24 * 3600)));
        	   }else{ echo date("Y/m/d",$output['supplier_time_data']['supply_end_time']);}?>
        	(到期)</td>
        </tr>
        <?php }?>
        <tr>
            <th>城市公司：</th>
            <td style="color: red;"><?php echo $output['joinin_detail']['city_name'];?></td>
        </tr>
        <tr>
            <th>是否是三证合一：</th>
            <td style="color: red;"><?php if($output['joinin_detail']['is_therea'] == 1){echo "是";}else{echo "否";}?></td>
        </tr>
        <tr>
            <th>是否是一般纳税人：</th>
            <td style="color: red;"><?php if($output['joinin_detail']['is_taxpayer'] == 1){echo "是";}else{echo "否";}?></td>
        </tr>
    </tbody>
    </table>
   <?php if(in_array(intval($output['joinin_detail']['joinin_state']), array(STORE_JOIN_STATE_NEW, STORE_JOIN_STATE_RZ,STORE_JOIN_STATE_RZHKD,STORE_JOIN_STATE_PAY)) || $output['joinin_detail']['store_state'] == 34) { ?>
    <div id="validation_message" style="color:red;display:block;"></div>
    <div>
        <?php if($output['is_rz_one'] == 1){?>
             <a id="btn_no" class="btn" href="JavaScript:void(0);"><span>拒绝</span></a> 
             <a id="btn_pass" class="btn" href="JavaScript:void(0);" <?php //if(strpos($_SERVER["HTTP_USER_AGENT"],'MSIE')){;?>onclick="test()" <?php //}?> ><span>通过</span></a>
        <?php }else{?>
            <?php if($_GET['is_rz'] == 1){?>
             <input type="hidden" name="first_city_id" value="1" />
   <!--         <a id="btn_fail" class="btn" href="JavaScript:void(0);"><span>回退</span></a> -->
             <?php }else{?>
            <a id="btn_fail" class="btn" href="JavaScript:void(0);"><span>拒绝</span></a> 
             <?php }?>
             <a id="btn_no" class="btn" href="JavaScript:void(0);"><span>拒绝</span></a>
            <?php if($_GET['is_rz'] == 1){?>
            <a id="btn_pass" class="btn" href="JavaScript:void(0);"><span>通过</span></a>
            <?php }?>
        <?php }?>
    </div>
    <?php } ?>
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