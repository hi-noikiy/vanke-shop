<?php
/**
 * 商品管理
 *
 *
 *
 ***/


class store_goods_onlineControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct ();
        Language::read ('member_store_goods_index');
    }
    public function indexOp() {
        $this->goods_listOp();
    }

    /**
     * 出售中的商品列表
     */
    public function goods_listOp() {
        $model_goods = Model('goods');

        $where = array();
        $where['store_id'] = $_SESSION['store_id'];
        if (intval($_GET['stc_id']) > 0) {
            $where['goods_stcids'] = array('like', '%,' . intval($_GET['stc_id']) . ',%');
        }
        if (trim($_GET['keyword']) != '') {
            switch ($_GET['search_type']) {
                case 0:
                    $where['goods_name'] = array('like', '%' . trim($_GET['keyword']) . '%');
                    break;
                case 1:
                    $where['goods_serial'] = array('like', '%' . trim($_GET['keyword']) . '%');
                    break;
                case 2:
                    $where['goods_commonid'] = intval($_GET['keyword']);
                    break;
            }
        }
        $goods_list = $model_goods->getGoodsCommonOnlineList($where);
        $new_good = array();
        if(!empty($goods_list) && is_array($goods_list)){
            foreach ($goods_list as $vl){
                $sp_nam = unserialize($vl['spec_name']);
                $sk_data = array();
                if(!empty(is_array(unserialize($vl['spec_value'])))){
                    foreach (unserialize($vl['spec_value']) as $key=>$v){
                        $sk_data[] = $sp_nam[$key];
                    }
                }
                $vl['sk_num'] = count($sk_data);
                $new_good[] = $vl;
            }
        }
        Tpl::output('show_page', $model_goods->showpage());
        Tpl::output('goods_list', $new_good);

        // 计算库存
        $storage_array = $model_goods->calculateStorage($goods_list);
        Tpl::output('storage_array', $storage_array);

        // 商品分类
        $store_goods_class = Model('store_goods_class')->getClassTree(array('store_id' => $_SESSION['store_id'], 'stc_state' => '1'));
        Tpl::output('store_goods_class', $store_goods_class);

        $this->profile_menu('goods_list', 'goods_list');
        Tpl::showpage('store_goods_list.online');
    }

    /**
     * 编辑商品页面
     */
    public function edit_goodsOp() {
        $common_id = $_GET['commonid'];
        if ($common_id <= 0) {
            showMessage(L('wrong_argument'), '', 'html', 'error');
        }
        $model_goods = Model('goods');
        $goodscommon_info = $model_goods->getGoodeCommonInfoByID($common_id);
        if (empty($goodscommon_info) || $goodscommon_info['store_id'] != $_SESSION['store_id'] || $goodscommon_info['goods_lock'] == 1) {
            showMessage(L('wrong_argument'), '', 'html', 'error');
        }
        $where = array('goods_commonid' => $common_id, 'store_id' => $_SESSION['store_id']);
        $goodscommon_info['g_storage'] = $model_goods->getGoodsSum($where, 'goods_storage');
        $goodscommon_info['spec_name'] = unserialize($goodscommon_info['spec_name']);
        if ($goodscommon_info['mobile_body'] != '') {
            $goodscommon_info['mb_body'] = unserialize($goodscommon_info['mobile_body']);
            //
	    if (is_array($goodscommon_info['mb_body'])) {
                $mobile_body = '[';
                foreach ($goodscommon_info['mb_body'] as $val ) {
                    $mobile_body .= '{"type":"' . $val['type'] . '","value":"' . $val['value'] . '"},';
                }
                $mobile_body = rtrim($mobile_body, ',') . ']';
            }
            $goodscommon_info['mobile_body'] = $mobile_body;
        }
        Tpl::output('goods', $goodscommon_info);

        if (intval($_GET['class_id']) > 0) {
            $goodscommon_info['gc_id'] = intval($_GET['class_id']);
        }
        $goods_class = Model('goods_class')->getGoodsClassLineForTag($goodscommon_info['gc_id']);
        Tpl::output('goods_class', $goods_class);

        $model_type = Model('type');
        // 获取类型相关数据
        $typeinfo = $model_type->getAttr($goods_class['type_id'], $_SESSION['store_id'], $goodscommon_info['gc_id']);
        list($spec_json, $spec_list, $attr_list, $brand_list) = $typeinfo;
        Tpl::output('spec_json', $spec_json);
        Tpl::output('sign_i', count($spec_list));
        Tpl::output('spec_list', $spec_list);
        Tpl::output('attr_list', $attr_list);
        Tpl::output('brand_list', $brand_list);

        // 取得商品规格的输入值
        $goods_array = $model_goods->getGoodsList($where, 'goods_id,g_costprice,goods_toc_price,goods_third_price,goods_marketprice,goods_price,goods_storage,goods_serial,goods_storage_alarm,goods_spec,min_num,max_num');
        $sp_value = array();
        if (!empty(is_array($goods_array))) {

            // 取得已选择了哪些商品的属性
            $attr_checked_l = $model_type->typeRelatedList ( 'goods_attr_index', array (
                    'goods_id' => intval ( $goods_array[0]['goods_id'] )
            ), 'attr_value_id' );
            if (! empty (is_array ( $attr_checked_l )) ) {
                $attr_checked = array ();
                foreach ( $attr_checked_l as $val ) {
                    $attr_checked [] = $val ['attr_value_id'];
                }
            }
            Tpl::output ( 'attr_checked', $attr_checked );

            $spec_checked = array();
            foreach ( $goods_array as $k => $v ) {
                $a = unserialize($v['goods_spec']);
                if (!empty(is_array($a))) {
                    foreach ($a as $key => $val){
                        $spec_checked[$key]['id'] = $key;
                        $spec_checked[$key]['name'] = $val;
                    }
                    $matchs = array_keys($a);
                    sort($matchs);
                    $id = str_replace ( ',', '', implode ( ',', $matchs ) );
                    $sp_value ['i_' . $id . '|marketprice'] = $v['goods_marketprice'];
                    $sp_value ['i_' . $id . '|g_costprice'] = $v['g_costprice'];
                    $sp_value ['i_' . $id . '|goods_toc_price'] = $v['goods_toc_price'];
                    $sp_value ['i_' . $id . '|goods_third_price'] = $v['goods_third_price'];
                    $sp_value ['i_' . $id . '|price'] = $v['goods_price'];
                    $sp_value ['i_' . $id . '|id'] = $v['goods_id'];
                    $sp_value ['i_' . $id . '|stock'] = $v['goods_storage'];
                    $sp_value ['i_' . $id . '|alarm'] = $v['goods_storage_alarm'];
                    $sp_value ['i_' . $id . '|sku'] = $v['goods_serial'];
                    $sp_value ['i_' . $id . '|min_num'] = $v['min_num'];
                    $sp_value ['i_' . $id . '|max_num'] = $v['max_num'];
                }
            }
            Tpl::output('spec_checked', $spec_checked);
        }
        Tpl::output ( 'sp_value', $sp_value );

        // 实例化店铺商品分类模型
        $store_goods_class = Model('store_goods_class')->getClassTree(array('store_id' => $_SESSION ['store_id'], 'stc_state' => '1'));
        Tpl::output('store_goods_class', $store_goods_class);
        //处理商品所属分类
        $store_goods_class_tmp = array();
        if (!empty($store_goods_class)){
            foreach ($store_goods_class as $k=>$v) {
                $store_goods_class_tmp[$v['stc_id']] = $v;
                if (is_array($v['child'])) {
                    foreach ($v['child'] as $son_k=>$son_v){
                        $store_goods_class_tmp[$son_v['stc_id']] = $son_v;
                    }
                }
            }
        }
        $goodscommon_info['goods_stcids'] = trim($goodscommon_info['goods_stcids'], ',');
        $goods_stcids = empty($goodscommon_info['goods_stcids'])?array():explode(',', $goodscommon_info['goods_stcids']);
        $goods_stcids_tmp = $goods_stcids_new = array();
        if (!empty($goods_stcids)){
            foreach ($goods_stcids as $k=>$v){
                $stc_parent_id = $store_goods_class_tmp[$v]['stc_parent_id'];
                //分类进行分组，构造为array('1'=>array(5,6,8));
                if ($stc_parent_id > 0){//如果为二级分类，则分组到父级分类下
                    $goods_stcids_tmp[$stc_parent_id][] = $v;
                } elseif (empty($goods_stcids_tmp[$v])) {//如果为一级分类而且分组不存在，则建立一个空分组数组
                    $goods_stcids_tmp[$v] = array();
                }
            }
            foreach ($goods_stcids_tmp as $k=>$v){
                if (!empty($v) && count($v) > 0){
                    $goods_stcids_new = array_merge($goods_stcids_new,$v);
                } else {
                    $goods_stcids_new[] = $k;
                }
            }
        }
        Tpl::output('store_class_goods', $goods_stcids_new);

        // 是否能使用编辑器
        if(checkPlatformStore()){ // 平台店铺可以使用编辑器
            $editor_multimedia = true;
        } else {    // 三方店铺需要
            $editor_multimedia = false;
            if ($this->store_grade['sg_function'] == 'editor_multimedia') {
                $editor_multimedia = true;
            }
        }
        Tpl::output ( 'editor_multimedia', $editor_multimedia );

        // 小时分钟显示
        $hour_array = array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23');
        Tpl::output('hour_array', $hour_array);
        $minute_array = array('05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55');
        Tpl::output('minute_array', $minute_array);

        // 关联版式
        $plate_list = Model('store_plate')->getStorePlateList(array('store_id' => $_SESSION['store_id']), 'plate_id,plate_name,plate_position');
        $plate_list = array_under_reset($plate_list, 'plate_position', 2);
        Tpl::output('plate_list', $plate_list);

        // F码
        if ($goodscommon_info['is_fcode'] == 1) {
            $fcode_array = Model('goods_fcode')->getGoodsFCodeList(array('goods_commonid' => $goodscommon_info['goods_commonid']));
            Tpl::output('fcode_array', $fcode_array);
        }
        $menu_promotion = array(
            'lock' => $goodscommon_info['goods_lock'] == 1 ? true : false,
            'gift' => $model_goods->checkGoodsIfAllowGift($goodscommon_info),
            'combo' => $model_goods->checkGoodsIfAllowCombo($goodscommon_info)
        );
        $this->profile_menu('edit_detail','edit_detail', $menu_promotion);
        
        //获取相关城市公司信息
        /* @Aletta 2017.06.05 */
        $model = Model();
        $field = 'store_joinin.store_state,store_joinin.city_center,city_centre.city_name';
        $on = 'store_joinin.city_center = city_centre.id';
        $city_store_list = $model->table('store_joinin,city_centre')->field($field)->join('left')->on($on)->where(array('store_joinin.member_id'=>$_SESSION['member_id'],'store_state'=>'40'))->select();
        Tpl::output('city_store_list', $city_store_list);
        
        //获取当前商品的销售区域
        $good_sales_area = $model->table('goods_common')->field("sales_area")->where(array('goods_commonid'=>$common_id))->find();
        Tpl::output('good_sales_area', explode(',',$good_sales_area['sales_area']));
        
        //Tpl::output('edit_goods_sign', true);
        Tpl::showpage('store_goods_edit.step2');
    }
    
    
    /**
     * 保存商品基本数据信息
     **/
    public function edit_save_dataOp(){
        $common_id = intval ( $_POST ['commonid'] );
        if (!chksubmit() || $common_id <= 0) {
            showDialog(L('store_goods_index_goods_edit_fail'), urlShop('store_goods_online', 'index'));
        }
        // 验证表单
        $obj_validate = new Validate ();
        $obj_validate->validateparam = array (
            array (
                "input" => $_POST["g_name"],
                "require" => "true",
                "message" => L('store_goods_index_goods_name_null')
            ),
            array (
                "input" => $_POST["g_price"],
                "require" => "true",
                "validator" => "Double",
                "message" => L('store_goods_index_goods_price_null')
            )
        );
        $error = $obj_validate->validate ();
        if ($error != '') {
            showDialog(L('error') . $error, urlShop('store_goods_online', 'index'));
        }

        $model = Model();
        $member_info = $model->table('store')->field('member_id')->where(array("store_id"=>$_SESSION['store_id']))->find();
        $goods_dri = BASE_DATA_PATH . DS . 'goods_data' . DS . $member_info["member_id"] . '.txt';
        $file_old=fopen($goods_dri,"w");
        fwrite($file_old,serialize($_POST));
        fclose($file_old);
        $url="/index.php?act=store_goods_online&op=edit_step_two_href&commonid=".$common_id;
        echo " <script language ='javascript' type = 'text/javascript'> ";
        echo " window.location.href = '$url' ";
        echo " </script> ";
        exit();
    }
    
    
    /**
     * 处理商品规格数据
     **/
    public function edit_step_two_hrefOp(){
        $common_id = $_GET['commonid'];
        if ($common_id <= 0) {
            showMessage(L('wrong_argument'), '', 'html', 'error');
        }
        $model = Model();
        $member_info = $model->table('store')->field('member_id')->where(array("store_id"=>$_SESSION['store_id']))->find();
        $goods_dri = BASE_DATA_PATH . DS . 'goods_data' . DS . $member_info["member_id"] . '.txt';
        $file_new=fopen($goods_dri,"r");
        $file_read = fread($file_new, filesize($goods_dri));
        fclose($file_new);
        $arr_new = unserialize($file_read);
        
        $model_goods = Model('goods');
        $goodscommon_info = $model_goods->getGoodeCommonInfoByID($common_id);
        $menu_promotion = array(
            'lock' => $goodscommon_info['goods_lock'] == 1 ? true : false,
            'gift' => $model_goods->checkGoodsIfAllowGift($goodscommon_info),
            'combo' => $model_goods->checkGoodsIfAllowCombo($goodscommon_info)
        );
        $this->profile_menu('edit_detail','edit_detail', $menu_promotion);
        
        $specifications_data = array();
        $specifications_check_data = array();
        if(!empty($arr_new['sp_val']) && is_array($arr_new['sp_val'])){
            foreach ($arr_new['sp_val'] as $key=>$val){
                $specifications_check_data[] = array("kv"=>$key,"s_name"=>$arr_new['sp_name'][$key]);
                $specifications_data[] = $val;
            }
        }
        Tpl::output('goodscommon', $goodscommon_info);
        if(!empty($specifications_data)){
            $sp_data = $this->getArrSetKey($this->getArrSet($specifications_data),$specifications_data);
            Tpl::output('specifications', $sp_data);
            Tpl::output('good_data', $this->get_sp_goods_list($common_id,$sp_data));
            Tpl::output('specifications_check', $specifications_check_data);
            Tpl::showpage('store_goods_edit.step02');
        }else{
            $field = 'goods_id,goods_marketprice,goods_price,g_costprice,goods_third_price,goods_storage,goods_storage_alarm,min_num,max_num,goods_spec';
            $good_list = $model->table('goods')->field($field)->where(array("goods_commonid"=>$common_id))->find();
            Tpl::output('good_data', $good_list);
            Tpl::showpage('store_goods_edit.step12');
        }
    }
    
    
    /**
     * 获取新规格商品数据
     **/
    private function get_sp_goods_list($com_id,$sp_data){
        if(!empty($com_id) && !empty($sp_data) && is_array($sp_data)){
            $model = Model();
            //获取所有商品的数据
            $field = 'goods_id,goods_marketprice,goods_price,g_costprice,goods_third_price,goods_storage,goods_storage_alarm,min_num,max_num,goods_spec';
            $good_list = $model->table('goods')->field($field)->where(array("goods_commonid"=>$com_id))->select();
            $new_good_list = array();
            if(!empty($good_list) && is_array($good_list)){
                foreach ($good_list as $good_val){
                    $good_sp_data = unserialize($good_val['goods_spec']);
                    if(!empty($good_sp_data) && is_array($good_sp_data)){
                        $key_data = array();
                        foreach ($good_sp_data as $key=>$g_val){
                            $key_data[] = $key;
                        }
                    }
                    $new_good_list[implode('_',$key_data)] = $good_val;
                }
            }
            $list = array();
            foreach ($sp_data as $sp_key=>$sp_val){
                $list[$sp_key] = $new_good_list[$sp_key];
            }
            return $list;
        }
    }
    
    /**
     * 编辑商品保存
     */
    public function edit_save_goodsOp() {
        $common_id = intval ( $_POST ['commonid'] );
        if (!chksubmit() || $common_id <= 0) {
            showDialog(L('store_goods_index_goods_edit_fail'), urlShop('store_goods_online', 'index'));
        }
        //获取之前保存的商品基本信息
        $model = Model();
        $member_info = $model->table('store')->field('member_id')->where(array("store_id"=>$_SESSION['store_id']))->find();
        $goods_dri = BASE_DATA_PATH . DS . 'goods_data' . DS . $member_info["member_id"] . '.txt';
        $file_new=fopen($goods_dri,"r");
        $file_read = fread($file_new, filesize($goods_dri));
        fclose($file_new);
        $good_old_data = unserialize($file_read);
        if(!empty($_POST['spec'])){
            $good_old_data['spec'] = $_POST['spec'];
        }else{
            $good_old_data['spec'][] = array(
                'good_sku'          => '',
                'good_id'           => $_POST['good_id'],
                "sp_value"          =>'',
                "marketprice"       =>$_POST['g_marketprice'],
                "price"             =>$_POST['g_price'],
                "goods_third_price" =>$_POST['goods_third_price'],
                "cosprice"          =>$_POST['g_costprice'],
                "stock"             =>$_POST['g_storage'],
                "alarm"             =>$_POST['g_alarm'],
                "min_num"           =>$_POST['min_num'],
                "max_num"           =>$_POST['max_num'],
            );
        }
        // 验证商品分类是否存在且商品分类是否为最后一级
        $data = Model('goods_class')->getGoodsClassForCacheModel();
        if (!isset($data[$good_old_data['cate_id']]) || isset($data[$good_old_data['cate_id']]['child']) || isset($data[$good_old_data['cate_id']]['childchild'])) {
            showDialog(L('store_goods_index_again_choose_category1'));
        }
        //cary 如果是绑定商城分类,那么不判断分类
        if (!C('store_class_bind_isuse')) {
            // 三方店铺验证是否绑定了该分类
            if (!checkPlatformStore()) {
                //去除商城分类检测,  在商品添加时候已经处理
                //商品分类 提供批量显示所有分类插件
                $model_bind_class = Model('store_bind_class');
                $goods_class = Model('goods_class')->getGoodsClassForCacheModel();
                $where['store_id'] = $_SESSION['store_id'];
                $class_2 = $goods_class[$good_old_data['cate_id']]['gc_parent_id'];
                $class_1 = $goods_class[$class_2]['gc_parent_id'];
                $where['class_1'] =  $class_1;
                $where['class_2'] =  $class_2;
                $where['class_3'] =  $good_old_data['cate_id'];
                $bind_info = $model_bind_class->getStoreBindClassInfo($where);
                if (empty($bind_info)){
                    $where['class_3'] =  0;
                    $bind_info = $model_bind_class->getStoreBindClassInfo($where);
                    if (empty($bind_info)){
                        $where['class_2'] =  0;
                        $where['class_3'] =  0;
                        $bind_info = $model_bind_class->getStoreBindClassInfo($where);
                        if (empty($bind_info)){
                            $where['class_1'] =  0;
                            $where['class_2'] =  0;
                            $where['class_3'] =  0;
                            $bind_info = $model_bind_class->getStoreBindClassInfo($where);
                            if (empty($bind_info)){
                                showDialog(L('store_goods_index_again_choose_category2'));
                            }
                        }
                    }
                }
            }
        }
        // 分类信息
        $goods_class = Model('goods_class')->getGoodsClassLineForTag(intval($good_old_data['cate_id']));
        //将对应的code插入物料表内
        $supply_code = $model->table('member')->field('supply_code')->where(array("member_id"=>$member_info['member_id']))->find();
        // 开始事务
        $model->beginTransaction();
        $model_gift = Model('goods_gift');
        // 清除原有规格数据
        $model_type = Model('type');
        $model_goods = Model ( 'goods' );
        require_once(BASE_RESOURCE_PATH.DS.'phpqrcode'.DS.'index.php');
        $PhpQRCode = new PhpQRCode();
        $PhpQRCode->set('pngTempDir',BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$_SESSION['store_id'].DS);
        $model_type->delGoodsAttr(array('goods_commonid' => $common_id));
        if (!empty($good_old_data ['spec']) && is_array ( $good_old_data ['spec'] )) {
            $model_goods = Model ( 'goods' );
            $common_data = $this->handleGoodCommonData($good_old_data,$goods_class);
            $gd_id = array();
            $good_rest = array();
            $product_rest = array();
            foreach ($good_old_data['spec'] as $key=>$good_value) {
                if(!empty($good_value['good_id'])){
                    $goods_info = $model_goods->getGoodsInfo(array('goods_id' => $good_value['good_id'], 'goods_commonid' => $common_id, 'store_id' => $_SESSION['store_id']), 'goods_id,goods_serial');
                    if(!empty($goods_info)){
                        //获取新的商品数据
                        $good_new_data = $this->getGoodData($common_id,$good_value,$key,$common_data,$goods_class,'old');
                        //跟新商品数据
                        $good_new_id = $model_goods->editGoodsById($good_new_data, $good_value['good_id']);
                        //跟新相对应的物料数据
                        $product_id = $this->upProductData($good_new_data,$common_data,$goods_info);
                        //如果good_id不为空，则进行查找更新
                        if($common_data['is_virtual'] == 1){
                            $model_gift->delGoodsGift(array('goods_id' => $good_value['good_id']));
                        }
                        $gd_id[] = $good_value['good_id'];
                        $good_rest[] =  $good_new_id ? 1:2;
                        $product_rest[] =  $product_id ? 1:2;
                        $colorid_array[] = intval($good_value['color']);
                        // 生成商品二维码
                        $PhpQRCode->set('date',WAP_SITE_URL . '/tmpl/product_detail.html?goods_id='.$good_value['good_id']);
                        $PhpQRCode->set('pngTempName', $good_value['good_id'] . '.png');
                        $PhpQRCode->init();
                    }
                }else{
                    $good_new_data = $this->getGoodData($common_id,$good_value,$key,$common_data,$goods_class,'new');
                    $product_data = $this->specificationsExternalMaterial($good_new_data,$good_value,$common_data,$supply_code,$goods_class);
                    $goods_id = $model_goods->addGoods($good_new_data);
                    $product_id = $model->table('product')->insert($product_data);
                    // 生成商品二维码
                    $PhpQRCode->set('date',WAP_SITE_URL . '/tmpl/product_detail.html?goods_id='.$goods_id);
                    $PhpQRCode->set('pngTempName', $goods_id . '.png');
                    $PhpQRCode->init();
                    $colorid_array[] = 0;
                    $gd_id[] = $goods_id;
                    $good_rest[] =  $goods_id ? 1:2;
                    $product_rest[] =  $product_id ? 1:2;
                    $model_type->addGoodsType($goods_id, $common_id, array('cate_id' => $good_old_data['cate_id'], 'type_id' => $good_old_data['type_id'], 'attr' => $good_old_data['attr']));
                }
            }
            // 清理商品数据
            $model_goods->delGoods(array('goods_id' => array('not in', $gd_id), 'goods_commonid' => $common_id, 'store_id' => $_SESSION['store_id']));
            // 清理商品图片表
            $colorid_array = array_unique($colorid_array);
            $model_goods->delGoodsImages(array('goods_commonid' => $common_id, 'color_id' => array('not in', $colorid_array)));
            // 更新商品默认主图
            $default_image_list = $model_goods->getGoodsImageList(array('goods_commonid' => $common_id, 'is_default' => 1), 'color_id,goods_image');
            if (!empty($default_image_list)) {
                foreach ($default_image_list as $val) {
                    //更新sc_goods_image
                    $default_image = Model()->table('goods_images')->field("goods_image_id")->where(array("goods_commonid"=>$common_id,'color_id' => $val['color_id'],'is_default'=>"1"))->select();
                    if($default_image){
                        Model()->table('goods_images')->where(array("goods_commonid"=>$common_id,'color_id' => $val['color_id'],'is_default'=>"1"))->update(array('goods_image' => $common_data['goods_image']));
                    }
                }
            
            }
            // 商品加入上架队列
            if (isset($good_old_data['starttime'])) {
                $selltime = strtotime($good_old_data['starttime']) + intval($good_old_data['starttime_H'])*3600 + intval($good_old_data['starttime_i'])*60;
                if ($selltime > TIMESTAMP) {
                    $this->addcron(array('exetime' => $selltime, 'exeid' => $common_id, 'type' => 1), true);
                }
            }
            // 添加操作日志
            $this->recordSellerLog('编辑商品，平台货号：'.$common_id);
            
            if ($common_data['is_virtual'] == 1 || $common_data['is_fcode'] == 1 || $common_data['is_presell'] == 1) {
                // 如果是特殊商品清理促销活动，抢购、限时折扣、组合销售
                QueueClient::push('clearSpecialGoodsPromotion', array('goods_commonid' => $common_id, 'goodsid_array' => $gd_id));
            } else {
                // 更新商品促销价格
                QueueClient::push('updateGoodsPromotionPriceByGoodsCommonId', $common_id);
            }
            
            // 生成F码
            if ($common_data['is_fcode'] == 1) {
                QueueClient::push('createGoodsFCode', array('goods_commonid' => $common_id, 'fc_count' => intval($good_old_data['g_fccount']), 'fc_prefix' => $good_old_data['g_fcprefix']));
            }
            $common_rest = $model_goods->editGoodsCommon($common_data, array('goods_commonid' => $common_id, 'store_id' => $_SESSION['store_id']));
            if ($common_rest && !in_array("2", $good_rest) && !in_array("2", $product_rest)) {
                //提交事务
                Model()->commit();
                showDialog(L('nc_common_op_succ'), urlShop('store_goods_online', 'index'), 'succ');
            } else {
                //回滚事务
                Model()->rollback();
                showDialog(L('store_goods_index_goods_edit_fail'), urlShop('store_goods_online', 'index'));
            }
        }
    }
    
    
    /**
     * 操作商品数据更新逻辑业务
     **/
    private function getGoodData($common_id,$good_data,$key_val,$common_data,$goods_class,$type){
        if(!empty($common_data) && !empty($good_data) && is_array($good_data)){
            $class_data_str = !empty($good_data['sp_value']) && is_array($good_data['sp_value']) ? implode(' ', $good_data['sp_value']):"";
            if(!empty($good_data['sp_value']) && is_array($good_data['sp_value']) && !empty($key_val)){
                $key_val_data = explode("_",$key_val);
                $sp_value_data = array();
                foreach ($good_data['sp_value'] as $k=>$vl){
                    $sp_value_data[$key_val_data[$k]] = $vl;
                }
            }
            $good_new_data = array(
                'goods_commonid'        =>$common_id,
                'goods_name'            =>$common_data['goods_name'] . ' ' . $class_data_str,
                'goods_jingle'          =>$common_data['goods_jingle'],
                'store_id'              =>$_SESSION['store_id'],
                'store_name'            =>$_SESSION['store_name'],
                'gc_id'                 =>$common_data['gc_id'],
                'gc_id_1'               =>$common_data['gc_id_1'],
                'gc_id_2'               =>$common_data['gc_id_2'],
                'gc_id_3'               =>$common_data['gc_id_3'],
                'brand_id'              =>$common_data['brand_id'],
                'goods_price'           =>$good_data['price'],
                'goods_promotion_price' =>$good_data['price'],
                'goods_storage_alarm'   =>intval($good_data['alarm']),
                'goods_spec'            =>!empty($sp_value_data) && is_array($sp_value_data) ? serialize($sp_value_data):"N;",
                'goods_storage'         =>$good_data['stock'],
                'goods_state'           =>$common_data['goods_state'],
                'goods_verify'          =>$common_data['goods_verify'],
                'goods_edittime'        =>TIMESTAMP,
                'areaid_1'              =>$common_data['areaid_1'],
                'areaid_2'              =>$common_data['areaid_2'],
                'transport_id'          =>$common_data['transport_id'],
                'goods_freight'         =>$common_data['goods_freight'],
                'goods_vat'             =>$common_data['goods_vat'],
                'goods_commend'         =>$common_data['goods_commend'],
                'goods_stcids'          =>$common_data['goods_stcids'],
                'is_virtual'            =>$common_data['is_virtual'],
                'virtual_indate'        =>$common_data['virtual_indate'],
                'virtual_limit'         =>$common_data['virtual_limit'],
                'virtual_invalid_refund'=>$common_data['virtual_invalid_refund'],
                'is_fcode'              =>$common_data['is_fcode'],
                'is_appoint'            =>$common_data['is_appoint'],
                'is_presell'            =>$common_data['is_presell'],
                'min_num'               =>$good_data['min_num'],
                'max_num'               =>$good_data['max_num'],
                'is_own_shop'           =>$common_data['is_own_shop'],
                'sales_area'            =>$common_data['sales_area'],
                'g_costprice'           =>$good_data['cosprice'],
                'goods_toc_price'       =>0,
                'goods_third_price'     =>$good_data['marketprice'],
                'goods_marketprice'     =>$good_data['marketprice'],
                
            );
            if($type == 'new'){
                $good_new_data['goods_promotion_price'] = $common_data['goods_price'];
                $good_new_data['goods_serial']          = $goods_class['gc_code']."W".$this->getSpecificationsNum(array('gc_id_1'=>$common_data['gc_id_1'],'gc_id_2'=>$common_data['gc_id_2'],'gc_id_3'=>$common_data['gc_id_3']));
                $good_new_data['materiel_code']         = $goods_class['gc_code']."W".$this->getSpecificationsNum(array('gc_id_1'=>$common_data['gc_id_1'],'gc_id_2'=>$common_data['gc_id_2'],'gc_id_3'=>$common_data['gc_id_3']));
                $good_new_data['goods_image']           = $common_data['goods_image'];
                $good_new_data['goods_addtime']         = TIMESTAMP;
                $good_new_data['color_id']              = 0;
            }else{
                if(!empty($common_data['goods_image'])){
                    $good_new_data['goods_image'] = $common_data['goods_image'];
                }             
                $good_new_data['color_id']          = intval($good_data['color']);              
                $good_new_data['have_gift']         = $common_data['is_virtual'] == 1 ? 0:"";
            }
            return $good_new_data;
        }
    }
    
    
    /**
     * 物料数据更新
     **/
    private function upProductData($good_data,$common_data,$goods_info){
        if(!empty($good_data) && !empty($common_data)){
            //获取当前分类下的物料编号
            $model = Model();
            $sp_values_product  = '';
            if(!empty($good_data['sp_value']) && is_array($good_data['sp_value'])){
                foreach($good_data['sp_value'] as $cp_values){
                    $sp_values_product .= $cp_values .' ';
                }
            }
            
            //更新物料编号表
            $product_data = array(
                'local_description' =>$good_data['goods_name'],
                'brand'             =>$common_data['brand_name'],
                'comment'           =>'11111',
                'product_spec'      =>$sp_values_product,
                'member_price'      =>$good_data['goods_third_price'],
                'contract_price'    =>$good_data['marketprice'] == 0 ? $common_data['goods_marketprice'] : $good_data['marketprice'],
                'vs_price'          =>$good_data['price'],
                'reference_price'   =>$good_data['g_costprice'],
            );
            $up = $model->table('product')->where('product_code="'.$goods_info['goods_serial'].'"')->update($product_data);
            return $up;
        }
    }
    
    
    /**
     * 配合商品数据处理整合数组
     * @Aletta 2017.06.09
     **/
    protected function handleGoodCommonData($data,$goods_class){
        if(!empty($data) && is_array($data)){
            // 序列化保存手机端商品描述数据
            if ($data['m_body'] != '') {
                $data['m_body'] = str_replace('&quot;', '"', $data['m_body']);
                $data['m_body'] = json_decode($data['m_body'], true);
                if (!empty($data['m_body'])) {
                    $data['m_body'] = serialize($data['m_body']);
                } else {
                    $data['m_body'] = '';
                }
            }
            $common_array = array(
                'goods_name'            => $data['g_name'],
                'goods_jingle'          => $data['g_jingle'],
                'gc_id'                 => intval($data['cate_id']),
                'gc_id_1'               => intval($goods_class['gc_id_1']),
                'gc_id_2'               => intval($goods_class['gc_id_2']),
                'gc_id_3'               => intval($goods_class['gc_id_3']),
                'gc_name'               => $data['cate_name'],
                'brand_id'              => $data['b_id'],
                'brand_name'            => $data['b_name'],
                'type_id'               => intval($data['type_id']),
                'goods_image'           => $data['image_path'],
                'goods_price'           => floatval($data['g_price']),
                'min_num'               => intval($data['min_num']),
                'max_num'               => intval($data['max_num']),
                'goods_marketprice'     => floatval($data['g_marketprice']),
                'goods_costprice'       => floatval($data['g_costprice']),
                'goods_discount'        => floatval($data['g_discount']),
                'goods_serial'          => $data['g_serial'],
                'goods_storage_alarm'   => intval($data['g_alarm']),
                'goods_attr'            => serialize($data['attr']),
                'goods_body'            => $data['g_body'],
                'goods_toc_price'       => floatval($data['goods_toc_price']),
                'goods_third_price'     => floatval($data['goods_third_price']),
                'sales_area'            => $data['city_center_id'],
                'mobile_body'           => $data['m_body'],
                'goods_commend'         => $data['g_commend'],
                'goods_state'           => ($this->store_info['store_state'] != 1) ? 0 : intval($data['g_state']),            // 店铺关闭时，商品下架
                'goods_selltime'        => strtotime($data['starttime']) + intval($data['starttime_H'])*3600 + intval($data['starttime_i'])*60,
                'goods_verify'          => (C('goods_verify') == 1) ? 10 : 1,
                'spec_name'             => is_array($data['spec']) ? serialize($data['sp_name']) : serialize(null),
                'spec_value'            => is_array($data['spec']) ? serialize($data['sp_val']) : serialize(null),
                'goods_vat'             => 1,
                'areaid_1'              => intval($data['province_id']),
                'areaid_2'              => intval($data['city_id']),
                'transport_id'          => ($data['freight'] == '0') ? '0' : intval($data['transport_id']), // 售卖区域
                'transport_title'       => $data['transport_title'],
                'goods_freight'         => floatval($data['g_freight']),
            );
            //查询店铺商品分类
            $goods_stcids_arr = array();
            if (!empty($data['sgcate_id'])){
                $sgcate_id_arr = array();
                foreach ($data['sgcate_id'] as $k=>$v){
                    $sgcate_id_arr[] = intval($v);
                }
                $sgcate_id_arr = array_unique($sgcate_id_arr);
                $store_goods_class = Model('store_goods_class')->getStoreGoodsClassList(array('store_id' => $_SESSION ['store_id'], 'stc_id' => array('in', $sgcate_id_arr), 'stc_state' => '1'));
                if (!empty($store_goods_class)){
                    foreach ($store_goods_class as $k=>$v){
                        if ($v['stc_id'] > 0){
                            $goods_stcids_arr[] = $v['stc_id'];
                        }
                        if ($v['stc_parent_id'] > 0){
                            $goods_stcids_arr[] = $v['stc_parent_id'];
                        }
                    }
                    $goods_stcids_arr = array_unique($goods_stcids_arr);
                    sort($goods_stcids_arr);
                }
            }
            if (empty($goods_stcids_arr)){
                $common_array['goods_stcids'] = '';
            } else {
                $common_array['goods_stcids'] = ','.implode(',',$goods_stcids_arr).',';// 首尾需要加,
            }
            $store_model = Model('store');
            $common_array['is_virtual']         = 0;//intval($_POST['is_gv']);
            $common_array['virtual_indate']     = $data['g_vindate'] != '' ? (strtotime($data['g_vindate']) + 24*60*60 -1) : 0;  // 当天的最后一秒结束
            $common_array['virtual_limit']      = intval($data['g_vlimit']) > 10 || intval($data['g_vlimit']) < 0 ? 10 : intval($data['g_vlimit']);
            $common_array['virtual_invalid_refund'] = intval($data['g_vinvalidrefund']);
            $common_array['is_fcode']           = 0;//intval($_POST['is_fc']);
            $common_array['is_appoint']         = intval($data['is_appoint']);     // 只有库存为零的商品可以预约
            $common_array['appoint_satedate']   = $common_array['is_appoint'] == 1 ? strtotime($data['g_saledate']) : '';   // 预约商品的销售时间
            $common_array['is_presell']         = 0;//$update_common['goods_state'] == 1 ? intval($_POST['is_presell']) : 0;     // 只有出售中的商品可以预售
            $common_array['presell_deliverdate']= $common_array['is_presell'] == 1? strtotime($data['g_deliverdate']) : ''; // 预售商品的发货时间
            $common_array['is_own_shop']        = in_array($_SESSION['store_id'], $store_model->getOwnShopIds()) ? 1 : 0;
            return $common_array;
        }
    }
    
    
    /**
     * 获取物料编号
     **/
    protected function getSpecificationsNum($common_array){
        $model = Model();
        //获取当前分类下的物料编号
        $goods_serial_id_where['gc_id_1'] = intval($common_array['gc_id_1']);
        $goods_serial_id_where['gc_id_2'] = intval($common_array['gc_id_2']);
        $goods_serial_id_where['gc_id_3'] = intval($common_array['gc_id_3']);
        $goods_serial_id = $model->table('product')->field('product_code')->where($goods_serial_id_where)->order('Length(product_id) desc,product_id DESC')->find();
          //判定当前物料编号是否属于全新规则12位数物料
        if(strlen($goods_serial_id['product_code']) > 10){
            $serial=  intval(substr($goods_serial_id['product_code'],-7));
        }else{
            $serial=  intval(substr($goods_serial_id['product_code'],-5));
        }

        //$serial=  intval(substr($goods_serial_id['product_code'],-7));
        $s_serial = $serial+1;
        switch (intval(strlen($s_serial))){
            case 1:
                $g_serial = "000000".$s_serial;
                break;
            case 2:
                $g_serial = "00000".$s_serial;
                break;
            case 3:
                $g_serial = "0000".$s_serial;
                break;
            case 4:
                $g_serial = "000".$s_serial;
                break;
            case 5:
                $g_serial = "00".$s_serial;
                break;
            case 6:
                $g_serial = "0".$s_serial;
                break;
            case 7:
                $g_serial = $s_serial;
                break;
        }
        return $g_serial;
    }
    
    
    /**
     * 重组外部物料信息数据（根据规格进行计算重组）
     **/
    protected function specificationsExternalMaterial($goods,$class_data,$common_data,$supply_code,$goods_class){
        if(!empty($goods) && is_array($goods) && !empty($class_data)){
            $sp_values_product  = '';
            if(!empty($class_data['sp_value']) && is_array($class_data['sp_value'])){
                foreach($class_data['sp_value'] as $cp_values){
                    $sp_values_product .= $cp_values .' ';
                }
            }
            $material = array(
                'product_id'                => $goods['goods_serial'],
                'product_code'              => $goods['goods_serial'],
                'local_description'         => $goods['goods_name'],
                'member_price'              => $class_data['goods_third_price'],
                'contract_price'            => $class_data['marketprice'],
                'vs_price'                  => $class_data['price'],
                'reference_price'           => $class_data['cosprice'],
                'product_spec'              => json_encode($class_data['sp_value']),
                'product_level'             => 0,
                'product_type'              => 0,
                'product_spec'              => $sp_values_product,
                'brand'                     => $common_data['brand_name'],
                'serialized_item_flag'      => 1,
                'supplier_id'               => $common_data['store_id'],
                'supplier_cd'               => $supply_code['supply_code'],
                'minimum_purchase_quantity' => $goods['min_num'],
                'gc_id'                     => intval($common_data['gc_id_3']),
                'gc_id_1'                   => intval($common_data['gc_id_1']),
                'gc_id_2'                   => intval($common_data['gc_id_2']),
                'gc_id_3'                   => intval($common_data['gc_id_3']),
                'update_author'             => $_SESSION['member_name'],
                'update_date'               => date('Y-m-d-H-i-s',time()),
                'create_author'             => $_SESSION['member_name'],
                'create_date'               => date('Y-m-d-H-i-s',time()),
            );
            $gc_class_name = explode(',',$goods_class['gc_tag_value']);
            $key_num_gc = count($gc_class_name);
            $material['gc_classname'] = $gc_class_name[$key_num_gc-1];
            return $material;
        }
    }
    
    
    /**
     * 获取商品相关规格数据信息
     **/
    public function get_good_sp_listOp(){
        $common_id = intval ( $_GET['commonid'] );
        if(!empty($common_id)){
            $model = Model();
            $common_data = $model->table('goods_common')->where("goods_commonid = '".$common_id."'")->find();
            if(!empty($common_data)){
                $sp_nam = unserialize($common_data['spec_name']);
                $sk_data = array();
                if(!empty(unserialize($common_data['spec_value'])) && is_array(unserialize($common_data['spec_value']))){
                    foreach (unserialize($common_data['spec_value']) as $key=>$v){
                        $sk_data[] = $sp_nam[$key];
                    }
                }
                $list = $model->table('goods')->field("goods_spec,goods_price,goods_marketprice,g_costprice,goods_third_price,goods_storage,goods_storage_alarm,min_num,max_num")->where("goods_commonid = '".$common_id."'")->select();
                Tpl::output('sk_data', $sk_data);
                Tpl::output('list', $list);
                Tpl::showpage('store_goods_list.sku', 'null_layout');
            }
        }
    }


    /**
     * 编辑图片
     */
    public function edit_imageOp() {
        $common_id = intval($_GET['commonid']);
        if ($common_id <= 0) {
            showMessage(L('wrong_argument'), urlShop('seller_center'), 'html', 'error');
        }
        $model_goods = Model('goods');
        $common_list = $model_goods->getGoodeCommonInfoByID($common_id, 'store_id,goods_lock,spec_value,is_virtual,is_fcode,is_presell');
        if ($common_list['store_id'] != $_SESSION['store_id'] || $common_list['goods_lock'] == 1) {
            showMessage(L('wrong_argument'), urlShop('seller_center'), 'html', 'error');
        }
        
        $spec_value = unserialize($common_list['spec_value']);
        Tpl::output('value', $spec_value['1']);

        $image_list = $model_goods->getGoodsImageList(array('goods_commonid' => $common_id));
        $image_list = array_under_reset($image_list, 'color_id', 2);

        $img_array = $model_goods->getGoodsList(array('goods_commonid' => $common_id), 'color_id,goods_image', 'color_id');
        // 整理，更具id查询颜色名称
        if (!empty($img_array)) {
            foreach ($img_array as $val) {
                if (isset($image_list[$val['color_id']])) {
                    $image_array[$val['color_id']] = $image_list[$val['color_id']];
                } else {
                    $image_array[$val['color_id']][0]['goods_image'] = $val['goods_image'];
                    $image_array[$val['color_id']][0]['is_default'] = 1;
                }
                $colorid_array[] = $val['color_id'];
            }
        }
        Tpl::output('img', $image_array);


        $model_spec = Model('spec');
        $value_array = $model_spec->getSpecValueList(array('sp_value_id' => array('in', $colorid_array), 'store_id' => $_SESSION['store_id']), 'sp_value_id,sp_value_name');
        if (empty($value_array)) {
            $value_array[] = array('sp_value_id' => '0', 'sp_value_name' => '无颜色');
        }
        Tpl::output('value_array', $value_array);

        Tpl::output('commonid', $common_id);

        $menu_promotion = array(
                'lock' => $common_list['goods_lock'] == 1 ? true : false,
                'gift' => $model_goods->checkGoodsIfAllowGift($common_list),
                'combo' => $model_goods->checkGoodsIfAllowCombo($common_list)
        );
        $this->profile_menu('edit_detail', 'edit_image', $menu_promotion);
        Tpl::output('edit_goods_sign', true);
        Tpl::showpage('store_goods_add.step3');
    }

    /**
     * 保存商品图片
     */
    public function edit_save_imageOp() {
        if (chksubmit()) {
            $common_id = intval($_POST['commonid']);
            if ($common_id <= 0 || empty($_POST['img'])) {
                showDialog(L('wrong_argument'), urlShop('store_goods_online', 'index'));
            }
            $model_goods = Model('goods');
            // 删除原有图片信息
            $model_goods->delGoodsImages(array('goods_commonid' => $common_id, 'store_id' => $_SESSION['store_id']));
            // 保存
            $insert_array = array();
            foreach ($_POST['img'] as $key => $value) {
                foreach ($value as $v) {
                    if ($v['name'] == '') {
                        continue;
                    }
                    //$k = 0;
                    // 商品默认主图
                    $update_array = array();        // 更新商品主图
                    $update_where = array();
                    $update_array['goods_image']    = $v['name'];
                    $update_where['goods_commonid'] = $common_id;
                    $update_where['store_id']       = $_SESSION['store_id'];
                    $update_where['color_id']       = $key;
                    if ($k == 0 || $v['default'] == 1) {
                        $k++;
                        $update_array['goods_image']    = $v['name'];
                        $update_where['goods_commonid'] = $common_id;
                        $update_where['store_id']       = $_SESSION['store_id'];
                        $update_where['color_id']       = $key;
                        // 更新商品主图
                        $model_goods->editGoods($update_array, $update_where);
                    }
                    $tmp_insert = array();
                    $tmp_insert['goods_commonid']   = $common_id;
                    $tmp_insert['store_id']         = $_SESSION['store_id'];
                    $tmp_insert['color_id']         = $key;
                    $tmp_insert['goods_image']      = $v['name'];
                    $tmp_insert['goods_image_sort'] = ($v['default'] == 1) ? 0 : $v['sort'];
                    $tmp_insert['is_default']       = $v['default'];
                    $insert_array[] = $tmp_insert;
                }
            }
            $rs = $model_goods->addGoodsImagesAll($insert_array);
            if ($rs) {
            // 添加操作日志
            $this->recordSellerLog('编辑商品，平台货号：'.$common_id);
                showDialog(L('nc_common_op_succ'), $_POST['ref_url'], 'succ');
            } else {
                showDialog(L('nc_common_save_fail'), urlShop('store_goods_online', 'index'));
            }
        }
    }

    /**
     * 编辑分类
     */
    public function edit_classOp() {
        // 实例化商品分类模型
        $model_goodsclass = Model('goods_class');
        // 商品分类
        $goods_class = $model_goodsclass->getGoodsClass($_SESSION['store_id']);

        // 常用商品分类
        $model_staple = Model('goods_class_staple');
        $param_array = array();
        $param_array['member_id'] = $_SESSION['member_id'];
        $staple_array = $model_staple->getStapleList($param_array);

        Tpl::output('staple_array', $staple_array);
        Tpl::output('goods_class', $goods_class);

        Tpl::output('commonid', $_GET['commonid']);
        $this->profile_menu('edit_class', 'edit_class');
        Tpl::output('edit_goods_sign', true);
        Tpl::showpage('store_goods_add.step1');
    }

    /**
     * 删除商品
     */
    public function drop_goodsOp() {
        $common_id = $this->checkRequestCommonId($_GET['commonid']);
        $commonid_array = explode(',', $common_id);
        $model_goods = Model('goods');
        $where = array();
        $where['goods_commonid'] = array('in', $commonid_array);
        $where['store_id'] = $_SESSION['store_id'];
        $return = $model_goods->delGoodsNoLock($where);
        if ($return) {
            // 添加操作日志
            $this->recordSellerLog('删除商品，平台货号：'.$common_id);
            showDialog(L('store_goods_index_goods_del_success'), 'reload', 'succ');
        } else {
            showDialog(L('store_goods_index_goods_del_fail'), '', 'error');
        }
    }

    /**
     * 商品下架
     */
    public function goods_unshowOp() {
        $common_id = $this->checkRequestCommonId($_GET['commonid']);
        $commonid_array = explode(',', $common_id);
        $model_goods = Model('goods');
        $where = array();
        $where['goods_commonid'] = array('in', $commonid_array);
        $where['store_id'] = $_SESSION['store_id'];
        $return = Model('goods')->editProducesOffline($where);
        if ($return) {
            // 更新优惠套餐状态关闭
            $goods_list = $model_goods->getGoodsList($where, 'goods_id');
            if (!empty($goods_list)) {
                $goodsid_array = array();
                foreach ($goods_list as $val) {
                    $goodsid_array[] = $val['goods_id'];
                }
                Model('p_bundling')->editBundlingCloseByGoodsIds(array('goods_id' => array('in', $goodsid_array)));
            }
            // 添加操作日志
            $this->recordSellerLog('商品下架，平台货号：'.$common_id);
            showDialog('商品已经下架，等待管理员审核', getReferer() ? getReferer() : 'index.php?act=store_goods_online&op=goods_list', 'succ', '', 2);
        } else {
            showDialog(L('store_goods_index_goods_unshow_fail'), '', 'error');
        }
    }

    /**
     * 设置广告词
     */
    public function edit_jingleOp() {
        if (chksubmit()) {
            $common_id = $this->checkRequestCommonId($_POST['commonid']);
            $commonid_array = explode(',', $common_id);
            $where = array('goods_commonid' => array('in', $commonid_array), 'store_id' => $_SESSION['store_id']);
            $update = array('goods_jingle' => trim($_POST['g_jingle']));
            $return = Model('goods')->editProducesNoLock($where, $update);
            if ($return) {
                // 添加操作日志
                $this->recordSellerLog('设置广告词，平台货号：'.$common_id);
                showDialog(L('nc_common_op_succ'), 'reload', 'succ');
            } else {
                showDialog(L('nc_common_op_fail'), 'reload');
            }
        }
        $common_id = $this->checkRequestCommonId($_GET['commonid']);

        Tpl::showpage('store_goods_list.edit_jingle', 'null_layout');
    }

    /**
     * 设置关联版式
     */
    public function edit_plateOp() {
        if (chksubmit()) {
            $common_id = $this->checkRequestCommonId($_POST['commonid']);
            $commonid_array = explode(',', $common_id);
            $where = array('goods_commonid' => array('in', $commonid_array), 'store_id' => $_SESSION['store_id']);
            $update = array();
            $update['plateid_top']        = intval($_POST['plate_top']) > 0 ? intval($_POST['plate_top']) : '';
            $update['plateid_bottom']     = intval($_POST['plate_bottom']) > 0 ? intval($_POST['plate_bottom']) : '';
            $return = Model('goods')->editGoodsCommon($update, $where);
            if ($return) {
                // 添加操作日志
                $this->recordSellerLog('设置关联版式，平台货号：'.$common_id);
                showDialog(L('nc_common_op_succ'), 'reload', 'succ');
            } else {
                showDialog(L('nc_common_op_fail'), 'reload');
            }
        }
        $common_id = $this->checkRequestCommonId($_GET['commonid']);

        // 关联版式
        $plate_list = Model('store_plate')->getStorePlateList(array('store_id' => $_SESSION['store_id']), 'plate_id,plate_name,plate_position');
        $plate_list = array_under_reset($plate_list, 'plate_position', 2);
        Tpl::output('plate_list', $plate_list);

        Tpl::showpage('store_goods_list.edit_plate', 'null_layout');
    }

    /**
     * 添加赠品
     */
    public function add_giftOp() {
        $common_id = intval($_GET['commonid']);
        if ($common_id <= 0) {
            showMessage(L('wrong_argument'), urlShop('seller_center'), 'html', 'error');
        }
        $model_goods = Model('goods');
        $goodscommon_info = $model_goods->getGoodeCommonInfoByID($common_id, 'store_id,goods_lock');
        if (empty($goodscommon_info) || $goodscommon_info['store_id'] != $_SESSION['store_id']) {
            showMessage(L('wrong_argument'), urlShop('seller_center'), 'html', 'error');
        }

        // 商品列表
        $goods_array = $model_goods->getGoodsListForPromotion(array('goods_commonid' => $common_id), '*', 0, 'gift');
        Tpl::output('goods_array', $goods_array);

        // 赠品列表
        $gift_list = Model('goods_gift')->getGoodsGiftList(array('goods_commonid' => $common_id));
        $gift_array = array();
        if (!empty($gift_list)) {
            foreach ($gift_list as $val) {
                $gift_array[$val['goods_id']][] = $val;
            }
        }
        Tpl::output('gift_array', $gift_array);
        $menu_promotion = array(
                'lock' => $goodscommon_info['goods_lock'] == 1 ? true : false,
                'gift' => $model_goods->checkGoodsIfAllowGift($goods_array[0]),
                'combo' => $model_goods->checkGoodsIfAllowCombo($goods_array[0])
        );
        $this->profile_menu('edit_detail', 'add_gift', $menu_promotion);
        Tpl::showpage('store_goods_edit.add_gift');
    }

    /**
     * 保存赠品
     */
    public function save_giftOp() {
        if (!chksubmit()) {
            showDialog(L('wrong_argument'));
        }
        $data = $_POST['gift'];
        $commonid = intval($_POST['commonid']);
        if ($commonid <= 0) {
            showDialog(L('wrong_argument'));
        }

        $model_goods = Model('goods');
        $model_gift = Model('goods_gift');

        // 验证商品是否存在
        $goods_list = $model_goods->getGoodsListForPromotion(array('goods_commonid' => $commonid, 'store_id' => $_SESSION['store_id']), 'goods_id', 0, 'gift');
        if (empty($goods_list)) {
            showDialog(L('wrong_argument'));
        }
        // 删除该商品原有赠品
        $model_gift->delGoodsGift(array('goods_commonid' => $commonid));
        // 重置商品礼品标记
        $model_goods->editGoods(array('have_gift' => 0), array('goods_commonid' => $commonid));
        // 商品id
        $goodsid_array = array();
        foreach ($goods_list as $val) {
            $goodsid_array[] = $val['goods_id'];
        }

        $insert = array();
        $update_goodsid = array();
        foreach ($data as $key => $val) {

            $owner_gid = intval($key);  // 主商品id
            // 验证主商品是否为本店铺商品,如果不是本店商品继续下一个循环
            if (!in_array($owner_gid, $goodsid_array)) {
                continue;
            }
            $update_goodsid[] = $owner_gid;
            foreach ($val as $k => $v) {
                $gift_gid = intval($k); // 礼品id
                // 验证赠品是否为本店铺商品，如果不是本店商品继续下一个循环
                $gift_info = $model_goods->getGoodsInfoByID($gift_gid, 'goods_name,store_id,goods_image,is_virtual,is_fcode,is_presell');
                $is_general = $model_goods->checkIsGeneral($gift_info);     // 验证是否为普通商品
                if ($gift_info['store_id'] != $_SESSION['store_id'] || $is_general == false) {
                    continue;
                }

                $array = array();
                $array['goods_id'] = $owner_gid;
                $array['goods_commonid'] = $commonid;
                $array['gift_goodsid'] = $gift_gid;
                $array['gift_goodsname'] = $gift_info['goods_name'];
                $array['gift_goodsimage'] = $gift_info['goods_image'];
                $array['gift_amount'] = intval($v);
                $insert[] = $array;
            }
        }
        // 插入数据
        if (!empty($insert)) $model_gift->addGoodsGiftAll($insert);
        // 更新商品赠品标记
        if (!empty($update_goodsid)) $model_goods->editGoodsById(array('have_gift' => 1), $update_goodsid);
        showDialog(L('nc_common_save_succ'), $_POST['ref_url'], 'succ');
    }

    /**
     * 推荐搭配
     */
    public function add_comboOp() {
        $common_id = intval($_GET['commonid']);
        if ($common_id <= 0) {
            showMessage(L('wrong_argument'), urlShop('seller_center'), 'html', 'error');
        }
        $model_goods = Model('goods');
        $goodscommon_info = $model_goods->getGoodeCommonInfoByID($common_id, 'store_id,goods_lock');
        if (empty($goodscommon_info) || $goodscommon_info['store_id'] != $_SESSION['store_id']) {
            showMessage(L('wrong_argument'), urlShop('seller_center'), 'html', 'error');
        }

        $goods_array = $model_goods->getGoodsListForPromotion(array('goods_commonid' => $common_id), '*', 0, 'combo');
        Tpl::output('goods_array', $goods_array);

        // 推荐组合商品列表
        $combo_list = Model('goods_combo')->getGoodsComboList(array('goods_commonid' => $common_id));
        $combo_goodsid_array = array();
        if (!empty($combo_list)) {
            foreach ($combo_list as $val) {
                $combo_goodsid_array[] = $val['combo_goodsid'];
            }
        }

        $combo_goods_array = $model_goods->getGeneralGoodsList(array('goods_id' => array('in', $combo_goodsid_array)), 'goods_id,goods_name,goods_image,goods_price');
        $combo_goods_list = array();
        if (!empty($combo_goods_array)) {
            foreach ($combo_goods_array as $val) {
                $combo_goods_list[$val['goods_id']] = $val;
            }
        }

        $combo_array = array();
        foreach ($combo_list as $val) {
            $combo_array[$val['goods_id']][] = $combo_goods_list[$val['combo_goodsid']];
        }
        Tpl::output('combo_array', $combo_array);

        $menu_promotion = array(
                'lock' => $goodscommon_info['goods_lock'] == 1 ? true : false,
                'gift' => $model_goods->checkGoodsIfAllowGift($goods_array[0]),
                'combo' => $model_goods->checkGoodsIfAllowCombo($goods_array[0])
        );
        $this->profile_menu('edit_detail', 'add_combo', $menu_promotion);
        Tpl::showpage('store_goods_edit.add_combo');
    }

    /**
     * 保存赠品
     */
    public function save_comboOp() {
        if (!chksubmit()) {
            showDialog(L('wrong_argument'));
        }
        $data = $_POST['combo'];
        $commonid = intval($_POST['commonid']);
        if ($commonid <= 0) {
            showDialog(L('wrong_argument'));
        }

        $model_goods = Model('goods');
        $model_combo = Model('goods_combo');

        // 验证商品是否存在
        $goods_list = $model_goods->getGoodsListForPromotion(array('goods_commonid' => $commonid, 'store_id' => $_SESSION['store_id']), 'goods_id', 0, 'combo');
        if (empty($goods_list)) {
            showDialog(L('wrong_argument'));
        }
        // 删除该商品原有赠品
        $model_combo->delGoodsCombo(array('goods_commonid' => $commonid));
        // 商品id
        $goodsid_array = array();
        foreach ($goods_list as $val) {
            $goodsid_array[] = $val['goods_id'];
        }

        $insert = array();
        if (!empty($data)) {
            foreach ($data as $key => $val) {
    
                $owner_gid = intval($key);  // 主商品id
                // 验证主商品是否为本店铺商品,如果不是本店商品继续下一个循环
                if (!in_array($owner_gid, $goodsid_array)) {
                    continue;
                }
                $val = array_unique($val);
                foreach ($val as $v) {
                    $combo_gid = intval($v); // 礼品id
                    // 验证推荐组合商品是否为本店铺商品，如果不是本店商品继续下一个循环
                    $combo_info = $model_goods->getGoodsInfoByID($combo_gid, 'store_id,is_virtual,is_fcode,is_presell');
                    $is_general = $model_goods->checkIsGeneral($combo_info);     // 验证是否为普通商品
                    if ($combo_info['store_id'] != $_SESSION['store_id'] || $is_general == false || $owner_gid ==$combo_gid) {
                        continue;
                    }
    
                    $array = array();
                    $array['goods_id'] = $owner_gid;
                    $array['goods_commonid'] = $commonid;
                    $array['combo_goodsid'] = $combo_gid;
                    $insert[] = $array;
                }
            }
            // 插入数据
            $model_combo->addGoodsComboAll($insert);
        }
        showDialog(L('nc_common_save_succ'), $_POST['ref_url'], 'succ');
    }

    /**
     * 搜索商品（添加赠品/推荐搭配)
     */
    public function search_goodsOp() {
        $where = array();
        $where['store_id'] = $_SESSION['store_id'];
        if ($_POST['name']) {
            $where['goods_name'] = array('like', '%'. $_POST['name'] .'%');
        }
        $model_goods = Model('goods');
        $goods_list = $model_goods->getGeneralGoodsList($where, '*', 5);
        Tpl::output('show_page', $model_goods->showpage(2));
        Tpl::output('goods_list', $goods_list);
        Tpl::showpage('store_goods_edit.search_goods', 'null_layout');
    }
    
    /**
     * 下载F码
     */
    public function download_f_code_excelOp() {
        $common_id = $_GET['commonid'];
        if ($common_id <= 0) {
            showMessage(L('wrong_argument'), '', '', 'error');
        }
        $common_info = Model('goods')->getGoodeCommonInfoByID($common_id);
        if (empty($common_info) || $common_info['store_id'] != $_SESSION['store_id']) {
            showMessage(L('wrong_argument'), '', '', 'error');
        }
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'号码');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'使用状态');
        $data = Model('goods_fcode')->getGoodsFCodeList(array('goods_commonid' => $common_id));
        foreach ($data as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['fc_code']);
            $tmp[] = array('data'=>$v['fc_state'] ? '已使用' : '未使用');
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset($common_info['goods_name'],CHARSET));
        $excel_obj->generateXML($excel_obj->charset($common_info['goods_name'],CHARSET).'-'.date('Y-m-d-H',time()));
    }

    /**
     * 验证commonid
     */
    private function checkRequestCommonId($common_ids) {
        if (!preg_match('/^[\d,]+$/i', $common_ids)) {
            showDialog(L('para_error'), '', 'error');
        }
        return $common_ids;
    }

    /**
     * ajax获取商品列表
     */
    public function get_goods_list_ajaxOp() {
        $common_id = $_GET['commonid'];
        if ($common_id <= 0) {
            echo 'false';exit();
        }
        $model_goods = Model('goods');
        $goodscommon_list = $model_goods->getGoodeCommonInfoByID($common_id, 'spec_name,store_id');
        if (empty($goodscommon_list) || $goodscommon_list['store_id'] != $_SESSION['store_id']) {
            echo 'false';exit();
        }
        $goods_list = $model_goods->getGoodsList(array('store_id' => $_SESSION['store_id'], 'goods_commonid' => $common_id), 'goods_id,goods_spec,store_id,goods_price,goods_serial,goods_storage_alarm,goods_storage,goods_image');
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
            $goods_list[$key]['alarm'] = ($val['goods_storage_alarm'] != 0 && $val['goods_storage'] <= $val['goods_storage_alarm']) ? 'style="color:red;"' : '';
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
     * 用户中心右边，小导航
     *
     * @param string $menu_type 导航类型
     * @param string $menu_key 当前导航的menu_key
     * @param boolean $allow_promotion
     * @return
     */
    private function profile_menu($menu_type,$menu_key, $allow_promotion = array()) {
        $menu_array = array();
        switch ($menu_type) {
            case 'goods_list':
                $menu_array = array(
                   array('menu_key' => 'goods_list',    'menu_name' => '出售中的商品', 'menu_url' => urlShop('store_goods_online', 'index'))
                );
                break;
            case 'edit_detail':
                if ($allow_promotion['lock'] === false) {
                    $menu_array = array(
                        array('menu_key' => 'edit_detail',  'menu_name' => '编辑商品', 'menu_url' => urlShop('store_goods_online', 'edit_goods', array('commonid' => $_GET['commonid'], 'ref_url' => $_GET['ref_url']))),
                        array('menu_key' => 'edit_image',   'menu_name' => '编辑图片', 'menu_url' => urlShop('store_goods_online', 'edit_image', array('commonid' => $_GET['commonid'], 'ref_url' => ($_GET['ref_url'] ? $_GET['ref_url'] : getReferer())))),
                    );
                }
                if ($allow_promotion['gift']) {
                    $menu_array[] = array('menu_key' => 'add_gift', 'menu_name' => '赠送赠品', 'menu_url' => urlShop('store_goods_online', 'add_gift', array('commonid' => $_GET['commonid'], 'ref_url' => ($_GET['ref_url'] ? $_GET['ref_url'] : getReferer()))));
                }
                if ($allow_promotion['combo']) {
                    $menu_array[] = array('menu_key' => 'add_combo', 'menu_name' => '推荐组合', 'menu_url' => urlShop('store_goods_online', 'add_combo', array('commonid' => $_GET['commonid'], 'ref_url' => ($_GET['ref_url'] ? $_GET['ref_url'] : getReferer()))));
                }
                break;
            case 'edit_class':
                $menu_array = array(
                    array('menu_key' => 'edit_class',   'menu_name' => '选择分类', 'menu_url' => urlShop('store_goods_online', 'edit_class', array('commonid' => $_GET['commonid'], 'ref_url' => $_GET['ref_url']))),
                    array('menu_key' => 'edit_detail',  'menu_name' => '编辑商品', 'menu_url' => urlShop('store_goods_online', 'edit_goods', array('commonid' => $_GET['commonid'], 'ref_url' => $_GET['ref_url']))),
                    array('menu_key' => 'edit_image',   'menu_name' => '编辑图片', 'menu_url' => urlShop('store_goods_online', 'edit_image', array('commonid' => $_GET['commonid'], 'ref_url' => ($_GET['ref_url'] ? $_GET['ref_url'] : getReferer())))),
                );
                break;
        }
        Tpl::output ( 'member_menu', $menu_array );
        Tpl::output ( 'menu_key', $menu_key );
    }
	//商城系统 批量生成二维码
	public function maker_qrcodeOp()
	{
	header("Content-Type: text/html; charset=utf-8");
		echo '正在生成，请耐心等待...';
		echo '<br/>';
		$store_id=$_SESSION['store_id'];
        require_once(BASE_RESOURCE_PATH.DS.'phpqrcode'.DS.'index.php');
        $PhpQRCode = new PhpQRCode();
        $PhpQRCode->set('pngTempDir',BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$_SESSION['store_id'].DS);
		//print_r($PhpQRCode);
		$model_goods = Model('goods');
		$where=array();
	    $where['store_id'] = $store_id;
		//$count=$model_goods->getGoodsCount($where);
		$lst=$model_goods->getGoodsList($where,'goods_id');
		if(empty($lst))
		{
			echo '未找到商品信息';
			retrun;
		}
		foreach($lst as $k=>$v)
		{
			$goods_id=$v['goods_id'];
			$qrcode_url=WAP_SITE_URL . '/tmpl/product_detail.html?goods_id='.$goods_id;
			$PhpQRCode->set('date',$qrcode_url);
			$PhpQRCode->set('pngTempName', $goods_id . '.png');
			$PhpQRCode->init();
			echo '生成成功'.$qrcode_url;
			echo '<br/>';
		}
		
		//生成店铺二维码
		$qrcode_url=WAP_SITE_URL . '/tmpl/store.html?store_id='.$store_id;
		$PhpQRCode->set('date',$qrcode_url);
		$PhpQRCode->set('pngTempName', $store_id . '_store.png');
		$PhpQRCode->init();
		echo '生成店铺二维码成功'.$qrcode_url;
		echo '<br/>';
		echo '<br/><b>全部生成完成</b>';
		
		
		
		
		
	}
	
	
    protected function getArrSet($arrs,$_current_index=-1){
        static $_total_arr;         //总数组
        static $_total_arr_index;   //总数组下标计数
        static $_total_count;       //输入的数组长度
        static $_temp_arr;          //临时拼凑数组
        //进入输入数组的第一层，清空静态数组，并初始化输入数组长度
        if($_current_index<0){
            $_total_arr=array();
            $_total_arr_index=0;
            $_temp_arr=array();
            $_total_count=count($arrs)-1;
            $this->getArrSet($arrs,0);
        }else{
            //循环第$_current_index层数组
            $str = '';
            foreach($arrs[$_current_index] as $key=>$v){
                //如果当前的循环的数组少于输入数组长度
                if($_current_index<$_total_count){
                    //将当前数组循环出的值放入临时数组
                    $_temp_arr[$_current_index]=$v;
                    //继续循环下一个数组
                    $this->getArrSet($arrs,$_current_index+1);
                }else if($_current_index==$_total_count){
                    //如果当前的循环的数组等于输入数组长度(这个数组就是最后的数组)
                    //将当前数组循环出的值放入临时数组
                    $_temp_arr[$_current_index]=$v;
                    //将临时数组加入总数组
                    $_total_arr[$_total_arr_index]=$_temp_arr;
                    //总数组下标计数+1
                    $_total_arr_index++;
                }
            }
        }
        return $_total_arr;
    }
   
    
    protected function getArrSetKey($arrs,$old_arr){
        $old_data = array();
        if(!empty($old_arr) && is_array($old_arr)){
            foreach ($old_arr as $old_v){
                foreach ($old_v as $old_key=>$old_vl){
                    $old_data[$old_key] = $old_vl;
                }
            }
        }
        if(!empty($arrs) && is_array($arrs)){
            foreach ($arrs as $v){
                $str_arr = array();
                foreach ($v as $vl){
                    foreach ($old_data as $k=>$old_val){
                        if($vl == $old_val){
                            $str_arr[] = $k;
                        }
                    }
                }
                $new_data[implode("_",$str_arr)] = $v;
            }
        }
        return $new_data;
    }

    
    
/**
     * 获取商品相关规格库存
     **/
    public function get_goods_kc_eidtOp(){
        $common_id = intval ( $_GET['commonid'] );
        if(!empty($common_id)){
            $model = Model();
            $common_data = $model->table('goods_common')->where("goods_commonid = '".$common_id."'")->find();
            if(!empty($common_data)){
                $sp_nam = unserialize($common_data['spec_name']);
                $sk_data = array();
                if(!empty(unserialize($common_data['spec_value'])) && is_array(unserialize($common_data['spec_value']))){
                    foreach (unserialize($common_data['spec_value']) as $key=>$v){
                        $sk_data[] = $sp_nam[$key];
                    }
                }
                $list = $model->table('goods')->field("goods_id,goods_spec,goods_price,goods_marketprice,g_costprice,goods_third_price,goods_storage,goods_storage_alarm")->where("goods_commonid = '".$common_id."'")->select();
                Tpl::output('sk_data', $sk_data);
                Tpl::output('list', $list);
                Tpl::showpage('store_goods_list.kc', 'null_layout');
            }
        }
    }
/**
     * 编辑库存
     **/
    public function goods_kc_eidtOp(){        
        $goods_idlist=$_POST['goods_id'];
        $goods_storage_list=$_POST['goods_storage'];
        $goods_storage_alarm_list=$_POST['goods_storage_alarm'];
        $model = Model();
        if(!empty($goods_idlist) && is_array($goods_idlist)){
        foreach ($goods_idlist as $value) {
            $data['goods_storage'] = empty($goods_storage_list[$value]) ? 0 : $goods_storage_list[$value];
            $data['goods_storage_alarm'] = empty($goods_storage_alarm_list[$value]) ? 0 : $goods_storage_alarm_list[$value];
            $list = $model->table('goods')->where(array('goods_id'=>$value))->update($data);
        }
        }
        $this->indexOp();

    }
}
