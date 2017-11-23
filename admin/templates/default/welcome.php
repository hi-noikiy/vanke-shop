<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.mousewheel.js"></script>

<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<link rel="stylesheet" href="../css/font_eolqem241z66flxr.css" media="all" />
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/main.css" media="all" />
<style>
    cite{color: beige;}
</style>

<div class="panel_box row" style="margin-left: 30px;margin-top: 30px;width:1200px">
    <div class="panel col">
        <a href="javascript:;">
            <div class="panel_icon">
                <i class="layui-icon" data-icon="&#xe613;">&#xe613;</i>
                <cite>认证待审核</cite>
            </div>
            <div class="panel_word newMessage">
                <span><?php echo $output['allnum']['num_a'];?></span>
            </div>
        </a>
    </div>
    <div class="panel col">
        <a href="javascript:;">
            <div class="panel_icon" style="background-color:#FF5722;">
                <i class="layui-icon" data-icon="&#xe632;">&#xe632;</i>
                <cite>开店待审核</cite>
            </div>
            <div class="panel_word userAll">
                <span><?php echo $output['allnum']['num_b'];?></span>
            </div>
        </a>
    </div>
    <div class="panel col">
        <a href="javascript:;" onclick="end_list()">
            <div class="panel_icon" style="background-color:#009688;">
                <i class="layui-icon" data-icon="&#xe64f;">&#xe64f;</i>
                <cite>即将到期供应商</cite>
            </div>
            <div class="panel_word userAll">
                <span><?php echo $output['allnum']['num_c'];?></span>
            </div>
        </a>
    </div>
    <div class="panel col">
        <a href="javascript:;">
            <div class="panel_icon" style="background-color:#5FB878;">
                <i class="layui-icon" data-icon="&#xe62f;">&#xe62f;</i>
                <cite>商品上架待审核</cite>
            </div>
            <div class="panel_word imgAll">
                <span><?php echo $output['allnum']['num_e'];?></span>
            </div>
        </a>
    </div>
    <div class="panel col">
        <a href="javascript:;">
            <div class="panel_icon" style="background-color:#F7B824;">
                <i class="layui-icon" data-icon="&#xe601;">&#xe601;</i>
                <cite>商品下架待审核</cite>
            </div>
            <div class="panel_word waitNews">
                <span><?php echo $output['allnum']['num_d'];?></span>
            </div>
        </a>
    </div>
<!--    <div class="panel col max_panel">
        <a href="javascript:;" data-url="page/news/newsList.html">
            <div class="panel_icon" style="background-color:#2F4056;">
                <i class="iconfont icon-text" data-icon="icon-text"></i>
                <cite>文章列表</cite>
            </div>
            <div class="panel_word allNews">
                <span></span>
                <cite>文章列表</cite>
            </div>
        </a>
    </div>-->
</div>

<script type="text/javascript">
    <?php if($output['suptimeend'] > 0){?>
    $(document).ready(function(){
        end_list();
    });
    <?php }?>
</script>

<script type="text/javascript">
var normal = ['week_add_member','week_add_product'];
var work = ['store_joinin','store_bind_class_applay','store_reopen_applay','store_expired','store_expire','brand_apply','cashlist','groupbuy_verify_list','points_order','complain_new_list','complain_handle_list', 'product_verify','inform_list','refund','return','vr_refund','cms_article_verify','cms_picture_verify','circle_verify','check_billno','pay_billno','mall_consult','delivery_point','offline'];
$(document).ready(function(){
	$.getJSON("index.php?act=dashboard&op=statistics", function(data){
	  $.each(data, function(k,v){
		  $("#statistics_"+k).html(v);
		  if (v!= 0 && $.inArray(k,work) !== -1){
			$("#statistics_"+k).parent().parent().parent().removeClass('none').addClass('high');
		  }else if (v == 0 && $.inArray(k,normal) !== -1){
			$("#statistics_"+k).parent().parent().parent().removeClass('normal').addClass('none');
		  }
	  });
	});
	//自定义滚定条
	$('#system-info').perfectScrollbar();
});

function end_list(){
    layui.use('layer', function(){
        var layer = layui.layer;
        layer.open({
            type: 2,
            title: '最近一个月即将到期供应商',
            shade: false,
            maxmin: false, //开启最大化最小化按钮
            resize: false,
            area: ['1250px', '580px'],
            content: '/admin/index.php?act=store&op=getSupplierTimeEnd',
        });
    });
}
</script>
