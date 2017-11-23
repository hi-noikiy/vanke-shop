$(function() {
    //var r = getCookie("key");
    var r = getCookie("seller_key");
    if (!r) {
        window.location.href = WapSiteUrl + "/tmpl/seller/login.html"
    }
    $.getJSON(ApiUrl + "/index.php?act=seller_order&op=order_info", {
        key: r,
        order_id: getQueryString("order_id")
    },
    function(t) {
        t.datas.order_info.WapSiteUrl = WapSiteUrl;
        //my_express_list
        //express_list

        $("#order-info-container").html(template.render("order-info-tmpl", t.datas.order_info));
        $(".delivery-send").click(d_s);
        $(".viewdelivery-order").click(l);
        $("#express_info").on("click",
            function() {
                $.areaSelected({
                    success: function(a) {
                        $("#express_info").val(a.exp_name).attr({
                            "data-id": a.exp_id,
                            "data-name": a.exp_name
                        })
                    }
                })
            });

        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=seller_order&op=get_current_deliver",
            data: {
                key: r,
                order_id: getQueryString("order_id")
            },
            dataType: "json",
            success: function(r) {
                //checkLogin(r.login);
                var e = r && r.datas;
                if (e.deliver_info) {
                    $("#delivery_content").html(e.deliver_info.context);
                    $("#delivery_time").html(e.deliver_info.time)
                }
            }
        })
    });
    function d_s() {
        var ord = $(this).attr("order_id");
        if ($.sValid()) {
            var i = $("#express_info").val();
            var d = $("#express_info").attr("data-id");
            var n = $("#shipping_id").val();

            var reciver_name = $("#reciver_name").val();
            var reciver_area = $("#reciver_area").val();
            var reciver_street = $("#reciver_street").val();
            var reciver_mob_phone = $("#reciver_mob_phone").val();
            var reciver_tel_phone = $("#reciver_tel_phone").val();
            //var reciver_dlyp = $("#reciver_dlyp").val();
            //var deliver_explain = $("#deliver_explain").val();
            var daddress_id = $("#daddress_id").val();
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?act=seller_order&op=order_deliver_send",
                data: {
                    key: r,
                    order_id: ord,
                    shipping_express_id: d,
                    reciver_name: reciver_name,
                    reciver_area: reciver_area,
                    reciver_street: reciver_street,
                    reciver_mob_phone:reciver_mob_phone,
                    reciver_tel_phone: reciver_tel_phone,
                    reciver_dlyp: '',
                    deliver_explain: '',
                    daddress_id: daddress_id,
                    shipping_code: n
                },
                dataType: "json",
                success: function(r) {
                    if (r.datas && r.datas == 1) {
                        console.log('ok');
                        //window.location.reload()
                    }
                }
            })
        }
    }
    $.sValid.init({
        rules: {
            express_info: "required",
            shipping_id: "required"
        },
        messages: {
            express_info: "物流公司必填！",
            shipping_id: "物流编号必填！"
        },
        callback: function(a, e, r) {
            if (a.length > 0) {
                var i = "";
                $.map(e,
                    function(a, e) {
                        i += "<p>" + a + "</p>"
                    });
                errorTipsShow(i)
            } else {
                errorTipsHide()
            }
        }
    });

    function l() {
        var r = $(this).attr("order_id");
        location.href = WapSiteUrl + "/tmpl/seller/order_delivery.html?order_id=" + r
    }


});
