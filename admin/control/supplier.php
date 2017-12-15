<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/12/15
 * Time: 上午11:06
 * 供应商认证审核操作
 */
class supplierControl extends SystemControl{

    private $model;

    private $supplier_model;

    private $city_list = array();

    private $join_type = array(
        STORE_JOIN_STATE_EMAIL      =>'邮箱未认证',
        STORE_JOIN_STATE_RZ         =>'认证申请',
        STORE_JOIN_STATE_RZSUCCESS  =>'认证成功',
    );

    public function __construct(){
        parent::__construct();
        $top_list = array(
            'join_list'=>'认证申请审核',
            'store_list'=>'开店申请审核',
        );
        $this->model = Model();
        $this->supplier_model = Model('supplier');
        $where = $this->admin_info['cityid'] > 0 ? " id = '".$this->admin_info['cityid']."'":"";
        $this->city_list = $this->model->table('city_centre')->where($where)->select();
        Tpl::output('top_list', $top_list);
    }



    //供应商等待审核列表
    public function join_listOp(){
        //查询列表数据
        $where = " 1 = 1 ";
        //名称
        if(!empty($_GET['store_name'])) {
            $where.= " and supplier.company_name like '%".$_GET['store_name']."%'";
            Tpl::output('store_name', $_GET['store_name']);
        }
        //账号
        if(!empty($_GET['number'])) {
            $where.= " and store_joinin.member_name = '".$_GET['number']."'";
            Tpl::output('number', $_GET['number']);
        }
        //城市公司
        if(!empty($_GET['city'])) {
            $where.= " and store_joinin.city_center = '".$_GET['city']."'";
            Tpl::output('city', $_GET['city']);
        }
        //状态
        if(!empty($_GET['type'])) {
            Tpl::output('type', $_GET['type']);
        }
        $where.= !empty($_GET['type']) ? " and store_joinin.joinin_state = '".$_GET['type']."'":" and store_joinin.joinin_state = '".STORE_JOIN_STATE_RZ."'";
        //管理员权限控制
        $where.= $this->admin_info['cityid'] > 0 ? " and city_center = '".intval($this->admin_info['cityid'])."'":"";
        $field = "store_joinin.member_id,supplier.company_name,store_joinin.member_name,company_address,store_joinin.city_center,store_joinin.joinin_state,city_name";
        $list = $this->supplier_model->join_list($where, 10, 'joinin_state asc',$field);
        Tpl::output('list', $list);
        Tpl::output('join_type', $this->join_type);
        Tpl::output('city_list', $this->city_list);
        Tpl::output('page',$this->supplier_model->showpage('2'));
        Tpl::showpage('supplier/join_list');
    }

    /**
     * 审核详细页
     */
    public function examine_joinOp(){
        if(empty($_GET['city'])){
            showMessage('城市公司参数错误','index.php?act=supplier&op=join_list');exit;
        }
        if(empty($_GET['member'])){
            showMessage('供应商参数错误','index.php?act=supplier&op=join_list');exit;
        }
        $field = "store_joinin.member_id,store_joinin.city_center,supplier.*,city_name,";
        $field.= "supplier_information.city_contacts_name,supplier_information.city_contacts_phone,supplier_information.account,supplier_information.settlement,";
        $field.= "supplier_information.account_type,supplier_information.settlement_type";
        $where = "store_joinin.member_id = '".$_GET['member']."' and store_joinin.city_center = '".$_GET['city']."'";
        $list = $this->supplier_model->join_detail($where,$field);
        Tpl::output('list',$list);
        Tpl::showpage('supplier/examine_join');
    }


    /**
     * 审核
     */
    public function store_verifyOp(){
        var_dump($_POST);
        if ($_POST['verify_type'] == "pass") {
            if (empty($_FILES['rz_evaluation_audit']['name'])) {
                showMessage('请先上传审核评估', '');
                exit;
            }
        }
        $join_data = $this->model->table('store_joinin')->where("member_id = '".$_POST['member_id']."' and city_center = '".$_POST['city_id']."'")->find();
        if(empty($join_data)){
            showMessage('供应商参数错误','index.php?act=supplier&op=join_list');exit;
        }
        $this->model->beginTransaction();
        //1、跟新join数据
        $up_join_data = array(
            'joinin_state'=>$_POST['verify_type'] == 'pass' ? STORE_JOIN_STATE_RZSUCCESS:STORE_JOIN_STATE_FNO,
            'joinin_message'=>$_POST['joinin_message'],
            'rz_evaluation_audit'=> empty($_FILES['rz_evaluation_audit']['name']) ? "":$this->upload_file('rz_evaluation_audit'),
        );
        $join_rest = $this->model->table('store_joinin')->where("member_id = '".$_POST['member_id']."' and city_center = '".$_POST['city_id']."'")->update($up_join_data);
        if($join_rest && ($_POST['verify_type'] == 'pass')){
            //判定是不是首次认证供应商
            $join_num = $this->model->table('store_joinin')->where("member_id = '".$_POST['member_id']."'")->count();
            if($join_num > 1){
                //二次认证处理

            }
        }else{
            $join_rest ? $this->model->commit():$this->model->rollback();
        }
    }


    private function upload_file($file) {
        $pic_name = '';
        $upload = new UploadFile();
        $uploaddir = ATTACH_PATH.DS.'store_joinin'.DS;
        $upload->set('default_dir',$uploaddir);
        $upload->set('allow_type',array('jpg','jpeg','gif','png','word','pdf','docx','xlsx','html'));
        if (!empty($_FILES[$file]['name'])){
            $result = $upload->upfile_exe($file);
            if ($result){
                $pic_name = $upload->file_name;
                $upload->file_name = '';
            }
        }
        return $pic_name;
    }

}