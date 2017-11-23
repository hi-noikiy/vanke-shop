<?php ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<link rel="stylesheet" type="text/css" href="<?php echo BASE_SITE_URL;?>/shop/templates/default/css/goods_quick.css"  />

<style>

</style>
<div class="wrap">
    <div class="tabmenu">
        <ul class="tab pngFix">
            <li class="active"><a href="index.php?act=member_information&op=get_goods">快速下单</a></li></ul>
    </div>
  <form method="get" name="formSearch" id="formSearch" width="100%" >
    <input type="hidden" name="act" value="member_information">
    <input type="hidden" name="op" value="get_goods">
    <table class="tb-type1 noborder search" >
      <tbody>
        <tr>
          <th><label for="search_goods_name"> <?php echo $lang['goods_index_name'];?></label></th>
          <td><input type="text" value="<?php echo $output['search']['search_goods_name'];?>" name="search_goods_name" id="search_goods_name" class="txt"></td>
          <th><label for="search_commonid">平台货号</label></th>
          <td><input type="text" value="<?php echo $output['search']['search_commonid']?>" name="search_commonid" id="search_commonid" class="txt" /></td>
          <th><label><?php echo $lang['goods_index_class_name'];?></label></th>
          <td width="40%" id="searchgc_td"></td><input type="hidden" id="choose_gcid" name="choose_gcid" value="0"/>
        </tr>
        <tr>
          <th><label for="search_store_name"><?php echo $lang['goods_index_store_name'];?></label></th>
          <td><input type="text" value="<?php echo $output['search']['search_store_name'];?>" name="search_store_name" id="search_store_name" class="txt"></td>
          <th><label><?php echo $lang['goods_index_brand'];?></label></th>
          <td>
            <div id="ajax_brand" class="ncsc-brand-select w180">
                  <div class="selection">
                  	<input name="b_name" id="b_name" value="<?php echo $_REQUEST['b_name'];?>" type="text" class="w140" readonly="readonly" />
                  	<input type="hidden" name="b_id" id="b_id" value="<?php echo $_REQUEST['b_id'];?>" />
                  </div>
                  <div class="ncsc-brand-select-container">
                    <div class="brand-index" data-url="index.php?act=common&op=ajax_get_brand">
                      <div class="letter" nctype="letter">
                        <ul>
                          <li><a href="javascript:void(0);" data-letter="all">全部品牌</a></li>
                          <li><a href="javascript:void(0);" data-letter="A">A</a></li>
                          <li><a href="javascript:void(0);" data-letter="B">B</a></li>
                          <li><a href="javascript:void(0);" data-letter="C">C</a></li>
                          <li><a href="javascript:void(0);" data-letter="D">D</a></li>
                          <li><a href="javascript:void(0);" data-letter="E">E</a></li>
                          <li><a href="javascript:void(0);" data-letter="F">F</a></li>
                          <li><a href="javascript:void(0);" data-letter="G">G</a></li>
                          <li><a href="javascript:void(0);" data-letter="H">H</a></li>
                          <li><a href="javascript:void(0);" data-letter="I">I</a></li>
                          <li><a href="javascript:void(0);" data-letter="J">J</a></li>
                          <li><a href="javascript:void(0);" data-letter="K">K</a></li>
                          <li><a href="javascript:void(0);" data-letter="L">L</a></li>
                          <li><a href="javascript:void(0);" data-letter="M">M</a></li>
                          <li><a href="javascript:void(0);" data-letter="N">N</a></li>
                          <li><a href="javascript:void(0);" data-letter="O">O</a></li>
                          <li><a href="javascript:void(0);" data-letter="P">P</a></li>
                          <li><a href="javascript:void(0);" data-letter="Q">Q</a></li>
                          <li><a href="javascript:void(0);" data-letter="R">R</a></li>
                          <li><a href="javascript:void(0);" data-letter="S">S</a></li>
                          <li><a href="javascript:void(0);" data-letter="T">T</a></li>
                          <li><a href="javascript:void(0);" data-letter="U">U</a></li>
                          <li><a href="javascript:void(0);" data-letter="V">V</a></li>
                          <li><a href="javascript:void(0);" data-letter="W">W</a></li>
                          <li><a href="javascript:void(0);" data-letter="X">X</a></li>
                          <li><a href="javascript:void(0);" data-letter="Y">Y</a></li>
                          <li><a href="javascript:void(0);" data-letter="Z">Z</a></li>
                          <li><a href="javascript:void(0);" data-letter="0-9">其他</a></li>
                        </ul>
                      </div>
                      <div class="search" nctype="search"><input name="search_brand_keyword" id="search_brand_keyword" type="text" class="text" placeholder="品牌名称关键字查找"/><a href="javascript:void(0);" class="ncsc-btn-mini" style="vertical-align: top;">Go</a></div>
                    </div>
                    <div class="brand-list" nctype="brandList">
                    <ul nctype="brand_list">
                        <?php if(is_array($output['brand_list']) && !empty($output['brand_list'])){?>
                        <?php foreach($output['brand_list'] as $val) { ?>
                        <li data-id='<?php echo $val['brand_id'];?>'data-name='<?php echo $val['brand_name'];?>'><em><?php echo $val['brand_initial'];?></em><?php echo $val['brand_name'];?></li>
                        <?php } ?>
                        <?php }?>
                    </ul>
                    </div>
                    <div class="no-result" nctype="noBrandList" style="display: none;">没有符合"<strong>搜索关键字</strong>"条件的品牌</div>
                  	</div>
                 </div>
          </td>

          <td ><a href="javascript:void(0);" id="ncsubmit" class="ncm-btn ncm-btn-green ajax_post_state" title="查询">&nbsp;查询</a></td>
          <td class="w120">&nbsp;</td>
        </tr>
      </tbody>
    </table>
  </form>

  <form method='post' id="form_goods" action="<?php echo urlAdmin('goods', 'goods_del');?>" >
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2" width="100%">
      <thead>
        <tr class="thead">
          <th class="w24"></th>
          <th class="w60 align-center" style="display:none;">货号</th>
          <th colspan="2" class="w300"><?php echo $lang['goods_index_name'];?></th>
            <th class="w177 align-center">规格</th>
          <th><?php echo $lang['goods_index_brand'];?>&<?php echo $lang['goods_index_class_name'];?></th>
            <th class="w72 align-center">价格</th>
            <th class="w72 align-center">库存</th>
            <th class="w72 align-center">购买数量</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($output['goods_list']) && is_array($output['goods_list'])) { ?>
        <?php foreach ($output['goods_list'] as $k => $v) {?>
        <tr class="hover edit">
            <td><input type="checkbox" name="gs_id[]" value="" commonid="<?php echo $v['goods_commonid'];?>" id="check_<?php echo $v['goods_commonid'];?>"></td>
          <td style="display:none;" ><i class="icon-plus-sign" style="cursor: pointer;" nctype="ajaxGoodsList" data-comminid="<?php echo $v['goods_commonid'];?>" title="点击展开查看此商品全部规格；规格值过多时请横向拖动区域内的滚动条进行浏览。"></i></td>
          <td class="align-center" style="display:none;"><?php echo $v['goods_commonid'];?></td>
          <td class="w60 picture"><div class="size-56x56"><span class="thumb size-56x56"><i></i><img src="<?php echo thumb($v, 60);?>" onload="javascript:DrawImage(this,56,56);"/></span></div></td>
          <td class="w300" style="">
          <dl class="goods-info"><dt class="goods-name" style="width:300px;overflow: hidden;  text-overflow: ellipsis;  white-space: nowrap;" title="<?php echo $v['goods_name'];?>"><?php echo $v['goods_name'];?></dt>
            </td>
            <td align="center" class="w177" id="spec_<?php echo $v['goods_commonid'];?>" good_id="">
                <select  style="display:none;width:177px;" id="<?php echo $v['goods_commonid'];?>" stype="spec">
                </select>
            </td>
          <td style="overflow: hidden;  text-overflow: ellipsis;  white-space: nowrap;" title="<?php echo $v['gc_name'];?>">
            <p><?php echo $v['gc_name'];?></p>
<!--            <p class="goods-brand">品牌：--><?php //echo $v['brand_name'];?><!--</p>-->
            </td>
          <td class="align-center" id="price_<?php echo $v['goods_commonid'];?>"><?php echo $v['goods_price']?></td>
          <td class="align-center" id="sum_<?php echo $v['goods_commonid'];?>"><?php echo $output['storage_array'][$v['goods_commonid']]['sum']?></td>
            <td>
                <div class="ncs-figure-input">
                    <input type="text" name="" id="quantity<?php echo $v['goods_commonid'];?>" value="1" size="3" maxlength="6" class="input-text" <?php if ($output['goods']['is_fcode'] == 1) {?>readonly<?php }?>>
                    <a href="javascript:void(0)" id="increase<?php echo $v['goods_commonid'];?>" goodid ="" class="increase" <?php if ($output['goods']['is_fcode'] != 1) {?>nctype="increase"<?php }?>>&nbsp;</a>
                    <a href="javascript:void(0)" id="decrease<?php echo $v['goods_commonid'];?>" goodid =""  class="decrease" <?php if ($output['goods']['is_fcode'] != 1) {?>nctype="decrease"<?php }?>>&nbsp;</a>
                </div>

            </td>
        </tr>
        <tr style="display:none;">
          <td colspan="20"><div class="ncsc-goods-sku"></div></td>
        </tr>
        <?php } ?>
        <?php } else { ?>
        <tr class="no_data">
          <td colspan="15"><?php echo $lang['nc_no_record'];?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr class="tfoot">
            <td colspan="3"></td>
          <td colspan="6" class="align-center">
            <div class="pagination"> <?php echo $output['page'];?> </div>
          </td>
            <td colspan="1">
                <div class="ncs-btn">
                    <a href="javascript:void(0);" nctype="addcart_submit" class="addcart " title="添加购物车"><i class="icon-shopping-cart"></i>添加购物车</a>
                    <!-- S 加入购物车弹出提示框 -->
                    <div class="ncs-cart-popup">
                        <dl>
                            <dt><?php echo $lang['goods_index_cart_success'];?><a title="<?php echo $lang['goods_index_close'];?>" onClick="$('.ncs-cart-popup').css({'display':'none'});">X</a></dt>
                            <dd><?php echo $lang['goods_index_cart_have'];?> <strong id="bold_num"></strong> <?php echo $lang['goods_index_number_of_goods'];?> <?php echo $lang['goods_index_total_price'];?><?php echo $lang['nc_colon'];?><em id="bold_mly" class="saleP"></em></dd>
                            <dd class="btns"><a href="javascript:void(0);" class="ncs-btn-mini ncs-btn-green" onclick="location.href='<?php echo SHOP_SITE_URL.DS?>index.php?act=cart'"><?php echo $lang['goods_index_view_cart'];?></a> <a href="javascript:void(0);" class="ncs-btn-mini" value="" onclick="$('.ncs-cart-popup').css({'display':'none'});"><?php echo $lang['goods_index_continue_shopping'];?></a></dd>
                        </dl>
                    </div>
                </div>
                <!-- E 加入购物车弹出提示框 -->
            </td>
        </tr>
      </tfoot>
    </table>
  </form>

</div>

<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/dialog/dialog.js" id="dialog_js" charset="utf-8"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/jquery-ui-1.10.1.custom/css/redmond/jquery-ui-1.10.1.custom.min.css" rel="stylesheet"/>
<script src="<?php echo RESOURCE_SITE_URL;?>/jquery-ui-1.10.1.custom/js/jquery-ui-1.10.1.custom.min.js"></script>




<link href="<?php echo RESOURCE_SITE_URL;?>/jqueryui-editable/css/jqueryui-editable.css" rel="stylesheet"/>
<script src="<?php echo RESOURCE_SITE_URL;?>/jqueryui-editable/js/jqueryui-editable.min.js"></script>


<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.edit.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/admincp.js" charset="utf-8"></script>
<style>
    .ui-button-icon-only .ui-button-text {
        padding: .4em;
        display: none;
    }
</style>
<script type="text/javascript">
var SITEURL = "<?php echo SHOP_SITE_URL; ?>";
var goodsMap ={};
$(function(){

//    $.fn.editable.defaults.mode = 'popup';

    //商品分类
	init_gcselect(<?php echo $output['gc_choose_json'];?>,<?php echo $output['gc_json']?>);
	/* AJAX选择品牌 */
    $("#ajax_brand").brandinit();

    $('#ncsubmit').click(function(){
       $('#formSearch').submit();
    });
    /* 商品购买数量增减js */
    // 增加
    $(document).on('click','a[nctype="increase"]',function(){
        var num = parseInt($('#quantity'+$(this).attr("commonid")).val());
        var max =goodsMap[$(this).attr("goodid")]['goods_storage'];
        if(num < max){
             $('#quantity'+$(this).attr("commonid")).val(num+1);
        }
    });
    //减少
    $(document).on('click','a[nctype="decrease"]',function(){
        var num = parseInt($('#quantity'+$(this).attr("commonid")).val());
        if(num > 1){
            $('#quantity'+$(this).attr("commonid")).val(num-1);
        }
    });

    //规格下拉列表绑定
    $(document).on('change','select[stype="spec"]',function(){
        var temO = goodsMap[$(this).val()];
        var temId =  $(this).attr("id");
        //alert();
        initEveryRow(temId,temO);
    });
    function initEveryRow(commonId,pro){
        $("#check_"+commonId).val(pro.goods_id);//修改checkbox td
        $("#price_"+commonId).html(pro.goods_price);//修改价格td，
        $("#sum_"+commonId).html(pro.goods_storage);//修改库存td
        $("#increase"+commonId).attr('goodid',pro.goods_id);//修改增加按钮的属性
        $("#increase"+commonId).attr('commonid',commonId);//修改增加按钮的属性
        $("#decrease"+commonId).attr('goodid',pro.goods_id);//修改减少按钮的属性
        $("#decrease"+commonId).attr('commonid',commonId);//修改减少按钮的属性
        $("#quantity"+commonId).val(1);//购买数量置为1

    }
    var firstPro = "";
    // ajax获取商品列表
    $('i[nctype="ajaxGoodsList"]').toggle(
        function(){
            $(this).removeClass('icon-plus-sign').addClass('icon-minus-sign');
            var _parenttr = $(this).parents('tr');
            var _commonid = $(this).attr('data-comminid');
            var _div = _parenttr.next().find('.ncsc-goods-sku');
            if (_div.html() == '') {
                $.getJSON('index.php?act=member_information&op=get_goods_list_ajax' , {commonid : _commonid}, function(date){
                    if (date != 'false') {
                        var _ul = $('<ul class="ncsc-goods-sku-list"></ul>');
                        $.each(date, function(i, o){
                            goodsMap[o.goods_id] = o;
                            if(i==0){
                                firstPro = o;
                            }
                            if(o.goods_spec){
                                $("#"+_commonid).append(o.goods_spec);
                                $("#price_"+_commonid).html(o.goods_price);//修改价格，库存
                                $("#sum_"+_commonid).html(o.goods_storage);//
                            }else{
                                $("#"+_commonid).remove();
                                $("#spec_"+_commonid).append('无');
                            }
                            //$("#spec_"+_commonid).attr("good_id",o.goods_id);

                            $("#"+_commonid).show();

                            /*var inStr = '<li>' +
                                '<input type="checkbox" name="gs_id[]" value="' + o.goods_id + '" product="' + o.goods_serial + '" commonId="' + _commonid + '">' +
                                '<div class="ncs-figure-input">'+
                                '<input type="text" name="" id="quantity' + o.goods_id + '" value="1" size="3" maxlength="6" class="input-text" <?php if ($output['goods']['is_fcode'] == 1) {?>readonly<?php }?>>'+
                                '<a href="javascript:void(0)" goodid ="' + o.goods_id + '" class="increase" <?php if ($output['goods']['is_fcode'] != 1) {?>nctype="increase"<?php }?>>&nbsp;</a> <a href="javascript:void(0)" goodid ="' + o.goods_id + '"  class="decrease" <?php if ($output['goods']['is_fcode'] != 1) {?>nctype="decrease"<?php }?>>&nbsp;</a> ' +
                                '</div>'+
                                '<div class="goods-thumb" style="display:none;" title="商家货号：' + o.goods_serial + '">' +
                                '<a href="' + o.url + '" target="_blank"></a></div>' + o.goods_spec + '' +
                                '<div class="goods-price" style="display:none;" >外部物料编号：<br/><em style="width:100px;" title="' + o.goods_serial + '">' + o.goods_serial + '</em></div>' +
                                '<div class="goods-price">价格：<em title="￥' + o.goods_price + '">￥' + o.goods_price + '</em></div><div class="goods-storage">库存：<em title="' + o.goods_storage + '">' + o.goods_storage + '</em></div>' +
                                '<a href="' + o.url + '" target="_blank" class="ncsc-btn-mini">查看商品详情</a>' +
                                '</li>';
                            $(inStr).appendTo(_ul);*/
                        });
                        //左边的复选框、价格、库存、增加/减少购买数量、购买数量初始化
                        initEveryRow(_commonid,firstPro)
                       /* _ul.appendTo(_div);
                        _parenttr.next().show();
                        // 计算div的宽度
                        _div.css('width', $(".wrap").width());

                        $(".ps-scrollbar-y-rail").remove();*/
                    }
                });
            } else {
            	_parenttr.next().show()
            }
        },
        function(){
            $(this).removeClass('icon-minus-sign').addClass('icon-plus-sign');
            $(this).parents('tr').next().hide();
        }
    );
    $('i[nctype="ajaxGoodsList"]').click();
    // 加入购物车。只需商品数量和id
   $('a[nctype="addcart_submit"]').click(function(){
       var goodsIdArray =new Array();
       $('#form_goods').find('input[name="gs_id[]"]:checked').each(function(){
           var temGood = {};
           var goods_id = parseInt($(this).val());
           temGood['goods_id']=goods_id;
           temGood['quantity']=parseInt($('#quantity'+$(this).attr("commonid")).val());;
           goodsIdArray.push(temGood);
       });
       if(goodsIdArray.length > 0){
           addCartBatch(goodsIdArray,'addcart_callback');
       }
    });

});
/* 加入购物车后的效果函数 */
function addcart_callback(data){
    $('#bold_num').html(data.num);
    $('#bold_mly').html(price_format(data.amount));
    $('.ncs-cart-popup').fadeIn('fast');
}
// 获得选中ID
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
