<?php ?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/layout.css" rel="stylesheet" type="text/css">
<div class="nch-container wrapper">
    <div class="left">
    
    <div class="nch-module nch-module-style01">
      <div class="title">
        <h3><?php echo $lang['article_article_new_article'];?></h3>
      </div>
      <div class="content">
        <ul class="nch-sidebar-article-list">
          <?php if(is_array($output['new_article_list']) and !empty($output['new_article_list'])){?>
          <?php foreach ($output['new_article_list'] as $k=>$v){?>
          <li><i></i><a <?php if($v['article_url']!=''){?>target="_blank"<?php }?> href="<?php if($v['article_url']!='')echo $v['article_url'];else echo urlShop('article', 'school_show', array('article_id'=>$v['article_id']));?>"><?php echo $v['article_title']?></a></li>
          <?php }?>
          <?php }else{?>
          <li><?php echo $lang['article_article_no_new_article'];?></li>
          <?php }?>
        </ul>
      </div>
    </div>
  </div>
  <div class="right" style="float:left;">
    <div class="nch-article-con">
     
      <?php if(!empty($output['article']) and is_array($output['article'])){?>
          <?php if($output['is_pur'] ==false){?>
            <ul class="nch-article-list">
              <?php foreach ($output['article'] as $article) {?>
                <dl>
                    <dt><a href="<?php echo urlShop('article', 'school_show', array('article_id'=>$article['article_id']));?>"><img src="<?php echo $article['pic'];?>"></a></dt>
                    <dd class="article-title"><a href="<?php echo urlShop('article', 'school_show', array('article_id'=>$article['article_id']));?>"><?php echo $article['article_title'];?></a></dd>
                    <dd class="article-descript"><?php echo $article['article_summary'];?></dd>
                    <dd class="article-date"><?php echo date('Y-m-d H:i',$article['article_time']);?></dd>
                  </dl>
                <!--
              <li><i></i><a  href="<?php echo urlShop('article', 'showPurchaseDetail', array('article_id'=>$article['article_id']));?>"><?php echo $article['article_title'];?></a><time><?php echo date('Y-m-d H:i',$article['article_time']);?></time></li>
              -->
                  <?php }?>
            </ul>
          <?php }?>
      <?php }else{?>
      <div><?php echo $lang['article_article_not_found'];?></div>
      <?php }?>
     
    </div> <div class="tc mb20">  <div class="pagination"> <?php echo $output['show_page'];?> </div></div>
  
  </div>
</div>
<link rel="stylesheet" href="<?php echo SHOP_TEMPLATES_URL;?>/css/modify.css">