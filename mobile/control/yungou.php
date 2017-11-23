<?php
/**
 * 云购
 **/



class yungouControl extends mobileMemberControl{//mobileHomeControl
    const YUNGOU_STATE_NORMAL = 1;
    const YUNGOU_STATE_COUNT = 2;
    const YUNGOU_STATE_PUBLIC = 3;
    const YUNGOU_STATE_COMPLATE = 4;
    const YUNGOU_COUNT_TIME = 180; //云购统计时间3分钟


    public function __construct() {
        parent::__construct();
    }

    public function yungou_classOP(){
        $model_yungou_class = Model('yungou_class');
        $condition = array();
        $condition['class_parent_id'] = 0;
        $condition['order'] = ' sort asc';
        $yungou_class = $model_yungou_class->getList($condition);

        $html = array();
        foreach ($yungou_class as $key => $v) {
            $html[$key]['id'] = $v['class_id'];
            $html[$key]['class_name'] = $v['class_name'];
        }
        output_data(array('class_list' => $html));
    }


    public function yungou_listOp() {
        $model_yungou = Model('yungou');

        //正在云购
        $condition = array();
        $condition['state'] = self::YUNGOU_STATE_NORMAL;
        $condition['start_time'] = array('lt', TIMESTAMP);

        // 分类筛选条件
        if (($class_id = (int) $_GET['class']) > 0) {
            $condition['yg_class_id'] = $class_id;
        }

        // 查询类型
        $query_fillter = trim($_GET['type']);

        $order = '';
        if(!empty($query_fillter)) {
            switch ($query_fillter) {
                case 'now':
                    $order = 'yg_id desc ';
                    break;
                case 'recommend':
                    $condition['recommended'] = 1;
                    break;
                case 'hot':
                    $order = 'yg_period desc ';
                    break;
                case 'left':
                    $order = 'buyer_left asc ';
                    break;
                case 'new':
                    $order = 'start_time desc ';
                    break;
                case 'limit':
                    $condition['buy_limit'] = array('gt', 0);
                    break;
                case 'va':
                    $order = 'total_buyer asc ';
                    break;
                case 'vd':
                    $order = 'total_buyer desc ';
                    break;
                case 'soon':
                    $condition['start_time'] = array('gt', TIMESTAMP);
                    break;
            }
        }

        $field = 'yungou.yg_id,yungou.yg_title, yungou.yg_period, yungou.complate_rate, yungou.buyer_count,yungou.total_buyer,yungou.buyer_left, goods.goods_id, goods.goods_image,goods.store_id';

// getYungouAndGoodsList($condition,$page=null,$order='',$field='*',$limit='')
        $yungou_list = $model_yungou->getYungouAndGoodsList($condition, 10, $order, $field);
        $page_count = $model_yungou->gettotalpage();

        $html = array();
        foreach ($yungou_list as $key => $v) {
            $html[$key]['yg_id'] = $v['yg_id'];
            $html[$key]['yg_title'] = $v['yg_title'];
            $html[$key]['yg_period'] = $v['yg_period'];
            $html[$key]['goods_id'] = $v['goods_id'];
            $html[$key]['goods_image_url'] =  cthumb($v['goods_image'], 360, $v['store_id']);
            $html[$key]['total_buyer'] = $v['total_buyer'];
            $html[$key]['buyer_count'] = $v['buyer_count'];
            $html[$key]['buyer_left'] = $v['buyer_left'];
            $html[$key]['complate_rate'] = $v['complate_rate'];
        }

        output_data(array('goods_list' => $html), mobile_page($page_count));
    }

    public function lotteryOp() {
        $model_yungou = Model('yungou');

        $condition = array();
        $condition['state'] = array('egt', self::YUNGOU_STATE_COUNT);

        $field = 'yungou.yg_id,yungou.yg_title, yungou.yg_period, yungou.complate_rate, yungou.buyer_count,
        yungou.end_time,yungou.lucky_code,yungou.state,
        yungou.total_buyer,yungou.buyer_left, yungou.lucky_member_id, yungou.commit_time,yungou.lucky_member_name,yungou.lucky_member_buy,
        yungou.yg_commit,goods.goods_image,goods.store_id';
        $order = 'end_time desc ';
        $lottery_list = $model_yungou->getYungouAndGoodsList($condition, 5, $order, $field);
        $page_count = $model_yungou->gettotalpage();

        $html = array();
        foreach ($lottery_list as $key => $v) {
            $html[$key]['yg_id'] = $v['yg_id'];
            $html[$key]['yg_title'] = $v['yg_title'];
            $html[$key]['yg_period'] = $v['yg_period'];
            $html[$key]['end_time'] =  @date('Y-m-d H:i:s', $v['end_time']);
            $html[$key]['lottery_time'] =  @date('Y-m-d H:i:s', $v['end_time']+180);
            $html[$key]['state'] = $v['state'];
            $html[$key]['lucky_code'] = $v['lucky_code'];

            $html[$key]['member_img'] = getMemberAvatarForID($v['lucky_member_id']);
            $html[$key]['lucky_member_name'] = $v['lucky_member_name'];
            $html[$key]['lucky_member_buy'] = $v['lucky_member_buy'];
            $html[$key]['goods_image_url'] =  cthumb($v['goods_image'], 360, $v['store_id']);

            $html[$key]['commit_time'] =  @date('Y-m-d H:i:s', $v['commit_time']);
            $html[$key]['yg_commit'] = $v['yg_commit'];

            $html[$key]['total_buyer'] = $v['total_buyer'];
            $html[$key]['buyer_count'] = $v['buyer_count'];
            $html[$key]['buyer_left'] = $v['buyer_left'];
            $html[$key]['complate_rate'] = $v['complate_rate'];
        }

        output_data(array('goods_list' => $html), mobile_page($page_count));
    }


    public function postOP(){
        $page = 10;
        $condition = array();
        $condition['is_commit'] = 1;
        $model_yungou = Model('yungou');
        $list = $model_yungou->getYungouInfoList($condition, $page, ' commit_time desc','yg_id,yg_title,yg_period,lucky_member_id,lucky_member_name,commit_time,yg_commit');
        $page_count = $model_yungou->gettotalpage();

        $html = array();
        foreach ($list as $key => $v) {
            $html[$key]['yg_id'] = $v['yg_id'];
            $html[$key]['url'] = urlShop('yungou', 'lottery', array('id' => $v['yg_id']));
            $html[$key]['member_img'] = getMemberAvatarForID($v['lucky_member_id']);
            $html[$key]['commit_time'] =  @date('Y-m-d H:i:s', $v['commit_time']);
            $html[$key]['yg_period'] = $v['yg_period'];
            $html[$key]['lucky_member_name'] = $v['lucky_member_name'];
            $html[$key]['yg_commit'] = $v['yg_commit'];
        }
        output_data(array('goods_list' => $html), mobile_page($page_count));
    }


    public function yungou_logOp() {
        $model_yungou = Model('yungou');
        $condition = array();
//        $condition['state'] = array('egt', self::YUNGOU_STATE_COUNT);
        $condition['yungou_join.member_id '] = $this->member_info['member_id'];

        $field = 'yungou.yg_id,yungou.yg_title, yungou.yg_period, yungou.complate_rate, yungou.buyer_count,
        yungou.end_time,yungou.lucky_code,yungou.state,
        yungou.total_buyer,yungou.buyer_left, yungou.lucky_member_id, yungou.commit_time,yungou.lucky_member_name,yungou.lucky_member_buy,
        yungou.yg_commit,goods.goods_image,goods.store_id';

        $order = 'yg_id desc ';
        $limit = '';
        $group = 'yungou_join.yg_id';

        $lottery_list = $model_yungou->getUserYungouLog($condition, 5, $order, $field, $limit, $group);
        $page_count = $model_yungou->gettotalpage();

        $html = array();
        foreach ($lottery_list as $key => $v) {
            $html[$key]['yg_id'] = $v['yg_id'];
            $html[$key]['yg_title'] = $v['yg_title'];
            $html[$key]['yg_period'] = $v['yg_period'];
            $html[$key]['end_time'] =  @date('Y-m-d H:i:s', $v['end_time']);
            $html[$key]['state'] = $v['state'];
            $html[$key]['lucky_code'] = $v['lucky_code'];

            $html[$key]['member_img'] = getMemberAvatarForID($v['lucky_member_id']);
            $html[$key]['lucky_member_buy'] = $v['lucky_member_buy'];
            $html[$key]['lucky_member_name'] = $v['lucky_member_name'];
            $html[$key]['goods_image_url'] =  cthumb($v['goods_image'], 360, $v['store_id']);

            $html[$key]['commit_time'] =  @date('Y-m-d H:i:s', $v['commit_time']);
            $html[$key]['yg_commit'] = $v['yg_commit'];

            $html[$key]['total_buyer'] = $v['total_buyer'];
            $html[$key]['buyer_count'] = $v['buyer_count'];
            $html[$key]['buyer_left'] = $v['buyer_left'];
            $html[$key]['complate_rate'] = $v['complate_rate'];
        }

        output_data(array('goods_list' => $html), mobile_page($page_count));
    }

    public function yungou_goodsOp() {
        $model_yungou = Model('yungou');
        $condition = array();
        $condition['yungou.lucky_member_id '] = $this->member_info['member_id'];
        $field = 'yungou.yg_id,yungou.yg_title, yungou.yg_period,yungou.ship_state,yungou.lucky_member_buy,
        yungou.end_time,yungou.state, yungou.lucky_member_id,yungou.lucky_code,goods.goods_image,goods.store_id';
        $order = 'ship_state asc ';
        $limit = '';
        $group = '';

        $lottery_list = $model_yungou->getUserYungouGoods($condition, 5, $order, $field, $limit, $group);
        $page_count = $model_yungou->gettotalpage();

        $html = array();
        foreach ($lottery_list as $key => $v) {
            $html[$key]['yg_id'] = $v['yg_id'];
            $html[$key]['yg_title'] = $v['yg_title'];
            $html[$key]['yg_period'] = $v['yg_period'];
            $html[$key]['end_time'] =  @date('Y-m-d H:i:s', $v['end_time']);
            $html[$key]['state'] = $v['state'];
            $html[$key]['ship_state'] = $v['ship_state'];
            $html[$key]['lucky_code'] = $v['lucky_code'];
            $html[$key]['lucky_member_buy'] = $v['lucky_member_buy'];

            $html[$key]['goods_image_url'] =  cthumb($v['goods_image'], 360, $v['store_id']);

        }

        output_data(array('goods_list' => $html), mobile_page($page_count));
    }
}
