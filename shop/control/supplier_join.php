<?php
/**
 * 商家入驻
 *
 *
 *
 ***/




class supplier_joinControl extends SupplierControl {

    //获取到当前登陆用户信息
    private $memberData = array();

    //供应商的商户信息
    private $supplierData = array();


    private $member_id;


    private $model;

    private $ptURoleId;

    private $img_path = DS.DIR_UPLOAD.DS.ATTACH_PATH.DS.'store_joinin'.DS;

    private $joinLog = array();



    public function __construct(){
        parent::__construct();
        Tpl::setLayout('store_joinin_layout');
        $this->model = Model();
        $this->ptURoleId = MEMBER_IDENTITY_ONE;
        $this->member_id = $_SESSION['member_id'];
        $this->memberData = $this->model->table('member')->where("member_id = '".$this->member_id."'")->find();
        $this->supplierData = $this->model->table('supplier')->where("member_id = '".$this->member_id."'")->find();
        $this->joinLog = $this->model->table('store_joinin')->where("member_id = '".$this->member_id."'")->find();
        Tpl::output('join_type',$this->joinLog['joinin_state']);
        if($_GET['op'] == 'index'){
            $step = !empty($_GET['step']) ? $_GET['step']:'agreement';
            //获取当前用户的状态
            if($this->memberData['role_id'] == $this->ptURoleId && !empty($this->supplierData)){
                //根据认证状态进行判定
                if($this->supplierData['supplier_state'] == '1'){
                    if(method_exists(__CLASS__,$step)){
                        if( empty($this->joinLog) || $this->joinLog['joinin_state'] == STORE_JOIN_STATE_NEW || $this->joinLog['joinin_state'] == STORE_JOIN_STATE_CALLBACK){
                            self::$step();
                        }
                        if( $this->joinLog['joinin_state']== STORE_JOIN_STATE_EMAIL){
                            self::email();
                        }
                    }
                }
            }
        }
    }

    public function indexOp(){
        Tpl::showpage('join_index');
    }

    //基本协议
    private function agreement(){
        $model_document = Model('document');
        $document_info = $model_document->getOneByCode('open_store');
        Tpl::output('agreement', $document_info['doc_content']);
        Tpl::showpage('join_agreement');
    }

    //公司基本信息
    private function company(){
        if($this->joinLog['joinin_state'] == STORE_JOIN_STATE_CALLBACK) {
            $companyCity = array(
                'province'=>empty($this->supplierData['company_province_id']) ? "":substr ( $this->supplierData['company_province_id'] , 0, -4)."0000",
                'city'=>empty($this->supplierData['company_province_id']) ? "":substr ( $this->supplierData['company_province_id'] , 0, -2)."00",
                'county'=>empty($this->supplierData['company_province_id']) ? "":$this->supplierData['company_province_id'],
            );
            //解析处理电话号码
            $company_phone_data = array();
            if (!empty($this->supplierData['company_phone'])) {
                $tell_data = explode("-", $this->supplierData['company_phone']);
                $company_phone_data['area_code'] = strlen($tell_data[0]) <= 4 ? $tell_data[0] : "";
                $company_phone_data['tell_num'] = strlen($tell_data[0]) <= 4 ? $tell_data[1] : $tell_data[0];
                $company_phone_data['extension'] = empty($tell_data[2]) ? '' : $tell_data[2];
            }
            Tpl::output('city_id', $this->joinLog['city_center']);
            Tpl::output('city_list', $companyCity);
            Tpl::output('company_phone', $company_phone_data);
            Tpl::output('supplier', $this->supplierData);
        }
        $stepKey = $this->getRandomString(9);
        $stepstring = $this->encrypt("1|".time(), 'E', $stepKey);
        Tpl::output('step_key',$stepKey);
        Tpl::output('step_str',$stepstring);
        Tpl::showpage('join_company');
    }

    //公司营业信息
    private function business(){
        if($this->joinLog['joinin_state'] == STORE_JOIN_STATE_CALLBACK){
            $businessCity = array(
                'province'=>empty($this->supplierData['business_licence_city_code']) ? "":substr ( $this->supplierData['business_licence_city_code'] , 0, -4)."0000",
                'city'=>empty($this->supplierData['business_licence_city_code']) ? "":substr ( $this->supplierData['business_licence_city_code'] , 0, -2)."00",
                'county'=>empty($this->supplierData['business_licence_city_code']) ? "":$this->supplierData['business_licence_city_code'],
            );
            $path = array(
                'business'      =>empty($this->supplierData['business_licence_number_electronic']) ? "":$this->img_path.$this->supplierData['business_licence_number_electronic'],
                'organization'  =>empty($this->supplierData['organization_code_electronic']) ? "":$this->img_path.$this->supplierData['organization_code_electronic'],
                'registration'  =>empty($this->supplierData['tax_registration_certificate_electronic']) ? "":$this->img_path.$this->supplierData['tax_registration_certificate_electronic'],
            );
            Tpl::output('city_list', $businessCity);
            Tpl::output('path', $path);
            Tpl::output('supplier', $this->supplierData);
        }
        $stepKey = $this->getRandomString(9);
        $stepstring = $this->encrypt("2|".time(), 'E', $stepKey);
        Tpl::output('step_key',$stepKey);
        Tpl::output('step_str',$stepstring);
        Tpl::showpage('join_business');
    }

    //银行基础信息
    private function bank(){
        if($this->joinLog['joinin_state'] == STORE_JOIN_STATE_CALLBACK) {
            //获取用户的银行信息数据
            $where = "supplier_id = '" . $this->supplierData['id'] . "' and member_id = '" . $this->memberData['member_id'] . "'";
            $accountBank = $this->model->table('supplier_account_bank')->where($where)->find();
            if(!empty($accountBank['city_code'])){
                $accountBank['account_province']= substr ( $accountBank['city_code'] , 0, -4)."0000";
                $accountBank['account_city']    = substr ( $accountBank['city_code'] , 0, -2)."00";
                $accountBank['account_county']  = $accountBank['city_code'];
            }
            $accountBank['account_path'] = empty($accountBank['bank_licence_electronic']) ? "":$this->img_path . $accountBank['bank_licence_electronic'];

            //结算信息
            $settlementBank = $this->model->table('supplier_settlement_bank')->where($where)->find();
            if(!empty($settlementBank['city_code'])){
                $settlementBank['settlement_province']= substr ( $settlementBank['city_code'] , 0, -4)."0000";
                $settlementBank['settlement_city']    = substr ( $settlementBank['city_code'] , 0, -2)."00";
                $settlementBank['settlement_county']  = $settlementBank['city_code'];
            }
            Tpl::output('account_bank', $accountBank);
            Tpl::output('settlement_bank', $settlementBank);
        }
        $stepKey = $this->getRandomString(9);
        $stepstring = $this->encrypt("3|".time(), 'E', $stepKey);
        Tpl::output('step_key',$stepKey);
        Tpl::output('step_str',$stepstring);
        Tpl::showpage('join_bank');
    }

    //验证邮箱
    private function email(){
        //检查邮箱认证状态
        if($this->joinLog['joinin_state'] == STORE_JOIN_STATE_EMAIL) {
            $log_where = "member_id = '".$this->memberData['member_id']."' and type = '2' and state = '1' and code = '1'";
            $email_log = $this->model->table('email_log')->where($log_where)->find();
            $stepstring = $this->encrypt("return|".time(), 'E', 'return');
            Tpl::output('email_type',empty($email_log) ? '1':'2');
            Tpl::output('break',urlencode($stepstring));
            Tpl::showpage('join_email');
        }else{
            @header('location: /shop/index.php');
        }
    }

    //回退修改数据  STORE_JOIN_STATE_CALLBACK
    public function breakOp(){
        if($this->joinLog['joinin_state'] == STORE_JOIN_STATE_EMAIL){
            $join_where = "member_id = '".$this->memberData['member_id']."'";
            $this->model->table('store_joinin')->where($join_where)->update(array('joinin_state'=>STORE_JOIN_STATE_CALLBACK));
            @header('location: /shop/index.php?act=supplier_join&step=bank');
        }
    }

    //跟新公司基本信息数据
    public function companyOp(){
        $restData = array('code'=>'-1','msg'=>'');
        if(!empty($_POST) && !empty($this->supplierData)){
            //校验步骤是否合法
            list($step,$times) = explode('|',$this->encrypt($_POST['step_str'], 'D', $_POST['step_key']));
            if(!empty($step)){
                $this->model->beginTransaction();
                $tel = !empty($_POST['area_code']) ? $_POST['area_code']."-":"";
                $tel.= !empty($_POST['tell_num']) ? $_POST['tell_num']:"";
                $tel.= !empty($_POST['extension']) ? "-".$_POST['extension']:"";
                $city = array(
                    'province'  =>$_POST['province'],
                    'city'      =>$_POST['city'],
                    'county'    =>$_POST['county'],
                );
                $data = array(
                    'member_name'           =>$this->memberData['member_name'],//店主用户名
                    'company_name'          =>$_POST['company_name'],//公司名称
                    'company_province_id'   =>$_POST['county'],//所在地省ID
                    'company_address'       =>$this->cityData($city),//公司地址
                    'company_address_detail'=>$_POST['address'],//公司详细地址
                    'company_phone'         =>$tel,//公司电话
                    'company_employee_count'=>$_POST['employee_count'],//员工总数
                    'company_registered_capital'=>$_POST['registered_capital'],//注册资金（单位/万元）
                    'contacts_name'         =>$_POST['contacts_name'],//联系人姓名
                    'contacts_phone'        =>$_POST['contacts_phone'],
                    'contacts_email'        =>$_POST['contacts_email'],
                    'legal_person'          =>$_POST['legal_person'],
                );
                $where = "id = '".$this->supplierData['id']."' and member_id = '".$this->memberData['member_id']."'";
                $rest = $this->model->table('supplier')->where($where)->update($data);
                if($rest){
                    if(!empty($this->joinLog)){
                        $join_where = "member_id = '".$this->memberData['member_id']."'";
                        $this->model->table('store_joinin')->where($join_where)->update(array('city_center'=>intval($_POST['city_center'])));
                    }else{
                        $join_param = array(
                            'member_id'      =>$this->memberData['member_id'],
                            'member_name'    =>$this->memberData['member_name'],
                            'seller_name'    =>$this->memberData['member_name'],
                            'city_center'    =>intval($_POST['city_center']),
                            'joinin_state'   =>STORE_JOIN_STATE_NEW,
                        );
                        $this->model->table('store_joinin')->insert($join_param);
                    }
                    //联系人数据插入
                    $contacts_data = $this->model->table('supplier_information')->where("member_id = '".$this->member_id."' and join_city = '".$_POST['city_center']."'")->find();
                    $contacts_up = array(
                        'join_city'             =>intval($_POST['city_center']),
                        'city_contacts_name'    =>$_POST['contacts_name'],
                        'city_contacts_phone'   =>$_POST['contacts_phone'],
                    );
                    //处理城市差异数据信息
                    if(!empty($contacts_data)){
                        $this->model->table('supplier_information')->where("id = '".$contacts_data['id']."'")->update($contacts_up);
                    }else{
                        $contacts_up['member_id'] = $this->memberData['member_id'];
                        $this->model->table('supplier_information')->insert($contacts_up);
                    }
                    $log_where = "member_id = '".$this->memberData['member_id']."' and type != '2' and state = '1'";
                    $email_log = $this->model->table('email_log')->where($log_where)->find();
                    if(!empty($email_log)){
                        $this->model->table('email_log')->where("id = '".$email_log['id']."'")->update(array('email'=>$_POST['contacts_email']));
                    }else{
                        $emailData = array(
                            'member_id'=>$this->memberData['member_id'],
                            'email'=>$_POST['contacts_email'],
                        );
                        $this->model->table('email_log')->insert($emailData);
                    }
                }
                $restData['code'] = $rest ? $this->model->commit():$this->model->rollback();
                $restData['code'] = $rest ? "1":"-1";
            }
        }
        echo json_encode($restData);
    }

    public function businessOp(){
        $restData = array('code'=>'-1','msg'=>'');
        if(!empty($_POST) && !empty($this->supplierData)){
            //校验步骤是否合法
            list($step,$times) = explode('|',$this->encrypt($_POST['step_str'], 'D', $_POST['step_key']));
            if(!empty($step)){
                $data = array(
                    'business_licence_number'   =>$_POST['licence_number'],//营业执照号
                    'business_licence_address'  =>$this->cityData(array('province'=>$_POST['licence_province'],'city'=>$_POST['licence_city'],'county'=>$_POST['licence_county'])),//营业执所在地
                    'business_licence_city_code'=>$_POST['licence_county'],
                    'business_licence_start'    =>$_POST['licence_start'],//营业执照有效期开始
                    'business_licence_end'      =>$_POST['licence_end'],//营业执照有效期结束
                    'business_sphere'           =>$_POST['licence_sphere'],//法定经营范围
                    'business_licence_number_electronic'=>$_POST['business_new'],//营业执照电子版
                    'is_taxpayer'               =>empty($_POST['is_taxpayer']) ? "2":"1",//是否一般纳税人
                    'is_therea'                 =>empty($_POST['is_therea']) ? "2":"1",//是否是三证合一（1：是，2：否）

                    //组织机构数据
                    'organization_code'         =>$_POST['organization_code'],//组织机构代码
                    'organization_code_electronic'=>$_POST['organization_new'],//组织机构代码电子版

                    //税务登记证数据
                    'tax_registration_certificate'=>$_POST['registration_code'],//税务登记证号
                    'taxpayer_id'               =>$_POST['taxpayer_code'],//纳税人识别号
                    'tax_registration_certificate_electronic'=>$_POST['registration_new'],//税务登记证号电子版
                );
                $where = "id = '".$this->supplierData['id']."' and member_id = '".$this->memberData['member_id']."'";
                $rest = $this->model->table('supplier')->where($where)->update($data);
                $restData['code'] = $rest ? "1":"-1";
            }
        }
        echo json_encode($restData);
    }


    public function bankOp(){
        $restData = array('code'=>'-1','msg'=>'');
        if(!empty($_POST) && !empty($this->supplierData)){
            //校验步骤是否合法
            list($step,$times) = explode('|',$this->encrypt($_POST['step_str'], 'D', $_POST['step_key']));
            if(!empty($step)){
                $this->model->beginTransaction();
                $information = array();
                $where =  "supplier_id = '".$this->supplierData['id']."' and member_id = '".$this->memberData['member_id']."'";
                $accountList = $this->model->table('supplier_account_bank')->where($where)->find();
                $accountData = array(
                    'account_name'      =>$_POST['account_names'],
                    'account_number'    =>$_POST['account_number'],
                    'bank_name'         =>$_POST['account_bank_name'],
                    'bank_branch_name'  =>$_POST['account_branch_name'],
                    'bank_branch_code'  =>$_POST['account_branch_code'],
                    'bank_address'      =>$this->cityData(array('province'=>$_POST['account_province'],'city'=>$_POST['account_city'],'county'=>$_POST['account_county'])),
                    'bank_licence_electronic'=>$_POST['account_new'],
                    'is_settlement'     =>empty($_POST['is_settlement']) ? "2":"1",
                    'city_code'         =>$_POST['account_county']
                );

                if(empty($accountList)){
                    $accountData['member_id'] = $this->memberData['member_id'];
                    $accountData['supplier_id'] = $this->supplierData['id'];
                    $rest_account = $this->model->table('supplier_account_bank')->insert($accountData);
                }else{
                    $rest_account = $this->model->table('supplier_account_bank')->where($where)->update($accountData);
                }
                //绑定开户行
                $information['account'] = serialize($accountData);

                //结算账户信息数据
                $settlementList = $this->model->table('supplier_settlement_bank')->where($where)->find();
                if(empty($_POST['is_settlement'])){
                    $city_string = $this->cityData(array('province'=>$_POST['settlement_province'],'city'=>$_POST['settlement_city'],'county'=>$_POST['settlement_county']));
                }else{
                    $city_string = $this->cityData(array('province'=>$_POST['account_province'],'city'=>$_POST['account_city'],'county'=>$_POST['account_county']));
                }
                $settlementData = array(
                    'settlement_name'=>empty($_POST['is_settlement']) ? $_POST['settlement_name']:$_POST['account_names'],
                    'settlement_number'=>empty($_POST['is_settlement']) ? $_POST['settlement_number']:$_POST['account_number'],
                    'bank_name'=>empty($_POST['is_settlement']) ? $_POST['settlement_bank_name']:$_POST['account_bank_name'],
                    'bank_branch_name'  =>empty($_POST['is_settlement']) ? $_POST['settlement_branch_name']:$_POST['account_branch_name'],
                    'bank_branch_code'  =>empty($_POST['is_settlement']) ? $_POST['settlement_branch_code']:$_POST['account_branch_code'],
                    'bank_address'      =>$city_string,
                    'city_code'         =>empty($_POST['is_settlement']) ? $_POST['settlement_county']:$_POST['account_county'],
                );

                if(empty($settlementList)){
                    $settlementData['member_id'] = $this->memberData['member_id'];
                    $settlementData['supplier_id'] = $this->supplierData['id'];
                    $rest_settlement = $this->model->table('supplier_settlement_bank')->insert($settlementData);
                }else{
                    $rest_settlement = $this->model->table('supplier_settlement_bank')->where($where)->update($settlementData);
                }

                //绑定开户行
                $information['settlement'] = serialize($settlementData);

                $information['state_type'] = 1;
                $information['account_type'] = 1;
                $information['settlement_type'] = 1;

                //提交数据
                if($rest_account && $rest_settlement){
                    //跟新认证记录数据的信息,更新为邮箱未认证
                    $join_where = "member_id = '".$this->memberData['member_id']."'";
                    $this->model->table('store_joinin')->where($join_where)->update(array('joinin_state'=>STORE_JOIN_STATE_EMAIL));
                    //跟新绑定银行数据
                    if(!empty($information)){
                        $this->model->table('supplier_information')->where("member_id = '".$this->member_id."' and join_city = '".$this->joinLog['city_center']."'")->update($information);
                    }
                    $this->model->commit();
                    $restData['code'] = "1";
                }else{
                    $this->model->rollback();
                    $restData['code'] = "-1";
                }

            }
        }
        echo json_encode($restData);
    }

    //上传文件操作
    public function upLoadFirldOp(){
        $firle_data = $this->upload_image('file');
        $data = array(
            "code"  =>'0',
            "msg"   =>'',
            "data"  =>array(
                "src"   => $this->img_path.$firle_data['path'],
                "name"  => $_FILES['file']['name'],
                "paname"=> $firle_data['path'],
                "type"  => $_GET['type'],
            ),
        );
        echo  json_encode($data);
    }

    //检查邮箱地址
    public function checkEmailOp(){
        if(!empty($_POST['email'])){
            $where = "member_email = '".$_POST['email']."' and member_email_bind = '1' and member_id != '".$this->memberData['member_id']."'";
            /*$where = "contacts_email = '".$_POST['email']."' and supplier_state in('2','3') and member_id != '".$this->memberData['member_id']."'";
            $data = $this->model->table('supplier')->where($where)->find();*/
            $data = $this->model->table('member')->where($where)->find();
            $rest = array(
                'code'=>empty($data) ? "1":"-1",
            );
            echo json_encode($rest);
        }
    }

    //发送邮件
    public function sendEmailOp(){
        $rest = array('code'=>'-1','msg'=>'邮箱数据信息有误，请检查');
        $log_where = "member_id = '".$this->memberData['member_id']."' and type != '2' and state = '1'";
        $email_log = $this->model->table('email_log')->where($log_where)->find();
        if($this->joinLog['joinin_state']== STORE_JOIN_STATE_EMAIL && !empty($email_log)){
            $code = $this->getRandomString(12);
            $uid = $this->memberData['member_id'].'|'.$this->supplierData["contacts_email"].'|'.$this->joinLog['city_center'].'|'.time();
            $uidstring = $this->encrypt($uid, 'E', $code);
            $verify_url = SHOP_SITE_URL.'/index.php?act=supplier_join&op=bind_email&uid='.urlencode($uidstring).'&code='.$code;
            $param = array(
                'site_name'=>C('site_name'),
                'user_name'=>$this->memberData['member_name'],
                'verify_url'=>$verify_url
            );
            $model_tpl = Model('mail_templates');
            $tpl_info = $model_tpl->getTplInfo(array('code'=>'bind_email'));
            $sendData = array(
                'to'        =>$this->supplierData["contacts_email"],
                'subject'   =>"供应商认证验证邮箱",
                'mailDetail'=>ncReplaceText($tpl_info['content'],$param),
            );
            $return_json = WebServiceUtil::getDataByCurl(YMA_EMAILRECEPTION, json_encode($sendData), 0);
            $returns = json_decode($return_json,true);
            $logData = array();
            if(!empty($returns) && $returns['resultCode']==0){
                $logData['type'] = "2";
                $logData['code'] = "1";
                $rest['code'] = "1";
                $rest['msg'] = "";
                $join_where = "member_id = '".$this->memberData['member_id']."'";
                $this->model->table('store_joinin')->where($join_where)->update(array('joinin_state'=>STORE_JOIN_STATE_RZ));
            }else{
                $logData['type'] = "3";
                $logData['code'] = "2";
                $rest['code'] = "-1";
                $rest['msg'] = "发送失败,请联系管理员";
            }
            $logData['send_time'] = time();
            $this->model->table('email_log')->where("id = '".$email_log['id']."'")->update($logData);
        }
        echo json_encode($rest);
    }


    //邮箱认证
    public function bind_emailOp(){
        if(!empty($_GET['uid']) && !empty($_GET['code'])){
            list($member,$email,$city,$snedTime) = explode('|',$this->encrypt($_GET['uid'], 'D', $_GET['code']));
            if(!empty($member) && !empty($email) && !empty($city) && !empty($snedTime) ){
                if($email == $this->supplierData['contacts_email']){
                    if($member == $this->member_id){
                        if( $snedTime <= ( time()+24*3600 ) ){
                            $this->model->table('store_joinin')->where("member_id = '".$this->member_id."'")->update(array('joinin_state'=>STORE_JOIN_STATE_RZ));
                            $log_where = "member_id = '".$this->member_id."' and type = '2' and state = '1' and code = '1'";
                            Model()->table('email_log')->where($log_where)->update(array('state'=>'2','u_time'=>time()));
                            $this->model->table('member')->where(array('member_id'=>$this->member_id))->update(array('member_email'=>$this->supplierData['contacts_email'],'member_email_bind'=>'1'));
                            showMessage('邮箱验证成功','index.php?act=supplier_join');
                        }else{
                            showMessage('邮件已经过期！请重新发送',SHOP_SITE_URL,'html','error');
                        }
                    }else{
                        showMessage('供应商数据有误，请联系管理员',SHOP_SITE_URL,'html','error');
                    }
                }else{
                    showMessage('邮箱数据有误，请联系管理员',SHOP_SITE_URL,'html','error');
                }
            }else{
                showMessage('非法请求',SHOP_SITE_URL,'html','error');
            }
        }
    }


    //删除文件
    public function delPathOp(){
        $path = $_POST['path'];
        if (!empty($path) && file_exists(BASE_ROOT_PATH . $path)) {
            //执行删除操作
            //FileUtil::unlinkFile('b/d/3.exe');
            $fileUtil = new FileUtil();
            if ($fileUtil->unlinkFile(BASE_ROOT_PATH . $path)) {
                echo '1';
            } else {
                echo '2';
            }
        }
    }


    private function upload_image($file) {
        $pic_name = '';
        $upload = new UploadFile();
        $fileUtil = new FileUtil();
        $uploaddir = ATTACH_PATH.DS.'store_joinin';
        if(!file_exists($uploaddir)){
            $fileUtil->createDir($uploaddir);
        }
        $upload->set('default_dir',$uploaddir.DS);
        $upload->set('allow_type',array('jpg','jpeg','gif','png'));
        if (!empty($_FILES[$file]['name'])){
            $result = $upload->upfile($file);
            if ($result){
                $pic_name = $upload->file_name;
                $upload->file_name = '';
            }
        }
        return array(
            'path'=>$pic_name,
            'name'=>$pic_name
        );
    }


    private function cityData($city = array()){
        if(is_array($city)){
            $province = Model()->table("linkage_province")->where("code = '".$city['province']."'")->find();
            $citys = Model()->table("linkage_city")->where("code = '".$city['city']."'")->find();
            $county = Model()->table("linkage_county")->where("code = '".$city['county']."'")->find();
            $data = array(
                $province['city_name'],$citys['city_name'],$county['city_name'],
            );
            return implode(' ',$data);
        }
    }


    /**
     *函数名称:encrypt
     *函数作用:加密解密字符串
     *$string   :需要加密解密的字符串
     *$operation:判断是加密还是解密:E:加密   D:解密
     *$key      :加密的钥匙(密匙);
     */
    private function encrypt($string,$operation,$key=''){
        $key=md5($key);
        $key_length=strlen($key);
        $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
        $string_length=strlen($string);
        $rndkey=$box=array();
        $result='';
        for($i=0;$i<=255;$i++) {
            $rndkey[$i]=ord($key[$i%$key_length]);
            $box[$i]=$i;
        }
        for($j=$i=0;$i<256;$i++) {
            $j=($j+$box[$i]+$rndkey[$i])%256;
            $tmp=$box[$i];
            $box[$i]=$box[$j];
            $box[$j]=$tmp;
        }
        for($a=$j=$i=0;$i<$string_length;$i++) {
            $a=($a+1)%256;
            $j=($j+$box[$a])%256;
            $tmp=$box[$a];
            $box[$a]=$box[$j];
            $box[$j]=$tmp;
            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
        }
        if($operation=='D') {
            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)) {
                return substr($result,8);
            }else{
                return'';
            }
        }else{
            return str_replace('=','',base64_encode($result));
        }
    }


    private function getRandomString($len, $chars=null){
        if (is_null($chars)){
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        }
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }





    /*    $key = getRandomString(9);
    $user_str = $AccessData['attributes']['uid']."|".$AccessData['attributes']['employeeNumber']."|".time();
    $uinfo = encrypt($user_str, 'E', $key);
    list($uid,$sapid,$times) = explode('|',$this->encrypt($id_cord, 'D', $key));
    */



}
