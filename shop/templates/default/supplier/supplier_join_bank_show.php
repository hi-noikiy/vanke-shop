<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/9
 * Time: 13:14
 */
?>
<div class="main" style="margin-top: 30px;margin:0 auto;width: 700px">
    <div class="layui-form" id="store_data">

        <?php if($output['type'] == 'account'){?>
        <div class="layui-form-item" style="margin-top: 40px;">
            <label class="layui-form-label" style="width: 180px"><?php echo $output['type_name']?>银行开户名：</label>
            <div class="layui-form-mid layui-word-aux"><?php echo $output['bank']['account_name']?></div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px">公司<?php echo $output['type_name']?>银行账号：</label>
            <div class="layui-form-mid layui-word-aux"><?php echo $output['bank']['account_number']?></div>
        </div>
        <?php }else{?>
            <div class="layui-form-item" style="margin-top: 40px;">
                <label class="layui-form-label" style="width: 180px"><?php echo $output['type_name']?>银行开户名：</label>
                <div class="layui-form-mid layui-word-aux"><?php echo $output['bank']['settlement_name']?></div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label" style="width: 180px">公司<?php echo $output['type_name']?>银行账号：</label>
                <div class="layui-form-mid layui-word-aux"><?php echo $output['bank']['settlement_number']?></div>
            </div>
        <?php }?>

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px"><?php echo $output['type_name']?>银行名称：</label>
            <div class="layui-form-mid layui-word-aux"><?php echo $output['bank']['bank_name']?></div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px"><?php echo $output['type_name']?>银行支行名称：</label>
            <div class="layui-form-mid layui-word-aux"><?php echo $output['bank']['bank_branch_name']?></div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px"><?php echo $output['type_name']?>支行联行号：</label>
            <div class="layui-form-mid layui-word-aux"><?php echo $output['bank']['bank_branch_code']?></div>
        </div>


        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 180px"><?php echo $output['type_name']?>行所在地：</label>
            <div class="layui-form-mid layui-word-aux"><?php echo $output['bank']['bank_address']?></div>
        </div>
        <?php if($output['type'] == 'account'){?>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: 180px">开户行电子许可证：</label>
                <div class="layui-form-mid layui-word-aux">
                    <a href="/data/upload/shop/store_joinin/<?php echo $output['bank']['bank_licence_electronic']?>" target="_blank">
                        <img width="150px" src="/data/upload/shop/store_joinin/<?php echo $output['bank']['bank_licence_electronic']?>" >
                    </a>
                </div>
            </div>
        <?php }?>
    </div>
</div>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script>
    layui.use(['form', 'layer'], function() {
        var form = layui.form,
            layer = layui.layer;

    });

</script>
