<?php ?>
<div class="ncc-bottom"> 
    <a href="javascript:void(0)" id='submitOrder' class="ncc-btn ncc-btn-acidblue fr">提交订单</a> 
</div>
<script>
function submitNext(){
	if (!SUBMIT_FORM) return;
        var fidel= $('#field＿name').val();
        if(fidel == 0){
             alert('请选择发票信息');return false;
        }
            //查询有无选择项目，
            $('#submitOrder').attr('disabled','disabled');
            <?php if($_SESSION['identity'] != MEMBER_IDENTITY_ONE){?>
            var aval =  $('#obj_cx :selected').val();
            if(aval == ''){
                alert('请选择项目预算');return false;
            }
            var goods_id = '';
            $("[lang]").each(function(){
                 goods_id += $(this).attr("lang")+",";
              });
           
            $.ajax({
                type : "POST",
                url : "index.php?act=buy&op=buy_checknum",
                data : {goods_id:goods_id},
                dataType : "json",
                success : function (data) {
                    var sumreturn =  1;
                    var send_two = 1;
                    var if_stop = 1;
                    $.each(data, function (i, item) {
                        var span_msg = $("[lang='"+item.goods_id+"']").parent().find('span').html();
                        if(span_msg == undefined){
                            var is_unde = 1;
                            span_msg = $("[lang='"+item.goods_id+"']").parent().html();
                        }
                        sumreturn += 1;
                        if(item.state == 1){
                            send_two += 1;
                            if(is_unde == 1){
                                var lang = span_msg+'<span style="color:red">  ';
                            }else{
                                var lang = '';
                            }
                            if(item.goods_min >= 0){
                                lang += '　　( 当前商品最小购买数量为'+item.goods_min+' )';
                            }
                            if(item.goods_max >= 0){
                                lang += '　　( 当前商品最大购买数量为'+item.goods_max+' )';
                            }
                            if(is_unde == 1){
                                lang += '</span>';
                                $("[lang='"+item.goods_id+"']").parent().html(lang);
                            }else{
                                $("[lang='"+item.goods_id+"']").parent().find('span').html(lang);
                            }
                            $("[lang='"+item.goods_id+"']").parents('td').next().next().css("color",'red') ;
                            if_stop = 2;
                        }
                        $('.is_post_type').val(2);
                    });
                    if(if_stop == 2){
                        return false;
                    }else{
                        if ($('input[name="cart_id[]"]').size() == 0) {
                                showDialog('所购商品无效', 'error','','','','','','','','',2);
                                return;
                        }
                        if ($('#address_id').val() == ''){
                                showDialog('<?php echo $lang['cart_step1_please_set_address'];?>', 'error','','','','','','','','',2);
                                return;
                        }
                        if ($('#buy_city_id').val() == '') {
                                showDialog('正在计算运费,请稍后', 'error','','','','','','','','',2);
                                return;
                        }
                        if (($('input[name="pd_pay"]').attr('checked') || $('input[name="rcb_pay"]').attr('checked')) && $('#password_callback').val() != '1') {
                                showDialog('使用充值卡/预存款支付，需输入支付密码并使用  ', 'error','','','','','','','','',2);
                                return;
                        }
                        if ($('input[name="fcode"]').size() == 1 && $('#fcode_callback').val() != '1') {
                                showDialog('请输入并使用F码', 'error','','','','','','','','',2);
                                return;
                        }
                        SUBMIT_FORM = false;
                        
                        //判断订单金额是否超出预算
                        if(parseFloat($("#orderTotal").html()) > parseFloat($(".money_obj").html())){
                            $("#ys_info").html("超出预算，无法购买");
                            return false;
                        }
                        
                        $('#order_form').submit();
                    }
                }
            });
            <?php }?>
        

	
        
}
$(function(){
        $(document).keydown(function(e) {
                if (e.keyCode == 13) {
                        submitNext();
                        return false;
                }
            });
        $('#submitOrder').on('click',function(){submitNext()});
        calcOrder();
});
</script>