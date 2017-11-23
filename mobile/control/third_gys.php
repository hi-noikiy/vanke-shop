<?php
/**
 * 第三方供应商
 *
 */


class third_gysControl extends mobileHomeControl{

    public function __construct() {
        parent::__construct();
        //判断用户是否登录
        
        if($_SESSION['member_id'] <= 0 || empty($_SESSION['member_id'])){
            echo 100002;exit;
        }
    }
    public function look_log(){
        
    }
    //查询用户是否已经申请过
    public function indexOp() {
       $model = Model('member_status');
       $log = $model->where('member_id='.$_SESSION['member_id'])->field('rz_status')->find();
       if($log){
           if($log['rz_status'] == 1){
               //认证中
               echo 100005;exit;
           }else if($log['rz_status'] == 2){
               echo 101010;exit;
           }else{
               echo 100006;exit;
           }
       }
    }

    //上传供应商资料
    public function addOp(){
        $data['name'] = $_POST['name'];
        $data['job_name'] = $_POST['job_name'];
        $data['city_centre'] = $_POST['city_centre'];
        $data['company_name'] = $_POST['company_name'];
        $data['product_name'] = $_POST['product_name'];
        $data['business_licence_number_electronic'] = $_POST['upload_yinyeimg'];
        $data['member_id'] = $_SESSION['member_id'];
        $data['member_name'] = $_SESSION['wap_member_info']['username'];
        $data['rz_status'] = 1;
        foreach($data as $rows){
            if(empty($rows)){
                echo 100001;exit;
            }
        }
        $model = Model('member_status');
        $log = $model->where('member_id='.$_SESSION['member_id'])->field('rz_status')->find();
        if($log['rz_status'] == 2){
            echo 100007;exit;
        }
        if($log){
            $add = $model->where('member_id='.$_SESSION['member_id'])->update($data);
        }else{
            $add = $model->insert($data);
        }
        if($add != false){
            echo 101010;exit;
        }
    }
    //获取城市中心
    public function getcity_idOp(){
        $model = Model('city_centre');
        $city = $model->field('id,city_name')->select();
        echo json_encode($city);exit;
    }
    //注销
    public function logoutOp(){
            session_unset();//
            session_destroy();//
            echo 1;exit;
    }
    

}
