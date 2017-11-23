<?php
/**
 * 微信支付接口类
 * 适用于APP内 WEBVIEW 访问支付
 */

/**
 * 	配置账号信息
 */



/**
 *
 * 微信支付API异常类
 * @author widyhu
 *
 */
class WxPayException extends Exception {
    public function errorMessage()
    {
        return $this->getMessage();
    }
}



/**
用于生成  预支付交易会话ID	prepayid
 */
class wxpay_app{
    const DEBUG = 0;

    protected $config;
    protected $values = array();
    protected $values2 = array();

    const SSLCERT_PATH = '../cert/apiclient_cert.pem';
    const SSLKEY_PATH = '../cert/apiclient_key.pem';
    const CURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
    const CURL_PROXY_PORT = 0;//8080;
    const REPORT_LEVENL = 1;


    public function __construct(){
        $this->config = (object) array(
            'appId' => '',
            'appSecret' => 'secert',
            'partnerId' => '', //mchID
            'apiKey' => '',
            'notifyUrl' => MOBILE_SITE_URL . '/api/payment/wxpay_app/notify_url.php',

            'orderSn' => date('YmdHis'),
            'orderInfo' => 'Test wxpay app pay',
            'orderFee' => 1,
            'orderAttach' => '_',
        );
    }

    public function setConfig($name, $value){
        $this->config->$name = $value;
    }

    public function setConfigs(array $params){
        foreach ($params as $name => $value) {
            $this->config->$name = $value;
        }
    }

    /**
     *
     * 设置参数
     * @param string $key
     * @param string $value
     */
    public function SetData($type, $key, $value){
        if($type ==1 ){
            $this->values[$key] = $value;
        }else{
            $this->values2[$key] = $value;
        }
    }

    /**
     * 获取设置的值
     */
    public function GetValues($type){
        if($type ==1 ){
            return $this->values;
        }else{
            return $this->values2;
        }
    }

    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    function MakeSign($type=1){
        if($type ==1 ){
            //签名步骤一：按字典序排序参数
            ksort($this->values);
            $string = $this->ToUrlParams($type);
            //签名步骤二：在string后加入KEY
            $string = $string . "&key=".$this->config->apiKey;
            //签名步骤三：MD5加密
            $string = md5($string);
            //签名步骤四：所有字符转为大写
            $result = strtoupper($string);
            return $result;
        }else{
            ksort($this->values2);
            $string = $this->ToUrlParams($type);
            $string = $string . "&key=".$this->config->apiKey;
            $string = md5($string);
            $result = strtoupper($string);
            return $result;
        }
    }

    /**
     * 设置签名，详见签名生成算法
     * @param string $value
     **/
    public function SetSign($type){
        if($type ==1 ){
            $sign = $this->MakeSign($type);
            $this->values['sign'] = $sign;
            return $sign;
        }else{
            $sign = $this->MakeSign($type);
            $this->values2['sign'] = $sign;
            return $sign;
        }
    }

    /**
     * 格式化参数格式化成url参数
     */
    public function ToUrlParams($type){
        $buff = "";
        if($type ==1 ){
            foreach ($this->values as $k => $v){
                if($k != "sign" && $v != "" && !is_array($v)){
                    $buff .= $k . "=" . $v . "&";
                }
            }
        }else{
            foreach ($this->values2 as $k => $v){
                if($k != "sign" && $v != "" && !is_array($v)){
                    $buff .= $k . "=" . $v . "&";
                }
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }


    /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public function ToXml($type){
        if($type ==1 ){
            if(!is_array($this->values) || count($this->values) <= 0){
                throw new WxPayException("数组数据异常！");
            }
            $xml = "<xml>";
            foreach ($this->values as $key=>$val){
                if (is_numeric($val)){
                    $xml.="<".$key.">".$val."</".$key.">";
                }else{
                    $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
                }
            }
        }else{
            if(!is_array($this->values2) || count($this->values2) <= 0){
                throw new WxPayException("数组数据异常！");
            }
            $xml = "<xml>";
            foreach ($this->values2 as $key=>$val){
                if (is_numeric($val)){
                    $xml.="<".$key.">".$val."</".$key.">";
                }else{
                    $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
                }
            }
        }

        $xml.="</xml>";
        return $xml;
    }

    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml  需要post的xml数据
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     * @throws WxPayException
     */
    public  function postXmlCurl($xml, $url, $useCert = false, $second = 30){
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        //如果有配置代理这里就设置代理
        if(self::CURL_PROXY_HOST != "0.0.0.0"  && self::CURL_PROXY_PORT != 0){
            curl_setopt($ch,CURLOPT_PROXY, self::CURL_PROXY_HOST);
            curl_setopt($ch,CURLOPT_PROXYPORT, self::CURL_PROXY_PORT);
        }
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, self::SSLCERT_PATH);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, self::SSLKEY_PATH);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            echo "curl出错，错误码:    $error";
            //throw new WxPayException("curl出错，错误码:$error");
        }
    }


    /**
     * 获取毫秒级别的时间戳
     */
    public function getMillisecond(){
        //获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }

    public function xmlToArray($xml){
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    public function getNonceStr($length = 32){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }


    /*
     * 统一下单生成prepayid , 封装数据提交给APP, 并响应APP回调
     * */
    public function paymentHtml(){
        $this->SetData(1, 'appid', $this->config->appId); // 微信开放平台审核通过的应用APPID
        $this->SetData(1, 'mch_id', $this->config->partnerId); // 微信支付分配的商户号  MCHID
        $this->SetData(1, 'body', $this->config->orderInfo);   //商品描述  Ipad mini  16G  白色	商品或支付单简要描述
        $this->SetData(1, 'out_trade_no', $this->config->orderSn); // 商户订单号,  商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $this->SetData(1, 'total_fee', $this->config->orderFee);  //订单总金额，单位为分，详见支付金额
        $this->SetData(1, 'spbill_create_ip', $_SERVER['REMOTE_ADDR']);  //123.12.12.123	用户端实际ip
        $this->SetData(1, 'notify_url', $this->config->notifyUrl); //通知地址,  http://www.weixin.qq.com/wxpay/pay.php	接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        $this->SetData(1, 'trade_type', 'APP'); //交易类型
        $this->SetData(1, 'attach', $this->config->orderAttach);
        $nonce_str = $this->getNonceStr();
        $this->SetData(1, 'nonce_str', $nonce_str); //随机字符串，不长于32位。推荐随机数生成算法,

        $this->SetSign(1);  //签名
        $xml = $this->ToXml(1);

        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $timeOut = 6;
        $response = $this->postXmlCurl($xml, $url, false, $timeOut);

        $response_array = $this->xmlToArray($response); //将微信返回转成数组

        $this->SetData(2, 'appid', $this->config->appId);
        $this->SetData(2, 'partnerid', $this->config->partnerId);
        $this->SetData(2, 'prepayid', $response_array['prepay_id']);
        $this->SetData(2, 'package', 'Sign=WXPay');
        $nonce_str = $this->getNonceStr();
        $this->SetData(2, 'noncestr', $nonce_str);
        $this->SetData(2, 'timestamp', time());

        $this->SetSign(2);  //签名
        $app_values = $this->GetValues(2);
        $app_values['order_id'] = $this->config->orderSn;
        $jsonParams = json_encode($app_values);

        if($this->config->orderAttach == 'r'){
            $jumpUrl =  WAP_SITE_URL . '/tmpl/member/order_list.html';
        }elseif ($this->config->orderAttach == 'v'){
            $jumpUrl =  WAP_SITE_URL . '/tmpl/member/vr_order_list.html';
        }else{
            $jumpUrl =  WAP_SITE_URL . '/tmpl/member/order_list.html?ordertype=other';
        }
        
        return <<<EOB
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-type" content="text/html;charset=utf-8" />
<title>微信安全支付</title>
</head>
<body>
正在加载…
<br/>
<span id="txt"></span>
<script>
function theBtnOnClicked(){
	var timestamp1 = Date.parse(new Date());
	document.getElementById("txt").innerHTML=timestamp1;
}
//调用java方法	
javaObject.javaDoIt('$jsonParams');
function app_return(order,result){
    switch(result){
    case '0': 
        msg='支付成功'; window.location.href='$jumpUrl'; break;
    case '-1':
        msg='支付错误'; alert('支付错误'); history.go(-1); break;
    case '-2':
        msg='支付取消'; alert('支付取消'); history.go(-1); break;
    default:
        msg='未知错误'; alert('未知错误');  history.go(-1); break;
    }
	//document.getElementById("txt").innerHTML='orderid:'+ order + ' result:' + msg+result;
}
</script>
</body>
</html>
EOB;
    }

    public function notify(){
        try {
            $data = $this->onNotify();
            $resultXml = $this->arrayToXml(array(
                'return_code' => 'SUCCESS',
            ));

            if (self::DEBUG) {
                file_put_contents(__DIR__ . '/log.txt', var_export($data, true), FILE_APPEND | LOCK_EX);
            }

        } catch (Exception $ex) {

            $data = null;
            $resultXml = $this->arrayToXml(array(
                'return_code' => 'FAIL',
                'return_msg' => $ex->getMessage(),
            ));

            if (self::DEBUG) {
                file_put_contents(__DIR__ . '/log_err.txt', $ex . PHP_EOL, FILE_APPEND | LOCK_EX);
            }

        }

        return array(
            $data,
            $resultXml,
        );
    }

    public function onNotify()
    {
        $d = $this->xmlToArray(file_get_contents('php://input'));

        if (empty($d)) {
            throw new Exception(__METHOD__);
        }

        if ($d['return_code'] != 'SUCCESS') {
            throw new Exception($d['return_msg']);
        }

        if ($d['result_code'] != 'SUCCESS') {
            throw new Exception("[{$d['err_code']}]{$d['err_code_des']}");
        }

        if (!$this->verify($d)) {
            throw new Exception("Invalid signature");
        }

        return $d;
    }

    public function verify(array $d)
    {
        if (empty($d['sign'])) {
            return false;
        }

        $sign = $d['sign'];
        unset($d['sign']);

        return $sign == $this->sign($d);
    }

    public function sign(array $data)
    {
        ksort($data);

        $a = array();
        foreach ($data as $k => $v) {
            if ((string) $v === '') {
                continue;
            }
            $a[] = "{$k}={$v}";
        }

        $a = implode('&', $a);
        $a .= '&key=' . $this->config->apiKey;

        return strtoupper(md5($a));
    }
    
    public function arrayToXml(array $data)
    {
        $xml = "<xml>";
        foreach ($data as $k => $v) {
            if (is_numeric($v)) {
                $xml .= "<{$k}>{$v}</{$k}>";
            } else {
                $xml .= "<{$k}><![CDATA[{$v}]]></{$k}>";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
}
?>