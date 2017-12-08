<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/12/4
 * Time: 下午3:41
 * 用户发票处理
 */
class member_invoiceControl extends BaseMemberControl{

    /**
     * 新增发票信息
     *
     */
    public function add_invOp(){
        if(!empty($_POST) && is_array($_POST)){
            $model_inv = Model('invoice');
            if($_POST['inv_type'] == '1'){
                $data = array(
                    'inv_state'    =>'1',
                    'inv_title'    =>$_POST['inv_person'] == '2' ? '个人':$_POST['pt_company'],
                    'inv_content'  =>'明细',
                    'inv_code'     =>$_POST['inv_person'] == '2' ? '':$_POST['taxpayer'],
                );
            }else{
                $data = array(
                    'inv_state'    =>'2',
                    'inv_company'  =>$_POST['ze_company'],
                    'inv_code'     =>$_POST['taxpayer'],
                    'inv_reg_addr' =>$_POST['reg_addr'],
                    'inv_reg_phone'=>$_POST['reg_phone'],
                    'inv_reg_bname'=>$_POST['reg_bname'],
                    'inv_reg_baccount'=>$_POST['reg_baccount'],
                );
            }
            //解析省份信息
            $province = Model()->table('area')->field('area_name')->where("area_id = '".$_POST['province']."'")->find();
            $city = Model()->table('area')->field('area_name')->where("area_id = '".$_POST['city']."'")->find();
            $county = Model()->table('area')->field('area_name')->where("area_id = '".$_POST['county']."'")->find();
            $city_data = array(
                $province['area_name'],$city['area_name'],$county['area_name'],
            );
            $data['inv_code'] = $_POST['taxpayer'];
            $data['inv_rec_name'] = $_POST['rec_name'];
            $data['inv_rec_mobphone'] = $_POST['rec_mobphone'];
            $data['inv_rec_province'] = implode(" ",$city_data);
            $data['inv_goto_addr'] = $_POST['rec_address'];
            $data['member_id'] = $_SESSION['member_id'];
            $data = strtoupper(CHARSET) == 'GBK' ? Language::getGBK($data) : $data;
            $insert_id = $model_inv->addInv($data);
            if ($insert_id) {
                $rs_data = array(
                    'inv_id'        =>$insert_id,
                    'inv_state'     =>$data['inv_state'],
                    'inv_state_str' =>$data['inv_state'] == '1' ? '普通发票':'增值税发票',
                    'inv_state_css' =>$data['inv_state'] == '1' ? 'layui-btn-warm':'layui-btn-danger',
                    'inv_title'     =>$data['inv_state'] == '1' ? $data['inv_title']:$data['inv_company'],
                    'inv_content'   =>$data['inv_state'] == '1' ? $data['inv_content']:$data['inv_code'].'&nbsp;&nbsp;'.$data['inv_rec_name'],
                );
                $rest_data = array('code'=>'1','msg'=>'','list'=>$rs_data);
            } else {
                $rest_data = array('code'=>'-1','msg'=>'');
            }
            echo json_encode($rest_data);
        }
    }

    //删除发票信息
    public function delInvOp(){
        $rest = array(
            'code'  =>'-1',
            'msg'   =>'发票信息有误，请重新操作或联系管理员',
            'data'  =>'',
        );
        $where = "inv_id = '".$_POST['id']."' and member_id = '".$_SESSION['member_id']."'";
        $invData = Model()->table('invoice')->where($where)->find();
        if(!empty($invData)){
            $res = Model()->table('invoice')->where($where)->delete();
            if($res){
                $rest['code'] = "1";
                $rest['msg'] = "success";
            }else{
                $rest['code'] = "-1";
                $rest['msg'] = "删除失败，请重新操作";
            }
        }
        echo json_encode($rest);
    }
}