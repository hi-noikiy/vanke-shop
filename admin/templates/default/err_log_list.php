<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/10/16
 * Time: 下午4:21
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
<div class="layui-container" style="margin-top: 30px;">
    <div class="layui-row">
        <table class="layui-table" lay-filter="list" id="list" style="margin: 0px 0;">
        </table>
    </div>
</div>

<link type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/select/css/selectpick.css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/date/css/jquery-ui-1.9.2.custom.css" type="text/css">

<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/form.js"></script>
<!-- 下拉JS样式加载 -->
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/select/js/jquery-1.8.3.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/select/js/selectpick.js"></script>

<script>
layui.use('table', function(){
    var table = layui.table;
    table.render({ //其它参数在此省略
            elem:'#list',
            url: '/admin/index.php?act=err_log&op=getLogList',
            height: 473,
            width: 1000,
            skin:'line',
            cols:  [[ //标题栏
                {field: 'name', title: '标题', width: 650, templet: '#titleTpl'},
                {field:'time', title: '时间', width: 348},
            ]],
            page: true,
            limits: [10],
            limit: 10,
            request: {
        pageName: 'page',
                limitName: 'nums',
            }
        });

    });
</script>
<script type="text/html" id="titleTpl">
    <a href="/admin/index.php?act=err_log&op=readlog&name={{d.name}}" class="layui-table-link" style="text-decoration:none;">
        <p style="text-align: left;">{{d.name}}</p>
    </a>
</script>