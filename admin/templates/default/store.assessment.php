<?php ?>
<script type="text/javascript">
 
</script>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['goods_class_index_class'];?></h3>
      <?php echo $output['top_link'];?>
    </div>
  </div>
  <div class="fixed-empty"></div>
   <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th class="nobg" colspan="12"><div class="title"><h5><?php echo $lang['nc_prompts'];?></h5><span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td>
          <ul>
              <li><?php echo $lang['goods_class_index_help1'];?></li>
             
              
              <form method='get' action="" id="store_id_changes" >
                <input type="hidden" name="act" value="assessment" />
                <input type="hidden" name="op" value="edit" />
                <input type="hidden" name="member_id" value="<?php if($_GET['op']=='template'){echo 999999999;}else{echo $_GET['member_id'];}?>" />
                <table class="table tb-type2">
                  <tbody>
                      <tr class="hover edit">
                      <td class="w96">
                          <span style="font-weight: bold;color:red;"> 供应商模板名称 </span>&nbsp;
                          <select name="store_id">
                              <?php foreach($output['store'] as $rows){ ?>
                                <option <?php if($_GET['store_id'] == $rows['member_id']){echo 'selected="selected"';}?> value="<?php echo $rows['member_id']?>"><?php echo $rows['store_name']?></option>
                              <?php }?>
                          </select>
                          <a href="JavaScript:void(0);" class="btn" id="submitidBtn">
                              <span>选择</span>
                          </a> 
                    </tr>
                   </tbody>
                </table>
                <tfoot>
                    
                  </tfoot>
              </form>
            </ul>
        </td>
      </tr>
    </tbody>
  </table>
  <form method='post' action="" id="form_submit_one" >
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="member_id" value="<?php echo $_GET['member_id'];?>" />
    <table class="table tb-type2">
      <thead>
        <tr class="thead">
          <th>指标名称</th>
          <!--<th>是否显示</th>-->
          <th></th>
          <th><?php echo $lang['nc_handle'];?></th>
        </tr>
      </thead>
      <tbody>
          <tr class="hover edit">
          <td class="w50pre name">
             <!--<img fieldid="1114" status="none" nc_type="flex" src="http://www.wanke.host/admin/templates/default/images/tv-expandable1.gif">--> 
             <input type="text" name="pdata[0][type_1]"/>
          <a class="btn-add-nofloat marginleft" href="javascript:void(0)">
              <span>新增分类</span>
          </a>
          </td>
          <td><?php echo $v['commis_rate'];?></td>
          <td class="w96">
                 <a   style="cursor:pointer" class="add_one" >新增</a>
          </td>
        </tr>
      </tbody>
    </table>
    <tfoot>
        <tr>
          <td colspan="2"><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span>提交</span></a></td>
        </tr>
      </tfoot>
  </form>
</div>
<script>
    var pid = 1;
    var i = 0;
    var type2= 0;
    var type  = 0;
    $(function(){
        $('#submitidBtn').click(function(){
            $('#store_id_changes').submit();
        })
        $('.marginleft').click(function(){
            i = parseInt(i*1)+parseInt(1);
            var addi = i;
            var htmlsinsert = '<tr class="hover edit class_two_'+addi+'"><td class="w50pre name"><img fieldid="1114" status="none" nc_type="flex" src="<?php echo ADMIN_SITE_URL;?>/templates/default/images/tv-expandable1.gif" />';
            htmlsinsert += '<input type="hidden" name="data['+addi+'][tid_1]" value="0" /><input type="text" name="data['+addi+'][type_2]" value="" />&nbsp;<a class="btn-add-nofloat add_assessment_'+addi+'" href="javascript:void(0)"><span>新增指标</span></a></td><td><?php echo $v['commis_rate'];?></td><td class="w96"><a   style="cursor:pointer" class="del" ><?php echo $lang['nc_del'];?></a></td>';
            $(this).parents('tr').after(htmlsinsert);
            $(".add_assessment_"+addi).bind("click",function(){
                type2 = parseInt(type2*1)+parseInt(1);
                var htmlsinsert = '<tr class="hover edit question_q"><td class="w50pre name" style="width:100%;"><img fieldid="1114" status="none" nc_type="flex" src="<?php echo ADMIN_SITE_URL;?>/templates/default/images/tv-expandable1.gif" /><img fieldid="1114" status="none" nc_type="flex" src="<?php echo ADMIN_SITE_URL;?>/templates/default/images/tv-expandable1.gif" /><input type="hidden" name="data['+addi+'][tid_2]" value="'+type2+'" />&nbsp;问题';
                htmlsinsert += '&nbsp;<input type="text" name="data['+addi+'][data_re]['+type2+'][question]" style="width:200px;" />&nbsp;&nbsp;权重&nbsp;&nbsp;<input style="width:30px;" class="scale_'+type2+'" name="data['+addi+'][data_re]['+type2+'][scale]" type="text" name="" />&nbsp;&nbsp;最低分描述&nbsp;<input style="width:200px;" name="data['+addi+'][data_re]['+type2+'][desc_1]" type="text" name="" />&nbsp;&nbsp;最高分描述&nbsp;<input style="width:200px;" name="data['+addi+'][data_re]['+type2+'][desc_5]" type="text" name="" /></td><td><?php echo $v['commis_rate'];?></td><td class="w96"><a   style="cursor:pointer" class="del" ><?php echo $lang['nc_del'];?></a></td>';
                $(this).parents('tr').after(htmlsinsert);
                $(".del").bind("click",function(){
                    $(this).parents('tr').remove();
                });
            });
            $(".del").bind("click",function(){
                $(this).parents('tr').remove();
            });
        });
       
       $('.add_one').click(function(){
           type = parseInt(type*1)+parseInt(1);
           var one_html = '<tr class="hover edit" style="background: rgb(255, 255, 255);"><td class="w50pre name">';
           one_html +=  '<input type="text" name="pdata['+type+'][type_1]"><a class="btn-add-nofloat marginleft marginleft_'+type+'" href="javascript:void(0)"><span>新增分类</span></a></td><td></td><td class="w96"><a   style="cursor:pointer" class="del" ><?php echo $lang['nc_del'];?></a></td></tr>';
           $(this).parents('tr').after(one_html);
           $(".marginleft_"+type).bind("click",function(){
                i = parseInt(i*1)+parseInt(1);
                var addi = i;
                var htmlsinsert = '<tr class="hover edit class_two_'+addi+'"><td class="w50pre name"><img fieldid="1114" status="none" nc_type="flex" src="<?php echo ADMIN_SITE_URL;?>/templates/default/images/tv-expandable1.gif" />';
                htmlsinsert += '<input type="hidden" name="data['+addi+'][tid_1]" value="'+type+'" /><input type="text" name="data['+addi+'][type_2]" value="" />&nbsp;<a class="btn-add-nofloat add_assessment_'+addi+'" href="javascript:void(0)"><span>新增指标</span></a></td><td><?php echo $v['commis_rate'];?></td><td class="w96"><a   style="cursor:pointer" class="del" ><?php echo $lang['nc_del'];?></a></td>';
                $(this).parents('tr').after(htmlsinsert);
                $(".add_assessment_"+addi).bind("click",function(){
                    type2 = parseInt(type2*1)+parseInt(1);
                    var htmlsinsert = '<tr class="hover edit question_q"><td class="w50pre name" style="width:100%;"><img fieldid="1114" status="none" nc_type="flex" src="<?php echo ADMIN_SITE_URL;?>/templates/default/images/tv-expandable1.gif" /><img fieldid="1114" status="none" nc_type="flex" src="<?php echo ADMIN_SITE_URL;?>/templates/default/images/tv-expandable1.gif" /><input type="hidden" name="data['+addi+'][tid_2]" value="'+type2+'" />&nbsp;问题';
                    htmlsinsert += '&nbsp;<input type="text" name="data['+addi+'][data_re]['+type2+'][question]" style="width:200px;" />&nbsp;&nbsp;权重&nbsp;&nbsp;<input style="width:30px;" class="scale_'+addi+'" name="data['+addi+'][data_re]['+type2+'][scale]" type="text" name="" />&nbsp;&nbsp;最低分描述&nbsp;<input style="width:200px;" name="data['+addi+'][data_re]['+type2+'][desc_1]" type="text" name="" />&nbsp;&nbsp;最高分描述&nbsp;<input style="width:200px;" name="data['+addi+'][data_re]['+type2+'][desc_5]" type="text" name="" /></td><td><?php echo $v['commis_rate'];?></td><td class="w96"><a   style="cursor:pointer" class="del" ><?php echo $lang['nc_del'];?></a></td>';
                    $(this).parents('tr').after(htmlsinsert);
                    $(".del").bind("click",function(){
                        $(this).parents('tr').remove();
                    });
                });
                $(".del").bind("click",function(){
                    $(this).parents('tr').remove();
                });
            });
            $(".del").bind("click",function(){
                    $(this).parents('tr').remove();
            });
       })
       
       $('#submitBtn').click(function(){
            var scale_num = 0;
            var cun_num = 0;
            for(i=1;i<=type2;i++){
                cun_num += $('.scale_'+i).val()*1;
                 //判断分类下是否有指标
                var class_two_th = $('.class_two_'+i).html();
                if(class_two_th != undefined){
                  var t_class_h  = $('.class_two_'+i).next().hasClass('question_q');
                  if(!t_class_h){
                    alert('请在分类下添加指标，或把二级分类删除');return false;
                  }
                }
            }
            if(cun_num > 100){
                alert('权重不能大于100！');
                return false;
            }else if(cun_num == 100){
                $('#form_submit_one').submit();
            }else{
                alert('权重必须等于100');
                return false;
            }
            
       })
       
    })
    </script>