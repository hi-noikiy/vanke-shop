function getQueryString(e) {
    var t = new RegExp("(^|&)" + e + "=([^&]*)(&|$)");
    var a = window.location.search.substr(1).match(t);
    if (a != null) return a[2];
    return ""
}
function addCookie(e, t, a) {
    var n = e + "=" + escape(t) + "; path=/";
    if (a > 0) {
        var r = new Date;
        r.setTime(r.getTime() + a * 3600 * 1e3);
        n = n + ";expires=" + r.toGMTString()
    }
    document.cookie = n
}
function getCookie(e) {
    var t = document.cookie;
    var a = t.split("; ");
    for (var n = 0; n < a.length; n++) {
        var r = a[n].split("=");
        if (r[0] == e) return unescape(r[1])
    }
    return null
}
function delCookie(e) {
    var t = new Date;
    t.setTime(t.getTime() - 1);
    var a = getCookie(e);
    if (a != null) document.cookie = e + "=" + a + "; path=/;expires=" + t.toGMTString()
}
function checkLogin(e) {
    if (e == 0) {
        location.href = WapSiteUrl + "/tmpl/member/login.html";
        return false
    } else {
        return true
    }
}
function contains(e, t) {
    var a = e.length;
    while (a--) {
        if (e[a] === t) {
            return true
        }
    }
    return false
}
function buildUrl(e, t) {
    switch (e) {
    case "keyword":
        return WapSiteUrl + "/tmpl/product_list.html?keyword=" + encodeURIComponent(t);
    case "special":
        return WapSiteUrl + "/special.html?special_id=" + t;
    case "goods":
        return WapSiteUrl + "/tmpl/product_detail.html?goods_id=" + t;
    case "url":
        return t
    }
    return WapSiteUrl
}
function errorTipsShow(e) {
    $(".error-tips").html(e).show();
    setTimeout(function() {
        errorTipsHide()
    },
    3e3)
}
function errorTipsHide() {
    $(".error-tips").html("").hide()
}
function writeClear(e) {
    if (e.val().length > 0) {
        e.parent().addClass("write")
    } else {
        e.parent().removeClass("write")
    }
    btnCheck(e.parents("form"))
}
function btnCheck(e) {
    var t = true;
    e.find("input").each(function() {
        if ($(this).hasClass("no-follow")) {
            return
        }
        if ($(this).val().length == 0) {
            t = false
        }
    });
    if (t) {
        e.find(".btn").parent().addClass("ok")
    } else {
        e.find(".btn").parent().removeClass("ok")
    }
}
function getSearchName() {
    var e = decodeURIComponent(getQueryString("keyword"));
    if (e == "") {
        if (getCookie("deft_key_value") == null) {
            $.getJSON(ApiUrl + "/index.php?act=index&op=search_hot_info",
            function(e) {
                var t = e.datas.hot_info;
                if (typeof t.name != "undefined") {
                    $("#keyword").attr("placeholder", t.name);
                    $("#keyword").html(t.name);
                    addCookie("deft_key_name", t.name, 1);
                    addCookie("deft_key_value", t.value, 1)
                } else {
                    addCookie("deft_key_name", "", 1);
                    addCookie("deft_key_value", "", 1)
                }
            })
        } else {
            $("#keyword").attr("placeholder", getCookie("deft_key_name"));
            $("#keyword").html(getCookie("deft_key_name"))
        }
    }
}
function getFreeVoucher(e) {
    var t = getCookie("key");
    if (!t) {
        checkLogin(0);
        return
    }
    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?act=member_voucher&op=voucher_freeex",
        data: {
            tid: e,
            key: t
        },
        dataType: "json",
        success: function(e) {
            checkLogin(e.login);
            var t = "领取成功";
            var a = "green";
            if (e.datas.error) {
                t = "领取失败：" + e.datas.error;
                a = "red"
            }
            $.sDialog({
                skin: a,
                content: t,
                okBtn: false,
                cancelBtn: false
            })
        }
    })
}
function updateCookieCart(e) {
    var t = decodeURIComponent(getCookie("goods_cart"));
    if (t) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=member_cart&op=cart_batchadd",
            data: {
                key: e,
                cartlist: t
            },
            dataType: "json",
            async: false
        });
        delCookie("goods_cart")
    }
}
function getCartCount(e, t) {
    var a = 0;
	delCookie("cart_count")
    if (getCookie("key") !== null && getCookie("cart_count") === null) {
        var e = getCookie("key");
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?act=member_cart&op=cart_count",
            data: {
                key: e
            },
            dataType: "json",
            async: false,
            success: function(e) {
                if (typeof e.datas.cart_count != "undefined") {
                    addCookie("cart_count", e.datas.cart_count, t);
                    a = e.datas.cart_count
                }
            }
        })
    } else {
        a = getCookie("cart_count")
    }
    if (a > 0 && $(".nctouch-nav-menu").has(".cart").length > 0) {
        $(".nctouch-nav-menu").has(".cart").find(".cart").parents("li").find("sup").show();
        $("#header-nav").find("sup").show()
    }
}
function getChatCount() {
    if ($("#header").find(".message").length > 0) {
        var e = getCookie("key");
        if (e !== null) {
            $.getJSON(ApiUrl + "/index.php?act=member_chat&op=get_msg_count", {
                key: e
            },
            function(e) {
                if (e.datas > 0) {
                    $("#header").find(".message").parent().find("sup").show();
                    $("#header-nav").find("sup").show()
                }
            })
        }
        $("#header").find(".message").parent().click(function() {
            window.location.href = WapSiteUrl + "/tmpl/member/chat_list.html"
        })
    }
}
$(function() {
    $(".input-del").click(function() {
        $(this).parent().removeClass("write").find("input").val("");
        btnCheck($(this).parents("form"))
    });
    $("body").on("click", "label",
    function() {
        if ($(this).has('input[type="radio"]').length > 0) {
            $(this).addClass("checked").siblings().removeAttr("class").find('input[type="radio"]').removeAttr("checked")
        } else if ($(this).has('[type="checkbox"]')) {
            if ($(this).find('input[type="checkbox"]').prop("checked")) {
                $(this).addClass("checked")
            } else {
                $(this).removeClass("checked")
            }
        }
    });
    if ($("body").hasClass("scroller-body")) {
        new IScroll(".scroller-body", {
            mouseWheel: true,
            click: true
        })
    }
    $("#header").on("click", "#header-nav",
    function() {
        if ($(".nctouch-nav-layout").hasClass("show")) {
            $(".nctouch-nav-layout").removeClass("show")
        } else {
            $(".nctouch-nav-layout").addClass("show")
        }
    });
    $("#header").on("click", ".nctouch-nav-layout",
    function() {
        $(".nctouch-nav-layout").removeClass("show")
    });
    $(document).scroll(function() {
        $(".nctouch-nav-layout").removeClass("show")
    });
    //getSearchName();
    //getCartCount();
    //getChatCount();
    $(document).scroll(function() {
        e()
    });
    $(".fix-block-r,footer").on("click", ".gotop",
    function() {
        btn = $(this)[0];
        this.timer = setInterval(function() {
            $(window).scrollTop(Math.floor($(window).scrollTop() * .8));
            if ($(window).scrollTop() == 0) clearInterval(btn.timer, e)
        },
        10)
    });
    function e() {
        $(window).scrollTop() == 0 ? $("#goTopBtn").addClass("hide") : $("#goTopBtn").removeClass("hide")
    }
}); (function($) {
    $.extend($, {
        scrollTransparent: function(e) {
            var t = {
                valve: "#header",
                scrollHeight: 50
            };
            var e = $.extend({},
            t, e);
            function a() {
                $(window).scroll(function() {
                    if ($(window).scrollTop() <= e.scrollHeight) {
                        $(e.valve).addClass("transparent").removeClass("posf")
                    } else {
                        $(e.valve).addClass("posf").removeClass("transparent")
                    }
                })
            }
            return this.each(function() {
                a()
            })()
        },
        areaSelected: function(options) {
            var defaults = {
                success: function(e) {}
            };
            var options = $.extend({},
            defaults, options);
            var exp_id = 0;
            var exp_name ='';
            function _init() {
                if ($("#areaSelected").length > 0) {
                    $("#areaSelected").remove()
                }
                var e = '<div id="areaSelected">' + '<div class="nctouch-full-mask left">' + '<div class="nctouch-full-mask-bg"></div>' + '<div class="nctouch-full-mask-block">' + '<div class="header">' + '<div class="header-wrap">' + '<div class="header-l"><a href="javascript:void(0);"><i class="back"></i></a></div>' + '<div class="header-title">' + "<h1>选择物流公司</h1>" + "</div>" + '<div class="header-r"><a href="javascript:void(0);"><i class="close"></i></a></div>' + "</div>" + "</div>" + '<div class="nctouch-main-layout">'  + '<div class="nctouch-main-layout"><ul class="nctouch-default-list"></ul></div>' + "</div>" + "</div>" + "</div>" + "</div>";
                $("body").append(e);
                _getAreaList();
                _bindEvent();
                _close()
            }
            function _getAreaList() {
                var k = getCookie("seller_key");

                $.ajax({
                    type: "get",
                    url: ApiUrl + "/index.php?act=seller_order&op=get_express_company",
                    data: {
                        key: k
                    },
                    dataType: "json",
                    async: false,
                    success: function(e) {
                        var t = e.datas;
                        var a = "";
                        var arr1 = t.express_company.my_express_list;
                        var arr2 = t.express_company.express_list;

                        for (var n = 0; n < arr1.length; n++) {
                            a += '<li><a href="javascript:void(0);" data-id="' + arr1[n] + '" data-name="' + arr2[arr1[n]].e_name + '"><h4>' + arr2[arr1[n]].e_name + '</h4><span class="arrow-r"></span> </a></li>'
                        }
                        $("#areaSelected").find(".nctouch-default-list").html(a);
                        if (typeof myScrollArea == "undefined") {
                            if (typeof IScroll == "undefined") {
                                $.ajax({
                                    url: WapSiteUrl + "/js/iscroll.js",
                                    dataType: "script",
                                    async: false
                                })
                            }
                            myScrollArea = new IScroll("#areaSelected .nctouch-main-layout", {
                                mouseWheel: true,
                                click: true
                            })
                        } else {
                            myScrollArea.refresh()
                        }
                    }
                });
                return false
            }
            function _bindEvent() {
                $("#areaSelected").find(".nctouch-default-list").off("click", "li > a");
                $("#areaSelected").find(".nctouch-default-list").on("click", "li > a",
                function() {
                    exp_id = $(this).attr("data-id");
                    exp_name = $(this).attr("data-name");
                    console.log($(this).attr("data-id"));
                    console.log($(this).attr("data-name"));
                    _finish();
                    return false;
                });
            }
            function _finish() {
                var e = {
                    exp_id: exp_id,
                    exp_name: exp_name
                };
                options.success.call("success", e);
                $("#areaSelected").find(".nctouch-full-mask").addClass("right").removeClass("left");
                return false
            }
            function _close() {
                $("#areaSelected").find(".header-l").off("click", "a");
                $("#areaSelected").find(".header-l").on("click", "a",
                function() {
                    $("#areaSelected").find(".nctouch-full-mask").addClass("right").removeClass("left")
                });
                return false
            }
            return this.each(function() {
                return _init()
            })()
        },
        animationLeft: function(e) {
            var t = {
                valve: ".animation-left",
                wrapper: ".nctouch-full-mask",
                scroll: ""
            };
            var e = $.extend({},
            t, e);
            function a() {
                $(e.valve).click(function() {
                    $(e.wrapper).removeClass("hide").removeClass("right").addClass("left");
                    if (e.scroll != "") {
                        if (typeof myScrollAnimationLeft == "undefined") {
                            if (typeof IScroll == "undefined") {
                                $.ajax({
                                    url: WapSiteUrl + "/js/iscroll.js",
                                    dataType: "script",
                                    async: false
                                })
                            }
                            myScrollAnimationLeft = new IScroll(e.scroll, {
                                mouseWheel: true,
                                click: true
                            })
                        } else {
                            myScrollAnimationLeft.refresh()
                        }
                    }
                });
                $(e.wrapper).on("click", ".header-l > a",
                function() {
                    $(e.wrapper).addClass("right").removeClass("left")
                })
            }
            return this.each(function() {
                a()
            })()
        },
        animationUp: function(e) {
            var t = {
                valve: ".animation-up",
                wrapper: ".nctouch-bottom-mask",
                scroll: ".nctouch-bottom-mask-rolling",
                start: function() {},
                close: function() {}
            };
            var e = $.extend({},
            t, e);
            function a() {
                e.start.call("start");
                $(e.wrapper).removeClass("down").addClass("up");
                if (e.scroll != "") {
                    if (typeof myScrollAnimationUp == "undefined") {
                        if (typeof IScroll == "undefined") {
                            $.ajax({
                                url: WapSiteUrl + "/js/iscroll.js",
                                dataType: "script",
                                async: false
                            })
                        }
                        myScrollAnimationUp = new IScroll(e.scroll, {
                            mouseWheel: true,
                            click: true
                        })
                    } else {
                        myScrollAnimationUp.refresh()
                    }
                }
            }
            return this.each(function() {
                if (e.valve != "") {
                    $(e.valve).on("click",
                    function() {
                        a()
                    })
                } else {
                    a()
                }
                $(e.wrapper).on("click", ".nctouch-bottom-mask-bg,.nctouch-bottom-mask-close",
                function() {
                    $(e.wrapper).addClass("down").removeClass("up");
                    e.close.call("close")
                })
            })()
        }
    })
})(Zepto);
$.fn.ajaxUploadImage = function(e) {
    var t = {
        url: "",
        data: {},
        start: function() {},
        success: function() {}
    };
    var e = $.extend({},
    t, e);
    var a;
    function n() {
        if (a === null || a === undefined) {
            alert("请选择您要上传的文件！");
            return false
        }
        return true
    }
    return this.each(function() {
        $(this).on("change",
        function() {
            var t = $(this);
            e.start.call("start", t);
            a = t.prop("files")[0];
            if (!n) return false;
            try {
                var r = new XMLHttpRequest;
                r.open("post", e.url, true);
                r.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                r.onreadystatechange = function() {
                    if (r.readyState == 4) {
                        returnDate = $.parseJSON(r.responseText);
                        e.success.call("success", t, returnDate)
                    }
                };
                var i = new FormData;
                for (k in e.data) {
                    i.append(k, e.data[k])
                }
                i.append(t.attr("name"), a);
                result = r.send(i)
            } catch(o) {
                console.log(o);
                alert(o)
            }
        })
    })
};
function loadSeccode() {
    $("#codekey").val("");
    $.ajax({
        type: "get",
        url: ApiUrl + "/index.php?act=seccode&op=makecodekey",
        async: false,
        dataType: "json",
        success: function(e) {
            $("#codekey").val(e.datas.codekey)
        }
    });
    $("#codeimage").attr("src", ApiUrl + "/index.php?act=seccode&op=makecode&k=" + $("#codekey").val() + "&t=" + Math.random())
}
function favoriteStore(e) {
    var t = getCookie("key");
    if (!t) {
        checkLogin(0);
        return
    }
    if (e <= 0) {
        $.sDialog({
            skin: "green",
            content: "参数错误",
            okBtn: false,
            cancelBtn: false
        });
        return false
    }
    var a = false;
    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?act=member_favorites_store&op=favorites_add",
        data: {
            key: t,
            store_id: e
        },
        dataType: "json",
        async: false,
        success: function(e) {
            if (e.code == 200) {
                a = true
            } else {
                $.sDialog({
                    skin: "red",
                    content: e.datas.error,
                    okBtn: false,
                    cancelBtn: false
                })
            }
        }
    });
    return a
}
function dropFavoriteStore(e) {
    var t = getCookie("key");
    if (!t) {
        checkLogin(0);
        return
    }
    if (e <= 0) {
        $.sDialog({
            skin: "green",
            content: "参数错误",
            okBtn: false,
            cancelBtn: false
        });
        return false
    }
    var a = false;
    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?act=member_favorites_store&op=favorites_del",
        data: {
            key: t,
            store_id: e
        },
        dataType: "json",
        async: false,
        success: function(e) {
            if (e.code == 200) {
                a = true
            } else {
                $.sDialog({
                    skin: "red",
                    content: e.datas.error,
                    okBtn: false,
                    cancelBtn: false
                })
            }
        }
    });
    return a
}
function favoriteGoods(e) {
    var t = getCookie("key");
    if (!t) {
        checkLogin(0);
        return
    }
    if (e <= 0) {
        $.sDialog({
            skin: "green",
            content: "参数错误",
            okBtn: false,
            cancelBtn: false
        });
        return false
    }
    var a = false;
    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?act=member_favorites&op=favorites_add",
        data: {
            key: t,
            goods_id: e
        },
        dataType: "json",
        async: false,
        success: function(e) {
            if (e.code == 200) {
                a = true
            } else {
                $.sDialog({
                    skin: "red",
                    content: e.datas.error,
                    okBtn: false,
                    cancelBtn: false
                })
            }
        }
    });
    return a
}
function dropFavoriteGoods(e) {
    var t = getCookie("key");
    if (!t) {
        checkLogin(0);
        return
    }
    if (e <= 0) {
        $.sDialog({
            skin: "green",
            content: "参数错误",
            okBtn: false,
            cancelBtn: false
        });
        return false
    }
    var a = false;
    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?act=member_favorites&op=favorites_del",
        data: {
            key: t,
            fav_id: e
        },
        dataType: "json",
        async: false,
        success: function(e) {
            if (e.code == 200) {
                a = true
            } else {
                $.sDialog({
                    skin: "red",
                    content: e.datas.error,
                    okBtn: false,
                    cancelBtn: false
                })
            }
        }
    });
    return a
}
function loadCss(e) {
    var t = document.createElement("link");
    t.setAttribute("type", "text/css");
    t.setAttribute("href", e);
    t.setAttribute("href", e);
    t.setAttribute("rel", "stylesheet");
    css_id = document.getElementById("auto_css_id");
    if (css_id) {
        document.getElementsByTagName("head")[0].removeChild(css_id)
    }
    document.getElementsByTagName("head")[0].appendChild(t)
}
function loadJs(e) {
    var t = document.createElement("script");
    t.setAttribute("type", "text/javascript");
    t.setAttribute("src", e);
    t.setAttribute("id", "auto_script_id");
    script_id = document.getElementById("auto_script_id");
    if (script_id) {
        document.getElementsByTagName("head")[0].removeChild(script_id)
    }
    document.getElementsByTagName("head")[0].appendChild(t)
}

if(WeiXinOauth){
	var key = getCookie('key');	
	if(key==null){			
		var ua = window.navigator.userAgent.toLowerCase();
		if(ua.match(/MicroMessenger/i) == 'micromessenger'){
			window.location.href=ApiUrl+"/index.php?act=auto&op=login&ref="+encodeURIComponent(window.location.href);
		}
	}
}