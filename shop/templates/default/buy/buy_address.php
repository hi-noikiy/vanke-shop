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
<div class="ncc-receipt-info">
  <div class="ncc-receipt-info-title">
    <h3>收货人信息</h3>
    <a href="javascript:void(0)" nc_type="buy_edit" id="edit_reciver">[修改]</a></div>
  <div id="addr_list" class="ncc-candidate-items">
<!--    <ul>
      <li><span class="true-name"><?php /*echo $output['address_info']['true_name'];*/?></span><span class="address"><?php /*echo intval($output['address_info']['dlyp_id']) ? '[自提服务站] ' : '';*/?><?php /*echo $output['address_info']['area_info'],$output['address_info']['address'];*/?></span><span class="phone"><i class="icon-mobile-phone"></i><?php /*echo $output['address_info']['mob_phone'] ? $output['address_info']['mob_phone'] : $output['address_info']['tel_phone'];*/?></span></li>
    </ul>-->
      <div class="step-cont">

          <div id="consignee-addr-one" class="consignee-content">
              <div class="ui-scrollbar-wrap" style="position: relative; overflow: hidden; width: 938px; height: 42px; z-index: 10;">
                  <div class="consignee-scrollbar" style="left: 0px; top: 0px; overflow: hidden; position: absolute; height: 42px;">
                      <div class="ui-scrollbar-main">
                          <div class="consignee-scroll">
                              <div class="consignee-cont" id="consignee1" style="height: 42px;">

                                  <ul id="consignee-list">
                                      <li class="ui-switchable-panel ui-switchable-panel-selected" style="display: list-item;" selected="selected"
                                          licode="<?php echo $output['address_info']['address_id'];?>" id="consignee_index">
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
                                              <a href="javascript:void(0);" onclick="default_add(this)" class="ftx-05 setdefault-consignee">设为默认地址</a>
                                              <a href="#none" class="ftx-05 edit-consignee">编辑</a>
                                              <a href="#none" class="ftx-05 del-consignee">删除</a>
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
                  <div class="consignee-scrollbar" style="left: 0px; top: 0px; overflow: hidden; position: absolute; height: 210px;">
                      <div class="ui-scrollbar-main">
                          <div class="consignee-scroll">
                              <div class="consignee-cont" id="consignee1" style="height: 210px;">

                                  <ul id="consignee-list" name="consignee-list">
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
                                              <a href="javascript:void(0);" onclick="default_add(this)" class="ftx-05 setdefault-consignee" addcode="<?php echo $add_val['address_id'];?>" >设为默认地址</a>
                                              <a href="#none" class="ftx-05 edit-consignee" addcode="<?php echo $add_val['address_id'];?>">编辑</a>
                                              <a href="#none" class="ftx-05 del-consignee" addcode="<?php echo $add_val['address_id'];?>">删除</a>
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
  </div>
</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/form.js"></script>
<script type="text/javascript">
    $("#consignee-list li").mouseover(function(){
        $(this).find(".op-btns").show();
    }).mouseleave(function(){
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

    $(".consignee-item").click(function(){
        //修改框体属性
        $("#add-select-a").find("span").attr('title',$(this).parent().attr('adname'));
        $("#add-select-a").find("span").html($(this).parent().attr('adname'));
        //修改其他属性
        $("#add-select-b").find(".addr-info").attr('title',$(this).parent().attr('adcity')+'&nbsp;&nbsp;'+$(this).parent().attr('adinfo'));
        $("#add-select-b").find(".addr-info").html($(this).parent().attr('adcity')+'&nbsp;&nbsp;'+$(this).parent().attr('adinfo'));

        $("#add-select-b").find(".mob-phone").html($(this).parent().attr('admobile'));
        $("#add-select-b").find(".tel-phone").html($(this).parent().attr('adphone'));

        $("#add_code").val($(this).parent().attr('adcode'));
        $("#consignee-addr-all").hide();$("#consignee-addr-one").show();
        $("#clodeAll").hide();$("#showAll").show();
    });

    //设置默认收货地址
function default_add(obj) {
    var id = $(obj).parent().parent().attr("adcode");
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
                        default_data(obj);
                        layer.closeAll();
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
}

function default_data(obj) {
    $("ul[name='consignee-list']").find("li").each(function () {
        $(this).find(".consignee-item").removeClass("item-selected");
    });
    var code = $(obj).parent().parent().attr("adcode");
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
    //修改收货地址

    //删除收货地址

//隐藏收货地址列表
function hideAddrList(addr_id,true_name,address,phone) {
    $('#edit_reciver').show();
	$("#address_id").val(addr_id);
	$("#addr_list").html('<ul><li><span class="true-name">'+true_name+'</span><span class="address">'+address+'</span><span class="phone"><i class="icon-mobile-phone"></i>'+phone+'</span></li></ul>');
	$('.current_box').removeClass('current_box');
	ableOtherEdit();
	$('#edit_payment').click();
}
//加载收货地址列表
$('#edit_reciver').on('click',function(){
    $(this).hide();
    disableOtherEdit('如需修改，请先保存收货人信息 ');
    $(this).parent().parent().addClass('current_box');
    $('#addr_list').load(SITEURL+'/index.php?act=buy&op=load_addr');
});
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