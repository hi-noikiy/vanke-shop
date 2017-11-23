<?php
/**
 * 会员管理
 *
 *
 *
 ***/



class memberControl extends SystemControl{
	const EXPORT_SIZE = 1000;
	public function __construct(){
		parent::__construct();
		Language::read('member');
	}

	/**
	 * 会员管理
	 */
	public function memberOp(){
		$lang	= Language::getLangContent();
		$model_member = Model('member');
				/**
		 * 删除
		 */
		if (chksubmit()){
			/**
			 * 判断是否是管理员，如果是，则不能删除
			 */
			/**
			 * 删除
			 */
			if (!empty($_POST['del_id'])){
				if (is_array($_POST['del_id'])){
					foreach ($_POST['del_id'] as $k => $v){
						$v = intval($v);
						$rs = true;//$model_member->del($v);
						if ($rs){
							//删除该会员商品,店铺
							//获得该会员店铺信息
							$member = $model_member->getMemberInfo(array(
								'member_id'=>$v
							));
							//删除用户
							$model_member->del($v);
						}
					}
				}
				showMessage($lang['nc_common_del_succ']);
			}else {
				showMessage($lang['nc_common_del_fail']);
			}
		}
		//会员级别
		$member_grade = $model_member->getMemberGradeArr();
		if ($_GET['search_field_value'] != '') {
    		switch ($_GET['search_field_name']){
    			case 'member_name':
    				$condition['member_name'] = array('like', '%' . trim($_GET['search_field_value']) . '%');
    				break;
    			case 'member_email':
    				$condition['member_email'] = array('like', '%' . trim($_GET['search_field_value']) . '%');
    				break;
				//v3- b11
				case 'member_mobile':
    				$condition['member_mobile'] = array('like', '%' . trim($_GET['search_field_value']) . '%');
    				break;
    			case 'member_truename':
    				$condition['member_truename'] = array('like', '%' . trim($_GET['search_field_value']) . '%');
    				break;
    		}
		}
		switch ($_GET['search_state']){
			case 'no_informallow':
				$condition['inform_allow'] = '2';
				break;
			case 'no_isbuy':
				$condition['is_buy'] = '0';
				break;
			case 'no_isallowtalk':
				$condition['is_allowtalk'] = '0';
				break;
			case 'no_memberstate':
				$condition['member_state'] = '0';
				break;
		}
		//会员等级
		$search_grade = intval($_GET['search_grade']);
		if ($search_grade >= 0 && $member_grade){
		    $condition['member_exppoints'] = array(array('egt',$member_grade[$search_grade]['exppoints']),array('lt',$member_grade[$search_grade+1]['exppoints']),'and');
		}
		//排序
		$order = trim($_GET['search_sort']);
		if (empty($order)) {
		    $order = 'member_id desc';
		}
		$member_list = $model_member->getMemberList($condition, '*', 10, $order);		
		//整理会员信息
		if (is_array($member_list)){
			foreach ($member_list as $k=> $v){
				$member_list[$k]['member_time'] = $v['member_time']?date('Y-m-d H:i:s',$v['member_time']):'';
				$member_list[$k]['member_login_time'] = $v['member_login_time']?date('Y-m-d H:i:s',$v['member_login_time']):'';
				$member_list[$k]['member_grade'] = ($t = $model_member->getOneMemberGrade($v['member_exppoints'], false, $member_grade))?$t['level_name']:'';
			}
		}
		Tpl::output('member_grade',$member_grade);
		Tpl::output('search_sort',trim($_GET['search_sort']));
		Tpl::output('search_field_name',trim($_GET['search_field_name']));
		Tpl::output('search_field_value',trim($_GET['search_field_value']));
		Tpl::output('member_list',$member_list);
		Tpl::output('page',$model_member->showpage());
		Tpl::showpage('member.index');
	}

	/**
	 * 会员修改
	 */
	public function member_editOp(){
		$lang	= Language::getLangContent();
		$model_member = Model('member');
		/**
		 * 保存
		 */
		if (chksubmit()){
			/**
			 * 验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
			array("input"=>$_POST["member_email"], "require"=>"true", 'validator'=>'Email', "message"=>$lang['member_edit_valid_email']),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {
				$update_array = array();
				$update_array['member_id']			= intval($_POST['member_id']);
				if (!empty($_POST['member_passwd'])){
					$update_array['member_passwd'] = md5($_POST['member_passwd']);
				}
				$update_array['member_email']		= $_POST['member_email'];
				$update_array['member_truename']	= $_POST['member_truename'];
				$update_array['member_sex'] 		= $_POST['member_sex'];
				$update_array['member_qq'] 			= $_POST['member_qq'];
				$update_array['member_ww']			= $_POST['member_ww'];
				$update_array['inform_allow'] 		= $_POST['inform_allow'];
				$update_array['is_buy'] 			= $_POST['isbuy'];
				$update_array['is_allowtalk'] 		= $_POST['allowtalk'];
				$update_array['member_state'] 		= $_POST['memberstate'];
				// 新增
				$update_array['member_cityid']		= $_POST['city_id'];
			        $update_array['member_provinceid']	= $_POST['province_id'];
			        $update_array['member_areainfo']	= $_POST['area_info'];
				$update_array['member_mobile'] 		= $_POST['member_mobile'];
				$update_array['member_email_bind'] 	= intval($_POST['memberemailbind']);
				$update_array['member_mobile_bind'] 	= intval($_POST['membermobilebind']);

			
				if (!empty($_POST['member_avatar'])){
					$update_array['member_avatar'] = $_POST['member_avatar'];
				}
				$result = $model_member->editMember(array('member_id'=>intval($_POST['member_id'])),$update_array);
				if ($result){
					$url = array(
					array(
					'url'=>'index.php?act=member&op=member',
					'msg'=>$lang['member_edit_back_to_list'],
					),
					array(
					'url'=>'index.php?act=member&op=member_edit&member_id='.intval($_POST['member_id']),
					'msg'=>$lang['member_edit_again'],
					),
					);
					$this->log(L('nc_edit,member_index_name').'[ID:'.$_POST['member_id'].']',1);
					showMessage($lang['member_edit_succ'],$url);
				}else {
					showMessage($lang['member_edit_fail']);
				}
			}
		}
		$condition['member_id'] = intval($_GET['member_id']);
		$member_array = $model_member->getMemberInfo($condition);

		Tpl::output('member_array',$member_array);
		Tpl::showpage('member.edit');
	}

	/**
	 * 新增会员
	 */
	public function member_addOp(){
		$lang	= Language::getLangContent();
		$model_member = Model('member');
		/**
		 * 保存
		 */
		if (chksubmit()){
			/**
			 * 验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
			    array("input"=>$_POST["member_name"], "require"=>"true", "message"=>$lang['member_add_name_null']),
			    array("input"=>$_POST["member_passwd"], "require"=>"true", "message"=>'密码不能为空'),
			    array("input"=>$_POST["member_email"], "require"=>"true", 'validator'=>'Email', "message"=>$lang['member_edit_valid_email'])
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {
				$insert_array = array();
				$insert_array['member_name']	= trim($_POST['member_name']);
				$insert_array['member_passwd']	= trim($_POST['member_passwd']);
				$insert_array['member_email']	= trim($_POST['member_email']);
				$insert_array['member_truename']= trim($_POST['member_truename']);
				$insert_array['member_sex'] 	= trim($_POST['member_sex']);
				$insert_array['member_qq'] 		= trim($_POST['member_qq']);
				$insert_array['member_ww']		= trim($_POST['member_ww']);
                //默认允许举报商品
                $insert_array['inform_allow'] 	= '1';
				if (!empty($_POST['member_avatar'])){
					$insert_array['member_avatar'] = trim($_POST['member_avatar']);
				}

				$result = $model_member->addMember($insert_array);
				if ($result){
					$url = array(
					array(
					'url'=>'index.php?act=member&op=member',
					'msg'=>$lang['member_add_back_to_list'],
					),
					array(
					'url'=>'index.php?act=member&op=member_add',
					'msg'=>$lang['member_add_again'],
					),
					);
					$this->log(L('nc_add,member_index_name').'[	'.$_POST['member_name'].']',1);
					showMessage($lang['member_add_succ'],$url);
				}else {
					showMessage($lang['member_add_fail']);
				}
			}
		}
		Tpl::showpage('member.add');
	}

	/**
	 * ajax操作
	 */
	public function ajaxOp(){
		switch ($_GET['branch']){
			/**
			 * 验证会员是否重复
			 */
			case 'check_user_name':
				$model_member = Model('member');
				$condition['member_name']	= $_GET['member_name'];
				$condition['member_id']	= array('neq',intval($_GET['member_id']));
				$list = $model_member->getMemberInfo($condition);
				if (empty($list)){
					echo 'true';exit;
				}else {
					echo 'false';exit;
				}
				break;
				/**
			 * 验证邮件是否重复
			 */
			case 'check_email':
				$model_member = Model('member');
				$condition['member_email'] = $_GET['member_email'];
				$condition['member_id'] = array('neq',intval($_GET['member_id']));
				$list = $model_member->getMemberInfo($condition);
				if (empty($list)){
					echo 'true';exit;
				}else {
					echo 'false';exit;
				}
				break;
		}
	}


	/**
     * 会员删除处理（仅限特殊处理使用）
     * @Author Aletta
     * @Date 2017-09-01
	 **/
	public function member_delOp(){
	    $member = $_GET['id'];
	    if(!empty($member)){
	        $model = Model();
            $member_data = $model->table('member')->where("member_name = '".$member."'")->find();
            if(!empty($member_data)){
                //获取商户店铺数据
                $store_data = $model->table('store')->where("member_id = '".$member_data['member_id']."'")->find();
                if(!empty($store_data)){
                    //检查订单商品表数据
                    $goods_data = $model->table('goods')->where("store_id = '".$store_data['store_id']."'")->find();
                    $order_data = $model->table('order')->where("store_id = '".$store_data['member_id']."'")->find();
                    if(!empty($goods_data) || !empty($order_data)){
                        echo "<font color=\"#FF0000\">该用户存在订单商品数据，无法做出删除操作</font> ";
                        exit;
                    }else{
                        echo "<font>用户存在存在店铺信息数据，正在执行清理。。。。。</font></br>";
                        $store_res = $model->table('store')->where("member_id = '".$member_data['member_id']."'")->delete();
                        echo $store_res ? "<font color=\"#66CD00\">store清理成功</font> ":"<font color=\"#FF0000\">store清理失败</font>";
                        echo "</br>";
                    }
                }else{
                    $store_res = true;
                }
                //开启事物做出清理删除动作 #ED9121
                $model->beginTransaction();
                //1、检查seller
                $seller_data = $model->table('seller')->where("member_id = '".$member_data['member_id']."'")->find();
                if(!empty($seller_data)){
                    echo "<font>用户存在存在卖家信息数据，正在执行清理。。。。。</font></br>";
                    $seller_res = $model->table('seller')->where("member_id = '".$member_data['member_id']."'")->delete();
                    echo $seller_res ? "<font color=\"#66CD00\">seller清理成功</font> ":"<font color=\"#FF0000\">seller清理失败</font>";
                    echo "</br>";
                }else{
                    $seller_res = true;
                }
                //2、检查join
                $join_data = $model->table('store_joinin')->where("member_id = '".$member_data['member_id']."'")->find();
                if(!empty($join_data)){
                    echo "<font>用户存在存在入住申请信息数据，正在执行清理。。。。。</font></br>";
                    $join_res = $model->table('store_joinin')->where("member_id = '".$member_data['member_id']."'")->delete();
                    echo $join_res ? "<font color=\"#66CD00\">store_joinin清理成功</font> ":"<font color=\"#FF0000\">store_joinin清理失败</font>";
                    echo "</br>";
                }else{
                    $join_res = true;
                }
                //检查supplier_information
                $information_data = $model->table('supplier_information')->where("member_id = '".$member_data['member_id']."'")->find();
                if(!empty($information_data)){
                    echo "<font>用户存在存在供应商联系人信息数据，正在执行清理。。。。。</font></br>";
                    $information_res = $model->table('supplier_information')->where("member_id = '".$member_data['member_id']."'")->delete();
                    echo $information_res ? "<font color=\"#66CD00\">supplier_information清理成功</font> ":"<font color=\"#FF0000\">supplier_information清理失败</font>";
                    echo "</br>";
                }else{
                    $information_res = true;
                }
                //检查supplier
                $supplier_data = $model->table('supplier')->where("member_id = '".$member_data['member_id']."'")->find();
                if(!empty($supplier_data)){
                    echo "<font>用户存在存在供应商基本信息数据，正在执行清理。。。。。</font></br>";
                    $supplier_res = $model->table('supplier')->where("member_id = '".$member_data['member_id']."'")->delete();
                    echo $supplier_res ? "<font color=\"#66CD00\">supplier清理成功</font> ":"<font color=\"#FF0000\">supplier清理失败</font>";
                    echo "</br>";
                }else{
                    $supplier_res = true;
                }
                //最后执行member清理
                echo "<font>用户存在存在用户基础信息数据，正在执行清理。。。。。</font></br>";
                $member_res = $model->table('member')->where("member_id = '".$member_data['member_id']."'")->delete();
                echo $member_res ? "<font color=\"#66CD00\">member清理成功</font> ":"<font color=\"#FF0000\">member清理失败</font>";
                echo "</br>";
                if($store_res && $seller_res && $join_res && $information_res && $supplier_res && $member_res){
                    $model->commit();
                    echo "<font>*************************************************</font></br>";
                    echo "<font color=\"#66CD00\">用户".$member."已经完成清理</font></br>";
                }else{
                    $model->rollback();
                    echo "<font>*************************************************</font></br>";
                    echo "<font color=\"#FF0000\">用户".$member."数据清理失败</font></br>";
                }
            }else{
                echo "<font color=\"#FF0000\">该用户不存在</font> ";
            }
        }else{
	        echo "<font color=\"#FF0000\">参数错误</font> ";
        }
    }

}
