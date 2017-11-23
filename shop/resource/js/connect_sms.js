	function get_sms_captcha(type){
        if($("#phone").val().length == 11 && $("#image_captcha").val().length == 4){
            var ajaxurl = 'index.php?act=connect_sms&op=get_captcha&nchash=1&type='+type;
            ajaxurl += '&captcha='+$('#image_captcha').val()+'&phone='+$('#phone').val();
			$.ajax({
				type: "GET",
				url: ajaxurl,
				async: false,
				success: function(rs){
                    if(rs == 'true') {
                        var html = $("#sms_text span").html();
                        $("#sms_text").html('短信验证码已发送<span>'+html+'</span>');
                        $("#sms_text span a").addClass('disable');
                        $("#sms_text span a.disable").attr('onclick','');
                        var count = 60;
                        $("#sms_text span a.disable").html('重发<i id="time" style="margin-right:0"></i>');                      
                        var interval = setInterval(function(){
                            if(count > 0){
                                count --;
                                $('#time').html('('+count+'s)');
                            } else{
                                clearInterval(interval);
                                $("#sms_text span a").attr("onclick","get_sms_captcha('"+type+"')").removeClass('disable');
                                $('#time').remove();
                            }
                        },1000)
                    } else {
                        showError(rs);
                    }
			    }
			});
    	}
	}
        //这里添加邮件发送按钮的方法
    function get_email_captcha(){
      if($("#post_form_email").valid()){
        if( $("#loginEmail").val()!=''&&$("#image_captcha_email").val().length == 4){
            var ajaxurl = 'index.php?act=connect_sms&op=check_EmailcaptchaNum&nchash=1&';
            ajaxurl += '&captcha='+$('#image_captcha_email').val()+'&email='+$('#loginEmail').val();
			$.ajax({
				type: "GET",
				url: ajaxurl,
				async: false,
				success: function(rs){
                                    if(rs == 'true') {
                                    var html = $("#email_text span").html();
                                    $("#email_text").html('邮件验证码已发送<span>'+html+'</span>');
                                    $("#email_text span a").addClass('disable');
                                    $("#email_text span a.disable").attr('onclick','');
                                    var count = 120;
                                    $("#email_text span a.disable").html('重发<i id="time" style="margin-right:0"></i>');                      
                                    var interval = setInterval(function(){
                                        if(count > 0){
                                            count --;
                                            $('#time').html('('+count+'s)');
                                        } else{
                                            clearInterval(interval);
                                            $("#email_text span a").attr("onclick","get_email_captcha()").removeClass('disable');
                                            $('#time').remove();
                                        }
                                    },1000)
                                    } else {
                                        showError(rs);
                                    }
                                }
			});
    	}
      }
    }
	function check_captcha(){
        if($("#phone").val().length == 11 && $("#sms_captcha").val().length == 6){
            var ajaxurl = 'index.php?act=connect_sms&op=check_captcha';
            ajaxurl += '&sms_captcha='+$('#sms_captcha').val()+'&phone='+$('#phone').val();
			$.ajax({
				type: "GET",
				url: ajaxurl,
				async: false,
				success: function(rs){
            	    if(rs == 'true') {
            	        $.getScript('index.php?act=connect_sms&op=register'+'&phone='+$('#phone').val());
            	        $("#register_sms_form").show();
            	        $("#post_form").hide();
            	    } else {
            	        showError(rs);
            	    }
			    }
			});
    	}
	}
        function check_email_captcha(){
        if($("#loginEmail").val()!= '' && $("#email_captcha").val().length == 6){
            var ajaxurl = 'index.php?act=connect_sms&op=check_email_captcha';
            ajaxurl += '&email_captcha='+$('#email_captcha').val()+'&loginEmail='+$('#loginEmail').val();
			$.ajax({
				type: "GET",
				url: ajaxurl,
				async: false,
				success: function(rs){
                                        if(rs == 'true') {
                                            $("#user_name").val($("#loginEmail").val());
                                            $("#email").val($("#loginEmail").val());
                                            $("#register_form").show();
                                            $("#post_form_email").hide();
                                        } else {
                                            showError(rs);
                                        }
                                }
			});
    	}
	}