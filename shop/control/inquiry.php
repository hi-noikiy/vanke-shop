<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/3
 * Time: 上午9:22
 * 询报价操作
 * inquiry.php
 *
 */
class inquiryControl extends HomeControl {

    //询报价列表数据接口
    private $InquiryList = '/impac/restapi/getInquiryList';
    
    private $InquiryLists = 'http://10.39.35.152:8080/xl04_war/restapi/getInquiryList';
    
    //获取询报价详情信息接口   
    private $InquiryInfo = '/impac/restapi/initQuoteData';
    
    private $InquiryInfos = 'http://10.39.35.152:8080/xl04_war/restapi/initQuoteData';
    
    //物料详情列表分页接口
    private $ItemlList = '/impac/restapi/getItemData';
    
    private $ItemlLists = 'http://10.39.35.152:8080/xl04_war/restapi/getItemData';
    
    //删除上传文件接口
    private $DeleteFile = '/impac/restapi/delAttachmentInfo';
    
    private $DeleteFiles = 'http://10.39.35.152:8080/xl04_war/restapi/delAttachmentInfo';
      
    //提交跟新数据
    private $InquiryUp = '/impac/restapi/updateQuoteInfo';
    
    private $InquiryUps = 'http://10.39.35.152:8080/xl04_war/restapi/updateQuoteInfo';
           
    //询报价类型
    private $TypeData = array(
        'C005WAITQUOTEANSWER-VS'=> '待报价',
        'C005QUOTEANSWERING-VS' => '报价中',
        'C005QUOTEANSWERED-VS'  => '报价完成',
    );

    //供应商权限
    private $role_id;

    //供应商CODE
    private $supplier_code;

    //物料数据路径
    private $path;
    
    //修改后的物料数据路径
    private $paths;

    public function __construct() {
        parent::__construct();
        //获取供应商的相关数据信息
        $model = Model();
        $member_info = $model->table('member')->where("member_id = '".$_SESSION['member_id']."'")->find();
        if($member_info['role_id'] == '03' || $member_info['role_id'] == '02'){//校验是否属于认证供应商
            $supplier_data = $model->table('supplier')->where("member_id = '".$_SESSION['member_id']."'")->find();
            $this->path = BASE_DATA_PATH . DS . "upload" . DS . "inquiry" . DS . $_SESSION['member_id'] . '.txt';
            $this->role_id = $member_info['role_id'];
            $this->supplier_code = $supplier_data['business_licence_number'];
        }else{
            showMessage('用户权限错误！', 'index.php', 'html', 'error');
        }
    }


    //询报价首页
    public function indexOp(){
        Language::read('home_article_index');
        $lang	= Language::getLangContent();
        Tpl::setLayout('home_layout');
        Tpl::output('index_sign','tender');
        $nav_link = array(
            array('title'=>$lang['homepage'], 'link'=>SHOP_SITE_URL),
            array('title'=>'询价信息',)
        );
        Model('seo')->type('article')->param(array('article_class'=>'询价信息'))->show();
        $tender_name = empty($_GET['tender_name']) ? '':$_GET['tender_name'];
        //获取供应商的相关数据信息
        $model = Model();
        $member_info = $model->table('member')->where("member_id = '".$_SESSION['member_id']."'")->find();
        if($this->role_id == '03' || $this->role_id == '02'){//校验是否属于认证供应商
            Tpl::output('type_data',$this->TypeData);
            Tpl::output('nav_link_list',$nav_link);
            Tpl::showpage('parentIframe.inquiry');
        }
    }

    //获取询价数据
    public function getInquiryListOp(){
        $page = empty($_GET['page']) ? '1':$_GET['page'];
        $inquiry_name = empty($_GET['name']) ? '':$_GET['name'];
        $startDate = empty($_GET['start']) ? '':$_GET['start'];
        $endDate = empty($_GET['end']) ? '':$_GET['end'];
        $status = empty($_GET['status']) ? '':$_GET['status'];
        //获取供应商的相关数据信息
        if($this->role_id == '03' || $this->role_id == '02'){//校验是否属于认证供应商
            $send_data = array(
                'pageNum'   =>$page,//第几页		默认为第1页
                'pageSize'  =>$_GET['nums'],//每页显示条数	默认为15条
                'title'     =>$inquiry_name,//询价标题	默认为空字符串
                'startDate' =>$startDate,//报价截止日期Start  默认为空字符串
                'endDate'   =>$endDate,//报价截止日期End    默认为空字符串
                'status'    =>$status,//状态
                'supplierCode'=>$this->supplier_code,
            );
            //$url = $this->getSendUrl().$this->tenderListShow;
            $url = $this->getSendUrl().$this->InquiryList;
            $return_json = WebServiceUtil::getDataByCurl($url, json_encode($send_data), 1);
            $rest_data = json_decode($return_json,true);
            $new_data = array();
            if(!empty($rest_data['data']) && is_array($rest_data['data'])){
                foreach ($rest_data['data'] as $vl){              
                    $new_val = array(
                        'title'     =>$vl['quoteRequestNm'],
                        'state'     =>$vl['status'],
                        'state_id'  =>$vl['statusId'],
                        'type'      =>$vl['quoteRequestType'],
                        'city'      =>$vl['requestOrg'],
                        'time'      =>$vl['desiredAnswerDate'],
                        'inquiry_id'=>$vl['quoteRequestId'],
                        'operation' =>$vl['operation'],
                        'quote_id'  =>$vl['quoteId'],
                    );
                    $new_data[] = $new_val;
                }
            }
            $list = array(
                'code'  => $rest_data['resultCode'],
                'msg'   => $rest_data['resultMsg'],
                'count' => $rest_data['dataCount'],
                'data'  => $new_data,
            );
            echo json_encode($list);
        }
    }

    //获取询报价详情信息
    public function inquiryInfoOp(){
        if(!empty($_GET['id']) && !empty($_GET['type'])){
            //创建路径地址，初始化数据
            $file_obj = new FileUtil();
            if(!file_exists(BASE_DATA_PATH . DS . "upload" . DS . "inquiry")){
                $file_obj->createDir(BASE_DATA_PATH . DS . "upload" . DS . "inquiry");
            }
            if(!file_exists($this->path)){
                $file_obj->createFile($this->path);
            }else{
                //初始化数据文件
                $this->inquiryPath(array(),$this->path);
            }
            //获取供应商的相关数据信息
            if($this->role_id == '03' || $this->role_id == '02') {//校验是否属于认证供应商
                $send_data = array(
                    'quoteRequestId'=>$_GET['id'],
                    'supplierCode'  =>$this->supplier_code,
                    'operation'     =>$_GET['type'],
                    'quoteId'       =>$_GET['quote']
                );
                $url = $this->getSendUrl().$this->InquiryInfo;
                $return_json = WebServiceUtil::getDataByCurl($url, json_encode($send_data), 1);
                $rest_data = json_decode($return_json,true);
                if(!empty($rest_data['data']) && is_array($rest_data['data'])){
                    $data_info = array(
                        'inquiry_id'    =>$_GET['id'],
                        'quoted_price'  =>$rest_data['data']['quoteAmount'], //报价金额
                        'hope_time'     =>empty($rest_data['data']['desiredDeliveryDate']) ? '':substr($rest_data['data']['desiredDeliveryDate'],0,4).'-'.substr($rest_data['data']['desiredDeliveryDate'],4,2).'-'.substr($rest_data['data']['desiredDeliveryDate'],6,2), //期望到货时间
                        'predict_time'  =>empty($rest_data['data']['deliveryDate']) ? '':$rest_data['data']['deliveryDate'], //预计交货时间
                        'valid_statr'   =>empty($rest_data['data']['expiredTimeFrom']) ? '':substr($rest_data['data']['expiredTimeFrom'],0,4).'-'.substr($rest_data['data']['expiredTimeFrom'],4,2).'-'.substr($rest_data['data']['expiredTimeFrom'],6,2), //有效开始时间
                        'valid_end'     =>empty($rest_data['data']['expiredTimeTo']) ? '':substr($rest_data['data']['expiredTimeTo'],0,4).'-'.substr($rest_data['data']['expiredTimeTo'],4,2).'-'.substr($rest_data['data']['expiredTimeTo'],6,2), //有效结束时间
                        'rate'          =>$rest_data['data']['taxId'], //开票税率
                        'mark'          =>$rest_data['data']['comment'], //备注
                        'quoteRequestId'=>$rest_data['data']['quoteRequestId'],
                        'path_name'     =>$rest_data['data']['attachmentInfo']['fileName'],
                        'path_url'      =>substr($rest_data['data']['attachmentInfo']['uploadPath'],1),
                    );
                    $send_data['totalcount'] = $rest_data['data']['itemCount'];
                    Tpl::output('quoteId',$rest_data['data']['quoteId']);
                    Tpl::output('type',$_GET['type']);
                    Tpl::output('sl_list',$rest_data['taxList']);
                    Tpl::output('list',$data_info);
                    Tpl::output('send_data',$send_data);             
                    Tpl::showpage('parentIframe.inquiry.info');
                }else{
                    echo "请求数据错误";
                }
            }else{
                echo "非法请求";
            }
        }
    }



    //上传文件操作
    public function upLoadFirldOp(){
        $firle_data = $this->upload_image('file');
        $data = array(
            "code"  =>'0',
            "msg"   =>'',
            "data"  =>array(
                "src"=>  'data'. DS . "upload" . DS .$firle_data['path'],
                "name"=>$_FILES['file']['name'],
            ),
        );
        echo  json_encode($data);
    }


    //获取物料询价列表数据
    public function getListOp(){
        $page = empty($_GET['page']) ? '1':$_GET['page'];
        $nums = empty($_GET['nums']) ? '20':$_GET['nums'];
        $quoteId = $_GET['quote'] ? $_GET['quote'] : '';
        $operation	 = $_GET['type']; 
        $id = $_GET['id'];
        $conut = $_GET['count'];
        $code = '-1';
        $msg = '请求地址数据错误';
        $new_list = array();
        if(!empty($_GET['id'])){
                //读取数据文件
                if($this->role_id == '03' || $this->role_id == '02') {//校验是否属于认证供应商
                	$send_data = array(
                			'quoteRequestId'=>$id,
                			'supplierCode'  =>$this->supplier_code,
                			'operation'     =>$operation,
                			'quoteId'       =>$quoteId,
                			'pageNum'       =>$page,
                			'pageSize'      =>$nums
                	);
                	$url = $this->getSendUrl().$this->ItemlList;
                	$return_json = WebServiceUtil::getDataByCurl($url, json_encode($send_data), 1);
                	$rest_data = json_decode($return_json,true);
                	if (!empty($rest_data['itemData'])) {
                		$num = 0;
                		foreach ($rest_data['itemData'] as $item){
                			$num ++;
                			$item_data = array(
                					'key'   =>$num,
                					'id'    =>$item['itemId'],//唯一ID
                					'name'  =>$item['productName'],//名称
                					'spec'  =>$item['productSpec'],//规格
                					'brand' =>$item['brand'],//品牌
                					'nums'  =>number_format($item['quantity'],0),//数量
                					'umit'  =>$item['unit'],//单位
                					'price' =>$item['quoteUnitPrice']?$item['quoteUnitPrice']:number_format(0,4),
                					'total' =>number_format($item['quantity']*$item['quoteUnitPrice'],4)
                			);
                			if (!empty($this->inquiryPath()) && $this->inquiryPath()[0]['type'] !== 1) {
                				foreach ($this->inquiryPath() as $v){
                					if ($v[$id]['itemId'] && $v[$id]['itemId'] == $item['itemId']){
                						$item_data['price'] = $v[$id]['quoteUnitPrice'];
                						$item_data['total'] = number_format($item['quantity']*$v[$id]['quoteUnitPrice'],4);
                					}                					 
                				}
                			}else {
                				$item_data['price'] = $item['quoteUnitPrice'];
                				$item_data['total'] = number_format($item['quantity']*$item['quoteUnitPrice'],4);
                			}            			
                			$item_datas[] = $item_data; 
                		}
                	}
	        $new_data = array(
	            'code'  => empty($item_datas) ? '-1':'0',
	            'msg'   => empty($item_datas) ? '请求地址数据错误':'success',
	            'count' => $conut,
	            'data'  => $item_datas,
	        );
            }
	        echo json_encode($new_data);
        }
    }

    //修改单价数据
    public function inquiryQuotationPriceOp(){
        $price = $_POST['price'];
        $id = $_POST['id'];
        $quoteRequestId = $_GET['quoteRequestId'];
        if(!empty($price) && !empty($id)){
	        $new_data = array(
	        		"$quoteRequestId"=>array(
	        			  'itemId'=>$id,
	        			  'quoteUnitPrice'=>$price
	        		)       		
	        );
	        //读取文件如果有数据替换
	        $file_data = $this->inquiryPath();
	        if (!empty($this->inquiryPath())){
	        	foreach ($file_data as $k=>$v){
	        		if ($v[$quoteRequestId]['itemId'] == $id){
	        			unset($file_data[$k]);
	        		}
	        	}
	        	if (is_array($file_data)) {
	        		$this->inquiryPath(array('type'=>1),$type = 'w');
	        		foreach ($file_data as $vf){
	        			$this->inquiryPath($vf);
	        		}
	        	}
	        	$arr_new = $this->inquiryPath($new_data);
	        }else {
	        	$arr_new = $this->inquiryPath($new_data);        	
	        }
	        echo '1';
	        exit;
        }
    }


    //提交数据
    public function inquiryDataOp(){
        $post_data = $_POST;
        $count = $_GET['count']?$_GET['count']:'';
        $quoteRequestId = $post_data['quoteRequestId'];
        if (!empty($post_data) && is_array($post_data)){
            if($this->role_id == '03' || $this->role_id == '02') {//校验是否属于认证供应商
                //校验是否存在物料报价数据为0的数据
                $price_data = $this->inquiryPath();
                $rest = '1';
                $num = 0;
                $data = array();
                if(!empty($price_data) && is_array($price_data)){
                     foreach ($price_data as $val){
                     	if (empty($val[$post_data['quoteRequestId']])) {
                    			continue;
                     	}
                     	$data[] = $val[$quoteRequestId];
                     	foreach ($val as $key=>$al){
                     		if ($al['quoteUnitPrice'] <= '0') {
                     			 $rest = '-1';
 								 break;
                     		}
                   		}
                    }
                }
                if (empty($post_data['quoteId']) && count($data) != $count) {
                	echo "请检查价格数据,价格不能存在小于或等于0";
                }else {
                	if($rest == '1'){
                		$send_data = array(
                				'deliveryDate'      =>empty($post_data['predict_time']) ? "":$post_data['predict_time'], //预计交货日期
                				'expiredTimeFrom'   =>empty($post_data['valid_statr']) ? "":$post_data['valid_statr'], // 有效期间(From)
                				'expiredTimeTo'     =>empty($post_data['valid_end']) ? "":$post_data['valid_end'], // 有效期间(To)
                				'itemData'          =>$data, // 物料列表
                				'quoteRequestId'    =>empty($post_data['quoteRequestId']) ? "":$post_data['quoteRequestId'], //  询价单号
                				'taxId'             =>empty($post_data['taxId']) ? "":$post_data['taxId'], //	税率Id
                				'supplierCode'      =>$this->supplier_code, // 供应商Code
                				'comment'           =>empty($post_data['identity']) ? "":$post_data['identity'], // 备注
                				'attachmentInfo'    =>array(
                						'uploadPath'        =>empty($post_data['up_path']) ? "":DS.$post_data['up_path'],
                						'fileName'          =>empty($post_data['up_name']) ? "":$post_data['up_name'],
                				),
                				'operation'         =>empty($post_data['operation']) ? "":$post_data['operation'],
                				'quoteId'           =>empty($post_data['quoteId']) ? "":$post_data['quoteId'],
                		);     
                		$url = $this->getSendUrl().$this->InquiryUp;
                		$return_json = WebServiceUtil::getDataByCurl($url, json_encode($send_data), 1);
                		$rest_data = json_decode($return_json,true);
                		if($rest_data['resultCode'] == '0'){
                			//清除文件内容
                			$this->inquiryPath(array('type'=>1),$type = 'w');
                			echo $rest_data['resultCode'];
                		}else{
                			echo "修改数据失败！请重试";
                		}
                	}else{
                		echo "请检查价格数据,价格不能存在小于或等于0";
                	}
                }          
            }
        }
    }
    //删除询报价文件
    public function delInquiryPathOp(){
    	$quoteId = $_GET['quote'];
    	$is_path = $_POST['up_path'];
        $path = BASE_ROOT_PATH.DS.$_POST['path'];
        if(!empty($path) && file_exists($path) && !empty($quoteId)){
            //执行删除操作
        	$url = $this->getSendUrl().$this->DeleteFile;
        	$return_json = WebServiceUtil::getDataByCurl($url, json_encode(array('quoteId'=>$quoteId)), 1);
        	$rest_data = json_decode($return_json,true);
        	//var_dump($rest_data);exit;
            //FileUtil::unlinkFile('b/d/3.exe');删除文件->$fileUtil = new FileUtil();$fileUtil->unlinkFile($path)
            if ($rest_data['resultCode'] == 0 || empty($is_path)){
            	$fileUtil = new FileUtil();
            	if ($fileUtil->unlinkFile($path)) {
            		echo '1';
            	}else{
            		echo '2';
            	}
            }else{
            	echo '2';
            }
        }
    }
    

    //上传报价文件
    private function upload_image($file) {
        $pic_name = '';
        $upload = new UploadFile();
        $fileUtil = new FileUtil();
        $uploaddir = ATTACH_PATH.DS.'inquiry'.DS. date('Y',time()) . DS . date('m-d',time());
		//shop/inquiry/2017/12-05
        if(!file_exists(BASE_DATA_PATH . DS . "upload" . DS . $uploaddir)){
            $fileUtil->createDir(BASE_DATA_PATH . DS . "upload" . DS . $uploaddir);
        }
        $upload->set('default_dir',$uploaddir.DS);
        $upload->set('allow_type',array('zip','rar','7z'));        
        if (!empty($_FILES[$file]['name'])){
            $result = $upload->upfile_all($file);
            if ($result){
                $pic_name = $upload->file_name;
                $upload->file_name = '';
            }
        }
        return array(
            'path'=>$uploaddir.DS.$pic_name,
            'name'=>$pic_name
        );
    }

    //操作文件数据的读写
    private function inquiryPath($data = array(),$type=''){
      if(!empty($data)){
      	if (empty($type)) {
      		$type = 'a';
      	}else{
      		$type = 'w';     		
      	}
            $file_old=fopen($this->path,$type);
            fwrite($file_old,json_encode($data,true).'|');
            fclose($file_old);
        }else{
            $file_new=fopen($this->path,"r");
            $file_read = fread($file_new, filesize($this->path));
            fclose($file_new);
            foreach (explode('|', $file_read) as $v){
            	$json = json_decode($v,true);
            	if (!empty($v)) {
            		if (!empty($json['type'])){
            			continue;
            		}
            		$datas[] = $json;
            	}
            }
            return $datas;
        }
    }

}