<?php ?>

<div class="ncsc-index">
  <div class="top-container">
    <div class="basic-info">
      <dl class="ncsc-seller-info">
        <dt class="seller-name" style="left: auto;">
          <h3><?php echo $_SESSION['seller_name']; ?></h3>
          <h5>(用户名：<?php echo $_SESSION['member_name']; ?>)</h5>
        </dt>
        <dd class="seller-permission" style="left: auto;">代理编号： <strong><?php echo $_SESSION['agent_id']; ?></strong></dd>
        <dd class="seller-last-login">最后登录：<strong><?php echo $_SESSION['seller_last_login_time'];?></strong> </dd>
      </dl>
    </div>
  </div>
  <div class="seller-cont">

    <div class="container type-b">
      <div class="hd">
        <h3>代理中心</h3>
        <h5></h5>
      </div>
      <div class="content">
        <ul>
          <?php
			if(is_array($output['help_list']) && !empty($output['help_list'])) {
				foreach($output['help_list'] as $val) {
			?>
          <li><a target="_blank" href="<?php echo urlShop('article', 'show', array('article_id' => $val['article_id']));?>" title="<?php echo $val['article_title']; ?>">
            <?php echo $val['article_title'];?></a></li>
          <?php
				}
			}
			 ?>
        </ul>
        <dl>
          <dt><?php echo $lang['store_site_info'];?></dt>
          <?php
			if(is_array($output['phone_array']) && !empty($output['phone_array'])) {
				foreach($output['phone_array'] as $key => $val) {
			?>
          <dd><?php echo $lang['store_site_phone'].($key+1).$lang['nc_colon'];?><?php echo $val;?></dd>
          <?php
				}
			}
			 ?>
          <dd><?php echo $lang['store_site_email'].$lang['nc_colon'];?><?php echo C('site_email');?></dd>
        </dl>
      </div>
    </div>

<!--    <div class="container type-c">-->
<!--      <div class="hd">-->
<!--        <h3>统计</h3>-->
<!--        <h5>统计店铺数 和 佣金金额</h5>-->
<!--      </div>-->
<!--      <div class="content">-->
<!--        <table class="ncsc-default-table">-->
<!--          <thead>-->
<!--            <tr>-->
<!--              <th class="w50">项目</th>-->
<!--              <th>店铺数</th>-->
<!--              <th class="w100">佣金</th>-->
<!--            </tr>-->
<!--          </thead>-->
<!--          <tbody>-->
<!--            <tr class="bd-line">-->
<!--              <td>上月</td>-->
<!--              <td>--><?php //echo empty($output['daily_sales']['ordernum']) ? '--' : $output['daily_sales']['ordernum'];?><!--</td>-->
<!--              <td>--><?php //echo empty($output['daily_sales']['orderamount']) ? '--' : $lang['currency'].$output['daily_sales']['orderamount'];?><!--</td>-->
<!--            </tr>-->
<!--            <tr class="bd-line">-->
<!--              <td>所有</td>-->
<!--              <td>--><?php //echo empty($output['monthly_sales']['ordernum']) ? '--' : $output['monthly_sales']['ordernum'];?><!--</td>-->
<!--              <td>--><?php //echo empty($output['monthly_sales']['orderamount']) ? '--' : $lang['currency'].$output['monthly_sales']['orderamount'];?><!--</td>-->
<!--            </tr>-->
<!--          </tbody>-->
<!--        </table>-->
<!--      </div>-->
<!--    </div>-->


  </div>
</div>
<script>
$(function(){
	var timestamp=Math.round(new Date().getTime()/1000/60);//异步URL一分钟变化一次
    $.getJSON('index.php?act=seller_center&op=statistics&rand='+timestamp, null, function(data){
        if (data == null) return false;
        for(var a in data) {
            if(data[a] != 'undefined' && data[a] != 0) {
                var tmp = '';
                if (a != 'goodscount' && a != 'imagecount') {
                    $('#nc_'+a).parents('a').addClass('num');
                }
                $('#nc_'+a).html(data[a]);
            }
        }
    });
});
</script>
