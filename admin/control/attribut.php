<?php
class attributControl extends SystemControl{
	const EXPORT_SIZE = 1000;
	public function __construct(){
		parent::__construct();
		Language::read('store,store_grade');
	}
    //商家属性申请
        public function IndexOp(){
            $model = Model();

            if(trim($_GET['member_id']) != ''){
                    $condition['member_id']	= array('like', '%'.$_GET['member_id'].'%');
                    Tpl::output('member_id',$_GET['member_id']);
            }
            if(trim($_GET['att_content']) != ''){
                $condition['att_content']	= array('like', '%'.$_GET['att_content'].'%');
                Tpl::output('att_content',$_GET['att_content']);
            }
            if(trim($_GET['att_state']) != ''){
                $condition['att_state']	= array('like', '%'.$_GET['att_state'].'%');
                Tpl::output('att_state',$_GET['att_state']);
            }
            //获取当前登录后台管理员 城市中心地区
            $admininfo = $this->getAdminInfo();
            if($admininfo['cityid'] > 0){
                $condition['city_id'] = $admininfo['cityid'];
            }
            $att_list = $model->table('seller_attribute')->where($condition)->order('id DESC')->limit('20')->page($page)->select();

            Tpl::output('att_list',$att_list);
            Tpl::output('page',$model->showpage('2'));
            Tpl::showpage('store.attribut');
        }
        //商家属性处理
        public function UpdateOp(){

            $model = Model();
            $a_id  = htmlspecialchars($_GET['id']);
            if($a_id > 0){
                if($_GET['type'] == 1){
                    $updata_data['att_state'] = 2;
                }
                if($_GET['type'] == 2){
                    $updata_data['att_state'] = 3;
                }
                if($_GET['type'] <= 0){
                    showMessage("参数错误-基本信息申请");
                }
                $if = $model->table('seller_attribute')->where('id='.$a_id)->update($updata_data);
                if($if != false){
                showMessage("基本信息申请通过成功");
                }else{
                showMessage("基本信息申请通过失败");
                }
            }else{
                showMessage("非法路径，已记录");
            }
            
        }
}