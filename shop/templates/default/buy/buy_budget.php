<?php ?>

<?php if($_SESSION['identity'] == MEMBER_IDENTITY_TWO){?>
    <div class="ncc-receipt-info"><div class="ncc-receipt-info-title">
            <h3>预算信息</h3></div>
        <div id="invoice_list" class="ncc-candidate-items">
            <ul>
                <li>
                    预算科目：<select name="obj_id" id='obj_cx'>
                        <option value="">请选择</option>
                        <option value="99">99</option>
    <!--                    <?php foreach($output['myrows'] as $rows){?>
                            <option value="<?php echo $rows['id'];?>"><?php echo $rows['desc'];?></option>
                        <?php }?>-->
                    </select>
                    <input type="hidden" name='obj_name' class='obj_name' value="">
                </li>
                <li>预算金额：<span class='money_obj' style="color:red">0.00</span></li>
                <li id="ys_info" style="color:red;"></li>
            </ul>
        </div>
    </div>
<?php }?>
<script type="text/javascript">
    //查询预算
    $('#obj_cx').change(function(){
        var val =  $('#obj_cx :selected').val();
        var name =  $('#obj_cx :selected').text();
        $.post(
            '<?php echo SHOP_SITE_URL;?>/index.php?act=buy&op=getobjmoney',
            {
                'val':val,
                'name':name
            },
            function(data){
                $('.money_obj').html(data);
                $('#submitOrder').removeAttr("disabled");
                SUBMIT_FORM = true;
            }

        );
    })

    //隐藏发票列表
    function hideInvList(content) {
        $('#field＿name').val(1);
        $('#edit_invoice').show();
        $("#invoice_list").html('<ul><li>'+content+'</li></ul>');
        $('.current_box').removeClass('current_box');
        ableOtherEdit();
        //重新定位到顶部
        $("html, body").animate({ scrollTop: 0 }, 0);
    }
    //加载发票列表 修改为页面加载时就跳出发票信息，如果没有，请添加
    $('#edit_invoice').on('click',function(){
        $(this).hide();
        disableOtherEdit('如需修改，请先保存发票信息');
        $(this).parent().parent().addClass('current_box');
        $('#invoice_list').load(SITEURL+'/index.php?act=buy&op=load_inv&vat_hash=<?php echo $output['vat_hash'];?>');
    });
    //修改为页面加载时就跳出发票信息，如果没有，请添加
/*    $(document).ready(function(){
        $("#edit_invoice").hide();
        disableOtherEdit('如需修改，请先保存发票信息');
        $("#edit_invoice").parent().parent().addClass('current_box');*/
        //$('#invoice_list').load(SITEURL+'/index.php?act=buy&op=load_inv&vat_hash=<?php echo $output['vat_hash'];?>');
    //});
</script>