<?php ?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['procurement_index_purchase_release'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=procurement&op=procurement"><span><?php echo $lang['nc_manage'];?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="pur_form" method="post">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="pur_id" value="<?php echo $output['pur']['purchase_rule_id'];?>" />
    <table class="table tb-type2 nobdb">
      <tbody>
        <tr>
          <td colspan="2" class="required"><label class="validation"><?php echo $lang['procurement_index_title'];?>: </label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['pur']['title'];?>" name="pur_title" id="pur_title" class="infoTableInput"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <!--增加字段：发布部门-->
        <tr>
          <td colspan="2" class="required"><label class="validation"><?php echo $lang['procurement_index_release_department'];?>: </label></td>
        </tr>
         <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['pur']['publish_department'];?>" name="pur_publish_department" id="pur_publish_department" class="infoTableInput"></td>
          <td class="vatop tips"></td>
        </tr>
        <!--增加字段：制度适用人员-->
        <tr>
          <td colspan="2" class="required"><label class="validation"><?php echo $lang['procurement_eidt_apply_member'];?>: </label></td>
        </tr>
         <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['pur']['object_person'];?>" name="pur_object_person" id="pur_object_person" class="infoTableInput"></td>
          <td class="vatop tips"></td>
        </tr>
        
        <tr>
          <td colspan="2" class="required"><label class="validation"><?php echo $lang['procurement_index_content'];?>: </label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php showEditor('pur_content',$output['pur']['content']);?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><?php echo $lang['procurement_index_file_upload'];?>:</td>
        </tr>
        <tr class="noborder">
            <td colspan="3" id="divComUploadContainer"><input type="file" multiple="multiple" id="fileupload" name="fileupload" /><span style = "color:red">请控制文件大小在10M以内</span></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><?php echo $lang['procurement_index_uploaded_file'];?>:</td>
        </tr>
        <tr>
          <td colspan="2" ><div class="tdare">
              <table width="600px" cellspacing="0" class="dataTable">
                <tbody id="thumbnails">
                   <?php if(is_array($output['file_upload'])){?>              
                  <tr id="<?php echo $output['file_upload']['upload_id'];?>" class="tatr2">
                    <input type="hidden" name="file_id[]" value="<?php echo $output['file_upload']['upload_id'];?>" />
                    <td><?php echo $output['file_upload']['file_name'];?></td>
                    <td><a href="javascript:del_file_upload('<?php echo$output['file_upload']['upload_id'];?>','<?php echo$output['file_upload']['item_id'];?>');"><?php echo $lang['nc_del'];?></a></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="15" ><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.iframe-transport.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.ui.widget.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.fileupload.js" charset="utf-8"></script> 
<script>
//按钮先执行验证再提交表单
$(function(){$("#submitBtn").click(function(){
    if($("#pur_form").valid()){
     $("#pur_form").submit();
	}
	});
});
//
$(document).ready(function(){
	$('#pur_form').validate({
        errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules : {
            pur_title : {
                required   : true
            },
            pur_content : {
                required   : true
            },
            pur_publish_department : {
                required   : true
            },
            pur_object_person : {
                required   : true
            }
        },
        messages : {
            pur_title : {
                required   : '<?php echo $lang['procurement_index_title_null'];?>'
            },
            pur_content : {
                required   : '<?php echo $lang['procurement_index_content_null'];?>'
            },
            pur_publish_department : {
                required   : "发布部门不能为空"
            },
            pur_object_person : {
                required   : "制度适用人员不能为空"
            }
        }
    });
    // 附件上传
    $('#fileupload').each(function(){
          $(this).fileupload({
            dataType: 'json',
            url: 'index.php?act=procurement&op=procurement_pic_upload&item_id=<?php echo $output['pur']['purchase_rule_id'];?>',
            done: function (e,data) {
             if(data.result.state=="1"){
                    alert(data.result.msg);
             }else if(data.result.state=="0"){          
                    if(data != 'error'){
                    add_uploadedfile(data.result);
                    }
             }
            }
        });
    });
});
function add_uploadedfile(file_data)
{
    //重复上传替换时，删除之前上传的文件
    var is_null = '<?php echo$output['file_upload']['upload_id'];?>';
    if(is_null!=""){
        $.getJSON('index.php?act=procurement&op=ajax&branch=del_file_upload&file_id='+'<?php echo$output['file_upload']['upload_id'];?>'+'&item_id='+'<?php echo$output['file_upload']['item_id'];?>', function(result){
    });
    }
    var newImg = '<tr id="' + file_data.file_id + '" class="tatr2"><input type="hidden" name="file_id[]" value="' + file_data.file_id + '" /><td><td>' + file_data.file_name + '</td><td><a href="javascript:del_file_upload(' + file_data.file_id +','+ file_data.item_id+');"><?php echo $lang['nc_del'];?></a></td></tr>';
    $('#thumbnails').html(newImg);
}
function del_file_upload(file_id,item_id)
{
    if(!window.confirm('<?php echo $lang['nc_ensure_del'];?>')){
        return;
    }
    $.getJSON('index.php?act=procurement&op=ajax&branch=del_file_upload&file_id=' + file_id +'&item_id='+item_id, function(result){
        if(result){
            $('#' + file_id).remove();
        }else{
            alert('<?php echo $lang['procurement_index_del_fail'];?>');
        }
    });
}
</script>