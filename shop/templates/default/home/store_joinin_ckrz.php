<?php ?>
<style>
table {
  background: white;
  border-collapse: collapse;
  margin: 1.25em 0 0;
  width: 100%;
}

table tr,
table th,
table td {
  border: none;
  border-bottom: 1px solid #e4ebeb;
  /*font-family: 'Lato', sans-serif;*/
  font-size: .875rem;
}

table th,
table td {
  padding: 10px 12px;
  text-align: center;
}

table th {
  background: #56a2cf;
  color: #ffffff;
  text-transform: uppercase;
}

table tr td {
  background: #fff;
  color: #999999;
}



table.bt tfoot th,
table.bt tfoot td,
table.bt tbody td {
  font-size: .8125rem;
  padding: 0;
}

table.bt tfoot th:before,
table.bt tfoot td:before,
table.bt tbody td:before {
  background: #56a2cf;
  color: white;
  margin-right: 10px;
  padding: 2px 10px;
}

table.bt tfoot th .bt-content,
table.bt tfoot td .bt-content,
table.bt tbody td .bt-content {
  display: inline-block;
  padding: 2px 5px;
}

table.bt tfoot th:first-of-type:before,
table.bt tfoot th:first-of-type .bt-content,
table.bt tfoot td:first-of-type:before,
table.bt tfoot td:first-of-type .bt-content,
table.bt tbody td:first-of-type:before,
table.bt tbody td:first-of-type .bt-content {
  padding-top: 10px;
}

table.bt tfoot th:last-of-type:before,
table.bt tfoot th:last-of-type .bt-content,
table.bt tfoot td:last-of-type:before,
table.bt tfoot td:last-of-type .bt-content,
table.bt tbody td:last-of-type:before,
table.bt tbody td:last-of-type .bt-content {
  padding-bottom: 10px;
}
table.two-axis tr td:first-of-type {
  background: #cadde1;
}

@media only screen and (max-width: 568px) {
  table.two-axis tr td:first-of-type,
  table.two-axis tr:nth-of-type(2n+2) td:first-of-type,
  table.two-axis tr td:first-of-type:before {
    background: #3584b3;
    color: #ffffff;
  }

  table.two-axis tr td:first-of-type {
    border-bottom: 1px solid #e4ebeb;
  }

  table.two-axis tr td:first-of-type:before {
    padding-bottom: 10px;
  }
}
</style>
<div id="apply_company_info" class="apply-company-info">
  	  <table id="table-two-axis" class="two-axis">
  	  <thead>
		  <tr>
			<th colspan='7'><span style="text-align:center;display:block;font-size:20px;"><strong><?php echo $output['stor_info']['company_name'];?></strong></span></th>
		  </tr>
		</thead>
		<thead>
		  <tr>
			<th>城市公司</th>
			<th>联系人</th>
            <th>联系人电话</th>
			<th>申请状态</th>
			<th>操作</th>
			<th>店铺状态</th>
			<th>操作</th>
		  </tr>
		</thead>
		<tbody>
		<?php if(!empty($output['log']) && is_array($output['log'])){?>
			<?php foreach ($output['log'] as $vl){?>
    		  <tr>
    			<td><?php echo $vl['city_name'];?></td>
    			<td <?php if($vl['first_city_id'] == $vl['city_center']){?>style='background: #eaf3f5;'<?php }?>>
                    <?php echo empty($vl['city_contacts_name']) ? $output['stor_info']['contacts_name']:$vl['city_contacts_name'];?></td>
                <td <?php if($vl['first_city_id'] == $vl['city_center']){?>style='background: #eaf3f5;'<?php }?>>
                      <?php echo empty($vl['city_contacts_phone']) ? $output['stor_info']['contacts_phone']:$vl['city_contacts_phone'];?></td>
    			<td <?php if($vl['first_city_id'] == $vl['city_center']){?>style='background: #eaf3f5;'<?php }?>>
        			<?php if($vl['joinin_state'] == STORE_JOIN_STATE_RZ){?>认证中
                    <?php }else if($vl['joinin_state'] == STORE_JOIN_STATE_RZSUCCESS){?> 认证成功
                    <?php }else if($vl['joinin_state'] == STORE_JOIN_STATE_EMAIL){?> 未认证邮箱
                    <?php }else if($vl['joinin_state'] == STORE_JOIN_STATE_FNO){?>认证拒绝
                    <?php }else{?>认证回退
                    <?php }?>
    			</td>
    			<td <?php if($vl['first_city_id'] == $vl['city_center']){?>style='background: #eaf3f5;'<?php }?>>
    				 <?php if($vl['first_city_id'] == $vl['city_center'] && ($output['edit_state'] == '44' || empty($output['edit_state']))){?>

    				 <a style="color: #999999;" href="<?php echo BASE_SITE_URL;?>/shop/index.php?act=store_joinin&op=ckedit&id=<?php echo $vl['city_center'];?>">编辑</a>
    				 &nbsp;&nbsp;|&nbsp;&nbsp;
    				 <a style="color: #999999;" href="<?php echo BASE_SITE_URL;?>/shop/index.php?act=store_joinin&op=ckinfo&id=<?php echo $vl['city_center'];?>">查看</a>
    				 <?php }else{?>
    				 <a style="color: #999999;" href="<?php echo BASE_SITE_URL;?>/shop/index.php?act=store_joinin&op=ckinfo&id=<?php echo $vl['city_center'];?>">查看</a>
    				 <?php }?>
    			</td>
    			<td <?php if($vl['first_city_id'] == $vl['city_center']){?>style='background: #eaf3f5;'<?php }?>>
    				<?php if($vl['store_state'] == STORE_JOIN_STATE_RZHKD){?>审核中
                    <?php }else if($vl['store_state'] == STORE_JOIN_STATE_FINAL){?> 开店成功
                    <?php }else if($vl['store_state'] == STORE_JOIN_STATE_KDJJ){?>开店拒绝
                    <?php }else{?>尚未开店
                    <?php }?>
    			</td>
    			<td <?php if($vl['first_city_id'] == $vl['city_center']){?>style='background: #eaf3f5;'<?php }?>>
    				<?php if($vl['store_state'] == STORE_JOIN_STATE_RZHKD){?>店铺审核中
                    <?php }else if($vl['store_state'] == STORE_JOIN_STATE_FINAL){ echo $vl['store_name'];?>
                    <?php }else{?>
                    	<a style="color: #999999;" href="<?php echo BASE_SITE_URL;?>/shop/index.php?act=store_join&op=ecrz">申请开店</a>
                    <?php }?>
    			</td>
    		  </tr>
		<?php }}?>
		</tbody>
	  </table>
</div>
