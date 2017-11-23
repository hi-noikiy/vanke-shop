$(function() {
    if (getQueryString("key") != "") {
        var a = getQueryString("key");
        var e = getQueryString("username");
        addCookie("key", a);
        addCookie("username", e)
    } else {
        var a = getCookie("key")
    }
    if (a) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=member_index",
            data: {
                key: a
            },
            dataType: "json",
            success: function(a) {
                checkLogin(a.login);
                // var e = '<div class="member-info">' + '<div class="user-avatar"> <img src="' + a.datas.member_info.avator + '"/> </div>' + '<div class="user-name"> <span>' + a.datas.member_info.user_name + "<sup>" + a.datas.member_info.level_name + "</sup></span> </div>" + "</div>" + '<div class="member-collect"><span><a href="favorites.html"><em>' + a.datas.member_info.favorites_goods + "</em>" + "<p>商品收藏</p>" + '</a> </span><span><a href="favorites_store.html"><em>' + a.datas.member_info.favorites_store + "</em>" + "<p>店铺收藏</p>" + '</a> </span><span><a href="views_list.html"><i class="goods-browse"></i>' + "<p>我的足迹</p>" + "</a> </span></div>";
                // $(".member-top").html(e);

                //member_mobile_bind
                window.location.href = WapSiteUrl + '/tmpl/member/member_mobile_bind.html';
                return;
            }
        })
    } else {
        var i = '<div class="member-info">' + '<a href="login.html" class="default-avatar" style="display:block;"></a>' + '<a href="login.html" class="to-login">点击登录</a>' + "</div>" + '<div class="member-collect"><span><a href="login.html"><i class="favorite-goods"></i>' + "<p>商品收藏</p>" + '</a> </span><span><a href="login.html"><i class="favorite-store"></i>' + "<p>店铺收藏</p>" + '</a> </span><span><a href="login.html"><i class="goods-browse"></i>' + "<p>我的足迹</p>" + "</a> </span></div>";
        $(".member-top").html(i);
        var i = '<li><a href="login.html"><i class="cc-01"></i><p>待付款</p></a></li>' + '<li><a href="login.html"><i class="cc-02"></i><p>待收货</p></a></li>' + '<li><a href="login.html"><i class="cc-03"></i><p>待自提</p></a></li>' + '<li><a href="login.html"><i class="cc-04"></i><p>待评价</p></a></li>' + '<li><a href="login.html"><i class="cc-05"></i><p>退款/退货</p></a></li>';
        $("#order_ul").html(i);
        return false
    }
    $.scrollTransparent()
});