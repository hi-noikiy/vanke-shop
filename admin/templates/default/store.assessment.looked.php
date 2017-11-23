
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo $lang['title'];?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=assessment&op=store"><span>管理</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <style>
      table tr td {font-size:15px;padding:10px;border: solid 1px #000;}
  </style>
      <table width="1000" style="border:solid 1px #ccc;" border="1">
          <tr style="background-color:#CCC;">
    <td rowspan="2"><div align="center"><code>一级评审项目</code></div></td>
    <td rowspan="2"><div align="center"><code>二级评审项目</code></div></td>
    <td rowspan="2"><div align="center"><code>审核指标</code></div></td>
    <td colspan="2"><div align="center"><code>评审标准</code></div></td>
    <td rowspan="2"><div align="center"><code>权重</code></div></td>
    <td rowspan="2"><div align="center"><code>评分</code></div></td>
  </tr>
  <tr style="background-color:#CCC;">
    <td><div align="center"><code>最高分描述</code></div></td>
    <td><div align="center"><code>最低分描述</code></div></td>
  </tr>
  <?php $num_c = 1;$num_code = 1;foreach($output['list'] as $rows){$i=0;?>
          <?php foreach($rows['data1'] as $data1){$i2 = 0; ?>
                <?php foreach($data1['data2'] as $data2){?>
            <tr>
                <?php if($i == 0){?>
                <!--一级分类-->
                <td rowspan="<?php echo $rows['num']?>"><code><?php echo $rows['type_1']?></code></td>
                <?php }?>
                <?php if($i2 == 0){?>
                <!--二级分类-->
                <td rowspan="<?php echo count($data1['data2']);?>"><code><?php echo $data1['type_2']?></code></td>
                <?php }?>
                <!--指标-->
                <td><code><?php echo $data2['question'];?></code></td>
                <td><code><?php echo $data2['desc_5'];?></code></td>
                <td><code><?php echo $data2['desc_1'];?></code></td>
                <td><code class='scale_<?php echo $num_c++;?>'><?php echo $data2['scale'];?></code></td>
                <td>
                    <code>
                        &nbsp; <span class='scalecode_<?php echo $num_code++;?>' ><?php echo $data2['score'];?></span>&nbsp;分
                    </code>
                   
                </td>
            </tr>
                <?php $i++;$i2++;}?>
          <?php }?>
    
    <?php }?>
        <tfoot>
        <tr>
          <td colspan="7"><a href="JavaScript:void(0);" class="txt red" id="look_num_up"><span></span></a></td>
        </tr>
      </tfoot>
</table>

</div>
<script>
$(function(){
    var num_c = <?php echo $num_c;?>;
    var cun_num = 0;
    for(i=1;i<=num_c-1;i++){
            var code_num = $('.scalecode_'+i).html();
            cun_num += $('.scale_'+i).html()/5*code_num;
    }
    $('#look_num_up').html('<span>本次评估等分为：'+cun_num+'</span>');
})
</script>