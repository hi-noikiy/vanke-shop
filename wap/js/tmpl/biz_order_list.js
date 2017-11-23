var page = pagesize;
var curpage = 1;
var hasMore = true;
var footer = false;
var reset = true;
var orderKey = "";
$(function() {
    //var e = getCookie("key");
    var e = getCookie("seller_key");
    if (!e) {
        window.location.href = WapSiteUrl + "/tmpl/seller/login.html"
    }
    if (getQueryString("data-state") != "") {
        $("#filtrate_ul").find("li").has('a[data-state="' + getQueryString("data-state") + '"]').addClass("selected").siblings().removeClass("selected")
    }
    $("#search_btn").click(function() {
        reset = true;
        t()
    });
    $("#fixed_nav").waypoint(function() {
        $("#fixed_nav").toggleClass("fixed")
    },
    {
        offset: "50"
    });
    function t() {
        if (reset) {
            curpage = 1;
            hasMore = true
        }
        $(".loading").remove();
        if (!hasMore) {
            return false
        }
        hasMore = false;
        var t = $("#filtrate_ul").find(".selected").find("a").attr("data-state");
        var r = $("#order_key").val();
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=seller_order&op=order_list&page=" + page + "&curpage=" + curpage,
            data: {
                key: e,
                state_type: t,
                order_key: r
            },
            dataType: "json",
            success: function(e) {
                //checkLogin(e.login);
                curpage++;
                hasMore = e.hasmore;
                if (!hasMore) {
                    get_footer()
                }
                if (e.datas.order_group_list.length <= 0) {  //e.datas.order_group_list.length
                    $("#footer").addClass("posa")
                } else {
                    $("#footer").removeClass("posa")
                }
                var t = e;
                t.WapSiteUrl = WapSiteUrl;
                t.ApiUrl = ApiUrl;
                //t.key = getCookie("key");
                t.key = getCookie("seller_key");
                template.helper("$getLocalTime",
                function(e) {
                    var t = new Date(parseInt(e) * 1e3);
                    var r = "";
                    r += t.getFullYear() + "年";
                    r += t.getMonth() + 1 + "月";
                    r += t.getDate() + "日 ";
                    r += t.getHours() + ":";
                    r += t.getMinutes();
                    return r
                });
                template.helper("p2f",
                function(e) {
                    return (parseFloat(e) || 0).toFixed(2)
                });
                template.helper("parseInt",
                function(e) {
                    return parseInt(e)
                });
                var r = template.render("order-list-tmpl", t);
                console.log(t);
                if (reset) {
                    reset = false;
                    $("#order-list").html(r)
                } else {
                    $("#order-list").append(r)
                }
            }
        })
    }
    $("#order-list").on("click", ".cancel-order", r);
    $("#order-list").on("click", ".shipping-fee", s_f);

    $("#order-list").on("click", ".delete-order", o);
    $("#order-list").on("click", ".viewdelivery-order", c);

    function s_f() {//修改运费
        var e = $(this).attr("order_id");
        $.sDialog({
            content: "请输入运费？<input type='text' autocomplete='on' maxlength='50' placeholder='输入运费,例如:10.00' name='order_key' id='shipping_fee' oninput='' style='display: inline-block;            width: 75%;        height: 1rem;        padding: 0.25rem;        margin: 0.25rem auto auto 0.75rem;        border: none;        border-radius: 0.2rem;        font-size: 0.6rem;        background-color: #fff;        line-height: 1rem;'>",
            okFn: function() {
                var shipping_fee =$('#shipping_fee').val();
                s_f_call(e,shipping_fee)
            }
        })
    }
    function s_f_call(r,fee) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=seller_order&op=order_ship_price",
            data: {
                order_id: r,
                key: e,
                shipping_fee: fee
            },
            dataType: "json",
            success: function(e) {
                if (e.datas && e.datas == 1) {
                    reset = true;
                    t()
                } else {
                    $.sDialog({
                        skin: "red",
                        content: e.datas.error,
                        okBtn: false,
                        cancelBtn: false
                    })
                }
            }
        })
    }


    function r() {
        var e = $(this).attr("order_id");
        $.sDialog({
            content: "确定取消订单？<input type='text' autocomplete='on' maxlength='50' placeholder='输入订单取消原因' name='order_key' id='cancel_reson' oninput='' style='display: inline-block;            width: 75%;        height: 1rem;        padding: 0.25rem;        margin: 0.25rem auto auto 0.75rem;        border: none;        border-radius: 0.2rem;        font-size: 0.6rem;        background-color: #fff;        line-height: 1rem;'>",
            okFn: function() {
                var reason =$('#cancel_reson').val();
                a(e,reason)
            }
        })
    }
    function a(r,reason) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=seller_order&op=order_cancel",
            data: {
                order_id: r,
                key: e,
                reason: reason
            },
            dataType: "json",
            success: function(e) {
                if (e.datas && e.datas == 1) {
                    reset = true;
                    t()
                } else {
                    $.sDialog({
                        skin: "red",
                        content: e.datas.error,
                        okBtn: false,
                        cancelBtn: false
                    })
                }
            }
        })
    }
    function o() {
        var e = $(this).attr("order_id");
        $.sDialog({
            content: "是否移除订单？<h6>电脑端订单回收站可找回订单！</h6>",
            okFn: function() {
                i(e)
            }
        })
    }
    function i(r) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=member_order&op=order_delete",
            data: {
                order_id: r,
                key: e
            },
            dataType: "json",
            success: function(e) {
                if (e.datas && e.datas == 1) {
                    reset = true;
                    t()
                } else {
                    $.sDialog({
                        skin: "red",
                        content: e.datas.error,
                        okBtn: false,
                        cancelBtn: false
                    })
                }
            }
        })
    }
    function s(r) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=member_order&op=order_receive",
            data: {
                order_id: r,
                key: e
            },
            dataType: "json",
            success: function(e) {
                if (e.datas && e.datas == 1) {
                    reset = true;
                    t()
                } else {
                    $.sDialog({
                        skin: "red",
                        content: e.datas.error,
                        okBtn: false,
                        cancelBtn: false
                    })
                }
            }
        })
    }
    function c() {
        var e = $(this).attr("order_id");
        location.href = WapSiteUrl + "/tmpl/seller/order_detail.html?order_id=" + e
    }
    $("#filtrate_ul").find("a").click(function() {
        $("#filtrate_ul").find("li").removeClass("selected");
        $(this).parent().addClass("selected").siblings().removeClass("selected");
        reset = true;
        window.scrollTo(0, 0);
        t()
    });
    t();
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 1) {
            t()
        }
    })
});
function get_footer() {
    if (!footer) {
        footer = true;
        $.ajax({
            url: WapSiteUrl + "/js/tmpl/footer.js",
            dataType: "script"
        })
    }
}