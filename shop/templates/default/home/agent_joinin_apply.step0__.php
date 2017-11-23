<?php ?>
<script type="text/javascript">
$(document).ready(function(){
    $('#btn_apply_agreement_c2c_next').on('click', function() {
        if($('#input_apply_agreement').prop('checked')) {
            window.location.href = "index.php?act=agent_joinin&op=step1";
        } else {
            alert('请阅读并同意协议');
        }
    });
});
</script>

<div class="store-joinin-apply"> 
  <!-- 协议 -->
  <div id="apply_agreement" class="apply-agreement">
    <h3>加盟协议</h3>
    <div class="apply-agreement-content">
        <?php echo $output['agreement'];?>
    </div>
    <div class="apple-agreement">
      <input id="input_apply_agreement" name="input_apply_agreement" type="checkbox" checked />
      <label for="input_apply_agreement">我已阅读并同意以上协议</label>
    </div>
    <div class="bottom">
		<a id="btn_apply_agreement_c2c_next" href="javascript:;" class="btn">申请加盟</a>
	</div>
  </div>
</div>
