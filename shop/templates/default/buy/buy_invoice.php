<?php ?>
<link rel="stylesheet" href="<?php echo RESOURCE_SITE_URL;?>/js/layui/css/layui.css" media="all">
<style>
.ncc-receipt-info-title{margin-top: 10px;margin-bottom: 10px;}
.span-btn {
    padding: 0 18px;
    text-align: center;
    font-size: 14px;
    color: #fff;
    white-space: nowrap;
    border: none;
    border-radius: 2px;
    opacity: .9;
    filter: alpha(opacity=90);
    margin-left: 20px;
    height: 30px;
    line-height: 30px;
    cursor:pointer
}
.invoice-list ul li:hover {
    background: rgba(255,245,204,0.25)
}
.invoice-btns {
    float: right;
    text-align: right;
    height: 30px;
    line-height: 30px;
    display: none;
}
.invoice-btns a {
    margin-right: 10px;
}
</style>
<div class="ncc-receipt-info" style="border-bottom: 0px;">
  <div class="ncc-receipt-info-title">
    <h3>发票信息</h3>
      <a href="javascript:void(0);" style="float: right;" id="new-inv">
      <span style="float: right;font-size:14px;color: #27A9E3">新增发票
          <i class="layui-icon" style="font-size: 15px; color: #27A9E3">&#xe608;</i>
      </span>
      </a>
  </div>
  <div id="invoice-list" class="ncc-candidate-items">
      <div id="invoice-one" class="invoice-item" style="display: none;">
          <span id="invoice-type" class="span-btn" style="height: 38px;line-height: 38px;cursor:default"></span>
          <span id="invoice-title" style="margin-left: 10px"></span>
          <span id="invoice-name" style="margin-left: 10px;color: #AAA;"></span>
          <span class="updata-invoice" style="float:right;margin-right: 10px;color: #27A9E3;cursor:pointer;margin-top: 5px;">[&nbsp;修改&nbsp;]</span>
          <input type="hidden" id="invoice_code" name="invoice_code" value=""/>
      </div>
      <div id="invoice-all" class="invoice-list" style="position: relative; overflow: hidden; width: 938px; height: 155px; z-index: 10;overflow-y:scroll;">
          <ul name="invoice-item-list">
           <?php if(!empty($output['invoice_list']) && is_array($output['invoice_list'])){?>
            <?php foreach ($output['invoice_list'] as $vl){?>
              <li style="border:none" id="invoice_<?php echo $vl['inv_id'];?>" inv-state="<?php echo $vl['inv_state'];?>"
                   inv-title="<?php echo $vl['inv_title'];?>" inv-content="<?php echo $vl['inv_content'];?>"
                   inv-id="<?php echo $vl['inv_id'];?>">
                  <?php if($vl['inv_state'] == '1'){?>
                    <span class="layui-btn layui-btn-warm" style="height: 30px;line-height: 30px;width:110px">普通发票</span>
                  <?php }else{?>
                    <span class="layui-btn layui-btn-danger" style="height: 30px;line-height: 30px;width:110px">增值税发票</span>
                  <?php }?>
                  <span><?php echo $vl['inv_title'];?></span>
                  <span style="color: #AAA;margin-left: 5px;"><?php echo $vl['inv_content'];?></span>
                  <div class="invoice-btns">
                      <a href="javascript:void(0);" class="ftx-05 edit-invoice" invoice-code="<?php echo $vl['inv_id'];?>">编辑</a>
                      <a href="javascript:void(0);" class="ftx-05 del-invoice" invoice-code="<?php echo $vl['inv_id'];?>">删除</a>
                  </div>
              </li>
            <?php }}?>
          </ul>
      </div>
      <div id="empty-invoice" <?php if(!empty($output['address_info'])){?>style="display: none" <?php }?> >
          <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;" >
              <legend>请添加确认收货地址信息</legend>
          </fieldset>
      </div>
  </div>
</div>


<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/jquery-3.2.1.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/layui.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/form.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/layui/city_select.js"></script>
<script type="text/javascript">
    $("ul[name='invoice-item-list']").on("mouseover", "li", function() {
        $(this).find(".invoice-btns").show();
    });

    $("ul[name='invoice-item-list']").on("mouseleave", "li", function() {
        $(this).find(".invoice-btns").hide();
    });

    //点击选择
    $("ul[name='invoice-item-list']").on("click", ".layui-btn", function() {
        $("#invoice-one").show();$("#invoice-all").hide();
        //判定发票类型
        $("#invoice-type").removeClass("layui-btn-warm");
        $("#invoice-type").removeClass("layui-btn-danger");
        if($(this).parent().attr("inv-state") == '1'){
            $("#invoice-type").html('普通发票');
            $("#invoice-type").addClass("layui-btn-warm");
        }else{
            $("#invoice-type").html('增值税发票');
            $("#invoice-type").addClass("layui-btn-danger");
        }
        $("#invoice-title").html($(this).parent().attr("inv-title"));
        $("#invoice-name").html($(this).parent().attr("inv-content"));
        $("#invoice_code").val($(this).parent().attr("inv-id"));
    });

    $("#invoice-one").on("click", ".updata-invoice", function() {
        $("#invoice-one").hide();$("#invoice-all").show();
    });

    $("#new-inv").click(function(){
        var member = "<?php echo $_SESSION['member_id']?>";
        var url = '/shop/index.php?act=buy&op=addInv';
        open_window(member,'新增收货人地址信息',url,'820','700');
    });


    //删除发票信息
    $("ul[name='invoice-item-list']").on("click", ".del-invoice", function() {
        var id = $(this).attr("invoice-code");
        $.ajax({
            type:"POST",
            //提交的网址
            url:"/shop/index.php?act=member_invoice&op=delInv",
            data:{id: id},
            datatype: "json",
            success:function(result){
                var result = JSON.parse(result);
                if(result.code == '1'){
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert('删除成功', {closeBtn: 0,title: '温馨提示',offset: '100px'}, function(index){
                            $("#invoice_"+id).remove();layer.closeAll();
                        });
                    })
                }else{
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        layer.alert(result.msg, {closeBtn: 0,title: '温馨提示',offset: '100px'}, function(index){
                            layer.closeAll();
                        });
                    })
                }
            }
        });
    });

function close_inv(){
    layui.use('layer', function(){
        var layer = layui.layer;
        layer.closeAll();
    })
}

function add_inv(list){
    var str = "<li style='border:none' id='invoice_"+list.inv_id+"' inv-state='"+list.inv_state+"'";
    str+= " inv-title='"+list.inv_title+"' inv-content='"+list.inv_content+"' inv-id='"+list.inv_id+"'>";
    str+= "<span class='layui-btn "+list.inv_state_css+"' style='height:30px;line-height:30px;width:110px'>"+list.inv_state_str+"</span>";
    str+= "<span>"+list.inv_title+"</span>";
    str+= "<span style='color: #AAA;margin-left: 5px;'>"+list.inv_content+"</span>";
    str+= "<div class='invoice-btns'>";
    str+= "<a href=\"javascript:void(0);\" class='ftx-05 edit-invoice' invoice-code='"+list.inv_id+"'>编辑</a>";
    str+= "<a href=\"javascript:void(0);\" class='ftx-05 del-invoice' invoice-code='"+list.inv_id+"'>删除</a>";
    str+= "</div></li>";
    $("ul[name='invoice-item-list']").append(str);
    close_inv();
}
</script>