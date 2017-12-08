<?php
/**
 * 收货地址
 *
 *
 *
 ***/




class member_addressControl extends BaseMemberControl{
	/**
	 * 会员地址
	 *
	 * @param
	 * @return
	 */
	public function addressOp() {

		Language::read('member_member_index');
		$lang	= Language::getLangContent();

		$address_class = Model('address');
		/**
		 * 判断页面类型
		 */
		if (!empty($_GET['type'])){
			/**
			 * 新增/编辑地址页面
			 */
			if (intval($_GET['id']) > 0){
				/**
				 * 得到地址信息
				 */
				$address_info = $address_class->getOneAddress(intval($_GET['id']));
				if ($address_info['member_id'] != $_SESSION['member_id']){
					showMessage($lang['member_address_wrong_argument'],'index.php?act=member_address&op=address','html','error');
				}
				/**
				 * 输出地址信息
				 */
				Tpl::output('address_info',$address_info);
			}
			/**
			 * 增加/修改页面输出
			 */
			Tpl::output('type',$_GET['type']);
			Tpl::showpage('member_address.edit','null_layout');
			exit();
		}
		/**
		 * 判断操作类型
		 */
		if (chksubmit()){
			/**
			 * 验证表单信息
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["true_name"],"require"=>"true","message"=>$lang['member_address_receiver_null']),
				array("input"=>$_POST["area_id"],"require"=>"true","validator"=>"Number","message"=>$lang['member_address_wrong_area']),
				array("input"=>$_POST["city_id"],"require"=>"true","validator"=>"Number","message"=>$lang['member_address_wrong_area']),
				array("input"=>$_POST["area_info"],"require"=>"true","message"=>$lang['member_address_area_null']),
				array("input"=>$_POST["address"],"require"=>"true","message"=>$lang['member_address_address_null']),
				array("input"=>$_POST['tel_phone'].$_POST['mob_phone'],'require'=>'true','message'=>$lang['member_address_phone_and_mobile'])
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showValidateError($error);
            }
            $data = array();
			$data['member_id'] = $_SESSION['member_id'];
			$data['true_name'] = $_POST['true_name'];
			$data['area_id'] = intval($_POST['area_id']);
			$data['city_id'] = intval($_POST['city_id']);
			$data['area_info'] = $_POST['area_info'];
			$data['address'] = $_POST['address'];
			$data['tel_phone'] = $_POST['tel_phone'];
			$data['mob_phone'] = $_POST['mob_phone'];
			$data['is_default'] = $_POST['is_default'] ? 1 : 0;
			if ($_POST['is_default']) {
			    $address_class->editAddress(array('is_default'=>0),array('member_id'=>$_SESSION['member_id'],'is_default'=>1));
			}

			if (intval($_POST['id']) > 0){
                $rs = $address_class->editAddress($data, array('address_id' => $_POST['id']));
				if (!$rs){
					showDialog($lang['member_address_modify_fail'],'','error');
				}
			}else {
			    $count = $address_class->getAddressCount(array('member_id'=>$_SESSION['member_id']));
			    if ($count >= 20) {
			        showDialog('最多允许添加20个有效地址','','error');
			    }
				$rs = $address_class->addAddress($data);
				if (!$rs){
					showDialog($lang['member_address_add_fail'],'','error');
				}
			}
			showDialog($lang['nc_common_op_succ'],'reload','js');
		}
		$del_id = isset($_GET['id']) ? intval(trim($_GET['id'])) : 0 ;
		if ($del_id > 0){
			$rs = $address_class->delAddress(array('address_id'=>$del_id,'member_id'=>$_SESSION['member_id']));
			if ($rs){
				showDialog(Language::get('member_address_del_succ'),'index.php?act=member_address&op=address','js');
			}else {
				showDialog(Language::get('member_address_del_fail'),'','error');
			}
		}
		$address_list = $address_class->getAddressList(array('member_id'=>$_SESSION['member_id']));
		
		self::profile_menu('address','address');
		Tpl::output('address_list',$address_list);
		Tpl::showpage('member_address.index');
	}

	/**
	 * 添加自提点型收货地址
	 */
	public function delivery_addOp() {
	    if (chksubmit()) {
	        $info = Model('delivery_point')->getDeliveryPointOpenInfo(array('dlyp_id'=>intval($_POST['dlyp_id'])));
	        if (empty($info)) {
	            showDialog('该自提点不存在','','error');
	        }
	        $data = array();
	        $data['member_id'] = $_SESSION['member_id'];
	        $data['true_name'] = $_POST['true_name'];
	        $data['area_id'] = $info['dlyp_area_3'];
	        $data['city_id'] = $info['dlyp_area_2'];
	        $data['area_info'] = $info['dlyp_area_info'];
	        $data['address'] = $info['dlyp_address'];
	        $data['tel_phone'] = $_POST['tel_phone'];
	        $data['mob_phone'] = $_POST['mob_phone'];
	        $data['dlyp_id'] = $info['dlyp_id'];
	        $data['is_default'] = 0;
	        if (intval($_POST['address_id'])) {
	            $result = Model('address')->editAddress($data, array('address_id' => intval($_POST['address_id'])));
	        } else {
                $count = Model('address')->getAddressCount(array('member_id'=>$_SESSION['member_id']));
                if ($count >= 20) {
                    showDialog('最多允许添加20个有效地址','','error');
                }
	            $result = Model('address')->addAddress($data);
	        }
	        if (!$result){
	            showDialog('保存失败','','error');
	        }
	        showDialog('保存成功','reload','js');
	    } else {
	        if (intval($_GET['id']) > 0) {
	            $model_addr = Model('address');
	            $condition = array('address_id'=>intval($_GET['id']),'member_id'=>$_SESSION['member_id']);
	            $address_info = $model_addr->getAddressInfo($condition);
	            //取出省级ID
	            $area_info = Model('area')->getAreaInfo(array('area_id'=>$address_info['city_id']));
	            $address_info['province_id'] = $area_info['area_parent_id'];
	            Tpl::output('address_info',$address_info);	            
	        }
	        Tpl::showpage('member_address.delivery_add','null_layout');
	    }
	}

	/**
	 * 展示自提点列表
	 */
	public function delivery_listOp() {
	    $model_delivery = Model('delivery_point');
	    $condition = array();
	    $condition['dlyp_area_3'] = intval($_GET['area_id']);
	    $list = $model_delivery->getDeliveryPointOpenList($condition,5);
	    Tpl::output('show_page',$model_delivery->showpage());
	    Tpl::output('list',$list);
	    Tpl::showpage('member_address.delivery_list','null_layout');
	}
	
	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @return
	 */
	private function profile_menu($menu_type,$menu_key='') {
		/**
		 * 读取语言包
		 */
		Language::read('member_layout');
		$menu_array	= array();
		switch ($menu_type) {
			case 'address':
				$menu_array = array(
				1=>array('menu_key'=>'address','menu_name'=>Language::get('nc_member_path_address_list'),	'menu_url'=>'index.php?act=member_adderss&op=address'));
				break;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}




    /**
     * 设置默认收货地址
     * @Author  : Aletta
     * @Time    : 2017-11-22 PM 14:25
     */
    public function defaultAddressOp(){
        $where = "address_id = '".$_POST['id']."' and member_id = '".$_SESSION['member_id']."'";
        $addData = Model()->table('address')->where($where)->find();
        if(!empty($addData)){
            //获取之前的默认地址数据
            $oldData = Model()->table('address')->where("is_default = '1' and member_id = '".$_SESSION['member_id']."'")->find();
            if(!empty($oldData)){
                Model()->table('address')->where("address_id = '".$oldData['address_id']."'")->update(array('is_default'=>0));
            }
            $rest = Model()->table('address')->where("address_id = '".$addData['address_id']."'")->update(array('is_default'=>1));
            echo $rest ? "1":"-1";
            exit;
        }
    }


    /**
     * 获取城市数据信息
     * @Author  : Aletta
     * @Time    : 2017-11-23 PM 14:02
     */
    public function getCityListOp(){
        $type = isset($_GET["type"]) ? $_GET["type"] : "1";
        $parent_id = isset($_GET["parent_id"]) ? $_GET["parent_id"] : "0";
        $where = "area_parent_id = '".$parent_id."' and area_deep = '".$type."'";
        $list = Model()->table("area")->where($where)->select();
        $provinces_json = json_encode($list);
        exit($provinces_json);
    }

    /**
     * 新增收货地址数据
     * @Author  : Aletta
     * @Time    : 2017-11-23 PM 14:02
     */
    public function newAddressOp(){
        $rest_data = array("code" => '-1', "msg" => "非法请求", 'data'=>'');
        $data = $_POST;
        if(!empty($data) && is_array($data) && !empty($_SESSION['member_id'])){
            $tel_phone = empty($data['area_code']) ? "":$data['area_code']."-";
            $tel_phone.= empty($data['tell_num']) ? "":$data['tell_num'];
            $tel_phone.= empty($data['extension']) ? "":"-".$data['extension'];
            //获取城市数据
            $province = Model()->table("area")->where("area_id = '".$data['province']."'")->find();
            $city = Model()->table("area")->where("area_id = '".$data['city']."'")->find();
            $county = Model()->table("area")->where("area_id = '".$data['county']."'")->find();

            $area_info = empty($province['area_name']) ? "":$province['area_name']."  ";
            $area_info.= empty($city['area_name']) ? "":$city['area_name'];
            $area_info.= empty($county['area_name']) ? "":"  ".$county['area_name'];

            $list = array(
                'true_name' =>$data['send_name'],
                'area_id'   =>$data['county'],
                'city_id'   =>$data['city'],
                'area_info' =>$area_info,
                'address'   =>$data['address'],
                'tel_phone' =>$tel_phone,
                'mob_phone' =>$data['phone'],
            );
            //add
            if(empty($data['addId']) && empty($data['showId'])){
                $list['member_id'] = $_SESSION['member_id'];
                $rest = Model()->table('address')->insert($list);
                if($rest){
                    $list['address_id'] = $rest;
                    $rest_data['code'] = '1';
                    $rest_data['msg'] = "success";
                    $rest_data['data'] = $list;
                }else{
                    $rest_data['code'] = '-1';
                    $rest_data['msg'] = "添加失败";
                }
            }
            //updata
            if(!empty($data['addId']) && !empty($data['showId'])){
                $address = Model()->table('address')->where("address_id = '".$data['addId']."' and member_id = '".$_SESSION['member_id']."'")->find();
                if(!empty($address)){
                    $rest = Model()->table('address')->where("address_id = '".$data['addId']."' and member_id = '".$_SESSION['member_id']."'")->update($list);
                    if($rest){
                        $list['address_id'] = $address['address_id'];
                        $rest_data['code'] = $data['addId'] == $data['showId'] ? '2':'1';
                        $rest_data['msg'] = "success";
                        $rest_data['data'] = $list;
                    }else{
                        $rest_data['code'] = '-1';
                        $rest_data['msg'] = "修改失败";
                    }
                }
            }
        }
        echo json_encode($rest_data);
    }



    /**
     * 删除收货地址
     * @Author  : Aletta
     * @Time    : 2017-11-24 PM 09:25
     */
    public function delAddressOp(){
        $rest = array(
            'code'  =>'-1',
            'msg'   =>'地址信息有误，请重新操作或联系管理员',
            'data'  =>'',
        );
        $where = "address_id = '".$_POST['id']."' and member_id = '".$_SESSION['member_id']."'";
        $addData = Model()->table('address')->where($where)->find();
        if(!empty($addData)){
            $res = Model()->table('address')->where($where)->delete();
            if($res){
                if($_POST['id'] == $_POST['sid']){
                    $show_data = Model()->table('address')->where("member_id = '".$_SESSION['member_id']."'")->order("is_default desc")->find();
                    $rest['data'] = $show_data;
                    $rest['code'] = empty($show_data) ? "2":"3";
                }else{
                    $rest['code'] = "1";
                }
                $rest['msg'] = "success";
            }else{
                $rest['code'] = "-1";
                $rest['msg'] = "删除失败，请重新操作";
            }
        }
        echo json_encode($rest);
    }
}
