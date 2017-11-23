<?php
/**
 * 文章
 *
 *
 *
 ***/




class parentIframeControl extends BaseHomeControl {

	/**
	 * 采购制度列表
	 */
	public function indexOp(){
		/**
		 * 读取语言包
		 */
		Language::read('home_article_index');
		$lang	= Language::getLangContent();
		/**
		 * 分类导航
		 */
		$nav_link = array(
			array(
				'title'=>$lang['homepage'],
				'link'=>SHOP_SITE_URL
			),
			array(
				'title'=>'招标信息',

			)

		);
		Tpl::output('nav_link_list',$nav_link);


		Model('seo')->type('article')->param(array('article_class'=>'招标信息'))->show();
		echo "ddddd" ;exit;
		Tpl::showpage('parentIframe');
	}
}
?>
