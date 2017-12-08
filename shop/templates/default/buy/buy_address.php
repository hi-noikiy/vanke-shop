<?php ?>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<style>
.consignee-content {overflow: hidden;max-height: 168px;}
.consignee-scrollbar {width: 938px;}
.consignee-scroll {position: relative;zoom: 1;}
.consignee-scroll .consignee-cont {margin: 0 10px 0 20px;overflow: hidden;}
.consignee-scroll .consignee-item {
    float: left;
    list-style: none;
    position: relative;
    border: 1px solid #ddd;
    height: 18px;
    line-height: 18px;
    padding: 5px 10px;
    width: 120px;
    text-align: center;
    cursor: pointer;
    background-color: #fff;
}
.consignee-scroll .consignee-cont ul {
    width: 99.8%;
}
ol, ul {
    list-style: none;
}
.consignee-scroll .consignee-cont li {
    list-style: none;
    height: 30px;
    margin: 6px 0;
    float: left;
    width: 99.8%;
    padding: 0px;
    border-top: none;
}
.consignee-scroll .consignee-cont li:hover {
    background: rgba(255,245,204,0.25)
}
.consignee-scroll .consignee-item.item-hover, .consignee-scroll .consignee-item.item-selected, .consignee-scroll .consignee-item:hover {
    border: 2px solid #e4393c;
    padding: 4px 10px;
}
.consignee-scroll .consignee-item.item-selected b {
    display: block;
    position: absolute;
    right: 0;
    bottom: 0;
    width: 12px;
    height: 12px;
    overflow: hidden;
    background: url(/shop/img/selected-icon.png) no-repeat;
}
.consignee-scroll .consignee-cont .addr-detail {
    float: left;
    height: 30px;
    line-height: 30px;
}
.consignee-scroll .consignee-cont .addr-detail span {
    display: inline-block;
    margin-left: 10px;
    *float: left;
}
.consignee-content .ui-scrollbar-bg {
    background: #ebebeb !important;
    width: 9px !important;
    left: 928px !important;
    border-radius: 6px !important;
}
.ui-scrollbar-bg {
    background-position: 100% 0;
}
.ui-scrollbar-item-consignee {
    border: #c7c7c7 1px solid;
    border-radius: 7px;
    background: #c7c7c7;
    cursor: pointer;
}
.consignee-scroll .consignee-cont .addr-ops, .consignee-scroll .consignee-cont .op-btns {
    float: right;
    text-align: right;
    height: 30px;
    line-height: 30px;
}
.op-btns {
    display: none;
}
.consignee-scroll .consignee-cont .addr-ops a, .consignee-scroll .consignee-cont .op-btns a {
    margin-right: 10px;
}
.ftx-05, .ftx05 {
    color: #005ea7;
}
a {
    color: #666;
    text-decoration: none;
}
.addr-switch {
    line-height: 18px;
    cursor: pointer;
}
.addr-switch.switch-off b {
    background: url(//misc.360buyimg.com/user/purchase/2.0.0/widget/consignee-scroll/i/addr-i.png) no-repeat 0 -10px;
}
.addr-switch b {
    display: inline-block;
    vertical-align: middle;
    height: 10px;
    line-height: 10px;
    width: 9px;
    margin-left: 5px;
    background: url(//misc.360buyimg.com/user/purchase/2.0.0/widget/consignee-scroll/i/addr-i.png) no-repeat 0 0;
}
</style>
<div class="ncc-receipt-info" style="border-bottom: 0px;">
  <div class="ncc-receipt-info-title">
    <h3>收货人信息</h3>
      <a href="javascript:void(0);" style="float: right;" id="new-address">
      <span style="float: right;font-size:14px;color: #27A9E3">新增地址
          <i class="layui-icon" style="font-size: 15px; color: #27A9E3">&#xe608;</i>
      </span>
      </a>
  </div>
  <div id="addr_list" class="ncc-candidate-items">
<!--    <ul>
      <li><span class="true-name"><?php /*echo $output['address_info']['true_name'];*/?></span><span class="address"><?php /*echo intval($output['address_info']['dlyp_id']) ? '[自提服务站] ' : '';*/?><?php /*echo $output['address_info']['area_info'],$output['address_info']['address'];*/?></span><span class="phone"><i class="icon-mobile-phone"></i><?php /*echo $output['address_info']['mob_phone'] ? $output['address_info']['mob_phone'] : $output['address_info']['tel_phone'];*/?></span></li>
    </ul>-->
      <div class="step-cont" <?php if(empty($output['address_info'])){?>style="display: none" <?php }?> >
          <div id="consignee-addr-one" class="consignee-content">
              <div class="ui-scrollbar-wrap" style="position: relative; overflow: hidden; width: 938px; height: 42px; z-index: 10;">
                  <div class="consignee-scrollbar" style="left: 0px; top: 0px; overflow: hidden; position: absolute; height: 42px;">
                      <div class="ui-scrollbar-main">
                          <div class="consignee-scroll">
                              <div class="consignee-cont" id="consignee1" style="height: 42px;">

                                  <ul id="consignee-list-one" name="consignee-list">
                                      <li class="ui-switchable-panel ui-switchable-panel-selected" style="display: list-item;" selected="selected"
                                          adcode="<?php echo $output['address_info']['address_id'];?>" id="consignee_index">
                                          <div class="consignee-item item-selected" id="add-select-a">
                                              <span limit="8" title="<?php echo $output['address_info']['true_name'];?>"><?php echo $output['address_info']['true_name'];?></span><b></b>
                                          </div>
                                          <div class="addr-detail" id="add-select-b">
                                              <span class="addr-info" limit="45" title="<?php echo $output['address_info']['area_info'].'&nbsp;&nbsp;'.$output['address_info']['address'];?>">
                                                  <?php echo $output['address_info']['area_info'].'&nbsp;&nbsp;'.$output['address_info']['address'];?></span>
                                              <span class="mob-phone addr-tel"><?php echo $output['address_info']['mob_phone'];?></span>
                                              <span class="tel-phone addr-tel"><?php echo $output['address_info']['tel_phone'];?></span>
                                          </div>
                                          <div class="op-btns">
                                              <a href="javascript:void(0);" class="ftx-05 setdefault-consignee">设为默认地址</a>
                                              <a href="javascript:void(0);" class="ftx-05 edit-consignee">编辑</a>
                                              <a href="javascript:void(0);" class="ftx-05 del-consignee">删除</a>
                                          </div>
                                          <input type="hidden" value="<?php echo $output['address_info']['address_id'];?>" name="add_code" id="add_code">
                                      </li>
                                  </ul>

                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <div id="consignee-addr-all" class="consignee-content" style="display: none">
              <div class="ui-scrollbar-wrap" style="position: relative; overflow: hidden; width: 938px; height: 168px; z-index: 10;overflow-y:scroll;">
                  <div class="consignee-scrollbar" style="left: 0px; top: 0px; overflow: hidden; position: absolute;">
                      <div class="ui-scrollbar-main">
                          <div class="consignee-scroll">
                              <div class="consignee-cont" id="consignee1" style="">

                                  <ul id="consignee-list-all" name="consignee-list">
                                  <?php if(!empty($output['address_list']) && is_array($output['address_list'])){?>
                                    <?php foreach($output['address_list'] as $add_val){?>
                                      <li class="ui-switchable-panel ui-switchable-panel-selected" style="display: list-item;" id="consignee_index_<?php echo $add_val['address_id'];?>"
                                          <?php if($add_val['is_default'] == '1'){?>selected="selected"<?php }?>
                                          adname="<?php echo $add_val['true_name'];?>" adcity="<?php echo $add_val['area_info'];?>"
                                          adinfo="<?php echo $add_val['address'];?>" admobile="<?php echo $add_val['mob_phone'];?>"
                                          adphone="<?php echo $add_val['tel_phone'];?>" adcode="<?php echo $add_val['address_id'];?>" >
                                          <div class="consignee-item  <?php if($add_val['is_default'] == '1'){?>item-selected<?php }?>">
                                              <span limit="8" title="<?php echo $add_val['true_name'];?>"><?php echo $add_val['true_name'];?></span><b></b>
                                          </div>
                                          <div class="addr-detail">
                                              <span class="addr-info" limit="45" title="<?php echo $add_val['area_info'].'&nbsp;&nbsp;'.$add_val['address'];?>">
                                                  <?php echo $add_val['area_info'].'&nbsp;&nbsp;'.$add_val['address'];?></span>
                                              <span class="mob-phone addr-tel"><?php echo $add_val['mob_phone'];?></span>
                                              <span class="tel-phone addr-tel"><?php echo $add_val['tel_phone'];?></span>
                                          </div>
                                          <div class="op-btns">
                                              <a href="javascript:void(0);" class="ftx-05 setdefault-consignee" addcode="<?php echo $add_val['address_id'];?>" >设为默认地址</a>
                                              <a href="javascript:void(0);" class="ftx-05 edit-consignee" addcode="<?php echo $add_val['address_id'];?>">编辑</a>
                                              <a href="javascript:void(0);" class="ftx-05 del-consignee" addcode="<?php echo $add_val['address_id'];?>">删除</a>
                                          </div>
                                      </li>
                                    <?php }}?>
                                  </ul>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="ui-scrollbar-bg" style="position: absolute; left: 927px; top: 0px; width: 11px; z-index: 11; height: 168px; display: block;"></div>
                  <div class="ui-scrollbar-item-consignee" id="slider-vertical" style="position: absolute; left: 927px; top: 2px; width: 9px; z-index: 12; height: 134px;"></div>
              </div>
          </div>

          <div class="addr-switch switch-on" id="showAll">
              <span>更多地址</span><b></b>
          </div>
          <div class="addr-switch switch-off" id="clodeAll" style="display: none">
              <span>收起地址</span><b></b>
          </div>
      </div>
      <div id="empty-address" <?php if(!empty($output['address_info'])){?>style="display: none" <?php }?> >
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
            url:"/shop/index.php?act=member_address&op=defaultAddress",
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
                content: '/shop/index.php?act=buy&op=addRessItem',
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
                content: '/shop/index.php?act=buy&op=addRessItem&id='+id+'&sid='+show_id,
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
            url:"/shop/index.php?act=member_address&op=delAddress",
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
    $.post(SITEURL + '/index.php?act=member_address&op=change_addr', {'freight_hash':'<?php echo $output['freight_hash'];?>',city_id:city_id,'area_id':area_id}, function(data){
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