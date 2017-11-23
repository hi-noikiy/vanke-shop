<?php
/**
 * 卖家账号模型
 *
 * 
 *
 *
 *
 */

class agentModel extends Model{

    public function __construct(){
        parent::__construct('agent');
    }

	/**
	 * 读取列表 
	 * @param array $condition
	 *
	 */
	public function getSellerList($condition, $page='', $order='', $field='*') {
        $result = $this->field($field)->where($condition)->page($page)->order($order)->select();
        return $result;
	}

   public function getCount($condition) {
        return $this->where($condition)->count();
    }

    /**
	 * 读取单条记录
	 * @param array $condition
	 *
	 */
    public function getSellerInfo($condition) {
        $result = $this->where($condition)->find();
        return $result;
    }

	/*
	 *  判断是否存在 
	 *  @param array $condition
     *
	 */
	public function isSellerExist($condition) {
        $result = $this->getSellerInfo($condition);
        if(empty($result)) {
            return FALSE;
        } else {
            return TRUE;
        }
	}

	/*
	 * 增加 
	 * @param array $param
	 * @return bool
	 */
    public function addSeller($param){
        return $this->insert($param);	
    }
	
	/*
	 * 更新
	 * @param array $update
	 * @param array $condition
	 * @return bool
	 */
    public function editSeller($update, $condition){
        return $this->where($condition)->update($update);
    }
	
	/*
	 * 删除
	 * @param array $condition
	 * @return bool
	 */
    public function delSeller($condition){
        return $this->where($condition)->delete();
    }

///////////////////////////////
    
    public function getAgentInfoList($condition, $page='', $order){
//        $condition_str = $this->_condition($condition);
        $param = array(
            'table'=>'agent_joinin,agent',
            'field'=>'agent_joinin.*,agent.*',
            'where'=>' 1=1 '.$condition,
            'join_type'=>'left join',
            'join_on'=>array(
                'agent_joinin.member_id = agent.member_id',
            ),
            'order' => $order
        );
        $result = Db::select($param,$page);
        return $result;
    }


    public function getAgentList($condition, $page = null, $order = '', $field = '*', $limit = '') {
        $result = $this->field($field)->where($condition)->order($order)->limit($limit)->page($page)->select();
        return $result;
    }


    public function getAgentStoreIDs($condition){
        return $this->table('agent_store')->where($condition)->field('store_id')->select();
//        $comma_separated = implode(",", $array);
//        return $comma_separated;

//        $condition_str = $this->_condition($condition);
//        $param = array(
//            'table'=>'agent_store,agent',
//            'field'=>'agent_store.store_id',
//            'where'=>$condition_str,
//            'join_type'=>'left join',
//            'join_on'=>array(
//                'agent_store.agent_id = agent.seller_id',
//            )
//        );
//        $result = Db::select($param);
//        return $result;
    }


    public function getAgentStoreCommisTotal($condition){
//        $condition_str = $this->_condition($condition);
        return Db::query("select sum(ob_commis_totals) as commis_total,sum(ob_commis_return_totals) as commis_return_total from `".DBPRE."order_bill` where ob_store_id in(select store_id from `".DBPRE."agent_store` where agent_id=".$condition.")");

    }

    public function getGradeShopList($condition,$page=''){
        $condition_str = $this->_condition($condition);
        $param = array(
            'table'=>'agent_grade,agent',
            'field'=>'agent_grade.*,agent.*',
            'where'=>$condition_str,
            'join_type'=>'left join',
            'join_on'=>array(
                'agent_grade.sg_id = agent.grade_id',
            )
        );
        $result = Db::select($param,$page);
        return $result;
    }


    public function getAgentStore($condition){
        $agent_id = $condition['seller_id'];

        $id_array= array();
        $store_list = $this->table('agent_store')->where(array('agent_id'=> $agent_id))->field('store_id')->select();
        foreach($store_list as $k => $v){
            $id_array[] = $v['store_id'];
        }
        $store_ids = implode(",", $id_array);

        if (!empty($store_ids)) {
            $param = array(
                'table'=>'store',
                'field'=>' * ',
                'where'=> ' store_id in ('. $store_ids .')  '
            );
            $result = Db::select($param);
        }else{
            return array(array('oc_commis_totals'=>0,'oc_commis_return_totals'=>0,'oc_shipping_totals'=>0));
        }

        return $result;
    }


    /*
     * 获取代理订单列表
     */
    public function getAgentOrder($condition){
        $agent_id = $condition['seller_id'];

        $id_array= array();
        $store_list = $this->table('agent_store')->where(array('agent_id'=> $agent_id))->field('store_id')->select();
        foreach($store_list as $k => $v){
            $id_array[] = $v['store_id'];
        }
        $store_ids = implode(",", $id_array);

        $time_fillter = '';
        if(!empty($condition['start_time'])){
            $time_fillter = ' and finnshed_time >= '. $condition['start_time'].' and finnshed_time <= '. $condition['end_time'];
        }

        if (!empty($store_ids)) {
            $param = array(
                'table'=>'order',
                'field'=>' * ',
                'where'=> ' order_state='.ORDER_STATE_SUCCESS.' and store_id in ('. $store_ids .')  '.$time_fillter,
                'order' => ' finnshed_time desc '
            );
            $result = Db::select($param);
        }else{
            return array(array('oc_commis_totals'=>0,'oc_commis_return_totals'=>0,'oc_shipping_totals'=>0));
        }

        return $result;
    }


    /*
 * 获取代理订单列表
 */
    public function getAgentVROrder($condition){
        $agent_id = $condition['seller_id'];

        $id_array= array();
        $store_list = $this->table('agent_store')->where(array('agent_id'=> $agent_id))->field('store_id')->select();
        foreach($store_list as $k => $v){
            $id_array[] = $v['store_id'];
        }
        $store_ids = implode(",", $id_array);

        $time_fillter = '';
        if(!empty($condition['start_time'])){
            $time_fillter = ' and finnshed_time >= '. $condition['start_time'].' and finnshed_time <= '. $condition['end_time'];
        }

        if (!empty($store_ids)) {
            $param = array(
                'table'=>'vr_order',
                'field'=>' * ',
                'where'=> ' order_state='.ORDER_STATE_SUCCESS.' and store_id in ('. $store_ids .')  '.$time_fillter,
                'order' => ' finnshed_time desc '
            );
            $result = Db::select($param);
        }else{
            return array(array('oc_commis_totals'=>0,'oc_commis_return_totals'=>0,'oc_shipping_totals'=>0));
        }

        return $result;
    }

    public function getAgentCommission($condition){
        $agent_id = $condition['seller_id'];
        $os_month = $condition['os_month'];

        $id_array= array();
        $store_list = $this->table('agent_store')->where(array('agent_id'=> $agent_id))->field('store_id')->select();
        foreach($store_list as $k => $v){
            $id_array[] = $v['store_id'];
        }
        $store_ids = implode(",", $id_array);

        if (!empty($store_ids)) {
                $id_fillter = array();
                $id_fillter['ob_store_id'] = array('in',$store_ids);

                $param = array(
                    'table'=>'order_bill',
                    'field'=>'IFNULL(sum(ob_commis_totals),0) as oc_commis_totals,IFNULL(sum(ob_commis_return_totals),0) as oc_commis_return_totals,IFNULL(sum(ob_shipping_totals),0) as oc_shipping_totals ',
                    'where'=> ' os_month= '.$os_month.' and ob_store_id in ('. $store_ids .') '//$id_fillter
                );
                $result = Db::select($param);

//            $field = '*';
//            $field = 'sum(ob_commis_totals) as ob_commis_totals,sum(ob_commis_return_totals) as ob_commis_return_totals';
//            $condition = array('ob_store_id'=>array('in',$store_ids));    //$condition = array('cart_id'=>array('in',array_keys($input_buy_items)),'buyer_id'=>$member_id);
//            $result =  $address_list = $this->field($field)->where($id_fillter)->select();
        }else{
            return array(array('oc_commis_totals'=>0,'oc_commis_return_totals'=>0,'oc_shipping_totals'=>0));
        }

        return $result;
    }

    /*
    虚拟订单
    */
    public function getAgentCommissionVR($condition){
        $agent_id = $condition['seller_id'];
        $os_month = $condition['os_month'];

        $id_array= array();
        $store_list = $this->table('agent_store')->where(array('agent_id'=> $agent_id))->field('store_id')->select();
        foreach($store_list as $k => $v){
            $id_array[] = $v['store_id'];
        }
        $store_ids = implode(",", $id_array);

        if (!empty($store_ids)) {
            $id_fillter = array();
            $id_fillter['ob_store_id'] = array('in',$store_ids);

            $param = array(
                'table'=>'vr_order_bill',
                'field'=>'IFNULL(sum(ob_commis_totals),0) as oc_vr_commis_totals ',
                'where'=> ' os_month= '.$os_month.' and ob_store_id in ('. $store_ids .') '//$id_fillter
            );
            $result = Db::select($param);

//            $field = '*';
//            $field = 'sum(ob_commis_totals) as ob_commis_totals,sum(ob_commis_return_totals) as ob_commis_return_totals';
//            $condition = array('ob_store_id'=>array('in',$store_ids));    //$condition = array('cart_id'=>array('in',array_keys($input_buy_items)),'buyer_id'=>$member_id);
//            $result =  $address_list = $this->field($field)->where($id_fillter)->select();
        }else{
            return array(array('oc_vr_commis_totals'=>0));
        }

        return $result;
    }




    public function getStoreCommission($condition){
        $agent_id = $condition['seller_id'];
        $os_month = $condition['os_month'];

        $id_array= array();
        $store_list = $this->table('agent_store')->where(array('agent_id'=> $agent_id))->field('store_id')->select();
        foreach($store_list as $k => $v){
            $id_array[] = $v['store_id'];
        }
        $store_ids = implode(",", $id_array);

        if (!empty($store_ids)) {
            $id_fillter = array();
            $id_fillter['ob_store_id'] = array('in',$store_ids);

            $param = array(
                'table'=>'order_bill',
                'field'=>'IFNULL(sum(ob_commis_totals),0) as oc_commis_totals,IFNULL(sum(ob_commis_return_totals),0) as oc_commis_return_totals',
                'where'=> ' os_month= '.$os_month.' and ob_store_id in ('. $store_ids .') '//$id_fillter
            );
            $result = Db::select($param);

//            $field = '*';
//            $field = 'sum(ob_commis_totals) as ob_commis_totals,sum(ob_commis_return_totals) as ob_commis_return_totals';
//            $condition = array('ob_store_id'=>array('in',$store_ids));    //$condition = array('cart_id'=>array('in',array_keys($input_buy_items)),'buyer_id'=>$member_id);
//            $result =  $address_list = $this->field($field)->where($id_fillter)->select();
        }else{
            return array(array('oc_commis_totals'=>0,'oc_commis_return_totals'=>0));
        }

        return $result;
    }
}
