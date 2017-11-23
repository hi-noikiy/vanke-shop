<?php
/**
 * 店铺等级管理
 *
 *
 *
 ***/



class agent_gradeControl extends SystemControl{
	public function __construct(){
		parent::__construct();
		Language::read('agent_grade');
	}
	/**
	 * 店铺等级
	 */
	public function agent_gradeOp(){
		/**
		 * 读取语言包
		 */
		$lang	= Language::getLangContent();

		$model_grade = Model('agent_grade');
		/**
		 * 删除
		 */
		if (chksubmit()){
			if (!empty($_POST['check_sg_id'])){
				if (is_array($_POST['check_sg_id'])){
					$model_agent = Model('agent');
					foreach ($_POST['check_sg_id'] as $k => $v){
						/**
						 * 该店铺等级下的所有店铺会自动改为默认等级
						 */
						$v = intval($v);
						//判断是否默认等级，默认等级不能删除
						if ($v == 1){
							//showMessage('默认等级不能删除 ','index.php?act=agent_grade&op=agent_grade');
							showMessage($lang['default_agent_grade_no_del'],'index.php?act=agent_grade&op=agent_grade');
						}
						//判断该等级下是否存在店铺，存在的话不能删除
						if ($this->isable_delGrade($v)){
							$model_grade->del($v);
						}
					}
				}
				dkcache('agent_grade');
				$this->log(L('nc_del,agent_grade').'[ID:'.implode(',',$_POST['check_sg_id']).']',1);
				showMessage($lang['nc_common_del_succ']);
			}else {
				showMessage($lang['nc_common_del_fail']);
			}
		}
		$condition['like_sg_name'] = trim($_POST['like_sg_name']);
		$condition['order'] = 'sg_sort';

		$grade_list = $model_grade->getGradeList($condition);

		Tpl::output('like_sg_name',trim($_POST['like_sg_name']));
		Tpl::output('grade_list',$grade_list);
		Tpl::showpage('agent_grade.index');
	}

	/**
	 * 新增等级
	 */
	public function agent_grade_addOp(){
		$lang	= Language::getLangContent();

		$model_grade = Model('agent_grade');
		if (chksubmit()){

			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["sg_name"], "require"=>"true", "message"=>$lang['agent_grade_name_no_null']),
				array("input"=>$_POST["ag_rate"], "require"=>"true", 'validator'=>'Number', "message"=>$lang['allow_pubilsh_product_num_only_lnteger']),
				array("input"=>$_POST["sg_sort"], "require"=>"true", 'validator'=>'Number', "message"=>$lang['sort_only_lnteger']),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {
				//验证等级名称
				if (!$this->checkGradeName(array('sg_name'=>trim($_POST['sg_name'])))){
					showMessage($lang['now_agent_grade_name_is_there']);
				}
				//验证级别是否存在
				if (!$this->checkGradeSort(array('sg_sort'=>trim($_POST['sg_sort'])))){
					showMessage($lang['add_gradesortexist']);
				}
				$insert_array = array();
				$insert_array['sg_name'] = trim($_POST['sg_name']);
//				$insert_array['sg_goods_limit'] = trim($_POST['sg_goods_limit']);
//				$insert_array['sg_space_limit'] = '100';
//				$insert_array['sg_album_limit'] = '' === trim($_POST['sg_album_limit']) ? 1000 : intval($_POST['sg_album_limit']);
//				$insert_array['sg_function'] = $_POST['sg_function']?implode('|',$_POST['sg_function']):'';
				$insert_array['sg_price'] = abs(floatval($_POST['sg_price']));
				$insert_array['sg_description'] = trim($_POST['sg_description']);
				$insert_array['sg_sort'] = trim($_POST['sg_sort']);
				$insert_array['ag_rate'] = trim($_POST['ag_rate']);
				$insert_array['sg_template'] = 'default';

				$result = $model_grade->add($insert_array);
				if ($result){
					dkcache('agent_grade');
					$this->log(L('nc_add,agent_grade').'['.$_POST['sg_name'].']',1);
					showMessage($lang['nc_common_save_succ'],'index.php?act=agent_grade&op=agent_grade');
				}else {
					showMessage($lang['nc_common_save_fail']);
				}
			}
		}
		Tpl::showpage('agent_grade.add');
	}

	/**
	 * 等级编辑
	 */
	public function agent_grade_editOp(){
		$lang	= Language::getLangContent();

		$model_grade = Model('agent_grade');
		if (chksubmit()){
			if (!$_POST['sg_id']){
				showMessage($lang['grade_parameter_error'],'index.php?act=agent_grade&op=agent_grade');
			}
			/**
			 * 验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["sg_name"], "require"=>"true", "message"=>$lang['agent_grade_name_no_null']),
				array("input"=>$_POST["ag_rate"], "require"=>"true", 'validator'=>'Number', "message"=>$lang['allow_pubilsh_product_num_only_lnteger']),
				array("input"=>$_POST["sg_sort"], "require"=>"true", 'validator'=>'Number', "message"=>$lang['sort_only_lnteger']),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {
				//如果是默认等级则级别为0
				if ($_POST['sg_id'] == 1){
					$_POST['sg_sort'] = 0;
				}
				//验证等级名称
				if (!$this->checkGradeName(array('sg_name'=>trim($_POST['sg_name']),'sg_id'=>intval($_POST['sg_id'])))){
					showMessage($lang['now_agent_grade_name_is_there'],'index.php?act=agent_grade&op=agent_grade_edit&sg_id='.intval($_POST['sg_id']));
				}
				//验证级别是否存在
				if (!$this->checkGradeSort(array('sg_sort'=>trim($_POST['sg_sort']),'sg_id'=>intval($_POST['sg_id'])))){
					showMessage($lang['add_gradesortexist'],'index.php?act=agent_grade&op=agent_grade_edit&sg_id='.intval($_POST['sg_id']));
				}
				$update_array = array();
				$update_array['sg_id'] = intval($_POST['sg_id']);
				$update_array['sg_name'] = trim($_POST['sg_name']);
//				$update_array['sg_goods_limit'] = trim($_POST['sg_goods_limit']);
//				$update_array['sg_album_limit'] = trim($_POST['sg_album_limit']);
//				$update_array['sg_function'] = $_POST['sg_function']?implode('|',$_POST['sg_function']):'';
				$update_array['sg_price'] = abs(floatval($_POST['sg_price']));
				$update_array['sg_description'] = trim($_POST['sg_description']);
				$update_array['sg_sort'] = trim($_POST['sg_sort']);
				$update_array['ag_rate'] = trim($_POST['ag_rate']);

				$result = $model_grade->update($update_array);
				if ($result){
					dkcache('agent_grade');
					$this->log(L('nc_edit,agent_grade').'['.$_POST['sg_name'].']',1);
					showMessage($lang['nc_common_save_succ']);
				}else {
					showMessage($lang['nc_common_save_fail']);
				}
			}
		}

		$grade_array = $model_grade->getOneGrade(intval($_GET['sg_id']));
		if (empty($grade_array)){
			showMessage($lang['illegal_parameter']);
		}
		//附加功能
		$grade_array['sg_function'] = explode('|',$grade_array['sg_function']);

		Tpl::output('grade_array',$grade_array);
		Tpl::showpage('agent_grade.edit');
	}

	/**
	 * 删除等级
	 */
	public function agent_grade_delOp(){
		/**
		 * 读取语言包
		 */
		$lang	= Language::getLangContent();
		$model_grade = Model('agent_grade');

		if (intval($_GET['sg_id']) > 0){
			//判断是否默认等级，默认等级不能删除
			if ($_GET['sg_id'] == 1){
				//showMessage('默认等级不能删除 ','index.php?act=agent_grade&op=agent_grade');
				showMessage($lang['default_agent_grade_no_del'],'index.php?act=agent_grade&op=agent_grade');
			}

			//判断该等级下是否存在店铺，存在的话不能删除
			if (!$this->isable_delGrade(intval($_GET['sg_id']))){
				showMessage($lang['del_gradehaveagent'],'index.php?act=agent_grade&op=agent_grade');
			}
			exit();
			/**
			 * 删除分类
			 */
			$model_grade->del(intval($_GET['sg_id']));
			dkcache('agent_grade');
			$this->log(L('nc_del,agent_grade').'[ID:'.intval($_GET['sg_id']).']',1);
			showMessage($lang['nc_common_del_succ'],'index.php?act=agent_grade&op=agent_grade');
		}else {
			showMessage($lang['nc_common_del_fail'],'index.php?act=agent_grade&op=agent_grade');
		}
	}


	/**
	 * ajax操作
	 */
	public function ajaxOp(){
		switch ($_GET['branch']){
			/**
			 * 店铺等级：验证是否有重复的名称
			 */
			case 'check_grade_name':
				if ($this->checkGradeName($_GET)){
					echo 'true'; exit;
				}else{
					echo 'false'; exit;
				}
				break;
			case 'check_grade_sort':
				if ($this->checkGradeSort($_GET)){
					echo 'true'; exit;
				}else{
					echo 'false'; exit;
				}
				break;
		}
	}
	/**
	 * 查询店铺等级名称是否存在
	 */
	private function checkGradeName($param){
		$model_grade = Model('agent_grade');
		$condition['sg_name'] = $param['sg_name'];
		$condition['no_sg_id'] = $param['sg_id'];
		$list = $model_grade->getGradeList($condition);
		if (empty($list)){
			return true;
		}else {
			return false;
		}
	}
	/**
	 * 查询店铺等级是否存在
	 */
	private function checkGradeSort($param){
		$model_grade = Model('agent_grade');
		$condition = array();
		$condition['sg_sort'] = "{$param['sg_sort']}";
		$condition['no_sg_id'] = '';
		if ($param['sg_id']){
			$condition['no_sg_id'] = "{$param['sg_id']}";
		}
		$list = array();
		$list = $model_grade->getGradeList($condition);
		if (is_array($list) && count($list)>0){
			return false;
		}else{
			return true;
		}
	}
	/**
	 * 判断店铺等级是否能删除
	 */
	public function isable_delGrade($sg_id){
		//判断该等级下是否存在店铺，存在的话不能删除
		$model_agent = Model('agent');
		$agent_list = $model_agent->getagentList(array('grade_id'=>$sg_id));
		if (count($agent_list) > 0){
			return false;
		}
		return true;
	}
}
