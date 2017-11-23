<?php
/**
 * 文章
 *
 *
 *
 ***/




class articleControl extends BaseHomeControl {
	/**
	 * 默认进入页面
	 */
	public function indexOp(){
		/**
		 * 读取语言包
		 */
		Language::read('home_article_index');
		if(!empty($_GET['article_id'])){
			$this->showOp();
			exit;
		}
		if(!empty($_GET['ac_id'])){
			$this->articleOp();
			exit;
		}
		showMessage(Language::get('article_article_not_found'),'','html','error');//'没有符合条件的文章'
	}
	/**
	 * 文章列表显示页面
	 */
	public function articleOp(){
		/**
		 * 读取语言包
		 */
		Language::read('home_article_index');
		$lang	= Language::getLangContent();
		if(empty($_GET['ac_id'])){
			showMessage($lang['para_error'],'','html','error');//'缺少参数:文章类别编号'
		}
		/**
		 * 得到导航ID
		 */
		$nav_id = intval($_GET['nav_id']) ? intval($_GET['nav_id']) : 0 ;
		Tpl::output('index_sign',$nav_id);
		/**
		 * 根据类别编号获取文章类别信息
		 */
		$article_class_model	= Model('article_class');
		$condition	= array();
		if(!empty($_GET['ac_id'])){
			$condition['ac_id']	= intval($_GET['ac_id']);
		}
		$article_class	= $article_class_model->getOneClass(intval($_GET['ac_id']));
		Tpl::output('class_name', $article_class['ac_name']);
		if(empty($article_class) || !is_array($article_class)){
			showMessage($lang['article_article_class_not_exists'],'','html','error');//'该文章分类并不存在'
		}
		$default_count	= 5;//定义最新文章列表显示文章的数量
		/**
		 * 分类导航
		 */
		$nav_link = array(
			array(
				'title'=>$lang['homepage'],
				'link'=>SHOP_SITE_URL
			),
			array(
				'title'=>$article_class['ac_name']
			)
		);
		Tpl::output('nav_link_list',$nav_link);

		/**
		 * 左侧分类导航
		 */
		$condition	= array();
		$condition['ac_parent_id']	= $article_class['ac_id'];
                $condition['no_ac_id']	= PURCHASE_TYPE;
		$sub_class_list	= $article_class_model->getClassList($condition);
		if(empty($sub_class_list) || !is_array($sub_class_list)){
			$condition['ac_parent_id']	= $article_class['ac_parent_id'];
                        $condition['no_ac_id']	= PURCHASE_TYPE;
			$sub_class_list	= $article_class_model->getClassList($condition);
		}
		Tpl::output('sub_class_list',$sub_class_list);
		/**
		 * 文章列表
		 */
		$child_class_list	= $article_class_model->getChildClass(intval($_GET['ac_id']));
		$ac_ids	= array();
		if(!empty($child_class_list) && is_array($child_class_list)){
			foreach ($child_class_list as $v){
				$ac_ids[]	= $v['ac_id'];
			}
		}
		$ac_ids	= implode(',',$ac_ids);
		$article_model	= Model('article');
		$condition 	= array();
		$condition['ac_ids']	= $ac_ids;
		$condition['article_show']	= '1';
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$article_list	= $article_model->getArticleList($condition,$page);
		Tpl::output('article',$article_list);
		Tpl::output('show_page',$page->show());
		/**
		 * 最新文章列表
		 */
		$count	= count($article_list);
		$new_article_list	= array();
		if(!empty($article_list) && is_array($article_list)){
			for ($i=0;$i<($count>$default_count?$default_count:$count);$i++){
				$new_article_list[]	= $article_list[$i];
			}
		}
		Tpl::output('new_article_list',$new_article_list);
		Model('seo')->type('article')->param(array('article_class'=>$article_class['ac_name']))->show();
		Tpl::showpage('article_list');
	}
        
        /**
	 * 采购制度列表
	 */
	public function rule_listOp(){
		/**
		 * 采购制度列表
		 */
		$article_model	= Model('article');
		$condition 	= array();
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$article_list	= $article_model->getRuleList($condition,$page);
		Tpl::output('article',$article_list);
		Tpl::output('show_page',$page->show());
                Tpl::showpage('article_rule_list');
	}
        
        /**
	 * 采购学堂列表
	 */
	public function purchase_schoolOp(){
		/**
		 * 读取语言包
		 */
                $_GET['ac_id']=PURCHASE_TYPE;
		Language::read('home_article_index');
		$lang	= Language::getLangContent();
		if(empty($_GET['ac_id'])){
			showMessage($lang['para_error'],'','html','error');//'缺少参数:文章类别编号'
		}
		
		/**
		 * 根据类别编号获取文章类别信息
		 */
		$article_class_model	= Model('article_class');
		$condition	= array();
		if(!empty($_GET['ac_id'])){
			$condition['ac_id']	= intval($_GET['ac_id']);
		}
		$article_class	= $article_class_model->getOneClass(intval($_GET['ac_id']));
		Tpl::output('class_name', $article_class['ac_name']);
		if(empty($article_class) || !is_array($article_class)){
			showMessage($lang['article_article_class_not_exists'],'','html','error');//'该文章分类并不存在'
		}
		$default_count	= 5;//定义最新文章列表显示文章的数量
		
		/**
		 * 文章列表
		 */
		$child_class_list	= $article_class_model->getChildClass(intval($_GET['ac_id']));
		$ac_ids	= array();
		if(!empty($child_class_list) && is_array($child_class_list)){
			foreach ($child_class_list as $v){
				$ac_ids[]	= $v['ac_id'];
			}
		}
		$ac_ids	= implode(',',$ac_ids);
		$article_model	= Model('article');
		$condition 	= array();
		$condition['ac_ids']	= $ac_ids;
		$condition['article_show']	= '1';
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$article_list	= $article_model->getArticleList($condition,$page);
                
                $model_upload = Model('upload');
                foreach($article_list as $k => $v){
                    $condition['upload_type'] = '1';
                    $condition['item_id'] = $v['article_id'];
                    $file_upload = $model_upload->getUploadList($condition);
                    $article_list[$k]['pic']=UPLOAD_SITE_URL.'/'.ATTACH_ARTICLE.'/'.$file_upload[0]['file_name'];;
                }
                
		Tpl::output('article',$article_list);
		Tpl::output('show_page',$page->show());
		/**
		 * 最新文章列表
		 */
		$count	= count($article_list);
		$new_article_list	= array();
		if(!empty($article_list) && is_array($article_list)){
			for ($i=0;$i<($count>$default_count?$default_count:$count);$i++){
				$new_article_list[]	= $article_list[$i];
			}
		}
		Tpl::output('new_article_list',$new_article_list);
		Model('seo')->type('article')->param(array('article_class'=>$article_class['ac_name']))->show();
		Tpl::showpage('article_school_list');
	}
        
	/**
	 * 采购学堂文章显示页面
	 */
	public function school_showOp(){
		/**
		 * 读取语言包
		 */
		Language::read('home_article_index');
		$lang	= Language::getLangContent();
		if(empty($_GET['article_id'])){
			showMessage($lang['para_error'],'','html','error');//'缺少参数:文章编号'
		}
		/**
		 * 根据文章编号获取文章信息
		 */
		$article_model	= Model('article');
		$article	= $article_model->getOneArticle(intval($_GET['article_id']));
		if(empty($article) || !is_array($article) || $article['article_show']=='0'){
			showMessage($lang['article_show_not_exists'],'','html','error');//'该文章并不存在'
		}
		Tpl::output('article',$article);

		/**
		 * 根据类别编号获取文章类别信息
		 */
		$article_class_model	= Model('article_class');
		$condition	= array();
		$article_class	= $article_class_model->getOneClass($article['ac_id']);
		if(empty($article_class) || !is_array($article_class)){
			showMessage($lang['article_show_delete'],'','html','error');//'该文章已随所属类别被删除'
		}
		$default_count	= 5;//定义最新文章列表显示文章的数量
		
		/**
		 * 文章列表
		 */
		$child_class_list	= $article_class_model->getChildClass($article_class['ac_id']);
		$ac_ids	= array();
		if(!empty($child_class_list) && is_array($child_class_list)){
			foreach ($child_class_list as $v){
				$ac_ids[]	= $v['ac_id'];
			}
		}
		$ac_ids	= implode(',',$ac_ids);
		$article_model	= Model('article');
		$condition 	= array();
		$condition['ac_ids']	= $ac_ids;
		$condition['article_show']	= '1';
		$article_list	= $article_model->getArticleList($condition);
		/**
		 * 寻找上一篇与下一篇
		 */
		$pre_article	= $next_article	= array();
		if(!empty($article_list) && is_array($article_list)){
			$pos	= 0;
			foreach ($article_list as $k=>$v){
				if($v['article_id'] == $article['article_id']){
					$pos	= $k;
					break;
				}
			}
			if($pos>0 && is_array($article_list[$pos-1])){
				$pre_article	= $article_list[$pos-1];
			}
			if($pos<count($article_list)-1 and is_array($article_list[$pos+1])){
				$next_article	= $article_list[$pos+1];
			}
		}
		Tpl::output('pre_article',$pre_article);
		Tpl::output('next_article',$next_article);
		/**
		 * 最新文章列表
		 */
		$count	= count($article_list);
		$new_article_list	= array();
		if(!empty($article_list) && is_array($article_list)){
			for ($i=0;$i<($count>$default_count?$default_count:$count);$i++){
				$new_article_list[]	= $article_list[$i];
			}
		}
		Tpl::output('new_article_list',$new_article_list);

		$seo_param = array();
		$seo_param['name'] = $article['article_title'];
		$seo_param['article_class'] = $article_class['ac_name'];
		Model('seo')->type('article_content')->param($seo_param)->show();
		Tpl::showpage('school_show');
	}

        /**
	 * 单篇文章显示页面
	 */
	public function showOp(){
		/**
		 * 读取语言包
		 */
		Language::read('home_article_index');
		$lang	= Language::getLangContent();
		if(empty($_GET['article_id'])){
			showMessage($lang['para_error'],'','html','error');//'缺少参数:文章编号'
		}
		/**
		 * 根据文章编号获取文章信息
		 */
		$article_model	= Model('article');
		$article	= $article_model->getOneArticle(intval($_GET['article_id']));
		if(empty($article) || !is_array($article) || $article['article_show']=='0'){
			showMessage($lang['article_show_not_exists'],'','html','error');//'该文章并不存在'
		}
		Tpl::output('article',$article);

		/**
		 * 根据类别编号获取文章类别信息
		 */
		$article_class_model	= Model('article_class');
		$condition	= array();
		$article_class	= $article_class_model->getOneClass($article['ac_id']);
		if(empty($article_class) || !is_array($article_class)){
			showMessage($lang['article_show_delete'],'','html','error');//'该文章已随所属类别被删除'
		}
		$default_count	= 5;//定义最新文章列表显示文章的数量
		/**
		 * 分类导航
		 */
		$nav_link = array(
			array(
				'title'=>$lang['homepage'],
				'link'=>SHOP_SITE_URL
			),
			array(
				'title'=>$article_class['ac_name'],
			    'link' => urlShop('article', 'article', array('ac_id' => $article_class['ac_id']))
			),
			array(
				'title'=>$lang['article_show_article_content']
			)
		);
		Tpl::output('nav_link_list',$nav_link);
		/**
		 * 左侧分类导航
		 */
		$condition	= array();
		$condition['ac_parent_id']	= $article_class['ac_id'];
                $condition['no_ac_id']	= PURCHASE_TYPE;
		$sub_class_list	= $article_class_model->getClassList($condition);
		if(empty($sub_class_list) || !is_array($sub_class_list)){
			$condition['ac_parent_id']	= $article_class['ac_parent_id'];
                        $condition['no_ac_id']	= PURCHASE_TYPE;
			$sub_class_list	= $article_class_model->getClassList($condition);
		}
		Tpl::output('sub_class_list',$sub_class_list);
		/**
		 * 文章列表
		 */
		$child_class_list	= $article_class_model->getChildClass($article_class['ac_id']);
		$ac_ids	= array();
		if(!empty($child_class_list) && is_array($child_class_list)){
			foreach ($child_class_list as $v){
				$ac_ids[]	= $v['ac_id'];
			}
		}
		$ac_ids	= implode(',',$ac_ids);
		$article_model	= Model('article');
		$condition 	= array();
		$condition['ac_ids']	= $ac_ids;
		$condition['article_show']	= '1';
		$article_list	= $article_model->getArticleList($condition);
		/**
		 * 寻找上一篇与下一篇
		 */
		$pre_article	= $next_article	= array();
		if(!empty($article_list) && is_array($article_list)){
			$pos	= 0;
			foreach ($article_list as $k=>$v){
				if($v['article_id'] == $article['article_id']){
					$pos	= $k;
					break;
				}
			}
			if($pos>0 && is_array($article_list[$pos-1])){
				$pre_article	= $article_list[$pos-1];
			}
			if($pos<count($article_list)-1 and is_array($article_list[$pos+1])){
				$next_article	= $article_list[$pos+1];
			}
		}
		Tpl::output('pre_article',$pre_article);
		Tpl::output('next_article',$next_article);
		/**
		 * 最新文章列表
		 */
		$count	= count($article_list);
		$new_article_list	= array();
		if(!empty($article_list) && is_array($article_list)){
			for ($i=0;$i<($count>$default_count?$default_count:$count);$i++){
				$new_article_list[]	= $article_list[$i];
			}
		}
		Tpl::output('new_article_list',$new_article_list);

		$seo_param = array();
		$seo_param['name'] = $article['article_title'];
		$seo_param['article_class'] = $article_class['ac_name'];
		Model('seo')->type('article_content')->param($seo_param)->show();
		Tpl::showpage('article_show');
	}

	/**
	 * 采购制度列表
	 */
	public function purchaseProcessOp(){
		/**
		 * 读取语言包
		 */
		Language::read('home_article_index');
		$lang	= Language::getLangContent();
		$article_class_model	= Model('article_class');
		$condition['ac_parent_id']	= '0';
		$sub_class_list	= $article_class_model->getClassList($condition);

		Tpl::output('sub_class_list',$sub_class_list);

		/**
		 * 分类导航
		 */
		$nav_link = array(
			array(
				'title'=>$lang['homepage'],
				'link'=>SHOP_SITE_URL
			),
			array(
				'title'=>$lang['purchase_school'],
				'link' => urlShop('article', 'purchaseProcess')
			),
			array(
				'title'=>$lang['purchase_rule']
			)
		);
		Tpl::output('nav_link_list',$nav_link);

		$purchase_rule_model	= Model('purchase_rule');
		$condition 	= array();
		$condition['field'] = "purchase_rule_id as article_id ,title as article_title,publish_department,object_person ,publish_date as article_time";
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$purchase_rule_list	= $purchase_rule_model->getPurchaseRuleList($condition,$page);
		Tpl::output('article',$purchase_rule_list);
		Tpl::output('class_name',$lang['purchase_rule']);
		Tpl::output('show_page',$page->show());
		Tpl::output('is_pur',true);
		Tpl::output('index_sign',"pur_school");
		Model('seo')->type('article')->param(array('article_class'=>$lang['purchase_school']))->show();
		Tpl::showpage('article_list');
	}


	/**
	 * 单采购制度显示页面
	 */
	public function showPurchaseDetailOp(){
		Language::read('home_article_index');
		$lang	= Language::getLangContent();
		if(empty($_GET['article_id'])){
			showMessage($lang['para_error'],'','html','error');//'缺少参数:文章编号'
		}
		/**
		 * 根据采购制度编号获取采购制度的内容
		 */
		$pur_rule_model	= Model('purchase_rule');
		$pur_rule	= $pur_rule_model->getOneByIdForShop(intval($_GET['article_id']));
		if(empty($pur_rule) || !is_array($pur_rule) ){
			showMessage($lang['article_pur_show_not_exists'],'','html','error');//'该文章并不存在'
		}
		Tpl::output('article',$pur_rule);


		/**
		 * 左侧分类导航
		 */
		$article_class_model	= Model('article_class');
		$condition	= array();
		$condition['ac_parent_id']	='0';
		$sub_class_list	= $article_class_model->getClassList($condition);
		Tpl::output('sub_class_list',$sub_class_list);


		$condition 	= array();
		$condition['field'] = "purchase_rule_id as article_id ,title as article_title ,publish_date as article_time";
		$purchase_rule_list	= $pur_rule_model->getPurchaseRuleList($condition);
		/**
		 * 寻找上一篇与下一篇
		 */
		$pre_article	= $next_article	= array();
		if(!empty($purchase_rule_list) && is_array($purchase_rule_list)){
			$pos	= 0;
			foreach ($purchase_rule_list as $k=>$v){
				if($v['article_id'] == $pur_rule['article_id']){
					$pos	= $k;
					break;
				}
			}
			if($pos>0 && is_array($purchase_rule_list[$pos-1])){
				$pre_article	= $purchase_rule_list[$pos-1];
			}
			if($pos<count($purchase_rule_list)-1 and is_array($purchase_rule_list[$pos+1])){
				$next_article	= $purchase_rule_list[$pos+1];
			}
		}
		Tpl::output('pre_article',$pre_article);
		Tpl::output('next_article',$next_article);


		$seo_param = array();
		$seo_param['name'] = $pur_rule['article_title'];
		//$seo_param['article_class'] = $article_class['ac_name'];
		//Model('seo')->type('article_content')->param($seo_param)->show();
		Tpl::output('index_sign',"pur_school");
		Tpl::output('is_pur',true);
		Model('seo')->type('article')->param(array('article_class'=>$lang['purchase_school']))->show();
		Tpl::showpage('rule_show');
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
?>
