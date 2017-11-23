<?php
include_once(BASE_ROOT_PATH.DS.'webservice.conf.php' );
function wanke_login(){
    if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        $zhu_zhe_er = ZZE_API_URL;
    } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
        $zhu_zhe_er = ZZE_API_URL;
    } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        $zhu_zhe_er = ZZE_API_URL;
    }else{
        $zhu_zhe_er = 'https://uat.4009515151.com/';
    }
    $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
    $login_url = $zhu_zhe_er."api/lebang/oauth?response_type=code&client_id="
        . ZZE_CLIENT_ID . "&redirect_uri=" . urlencode(ZZE_REDIRECT_URI."shop/api/wanke/")
        . "&state=" . $_SESSION['state']
        . "&scopes="."r-staff"
        . "&relogin="."y";
    header("Location:$login_url");
}

//用户点击助这儿登录按钮调用此函数
wanke_login();

