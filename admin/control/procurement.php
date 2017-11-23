<?php
/**
 * 系统文章管理
 *
 *
 *
 ***/


class procurementControl extends SystemControl{
	public function __construct(){
		parent::__construct();
		Language::read('procurement');
	}

	/**
	 * 系统文章管理首页
	 */
	public function indexOp(){
		$this->procurementOp();
		exit;
	}

	/**
	 * 系统文章列表
	 */
	public function procurementOp(){
		$model_pur	= Model('purchase_rule');
		$pur_list	= $model_pur->getList();
		Tpl::output('pur_list',$pur_list);
		Tpl::showpage('procurement.index');
	}

	/**
	 * 系统文章编辑
	 */
	public function editOp(){
    		$lang	= Language::getLangContent();
		/**
		 * 更新
		 */
		if(chksubmit()){
			/**
			 * 验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["pur_title"], "require"=>"true", "message"=>$lang['procurement_index_title_null']),
				array("input"=>$_POST["pur_content"], "require"=>"true", "message"=>$lang['procurement_index_content_null']),
                                array("input"=>$_POST["pur_publish_department"],"require"=>"true",  "message"=>"发布部门不能为空"),
                                array("input"=>$_POST["pur_publish_department"],"require"=>"true",  "message"=>"制度适用人员不能为空")
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {

				$param	= array();
				$param['purchase_rule_id']	= intval($_POST['pur_id']);
				$param['title']	= trim($_POST['pur_title']);
				$param['content']= htmlspecialchars_decode(trim($_POST['pur_content']));
                                $param['publish_department']= trim($_POST['pur_publish_department']);
                                $param['object_person']= trim($_POST['pur_object_person']);
				$param['publish_date']	= time();
                                $param['attachment']   = $_POST['file_id'][0];
				$model_pur	= Model('purchase_rule');

				$result	= $model_pur->update($param);                

				if ($result){
					/**
					 * 更新图片信息ID
					 */
					$model_upload = Model('upload');
					if (is_array($_POST['file_id'])){
						foreach ($_POST['file_id'] as $k => $v){
							$v = intval($v);
							$update_array = array();
							$update_array['upload_id'] = $v;
							$update_array['item_id'] = intval($_POST['pur_id']);
							$model_upload->update($update_array);
							unset($update_array);
						}
					}

					$url = array(
						array(
							'url'=>'index.php?act=procurement&op=procurement',
							'msg'=>$lang['procurement_edit_back_to_list']
						),
						array(
							'url'=>'index.php?act=procurement&op=edit&purchase_rule_id='.intval($_POST['pur_id']),
							'msg'=>$lang['procurement_edit_again']
						),
					);
					$this->log(L('nc_edit,procurement_index_procurement').'[ID:'.$_POST['pur_id'].']',1);
					showMessage($lang['nc_common_save_succ'],$url);
				}else {
					showMessage($lang['nc_common_save_fail']);
				}
			}
		}
		/**
		 * 编辑
		 */
		if(empty($_GET['purchase_rule_id'])){
			showMessage($lang['miss_argument']);
		}
		$model_pur	= Model('purchase_rule');
		$pur	= $model_pur->getOneById(intval($_GET['purchase_rule_id']));

		/**
		 * 模型实例化
		 */
		$model_upload = Model('upload');
		$condition['upload_type'] = '7';
		$condition['item_id'] = $pur['purchase_rule_id'];
		$file_upload = $model_upload->getUploadList($condition);
		if (is_array($file_upload)){
				$file_upload[0]['upload_path'] = UPLOAD_SITE_URL.'/'.ATTACH_ARTICLE.'/'.$file_upload[0]['file_name'];
                                $file_upload[0]['item_id'] = $pur['purchase_rule_id'];
		}
                $file_upload = $file_upload[0];
		Tpl::output('PHPSESSID',session_id());
		Tpl::output('file_upload',$file_upload);
		Tpl::output('pur',$pur);
		Tpl::showpage('procurement.edit');
	}

	/**
	 * 图片或PDF上传
	 */
	public function procurement_pic_uploadOp(){
	    /**         
	     * 上传图片 控制上传文件大小要在php.ini中设置默认值 默认为2M
	     */
            
            if($_FILES['fileupload']['size']>5242880 ||$_FILES['fileupload']['size']==0){
                $data =array();
                $data['msg'] = "上传文件过大";
                $data['state'] = "1";
                $output = json_encode($data);
                echo $output;
                exit;
            }
            
	    $upload = new UploadFile();
	    $upload->set('default_dir',ATTACH_ARTICLE);
            $type=$_FILES['fileupload']['type'];
            //if($type=="application/pdf"){
                $result = $upload->upfile_all('fileupload');
           // }else{
               // $result = $upload->upfile('fileupload');
           // }
            
	   
	    if ($result){
	        $_POST['pic'] = $upload->file_name;
	    }else {
	        echo 'error';exit;
	    }
	    /**
	     * 模型实例化
	     */
//	    $model_upload = Model('upload');
	    /**
	     * 图片数据入库
	    */
	    $insert_array = array();
	    $insert_array['file_name'] = $_POST['pic'];
	    $insert_array['upload_type'] = '7';
	    $insert_array['file_size'] = $_FILES['fileupload']['size'];
	    $insert_array['item_id'] = intval($_GET['item_id']);
	    $insert_array['upload_time'] = time();
            $is_in_where['item_id']  = intval($_GET['item_id']);
            $model = Model();
            $is_in = $model->table('upload')->where($is_in_where)->find();
            if($is_in != false){
                $result_in = $model->table('upload')->where($is_in_where)->update($insert_array);
                if($result_in != false){
                    $result = $insert_array['item_id'];
                }
            }else{
                $result = $model->table('upload')->where($is_in_where)->insert($insert_array);
            }
	    if ($result){
	        $data = array();
                $data['item_id']= intval($_GET['item_id']);
	        $data['file_id'] = $result;
	        $data['file_name'] = $_POST['pic'];
	        $data['file_path'] = $_POST['pic'];
                $data['state'] = "0";
	        /**
	         * 整理为json格式
	         */
	        $output = json_encode($data);
	        echo $output;
	    }

	}
	/**
	 * ajax操作
	 */
	public function ajaxOp(){
		switch ($_GET['branch']){
			/**
			 * 删除文章图片
			 */
			case 'del_file_upload':
				if (intval($_GET['file_id']) > 0){
					$model_upload = Model('upload');
					/**
					 * 删除图片
					 */
					$file_array = $model_upload->getOneUpload(intval($_GET['file_id']));
					@unlink(BASE_UPLOAD_PATH.DS.ATTACH_ARTICLE.DS.$file_array['file_name']);
					/**
					 * 删除信息
					 */
					$model_upload->del(intval($_GET['file_id']));
                                        //去掉对应的文章附件字段的值
                                        $update['attachment'] = "";
                                        $resutl =  Model()->table("purchase_rule")->where(array("purchase_rule_id"=>$_GET['item_id']))->update($update);
					echo 'true';exit;
				}else {
					echo 'false';exit;
				}
				break;
		}
	}
        /**
         * 新增操作
         */
        public function addOp(){
            $lang	= Language::getLangContent();
            //获取admin登陆用户的信息
            $user = unserialize(decrypt(cookie('sys_key'),MD5_KEY));
            $city_name = Model()->table('city_centre')->field('city_name')->where(array("id"=>$user['cityid']))->find();
            $city_name = $city_name['city_name'] == "" ?"N":$city_name['city_name'];
		/**
		 * 更新
		 */
		if(chksubmit()){
			/**
			 * 验证
			 */
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["pur_title"], "require"=>"true", "message"=>$lang['procurement_index_title_null']),
				array("input"=>$_POST["pur_content"], "require"=>"true", "message"=>$lang['procurement_index_content_null']),
                                array("input"=>$_POST["pur_publish_department"],"require"=>"true",  "message"=>"发布部门不能为空"),
                                array("input"=>$_POST["pur_publish_department"],"require"=>"true",  "message"=>"制度适用人员不能为空")
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {         
				$param	= array();
				$param['title']	= trim($_POST['pur_title']);
				$param['content']= htmlspecialchars_decode(trim($_POST['pur_content']));
                                $param['publish_department']= trim($_POST['pur_publish_department']);
                                $param['object_person']= trim($_POST['pur_object_person']);
				$param['publish_date']	= time();
                                $param['attachment']   = $_POST['file_id'][0];
                                $param['admin_id']  = $user['id'];
                                $param['city_id']  = $user['cityid'];
                                $param['city_name']  = $city_name;
				$model_pur	= Model('purchase_rule');
                              
				$result	= $model_pur->insert($param);               
				if ($result){
					/**
					 * 更新图片信息ID
					 */
					$model_upload = Model('upload');
                                        
					if (is_array($_POST['file_id'])){
						foreach ($_POST['file_id'] as $k => $v){
							$v = intval($v);
							$update_array = array();
							$update_array['upload_id'] = $v;
							$update_array['item_id'] = $result;
							$model_upload->update($update_array);
							unset($update_array);
						}
					}

					$url = array(
						array(
							'url'=>'index.php?act=procurement&op=procurement',
							'msg'=>$lang['procurement_edit_back_to_list']
						),
						array(
							'url'=>'index.php?act=procurement&op=edit&purchase_rule_id='.intval($_POST['pur_id']),
							'msg'=>$lang['procurement_edit_again']
						),
					);
					$this->log(L('nc_edit,procurement_index_procurement').'[ID:'.$_POST['pur_id'].']',1);
					showMessage($lang['nc_common_save_succ'],$url);
				}else {
					showMessage($lang['nc_common_save_fail']);
				}
			}
		}
                //点击新增时显示显示发布部门和制度适用人员
                
                $pur = array(
                    "publish_department"=>$city_name
                );
                Tpl::output('PHPSESSID',session_id());
                Tpl::output('pur',$pur);
		Tpl::showpage('procurement.add');
        }
        /**
         * 下载链接
         */
        public function downloadOp(){
            $model_upload = Model('upload');
            $condition['upload_type'] = '7';
            $condition['item_id'] = $_GET['purchase_rule_id'];
            $file_upload = $model_upload->getUploadList($condition);
		if (is_array($file_upload)){
			foreach ($file_upload as $k => $v){
				$file_upload[$k]['upload_path'] = BASE_UPLOAD_PATH.DS.ATTACH_ARTICLE.DS;
                                $this->down_file($file_upload[$k]['file_name'], $file_upload[$k]['upload_path']);
			}
		}
            
        }
        
        function down_file($file_name,$file_sub_dir){

        //原因 php文件函数，比较古老，需要对中文转码 gb2312
//        $file_name=iconv("utf-8","gb2312",$file_name);

        //绝对路径
        $file_path=$file_sub_dir.$file_name;
        //1.打开文件
        if(!file_exists($file_path)){
        echo "文件不存在!";
        return ;
        }
     
        $fp=fopen($file_path,"r");
        //2.处理文件
        //获取下载文件的大小
        $file_size=filesize($file_path);

        //返回的文件
        header("Content-type: application/octet-stream");
        //按照字节大小返回
        header("Accept-Ranges: bytes");
        //返回文件大小
        header("Accept-Length: $file_size");
        //这里客户端的弹出对话框，对应的文件名
        header("Content-Disposition: attachment; filename=".$file_name);


        //向客户端回送数据

        $buffer=1024;
        //为了下载的安全，我们最好做一个文件字节读取计数器
        $file_count=0;
        //这句话用于判断文件是否结束
        while(!feof($fp) && ($file_size-$file_count>0) ){
        $file_data=fread($fp,$buffer);
        //统计读了多少个字节
        $file_count+=$buffer;
        //把部分数据回送给浏览器;
        echo $file_data;
        }
        
        }
}