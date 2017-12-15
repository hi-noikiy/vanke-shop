<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/12/15
 * Time: 下午2:39
 */
?>
<div class="item-title">
    <h3>供应商审核管理</h3>
    <ul class="tab-base">
        <?php if(!empty($output['top_list']) && is_array($output['top_list'])){?>
            <?php foreach ($output['top_list'] as $key=>$val){?>
                <li><a <?php if($_GET['op'] == $key){?> href="JavaScript:void(0);" class="current" <?php }else{?>  href="index.php?act=supplier&op=<?php echo $key?>" <?php } ?> ><span><?php echo $val?></span></a></li>
        <?php }}?>
    </ul>
</div>
