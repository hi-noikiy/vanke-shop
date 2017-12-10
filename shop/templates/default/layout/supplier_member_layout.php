<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/9
 * Time: 13:18
 */?>
<?php include template('layout/common_layout');?>
<?php include template('layout/cur_local');?>
<style>
    .ncm-container {
        width: 1200px;
        margin: 0 auto 30px auto;
    }
    .site-fix .site-tree {
        position: fixed;
        top: 0;
        bottom: 0;
        z-index: 666;
        min-height: 0;
        overflow: auto;
        background-color: #fff;
    }
    .site-tree .layui-tree {
        line-height: 32px;
    }
    .layui-tree li {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
    li {
        list-style: none;
    }
    .site-tree .layui-tree li h2 {
        line-height: 36px;
        border-left: 5px solid #009E94;
        margin: 5px 0 5px;
        padding: 0 10px;
        background-color: #f2f2f2;
    }

    h1, h2, h3 {
        font-size: 14px;
    }
    .layui-tree li a {
        font-size: 0;
    }
    .layui-tree li .layui-tree-spread, .layui-tree li a {
        display: inline-block;
        vertical-align: top;
        height: 26px;
        cursor: pointer;
    }
    a {
        color: #333;
        text-decoration: none;
    }
    .site-tree .layui-tree .site-tree-noicon a cite {
        padding-left: 15px;
    }
    .site-tree .layui-tree li a cite {
        padding: 0 8px;
    }
    .layui-tree li a cite {
        padding: 0 6px;
        font-size: 14px;
        font-style: normal;
    }
    a cite {
        font-style: normal;
    }
    .ncm-l-top {
        background-color: #27A9E3;
        width: 100%;
        height: 40px;
        position: relative;
        z-index: 3;
    }
    .ncm-l-top h2 a {
        font-size: 14px;
        font-weight: 600;
        color: #FFF;
        height: 20px;
        float: left;
        padding: 10px 0 10px 20px;
    }
    .clear {
        font-size: 0px;
        line-height: 0px;
        height: 0;
        margin: 0;
        padding: 0;
        float: none;
        clear: both;
        border: 0;
    }
</style>

<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/member.css" rel="stylesheet" type="text/css">
<div class="ncm-container">
    <div style="width: 210px;float: left">
        <div class="ncm-l-top" style="margin-bottom: 15px; background-color:#27A9E3;border-radius:5px">
            <h2><a href="/shop/index.php?act=seller_login&op=show_login">我的店铺中心</a></h2>
        </div>
        <div class="ncm-l-top">
            <h2><a href="/shop/index.php?act=supplier_member">我的商户中心</a></h2>
        </div>
        <div class="site-tree">
            <ul class="layui-tree">

                <li><h2>商户信息管理</h2></li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=member_information&op=member">
                        <cite>资料修改</cite>
                    </a>
                </li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=member_information&op=avatar">
                        <cite>头像设置</cite>
                    </a>
                </li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=login&op=logout">
                        <cite>安全退出</cite>
                    </a>
                </li>

                <li><h2>认证管理</h2></li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=supplier_member&op=join_log">
                        <cite>城市公司认证记录</cite>
                    </a>
                </li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=supplier_member&op=join_city">
                        <cite>认证申请</cite>
                    </a>
                </li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=supplier_member&op=join_store">
                        <cite>开店申请</cite>
                    </a>
                </li>

                <li><h2>城市公司管理</h2></li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=supplier_member&op=contacts_list">
                        <cite>联系人管理</cite>
                    </a>
                </li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=supplier_member&op=account_list">
                        <cite>开户银行管理</cite>
                    </a>
                </li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=supplier_member&op=settlement_list">
                        <cite>结算银行管理</cite>
                    </a>
                </li>

                <li><h2>银行信息管理</h2></li>
                <li class="site-tree-noicon ">
                    <a href="/doc/">
                        <cite>银行账户管理</cite>
                    </a>
                </li>

                <li><h2>交易管理</h2></li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=member_unline&op=index">
                        <cite>线下订单</cite>
                    </a>
                </li>


                <li><h2>消息中心</h2></li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=member_message&op=message">
                        <cite>收到消息</cite>
                    </a>
                </li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=member_message&op=privatemsg">
                        <cite>已发送消息</cite>
                    </a>
                </li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=member_message&op=systemmsg">
                        <cite>系统消息</cite>
                    </a>
                </li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=member_message&op=personalmsg">
                        <cite>私信</cite>
                    </a>
                </li>
                <li class="site-tree-noicon ">
                    <a href="/shop/index.php?act=member_message&op=setting">
                        <cite>接收设置</cite>
                    </a>
                </li>

            </ul>
        </div>
    </div>
    <div style="width: 970px;float: left;margin-left: 20px;">
        <?php require_once($tpl_file);?>
    </div>
    <div class="clear"></div>
</div>
<style>.bj{color: #ff2222;font-size: 18px;margin-left: 3px;margin-right: 3px;}</style>
<?php require_once template('footer');?>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<script>
</script>
</body>
</html>
