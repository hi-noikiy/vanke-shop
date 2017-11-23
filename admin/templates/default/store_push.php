<?php ?>
  <div class="fixed-bar">
    <div class="item-title">
      <h3>手动推送</h3>
      <ul class="tab-base">
		<li><a href="index.php?act=store&op=store_joinin2"><span>认证申请审核</span></a></li>
        <li><a href="index.php?act=store&op=store_joinin"><span>审核</span></a></li>
        <li><a href="index.php?act=store_edit&op=index" ><span>修改供应商审核</span></a></li>
        <li><a href="index.php?act=store&op=newshop_add" ><span>新增供应商</span></a></li>
        <li><a href="index.php?act=store&op=newtemporary_add" ><span>新增临时供应商</span></a></li>
        <li><a href="index.php?act=store&op=type_level_list" ><span>供应商类型级别修改</span></a></li>
        <li><a href="index.php?act=store&op=store_type_edit" ><span>供应商店铺类型修改</span></a></li>
        <li><a <?php if($_GET['op'] == "store_push_list"){?> href="JavaScript:void();" class="current" <?php }else{?>  href="index.php?act=store&op=store_push_list" <?php } ?> ><span>手动推送合同</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" name="formSearch">
    <table class="tb-type1 noborder search">
      <tbody>
        <tr style="height: 50px;">
          <th><label for="store_name" style="margin-left: 20px;">供应商</label></th>
          <td><input type="text" value="<?php echo $output['show_where']['supplier_name']?>" name="supplier_name" id="supplier_name" class="txt" ></td>
          <th><label for="owner_and_name">供应商账号</label></th>
          <td><input type="text" value="<?php echo $output['show_where']['supplier_id']?>" name="supplier_id" id="supplier_id" class="txt"></td>
            <th><label>推送状态</label></th>
            <td>
                <select id="push_state">   
               		<option value="">请选择推送状态</option>               		
               		<?php if ($output['show_where']['push_state'] == 2){?>
               		<option value="2" selected = "selected">推送失败</option>
               		<?php }else {?>
               		<option value="2">推送失败</option>
               		<?php }?>
               		<?php if ($output['show_where']['push_state'] == 3){?>
               		<option value="3" selected = "selected">未推送</option>
               		<?php }else {?>
               		<option value="3">未推送</option>
               		<?php }?>          
                </select> 
            </td>        
             <th><label>城市公司</label></th>
            <td>
                <select id="supplier_cityid">
                    <option value="">请选择城市公司</option>
                    <?php if (!empty($output['citys_list'])){?>
                    <?php foreach ($output['citys_list'] as $city){?>
                    <option value="<?php echo $city['id'];?>" <?php if($output['show_where']['supplier_cityid'] == $city['id']) echo 'selected'; ?> ><?php echo $city['city_name'];?></option>
                    <?php }?>
                    <?php }?>
                </select>
            </td>              
            <td class="align-right">      
			<a href="" class="btn-search " id="subm" onclick="get_url()">&nbsp;</a>          	
			</td>
        </tr>
        </tbody>
    </table>
    <table class="table tb-type2 nobdb">
    <thead>
      <tr class="thead">
        <th class="align-center" style="width: 25%;">供应商</th>
        <th class="align-center" style="width: 15%;">供应商账号</th>
        <th class="align-center" style="width: 15%;">所在地</th>
        <th class="align-center" style="width: 15%;">状态</th>
        <th class="align-center" style="width: 15%;">城市公司</th>
        <th class="align-center" style="width: 15%;">操作</th>
      </tr>
    </thead>
    <tbody id="datatable">
      <?php if(!empty($output['push_result_list'])){ ?>
      <?php foreach ($output['push_result_list'] as $k=>$v){ ?>
      <tr class="hover">
        <td class="align-center"><?php echo $v['company_name'];?></td>
        <td class="align-center"><?php echo $v['member_name']; ?></td>
        <td class="align-center"><?php echo $v['member_areainfo']; ?></td>
        <?php if ($v['contract_type'] == 1){?>
        <td class="align-center"><?php echo '推送成功';?></td>
        <?php }elseif ($v['contract_type'] == 2){?>
        <td class="align-center"><?php echo '推送失败';?></td>
        <?php }else{?>
        <td class="align-center"><?php echo '未推送';?></td>       
        <?php }?>
        <td class="align-center"><?php echo $output['citys_name'][$k]['city_name'];?></td>
        <?php if (intval($v['contract_type']) == 1){?>
        <td class="align-center"></td>
      <?php }elseif (intval($v['contract_type']) == 2){?>
        <td class="align-center"><a href="index.php?act=store&op=store_push_contract&member_id=<?php echo $v['member_id'];?>&city_center=<?php echo $v['city_center'];?>&first_city_start=<?php echo $v['first_city_id'];?>">请重新推送</a></td>
      <?php }elseif (intval($v['contract_type']) == 3){?>
        <td class="align-center"><a href="index.php?act=store&op=store_push_contract&member_id=<?php echo $v['member_id'];?>&city_center=<?php echo $v['city_center'];?>&first_city_start=<?php echo $v['first_city_id'];?>">请推送</a></td>
      <?php }else{?>
        <td class="align-center">未知错误！</td>      
      <?php }?>
      </tr>
      <?php } ?>
      <?php }else { ?>
      <tr class="no_data">
        <td colspan="15"><?php echo $lang['nc_no_record'];?></td>
      </tr>
      <?php } ?>
    </tbody>
    <?php if(!empty($output['push_result_list']) && is_array($output['push_result_list'])){ ?>
    <tfoot>
      <tr class="tfoot">
        <td colspan="15" id="dataFuncs"><div class="pagination"> <?php echo $output['show_page'];?> </div></td>
      </tr>
    </tfoot>
    <?php } ?>
  </table>
</form>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.edit.js" charset="utf-8"></script>
<script type="text/javascript">
function get_url(){
	var name = document.getElementById("supplier_name").value;  
	var id = document.getElementById("supplier_id").value;
	var state = document.getElementById("push_state").value;
	var cityid = document.getElementById("supplier_cityid").value;
	document.getElementById("subm").href='index.php?act=store&op=store_push_list&supplier_name='+name+'&supplier_id='+id+'&push_state='+state+'&supplier_cityid='+cityid;	   
}
</script>
