<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['store_type'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=store_type&op=store_type"><span><?php echo $lang['manage'];?></span></a></li>
        <li><a href="JavaScript:void(0);" class="current"><span><?php echo $lang['nc_new'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="store_type_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="st_name"><?php echo $lang['store_type_name'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="" name="st_name" id="st_name" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>

        <tr>
          <td colspan="2" class="required"><label for="st_sort"><?php echo $lang['nc_sort'];?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="255" name="st_sort" id="st_sort" class="txt"></td>
          <td class="vatop tips"><?php echo $lang['update_sort'];?></td>
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
    if($("#store_type_form").valid()){
     $("#store_type_form").submit();
	}
	});
});

$(document).ready(function(){
	$('#store_type_form').validate({
        errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },

        rules : {
            st_name : {
                required : true,
                remote   : {                
                url :'index.php?act=store_type&op=ajax&branch=check_class_name',
                type:'get',
                data:{
                    st_name : function(){
                        return $('#st_name').val();
                    }
                  }
                }
            },
            st_sort : {
                number   : true
            }
        },
        messages : {
            st_name : {
                required : '<?php echo $lang['store_type_name_no_null'];?>',
                remote   : '<?php echo $lang['store_type_name_is_there'];?>'
            },
            st_sort  : {
                number   : '<?php echo $lang['store_type_sort_only_number'];?>'
            }
        }
    });
});
</script>