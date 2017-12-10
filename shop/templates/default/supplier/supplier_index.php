<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/9
 * Time: 13:14
 */
?>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<div class="breadcrumb">
    <i class="layui-icon">&#xe68e;</i>
    <a href="/shop/index.php?act=supplier_member">
        <span>我的商户中心</span>
    </a>
    <span class="arrow">></span> <span>首页</span>
</div>
<div class="main" style="margin-top: 30px;">
    <div style="min-height: 180px;">
        <div>
            <div class="info-lcol">
                <div class="layui-inline u-pic" style="float: left">
                    <img src="//i.jd.com/commons/img/no-img_mid_.jpg" class="layui-circle">
                </div>
                <div class="info-m" style="float: left;margin-left: 20px;">
                    <div class="u-name">
                        <?php echo $output['supplier']['company_name'];?>
                    </div>
                    <div class="u-level">
                        <?php if($output['supplier']['level']>0){?>
                            <?php if($output['supplier']['level'] == '1'){?>
                                <span class="layui-badge">优选供应商</span>
                            <?php }?>
                            <?php if($output['supplier']['level'] == '2'){?>
                                <span class="layui-badge layui-bg-orange">合格供应商</span>
                            <?php }?>
                        <?php }?>
                    </div>
                    <div class="u-safe">
                        <span>用户名称：</span>
                        <i id="cla" class="safe-rank05"></i>
                        <strong id="rank-text" class="rank-text ftx-05">
                            <?php echo $output['supplier']['member_name'];?>
                        </strong>
                    </div>
                    <div class="u-safe">
                        <span>公司电话：</span>
                        <i id="cla" class="safe-rank05"></i>
                        <strong id="rank-text" class="rank-text ftx-05">
                            <?php echo $output['supplier']['company_phone'];?>
                        </strong>
                    </div>
                    <div class="u-safe">
                        <span>公司所在地：</span>
                        <i id="cla" class="safe-rank05"></i>
                        <strong id="rank-text" class="rank-text ftx-05">
                            <?php echo $output['supplier']['company_address'];?>
                        </strong>
                    </div>
                </div>
            </div>
            <div style="float: left;">

                <div class="acco-info">
                    <span style="font-size: 16px;margin-left: 10px;">公司联系人信息：</span>
                    <div style="margin-left: 25px;margin-top: 10px;">
                        联系人姓名：<?php echo $output['supplier']['contacts_name'];?>
                    </div>
                    <div style="margin-left: 25px;margin-top: 10px;">
                        联系人电话：<?php echo $output['supplier']['contacts_phone'];?>
                    </div>
                    <div style="margin-left: 25px;margin-top: 10px;">
                        联系人邮箱：<?php echo $output['supplier']['contacts_email'];?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div>
        <div class="layui-collapse" lay-filter="test">
            <div class="layui-colla-item">
                <h2 class="layui-colla-title">公司法定经营范围</h2>
                <div class="layui-colla-content layui-show">
                    <p><?php echo $output['supplier']['business_sphere'];?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/supplier/supplier_index.css" media="all">
