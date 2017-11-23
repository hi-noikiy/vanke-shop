<?php
/**
 * 商品栏目管理
 *
 *
 *
 *
 */


class goodsControl extends SystemControl{
    const EXPORT_SIZE = 5000;
    public function __construct() {
        parent::__construct ();
        Language::read('goods');
    }

    /**
     * 商品设置
     */
    public function goods_setOp() {
		$model_setting = Model('setting');
		if (chksubmit()){
			$update_array = array();
			$update_array['goods_verify'] = $_POST['goods_verify'];
			$result = $model_setting->updateSetting($update_array);
			if ($result === true){
				$this->log(L('nc_edit,nc_goods_set'),1);
				showMessage(L('nc_common_save_succ'));
			}else {
				$this->log(L('nc_edit,nc_goods_set'),0);
				showMessage(L('nc_common_save_fail'));
			}
		}
		$list_setting = $model_setting->getListSetting();
		Tpl::output('list_setting',$list_setting);
        Tpl::showpage('goods.setting');
    }



    /**
     * 商品管理
     */
    public function goodsOp() {
        $model_goods = Model ( 'goods' );
        /**
         * 处理商品分类
         */
        $choose_gcid = ($t = intval($_REQUEST['choose_gcid']))>0?$t:0;
        $gccache_arr = Model('goods_class')->getGoodsclassCache($choose_gcid,3);
	    Tpl::output('gc_json',json_encode($gccache_arr['showclass']));
            Tpl::output('gc_choose_json',json_encode($gccache_arr['choose_gcid']));

        /**
         * 查询条件
         */
        $where = array('del'=>1);
        //添加时间
        $if_start_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_start_time']);
        $if_end_time = preg_match('/^20\d{2}-\d{2}-\d{2}$/',$_GET['query_end_time']);
        $start_unixtime = $if_start_time ? strtotime($_GET['query_start_time']) : null;
        $end_unixtime = $if_end_time ? strtotime($_GET['query_end_time']): null;
        if ($start_unixtime || $end_unixtime) {
            $where['goods_addtime'] = array('time',array($start_unixtime,$end_unixtime));
        }
        
        
        if ($_GET['search_goods_name'] != '') {
            $where['goods_common.goods_name'] = array('like', '%' . trim($_GET['search_goods_name']) . '%');
        }
        if (intval($_GET['search_commonid']) > 0) {
            $where['goods_common.goods_commonid'] = intval($_GET['search_commonid']);
        }
        if ($_GET['search_store_name'] != '') {
            $where['goods_common.store_name'] = array('like', '%' . trim($_GET['search_store_name']) . '%');
        }
        if (intval($_GET['b_id']) > 0) {
            $where['goods_common.brand_id'] = intval($_GET['b_id']);
        }
        if ($choose_gcid > 0){
            $where['goods_common.gc_id_'.($gccache_arr['showclass'][$choose_gcid]['depth'])] = $choose_gcid;
	}
        if (in_array($_GET['search_state'], array('0','1','10'))) {
            $where['goods_common.goods_state'] = $_GET['search_state'];
        }
        if (in_array($_GET['search_verify'], array('0','1','10'))) {
            $where['goods_common.goods_verify'] = $_GET['search_verify'];
        }
        //获取当前登录后台管理员 城市中心地区
        $admininfo = $this->getAdminInfo();
        if($admininfo['cityid'] > 0){
            $where['store.first_city_id'] = $admininfo['cityid'];
        }
//                
        switch ($_GET['type']) {
            // 禁售
            case 'lockup':
                $goods_list = $model_goods->getGoodsCommonLockUpList($where);
                break;
            //下架审核
            case 'off':
                $where['goods_common.goods_state'] = '2';
                $goods_list = $model_goods->getGoodsCommonWaitVerifyList3($where);
                break;
            // 等待审核
            case 'waitverify':
                if($where['goods_common.goods_verify']==""){
                    $where['goods_common.goods_verify'] = '10';
                }
                $goods_list = $model_goods->getGoodsCommonWaitVerifyList2($where, '*', 10, 'goods_common.goods_verify desc, goods_common.goods_commonid desc');
                break;
            // 全部商品
            default:
                $goods_list = $model_goods->getGoodsCommonList($where);
                break;
        }
        Tpl::output('goods_list', $goods_list);
        Tpl::output('page', $model_goods->showpage(2));

        $storage_array = $model_goods->calculateStorage($goods_list);
        Tpl::output('storage_array', $storage_array);

        // 品牌
        $brand_list = Model('brand')->getBrandPassedList(array());

        Tpl::output('search', $_GET);
        Tpl::output('brand_list', $brand_list);

        Tpl::output('state', array('1' => '出售中', '0' => '仓库中', '10' => '违规下架', '2'=>'下架审核中'));

        Tpl::output('verify', array('1' => '通过', '0' => '未通过', '10' => '等待审核'));

        Tpl::output('ownShopIds', array_fill_keys(Model('store')->getOwnShopIds(), true));

        switch ($_GET['type']) {
            // 禁售
            case 'lockup':
                Tpl::showpage('goods.close');
                break;
            case 'off':
                Tpl::showpage('goods.off');
                break;
            // 等待审核
            case 'waitverify':
                Tpl::showpage('goods.verify');
                break;
            // 全部商品
            default:
                Tpl::showpage('goods.index');
                break;
        }
    }

    public function goodsSupListOp(){
        Tpl::output('commonid', $_GET['commonid']);
        Tpl::showpage('goods.sup.list');
    }

    public function getGoodSupListOp(){
        $page = empty($_GET['page']) ? '1':$_GET['page'];
        if(!empty($_GET['cm_id'])){
            $model = Model();
            $where = "goods_commonid = '".$_GET['cm_id']."'";
            $field = "goods.goods_id,goods_price,g_costprice,goods_third_price,goods_marketprice,min_num,max_num,goods_image,goods_storage,goods_spec,";
            $field.= "goods_salenum,store_id,materiel_code,to_product_id";
            $dataNum = $model->table("goods")->where($where)->count();
            $on = 'goods.materiel_code = product.product_id';
            $data = $model->table("goods,product")->field($field)->join('left')->on($on)->where($where)->select();
            $newData = array();
            if(!empty($data)){
                foreach ($data as $vl){
                    $vl['img_url'] = $this->cthumbSup($vl,'60');
                    $vl['sup'] = is_array(unserialize($vl['goods_spec'])) ? implode(' ',unserialize($vl['goods_spec'])):$vl['goods_spec'];
                    $newData[] = $vl;
                }
            }
            $list = array(
                'code'  => 0,
                'msg'   => '',
                'count' => $dataNum,
                'data'  => $newData,
            );
            echo json_encode($list);
        }
    }

    /**
     * 违规下架
     */
    public function goods_lockupOp() {
        if (chksubmit()) {
            $commonids = $_POST['commonids'];
            $commonid_array = explode(',', $commonids);
            foreach ($commonid_array as $value) {
                if (!is_numeric($value)) {
                    showDialog(L('nc_common_op_fail'), 'reload');
                }
            }
            $update = array();
            $update['goods_stateremark'] = trim($_POST['close_reason']);

            $where = array();
            $where['goods_commonid'] = array('in', $commonid_array);

            Model('goods')->editProducesLockUp($update, $where);
            showDialog(L('nc_common_op_succ'), 'reload', 'succ');
        }
        Tpl::output('commonids', $_GET['id']);
        Tpl::showpage('goods.close_remark', 'null_layout');
    }

    /**
     * 删除商品
     */
    public function goods_delOp() {
        $common_id = intval($_GET['goods_id']);
        if ($common_id <= 0) {
            showDialog(L('nc_common_op_fail'), 'reload');
        }
        Model('goods')->delGoodsAll(array('goods_commonid' => $common_id));
        showDialog(L('nc_common_op_succ'), 'reload', 'succ');
    }


    /**
     * 审核商品(下架)
     */
    public function goods_offOp(){
        if (chksubmit()) {
            $model = Model();
            $good_data = $model->table("goods_common")->where("goods_commonid = '".$_POST['commonids']."'")->find();
            if(!empty($good_data)){
                $state = $_POST['verify_state'] == '1' ? 0:1;
                //跟新审核数据
                $rest = $model->table("goods_common")->where("goods_commonid = '".$_POST['commonids']."'")->update(array('goods_state'=>$state));
                if($rest){
                    $model->table("goods")->where("goods_commonid = '".$_POST['commonids']."'")->update(array('goods_state'=>$state));
                }
                showMessage('操作成功','index.php?act=goods&op=goods&type=off');exit;
            }
        }
        Tpl::output('commonids', $_GET['id']);
        Tpl::showpage('goods.off_remark', 'null_layout');
    }

    /**
     * 审核商品
     */
    public function goods_verifyOp(){
        if (chksubmit()) {
            //处理商品内部物料编号
            $array = array();
            $array['goods_id'] = $_POST['goods_id'];
            $array['waibu'] = $_POST['waibu'];
            $array['neibu'] = $_POST['neibu'];
            $data = array();
            foreach($array['goods_id'] as $key=>$rows){
                $data[$key]['goods_id'] = $rows;
            }
            foreach($array['waibu'] as $key=>$rows){
                $data[$key]['waibu'] = $rows;
            }
            foreach($array['neibu'] as $key=>$rows){
                $data[$key]['neibu'] = $rows;
            }
            if(intval($_POST['verify_state']) != 0){
            //审核通过更新商品内部编号
            $model = Model();
            foreach($data as $goods_up){
                $data_up['to_product_id'] = $goods_up['neibu'];
                $data_where['product_id'] = $goods_up['waibu'];
                $updat_list[] = $goods_up['waibu'];
                $model->table('product')->where($data_where)->update($data_up);
                $this->updateProductAsGoodsPrice($goods_up['waibu']);
            }
            try{
            $this->transProductToYMA($updat_list,1);
            }catch(Exception $e){
                Log::record4inter("物料推送接口异常,物料数据为：".  json_encode($updat_list), log::ERR);
            }
            }
            $commonids = $_POST['commonids'];
            $commonid_array = explode(',', $commonids);
            foreach ($commonid_array as $value) {
                if (!is_numeric($value)) {
                    showDialog(L('nc_common_op_fail'), 'reload');
                }
            }
            $update2 = array();
            $update2['goods_verify'] = intval($_POST['verify_state']);

            $update1 = array();
            $update1['goods_verifyremark'] = trim($_POST['verify_reason']);
            $update1 = array_merge($update1, $update2);
            $where = array();
            $where['goods_commonid'] = array('in', $commonid_array);

            $model_goods = Model('goods');
            if (intval($_POST['verify_state']) == 0) {
                $update2['goods_verify'] = intval($_POST['verify_state']);
                $model_goods->editProducesVerifyFail($where, $update1, $update2);
            } else {
                $model_goods->editProduces($where, $update1, $update2);
            }
            
            showDialog(L('nc_common_op_succ'), 'reload', 'succ');
        }
        $model = Model();
        $goods_data_id = explode(',',$_GET['id']);
        if(is_array($goods_data_id)){
            $where_goods['goods_commonid'] = array('in',htmlspecialchars($_GET['id']));
            $goods_greal_id = $model->table('goods,product')->join('left')->on('goods.goods_serial = product.product_id')->field('goods.goods_marketprice,goods_name,goods_id,goods_commonid,goods_serial,to_product_id')->where($where_goods)->select();
        }else{
            $where_goods['goods_commonid'] = htmlspecialchars($_GET['id']);
            $goods_greal_id = $model->table('goods,product')->join('left')->on('goods.goods_serial = product.product_id')->field('goods.goods_marketprice,goods_name,goods_id,goods_commonid,goods_serial,to_product_id')->where($where_goods)->select();
        }
        $nbmc  = array();
        if(is_array($goods_greal_id)){
            foreach ($goods_greal_id as $v){
                $nebuwl=$model->table('product')->field('local_description,product_spec')->where(" product_id =  '".$v['to_product_id']."'")->find();
                $v['local_description']=$nebuwl['local_description'];
                $v['product_spec']=$nebuwl['product_spec'];
                $nbmc[]=$v;
            }
        }
        Tpl::output('goods_greal',$nbmc);
        Tpl::output('commonids', $_GET['id']);
        Tpl::showpage('goods.verify_remark', 'null_layout');
    }
    
    /*

     * 内部编号查询
     *      */
    
    public function goods_nbbhOp(){
        $model = Model();
        //查询内部物料编号前获取当前商品分类
        $goods_class = $model->table('goods')->where('goods_id='.$_GET['g_id'])->field('gc_id,gc_id_1,gc_id_2,gc_id_3')->find();

        $where['product_level'] = 1;
        $where['gc_id_1'] = $goods_class['gc_id_1'];
        $where['gc_id_2'] = $goods_class['gc_id_2'];
        $where['gc_id_3'] = $goods_class['gc_id_3'];
        //查询当前物料分类名称
        $gc_name = $model->table('product')->where('product_code="'.$_GET['id'].'"')->field('gc_classname')->find();
        
        $nbbh  = $model->table('product')->field('local_description,product_code,product_spec,brand')->where($where)->select();

        // 一级商品分类
        $gc_list = Model('goods_class')->getGoodsClassListByParentId(0);
        Tpl::output('gc_name',$gc_name['gc_classname']);
        Tpl::output('gc_list', $gc_list);
        Tpl::output('bh',$nbbh);
        Tpl::output('ids',$_GET['id']);
        Tpl::showpage('goods.goods_nbbh','null_layout');
    }

    public function goods_nbbh_serOp(){
        $model = Model();
        $name = htmlspecialchars($_POST['name']);
        $product_code = htmlspecialchars($_POST['product_code']);
        if($_POST['mls_id'] > 0){
            $post_gcid = $_POST['mls_id'];
            $like = Model()->table("goods_class")->field("gc_class_code")->where("gc_id=".$post_gcid)->find();
            $where['product_id'] = array('like',$like['gc_class_code']."%");
        }
        $where['product_level'] = 1;
        if($name){
            $where['local_description'] = array('like',"%".$name."%");
        }
        if($product_code){
            $where['product_code'] = array('like',"%".$product_code."%");
        }
        $list  = $model->table('product')->field('local_description,product_code,product_spec,brand')->where($where)->select();

        $data = "<tr class='noborder'>"
                . "<td colspan='2' class='required'><label>物料名称</label></td>"
                . "<td colspan='2' class='required'><label>物料编号</label></td>"
                . "<td colspan='2' class='required'><label>品牌</label></td>"
                . "<td colspan='2' class='required'><label>规格</label></td>"
                . "</tr>";

        foreach($list as $rows){
            $data .= "<tr class='noborder'>"
                    . "<td colspan='2'>".$rows['local_description']."</td>"
                    ." <td colspan='2' class='product_id_close'>".$rows['product_code']."</td>"
                    . "<td colspan='2' >".$rows['brand']."</td>"
                    . "<td colspan='2' >".$rows['product_spec']."</td>"
                    . "</tr>";
        }
        echo $data;
    }
    /**
     * 更新goods_common 商品佣金比例
     */
    public function goods_commis_rate_updateOp() {
        $cid = intval($_GET['id']);
        if($cid <= 0) {
            echo json_encode(array('result'=>FALSE,'message'=>Language::get('param_error')));
            die;
        }
        $new_commis_rate = intval($_GET['value']);

//        Log::record('goods_commis_rate_update:   '.$bid.' & '.$new_commis_rate ,'ERR');
        if ($new_commis_rate < 0 || $new_commis_rate >= 100) {
            echo json_encode(array('result'=>FALSE,'message'=>Language::get('param_error')));
            die;
        } else {
            $update = array('goods_commis_rate' => $new_commis_rate);
            $condition = array('goods_commonid' => $cid);
            $model_goods = Model('goods');
            $result = $model_goods->editGoodsCommon($update, $condition);

//            $model = Model();
//            $result = $model->table('goods_common')->where($condition)->update($update);
            if($result) {
                echo json_encode(array('result'=>TRUE));
                die;
            } else {
                echo json_encode(array('result'=>FALSE,'message'=>L('nc_common_op_fail')));
                die;
            }
        }
    }

    /**
     * 更新商品佣金截至时间
     */
    public function goods_commis_time_updateOp() {
        $cid = intval($_POST['pk']);
        if($cid <= 0) {
            echo json_encode(array('result'=>FALSE,'message'=>Language::get('param_error')));
            die;
        }
        $post_time = strval($_POST['value']);
        if(empty($post_time)){
            $new_commis_end = 0;
        }else{
            $new_commis_end = strtotime($post_time);
        }

        $update = array('goods_commis_end' => $new_commis_end);
        $condition = array('goods_commonid' => $cid);
        $model_goods = Model('goods');
        $result = $model_goods->editGoodsCommon($update, $condition);

        if($result) {
            echo json_encode(array('result'=>TRUE));
            die;
        } else {
            echo json_encode(array('result'=>FALSE,'message'=>L('nc_common_op_fail')));
            die;
        }
    }


    /**
     * ajax获取商品列表
     */
    public function get_goods_list_ajaxOp() {
        $commonid = $_GET['commonid'];
        if ($commonid <= 0) {
            echo 'false';exit();
        }
        $model_goods = Model('goods');
        $goodscommon_list = $model_goods->getGoodeCommonInfoByID($commonid, 'spec_name');
        if (empty($goodscommon_list)) {
            echo 'false';exit();
        }
        $goods_list = $model_goods->getGoodsList(array('goods_commonid' => $commonid), 'goods_id,goods_spec,store_id,goods_price,goods_serial,goods_storage,goods_image,goods_marketprice,g_costprice,min_num');
        if (empty($goods_list)) {
            echo 'false';exit();
        }

        $spec_name = array_values((array)unserialize($goodscommon_list['spec_name']));
        foreach ($goods_list as $key => $val) {
            $goods_spec = array_values((array)unserialize($val['goods_spec']));
            $spec_array = array();
            foreach ($goods_spec as $k => $v) {
                $spec_array[] = '<div class="goods_spec">' . $spec_name[$k] . L('nc_colon') . '<em title="' . $v . '">' . $v .'</em>' . '</div>';
            }
            $goods_list[$key]['goods_image'] = thumb($val, '60');
            $goods_list[$key]['goods_spec'] = implode('', $spec_array);
            $goods_list[$key]['url'] = urlShop('goods', 'index', array('goods_id' => $val['goods_id']));
        }
        /**
         * 转码
         */
        if (strtoupper(CHARSET) == 'GBK') {
            Language::getUTF8($goods_list);
        }
        echo json_encode($goods_list);
    }



    /**
     * 下架商品批量删除
     */
    public function goods_lockup_delallOp() {
        $page = intval($_GET['page']);
        $model_goods = Model ( 'goods' );

        /**
         * 查询条件
         */
        $where = array();
        $goods_list = $model_goods->getGoodsCommonLockUpList($where);
        foreach ($goods_list as $key => $val) {
            $common_id = intval( $goods_list[$key]['goods_commonid']);
            if ($common_id > 0) {
                Model('goods')->delGoodsAll(array('goods_commonid' => $common_id));
            }
        }
        $page=$page+1;
        if($page < 5 ){
            echo "第".$page."页删除成功<script>window.location.href='". urlAdmin('goods', 'goods_lockup_delall', array('page' => $page))."'</script>";
        }else{
            echo '删除完成';
        }


//        Tpl::showpage('goods.close');
    }
    /**
     * 商品审核时对商品的价格进行修改 同时更新物料表对应价格数据
     * @param type $product_id  审核的外部物料
     */
    public function updateProductAsGoodsPrice($product_id){
        $model = Model();
        $price_list  = $model->table('goods')->where(array("materiel_code"=>$product_id))->field("goods_price,goods_marketprice,g_costprice,goods_third_price")->find();
        $update = array();
        $update['vs_price']=$price_list['goods_price'];
        $update['contract_price']=$price_list['goods_marketprice'];
        $update['reference_price']=$price_list['g_costprice'];
        $update['member_price']=$price_list['goods_third_price'];
        $model->table('product')->where(array("product_id"=>$product_id))->update($update);
    }



    /**
     * 取得商品缩略图的完整URL路径，接收商品信息数组，返回所需的商品缩略图的完整URL
     *
     * @param array $goods 商品信息数组
     * @param string $type 缩略图类型  值为60,240,360,1280
     * @return string
     */
    private function cthumbSup($goods = array(), $type = ''){
        $type_array = explode(',_', ltrim(GOODS_IMAGES_EXT, '_'));
        if (!in_array($type, $type_array)) {
            $type = '240';
        }
        if (empty($goods)){
            return UPLOAD_SITE_URL.'/'.defaultGoodsImage($type);
        }
        if (array_key_exists('apic_cover', $goods)) {
            $goods['goods_image'] = $goods['apic_cover'];
        }
        if (empty($goods['goods_image'])) {
            return UPLOAD_SITE_URL.'/'.defaultGoodsImage($type);
        }
        $search_array = explode(',', GOODS_IMAGES_EXT);
        $file = str_ireplace($search_array,'',$goods['goods_image']);
        $fname = basename($file);
        //取店铺ID
        if (preg_match('/^(\d+_)/',$fname)){
            $store_id = substr($fname,0,strpos($fname,'_'));
        }else{
            $store_id = $goods['store_id'];
        }
        $file = $type == '' ? $file : str_ireplace('.', '_' . $type . '.', $file);
        if (!file_exists(BASE_UPLOAD_PATH.'/'.ATTACH_GOODS.'/'.$store_id.'/'.$file)){
            return UPLOAD_SITE_URL.'/'.defaultGoodsImage($type);
        }
        $thumb_host = UPLOAD_SITE_URL.'/'.ATTACH_GOODS;
        return $thumb_host.'/'.$store_id.'/'.$file;
    }
    
}
