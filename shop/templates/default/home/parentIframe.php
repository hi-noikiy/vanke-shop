<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/layout.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
  document.domain ='<?php echo SYSTEM_SITE_DOMAIN;?>';
  function pop_login_dialog_4child_iframe( inputData){
    var ref_url_iframe="";
    var ref_url=''+window.location.href;
    if ( '<?php echo  $output['member_info']['level_name'] ?>') {
      //alert("您已登录！");
    }else{
      if(inputData['is_current_page'] ){//是否是要跳转到当前页面
        if(inputData['ref_url_iframe']){
          ref_url_iframe = inputData['ref_url_iframe'];
        }else{  //登录成功不需要跳转，则停留在当前页面
          ref_url_iframe =  $("#purIframe").attr("src");
        }
        setCookieByMilliAndEncodeURI('ref_url_iframe',ref_url_iframe,1440000,'/');
      }else{//跳转到其他的页面
        if(inputData['ref_url_iframe']){
          ref_url = inputData['ref_url_iframe'];
        }
        setCookieByMilliAndEncodeURI('ref_url',ref_url,1440000,'/');
      }
      login_dialog();
      return;
    }
  }
  /*$(function(){
    var inputData = {};
    inputData['ref_url_iframe']="index.php?act=member_unline&op=tenderInquiry&mtype=tender&up=mc";
    inputData['is_current_page']=false;
    pop_login_dialog_4child_iframe( inputData);
   DEFAULT_URL = "<?php echo $output['default_url'] ?>";
   src="<?php echo  SHOP_SITE_URL ?>/templates/default/home/temp.php"
  });*/
</script>
<div class="nch-container wrapper">
  <iframe id="purIframe" src="<?php echo $output['default_url'] ?>" style="border:0px;" width="1200px" height="780px" ></iframe>
</div>
