<?php
/**
 * 代理
 *
 *
 *
 ***/


class agent_joininControl extends SystemControl{
	public function __construct(){
		parent::__construct();
		Language::read('setting');
	}



	/**
	 * 基本设置
	 */
	public function settingOp(){
		$model_setting = Model('setting');
		if (chksubmit()){
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(

			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {
				$update_array = array();
				$update_array['agent_isuse'] = $_POST['agent_isuse'];
				$update_array['points_store_invite'] = intval($_POST['points_store_invite'])?$_POST['points_store_invite']:0;
				$result = $model_setting->updateSetting($update_array);
				if ($result === true){
					$this->log(L('nc_edit,nc_operation,nc_operation_set'),1);
					showMessage(L('nc_common_save_succ'));
				}else {
					showMessage(L('nc_common_save_fail'));
				}
			}
		}
		$list_setting = $model_setting->getListSetting();
		Tpl::output('list_setting',$list_setting);
		Tpl::showpage('agent_joinin_setting');
	}


	/**
	 * 前台头部图片传
	 */
	public function edit_infoOp() {
	    $size = 3;//上传显示图片总数
	    $i = 1;
	    $info['pic'] = array();
	    $info['show_txt'] = '';
	    $model_setting = Model('setting');
	    $code_info = $model_setting->getRowSetting('agent_joinin_pic');
	    if(!empty($code_info['value'])) {
	        $info = unserialize($code_info['value']);
	    }
	    if (chksubmit()) {
	        $info['show_txt'] = $_POST['show_txt'];
    	    for ($i;$i <= $size;$i++) {
    	        $file = 'pic'.$i;
    	        $info['pic'][$i] = $_POST['show_pic'.$i];
        		if (!empty($_FILES[$file]['name'])) {//上传图片
        		    $filename_tmparr = explode('.', $_FILES[$file]['name']);
        		    $ext = end($filename_tmparr);
        		    $file_name = 'agent_joinin_'.$i.'.'.$ext;
        			$upload = new UploadFile();
        			$upload->set('default_dir',ATTACH_COMMON);
        			$upload->set('file_name',$file_name);
        			$result = $upload->upfile($file);
        			if ($result) {
        			    $info['pic'][$i] = $file_name;
        			}
        		}
    	    }
    	    $code_info = serialize($info);

    	    $update_array = array();
    	    $update_array['agent_joinin_pic'] = $code_info;
    	    $result = $model_setting->updateSetting($update_array);
    	    showMessage(Language::get('nc_common_save_succ'),'index.php?act=agent_joinin&op=edit_info');
	    }
		Tpl::output('size',$size);
		Tpl::output('pic',$info['pic']);
		Tpl::output('show_txt',$info['show_txt']);
		Tpl::showpage('agent_joinin_pic');
	}


	/**
	 * 入驻指南
	 *
	 */
    public function help_listOp() {
		$model_help = Model('help');
		$condition = array();
		$condition['type_id'] = '20'; // 加盟文章分类 cary
		$help_list = $model_help->getStoreHelpList($condition);
		Tpl::output('help_list',$help_list);
		Tpl::showpage('agent_joinin_help');
    }

	/**
	 * 编辑入驻指南
	 *
	 */
	public function edit_helpOp() {
		$model_help = Model('help');
		$condition = array();
		$help_id = intval($_GET['help_id']);
		$condition['help_id'] = $help_id;
		$help_list = $model_help->getStoreHelpList($condition);
		$help = $help_list[0];
		Tpl::output('help',$help);
		if (chksubmit()) {
		    $help_array = array();
		    $help_array['help_title'] = $_POST['help_title'];
		    $help_array['help_info'] = $_POST['content'];
		    $help_array['help_sort'] = intval($_POST['help_sort']);
		    $help_array['update_time'] = time();
		    $state = $model_help->editHelp($condition, $help_array);
			if ($state) {
			    $this->log('编辑代理加盟指南，编号'.$help_id);
				showMessage(Language::get('nc_common_save_succ'),'index.php?act=agent_joinin&op=help_list');
			} else {
				showMessage(Language::get('nc_common_save_fail'));
			}
		}
		$condition = array();
		$condition['item_id'] = $help_id;
		$pic_list = $model_help->getHelpPicList($condition);
		Tpl::output('pic_list',$pic_list);
	    Tpl::showpage('agent_joinin_help.edit');
	}

	/**
	 * 上传图片
	 */
	public function upload_picOp() {
	    $data = array();
		if (!empty($_FILES['fileupload']['name'])) {//上传图片
		    $fprefix = 'help_store';
			$upload = new UploadFile();
			$upload->set('default_dir',ATTACH_ARTICLE);
			$upload->set('fprefix',$fprefix);
			$upload->upfile('fileupload');
		    $model_upload = Model('upload');
		    $file_name = $upload->file_name;
		    $insert_array = array();
		    $insert_array['file_name'] = $file_name;
		    $insert_array['file_size'] = $_FILES['fileupload']['size'];
		    $insert_array['upload_time'] = time();
		    $insert_array['item_id'] = intval($_GET['item_id']);
		    $insert_array['upload_type'] = '2';
		    $result = $model_upload->add($insert_array);
			if ($result) {
			    $data['file_id'] = $result;
			    $data['file_name'] = $file_name;
			}
		}
	    echo json_encode($data);exit;
	}

	/**
	 * 删除图片
	 */
	public function del_picOp() {
		$condition = array();
		$condition['upload_id'] = intval($_GET['file_id']);
	    $model_help = Model('help');
	    $state = $model_help->delHelpPic($condition);
		if ($state) {
		    echo 'true';exit;
		} else {
		    echo 'false';exit;
		}
	}
}
