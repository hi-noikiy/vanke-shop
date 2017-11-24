<?php ?>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<style>
.ncc-receipt-info-title{margin-top: 10px;margin-bottom: 10px;}
</style>
<div class="ncc-receipt-info" style="border-bottom: 0px;border-top: 0px;">
  <div class="ncc-receipt-info-title">
    <h3>发票信息</h3>
      <a href="javascript:void(0);" style="float: right;" id="new-address">
      <span style="float: right;font-size:14px;color: #27A9E3">新增发票
          <i class="layui-icon" style="font-size: 15px; color: #27A9E3">&#xe608;</i>
      </span>
      </a>
  </div>
  <div id="invoice-list" class="ncc-candidate-items">
      <div>

      </div>
      <div id="empty-invoice" <?php if(!empty($output['address_info'])){?>style="display: none" <?php }?> >
          <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;" >
              <legend>请添加确认收货地址信息</legend>
          </fieldset>
      </div>
  </div>
</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/form.js"></script>
<script type="text/javascript">
    $("ul[name='consignee-list']").on("mouseover", "li", function() {
        $(this).find(".op-btns").show();
    });

    $("ul[name='consignee-list']").on("mouseleave", "li", function() {
        $(this).find(".op-btns").hide();
    });

    $("#showAll").click(function(){
        $("#consignee-addr-all").show();$("#consignee-addr-one").hide();
        $("#clodeAll").show();$("#showAll").hide();
    });

    $("#clodeAll").click(function(){
        $("#consignee-addr-all").hide();$("#consignee-addr-one").show();
        $("#clodeAll").hide();$("#showAll").show();
    });

    $("ul[name='consignee-list']").on("click", ".consignee-item", function() {
        //修改框体属性
        $("#add-select-a").find("span").attr('title',$(this).parent().attr('adname'));
        $("#add-select-a").find("span").html($(this).parent().attr('adname'));
        //修改其他属性
        $("#add-select-b").find(".addr-info").attr('title',$(this).parent().attr('adcity')+'&nbsp;&nbsp;'+$(this).parent().attr('adinfo'));
        $("#add-select-b").find(".addr-info").html($(this).parent().attr('adcity')+'&nbsp;&nbsp;'+$(this).parent().attr('adinfo'));

        $("#add-select-b").find(".mob-phone").html($(this).parent().attr('admobile'));
        $("#add-select-b").find(".tel-phone").html($(this).parent().attr('adphone'));
        $("#add_code").val($(this).parent().attr('adcode'));
        $("#consignee_index").attr('adcode',$(this).parent().attr('adcode'));
        $("#consignee-addr-all").hide();$("#consignee-addr-one").show();
        $("#clodeAll").hide();$("#showAll").show();
    });

    //设置默认收货地址
    $("ul[name='consignee-list']").on("click", ".setdefault-consignee", function() {
        var id = $(this).parent().parent().attr("adcode");
        $.ajax({
            type:"POST",
            //提交的网址
            url:"/shop/index.php?act=buy&op=defaultAddress",
            data:{id: id},
            datatype: "json",
            success:function(result){
                if(result == '1'){
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert('修改成功', {closeBtn: 0,title: '温馨提示',offset: '100px'}, function(index){
                            default_data(this,id);layer.closeAll();
                        });
                    })
                }else{
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert('修改失败', {closeBtn: 0,title: '温馨提示',offset: '100px'}, function(index){
                            layer.closeAll();
                        });
                    })
                }
            }
        });
    });

    function default_data(obj,code) {
        $("#consignee-list-all").find("li").each(function () {
            $(this).find(".consignee-item").removeClass("item-selected");
        });
        $("#consignee_index_"+code).find(".consignee-item").addClass("item-selected");
        //完成赋值
        $("#add-select-a").find("span").attr('title',$("#consignee_index_"+code).attr('adname'));
        $("#add-select-a").find("span").html($("#consignee_index_"+code).attr('adname'));
        //修改其他属性
        $("#add-select-b").find(".addr-info").attr('title',$("#consignee_index_"+code).attr('adcity')+'&nbsp;&nbsp;'+$("#consignee_index_"+code).attr('adinfo'));
        $("#add-select-b").find(".addr-info").html($("#consignee_index_"+code).attr('adcity')+'&nbsp;&nbsp;'+$("#consignee_index_"+code).attr('adinfo'));

        $("#add-select-b").find(".mob-phone").html($("#consignee_index_"+code).attr('admobile'));
        $("#add-select-b").find(".tel-phone").html($("#consignee_index_"+code).attr('adphone'));

        $("#add_code").val($("#consignee_index_"+code).attr('adcode'));
        $("#consignee-addr-all").hide();$("#consignee-addr-one").show();
        $("#clodeAll").hide();$("#showAll").show();
    }

    $("#new-address").click(function(){
        layui.use('layer', function(){
            var layer = layui.layer;
            layer.open({
                type: 2,
                title: '新增收货人地址信息',
                maxmin: false, //开启最大化最小化按钮
                resize: false,
                fixed: true,
                offset: 20,
                shade: [0.8, '#393D49'],
                area: ['820px', '600px'],
                content: '/shop/index.php?act=buy&op=Address',
            });
        });
    });


    //修改收货地址 edit-consignee
    $("ul[name='consignee-list']").on("click", ".edit-consignee", function() {
        var id = $(this).parent().parent().attr("adcode");
        var show_id = $("#consignee_index").attr("adcode");
        layui.use('layer', function(){
            var layer = layui.layer;
            layer.open({
                type: 2,
                title: '修改收货人地址信息',
                maxmin: false, //开启最大化最小化按钮
                resize: false,
                fixed: true,
                offset: 20,
                shade: [0.8, '#393D49'],
                area: ['820px', '600px'],
                content: '/shop/index.php?act=buy&op=Address&id='+id+'&sid='+show_id,
            });
        });
    });


    //删除收货地址
    $("ul[name='consignee-list']").on("click", ".del-consignee", function() {
        var id = $(this).parent().parent().attr("adcode");
        var show_id = $("#consignee_index").attr("adcode");
        $.ajax({
            type:"POST",
            //提交的网址
            url:"/shop/index.php?act=buy&op=delAddress",
            data:{id: id, sid:show_id},
            datatype: "json",
            success:function(result){
                var result = JSON.parse(result);
                if(result.code == '1'){
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert('删除成功', {closeBtn: 0,title: '温馨提示',offset: '100px'}, function(index){
                            $("#consignee_index_"+id).remove();layer.closeAll();
                        });
                    })
                }else if(result.code == '2'){
                    //删除数据后没有任何地址记录，删除选中记录以及主数据记录
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert('删除成功', {closeBtn: 0,title: '温馨提示',offset: '100px'}, function(index){
                            $("#consignee_index_"+id).remove();
                            $("#add_code").val('');$("#empty-address").show();
                            $(".step-cont").hide();layer.closeAll();
                        });
                    })
                }else if(result.code == '3'){
                    //删除数据后还存在至少一条的数据记录，删除选中记录以及跟新主数据记录
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert('删除成功', {closeBtn: 0,title: '温馨提示',offset: '100px'}, function(index){
                            $("#consignee_index_"+id).remove();upStr(result.data);layer.closeAll();
                        });
                    })
                }else{
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert(result.msg, {closeBtn: 0,title: '温馨提示',offset: '100px'}, function(index){
                            layer.closeAll();
                        });
                    })
                }
            }
        });
    });

    //追加地址元素
    function appStr(list) {
        var str = "<li class='ui-switchable-panel ui-switchable-panel-selected' style='display: list-item;' id='consignee_index_"+list.address_id+"'";
        str+=" adname='"+list.true_name+"' adcity='"+list.area_info+"'";
        str+=" adinfo='"+list.address+"' admobile='"+list.mob_phone+"'";
        str+=" adphone='"+list.tel_phone+"' adcode='"+list.address_id+"' >";
        str+="<div class='consignee-item'>";
        str+="<span limit='8' title='"+list.true_name+"'>"+list.true_name+"</span><b></b>";
        str+="</div><div class='addr-detail'>";
        str+="<span class='addr-info' limit='45' title='"+list.area_info+"  "+list.address+"'>";
        str+=list.area_info+"  "+list.address+"</span>";
        str+="<span class='mob-phone addr-tel'>"+list.mob_phone+"</span>";
        str+="<span class='tel-phone addr-tel'>"+list.tel_phone+"</span>";
        str+="</div><div class='op-btns'>";
        str+="<a href='javascript:void(0);' class='ftx-05 setdefault-consignee' >设为默认地址</a>";
        str+="<a href='javascript:void(0);' class='ftx-05 edit-consignee'>编辑</a>";
        str+="<a href='javascript:void(0);' class='ftx-05 del-consignee'>删除</a>";
        str+="</div></li>";
        $("#consignee-list-all").append(str);
        upStr(list);
        layui.use('layer', function() {
            var layer = layui.layer;
            layer.closeAll();
        });
    }

    function upStr(list){
        //替换默认地址信息
        $("#add-select-a").find("span").attr('title',list.true_name);
        $("#add-select-a").find("span").html(list.true_name);
        //修改其他属性
        $("#add-select-b").find(".addr-info").attr('title',list.area_info+'&nbsp;&nbsp;'+list.address);
        $("#add-select-b").find(".addr-info").html(list.area_info+'&nbsp;&nbsp;'+list.address);

        $("#add-select-b").find(".mob-phone").html(list.mob_phone);
        $("#add-select-b").find(".tel-phone").html(list.tel_phone);
        $("#add_code").val(list.address_id);
        $("#consignee_index").attr('adcode',list.address_id);
        $("#consignee-addr-all").hide();$("#consignee-addr-one").show();
        $("#clodeAll").hide();$("#showAll").show();
        //处理为空的显示隐藏
        $("#empty-address").hide();$(".step-cont").show();
    }

    function upSelfStr(list){
        //跟新li元素信息
        $("#consignee_index_"+list.address_id).attr('adname',list.true_name);
        $("#consignee_index_"+list.address_id).attr('adcity',list.area_info);
        $("#consignee_index_"+list.address_id).attr('adinfo',list.address);
        $("#consignee_index_"+list.address_id).attr('admobile',list.mob_phone);
        $("#consignee_index_"+list.address_id).attr('adphone',list.tel_phone);
        $("#consignee_index_"+list.address_id).attr('adcode',list.address_id);

        $("#consignee_index_"+list.address_id).find(".consignee-item").find("span").attr('title',list.true_name);
        $("#consignee_index_"+list.address_id).find(".consignee-item").find("span").html(list.true_name);
        //修改其他属性
        $("#consignee_index_"+list.address_id).find(".addr-info").attr('title',list.area_info+'&nbsp;&nbsp;'+list.address);
        $("#consignee_index_"+list.address_id).find(".addr-info").html(list.area_info+'&nbsp;&nbsp;'+list.address);

        $("#consignee_index_"+list.address_id).find(".mob-phone").html(list.mob_phone);
        $("#consignee_index_"+list.address_id).find(".tel-phone").html(list.tel_phone);
        layui.use('layer', function() {
            var layer = layui.layer;
            layer.closeAll();
        });
    }





//异步显示每个店铺运费 city_id计算运费area_id计算是否支持货到付款
function showShippingPrice(city_id,area_id) {
	$('#buy_city_id').val('');
    $.post(SITEURL + '/index.php?act=buy&op=change_addr', {'freight_hash':'<?php echo $output['freight_hash'];?>',city_id:city_id,'area_id':area_id}, function(data){
    	if(data.state == 'success') {
    	    $('#buy_city_id').val(city_id);
    	    $('#allow_offpay').val(data.allow_offpay);
            if (data.allow_offpay_batch) {
                var arr = new Array();
                $.each(data.allow_offpay_batch, function(k, v) {
                    arr.push('' + k + ':' + (v ? 1 : 0));
                });
                $('#allow_offpay_batch').val(arr.join(";"));
            }
    	    $('#offpay_hash').val(data.offpay_hash);
    	    $('#offpay_hash_batch').val(data.offpay_hash_batch);
    	    var content = data.content;
    	    var amount = 0;
            for(var i in content){
                var content_num = number_format(content[i],4);
                if(content_num.substring(content_num.length-2) == 00){
                    $('#eachStoreFreight_'+i).html(number_format(content[i],2));
                }else{
                    $('#eachStoreFreight_'+i).html(number_format(content[i],4));
                }
            	amount = amount + parseFloat(content[i]);
            }
            calcOrder();
    	}

    },'json');
}
$(function(){
    <?php if (!empty($output['address_info']['address_id'])) {?>
    showShippingPrice(<?php echo $output['address_info']['city_id'];?>,<?php echo $output['address_info']['area_id'];?>);
    <?php } else {?>
    $('#edit_reciver').click();
    <?php }?>
});
</script>