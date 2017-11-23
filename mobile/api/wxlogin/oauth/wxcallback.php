<?php 
//require_once(BASE_PATH.DS.'api'.DS.'weixin'.DS.'comm'.DS."config.php");
//require_once(BASE_PATH.DS.'api'.DS.'weixin'.DS.'comm'.DS."utils.php");


require_once(dirname(__FILE__).'/'.'../comm/config.php');
require_once(dirname(__FILE__).'/'.'../comm/utils.php');

function wx_callback()
{
    //debug
    //print_r($_REQUEST);
    //print_r($_SESSION);

    if($_REQUEST['state'] == $_SESSION['state']) //csrf
    {
        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?grant_type=authorization_code"
            . "&appid=" . $_SESSION["appid"]
			//. "&redirect_uri=" . urlencode($_SESSION["callback"])
            . "&secret=" . $_SESSION["appkey"]
			. "&code=" . $_REQUEST["code"];

        $response = get_url_contents($token_url);
       
            $msg = json_decode($response);
            if (isset($msg->errcode))
            {
                echo "<h3>error:</h3>" . $msg->errcode;
                echo "<h3>msg  :</h3>" . $msg->errmsg;
                exit;
            }       

        //debug
        //print_r($params);

        //set access token to session
        $_SESSION["access_token"] =$msg->access_token; //$params["access_token"];
		$_SESSION["openid"] = $msg->openid;
		if(isset($msg->unionid))
		{
			$_SESSION["unionid"] = $msg->unionid;
		}

    }
    else 
    {
        echo("The state does not match. You may be a victim of CSRF.");
    }
}


	//登录成功后的回调地址,主要保存access token he openid
	wx_callback();

	@header('location: http://ymh.ox.pw/wap/tmpl/member/wxlogin.html');
	//@header('location: index.php?act=connectwx');
	exit;
?>
