<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/12/4
 * Time: 上午9:59
 */
?>
<div class="ncs-goods-summary">
    <div class="name">
        <h1><?php echo $output['goods']['goods_name']; ?></h1>
        <strong><?php echo str_replace("\n", "<br>", $output['goods']['goods_jingle']);?></strong>
        <!--<span style="color:red">城市公司：<?php /*echo $output['store_info']['store_city_name'];*/?></span>-->
        </br>
        <span style="color:red">可销售城市公司：<?php echo $output['sales_area'];?></span>
    </div>
    <div class="ncs-meta">
        <?php if(!$_SESSION['member_id']){?>
            <dl>
                <dt><?php echo $lang['goods_index_goods_price'];?><?php echo $lang['nc_colon'];?></dt>
                <dd class="price">
                    <strong><?php echo $lang['currency'].ncPriceFormat($output['goods']['g_costprice']);?></strong>
                </dd>
            </dl>
        <?php  }else{//判断当前登录角色身份?>
        <dl>
            <?php if($_SESSION['identity'] == MEMBER_IDENTITY_TWO){//这是采购员?>
                <dt style='width:80px;' >高级会员价<?php echo $lang['nc_colon'];?></dt>
                <dd class="price">
                    <strong><?php echo $lang['currency'].ncPriceFormat($output['goods']['goods_price']);?></strong>
                </dd>
            <?php }else if($_SESSION['identity'] == MEMBER_IDENTITY_FIVE){//这是第三方采购员 ?>
                <dt style='width:80px;'>高级会员价<?php echo $lang['nc_colon'];?></dt>
                <dd class="cost-price"><strong><?php echo $lang['currency'].ncPriceFormat($output['goods']['goods_price']);?></strong></dd>
                <dt style='width:80px;'>普通会员价<?php echo $lang['nc_colon'];?></dt>
                <dd class="price">
                    <strong><?php echo $lang['currency'].ncPriceFormat($output['goods']['goods_third_price']);?></strong>
                </dd>
            <?php }else{//这是第普通用户 ?>
                <dt><?php echo $lang['goods_index_goods_price'];?><?php echo $lang['nc_colon'];?></dt>
                <dd class="price">
                    <strong><?php echo $lang['currency'].ncPriceFormat($output['goods']['g_costprice']);?></strong>
                </dd>
            <?php } ?>
        </dl>
        <?php }?>

            <dl class="rate">
                <dt>商品评分：</dt>
                <!-- S 描述相符评分 -->
                <dd><span class="raty" data-score="<?php echo $output['goods_evaluate_info']['star_average'];?>"></span><a href="#ncGoodsRate">共有<?php echo $output['goods']['evaluation_count']; ?>条评价</a></dd>
                <!-- E 描述相符评分 -->
            </dl>
            <!-- E 商品发布价格 -->

    </div>


    <?php if($output['goods']['goods_state'] != 10 && $output['goods']['goods_verify'] == 1){?>
        <div class="ncs-key">
            <!-- S 商品规格值-->
            <?php if (is_array($output['goods']['spec_name'])) { ?>
                <hr/>
                <?php foreach ($output['goods']['spec_name'] as $key => $val) {?>
                    <?php if(!empty($output['goods']['spec_value'][$key]) && is_array($output['goods']['spec_value'][$key])){?>
                    <dl nctype="nc-spec">
                        <dt><?php echo $val;?><?php echo $lang['nc_colon'];?></dt>
                        <dd>
                            <ul nctyle="ul_sign">
                                <?php foreach($output['goods']['spec_value'][$key] as $k => $v) {?>
                                    <?php if( $key == 1 ){?>
                                        <!-- 图片类型规格-->
                                        <li class="sp-img"><a href="javascript:void(0);" class="<?php if (isset($output['goods']['goods_spec'][$k])) {echo 'hovered';}?>" data-param="{valid:<?php echo $k;?>}" title="<?php echo $v;?>"><img src="<?php echo $output['spec_image'][$k];?>"/><?php echo $v;?><i></i></a></li>
                                    <?php }else{?>
                                        <!-- 文字类型规格-->
                                        <li class="sp-txt"><a href="javascript:void(0)" class="<?php if (isset($output['goods']['goods_spec'][$k])) { echo 'hovered';} ?>" data-param="{valid:<?php echo $k;?>}"><?php echo $v;?><i></i></a></li>
                                    <?php }?>
                                <?php }?>
                            </ul>
                        </dd>
                    </dl>
                <?php }}?>
            <?php }?>
            <!-- E 商品规格值-->
        </div>
        <!-- S 购买数量及库存 -->
        <?php if ($output['goods']['goods_state'] != 0 && $output['goods']['goods_storage'] >= 0) {?>
            <?php if($output['goods']['goods_storage'] <= 0 ){?>
                <div class="ncs-buy">
                    <div class="ncs-btn">
                        <a href="javascript:void(0);" nctype="addcart_submit" class="addcart no-addcart"
                           title="<?php echo $lang['goods_index_add_to_cart'];?>">
                            <i class="icon-shopping-cart"></i>
                            该商品已无库存，请浏览购买其它商品
                        </a>
                    </div>
                </div>
            <?php }else{?>
                <div class="ncs-buy">
                    <div style="float: left;width: 65px;">
                        购买数量<?php echo $lang['nc_colon'];?>
                    </div>
                    <div style="float: left;">
                        <div class="ncs-figure-input">
                            <input type="text" name="" id="quantity" size="3" style="width: 100px;height: 42px;" onkeyup="this.value=this.value.replace(/\D/g,'')"
                                   onafterpaste="this.value=this.value.replace(/\D/g,'')"
                                   class="input-text" value="<?php echo $output['goods']['min_num'] > 0 ? $output['goods']['min_num']:'1';?>">
                            <a href="javascript:void(0)" style="left: 100px" class="increase" nctype="increase">&nbsp;</a>
                            <a href="javascript:void(0)" style="left: 100px" class="decrease" nctype="decrease">&nbsp;</a>
                        </div>
                        <div style="top: 22px;left: 230px;" class="ncs-point" style="display: none;">
                            <!--<i style="margin-left: -10px;"></i>-->
                            <!-- S 库存 -->
                            <span>您选择的商品库存<strong nctype="goods_stock"><?php echo $output['goods']['goods_storage']; ?></strong>
                                <?php echo $lang['nc_jian'];?></span>
                            <!-- E 库存 -->
                        </div>
                    </div>
                </div>
                <div class="ncs-buy">
                    <!-- S 购买按钮 -->
                    <div class="ncs-btn">
                        <!--  限制购买-->
                        <?php if($output['goods']['goods_state'] == 0){ ?>
                            <a class="addcart" style="background-color:#777">当前商品已经下架</a>
                            <!--  限制购买区域-->
                        <?php }else if($_SESSION['identity'] == MEMBER_IDENTITY_FIVE && $output['buy_qs'] != 1){ ?>
                            <a class="addcart">您不能购买当前城市公司的商品</a>
                        <?php }else if($_SESSION['identity'] == MEMBER_IDENTITY_THREE || $_SESSION['identity'] == MEMBER_IDENTITY_FOUR){ ?>
                            <a class="addcart">您不能购买当前城市公司的商品</a>
                        <?php }else if($output['authority'] == 2){ ?>
                            <a class="addcart">您不能购买当前城市公司的商品</a>
                        <?php }else{?>
                            <!-- 加入购物车-->
                            <a href="javascript:void(0);" nctype="addcart_submit" class="addcart"
                               title="<?php echo $lang['goods_index_add_to_cart'];?>">
                                <i class="icon-shopping-cart"></i>
                                <?php echo $lang['goods_index_add_to_cart'];?>
                            </a>
                            <!-- 立即购买-->
                            <a href="javascript:void(0);" nctype="buynow_submit" class="buynow"
                               title="<?php echo $output['goods']['buynow_text'];?>">
                                <?php echo $output['goods']['buynow_text'];?>
                            </a>
                        <?php }?>


                        <!-- S 加入购物车弹出提示框 -->
                        <div class="ncs-cart-popup">
                            <dl>
                                <dt><?php echo $lang['goods_index_cart_success'];?><a title="<?php echo $lang['goods_index_close'];?>" onClick="$('.ncs-cart-popup').css({'display':'none'});">X</a></dt>
                                <dd><?php echo $lang['goods_index_cart_have'];?> <strong id="bold_num"></strong> <?php echo $lang['goods_index_number_of_goods'];?> <?php echo $lang['goods_index_total_price'];?><?php echo $lang['nc_colon'];?><em id="bold_mly" class="saleP"></em></dd>
                                <dd class="btns"><a href="javascript:void(0);" class="ncs-btn-mini ncs-btn-green" onclick="location.href='<?php echo SHOP_SITE_URL.DS?>index.php?act=cart'"><?php echo $lang['goods_index_view_cart'];?></a> <a href="javascript:void(0);" class="ncs-btn-mini" value="" onclick="$('.ncs-cart-popup').css({'display':'none'});"><?php echo $lang['goods_index_continue_shopping'];?></a></dd>
                            </dl>
                        </div>
                        <!-- E 加入购物车弹出提示框 -->
                    </div>
                    <!-- E 购买按钮 -->
                </div>
            <?php }?>
        <?php } ?>

    <?php }else if($output['goods']['goods_state'] == 10){?>
        <div class="ncs-buy">
            <div class="ncs-saleout">
                <dl>
                    <dt><i class="icon-info-sign"></i><?php echo $lang['goods_index_is_no_goods_state'];?></dt>
                    <dd><?php echo $lang['goods_index_is_no_show_message_one'];?></dd>
                    <dd><?php echo $lang['goods_index_is_no_show_message_two_1'];?>&nbsp;<a href="<?php echo urlShop('show_store', 'index', array('store_id'=>$output['goods']['store_id']), $output['store_info']['store_domain']);?>" class="ncbtn-mini"><?php echo $lang['goods_index_is_no_show_message_two_2'];?></a>&nbsp;<?php echo $lang['goods_index_is_no_show_message_two_3'];?> </dd>
                </dl>
            </div>
        </div>
    <?php }else if($output['goods']['goods_verify'] != 1){?>
        <div class="ncs-buy">
            <div class="ncs-saleout">
                <dl>
                    <dt><i class="icon-info-sign"></i><?php echo $lang['goods_index_is_no_goods_verify'];?></dt>
                    <dd><?php echo $lang['goods_index_is_no_show_message_one'];?></dd>
                    <dd><?php echo $lang['goods_index_is_no_show_message_two_1'];?>&nbsp;<a href="<?php echo urlShop('show_store', 'index', array('store_id'=>$output['goods']['store_id']), $output['store_info']['store_domain']);?>" class="ncbtn-mini"><?php echo $lang['goods_index_is_no_show_message_two_2'];?></a>&nbsp;<?php echo $lang['goods_index_is_no_show_message_two_3'];?> </dd>
                </dl>
            </div>
        </div>
    <?php }else{?>
        <div class="ncs-buy">
            <div class="ncs-saleout">
                <dl>
                    <dt><i class="icon-info-sign"></i><?php echo $lang['goods_index_is_no_show'];?></dt>
                    <dd><?php echo $lang['goods_index_is_no_show_message_one'];?></dd>
                    <dd><?php echo $lang['goods_index_is_no_show_message_two_1'];?>&nbsp;<a href="<?php echo urlShop('show_store', 'index', array('store_id'=>$output['goods']['store_id']), $output['store_info']['store_domain']);?>" class="ncbtn-mini"><?php echo $lang['goods_index_is_no_show_message_two_2'];?></a>&nbsp;<?php echo $lang['goods_index_is_no_show_message_two_3'];?> </dd>
                </dl>
            </div>
        </div>
    <?php }?>
    <!-- E 购买数量及库存 -->

    <!--E 商品信息 -->
</div>
<script type="text/javascript">
    /* 商品购买数量增减js */
    $('#quantity').blur(function(){
        var nums = $(this);
        //库存校验
        var storage = '<?php echo $output['goods']['goods_storage']; ?>';
        if( parseInt(nums.val()) > parseInt(storage) ){
            temp_amount = parseInt(storage);
        }
        //最大购买校验
        var max_num = '<?php echo $output['goods']['max_num']; ?>';
        if( parseInt(nums.val()) > parseInt(max_num) ){
            temp_amount = parseInt(max_num);
        }
        //最小购买校验
        var min_num = '<?php echo $output['goods']['min_num']; ?>';
        if(parseInt(nums.val()) < parseInt(min_num)){
            temp_amount = parseInt(min_num);
        }
        $(this).val(temp_amount);
    });
    // 增加
    $('a[nctype="increase"]').click(function(){
        max_num = parseInt("<?php echo $output['goods']['max_num'];?>");
        nums = parseInt($('#quantity').val());
        if( max_num > 0 ) {
            if (nums + 1 <= max_num) {
                $('#quantity').val(nums+1);
            }else{
                alert('该商品最多只能购买'+max_num+'件');
            }
        }else{
            $('#quantity').val(nums+1);
        }
    });
    //减少
    $('a[nctype="decrease"]').click(function(){
        min_num = parseInt("<?php echo $output['goods']['min_num'];?>");
        nums= parseInt($('#quantity').val());
        if(nums > 1){
            //处理购买最小限制
            if( min_num > 0 ){
                if( nums - 1 >= min_num ){
                    $('#quantity').val(nums-1);
                }else{
                    alert('该商品最少需要购买'+min_num+'件');
                }
            }else{
                $('#quantity').val(nums-1);
            }
        }
    });

    // 加入购物车
    $('a[nctype="addcart_submit"]').click(function(){
        addcart(<?php echo $output['goods']['goods_id'];?>, checkQuantity(),'addcart_callback');
    });

    $('a[nctype="buynow_submit"]').click(function(){
        buynow(<?php echo $output['goods']['goods_id']?>,checkQuantity());
    });
</script>
