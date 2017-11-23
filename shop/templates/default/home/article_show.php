<?php ?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/layout.css" rel="stylesheet" type="text/css">
<div class="nch-container wrapper">
  <div class="left">
    <div class="nch-module nch-module-style01" <?php if($_SESSION['identity'] != MEMBER_IDENTITY_TWO ) echo 'style="display: none"' ?>>
      <div class="title">
        <h3><?php echo $lang['purchase_rule'];?></h3>
      </div>
      <div class="content">
        <ul class="nch-sidebar-article-class">
          <li><a href="<?php echo urlShop('article', 'purchaseProcess');?>"><?php echo $lang['purchase_rule']?></a></li>
        </ul>
      </div>
    </div>
    <div class="nch-module nch-module-style01">
      <div class="title">
        <h3><?php echo $lang['article_article_article_class'];?></h3>
      </div>
      <div class="content">
        <div class="nch-sidebar-article-class">
          <ul>
            <?php foreach ($output['sub_class_list'] as $k=>$v){?>
            <li><a href="<?php echo urlShop('article', 'article', array('ac_id'=>$v['ac_id']));?>"><?php echo $v['ac_name']?></a></li>
            <?php }?>
          </ul>
        </div>
      </div>
    </div>
    <div class="nch-module nch-module-style03">
      <div class="title">
        <h3><?php echo $lang['article_article_new_article'];?></h3>
      </div>
      <div class="content">
        <ul class="nch-sidebar-article-list">
          <?php if(is_array($output['new_article_list']) and !empty($output['new_article_list'])){?>
          <?php foreach ($output['new_article_list'] as $k=>$v){?>
          <li><i></i><a <?php if($v['article_url']!=''){?>target="_blank"<?php }?> href="<?php if($v['article_url']!='')echo $v['article_url'];else echo urlShop('article', 'show', array('article_id'=>$v['article_id']));?>"><?php echo $v['article_title']?></a></li>
          <?php }?>
          <?php }else{?>
          <li><?php echo $lang['article_article_no_new_article'];?></li>
          <?php }?>
        </ul>
      </div>
    </div>
  </div>
  <div class="right">
    <div class="nch-article-con">
      <h1><?php echo $output['article']['article_title'];?></h1>
      <?php if($output['is_pur'] ==false){?>
        <h2> <?php  echo date('Y-m-d H:i',$output['article']['article_time']);?></h2>
      <?php }else{?>
          <h2> <?php echo  '发布部门:' . $output['article']['publish_department'];?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo '适用人员:'. $output['article']['object_person'];?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo date('Y-m-d H:i',$output['article']['article_time']);?>
            <?php if(!empty($output['article']['attachment'] )){?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a style="color:#006bcd;" href="index.php?act=article&op=download&purchase_rule_id=<?php echo  $output['article']['article_id'];?>" >附件下载</a> <?php }?>
          </h2>
      <?php }?>
      <div class="default">
        <p><?php echo $output['article']['article_content'];?></p>
      </div>
      <div class="more_article"> <span class="fl"><?php echo $lang['article_show_previous'];?>：
        <?php if(!empty($output['pre_article']) and is_array($output['pre_article'])){?>
        <a <?php if($output['pre_article']['article_url']!=''){?>target="_blank"<?php }?> href="<?php if($output['pre_article']['article_url']!='')echo $output['pre_article']['article_url'];else if($output['is_pur']) echo urlShop('article', 'showPurchaseDetail', array('article_id'=>$output['pre_article']['article_id'])); else echo urlShop('article', 'show', array('article_id'=>$output['pre_article']['article_id']));?>"><?php echo $output['pre_article']['article_title'];?></a> <time><?php echo date('Y-m-d H:i',$output['pre_article']['article_time']);?></time>
        <?php }else{?>
        <?php if($output['is_pur']) echo $lang['article_pur_not_found']; else echo $lang['article_article_not_found'];?>
        <?php }?>
        </span> <span class="fr"><?php echo $lang['article_show_next'];?>：
        <?php if(!empty($output['next_article']) and is_array($output['next_article'])){?>
        <a <?php if($output['next_article']['article_url']!=''){?>target="_blank"<?php }?> href="<?php if($output['next_article']['article_url']!='')echo $output['next_article']['article_url'];else if($output['is_pur']) echo urlShop('article', 'showPurchaseDetail', array('article_id'=>$output['next_article']['article_id'])); else  echo urlShop('article', 'show', array('article_id'=>$output['next_article']['article_id']));?>"><?php echo $output['next_article']['article_title'];?></a> <time><?php echo date('Y-m-d H:i',$output['next_article']['article_time']);?></time>
        <?php }else{?>
        <?php if($output['is_pur']) echo $lang['article_pur_not_found']; else echo $lang['article_article_not_found'];?>
        <?php }?>
        </span> </div>
    </div>
  </div>
</div>
