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

    private $InquiryInfo = '/impac/restapi/initQuoteData';

    //跟新数据
    private $InquiryUp = '/impac/restapi/updateQuoteInfo';

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
                'pageSize'  =>'10',//每页显示条数	默认为15条
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
                    //处理日期
                    $time_str = substr($vl['desiredAnswerDate'],0,4).'-';
                    $time_str.= substr($vl['desiredAnswerDate'],4,2).'-';
                    $time_str.= substr($vl['desiredAnswerDate'],6,2).' ';
                    $time_str.= substr($vl['desiredAnswerDate'],8,2).':';
                    $time_str.= substr($vl['desiredAnswerDate'],10,2);
                    $new_val = array(
                        'title'     =>$vl['quoteRequestNm'],
                        'state'     =>$vl['status'],
                        'state_id'  =>$vl['statusId'],
                        'type'      =>$vl['quoteRequestType'],
                        'city'      =>$vl['requestOrg'],
                        'time'      =>$time_str,
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
                $this->inquiryPath(array());
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
                        'predict_time'  =>empty($rest_data['data']['deliveryDate']) ? '':substr($rest_data['data']['deliveryDate'],0,4).'-'.substr($rest_data['data']['deliveryDate'],4,2).'-'.substr($rest_data['data']['deliveryDate'],6,2), //预计交货时间
                        'valid_statr'   =>empty($rest_data['data']['expiredTimeFrom']) ? '':substr($rest_data['data']['expiredTimeFrom'],0,4).'-'.substr($rest_data['data']['expiredTimeFrom'],4,2).'-'.substr($rest_data['data']['expiredTimeFrom'],6,2), //有效开始时间
                        'valid_end'     =>empty($rest_data['data']['expiredTimeTo']) ? '':substr($rest_data['data']['expiredTimeTo'],0,4).'-'.substr($rest_data['data']['expiredTimeTo'],4,2).'-'.substr($rest_data['data']['expiredTimeTo'],6,2), //有效结束时间
                        'rate'          =>$rest_data['data']['taxId'], //开票税率
                        'mark'          =>$rest_data['data']['comment'], //备注
                        'currency'      =>$rest_data['data']['currencyNm'], //单位
                        'quoteRequestId'=>$rest_data['data']['quoteRequestId'],
                        'path_name'     =>$rest_data['dataThree'][0]['fileName'],
                        'path_url'      =>$rest_data['dataThree'][0]['uploadPath'],
                    );
                    //写入数据
                    if(!empty($rest_data['data']['jsonResult']) && is_array(json_decode($rest_data['data']['jsonResult'],true))){
                        //将读取到的数据写入文件，以便后续返回
                        $list_data = json_decode($rest_data['data']['jsonResult'],true);
                        $this->inquiryPath($list_data);
                    }
                    Tpl::output('quoteId',$rest_data['data']['quoteId']);
                    Tpl::output('type',$_GET['type']);
                    Tpl::output('sl_list',$rest_data['dataSecond']);
                    Tpl::output('list',$data_info);
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
        //"data" . DS . "upload" . DS .
        $firle_data = $this->upload_image('file');
        $data = array(
            "code"  =>'0',
            "msg"   =>'',
            "data"  =>array(
                "src"=> DS . "data" . DS . "upload" . DS .$firle_data['path'],
                "name"=>$_FILES['file']['name'],
            ),
        );
        echo  json_encode($data);
    }


    //获取物料询价列表数据
    public function getListOp(){
        $page = empty($_GET['page']) ? '1':$_GET['page'];
        $nums = empty($_GET['nums']) ? '20':$_GET['nums'];
        $code = '-1';
        $msg = '请求地址数据错误';
        $new_list = array();
        if(!empty($_GET['id'])){
            //获取供应商的相关数据信息
            if($this->role_id == '03' || $this->role_id == '02') {//校验是否属于认证供应商
                $send_data = array(
                    'quoteRequestId'=>$_GET['id'],
                    'supplierCode'  =>$this->supplier_code,
                );
                //读取数据文件
                $list_data = $this->inquiryPath();
                if(!empty($list_data) && is_array($list_data)){
                    foreach ($list_data as $key=>$val){
                        $data = array(
                            'key'   =>$key+1,
                            'id'    =>$val['quoteRequestProductId'],//唯一ID
                            'name'  =>$val['productName'],//名称
                            'spec'  =>$val['productSpec'],//规格
                            'brand' =>$val['brand'],//品牌
                            'nums'  =>number_format($val['quantity'],0),//数量
                            'umit'  =>$val['unit'],//单位
                            'price' =>number_format($val['quoteUnitPrice'],4),//单价
                            'total' =>number_format($val['quantity']*$val['quoteUnitPrice'],4),
                        );
                        $new_list[] = $data;
                    }
                }
            }
        }
        $new_data = array(
            'code'  => empty($new_list) ? '-1':'0',
            'msg'   => empty($new_list) ? '请求地址数据错误':'success',
            'count' => count($new_list),
            'data'  => $this->pageArrayList($nums,$page,$new_list),
        );
        echo json_encode($new_data);
    }

    //修改单价数据
    public function inquiryQuotationPriceOp(){
        $price = $_POST['price'];
        $id = $_POST['id'];
        if(!empty($price) && !empty($id)){
            //获取老的数据文件
            $arr_new = $this->inquiryPath();
            if(!empty($arr_new) && is_array($arr_new)){
                $new_data = array();
                foreach ($arr_new as $val){
                    if($val['quoteRequestProductId'] == $id){
                        $val['quoteUnitPrice'] = number_format($price,4);
                        $val['quotePrice'] = number_format($price*str_replace(',', '', $val['quantity']),4);
                    }
                    $new_data[] = $val;
                }
                //将跟新的数据重新写入文件
                $this->inquiryPath($new_data);
                echo '1';
                exit;
            }
        }
    }


    //提交数据
    public function inquiryDataOp(){
        $post_data = $_POST;
        if (!empty($post_data) && is_array($post_data)){
            if($this->role_id == '03' || $this->role_id == '02') {//校验是否属于认证供应商
                //校验是否存在物料报价数据为0的数据
                $price_data = $this->inquiryPath();
                $rest = '1';
                if(!empty($price_data) && is_array($price_data)){
                    foreach ($price_data as $val){
                        if($val['quoteUnitPrice'] <= 0){
                            $rest = '-1';
                            break;
                        }
                    }
                }
                if($rest == '1'){
                    $send_data = array(
                        'deliveryDate'      =>empty($post_data['predict_time']) ? "":$post_data['predict_time'], //预计交货日期
                        'expiredTimeFrom'   =>empty($post_data['valid_statr']) ? "":$post_data['valid_statr'], // 有效期间(From)
                        'expiredTimeTo'     =>empty($post_data['valid_end']) ? "":$post_data['valid_end'], // 有效期间(To)
                        'jsonResult'        =>$this->inquiryPath(), // 物料列表
                        'quoteAmount'       =>empty($post_data['quoted_price']) ? "":$post_data['quoted_price'], // 报价金额
                        'quoteRequestId'    =>empty($post_data['quoteRequestId']) ? "":$post_data['quoteRequestId'], //  询价单号
                        'taxId'             =>empty($post_data['taxId']) ? "":$post_data['taxId'], //	税率Id
                        'supplierCode'      =>$this->supplier_code, // 供应商Code
                        'comment'           =>empty($post_data['identity']) ? "":$post_data['identity'], // 备注
                        'attachmentInfo'    =>array(
                            array(
                                'uploadPath'        =>empty($post_data['up_path']) ? "":$post_data['up_path'],
                                'fileName'          =>empty($post_data['up_name']) ? "":$post_data['up_name'],
                            ),
                        ),
                        'operation'         =>empty($post_data['operation']) ? "":$post_data['operation'],
                        'quoteId'           =>empty($post_data['quoteId']) ? "":$post_data['quoteId'],
                    );
                    $url = $this->getSendUrl().$this->InquiryUp;
                    $return_json = WebServiceUtil::getDataByCurl($url, json_encode($send_data), 1);
                    $rest_data = json_decode($return_json,true);
                    if($rest_data['resultCode'] == '0'){
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

    //删除询报价文件
    public function delInquiryPathOp(){
        $path = $_POST['path'];
        if(!empty($path) && file_exists(BASE_ROOT_PATH.DS.$path)){
            //执行删除操作
            //FileUtil::unlinkFile('b/d/3.exe');
            $fileUtil = new FileUtil();
            if($fileUtil->unlinkFile(BASE_ROOT_PATH.DS.$path)){
                echo '1';
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
        if(!file_exists($uploaddir)){
            $fileUtil->createDir(BASE_DATA_PATH . DS . "upload" . DS . "browser");
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


    //数组分页
    private function pageArrayList($count,$page,$array,$order='0'){
        $page=(empty($page)) ? '1':$page; #判断当前页面是否为空 如果为空就表示为第一页面
        $start=($page-1)*$count; #计算每次分页的开始位置
        if($order==1){
            $array=array_reverse($array);
        }
        $totals=count($array);
        //$countpage=ceil($totals/$count); #计算总页面数
        $pagedata=array();
        $pagedata=array_slice($array,$start,$count);
        return $pagedata;  #返回查询数据
    }

    //操作文件数据的读写
    private function inquiryPath($data = array()){
        if(!empty($data)){
            $file_old=fopen($this->path,"w");
            fwrite($file_old,serialize($data));
            fclose($file_old);
        }else{
            $file_new=fopen($this->path,"r");
            $file_read = fread($file_new, filesize($this->path));
            fclose($file_new);
            return unserialize($file_read);
        }
    }
}