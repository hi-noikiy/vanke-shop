<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['agent_grade'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=agent_grade&op=agent_grade" ><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['nc_new'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="grade_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <tbody>

        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="sg_name"><?php echo $lang['agent_grade_name'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="" id="sg_name" name="sg_name" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
            <!-- <td colspan="2" class="required"><label><?php echo $lang['nc_sort'];?>:</label></td> -->
            <td colspan="2" class="required"><label class="validation"><?php echo $lang['grade_sortname']; //级别?>: </label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input type="text" id="sg_sort" name="sg_sort" class="txt"></td>
            <td class="vatop tips"><?php echo $lang['grade_sort_tip']; //数值越大表明级别越高?></td>
        </tr>

        <tr>
          <td colspan="2" class="required"><label class="validation" for="sg_price"><?php echo $lang['charges_standard'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="" id="sg_price" name="sg_price" class="txt"></td>
          <td class="vatop tips"><?php echo $lang['charges_standard_notice'];?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="sg_description"><?php echo $lang['application_note'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><textarea rows="6" class="tarea" id="sg_description" name="sg_description"></textarea></td>
          <td class="vatop tips"><?php echo $lang['application_note_notice'];?></td>
        </tr>



      <tr>
          <td colspan="2" class="required"><label class="validation">分润点: </label></td>
      </tr>
      <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="ag_rate" name="ag_rate" class="txt"></td>
          <td class="vatop tips">销售佣金的提成比例, 百分比数值</td>
      </tr>



      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="15"><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script>
//按钮先执行验证再提交表单
$(function(){$("#submitBtn").click(function(){
    if($("#grade_form").valid()){
     $("#grade_form").submit();
	}
	});
});
//
$(document).ready(function(){
	$('#grade_form').validate({
        errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },

        rules : {
            sg_name : {
                required : true,
                remote   : {
                url :'index.php?act=agent_grade&op=ajax&branch=check_grade_name',
                type:'get',
                data:{
                        sg_name : function(){
                        	return $('#sg_name').val();
                        },
                        sg_id  : ''
                    }
                }
            },
			sg_price : {
                required  : true,
                number : true,
                min : 0
            },
            ag_rate : {
                digits  : true
            },
            sg_space_limit : {
                digits : true
            },
            sg_sort : {
            	required  : true,
                digits  : true,
                min : 1,
                remote   : {
	                url :'index.php?act=agent_grade&op=ajax&branch=check_grade_sort',
	                type:'get',
	                data:{
	                        sg_sort : function(){
	                        	return $('#sg_sort').val();
	                        },
	                        sg_id  : ''
	                    }
                }
            }
        },
        messages : {
            sg_name : {
                required : '<?php echo $lang['agent_grade_name_no_null'];?>',
                remote   : '<?php echo $lang['now_agent_grade_name_is_there'];?>'
            },
			sg_price : {
                required  : "<?php echo $lang['charges_standard_no_null'];?>",
                number : "<?php echo $lang['charges_standard_no_null'];?>",
                min : "<?php echo $lang['charges_standard_no_null'];?>"
            },
            ag_rate : {
                digits : '<?php echo $lang['only_lnteger'];?>'
            },
            sg_space_limit : {
                digits  : '<?php echo $lang['only_lnteger'];?>'
            },
            sg_sort  : {
            	required : '<?php echo $lang['grade_add_sort_null_error']; //级别信息不能为空?>',
                digits   : '<?php echo $lang['only_lnteger'];?>',
                remote   : '<?php echo $lang['add_gradesortexist']; //级别已经存在?>'
            }
        }
    });
});
</script>
