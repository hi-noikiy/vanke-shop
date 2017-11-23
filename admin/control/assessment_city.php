<?php
class assessment_cityControl extends SystemControl{
	const EXPORT_SIZE = 1000;
	public function __construct(){
            parent::__construct();
            Language::read('goods_class');
	}
        //评估管理 列表页
        public function indexOp(){
            
            
            $model = Model();
            $is_mub_list_where['member_id']  = htmlspecialchars($_GET['member_id']);
            $is_mub_list = $model->table('exam')->where($is_mub_list_where)->select();
            if($is_mub_list){
                redirect('index.php?act=assessment&op=edit&member_id='.$is_mub_list_where['member_id'].'&store_id='.$is_mub_list_where['member_id']);
            }
            if(chksubmit()){
                if($_POST){
                    try {
                        $exam_model = Model('exam');
                        $exam_model->beginTransaction();
                        $data_insert = array();
                        $data_insert_num = 0;
                        $check_num  = 0;
                        //删除旧的模板
                        $model->where('member_id='.$_POST['member_id'])->delete();
                        foreach($_POST['data'] as $key=>$rows){
                            $data_insert['tid_1']   = $rows['tid_1'];
                            $data_insert['tid_2']   = $rows['tid_2'];
                            $data_insert['type_1']  = $_POST['pdata'][$rows['tid_1']]['type_1'];
                            $data_insert['type_2']  = $rows['type_2'];
                            $data_insert['member_id']  = $_POST['member_id'];
                            foreach($rows['data_re'] as $datarows){
                                $data_insert['ptid']    = $data_insert['tid_1'];
                                $data_insert['question']    = $datarows['question'];
                                $data_insert['scale']       = intval($datarows['scale']);
                                $data_insert['desc_1']      = $datarows['desc_1'];
                                $data_insert['desc_5']      = $datarows['desc_5'];
                                $scale += $data_insert['scale'];
                                //循环出这个属性
                                $insert = $exam_model->insert($data_insert);
                                if($insert != false){
                                    $data_insert_num++;
                                }
                                $check_num++;
                            }
                        }
                        if($scale < 100){
                            $exam_model->rollback();
                            showMessage("权重必须等于100！");
                        }
                        //判断是否全部插入数据库
                        if($check_num == $data_insert_num){
                            $exam_model->commit();
                            showMessage("添加成功！",ADMIN_SITE_URL.'/index.php?act=assessment&op=exam&member_id='.$_POST['member_id']);
                        }else{
                            showMessage("添加失败！");
                        }
                    } catch (Exception $ex) {
                        showMessage("添加失败！");
                        $exam_model->rollback();
                    }
                    
                }
            }
            $condiiton['store.store_id']  = array('gt',0);
            $store = $model->table('store,exam')
                    ->where($condiiton)
                    ->join('right')
                    ->on('store.member_id=exam.member_id')
                    ->field('store.store_name,exam.member_id')
                    ->group('store.member_id')
                    ->select();
            Tpl::output('store',$store);
            Tpl::output('class_list',$att_list);
            Tpl::output('page',$model->showpage('2'));
            Tpl::showpage('store.assessment');
        }
        public function editOp(){
            $model  = Model();
            $condiiton['store.store_id']  = array('gt',0);
            $store = $model->table('store,exam')
                    ->where($condiiton)
                    ->join('right')
                    ->on('store.member_id=exam.member_id')
                    ->field('store.store_name,exam.member_id')
                    ->group('store.member_id')
                    ->select();
		
            if($_GET['store_id'] > 0){
                //查询模板
                $member_id = htmlspecialchars($_GET['store_id']);
                $list_data = $model->table('exam')->where('member_id='.$member_id)->select();
                
                //查询父ID 重复的值
                $list = $model->where('member_id='.$member_id)->field('distinct tid_1')->select();
               
                if(!$list_data){
                    showMessage("请先添加评估模板！",ADMIN_SITE_URL.'/index.php?act=assessment&op=index&member_id='.$member_id);
                }
                $data = array();
                foreach($list_data as $rows_t){
                    
                    foreach($list as $rows){
                        if($rows['tid_1'] == $rows_t['tid_1']){
                            $data[$rows['tid_1']]['type_1'] = $rows_t['type_1'];
                            $data[$rows['tid_1']]['tid_1'] = $rows_t['tid_1'];
                            $data[$rows['tid_1']]['member_id'] = $rows_t['member_id'];
                            break;
                        }
                    }
                    
                }
                //查询二级分类 
                $list_two_where['member_id'] = $member_id;
                $data_two = array();
                $i=0;
                foreach($list as $rows){
                    $list_two_where['ptid'] = $rows['tid_1'];
                    $list_two = $model->where($list_two_where)->select();
                    //print_r($list_two);exit;
                    foreach($list_data as $rows_t1){
                 
                        foreach($list_two as $rows){
                            if($rows['tid_2'] == $rows_t1['tid_2']){
                                $data[$rows_t1['tid_1']]['data1'][$rows['tid_2']]['type_2'] = $rows_t1['type_2'];
                                $data[$rows_t1['tid_1']]['data1'][$rows['tid_2']]['tid_2'] = $rows_t1['tid_2'];
                                $data[$rows_t1['tid_1']]['data1'][$rows['tid_2']]['member_id'] = $rows_t1['member_id'];
                                $querstion_where['tid_2'] = $rows_t1['tid_2'];
                                $querstion_where['member_id'] = $member_id;
                                $data2 = $model->where($querstion_where)->field('id,question,scale,desc_1,desc_5')->select();
                                $data[$rows_t1['tid_1']]['data1'][$rows['tid_2']]['data2'] = $data2;
                            }
                        }
                    
                    }
                }
                foreach($data as $key=>$rows){
                    foreach($rows['data1'] as  $t_rows){
                        $data[$key]['num'] += count($t_rows['data2']);
                    }
                }
                
            }
            
            //提交修改
            if(chksubmit()){
                if($_POST){
                    try {
                        $exam_model = Model('exam');
                        $exam_model->beginTransaction();
                        $data_insert = array();
                        $data_insert_num = 0;
                        $check_num  = 0;
                        //删除旧的模板
                        $model->where('member_id='.$_POST['member_id'])->delete();
                        foreach($_POST['data'] as $key=>$rows){
                            $data_insert['tid_1']   = $rows['tid_1'];
                            $data_insert['tid_2']   = $rows['tid_2'];
                            $data_insert['type_1']  = $_POST['pdata'][$rows['tid_1']]['type_1'];
                            $data_insert['type_2']  = $rows['type_2'];
                            $data_insert['member_id']  = $_POST['member_id'];
                            foreach($rows['data_re'] as $datarows){
                                $data_insert['ptid']    = $data_insert['tid_1'];
                                $data_insert['question']    = $datarows['question'];
                                $data_insert['scale']       = intval($datarows['scale']);
                                $data_insert['desc_1']      = $datarows['desc_1'];
                                $data_insert['desc_5']      = $datarows['desc_5'];
                                $scale += $data_insert['scale'];
                                //循环出这个属性
                                $insert = $exam_model->insert($data_insert);
                                if($insert != false){
                                    $data_insert_num++;
                                }
                                $check_num++;
                            }
                        }
                        if($scale < 100){
                            $exam_model->rollback();
                            showMessage("权重必须等于100！");
                        }
                        //判断是否全部插入数据库
                        if($check_num == $data_insert_num){
                            $exam_model->commit();
                            showMessage("添加成功！",ADMIN_SITE_URL.'/index.php?act=assessment&op=exam&member_id='.$_POST['member_id']);
                        }else{
                            showMessage("添加失败！");
                        }
                    } catch (Exception $ex) {
                        showMessage("添加失败！");
                        $exam_model->rollback();
                    }
                    
                }
            }
            
            
            Tpl::output('mub',$data);
            Tpl::output('store',$store);
            Tpl::showpage('store.assessment.medit');
        }


        public function examOp(){
            
            $member_id = htmlspecialchars($_GET['member_id']);
            if($member_id > 0){
                $model = Model('exam');
                //查询父ID 重复的值
                $list = $model->where('member_id='.$member_id)->field('distinct tid_1')->select();
               
                $list_data = $model->where('member_id='.$member_id)->select();
                if(!$list_data){
                    showMessage("请先添加评估模板！",ADMIN_SITE_URL.'/index.php?act=assessment&op=index&member_id='.$member_id);
                }
                $data = array();
                foreach($list_data as $rows_t){
                    
                    foreach($list as $rows){
                        if($rows['tid_1'] == $rows_t['tid_1']){
                            $data[$rows['tid_1']]['type_1'] = $rows_t['type_1'];
                            $data[$rows['tid_1']]['tid_1'] = $rows_t['tid_1'];
                            $data[$rows['tid_1']]['member_id'] = $rows_t['member_id'];
                            break;
                        }
                    }
                    
                }
                //查询二级分类 
                $list_two_where['member_id'] = $member_id;
                $data_two = array();
                $i=0;
                foreach($list as $rows){
                    $list_two_where['ptid'] = $rows['tid_1'];
                    $list_two = $model->where($list_two_where)->select();
                    //print_r($list_two);exit;
                    foreach($list_data as $rows_t1){
                 
                        foreach($list_two as $rows){
                            if($rows['tid_2'] == $rows_t1['tid_2']){
                                $data[$rows_t1['tid_1']]['data1'][$rows['tid_2']]['type_2'] = $rows_t1['type_2'];
                                $data[$rows_t1['tid_1']]['data1'][$rows['tid_2']]['tid_2'] = $rows_t1['tid_2'];
                                $data[$rows_t1['tid_1']]['data1'][$rows['tid_2']]['member_id'] = $rows_t1['member_id'];
                                $querstion_where['tid_2'] = $rows_t1['tid_2'];
                                $querstion_where['member_id'] = $member_id;
                                $data2 = $model->where($querstion_where)->field('id,question,scale,desc_1,desc_5')->select();
                                $data[$rows_t1['tid_1']]['data1'][$rows['tid_2']]['data2'] = $data2;
                            }
                        }
                    
                    }
                }
                foreach($data as $key=>$rows){
                    foreach($rows['data1'] as  $t_rows){
                        $data[$key]['num'] += count($t_rows['data2']);
                    }
                }
                //计算各级分类有多少个记录
                Tpl::output('list',$data);
            }else{
                showMessage("供应商参数错误！");
            }
            Tpl::showpage('store.assessment.edit','exam_layout');
        }
        public function up_examOp(){
            
            $member_id = htmlspecialchars($_GET['member_id']);
            if($member_id > 0){
                $model = Model();
                $i=0;
                $it=0;
                $model->beginTransaction();
                foreach($_POST['data'] as $key=>$rows){
                    //获取exam 的ID 组成新分数记录插入
                    $exam = $model->table('exam')->where('id='.$key)->find();
                    $exam['score'] = $rows['score'];
                    unset($exam['id']);
                    $exam['addtime'] = time();
                    $insert = $model->table('exam_score')->insert($exam);
                    if($insert != false){
                        $i++;
                    }
                    $it++;
                }
                if($i == $it){
                    $model->commit();
                    showMessage("评估成功！",ADMIN_SITE_URL.'/index.php?act=assessment&op=store');
                }else{
                    $model->rollback();
                    showMessage("评估失败！",ADMIN_SITE_URL.'/index.php?act=assessment&op=exam&member_id='.$member_id);
                }
            }else{
                showMessage("供应商参数错误！");
            }
        }
        public function lookOp(){
            $model = Model();
            
            $condiiton['exam_score.member_id'] = htmlspecialchars($_GET['member_id']);
            $condiiton['exam_score.addtime'] = array('gt',0);
            
            $exam_list = $model->table('store,exam_score')
                    ->where($condiiton)
                    ->join('right')
                    ->on('store.member_id = exam_score.member_id')
                    ->field('store.store_name,exam_score.member_id,exam_score.addtime')
                    ->group('exam_score.addtime')
                    ->select();
            
            Tpl::output('list',$exam_list);
            Tpl::showpage('store.assessment.look');
        }
        
        public function lookedOp(){
            
            
            $member_id = htmlspecialchars($_GET['member_id']);
            if($member_id > 0){
                $model = Model('exam_score');
                //查询父ID 重复的值
                $where_listdata['member_id'] = $member_id;
                $where_listdata['addtime'] = intval($_GET['time']);
                $list = $model->where($where_listdata)->field('distinct tid_1')->select();
                $list_data = $model->where($where_listdata)->select();
                
                if(!$list_data){
                    showMessage("请先添加评估模板！",ADMIN_SITE_URL.'/index.php?act=assessment&op=index&member_id='.$member_id);
                }
                $data = array();
                foreach($list_data as $rows_t){
                    
                    foreach($list as $rows){
                        if($rows['tid_1'] == $rows_t['tid_1']){
                            $data[$rows['tid_1']]['type_1'] = $rows_t['type_1'];
                            $data[$rows['tid_1']]['tid_1'] = $rows_t['tid_1'];
                            $data[$rows['tid_1']]['member_id'] = $rows_t['member_id'];
                            break;
                        }
                    }
                    
                }
                //查询二级分类 
                $list_two_where['member_id'] = $member_id;
                $list_two_where['addtime'] = intval($_GET['time']);
                $data_two = array();
                $i=0;
                foreach($list as $rows){
                    $list_two_where['ptid'] = $rows['tid_1'];
                    $list_two = $model->where($list_two_where)->select();
                    //print_r($list_two);exit;
                    foreach($list_data as $rows_t1){
                        
                        foreach($list_two as $rows_to){
                            if($rows_to['tid_2'] == $rows_t1['tid_2']){
                                $data[$rows_t1['tid_1']]['data1'][$rows_to['tid_2']]['type_2'] = $rows_t1['type_2'];
                                $data[$rows_t1['tid_1']]['data1'][$rows_to['tid_2']]['tid_2'] = $rows_t1['tid_2'];
                                $data[$rows_t1['tid_1']]['data1'][$rows_to['tid_2']]['member_id'] = $rows_t1['member_id'];
                                $querstion_where['tid_2'] = $rows_t1['tid_2'];
                                $querstion_where['member_id'] = $member_id;
                                $querstion_where['addtime'] = intval($_GET['time']);
                                $data2 = $model->where($querstion_where)->field('id,question,scale,desc_1,desc_5,score')->select();
                                $data[$rows_t1['tid_1']]['data1'][$rows_to['tid_2']]['data2'] = $data2;
                            }
                        }
                    
                    }
                }
                foreach($data as $key=>$rows){
                    foreach($rows['data1'] as  $t_rows){
                        $data[$key]['num'] += count($t_rows['data2']);
                    }
                }
                //计算各级分类有多少个记录
                Tpl::output('list',$data);
            }else{
                showMessage("供应商参数错误！");
            }
            Tpl::showpage('store.assessment.looked','exam_layout');
        }
        /*
         * 
         * 查询管理员可编辑供应商列表
         */
        public function storeOp(){
            //根据后台管理员城市中心ID
            //匹配所有满足在这个城市中心认证过开店的店铺
            $model = Model();
            //$store_list_where['store_state'] = STORE_JOIN_STATE_FINAL;
            $store_list_where['joinin_state'] = STORE_JOIN_STATE_RZSUCCESS;
            $limit = '';
            if($_GET['store_name']){
                $store_list_where['store_name'] = array('like','%'.htmlspecialchars($_GET['store_name']).'%');
            }
            $store_list = $model->table('store_joinin')->field('member_id,member_name,company_name,seller_name,store_name,city_center')
                                ->where($store_list_where)
                                ->order('member_id desc')
                                ->limit($limit)
                                ->page(10)
                                ->select();
            
            Tpl::output('store_name',$_GET['store_name']);
            Tpl::output('store_list',$store_list);
            Tpl::output('page',$model->showpage('2'));
            Tpl::showpage('store.assessment_city.store');
        }
}