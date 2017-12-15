<?php ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>询价信息</title>
    <link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
    <style>
        .layui-table-tool{ text-align:center}
        a{text-decoration:none}
    </style>
</head>
<body>
<div class="layui-container">
    <form id="data-list" name="data-list" method="post" action="/shop/index.php?act=inquiry&op=index" class="layui-form">
        <div class="layui-row">
            <div class="layui-col-md5">
                <label class="layui-form-label" style="margin-left: -10px;">询价标题</label>
                <input type="text" name="inquiry_name" required lay-verify="" placeholder="请输入标题" autocomplete="off"
                       style="width:350px" class="layui-input" value="<?php echo empty($_GET['inquiry_name']) ? '':$_GET['inquiry_name'];?>">
            </div>
                   <div class="layui-col-md2">
                        <label class="layui-form-label" style="margin-left: -70px;">状态</label>
                        <div class="select-type" style="float:left;width:120px;border-radius:0;margin-left: -10px;">
                            <select name="status">
                                <option value="" <?php if(empty($_GET['status'])){?>selected<?php }?> >全部</option>
                                <?php if(!empty($output['type_data']) && is_array($output['type_data'])){?>
                                    <?php foreach ($output['type_data'] as $key=>$val){?>
                                        <option value="<?php echo $key;?>" <?php if($_GET['status'] == $key){?>selected<?php }?> ><?php echo $val;?></option>
                                <?php }}?>
                            </select>
                        </div>
                       <input type="hidden" id="type-val" value="<?php echo $_GET['status'];?>">
                    </div>
            <div class="layui-col-md5">
                            <label class="layui-form-label" style="margin-left: -45px;width: 95px;">报价截止日期</label>
                            <input type="text" id="start" name="start" style="float: left;height:30px;width:120px">
                            <i class="layui-icon" style="font-size: 30px;float: left;margin-left: -35px;">&#xe637;</i>
                            <span style="font-size: 30px;float: left;margin-left: 5px;margin-right: 5px;">-</span>
                            <input type="text" id="end" name="end" style="float: left;height:30px;width:120px">
                            <i class="layui-icon" style="font-size: 30px;float: left;margin-left: -35px;">&#xe637;</i>


<!--                <a href="javascript:void(0);" onclick="get_data()">-->
                    <button class="layui-btn layui-btn-primary" lay-submit="" lay-filter="data-list" style="text-decoration:none;height: 35px;font-size:15px;margin-left: 20px;">搜索
                        <i class="layui-icon" style="color: #71b704;">&#xe615;</i></button>
                    <!--<span style="margin-left: -3px;">搜索</span>-->
               <!-- </a>-->
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
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/city_select.js"></script>
<script>
    layui.use(['form', 'layedit', 'laydate', 'table'], function(){
        var table = layui.table,
            laydate = layui.laydate,
            form = layui.form,
            layedit = layui.layedit;


        table.render({
            elem:'#list',
            url: '/shop/index.php?act=inquiry&op=getInquiryList',
            height: 472,
            skin:'line',
            cols:  [[ //标题栏
                {field: 'title', title: '询价标题', width: 357, templet: '#titleTpl'},
                {field: 'state', title: '状态', width: 100},
                {field: 'type', title: '询价类型', width: 110},
                {field: 'city', title: '城市公司', width: 240},
                {field:'time', title: '报价截止日期', width: 200},
                {field:'operation', title: '操作', width: 130, templet: '#czTpl'},
            ]],
            page: true,
            limits: [10,20,30,50,80,100],
            limit: 10,
            where: {
                name: '<?php echo empty($_GET['inquiry_name']) ? '':$_GET['inquiry_name'];?>',
                start: '<?php echo empty($_GET['start']) ? '':$_GET['start'];?>',
                end: '<?php echo empty($_GET['end']) ? '':$_GET['end'];?>',
                status: '<?php echo empty($_GET['status']) ? '':$_GET['status'];?>',
            },
            request: {
                pageName: 'page',
                limitName: 'nums',
            }
        });

        //执行一个laydate实例
        laydate.render({
            elem: '#start', //指定元素
        });

        laydate.render({
            elem: '#end', //指定元素
        });

        //监听提交
        form.on('submit(data-list)', function(data){
            var url = "/shop/index.php?act=inquiry&op=index";
            window.location.href= url + '&inquiry_name=' + data.field.inquiry_name + '&status=' + data.field.status + '&start=' + data.field.start + '&end=' + data.field.end;
            form.render('select');
            return false;
        });

    });


    $(function() {
        $("#type-list").selectpick({
            container: '.select-type',
            height: 35, // 下拉框的高度
            width: 100, // 下拉框的宽度
            onSelect: function(value,text){
                $("#type-val").val(value);
            }
        });
    });


    function get_data(){
        var params = serializeForm('data-list');
        var url = "/shop/index.php?act=inquiry&op=index";
        window.location.href= url + '&' + params;
    }

    function inquiryInfo(id,type,quote) {
        if(type == '1'){
            var title_str = '立即报价';
        }else{
            var title_str = '修改报价';
        }
        var member = "<?php echo $_SESSION['member_id']?>";
        var url = '/shop/index.php?act=inquiry&op=inquiryInfo&id='+id+'&type='+type+'&quote='+quote;
        open_window(member,title_str,url,'1270','610');
    }


    function cancel(){
        layui.use('layer', function(){
            var layer = layui.layer;
            layer.closeAll();
        })
    }


</script>
<script type="text/html" id="titleTpl">
    <p style="text-align: left;overflow:hidden;line-height:25px;width:425px;height: 20px;text-overflow: ellipsis; white-space: nowrap;"
       title="{{d.title}}">{{d.title}}</p>
</script>
<script type="text/html" id="czTpl">
    {{#  if(d.operation == 1){ }}
    <a href="javascript:void(0);" onclick="inquiryInfo('{{d.inquiry_id}}','{{d.operation}}','{{d.quote_id}}')" class="layui-table-link" style="text-decoration:none;">
        <i class="layui-icon" style="font-size: 20px; color: #71b704;float: left;">&#xe65e;</i>
        <p style="text-align: left;color: #71b704">立即报价</p>
    </a>
    {{#  } else if(d.operation == 2){ }}
    <a href="javascript:void(0);" onclick="inquiryInfo('{{d.inquiry_id}}','{{d.operation}}','{{d.quote_id}}')" class="layui-table-link" style="text-decoration:none;">
        <i class="layui-icon" style="font-size: 20px; color: #71b704;float: left;">&#xe631;</i>
        <p style="text-align: left;color: #71b704">修改报价</p>
    </a>
    {{#  } else { }}
    {{#  } }}
</script>