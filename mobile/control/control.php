<?php
/**
 * mobile父类
 *
 */



/********************************** 前台control父类 **********************************************/

class mobileControl{

    //客户端类型
    protected $client_type_array = array('android', 'wap', 'wechat', 'ios', 'windows');
    //列表默认分页数
    protected $page = 5;


    public function __construct() {
        Language::read('mobile');

        //分页数处理
        $page = intval($_GET['page']);
        if($page > 0) {
            $this->page = $page;
        }
    }
    //判断是否是第三方供应商
    public function is_third_postOp(){
        if($_SESSION['wap_member_info']['userid'] > 0){
            //判断是否是第三方采购员
            if($_SESSION['identity'] != MEMBER_IDENTITY_FIVE){
                $data['code'] = 1;
                echo json_encode($data);exit;
            }else{
                $data['code'] = 2;
                echo json_encode($data);exit;
            }
        }else{
            $data['code'] = 3;
            echo json_encode($data);exit;
        }
    }
    //获取服务器时间
    public function gettimestamp_postOp(){
            
            $stream = $_POST['photo'];
            //获取扩展名和文件名
            if (preg_match('/(?<=\/)[^\/]+(?=\;)/',$stream,$pregR)) $streamFileType ='.' .$pregR[0];  
            //读取扩展名，如果你的程序仅限于画板上来的，那一定是png，这句可以直接streamFileType 赋值png
            $streamFileRand = date('YmdHis').rand(1000,9999);    
            //产生一个随机文件名（因为你base64上来肯定没有文件名，这里你可以自己设置一个也行）

            //处理base64文本，用正则把第一个base64,之前的部分砍掉
            preg_match('/(?<=base64,)[\S|\s]+/',$stream,$streamForW);
            //这是我自己的一个静态类，输出错误信息的，你可以换成你的程序
            $output_file_news = BASE_ROOT_PATH.'/data/upload/shop/store_joinin';
             if (!file_exists($output_file_news)){
                mkdir($output_file_news);
            }
            function base64_to_jpeg( $base64_string, $output_file ) {
                $ifp = fopen( $output_file, "wb" ); 
                fwrite( $ifp, base64_decode( $base64_string) ); 
                fclose( $ifp ); 
                return( $output_file ); 
            }
            $image = base64_to_jpeg( $streamForW[0], $output_file_news.'/'.$streamFileRand.'.jpg' );
            $data_re['code'] = 1;
            $data_re['picUrl'] = $streamFileRand.'.jpg';
            echo json_encode($data_re);exit;
    }
}

class mobileHomeControl extends mobileControl{
    public function __construct() {
        parent::__construct();
    }

    protected function getMemberIdIfExists()
    {
        $key = $_POST['key'];
        if (empty($key)) {
            $key = $_GET['key'];
        }

        $model_mb_user_token = Model('mb_user_token');
        $mb_user_token_info = $model_mb_user_token->getMbUserTokenInfoByToken($key);
        if (empty($mb_user_token_info)) {
            return 0;
        }

        return $mb_user_token_info['member_id'];
    }
}



class mobileMemberControl extends mobileControl{

    protected $member_info = array();

    public function __construct() {
        parent::__construct();
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if (strpos($agent, "MicroMessenger") && $_GET["act"]=='auto') {	
			$this->appId ="";
			$this->appSecret = "";	
			//$this->appId = C('app_weixin_appid');
			//$this->appSecret = C('app_weixin_secret');;		
        }else{
			$model_mb_user_token = Model('mb_user_token');
			$key = $_POST['key'];
			if(empty($key)) {
				$key = $_GET['key'];
			}
			$mb_user_token_info = $model_mb_user_token->getMbUserTokenInfoByToken($key);
			if(empty($mb_user_token_info)) {
				output_error('请登录', array('login' => '0'));
			}

        $model_member = Model('member');
        $this->member_info = $model_member->getMemberInfoByID($mb_user_token_info['member_id']);

			

			if(empty($this->member_info)) {
				output_error('请登录', array('login' => '0'));
			} else {
				$this->member_info['client_type'] = $mb_user_token_info['client_type'];
				$this->member_info['openid'] = $mb_user_token_info['openid'];
				$this->member_info['token'] = $mb_user_token_info['token'];
				$level_name = $model_member->getOneMemberGrade($mb_user_token_info['member_id']);
				$this->member_info['level_name'] = $level_name['level_name'];
				//读取卖家信息
				$seller_info = Model('seller')->getSellerInfo(array('member_id'=>$this->member_info['member_id']));
				$this->member_info['store_id'] = $seller_info['store_id'];
			}
		}
    }

    public function getOpenId()
    {
        return $this->member_info['openid'];
    }

    public function setOpenId($openId)
    {
        $this->member_info['openid'] = $openId;
        Model('mb_user_token')->updateMemberOpenId($this->member_info['token'], $openId);
    }
}

class mobileSellerControl extends mobileControl{

    protected $seller_info = array();
    protected $seller_group_info = array();
    protected $member_info = array();
    protected $store_info = array();
    protected $store_grade = array();

    public function __construct() {
        parent::__construct();

        $model_mb_seller_token = Model('mb_seller_token');

        $key = $_POST['key']?$_POST['key']:$_GET['key'];
        if(empty($key)) {
            output_error('请登录', array('login' => '0'));
        }

        $mb_seller_token_info = $model_mb_seller_token->getSellerTokenInfoByToken($key);
        if(empty($mb_seller_token_info)) {
            output_error('请登录', array('login' => '0'));
        }

        $model_seller = Model('seller');
        $model_member = Model('member');
        $model_store = Model('store');
        $model_seller_group = Model('seller_group');

        $this->seller_info = $model_seller->getSellerInfo(array('seller_id' => $mb_seller_token_info['seller_id']));
        $this->member_info = $model_member->getMemberInfoByID($this->seller_info['member_id']);
        $this->store_info = $model_store->getStoreInfoByID($this->seller_info['store_id']);
        $this->seller_group_info = $model_seller_group->getSellerGroupInfo(array('group_id' => $this->seller_info['seller_group_id']));

        // 店铺等级
        if (intval($this->store_info['is_own_shop']) === 1) {
            $this->store_grade = array(
                'sg_id' => '0',
                'sg_name' => '自营店铺专属等级',
                'sg_goods_limit' => '0',
                'sg_album_limit' => '0',
                'sg_space_limit' => '999999999',
                'sg_template_number' => '6',
                'sg_price' => '0.00',
                'sg_description' => '',
                'sg_function' => 'editor_multimedia',
                'sg_sort' => '0',
            );
        } else {
            $store_grade = rkcache('store_grade', true);
            $this->store_grade = $store_grade[$this->store_info['grade_id']];
        }

        if(empty($this->member_info)) {
            output_error('请登录', array('login' => '0'));
        } else {
            $this->seller_info['client_type'] = $mb_seller_token_info['client_type'];
        }
    }
}
