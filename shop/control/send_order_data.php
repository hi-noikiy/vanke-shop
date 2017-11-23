<?php
/**
 * 购买流程
 ***/






class send_order_dataControl extends BaseHomeControl {
    public function __construct() {
        parent::__construct ();
    }
    public function indexOp(){
        
        Tpl::output("order_sn",$_GET['order_sn']);
        Tpl::showpage("send_order_data",'null_layout');
    }
    

}
