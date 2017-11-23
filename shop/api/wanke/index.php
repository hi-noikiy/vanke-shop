<?php
    session_start();
    include_once ('../../../global.php');
    include_once(BASE_ROOT_PATH.'/webservice.conf.php' );
    function wanke_callback($state,$code){
        if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            $zhu_zhe_er = ZZE_API_URL;
        } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
            $zhu_zhe_er = ZZE_API_URL;
        } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            $zhu_zhe_er = ZZE_API_URL;
        }else{
            $zhu_zhe_er = 'https://uat.4009515151.com/';
        }
       $ch = curl_init();
       $data = array ('client_id' => ZZE_CLIENT_ID,
                      'client_secret' => ZZE_CLIENT_SECRET,
                      'grant_type' => 'authorization_code',
                      'code' => $code ,
                      'redirect_uri' => ZZE_REDIRECT_URI."shop/api/wanke/"
                     );
       curl_setopt($ch, CURLOPT_URL, $zhu_zhe_er.'api/lebang/oauth/access_token');
       curl_setopt($ch, CURLOPT_POST, true);      
       curl_setopt($ch, CURLOPT_POSTFIELDS, $data);      
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      
       $handles = curl_exec($ch);      
       curl_close($ch);   
       $handlesJson=json_decode($handles,true);
       $access_token = $handlesJson['access_token'];
       $url=$zhu_zhe_er.'api/lebang/staffs/me?access_token='.$access_token;
       $userInfo = file_get_contents($url);
       $info = json_decode($userInfo,true);
       $identity_id = $info['result']['identity_id'];
       header('location: '.ZZE_REDIRECT_URI.'shop/index.php?act=connect_wk&zhe_id_cord='.$identity_id);
    }

    wanke_callback($_GET['state'],$_GET['code']);
?> 
