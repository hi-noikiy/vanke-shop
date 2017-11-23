$(function() {
    var a = getCookie("key");
    var e = '<div class="nctouch-footer-wrap posr">' + '<div class="nav-text">';
    if (a) {
        e += '<a href="' + WapSiteUrl + '/tmpl/member/member.html">我的商城</a>' + '<a id="logoutbtn" href="javascript:void(0);">注销</a>' + '<a href="' + WapSiteUrl + '/tmpl/member/member_feedback.html">反馈</a>'
    } else {
        e += '<a href="' + WapSiteUrl + '/tmpl/member/login.html">登录</a>' + '<a href="' + WapSiteUrl + '/tmpl/member/register.html">注册</a>' + '<a href="' + WapSiteUrl + '/tmpl/member/login.html">反馈</a>'
    }
    e += '<a href="javascript:void(0);" class="gotop">返回顶部</a>' + "</div>" + '<div class="nav-pic">' + '<a href="' + SiteUrl + '/index.php?act=mb_app" class="app"><span><i></i></span><p>客户端</p></a>' + '<a href="javascript:void(0);" class="touch"><span><i></i></span><p>触屏版</p></a>' + '<a href="' + SiteUrl + '" class="pc"><span><i></i></span><p>电脑版</p></a>' + "</div>" + '<div class="copyright">' + 'Copyright&nbsp;&copy;&nbsp;2005-2016 版权所有' + "</div>";
    if (!window.mb_curr) {
        window.mb_curr ='';
    }
    var mb_list = ['index','categroy','cart','home'];
    var mb_statue = [];
    for (mb in mb_list) {
        if(mb_list[mb] == window.mb_curr){
            mb_statue.push('curr');
        }else{
            mb_statue.push('');
        }
    }

var f = '<div class="menubar" data-position="fixed" data-role="footer" style="border:none;border-top:1px solid #e0e0e0;background:#fff;"><ul><li><a class="'+mb_statue[0]+'" href="' + WapSiteUrl + '/index.html"><p><i class="home"></i></p><p><b>首页</b></p></a></li><li><a class="'+mb_statue[1]+'" href="' + WapSiteUrl + '/tmpl/product_first_categroy.html"><p><i class="type"></i></p><p><b>分类</b></p></a></li><li>';
f += '<a class="'+mb_statue[2]+'" href="' + WapSiteUrl + '/tmpl/cart_list.html" data-icon="mycart"><p><i class="cart"></i></p><p><b>购物车</b></p></a></li><li><a class="'+mb_statue[3]+'" href="' + WapSiteUrl + '/tmpl/member/member.html"><p><i class="my"></i></p><p><b>我的</b></p></a></li></ul></div>';

    $("#footer").html(f);//e
    var a = getCookie("key");
    $("#logoutbtn").click(function() {
        var a = getCookie("username");
        var e = getCookie("key");
        var i = "wap";
        $.ajax({
            type: "get",
            url: ApiUrl + "/index.php?act=logout",
            data: {
                username: a,
                key: e,
                client: i
            },
            success: function(a) {
                if (a) {
                    delCookie("username");
                    delCookie("key");
                    delCookie("has_store");
                    delCookie("member_key");
                    location.href = WapSiteUrl
                }
            }
        })
    })
});