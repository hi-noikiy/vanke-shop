<?php ?>
<div class="page">
    <div class="fixed-empty"></div>
  <form method="get" name="formSearch">
    <input type="hidden" value="codemanages" name="act">
    <input type="hidden" value="index" name="op">
    <table class="tb-type1 noborder search">
      <tbody>
          <tr>
              <td class="w48"><input type="checkbox" name="product_type" value="Z" class="checkitem" <?php if(!empty($output['product_type'])) {?> checked="checked" <?php }?>> 
             <th><label for="product_code">商城物料</label></th>
              <td class="w48"><input type="checkbox" name="product_level_w" value="Z" class="checkitem"  <?php if(!empty($output['product_level_w'])) {?> checked="checked" <?php }?>> 
             <th><label for="product_code">外部物料</label></th>
              <td class="w48"><input type="checkbox" name="product_level_n" value="O" class="checkitem" <?php if(!empty($output['product_level_n'])) {?> checked="checked" <?php }?>> 
             <th><label for="product_code">内部物料</label></th>
          </tr>
         <tr>
           <th><label for="product_code">物料编号</label></th>
          <td><input type="text" value="<?php echo $output['product_code'];?>" name="product_code" id="product_code" class="txt"></td> 
          <th><label for="product_code">物料类别</label></th>
          <td id="searchgc_td"></td><input type="hidden" id="choose_gcid" name="choose_gcid" value="0"/>
        </tr> 
        <tr>
          <th><label for="local_description">物料名称</label></th>
          <td><input type="text" value="<?php echo $output['local_description'];?>" name="local_description" id="local_description" class="txt"></td>
         
          <th><label for="local_description">品牌</label></th>
          <td><input type="text" value="<?php echo $output['brand'];?>" name="brand_name" id="brand_id" class="txt"></td>
          
          <th><label for="local_description">规格</label></th>
          <td><input type="text" value="<?php echo $output['product_spec'];?>" name="product_spec_name" id="product_spec_id" class="txt"></td>
          
          
          <td><a href="javascript:document.formSearch.submit();" class="btn-search " title="<?php echo $lang['nc_query'];?>">&nbsp;</a>
         <?php if($output['product_id'] != '' or $output['product_code'] != '' or $output['local_description'] != '' or $output['product_type'] != '' or $output['product_level_w'] != '' or $output['product_level_n'] != '' or $output['brand'] != '' or $output['product_spec'] != ''){?>
         <a href="index.php?act=codemanages&op=index" class="btns " title="<?php echo $lang['nc_cancel_search'];?>"><span><?php echo $lang['nc_cancel_search'];?></span></a>
          <?php }?>
          </td>
            <td><a href="index.php?act=codemanages&op=add&type=W" class="btn-add">新增外部物料</a></td>
            <td><a href="index.php?act=codemanages&op=add&type=N" class="btn-add">新增内部物料</a></td>
        </tr>
        </tbody>
    </table>
</form>
  <!--<div class="fixed-bar">
    <div class="item-title">
      <h3>物料管理</h3>
    </div>
  </div>--><div class="fixed-bar">
        <div class="item-title">
            <h3>物料管理</h3>
            <ul class="tab-base">

                <!--<li><a href="index.php?act=store&op=store" ><span><?php echo $lang['manage'];?></span></a></li>-->
                <li><a <?php if($_GET['op'] == "index"){?> href="JavaScript:void(0);" class="current" <?php }else{?>  href="index.php?act=codemanages&op=index" <?php } ?> ><span>物料列表</span></a></li>
                <li><a <?php if($_GET['op'] == "wl_add"){?> href="JavaScript:void(0);" class="current" <?php }else{?>  href="index.php?act=codemanages&op=wl_add" <?php } ?> ><span>物料导入</span></a></li>
            </ul>
        </div>
    </div>
    <table class="table tb-type2">
      <thead>
        <tr class="space">
          <th colspan="15" class="nobg"><?php echo $lang['nc_list'];?></th>
        </tr>
        <tr class="thead">
          <th class="align-center">一级分类</th>
          <th class="align-center">二级分类</th>
          <th class="align-center">三级分类</th>
          <th class="align-center">物料编号</th>
          <th class="align-center">物料名称</th>
          
          <th class="align-center">品牌</th>
          <th class="align-center">规格</th>
          <th class="align-center">是否内部物料</th>
          <th class="align-center">关联内部物料编号</th>
          <th class="align-center">操作</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($output['code']) && is_array($output['code'])){ ?>
        <?php foreach($output['code'] as $k => $v){ ?>
        <tr class="hover">
          <td class="align-center"><?php echo $v['gc_name1'];?></td>
          <td class="align-center"><?php echo $v['gc_name2'];?></td>
          <td class="align-center"><?php echo $v['gc_name3'];?></td> 
          <td class="align-center"><?php echo $v['product_code'];?></td>
          <td class="align-center"><?php echo $v['local_description']; ?></td>
          <td class="w150 align-center"><?php echo $v['brand'] ? $v['brand'] : '未填写品牌'; ?></td>
          <td class="align-center"><?php echo $v['product_spec'] ? $v['product_spec'] : '未填写规格'; ?></td>
          <td class="align-center"><?php echo $v['product_level']==0 ? "否" : '是'; ?></td>
          <td class="align-center"><?php echo $v['to_product_id'] ? $v['to_product_id'] : ''; ?></td>
          <td class="w150 align-center">

              <?php if($v['product_type']==1) { ?>
              <a href="<?php echo ADMIN_SITE_URL;?>/index.php?act=codemanages&op=edit&id=<?php echo base64_encode($v['product_id']);?>" >
                  <?php echo $lang['city_edit'];?>
              </a>  
              <?php }else {?>
              <a href="<?php echo ADMIN_SITE_URL;?>/index.php?act=codemanages&op=detail&id=<?php echo base64_encode($v['product_id']);?>" >
                  <?php echo "查看";?>
              </a> 
              <a href="<?php echo ADMIN_SITE_URL;?>/index.php?act=codemanages&op=edit&id=<?php echo base64_encode($v['product_id']);?>" >
                  <?php echo $lang['city_edit'];?>
              </a>  
              <?php }?>
           <!--不需要物料删除功能   
              <a class='ajax_del' lang='<?php echo base64_encode($v['product_id']);?>' href="javascript:void(0);" >
                  <?php echo $lang['city_del'];?>
              </a>
           -->
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
        <?php if(!empty($output['code']) && is_array($output['code'])){ ?>
        <tr class="tfoot">
          <td colspan="16">
             <div class="pagination"> <?php echo $output['page'];?> </div></td>
        </tr>
        <?php } ?>
      </tfoot>
    </table>
</div>
<!--<script>
    $(".ajax_del").click(function(){
        var id = $(this).attr("lang");
        var a=confirm("您确认要删除吗？");
        if(a==true)
        {
            window.location.href="<?php echo ADMIN_SITE_URL;?>/index.php?act=codemanages&op=del&id="+id;
        }
      });
    
    </script>-->
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script>
<script>
    //商品分类
init_gcselect(<?php echo $output['gc_choose_json'];?>,<?php echo $output['gc_json']?>);
        
function getId() {
    var str = '';
    $('#form_goods').find('input[name="id[]"]:checked').each(function(){
        id = parseInt($(this).val());
        if (!isNaN(id)) {
            str += id + ',';
        }
    });
    if (str == '') {
        return false;
    }
    str = str.substr(0, (str.length - 1));
    return str;
}
</script>