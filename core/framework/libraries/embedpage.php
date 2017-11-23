<?php
class Embedpage {
	public static  function  getPurShopCdByDES(){
		return str_replace("+","%2B",Des::share()->encode(SYSTEM_USER_NAME,SYSTEM_USER_NAME_DES_KEY));
	}

	public static  function  getPurUserCdByDES( $userCd){
		if(empty($userCd)){
			return  '';
		}
		return str_replace("+","%2B",Des::share()->encode($userCd,SYSTEM_USER_NAME_DES_KEY));
	}

	public static  function  getCommonParams(){
		return 'shopCd='.Embedpage::getPurShopCdByDES().'&userCd='.Embedpage::getPurUserCdByDES($_SESSION['member_name'])."&roleId=". $_SESSION['identity']."&languageCode=zh"."&domain=".SYSTEM_SITE_DOMAIN;
	}
}
?>
