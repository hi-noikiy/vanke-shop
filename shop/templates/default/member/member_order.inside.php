<?php ?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />

<div class="wrap">
  <div class="tabmenu">
    <?php include template('layout/inordermenu');?>
  </div>
  <form method="get" action="index.php" target="_self">
    <table class="ncm-search-table">
      <input type="hidden" name="act" value="member_inorder" />
	  <input type="hidden" name="op" value="inside_order" />
      <tr>
        <td>&nbsp;</td>
        <th><?php echo $lang['member_order_state'];?></th>
        <td class="w100"><select name="state_type">
            <option value="" <?php echo $_GET['state_type']==''?'selected':''; ?>><?php echo $lang['member_order_all'];?></option>
            <option value="<?php echo ORDER_STATUS_SEND_ONE;?>" <?php echo $_GET['state_type']==ORDER_STATUS_SEND_ONE ?'selected':''; ?>><?php echo $lang['order_audit'];?></option>
            <option value="<?php echo ORDER_STATUS_SEND_TWO;?>" <?php echo $_GET['state_type']==ORDER_STATUS_SEND_TWO ?'selected':''; ?>><?php echo $lang['order_pending_audit'];?></option>
            <option value="<?php echo ORDER_STATUS_SUCCESS;?>" <?php echo $_GET['state_type']==ORDER_STATUS_SUCCESS ?'selected':''; ?>><?php echo $lang['acceptance_audit'];?></option>
            <option value="<?php echo ORDER_STATUS_ERROR;?>" <?php echo $_GET['state_type']==ORDER_STATUS_ERROR ?'selected':''; ?>><?php echo $lang['order_audit_return'];?></option>
            <option value="<?php echo ORDER_STATUS_OUT;?>" <?php echo $_GET['state_type']==ORDER_STATUS_OUT ?'selected':''; ?>><?php echo $lang['order_audit_reject'];?></option>
            <option value="<?php echo ORDER_WAIT_CANCEL;?>" <?php echo $_GET['state_type']==ORDER_WAIT_CANCEL ?'selected':''; ?>><?php echo $lang['order_wait_cancel'];?></option>
            <option value="<?php echo ORDER_STATUS_CUS_RECEIVED;?>" <?php echo $_GET['state_type']==ORDER_STATUS_CUS_RECEIVED ?'selected':''; ?>><?php echo $lang['order_seized'];?></option>
            <option value="<?php echo ORDER_STATUS_SEND_HET;?>" <?php echo $_GET['state_type']==ORDER_STATUS_SEND_HET ?'selected':''; ?>><?php echo $lang['order_pending_receipt'];?></option>
            <option value="<?php echo ORDER_STATE_SEND;?>" <?php echo $_GET['state_type']==ORDER_STATE_SEND ?'selected':''; ?>><?php echo $lang['order_state_send'];?></option>
            <option value="<?php echo ORDER_STATUS_RECEIVED;?>" <?php echo $_GET['state_type']==ORDER_STATUS_RECEIVED ?'selected':''; ?>><?php echo $lang['order_state_success'];?></option>
            <option value="<?php echo ORDER_STATE_CANCEL;?>" <?php if(isset($_GET['state_type']) && $_GET['state_type']!= '' ) {echo $_GET['state_type']==ORDER_STATE_CANCEL ?'selected':'';} ?>><?php echo $lang['order_state_cancel'];?></option>
          </select></td>
        <th><?php echo $lang['member_order_time'];?></th>
        <td class="w240"><input type="text" class="text w70" name="query_start_date" id="query_start_date" value="<?php echo $_GET['query_start_date']; ?>"/><label class="add-on"><i class="icon-calendar"></i></label>&nbsp;&#8211;&nbsp;<input type="text" class="text w70" name="query_end_date" id="query_end_date" value="<?php echo $_GET['query_end_date']; ?>"/><label class="add-on"><i class="icon-calendar"></i></label></td>
        <th><?php echo $lang['member_order_sn'];?></th>
        <td class="w160"><input type="text" class="text w150" name="order_sn" value="<?php echo $_GET['order_sn']; ?>"></td>
        <td class="w70 tc"><label class="submit-border">
            <input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>"/>
          </label></td>
      </tr>
    </table>
  </form>
  <table class="ncm-default-table order">
    <thead>
      <tr>
        <th class="w10"></th>
        <th colspan="2">商品</th>
        <th class="w100">单价（元）</th>
        <th class="w40">数量</th>
        <th class="w100">售后</th>
        <th class="w120">订单金额</th>
        <th class="w100">交易状态</th>
        <th class="w150">交易操作</th>
      </tr>
    </thead>
    <?php if ($output['order_group_list']) { ?>
    <?php foreach ($output['order_group_list'] as $order_pay_sn => $group_info) { ?>
    <?php $p = 0;?>
    <tbody order_id="" <?php if (!empty($group_info['pay_amount']) && $p == 0) {?> class="pay" <?php }?>>
      <?php foreach($group_info['order_list'] as $order_id => $order_info) {?>
      <?php if (empty($group_info['pay_amount'])) {?>
      <tr>
        <td colspan="19" class="sep-row"></td>
      </tr>
      <?php }?>
      <?php if (!empty($group_info['pay_amount']) && $p == 0) {?>
      <tr>
        <td colspan="19" class="sep-row"></td>
      </tr>
      <tr>
        <td colspan="19" class="pay-td"><span class="ml15">在线支付金额：<em>￥<?php echo ncPriceFormat($group_info['pay_amount']);?></em></span> <a class="ncm-btn ncm-btn-orange fr mr15" href="index.php?act=buy&op=pay&pay_sn=<?php echo $order_pay_sn; ?>"><i class="icon-shield"></i>订单支付</a></td>
      </tr>
      <?php }?>
      <?php $p++;?>
      <tr>
        <th colspan="19"> <span class="ml10">
          <!-- order_sn -->
          <?php echo $lang['member_order_sn'].$lang['nc_colon'];?>
          <?php if($order_info['order_num_sequence']){echo $order_info['mother_orderid'].'-'.$order_info['order_num_sequence'];}else{?>
          <?php echo $order_info['order_sn']; }?>
          <?php if ($order_info['order_from'] == 2){?>
          <i class="icon-mobile-phone"></i>
          <?php }?>
          </span>
          <!-- order_time -->
          <span><?php echo $lang['member_order_time'].$lang['nc_colon'];?><?php echo date("Y-m-d H:i:s",$order_info['add_time']); ?></span>

          <!-- store_name -->
          <span><a href="<?php echo urlShop('show_store','index',array('store_id'=>$order_info['store_id']), $order_info['extend_store']['store_domain']);?>" title="<?php echo $order_info['store_name'];?>"><?php echo $order_info['store_name']; ?></a></span>

          <!-- QQ -->
          <span member_id="<?php echo $order_info['extend_store']['member_id'];?>">
          <?php if(!empty($order_info['extend_store']['store_qq'])){?>
          <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $order_info['extend_store']['store_qq'];?>&site=qq&menu=yes" title="QQ: <?php echo $order_info['extend_store']['store_qq'];?>"><img border="0" src="http://wpa.qq.com/pa?p=2:<?php echo $order_info['extend_store']['store_qq'];?>:52" style=" vertical-align: middle;"/></a>
          <?php }?>

          <!-- wang wang -->
          <?php if(!empty($order_info['extend_store']['store_ww'])){?>
          <a target="_blank" href="http://amos.im.alisoft.com/msg.aw?v=2&uid=<?php echo $order_info['extend_store']['store_ww'];?>&site=cntaobao&s=2&charset=<?php echo CHARSET;?>"  class="vm" ><img border="0" src="http://amos.im.alisoft.com/online.aw?v=2&uid=<?php echo $order_info['extend_store']['store_ww'];?>&site=cntaobao&s=2&charset=<?php echo CHARSET;?>" alt="Wang Wang"  style=" vertical-align: middle;"/></a>
          <?php }?>
          </span> <a href="javascript:void(0)" class="share-goods" nc_type="sharegoods" data-param='{"gid":"<?php echo $order_info['goods_list'][0]['goods_id'];?>"}'><i class="icon-share"></i><?php echo $lang['member_order_snsshare'];?></a>

          <!-- 放入回收站 -->

          <?php if ($order_info['if_delete']) { ?>
          <a href="javascript:void(0);" class="order-trash" onclick="ajax_get_confirm('您确定要删除吗?删除后该订单可以在回收站找回，或彻底删除', 'index.php?act=member_order&op=change_state&state_type=order_delete&order_id=<?php echo $order_info['order_id']; ?>');"><i class="icon-trash"></i>删除</a>
          <?php } ?>

          <!-- 还原订单 -->

          <?php if ($order_info['if_restore']) { ?>
          <a href="javascript:void(0);" class="order-trash" onclick="ajax_get_confirm('您确定要还原吗?', 'index.php?act=member_order&op=change_state&state_type=order_restore&order_id=<?php echo $order_info['order_id']; ?>');"><i class="icon-refresh"></i>还原</a>
          <?php } ?>
        </th>
      </tr>

      <!-- S 商品列表 -->
      <?php $i = 0;?>
      <?php if(is_array($order_info['goods_list'])){?>
      <?php foreach ($order_info['goods_list'] as $k => $goods_info) {?>
      <?php $i++;?>
      <tr>
        <td class="bdl"></td>
        <td class="w70"><div class="ncm-goods-thumb"><a href="<?php echo $goods_info['goods_url'];?>" target="_blank"><img src="<?php echo $goods_info['image_60_url'];?>" onMouseOver="toolTip('<img src=<?php echo $goods_info['image_240_url'];?>>')" onMouseOut="toolTip()"/></a></div></td>
        <td class="tl"><dl class="goods-name">
            <dt><a href="<?php echo $goods_info['goods_url'];?>" title='<?php echo $goods_info['goods_name']; ?>' target="_blank"><?php echo $goods_info['goods_name']; ?></a></dt>
            <?php if (!empty($goods_info['goods_type_cn'])) { ?>
            <dd><span class="sale-type"><?php echo $goods_info['goods_type_cn'];?></span></dd>
            <?php } ?>
          </dl></td>
        <td><?php echo $goods_info['goods_price'];?></td>
        <td><?php echo $goods_info['goods_num']; ?></td>
        <td><!-- 退款 -->

          
              <?php if ($order_info['check_back'] && $goods_info['refund'] == 1){?>
            <p><a href="index.php?act=member_inrefund&op=add_refund&order_id=<?php echo $order_info['order_id']; ?>&goods_id=<?php echo $goods_info['rec_id']; ?>">退款/退货</a></p>
            <?php }?>

          <?php if ($order_info['if_complain']){ ?>
          <p><a href="index.php?act=member_complain&op=complain_new&order_id=<?php echo $order_info['order_id']; ?>&goods_id=<?php echo $goods_info['rec_id']; ?>">交易投诉</a></p>
          <?php } ?></td>

        <!-- S 合并TD -->
        <?php if (($order_info['goods_count'] > 1 && $k ==0) || ($order_info['goods_count'] == 1)){?>
        <td class="bdl" rowspan="<?php echo $order_info['goods_count'];?>"><p class=""><strong><?php echo $order_info['order_amount']; ?></strong></p>
          <p class="goods-freight">
            <?php if ($order_info['shipping_fee'] > 0){?>
            (<?php echo $lang['member_order_shipping_han'];?>运费<?php echo $order_info['shipping_fee'];?>)
            <?php }else{?>
            <?php echo $lang['nc_common_shipping_free'];?>
            <?php }?>
          </p>
          <p title="<?php echo $lang['member_order_pay_method'].$lang['nc_colon'];?><?php echo $order_info['payment_name']; ?>"><?php echo $order_info['payment_name']; ?></p></td>
        <td class="bdl" rowspan="<?php echo $order_info['goods_count'];?>">
        <p><?php echo $order_info['state_desc']; ?> 
        	<?php echo $order_info['evaluation_status'] ? $lang['member_order_evaluated'] : '';?></p>

          <!-- 订单查看 -->

          <p><a href="index.php?act=member_inorder&op=show_order&order_id=<?php echo $order_info['order_id']; ?>" target="_blank"><?php echo $lang['member_order_view_order'];?></a></p>

          
        <td class="bdl bdr" rowspan="<?php echo $order_info['goods_count'];?>"><!-- 永久删除 -->


          <!-- 取消订单 -->
            <?php if(in_array($order_info['order_state'],array(ORDER_WAIT_CANCEL,ORDER_STATUS_SEND_ONE,ORDER_STATUS_SEND_TWO,ORDER_DELIVER_GOODS,ORDER_STATUS_SUCCESS,ORDER_STATUS_ERROR,ORDER_STATUS_OUT,ORDER_STATUS_CUS_RECEIVED,ORDER_STATUS_SEND_HET))){?>
          <p><!-- 订单状态 -->
          <?php 
          
          if($order_info['order_state'] == ORDER_STATUS_SEND_ONE){
			  echo $lang['order_audit'];
                          echo '<br/><a href="javascript:void();" lang="'.$order_info['order_sn'].'" class="ncm-btn ncm-btn-green ajax_post_state" >发起审核</a>';
          }elseif($order_info['order_state'] == ORDER_STATUS_SEND_TWO){
			  echo $lang['order_pending_audit'];
		  }elseif($order_info['order_state'] == ORDER_WAIT_CANCEL){
			  echo $lang['order_wait_cancel'];
		  }elseif($order_info['order_state'] == ORDER_STATUS_SUCCESS){
			  echo $lang['acceptance_audit'];
		  }elseif($order_info['order_state'] == ORDER_DELIVER_GOODS){
		      echo $lang['deliver_audit'];
		  }elseif($order_info['order_state'] == ORDER_STATUS_ERROR){
			  echo $lang['order_audit_return'];
                  }elseif($order_info['order_state'] == ORDER_STATUS_OUT){
                          echo $lang['order_audit_reject'];
                  }elseif($order_info['order_state'] == ORDER_STATUS_CUS_RECEIVED){
                      if ($order_info['if_lock']) {
                          echo $lang['order_back'];
                      }else{
                          echo $lang['order_check'];
                      }
                          
                  }elseif($order_info['order_state'] == ORDER_STATUS_SEND_HET){
                      echo $lang['order_wait_check'];
                  }
                  ?>
          </p>
          <?php }?>

          <!-- 订单待受理阶段取消订单 -->
          <?php if (($order_info['order_state'] == ORDER_STATUS_SUCCESS || $order_info['order_state'] == ORDER_STATUS_SEND_ONE) &&  !$order_info['if_lock']) { ?>
          <p><a href="javascript:void(0)" style="margin-top:5px;" class="ncm-btn ncm-btn-red" nc_type="dialog" dialog_width="480" dialog_title="<?php echo $lang['member_order_cancel_order'];?>" dialog_id="buyer_order_cancel_order" uri="index.php?act=member_order&op=change_state&state_type=order_cancel&order_id=<?php echo $order_info['order_id']; ?>"  id="order<?php echo $order_info['order_id']; ?>_action_cancel"> <?php echo $lang['member_order_cancel_order'];?></a></p>
          <?php } ?>
          
           <!-- 订单待发货阶段取消订单 -->
          <?php if ($order_info['order_state'] == ORDER_DELIVER_GOODS &&  !$order_info['if_lock']) { ?>
          <p><a href="javascript:void(0)" style="margin-top:5px;" class="ncm-btn ncm-btn-red" nc_type="dialog" dialog_width="480" dialog_title="<?php echo $lang['member_order_cancel_order'];?>" dialog_id="buyer_order_cancel_order" uri="index.php?act=member_order&op=change_state&state_type=order_cancel&order_id=<?php echo $order_info['order_id']; ?>"  id="order<?php echo $order_info['order_id']; ?>_action_cancel"> <?php echo $lang['member_order_cancel_order'];?></a></p>
          <?php } ?>
          
              
          <!-- 收货 仅有订单状态为30or33时才可以收货-->
          <?php if ($order_info['if_receive'] && !$order_info['if_lock'] && ($order_info['order_state']==ORDER_STATUS_CUS_RECEIVED)) { ?>
          <p><a href="javascript:void(0)" style="margin-top:5px;" class="ncm-btn" nc_type="dialog" dialog_id="buyer_order_confirm_order" dialog_width="480" dialog_title="<?php echo $lang['member_order_ensure_order'];?>" uri="index.php?act=member_order&op=change_state&state_type=order_receive&order_sn=<?php echo $order_info['order_sn']; ?>&order_id=<?php echo $order_info['order_id']; ?>" id="order<?php echo $order_info['order_id']; ?>_action_confirm"><?php echo $lang['member_order_ensure_order'];?></a></p>
          <?php } ?>
          
          <!-- 评价 -->
          <a href="member_order.index.php"></a>

          <?php if ($order_info['if_evaluation']) { ?>
          <p><a class="ncm-btn ncm-btn-acidblue" href="index.php?act=member_evaluate&op=add&order_id=<?php echo $order_info['order_id']; ?>"><i class="icon-thumbs-up-alt"></i><?php echo $lang['member_order_want_evaluate'];?></a></p>
          <?php } ?>

          <!-- 已经评价 -->

          <?php if ($order_info['evaluation_state'] == 1) { echo $lang['order_state_eval'];} ?>
          <?php if ($order_info['if_drop']) { ?>
          <p><a href="javascript:void(0);" onclick="ajax_get_confirm('您确定要永久删除吗?永久删除后您将无法再查看该订单，也无法进行投诉维权，请谨慎操作！', 'index.php?act=member_order&op=change_state&state_type=order_drop&order_id=<?php echo $order_info['order_id']; ?>');" class="ncm-btn ncm-btn-red mt5"><i class="icon-trash"></i>永久删除</a></p>
          <?php } ?></td>
        <!-- E 合并TD -->
        <?php } ?>
      </tr>

      <!-- S 赠品列表 -->

      <?php if (!empty($order_info['zengpin_list']) && $i == count($order_info['goods_list'])) { ?>
      <tr>
        <td class="bdl"></td>
        <td colspan="5" class="tl"><div class="ncm-goods-gift"> 赠品：
            <ul>
              <?php foreach ($order_info['zengpin_list'] as $zengpin_info) { ?>
              <li><a title="赠品：<?php echo $zengpin_info['goods_name'];?> * <?php echo $zengpin_info['goods_num'];?>" href="<?php echo $zengpin_info['goods_url'];?>" target="_blank"><img src="<?php echo $zengpin_info['image_60_url'];?>" onMouseOver="toolTip('<img src=<?php echo $zengpin_info['image_240_url'];?>>')" onMouseOut="toolTip()"/></a></li>
              <?php } ?>
            </ul>
          </div></td>
      </tr>
      <?php } ?>
      <!-- E 赠品列表 -->

      <?php } }?>
      <!-- E 商品列表 -->

      <?php } ?>
    </tbody>
    <?php } ?>
    <?php } else { ?>
    <tbody>
      <tr>
        <td colspan="20" class="norecord"><div class="warning-option"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></div></td>
      </tr>
    </tbody>
    <?php } ?>
    <?php if($output['order_pay_list']) { ?>
    <tfoot>
      <tr>
        <td colspan="19"><div class="pagination"> <?php echo $output['show_page']; ?> </div></td>
      </tr>
    </tfoot>
    <?php } ?>
  </table>
</div>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/sns.js" ></script>
<script type="text/javascript">
$(function(){
    $('#query_start_date').datepicker({dateFormat: 'yy-mm-dd'});
    $('#query_end_date').datepicker({dateFormat: 'yy-mm-dd'});
//    //自动跳转
//    <?php if($output['order_num']){?>
//        var order_num = <?php echo $output['order_num'];?>;
//        var url='<?php echo BASE_SITE_URL;?>/shop/index.php?act=member_push_order&op=transOrderToYMA';
//        $.getJSON(url, {'order_num':order_num}, function(data){
//            if (data){
//                if (data.resultCode == 0){
//                    window.open("<?php echo YMA_WEBSERVICE_URL_ORDER;?>&BOID=" + order_num);
//                }
//            }
//        });
//    <?PHP }?>
    
    $('.ajax_post_state').click(function(){
        var order_num = $(this).attr('lang');
        var url='<?php echo BASE_SITE_URL;?>/shop/index.php?act=member_push_order&op=transOrderToYMA';
        $.getJSON(url, {'order_num':order_num}, function(data){
            if (data){
                if (data.resultCode == 0){
                    window.open("<?php echo YMA_WEBSERVICE_URL_ORDER;?>&BOID=" + order_num);
                }else if(data.resultCode == -2){
                    alert(data.resultMsg);
					}else{
                    alert("订单暂时无法审核，请联系管理员!");
                }
            }
        });
    })
});
</script>
