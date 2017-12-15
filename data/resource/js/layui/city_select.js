/**
 * Created by zhengguiyun on 2017/12/14.
 */
function get_province(obj_id,select_id){
    $.ajax({
        type: "get",
        url: "/shop/index.php?act=base_list&op=provinceList", // type=1表示查询省份
        data: {},
        dataType: "json",
        success: function(data) {
            $("#"+obj_id).html("<option value=''>请选择省份</option>");
            $.each(data, function(i, item) {
                if(item.code == select_id){
                    $("#"+obj_id).append("<option value='" + item.code + "' selected >" + item.city_name + "</option>");
                }else{
                    $("#"+obj_id).append("<option value='" + item.code + "'>" + item.city_name + "</option>");
                }
            });
            layui.use(['form'], function(){
                var form = layui.form;
                form.render('select');
            });
        }
    });
}


function get_city(province_id,obj_id,select_id){
    $.ajax({
        type: "get",
        url: "/shop/index.php?act=base_list&op=cityList", // type =2表示查询市
        data: {"parent_id": province_id},
        dataType: "json",
        success: function(list) {
            $("#"+obj_id).html("<option value=''>请选择市</option>");
            $.each(list, function(i, item) {
                if(item.code == select_id){
                    $("#"+obj_id).append("<option value='" + item.code + "' selected >" + item.city_name + "</option>");
                }else{
                    $("#"+obj_id).append("<option value='" + item.code + "' >" + item.city_name + "</option>");
                }
            });
            layui.use(['form'], function(){
                var form = layui.form;
                form.render('select');
            });
        }
    });
}



function get_county(city_id,obj_id,select_id){
    $.ajax({
        type: "get",
        url: "/shop/index.php?act=base_list&op=countyList", // type =2表示查询市
        data: {"parent_id": city_id},
        dataType: "json",
        success: function(list) {
            $("#"+obj_id).html("<option value=''>请选择市</option>");
            $.each(list, function(i, item) {
                if(item.code == select_id){
                    $("#"+obj_id).append("<option value='" + item.code + "' selected >" + item.city_name + "</option>");
                }else{
                    $("#"+obj_id).append("<option value='" + item.code + "' >" + item.city_name + "</option>");
                }
            });
            layui.use(['form'], function(){
                var form = layui.form;
                form.render('select');
            });
        }
    });
}


function open_window(member,title,url,width,height) {
    if (member.length == 0){
        window.location.href="/index.php";
    }else{
        layui.use('layer', function(){
            var layer = layui.layer;
            layer.open({
                type: 2,
                title: title,
                maxmin: false, //开启最大化最小化按钮
                resize: false,
                fixed: true,
                offset: 20,
                shade: [0.8, '#393D49'],
                area: [width+'px', height+'px'],
                content: url,
            })
        });
    }
}