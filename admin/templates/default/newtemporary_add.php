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
            $("#store_name").rules("remove");
            $("#seller_name").rules("remove");

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
      <h3>新增临时供应商</h3>
      <ul class="tab-base">
        <li><a <?php if($_GET['op'] == "store_joinin2"){?> href="JavaScript:void(0);" class="current" <?php }else{?>  href="index.php?act=store&op=store_joinin2" <?php } ?> ><span>认证申请审核</span></a></li>
        <li><a <?php if($_GET['op'] == "store_joinin"){?> href="JavaScript:void(0);" class="current" <?php }else{?>  href="index.php?act=store&op=store_joinin" <?php } ?> ><span><?php echo $lang['pending'];?>审核</span></a></li>
        <li><a href="index.php?act=store_edit&op=index" ><span>修改供应商审核</span></a></li>
        <li><a href="index.php?act=store&op=newshop_add" ><span>新增供应商</span></a></li>
        <li><a <?php if($_GET['op'] == "newtemporary_add"){?> href="JavaScript:void(0);" class="current" <?php }else{?>   href="index.php?act=store&op=newtemporary_add" <?php } ?> ><span>新增临时供应商</span></a></li> 
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
            <li>用于紧急采购、零星采购和线下网络采购</li>
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
            <td class="vatop rowform"><input type="text" value="<?php echo $output['supply_code']; ?>" id="supply_code" name="supply_code" class="txt" /></td>
            <td class="vatop tips">可使用随机生成或自行填写</td>
        </tr>

        <tr>
            <td colspan="2" class="required"><label class="validation" for="contacts_name">联系人姓名</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input type="text" value="" id="contacts_name" name="contacts_name" class="txt" /></td>
            <td class="vatop tips"></td>
        </tr> 
        <tr>
            <td colspan="2" class="required"><label class="validation" for="contacts_phone">联系人手机</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input type="text" value="" id="contacts_phone" name="contacts_phone" class="txt" /></td>
            <td class="vatop tips"></td>
        </tr> 

          <tr>
            <td colspan="2" class="required"><label  for="contacts_email">电子邮箱</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input type="text" value="" id="contacts_email" name="contacts_email" class="txt" /></td>
            <td class="vatop tips"></td>
        </tr> 
        <tr>
            <td colspan="2" class="required"><label class="validation" for="bank_account_name">银行开户名</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input type="text" value="" id="bank_account_name" name="bank_account_name" class="txt" /></td>
            <td class="vatop tips"></td>
        </tr> 

        <tr>
            <td colspan="2" class="required"><label class="validation" for="bank_account_number">公司银行账号</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input type="text" value="" id="bank_account_number" name="bank_account_number" class="txt" /></td>
            <td class="vatop tips"></td>
        </tr> 
        
        <tr>
            <td colspan="2" class="required"><label class="validation" for="bank_name">开户银行支行名称</label></td>
        </tr>
        <tr class="noborder">
            <td class="vatop rowform"><input type="text" value="" id="bank_name" name="bank_name" class="txt" /></td>
            <td class="vatop tips"></td>
        </tr> 
        
        
        
        <tr>
            <td colspan="10" class="required"><label class="validation" for="settlement_bank_address">开户银行所在地</label></td>
        </tr>
        <tr>
            <td><input id="settlement_bank_address" name="settlement_bank_address" type="hidden" />
              <span></span></td>
        </tr>

<!--        <tr>
            <td colspan="10" class="required"><label class="validation" for="bank_address">城市公司：</label></td>
        </tr>
        
       <tr>
          <td  id="prov_2">              
             <select id="city_centre_2" name="city_centre">
                 <option>请选择</option>
                <?php foreach($output['city'] as $rows){ ?>
                 <option value="<?php echo $rows['id'];?>" <?php if($output['data_rz']['city_center'] == $rows['id']){echo "selected='selected'";}?>><?php echo $rows['city_name']?></option>
                <?php } ?>
             </select>
              <span></span>
             <label for="city_name" id="city_name2" style="display: none;" class="error">请选择城市公司</label>
           </td>
       </tr>-->
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="member_name">供应商账号:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" value="<?php echo $output['member_name']; ?>" id="member_name" name="member_name" class="txt" /></td>
          <td class="vatop tips">供应商账号请输入3-15位数字，可使用随机生成账号</td>
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
$("#settlement_bank_address").nc_region();

$(function(){

    var rulesTmp = {
        member_name: {
            digits: true ,
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
        supply_type : {
            required : true,
        },
        company_name : {
            required : true,
        },
        supply_code : {
            required : true,
        },
        contacts_email : {
            email : true,
        },
        contacts_name : {
            required : true,
        },
        contacts_phone : {
            required: true,
            isMobile: true
        },
        bank_account_name : {
            required : true,
        },
        bank_account_number : {
            required : true,
        },
        settlement_bank_address : {
            required : true,
        },
        bank_name :{
            required : true,
        }
    };
    var messagesTmp = {
        member_name: {
            digits: '必须为数字',
            required : '请输入供应商账号',
            minlength : '供应商账号最短为3位',
            maxlength : '供应商账号最长为15位',
            remote   : '此名称已被占用，请重新输入',
            
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
        contacts_email : {
           email: '请填写正确的邮箱地址',
        },
        contacts_name : {
            required : '请输入联系人姓名',
        }
        ,
        contacts_phone : {
            required : '请输入联系人手机号码',
        }
        ,
        bank_account_name : {
            required : '请输入银行开户名',
        }
        ,
        bank_account_number : {
            required : '请输入公司银行账号',
        }
        ,
        settlement_bank_address : {
            required : '请选择开户银行所在地',
        }
        ,
        bank_name : {
            required : '请填写开户银行支行名称',
        }
 
        
    };
    
     // 手机号码验证
    jQuery.validator.addMethod("isMobile", function(value, element) {
        var length = value.length;
        var mobile = /^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/;
        return this.optional(element) || (length == 11 && mobile.test(value));
    }, "请正确填写您的手机号码");
    
    //按钮先执行验证再提交表单
    $("#submitBtn").click(function(){
        if($("#store_form").valid()){
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
