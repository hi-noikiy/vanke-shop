<?php ?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/layout.css" rel="stylesheet" type="text/css">
<div class="nch-container wrapper">
  
  <div class="right" style="width:1200px;">
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
