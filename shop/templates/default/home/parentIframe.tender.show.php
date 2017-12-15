<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/10/26
 * Time: 上午9:36
 */
?>
<?php ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>招标信息</title>
    <link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
    <style>
        .layui-table-tool{ text-align:center}
        a{text-decoration:none}
    </style>
</head>
<body>
<div class='layui-col-md3' style='width:600px;margin:0 auto;margin-top: 20px;'>
    <div class="layui-row">
        <div class="layui-col-md5">
            <label class="layui-form-label" style="margin-left: -10px;">招标标题</label>
            <div class='layui-form-mid layui-word-aux' title="<?php echo $output['title'];?>"
                 style="overflow:hidden;line-height:25px;width:480px;height: 20px;text-overflow: ellipsis; white-space: nowrap;"><?php echo $output['title'];?></div>
        </div>
    </div>
    <div class="layui-row">
        <div class="layui-col-md5">
            <label class="layui-form-label" style="margin-left: -10px;">城市公司</label>
            <div class='layui-form-mid layui-word-aux'><?php echo $output['city'];?></div>
        </div>
    </div>
    <div class="layui-row">
        <div class="layui-col-md5">
            <label class="layui-form-label" style="margin-left: -10px;">截止时间</label>
            <div class='layui-form-mid layui-word-aux'><?php echo $output['end_time'];?></div>
        </div>
    </div>
    <div class="layui-row">
        <table class="layui-table" lay-filter="list" id="list" style="margin: 0px 0;">
        </table>
    </div>
    <div class="layui-row">
        <button type="reset" onclick="cancel_all()" style="text-decoration:none;margin-left: 250px;margin-top: 20px;"
                class="layui-btn layui-btn-primary">关&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;闭</button>
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
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/form.js"></script>

<script>
    layui.use('table', function(){
        var table = layui.table;
        table.render({ 
            elem:'#list',
            url: '/shop/index.php?act=tender&op=getTenderMaterial&tender_id=<?php echo $output['tender_id'];?>',
            height: 276,
            skin:'line',
            cols:  [[ //标题栏
                {field: 'fileName', title: '标书名称', width: 448, templet: '#titleTpl'},
                {field:'fileUrl', title: '操作', width: 150, templet: '#czTpl'},
            ]],
            page: true,
            limits: [5],
            limit: 5,
            where: {
                tender_name: '<?php echo empty($_GET['tender_name']) ? '':$_GET['tender_name'];?>',
            },
            request: {
                pageName: 'page',
                limitName: 'nums',
            }
        });

    });



    function cancel_all(){
        parent.cancel();
    }




    function down(tender_name, tender_url){
        var $eleForm = $("<form method='get'></form>");
        $eleForm.attr("action",tender_url);
        $(document.body).append($eleForm);
        //提交表单，实现下载
        $eleForm.submit();
    }


</script>
<script type="text/html" id="titleTpl">
    <p style="text-align: left;">{{d.fileName}}</p>
</script>
<script type="text/html" id="czTpl">
    <a href="javascript:void(0);" onclick="down('{{d.fileName}}','{{d.fileUrl}}')" style="text-decoration:none;line-height:30px;background-color: #71b704;" class="layui-btn">
        <i class="layui-icon" style="margin-top: -5px;">&#xe67c;</i>下载标书
    </a>
</script>
