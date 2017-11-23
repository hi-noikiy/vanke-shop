<?php ?>
      <?php if($item_edit_flag) { ?>
<table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th colspan="12" class="nobg"> <div class="title nomargin">
            <h5><?php echo $lang['nc_prompts'];?></h5>
            <span class="arrow"></span> </div>
        </th>
      </tr>
      <tr>
        <td><ul>
            <li>鼠标移动到内容上出现编辑按钮可以对内容进行修改</li>
            <li>操作完成后点击保存编辑按钮进行保存</li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <?php } ?>
<div class="index_block home5">
      <?php if($item_edit_flag) { ?>
  <h3>模型版块布局E</h3>
  <?php } ?>
  <div class="title">
    <?php if($item_edit_flag) { ?>
        <p>
      标题：<input id="home1_title" type="text" class="txt w200" name="item_data[title]" value="<?php echo $item_data['title'];?>"> 商品分类编号：
    <input id="home1_catalog" type="text" class="txt w24" name="item_data[catalog_id]" value="<?php echo $item_data['catalog_id'];?>">
      </p><br/>
      <h5 style="font-size: 13px; color: #09C;">上左：</h5>
      <p>标题：<input id="home1_left_title" type="text" class="txt w200" name="item_data[left_title]" value="<?php echo $item_data['left_title'];?>">
        售价：<input id="home1_left_price" type="text" class="txt w24" name="item_data[left_price]" value="<?php echo $item_data['left_price'];?>"> 市价：<input id="home1_left_market" type="text" class="txt w48" name="item_data[left_market]" value="<?php echo $item_data['left_market'];?>">
      </p>
      <br/>
      <h5 style="font-size: 13px; color: #09C;">上右：</h5>
      <p>
        图1标题：<input id="home1_right_title1" type="text" class="txt w200" name="item_data[right_title1]" value="<?php echo $item_data['right_title1'];?>">
        售价：<input id="home1_right_price1" type="text" class="txt w24" name="item_data[right_price1]" value="<?php echo $item_data['right_price1'];?>"> 市价：<input id="home1_right_market1" type="text" class="txt w48" name="item_data[right_market1]" value="<?php echo $item_data['right_market1'];?>">
<br/>
        图2标题：<input id="home1_right_title2" type="text" class="txt w200" name="item_data[right_title2]" value="<?php echo $item_data['right_title2'];?>">
        售价：<input id="home1_right_price2" type="text" class="txt w24" name="item_data[right_price2]" value="<?php echo $item_data['right_price2'];?>"> 市价：<input id="home1_right_market2" type="text" class="txt w48" name="item_data[right_market2]" value="<?php echo $item_data['right_market2'];?>">
      </p>
        <br/>
        <h5 style="font-size: 13px; color: #09C;">底部：</h5>
        <p>
            图1标题：<input id="home1_bottom_title1" type="text" class="txt w200" name="item_data[bottom_title1]" value="<?php echo $item_data['bottom_title1'];?>">
            售价：<input id="home1_bottom_price1" type="text" class="txt w24" name="item_data[bottom_price1]" value="<?php echo $item_data['bottom_price1'];?>"> 市价：<input id="home1_bottom_market1" type="text" class="txt w48" name="item_data[bottom_market1]" value="<?php echo $item_data['bottom_market1'];?>">
            <br/>
            图2标题：<input id="home1_bottom_title2" type="text" class="txt w200" name="item_data[bottom_title2]" value="<?php echo $item_data['bottom_title2'];?>">
            售价：<input id="home1_bottom_price2" type="text" class="txt w24" name="item_data[bottom_price2]" value="<?php echo $item_data['bottom_price2'];?>"> 市价：<input id="home1_bottom_market2" type="text" class="txt w48" name="item_data[bottom_market2]" value="<?php echo $item_data['bottom_market2'];?>">
            <br/>
            图3标题：<input id="home1_bottom_title3" type="text" class="txt w200" name="item_data[bottom_title3]" value="<?php echo $item_data['bottom_title3'];?>">
            售价：<input id="home1_bottom_price3" type="text" class="txt w24" name="item_data[bottom_price3]" value="<?php echo $item_data['bottom_price3'];?>"> 市价：<input id="home1_bottom_market3" type="text" class="txt w48" name="item_data[bottom_market3]" value="<?php echo $item_data['bottom_market3'];?>">
        </p>
    <?php } else { ?>
    <span><?php echo $item_data['title'];?></span>
    <?php } ?>
  </div>
  <div class="content">
      <?php if($item_edit_flag) { ?>
    <h5>内容：</h5>
    <?php } ?>

    <div class="home2_1">
      <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['square_image']);?>" alt="">
        <?php if($item_edit_flag) { ?>
          <input nctype="image_name" name="item_data[square_image]" type="hidden" value="<?php echo $item_data['square_image'];?>">
          <input nctype="image_type" name="item_data[square_type]" type="hidden" value="<?php echo $item_data['square_type'];?>">
          <input nctype="image_data" name="item_data[square_data]" type="hidden" value="<?php echo $item_data['square_data'];?>">
          <a nctype="btn_edit_item_image" data-desc="320*520" href="javascript:;"><i class="icon-edit"></i>编辑</a>
        <?php } ?>
      </div>
    </div>

    <div class="home2_2">
      <div class="home2_2_1">
        <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['rectangle1_image']);?>" alt="">
          <?php if($item_edit_flag) { ?>
          <input nctype="image_name" name="item_data[rectangle1_image]" type="hidden" value="<?php echo $item_data['rectangle1_image'];?>">
          <input nctype="image_type" name="item_data[rectangle1_type]" type="hidden" value="<?php echo $item_data['rectangle1_type'];?>">
          <input nctype="image_data" name="item_data[rectangle1_data]" type="hidden" value="<?php echo $item_data['rectangle1_data'];?>">
          <a nctype="btn_edit_item_image" data-desc="320*260" href="javascript:;"><i class="icon-edit"></i>编辑</a>
          <?php } ?>
        </div>
        <div class="home2_2_2">
          <div nctype="item_image" class="item"> <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['rectangle2_image']);?>" alt="">
            <?php if($item_edit_flag) { ?>
            <input nctype="image_name" name="item_data[rectangle2_image]" type="hidden" value="<?php echo $item_data['rectangle2_image'];?>">
            <input nctype="image_type" name="item_data[rectangle2_type]" type="hidden" value="<?php echo $item_data['rectangle2_type'];?>">
            <input nctype="image_data" name="item_data[rectangle2_data]" type="hidden" value="<?php echo $item_data['rectangle2_data'];?>">
            <a nctype="btn_edit_item_image" data-desc="320*260" href="javascript:;"><i class="icon-edit"></i>编辑</a>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <div class="home2_3">
      <div class="home2_3_1">
        <div nctype="item_image" class="item">
            <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['bottom1_image']);?>" alt="">
            <?php if($item_edit_flag) { ?>
              <input nctype="image_name" name="item_data[bottom1_image]" type="hidden" value="<?php echo $item_data['bottom1_image'];?>">
              <input nctype="image_type" name="item_data[bottom1_type]" type="hidden" value="<?php echo $item_data['bottom1_type'];?>">
              <input nctype="image_data" name="item_data[bottom1_data]" type="hidden" value="<?php echo $item_data['bottom1_data'];?>">
              <a nctype="btn_edit_item_image" data-desc="180*180" href="javascript:;"><i class="icon-edit"></i>编辑</a>
            <?php } ?>
          </div>
      </div>
      <div class="home2_3_2">
        <div nctype="item_image" class="item">
            <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['bottom2_image']);?>" alt="">
            <?php if($item_edit_flag) { ?>
              <input nctype="image_name" name="item_data[bottom2_image]" type="hidden" value="<?php echo $item_data['bottom2_image'];?>">
              <input nctype="image_type" name="item_data[bottom2_type]" type="hidden" value="<?php echo $item_data['bottom2_type'];?>">
              <input nctype="image_data" name="item_data[bottom2_data]" type="hidden" value="<?php echo $item_data['bottom2_data'];?>">
              <a nctype="btn_edit_item_image" data-desc="180*180" href="javascript:;"><i class="icon-edit"></i>编辑</a>
            <?php } ?>
          </div>
      </div>
      <div class="home2_3_3">
        <div nctype="item_image" class="item">
            <img nctype="image" src="<?php echo getMbSpecialImageUrl($item_data['bottom3_image']);?>" alt="">
            <?php if($item_edit_flag) { ?>
              <input nctype="image_name" name="item_data[bottom3_image]" type="hidden" value="<?php echo $item_data['bottom3_image'];?>">
              <input nctype="image_type" name="item_data[bottom3_type]" type="hidden" value="<?php echo $item_data['bottom3_type'];?>">
              <input nctype="image_data" name="item_data[bottom3_data]" type="hidden" value="<?php echo $item_data['bottom3_data'];?>">
              <a nctype="btn_edit_item_image" data-desc="180*180" href="javascript:;"><i class="icon-edit"></i>编辑</a>
            <?php } ?>
          </div>
      </div>
    </div>

  </div>
</div>
