<?php
require_once("./webService.ini.php");
/**
 * Created by PhpStorm.
 * User: win7
 * Date: 2016/6/29
 * Time: 15:30
 */
class WebServiceInterceptor
{
    public static function check($key){
        global $webServiceConfig;
        if($webServiceConfig['isAuthen']){
            if($webServiceConfig['authenKey'] == $key ){
                return true;
            }else{
                return false;
            }
        }else{//验证
            return true;
        }
    }
}
?>
