<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/9
 * Time: 13:14
 */
?>
<div class="breadcrumb">
    <i class="layui-icon">&#xe68e;</i>
    <a href="/shop/index.php?act=supplier_member">
        <span>我的商户中心</span>
    </a>
    <span class="arrow">></span> <span>认证管理</span>
    <span class="arrow">></span> <span>开店申请</span>
</div>
<div class="main" style="margin-top: 30px;">
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>店铺经营信息</legend>
    </fieldset>
    <form class="layui-form" id="store_data">
        <?php if(!empty($output['join_city']) && $output['join_city'] == '1'){?>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: 180px"></label>
                <div class="layui-form-mid layui-word-aux">已无可开店的城市,请前往认证其它城市公司</div>
            </div>
        <?php }else{?>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: 180px">商家账号：</label>
                <div class="layui-form-mid layui-word-aux"><?php echo $output['store_data']['suplier_name'];?></div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label" style="width: 180px">店铺名称：</label>
                <?php if(empty($output['store_data']['store_name'])){?>
                    <div class="layui-input-block">
                        <input type="text" name="store_name" lay-verify="required" placeholder="请输入店铺名称" autocomplete="off" class="layui-input">
                    </div>
                <?php }else{?>
                    <div class="layui-form-mid layui-word-aux"><?php echo $output['store_data']['store_name'];?></div>
                <?php }?>
            </div>

            <?php if(!empty($output['store_class']['y']) && is_array($output['store_class']['y'])){?>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: 180px">当前店铺分类：</label>
                    <div class="layui-input-block">
                        <div style="width: 400px;margin-left: 100px;">
                            <?php foreach ($output['store_class']['y'] as $class_data){?>
                                <div class="layui-unselect layui-form-checkbox layui-form-checked" lay-skin="">
                                    <span><?php echo $class_data['sc_name'];?></span><i class="layui-icon"></i>
                                </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
            <?php }?>

            <div class="layui-form-item">
                <label class="layui-form-label" style="width: 180px">可选店铺分类：</label>
                <div class="layui-input-block">
                    <div style="width: 400px;margin-left: 100px;">
                        <?php if(!empty($output['store_class']['n']) && is_array($output['store_class']['n'])){?>
                            <?php foreach ($output['store_class']['n'] as $class_data){?>
                                <input type="checkbox" name="class[]" title="<?php echo $class_data['sc_name'];?>" value="<?php echo $class_data['sc_id'];?>">
                            <?php }}?>
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label" style="width: 180px"></label>
                <div class="layui-form-mid layui-word-aux">请根据您所经营的内容认真选择店铺分类，选择提交后商家不可在修改。</div>
            </div>

            <?php if(!empty($output['join_city']) && $output['join_city'] == '1'){?>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: 180px"></label>
                    <div class="layui-form-mid layui-word-aux">已无可开店的城市,请前往认证其它城市公司</div>
                </div>
            <?php }else{?>
                <div class="layui-form-item">
                    <label class="layui-form-label"  style="width: 180px">城市公司所在地：</label>
                    <div class="layui-input-block">
                        <div style="width: 430px;margin-left: 100px;">
                            <?php if(!empty($output['city_list']) && is_array($output['city_list'])){?>
                                <?php foreach ($output['city_list'] as $city_data){?>
                                    <input type="checkbox" name="city[]" title="<?php echo $city_data['city_name'];?>" value="<?php echo $city_data['id'];?>">
                            <?php }}?>
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block" name="store-data">
                        <a class="layui-btn" lay-submit lay-filter="store-data" style="float: right;margin-right: 400px;margin-top: 50px">提交开店申请</a>
                    </div>
                </div>
            <?php }?>
        <?php }?>
    </form>
</div>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/supplier/supplier_index.css" media="all">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/form.js"></script>
<script>
    layui.use(['form', 'layer'], function(){
        var form = layui.form,
            layer = layui.layer;

        $("div[name='store-data']").on("click", ".layui-btn", function() {
            var citys = document.getElementsByName("city[]");
            citys_num = 0;
            for(i=0;i<citys.length;i++){
                if(citys[i].checked)
                    citys_num++;
            }

            if(citys_num == 0){
                layui.use('layer', function(){
                    var layer = layui.layer;
                    layer.alert('请选择城市公司所属地', {closeBtn: 0,title: '温馨提示',offset: '300px'}, function(index){
                        layer.closeAll();
                    });
                })
                return false;
            }

            $.ajax({
                type:"POST",
                url:"/shop/index.php?act=supplier_member&op=store_add",
                data:serializeForm('store_data'),
                datatype: "json",
                beforeSend: function () {
                    loads = layer.load(1, {
                        shade: [0.5,'#000'] //0.1透明度的白色背景
                    });
                },
                success:function(result){
                    layer.close(loads);
                    var result = JSON.parse(result);
                    if(result.code == '1'){
                        layer.alert('申请成功', {closeBtn: 0,title: '温馨提示',offset: '300px'}, function(index){
                            window.location.href="/shop/index.php?act=supplier_member&op=join_log";
                            layer.closeAll();
                        });
                    }else{
                        layer.alert('申请失败', {closeBtn: 0,title: '温馨提示',offset: '300px'}, function(index){
                            layer.closeAll();
                        });
                    }
                }
            });
        });
    });

</script>
