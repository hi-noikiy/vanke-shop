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
        <ul class="nch-sidebar-article-class">
          <?php foreach ($output['sub_class_list'] as $k=>$v){?>
          <li><a href="<?php echo urlShop('article', 'article', array('ac_id'=>$v['ac_id']));?>"><?php echo $v['ac_name']?></a></li>
          <?php }?>
        </ul>
      </div>
    </div>
    <div class="nch-module nch-module-style01">
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
  <div class="right" >
    <div class="nch-article-con">
      <div class="title-bar">
        <h3><?php echo $output['class_name'];?></h3>
      </div>
      <?php if(!empty($output['article']) and is_array($output['article'])){?>
          <?php if($output['is_pur'] ==false){?>
            <ul class="nch-article-list">
              <?php foreach ($output['article'] as $article) {?>
              <li><i></i><a  <?php if($article['article_url']!=''){?>target="_blank"<?php }?> href="<?php if($article['article_url']!='')echo $article['article_url'];else  echo urlShop('article', 'show', array('article_id'=>$article['article_id']));?>"><?php echo $article['article_title'];?></a><time><?php echo date('Y-m-d H:i',$article['article_time']);?></time></li>
              <?php }?>
            </ul>
          <?php }else{?>
              <table class="ncm-default-table order">
                  <thead>
                    <tr>
                      <th class="w10"</th>
                      <th class="w500">标题</th>
                      <th class="w400">发布部门</th>
                      <th class="w800">适用人员</th>
                      <th class="w200">发布日期</th>
                    </tr>
                  </thead>
                  <tbody >
                      <?php foreach ($output['article'] as $article) {?>
                          <tr style="height:30px;padding:5px 0;">
                            <td class="w1"></td>
                            <td class="w500"><a  <?php if($article['article_url']!=''){?>target="_blank"<?php }?> href="<?php if($article['article_url']!='')echo $article['article_url'];else  echo urlShop('article', 'showPurchaseDetail', array('article_id'=>$article['article_id']));?>"><?php echo $article['article_title'];?></a></td>
                            <td class="w400"><?php echo $article['publish_department'];?></td>
                            <td class="w800"><?php echo $article['object_person'];?></td>
                            <td class="w300"><time><?php echo date('Y-m-d H:i',$article['article_time']);?></time></td>
                          </tr>
                      <?php }?>
                  </tbody>
              </table>
          <?php }?>
      <?php }else{?>
      <div><?php echo $lang['article_article_not_found'];?></div>
      <?php }?>
     
    </div> <div class="tc mb20">  <div class="pagination"> <?php echo $output['show_page'];?> </div></div>
  
  </div>
</div>
