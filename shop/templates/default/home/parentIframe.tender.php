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
<div class="layui-container">
    <form id="data-list" name="data-list" method="post" action="">
    <div class="layui-row">
        <div class="layui-col-md5">
            <label class="layui-form-label" style="margin-left: -10px;">招标标题</label>
            <input type="text" name="tender_name" required lay-verify="required" placeholder="请输入标题" autocomplete="off"
                   style="width:350px" class="layui-input" value="<?php echo empty($_GET['tender_name']) ? '':$_GET['tender_name'];?>">
        </div>
<!--        <div class="layui-col-md2">
            <label class="layui-form-label" style="margin-left: -70px;">状态</label>
            <div class="select-type" style="float:left;width:120px;border-radius:0;margin-left: -10px;">
                <select id="type-list">
                    <option>全部</option>
                    <option value="1">待报价</option>
                    <option value="2">报价完成</option>
                    <option value="3">报价中</option>
                </select>
            </div>
        </div>-->
        <div class="layui-col-md5">
<!--            <label class="layui-form-label" style="margin-left: -45px;width: 95px;">招标截止日期</label>
            <input type="text" id="start" style="float: left;height:30px;width:120px">
            <i class="layui-icon" style="font-size: 30px;float: left;margin-left: -35px;">&#xe637;</i>
            <span style="font-size: 30px;float: left;margin-left: 5px;margin-right: 5px;">-</span>
            <input type="text" id="end" style="float: left;height:30px;width:120px">
            <i class="layui-icon" style="font-size: 30px;float: left;margin-left: -35px;">&#xe637;</i>-->


            <a href="javascript:void(0);" onclick="get_data()" style="text-decoration:none;height: 35px;font-size:15px;width: 75px;margin-left: 20px;"
               class="layui-btn layui-btn-primary layui-btn-small">
                <span style="margin-left: -3px;">搜索</span>
                <i class="layui-icon" style="color: #71b704;">&#xe615;</i>
            </a>
        </div>
    </div>
    </form>
    <div class="layui-row">
        <table class="layui-table" lay-filter="list" id="list" style="margin: 0px 0;">
        </table>
    </div>
</div>

<link type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/select/css/selectpick.css" rel="stylesheet" />

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
        table.render({ //其它参数在此省略13925127998 &#xe61c;
            elem:'#list',
            url: '/shop/index.php?act=tender&op=getTenderList',
            height: 472,
            skin:'line',
            cols:  [[ //标题栏
                {field: 'tenderName', title: '招标标题', width: 457, templet: '#titleTpl'},
                {field: 'type_name', title: '状态', width: 100},
                {field: 'city_name', title: '城市公司', width: 170},
                {field: 'finishDate', title: '报名截止日期', width: 170},
                {field:'validThruDate', title: '投标截止日期', width: 140},
                {field:'buttonFlag', title: '操作', width: 100, templet: '#czTpl'},
            ]],
            page: true,
            limits: [10],
            limit: 10,
            where: {
                tender_name: '<?php echo empty($_GET['tender_name']) ? '':$_GET['tender_name'];?>',
            },
            request: {
                pageName: 'page',
                limitName: 'nums',
            }
        });

    });

    layui.use('laydate', function(){
        var laydate = layui.laydate;

        //执行一个laydate实例
        laydate.render({
            elem: '#start', //指定元素
        });

        laydate.render({
            elem: '#end', //指定元素
        });
    });


    $(function() {
        $("#type-list").selectpick({
            container: '.select-type',
            height: 35, // 下拉框的高度
            width: 100, // 下拉框的宽度
            onSelect: function(value,text){
                alert("这是回调函数，选中的值："+value+" \n选中的下拉框文本："+text);
            }
        });
    });


    function get_data(){
        var params = serializeForm('data-list');
        var url = "/shop/index.php?act=tender&op=index";
        window.location.href= url + '&' + params;
    }

    function bidTender(title,end_time,tender_id,city) {
        layui.use('layer', function(){
            var layer = layui.layer;
            layer.open({
                type: 2,
                title: '我要投标',
                maxmin: false, //开启最大化最小化按钮
                resize: false,
                fixed: true,
                shade: [0.8, '#393D49'],
                area: ['650px', '600px'],
                content: '/shop/index.php?act=tender&op=tenderMaterial&title='+encodeURI(title)+'&time='+end_time+'&tender_id='+tender_id+'&city='+city,
            });
        });
    }


    function cancel(){
        layui.use('layer', function(){
            var layer = layui.layer;
            layer.closeAll();
        })
    }

    function signTender(tender_id){
        $.ajax({
            type:"POST",
            //提交的网址
            url:"/shop/index.php?act=tender&op=signTender",
            data:{tender_id: tender_id},
            datatype: "json",
            success:function(result){
                if(result == '0'){
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert('报名成功', {closeBtn: 0,title: '温馨提示',}, function(index){
                            layer.closeAll();
                            window.parent.location.reload();
                        });
                    })
                }else{
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert('报名失败', {closeBtn: 0,title: '温馨提示',}, function(index){
                            layer.closeAll();
                        });
                    })
                }
            }
        });
    }

</script>
<script type="text/html" id="titleTpl">
        <p style="text-align: left;overflow:hidden;line-height:25px;width:425px;height: 20px;text-overflow: ellipsis; white-space: nowrap;"
           title="{{d.tenderName}}">{{d.tenderName}}</p>
</script>
<script type="text/html" id="czTpl">
    {{#  if(d.buttonFlag == 1){ }}
        <a href="javascript:void(0);" onclick="bidTender('{{d.tenderName}}','{{d.validThruDate}}','{{d.tenderId}}','{{d.city_name}}')" class="layui-table-link" style="text-decoration:none;">
            <i class="layui-icon" style="font-size: 20px; color: #71b704;float: left;">&#xe629;</i>
            <p style="text-align: left;color: #71b704;">我要投标</p>
        </a>
    {{#  } else if(d.buttonFlag == 2){ }}
        <a href="javascript:void(0);" onclick="signTender('{{d.tenderId}}')" class="layui-table-link" style="text-decoration:none;">
            <i class="layui-icon" style="font-size: 20px; color: #71b704;float: left;">&#xe61c;</i>
            <p style="text-align: left;color: #71b704;">我要报名</p>
        </a>
    {{#  } else { }}
    {{#  } }}
</script>