<?php ?>

<?php if($_SESSION['identity'] == MEMBER_IDENTITY_TWO){?>
    <style>#budget_list input{height: 30px;}</style>
    <div class="ncc-receipt-info">
        <div class="ncc-receipt-info-title">
            <h3>预算信息</h3>
        </div>
        <div id="budget_list" class="ncc-candidate-items" style="height: 120px;">
            <table style="border-collapse:separate; border-spacing:10px;">
                <tr>
                    <td>预算科目：</td>
                    <td style="width: 350px;">
                        <select name="obj_list" id='obj_list' lay-filter="obj_list" lay-verify="required"></select>
                    </td>
                </tr>
                <tr>
                    <td>预算金额：</td>
                    <td style="width: 350px;"><span class='money_obj' style="color:red">0.00</span></td>
                </tr>
                <tr>
                    <td>物资用途：</td>
                    <td style="width: 350px;">
                        <input name="buy_type" value="1"  lay-filter="inv_type" title="自用" checked="" type="radio">
                        <input name="buy_type" value="2"  lay-filter="inv_type" title="带采购" type="radio">
                    </td>
                </tr>
            </table>
            <input type="hidden" name='obj_name' class='obj_name' value="">
        </div>
    </div>
<?php }?>
<script type="text/javascript">


    $(document).ready(function() {
        //  加载所有的省份
        $.ajax({
            type: "get",
            url: "/shop/index.php?act=buy&op=getBudgetList", // type=1表示查询省份
            data: {},
            dataType: "json",
            success: function(data) {
                $("#obj_list").html("<option value=''>请选择预算</option>");
                $.each(data, function(i, item) {
                    $("#obj_list").append("<option value='" + item.id + "'>" + item.desc + "</option>");
                });
                layui.use(['form'], function(){
                    var form = layui.form;
                    form.render('select');
                });
            }
        });
    });


    layui.use(['form', 'layedit', 'laydate'], function() {
        var form = layui.form
            , layer = layui.layer
            , layedit = layui.layedit
            , laydate = layui.laydate;

        form.on('select(obj_list)', function(data){
            if(data.value != ''){
                $.ajax({
                    type: "post",
                    url: "/shop/index.php?act=buy&op=getobjmoney", // type =2表示查询市
                    data: {"val": data.value, "name": data.elem[data.elem.selectedIndex].text},
                    dataType: "json",
                    success: function(list) {
                        $('.money_obj').html(list);
                        $('#obj_name').val(data.value);
                    }
                });
            }else{
                $('.money_obj').html('0.00');
                $('#obj_name').val('');
            }
        });


    });


    //查询预算
/*    $('#obj_cx').change(function(){
        var val =  $('#obj_cx :selected').val();
        var name =  $('#obj_cx :selected').text();
        $.post(
            '/*echo SHOP_SITE_URL;/index.php?act=buy&op=getobjmoney',
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
    })*/

</script>