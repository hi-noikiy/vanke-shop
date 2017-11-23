var SimpleTemplate = function(e) {
    this.init(e)
};
SimpleTemplate.prototype = {
    data: {},
    tags: [],
    regexp: {
        logic: /\s*\[#(if|foreach):(.*?)#\]([\s\S]*?)\[#\/\1#\]\s*/g,
        common: /\[#(.+?)#\]/g
    },
    validActions: {
        include: 1,
        js: 2,
        string: 3,
        "int": 3,
        "boolean": 3,
        "float": 3,
        money: 3,
        foreach: 4,
        "if": 5
    },
    init: function(e) {
        this.template = e || ""
    },
    getTagValue: function(e) {
        var t = e.split("."),
            a;
        if (this.tags.length === 0 || $.inArray(t[0], this.tags) > -1) {
            a = this.data[t[0]];
            for (var i = 1, n = t.length; i < n; i++) {
                if (typeof a === "object") {
                    a = a[t[i]]
                } else {
                    a = "";
                    break
                }
            }
        }
        return convertType(a, "string")
    },
    includeTag: function(e) {
        return $(e).renderTpl(this.data, this.tags)
    },
    execJs: function(tmp_script) {
        var tmp_return = {
            e: false,
            value: ""
        };
        with(this.data) {
            try {
                tmp_return.value = eval(tmp_script)
            } catch (e) {
                tmp_return.e = e
            }
        }
        return tmp_return
    },
    jsTag: function(e) {
        var t = this.execJs(e);
        if (t.e) {
            console.log("js", e)
        }
        return convertType(t.value, "string")
    },
    dataTypeTag: function(e, t) {
        var a = this.jsTag(e);
        return convertType(a, t)
    },
    replaceCommonTag: function(e, t) {
        var a = [],
            i = 0,
            n = this;
        $.each(t.split(":"), function(e, t) {
            if (t in n.validActions) {
                $.inArray(t, a) === -1 && a.push(t);
                i += t.length + 1
            } else {
                return false
            }
        });
        t.indexOf("||") > -1 && $.inArray("js", a) === -1 && a.push("js");
        if (a.length > 0) {
            t = t.substr(i);
            for (var r = a.length - 1, s = t; r >= 0; r--) {
                switch (this.validActions[a[r]]) {
                    case 1:
                        s = this.includeTag(s);
                        break;
                    case 2:
                        s = this.jsTag(s);
                        break;
                    case 3:
                        s = this.dataTypeTag(s, a[r]);
                        break;
                    default:
                        s = ""
                }
            }
            return s
        } else if (t.indexOf(":") === -1 && (this.tags.length === 0 || $.inArray(t.split(".")[0], this.tags) === -1)) {
            return this.getTagValue(t)
        } else {
            return e
        }
    },
    replaceCommonTags: function(e) {
        var t = this;
        return e.replace(this.regexp.common, function(e, a) {
            return t.replaceCommonTag(e, a)
        })
    },
    getSpaces: function(e, t) {
        var a = arguments.callee;
        t = t ? 1 : 0;
        if (e !== a.string) {
            var i = (/^\s+/.exec(e) || [""])[0];
            var n = (/\s+$/.exec(e) || [""])[0];
            a.string = e;
            a.spaces = [{
                p1: i,
                p2: n
            }];
            if ((i.length === 0 || i.indexOf("\n") > -1) && (n.length === 0 || n.indexOf("\n") > -1)) {
                i = i.replace(/\r?\n/g, "\n"), i = i.substr(0, i.lastIndexOf("\n"));
                n = n.replace(/\r?\n/g, "\n"), n = n.substr(n.indexOf("\n") + 1)
            }
            a.spaces.push({
                p1: i,
                p2: n
            })
        }
        return a.spaces[t]
    },
    foreachTag: function(e, t, a) {
        var i = this;
        var n = e.split(":"),
            r = n[0],
            s = n[1],
            l = n[2],
            o = n[3];
        var c = 1,
            h = 0,
            d = [];
        if (this.tags.length > 0) {
            l && this.tags.push(l);
            s && this.tags.push(s);
            o && this.tags.push(o)
        }
        if (typeof this.data[r] === "object") {
            $.each(this.data[r], function() {
                h++
            });
            $.each(this.data[r], function(e, a) {
                l && (i.data[l] = e);
                s && (i.data[s] = a);
                o && (i.data[o] = {
                    iteration: c++,
                    total: h
                });
                var n = i.replaceLogicTags(t);
                n = i.replaceCommonTags(n);
                d.push(n)
            })
        }
        var u = this.getSpaces(a, d.length === 0);
        if (d.length > 0) {
            return u.p1 + d.join("") + u.p2
        } else {
            return u.p1 + u.p2
        }
    },
    ifTag: function(e, t, a) {
        var i = this.execJs(e);
        if (i.e) {
            console.log("if", e)
        }
        var n = this.getSpaces(a, !i.value);
        if (i.value) {
            var r = this.replaceLogicTags(t);
            return n.p1 + this.replaceCommonTags(r) + n.p2
        } else {
            return n.p1 + n.p2
        }
    },
    replaceLogicTag: function(e, t, a, i) {
        switch (t) {
            case "foreach":
                return this.foreachTag(a, i, e);
            case "if":
                return this.ifTag(a, i, e)
        }
    },
    replaceLogicTags: function(e) {
        var t = this;
        return e.replace(this.regexp.logic, function(e, a, i, n) {
            return t.replaceLogicTag(e, a, i, n)
        })
    },
    renderContent: function(e, t, a) {
        this.data = t || {};
        this.data._D = this.data;
        this.tags = a || [];
        var i = this.replaceLogicTags(e);
        i = this.replaceCommonTags(i);
        return i
    }
};
jQuery.fn.extend({
    renderTpl: function(e, t) {
        var a = $.trim(this.eq(0).html());
        return $.renderContent(a, e, t)
    }
});
jQuery.renderContent = function() {
    var e = new SimpleTemplate;
    return $.proxy(e.renderContent, e)
}();
jQuery.slideLoader = function(e) {
    e = e || {};
    $.each(["before", "success", "error"], function(t, a) {
        e[a] = $.isFunction(e[a]) ? e[a] : function() {}
    });
    e.page = e.page !== undefined ? convertType(e.page, "int") : 1;
    e.totalPage = e.totalPage !== undefined ? convertType(e.totalPage, "int") : -1;
    e.mode = e.mode || "page";
    e.type = String(e.type).toUpperCase(), e.type = $.inArray(e.type, ["GET", "POST"]) !== -1 ? e.type : "GET";
    e.container = e.container || $(window);
    e.event = e.event || "scroll";
    if (!e.url) {
        return false
    } else if (e.mode === "page") {
        if (e.totalPage === 0 || e.totalPage > 0 && e.page >= e.totalPage) {
            return false
        }
    }
    e.isLastPage = function(e) {
        if (this.mode === "page" && this.totalPage !== -1) {
            return this.page + (e || 1) >= this.totalPage ? 1 : 0
        } else return -1
    };
    e.canLoadNext = function(t) {
        if (t.type === "scroll") {
            var a = $(window);
            var i = $(document).height();
            var n = a.scrollTop() + a.height();
            var r = Math.max(200, e.threshold || a.height());
            return n + r > i
        } else {
            return true
        }
    };
    e.callback = function(t) {
        if (!e.loading && !e.loaded && e.canLoadNext(t)) {
            if (e.mode === "page" && e.totalPage !== -1 && e.page >= e.totalPage) {
                return
            } else if (e.before.call(e, t) === false) {
                return
            }
            e.loading = true;
            var a = $.isFunction(e.data) ? e.data.apply(e) : e.data ? e.data : {};
            a = jsType(a) === "Object" ? a : {};
            e.mode === "page" && (a.page = e.page + 1);
            e.type === "GET" && (a._ = Math.floor(Math.random() * 1e3));
            $.ajax({
                type: e.type,
                url: e.url,
                dataType: "json",
                data: a,
                success: function(t) {
                    var a = e.success.call(e, t);
                    a !== false && e.page++;
                    a === true && (e.loaded = true);
                    e.loading = false
                },
                error: function() {
                    e.error.call(e);
                    e.loading = false
                }
            })
        }
    };
    e.container.bind(e.event, e.callback);
    e.event === "scroll" && e.container.trigger("scroll");
    return e
};
jQuery.showLeftTime = function(e, t) {
    e = e || {};
    e.params = t || {};
    e.sec = convertType(e.sec, "int");
    e.org_sec = e.sec;
    e.time = Date.now() / 1e3;
    e.mode = e.mode === "sec" ? "sec" : "time";
    e.begin = $.isFunction(e.begin) ? e.begin : function() {};
    e.step = $.isFunction(e.step) ? e.step : function() {};
    e.end = $.isFunction(e.end) ? e.end : function() {};
    e.tags = $.extend(true, {
        day: {
            name: "天",
            show: false,
            prefix: false
        },
        hour: {
            name: "时",
            show: false,
            prefix: true
        },
        minute: {
            name: "分",
            show: false,
            prefix: true
        },
        second: {
            name: "秒",
            show: true,
            prefix: true
        }
    }, e.tags);
    if (e.sec <= 0) {
        return false
    }
    e.stepCallback = function() {
        var t = e.sec;
        var a = Math.floor(t / 86400);
        var i = Math.floor(t / 3600) % 24;
        var n = Math.floor(t / 60) % 60;
        var r = t % 60;
        var s = {
            day: a,
            hour: i,
            minute: n,
            second: r,
            time_str: ""
        };
        for (var l in e.tags) {
            var o = e.tags[l];
            var c = l + "_str";
            s[c] = e.prefixZero(s[l]);
            if (o.name && (s[l] > 0 || o.show)) {
                s.time_str += (o.prefix ? s[c] : s[l]) + o.name
            }
        }
        e.org_sec === t && e.begin.call(e, s);
        e.step.call(e, s);
        if (e.mode === "time") {
            e.sec = Math.round(e.org_sec + e.time - Date.now() / 1e3)
        } else e.sec--;
        if (e.sec <= 0) {
            clearInterval(e.timeId);
            e.end.call(e, s)
        }
    };
    e.prefixZero = function(e) {
        e = "00" + e;
        return e.substr(e.length - 2)
    };
    e.stepCallback();
    e.timeId = setInterval(e.stepCallback, 1e3);
    return e
};
var Lists = {
    showLeftTime: function(e) {
        return $.showLeftTime({
            sec: e.sec,
            step: function(e) {
                e.tag = this.params.tag;
                var t = $("#seckill_time-tpl").renderTpl(e);
                $(".seckill-title .date").html(t)
            },
            end: function() {
                location.reload()
            }
        }, e)
    },
    loadList: function(e) {
        var t = $("#getMorePage");
        var a = $(".list-ul");
        var i = 0;
        $.each(e.list, function(e, t) {
            var n = $($("#list_template-tpl").renderTpl(t));
            if (i++ >= 2) {
                n.find(".pic img").lazyload({
                    effect: "fadeIn",
                    threshold: 200,
                    forceInit: false
                })
            } else if (t.simg) {
                n.find(".pic img").attr("src", t.simg)
            }
            a.append(n)
        });
        if (i === 0 || this.isLastPage() === 1) {
            t.addClass("non").html("没有更多产品");
            return true
        } else {
            t.removeClass("non").html("查看更多&hellip;")
        }
    },
    loadIndexList: function(e) {
        var t = this.wrap,
            a = this;
        e.source === "new" && (this.totalPage = e.totalPage);
        var i = 0;
        $.each(e.list, function(e, a) {
            var n = $($("#list_template-tpl").renderTpl(a));
            if (i++ >= 2) {
                n.find(".pic img").lazyload({
                    effect: "show",
                    threshold: 200,
                    forceInit: false
                })
            } else if (a.simg) {
                n.find(".pic img").attr("src", a.simg)
            }
            t.append(n)
        });
        t.data("status", "hide").is(":visible") && $(".loading-text").hide();
        if (i === 0 || a.isLastPage() === 1) {
            t.data("status", "end").is(":visible") && $(".load-more").show();
            return true
        }
    }
};
var ListsEvent = {
    bindIndexEvent: function() {
        $.slideLoader({
            url: "/ajax/index_load.php?source=recommend",
            totalPage: window.totalPage,
            wrap: $('.list-ul[data-type="recommend"]'),
            before: function(e) {
                if (e.type !== "scroll" || $('#list_data .col-title a[data-type="recommend"]').is(".cur")) {
                    this.wrap.data("status", "load").is(":visible") && $(".loading-text").show()
                } else {
                    return false
                }
            },
            success: Lists.loadIndexList,
            error: function() {
                this.wrap.data("status", "hide").is(":visible") && $(".loading-text").hide()
            }
        });
        $.slideLoader({
            url: "/ajax/index_load.php?source=new",
            event: "scroll load_newList",
            page: 0,
            wrap: $('.list-ul[data-type="new"]'),
            before: function(e) {
                if (e.type !== "scroll" || $('#list_data .col-title a[data-type="new"]').is(".cur")) {
                    this.wrap.data("status", "load").is(":visible") && $(".loading-text").show()
                } else {
                    return false
                }
            },
            success: Lists.loadIndexList,
            error: function() {
                this.wrap.data("status", "hide").is(":visible") && $(".loading-text").hide()
            }
        });
        var e = $(".list-ul[data-type]").data("status", "hide").filter('[data-type="recommend"]');
        window.totalPage <= 1 && e.data("status", "end") && $(".load-more").show();
        $("#list_data .col-title a[data-type]").click(this.changeShowList);
        $(".sort-nav a").click(this.jumpCat);
        $(".channel-nav a").click(function() {
            _gaq.push(["_trackEvent", "Index-品类", "click", "cat-" + getUrlParam("cat", $(this).attr("href"))])
        });
        $(".list-ul a").live("click", function() {
            _gaq.push(["_trackEvent", "Index-推荐", "click", "goodsId-" + getUrlParam("id", $(this).attr("href"))])
        });
        getLocation();
        Lists.showLeftTime(window.miaosha)
    },
    bindListEvent: function() {
        var e = $("#getMorePage"),
            t = $("#filterPop");
        $.slideLoader({
            url: e.attr("param"),
            totalPage: window.totalPage,
            container: e,
            event: "click",
            before: function() {
                e.addClass("non").html("加载中&hellip;")
            },
            success: Lists.loadList,
            error: function() {
                e.removeClass("non").html("查看更多&hellip;")
            }
        });
        $(".plus-screen").click(this.showFilter);
        $(".com-shade").click(this.hideFilter);
        t.find(".pop-check-box i").click(this.selectFilterCheckbox);
        t.find(".pop-radio-box-1 li").click(this.selectFilterRadio);
        t.find(".screen-submit").live("click", this.filterSubmit);
        $("#gisInfo .refresh").click(this.refreshGis);
        $('input[name="q"]').keypress(this.searchInput);
        $(".searchbtn").click(this.searchSubmit);
        if (navigator.userAgent.indexOf("MicroMessenger") !== -1) {
            getLocation()
        }
    },
    changeShowList: function() {
        var e = $(this);
        var t = e.attr("data-type");
        var a = e.parent().siblings('.list-ul[data-type="' + t + '"]');
        if (!e.is(".cur")) {
            e.siblings(".cur").removeClass("cur").end().addClass("cur");
            a.siblings("ul:visible").hide().end().show();
            var i = a.data("status");
            var n = $(".loading-text").hide();
            var r = $(".load-more").hide();
            i === "load" && n.show();
            i === "end" && r.show()
        }
        if (t === "new" && a.children().length === 0) {
            $(window).trigger("load_newList")
        }
        return false
    },
    showFilter: function() {
        var e = $("#filterPop");
        _gaq.push(["_trackEvent", "List-筛选", "show"]);
        e.css({
            marginTop: -e.height() / 2
        }).show().prev(".com-shade").show()
    },
    hideFilter: function() {
        $("#filterPop").hide();
        $(this).hide()
    },
    selectFilterCheckbox: function() {
        $(this).toggleClass("current")
    },
    selectFilterRadio: function() {
        $(this).parent().children().removeClass("current");
        $(this).addClass("current")
    },
    filterSubmit: function() {
        var e = getUrlParam();
        var t = [];
        $("#filterPop").find("[data-var]").each(function() {
            var a = $(this).attr("data-var");
            var i = $(this).find(".current").attr("data-value");
            if (i !== undefined) {
                e[a] = i;
                t.push(a + ":" + i)
            } else {
                delete e[a]
            }
        });
        _gaq.push(["_trackEvent", "List-筛选", "filter", t.join("|")]);
        $(".com-shade").trigger("click");
        var a = UrlInfo.parseUrl("/list.php");
        a.query = e;
        location.href = UrlInfo.buildUrl(a)
    },
    refreshGis: function() {
        var e = $(this);
        e.css("-webkit-animation", "cycle 2s backwards infinite linear");
        getLocation(function() {
            location.reload()
        }, function() {
            e.css("-webkit-animation", "")
        })
    },
    searchInput: function(e) {
        if (e.which === 13) {
            $(".searchbtn").trigger("click")
        }
    },
    searchSubmit: function() {
        var e = $('input[name="q"]').val();
        _gaq.push(["_trackEvent", "List-搜索", "search"]);
        location.href = UrlInfo.keepParam("/search.php", ["q", "fr"], {
            q: e,
            fr: UrlInfo.getParam("fr")
        })
    },
    jumpCat: function() {
        var e = $(this).attr("href");
        if (getCookie("cur_lng") && getCookie("cur_lat")) {
            e = UrlInfo.keepParam(e, ["sort"], {
                sort: "distance"
            })
        }
        _gaq.push(["_trackEvent", "Index-分类", "click", "cat-" + getUrlParam("cat", e)]);
        location.href = e;
        return false
    }
};
$(function() {
    switch (window.exec_page) {
        case "list":
            ListsEvent.bindListEvent();
            break;
        case "index":
            ListsEvent.bindIndexEvent();
            break
    }
    $(".pic img").lazyload({
        effect: "fadeIn",
        threshold: 200
    })
});
$(window).ready(function() {
    var e = $("body");
    var t = e.width();
    if (t >= 640) {
        $(".banner-view").width("640px");
        $(".banner-view").height((640*280)/750+"px");//
        // $(".banner-view").height("112px");
        $("#banner_list li a img").width("640px");
        $("#banner_list li a img").height((640*280)/750+"px");//
        $(".channel-nav .row-col a img").width("302px")
    } else {
        $(".banner-view").width(t + "px");
        $(".banner-view").height(t / 750 * 280 + "px");
        // $(".banner-view").height(t / 640 * 112 + "px");
        $("#banner_list li a img").width(t + "px");
        $("#banner_list li a img").height(t / 750 * 280 + "px");
        $(".channel-nav .row-col a img").width((t - 15) / 2 + "px")
    }
});
$(window).resize(function() {
    var e = $("body");
    var t = e.width();
    if (t >= 640) {
        $(".banner-view").width("640px");
        $(".banner-view").height((640*280)/750+"px");
        $("#banner_list li a img").width("640px");
        $("#banner_list li a img").height((640*280)/750+"px");
        $(".channel-nav .row-col a img").width("302px")
    } else {
        $(".banner-view").width(t + "px");
        $(".banner-view").height(t / 750 * 280 + "px");
        $("#banner_list li a img").width(t + "px");
        $("#banner_list li a img").height(t / 750 * 280 + "px");
        $(".channel-nav .row-col a img").width((t - 15) / 2 + "px")
    }
});