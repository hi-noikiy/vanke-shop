<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>确认发货</title>
    <link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
    <style>
        body{ text-align:center;background-color: #fff;}
    </style>
</head>
<body>
<div class="layui-main-md8">
    <div class="layui-row-md8" style="background-color:#fff;height:50px;margin-top: 15px;">
        <form id="data-list" name="data-list" method="post" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">姓名</label>
                <div class="layui-input-inline">
                    <input type="text" name="member" value="<?php echo empty($_GET['member']) ? '':$_GET['member'];?>" placeholder="请输入姓名" autocomplete="off" class="layui-input">
                </div>

                <label class="layui-form-label">地址</label>
                <div class="layui-input-inline">
                    <input type="text" name="address" value="<?php echo empty($_GET['address']) ? '':$_GET['address'];?>" placeholder="请输入地址" autocomplete="off" class="layui-input">
                </div>

                <label class="layui-form-label">联系方式</label>
                <div class="layui-input-inline">
                    <input type="text" name="phone" value="<?php echo empty($_GET['phone']) ? '':$_GET['phone'];?>" placeholder="请输入联系方式" autocomplete="off" class="layui-input">
                </div>

                <label class="layui-form-label">
                    <a href="javascript:void(0);" onclick="get_data()"><i class="layui-icon" style="font-size: 30px; color: #1E9FFF;">&#xe615;</i></a>
                </label>
            </div>
        </form>
    </div>
    <div class="layui-row-md8" style="margin:0px 0;">
        <table class="layui-table" lay-filter="list" id="list" style="margin: 0px 0;">
            <thead>
            </thead>
        </table>
    </div>
</div>

<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/form.js"></script>
<script>
    layui.use('table', function(){
        var table = layui.table;
        table.render({ //其它参数在此省略
            elem:'#list',
            url: 'index.php?act=store_deliver&op=send_address_list',
            height: 472,
            width:1080,
            cols:  [[ //标题栏
                {field: 'seller_name', title: '姓名', width: 100},
                {field: 'area_info', title: '省市', width: 200},
                {field: 'address', title: '详细地址', width: 400},
                {field: 'telphone', title: '联系电话', width: 272},
                {field:'cz', title: '操作', width: 100, templet: '#titleTpl'},
            ]],
            page: true,
            limits: [10],
            limit: 10,
            where: {
                order_id: '<?php echo intval($output['order_id']);?>',
                member: '<?php echo $_GET['member'];?>',
                address: '<?php echo $_GET['address'];?>',
                phone: '<?php echo $_GET['phone'];?>',
            },
            request: {
                pageName: 'page',
                limitName: 'nums',
            }
        });

        table.on('tool(list)', function(obj){
            var address_data = obj.data;
            var layEvent = obj.event;
            var tr = obj.tr;
            var order_id = '<?php echo $output['order_id'];?>';

            if(layEvent === 'select'){ //查看
                $.post("index.php?act=store_deliver&op=send_address_save&order_id="+order_id+"&daddress_id="+address_data.address_id)
                    .done(function(result) {
                        if ( result == 'true' ) {
                            parent.edit_data(address_data.address_id, address_data.seller_name, address_data.telphone, address_data.area_info, address_data.address);
                        } else {
                            parent.err_cz();
                        }
                    });
            }
        });
    });

    function get_data(){
        var params = serializeForm('data-list');
        var order_id = '<?php echo $output['order_id'];?>';
        var url = "index.php?act=store_deliver&op=send_address_select&order_id="+order_id;
        window.location.href= url + "&" + params;
    }


</script>
<script type="text/html" id="titleTpl">
    <a class="layui-btn layui-btn-mini" lay-event="select">选择</a>
</script>
<style>
    .layui-table-view{margin:0px 0;}
</style>
</body>
</html>
