$(function() {
    $("#logoutbtn").click(function() {
        var a = getCookie("seller_name");
        var e = getCookie("seller_key");
        var i = "wap";
        $.ajax({
            type: "get",
            url: ApiUrl + "/index.php?act=seller_logout",
            data: {
                seller_name: a,
                key: e,
                client: i
            },
            success: function(a) {
                if (a) {
                    delCookie("seller_name");
                    delCookie("seller_key");
                    delCookie("store_id");
                    var member_key = getCookie("member_key");//覆盖key
                    addCookie("key", member_key, 188);

                    location.href = WapSiteUrl + "/tmpl/member/member.html";
                }
            }
        })
    })


    if (getQueryString("key") != "") {
        var a = getQueryString("key");
        var e = getQueryString("username");
        addCookie("key", a);
        addCookie("seller_name", e)
    } else {
        //var a = getCookie("key");
        var a = getCookie("seller_key");
        var logintype = getCookie("login_type");
    }

    if (a) {// && logintype=='seller'
        //return false;
        $.ajax({
            type: "post",
            //url: ApiUrl + "/index.php?act=member_index",
            url: ApiUrl + "/index.php?act=seller_store&op=store_info",

            data: {
                key: a
            },
            dataType: "json",
            success: function(a) {
                //checkLogin(a.login);
                var e = '<div class="store-avatar"><img src="'+ a.datas.store_info.store_avatar1 + '"></div>'+
                    '<div class="store-name">'+ a.datas.store_info.store_name + '</div> '+
                    '<div class="store-favorate"><span class="num"><input type="hidden" id="store_favornum_hide" value="'+ a.datas.store_info.store_collect +'"><em id="store_favornum">1</em><p>收藏</p></span></div>' ;

                    //'<div class="member-info">' + '<div class="user-avatar"> <img src="' + a.datas.member_info.avator + '"/> </div>' + '<div class="user-name"> <span>' + a.datas.member_info.user_name + "<sup>" + a.datas.member_info.level_name + "</sup></span> </div>" + "</div>" + '<div class="member-collect"><span><a href="favorites.html"><em>' + a.datas.member_info.favorites_goods + "</em>" + "<p>商品收藏</p>" + '</a> </span><span><a href="favorites_store.html"><em>' + a.datas.member_info.favorites_store + "</em>" + "<p>店铺收藏</p>" + '</a> </span><span><a href="views_list.html"><i class="goods-browse"></i>' + "<p>我的足迹</p>" + "</a> </span></div>";
                $("#seller-home-top").html(e);
                //var e = '<li><a href="order_list.html?data-state=state_new">' + (a.datas.member_info.order_nopay_count > 0 ? "<em></em>": "") + '<i class="cc-01"></i><p>待付款</p></a></li>' + '<li><a href="order_list.html?data-state=state_send">' + (a.datas.member_info.order_noreceipt_count > 0 ? "<em></em>": "") + '<i class="cc-02"></i><p>待收货</p></a></li>' + '<li><a href="order_list.html?data-state=state_notakes">' + (a.datas.member_info.order_notakes_count > 0 ? "<em></em>": "") + '<i class="cc-03"></i><p>待自提</p></a></li>' + '<li><a href="order_list.html?data-state=state_noeval">' + (a.datas.member_info.order_noeval_count > 0 ? "<em></em>": "") + '<i class="cc-04"></i><p>待评价</p></a></li>' + '<li><a href="member_refund.html">' + (a.datas.member_info.
                //return > 0 ? "<em></em>": "") + '<i class="cc-05"></i><p>退款/退货</p></a></li>';
                //$("#order_ul").html(e);
                //var e = '<li><a href="predepositlog_list.html"><i class="cc-06"></i><p>预存款</p></a></li>' + '<li><a href="rechargecardlog_list.html"><i class="cc-07"></i><p>充值卡</p></a></li>' + '<li><a href="voucher_list.html"><i class="cc-08"></i><p>代金券</p></a></li>' + '<li><a href="redpacket_list.html"><i class="cc-09"></i><p>红包</p></a></li>' + '<li><a href="pointslog_list.html"><i class="cc-10"></i><p>积分</p></a></li>';
                //$("#asset_ul").html(e);
                return false
            }
        })
    } else {
        console.log('not login');
        window.location.href = WapSiteUrl + "/tmpl/seller/login.html";
        return
        //var i = '<div class="member-info">' + '<a href="login.html" class="default-avatar" style="display:block;"></a>' + '<a href="../member/login.html" class="to-login">用户登录</a> | <a href="../seller/login.html" class="to-login">商家登录</a>' + "</div>" + '<div class="member-collect"><span><a href="login.html"><i class="favorite-goods"></i>' + "<p>商品收藏</p>" + '</a> </span><span><a href="login.html"><i class="favorite-store"></i>' + "<p>店铺收藏</p>" + '</a> </span><span><a href="login.html"><i class="goods-browse"></i>' + "<p>我的足迹</p>" + "</a> </span></div>";
        //$(".member-top").html(i);
        //var i = '<li><a href="login.html"><i class="cc-01"></i><p>待付款</p></a></li>' + '<li><a href="login.html"><i class="cc-02"></i><p>待收货</p></a></li>' + '<li><a href="login.html"><i class="cc-03"></i><p>待自提</p></a></li>' + '<li><a href="login.html"><i class="cc-04"></i><p>待评价</p></a></li>' + '<li><a href="login.html"><i class="cc-05"></i><p>退款/退货</p></a></li>';
        //$("#order_ul").html(i);
        //return false
    }
    //$.scrollTransparent()
});