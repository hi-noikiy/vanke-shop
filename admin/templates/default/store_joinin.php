<?php ?>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['store'];?></h3>
      <ul class="tab-base">
	<li><a <?php if($_GET['op'] == "store_joinin2"){?> href="JavaScript:void(0);" class="current" <?php }else{?>  href="index.php?act=store&op=store_joinin2" <?php } ?> ><span>认证申请审核</span></a></li>
        <li><a <?php if($_GET['op'] == "store_joinin"){?> href="JavaScript:void(0);" class="current" <?php }else{?>  href="index.php?act=store&op=store_joinin" <?php } ?> ><span><?php echo $lang['pending'];?>审核</span></a></li>
        <li><a href="index.php?act=store_edit&op=index" ><span>修改供应商审核</span></a></li>
        <li><a href="index.php?act=store&op=newshop_add" ><span>新增供应商</span></a></li>
        <li><a href="index.php?act=store&op=newtemporary_add" ><span>新增临时供应商</span></a></li>
        <li><a href="index.php?act=store&op=type_level_list" ><span>供应商类型级别修改</span></a></li>
          <li><a href="index.php?act=store&op=store_type_edit" ><span>供应商店铺类型修改</span></a></li>
      	  <li><a href="index.php?act=store&op=store_push_list" ><span>手动推送合同</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" name="formSearch">
    <input type="hidden" value="store" name="act">
    <?php if($_GET['op'] == "store_joinin2"){?>
    <input type="hidden" value="store_joinin2" name="op">
    <?php }else{?>
    <input type="hidden" value="store_joinin" name="op">
    <?php } ?>
    <table class="tb-type1 noborder search">
      <tbody>
        <tr>
          <th><label for="store_name"><?php echo $lang['store_name'];?></label></th>
          <td><input type="text" value="<?php echo $output['store_name'];?>" name="store_name" id="store_name" class="txt"></td>
          <th><label for="owner_and_name">供应商账号</label></th>
          <td><input type="text" value="<?php echo $output['owner_and_name'];?>" name="owner_and_name" id="owner_and_name" class="txt"></td>
          <!--<th><label><?php /*echo $lang['belongs_level'];*/?></label></th>
          <td><select name="grade_id">
              <option value=""><?php /*echo $lang['nc_please_choose'];*/?>...</option>
              <?php /*if(!empty($output['grade_list']) && is_array($output['grade_list'])){ */?>
              <?php /*foreach($output['grade_list'] as $k => $v){ */?>
              <option value="<?php /*echo $v['sg_id'];*/?>" <?php /*if($v['sg_id']==$_GET['grade_id']) { echo 'selected'; }*/?>><?php /*echo $v['sg_name'];*/?></option>
              <?php /*} */?>
              <?php /*} */?>
            </select></td>-->
            <th><label>认证状态</label></th>
            <td>
                <select name="joinin_state">
                    <option value=""><?php echo $lang['nc_please_choose'];?>...</option>
                    <?php if(!empty($output['joinin_state_array']) && is_array($output['joinin_state_array'])){ ?>
                    <?php foreach($output['joinin_state_array'] as $k => $v){ ?>
                    <option value="<?php echo $k;?>" <?php if($k==$_GET['joinin_state']) { echo 'selected'; }?>><?php echo $v;?></option>
                    <?php } ?>
                    <?php } ?>
                </select>
            </td>
            
                    <!--城市中心-->
            <th><label>城市中心</label></th>
                <td>
                     <select name="city_id" class="querySelect">
                          <option value=""><?php echo $lang['nc_please_choose'];?></option>
                         <?php if(count($output['city_centreList'])>0){?>            
                         <?php foreach($output['city_centreList'] as $city_centre){?>                                        
                         <option value ="<?php echo $city_centre['id'];?>" <?php if($_GET['city_id'] == $city_centre['id']) echo 'selected'; ?>  ><?php echo $city_centre['city_name'];?></option>                
                         <?php } }?>
                        </select>
                 </td>
            
            <td><a href="javascript:document.formSearch.submit();" class="btn-search " title="<?php echo $lang['nc_query'];?>">&nbsp;</a>
                <?php if($output['owner_and_name'] != '' or $output['store_name'] != '' or $output['grade_id'] != ''){?>
                <a href="index.php?act=store&op=store_joinin2" class="btns " title="<?php echo $lang['nc_cancel_search'];?>"><span><?php echo $lang['nc_cancel_search'];?></span></a>
                <?php }?></td>
        </tr>
        </tbody>
    </table>
</form>
<table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th colspan="12"><div class="title">
            <h5><?php echo $lang['nc_prompts'];?></h5>
            <span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td><ul>
            <li>点击审核按钮可以对开店申请进行审核，点击查看按钮可以查看开店信息</li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <form method="post" id="store_form" name="store_form">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="type" id="type" value="" />
    <table class="table tb-type2">
      <thead>
        <tr class="thead">
          <th><?php echo $lang['store_name'];?></th>
          <th><?php echo $lang['store_user_name'];?></th>
          <th><?php echo $lang['location'];?></th>
          <!-- 所属等级 -->
<!--          <th class="align-center"><?php echo $lang['belongs_level'];?></th>-->
          <th class="align-center"><?php echo $lang['state'];?></th>
          <th class="align-center">城市公司</th>
          <th class="align-center"><?php echo $lang['operation'];?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['store_list']) && is_array($output['store_list'])){ ?>
        <?php foreach($output['store_list'] as $k => $v){ ?>
        <tr class="hover edit">
          <td><?php echo $v['company_name'];?></td>
          <td><?php echo $v['member_name'];?></td>
          <td class="w150"><?php echo $v['company_address'];?></td>
          <!-- 所属等级 -->
<!--          <td class="align-center"><?php echo $v['sg_name'];?></td>-->
          <td class="align-center"><?php if($_GET['op'] == "store_joinin"){if($v['store_state']==34){echo "开店申请";}else if($v['store_state']==40){echo "开店成功";}else{echo "开店失败";}}else{echo $output['joinin_state_array'][$v['joinin_state']];}?></td>
          <td class="align-center"><?php echo $v['city_center_name'];?></td>
          <td class="w72 align-center">
              <?php if(in_array(intval($v['joinin_state']), array(STORE_JOIN_STATE_RZ,STORE_JOIN_STATE_RZHKD))) { ?>
              <a href="index.php?act=store&op=store_joinin_detail<?php if($_GET['op'] == "store_joinin2"){ echo "&is_rz=1";}?>&member_id=<?php echo $v['member_id'];?>&city=<?php echo $v['city_center'];?>">审核</a>
              <?php } else if($v['store_state'] == 34 && $_GET['op'] != 'store_joinin2') { ?>
              <a href="index.php?act=store&op=store_joinin_detail<?php if($_GET['op'] == "store_joinin2"){ echo "&is_rz=1";}?>&member_id=<?php echo $v['member_id'];?>&city=<?php echo $v['city_center'];?>">审核</a>
              <?php } else { ?>
              <a href="index.php?act=store&op=store_joinin_detail&member_id=<?php echo $v['member_id'];?>&city=<?php echo $v['city_center'];?>">查看</a>
              <?php } ?>
               <?php if(intval($v['joinin_state'])<40) { ?>
              &nbsp;&nbsp; <a href="index.php?act=store&op=del_join&id=<?php echo $v['member_id']?>&city=<?php echo $v['city_center'];?>&city=<?php echo $v['city_center'];?>">删除</a>
               <?php } ?>
              <?php if(intval($v['joinin_state'])==44) { ?>
                  &nbsp;&nbsp; <a href="index.php?act=store&op=supplier_edit&member_id=<?php echo $v['member_id']?>&city=<?php echo $v['city_center'];?>&city=<?php echo $v['city_center'];?>">编辑</a>
              <?php } ?>

          </td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr class="no_data">
          <td colspan="10"><?php echo $lang['nc_no_record'];?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td></td>
          <td colspan="15">
              <?php if(!empty($output['store_list']) && is_array($output['store_list'])){ ?>
              <div class="pagination"><?php echo $output['page'];?></div>
              <?php } ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.edit.js" charset="utf-8"></script>
<script>
function audit_submit(type){
	$('#type').val(type);
	$("#store_form").submit();
	return true;
}
</script>
