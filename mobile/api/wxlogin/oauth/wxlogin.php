<?php
//require_once(BASE_PATH.DS.'api'.DS.'weixin'.DS.'comm'.DS."config.php");
require_once(dirname(__FILE__).'/'.'../comm/config.php');

function weixin_login($appid, $scope, $callback)
{

   $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
    $login_url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid="
	//&state=STATE#wechat_redirect";// "https://open.weixin.qq.com/connect/qrconnect?response_type=code&appid=" 
        . $appid . "&redirect_uri=" . urlencode($callback)
		. "&response_type=code"
        . "&scope=".$scope
        . "&state=" . $_SESSION['state']."#wechat_redirect";
    header("Location:$login_url");
}

//用户点击微信登录按钮调用此函数
weixin_login($_SESSION["appid"], 'snsapi_userinfo', $_SESSION["callback"]);
?>
