<?php
/**
 * 店铺管理界面
 *
 ***/



class store_listControl extends SystemControl{
	const EXPORT_SIZE = 1000;
	public function __construct(){
		parent::__construct();
		Language::read('store,store_grade');
	}

        
        /**
	 * 认证 待审核列表
	 */
	public function store_listOp(){
		//店铺列表
		if(!empty($_GET['owner_and_name'])) {
			$condition['member_name'] = array('like','%'.$_GET['owner_and_name'].'%');
                        Tpl::output('owner_and_name', $_GET['owner_and_name']);
		}
		if(!empty($_GET['store_name'])) {
			$condition['company_name'] = array('like','%'.$_GET['store_name'].'%');
                        Tpl::output('store_name', $_GET['store_name']);
		}
                //获取当前登录后台管理员 城市中心地区
                $admininfo = $this->getAdminInfo();
                if($admininfo['cityid'] > 0){
                    //$condition['city_center'] = intval($admininfo['cityid']);
                }
		$model_store_joinin = Model('store_joinin');
		$store_list = $model_store_joinin->getList($condition, 10, 'joinin_state asc');
                $model = Model();
                $city = $model->table('city_centre')->where('city_state=1')->select();
                foreach($store_list as $key=>$rows){
                    foreach($city as $c){
                        if($rows['city_center'] == $c['id']){
                            $store_list[$key]['city_center_name'] = $c['city_name'];
                        }
                    }
                }
		Tpl::output('store_list', $store_list);

		//店铺等级
		$model_grade = Model('store_grade');
		$grade_list = $model_grade->getGradeList();
		Tpl::output('grade_list', $grade_list);

		Tpl::output('page',$model_store_joinin->showpage('2'));
		Tpl::showpage('store_list');
	}

	/**
	 * 审核详细页
	 */
	public function store_joinin_detailOp(){
            $model_store_joinin = Model('store_joinin');
            if(empty($_GET['city'])){
                showMessage('城市公司参数错误','index.php?act=store_list&op=store_list');exit;
            }
            $joinin_detail_where['member_id'] = $_GET['member_id'];
            $joinin_detail_where['city_center'] = $_GET['city'];
            $joinin_detail = $model_store_joinin->getOne($joinin_detail_where);
            //获取城市中心
            $model = Model();
            $city = $model->table('city_centre')->field('city_name')->where('id='.$_GET['city'])->find();
            $joinin_detail['city_name'] = $city['city_name'];
            $joinin_detail_title = '查看';
            if(in_array(intval($joinin_detail['joinin_state']), array(STORE_JOIN_STATE_NEW, STORE_JOIN_STATE_PAY))) {
                $joinin_detail_title = '审核';
            }
            if (!empty($joinin_detail['sg_info'])) {
                $store_grade_info = Model('store_grade')->getOneGrade($joinin_detail['sg_id']);
                $joinin_detail['sg_price'] = $store_grade_info['sg_price'];
            } else {
                $joinin_detail['sg_info'] = @unserialize($joinin_detail['sg_info']);
                if (is_array($joinin_detail['sg_info'])) {
                    $joinin_detail['sg_price'] = $joinin_detail['sg_info']['sg_price'];
                }
            }
            //判断是否可以回退
            $where_is_rz['joinin_state'] = 44;
            $where_is_rz['member_id'] = $_GET['member_id'];
            $is_rz_one = $model->table('store_joinin')->field('joinin_state')->where($where_is_rz)->find();

            if($is_rz_one['joinin_state'] == 44){
                Tpl::output('is_rz_one',1);
            }else{
                Tpl::output('is_rz_one',2);
            }
            Tpl::output('joinin_detail_title', $joinin_detail_title);
            Tpl::output('joinin_detail', $joinin_detail);
            Tpl::showpage('store_list.detail');
	}

}
