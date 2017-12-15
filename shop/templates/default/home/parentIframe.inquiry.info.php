<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/3
 * Time: 上午9:25
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>layui</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
</head>
<style>
    .layui-table-tool{ text-align:center}
    a{text-decoration:none}
    .layui-form-item{margin-bottom:2px}
</style>
<div style="margin:0 auto;width:1250px;margin-top: 10px;">
<form class="layui-form" action="" style="margin-left: 20px">
    <div class="layui-form-item" style="margin:0;padding:0;">
        <div class="layui-inline" style="margin-left: 100px;" >
            <label class="layui-form-label">报价金额：</label>
            <div class="layui-input-inline">
                <div class="layui-form-mid layui-word-aux">
                    <?php echo $output['list']['quoted_price'];?>&nbsp;&nbsp;元
                </div>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label" style="width:110px">期望到货时间：</label>
            <div class="layui-input-inline">
                <div class="layui-form-mid layui-word-aux"><?php echo $output['list']['hope_time'];?></div>
            </div>
        </div>
        <div class="layui-inline" style="margin-left:-30px;">
            <label class="layui-form-label" style="width:110px"><span class="layui-badge-dot"></span>预计交货时间：</label>
            <div class="layui-input-inline">
                <input type="text" id="predict_time" name="predict_time" style="float: left;height:30px;width:180px" lay-verify="required" value="<?php echo $output['list']['predict_time'];?>">
                <i class="layui-icon" style="font-size: 30px;float: left;margin-left: -30px;margin-top: -1px;">&#xe637;</i>
            </div>
        </div>
    </div>

    <div class="layui-form-item" style="margin:0;padding:0;">
        <label class="layui-form-label" style="margin-left: 100px;"><span class="layui-badge-dot"></span>有效时间：</label>
        <div class="layui-input-inline">
            <input type="text" id="valid_statr" name="valid_statr" style="float: left;height:30px;width:180px"
                   lay-verify="required" value="<?php echo $output['list']['valid_statr'];?>" >
            <i class="layui-icon" style="font-size: 30px;float: left;margin-left: -30px;margin-top: -1px;">&#xe637;</i>
        </div>
        <div class="layui-input-inline">
            <input type="text" id="valid_end" name="valid_end" style="float: left;height:30px;width:180px"
                   lay-verify="required" value="<?php echo $output['list']['valid_end'];?>" >
            <i class="layui-icon" style="font-size: 30px;float: left;margin-left: -30px;margin-top: -1px;">&#xe637;</i>
        </div>
        <div class="layui-input-inline">&nbsp;</div>
        <div class="layui-inline" style="margin-left:-30px;">
            <label class="layui-form-label">开票税率：</label>
            <div class="layui-input-inline" style="margin-left:-5px;">
                <select name="taxId">
                    <option value="">请选择开票税率</option>
                    <?php if(!empty($output['sl_list']) && is_array($output['sl_list'])){?>
                        <?php foreach ($output['sl_list'] as $val){?>
                            <option value="<?php echo $val['taxId'];?>" <?php if($output['list']['rate'] == $val['taxId']){?>selected<?php }?> ><?php echo $val['taxName'];?></option>
                        <?php }}?>
                </select>
            </div>
        </div>
    </div>

    <div class="layui-form-item" style="margin:0;padding:0;">
        <label class="layui-form-label" style="margin-left: 100px;">备注：</label>
        <div class="layui-input-block">
            <input name="identity" placeholder="" autocomplete="off"
                   value="<?php echo $output['list']['mark'];?>" style="width:865px" class="layui-input" type="text">
        </div>
    </div>
    <div class="layui-form-item" style="margin:0;padding:0;">
        <label class="layui-form-label" style="margin-left: 100px;">报价文件：</label>
        <div class="layui-input-inline" style="width:865px">
            <div id="show-path" class="layui-form-mid layui-word-aux" style="width:865px;<?php if(!empty($output['list']['path_url'])){?>display: none<?php }?>">
                <button type="button" class="layui-btn" id="inquiry-path" style="height:35px;line-height:35px;background-color: #71b704">
                    <i class="layui-icon"></i>
                    上传报价文件
                </button>
            </div>
            <div id="none-path" class="layui-form-mid layui-word-aux" style="width:865px;<?php if(empty($output['list']['path_url'])){?>display: none<?php }?>">
                <div style="width:500px;float: left">
                    <p id="path-name" style="float: left"><?php echo $output['list']['path_name'];?></p>
                    <a href="javascript:void(0)" onclick="del_path()">
                        <i title="删除文件" class="layui-icon" style="font-size: 20px;margin-left: 15px;color: #5cb85c">&#xe640;</i>
                    </a>
                    <a href="javascript:void(0)" onclick="down_path()">
                        <i title="下载查看文件" class="layui-icon" style="font-size: 20px;margin-left: 15px;color: #5cb85c">&#xe601;</i>
                    </a>
                </div>
            </div>
            <div class="layui-elem-quote" style="padding:0px;margin:0;float: left;height: 30px;border-left:0px solid #5cb85c;color: #999 !important;text-align:center;line-height:20px">
                &nbsp;&nbsp;&nbsp;温馨提示：上传文件后缀名必须为.zip或者.rar或者.7z，如有多个报价文件，请将多个文件放在一个文件夹中压缩后在上传！
            </div>
            <input type="hidden" id="up_path" name="up_path" value="<?php echo $output['list']['path_url'];?>" >
            <input type="hidden" id="up_name" name="up_name" value="<?php echo $output['list']['path_name'];?>" >
            <input type="hidden" id="operation" name="operation" value="<?php echo $output['type'];?>" >
            <input type="hidden" id="quoteId" name="quoteId" value="<?php echo $output['quoteId'];?>" >
        </div>
    </div>
    <div class="layui-form-item" style="margin-left:20px;margin-top: -20px;">
        <table class="layui-table" id="list" lay-filter="demoEvent">
        </table>
    </div>

    <input type="hidden" id="quoteRequestId" name="quoteRequestId" value="<?php echo $output['list']['quoteRequestId'];?>" >
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="" lay-filter="data-list"
            style="margin-left: 405px;background-color:#71b704">立即提交</button>
            <button class="layui-btn layui-btn-primary" onclick="quxiao()">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
        </div>
    </div>
</form>
</div>

<style>
    .layui-laypage-1{margin-left: 400px;}
</style>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/form.js"></script>
<script>
    layui.use(['form', 'layedit', 'laydate', 'upload', 'table'], function(){
        var form = layui.form,
            layer = layui.layer,
            layedit = layui.layedit,
            table = layui.table,
            upload = layui.upload,
            laydate = layui.laydate;

        //日期
        laydate.render({
            elem: '#predict_time'
        });
        laydate.render({
            elem: '#valid_statr'
        });
        laydate.render({
            elem: '#valid_end'
        });

     
        table.render({ //其它参数在此省略13925127998 &#xe61c;templet: '#titleTpl' layout:['prev','next'],
            elem:'#list',
            url: '/shop/index.php?act=inquiry&op=getList&id=<?php echo $output['list']['inquiry_id'];?>&count=<?php echo $output['send_data']['totalcount'];?>&type=<?php echo $output['send_data']['operation'];?>&quote=<?php echo $output['send_data']['quoteId'];?>',
            height: 280,
            width:1174,
            cols:  [[ //标题栏
                {field: 'key', title: 'ID', width: 50},
                {field: 'name', title: '物料名称', width: 290, templet: '#nameTpl'},
                {field: 'brand', title: '品牌', width: 150, templet: '#brandTpl'},
                {field: 'spec', title: '规格', width: 190, templet: '#specTpl'},
                {field:'nums', title: '数量', width: 100},
                {field:'umit', title: '单位', width: 100},
                {field:'price', title: '单价(点击修改)', width: 130, event: 'setSign', style:'cursor: pointer;'},
                {field:'total', title: '总金额', width: 160},
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


//创建一个上传组件
        upload.render({
            elem: '#inquiry-path',
            url: '/shop/index.php?act=inquiry&op=upLoadFirld',
            method:'post',
            data: {},
            exts: 'zip|rar|7z',
            accept: 'file',
            done: function(res){ //上传后的回调
                if(res.code == 0){
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert('上传成功', {closeBtn: 0,title: '温馨提示',}, function(index){
                            $("#up_path").val(res.data.src);
                            $("#up_name").val(res.data.name);
                            //显示数据
                            $("#show-path").hide();
                            $("#none-path").show();
                            $("#path-name").text(res.data.name);
                            layer.closeAll();
                        });
                    })
                }
            },
            error: function(){
                //请求异常回调
            }
        })

        //监听单元格事件
        table.on('tool(demoEvent)', function(obj){
            var data = obj.data;
            if(obj.event === 'setSign'){
                layer.prompt({
                    formType: 0,
                    title: '修改单价',
                    value: data.sign,
                    area: ['100px', '50px']
                }, function(value, index){
                    var re = /^(([1-9][0-9]*\.[0-9][0-9]*)|([0]\.[0-9][0-9]*)|([1-9][0-9]*)|([0]{1}))$/;
                    if (re.test(value) && value > 0) {
                        layer.close(index);
                        //这里一般是发送修改的Ajax请求
                        //将修改的价格进行保存
                        $.ajax({
                            type:"POST",
                            //提交的网址
                            url:"/shop/index.php?act=inquiry&op=inquiryQuotationPrice&quoteRequestId=<?php echo $output['send_data']['quoteRequestId'];?>",
                            data:{price: toDecimal2(value),id:data.id},
                            datatype: "json",
                            success:function(result){
                                if(result == '1'){
                                    obj.update({
                                        price: toDecimal2(value),
                                        total: toDecimal2(Number(data.nums.replace(/,/g,'')) * Number(value.replace(/,/g,'')))
                                    });
                                }
                            }
                        });
                    }else{
                        alert("输入价格无效");
                    }
                });
            }
        });


        //监听提交
        form.on('submit(data-list)', function(data){
            $.ajax({
                type:"POST",
                //提交的网址
                url:"/shop/index.php?act=inquiry&op=inquiryData&count=<?php echo $output['send_data']['totalcount'];?>",
                data:data.field,
                datatype: "json",
                success:function(result){
                    if(result == '0'){
                        layui.use('layer', function(){
                            var layer = layui.layer;
                            layer.alert('提交成功', {closeBtn: 0,title: '温馨提示',}, function(index){
                                layer.closeAll();
                                window.parent.location.reload();
                            });
                        })
                    }else{
                        layui.use('layer', function(){
                            var layer = layui.layer;
                            layer.alert(result, {closeBtn: 0,title: '温馨提示',}, function(index){
                                layer.closeAll();
                            });
                        })
                    }
                }
            });
            return false;
        });


    });

    function toDecimal2(x) {
        var f = parseFloat(x);
        if (isNaN(f)) {
            return false;
        }
        var f = Math.round(x*10000)/10000;
        var s = f.toString();
        var rs = s.indexOf('.');
        if (rs < 0) {
            rs = s.length;
            s += '.';
        }
        while (s.length <= rs + 4) {
            s += '0';
        }
        return s;
    }


    function quxiao(){
        parent.cancel();
    }


    function del_path(){
        layui.use('layer', function(){
            var layer = layui.layer;
            layer.confirm('你确定删除该文件吗？', {
                title:'温馨提示',
                btn: ['确定', '取消'],
                shade:0.5,
                closeBtn: 0,
            }, function(index, layero){
                var path_url = $('#up_path').val();
                $.ajax({
                    type:"POST",
                    //提交的网址
                    url:"/shop/index.php?act=inquiry&op=delInquiryPath&quote=<?php echo $output['send_data']['quoteId'];?>",
                    data:{path: path_url},
                    datatype: "json",
                    success:function(result){
                        if(result == '1'){
                            layui.use('layer', function(){
                                var layer = layui.layer;
                                layer.alert('删除成功', {closeBtn: 0,title: '温馨提示',}, function(index){
                                	$('#up_path').val("");
                                	$('#up_name').val("");
                                    $("#show-path").show();
                                    $("#none-path").hide();
                                    layer.close(layer.index);
                                });
                            })
                        }else{
                            layui.use('layer', function(){
                                var layer = layui.layer;
                                layer.alert('删除失败', {closeBtn: 0,title: '温馨提示',}, function(index){
                                    layer.close(layer.index);
                                });
                            })
                        }
                    }
                });
                return false;
            });
        })
    }

    function down_path(){
        var inquiry_url = '/'+$('#up_path').val();
        var $eleForm = $("<form method='get'></form>");
        $eleForm.attr("action",inquiry_url);
        $(document.body).append($eleForm);
        //提交表单，实现下载
        $eleForm.submit();
    }
</script>
<script type="text/html" id="nameTpl">
    <p style="text-align: left;overflow:hidden;line-height:25px;width:280px;height: 38px;text-overflow: ellipsis; white-space: nowrap;"
       title="{{d.name}}">{{d.name}}</p>
</script>
<script type="text/html" id="brandTpl">
    <p style="text-align: left;overflow:hidden;line-height:25px;width:280px;height: 38px;text-overflow: ellipsis; white-space: nowrap;"
       title="{{d.brand}}">{{d.brand}}</p>
</script>
<script type="text/html" id="specTpl">
    <p style="text-align: left;overflow:hidden;line-height:25px;width:280px;height: 38px;text-overflow: ellipsis; white-space: nowrap;"
       title="{{d.spec}}">{{d.spec}}</p>
</script>
</body>
</html>
