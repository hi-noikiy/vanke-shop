$(function() {
    //var e = getCookie("key");
    var e = getCookie("seller_key");
    //var login_type = getCookie("login_type");
    //var seller_key = getCookie("seller_key");

    var r = WapSiteUrl + "/tmpl/seller/home.html"; //document.referrer;
    if (e) {
        window.location.href = r;
        return
    }

    $.sValid.init({
        rules: {
            username: "required",
            userpwd: "required"
        },
        messages: {
            username: "用户名必须填写！",
            userpwd: "密码必填!"
        },
        callback: function(e, r, a) {
            if (e.length > 0) {
                var i = "";
                $.map(r,
                    function(e, r) {
                        i += "<p>" + e + "</p>"
                    });
                errorTipsShow(i)
            } else {
                errorTipsHide()
            }
        }
    });
    var a = true;
    $("#loginbtn").click(function() {
        //if (!$(this).parent().hasClass("ok")) {   //部分手机记住密码会无法登陆,改用下面验证方式
        //    return false
        //}
        if ($.sValid()) {
            console.log('验证');
        }else{
            return false
        }

        if (a) {
            a = false
        } else {
            return false
        }
        var e = $("#username").val();
        var i = $("#userpwd").val();
        var t = "wap";
        if ($.sValid()) {
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?act=seller_login",
                data: {
                    seller_name: e,
                    password: i,
                    client: t
                },
                dataType: "json",
                success: function(e) {
                    a = true;
                    if (!e.datas.error) {
                        console.log('log ok');
                        if (typeof e.datas.key == "undefined") {
                            return false
                        } else {
                            var i = 0;
                            if ($("#checkbox").prop("checked")) {
                                i = 188
                            }
                            //updateCookieCart(e.datas.key);
                            //addCookie("username", e.datas.username, i);
                            addCookie("seller_name", e.datas.seller_name, i);
                            addCookie("key", e.datas.key, i);
                            //addCookie("login_type", 'seller', i); //商家登录
                            addCookie("seller_key", e.datas.key, i);
                            addCookie("store_id", e.datas.store_id, i);
                            console.log(r);
                            location.href = r
                        }
                        errorTipsHide()
                    } else {
                        console.log('log fail');
                        errorTipsShow("<p>" + e.datas.error + "</p>")
                    }
                }
            })
        }
    });

});