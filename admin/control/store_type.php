<?php
/**
 * 店铺分类管理
 *
 *
 *
 ***/



class store_typeControl extends SystemControl{
	public function __construct(){
		parent::__construct();
		Language::read('store_type');
	}

	/**
	 * 店铺分类
	 */
	public function store_typeOp(){
		$lang	= Language::getLangContent();
		$model_class = Model('store_type');

		//删除
		if (chksubmit()){
			if (!empty($_POST['check_st_id']) && is_array($_POST['check_st_id']) ){
			    $result = $model_class->delStoreClass(array('st_id'=>array('in',$_POST['check_st_id'])));
				if ($result) {
			        $this->log(L('nc_del,store_type').'[ID:'.implode(',',$_POST['check_st_id']).']',1);
				    showMessage($lang['nc_common_del_succ']);
				}
			}
		    showMessage($lang['nc_common_del_fail']);
		}

		$store_type_list = $model_class->getStoreClassList(array(),20);
		Tpl::output('lang_bind',"商城分类绑定");
		Tpl::output('class_list',$store_type_list);
		Tpl::output('page',$model_class->showpage());
		Tpl::showpage('store_type.index');
	}

	/**
	 * 商品分类添加
	 */
	public function store_type_addOp(){
		$lang	= Language::getLangContent();
		$model_class = Model('store_type');
		if (chksubmit()){
			//验证
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
			array("input"=>$_POST["st_name"], "require"=>"true", "message"=>$lang['store_type_name_no_null']),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {
				$insert_array = array();
				$insert_array['st_name'] = $_POST['st_name'];
				$insert_array['st_sort'] = intval($_POST['st_sort']);
				$result = $model_class->addStoreClass($insert_array);
				if ($result){
					$url = array(
					array(
					'url'=>'index.php?act=store_type&op=store_type_add',
					'msg'=>$lang['continue_add_store_type'],
					),
					array(
					'url'=>'index.php?act=store_type&op=store_type',
					'msg'=>$lang['back_store_type_list'],
					)
					);
					$this->log(L('nc_add,store_type').'['.$_POST['st_name'].']',1);
					showMessage($lang['nc_common_save_succ'],$url,'html','succ',1,5000);
				}else {
					showMessage($lang['nc_common_save_fail']);
				}
			}
		}
		Tpl::showpage('store_type.add');
	}

	/**
	 * 编辑
	 */
	public function store_type_editOp(){
		$lang	= Language::getLangContent();

		$model_class = Model('store_type');

		if (chksubmit()){
			//验证
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
			array("input"=>$_POST["st_name"], "require"=>"true", "message"=>$lang['store_type_name_no_null']),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {
				$update_array = array();
				$update_array['st_name'] = $_POST['st_name'];
				$update_array['st_sort'] = intval($_POST['st_sort']);
				$result = $model_class->editStoreClass($update_array,array('st_id'=>intval($_POST['st_id'])));
				if ($result){
					$this->log(L('nc_edit,store_type').'['.$_POST['st_name'].']',1);
					showMessage($lang['nc_common_save_succ'],'index.php?act=store_type&op=store_type');
				}else {
					showMessage($lang['nc_common_save_fail']);
				}
			}
		}

		$class_array = $model_class->getStoreClassInfo(array('st_id'=>intval($_GET['st_id'])));
		if (empty($class_array)){
			showMessage($lang['illegal_parameter']);
		}

		Tpl::output('class_array',$class_array);
		Tpl::showpage('store_type.edit');
	}

	/**
	 * 删除分类
	 */
	public function store_type_delOp(){
		$lang	= Language::getLangContent();
		$model_class = Model('store_type');
		if (intval($_GET['st_id']) > 0){
			$array = array(intval($_GET['st_id']));
			$result = $model_class->delStoreClass(array('st_id'=>intval($_GET['st_id'])));
			if ($result) {
			     $this->log(L('nc_del,store_type').'[ID:'.$_GET['st_id'].']',1);
			     showMessage($lang['nc_common_del_succ'],getReferer());
			}
		}
		showMessage($lang['nc_common_del_fail'],'index.php?act=store_type&op=store_type');
	}

	/**
	 * ajax操作
	 */
	public function ajaxOp(){
	    $model_class = Model('store_type');
	    $update_array = array();
		switch ($_GET['branch']){
			//分类：验证是否有重复的名称
			case 'store_type_name':
			    $condition = array();
				$condition['st_name'] = $_GET['value'];
				$condition['st_id'] = array('st_id'=>array('neq',intval($_GET['st_id'])));
				$class_list = $model_class->getStoreClassList($condition);
				if (empty($class_list)){
					$update_array['st_name'] = $_GET['value'];
					$update = $model_class->editStoreClass($update_array,array('st_id'=>intval($_GET['id'])));
					$return = $update ? 'true' : 'false';
				} else {
					$return = 'false';
				}
				break;
			//分类： 排序 显示 设置
			case 'store_type_sort':
				$model_class = Model('store_type');
				$update_array['st_sort'] = intval($_GET['value']);
				$result = $model_class->editStoreClass($update_array,array('st_id'=>intval($_GET['id'])));
				$return = $result ? 'true' : 'false';
				break;
			//分类：添加、修改操作中 检测类别名称是否有重复
			case 'check_class_name':
				$condition['st_name'] = $_GET['st_name'];
				$condition['st_id'] = array('st_id'=>array('neq',intval($_GET['st_id'])));
				$class_list = $model_class->getStoreClassList($condition);
				$return = empty($class_list) ? 'true' : 'false';
				break;
		}
		exit($return);
	}
}
