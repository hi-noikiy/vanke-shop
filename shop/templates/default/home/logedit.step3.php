<?php ?>

<!-- 公司信息 v3-10 简化 -->

<div id="apply_company_info" class="apply-company-info">
     <table border="0" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
          <th colspan="2">公司及联系人信息</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>公司名称：</th>
          <td><p><?php echo $output['data_rz']['company_name'];?></p></td>
        </tr>
        <tr>
          <th>公司所在地：</th>
          <td  id="prov"><?php echo $output['data_rz']['company_address'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>公司详细地址：</th>
          <td><?php echo $output['data_rz']['company_address_detail'];?>
            <span></span></td>
        </tr>
	
        <tr>
          <th>公司电话：</th>
          <td><?php echo $output['data_rz']['company_phone'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>员工总数：</th>
          <td><?php echo $output['data_rz']['company_employee_count'];?>
            &nbsp;人 <span></span></td>
        </tr>
        <tr>
          <th>注册资金：</th>
          <td><?php echo $output['data_rz']['company_registered_capital'];?>
            &nbsp;万元<span></span></td>
        </tr>
        <tr>
          <th>联系人姓名：</th>
          <td><?php echo $output['data_rz']['contacts_name'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>联系人手机：</th>
          <td><?php echo $output['data_rz']['contacts_phone'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>电子邮箱：</th>
          <td><?php echo $output['data_rz']['contacts_email'];?>
            <span></span></td>
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
        <tr>
          <th>城市公司所在地：</th>
          <td  id="prov_2">
            <?php foreach($output['city'] as $rows){ ?>
             <?php if($output['data_rz']['city_center'] == $rows['id']){echo $rows['city_name'];}?>
            <?php } ?>
              <span></span>
           </td>
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
            <th colspan="20">营业执照信息（副本）<em>注：如果是三证合一，则不需要上传一般纳税人证明！</em></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>营业执照号：</th>
          <td><?php echo $output['data_rz']['business_licence_number'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>营业执照所在地：</th>
          <td><?php echo $output['data_rz']['business_licence_address'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>营业执照有效期：</th>
          <td><?php echo $output['data_rz']['business_licence_start'];?>
            <span></span>-
            <?php echo $output['data_rz']['business_licence_end'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>经营范围：</th>
          <td><?php echo $output['data_rz']['business_sphere'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>营业执照电子版：</th>
          <td><img style="width:300px;" src="<?php echo getStoreJoininImageUrl($output['data_rz']['business_licence_number_electronic']);?>">
            </td>
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
          <th colspan="20">是否是三证合一</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>三证合一：</th>
          <td><?php if($output['data_rz']['is_therea'] == 1){echo "是";}else{echo '否';}?>
            <span></span></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
      
      
    <table border="0" id="organization_table" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
          <th colspan="20">组织机构代码证</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>组织机构代码：</th>
          <td><?php echo $output['data_rz']['organization_code'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>组织机构代码证电子版：</th>
          <td>
              <img style="width:300px;" src="<?php echo getStoreJoininImageUrl($output['data_rz']['organization_code_electronic']);?>">
          </td>
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
          <th colspan="20">一般纳税人证明<em>注：所属企业具有一般纳税人证明时，此项为必填。</em></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150">一般纳税人证明：</th>
          <td>
              <img  style="width:300px;" src="<?php echo getStoreJoininImageUrl($output['data_rz']['general_taxpayer']);?>"></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
      
      <table border="0" id="registration_certificate_table" cellpadding="0" cellspacing="0" class="all" style="display: block;">
      <thead>
        <tr>
          <th colspan="20">税务登记证</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150">税务登记证号：</th>
          <td><?php echo $output['data_rz']['tax_registration_certificate'];?>
              <span></span></td>
        </tr>
	<!-- v3-10 简化-->
        <tr>
          <th>纳税人识别号：</th>
          <td><?php echo $output['data_rz']['taxpayer_id'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>税务登记证号电子版：</th>
          <td>
               <img style="width:300px;" src="<?php echo getStoreJoininImageUrl($output['data_rz']['tax_registration_certificate_electronic']);?>">
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
      
</div>
<?php ?>

<!-- 公司资质 -->

<div id="apply_credentials_info" class="apply-credentials-info">
    <table border="0" cellpadding="0" cellspacing="0" class="all">
      <thead>
        <tr>
          <th colspan="20">开户银行信息</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150">银行开户名：</th>
          <td><?php echo $output['data_rz']['bank_account_name'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>公司银行账号：</th>
          <td><?php echo $output['data_rz']['bank_account_number'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>开户银行支行名称：</th>
          <td><?php echo $output['data_rz']['bank_name'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>支行联行号：</th>
          <td><?php echo $output['data_rz']['bank_code'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>开户银行所在地：</th>
          <td><?php echo $output['data_rz']['bank_address'];?>
            <span></span></td>
        </tr>
        <tr>
          <th>开户银行许可证电子版：</th>
          <td>
              <img style="width:300px;" src="<?php echo getStoreJoininImageUrl($output['data_rz']['bank_licence_electronic']);?>">
          </td>
        </tr> 
      </tbody>
      <tfoot>
        <tr>
          <td colspan="20">&nbsp;</td>
        </tr>
      </tfoot>
    </table>
    <div id="div_settlement" style="display: none;">
      <table border="0" cellpadding="0" cellspacing="0" class="all">
        <thead>
          <tr>
            <th colspan="20">结算账号信息</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th class="w150">银行开户名：</th>
            <td><?php echo $output['data_rz']['settlement_bank_account_name'];?>
              <span></span></td>
          </tr>
          <tr>
            <th>公司银行账号：</th>
            <td><?php echo $output['data_rz']['settlement_bank_account_number'];?>
              <span></span></td>
          </tr>
          <tr>
            <th>开户银行支行名称：</th>
            <td><?php echo $output['data_rz']['settlement_bank_name'];?>
              <span></span></td>
          </tr>
	  <!--  v3-10 简化-->
          <!--tr>
            <th>支行联行号：</th>
            <td><input id="settlement_bank_code" name="settlement_bank_code" type="text" class="w200"/>
              <span></span></td>
          </tr-->
          <tr>
            <th>开户银行所在地：</th>
            <td><?php echo $output['data_rz']['settlement_bank_address'];?>
              <span></span></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="20">&nbsp;</td>
          </tr>
        </tfoot>
      </table>
    </div>
    
</div>
