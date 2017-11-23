<?php
/**
 * 云购模型
 *
 *
 *
 *
 *
 */

class yungouModel extends Model{
    const TABLE_NAME = 'yungou';
    const PK = 'yg_id';
    const YUNGOU_START_NUM = 100000000; //云购码起始
    const YUNGOU_COUNT_TIME = 180; //云购统计时间3分钟

    const YUNGOU_STATE_NORMAL = 1;
    const YUNGOU_STATE_COUNT = 2;
    const YUNGOU_STATE_PUBLIC = 3;
    const YUNGOU_STATE_CONFIRM = 4;

    private $yungou_state_array = array(
        0 => '全部',
        self::YUNGOU_STATE_NORMAL => '正常',
        self::YUNGOU_STATE_COUNT => '统计中',
        self::YUNGOU_STATE_PUBLIC => '已揭晓',
        self::YUNGOU_STATE_CONFIRM => '已确认',
    );

    const SHIP_STATE_CONFIRM = 1;
    const SHIP_STATE_SEND = 2;
    const SHIP_STATE_RECEIVE = 3;
    const SHIP_STATE_COMPLATE = 4;

    private $ship_state_array = array(
        0 => '--',
        self::SHIP_STATE_CONFIRM => '已确认',
        self::SHIP_STATE_SEND => '已发货',
        self::SHIP_STATE_RECEIVE => '已收货',
        self::SHIP_STATE_COMPLATE => '已完成',
    );

    public function __construct() {
        parent::__construct('yungou');
    }

    public function getYungouExtendList($condition, $page = null, $order = 'state asc', $field = '*', $limit = 0) {
        $yungou_list = $this->getYungouList($condition, $page, $order, $field, $limit);
        if(!empty($yungou_list)) {
            for($i =0, $j = count($yungou_list); $i < $j; $i++) {
                $yungou_list[$i] = $this->getYungouExtendInfo($yungou_list[$i]);
            }
        }
        return $yungou_list;
    }



    public function getYungouList($condition, $page = null, $order = 'state asc', $field = '*', $limit = 0) {
        $on = 'yungou.goods_id=goods.goods_id';
        $result = $this->table('yungou,goods')->field($field)->join('left')->on($on)->where($condition)->page($page)->order($order)->limit($limit)->select();
        return $result;
    }


    public function getYungouExtendInfo($yungou_info) {
        $yungou_info['start_time_text'] = date('Y-m-d H:i', $yungou_info['start_time']);
        if(empty($yungou_info['end_time'])){
            $yungou_info['end_time_text'] = '';
        }else{
            $yungou_info['end_time_text'] = date('Y-m-d H:i', $yungou_info['end_time']);
        }

        $yungou_info['yungou_state_text'] = $this->yungou_state_array[$yungou_info['state']];
        $yungou_info['ship_state_text'] = $this->ship_state_array[$yungou_info['ship_state']];

        return $yungou_info;
    }

    public function getOneById($id){
        return Db::getRow(array('table'=>'yungou','field'=>'yg_id','value'=>$id));
    }

    public function getYungouById($id,$field){
        $condition = array();
        $condition['yg_id'] = $id;
        return $this->table('yungou')->field($field)->where($condition)->find();
    }

    public function getGroupbuyOnlineList($condition, $page = null, $order = 'state asc', $field = '*') {
        $condition['state'] = self::YUNGOU_STATE_NORMAL;
        $condition['start_time'] = array('lt', TIMESTAMP);
        return $this->getYungouExtendList($condition, $page, $order, $field);
    }

    public function getYungouOnlineList($condition, $page = null, $order = 'state asc', $field = '*') {
        return $this->getYungouExtendList($condition, $page, $order, $field);
    }

    public function getYungouAndGoodsList($condition,$page=null,$order='',$field='*',$limit=''){
        $on = 'yungou.goods_id=goods.goods_id';
        $result = $this->table('yungou,goods')->field($field)->join('left')->on($on)->where($condition)->page($page)->order($order)->limit($limit)->select();
        return $result;
    }

    public function getUserYungouLog($condition,$page=null,$order='',$field='*',$limit='',$group=''){
        $on = 'yungou_join.yg_id=yungou.yg_id ,yungou.goods_id=goods.goods_id';
        $result = $this->table('yungou_join,yungou,goods')->field($field)->join('inner,left')->on($on)->where($condition)->page($page)->order($order)->limit($limit)->group($group)->select();
        return $result;
    }


    public function getUserYungouGoods($condition,$page=null,$order='',$field='*',$limit='',$group=''){
        $on = 'yungou.goods_id=goods.goods_id';
        $result = $this->table('yungou,goods')->field($field)->join('left')->on($on)->where($condition)->page($page)->order($order)->limit($limit)->group($group)->select();
        return $result;
    }

    /*
     * 首页揭晓
     * */
    public function getYungouIndexPublic($page=null,$limit=''){
        $field='*, (end_time+'.self::YUNGOU_COUNT_TIME.') as djs';
        $condition = array();
        $condition['state'] = self::YUNGOU_STATE_COUNT;
        $where = " yungou.state >= 2 ";
        $on = 'yungou.goods_id=goods.goods_id';
        $order = 'yungou.end_time desc';
//        $result = $this->table('yungou,goods')->field($field)->join('left')->on($on)->where($condition)->page($page)->order($order)->limit($limit)->select();
        $result = $this->table('yungou,goods')->field($field)->join('left')->on($on)->where($where)->page($page)->order($order)->limit($limit)->select();
        return $result;
    }

    //获取最新云购信息
    public function getYungouAndGoodsOne($goods_id,$period_id){
        if(intval($goods_id) <= 0) {
            return null;
        }
        $order='';
        $field='*';
        $condition = array();
        $condition['yungou.goods_id'] = $goods_id;
        if(intval($period_id) <= 0) {
            $order='yg_period desc';
        }else{
            $condition['yg_period'] = $period_id;
        }
        $on = 'yungou.goods_id=goods.goods_id';
        $result = $this->table('yungou,goods')->field($field)->join('left')->on($on)->where($condition)->order($order)->find();
        return $result;
    }


    public function getYungouInfoByID($yg_id) {
        $on = 'yungou.goods_id=goods.goods_id';
        return $this->table('yungou,goods')->field('*')->join('left')->on($on)->where(array('yg_id'=>$yg_id))->find();
    }

    public function getYungouInfoByGoodsID($goods_id,$period_id){
        if(intval($goods_id) <= 0) {
            return null;
        }

        $order='';
        $field='*';
        $condition = array();
        $condition['goods_id'] = $goods_id;
        if(intval($period_id) <= 0) {
            $order='yg_period desc';
        }else{
            $condition['yg_period'] = $period_id;
        }
        return $this->table('yungou')->field($field)->where($condition)->order($order)->find();
    }


    public function getYungouBuyerCount($yg_id,$member_id){
        if(intval($yg_id) <= 0 || intval($member_id) <= 0) {
            return 0;
        }
        $field=' count(*) as buyer_count';

        $condition = array();
        $condition['yg_id'] = $yg_id;
        $condition['buyer_id'] = $member_id;

        $resule = $this->table('yungou_code')->field($field)->where($condition)->find();
        return $resule['buyer_count'];
    }

    /**
    更新云购可购买数
     */
    public function updateYungouBuyerLeft($yg_id,$num,$type='sub'){
        if(intval($yg_id) <= 0 || intval($num) <= 0) {
            return false;
        }
        $condition = array('yg_id'=>$yg_id);

        $left = $this->table('yungou')->field('total_buyer,buyer_left')->where($condition)->find();
        $buy_left = intval($left['buyer_left']);
        $total_buyer = intval($left['total_buyer']);
        if(  $buy_left >= $num){
            if($type == 'add'){
                $last = $buy_left + $num;
            }elseif($type == 'sub'){
                $last = $buy_left - $num;
            }else{
                return false;
            }

            $buyer_count = $total_buyer - $last;
            $complate_rate = round($buyer_count / $total_buyer *100,2);
            $update = $this->table('yungou')->where($condition)->update(array('buyer_left'=>$last,'buyer_count'=>$buyer_count, 'complate_rate'=> $complate_rate));
            return $update;
        }else{
            return false;
        }
    }

    /*
     * 更新买家余额
     * */
    public function updateBuyerMoney($num,$type='sub',$member_id){
        if(intval($num) <= 0) {
            return false;
        }

        if($member_id) {
            $condition = array('member_id'=>$member_id);
            $member_info = $this->table('member')->field('available_predeposit')->where($condition)->find();
            $member_money = intval($member_info['available_predeposit']);

            if($type == 'add'){
                $last = $member_money + $num;
            }elseif($type == 'sub'){
                $last = $member_money - $num;
            }else{
                return false;
            }

            $update = $this->table('member')->where($condition)->update(array('available_predeposit'=>$last));
            return $update;
        }else{
            return false;
        }
    }

    /*
     * 更新云购参与或撤销记录
     * */
    public function getYungouJoin($yg_id,$join_id,$num,$type){
        $member_id = $_SESSION['member_id'];
        if($type == 'do'){
            $update_str= "update `".DBPRE."yungou_code` set buyer_id=$member_id ,join_id=$join_id  where yg_id=$yg_id and buyer_id=0  ORDER BY RAND() LIMIT $num";
            return Db::query($update_str);
        }elseif($type == 'undo'){
            $update_str= "update `".DBPRE."yungou_code` set buyer_id=0 ,join_id=0  where yg_id=$yg_id and buyer_id=$member_id and join_id=$join_id";
            return Db::query($update_str);
        }
    }

    /*
     * 开始云购
     * */
    public function _yungou_join_sync($yg_id,$num){
        $last_num = self::YUNGOU_START_NUM + $num;

        $condition = array('yg_id'=>$yg_id);
        $yg_code = $this->table('yungou_code')->field('IFNULL(max(yg_code),0) as max_code')->where($condition)->find();
        $max_code = intval($yg_code['max_code']);
        if( $max_code == $last_num){
            return 1;
        }

        $split_num = 1000;
        $tt = intval($num/$split_num);

        $is_flag =1;
        $insert_str= "insert into `".DBPRE."yungou_code` (yg_id,yg_code) VALUES ";
        for ($i=0;$i<=$tt;$i=$i+1){
            $start = $i*$split_num + self::YUNGOU_START_NUM;
            $end = $start + $split_num;

            if ($end > $last_num){
                $end = $last_num;
            }

            $str = '';
            for($j = $start +1; $j <=$end; $j=$j+1){
                if(empty($str)){
                    $str .= "($yg_id,$j)";
                }else{
                    $str .= ",($yg_id,$j)";
                }
            }

            if(!empty($str)){
                $flag =  Db::query( $insert_str.$str);
                $is_flag = $is_flag * $flag;
            }
        }
        return $is_flag;
    }

    public function addYungouJoin($insert) {
        return $this->table('yungou_join')->insert($insert);
    }

    public function editYungouJoin($condition, $update) {
        return $this->table('yungou_join')->where($condition)->update($update);
    }

    public function getYungouJoinList($condition, $page = null, $order = 'last_buy_time desc', $field = '*',$limit='') {
        $result = $this->table('yungou_join')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
        return $result;
    }

    public function getUserCodeList($condition, $page = null, $order = 'id desc', $field = '*',$limit='') {
        $result = $this->table('yungou_code')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
        return $result;
    }

    public function getYungouInfoList($condition, $page = null, $order = '', $field = '*',$limit='') {
        $result = $this->table('yungou')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
        return $result;
    }

    public function getJoinCount($condition) {
        return $this->where($condition)->count();
    }

    public function getYungouCount($condition) {
        return $this->table('yungou')->where($condition)->count();
    }

    public function getYungouJoinTop() {
        $page = null;
        $field = '*';
        $limit='';
        $order = 'last_buy_time desc';
        $on = 'yungou_join.yg_id = yungou.yg_id';
        $result = $this->table('yungou_join,yungou')->field($field)->join('left')->on($on)->page($page)->order($order)->limit($limit)->select();
        return $result;
    }

    /*
     * 根据云购ID查询最新
     * */
    public function getYungouJoinTopByID($yg_id, $num) {
        if(intval($yg_id) <= 0 || intval($num) <= 0) {
            return false;
        }

        $condition =array('yungou_join.yg_id'=> $yg_id);
        $field = '*';
        $order = 'last_buy_time desc';
        $on = 'yungou_join.yg_id = yungou.yg_id';
        $result = $this->table('yungou_join,yungou')->field($field)->join('left')->on($on)->where($condition)->order($order)->limit($num)->select();
        return $result;
    }

    /*
     * 云购最后100购买者
     * */
    public function getYungouLast100($end_time) {
        $condition = array();
        $condition['yungou_join.last_buy_time'] =  array('lt', $end_time);
        $field = 'yungou_join.member_name,yungou_join.last_buy_time, yungou_join.time_int, yungou_join.msec,yungou_join.buy_num, yungou.yg_period, yungou.yg_title, yungou.yg_id ';
        $limit='100';
        $order = 'last_buy_time desc';
        $on = 'yungou_join.yg_id = yungou.yg_id';
        $result = $this->table('yungou_join,yungou')->field($field)->join('left')->on($on)->where($condition)->order($order)->limit($limit)->select();//     page($page)->
        return $result;
    }

    /*
     * 获取幸运用户ID
     * */
    public function getYungouLuckyMemberID($yg_id, $locky_code) {
        if(intval($yg_id) <= 0 || intval($locky_code) <= 0) {
            return false;
        }

        $condition = array();
        $condition['yg_id'] = $yg_id;
        $condition['yg_code'] = $locky_code;
        $result = $this->table('yungou_code')->field(' buyer_id')->where($condition)->find();
        if($result['buyer_id']){
            $condition1 = array();
            $condition1['member_id'] = $result['buyer_id'];

            $result = $this->table('member')->field('member_id,member_name')->where($condition1)->find();
            return $result;
        }
        return false;
    }

    public function save($param){
        return $this->table('yungou')->insert($param);
    }


    public function drop($param){
        return $this->where($param)->delete();
    }

    public function edit($update_array, $where_array){
        return $this->table('yungou')->where($where_array)->update($update_array);
    }

    /**
     * 状态数组
     */
    public function getYungouStateArray() {
        return $this->yungou_state_array;
    }

    /*
     * 物流状态
     * */
    public function getShipStateArray() {
        return $this->ship_state_array;
    }

    /*
     * 获取最新云购期数
     * */
    public function getLastPeriod($goods_id) {
        if(intval($goods_id) <= 0 ) {
            return false;
        }

        $result = $this->table('yungou')->field('yg_id,yg_period')->where(array('goods_id'=> $goods_id))->order(' yg_period desc')->find();
        if($result){
            return $result;
        }else{
            return 0;
        }
    }

    /*
     * 返回详细页面的Period
     * */
    public function getPeriods($period, $last_period){
        if(intval($period) <= 0 || intval($last_period) <= 0) {
            return false;
        }

        $arr = array();
        if($period >9 ){
            $start = $period;
            $end = $period-9;
        }else{
            if($last_period > 9){
                $start = 9;
                $end = 0;
            }else{
                $start = $last_period-1;
                $end =  0;
            }
        }

        for($i= $start; $i> $end; $i--){
            $arr[] = $i;
        }

        return $arr;
    }

    /*
     * 获取幸运买家信息,本期购买数,
     * */
    public function getLuckyBuyerInfo($yg_id,$member_id){
        $info = array();
        $result = $this->table('yungou_join')->field('member_name,ip_local')->where(array('yg_id'=> $yg_id,'member_id'=> $member_id ))->find();
        if($result){
            $info['member_id'] = $member_id;
            $info['member_name'] = $result['member_name'];
            $info['ip_local'] = $result['ip_local'];

            $result1 = $this->table('yungou_join')->field(' sum(buy_num) as total_buy ')->where(array('yg_id'=> $yg_id,'member_id'=> $member_id  ))->find();
            $info['total_buy'] = $result1['total_buy'];
        }
        return $info;
    }

    public function getYungouLast100_______($end_time) {
        $condition = array();
        $condition['yungou_join.last_buy_time'] =  array('lt', $end_time);
        $field = 'yungou_join.member_name,yungou_join.last_buy_time, yungou_join.time_int, yungou_join.msec,yungou_join.buy_num, yungou.yg_period, yungou.yg_title, yungou.yg_id ';
        //member_name,last_buy_time, time_int, msec, yg_period, yg_title, yg_id,
        $limit='100';
        $order = 'last_buy_time desc';
        $on = 'yungou_join.yg_id = yungou.yg_id';
        $result = $this->table('yungou_join,yungou')->field($field)->join('left')->on($on)->where($condition)->order($order)->limit($limit)->select();//     page($page)->
        return $result;
    }
    /**
     * 云购日志列表
     *
     * @param array $condition 条件数组
     * @param array $page   分页
     * @param array $field   查询字段
     * @param array $page   分页
     */
    public function getYungouJoinLogList($condition_array,$page='',$field='*'){
        $condition_sql = '';
        if ($condition_array['member_id']) {
            $condition_sql	.= " and `yungou_join`.member_id = '{$condition_array['member_id']}'";
        }
        if ($condition_array['yg_stage']) {
            $condition_sql	.= " and `yungou`.state = '{$condition_array['yg_stage']}'";
        }
        if ($condition_array['saddtime']){
            $condition_sql	.= " and `yungou_join`.last_buy_time >= '{$condition_array['saddtime']}'";
        }
        if ($condition_array['eaddtime']){
            $condition_sql	.= " and `yungou_join`.last_buy_time <= '{$condition_array['eaddtime']}'";
        }

        $param	= array();
        $param['table']	= 'yungou_join,yungou';
        $param['join_type']	= empty($condition['join_type'])?'left join':$condition['join_type'];
        $param['join_on']	= array('yungou_join.yg_id = yungou.yg_id');
        $param['where']	= $condition_sql;
        $param['field'] = $field;
        $param['order'] = $condition_array['order'] ? $condition_array['order'] : 'yungou_join.last_buy_time desc';
        $param['limit'] = $condition_array['limit'];
        $param['group'] = $condition_array['group'];
        return Db::select($param,$page);
    }

    /**
     * 用户中奖纪录
     */
    public function getYungouUserWinList($condition_array,$page='',$field='*'){
        $condition_sql = '';
        if ($condition_array['member_id']) {
            $condition_sql	.= " and `yungou`.lucky_member_id = '{$condition_array['member_id']}'";
        }
        if ($condition_array['ship_stage']) {
            $condition_sql	.= " and `yungou`.ship_state = '{$condition_array['ship_stage']}'";
        }

        $param	= array();
        $param['table']	= 'yungou,goods';
        $param['join_type']	= empty($condition['join_type'])?'left join':$condition['join_type'];
        $param['join_on']	= array('yungou.goods_id=goods.goods_id');
        $param['where']	= $condition_sql;
        $param['field'] = $field;
        $param['order'] = $condition_array['order'] ? $condition_array['order'] : 'yungou.ship_state asc,yungou.end_time desc';
        $param['limit'] = $condition_array['limit'];
        $param['group'] = $condition_array['group'];
        return Db::select($param,$page);
    }


    /*
     * 买家总消费
     * */
    public function getYungouUserMoneyCount(){
        if($_SESSION['member_id']) {

            $field=' sum(buy_num) as money_count';
            $condition = array();
            $condition['member_id'] = $_SESSION['member_id'];
            $resule = $this->table('yungou_join')->field($field)->where($condition)->find();
            return $resule['money_count'];
        }
        return 0;
    }


    /**
     * 删除缓存
     *
     * @param string $key 缓存键
     */
    public function dropCachedData($key) {
        unset($this->cachedData[$key]);
        dkcache($key);
    }

    protected $cachedData;

}
