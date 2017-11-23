<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['city_setting'];?></h3>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <?php if($_GET['op'] == 'add'){?>
  <form id="admin_form" method="post" action='index.php?act=city_manges&op=add'>
   <?php }else{?>
  <form id="admin_form" method="post" action='index.php?act=city_manges&op=edit&id=<?php echo $output['city']['id'];?>'>
    <?php }?>
   <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <tbody>
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2"><?php echo $lang['city_name']; ?>:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="city_name" class="txt" value="<?php echo $output['city']['city_name'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
       
<!--        <tr>
          <td colspan="2" class="required"><label for="new_pw"><?php echo $lang['city_state']; ?>:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform">
                <input  value="1" <?php if($output['city']['city_state'] == 1){echo "checked='checked'";}?> name="city_state"  type="radio">正常
               <input value="2" <?php if($output['city']['city_state'] == 2){echo "checked='checked'";}?> name="city_state" type="radio">关闭
            </td>
           <td class="vatop tips"><?php echo $lang['admin_edit_pwd_tip1'];?></td>
        </tr>-->
        <tr>
          <td colspan="2" class="required"><labe for="new_pw2"><?php echo $lang['city_back']; ?>:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="back" class="txt" value="<?php echo $output['city']['back'];?>" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required">
              <label class="required" for="gadmin_name"><?php echo $lang['city_bukrs'];?>:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input id="new_pw2" name="bukrs" value="<?php echo $output['city']['bukrs'];?>" class="txt" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="required" for="gadmin_name"><?php echo $lang['city_comtxt'];?>:</label></td>
        </tr>
        <tr class="noborder">
         <td class="vatop rowform"><input id="new_pw2" name="comtxt" value="<?php echo $output['city']['comtxt'];?>" class="txt" type="text"></td>
          <td class="vatop tips"></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="2" ><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script>
//按钮先执行验证再提交表单
$(function(){$("#submitBtn").click(function(){
    if($("#admin_form").valid()){
     $("#admin_form").submit();
	}
	});
});
$(document).ready(function(){
	$("#admin_form").validate({
		errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules : {
        	new_pw : {
				minlength: 6,
				maxlength: 20
            },
            new_pw2 : {
				minlength: 6,
				maxlength: 20,
				equalTo: '#new_pw'
            },
            gid : {
                required : true
            }
        },
        messages : {
        	new_pw : {
				minlength: '<?php echo $lang['admin_add_password_max'];?>',
				maxlength: '<?php echo $lang['admin_add_password_max'];?>'
            },
            new_pw2 : {
				minlength: '<?php echo $lang['admin_add_password_max'];?>',
				maxlength: '<?php echo $lang['admin_add_password_max'];?>',
				equalTo:   '<?php echo $lang['admin_edit_repeat_error'];?>'
            },
            gid : {
                required : '<?php echo $lang['admin_add_gid_null'];?>',
            }
        }
	});
});
</script>