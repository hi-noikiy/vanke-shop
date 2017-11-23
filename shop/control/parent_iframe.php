<?php
/**
 * 文章
 *
 *
 *
 ***/




class parent_iframeControl extends BaseHomeControl {

    public function __construct(){
        parent::__construct();
    }

	/**
	 * 采购制度列表
	 */
	public function indexOp(){
		/**
		 * 读取语言包
		 */
		Language::read('home_article_index');
		$lang	= Language::getLangContent();
		$default_url="";
		/**
		 * 分类导航
		 */
		if($_GET['mtype']=='tender'){
			Tpl::output('index_sign','tender');
			$nav_link = array(
				array('title'=>$lang['homepage'], 'link'=>SHOP_SITE_URL),
				array('title'=>'招标信息',)
			);
			Model('seo')->type('article')->param(array('article_class'=>'招标信息'))->show();
		}else{
			Tpl::output('index_sign','inquiry');
			$nav_link = array(
				array('title'=>$lang['homepage'], 'link'=>SHOP_SITE_URL),
				array('title'=>'询价信息',)
			);
			Model('seo')->type('article')->param(array('article_class'=>'询价信息'))->show();
		}
		Tpl::output('nav_link_list',$nav_link);

		if( $_SESSION['ref_url_iframe']=='' ){
			if($_GET['mtype']=='tender'){
				$default_url = IFRAME_TENDER_DEFAULT;//招标模块默认的页面
			}else{
				$default_url = IFRAME_INQUIRY_DEFAULT;//询价模块默认的页面
			}
			if(stripos("?",$default_url)>=0){
				$default_url.='&'.Embedpage::getCommonParams().'&'.'usePosit=index';
			}else{
				$default_url.='?'.Embedpage::getCommonParams().'&'.'usePosit=index';//拼接上要传给采购系统的固定的参数
			}
		}else{		//需要跳转到采购系统指定的url时
			$default_url= $_SESSION['ref_url_iframe'] ;
			if(stripos("?",$_SESSION['ref_url_iframe'])>=0){
				$default_url.='&'.Embedpage::getCommonParams().'&'.'usePosit=index';
			}else{
				$default_url.='?'.Embedpage::getCommonParams().'&'.'usePosit=index';
			}
			$_SESSION['ref_url_iframe']="";
		}
		Tpl::output('default_url',$default_url);


		Tpl::showpage('parentIframe');
	}

}
?>
