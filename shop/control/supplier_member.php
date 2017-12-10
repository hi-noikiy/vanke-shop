<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/9
 * Time: 13:09
 * 供应商会员中心
 */
class supplier_memberControl extends SupplierMemberControl{

    private $supplierData = array();

    public function __construct(){
        parent::__construct();
        $this->model = Model();
        $this->ptURoleId = MEMBER_IDENTITY_ONE;
        $this->member_id = $_SESSION['member_id'];
        $this->memberData = $this->model->table('member')->where("member_id = '".$this->member_id."'")->find();
        $this->supplierData = $this->model->table('supplier')->where("member_id = '".$this->member_id."'")->find();
        //Tpl::setLayout('store_joinin_layout');
    }

    //供应商首页
    public function indexOp(){
        Tpl::output('supplier',$this->supplierData);
        Tpl::showpage('supplier_index');
    }

    //供应商认证记录
    public function join_logOp(){
        //获取当前城市的首次认证数据信息
        $field = "store_joinin.member_id,store_joinin.joinin_state,store_joinin.store_state,store_joinin.city_center,city_contacts_name,city_contacts_phone,city_name,account_bank,settlement_bank";
        $on = "store_joinin.member_id = supplier_information.member_id and store_joinin.city_center = supplier_information.join_city,";
        $on.="store_joinin.city_center = city_centre.id";
        if($this->supplierData['first_city_id'] > 0){
            $first_where = "store_joinin.member_id = '".$this->member_id."' and store_joinin.city_center = '".$this->supplierData['first_city_id']."'";
            //获取所有申请记录
            $all_where = "store_joinin.member_id = '".$this->member_id."' and store_joinin.city_center != '".$this->supplierData['first_city_id']."'";
            $all_list = $this->model->table('store_joinin,supplier_information,city_centre')->field($field)->join('left,left')->on($on)->where($all_where)->select();
            $new_all_list = array();
            if(!empty($all_list) && is_array($all_list)){
                foreach ($all_list as $val){
                    //追加开户银行信息
                    $val['account_bank_info'] = $val['account_bank'] == '0' ? array():$this->get_account_bank($val['account_bank']);
                    //追加结算银行信息
                    $val['settlement_bank_info'] = $val['settlement_bank'] == '0' ? array():$this->get_settlement_bank($val['settlement_bank']);
                    $new_all_list[] = $val;
                }
            }
            Tpl::output('all_list',$new_all_list);
        }else{
            $first_where = "store_joinin.member_id = '".$this->member_id."'";
        }
        $first_list = $this->model->table('store_joinin,supplier_information,city_centre')->field($field)->join('left,left')->on($on)->where($first_where)->find();
        //追加开户银行信息
        $first_list['account_bank_info'] = $first_list['account_bank'] == '0' ? array():$this->get_account_bank($first_list['account_bank']);
        //追加结算银行信息
        $first_list['settlement_bank_info'] = $first_list['settlement_bank'] == '0' ? array():$this->get_settlement_bank($first_list['settlement_bank']);
        Tpl::output('first_list',$first_list);
        Tpl::showpage('supplier_join_log');
    }

    //开店申请
    public function join_storeOp(){
        //查询已认证的城市公司是否还有尚未开店的数据
        $join_where = "joinin_state = '".STORE_JOIN_STATE_RZSUCCESS."' and store_state not in('".STORE_JOIN_STATE_RZHKD."','".STORE_JOIN_STATE_FINAL."') and member_id = '".$this->member_id."'";
        $join_list = $this->model->table('store_joinin')->where($join_where)->select();
        if(!empty($join_list) && is_array($join_list)){
            //获取城市中心信息
            $city_ID = array();
            foreach ($join_list as $join_val){
                $city_ID[] = $join_val['city_center'];
            }
            $city_list = $this->model->table('city_centre')->where("id in('".implode("','",$city_ID)."')")->select();
            Tpl::output('city_list',$city_list);

            //店铺分类
            $all_store_join = $this->model->table('store_joinin')->where("member_id = '".$this->member_id."'")->order("char_length(sc_id) desc")->find();

            $model_store = Model('store_class');
            $store_class = $model_store->getStoreClassList(array(),'',false);
            if(!empty($store_class) && is_array($store_class)){
                $frist_class_id = unserialize($all_store_join['store_class_ids']);
                $new_store_class = array();
                foreach ($store_class as $val){
                    if(is_array($frist_class_id) && in_array($val['sc_id'].",", $frist_class_id)){
                        $new_store_class['y'][] = $val;
                    }else{
                        $new_store_class['n'][] = $val;
                    }
                }
            }
            if(count($join_list) == '1'){
                $store_data = array("store_name"=>'',"suplier_name"=>$this->memberData['member_name']);
            }else{
                $store_join_first = $this->model->table('store_joinin')->where("member_id = '".$this->member_id."' and city_center = first_city_id")->find();
                $store_data = array("store_name"=>$store_join_first['store_name'],"suplier_name"=>$this->memberData['member_name']);
            }
            Tpl::output('store_data', $store_data);
            Tpl::output('store_class', $new_store_class);
        }else{
            Tpl::output('join_city', 1);
        }
        Tpl::showpage('supplier_join_store');
    }

    //保存开店申请
    public function store_addOp(){
        $reData = array('code'=>'-1');
        if(!empty($_POST)){
            $upda_join_store_where = array(
                'member_id'=>$this->member_id,
            );
            //处理多个城市同时开店
            if(!empty($_POST['city']) && is_array($_POST['city'])){
                $city_where_string = implode(',',$_POST['city']);
                $upda_join_store_where['city_center'] = array('in',$city_where_string);
            }
            $store_join_first = $this->model->table('store_joinin')->where("member_id = '".$this->member_id."' and city_center = first_city_id")->find();
            $upda_join_store = $this->get_store_join($_POST['class'],$_POST['city'],$store_join_first);
            //获取是否需要更新商户名
            $join_num = $this->model->table('store_joinin')->where("member_id = '".$this->member_id."'")->count();
            if($join_num == '1' && !empty($_POST['store_name'])){
                $upda_join_store['store_name'] = $_POST['store_name'];
            }else{
                $upda_join_store['store_name'] = $store_join_first['store_name'];
            }
            $rest = $this->model->table('store_joinin')->where($upda_join_store_where)->update($upda_join_store);
            $reData['code'] = $rest ? "1":"-1";
        }
        echo json_encode($reData);
    }

    //城市公司联系人管理
    public function contacts_listOp(){
        $field = "store_joinin.member_id,store_joinin.joinin_state,store_joinin.store_state,store_joinin.city_center,city_name,city_contacts_name,city_contacts_phone";
        $on = "store_joinin.member_id = supplier_information.member_id and store_joinin.city_center = supplier_information.join_city,";
        $on.="store_joinin.city_center = city_centre.id";
        $all_where = "store_joinin.member_id = '".$this->member_id."'";
        $all_list = $this->model->table('store_joinin,supplier_information,city_centre')->field($field)->join('left,left')->on($on)->where($all_where)->select();
        Tpl::output('all_list',$all_list);
        Tpl::showpage('supplier_contacts_list');
    }


    //开户行管理
    public function account_listOp(){
        $field = "store_joinin.member_id,store_joinin.joinin_state,store_joinin.store_state,store_joinin.city_center,city_name,account_bank,settlement_bank";
        $on = "store_joinin.member_id = supplier_information.member_id and store_joinin.city_center = supplier_information.join_city,";
        $on.="store_joinin.city_center = city_centre.id";
        $all_where = "store_joinin.member_id = '".$this->member_id."'";
        $all_list = $this->model->table('store_joinin,supplier_information,city_centre')->field($field)->join('left,left')->on($on)->where($all_where)->select();
        $new_all_list = array();
        if(!empty($all_list) && is_array($all_list)){
            foreach ($all_list as $val){
                //追加开户银行信息
                $val['account_bank_info'] = $val['account_bank'] == '0' ? array():$this->get_account_bank($val['account_bank']);
                $new_all_list[] = $val;
            }
        }
        Tpl::output('all_list',$new_all_list);
        Tpl::showpage('supplier_account_list');
    }


    //结算行管理
    public function settlement_listOp(){
        $field = "store_joinin.member_id,store_joinin.joinin_state,store_joinin.store_state,store_joinin.city_center,city_name,account_bank,settlement_bank";
        $on = "store_joinin.member_id = supplier_information.member_id and store_joinin.city_center = supplier_information.join_city,";
        $on.="store_joinin.city_center = city_centre.id";
        $all_where = "store_joinin.member_id = '".$this->member_id."'";
        $all_list = $this->model->table('store_joinin,supplier_information,city_centre')->field($field)->join('left,left')->on($on)->where($all_where)->select();
        $new_all_list = array();
        if(!empty($all_list) && is_array($all_list)){
            foreach ($all_list as $val){
                //追加结算银行信息
                $val['settlement_bank_info'] = $val['settlement_bank'] == '0' ? array():$this->get_settlement_bank($val['settlement_bank']);
                $new_all_list[] = $val;
            }
        }
        Tpl::output('all_list',$new_all_list);
        Tpl::showpage('supplier_settlement_list');
    }


    //认证其它城市公司
    public function join_cityOp(){
        //获取首次认证是否已经审核
        $first_join = $this->model->table('store_joinin')->where("member_id = '".$this->member_id."' and city_center = first_city_id")->find();
        if($first_join['joinin_state'] != STORE_JOIN_STATE_RZ) {
            $join_where = "joinin_state != '" . STORE_JOIN_STATE_FNO . "' and member_id = '" . $this->member_id . "'";
            $join_list = $this->model->table('store_joinin')->field("city_center")->where($join_where)->select();
            $city_where = '';
            if (!empty($join_list) && is_array($join_list)) {
                $join_city_list = array_column($join_list, 'city_center');
                $city_where .= "id not in('" . implode("','", $join_city_list) . "')";
            }
            //获取所有联系人信息
            $contacts_list = $this->model->table('supplier_information')->field("city_contacts_name,city_contacts_phone")->where("member_id = '" . $this->member_id . "'")->select();
            $city_list = $this->model->table('city_centre')->where($city_where)->select();
            //获取开户银行信息
            $account_bank_list = $this->model->table('supplier_account_bank')->field("id,account_number,bank_name")->where("member_id = '" . $this->member_id . "'")->select();
            //获取结算银行信息
            $settlement_bank_list = $this->model->table('supplier_settlement_bank')->field("id,settlement_number,bank_name")->where("member_id = '" . $this->member_id . "'")->select();
            Tpl::output('contacts_list', $this->remove_duplicate($contacts_list, 'city_contacts_name'));
            Tpl::output('account_bank_list', $this->remove_duplicate($account_bank_list, 'account_number'));
            Tpl::output('settlement_bank_list', $this->remove_duplicate($settlement_bank_list, 'settlement_number'));
            Tpl::output('city_list', $city_list);
        }else{
            Tpl::output('join_type', 'line');
        }
        Tpl::showpage('supplier_join_city');
    }

    //保存认证城市数据信息
    public function city_addOp(){
        $restData = array('code'=>'-1','msg'=>'');
        if(!empty($_POST)){
            $this->model->beginTransaction();
            $first_join = $this->model->table('store_joinin')->where("member_id = '".$this->member_id."' and city_center = first_city_id and joinin_state = '".STORE_JOIN_STATE_RZSUCCESS."'")->find();
            //查询当前记录是否存在（审核拒绝触发）
            $join_old = $this->model->table('store_joinin')->field("city_center")->where("member_id = '".$this->member_id."' and city_center = '".$_POST['city']."'")->find();
            if(!empty($join_old)){
                //更新状态即可
                $rest =  $this->model->table('store_joinin')->where("member_id = '".$this->member_id."' and city_center = '".$_POST['city']."'")->update(array('joinin_state'=>STORE_JOIN_STATE_RZ,'joinin_message'=>''));
            }else{
                //添加  first_city_id
                $join_data = array(
                    'member_id'         =>$this->member_id,
                    'member_name'       =>$this->memberData['member_name'],
                    'store_class_ids'   =>serialize(array()),
                    'store_class_names' =>serialize(array()),
                    'joinin_state'      =>STORE_JOIN_STATE_RZ,
                    'city_center'       =>$_POST['city'],
                    'first_city_id'     =>empty($first_join) ? $_POST['city']:$first_join['first_city_id'],
                );
                $rest = $this->model->table('store_joinin')->insert($join_data);
                if($rest){
                    $information_log = $this->model->table('supplier_information')->where("member_id = '".$this->member_id."' and join_city = '".$_POST['city']."'")->find();
                    $other_data = array(
                        'city_contacts_name'    =>$_POST['contacts_name'],
                        'city_contacts_phone'   =>$_POST['contacts_phone'],
                        'account_bank'          =>$_POST['account_bank'],
                        'settlement_bank'       =>$_POST['settlement_bank'],
                    );
                    if(!empty($information_log)){
                        $this->model->table('supplier_information')->where("id = '".$information_log['id']."'")->update($other_data);
                    }else{
                        $other_data['member_id'] = $this->member_id;
                        $other_data['join_city'] = $_POST['city'];
                        $this->model->table('supplier_information')->insert($other_data);
                    }
                }
            }
            $restData['code'] = $rest ? $this->model->commit():$this->model->rollback();
            $restData['code'] = $rest ? "1":"-1";
        }
        echo json_encode($restData);
    }

    //去除数组中的重复数据 array_unique($a
    private function remove_duplicate($array = array(),$field = ''){
        $result=array();
        if(!empty($array) && is_array($array) && !empty($field)){
            $source = array();
            foreach ($array as $k=>$v){
                $source[$k] = $v[$field];
            }
            $new_data = array_unique($source);
            foreach ($new_data as $key=>$val){
                $result[] = $array[$key];
            }
        }
        return $result;
    }


    //获取绑定银行的信息数据
    private function get_account_bank($id){
        return $this->model->table('supplier_account_bank')->where("id = '".$id."'")->find();
    }

    private function get_settlement_bank($id){
        return $this->model->table('supplier_settlement_bank')->where("id = '".$id."'")->find();
    }

    /**
     * 针对开店申请进行数据处理
     * $data 前端POST提交的数据
     **/
    private function get_store_join($class_id=array(),$city_id=array(),$store_join){
        if (!empty($class_id) && !empty($city_id)) {
            //获取原始数据
            if(!empty($store_join)){
                if(!empty($class_id)){
                    $new_class_id = array_unique(array_merge($class_id,explode(',',$store_join['sc_id'])));
                }else{
                    $new_class_id = explode(',',$store_join['sc_id']);
                }
            }else{
                $new_class_id = $class_id;
            }

            //定义初始化class数组
            $store_class_ids = array();
            $store_class_names = array();
            if (is_array($new_class_id)) {
                foreach ($new_class_id as $value) {
                    $store_class_ids[] = $value . ',';
                    $class_data = $this->model->table("goods_class")->field('gc_name')->where("gc_id = '" . $value . "'")->find();
                    $store_class_names[] = $class_data['gc_name'] . ',';
                }
            }
            //取最小级分类最新分佣比例
            $sc_ids = array();
            foreach ($store_class_ids as $v) {
                $v = explode(',', trim($v, ','));
                if (!empty($v) && is_array($v)) {
                    $sc_ids[] = end($v);
                }
            }
            if (!empty($sc_ids)) {
                $store_class_commis_rates = array();
                $goods_class_list = Model('goods_class')->getGoodsClassListByIds($sc_ids);
                if (!empty($goods_class_list) && is_array($goods_class_list)) {
                    $sc_ids = array();
                    foreach ($goods_class_list as $v) {
                        $store_class_commis_rates[] = $v['commis_rate'];
                    }
                }
            }
            $param = array();
            //序列化class数据
            $param['store_class_ids'] = serialize($store_class_ids);
            $param['store_class_names'] = serialize($store_class_names);
            $param['joinin_year'] = 100;//intval($_POST['joinin_year']);

            if (is_array($store_class_commis_rates)) {
                $param['store_class_commis_rates'] = implode(',', $store_class_commis_rates);
            } else {
                $param['store_class_commis_rates'] = $store_class_commis_rates;
            }

            //取店铺等级信息
            $grade_list = rkcache('store_grade', true);
            $data['sg_id'] = "1";
            $data['sg_name'] = "系统默认";
            if (!empty($grade_list[$data['sg_id']])) {
                $param['sg_id'] = $data['sg_id'];
                $param['sg_name'] = $grade_list[$data['sg_id']]['sg_name'];
                $param['sg_info'] = serialize(array('sg_price' => $grade_list[$data['sg_id']]['sg_price']));
            }
            if (!empty($grade_list[$data['sg_id']])) {
                $param['sg_id'] = $data['sg_id'];
                $param['sg_name'] = $grade_list[$data['sg_id']]['sg_name'];
                $param['sg_info'] = serialize(array('sg_price' => $grade_list[$data['sg_id']]['sg_price']));
            }

            if (!empty($new_class_id) && is_array($new_class_id)) {
                $sc_id_data = $sc_name_data = $sc_bail_data = array();
                foreach ($new_class_id as $v) {
                    $store_class_info = Model('store_class')->getStoreClassInfo(array('sc_id' => intval($v)));
                    if ($store_class_info) {
                        $sc_id_data[] = $store_class_info['sc_id'];
                        $sc_name_data[] = $store_class_info['sc_name'];
                        $sc_bail_data[] = $store_class_info['sc_bail'];
                    }
                }
                $param['sc_id'] = implode(',', $sc_id_data);
                $param['sc_name'] = implode(',', $sc_name_data);
                $param['sc_bail'] = implode(',', $sc_bail_data);
            }
            //增加店铺类型
            $param['store_type_id'] = "8";//$_POST['st_id'];
            $param['store_type_name'] = "专营店";//$_POST['st_name'];
            $param['store_state'] = STORE_JOIN_STATE_RZHKD;
            //店铺应付款
            $param['paying_amount'] = floatval($grade_list[$data['sg_id']]['sg_price']) * $param['joinin_year'] + floatval($param['sc_bail']);
            return $param;
        }
    }
}
