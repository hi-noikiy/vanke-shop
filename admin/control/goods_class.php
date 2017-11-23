<?php
/**
 * 商品分类管理
 *
 *
 *
 ***/


class goods_classControl extends SystemControl{
	private $links = array(
		array('url'=>'act=goods_class&op=goods_class','lang'=>'nc_manage'),
		array('url'=>'act=goods_class&op=goods_class_add','lang'=>'nc_new'),
		array('url'=>'act=goods_class&op=goods_class_export','lang'=>'goods_class_index_export'),
		array('url'=>'act=goods_class&op=goods_class_import','lang'=>'goods_class_index_import'),
		array('url'=>'act=goods_class&op=tag','lang'=>'goods_class_index_tag'),
	);
	public function __construct(){
		parent::__construct();
		Language::read('goods_class');
	}

	/**
	 * 分类管理
	 */
	public function goods_classOp(){
		$lang	= Language::getLangContent();
		$model_class = Model('goods_class');
		if (chksubmit()){
			//删除
			if ($_POST['submit_type'] == 'del'){
			    $gcids = implode(',', $_POST['check_gc_id']);
				if (!empty($_POST['check_gc_id'])){
					if (!is_array($_POST['check_gc_id'])){
    					$this->log(L('nc_delete,goods_class_index_class').'[ID:'.$gcids.']',0);
    					showMessage($lang['nc_common_del_fail']);
					}
					$del_array = $model_class->delGoodsClassByGcIdString($gcids);
					$this->log(L('nc_delete,goods_class_index_class').'[ID:'.$gcids.']',1);
					showMessage($lang['nc_common_del_succ']);
				}else {
					$this->log(L('nc_delete,goods_class_index_class').'[ID:'.$gcids.']',0);
					showMessage($lang['nc_common_del_fail']);
				}
			}
		}

		//父ID
		$parent_id = $_GET['gc_parent_id']?intval($_GET['gc_parent_id']):0;

		//列表
		$tmp_list = $model_class->getTreeClassList(3);
		if (is_array($tmp_list)){
			foreach ($tmp_list as $k => $v){
				if ($v['gc_parent_id'] == $parent_id){
					//判断是否有子类
					if ($tmp_list[$k+1]['deep'] > $v['deep']){
						$v['have_child'] = 1;
					}
					$class_list[] = $v;
				}
			}
		}
		if ($_GET['ajax'] == '1'){
			//转码
			if (strtoupper(CHARSET) == 'GBK'){
				$class_list = Language::getUTF8($class_list);
			}
			$output = json_encode($class_list);
			print_r($output);
			exit;
		}else {
			Tpl::output('class_list',$class_list);
			Tpl::output('top_link',$this->sublink($this->links,'goods_class'));
			Tpl::showpage('goods_class.index');
		}
	}

	/**
	 * 商品分类添加
	 */
	public function goods_class_addOp(){
		$lang	= Language::getLangContent();
		$model_class = Model('goods_class');
		if (chksubmit()){
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["gc_name"], "require"=>"true", "message"=>$lang['goods_class_add_name_null']),
				array("input"=>$_POST["gc_sort"], "require"=>"true", 'validator'=>'Number', "message"=>$lang['goods_class_add_sort_int']),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}else {
                            $code = $this->getMaxGoodsClassCodeOp($_POST['gc_parent_id']);
                            if($code=="0"){
                               showMessage("当前父级分类不存在且不是追加第一级分类，新增分类失败"); 
                            }
                            if($code=="24"||$code=="100"){
                                 showMessage("当前父级分类超过允许的最大子分类".$code."个，新增分类失败");
                            }else{
                                $_POST['class_code'] = $code;
                            }
                            
                                if($_POST['class_code'] == ''){
                                    showMessage("请添加商品分类Code");
                                }
				$insert_array = array();
				$insert_array['gc_name']		= $_POST['gc_name'];
				$insert_array['type_id']		= intval($_POST['t_id']);
				$insert_array['type_name']		= trim($_POST['t_name']);
				$insert_array['gc_parent_id']	= intval($_POST['gc_parent_id']);
				$insert_array['commis_rate']    = intval($_POST['commis_rate']);
				$insert_array['gc_sort']		= intval($_POST['gc_sort']);
                                $insert_array['gc_virtual']     = intval($_POST['gc_virtual']);
                                $insert_array['gc_class_code']		= $_POST['class_code'];
                                
				$result = $model_class->addGoodsClass($insert_array);
				if ($result){
    				if ($insert_array['gc_parent_id'] == 0) {
            			if (!empty($_FILES['pic']['name'])) {//上传图片
            				$upload = new UploadFile();
                			$upload->set('default_dir',ATTACH_COMMON);
                			$upload->set('file_name','category-pic-'.$result.'.jpg');
            				$upload->upfile('pic');
            			}
    				}

					//将新增的分类推送到采购系统@author lwl
					//product_category_code,description,from_product_category_code
					//glass_state（0失效/删除1追加2更新）
					try{
						$out_good_Class =  array();
						$out_good_Class['product_category_code']		= $insert_array['gc_class_code']	;
						$out_good_Class['description']		= $insert_array['gc_name']	;
						$out_good_Class['glass_state']		= '1';
						if($insert_array['gc_parent_id'] == '0'){
							$out_good_Class['from_product_category_code']		= "0";
						}else{
							$out_good_Class['from_product_category_code']		= $insert_array['gc_parent_id'];
						}
						$this->pushAGoodsClassToVS($out_good_Class);
					}catch (Exception $exc){
						log::record4inter($insert_array['product_category_code'].$lang['goods_class_push_to_fail'].$exc->getMessage(), log::ERR);
					}

					$url = array(
						array(
							'url'=>'index.php?act=goods_class&op=goods_class_add&gc_parent_id='.$_POST['gc_parent_id'],
							'msg'=>$lang['goods_class_add_again'],
						),
						array(
							'url'=>'index.php?act=goods_class&op=goods_class',
							'msg'=>$lang['goods_class_add_back_to_list'],
						)
					);
					$this->log(L('nc_add,goods_class_index_class').'['.$_POST['gc_name'].']',1);
					showMessage($lang['nc_common_save_succ'],$url);
				}else {
					$this->log(L('nc_add,goods_class_index_class').'['.$_POST['gc_name'].']',0);
					showMessage($lang['nc_common_save_fail']);
				}
			}
		}

		//父类列表，只取到第二级
		$parent_list = $model_class->getTreeClassList(2);
		$gc_list = array();
		if (is_array($parent_list)){
			foreach ($parent_list as $k => $v){
				$parent_list[$k]['gc_name'] = str_repeat("&nbsp;",$v['deep']*2).$v['gc_name'];
				if($v['deep'] == 1) $gc_list[$k] = $v;
			}
		}
		Tpl::output('gc_list', $gc_list);
		//类型列表
		$model_type	= Model('type');
		$type_list	= $model_type->typeList(array('order'=>'type_sort asc'), '', 'type_id,type_name,class_id,class_name');
		$t_list = array();
		if(is_array($type_list) && !empty($type_list)){
			foreach($type_list as $k=>$val){
				$t_list[$val['class_id']]['type'][$k] = $val;
				$t_list[$val['class_id']]['name'] = $val['class_name']==''?L('nc_default'):$val['class_name'];
			}
		}
		ksort($t_list);


		Tpl::output('type_list',$t_list);
		Tpl::output('gc_parent_id',$_GET['gc_parent_id']);
		Tpl::output('parent_list',$parent_list);
		Tpl::output('top_link',$this->sublink($this->links,'goods_class_add'));
		Tpl::showpage('goods_class.add');
	}

	/**
	 * 编辑
	 */
	public function goods_class_editOp(){
		$lang	= Language::getLangContent();
		$model_class = Model('goods_class');

		if (chksubmit()){
			$obj_validate = new Validate();
			$obj_validate->validateparam = array(
				array("input"=>$_POST["gc_name"], "require"=>"true", "message"=>$lang['goods_class_add_name_null']),
			    array("input"=>$_POST["commis_rate"], "require"=>"true", 'validator'=>'range','max'=>100,'min'=>0, "message"=>$lang['goods_class_add_commis_rate_error']),
				array("input"=>$_POST["gc_sort"], "require"=>"true", 'validator'=>'Number', "message"=>$lang['goods_class_add_sort_int']),
			);
			$error = $obj_validate->validate();
			if ($error != ''){
				showMessage($error);
			}

			// 更新分类信息
			$where = array('gc_id' => intval($_POST['gc_id']));
			//$where = array('gc_id' => 'eeeee');
			$update_array = array();
			$update_array['gc_name'] 		= $_POST['gc_name'];
			$update_array['type_id']		= intval($_POST['t_id']);
			$update_array['type_name']		= trim($_POST['t_name']);
			$update_array['commis_rate']    = intval($_POST['commis_rate']);
			$update_array['gc_sort']		= intval($_POST['gc_sort']);
                        $update_array['gc_class_code']		= $_POST['class_code'];
            $update_array['gc_virtual']     = intval($_POST['gc_virtual']);
            //
			$update_array['gc_parent_id']	= intval($_POST['gc_parent_id']);

			//判断分类名称是否修改。若修改则需要推送给采购系统
			$out_good_class = $model_class->table("goods_class")->field('gc_class_code as product_category_code,gc_name as description,gc_parent_id as from_product_category_code')->Where($where)->find();
			if((!empty( $out_good_class) ) && $out_good_class['description'] != $update_array['gc_name']){
				$out_good_class['glass_state']		= '2';
				$out_good_class['description'] = $update_array['gc_name'];
				try{
					$this->pushAGoodsClassToVS($out_good_class);
				} catch (Exception $exc) {
					log::record4inter(json_encode($out_good_class,true) . $lang['goods_class_push_to_fail'] . $exc->getMessage(), log::ERR);
				}
			}

			$result = $model_class->editGoodsClass($update_array, $where);
			if (!$result){
				$this->log(L('nc_edit,goods_class_index_class').'['.$_POST['gc_name'].']',0);
				showMessage($lang['goods_class_batch_edit_fail']);
			}

			if (!empty($_FILES['pic']['name'])) {//上传图片
				$upload = new UploadFile();
    			$upload->set('default_dir',ATTACH_COMMON);
    			$upload->set('file_name','category-pic-'.intval($_POST['gc_id']).'.jpg');
				$upload->upfile('pic');
			}

            // 检测是否需要关联自己操作，统一查询子分类
            if ($_POST['t_commis_rate'] == '1' || $_POST['t_associated'] == '1' || $_POST['t_gc_virtual'] == '1') {
                $gc_id_list = $model_class->getChildClass($_POST['gc_id']);
                $gc_ids = array();
                if (is_array($gc_id_list) && !empty($gc_id_list)) {
                    foreach ($gc_id_list as $val){
                        $gc_ids[] = $val['gc_id'];
                    }
                }
            }

			// 更新该分类下子分类的所有分佣比例
			if ($_POST['t_commis_rate'] == '1' && !empty($gc_ids)){
	            $model_class->editGoodsClass(array('commis_rate'=>$update_array['commis_rate']),array('gc_id'=>array('in',$gc_ids)));
			}

			// 更新该分类下子分类的所有类型
			if ($_POST['t_associated'] == '1' && !empty($gc_ids)){
			    $where = array();
			    $where['gc_id'] = array('in', $gc_ids);
			    $update = array();
			    $update['type_id'] = intval($_POST['t_id']);
			    $update['type_name'] = trim($_POST['t_name']);
			    $model_class->editGoodsClass($update, $where);
			}

            // 虚拟商品
            if ($_POST['t_gc_virtual'] == '1' && !empty($gc_ids)) {
                $model_class->editGoodsClass(array('gc_virtual'=>$update_array['gc_virtual']),array('gc_id'=>array('in',$gc_ids)));
            }

			$url = array(
				array(
					'url'=>'index.php?act=goods_class&op=goods_class_edit&gc_id='.intval($_POST['gc_id']),
					'msg'=>$lang['goods_class_batch_edit_again'],
				),
				array(
					'url'=>'index.php?act=goods_class&op=goods_class',
					'msg'=>$lang['goods_class_add_back_to_list'],
				)
			);
			$this->log(L('nc_edit,goods_class_index_class').'['.$_POST['gc_name'].']',1);
			showMessage($lang['goods_class_batch_edit_ok'],$url,'html','succ',1,5000);
		}

		$class_array = $model_class->getGoodsClassInfoById(intval($_GET['gc_id']));
		if (empty($class_array)){
			showMessage($lang['goods_class_batch_edit_paramerror']);
		}

		//类型列表
		$model_type	= Model('type');
		$type_list	= $model_type->typeList(array('order'=>'type_sort asc'), '', 'type_id,type_name,class_id,class_name');
		$t_list = array();
		if(is_array($type_list) && !empty($type_list)){
			foreach($type_list as $k=>$val){
				$t_list[$val['class_id']]['type'][$k] = $val;
				$t_list[$val['class_id']]['name'] = $val['class_name']==''?L('nc_default'):$val['class_name'];
			}
		}
		ksort($t_list);
		//父类列表，只取到第二级
		$parent_list = $model_class->getTreeClassList(2);
		if (is_array($parent_list)){
			foreach ($parent_list as $k => $v){
				$parent_list[$k]['gc_name'] = str_repeat("&nbsp;",$v['deep']*2).$v['gc_name'];
			}
		}
		Tpl::output('parent_list',$parent_list);
		// 一级分类列表
		$gc_list = Model('goods_class')->getGoodsClassListByParentId(0);
		Tpl::output('gc_list', $gc_list);
	    $pic_name = BASE_UPLOAD_PATH.'/'.ATTACH_COMMON.'/category-pic-'.$class_array['gc_id'].'.jpg';
	    if (file_exists($pic_name)) {
	        $class_array['pic'] = UPLOAD_SITE_URL.'/'.ATTACH_COMMON.'/category-pic-'.$class_array['gc_id'].'.jpg';
	    }

		Tpl::output('type_list',$t_list);
		Tpl::output('class_array',$class_array);
		$this->links[] = array('url'=>'act=goods_class&op=goods_class_edit','lang'=>'nc_edit');
		Tpl::output('top_link',$this->sublink($this->links,'goods_class_edit'));
		Tpl::showpage('goods_class.edit');
	}

	/**
	 * 分类导入
	 */
	public function goods_class_importOp(){
		$lang	= Language::getLangContent();
		$model_class = Model('goods_class');
		//导入
		if (chksubmit()){
			//得到导入文件后缀名
			$csv_array = explode('.',$_FILES['csv']['name']);
			$file_type = end($csv_array);
			if (!empty($_FILES['csv']) && !empty($_FILES['csv']['name']) && $file_type == 'csv'){
				$fp = @fopen($_FILES['csv']['tmp_name'],'rb');
				// 父ID
				$parent_id_1 = 0;

				while (!feof($fp)) {
					$data = fgets($fp, 4096);
					switch (strtoupper($_POST['charset'])){
						case 'UTF-8':
							if (strtoupper(CHARSET) !== 'UTF-8'){
								$data = iconv('UTF-8',strtoupper(CHARSET),$data);
							}
							break;
						case 'GBK':
							if (strtoupper(CHARSET) !== 'GBK'){
								$data = iconv('GBK',strtoupper(CHARSET),$data);
							}
							break;
					}

					if (!empty($data)){
						$data	= str_replace('"','',$data);
						//逗号去除
						$tmp_array = array();
						$tmp_array = explode(',',$data);
						if($tmp_array[0] == 'sort_order')continue;
						//第一位是序号，后面的是内容，最后一位名称
						$tmp_deep = 'parent_id_'.(count($tmp_array)-1);

						$insert_array = array();
						$insert_array['gc_sort'] = $tmp_array[0];
						$insert_array['gc_parent_id'] = $$tmp_deep;
						$insert_array['gc_name'] = $tmp_array[count($tmp_array)-1];
						$gc_id = $model_class->addGoodsClass($insert_array);
						//赋值这个深度父ID
						$tmp = 'parent_id_'.count($tmp_array);
						$$tmp = $gc_id;
					}
				}
				$this->log(L('goods_class_index_import,goods_class_index_class'),1);
				showMessage($lang['nc_common_op_succ'],'index.php?act=goods_class&op=goods_class');
			}else {
				$this->log(L('goods_class_index_import,goods_class_index_class'),0);
				showMessage($lang['goods_class_import_csv_null']);
			}
		}
		Tpl::output('top_link',$this->sublink($this->links,'goods_class_import'));
		Tpl::showpage('goods_class.import');
	}

	/**
	 * 分类导出
	 */
	public function goods_class_exportOp(){
		if (chksubmit()){
			$model_class = Model('goods_class');
			$class_list = $model_class->getTreeClassList();

			@header("Content-type: application/unknown");
        	@header("Content-Disposition: attachment; filename=goods_class.csv");
			if (is_array($class_list)){
				foreach ($class_list as $k => $v){
					$tmp = array();
					//序号
					$tmp['gc_sort'] = $v['gc_sort'];
					//深度
					for ($i=1; $i<=($v['deep']-1); $i++){
						$tmp[] = '';
					}
					//分类名称
					$tmp['gc_name'] = $v['gc_name'];
					//转码 utf-gbk
					if (strtoupper(CHARSET) == 'UTF-8'){
						switch ($_POST['if_convert']){
							case '1':
								$tmp_line = iconv('UTF-8','GB2312//IGNORE',join(',',$tmp));
								break;
							case '0':
								$tmp_line = join(',',$tmp);
								break;
						}
					}else {
						$tmp_line = join(',',$tmp);
					}
					$tmp_line = str_replace("\r\n",'',$tmp_line);
					echo $tmp_line."\r\n";
				}
			}
			$this->log(L('goods_class_index_export,goods_class_index_class'),1);
			exit;
		}
		Tpl::output('top_link',$this->sublink($this->links,'goods_class_export'));
		Tpl::showpage('goods_class.export');
	}

	/**
	 * 删除分类
	 */
	public function goods_class_delOp(){
		$lang	= Language::getLangContent();
		$model_class = Model('goods_class');
		if (intval($_GET['gc_id']) > 0){

			$where = array('gc_id' => intval($_GET['gc_id']));
			$out_good_class = $model_class->table("goods_class")->field('gc_class_code as product_category_code,gc_name as description,gc_parent_id as from_product_category_code')->Where($where)->find();
			//删除分类
			$model_class->delGoodsClassByGcIdString(intval($_GET['gc_id']));

			//glass_state（0失效/删除1追加2更新）
			if(!empty( $out_good_class)  ) {
				try {
					$out_good_class['glass_state']		= '0';
					$this->pushAGoodsClassToVS($out_good_class);
				} catch (Exception $exc) {
					log::record4inter(json_encode($out_good_class,true) . $lang['goods_class_push_to_fail'] . $exc->getMessage(), log::ERR);
				}
			}
			//删除分类时，推送删除的分类给采购系统

			$this->log(L('nc_delete,goods_class_index_class') . '[ID:' . intval($_GET['gc_id']) . ']',1);
			showMessage($lang['nc_common_del_succ'],'index.php?act=goods_class&op=goods_class');
		}else {
			$this->log(L('nc_delete,goods_class_index_class') . '[ID:' . intval($_GET['gc_id']) . ']',0);
			showMessage($lang['nc_common_del_fail'],'index.php?act=goods_class&op=goods_class');
		}
	}

	/**
	 * tag列表
	 */
	public function tagOp(){
		$lang	= Language::getLangContent();

		/**
		 * 处理商品分类
		 */
		$choose_gcid = ($t = intval($_REQUEST['choose_gcid']))>0?$t:0;
		$gccache_arr = Model('goods_class')->getGoodsclassCache($choose_gcid,3);
		Tpl::output('gc_json',json_encode($gccache_arr['showclass']));
		Tpl::output('gc_choose_json',json_encode($gccache_arr['choose_gcid']));

		$model_class_tag = Model('goods_class_tag');

		if (chksubmit()){
			//删除
			if ($_POST['submit_type'] == 'del'){
				if (is_array($_POST['tag_id']) && !empty($_POST['tag_id'])){
					//删除TAG
					$model_class_tag->delTagByIds(implode(',',$_POST['tag_id']));
					$this->log(L('nc_delete').'tag[ID:'.implode(',',$_POST['tag_id']).']',1);
					showMessage($lang['nc_common_del_succ']);
				}else {
					$this->log(L('nc_delete').'tag',0);
					showMessage($lang['nc_common_del_fail']);
				}
			}
		}

		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$where = array();
		if ($choose_gcid > 0){
		    $where['gc_id_'.($gccache_arr['showclass'][$choose_gcid]['depth'])] = $choose_gcid;
		}
		$tag_list = $model_class_tag->getTagList($where, $page);
		Tpl::output('tag_list', $tag_list);
		Tpl::output('page',$page->show());
		Tpl::output('top_link',$this->sublink($this->links,'tag'));
		Tpl::showpage('goods_class_tag.index');
	}

	/**
	 * 重置TAG
	 */
	public function tag_resetOp(){
		$lang	= Language::getLangContent();
		//实例化模型
		$model_class = Model('goods_class');
		$model_class_tag = Model('goods_class_tag');

		//清空TAG
		$return = $model_class_tag->clearTag();
		if(!$return){
			showMessage($lang['goods_class_reset_tag_fail'], 'index.php?act=goods_class&op=tag');
		}

		//商品分类
		$goods_class		= $model_class->getTreeClassList(3);
		//格式化分类。组成三维数组
		if(is_array($goods_class) and !empty($goods_class)) {
			$goods_class_array = array();
			foreach ($goods_class as $val) {
				//一级分类
				if($val['gc_parent_id'] == 0) {
					$goods_class_array[$val['gc_id']]['gc_name']	= $val['gc_name'];
					$goods_class_array[$val['gc_id']]['gc_id']		= $val['gc_id'];
					$goods_class_array[$val['gc_id']]['type_id']	= $val['type_id'];
				}else {
					//二级分类
					if(isset($goods_class_array[$val['gc_parent_id']])){
						$goods_class_array[$val['gc_parent_id']]['sub_class'][$val['gc_id']]['gc_name']			= $val['gc_name'];
						$goods_class_array[$val['gc_parent_id']]['sub_class'][$val['gc_id']]['gc_id']			= $val['gc_id'];
						$goods_class_array[$val['gc_parent_id']]['sub_class'][$val['gc_id']]['gc_parent_id']	= $val['gc_parent_id'];
						$goods_class_array[$val['gc_parent_id']]['sub_class'][$val['gc_id']]['type_id']			= $val['type_id'];
					}else{
						foreach ($goods_class_array as $v){
							//三级分类
							if(isset($v['sub_class'][$val['gc_parent_id']])){
								$goods_class_array[$v['sub_class'][$val['gc_parent_id']]['gc_parent_id']]['sub_class'][$val['gc_parent_id']]['sub_class'][$val['gc_id']]['gc_name']	= $val['gc_name'];
								$goods_class_array[$v['sub_class'][$val['gc_parent_id']]['gc_parent_id']]['sub_class'][$val['gc_parent_id']]['sub_class'][$val['gc_id']]['gc_id']	= $val['gc_id'];
								$goods_class_array[$v['sub_class'][$val['gc_parent_id']]['gc_parent_id']]['sub_class'][$val['gc_parent_id']]['sub_class'][$val['gc_id']]['type_id']	= $val['type_id'];
							}
						}
					}
				}
			}

			$return = $model_class_tag->tagAdd($goods_class_array);

			if($return){
				$this->log(L('nc_reset').'tag',1);
				showMessage($lang['nc_common_op_succ'], 'index.php?act=goods_class&op=tag');
			}else{
				$this->log(L('nc_reset').'tag',0);
				showMessage($lang['nc_common_op_fail'], 'index.php?act=goods_class&op=tag');
			}
		}else{
			$this->log(L('nc_reset').'tag',0);
			showMessage($lang['goods_class_reset_tag_fail_no_class'], 'index.php?act=goods_class&op=tag');
		}
	}

	/**
	 * 更新TAG名称
	 */
	public function tag_updateOp(){
		$lang	= Language::getLangContent();
		$model_class = Model('goods_class');
		$model_class_tag = Model('goods_class_tag');

		//需要更新的TAG列表
		$tag_list = $model_class_tag->getTagList(array(), '', 'gc_tag_id,gc_id_1,gc_id_2,gc_id_3');
		if(is_array($tag_list) && !empty($tag_list)){
			foreach ($tag_list as $val){
				//查询分类信息
				$in_gc_id = array();
				if($val['gc_id_1'] != '0'){
					$in_gc_id[] = $val['gc_id_1'];
				}
				if($val['gc_id_2'] != '0'){
					$in_gc_id[] = $val['gc_id_2'];
				}
				if($val['gc_id_3'] != '0'){
					$in_gc_id[] = $val['gc_id_3'];
				}
				$gc_list	= $model_class->getGoodsClassListByIds($in_gc_id);

				//更新TAG信息
				$update_tag					= array();
				if(isset($gc_list['0']['gc_id']) && $gc_list['0']['gc_id'] != '0'){
					$update_tag['gc_id_1']		= $gc_list['0']['gc_id'];
					$update_tag['gc_tag_name']	.= $gc_list['0']['gc_name'];
				}
				if(isset($gc_list['1']['gc_id']) && $gc_list['1']['gc_id'] != '0'){
					$update_tag['gc_id_2']		= $gc_list['1']['gc_id'];
					$update_tag['gc_tag_name']	.= "&nbsp;&gt;&nbsp;".$gc_list['1']['gc_name'];
				}
				if(isset($gc_list['2']['gc_id']) && $gc_list['2']['gc_id'] != '0'){
					$update_tag['gc_id_3']		= $gc_list['2']['gc_id'];
					$update_tag['gc_tag_name']	.= "&nbsp;&gt;&nbsp;".$gc_list['2']['gc_name'];
				}
				unset($gc_list);
				$update_tag['gc_tag_id']	= $val['gc_tag_id'];
				$return = $model_class_tag->updateTag($update_tag);
				if(!$return){
					$this->log(L('nc_update').'tag',0);
					showMessage($lang['nc_common_op_fail'], 'index.php?act=goods_class&op=tag');
				}
			}
			$this->log(L('nc_update').'tag',1);
			showMessage($lang['nc_common_op_succ'], 'index.php?act=goods_class&op=tag');
		}else{
			$this->log(L('nc_update').'tag',0);
			showMessage($lang['goods_class_update_tag_fail_no_class'], 'index.php?act=goods_class&op=tag');
		}

	}

	/**
	 * 删除TAG
	 */
	public function tag_delOp(){
		$id = intval($_GET['tag_id']);
		$lang	= Language::getLangContent();
		$model_class_tag = Model('goods_class_tag');
		if ($id > 0){
			/**
			 * 删除TAG
			 */
			$model_class_tag->delTagByIds($id);
			$this->log(L('nc_delete').'tag[ID:'.$id.']',1);
			showMessage($lang['nc_common_op_succ']);
		}else {
			$this->log(L('nc_delete').'tag[ID:'.$id.']',0);
			showMessage($lang['nc_common_op_fail']);
		}
	}
	
	 /**
     * 分类导航
     */
    public function nav_editOp() {
        $gc_id = $_REQUEST['gc_id'];
        $model_goods = Model('goods_class');
        $class_info = $model_goods->getGoodsClassInfoById($gc_id);
        $model_class_nav = Model('goods_class_nav');
        $nav_info = $model_class_nav->getGoodsClassNavInfoByGcId($gc_id);
        if (chksubmit()) {
            $update = array();
            $update['gc_id'] = $gc_id;
            $update['cn_alias'] = $_POST['cn_alias'];
            if (is_array($_POST['class_id'])) {
                $update['cn_classids'] = implode(',', $_POST['class_id']);
            }else{ //@caryFix
				$update['cn_classids'] = '';
			}
            if (is_array($_POST['brand_id'])) {
                $update['cn_brandids'] = implode(',', $_POST['brand_id']);
            }
            $update['cn_adv1_link'] = $_POST['cn_adv1_link'];
            $update['cn_adv2_link'] = $_POST['cn_adv2_link'];
            if (!empty($_FILES['pic']['name'])) {//上传图片
                $upload = new UploadFile();
                @unlink(BASE_UPLOAD_PATH. '/' . ATTACH_GOODS_CLASS . '/' . $nav_info['cn_pic']);
                $upload->set('default_dir',ATTACH_GOODS_CLASS);
                $upload->upfile('pic');
                $update['cn_pic'] = $upload->file_name;
            }
            if (!empty($_FILES['adv1']['name'])) {//上传广告图片
                $upload = new UploadFile();
                @unlink(BASE_UPLOAD_PATH. '/' . ATTACH_GOODS_CLASS . '/' . $nav_info['cn_adv1']);
                $upload->set('default_dir',ATTACH_GOODS_CLASS);
                $upload->upfile('adv1');
                $update['cn_adv1'] = $upload->file_name;
            }
            if (!empty($_FILES['adv2']['name'])) {//上传广告图片
                $upload = new UploadFile();
                @unlink(BASE_UPLOAD_PATH. '/' . ATTACH_GOODS_CLASS . '/' . $nav_info['cn_adv2']);
                $upload->set('default_dir',ATTACH_GOODS_CLASS);
                $upload->upfile('adv2');
                $update['cn_adv2'] = $upload->file_name;
            }
            if (empty($nav_info)) {
                $result = $model_class_nav->addGoodsClassNav($update);
            } else {
                $result = $model_class_nav->editGoodsClassNav($update, $gc_id);
            }
            if($result){
                $this->log('编辑分类导航，'.$class_info['gc_name'],1);
                showMessage('编辑成功');
            }else{
                $this->log('编辑分类导航，'.$class_info['gc_name'],0);
                showMessage('编辑成功', '', '', 'error');
            }
        }

        $pic_name = BASE_UPLOAD_PATH . '/' . ATTACH_GOODS_CLASS . '/' . $nav_info['cn_pic'];
        if (file_exists($pic_name)) {
            $nav_info['cn_pic'] = UPLOAD_SITE_URL. '/' . ATTACH_GOODS_CLASS . '/' . $nav_info['cn_pic'];
        }
        $pic_name = BASE_UPLOAD_PATH . '/' . ATTACH_GOODS_CLASS . '/' . $nav_info['cn_adv1'];
        if (file_exists($pic_name)) {
            $nav_info['cn_adv1'] = UPLOAD_SITE_URL. '/' . ATTACH_GOODS_CLASS . '/' . $nav_info['cn_adv1'];
        }
        $pic_name = BASE_UPLOAD_PATH . '/' . ATTACH_GOODS_CLASS . '/' . $nav_info['cn_adv2'];
        if (file_exists($pic_name)) {
            $nav_info['cn_adv2'] = UPLOAD_SITE_URL. '/' . ATTACH_GOODS_CLASS . '/' . $nav_info['cn_adv2'];
        }
        $nav_info['cn_classids'] = explode(',', $nav_info['cn_classids'] );
        $nav_info['cn_brandids'] = explode(',', $nav_info['cn_brandids'] );
        Tpl::output('nav_info', $nav_info);
        Tpl::output('class_info', $class_info);
        // 一级分类列表
        $gc_list = $model_goods->getGoodsClassListByParentId(0);
        Tpl::output('gc_list', $gc_list);
    
        // 全部三级分类
        $third_class = $model_goods->getChildClassByFirstId($gc_id);
        Tpl::output('third_class', $third_class);
    
        // 品牌列表
        $model_brand    = Model('brand');
        $brand_list     = $model_brand->getBrandPassedList(array());
        $b_list = array();
        if(is_array($brand_list) && !empty($brand_list)){
            foreach($brand_list as $k=>$val){
                $b_list[$val['class_id']]['brand'][$k] = $val;
                $b_list[$val['class_id']]['name'] = $val['brand_class']==''?L('nc_default'):$val['brand_class'];
            }
        }
        ksort($b_list);
        Tpl::output('brand_list', $b_list);
    
        Tpl::showpage('goods_class.nav_edit');
    }

	/**
	 * ajax操作
	 */
	public function ajaxOp(){
		$lang	= Language::getLangContent();
		switch ($_GET['branch']){
			/**
			 * 更新分类
			 */
			case 'goods_class_name':
				$model_class = Model('goods_class');
				$class_array = $model_class->getGoodsClassInfoById(intval($_GET['id']));

				$condition['gc_name'] = trim($_GET['value']);
				$condition['gc_parent_id'] = $class_array['gc_parent_id'];
				$condition['gc_id'] = array('neq' => intval($_GET['id']));
				$class_list = $model_class->getGoodsClassList($condition);
				if (empty($class_list)){
				    $where = array('gc_id' => intval($_GET['id']));
					$update_array = array();
					$update_array['gc_name'] = trim($_GET['value']);

					$out_good_class = $model_class->table("goods_class")->field('gc_class_code as product_category_code,gc_name as description,gc_parent_id as from_product_category_code')->Where($where)->find();
					if((!empty( $out_good_class) ) && $out_good_class['description'] != $update_array['gc_name']){
						$out_good_class['glass_state']		= '2';
						$out_good_class['description'] = $update_array['gc_name'];
						try{
							$this->pushAGoodsClassToVS($out_good_class);
						} catch (Exception $exc) {
							log::record4inter(json_encode($out_good_class,true) . $lang['goods_class_push_to_fail'] . $exc->getMessage(), log::ERR);
						}
					}

					$model_class->editGoodsClass($update_array, $where);
					echo 'true';exit;
				}else {
					echo 'false';exit;
				}
				break;
			/**
			 * 分类 排序 显示 设置
			 */
			case 'goods_class_sort':
			case 'goods_class_show':
			case 'goods_class_index_show':
				$model_class = Model('goods_class');
			    $where = array('gc_id' => intval($_GET['id']));
				$update_array = array();
				$update_array[$_GET['column']] = $_GET['value'];
				$model_class->editGoodsClass($update_array, $where);
				echo 'true';exit;
				break;
			/**
			 * 添加、修改操作中 检测类别名称是否有重复
			 */
			case 'check_class_name':
				$model_class = Model('goods_class');
				$condition['gc_name'] = trim($_GET['gc_name']);
				$condition['gc_parent_id'] = intval($_GET['gc_parent_id']);
				$condition['gc_id'] = array('neq', intval($_GET['gc_id']));
				$class_list = $model_class->getGoodsClassList($condition);
				if (empty($class_list)){
					echo 'true';exit;
				}else {
					echo 'false';exit;
				}
				break;
			/**
			 * TAG值编辑
			 */
			case 'goods_class_tag_value':
				$model_class_tag = Model('goods_class_tag');
				$update_array = array();
				$update_array['gc_tag_id'] = intval($_GET['id']);
				/**
				 * 转码  防止GBK下用中文逗号截取不正确
				 */
				$comma = '，';
				if (strtoupper(CHARSET) == 'GBK'){
					$comma = Language::getGBK($comma);
				}
				$update_array[$_GET['column']] = trim(str_replace($comma,',',$_GET['value']));
				$model_class_tag->updateTag($update_array);
				echo 'true';exit;
				break;
		}
	}



	/**
	 * 分类是否可删除校验，若该分类下有子分类则不删除。无子分类，且当前分类没有跟商品绑定也不能删除。
	 */
	public function goods_class_checkOp(){
		$lang	= Language::getLangContent();
		$resultArray = array();
		$resultArray['isDel']="00";//00表示不能删除，-1表示这个分类不存在，01表示可以删除
		$resultArray['msg']="";
		$model = Model();
		$currentGoodsClass = $model->table("goods_class")->where(array('gc_id'=>$_GET['gc_id']))->select();
		if(!empty($currentGoodsClass)){//当前分类存在

			$tmpArray = $model->table("goods_class")->where(array('gc_parent_id'=>$_GET['gc_id']))->limit(1)->select();
			if(empty($tmpArray)){//该分类下没有子分类

				$tmpArrayByGc_id = $model->table("goods")->where(array('gc_id'=>$_GET['gc_id']))->limit(1)->select();
				if(empty($tmpArrayByGc_id)){//没有商品跟当前分类绑定

					$resultArray['isDel']="01";
				}else{//没有商品跟当前分类绑定
					$resultArray['msg']=$currentGoodsClass[0]['gc_name'].$lang['goods_class_index_del_tip01'];
				}
			}else{//该分类下有子分类
				$resultArray['msg']=$currentGoodsClass[0]['gc_name'].$lang['goods_class_index_del_tip02'];
			}
		}else{//当前分类不存在
			$resultArray['isDel']="-1";
			$resultArray['msg']=$lang['goods_class_index_del_tip03'];
		}

		echo json_encode($resultArray,true);
	}

	/**
	 *
	 * 推送所有商品分类到后台的采购系统
	 */
	public function pushGoodsClassToVSOp($goodsClass ){
		$lang	= Language::getLangContent();
		$resultArray = array(
				'resultCode'=>'0',
				'resultMsg='=>$lang['goods_class_push_to_sucess'],
				'resultData'=>''
		);//0表示推送成功，-1表示推送失败
		$model = Model();//product_category_code,description,from_product_category_code
		$GoodsClass = $model->table("goods_class")->field('gc_id as product_category_code,gc_name as description,gc_parent_id as from_product_category_code')->select();
		if(!empty($goodsClass)) {//没有分类
			try{
				$goodsClass = json_encode($goodsClass);
				//$url = YMA_WEBSERVICE_URL_HEAD."/impac/services/insertAndUpdate/insertAndUpdateProductCategory";
				$url=YMA_WEBSERVICE_INSERT_AND_UPDATE_PRODUCT_CATEGORY;
                                $resultArray = WebServiceUtil::getDataByCurl($url, $goodsClass, 0);
				$resultArray = json_decode($resultArray,treu);
				if($resultArray['resultCode']=='0'){
					log::record4inter($lang['goods_class_push_to_sucess'], log::INFO);
					echo  json_encode($resultArray,true);exit;
				}else{
					log::record4inter($lang['goods_class_push_to_fail'], log::ERR);
					$resultArray['resultCode']="-1";
					$resultArray['resultMsg']=$lang['goods_class_push_to_fail'];
					echo  json_encode($resultArray,true);exit;
				}

			} catch (Exception $exc) {
				log::record4inter($exc->getMessage(), log::ERR);
				$resultArray['resultCode']="-1";
				$resultArray['resultMsg']=$lang['goods_class_push_to_fail'];
				echo json_encode($resultArray,true);exit;
			}
		}else{
			log::record4inter($lang['goods_class_push_to_fail'], log::ERR);
			$resultArray['resultCode']="-1";
			$resultArray['resultMsg']=$lang['goods_class_push_no_data'];
			echo json_encode($resultArray,true);exit;
		}
	}

	/**
	 *
	 * 推送单条商品分类到后台的采购系统
	 *glass_state（0失效/删除1追加2更新）
	 */
	public function pushAGoodsClassToVS($goodsClass ){
		$model_class = Model('goods_class');
		if($goodsClass['from_product_category_code'] == '0'){
			$goodsClass['from_product_category_code']		= "";
		}else{
			$where2 = array('gc_id' => intval($goodsClass['from_product_category_code']));
			$good_class_temp = $model_class->table("goods_class")->field('gc_class_code as from_product_category_code ')->Where($where2)->find();
			$goodsClass['from_product_category_code']		=$good_class_temp['from_product_category_code'];
		}
		$outDataObj = array();
		$outData =  array($goodsClass);
		//array_push($outData ,$goodsClass);
		$outDataObj['product_category_json']=$outData;
		$lang	= Language::getLangContent();
		$resultArray = array(
			'resultCode'=>'0',
			'resultMsg='=>$lang['goods_class_push_to_sucess'],
			'resultData'=>''
		);//0表示推送成功，-1表示推送失败
		//$url = YMA_WEBSERVICE_URL_HEAD."/impac/services/insertAndUpdate/insertAndUpdateProductCategory";
                $url = YMA_WEBSERVICE_INSERT_AND_UPDATE_PRODUCT_CATEGORY;
		if(!empty($goodsClass)) {//没有分类
			try{
				$outDataObj = json_encode($outDataObj);

				$resultArray = WebServiceUtil::getDataByCurl($url, $outDataObj, 0);
				$resultArray = json_decode($resultArray,treu);
				if($resultArray['resultCode']=='0'){
					CommonUtil::insertData2PushLog($resultArray, '0', $outDataObj, $url, 3);
					log::record4inter('glass_state（0失效/删除1追加2更新）'.$lang['goods_class_push_to_sucess'].$outDataObj, log::INFO);
					return  json_encode($resultArray,true);
				}else{
					CommonUtil::insertData2PushLog($resultArray, '0', $outDataObj, $url, 3);
					log::record4inter('glass_state（0失效/删除1追加2更新）'.$lang['goods_class_push_to_fail']."because of:".$outDataObj.json_encode($resultArray,true), log::ERR);
					$resultArray['resultCode']="-1";
					return  json_encode($resultArray,true);
				}

			} catch (Exception $exc) {
				log::record4inter($exc->getMessage(), log::ERR);
				$resultArray['resultCode']="-1";
				$resultArray['resultMsg']=$lang['goods_class_push_to_fail'];
				CommonUtil::insertData2PushLog($exc->getMessage(), '0', $outDataObj, $url, 3);
				return json_encode($resultArray,true);
			}
		}else{
			log::record4inter($lang['goods_class_push_to_fail'], log::ERR);
			$resultArray['resultCode']="-1";
			$resultArray['resultMsg']=$lang['goods_class_push_no_data'];
			CommonUtil::insertData2PushLog(json_encode($resultArray,true), '0', $outDataObj, $url, 3);
			return json_encode($resultArray,true);
		}
	}
        /**
         * 追加分类页面调用
         * 自动生成分类编码
         * @param int $gc_id   父级分类ID
         * @return string      正确：返回增加1的编码  例如 AA15    错误：24 当前父分类超过24个无法继续添加   100当前父分类超过100个无法继续添加  0当前父分类不存在
         */
        public function getMaxGoodsClassCodeOp($gc_id){
            $key = 65;
            $arr = array();
            for($i=65;$i<91;$i++){
                $arr[chr($key)] = $i;
                $key++;
            }
            if(empty($gc_id)){
                $gc_id = 0;
            }
            $max_code_DB = Model()->table('goods_class')->field('gc_class_code,right(gc_class_code,2) as gc_id_new')->where(array("gc_parent_id"=>$gc_id))->order('gc_id_new desc')->limit(1)->find();       
          //  $max_code_DB = Model()->table('goods_class')->field('gc_class_code')->where(array("gc_parent_id"=>$gc_id))->order('gc_id desc')->limit(1)->find();
            switch (strlen($max_code_DB['gc_class_code'])) {
                case 1:
                    //追加第一级分类
                    $num = $arr[$max_code_DB['gc_class_code']]+1;
                    if(!($num>64&&$num<91)){
                        $code = "24";
                        return $code;
                    }else{
                         $code = chr($num);
                    }
                    break;
                case 2:
                    //追加第二级分类
                    $num  = $arr[substr($max_code_DB['gc_class_code'], -1)]+1;
                    if(!($num>64&&$num<91)){
                        $code = "24";
                        return $code;
                    }else{
                        $code = substr($max_code_DB['gc_class_code'], 0,1).chr($num);
                    }
                    break;
                case 4:
                    //追加第三级分类  
                    $num  = substr($max_code_DB['gc_class_code'], -2)+1;
                    if($num>99){
                        $code = "100";
                        return $code;
                    }else{
                        //小于9的数据要补0
                        if($num<=9){
                            $num ="0".$num;
                        }
                         $code = substr($max_code_DB['gc_class_code'], 0,2).$num;
                    }
                    break;
                case 0:
                    //当前父分类不存在(新增第一个子分类)
                    //第一，判断父分类是第几级分类，是第一级则使用父分类追加A 第二级则使用父分类追加01
                    $parent_code_num = Model()->table('goods_class')->field('gc_parent_id,gc_class_code')->where(array("gc_id"=>$gc_id))->find();
                    if($parent_code_num['gc_parent_id']==0){
                        //父分类为第一级，当前新增分类为第二级   父CODE .A
                        $code = $parent_code_num['gc_class_code']."A";
                    }elseif($parent_code_num['gc_parent_id']>0){
                        //父分类为第二级分类，当前新增分类为第三级 父CODE .01
                        $code = $parent_code_num['gc_class_code']."01";
                    }else{
                        $code = "0";
                    }
                    break;
            }
            return $code;
        }
}
