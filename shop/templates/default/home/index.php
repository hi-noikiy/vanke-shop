<?php ?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/index.css" rel="stylesheet" type="text/css"
      xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/home_index.js" charset="utf-8"></script>
<!--[if IE 6]>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/ie6.js" charset="utf-8"></script>
<![endif]-->
<script type="text/javascript">
var uid = window.location.href.split("#MI");
var  fragment = uid[1];
if(fragment){
	if (fragment.indexOf("MI") == 0) {document.cookie='uid=0';}
else {document.cookie='uid='+uid[1];}
	}

var stid = window.location.href.split("#ST");
var  fragments = stid[1];
if(fragments){
  if (fragments.indexOf("ST") == 0) {document.cookie='stid=0';}
  else {document.cookie='stid='+stid[1];}
}
function purRuleCheck(purId){
  if ( '<?php echo  $output['member_info']['level_name'] ?>') {
    if ( '<?php if( $_SESSION['identity'] == MEMBER_IDENTITY_TWO ) echo 'true';else echo"false"; ?>' === 'true' ) {
      window.open("<?php echo BASE_SITE_URL ?>/shop/article_pur-"+ purId+".html");
    }else{
      alert('<?php echo $lang['no_per_acc_procurement_rule'];?>');
    }
  }else{
    login_dialog();
  }

}
</script>
<!--tab切换-->
<script>
  $(function(){
    window.onload = function() {
      $('.btnleft').css('border-bottom','2px solid #8ec21f');

      /**
       * 采购供应商tab切换 控制角色 供应商登陆不能切换成采购员  采购员登陆不能切换成供应商
       */
      var $role_id ="<?php echo $_SESSION['identity'] ?>";
      var $li = $('#purTab li');
      var $ul = $('#content ul');
      if($role_id==<?php echo MEMBER_IDENTITY_THREE ?>||$role_id==<?php echo MEMBER_IDENTITY_FOUR ?>){
        $li.removeClass();
        $li.css('border-bottom','none');
        $li.eq(1).addClass('btnleft');
        $li.eq(1).css('border-bottom','2px solid #8ec21f');
        $ul.css('display','none');
        $ul.eq(1).css('display','block');
     
      }else if($role_id==<?php echo MEMBER_IDENTITY_ONE ?>){
      $li.mouseover(function(){
        var $this = $(this);
        var $t = $this.index();
        $li.removeClass();
        $li.css('border-bottom','none');
        $this.addClass('btnleft');
        $this.css('border-bottom','2px solid #8ec21f');
        $ul.css('display','none');
        $ul.eq($t).css('display','block');

        });
        }
       //异步调用接口获得招标询价信息
      $.ajax({
        type:'GET',
        url:'index.php?act=index&op=ajaxGetInfo',
        cache:false,
        dataType:'json',
        success:function(talk_list){
            if(talk_list.length >= 1) {
                for(var i = 0; i < talk_list.length; i++)
                {
                    var link = "<li><a href="+talk_list[i].href+">"+talk_list[i].title+"</a></li> ";
                    $("#tender_list").append(link);
                }
            }
        }
	});
      /**
       * 招标询价和采购制度切换
       */
      var $li02 = $('#purTab02 li');
      var $ul02 = $('#tab02Content ul');

      $li02.mouseover(function(){
        var $this = $(this);
        var $t = $this.index();
        $li02.removeClass();
        $li02.css('border-bottom','none');
        $this.addClass('btnleft');
        $this.css('border-bottom','2px solid #8ec21f');
        $ul02.css('display','none');
        $ul02.eq($t).css('display','block');

      })

      //跳转到采购系统js校验
      $('#pur_require_a').click(function(){
            if ( '<?php echo $output['member_info']['level_name'] ?>' ) {
              if ( '<?php if( $_SESSION['identity'] == MEMBER_IDENTITY_TWO ) echo 'true';else echo"false"; ?>' === 'true' ) {
                window.open("<?php echo YMA_WEBSERVICE_URL_HEAD ?>/impac/loginFromUrl.do?userCd=<?php echo $output['member_info']['pernr_id'];?>");
              }else{
                alert('<?php echo $lang['no_per_acc'];?>');
              }
            }else{
                login_dialog();
            }
      });

      $('#pur_plan_a').click(function(){
            if ( '<?php echo  $output['member_info']['level_name'] ?>') {
              if ( '<?php if( $_SESSION['identity'] == MEMBER_IDENTITY_TWO ) echo 'true';else echo"false"; ?>' === 'true' ) {
                window.open("<?php echo YMA_WEBSERVICE_URL_HEAD ?>/impac/loginFromUrl.do?userCd=<?php echo $output['member_info']['pernr_id'];?>");
              }else{
                alert('<?php echo $lang['no_per_acc'];?>');
              }
            }else{
              login_dialog();
            }
      });

      $('#pur_order_a').click(function(){
        if ( '<?php echo  $output['member_info']['level_name'] ?>') {
          if ( '<?php if( $_SESSION['identity'] == MEMBER_IDENTITY_TWO ) echo 'true';else echo"false"; ?>' === 'true' ) {
            window.open("<?php echo YMA_WEBSERVICE_URL_HEAD ?>/impac/loginFromUrl.do?userCd=<?php echo $output['member_info']['pernr_id'];?>");
          }else{
            alert('<?php echo $lang['no_per_acc'];?>');
          }
        }else{
          login_dialog();
        }
      });
      //
    }
  });
</script>
<div class="clear"></div>

<!-- HomeFocusLayout Begin-->
<div class="home-focus-layout"> <?php echo $output['web_html']['index_pic'];?>
  <div class="right-sidebar">
    <div class="proclamation"  >
        <div  class="portrait">
            <?php if ($_SESSION['member_id']==""){?>
            <a href="<?php echo $output['idm_url'];?>oauth2.0/authorize?client_id=AUTH_CG&redirect_uri=<?php echo $output['item_url'];?>shop/api.php?act=IDM&response_type=code">
                <img src="<?php echo SHOP_TEMPLATES_URL;?>/images/touxiang.gif" style="width:60px; height:60px">
            </a>
            <a class="emp-login" href="<?php echo $output['idm_url'];?>oauth2.0/authorize?client_id=AUTH_CG&redirect_uri=<?php echo $output['item_url'];?>shop/api.php?act=IDM&response_type=code">万科物业员工登录</a>
            <?php  }else{?>
                <img src="<?php echo SHOP_TEMPLATES_URL;?>/images/touxiang.gif" style="width:60px; height:60px">
                <p>您好，欢迎来到万科物业采购平台</p>
            <?php  } ?>
        </div>
      <ul class="btnCome" id="purTab">
          <li class="btnleft"><?php echo $lang['buyer_entrance'];?></li>
          <li class="btnright"><?php echo $lang['supplier_portal'];?></li>
      </ul>
      <div id="content">
          <ul class="tabs-nav" style="display: block;" >
              <?php if ($_SESSION['member_id']==""){?>
              <!-- 未登录状态 先进行隐藏显示暂未开放 -->
<!--              <li class="tabs-selected" style="left: 14%;">
                  <h3><a href="<?php echo urlShop('login','register');?>" ><?php echo $lang['index_register'];?></a></h3>
              </li>
              <li class="tabs-selected" style="left: 17%;">
                  <h3><a href="<?php echo  urlShop('login','index');?>"><?php echo $lang['index_login'];?></a></h3>
              </li>-->
              <li class="tabs-selected" style="left: 14%;">
              <h5>暂未开放此项功能</h5>

              <?php  }else{?>
              <!-- 登录状态 -->
              <?php if($_SESSION['identity']==MEMBER_IDENTITY_TWO){?>
              <li class="tabs-selected" style="left: 14%;">
                  <h3><a href="<?php echo YMA_IFRAME_URL_HEAD ?>/impac/loginFromUrl.do?shopCd=<?php echo Embedpage::getPurShopCdByDES();?>&userCd=<?php echo Embedpage::getPurUserCdByDES( $output['member_info']['pernr_id']);?>&validateFlg=true">采购后台</a></h3>
              </li>
              <?php }?>
              <li class="tabs-selected" style="left: 17%;">
                  <h3><a href="<?php echo  urlShop('member','home');?>">用户中心</a></h3>
              </li>
              <?php  } ?>
          </ul>
        <ul class="tabs-nav" style="display: none;" >
          <?php if ($output['member_info']['level_name']) {
            echo '<li class="tabs-selected" style="left: 14%;">
					<h3><a href="'. urlShop('show_join','index') .'">认证'. $lang['index_come_in'].'</a></h3>
				</li>';
            echo '<li class="tabs-selected" style="left: 17%;">
				<h3><a href="' . urlShop('member','home').'">用户中心</a></h3>
			</li>';
          }else{
            echo'<li class="tabs-selected">
		<h3><a href="'. urlShop('login', 'register',array('ref_url'=> urlencode($output['ref_url']) )).'">'. $lang['index_register'].'</a></h3>
	</li>';
            echo'<li class="tabs-selected" >
	<h3><a href="'. urlShop('show_join','index').'">'. $lang['index_come_in'].'</a></h3>
</li>';
            echo'<li class="tabs-selected">
<h3><a href="'. urlShop('login','index').'">'. $lang['index_login'].'</a></h3>
</li>';}?>
        </ul>

      </div>
      <ul class="btnCome" id="purTab02">
        <li class="btnleft"><?php echo $lang['tender_inquiry'];?></li>
        <li class="btnright"><?php echo $lang['procurement_system'];?></li>
      </ul>
      <div id="tab02Content">
          <ul class="dynamics" id="tender_list">
           <!--   <?php if(sizeof($output['tender_inquiry']) > 0){ ?>
              <?php for($i = 0 ; $i<sizeof($output['tender_inquiry']);$i++){ ?>
              <li><a href="<?php echo $output['tender_inquiry'][$i]['href'];?>"><?php echo $output['tender_inquiry'][$i]['title'];?></a></li>  
              <?php }   ?>
              <?php }  ?>
           -->
          </ul>
          <ul class="dynamics" style="display: none;">
            <?php if(sizeof($output['article']) > 0 ) {foreach ($output['article'] as $article) {?>
                <li><a  onclick="purRuleCheck(<?php echo $article['article_id']; ?>)" title="<?php echo $article['article_title'];?>" <?php if($article['article_url']!=''){?>target="_blank"<?php }?> href="javaScript:void(0);"><?php echo '【'.$article['publish_department'] ."】".$article['article_title'];?></a></li>
            <?php }}else{?>
                <li><?php echo $lang['no_procurement_rule'];?></li>
            <?php }?>
          </ul>
      </div>
    </div>


  </div>
</div>
<!--HomeFocusLayout End-->

<div class="home-sale-layout wrapper">
  <?php if(!empty($output['xianshi_item']) && is_array($output['xianshi_item'])) { ?>
  <div class="right-sidebar">
    <div class="title">
      <h3><?php echo $lang['nc_xianshi'];?></h3>
    </div>
    <div id="saleDiscount" class="sale-discount">
      <ul>
        <?php foreach($output['xianshi_item'] as $val) { ?>
        <li>
          <dl>
            <dt class="goods-name"><?php echo $val['goods_name']; ?></dt>
            <dd class="goods-thumb"><a href="<?php echo urlShop('goods','index',array('goods_id'=> $val['goods_id']));?>"> <img src="<?php echo thumb($val, 240);?>"></a></dd>
            <dd class="goods-price"><?php echo ncPriceFormatForList($val['xianshi_price']); ?> <span class="original"><?php echo ncPriceFormatForList($val['goods_price']);?></span></dd>
            <dd class="goods-price-discount"><em><?php echo $val['xianshi_discount']; ?></em></dd>
            <dd class="time-remain" count_down="<?php echo $val['end_time']-TIMESTAMP;?>"><i></i><em time_id="d">0</em><?php echo $lang['text_tian'];?><em time_id="h">0</em><?php echo $lang['text_hour'];?> <em time_id="m">0</em><?php echo $lang['text_minute'];?><em time_id="s">0</em><?php echo $lang['text_second'];?> </dd>
            <dd class="goods-buy-btn"></dd>
          </dl>
        </li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <?php } ?>

  <div class="left-layout" style="overflow: hidden;height:295px;margin-bottom: 10px;">
    <div class="title">
      <h2 title="热门推荐">热门推荐</h2>
    </div>
    <?php echo $output['web_html']['index_sale'];?> </div>
</div>
<div class="wrapper">
  <div class="mt10">
    <div class="mt10"><?php echo loadadv(11,'html');?></div>
  </div>
</div>
<!--StandardLayout Begin--> 
<?php echo $output['web_html']['index'];?> 
<!--StandardLayout End--> 

<div class="wrapper">
  <div class="mt10"><?php echo loadadv(9,'html');?></div>
</div>

<!--link Begin-->

<div class="wrapper partner" style="display: none;">
  <div class="title">
    <h2 title="入驻商家">入驻商家</h2>
  </div>
  <div class="partner-list">
    <div class="piclink">
      <?php if(is_array($output['$link_list']) && !empty($output['$link_list'])) {
        foreach($output['$link_list'] as $val) {
          if($val['link_pic'] != ''){
            ?>
            <span><a href="<?php echo $val['link_url']; ?>" target="_blank"><img src="<?php echo $val['link_pic']; ?>" title="<?php echo $val['link_title']; ?>" alt="<?php echo $val['link_title']; ?>" width="88" height="31" ></a></span>
            <?php
          }
        }
      }
      ?>
      <div class="clear"></div>
    </div>
  </div>
</div>


<!--link end-->

<div class="footer-line"></div>
<!--首页底部保障开始-->
<?php require_once template('layout/index_ensure');?>
<!--首页底部保障结束--> 
<!--StandardLayout Begin-->
<!--<div class="nav_Sidebar"  style="display: none;">
<a class="nav_Sidebar_1" href="javascript:;" ></a>
<a class="nav_Sidebar_2" href="javascript:;" ></a>
<a class="nav_Sidebar_3" href="javascript:;" ></a>
<a class="nav_Sidebar_4" href="javascript:;" ></a>
<a class="nav_Sidebar_5" href="javascript:;" ></a>
<a class="nav_Sidebar_6" href="javascript:;" ></a>
<a class="nav_Sidebar_7" href="javascript:;" ></a>
<a class="nav_Sidebar_8" href="javascript:;" ></a>
</div>-->
<!--StandardLayout End-->