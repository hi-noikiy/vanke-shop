$(function() {
    //var e = getCookie("key");
    var e = getCookie("seller_key");
    if (!e) {
        window.location.href = WapSiteUrl + "/tmpl/seller/login.html";
        return
    }
    var r = getQueryString("order_id");
    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?act=seller_order&op=search_deliver",
        data: {
            key: e,
            order_id: r
        },
        dataType: "json",
        success: function(e) {
            //checkLogin(e.login);
            var r = e && e.datas;
            if (!r) {
                r = {};
                r.err = "暂无物流信息"
            }
            var t = template.render("order-delivery-tmpl", r);
            $("#order-delivery").html(t)
        }
    })
});