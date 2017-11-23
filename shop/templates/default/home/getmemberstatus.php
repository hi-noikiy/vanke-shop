<?php

?>
<!--<div class="bigBackground">
    <div class="wrapBigBg">
        <div class="CenterBtn">
            <?php if($output['succ'] == 'successs'){?>
                 <p>认证成功，请重新登录</p>
            <?php }?>
            <a class="btn" href="<?php echo urlShop('getmemberstatus', 'rz');?>">我是物业采购员</a>
            <a class="btn" href="<?php echo urlShop('store_joinin', 'step0');?>">我是供应商</a>
            <a class="btn" href="<?php echo urlShop('login', 'logout');?>">返回登录页面</a>
        </div>
    </div>
</div>-->
<style>
.nc-login ul li {
	width: 100px;
	margin-top: 34px;
        border:none;
}
.tabs-content{
    width:100%;
}
.tabs-container ul li a{
	font-size:14px;
}
.img_div{
	margin: 0 auto;
}
.tabs-content div{
	width:80px;
	height:80px;
}


.header_line{
	border-bottom:none;
}
.header_line span{
	background:none;
}
#faq{
    border-top: none;
    padding-top: 0px;
}
</style>
<!--采购员和供应商登入框-->
<!--<div style="width:100%;background:#8ec31e;height:410px;">
<div class="nc-login-layout" style="width:1000px;">
  <div class="left-pic" style="margin-top:30px;"><img src="<?php echo SHOP_TEMPLATES_URL;?>/images/1.jpg"  border="0"></div>
  <div class="nc-login" style="margin:30px 0;width:480px;" >
  <div class="arrow"></div>
    <div class="nc-login-mode" style="margin-top:55px;">
      <div id="tabs_container" class="tabs-container" style="background:#FFFFFF; width:480px ;height:250px;border-radius:5px;margin-top:35px;" >
      <div style="height:140px;">
           <ul  class="tabs-nav" style="width:100%;margin-left:104px;">
            <li><div id="ZZE_LOGIN" class="tabs-content" >
               <div style="margin:0 auto;">
                   <i style="vertical-align: middle;"><a href="<?php echo urlShop('getmemberstatus', 'rz');?>" ><img src="<?php echo SHOP_TEMPLATES_URL;?>/images/icon02.png" style="margin-left:auto;"></a></i><br>
               </div>  
               <a href="<?php echo urlShop('getmemberstatus', 'rz');?>">我是物业采购员<i></i></a>
           </div>   
            </li>
            <li style="margin-left:72px;"><div id="ZZE_LOGIN" class="tabs-content" >
                <div style="margin:0 auto;">
                    <i style="vertical-align: middle;"><a href="<?php echo urlShop('store_joinin', 'step0');?>" ><img src="<?php echo SHOP_TEMPLATES_URL;?>/images/icon01.png"  style="margin-left:auto;"></a></i><br>
                </div>  
                <a href="<?php echo urlShop('store_joinin', 'step0');?>">我是供应商<i></i></a>
            </div> 
            </li>
         </ul>
         </div> 
         <div style=" text-align:center;height:48px;margin-bottom: 34px;margin-top: 24px;">
            <a href="<?php echo urlShop('login', 'logout');?>" >
            	<input style="background:#8ec31e;height: 42px;width: 348px;border-radius:5px;color:#FFF;font-size: 14px;" type="button" value="返回登录页面"/>
            </a>
        </div>
      </div>
      
    </div>
</div>
</div>
</div>-->


<!--只有供应商登入框-->
<div style="width:100%;background:#8ec31e;height:410px;">
<div class="nc-login-layout" style="width:1000px;">
  <div class="left-pic" style="margin-top:30px;"><img src="<?php echo SHOP_TEMPLATES_URL;?>/images/1.jpg"  border="0"></div>
  <div class="nc-login" style="margin:30px 0;width:480px;" >
  <div class="arrow"></div>
  <div class="nc-login-mode" style="margin-top:55px;">
      <div id="tabs_container" class="tabs-container" style="background:#FFFFFF; width:480px ;height:240px;border-radius:5px;margin-top:35px;" >
      <div style="height:140px;">
          <ul  class="tabs-nav" style="width:100%; margin: 0 auto;">
            <li style="margin: 34px auto 0;width:100%"><div id="ZZE_LOGIN" class="tabs-content" style=" text-align:center;">
                <div class="img_div">
                    <i style="vertical-align: middle;"><a href="<?php echo urlShop('store_joinin', 'step0');?>" >
                    <img src="<?php echo SHOP_TEMPLATES_URL;?>/images/icon01.png" class="icon_b"></a></i><br>
                </div>  
                <a href="<?php echo urlShop('store_joinin', 'step0');?>">我是供应商<i></i></a>
            </div> 
            </li>
         </ul>
         </div> 
         <div style=" text-align:center;height:48px;margin-bottom: 34px;margin-top: 24px;">
            <a href="<?php echo urlShop('login', 'logout');?>" >
            	<input style="background:#8ec31e;height: 42px;width: 348px;border-radius:5px;color:#FFF;font-size: 14px;" type="button" value="返回登录页面"/>
            </a>
        </div>
      </div>
      
    </div>
</div>
</div>
</div>
