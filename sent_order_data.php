<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
@include('data/config/config.ini.php');
?>
<html>
    <head>
        <title>提交修改</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>

        <h3>合同页面</h3>
        订单号<?php echo $_GET['order_sn'];?></br>
        <form name="myform" method = 'post'  action = '<?php echo $config['base_site_url'];?>/wanke/client_2.php?orderSn=<?php echo $_GET['order_sn'];?>&orderState=14' >
            
            <input type="submit" value="审核通过" />
        </form>

        <form name="myform" method = 'post'  action = '<?php echo $config['base_site_url'];?>/wanke/client_2.php?orderSn=<?php echo $_GET['order_sn'];?>&orderState=81' >

            <input type="submit" value="审核拒绝" />
        </form>

        <form name="myform" method = 'post'  action = '<?php echo $config['base_site_url'];?>/wanke/client_2.php?orderSn=<?php echo $_GET['order_sn'];?>&orderState=18' >

            <input type="submit" value="审批退回" />
        </form>
    </body>
</html>
