<?php
/**
 * 系统后台公共方法
 *
 * 包括系统后台父类
 *
 ***/


class SystemControl{

	/**
	 * 管理员资料 name id group
	 */
	protected $admin_info;

	/**
	 * 权限内容
	 */
	protected $permission;
	protected function __construct(){
		Language::read('common,layout');
		/**
		 * 验证用户是否登录
		 * $admin_info 管理员资料 name id
		 */
		$this->admin_info = $this->systemLogin();
		if ($this->admin_info['id'] != 1){
			// 验证权限
			$this->checkPermission();
		}
		
		//转码  防止GBK下用ajax调用时传汉字数据出现乱码
		if (($_GET['branch']!='' || $_GET['op']=='ajax') && strtoupper(CHARSET) == 'GBK'){
			$_GET = Language::getGBK($_GET);
		}
	}

	/**
	 * 取得当前管理员信息
	 *
	 * @param
	 * @return 数组类型的返回结果
	 */
	protected final function getAdminInfo(){
		return $this->admin_info;
	}
	
	
	/**
	 * 取得当前管理员信息最近修改密码时间
	 *
	 * @param  @Aletta
	 * @return 密码强制到期时间戳
	 */
	protected final function getExpireTime(){
	    $model = Model();
	    $time_info = $model->table('admin')->field("up_pwd_time")->where(array("admin_id"=>$this->admin_info['id']))->find();
	    return $time_info['up_pwd_time'] + ADMIN_PWD_WHEN_LONG * 24 * 3600;
	}

	/**
	 * 系统后台登录验证
	 *
	 * @param
	 * @return array 数组类型的返回结果
	 */
	protected final function systemLogin(){
		//取得cookie内容，解密，和系统匹配
		$user = unserialize(decrypt(cookie('sys_key'),MD5_KEY));
		if (!key_exists('gid',(array)$user) || !isset($user['sp']) || (empty($user['name']) || empty($user['id']))){
			@header('Location: index.php?act=login&op=login');exit;
		}else {
			$this->systemSetKey($user);
		}
		return $user;
	}

	/**
	 * 系统后台 会员登录后 将会员验证内容写入对应cookie中
	 *
	 * @param string $name 用户名
	 * @param int $id 用户ID
	 * @return bool 布尔类型的返回结果
	 */
	protected final function systemSetKey($user){
		setNcCookie('sys_key',encrypt(serialize($user),MD5_KEY),3600,'',null);
	}

	/**
	 * 验证当前管理员权限是否可以进行操作
	 *
	 * @param string $link_nav
	 * @return
	 */
	protected final function checkPermission($link_nav = null){
		if ($this->admin_info['sp'] == 1) return true;

		$act = $_GET['act']?$_GET['act']:$_POST['act'];
		$op = $_GET['op']?$_GET['op']:$_POST['op'];
		if (empty($this->permission)){
			$gadmin = Model('gadmin')->getby_gid($this->admin_info['gid']);
			$permission = decrypt($gadmin['limits'],MD5_KEY.md5($gadmin['gname']));
			$this->permission = $permission = explode('|',$permission);
		}else{
			$permission = $this->permission;
		}
		//显示隐藏小导航，成功与否都直接返回
		if (is_array($link_nav)){
			if (!in_array("{$link_nav['act']}.{$link_nav['op']}",$permission) && !in_array($link_nav['act'],$permission)){
				return false;
			}else{
				return true;
			}
		}

		//以下几项不需要验证
		$tmp = array('index','dashboard','login','common','cms_base');
		if (in_array($act,$tmp)) return true;
		if (in_array($act,$permission) || in_array("$act.$op",$permission)){
			return true;
		}else{
			$extlimit = array('ajax','export_step1');
			if (in_array($op,$extlimit) && (in_array($act,$permission) || strpos(serialize($permission),'"'.$act.'.'))){
				return true;
			}
			//带前缀的都通过
			foreach ($permission as $v) {
				if (!empty($v) && strpos("$act.$op",$v.'_') !== false) {
					return true;break;
				}
			}
		}
		showMessage(Language::get('nc_assign_right'),'','html','succ',0);
	}

	/**
	 * 取得后台菜单
	 *
	 * @param string $permission
	 * @return
	 */
	protected final function getNav($permission = '',&$top_nav,&$left_nav,&$map_nav){

		$act = $_GET['act']?$_GET['act']:$_POST['act'];
		$op = $_GET['op']?$_GET['op']:$_POST['op'];
		if ($this->admin_info['sp'] != 1 && empty($this->permission)){
			$gadmin = Model('gadmin')->getby_gid($this->admin_info['gid']);
			$permission = decrypt($gadmin['limits'],MD5_KEY.md5($gadmin['gname']));
			$this->permission = $permission = explode('|',$permission);
		}
		Language::read('common');
		$lang = Language::getLangContent();
		$array = require(BASE_PATH.'/include/menu.php');
		$array = $this->parseMenu($array);
		//管理地图
		$map_nav = $array['left'];
		unset($map_nav[0]);

		$model_nav = "<li><a class=\"link actived\" id=\"nav__nav_\" href=\"javascript:;\" onclick=\"openItem('_args_');\"><span>_text_</span></a></li>\n";
		$top_nav = '';

		//顶部菜单
		foreach ($array['top'] as $k=>$v) {
			$v['nav'] = $v['args'];
			$top_nav .= str_ireplace(array('_args_','_text_','_nav_'),$v,$model_nav);
		}
		$top_nav = str_ireplace("\n<li><a class=\"link actived\"","\n<li><a class=\"link\"",$top_nav);

		//左侧菜单
		$model_nav = "
          <ul id=\"sort__nav_\">
            <li>
              <dl>
                <dd>
                  <ol>
                    list_body
                  </ol>
                </dd>
              </dl>
            </li>
          </ul>\n";
		$left_nav = '';
		foreach ($array['left'] as $k=>$v) {
			$left_nav .= str_ireplace(array('_nav_'),array($v['nav']),$model_nav);
			$model_list = "<li nc_type='_pkey_'><a href=\"JavaScript:void(0);\" name=\"item__opact_\" id=\"item__opact_\" onclick=\"openItem('_args_');\">_text_</a></li>";
			$tmp_list = '';

			$current_parent = '';//当前父级key

			foreach ($v['list'] as $key=>$value) {
				$model_list_parent = '';
				$args = explode(',',$value['args']);
				if ($admin_array['admin_is_super'] != 1){
					if (!@in_array($args[1],$permission)){
						//continue;
					}
				}

				if (!empty($value['parent'])){
					if (empty($current_parent) || $current_parent != $value['parent']){
						$model_list_parent = "<li nc_type='parentli' dataparam='{$value['parent']}'><dt>{$value['parenttext']}</dt><dd style='display:block;'></dd></li>";
					}
					$current_parent = $value['parent'];
				}

				$value['op'] = $args[0];
				$value['act'] = $args[1];
				//$tmp_list .= str_ireplace(array('_args_','_text_','_op_'),$value,$model_list);
				$tmp_list .= str_ireplace(array('_args_','_text_','_opact_','_pkey_'),array($value['args'],$value['text'],$value['op'].$value['act'],$value['parent']),$model_list_parent.$model_list);
			}

			$left_nav = str_replace('list_body',$tmp_list,$left_nav);

		}
	}

	/**
	 * 过滤掉无权查看的菜单
	 *
	 * @param array $menu
	 * @return array
	 */
	private final function parseMenu($menu = array()){
		if ($this->admin_info['sp'] == 1) return $menu;
		foreach ($menu['left'] as $k=>$v) {
			foreach ($v['list'] as $xk=>$xv) {
				$tmp = explode(',',$xv['args']);
				//以下几项不需要验证
				$except = array('index','dashboard','login','common');
				if (in_array($tmp[1],$except)) continue;
				if (!in_array($tmp[1],$this->permission) && !in_array($tmp[1].'.'.$tmp[0],$this->permission)){
					unset($menu['left'][$k]['list'][$xk]);
				}
			}
			if (empty($menu['left'][$k]['list'])) {
				unset($menu['top'][$k]);unset($menu['left'][$k]);
			}
		}
		return $menu;
	}

	/**
	 * 取得顶部小导航
	 *
	 * @param array $links
	 * @param 当前页 $actived
	 */
	protected final function sublink($links = array(), $actived = '', $file='index.php'){
		$linkstr = '';
		foreach ($links as $k=>$v) {
			parse_str($v['url'],$array);
			if (!$this->checkPermission($array)) continue;
			$href = ($array['op'] == $actived ? null : "href=\"{$file}?{$v['url']}\"");
			$class = ($array['op'] == $actived ? "class=\"current\"" : null);
			$lang = L($v['lang']);
			$linkstr .= sprintf('<li><a %s %s><span>%s</span></a></li>',$href,$class,$lang);
		}
		return "<ul class=\"tab-base\">{$linkstr}</ul>";
	}

	/**
	 * 记录系统日志
	 *
	 * @param $lang 日志语言包
	 * @param $state 1成功0失败null不出现成功失败提示
	 * @param $admin_name
	 * @param $admin_id
	 */
	protected final function log($lang = '', $state = 1, $admin_name = '', $admin_id = 0){
		if (!C('sys_log') || !is_string($lang)) return;
		if ($admin_name == ''){
			$admin = unserialize(decrypt(cookie('sys_key'),MD5_KEY));
			$admin_name = $admin['name'];
			$admin_id = $admin['id'];
		}
		$data = array();
		if (is_null($state)){
			$state = null;
		}else{
//			$state = $state ? L('nc_succ') : L('nc_fail');
			$state = $state ? '' : L('nc_fail');
		}
		$data['content'] 	= $lang.$state;
		$data['admin_name'] = $admin_name;
		$data['createtime'] = TIMESTAMP;
		$data['admin_id'] 	= $admin_id;
		$data['ip']			= getIp();
		$data['url']		= $_REQUEST['act'].'&'.$_REQUEST['op'];
		return Model('admin_log')->insert($data);
	}

	/**
	 * 添加到任务队列
	 *
	 * @param array $goods_array
	 * @param boolean $ifdel 是否删除以原记录
	 */
	protected function addcron($data = array(), $ifdel = false) {
	    $model_cron = Model('cron');
	    if (isset($data[0])) { // 批量插入
	        $where = array();
	        foreach ($data as $k => $v) {
	            if (isset($v['content'])) {
	                $data[$k]['content'] = serialize($v['content']);
	            }
	            // 删除原纪录条件
	            if ($ifdel) {
	                $where[] = '(type = ' . $data['type'] . ' and exeid = ' . $data['exeid'] . ')';
	            }
	        }
	        // 删除原纪录
	        if ($ifdel) {
	            $model_cron->delCron(implode(',', $where));
	        }
	        $model_cron->addCronAll($data);
	    } else { // 单条插入
	        if (isset($data['content'])) {
	            $data['content'] = serialize($data['content']);
	        }
	        // 删除原纪录
	        if ($ifdel) {
                $model_cron->delCron(array('type' => $data['type'], 'exeid' => $data['exeid']));
	        }
	        $model_cron->addCron($data);
	    }
	}
       /**
     * 后台系统审核商品通过后推送物料数据给采购
     * @param type $input   物料数组
     * @param type $type   code类型，0：失效删除  1：追加更新
     * @return boolean
     */
    public function transProductToYMA($input,$type){
        if(empty($input)){
            Log::record4inter('物料数据推送接口，物料数组为空', log::INFO);
            return false;
        }
         if(empty($type)){
            $type="1";
        }
        $field='product_code'    
              . ',local_description' 
              . ',product_spec'
              . ',brand'
              . ',serialized_item_flag'
              . ',minimum_purchase_quantity'
              . ',minimum_sales_quantity'
              . ',unit_of_measure_purchase_id'
              . ',unit_of_measure_inventory_id'
              . ',vs_price'
              . ',contract_price'
              . ',reference_price'
              . ',supplier_cd'
              . ',product_level'
              . ',product_type'
              . ',to_product_id  as to_product_code'
              . ',deleted_flag';  
        $array = $this->getProductListByIds($input,$field);
        $goods = $this->getProductGcidsByIds($input,'gc_id_1,gc_id_2,gc_id_3');
        $product_category_id = $goods[1].",".$goods[2].",".$goods[3];
        for($i=0;$i<sizeof($input);$i++){
            $array[$i]['product_category_codes']= $product_category_id;
            if( $array[$i]['unit_of_measure_purchase_id']=="" || $array[$i]['unit_of_measure_purchase_id']==null){
                $array[$i]['unit_of_measure_purchase_id']="个";
            }
            if( $array[$i]['unit_of_measure_inventory_id']=="" || $array[$i]['unit_of_measure_inventory_id']==null){
                $array[$i]['unit_of_measure_inventory_id']="个";
            }
        }
        $send  = array(
            'product_json' =>$array
        );
        $send_json = json_encode($send);
        $url =YMA_WEBSERVICE_INSERT_AND_UPDATE_PRODUCT;
        $data_json =WebServiceUtil::getDataByCurl($url, $send_json, 0);
        $data=  json_decode($data_json,true);
        //如果推送失败 则将product的english_description 字段置成-1
        $model = Model();
        $is_error = array();
        if($data['resultCode']!="0"||empty($data)){
            $is_error['english_description']="-1";
        }else{
            $is_error['english_description']="0";
        }
        CommonUtil::insertData2PushLog($data, 0, $send_json, $url, "2");
        $contract_state = array();
        for($i = 0;$i<sizeof($input);$i++){
        $contract_state[$i]  = $model->table('product')->field("sales_description,product_level")->where(array("product_id"=>$input[$i]))->find();
        if($contract_state[$i]['sales_description']!=0||empty($contract_state)){
            if($contract_state[$i]['product_level']=='0'){
                //同时推送给合同系统
                $data2_json = $this->transProductToCONTRACT($input[$i]);
           
                //如果推送失败 则将product的sales_description 字段置成-1
                $data2 = json_decode($data2_json,true);
             
                if($data2['resultCode']=="200"||($data2['resultCode']=="201" && $data2['resultMsg']=="物料编码已存在。")){
                    $is_error['sales_description']="0"; 
                }else {
                    $is_error['sales_description']="-1";
                }
            }else{
                $is_error['sales_description']="0"; 
            }
        }
        
                $model->table("product")->where(array("product_id"=>$input[$i]))->update($is_error);
        }
        
        return $data;
        //return $data['resultMsg'].$data2['resultMsg'];
    }
    /**
     * 通过多个id获取物料list
     * @param type $id     物料编号数组
     * @param type $field  要查询的字段
     * @return type        结果数组
     */
    public function getProductListByIds($id,$field){
        try{
            $array = array();
            $ids = $this->getWhereCondition($id,",");
            if(!empty($ids)){
                $where['product_id'] = array('in',$ids);
                $array = Model()->table('product')->field($field)->where($where)->select();
                return $array;
            }else{
                return null;
            }
        }  catch (Exception $e){
            Log::record4inter("物料编号:".json_encode($id)."异常，找不到对应的物料", log::INFO);
        }
    }
    /**
     * 数组转拼接字符串
     * @param type $id      需要转的数组
     * @param type $String  拼接的符号
     * @return string       字符串
     */
    public function getWhereCondition($id,$String){
        if(!empty($id)){
            $ids=$id[0];
            for($i=1;$i<sizeof($id);$i++){
                $ids=$ids.$String.$id[$i];
            }
        return $ids;
        }else{
            return "";
        }
    }
    /**
     * 
     * @param type $id     物料编号数组
     * @param type $field  要查询的字段
     * @return type        结果数组
     */
    public function getProductGcidsByIds($id,$field){
        try{
            $ids = $id[0];
            if(!empty($ids)){
               $where['product_id'] = array('in',$ids);
                $array = Model()->table('product')->field($field)->where($where)->find();
                //将得到的商品分类id 替换成分类code
                    for($i = 1 ; $i<4;$i++){
                        $code = Model()->table("goods_class")->field("gc_class_code")->where(array("gc_id"=>$array["gc_id_".$i]))->find();
                        $return[$i] =$code['gc_class_code'];
                    }
                return $return;
            }else{
                return null;
            }
        }  catch (Exception $e){
            Log::record4inter("物料编号:".json_encode($id)."异常，找不到对应的商品", log::INFO);
        }
    }
    /**
     * 供应商认证或入住流程审核完成给申请者短信(邮件)提醒
     * @param type $param 页面上的参数数组
     * @param type $joinin_detail_where 获取对应供应商信息的用户id 和城市中心id 
     * @param type $type  "认证"  or "入驻"
     * @param type $sendEmail  true:发送邮件   false:不发送邮件
     */
    public function sendMsg4Review($param,$joinin_detail_where,$type,$sendEmail){
        //这里调用方法向注册的供应商发送邮件
        $model_tpl = Model('mail_templates');
        $supplierInfo = Model()->table('store_joinin')->field('contacts_email,contacts_name,contacts_phone')->where($joinin_detail_where)->find(); 
        //这里调用方法向注册的供应商发送短信
        $tpl_info = $model_tpl->getTplInfo(array('code'=>"send_notice_mobile"));
        //根据前台点击方式选择审核状态
        if($param['joinin_state']==STORE_JOIN_STATE_RZSUCCESS||$param['store_state']==STORE_JOIN_STATE_FINAL){
           $status = '通过';
        }elseif ($param['joinin_state']==STORE_JOIN_STATE_FNO||$param['store_state']==STORE_JOIN_STATE_KDJJ) {
           $status = '拒绝';
        }else{
           $status = '回退';
        } 
        $getMsg =$param['joinin_message'] == "" ? $param['joinin_message_open']:$param['joinin_message'];
        $param_send = array();
        $param_send['site_name'] = C('site_name');
        $param_send['user_name'] = $supplierInfo['contacts_name'];
        $param_send['type'] = $type;
        $param_send['audit_status'] = $status;
        $param_send['message'] = $getMsg;
        $message = ncReplaceText($tpl_info['content'],$param_send);
        $sms = new Sms();
        $result = $sms->send($supplierInfo['contacts_phone'],$message);
        //如果需要发送邮件则传入true
        if($sendEmail){
        $tpl_info = $model_tpl->getTplInfo(array('code'=>"send_notice_email"));
        $param_send['send_time'] = date('Y-m-d H:i:s',time());
        $subject = ncReplaceText($tpl_info['title'],$param_send);
        $message = ncReplaceText($tpl_info['content'],$param_send);
//      使用ssl形式发送邮件
//      $email	= new Email();
//	$result	= $email->send_sys_email($supplierInfo['contacts_email'],$subject,$message);
        $email = new MySendMail();
        $result	= $email->send_sys_email($supplierInfo['contacts_email'],$subject,$message);
        }
    }
    /**
     * 向合同系统推送物料信息 
     * 在给采购推送时调用   一次只推送一个
     * @param type $input 物料编号
     */
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
        
}
