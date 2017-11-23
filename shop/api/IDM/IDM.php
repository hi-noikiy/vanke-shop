<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/11/1
 * Time: 上午9:17
 * IDM认证登陆
 */
include_once(BASE_ROOT_PATH.DS.'webservice.conf.php' );
include_once(BASE_ROOT_PATH.DS.'/core/framework/webService/WebServiceUtil.php' );
function IDM_logon(){
    //获取地址
    if(is_https()){
        $item_url = 'https://mall.vankeservice.com/';
        $idm_url = 'https://siam.vankeservice.com/';
        $client_id = "AUTH_CG";
        $client_secret = "cs3Bvb3M1298tybbe45Yhe3c45PG6as3";
    }else{
        $item_url = 'http://120.77.38.59/';
        $idm_url = 'https://siamtest.vankeservice.com/';
        $client_id = "AUTH_CG";
        $client_secret = "c4eBvb3M1298tybbe45Yhe3c45PG6s2q";
    }
    //获取IDM获取令牌
    $token_code = $_GET['code'];
    if(!empty($token_code)){
        //根据临时林牌获取AccessToken
        $AccessTokenUrl = $idm_url.'oauth2.0/accessTokenByJson';
        $AccessTokenData = array(
            'client_id'=>$client_id,
            'client_secret'=>$client_secret,
            'grant_type'=>'authorization_code',
            'redirect_uri'=>$item_url.'shop/api.php?act=IDM',
            'code'=>$token_code,
        );
        $AccessTokenData = curl_post($AccessTokenUrl,$AccessTokenData);
        if(!empty($AccessTokenData['access_token'])){
            $AccessDataUrl = $idm_url.'oauth2.0/profileByJson';
            list($token_key,$token_val) = explode('=',$AccessTokenData['access_token']);
            $AccessSendData = array(
                $token_key=>$token_val
            );
            $AccessData = curl_post($AccessDataUrl,$AccessSendData);
            if(!empty($AccessData['attributes'])){
                //获取用户数据
                if(!empty($AccessData['attributes']['uid']) && !empty($AccessData['attributes']['employeeNumber'])){
                    $user_str = $AccessData['attributes']['uid']."|".$AccessData['attributes']['employeeNumber'];
                    $uinfo = encrypt($user_str, 'E', 'vanke');
                    header('location: '.$item_url.'shop/index.php?act=connect_wk&uid='.$uinfo);
                }
            }
        }else{
            $str = "<script\>";
            $str.= "alert('获取IDM认证令牌异常，请联系管理员！');";
            $str.= "window.location.href='".$item_url."';";
            $str.= "<\/script>";
            echo $str;exit();
        }
    }else{
        $login_url = $idm_url.'oauth2.0/authorize?client_id='.$client_id.'&redirect_uri='.$item_url.'shop/api.php?act=IDM&response_type=code';
        header("Location:$login_url");
    }

}

function is_https(){
    if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
        return true;
    } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true;
    }
    return false;
}

function curl_post($url,$data){
    log::record4inter("远程服务器的url：".$url, log::INFO);
    log::record4inter("向远程服务器发送的json数据：".json_encode($data), log::INFO);
    $curl = curl_init();
    //设置提交的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);
    //设置post数据
    $post_data = http_build_query($data);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    //执行命令
    $rest_data = curl_exec($curl);
    if (curl_errno($curl)) {
        log::record4inter("Errno(IDM)：".curl_error($curl)."\n\n\n", log::ERR);//捕抓异常
    }
    log::record4inter("远程服务器返回的json数据：".$rest_data."\n\n\n", log::INFO);
    //关闭URL请求
    curl_close($curl);
    if(empty(json_decode($rest_data,true))){
        log::record4inter("远程服务器的url：".$url.".\n"."向远程服务器发送的json数据：".json_encode($data).".\n"."远程服务器返回为空,请检查url是否正确",log::ERR);
    }
    return json_decode($rest_data,true);
}


/**
 *函数名称:encrypt
 *函数作用:加密解密字符串
 *$string   :需要加密解密的字符串
 *$operation:判断是加密还是解密:E:加密   D:解密
 *$key      :加密的钥匙(密匙);
 */
function encrypt($string,$operation,$key=''){
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

IDM_logon();


class Log{

    const SQL       = 'SQL';
    const ERR       = 'ERR';
    const IN_ERR       = 'IN_ERR';
    const INFO       = 'INFO';
    const IN_INFO       = 'IN_INFO';
    const MOBILE_MESSAGE = 'MOBILE_MESSAGE';
    private static $log =   array();

    public static function record($message,$level=self::ERR) {
        $now = @date('Y-m-d H:i:s',time());
        switch ($level) {
            case self::SQL:
                self::$log[] = "[{$now}] {$level}: {$message}\r\n";
                break;
            case self::ERR:
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'err.log';
                $url = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
                $url .= " ( act={$_GET['act']}&op={$_GET['op']} ) ";
                $content = "[{$now}] {$url}\r\n{$level}: {$message}\r\n";
                try{
                    file_put_contents($log_file,$content, FILE_APPEND);
                }catch(Exception $e) {
                }
                break;
        }
    }

    public static function record4inter($message,$level=self::ERR) {
        $now = @date('Y-m-d H:i:s',time());
        switch ($level) {
            case self::SQL:
                self::$log[] = "[{$now}] {$level}: {$message}\r\n";
                break;
            case self::ERR:
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'out_inter_error.log';
                break;
            case self::INFO:
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'out_inter_info.log';
                break;
            case self::IN_ERR:
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'in_inter_error.log';
                break;
            case self::IN_INFO:
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'in_inter_info.log';
                break;
            case self::MOBILE_MESSAGE:
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'mobile_msg_info.log';
                break;
            default :
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'info.log';
                break;

        }
        $url = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
        $url .= " ( act={$_GET['act']}&op={$_GET['op']} ) ";
        $content = "[{$now}] {$url}\r\n{$level}: {$message}\r\n";
        try{
            file_put_contents($log_file,$content, FILE_APPEND);
        }catch(Exception $e) {
            self::record(json_encode($e,true),self::ERR);
        }
    }

    public static function read(){
        return self::$log;
    }
}
