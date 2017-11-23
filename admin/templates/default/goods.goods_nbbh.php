<?php ?>

<div class="page2" style="overflow:scroll;height:500px;">
    <form method="post" name="form2" id="form2" action="<?php echo urlAdmin('goods', 'goods_nbbh_ser');?>">
        <table class="table tb-type2 nobdb">
        <tbody>
          
          <tr class="noborder">
              <td colspan="2" class="required" id="gcategory"><label>分类</label>
              <input type="hidden" value="0" name="produce_gcid" class="mls_id">
                <input type="hidden" value="<?php echo $output['gc_name'];?>" name="produce_gcclasname" class="mls_name">
                <span class="mr10"><?php echo $output['gc_name'];?></span>
                            <input class="edit_gcategory" type="button" value="编辑">
                             <select style='display:none;' class="class-select">
                            <option value="0">请选择...</option>
                            <?php if(!empty($output['gc_list'])){ ?>
                            <?php foreach($output['gc_list'] as $k => $v){ ?>
                            <?php if ($v['gc_parent_id'] == 0) {?>
                            <option value="<?php echo $v['gc_id'];?>"><?php echo $v['gc_name'];?></option>
                            <?php } ?>
                            <?php } ?>
                            <?php } ?>
                </select>
                </td>
          </tr>
           <tr class="noborder">
               <td class="required"><label>物料名称</label>&nbsp;&nbsp;<input type="text" name="se_name" class="se_name" value="" /></td>
               <td class="required"><label>物料编号</label>&nbsp;&nbsp;<input type="text" name="product_code" class="product_code" value="" /></td>
                <td colspan="2"><a href="javascript:void(0);" class="btn" id="form2_submit" nctype="btn_submit"><span>搜索</span></a></td>
          </tr>
        </tbody>
    </table>
    </form>
    <table class="table tb-type2 nobdb">
      <tbody class="ser_neirong">
        <tr class="noborder">
            <td colspan="2" class="required"><label>物料名称</label></td>
          <td colspan="2" class="required"><label>物料编号</label></td>
          <td colspan="2" class="required"><label>品牌</label></td>
          <td colspan="2" class="required"><label>规格</label></td>
        </tr>
        <?php foreach($output['bh'] as $rows){?>
        <tr class="noborder">
            <td colspan="2"><?php echo $rows['local_description'];?></td>
            <td colspan="2" style="cursor:pointer" class="product_id_close"><?php echo $rows['product_code'];?></td>
            <td colspan="2"><?php echo $rows['brand'];?></td>
            <td colspan="2"><?php echo $rows['product_spec'];?></td>
        </tr>
        <?php }?>
      </tbody>
    </table>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script> 
<script>
    //通过选择器选择到当前的元素，赋值给对应前端id参数
    $(function(){
        $('.product_id_close').click(function(){
            var nbbh = $(this).html();
            var local_description =$(this).prev().html();
            var product_spec = $(this).next().next().html();
            $('#<?php echo $output['ids'];?>').val(nbbh);
            $('#<?php echo $output['ids'];?>').parent().parent().find('input[data-type="product_spec"]').val(product_spec);
            $('#<?php echo $output['ids'];?>').parent().parent().find('input[data-type="local_description"]').val(local_description);
            $('#fwin_goods_nbbh').hide();
        })
        $('#form2_submit').click(function(){
            var name = $('.se_name').val();
            var mls_id = $('.mls_id').val();
            var mls_name = $('.mls_name').val();
            var product_code = $('.product_code').val();
            $.post(
                '<?php echo ADMIN_SITE_URL;?>/index.php?act=goods&op=goods_nbbh_ser',
                {
                    'name':name,
                    'mls_id':mls_id,
                    'mls_name':mls_name,
                    'product_code':product_code
                },
                function(data){
                    $('.ser_neirong').html(data);
                    $('.product_id_close').click(function(){
                        var nbbh = $(this).html();
            var local_description =$(this).prev().html();
            var product_spec = $(this).next().next().html();
            $('#<?php echo $output['ids'];?>').val(nbbh);
            $('#<?php echo $output['ids'];?>').parent().parent().find('input[data-type="product_spec"]').val(product_spec);
            $('#<?php echo $output['ids'];?>').parent().parent().find('input[data-type="local_description"]').val(local_description);
            $('#fwin_goods_nbbh').hide();
                    })
                }
            );
        })
    })
    
    gcategoryInit('gcategory');
    </script>
