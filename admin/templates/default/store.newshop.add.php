<?php ?>
<script type="text/javascript">

    function selectCity(name){
        if($('#'+name).attr('checked')) {
            $('.'+name).attr('checked',true);
        }else {
            $('.'+name).attr('checked',false);
        }
    }
    function create_store_tab(obj){
        if($(obj).val()==='0'){//隐藏店铺输入框
            $("#store_tr_title").hide();
            $("#store_tr_input").hide();
            $("#seller_name_title").hide();
            $("#seller_name_input").hide();
           // if('store_name' in rulesTmp ) {
                $("#store_name").rules("remove");
            $("#seller_name").rules("remove");
            //}
        }else{//显示店铺输入框
            $("#store_tr_title").show();
            $("#store_tr_input").show();
            $("#seller_name_title").show();
            $("#seller_name_input").show();
            $("#store_name").rules("add", {
                required : true,
                remote : '<?php echo urlAdmin('ownshop', 'ckeck_store_name')?>',
                messages:{
                    required: '请输入店铺名称',
                    remote : '店铺名称已存在'
                }
            });
            $("#seller_name").rules("add", {
                required : true,
                minlength : 3,
                maxlength : 15,
                remote   : {
                    url : 'index.php?act=ownshop&op=check_seller_name',
                    type: 'get',
                    data:{
                        seller_name : function(){
                            return $('#seller_name').val();
                        }
                    }
                },
                messages: {
                    required : '请输入供应商卖家账号',
                    minlength : '供应商卖家账号最短为3位',
                    maxlength : '供应商卖家账号最长为15位',
                    remote   : '此名称已被占用，请重新输入'
                },
            });
        }

    }
</script>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3>新增外驻供应商</h3>
      <ul class="tab-base">
        <li><a <?php if($_GET['op'] == "store_joinin2"){?> href="JavaScript:void(0);" class="current" <?php }else{?>  href="index.php?act=store&op=store_joinin2" <?php } ?> ><span>认证申请审核</span></a></li>
        <li><a <?php if($_GET['op'] == "store_joinin"){?> href="JavaScript:void(0);" class="current" <?php }else{?>  href="index.php?act=store&op=store_joinin" <?php } ?> ><span><?php echo $lang['pending'];?>审核</span></a></li>
        <li><a href="index.php?act=store_edit&op=index" ><span>修改供应商审核</span></a></li>
        <!--<li><a href="index.php?act=store&op=reopen_list" ><span>续签申请</span></a></li>-->
<!--        <li><a href="index.php?act=store&op=store_bind_class_applay_list" ><span>经营类目申请</span></a></li>-->
        <li><a href="JavaScript:void(0);" class="current"><span>新增供应商</span></a></li>
          <li><a href="index.php?act=store&op=newtemporary_add" ><span>新增临时供应商</span></a></li>
          <li><a href="index.php?act=store&op=type_level_list" ><span>供应商类型级别修改</span></a></li>
          <li><a href="index.php?act=store&op=store_type_edit" ><span>供应商店铺类型修改</span></a></li>
          <li><a href="index.php?act=store&op=store_push_list" ><span>手动推送合同</span></a></li>         
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th colspan="12"><div class="title">
            <h5><?php echo $lang['nc_prompts'];?></h5>
            <span class="arrow"></span></div>
        </th>
      </tr>
      <tr>
        <td><ul>
            <li>平台可以在此处添加外驻供应商，新增的外驻供应商默认为开启状态</li>
            <li>新增外驻供应商默认绑定所有经营类目并且佣金为0，可以手动设置绑定其经营类目</li>
            <li>新增外驻供应商将自动创建供应商会员账号（用于登录网站会员中心）以及商家账号（用于登录商家中心）</li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <form id="store_form" name="store_form" method="post" acrion="index.php?act=store&op=newshop_add">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="store_id" value="<?php echo $output['store_array']['store_id']; ?>" />
    <table class="table tb-type2">
      <tbody>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="is_create_store">是否同时创建商店:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input type="radio" value="0" name="is_create_store" checked="true" onclick="create_store_tab(this);"/>否</td>
            <td class="vatop tips"><input type="radio" value="1" name="is_create_store"  onclick="create_store_tab(this);"/>是</td>
        </tr>

        <tr>
            <td colspan="2" class="required"><label class="validation" for="company_name">供应商名称:</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input type="text" value="" id="company_name" name="company_name" class="txt" /></td>
            <td class="vatop tips"></td>
        </tr>
        <tr>
            <td colspan="2" class="required"><label class="validation" for="supply_code">营业执照或组织机构代码</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input type="text" value="" id="supply_code" name="supply_code" class="txt" /></td>
            <td class="vatop tips"></td>
        </tr>

        <tr  id="store_tr_title"　 class="noborder" style="display: none">
            <td colspan="2" class="required"><label class="validation" for="store_name">店铺名称:</label></td>
        </tr>
        <tr id="store_tr_input" class="noborder" style="display: none">
            <td class="vatop rowform"><input type="text" value="" id="store_name" name="store_name" class="txt" /></td>
            <td class="vatop tips"></td>
        </tr>
        <tr>
            <td colspan="2" class="required"><label class="validation" for="supply_type">供应商类型:</label></td>
        </tr>
        <tr class="noborder">
            <td>
                <select name="supply_type">
                    <option value=""><?php echo $lang['nc_please_choose'];?>...</option>
                    <?php if(!empty($output['supply_type_list']) && is_array($output['supply_type_list'])){ ?>
                        <?php foreach($output['supply_type_list'] as $k => $v){ ?>
                            <option value="<?php echo $k;?>" <?php if($_GET['supply_type'] == $k){?>selected<?php }?>><?php echo $v;?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2"><table class="table tb-type2 nomargin">
                    <thead>
                    <tr class="noborder">
                        <td class="required" ><label class="validation" for="cityArray">选择要认证的中心城市</label></td>
                        <td > <input id="citycentre" id="limitAll" value="1" type="checkbox" onclick="selectCity('citycentre')">&nbsp;&nbsp;全选</td>
                        <td class="vatop tips"></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($output['twoDimencityList']) && is_array($output['twoDimencityList'])){ ?>
                        <?php foreach((array)$output['twoDimencityList'] as $k001 => $cityList) { ?>
                            <tr class="noborder">
                                <?php foreach((array)$cityList as $k002 => $v) { ?>
                                    <td>
                                        <label style="width:100px"><?php echo (!empty($v['nav'])) ? $v['nav'] : '&nbsp;'; ?></label>
                                        <input id="city<?php echo $v['id'];?>" class="citycentre" checked  type="checkbox" name="cityArray[]" value="<?php echo $v['id'];?>" >
                                        <label for="city<?php echo $v['id'];?>"><b><?php echo $v['city_name'];?></b>&nbsp;&nbsp;</label>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="member_name">供应商账号:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="" id="member_name" name="member_name" class="txt" /></td>
          <td class="vatop tips">用于登录会员中心</td>
        </tr>
        <tr id="seller_name_title" style="display: none">
          <td colspan="2" class="required"><label class="validation" for="seller_name">供应商卖家账号:</label></td>
        </tr>
        <tr id="seller_name_input" class="noborder" style="display: none">
          <td class="vatop rowform"><input type="text" value="" id="seller_name" name="seller_name" class="txt" /></td>
          <td class="vatop tips">用于登录商家中心，可与供应商账号不同</td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="member_passwd">登录密码:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="password" value="" id="member_passwd" name="member_passwd" class="txt" /></td>
          <td class="vatop tips"></td>
        </tr>

      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="15"><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo $lang['nc_submit'];?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script type="text/javascript">
$(function(){

    var rulesTmp = {
        member_name: {
            required : true,
            minlength : 3,
            maxlength : 15,
            remote   : {
                url : 'index.php?act=ownshop&op=check_member_name',
                type: 'get',
                data:{
                    member_name : function(){
                        return $('#member_name').val();
                    }
                }
            }
        },
        /*seller_name: {
            required : true,
            minlength : 3,
            maxlength : 15,
            remote   : {
                url : 'index.php?act=ownshop&op=check_seller_name',
                type: 'get',
                data:{
                    seller_name : function(){
                        return $('#seller_name').val();
                    }
                }
            }
        },*/
        member_passwd : {
            required : true,
            minlength: 6
        },
        supply_type : {
            required : true,
        },
        company_name : {
            required : true,
        },
        supply_code : {
            required : true,
        },
        cityArray : {
            required : true,
        }
    };
    var messagesTmp = {
        member_name: {
            required : '请输入供应商账号',
            minlength : '供应商账号最短为3位',
            maxlength : '供应商账号最长为15位',
            remote   : '此名称已被占用，请重新输入'
        },
       /* seller_name: {
            required : '请输入供应商卖家账号',
            minlength : '供应商卖家账号最短为3位',
            maxlength : '供应商卖家账号最长为15位',
            remote   : '此名称已被占用，请重新输入'
        },*/
        member_passwd : {
            required : '请输入登录密码',
            minlength: '登录密码长度不能小于6'
        },
        supply_type : {
            required : '请选择供应商类型',
        },
        company_name : {
            required : '请输入供应商名称',
        },
        supply_code : {
            required : '请输入供应商营业执照或组织机构代码',
        },
        cityArray : {
            required : '请选择要认证的城市',
        }
    };
    //按钮先执行验证再提交表单
    $("#submitBtn").click(function(){
        if($("#store_form").valid()){
            //$("#store_form").submit();
            document.store_form.submit();
        }
    });
    $('#store_form').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.parentsUntil('tr').parent().prev().find('td:first'));
        },
        rules : rulesTmp,
        messages : messagesTmp
    });


});
</script>
