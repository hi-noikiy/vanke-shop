<?php


/**
 * @brief 本文件作为demo的配置文件。
 */

/**
 * 正式运营环境请关闭错误信息
 * ini_set("error_reporting", E_ALL);
 * ini_set("display_errors", TRUE);
 * QQDEBUG = true  开启错误提示
 * QQDEBUG = false 禁止错误提示
 * 默认禁止错误信息
 */
//define("QQDEBUG", false);
define("QQDEBUG", true);
if (defined("QQDEBUG") && QQDEBUG)
{
    @ini_set("error_reporting", E_ALL);
    @ini_set("display_errors", TRUE);
}

/**
 * session
 */
//require_once(BASE_PATH.DS.'api'.DS.'weixin'.DS.'comm'.DS."session.php");



//申请到的appid
$_SESSION["appid"]    ="";
$_SESSION["appkey"]   = "";

//登录成功后跳转的地址,请确保地址真实可用，否则会导致登录失败。
//$_SESSION["callback"] = "http://your domain/oauth/get_access_token.php"; 
$_SESSION["callback"] = "http://ymh.ox.pw/mobile/index.php?act=login&op=wxback";

?>
