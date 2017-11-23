<?php ?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/layout.css" rel="stylesheet" type="text/css">
<div class="nch-container wrapper">
  <div class="right" style="width:1200px;">
    <div class="nch-article-con">
      <div class="title-bar">
        <h3><?php echo $output['class_name'];?></h3>
      </div>
      <?php if(!empty($output['article']) and is_array($output['article'])){?>
          <?php if($output['is_pur'] ==false){?>
            <ul class="nch-article-list">
              <?php foreach ($output['article'] as $article) {?>
              <li><i></i><a  href="<?php echo urlShop('article', 'showPurchaseDetail', array('article_id'=>$article['purchase_rule_id']));?>"><?php echo $article['title'];?></a><time><?php echo date('Y-m-d H:i',$article['publish_date']);?></time></li>
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
