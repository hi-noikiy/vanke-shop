<?php
/**
 * 选择认证
 ***/



class getmemberstatusControl extends BaseHomeControl {
        
        public function __construct() {

            parent::__construct();
            Tpl::setLayout('third_joinin_layout');
            //判断除 普通会员外都不能进来
            if($_SESSION['identity'] != MEMBER_IDENTITY_ONE && !empty($_SESSION['identity'])){
                redirect('index.php');
            }
            $this->checkLogin();
            if($_GET['op'] != 'check_seller_name_exist' && $_GET['op'] != 'checkname' && $_GET['op'] != 'ecrz'  && $_GET['op'] != 'send_email'  &&  $_GET['op'] != 'callbackecmail') {
                $this->check_joinin_state();
            }
            
            $phone_array = explode(',',C('site_phone'));
            Tpl::output('phone_array',$phone_array);
            Tpl::output('html_title',C('site_name').' - '.'物业采购认证申请');
            Tpl::output('article_list','');//底部不显示文章分类
	}
        
        private function check_joinin_state() {
        
        //获取用户是否已经认证过如果认证通过则进入选择 用户是继续认证还是开店申请
        $model_store_joinin = Model('member_status');
        $joinin_detail_where['member_id'] = $_SESSION['member_id'];
        $joinin_detail = $model_store_joinin->where($joinin_detail_where)->find();
        if(!empty($joinin_detail)) {
            $this->joinin_detail = $joinin_detail;
            switch (intval($joinin_detail['rz_status'])) {
                case 1:
                    $this->show_join_message('您的认证审核已经提交，请等待管理员认证！', FALSE);
                    break;
                case 2:
                    if(!in_array($_GET['op'], array('index'))) {
                        $this->show_join_message('认证成功', FALSE);
                    }
                    Tpl::output('succ','successs');
                    break;
                case 3:
                    if(!in_array($_GET['op'], array('rz','rz_save'))) {
                    $this->show_join_message('认证失败！<a href="'.SHOP_SITE_URL.DS.'index.php?act=getmemberstatus&op=rz">重新审核</a> ', FALSE);
                    }
                    break;
            }
        }
    }

	/**
	 * 选择认证
	 */
	public function indexOp() {

        Tpl::showpage('getmemberstatus');
	}
        
        //认证第三方采购员
        public function rzOp(){
        //获取城市中心地址
        $model = Model();
        $city_center_where['city_state'] = 1;
        $city_center =  $model->table('city_centre')->where($city_center_where)->select();
        $model_store_joinin = Model('member_status');
        $joinin_detail_where['member_id'] = $_SESSION['member_id'];
        $joinin_detail = $model_store_joinin->where($joinin_detail_where)->find();
        if($joinin_detail != false){
            Tpl::output('data',$joinin_detail);
        }
        Tpl::output('city', $city_center);
        Tpl::output('step', '1');
        Tpl::output('sub_step', 'step1');
        Tpl::showpage('getmemberstatus.rz');
        }
        
        /*
         * 第三方认证提交
         */
        public function rz_saveOp(){
           if(!empty($_POST)){
               $insert_data['name']                             = htmlspecialchars($_POST['name']);
               $insert_data['job_name']                         = htmlspecialchars($_POST['job_name']);
               $insert_data['company_name']                     = htmlspecialchars($_POST['company_name']);
               $insert_data['product_name']                     = htmlspecialchars($_POST['product_name']);
               $insert_data['city_centre']                      = htmlspecialchars($_POST['city_centre']);
               $insert_data['member_id'] = $_SESSION['member_id'];
               $insert_data['member_name'] = $_SESSION['member_name'];
               $insert_data['rz_status'] = 1;
               if($_FILES['business_licence_number_electronic']['name']){
                   $insert_data['business_licence_number_electronic'] = $this->upload_image('business_licence_number_electronic');
               }
               $model = Model('member_status');
               if($_POST['news_zx'] == 1){
                   $add = $model->where('member_id='.$_SESSION['member_id'])->update($insert_data);
               }else{
                $add = $model->insert($insert_data);
               }
               if($add != false){
                   redirect(SHOP_SITE_URL.DS.'index.php?act=getmemberstatus&op=index');exit;
               }else{
                   showMessage('申请认证失败，请稍后尝试！');
               }
           }else{
               redirect(SHOP_SITE_URL.DS.'index.php?act=getmemberstatus&op=index');exit;
           }
        }
        /*
         * 图片上传
         */
        private function upload_image($file) {
                $pic_name = '';
                $upload = new UploadFile();
                $uploaddir = ATTACH_PATH.DS.'store_joinin'.DS;
                $upload->set('default_dir',$uploaddir);
                $upload->set('allow_type',array('jpg','jpeg','gif','png'));
                if (!empty($_FILES[$file]['name'])){
                    $result = $upload->upfile($file);
                    if ($result){
                        $pic_name = $upload->file_name;
                        $upload->file_name = '';
                    }
                }
            return $pic_name;
        }
        /*
         * 展示图片
         */
        private function show_join_message($message, $btn_next = FALSE, $step = '1') {
            Tpl::output('joinin_message', $message);
            Tpl::output('btn_next', $btn_next);
            Tpl::output('step', $step);
            Tpl::output('sub_step', 'step3');
            Tpl::showpage('getmemberstatus.rz');
        exit;
    }
        

}
