$(function(){

	$.ajax({
		type:'post',
		timeout : 20000,
		url:ApiUrl+"/index.php?act=connectwx",
		//data:{username:username,password:pwd,client:client},
		dataType:'json',
		success:function(result){
//console.log('wxlogin.js check');
//console.log(result);

			if(!result.datas.error){
				if(typeof(result.datas.key)=='undefined'){
					return false;
				}else{
					addCookie('username',result.datas.username);
					addCookie('key',result.datas.key);
					if(result.datas.reg){//cary 判断注册
						location.href = WapSiteUrl+'/tmpl/member/member_mobile_bind.html';
					}else{
						location.href = WapSiteUrl+'/tmpl/member/member.html?act=member&from=wx&'+result.datas.reg;
					}

				}
				//$(".error-tips").hide();
			}else{
				location.href = WapSiteUrl+'/index.html';
				//$(".error-tips").html(result.datas.error).show();
			}
		},
		error : function(xhr,textStatus){
			if(textStatus=='timeout'){
				//处理超时的
				location.href = WapSiteUrl+'/tmpl/member/member_mobile_bind.html';
			}
			else{
				//其他错误的
			}
		}
	});
});