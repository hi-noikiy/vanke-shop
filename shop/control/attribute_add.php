<?php
/**
 * 品牌管理
 *
 *
 *
 ***/



class attribute_addControl extends BaseSellerControl {
	public function __construct() {
		parent::__construct();
		Language::read('member_store_brand');
	}

	public function indexOp(){
		$this->brand_listOp();
	}

	/**
	 * 属性列表
	 */
	public function brand_listOp() {
		$model = Model();
		$condition = array();
		$condition['store_id'] = $_SESSION['store_id'];

                $brand_list = $model->table('seller_attribute')->where($condition)->order($order)->page('10')->limit($limit)->select();
		Tpl::output('brand_list',$brand_list);
		Tpl::output('show_page',$model->showpage());

		self::profile_menu('brand_list','brand_list');
		Tpl::showpage('attribute_list');
	}

	/**
	 * 品牌添加页面
	 */
	public function brand_addOp() {
            
		$lang	= Language::getLangContent();
		$model_brand = Model('brand');
		if($_GET['brand_id'] != '') {
			$brand_array = $model_brand->getBrandInfo(array('brand_id' => $_GET['brand_id'], 'store_id' => $_SESSION['store_id']));
			if (empty($brand_array)){
				showMessage($lang['wrong_argument'],'','html','error');
			}
			Tpl::output('brand_array',$brand_array);
		}

		// 一级商品分类
		$gc_list = Model('goods_class')->getGoodsClassListByParentId(0);
		Tpl::output('gc_list', $gc_list);

		Tpl::showpage('attribute.add','null_layout');
	}

	/**
	 * 品牌保存
	 */
	public function brand_saveOp() {
            
		$model = Model();
                $store = $model->table('store')->where('store_id='.$_SESSION['store_id'])->field('member_id,member_name,first_city_id')->find();
                
                $insert_array = array();
                $insert_array['member_id']     = $store['member_name'];
                $insert_array['member_name']     = $_SESSION['store_name'];
                $insert_array['att_date'] = date("Y-m-d");
                $insert_array['city_id']     = $store['first_city_id'];
                $insert_array['att_type']      = htmlspecialchars($_POST['attribute_type']);
                $insert_array['att_title']      = htmlspecialchars($_POST['attribute_name']);
                $insert_array['att_content']   = htmlspecialchars($_POST['attribute_desc']);
                $insert_array['att_state']        = 1;
                $insert_array['att_from']        = 0;
                $insert_array['store_id']     = $_SESSION['store_id'];
                $insert_array['store_name']       = $_SESSION['store_name'];
                $result = $model->table('seller_attribute')->insert($insert_array);
                if ($result){
                        showDialog("申请提交成功！请等待管理员审核！",'index.php?act=attribute_add&op=brand_list','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
                }else {
                        showDialog("申请提交失败！");
                }
	}

	/**
	 * 用户中心右边，小导航
	 *
	 * @param string	$menu_type	导航类型
	 * @param string 	$menu_key	当前导航的menu_key
	 * @param array 	$array		附加菜单
	 * @return
	 */
	private function profile_menu($menu_type,$menu_key='',$array=array()) {
		Language::read('member_layout');
		$lang	= Language::getLangContent();
		$menu_array		= array();
		switch ($menu_type) {
			case 'brand_list':
				$menu_array = array(
                                        1=>array('menu_key'=>'brand_list', 'menu_name'=>"基本信息申请", 'menu_url'=>'index.php?act=attribute_add&op=op')
				);
				break;
		}
		if(!empty($array)) {
			$menu_array[] = $array;
		}
		Tpl::output('member_menu',$menu_array);
		Tpl::output('menu_key',$menu_key);
	}
        
}
