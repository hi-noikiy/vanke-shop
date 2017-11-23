<?php
/**
 * 
 * 第三方采购员认证
 *
 */


class third_memberControl extends SystemControl{
    public function __construct() {
        parent::__construct ();
    }

    /**
     *  列表
     */
    public function indexOp() {
        $model = Model('member_status');
        
        if($_GET['states'] == 'state'){
            $condition['rz_status'] = 1; 
        }
        //获取城市中心列表页
        $member_list = $model->where($condition)->page(10)->order('id DESC')->select();
        
        
        Tpl::output('member_list',$member_list);
        Tpl::output('page',$model->showpage());
        
        Tpl::showpage('third_member_index');
    }
    
    /*
     * 查看详细信息
     */
    public function detailOp(){
       $model = Model('member_status');
        
       $id = htmlspecialchars($_GET['id']);
       
       $news = $model->where('id='.$id)->find();
       //查询城市中心
       $city_model = Model('city_centre');
       
       $city = $city_model->where('id='.$news['city_centre'])->field('city_name')->find();
       Tpl::output('city',$city);
       Tpl::output('news',$news);
       Tpl::showpage('third_member_detail');
    }
    
    /*
     * 审核操作
     */
    public function saveOp(){
        //判断是拒绝还是通过
        if($_POST['verify_type'] == 'pass'){
            $data['rz_status'] = 2;
        }else{
            $data['rz_status'] = 3;
        }
        $model = Model('member_status');
        $up = $model->where('member_id='.$_POST['member_id'])->update($data);
        if($_POST['verify_type'] == 'pass'){
            //更新会员状态
            $updata_member['role_id'] = MEMBER_IDENTITY_FIVE;
            $member_model = Model('member');
            $up_memberstatus = $member_model->where('member_id='.$_POST['member_id'])->update($updata_member);
        }
        if($up != false && $up_memberstatus != false){
            if($_POST['verify_type'] == 'pass'){
                showMessage('申请通过成功！','index.php?act=third_member&op=index');
            }else{
                showMessage('审核拒绝成功！','index.php?act=third_member&op=index');
            }
        }else{
            showMessage('审核失败请稍后尝试');
        }
    }
   

}
