<?php
/**
 * 城市中心维护
 *
 *
 *
 *
 */


class codemanagesControl extends SystemControl{
    public function __construct() {
        parent::__construct ();
    }

    /**
     * 城市中心维护 列表
     */
    public function indexOp() {
        $model = Model();
        if(!empty($_GET['product_type'])){
            $product_type = htmlspecialchars($_GET['product_type']);
            $where['product_type'] = 0;
            Tpl::output('product_type',$product_type);
        }
        if($_GET['product_level_w'] && empty($_GET['product_level_n'])){
            $product_level = htmlspecialchars($_GET['product_level_w']);
            $where['product_level'] = 0;
            Tpl::output('product_level_w',$product_level);
        }
        if($_GET['product_level_n'] && empty($_GET['product_level_w'])){
             $product_level = htmlspecialchars($_GET['product_level_n']);
            $where['product_level'] = 1;
            Tpl::output('product_level_n',$product_level);
        }
        if($_GET['product_level_w'] && $_GET['product_level_n']){
            $product_level_w = htmlspecialchars($_GET['product_level_w']);
            $product_level_n = htmlspecialchars($_GET['product_level_n']);
            Tpl::output('product_level_n',$product_level_n);
            Tpl::output('product_level_w',$product_level_w);
        }
        
        if($_GET['brand_name']){
            $brand = htmlspecialchars($_GET['brand_name']);
            $where['brand'] = array('like','%'.$brand.'%');
            Tpl::output('brand',$brand);
        }
        if($_GET['product_spec_name']){
            $product_spec = htmlspecialchars($_GET['product_spec_name']);
            $where['product_spec'] = array('like','%'.$product_spec.'%');
            Tpl::output('product_spec',$product_spec);
        }
        if($_GET['product_id']){
            $product_id = htmlspecialchars($_GET['product_id']);
            $where['product_id'] = array('like','%'.$product_id.'%');
            Tpl::output('product_id',$product_id);
        }
        if($_GET['product_code']){
            $product_code = htmlspecialchars($_GET['product_code']);
            $where['product_code'] = array('like','%'.$product_code.'%');
            Tpl::output('product_code',$product_code);
        }
       if($_GET['local_description']){
            $local_description = htmlspecialchars($_GET['local_description']);
            $where['local_description'] = array('like','%'.$local_description.'%');
            Tpl::output('local_description',$local_description);
       }
       if($_GET['search_gc']){
           if($_GET['search_gc'][0]!=0){
                $where['gc_id_1'] = $_GET['search_gc'][0];
           }
           if($_GET['search_gc'][1]!=0){
                $where['gc_id_2'] = $_GET['search_gc'][1];
           }
           if($_GET['search_gc'][2]!=0){
                $where['gc_id_3'] = $_GET['search_gc'][2];
           }
       }
        //$where['product_type']="1";
        //获取城市中心列表页
        $code = $model->table('product')->where($where)->page(10)->select();
        for($i=0;$i<sizeof($code);$i++){
            $goods_name = $model->table("goods_class")->field("gc_name")->where(array("gc_id"=>$code[$i]["gc_id_1"]))->find();
            $code[$i]["gc_name1"]=$goods_name["gc_name"];
            $goods_name = $model->table("goods_class")->field("gc_name")->where(array("gc_id"=>$code[$i]["gc_id_2"]))->find();
            $code[$i]["gc_name2"]=$goods_name["gc_name"];
            $goods_name = $model->table("goods_class")->field("gc_name")->where(array("gc_id"=>$code[$i]["gc_id_3"]))->find();
            $code[$i]["gc_name3"]=$goods_name["gc_name"];
            
        }
       // $goods_name = $model->table("goods_class")->field("gc_name")->where(array("gc_id"=>$code[0]["gc_id_1"]))->find();
        /**
         * 处理商品分类
         */
        $choose_gcid = ($t = intval($_REQUEST['choose_gcid']))>0?$t:0;
        $gccache_arr = Model('goods_class')->getGoodsclassCache($choose_gcid,3);
	Tpl::output('gc_json',json_encode($gccache_arr['showclass']));
	Tpl::output('gc_choose_json',json_encode($gccache_arr['choose_gcid']));       
        Tpl::output('code',$code);
        Tpl::output('page',$model->showpage());
        Tpl::showpage('code.setting');
    }
    
    /**
     * 城市中心维护 编辑
     */
    public function addOp() {
        
        $model = Model();
        // 一级商品分类
        $gc_list = Model('goods_class')->getGoodsClassListByParentId(0);
        Tpl::output('gc_list', $gc_list);

        if (chksubmit()){
          try{
            $model->beginTransaction();
            $gc_id = $_POST['class_id'];//获取都第三级分类id
            
            $type  = $_POST['product_type'];// N W
            //首先判断是否有传入物料名称
            if($type=="W"&&$_POST['is_to_product_id']=="1"){
               $to_product_name = $_POST['to_product_name'];
               $to_product_id   = $_POST['to_product_id'];
               $return_array = $this->insertToProductId($to_product_name, $to_product_id, $gc_id,$_POST['brand_class']);
            }
            $product_id = $this->getclasscode($gc_id, $type);
            
            //首先插入数据库占用此条数据
            $data = array(
                "product_id"=>$product_id,
                "product_code"=>$product_id,
                "gc_id"=>$gc_id,
                'product_level'=>0
            );       
            $is_success = $model->table('product')->insert($data);
            //如果插入成功 则更新数据 否则需要重新来过 控制物料不出错
            if($is_success==true){
                 //更新数据
                $update_data = array();
                $update_data['local_description']              = $_POST['local_description'];
                $update_data['product_classification_id']      = $_POST['product_classification_id'];
                $update_data['unit_of_measure_inventory_id']   = $_POST['unit_of_measure_inventory_id'];
                $update_data['unit_of_measure_purchase_id']    = $_POST['unit_of_measure_purchase_id'];
                $update_data['minimum_sales_quantity']         = $_POST['minimum_sales_quantity'];
                $update_data['minimum_purchase_quantity']      = $_POST['minimum_purchase_quantity'];
                if($type=="W"){
                     $update_data['to_product_id']                  = $_POST['to_product_id']=="" ? ($return_array['to_product_id_num']=="" ? "": $return_array['to_product_id_num']) : $_POST['to_product_id']; 
                }
                $update_data['brand']                          = $_POST['brand'];
                $update_data['product_spec']                   = $_POST['product_spec'];
                $update_data['unit_scale']                     = $_POST['unit_scale'];
                $update_data['member_price']                   = $_POST['member_price'];
                $update_data['contract_price']                 = $_POST['contract_price'];
                $update_data['vs_price']                       = $_POST['vs_price'];
                $update_data['reference_price']                = $_POST['reference_price'];
                $update_data['deleted_flag']                   = $_POST['deleted_flag'];
                $update_data['supplier_cd']                    = "";
                $update_data['serialized_item_flag']           = $_POST['serialized_item_flag'];
                $update_data['product_type']                   = 1;
                if($type=="N"){
                    $update_data['product_level']                  = 1;
                }else{
                    $update_data['product_level']                  = 0;
                }
                
                $update_data['gc_id']                          = $_POST['class_id'];
                
                //查询商品分类是否有父级ID
                $gc_class = $model->table('goods_class')->where('gc_id='.$_POST['class_id'])->field('gc_parent_id')->find();
                //如果没有有上级ID 则是一级分类
            if($gc_class['gc_parent_id'] == 0){
                    //插入分类ID
                    $update_data['gc_id_1']                  = $_POST['class_id'];
                    $update_data['gc_id_2']                  = 0;
                    $update_data['gc_id_3']                  = 0;
                }else{
                    //有上级 查询这个上级ID
                    $gc_class_two = $model->table('goods_class')->where('gc_id='.$gc_class['gc_parent_id'])->field('gc_parent_id')->find();
                    
                    if($gc_class_two['gc_parent_id'] > 0){
                       //如果还有父级则是三级ID没有则是二级ID
                        $update_data['gc_id_1']                  = $gc_class_two['gc_parent_id'];
                        $update_data['gc_id_2']                  = $gc_class['gc_parent_id'];
                        $update_data['gc_id_3']                  = $_POST['class_id'];
                    }else{
                        //如果没有 就是二级分类
                        $update_data['gc_id_1']                  = $gc_class['gc_parent_id'];
                        $update_data['gc_id_2']                  = $_POST['class_id'];
                        $update_data['gc_id_3']                  = 0;
                    }
                    
                }
                
                $update_data['gc_classname']                     = $_POST['brand_class'];
                $update_data['create_date']                      =date('Y-m-d H:i:s',time());
                $update_data['create_author']                    =$this->getAdminInfo()['name'];
                $update_data['update_date']                      =date('Y-m-d H:i:s',time());
                $update_data['update_author']                    =$this->getAdminInfo()['name'];
                
                
                //判断是否有重名的参数
                $condition	= array();
                 if($type=="N") {
                      $condition['product_level'] = 1;
                }else{
                      $condition['product_level'] = 0;
                }
                if($update_data['gc_id_1']) {
                      $condition['gc_id_1'] = $update_data['gc_id_1'];
                }
                if($update_data['gc_id_2']) {
                      $condition['gc_id_2'] = $update_data['gc_id_2'];
                }
                if($update_data['gc_id_3']) {
                      $condition['gc_id_3'] = $update_data['gc_id_3'];
                }
                if($update_data['local_description']) {
                      $condition['local_description'] = $update_data['local_description'];
                }
                if($update_data['brand']) {
                      $condition['brand'] = $update_data['brand'];
                }
                if($update_data['product_spec']) {
                      $condition['product_spec'] = $update_data['product_spec'];
                }
                //判断物料名称是否重复如果重复不给予添加
                $result = $model->table('product')->where($condition)->field('product_id')->find();;
                if(!empty($result)){
                       $model->rollback();
                       showMessage('物料信息已经存在！'); 
                }
                
                
                $update = $model->table('product')->where(array("product_id"=>$product_id))->update($update_data);
                
                if ($update != false){
                        $model->commit();
                        $send_data[0] =  $product_id;
                        $this->transProductToYMA($send_data,1);
                        showMessage('新增物料编号成功');
                }else {
                        showMessage('新增物料编号失败');
                }
            }else{
                showMessage('新增物料编号失败');
            }   
          }  catch (Exception $ex){
              $model->rollback();
               showMessage('新增物料编号失败');
          } 
        }
        Tpl::showpage('code.add');
    }
    
    /**
     * 城市中心维护 删除
     */
    public function delOp() {
        $model = Model();
        $product_id = htmlspecialchars(base64_decode($_GET['id']));
        
        if($product_id){
            
            $model = Model();
            $code_del = $model->table('product')->where('product_id="'.$product_id.'"')->delete();
            if ($code_del != false){
                        $send_data[] = $product_id;
                        $this->transProductToYMA($send_data,0);
                        showMessage('删除物料编号成功');
                }else {
                        showMessage('删除物料编号失败');
                }
        }else{
            showMessage("参数错误！请联系管理员");
        }
    }
    
     /**
     * 城市中心维护 编辑
     */
    public function editOp() {
       
        $product_id = htmlspecialchars(base64_decode($_GET['id']));
        // 一级商品分类
        $gc_list = Model('goods_class')->getGoodsClassListByParentId(0);
        Tpl::output('gc_list', $gc_list);
        if($product_id){
            
            $model = Model();
            if (chksubmit()){
                
                //更新数据
                $update_array = array();
                $update_array['product_id']                     = $_POST['product_code'];
                $update_array['product_code']                   = $_POST['product_code'];
                $update_array['local_description']              = $_POST['local_description'];
                $update_array['product_classification_id']      = $_POST['product_classification_id'];
                $update_array['unit_of_measure_inventory_id']   = $_POST['unit_of_measure_inventory_id'];
                $update_array['unit_of_measure_purchase_id']    = $_POST['unit_of_measure_purchase_id'];
                $update_array['minimum_sales_quantity']         = $_POST['minimum_sales_quantity'];
                $update_array['minimum_purchase_quantity']      = $_POST['minimum_purchase_quantity'];
                $update_array['to_product_id']                  = $_POST['to_product_id'];
                $update_array['brand']                          = $_POST['brand'];
                $update_array['product_spec']                   = $_POST['product_spec'];
                $update_array['unit_scale']                     = $_POST['unit_scale'];
                $update_array['supplier_cd']                    = "";
                $update_array['member_price']                   = $_POST['member_price'];
                $update_array['contract_price']                 = $_POST['contract_price'];
                $update_array['vs_price']                       = $_POST['vs_price'];
                $update_array['reference_price']                = $_POST['reference_price'];
                $update_array['deleted_flag']                   = $_POST['deleted_flag'];
                $update_array['serialized_item_flag']           = $_POST['serialized_item_flag'];
                $update_array['product_type']                   = 1;
                $result = $model->table('product')->where('product_id="'.$product_id.'"')->update($update_array);
                
                if ($result === true){
                        $send_data[] =  $update_array['product_code'];
                        $this->transProductToYMA($send_data,1);
                        showMessage('修改物料编号成功');
                }else {
                        showMessage('修改物料编号失败');
                }
            }
            $field = 'product_id,product_code,local_description,product_classification_id,product_level,unit_of_measure_inventory_id,unit_of_measure_purchase_id,minimum_sales_quantity,minimum_purchase_quantity,to_product_id,brand,product_spec,unit_scale,member_price,contract_price,reference_price,vs_price,product_type,gc_classname,english_description,sales_description,deleted_flag,serialized_item_flag';
            $where_p['product_id'] = $product_id;
            $code = $model->table('product')->where($where_p)->field($field)->find();

            Tpl::output('code',$code);
        }else{
            showMessage("参数错误！请联系管理员");
        }
        
        Tpl::showpage('code.edit');
    }
    public function getclasscodeOp(){
        //这是获取product分类下面的自动拼接物料编号
        //$model = Model();
        $gc_id = htmlspecialchars($_POST['classid']);
        $type=$_POST['product_type'];

            if($type=="W"){
                $re_serial = $this->getclasscode($gc_id,$type);
            }else{
                $re_serial = $this->getclasscode($gc_id);
            }
        
        echo $re_serial;exit;
    }
    
    /**
     * 剥离获取分类code方法 
     * 返回物料编号
     * @param type $gc_id   三级分类id
     * @param type $type    内部(N)或者外部(W)  默认内部
     * @return type $re_serial  返回应该生成的物料编号
     */
    public function getclasscode($gc_id,$type="N"){
        if($type=="W"){
            $product_level="0";
        }else{
            $product_level="1";
        }
          $model = Model();
          $where = array(
              "gc_id"=>$gc_id,
              "product_level"=>$product_level,
          );
        $product_last = $model->table('product')->where($where)->field('product_code')->order('Length(product_id) desc,product_id DESC')->find();
        //判定当前物料编号是否属于全新规则12位数物料
        if(strlen($product_last['product_code']) > 10){
            $serial=  intval(substr($product_last['product_code'],-7));
        }else{
            $serial=  intval(substr($product_last['product_code'],-5));
        }
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
        //获取分类code
        $gc_code = $model->table('goods_class')->where('gc_id='.$gc_id)->field('gc_class_code')->find();
        $re_serial = $gc_code['gc_class_code'].$type.$g_serial;
        return $re_serial;
    }
    
    public function getToProductIdOp(){
       $model = Model();
        //查询内部物料编号前获取当前商品分类
        $goods_class = $model->table('goods')->where('goods_id='.$_GET['g_id'])->field('gc_id,gc_id_1,gc_id_2,gc_id_3')->find();

        $where['product_level'] = 1;
        $where['gc_id_1'] = $goods_class['gc_id_1'];
        $where['gc_id_2'] = $goods_class['gc_id_2'];
        $where['gc_id_3'] = $goods_class['gc_id_3'];
        //查询当前物料分类名称
        $gc_name = $model->table('product')->where('product_code="'.$_GET['id'].'"')->field('gc_classname')->find();
        
        $nbbh  = $model->table('product')->field('local_description,product_code')->where($where)->select();

        // 一级商品分类
        $gc_list = Model('goods_class')->getGoodsClassListByParentId(0);
        Tpl::output('gc_name',$gc_name['gc_classname']);
        Tpl::output('gc_list', $gc_list);
        Tpl::output('bh',$nbbh);
        Tpl::output('ids',$_GET['id']);
        Tpl::showpage('product.getproduct','null_layout');
    }
    /**
     * 新增外部物料时 若没有选则内部物料 则自动插入一条内部物料与之挂钩
     * @param type $to_product_name   内部物料名称  必填
     * @param type $to_product_id     内部物料id    
     * @param type $gc_id             三级分类id
     * @return type                    to_product_id_num  表示插入的物料编号   $is_success 插入成功或者失败 
     */
    public function insertToProductId($to_product_name,$to_product_id,$gc_id,$gc_name){

                //如果物料id为空 那么获取物料名称 对应插入一条内部物料编号
                $model = Model();
                if(empty($to_product_id)){
                    $to_product_id_num = $this->getclasscode($gc_id);
                    //获取分类三级id
                    $gc_class = $model->table('goods_class')->where('gc_id='.$gc_id)->field('gc_parent_id')->find();
                      if($gc_class['gc_parent_id'] == 0){
                          showMessage('新增物料编号失败');exit;
                      }  else {
                        $gc_class_two = $model->table('goods_class')->where('gc_id='.$gc_class['gc_parent_id'])->field('gc_parent_id')->find();
                      }
                    $insert_data = array(
                        "product_id" => $to_product_id_num,
                        "product_code"=>$to_product_id_num,
                        "local_description" =>$to_product_name,
                        "gc_id"=>$gc_id,
                        "gc_id_1" =>$gc_class_two['gc_parent_id'],
                        "gc_id_2" =>$gc_class['gc_parent_id'],
                        "gc_id_3" =>$gc_id,
                        "product_type"=>1,
                        "product_level"=>1,
                        "supplier_cd"=>"",
                        "gc_classname" =>$gc_name,
                        "create_date" =>date('Y-m-d H:i:s',time()),
                        "create_author"=>$this->getAdminInfo()['name'],
                    );
                    $is_success = $model->table("product")->insert($insert_data);
                    
                    if(!$is_success){
                         showMessage('新增物料编号失败');exit;
                    }
                    $send_data[0] =  $to_product_id_num;
                    $this->transProductToYMA($send_data,1);
                    $array = array(
                        "is_success"=>$is_success,
                        "to_product_id_num"=>$to_product_id_num
                    );
                    return $array;
                }
                $array = array(
                     "is_success"=>true,
                     "to_product_id_num"=>$to_product_id
                );
                return $array;
    }
    /**
     * ajax对输入的内部物料编号做存在性检验
     */
    public function checkToProductIdOp(){
            $to_product_id = htmlspecialchars($_GET['to_product_id']);
            $model = Model();
            $where = array(
                "product_id"=>$to_product_id,
                "product_level"=>"1"
            );
            $db = $model->table("product")->field("local_description")->where($where)->find();

            $return = array();
            if(!empty($db)){
                $return['code']="0";
                $return['local_description']=$db['local_description'];
                echo json_encode($return);exit;
            }
            $return['code']="-1";
            $return['local_description']="该内部物料编号不存在，填空自动生成或者选择一个以存在的内部物料编号";
            echo json_encode($return);
        
    }
    /**
     * 后台发布的商品可用查看物料明细，但不允许编辑
     */
    public function detailOp(){
        $product_id = htmlspecialchars(base64_decode($_GET['id']));
        if($product_id){
            $model = Model();
            $field = 'product_id'
                    . ',product_code'
                    . ',local_description'
                    . ',product_classification_id'
                    . ',product_level'
                    . ',unit_of_measure_inventory_id'
                    . ',unit_of_measure_purchase_id'
                    . ',minimum_sales_quantity'
                    . ',minimum_purchase_quantity'
                    . ',to_product_id'
                    . ',brand'
                    . ',product_spec'
                    . ',unit_scale'
                    . ',member_price'
                    . ',contract_price'
                    . ',reference_price'
                    . ',vs_price'
                    . ',product_type'
                    . ',gc_classname'
                    . ',english_description'
                    . ',sales_description'
                    . ',deleted_flag'
                    . ',serialized_item_flag';
            $where_p['product_id'] = $product_id;
            $code = $model->table('product')->where($where_p)->field($field)->find();

            Tpl::output('code',$code);
        }else{
            showMessage("参数错误！请联系管理员");
        }
        
        Tpl::showpage('code.detail');
    }

    public function detail_pushOp(){
        $product_id = htmlspecialchars(base64_decode($_GET['id']));
        if($product_id){
            $send_data[0] =  $product_id;
            $this->transProductToYMA($send_data,1);
            $model = Model();
            $field = 'product_id'
                    . ',product_code'
                    . ',local_description'
                    . ',product_classification_id'
                    . ',product_level'
                    . ',unit_of_measure_inventory_id'
                    . ',unit_of_measure_purchase_id'
                    . ',minimum_sales_quantity'
                    . ',minimum_purchase_quantity'
                    . ',to_product_id'
                    . ',brand'
                    . ',product_spec'
                    . ',unit_scale'
                    . ',member_price'
                    . ',contract_price'
                    . ',reference_price'
                    . ',vs_price'
                    . ',product_type'
                    . ',gc_classname'
                    . ',english_description'
                    . ',sales_description'
                    . ',deleted_flag'
                    . ',serialized_item_flag';
            $where_p['product_id'] = $product_id;
            $code = $model->table('product')->where($where_p)->field($field)->find();
             Tpl::output('code',$code);
        }else{
             showMessage("参数错误！请联系管理员");
        }
        
         Tpl::showpage('code.detail');
    }





    //获取物料数据 
    private function get_wuliao_num($gc_id){
            $model = Model();
            $where = array(
              "gc_id"=>$gc_id,
              "product_level"=>"0",
             );
            $type="W";
            $product_last = $model->table('product')->where($where)->field('product_code')->order('Length(product_id) desc,product_id DESC')->find();
            //判定当前物料编号是否属于全新规则12位数物料
            if(strlen($product_last['product_code']) > 10){
                $serial=  intval(substr($product_last['product_code'],-7));
            }else{
                $serial=  intval(substr($product_last['product_code'],-5));
            }
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
            //获取分类code
            $gc_code = $model->table('goods_class')->where('gc_id='.$gc_id)->field('gc_class_code')->find();
            //拼接新的物料编码值
            
            $product_id =$gc_code['gc_class_code'].$type.$g_serial;
            return $product_id;
        }


    private function add_good_data($product_id,$goods){
        $model = Model();
        $update_array = array();
                   //生成外部物料编号 插入物料编号表
                            $brandinfo = $model->table('brand')->where(array("brand_id"=>$goods['brand_id']))->find();
                            $brand_name=$brandinfo['brand_name']?$brandinfo['brand_name']:" ";
                            $update_array['brand']=$brand_name; //品牌名称
                            
                            $update_array['product_id'] =  $product_id; //物料ID
                            $update_array['product_code'] =  $product_id; //物料编号
                            $update_array['local_description'] = $goods['goods_name']; //物料名称
                            //重新对应页面的价格数据插入物料表
                            $goods_third_price=$goods['goods_third_price']?$goods['goods_third_price']:"0.00";
                            $update_array['member_price'] = $goods_third_price; //第三方价格
                            
                            $goods_marketprice=$goods['goods_marketprice']?$goods['goods_marketprice']:"0.00";
                            $update_array['contract_price'] = $goods_marketprice; //协议价格
                            
                            $goods_price=$goods['goods_price']?$goods['goods_price']:"0.00";
                            $update_array['vs_price'] = $goods_price; //会员价
                            
                            $g_cosprice=$goods['g_cosprice']?$goods['g_cosprice']:"0.00";
                            $update_array['reference_price'] = $g_cosprice;//参考价格=市场价
                            
                            $a=empty($goods['goods_spec']);
                            $c=$goods['goods_spec'];
                            $b=$goods['goods_spec']!='N;';
                            if(!empty($goods['goods_spec']) && $goods['goods_spec']!='N;'){
                            $goods_specArray=(unserialize($goods['goods_spec'])) ;
                            foreach($goods_specArray as $goods_spec){
                                $goodsspec.=$goods_spec;     
                            }
                            }else{
                                $goodsspec=" ";
                            }
                            $update_array['product_spec'] = $goodsspec; //规格
                            $update_array['product_level'] = 0;  //是否是内部物料0否 1是
                            $update_array['product_type'] = 0;  //是否是商家发布 0是1否

                            $brand=$model->table('brand')->where(array('brand_id'=>$goods['brand_id']))->find();                       
                            $update_array['brand']                  = $brand['brand_name'];  //品牌 根据brand_id到sc_brand获取
                            $update_array['serialized_item_flag'] = 1; //是否可修改
                            $update_array['supplier_id'] = $goods['store_id']; //店铺id
                               //获取member中的企业证件号码
                            $member = $model->table('store')->where(array("store_id"=>$goods['store_id']))->find();
                            $supply_code = $model->table('member')->where(array("member_id"=>$member['member_id']))->find();

                            $update_array['supplier_cd'] = $supply_code['supply_code']; //企业增加号码 供应商编号在member中获取 
                            $update_array['minimum_purchase_quantity'] = $goods['min_num'];


                            $update_array['gc_id']              = intval($goods['gc_id_3']);
                            $update_array['gc_id_1']            = intval($goods['gc_id_1']);
                            $update_array['gc_id_2']            = intval($goods['gc_id_2']);
                            $update_array['gc_id_3']            = intval($goods['gc_id_3']);

                            $update_array['update_author']      = $member['member_name'];
                            $update_array['update_date']      = date('Y-m-d-H-i-s',time());
                            $update_array['create_author']      = $member['member_name'];
                            $update_array['create_date']      = date('Y-m-d-H-i-s',time());

                            $gc_code = $model->table('goods_class')->where('gc_id='.$goods['gc_id'])->find();
                            $update_array['gc_classname'] = $gc_code['gc_name'];
                            
                            
                            return $update_array;
    }



    //物料编号重复修复逻辑
     public function del6Op(){ 
        $product_id=$_GET['product_id'];  
        $data = array($product_id);

        //调取商品数据集合
        $goods_list = $this->get_good_data($data);
        $model = Model();
        //开启事物
        $model->beginTransaction(); 
         //跟新商品数据
         if(is_array($goods_list)){
            foreach ($goods_list as $good){
                $update_goods = array();
                //获取新物料编码
                $product_id = $this->get_wuliao_num($good['gc_id']);
                $good_product_data = $this->add_good_data($product_id,$good);
                $update_goods['materiel_code'] = $product_id;
                $update_goods['goods_serial']  = $product_id;
                $result_good = $model->table('goods')->where(array('goods_id'=>$good['goods_id']))->update($update_goods);
                $result_product = $model->table('product')->insert($good_product_data);
                if($result_good && $result_product){
                    $model->commit(); 
                    var_dump("添加成功1！");
                //品牌 待确认根据brand_id到sc_brand获取           
                $brand=$model->table('brand')->where(array('brand_id'=>$good['brand_id']))->find(); 
                //推送采购系统
                $updat_list[] = $product_id;
                 try{
                 //$this->send_qiang($product_id,$good_product_data,$brand);
                 $this->transProductToYMA($updat_list,1);
                }catch(Exception $e){
                Log::record4inter("物料推送接口异常,物料数据为：".  json_encode($updat_list), log::ERR);
            }  
               // $this->send_qiang($product_id,$good_product_data,$brand);
//                //推送合同系统
                $this->transProductToCONTRACT($good_product_data['product_id']);
                }else{
                   $model->rollback();
                   var_dump("数据回滚2！");
                }
            }
         }

    }  
     public function transProductToCONTRACT($product){
        $model = Model();
        $field = 'product_id as p_segment'
                .',unit_of_measure_inventory_id as p_uom'
                .',local_description as p_description';
//                .',gc_classname as p_category_name ';   substr($aa, 0,4);
            $product_info = $model->table("product")->field($field)->where(array("product_id"=>$product))->find();
            $product_info['p_category_name'] =  substr($product, 0,4);
            $product_info['p_category_level']="3";
            $product_info['p_source_type']="CG";
            //判断如果是发布商品时单位显示为个 如果是后台添加则有单位传单位 没有单位传个
            if($product_info['p_uom']== null ||$product_info['p_uom']==""){
                $product_info['p_uom']='个';
            }
            $json = json_encode($product_info);
            $url = CONTRACT_WS_INSERT_INVITEM;
            $return_json  = WebServiceUtil::getDataByCurl($url, $json, 1);
            $array = json_decode($return_json,true);
            CommonUtil::insertData2PushLog($array, 0, $json, $url, 12);
            return $return_json;
    }
    
    
    
          /*
           *   供应商推送失败逻辑
           */
    
	 public function gongysOp(){
            //获取供应商编号
            $product_id=$_GET['member_name'];  
             //拼接参数
            $model = new Model();
            $supplyinfo = array();
            $where = array("member_name"=>$product_id);
            $storejoininfo = $model->table("store_joinin")->where($where)->find();
             //推送   
            $citywhere = array("id"=>$storejoininfo['first_city_id']);
            $cityinfo =  $model->table("city_centre")->where($citywhere)->find(); 
         if(empty($storejoininfo)){
             var_dump("该会员身份不是供应商");
             $supplyinfo['error']= "该会员身份不是供应商";
            return $supplyinfo;
            }
        
        $memberinfo = $model->table("member")->where($where)->find();
        if(empty($memberinfo)||$memberinfo==0){
            $supplyinfo['error']= "找不到该会员信息";
            return $supplyinfo;
            }
        //组织给合同系统的供应商数据
        $areaArray = explode(" ",$storejoininfo['company_address']);
        $supply_province = empty($storejoininfo['company_address'])?" ":$areaArray[0];//省
        $p_org_id=$cityinfo['zt_city_code'];//城市公司名称-->更改为城市公司战图编码
        //通过战途编码到采购系统获取到城市公司下的所有分公司
         //处理事业本部问题
         if(preg_match("/\x20*https?\:\/\/.*/i","",$_SERVER['SERVER_NAME'])){
             $dbName = "vs_purchase2";
         }else{
             $dbName = "vs_purchase_t2";
         }
         if($p_org_id == 'W000001'){
             $p_org_id_list = array();
             $p_org_id_data = $model->table("city_centre")->select();
             if(!empty($p_org_id_data) && is_array($p_org_id_data)){
                 foreach ($p_org_id_data as $vl){
                     $p_org_id_list[] = "'".$vl['zt_city_code']."'";
                 }
                 $Eas_Seq_array = Model()->query("select distinct vanke_pj_contract.contract_city_code,vanke_pj_contract.contract_city_name  from ".$dbName.".vanke_pj_contract where vanke_pj_contract.city_code in(".implode(',',$p_org_id_list).")");
             }
         }else{
             $Eas_Seq_array = Model()->query("select distinct vanke_pj_contract.contract_city_code,vanke_pj_contract.contract_city_name  from ".$dbName.".vanke_pj_contract where vanke_pj_contract.city_code= '".$p_org_id."'");   //生产环境地址 ：vs_purchase2 测试环境地址 vs_purchase_t2
         }
         $tmp = array();
         if(!empty($Eas_Seq_array) && is_array($Eas_Seq_array)){
             foreach ($Eas_Seq_array as $vls){
                 $tmp[] = array(
                     'p_org_id'=>empty($vls['contract_city_code']) ? 'W000001':$vls['contract_city_code'],
                     'vendor_site_code'=>mb_substr($vls['contract_city_name'],0,5)
                 );
             }
         }else{
             $supplyinfo['error']= "城市信息数据有误！";
             return $supplyinfo;
         }
           $supplyinfoForCT['p_org_site']=$tmp;
           $supplyinfoForCT['p_vendor_name']=$storejoininfo['company_name'];//供应商名称
           $supplyinfoForCT['p_vendor_number']=empty($memberinfo['supply_eas_code'])?$memberinfo['supply_code']:$memberinfo['supply_eas_code'];//供应商编码 若不存在eas编码则传供应商编码
           $supplyinfoForCT['p_country']='中国';//国家
           $supplyinfoForCT['p_province']=$supply_province;//省
           $supplyinfoForCT['p_loc_address']=$storejoininfo['company_address'].$storejoininfo['company_address_detail'];//详细地址
           $supplyinfoForCT['p_person_name']=empty($storejoininfo['contacts_name']) ? $memberinfo['member_name']:$storejoininfo['contacts_name'];//联系人
           $supplyinfoForCT['p_tel_number']=empty($storejoininfo['contacts_phone'] )? $memberinfo['member_name']:$storejoininfo['contacts_phone'];//联系人电话
           $supplyinfoForCT['p_bank_name']=empty($storejoininfo['bank_name']) ?"":$storejoininfo['bank_name'];//银行名称
           $supplyinfoForCT['p_bank_branch_name']=empty($storejoininfo['bank_name']) ?"":$storejoininfo['bank_name'];//分行
           $supplyinfoForCT['p_bank_account_number']=empty($storejoininfo['bank_account_number']) ?"":preg_replace('# #','',$storejoininfo['bank_account_number']);//银行帐号 格式：正数。如：29394848
           $supplyinfoForCT['p_bank_account_name']=empty($storejoininfo['bank_account_name']) ?"":$storejoininfo['bank_account_name'];//开户姓名：
           $supplyinfo['ct_supply_info'] = $supplyinfoForCT;     
           //推送到合同系统 ,移动到getSupplyInfoByKey中将返回值和eas一样放到$supplyinfo
           $TO_CT_URL = CONTRACT_WS_INSERT_SUPPLIER;
           $supplyinfo_json = json_encode($supplyinfo['ct_supply_info']); 
           $to_ct_result_json = WebServiceUtil::getDataByCurl($TO_CT_URL, $supplyinfo_json, 0);
           $to_ct_result = json_decode($to_ct_result_json,true);
           CommonUtil::insertData2PushLog($to_ct_result, '', $supplyinfo_json, $TO_CT_URL, 15);   
           $supplyinfoForCTS['resultCode'] = $to_ct_result['resultCode'];
           $supplyinfoForCTS['resultMsg']  = $to_ct_result['resultMsg'];//返回信息
           $supplyinfoForCTS['supplierNum']  = $to_ct_result['supplierNum'];//返回信息
          
        var_dump($to_ct_result['resultMsg']);
           //将合同返回的参数放到
        $member_data = array('supply_ht_code' =>empty($to_ct_result['supplierNum'])? $memberinfo['supply_code']: $to_ct_result['supplierNum'] );
        $model->table("member")->where($where)->update($member_data);
           
           if( $to_ct_result['resultCode']==201){
                var_dump("失败");
           }else{
                var_dump("成功");
           }
               }  


    public function getToProductId2Op(){
       $model = Model();
       
       if($_GET['local_description']){
         $where['local_description'] = $_GET['local_description'];  
       }
       if($_GET['brand']){
        $where['brand'] = $_GET['brand']; 
       }else{
        $where['brand'] = "";   
       }
       if($_GET['product_spec']){
        $where['product_spec'] = $_GET['product_spec']; 
       }else{
        $where['product_spec'] = "";   
       }       
        if($_GET['class_id']){
        $where['gc_id_3'] = $_GET['class_id']; 
       }       
        $where['product_level'] = 1;
        //查询当前物料分类名称
        $nbbh  = $model->table('product')->field('local_description,product_code,product_spec,brand')->where($where)->select();
         // 一级商品分类
        $gc_list = Model('goods_class')->getGoodsClassListByParentId(0);
        Tpl::output('gc_list', $gc_list);
        Tpl::output('bh',$nbbh);
        Tpl::showpage('product.getproduct','null_layout');
    }
    
    public function getToProductId3Op(){
       $model = Model();
       
       if($_POST['local_description']){
         $where['local_description'] = $_POST['local_description'];  
       }
       if($_POST['brand']){
        $where['brand'] = $_POST['brand']; 
       }else{
        $where['brand'] = "";   
       }
       if($_POST['product_spec']){
        $where['product_spec'] = $_POST['product_spec']; 
       }else{
        $where['product_spec'] = "";   
       }
       
        if($_POST['class_id']){
        $where['gc_id_3'] = $_POST['class_id']; 
       }
       
        $where['product_level'] = 1;
        $nbbh  = $model->table('product')->field('local_description,product_code,product_spec,brand')->where($where)->select();
        if($nbbh){
              echo json_encode($nbbh[0]);die;
//            echo $nbbh[0];
        }else{
            echo "0";
        }
 
    }
      public function automaticOp(){
          $a=$_POST['local_description'];
         if(empty($_POST['local_description'])){
              echo "0";exit;
         }
        //查询当前物料分类名称
        $return_array = $this->insertToProductAutomatic($_POST['local_description'], "", $_POST['class_id'],$_POST['brand_class'],$_POST['brand'],$_POST['product_spec']);
        
        if($return_array){
             echo json_encode($return_array);die;
        }else{
            echo "0";
        }
 
    }
    
     /**
     * 新增外部物料时 若没有选则内部物料 则自动插入一条内部物料与之挂钩
     * @param type $to_product_name   内部物料名称  必填
     * @param type $to_product_id     内部物料id    
     * @param type $gc_id             三级分类id
     * @return type                    to_product_id_num  表示插入的物料编号   $is_success 插入成功或者失败 
     */
    public function insertToProductAutomatic($to_product_name,$to_product_id,$gc_id,$gc_name,$brand,$product_spec){

                //如果物料id为空 那么获取物料名称 对应插入一条内部物料编号
                $model = Model();
                if(empty($to_product_id)){
                    $to_product_id_num = $this->getclasscode($gc_id);
                    //获取分类三级id
                    $gc_class = $model->table('goods_class')->where('gc_id='.$gc_id)->field('gc_parent_id')->find();
                      if($gc_class['gc_parent_id'] == 0){
                          showMessage('新增物料编号失败');exit;
                      }  else {
                        $gc_class_two = $model->table('goods_class')->where('gc_id='.$gc_class['gc_parent_id'])->field('gc_parent_id')->find();
                      }
                    $insert_data = array(
                        "product_id" => $to_product_id_num,
                        "product_code"=>$to_product_id_num,
                        "local_description" =>$to_product_name,
                        "gc_id"=>$gc_id,
                        "gc_id_1" =>$gc_class_two['gc_parent_id'],
                        "gc_id_2" =>$gc_class['gc_parent_id'],
                        "gc_id_3" =>$gc_id,
                        "product_type"=>1,
                        "product_level"=>1,
                        "supplier_cd"=>"",
                        "brand"=>  empty($brand) ? " ":$brand,
                        "product_spec"=>  empty($product_spec)?" " :$product_spec,
                        "gc_classname" =>$gc_name,
                        "create_date" =>date('Y-m-d H:i:s',time()),
                        "create_author"=>$this->getAdminInfo()['name'],
                    );
                    $is_success = $model->table("product")->insert($insert_data);
                    
                    if(!$is_success){
                         showMessage('新增物料编号失败');exit;
                    }
                    $send_data[0] =  $to_product_id_num;
                    $this->transProductToYMA($send_data,1);
                }
                $array = array(
                     "local_description"=>$to_product_name,
                     "product_id"=>$to_product_id_num
                );
                return $array;
    }
    

       /**
        * 物料导入功能
        **/
       public function wl_addOp(){
           if(!empty($_GET['ec'])) {
               //a:0:{}
               $path = BASE_DATA_PATH . DS . "upload" . DS . "excel" . DS;
               $destination = $path . $this->decode($_GET['ec']);
               include(BASE_DATA_PATH . DS . 'excel' . DS . 'reader.php'); //引入类库，类的配置文件已经被此文件引入
               $data = new Spreadsheet_Excel_Reader();
               $data->setOutputEncoding('UTF-8'); //设置输出的编码为utf8
               $data->read(iconv("UTF-8", "GB2312", $destination)); //要读取的excel文件地址
               //var_dump($data->sheets[0]);
               Tpl::output('numRows', $data->sheets[0]['numRows']);
               Tpl::output('path', $_GET['ec']);
               $model = Model();
               $data = $model->table('wl_list')->select();
               $err_log_dri = BASE_DATA_PATH . DS . "upload" . DS ."excel" . DS . 'err_log.txt';
               $file_new=fopen($err_log_dri,"r");
               $file_read = fread($file_new, filesize($err_log_dri));
               fclose($file_new);
               $err_log_data = unserialize($file_read);
               if(!empty($data)){
                   $err_data = $model->table('wl_list')->where("err_type = '1'")->select();
                   Tpl::output('numRows', $model->table('wl_list')->where("err_type = '2'")->count());
                   Tpl::output('err_data', $err_data);
               }
               Tpl::output('dr_show', empty($data) ? 1:2);
               Tpl::output('dl_show', empty($err_log_data) ? 1:2);
               Tpl::output('err_list', $err_log_data);
           }
           Tpl::showpage('wl_add');
       }


    public function check_wlOp(){
        if(!empty($_GET['ec']) && !empty($_GET['pnum'])) {
            $pnum = empty($_GET['pnum']) ? 0:$_GET['pnum'];
            $path = BASE_DATA_PATH . DS . "upload" . DS . "excel" . DS;
            $destination = $path . $this->decode($_GET['ec']);
            include(BASE_DATA_PATH . DS . 'excel' . DS . 'reader.php'); //引入类库，类的配置文件已经被此文件引入
            $data = new Spreadsheet_Excel_Reader();
            $data->setOutputEncoding('UTF-8'); //设置输出的编码为utf8
            $data->read(iconv("UTF-8", "GB2312", $destination)); //要读取的excel文件地址
            $list = $data->sheets[0]['cells'][$pnum];
            if(!empty($list) && !empty($list['4'])){
                $new_data = array(
                    'class_big'     => $list['1'],
                    'class_middel'  => $list['2'],
                    'class_small'   => $list['3'],
                    'name'          => $list['4'],
                    'brand'         => $list['5'],
                    'spec'          => $list['6'],
                    'type_name'     => $list['7']=='是' ? '2':'1',
                    'identifying'   => $_GET['ec'],
                );
                $model = Model();
                $sc_goods_class_a = $model->table("goods_class")->where("gc_name = '" . $list['1'] . "' and gc_parent_id = '0'")->find();
                if(!empty($sc_goods_class_a)){
                    $new_data['class_big_id'] = $sc_goods_class_a['gc_id'];
                    $sc_goods_class_b = $model->table("goods_class")->where("gc_name = '" . $list['2'] . "' and gc_parent_id = '" . $sc_goods_class_a['gc_id'] . "'")->find();
                    if(!empty($sc_goods_class_b)){
                        $new_data['class_middel_id'] = $sc_goods_class_b['gc_id'];
                        $sc_goods_class_c = $model->table("goods_class")->where("gc_name = '" . $list['3'] . "' and gc_parent_id = '" . $sc_goods_class_b['gc_id'] . "'")->find();
                        if(!empty($sc_goods_class_c)){
                            $new_data['class_small_id'] = $sc_goods_class_c['gc_id'];
                            $new_data['gc_name'] = $sc_goods_class_c['gc_class_code'];
                            $new_data['gc_class_code'] = $sc_goods_class_c['gc_name'];
                            //查询
                            $where = "local_description = '".$list['4']."' ";
                            $where.= empty($list['5']) ? "":"and brand = '".$list['5']."' ";
                            $where.= empty($list['6']) ? "":"and product_spec = '".$list['6']."' ";
                            //处理内部物料或者外部物料
                            if($new_data['type_name'] == '2'){
                                //内部
                                $where.= "and product_level = '1' ";
                                $wl_data = $model->table("product")->where($where)->find();
                                if(empty($wl_data)){
                                    $new_data['err_type'] = 2;
                                }else{
                                    $new_data['err_log'] = '内部物料重复';
                                }
                            }else{
                                //外部
                                $where.= "and gc_id_1 = '".$new_data['class_big_id']."' ";
                                $where.= "and gc_id_2 = '".$new_data['class_middel_id']."' ";
                                $where.= "and gc_id_3 = '".$new_data['class_small_id']."' ";
                                $where.= "and product_level = '0' ";
                                $wl_data = $model->table("product")->where($where)->find();
                                if(empty($wl_data)){
                                    //匹配内部物料
                                    $where_nei = "local_description = '".$list['4']."' ";
                                    $where_nei.= empty($list['5']) ? "":"and brand = '".$list['5']."' ";
                                    $where_nei.= empty($list['6']) ? "":"and product_spec = '".$list['6']."' ";
                                    $where_nei.= "and product_level = '1' ";
                                    $wl_data_nei = $model->table("product")->where($where_nei)->find();
                                    if(empty($wl_data_nei)){
                                        $new_data['is_to_nei'] = 'N';
                                        $new_data['err_type'] = 2;
                                    }else{
                                        $new_data['is_to_nei'] = 'Y';
                                        $new_data['err_log'] = '匹配到内部物料';
                                    }
                                }else{
                                    $new_data['err_log'] = '外部物料重复';
                                }
                            }
                        }else{
                            $new_data['err_log'] = '物料小类不存在';
                        }
                    }else{
                        $new_data['err_log'] = '物料中类不存在';
                    }
                }else{
                    $new_data['err_log'] = '物料大类不存在';
                }

                $res_wl = $model->table('wl_list')->insert($new_data);
                if ($pnum < $data->sheets[0]['numRows']) {
                    sleep(0.1);
                    echo '物料:"'.$list['4'].'" 处理完成！</n>';
                } else {
                    echo '检查物料结束!</n>';
                }
            }
        }
    }


    public function add_wlOp(){
        if(!empty($_GET['ec']) && !empty($_GET['pnum'])) {
            $pnum = empty($_GET['pnum']) ? 0:$_GET['pnum'];
            //查询出一条为处理数据
            $model = Model();
            $data = $model->table('wl_list')->where("err_type = '2' and cl_type = '1'")->find();
            if(!empty($data)){
                $log_data_a = $log_data = array(
                    'class_big'     => $data['class_big'],
                    'class_middel'  => $data['class_middel'],
                    'class_small'   => $data['class_small'],
                    'name'          => $data['name'],
                    'brand'         => $data['brand'],
                    'spec'          => $data['spec'],
                );
                $err_log_dri = BASE_DATA_PATH . DS . "upload" . DS ."excel" . DS . 'err_log.txt';
                $file_new=fopen($err_log_dri,"r");
                $file_read = fread($file_new, filesize($err_log_dri));
                fclose($file_new);
                $err_data = unserialize($file_read);
                $good_class_data = array(
                    'gc_id_1' => $data['class_big_id'],
                    'gc_id_2' => $data['class_middel_id'],
                    'gc_id_3' => $data['class_small_id']
                );
                if($data['type_name'] == '1'){
                    //处理外部物料
                    $wl_num_nei = $data['gc_name']."N".$this->getSpecificationsNum($good_class_data,'N');
                    $res_wl_nei = $this->add_wl_data($data,$wl_num_nei,'','N');
                    if($res_wl_nei){
                        $wl_num = $data['gc_name']."W".$this->getSpecificationsNum($good_class_data,'W');
                        //内部首先推送
                        $res_wl = $this->add_wl_data($data,$wl_num,$wl_num_nei,'W');
                        //发起采购推送
                        $res_nei_cg = $this->send_caigou($data,$wl_num_nei);
                        if($res_nei_cg['resultCode']!='0'){
                            $log_data['type_name'] = '是';
                            $log_data['code'] = $wl_num_nei;
                            $log_data['cg_log'] = "采购数据推送失败";
                            $err_data[] = $log_data;
                        }
                        if($res_wl){
                            $log_data_a['cl_type'] = 2;
                            $log_data_a['pr_type'] = 2;
                            $log_data_a['type_name'] = '否';
                            $log_data_a['code'] = $wl_num;
                            //发起采购推送
                            $res_wai_cg = $this->send_caigou($data,$wl_num,$wl_num_nei);
                            if($res_wai_cg['resultCode']!='0'){
                                $log_data_a['cl_type'] = 2;
                                $log_data_a['pr_type'] = 2;
                                $log_data_a['type_name'] = '否';
                                $log_data_a['code'] = $wl_num;
                                $log_data_a['cg_log'] = "采购数据推送失败";
                                $err_data[] = $log_data_a;
                            }
                        }else{
                            $log_data_a['cl_type'] = 2;
                            $log_data_a['pr_type'] = 1;
                            $log_data_a['type_name'] = '否';
                            $log_data_a['code'] = $wl_num;
                            $log_data_a['cg_log'] = "外部物料添加失败";
                            $err_data[] = $log_data_a;
                        }
                    }else{
                        $log_data_a['cl_type'] = 2;
                        $log_data_a['pr_type'] = 3;
                        $log_data['type_name'] = '是';
                        $log_data['code'] = $wl_num_nei;
                        $log_data['cg_log'] = "内部物料添加失败";
                        $err_data[] = $log_data;
                    }
                }else{
                    //处理内部物料
                    $log_data['type_name'] = '是';
                    $wl_num_nei = $data['gc_name']."N".$this->getSpecificationsNum($good_class_data,'N');
                    $log_data['code'] = $wl_num_nei;
                    $res_wl = $this->add_wl_data($data,$wl_num_nei,'','N');
                    if($res_wl){
                        $res_nei_cg = $this->send_caigou($data,$wl_num_nei);
                        if($res_nei_cg['resultCode']!='0'){
                            $log_data_a['cl_type'] = 2;
                            $log_data_a['pr_type'] = 2;
                            $log_data_a['type_name'] = '否';
                            $log_data_a['code'] = $wl_num_nei;
                            $log_data_a['cg_log'] = "采购数据推送失败";
                            $err_data[] = $log_data_a;
                        }
                    }else{
                        $log_data_a['cl_type'] = 2;
                        $log_data_a['pr_type'] = 3;
                        $log_data['type_name'] = '是';
                        $log_data['code'] = $wl_num_nei;
                        $log_data['cg_log'] = "内部物料添加失败";
                        $err_data[] = $log_data;
                    }
                }
                //添加完成处理推送数据
                if($res_wl){
                    $res_data['cl_type'] = 2;
                    $res_data['pr_type'] = 2;
                }else{
                    $res_data['cl_type'] = 2;
                    $res_data['pr_type'] = 3;
                    $log = '物料:"'.$data['name'].'" 添加失败！</n>';
                }
                $model->table('wl_list')->where("id = '".$data['id']."'")->update($res_data);
                $file_old=fopen($err_log_dri,"w");
                fwrite($file_old,serialize($err_data));
                fclose($file_old);
                sleep(0.1);
                echo $log;
            }else {
                echo '检查物料结束!</n>';
            }
        }
    }


       /*public function check_wlOp(){
           if(!empty($_GET['ec']) && !empty($_GET['pnum'])) {
               $pnum = empty($_GET['pnum']) ? 0:$_GET['pnum'];
               $path = BASE_DATA_PATH . DS . "upload" . DS . "excel" . DS;
               $destination = $path . $this->decode($_GET['ec']);
               include(BASE_DATA_PATH . DS . 'excel' . DS . 'reader.php'); //引入类库，类的配置文件已经被此文件引入
               $data = new Spreadsheet_Excel_Reader();
               $data->setOutputEncoding('UTF-8'); //设置输出的编码为utf8
               $data->read(iconv("UTF-8", "GB2312", $destination)); //要读取的excel文件地址
               $list = $data->sheets[0]['cells'][$pnum];
               if(!empty($list) && !empty($list['4'])){
                   $err_dri = $path . date("Ymd",time()) . '_err.txt';
                   $file_new_err=fopen($err_dri,"r");
                   $file_err = fread($file_new_err, filesize($err_dri));
                   fclose($file_new_err);
                   $old_err = unserialize($file_err);
                   //打开整理数据
                   $yes_dri = $path . date("Ymd",time()) . '_yes.txt';
                   $file_new_yes=fopen($yes_dri,"r");
                   $file_yes = fread($file_new_yes, filesize($yes_dri));
                   fclose($file_new_yes);
                   $old_yes = unserialize($file_yes);
                   $model = Model();
                   $new_list = array();
                   $sc_goods_class_a = $model->table("goods_class")->where("gc_name = '" . $list['1'] . "' and gc_parent_id = '0'")->find();
                   if(!empty($sc_goods_class_a)){
                       $list['8'] = $sc_goods_class_a['gc_id'];
                       $sc_goods_class_b = $model->table("goods_class")->where("gc_name = '" . $list['2'] . "' and gc_parent_id = '" . $sc_goods_class_a['gc_id'] . "'")->find();
                        if(!empty($sc_goods_class_b)){
                            $list['9'] = $sc_goods_class_b['gc_id'];
                            $sc_goods_class_c = $model->table("goods_class")->where("gc_name = '" . $list['3'] . "' and gc_parent_id = '" . $sc_goods_class_b['gc_id'] . "'")->find();
                            if(!empty($sc_goods_class_c)){
                                $list['10'] = $sc_goods_class_c['gc_id'];
                                $list['11'] = $sc_goods_class_c['gc_class_code'];
                                $list['12'] = $sc_goods_class_c['gc_name'];
                                $where = "local_description = '".$list['4']."' ";
                                $where.= empty($list['5']) ? "":"and brand = '".$list['5']."' ";
                                $where.= empty($list['6']) ? "":"and product_spec = '".$list['6']."' ";
                                $where.= "and gc_id_1 = '".$list['8']."' ";
                                $where.= "and gc_id_2 = '".$list['9']."' ";
                                $where.= "and gc_id_3 = '".$list['10']."' ";
                                $wl_data = $model->table("product")->where($where)->find();
                                if(empty($wl_data)){
                                    //获取物料编号
                                    $wl_num = $list['11']."W".$this->getSpecificationsNum(array('gc_id_1'=>$list['8'],'gc_id_2'=>$list['9'],'gc_id_3'=>$list['10']));
                                    $res_wl = $this->add_wl($list,$wl_num);
                                    if($res_wl){
                                        $cg_res = $this->send_caigou($list,$wl_num);
                                        $ht_res = $this->send_hetong($list,$wl_num);
                                        $where = "product_id = '".$wl_num."'";
                                        if($cg_res['resultCode']=='0'){
                                            $model->table("product")->where($where)->update(array('english_description'=>0));
                                        }
                                        if($ht_res['resultCode'] == '200'){
                                            $model->table("product")->where($where)->update(array('sales_description'=>0));
                                        }
                                        $list['err'] = '-1';
                                    }else{
                                        $list['err'] = '物料导入失败';
                                    }
                                }else{
                                    $list['err'] = '物料重复';
                                }
                            }else{
                                $list['err'] = '物料小类不存在';
                            }
                        }else{
                            $list['err'] = '物料中类不存在';
                        }
                   }else{
                       $list['err'] = '物料大类不存在';
                   }
                   if($list['err'] == '-1'){
                       $old_yes[$pnum] = $list;
                       $file_yes=fopen($yes_dri,"w");
                       fwrite($file_yes,serialize($old_yes));
                       fclose($file_yes);
                   }else{
                       $old_err[$pnum] = $list;
                       $file_err=fopen($err_dri,"w");
                       fwrite($file_err,serialize($old_err));
                       fclose($file_err);
                   }
               }
               if ($pnum < $data->sheets[0]['numRows']) {
                   sleep(0.1);
                   echo '物料:"'.$list['4'].'" 处理完成！<\/br>';
               } else {
                   echo '导入物料结束!<\/br>';
               }
           }
       }*/


       private function add_wl_data($list,$wl_num,$nei_num=NULL,$type='W'){
           $model = Model();
           $material = array(
               'product_id'                => $wl_num,
               'product_code'              => $wl_num,
               'local_description'         => empty($list['name']) ? "":$list['name'],
               'product_spec'              => empty($list['spec']) ? "":$list['spec'],
               'product_type'              => 0,
               'brand'                     => empty($list['brand']) ? "":$list['brand'],
               'serialized_item_flag'      => 1,
               'gc_id'                     => intval($list['class_small_id']),
               'gc_id_1'                   => intval($list['class_big_id']),
               'gc_id_2'                   => intval($list['class_middel_id']),
               'gc_id_3'                   => intval($list['class_small_id']),
               'update_author'             => 'admin_0',
               'update_date'               => date('Y-m-d-H-i-s',time()),
               'create_author'             => 'admin_0',
               'create_date'               => date('Y-m-d-H-i-s',time()),
               'gc_classname'              => $list['gc_class_code']
           );
           if($type == 'N'){
                //N
               $material['product_level'] = 1;
           }else{
               //Y
               $material['product_level'] = 0;
               $material['to_product_id'] = $nei_num;
           }
           $insert_proc = $model->table('product')->insert($material);
           return $insert_proc;
       }

       public function wl_upOp(){
           $fileInfo = $_FILES["myFile"];
           $filename = $fileInfo["name"];
           $type = $fileInfo["type"];
           $error = $fileInfo["error"];
           $size = $fileInfo["size"];
           $tmp_name = $fileInfo["tmp_name"];
           $maxSize=2*1024*1024;//允许的最大值
           $allowExt=array("xls");
           $flag = true;//检测是否为真实的图片类型


           $path = BASE_DATA_PATH . DS . "upload" . DS ."excel" . DS;
            //执行清空表
           $model = Model();
           @$model->query('truncate sc_wl_list');
           $err_log_dri = BASE_DATA_PATH . DS . "upload" . DS ."excel" . DS . 'err_log.txt';
           $file_old=fopen($err_log_dri,"w");
           fwrite($file_old,serialize(array()));
           fclose($file_old);
           //判断错误号
           if($error == 0){
               //判断上传文件的大小
               if($size>$maxSize){
                   showMessage('上传文件过大','index.php?act=codemanages&op=wl_add');
                   //exit("上传文件过大");
               }

               //检测文件类型
               //取出文件扩展名
               $ext = pathinfo($filename,PATHINFO_EXTENSION);
               if(!in_array($ext,$allowExt)){
                   showMessage('非法文件类型','index.php?act=codemanages&op=wl_add');
                   //exit("非法文件类型");
               }

               //创建目录
               //$path = "/work/wkShop/data/upload/excel/";
               if(!file_exists($path)){
                   mkdir($path,0777,true);
                   chmod($path,0777);
               }

               //确保文件名唯一,防止重名覆盖
               $uniName = md5(uniqid(microtime(true),true)).".".$ext;
               $destination = $path.$uniName;
               if(@move_uploaded_file($tmp_name,$destination)){
                   showMessage('上传成功','index.php?act=codemanages&op=wl_add&ec='.$this->encode($uniName));
               }else{
                   showMessage('上传失败','index.php?act=codemanages&op=wl_add');
               }
           }else{
               switch($error){
                   case 1:
                   case 2:
                   case 3:
                   case 4:
                   case 6:
                   case 7:
                   case 8:
                       echo "上传错误";
                       break;
               }
           }
       }


    /**
     * 简单对称加密算法之加密
     * @param String $string 需要加密的字串
     * @param String $skey 加密EKY
     * @author Aletta
     * @date 2017-08-9
     * @return String
     */
    private function encode($string = '', $skey = 'vanke') {
        $strArr = str_split(base64_encode($string));
        $strCount = count($strArr);
        foreach (str_split($skey) as $key => $value)
            $key < $strCount && $strArr[$key].=$value;
        return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
    }
    /**
     * 简单对称加密算法之解密
     * @param String $string 需要解密的字串
     * @param String $skey 解密KEY
     * @author Aletta
     * @date 2017-08-9
     * @return String
     */
    private function decode($string = '', $skey = 'vanke') {
        $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
        $strCount = count($strArr);
        foreach (str_split($skey) as $key => $value)
            $key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
        return base64_decode(join('', $strArr));
    }



    /**
     * 获取物料编号
     * @Author Aletta
     * @Date 2017-08-15
     * 1:外部，2：内部
     **/
    protected function getSpecificationsNum($common_array,$type){
        $model = Model();
        //获取当前分类下的物料编号
        $goods_serial_id_where['gc_id_1'] = intval($common_array['gc_id_1']);
        $goods_serial_id_where['gc_id_2'] = intval($common_array['gc_id_2']);
        $goods_serial_id_where['gc_id_3'] = intval($common_array['gc_id_3']);
        if($type == 'W'){
            $goods_serial_id_where['product_level'] = 0;
        }else{
            $goods_serial_id_where['product_level'] = 1;
        }
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
     * 向采购推送物料数据
     * @Author Aletta
     * @Date 2017-08-15
     **/
    private function send_caigou($list = array(),$wl_num,$nei_num=NULL){
        if(!empty($list)){
            $data = array(
                "product_code"              => $wl_num,  //物料id
                "product_category_codes"    => $this->get_class_code($list['class_big_id']).",".$this->get_class_code($list['class_middel_id']).",".$this->get_class_code($list['class_small_id']),//code
                "local_description"         => empty($list['name']) ? "":$list['name'],  //物料名称
                "brand"                     => empty($list['brand']) ? "":$list['brand'],  //品牌名称
                "reference_price"           => '0.00', //参考价格
                "vs_price"                  => '0.00', //会员价格
                "contract_price"            => '0.00', //协议价格
                "product_spec"              => empty($list['spec']) ? "":$list['spec'],//规格
                "minimum_purchase_quantity" =>'0', //最小采购数量
                "minimum_sales_quantity"    => "1", //最小库存
                "unit_of_measure_purchase_id"=>"个",  //采购单位
                "unit_of_measure_inventory_id"=> "个", //库存单位
                "serialized_item_flag"      => "1", //是否可修改
                "product_type"              => "0", //物料类型
                "supplier_cd"               => "",  //企业证件
                "deleted_flag"              => "1" //是否有效
            );
            if($list['type_name'] == '2'){
                //N
                $data['product_level'] = 1;
                $data['to_product_code'] = '';
            }else{
                //Y
                $data['product_level'] = 0;
                $data['to_product_code'] = $nei_num;
            }
            $send_data = json_encode(array('product_json'=>array($data)));
            log::record4inter("物料添加:".$send_data, log::MOBILE_MESSAGE);
            $url=YMA_WEBSERVICE_INSERT_AND_UPDATE_PRODUCT;
            $resultArray = WebServiceUtil::getDataByCurl($url, $send_data, 0);

            $resultArray = json_decode($resultArray,true);

            if($resultArray['resultCode']=='0'){
                log::record4inter("推送采购物料数据成功", log::INFO);
                echo '物料"'.$list['name'].'"推送采购后台成功！<\n>';
            }else{
                log::record4inter("推送采购物料数据失败", log::ERR);
                echo '物料"'.$list['name'].'"推送采购后台失败！<\n>';
            }

            return $resultArray;
        }
    }


    private function get_class_code($id){
        $model = Model();
        $list = $model->table("goods_class")->where("gc_id = '".$id."'")->find();
        return $list['gc_class_code'];
    }



    /**
     * 向合同推送物料数据
     * @Author Aletta
     * @Date 2017-08-15
     **/
    private function send_hetong($list = array(),$wl_num){
        if(!empty($list)){
            $data = array(
                'p_segment'         => $wl_num,
                'p_uom'             => '个',
                'p_description'     => empty($list['name']) ? "":$list['name'],
                'p_category_name'   => substr($list['gc_class_code'], 0,4),
                'p_category_level'  => '3',
                'p_source_type'     => "CG",
            );
            $json = json_encode($data);
            $url = CONTRACT_WS_INSERT_INVITEM;
            $return_json  = WebServiceUtil::getDataByCurl($url, $json, 1);
            $array = json_decode($return_json,true);
            CommonUtil::insertData2PushLog($array, 0, $json, $url, 12);
            if($array['resultCode'] == '200'){
                echo '物料"'.$list['name'].'"推送合同后台成功！</n>';
            }else{
                echo '物料"'.$list['name'].'"推送合同后台失败！</n>';
            }
            return $array;
        }
    }


    public function export_stepOp(){
        $model = Model();
        $page	= new Page();
        $page->setEachNum(5000);
        $list	= $model->table('wl_list')->where("err_type = '1'")->select();
        $this->createExcel($list);
    }

    public function export_step1Op(){
        $model = Model();
        $page	= new Page();
        $page->setEachNum(5000);
        $err_log_dri = BASE_DATA_PATH . DS . "upload" . DS ."excel" . DS . 'err_log.txt';
        $file_new=fopen($err_log_dri,"r");
        $file_read = fread($file_new, filesize($err_log_dri));
        fclose($file_new);
        $list = unserialize($file_read);
        $this->createExcel1($list);
    }

    private function createExcel($data = array()){
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'物料大类');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'物料中类');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'物料小类');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'物料名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'品牌');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'规格/型号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'是否内部物料');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'错误原因');

        foreach ((array)$data as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['class_big']);
            $tmp[] = array('data'=>$v['class_middel']);
            $tmp[] = array('data'=>$v['class_small']);
            $tmp[] = array('data'=>$v['name']);
            $tmp[] = array('data'=>$v['brand']);
            $tmp[] = array('data'=>$v['spec']);
            $tmp[] = array('data'=>$v['type_name']==1 ? '否':'是');
            $tmp[] = array('data'=>$v['err_log']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('物料导入异常数据',CHARSET));
        $excel_obj->generateXML($excel_obj->charset('物料导入异常数据',CHARSET).'-'.date('Y-m-d-H',time()));
    }

    private function createExcel1($data = array()){
        Language::read('export');
        import('libraries.excel');
        $excel_obj = new Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
        //header
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'物料编号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'物料大类');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'物料中类');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'物料小类');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'物料名称');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'品牌');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'规格/型号');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'是否内部物料');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'采购推送状态');
        $excel_data[0][] = array('styleid'=>'s_title','data'=>'合同推送状态');

        foreach ((array)$data as $k=>$v){
            $tmp = array();
            $tmp[] = array('data'=>$v['code']);
            $tmp[] = array('data'=>$v['class_big']);
            $tmp[] = array('data'=>$v['class_middel']);
            $tmp[] = array('data'=>$v['class_small']);
            $tmp[] = array('data'=>$v['name']);
            $tmp[] = array('data'=>$v['brand']);
            $tmp[] = array('data'=>$v['spec']);
            $tmp[] = array('data'=>$v['type_name']);
            $tmp[] = array('data'=>$v['cg_type']==2 ? "成功":"失败");
            $tmp[] = array('data'=>$v['ht_type']==2 ? "成功":"失败");
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data,CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('物料导入推送异常数据',CHARSET));
        $excel_obj->generateXML($excel_obj->charset('物料导入推送异常数据',CHARSET).'-'.date('Y-m-d-H',time()));
    }

}


