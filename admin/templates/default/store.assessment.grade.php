<?php ?>

<div class="page2" style='height:100%;'>
    <table class="table tb-type2 nobdb" id ="fwin_grade_shan">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label>供应商评级:</label></td>
        </tr>
        <tr class="noborder">
          <td><select id='grade_shan'>
                  <option value="1" >优秀供应商</option>
                  <option value="2" >合格供应商</option>
                  <option value="3" >淘汰供应商</option>
              </select>
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="2">
              <a href="javascript:void(0);" class="btn" nctype="btn_submit">
                  <span>
                      <?php echo $lang['nc_submit'];?>
                  </span>
              </a>
          </td>
        </tr>
      </tfoot>
    </table>
</div>
<script>
    $(function(){
        $('.btn').click(function(){
            var shan_id = $('#grade_shan option:selected').val();
            $.post(
                '<?php echo ADMIN_SITE_URL;?>/index.php?act=assessment&op=grade_shan&id=<?php echo $_GET['id'];?>&member_id=<?php echo $_GET['member_id'];?>',
                {
                   shan_id:shan_id,
                },
                function(data){
                    if(data == 0){
                        alert('您没有操作当前供应商的权限');return false;
                    }else if(data == 1){
                        alert('评级成功！');
                        $('#fwin_grade_shan').hide();
                        $('#dialog_manage_screen_locker').hide();
                    }else if(data == 2){
                        alert('评级失败！请稍后尝试！');return false;
                    }else if(data == 4){
                        alert('操作失败！');return false;
                    }
                }
            );
        })
    })
</script>
