<?php
/**
 * 城市中心维护
 *
 *
 *
 *
 */


class city_mangesControl extends SystemControl{
    public function __construct() {
        parent::__construct ();
    }

    /**
     * 城市中心维护 列表
     */
    public function indexOp() {
        $model = Model();
        
        //获取城市中心列表页
        $city_list = $model->table('city_centre')->page(10)->select();
        
        Tpl::output('city',$city_list);
        Tpl::output('page',$model->showpage());
        
        Tpl::showpage('city.setting');
    }
    
    /**
     * 城市中心维护 编辑
     */
    public function addOp() {
       
        $model = Model();
        if (chksubmit()){
            //更新数据
            $insert_array = array();
            $insert_array['city_name'] = $_POST['city_name'];
            $insert_array['city_state'] = $_POST['city_state'];
            $insert_array['back'] = $_POST['back'];
            $insert_array['bukrs'] = $_POST['bukrs'];
            $insert_array['comtxt'] = $_POST['comtxt'];

            $result = $model->table('city_centre')->insert($insert_array);
            if ($result != false){
                    showMessage('新增城市中心成功');
            }else {
                    showMessage('新增城市中心失败');
            }
        }
        Tpl::showpage('city.edit');
    }
    
//    /**
//     * 城市中心维护 删除
//     */
//    public function delOp() {
//        $model = Model();
//        $city_id = intval($_GET['id']);
//        if($city_id > 0){
//            
//            $model = Model();
//            $city_del = $model->table('city_centre')->where('id='.$city_id)->delete();
//            if ($city_del != false){
//                        showMessage('删除城市中心成功');
//                }else {
//                        showMessage('删除城市中心失败');
//                }
//        }else{
//            showMessage("参数错误！请联系管理员");
//        }
//    }
    
     /**
     * 城市中心维护 编辑
     */
    public function editOp() {
       
        $city_id = intval($_GET['id']);
        
        if($city_id > 0){
            
            $model = Model();
            if (chksubmit()){
                //更新数据
                $update_array = array();
                $update_array['city_name'] = $_POST['city_name'];
                $update_array['city_state'] = $_POST['city_state'];
                $update_array['back'] = $_POST['back'];
                $update_array['bukrs'] = $_POST['bukrs'];
                $update_array['comtxt'] = $_POST['comtxt'];
                
                $result = $model->table('city_centre')->where('id='.$city_id)->update($update_array);
                if ($result === true){
                        showMessage('修改城市中心成功');
                }else {
                        showMessage('修改城市中心失败');
                }
            }
        
            $city = $model->table('city_centre')->where('id='.$city_id)->find();
            
        }else{
            showMessage("参数错误！请联系管理员");
        }
        
        
        Tpl::output('city',$city);
        Tpl::showpage('city.edit');
    }
   

}
