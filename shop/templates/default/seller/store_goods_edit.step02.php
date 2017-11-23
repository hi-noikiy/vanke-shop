<?php ?>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ajaxContent.pack.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.iframe-transport.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.ui.widget.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/fileupload/jquery.fileupload.js" charset="utf-8"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.charCount.js"></script>
<!--[if lt IE 8]>
  <script src="<?php echo RESOURCE_SITE_URL;?>/js/json2.js"></script>
<![endif]-->
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/store_goods_add.step2.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<style type="text/css">
#fixedNavBar { filter:progid:DXImageTransform.Microsoft.gradient(enabled='true',startColorstr='#CCFFFFFF', endColorstr='#CCFFFFFF');background:rgba(255,255,255,0.8); width: 90px; margin-left: 510px; border-radius: 4px; position: fixed; z-index: 999; top: 172px; left: 50%;}
#fixedNavBar h3 { font-size: 12px; line-height: 24px; text-align: center; margin-top: 4px;}
#fixedNavBar ul { width: 80px; margin: 0 auto 5px auto;}
#fixedNavBar li { margin-top: 5px;}
#fixedNavBar li a { font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 20px; background-color: #F5F5F5; color: #999; text-align: center; display: block;  height: 20px; border-radius: 10px;}
#fixedNavBar li a:hover { color: #FFF; text-decoration: none; background-color: #27a9e3;}
</style>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>

<div class="item-publish">
  <form method="post" id="goods_form" action="<?php echo urlShop('store_goods_online', 'edit_save_goods');?>">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="commonid" value="<?php echo $output['goodscommon']['goods_commonid'];?>" />
    <input type="hidden" name="ref_url" value="<?php echo $_GET['ref_url'] ? $_GET['ref_url'] : getReferer();?>" />
    <div class="ncsc-form-goods">
      <h3 id="demo1">商品规格库存管理</h3>
      <dl nc_type="spec_dl" class="spec-bg" style="overflow: visible;">
        <dd class="spec-dd" style="width:100%">
          <table border="0" cellpadding="0" cellspacing="0" class="spec_table" style="margin:0 0">
            <thead>
              <?php if(is_array($output['specifications_check']) && !empty($output['specifications_check'])){?>
              <?php foreach ($output['specifications_check'] as $k=>$val){?>
            <th nctype="spec_name_<?php echo $val['kv'];?>"><?php echo $val['s_name'];?></th>
              <?php }?>
              <?php }?>
            <th class="w90"><span class="red">*</span>协议价(元)
                <div class="batch"><i class="icon-edit" title="批量操作"></i>
                  <div class="batch-input" style="display:none;">
                    <h6>批量设置价格：</h6>
                    <a href="javascript:void(0)" class="close">X</a>
                    <input name="" type="text" class="text price" />
                    <a href="javascript:void(0)" class="ncsc-btn-mini" data-type="marketprice">设置</a><span class="arrow"></span></div>
                </div>
              </th>
            <th class="w90"><span class="red">*</span>高级会员价(元)
                <div class="batch"><i class="icon-edit" title="批量操作"></i>
                  <div class="batch-input" style="display:none;">
                    <h6>批量设置价格：</h6>
                    <a href="javascript:void(0)" class="close">X</a>
                    <input name="" type="text" class="text price" />
                    <a href="javascript:void(0)" class="ncsc-btn-mini" data-type="price">设置</a><span class="arrow"></span></div>
                </div>
            </th>
                
            <th class="w90"><span class="red">*</span>普通会员价(元)
                <div class="batch"><i class="icon-edit" title="批量操作"></i>
                  <div class="batch-input" style="display:none;">
                    <h6>批量设置价格：</h6>
                    <a href="javascript:void(0)" class="close">X</a>
                    <input name="" type="text" class="text price" />
                    <a href="javascript:void(0)" class="ncsc-btn-mini" data-type="goods_third_price">设置</a><span class="arrow"></span></div>
                </div>
            </th>
              
                <th class="w90"><span class="red">*</span>市场价(元)
                <div class="batch"><i class="icon-edit" title="批量操作"></i>
                  <div class="batch-input" style="display:none;">
                    <h6>批量设置价格：</h6>
                    <a href="javascript:void(0)" class="close">X</a>
                    <input name="" type="text" class="text price" />
                    <a href="javascript:void(0)" class="ncsc-btn-mini" data-type="g_costprice">设置</a><span class="arrow"></span></div>
                </div>
                </th>
              <th class="w60"><span class="red">*</span><?php echo $lang['store_goods_index_stock'];?>
                <div class="batch"><i class="icon-edit" title="批量操作"></i>
                  <div class="batch-input" style="display:none;">
                    <h6>批量设置库存：</h6>
                    <a href="javascript:void(0)" class="close">X</a>
                    <input name="" type="text" class="text stock" />
                    <a href="javascript:void(0)" class="ncsc-btn-mini" data-type="stock">设置</a><span class="arrow"></span></div>
                </div></th>
              <th class="w70">预警值
                <div class="batch"><i class="icon-edit" title="批量操作"></i>
                  <div class="batch-input" style="display:none;">
                    <h6>批量设置预警值：</h6>
                    <a href="javascript:void(0)" class="close">X</a>
                    <input name="" type="text" class="text stock" />
                    <a href="javascript:void(0)" class="ncsc-btn-mini" data-type="alarm">设置</a><span class="arrow"></span></div>
                </div></th>
			  <th class="w70"><span class="red">*</span>最小购买</th>
			  <th class="w70"><span class="red">*</span>最大购买</th>

                </thead>
           <tbody id="specification_stock" nc_type="spec_table">
            <?php if(!empty($output['specifications']) && is_array($output['specifications'])){?>
            	<?php foreach ($output['specifications'] as $k_one=>$vals){?>
            	<tr id="spec_<?php echo $k_one;?>" nc_type="good_data">
            		<input type="hidden" name="spec[<?php echo $k_one;?>][good_sku]" value="<?php echo $k_one;?>" />
            		<input type="hidden" name="spec[<?php echo $k_one;?>][good_id]" value="<?php echo $output['good_data'][$k_one]['goods_id'];?>" />
            		<?php foreach ($output['specifications_check'] as $k=>$val){?>
            			<input type="hidden" name="spec[<?php echo $k_one;?>][sp_value][]" value="<?php echo $vals[$k];?>" />
            			<td><?php echo $vals[$k];?></td>
            		<?php }?>																													
            		<td align="center"><input style="width:35px;" class="text price" type="text" name="spec[<?php echo $k_one;?>][marketprice]" data_type="marketprice" nc_type="price" value="<?php echo $output['good_data'][$k_one]['goods_marketprice'];?>" onkeyup="value=value.replace(/[^\d.]/g,'')" /></td>
            		<td align="center"><input style="width:35px;" class="text price" type="text" name="spec[<?php echo $k_one;?>][price]" data_type="price" nc_type="price" value="<?php echo $output['good_data'][$k_one]['goods_price'];?>" onkeyup="value=value.replace(/[^\d.]/g,'')" /></td>
            		<td align="center"><input style="width:35px;" class="text price" type="text" name="spec[<?php echo $k_one;?>][goods_third_price]" data_type="goods_third_price" nc_type="price" value="<?php echo $output['good_data'][$k_one]['goods_third_price'];?>" onkeyup="value=value.replace(/[^\d.]/g,'')" /></td>
            		<td align="center"><input style="width:35px;" class="text price" type="text" name="spec[<?php echo $k_one;?>][cosprice]" data_type="g_costprice" nc_type="price" value="<?php echo $output['good_data'][$k_one]['g_costprice'];?>" onkeyup="value=value.replace(/[^\d.]/g,'')" /></td>
            		<td align="center"><input style="width:35px;" class="text price" type="text" name="spec[<?php echo $k_one;?>][stock]" data_type="stock" nc_type="price" value="<?php echo $output['good_data'][$k_one]['goods_storage'];?>" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')"/></td>
            		<td align="center"><input style="width:35px;" class="text price" type="text" name="spec[<?php echo $k_one;?>][alarm]" data_type="alarm" nc_type="price" value="<?php echo $output['good_data'][$k_one]['goods_storage_alarm'];?>" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')"/></td>
            		<td align="center"><input style="width:35px;" class="text price" type="text" name="spec[<?php echo $k_one;?>][min_num]" data_type="min_num" nc_type="price" value="<?php echo empty($output['good_data'][$k_one]['min_num']) ? 1:$output['good_data'][$k_one]['min_num'];?>" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')"/></td>
            		<td align="center"><input style="width:35px;" class="text price" type="text" name="spec[<?php echo $k_one;?>][max_num]" data_type="max_num" nc_type="price" value="<?php echo  empty($output['good_data'][$k_one]['max_num']) ? 9999:$output['good_data'][$k_one]['max_num'];?>" onkeyup="this.value=this.value.replace(/[^0-9]/g,'')"/></td>
            	</tr>
            <?php }}?>
            </tbody>
          </table>
          <p class="hint">点击<i class="icon-edit"></i>可批量修改所在列的值。</p>
        </dd>
      </dl>

    </div>
    <div class="bottom tc hr32">
      <label class="submit-border">
        <input type="submit" class="submit" value="提交保存" />
      </label>
    </div>
  </form>
</div>
<script type="text/javascript">
var SITEURL = "<?php echo SHOP_SITE_URL; ?>";
var DEFAULT_GOODS_IMAGE = "<?php echo thumb(array(), 60);?>";
var SHOP_RESOURCE_SITE_URL = "<?php echo SHOP_RESOURCE_SITE_URL;?>";

$(function(){

	$("#goods_form").bind("submit", function(){  
		var num_a = 0;
		var num_b = 0;
		$("[nc_type='spec_table'] tr[nc_type='good_data']").each(function(){
		var marketprice = isNaN(parseFloat($(this).find("input[data_type='marketprice']").val())) ? 0:parseFloat($(this).find("input[data_type='marketprice']").val());
        	var price = isNaN(parseFloat($(this).find("input[data_type='price']").val())) ? 0:parseFloat($(this).find("input[data_type='price']").val());
        	var goods_third_price = isNaN(parseFloat($(this).find("input[data_type='goods_third_price']").val())) ? 0:parseFloat($(this).find("input[data_type='goods_third_price']").val());
        	var cosprice = isNaN(parseFloat($(this).find("input[data_type='g_costprice']").val())) ? 0:parseFloat($(this).find("input[data_type='g_costprice']").val());
        	var min_num = isNaN(parseInt($(this).find("input[data_type='min_num']").val())) ? 0:parseInt($(this).find("input[data_type='min_num']").val());
        	var max_num = isNaN(parseInt($(this).find("input[data_type='max_num']").val())) ? 0:parseInt($(this).find("input[data_type='max_num']").val());
        	if(marketprice < price){
            	num_a++;
            	$(this).find("input[data_type='marketprice']").css('background-color','#FFAEB9');
            }else{
            	$(this).find("input[data_type='marketprice']").css('background-color','#fff');
            }
        	if(min_num > max_num){
        		num_b++;
            	$(this).find("input[data_type='min_num']").css('background-color','#FFAEB9');
            }else{
            	$(this).find("input[data_type='min_num']").css('background-color','#fff');
            }
		});
		if(num_a > 0){
			alert("协议价需高于高级会员价");
			return false;
		}
		if(num_b > 0){
			alert("最小购买数量不得小于最大购买数量");
			return false;
		}
	});
	
	//电脑端手机端tab切换
	$(".tabs").tabs();
	jQuery.validator.addMethod("checkFCodePrefix", function(value, element) {       
		return this.optional(element) || /^[a-zA-Z]+$/.test(value);       
	},'<i class="icon-exclamation-sign"></i>请填写不多于5位的英文字母');  
    $('#goods_form').validate({
        errorPlacement: function(error, element){
            $(element).nextAll('span').append(error);
        },
        <?php if ($output['edit_goods_sign']) {?>
        submitHandler:function(form){
            ajaxpost('goods_form', '', '', 'onerror');
        },
        <?php }?>
        rules : {
            g_vindate : {
                required    : function() {if ($("#is_gv_1").prop("checked")) {return true;} else {return false;}}
            },
			g_vlimit : {
				required	: function() {if ($("#is_gv_1").prop("checked")) {return true;} else {return false;}},
				range		: [1,10]
			},
			g_fccount : {
				<?php if (!$output['edit_goods_sign']) {?>required	: function() {if ($("#is_fc_1").prop("checked")) {return true;} else {return false;}},<?php }?>
				range		: [1,100]
			},
			g_fcprefix : {
				<?php if (!$output['edit_goods_sign']) {?>required	: function() {if ($("#is_fc_1").prop("checked")) {return true;} else {return false;}},<?php }?>
				checkFCodePrefix : true,
				rangelength	: [3,5]
			},
			g_saledate : {
				required	: function () {if ($('#is_appoint_1').prop("checked")) {return true;} else {return false;}}
			},
			g_deliverdate : {
				required	: function () {if ($('#is_presell_1').prop("checked")) {return true;} else {return false;}}
			}
        },
        messages : {
            g_vindate : {
                required    : '<i class="icon-exclamation-sign"></i>请选择有效期'
            },
			g_vlimit : {
				required	: '<i class="icon-exclamation-sign"></i>请填写1~10之间的数字',
				range		: '<i class="icon-exclamation-sign"></i>请填写1~10之间的数字'
			},
			g_fccount : {
				required	: '<i class="icon-exclamation-sign"></i>请填写1~100之间的数字',
				range		: '<i class="icon-exclamation-sign"></i>请填写1~100之间的数字'
			},
			g_fcprefix : {
				required	: '<i class="icon-exclamation-sign"></i>请填写3~5位的英文字母',
				rangelength	: '<i class="icon-exclamation-sign"></i>请填写3~5位的英文字母'
			},
			g_saledate : {
				required	: '<i class="icon-exclamation-sign"></i>请选择有效期'
			},
			g_deliverdate : {
				required	: '<i class="icon-exclamation-sign"></i>请选择有效期'
			}
        }
    });
    <?php if (isset($output['goods'])) {?>
	setTimeout("setArea(<?php echo $output['goods']['areaid_1'];?>, <?php echo $output['goods']['areaid_2'];?>)", 1000);
	<?php }?>
	
});
// 按规格存储规格值数据
var spec_group_checked = [<?php for ($i=0; $i<$output['sign_i']; $i++){if($i+1 == $output['sign_i']){echo "''";}else{echo "'',";}}?>];
var str = '';
var V = new Array();

<?php for ($i=0; $i<$output['sign_i']; $i++){?>
var spec_group_checked_<?php echo $i;?> = new Array();
<?php }?>

$(function(){
	$('dl[nctype="spec_group_dl"]').on('click', 'span[nctype="input_checkbox"] > input[type="checkbox"]',function(){
		into_array();
		goods_stock_set();
	});


	$("#city_center_data tr td input[type='checkbox']").bind("click", function () {
		 var obj = $(this);
		 var val = $(this).val();
		 if(val == '1'){
			 $("#city_center_data tr td input[type='checkbox']").not(".city_conters").each(function () {
  			 if(obj.is(":checked")){
  				 $(this).attr("checked", false);
      			 $(this).attr('disabled',true);
  		     }else{
      			 $(this).attr('disabled',false);
  			 }
			 });
	     }
	     var city_id = new Array(); 
		 $("#city_center_data tr td input[type='checkbox']:checked").each(function () {
			 city_id.push($(this).val()); 
		 });
		 $("#city_center_id").val(city_id.join(','));
 });

	// 提交后不没有填写的价格或库存的库存配置设为默认价格和0
	// 库存配置隐藏式 里面的input加上disable属性
	$('input[type="submit"]').click(function(){
		$('input[data_type="stock"]').each(function(){
			if($(this).val() == ''){
				$(this).val('0');
			}
		});
		$('input[data_type="alarm"]').each(function(){
			if($(this).val() == ''){
				$(this).val('0');
			}
		});
		if($('dl[nc_type="spec_dl"]').css('display') == 'none'){
			$('dl[nc_type="spec_dl"]').find('input').attr('disabled','disabled');
		}
	});
	
});

// 将选中的规格放入数组
function into_array(){
<?php for ($i=0; $i<$output['sign_i']; $i++){?>
		
		spec_group_checked_<?php echo $i;?> = new Array();
		$('dl[nc_type="spec_group_dl_<?php echo $i;?>"]').find('input[type="checkbox"]:checked').each(function(){
			i = $(this).attr('nc_type');
			v = $(this).val();
			c = null;
			if ($(this).parents('dl:first').attr('spec_img') == 't') {
				c = 1;
			}
			spec_group_checked_<?php echo $i;?>[spec_group_checked_<?php echo $i;?>.length] = [v,i,c];
		});

		spec_group_checked[<?php echo $i;?>] = spec_group_checked_<?php echo $i;?>;

<?php }?>
}

// 生成库存配置
function goods_stock_set(){
    //  店铺价格 商品库存改为只读
    $('dl[nc_type="spec_dl"]').show();
    str = '<tr>';
    <?php recursionSpec(0,$output['sign_i']);?>
    if(str == '<tr>'){
        $('dl[nc_type="spec_dl"]').hide();
    }else{
        $('tbody[nc_type="spec_table"]').empty().html(str)
            .find('input[nc_type]').each(function(){
                s = $(this).attr('nc_type');
                try{$(this).val(V[s]);}catch(ex){$(this).val('');};
                if ($(this).attr('data_type') == 'stock' && $(this).val() == ''){
                    $(this).val('0');
                }
                if ($(this).attr('data_type') == 'alarm' && $(this).val() == ''){
                    $(this).val('0');
                }
            }).end()
            .find('input[data_type="stock"]').change(function(){
                computeStock();    // 库存计算
            }).end()
            .find('input[data_type="price"]').change(function(){
                computePrice();     // 价格计算
            }).end()
            .find('input[nc_type]').change(function(){
                s = $(this).attr('nc_type');
                V[s] = $(this).val();
            });
            
            $('input[name="g_storage"]').attr('readonly','readonly').css('background','#E7E7E7 none');
    }
}

<?php 
/**
 * 
 * 
 *  生成需要的js循环。递归调用	PHP
 * 
 *  形式参考 （ 2个规格）
 *  $('input[type="checkbox"]').click(function(){
 *      str = '';
 *      for (var i=0; i<spec_group_checked[0].length; i++ ){
 *      td_1 = spec_group_checked[0][i];
 *          for (var j=0; j<spec_group_checked[1].length; j++){
 *              td_2 = spec_group_checked[1][j];
 *              str += '<tr><td>'+td_1[0]+'</td><td>'+td_2[0]+'</td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td>';
 *          }
 *      }
 *      $('table[class="spec_table"] > tbody').empty().html(str);
 *  });
 */

function recursionSpec($len,$sign) {
    if($len < $sign){
        echo "for (var i_".$len."=0; i_".$len."<spec_group_checked[".$len."].length; i_".$len."++){td_".(intval($len)+1)." = spec_group_checked[".$len."][i_".$len."];\n";
        $len++;
        recursionSpec($len,$sign);
    }else{
        echo "var tmp_spec_td = new Array();\n";
        for($i=0; $i< $len; $i++){
            echo "tmp_spec_td[".($i)."] = td_".($i+1)."[1];\n";
        }
        echo "tmp_spec_td.sort(function(a,b){return a-b});\n";
        echo "var spec_bunch = 'i_';\n";
        for($i=0; $i< $len; $i++){
            echo "spec_bunch += tmp_spec_td[".($i)."];\n";
        }
        echo "str += '<input type=\"hidden\" name=\"spec['+spec_bunch+'][goods_id]\" nc_type=\"'+spec_bunch+'|id\" value=\"\" />';";
        for($i=0; $i< $len; $i++){
            echo "if (td_".($i+1)."[2] != null) { str += '<input type=\"hidden\" name=\"spec['+spec_bunch+'][color]\" value=\"'+td_".($i+1)."[1]+'\" />';}";
            echo "str +='<td><input type=\"hidden\" name=\"spec['+spec_bunch+'][sp_value]['+td_".($i+1)."[1]+']\" value=\"'+td_".($i+1)."[0]+'\" />'+td_".($i+1)."[0]+'</td>';\n";
        }
                echo "str +='"
                  . "<td><input class=\"text price\" type=\"text\" name=\"spec['+spec_bunch+'][marketprice]\" data_type=\"marketprice\" nc_type=\"'+spec_bunch+'|marketprice\" value=\"\" /><!--em class=\"add-on\"><i class=\"icon-renminbi\"></i></em--></td>"
                  . "<td><input class=\"text price\" type=\"text\" name=\"spec['+spec_bunch+'][price]\" data_type=\"price\" nc_type=\"'+spec_bunch+'|price\" value=\"\" /><!--em class=\"add-on\"><i class=\"icon-renminbi\"></i></em--></td>"
                  . "<td><input class=\"text price\" type=\"text\" name=\"spec['+spec_bunch+'][goods_third_price]\" data_type=\"goods_third_price\" nc_type=\"'+spec_bunch+'|goods_third_price\" value=\"\" /><!--em class=\"add-on\"><i class=\"icon-renminbi\"></i></em--></td>"
                  . "<td><input class=\"text price\" type=\"text\" name=\"spec['+spec_bunch+'][g_cosprice]\" data_type=\"g_costprice\" nc_type=\"'+spec_bunch+'|g_costprice\" value=\"\" /><!--em class=\"add-on\"><i class=\"icon-renminbi\"></i></em--></td>"
                  . "<td><input class=\"text stock\" type=\"text\" name=\"spec['+spec_bunch+'][stock]\" data_type=\"stock\" nc_type=\"'+spec_bunch+'|stock\" value=\"\" /></td>"
                  . "<td><input class=\"text stock\" type=\"text\" name=\"spec['+spec_bunch+'][alarm]\" data_type=\"alarm\" nc_type=\"'+spec_bunch+'|alarm\" value=\"\" /></td>"
                  . "<td><input class=\"text min_num\" type=\"text\" name=\"spec['+spec_bunch+'][min_num]\" data_type=\"min_num\" nc_type=\"'+spec_bunch+'|min_num\" value=\"\" /></td>"
                  . "<td><input class=\"text max_num\" type=\"text\" name=\"spec['+spec_bunch+'][max_num]\" data_type=\"max_num\" nc_type=\"'+spec_bunch+'|max_num\" value=\"\" /></td></td></tr>';\n";
        for($i=0; $i< $len; $i++){
            echo "}\n";
        }
    }
}

?>


<?php if (!empty($output['goods']) && $_GET['class_id'] <= 0 && !empty($output['sp_value']) && !empty($output['spec_checked']) && !empty($output['spec_list'])){?>
//  编辑商品时处理JS
$(function(){
	var E_SP = new Array();
	var E_SPV = new Array();
	<?php
	$string = '';
	foreach ($output['spec_checked'] as $v) {
		$string .= "E_SP[".$v['id']."] = '".$v['name']."';";
	}
	echo $string;
	echo "\n";
	$string = '';
	foreach ($output['sp_value'] as $k=>$v) {
		$string .= "E_SPV['{$k}'] = '{$v}';";
	}
	echo $string;
	?>
	V = E_SPV;
	$('dl[nc_type="spec_dl"]').show();
	$('dl[nctype="spec_group_dl"]').find('input[type="checkbox"]').each(function(){
		//  店铺价格 商品库存改为只读
		$('input[name="g_storage"]').attr('readonly','readonly').css('background','#E7E7E7 none');
		s = $(this).attr('nc_type');
		if (!(typeof(E_SP[s]) == 'undefined')){
			$(this).attr('checked',true);
			v = $(this).parents('li').find('span[nctype="pv_name"]');
			if(E_SP[s] != ''){
				$(this).val(E_SP[s]);
				v.html('<input type="text" maxlength="20" value="'+E_SP[s]+'" />');
			}else{
				v.html('<input type="text" maxlength="20" value="'+v.html()+'" />');
			}
			change_img_name($(this));			// 修改相关的颜色名称
		}
	});

    into_array();	// 将选中的规格放入数组
    str = '<tr>';
    <?php recursionSpec(0,$output['sign_i']);?>
    if(str == '<tr>'){
        $('dl[nc_type="spec_dl"]').hide();
    }else{
        $('tbody[nc_type="spec_table"]').empty().html(str)
            .find('input[nc_type]').each(function(){
                s = $(this).attr('nc_type');
                try{$(this).val(E_SPV[s]);}catch(ex){$(this).val('');};
            }).end()
            .find('input[data_type="stock"]').change(function(){
                computeStock();    // 库存计算
            }).end()
            .find('input[data_type="price"]').change(function(){
                computePrice();     // 价格计算
            }).end()
            .find('input[type="text"]').change(function(){
                s = $(this).attr('nc_type');
                V[s] = $(this).val();
            });
    }
});
<?php }?>
</script> 
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/scrolld.js"></script>
<script type="text/javascript">$("[id*='Btn']").stop(true).on('click', function (e) {e.preventDefault();$(this).scrolld();})</script>
