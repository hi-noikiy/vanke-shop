<?php
/**
 * 默认展示页面
 *
 *
*/



class indexControl extends BaseHomeControl{
	public function indexOp(){

        if($this->is_https()){
            $item_url = 'https://mall.vankeservice.com/';
            $idm_url = 'https://siam.vankeservice.com/';
        }else{
            $item_url = 'http://120.77.38.59/';
            $idm_url = 'https://siamtest.vankeservice.com/';
        }
        Tpl::output('item_url',$item_url);
        Tpl::output('idm_url',$idm_url);
                       //输出城市中心
        $model_city_centre = Model();
        $city_centreList=$model_city_centre->table("city_centre")->field("id,city_name")->select(); 
        $zt=true;//首页才会显示
        //获取到session中的ctid   
        $ct_id=$_SESSION['ctid'];
        Tpl::output('ct_id',$ct_id); 
        Tpl::output('zt',$zt); 
        Tpl::output('city_centreList',$city_centreList); 

        $member_name=$model_city_centre->table("member")->where(array("member_id"=>$_SESSION['member_id']))->field("role_id,member_truename")->find(); 
        if($member_name['role_id'] == 01){
            $_SESSION['company_name']=$member_name['member_truename'];
        }else{
         //输出公司名称
        $store_info=$model_city_centre->table("supplier")->where(array("member_id"=>$_SESSION['member_id']))->field("company_name")->find();        
        $_SESSION['company_name']=$store_info['company_name'];  
        }
        
		Language::read('home_index_index');
		Tpl::output('index_sign','index');
		
		//把加密的用户id写入cookie  已换另一个方式，临时去掉此方法
		$uid = intval(base64_decode($_COOKIE['uid']));

		//抢购专区
		Language::read('member_groupbuy');
        $model_groupbuy = Model('groupbuy');
        $group_list = $model_groupbuy->getGroupbuyCommendedList(4);
		Tpl::output('group_list', $group_list);
		//友情链接
		$model_link = Model('link');
		$link_list = $model_link->getLinkList($condition,$page);
		//热门晒单
    	$goods_evaluate_info = Model('evaluate_goods')->getEvaluateGoodsList(6);
    	Tpl::output('goods_evaluate_info', $goods_evaluate_info);
		/**
		 * 整理图片链接
		 */
		if (is_array($link_list)){
			foreach ($link_list as $k => $v){
				if (!empty($v['link_pic'])){
					$link_list[$k]['link_pic'] = UPLOAD_SITE_URL.'/'.ATTACH_PATH.'/common/'.DS.$v['link_pic'];
				}
			}
		}
		Tpl::output('$link_list',$link_list);
		//限时折扣
        $model_xianshi_goods = Model('p_xianshi_goods');
        $xianshi_item = $model_xianshi_goods->getXianshiGoodsCommendList(4);
		Tpl::output('xianshi_item', $xianshi_item);

		//板块信息
		$model_web_config = Model('web_config');
		$web_html = $model_web_config->getWebHtml('index');
		Tpl::output('web_html',$web_html);

		//取采购制度
		$purchase_rule_model	= Model('purchase_rule');
		$condition 	= array();
		$condition['field'] = "purchase_rule_id as article_id ,title as article_title,publish_department,object_person ,publish_date as article_time";
		$page	= new Page();
		$page->setEachNum(10);
		$page->setStyle('admin');
		$purchase_rule_list	= $purchase_rule_model->getPurchaseRuleList($condition,$page);
		Tpl::output('article',$purchase_rule_list);
                //取用户信息
                $model_member = new Model('member');
                $member_info_per  = $model_member->table('member')->field('pernr_id')->where(array('member_id'=>$_SESSION['member_id']))->select();
                Tpl::output('member_info_per',$member_info_per[0]);
//                //取招投标信息
//                $TIinfo = $this->handleInfoForTI();
//                Tpl::output('tender_inquiry',$TIinfo);
		Model('seo')->type('index')->show();
//                //判断是否有店铺推荐入住如果有则保存COOKIE['stid']
		Tpl::showpage('index');
	}

	//json输出商品分类
	public function josn_classOp() {
		/**
		 * 实例化商品分类模型
		 */
		$model_class		= Model('goods_class');
		$goods_class		= $model_class->getGoodsClassListByParentId(intval($_GET['gc_id']));
		$array				= array();
		if(is_array($goods_class) and count($goods_class)>0) {
			foreach ($goods_class as $val) {
				$array[$val['gc_id']] = array('gc_id'=>$val['gc_id'],'gc_name'=>htmlspecialchars($val['gc_name']),'gc_parent_id'=>$val['gc_parent_id'],'commis_rate'=>$val['commis_rate'],'gc_sort'=>$val['gc_sort']);
			}
		}
		/**
		 * 转码
		 */
		if (strtoupper(CHARSET) == 'GBK'){
			$array = Language::getUTF8(array_values($array));//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
		} else {
			$array = array_values($array);
		}
		echo $_GET['callback'].'('.json_encode($array).')';
	}

	//json输出商品分类信息
	public function josn_classinfoOp() {
		$model_class		= Model('goods_class');
		$goods_class		= $model_class->getGoodsClassInfoById(intval($_GET['gc_id']));
		$array				= array();
		if(is_array($goods_class) and count($goods_class)>0) {
			$val = $goods_class;
			$array[$val['gc_id']] = array('gc_id'=>$val['gc_id'],'gc_name'=>htmlspecialchars($val['gc_name']),'gc_parent_id'=>$val['gc_parent_id'],'commis_rate'=>$val['commis_rate'],'gc_sort'=>$val['gc_sort']);
		}
		/**
		 * 转码
		 */
		if (strtoupper(CHARSET) == 'GBK'){
			$array = Language::getUTF8(array_values($array));//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
		} else {
			$array = array_values($array);
		}
		echo $_GET['callback'].'('.json_encode($array).')';
	}
   
	
	//闲置物品地区json输出
	public function flea_areaOp() {
		if(intval($_GET['check']) > 0) {
			$_GET['area_id'] = $_GET['region_id'];
		}
		if(intval($_GET['area_id']) == 0) {
			return ;
		}
		$model_area	= Model('flea_area');
		$area_array			= $model_area->getListArea(array('flea_area_parent_id'=>intval($_GET['area_id'])),'flea_area_sort desc');
		$array	= array();
		if(is_array($area_array) and count($area_array)>0) {
			foreach ($area_array as $val) {
				$array[$val['flea_area_id']] = array('flea_area_id'=>$val['flea_area_id'],'flea_area_name'=>htmlspecialchars($val['flea_area_name']),'flea_area_parent_id'=>$val['flea_area_parent_id'],'flea_area_sort'=>$val['flea_area_sort']);
			}
			/**
			 * 转码
			 */
			if (strtoupper(CHARSET) == 'GBK'){
				$array = Language::getUTF8(array_values($array));//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
			} else {
				$array = array_values($array);
			}
		}
		if(intval($_GET['check']) > 0) {//判断当前地区是否为最后一级
			if(!empty($array) && is_array($array)) {
				echo 'false';
			} else {
				echo 'true';
			}
		} else {
			echo json_encode($array);
		}
	}

	//json输出闲置物品分类
	public function josn_flea_classOp() {
		/**
		 * 实例化商品分类模型
		 */
		$model_class		= Model('flea_class');
		$goods_class		= $model_class->getClassList(array('gc_parent_id'=>intval($_GET['gc_id'])));
		$array				= array();
		if(is_array($goods_class) and count($goods_class)>0) {
			foreach ($goods_class as $val) {
				$array[$val['gc_id']] = array('gc_id'=>$val['gc_id'],'gc_name'=>htmlspecialchars($val['gc_name']),'gc_parent_id'=>$val['gc_parent_id'],'gc_sort'=>$val['gc_sort']);
			}
		}
		/**
		 * 转码
		 */
		if (strtoupper(CHARSET) == 'GBK'){
			$array = Language::getUTF8(array_values($array));//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
		} else {
			$array = array_values($array);
		}
		echo json_encode($array);
	}
	
   /**
     * json输出地址数组 原data/resource/js/area_array.js
     */
    public function json_areaOp()
    {
        echo $_GET['callback'].'('.json_encode(Model('area')->getAreaArrayForJson()).')';
    }
	
	/**
     * json输出地址数组
     */
    public function json_area_showOp()
    {
        $area_info['text'] = Model('area')->getTopAreaName(intval($_GET['area_id']));
        echo $_GET['callback'].'('.json_encode($area_info).')';
    }
	//判断是否登录
	public function loginOp(){
		echo ($_SESSION['is_login'] == '1')? '1':'0';
	}

	/**
	 * 头部最近浏览的商品
	 */
	public function viewed_infoOp(){
	    $info = array();
		if ($_SESSION['is_login'] == '1') {
		    $member_id = $_SESSION['member_id'];
		    $info['m_id'] = $member_id;
		    if (C('voucher_allow') == 1) {
		        $time_to = time();//当前日期
    		    $info['voucher'] = Model()->table('voucher')->where(array('voucher_owner_id'=> $member_id,'voucher_state'=> 1,
    		    'voucher_start_date'=> array('elt',$time_to),'voucher_end_date'=> array('egt',$time_to)))->count();
		    }
    		$time_to = strtotime(date('Y-m-d'));//当前日期
    		$time_from = date('Y-m-d',($time_to-60*60*24*7));//7天前
		    $info['consult'] = Model()->table('consult')->where(array('member_id'=> $member_id,
		    'consult_reply_time'=> array(array('gt',strtotime($time_from)),array('lt',$time_to+60*60*24),'and')))->count();
		}
		$goods_list = Model('goods_browse')->getViewedGoodsList($_SESSION['member_id'],5);
		if(is_array($goods_list) && !empty($goods_list)) {
		    $viewed_goods = array();
		    foreach ($goods_list as $key => $val) {
		        $goods_id = $val['goods_id'];
		        $val['url'] = urlShop('goods', 'index', array('goods_id' => $goods_id));
		        $val['goods_image'] = thumb($val, 60);
		        $viewed_goods[$goods_id] = $val;
		    }
		    $info['viewed_goods'] = $viewed_goods;
		}
		if (strtoupper(CHARSET) == 'GBK'){
			$info = Language::getUTF8($info);
		}
		echo json_encode($info);
	}
	/**
	 * 查询每月的周数组
	 */
	public function getweekofmonthOp(){
	    import('function.datehelper');
	    $year = $_GET['y'];
	    $month = $_GET['m'];
	    $week_arr = getMonthWeekArr($year, $month);
	    echo json_encode($week_arr);
	    die;
	}
        /**
         * 前台加载时向采购请求招投标询价信息
         * @return array   格式  0=》"message": "【招标公告】招标信息1"，"message_type": "1"  ； 1=》  "message": "【询价公告】询价信息1"，"message_type": "0"
         */
        public function getTenderAndInquiry(){
            $url = YMA_WEBSERVICE_RETRIEVE_TENDER_INQUERY;
            $return_json = WebServiceUtil::getDataByCurl($url, "{}", "1");
            $return_data = json_decode($return_json,true);
            if($return_data['resultCode']=="0"){
                return $return_data['resultData'];
            }
            return $array;
        }
        /**
         * 处理接口传回来的数据
         * @return string
         */
        public function handleInfoForTI(){
            $array = $this->getTenderAndInquiry();
            $data = array();
            if(!empty($array)){
                //给出迭代次数
                if(sizeof($array)<=9){
                    $count = sizeof($array);
                }elseif(sizeof($array)>9){
                    $count = 9;
                }
                for($i = 0 ;$i<$count;$i++){
                    $data[$i]['title']=$array[$i]['message'];
                    if($array[$i]["message_type"]==0){
                        //询价页面
                        $data[$i]['href'] ="index.php?act=parent_iframe&op=index&mtype=inquiry";
                    }else{
                        //招标页面
                        $data[$i]['href'] ="index.php?act=parent_iframe&op=index&mtype=tender";
                    }
                }
            }
             return $data;
            
        }
        public function ajaxGetInfoOp(){
            //取招投标信息
            $TIinfo = $this->handleInfoForTI();
            echo json_encode($TIinfo);
        }
        
        
        public function setCityIdOp(){  
            $_SESSION['ctid'] = $_GET['ctid'];
        }               
  
        
        
        }
