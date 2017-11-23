<?php
/**
 * 批量处理订单生成控制器
 ***/



class interfaceControl extends BaseHomeControl {

  public function testOp(){
//      $product = "AA01W00005";
//      $model = Model();
//        $field = 'product_id as p_segment'
//                .',unit_of_measure_inventory_id as p_uom'
//                .',local_description as p_description';
////                .',gc_classname as p_category_name ';   substr($aa, 0,4);
//            $product_info = $model->table("product")->field($field)->where(array("product_id"=>$product))->find();
//            $product_info['p_category_name'] =  substr($product, 0,4);
//            $product_info['p_category_level']="3";
//            $product_info['p_source_type']="CG";
//            //判断如果是发布商品时单位显示为个 如果是后台添加则有单位传单位 没有单位传个
//            if($product_info['p_uom']== null ||$product_info['p_uom']==""){
//                $product_info['p_uom']='个';
//            }
//            var_dump($product_info);exit;
      
      
      
      $result1 = false;
      $result2 = false;
      $email	= new Email();
      $result1	= $email->send_sys_email("v-raoxz@vanke.com","来自商城的测试邮件","ceshi jiesssssssguo0 o");
      var_dump("内部");
      var_dump($result1);
      $result2	= $email->send_sys_email("375253446@qq.com","来自商城的测试邮件","ceshi jiegssssssssuo0 o");
      var_dump("外部");
      var_dump($result2);
      exit;
  }
    
  




  }
