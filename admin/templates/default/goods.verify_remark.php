<?php ?>

<div class="page">
  <form method="post" name="form1" id="form1" action="<?php echo urlAdmin('goods', 'goods_verify');?>">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" value="<?php echo $output["commonids"];?>" name="commonids">
    <style>
        .wb_nb_bh tr td {
            text-align: center;
            padding:3px;
        }
    </style>
    <table class="wb_nb_bh" data-type="wb_nb_bh_id">
        <tr class="noborder">  
          <td colspan="2" class="required"><label>商品名称规格</label></td>
          <td colspan="2" class="required"><label>外部物料编号</label></td>
          <td colspan="2" class="required"><label>协议价格</label></td>
          <td colspan="2" class="required"><label>内部物料名称</label></td>
          <td colspan="2" class="required"><label>内部物料编号</label></td>
          <td colspan="2" class="required"><label>内部物料规格</label></td>
          <td colspan="2" class="required"><label>选择</label></td>
        </tr>
        <?php foreach($output['goods_greal'] as $rows){?>
        <tr class="noborder" data-type="wlxxsh">   
        <input type="hidden" value="<?php echo $rows['goods_id'];?>" name="goods_id[]" >
          <td colspan="2" class="required"><input type="text" name="goods_name[]" readonly="readonly" value="<?php echo $rows['goods_name']?>" /></td>
          <td colspan="2" class="required"><input type="text" name="waibu[]" readonly="readonly" value="<?php echo $rows['goods_serial']?>" /></td>
          <td colspan="2" class="required"><input type="text" name="goods_marketprice[]" readonly="readonly" value="<?php echo $rows['goods_marketprice']?>" /></td>         
          <td colspan="2" class="required"><input type="text" data-type="local_description" readonly="readonly" name="local_description" id="local_description" value="<?php echo $rows['local_description']?>"  /></td>
          <td colspan="2" class="required"><input type="text" name="neibu[]"readonly="readonly" nc-type="nbbm" id="<?php echo $rows['goods_serial']?>" value="<?php echo $rows['to_product_id']?>"  /></td>
          <td colspan="2" class="required"><input type="text" data-type="product_spec" readonly="readonly"  name="product_spec" id="product_spec" value="<?php echo $rows['product_spec']?>"  /></td>
          <td colspan="2" class="required"><a href='javascript:void(0);' lang="<?php echo $rows['goods_serial']?>" goods_id="<?php echo $rows['goods_id'];?>" class='getwlbh'>选择编号</a></td>
        </tr>
        <?php }?>
    </table>
    
    <table class="table tb-type2 nobdb">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label>审核通过:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform onoff">
            <label for="rewrite_enabled"  class="cb-enable selected" title="<?php echo $lang['nc_yes'];?>"><span><?php echo $lang['nc_yes'];?></span></label>
            <label for="rewrite_disabled" class="cb-disable" title="<?php echo $lang['nc_no'];?>"><span><?php echo $lang['nc_no'];?></span></label>
            <input id="rewrite_enabled" name="verify_state" checked="checked" value="1" type="radio">
            <input id="rewrite_disabled" name="verify_state" value="0" type="radio"></td>
          <td class="vatop tips">
            <?php echo $lang['open_rewrite_tips'];?></td>
        </tr>
        <tr nctype="reason" style="display: none;">
          <td colspan="2" class="required"><label for="verify_reason">未通过理由:</label></td>
        </tr>
        <tr class="noborder" nctype="reason" style="display :none;">
          <td class="vatop rowform"><textarea rows="6" class="tarea" cols="60" name="verify_reason" id="verify_reason"></textarea></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="2"><a href="javascript:void(0);" class="btn" nctype="btn_submit"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/admincp.js" charset="utf-8"></script>
<script>  
$(function(){
    var a =1;
    
    $('a[nctype="btn_submit"]').click(function(){
        var num =0; 
        $("table[data-type='wb_nb_bh_id'] tr[data-type='wlxxsh']").each(function(){
          var wbwlnb= $(this).find("input[nc-type='nbbm']").val();
          if(wbwlnb == "" ){
              num++ ;
          }
        });
    if (num > 0 && a==1){
             alert("内部编码不能为空！"); 
          }else{
              ajaxpost('form1', '', '', 'onerror');  
          }    
    });
    $('input[name="verify_state"]').click(function(){
        if ($(this).val() == 1) {
            a=1;
            $('tr[nctype="reason"]').hide();
        } else {
            $('tr[nctype="reason"]').show();
            a=2;
        }
    });
    $('.getwlbh').click(function(){
        var id= $(this).attr("lang");
        var g_id = $(this).attr('goods_id');
        _uri_nbb = "<?php echo ADMIN_SITE_URL;?>/index.php?act=goods&op=goods_nbbh&id="+id+"&g_id="+g_id;
        CUR_DIALOG = ajax_form('goods_nbbh', '获取物料编号', _uri_nbb, 630);
    })
});

</script>