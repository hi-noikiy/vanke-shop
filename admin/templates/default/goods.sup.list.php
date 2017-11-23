<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/10/19
 * Time: 上午10:19
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>确认发货</title>
    <link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
    <style>
        .layui-table-tool{ text-align:center}
    </style>
</head>
<body>
<div class="layui-container" style="width:1250px;padding:0 0;">
    <div class="layui-row">
        <table class="layui-table" lay-filter="list" id="list" style="margin: 0px 0;margin-top: -10px;">
        </table>
    </div>
</div>

<link type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/select/css/selectpick.css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/date/css/jquery-ui-1.9.2.custom.css" type="text/css">

<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/form.js"></script>

<script>
layui.use('table', function(){
    var table = layui.table;
    table.render({ //其它参数在此省略templet: '#titleTpl'9750
            elem:'#list',
            url: '/admin/index.php?act=goods&op=getGoodSupList&cm_id=<?php echo $output['commonid'];?>',
            height: 472,
            width: 1250,
            skin:'line',
            cols:  [[ //标题栏
                {field: 'img_url', title: '缩略图', width: 80, templet: '#imgTpl'},
                {field: 'materiel_code', title: '外部物料', width: 150},
                {field: 'to_product_id', title: '内部物料', width: 140},
                {field: 'sup', title: '规格', width: 350},
                {field: 'goods_price', title: '价格', width: 100},
                {field: 'g_costprice', title: '市场价', width: 100},
                {field: 'goods_third_price', title: '第三方价', width: 100},
                {field: 'goods_marketprice', title: '协议价', width: 100},
                {field: 'goods_storage', title: '库存', width: 110},
                {field: 'max_num', title: '最大购买', width: 110},
                {field: 'min_num', title: '最小购买', width: 110},
                {field: 'goods_salenum', title: '销售量', width: 110},
                {field: 'goods_id', title: '', width: 50, templet: '#listTpl'},
            ]],
            page: true,
            limits: [10],
            limit: 10,
            where: {
                commonid: '<?php echo $output['commonid'];?>',
            },
            request: {
        pageName: 'page',
                limitName: 'nums',
            }
        });

    });
</script>
<script type="text/html" id="imgTpl">
    <a href="/shop/item-{{d.goods_id}}.html" target="view_window" class="layui-table-link" style="text-decoration:none;">
        <img src="{{d.img_url}}">
    </a>
</script>
<script type="text/html" id="listTpl">
    <a href="/shop/item-{{d.goods_id}}.html" target="view_window" class="layui-table-link" style="text-decoration:none;">
        <i class="layui-icon" style="font-size: 20px; color: #1E9FFF;">&#xe63c;</i>
    </a>
</script>