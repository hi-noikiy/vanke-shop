<?php
/**
 * 店铺卖家注销
 *
 *
 *
 ***/




class agent_logoutControl extends BaseSellerControl {

	public function __construct() {
//		parent::__construct();
	}

    public function indexOp() {
        $this->logoutOp();
    }

    public function logoutOp() {
//        $this->recordSellerLog('注销成功');
        // 清除店铺消息数量缓存
//        setNcCookie('storemsgnewnum'.$_SESSION['agent_id'],0,-3600);
        session_destroy();
        redirect('index.php?act=agent_login');
    }

}