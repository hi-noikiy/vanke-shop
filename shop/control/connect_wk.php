<?php
//住这儿登录
class connect_wkControl extends BaseHomeControl{
	public function __construct(){
		parent::__construct();
		/**
		 * 初始化测试数据
		 */
		if (!$_GET['uid']){
			showMessage('请先登录IDAM','index.php','html','error');//'系统错误'
		}
		Tpl::output('hidden_nctoolbar', 1);
			Tpl::setLayout('login_layout');
	}
	/**
	 * 首页
	 */
	public function indexOp(){
		/**
		 * 检查登录状态
		 */
           
		if($_SESSION['is_login'] == '1') {
			showDialog('登录成功','index.php?act=member&op=home','succ');
		    //header('location: http://10.0.73.55/shop');	
		}else {
			$this->autologin($_GET['uid']);
		}
	}

	/**
	 * 自动登录
	 */
	public function autologin($id_cord){
		//查询是否已存在手机号
		$model_member	= Model('member');
		//处理返回的字符串
        list($uid,$sapid) = explode('|',$this->encrypt($id_cord, 'D', 'vanke'));
		$member_info = $model_member->getMemberInfo(array('member_name'=>$sapid));
		if (is_array($member_info) && count($member_info)>0){
			if(!$member_info['member_state']){//1为启用 0 为禁用
				showMessage('无权限登录','','html','error');
			}
			$model_member->createSession($member_info);
			showDialog('登录成功','index.php?act=member&op=home','succ');
                        //header('location: http://10.0.73.55/shop');
		}else{
            $member_info = $model_member->getMemberInfo(array('member_name'=>$uid));
            if (is_array($member_info) && count($member_info)>0){
                if(!$member_info['member_state']){//1为启用 0 为禁用
                    showMessage('无权限登录','','html','error');
                }
                $model_member->createSession($member_info);
                showDialog('登录成功','index.php?act=member&op=home','succ');
                //header('location: http://10.0.73.55/shop');
            }
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


}
