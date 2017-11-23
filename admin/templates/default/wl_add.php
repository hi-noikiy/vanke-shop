<?php ?>
<div class="page">
    <div class="fixed-empty"></div>
<?php if(empty($_GET['ec'])){?>
    <h2 class="top_title">Excel物料导入</h2>
    <div style="width:100%">
        <div style="width:40%;float:left">
            <form action="/admin/index.php?act=codemanages&op=wl_up" method="post" enctype="multipart/form-data">
                 请选择你要上传的文件:
                 <input type="file" name="myFile"><br>
                 <input type="submit" value="上传文件">

             <!--限制客户端上传文件的最大值 隐藏域另起一行-->
             <!--<input type="hidden" name="MAX_FILE_SIZE" value="字节数">-->

             <!--accept设置上传文件的类型-->
             <!--<input type="file" name="myfile" accept="image/jpg,image/png,image/gif">-->
            </form>
        </div>
        <div>
            <table border="1" style="width:49%;vertical-align:middle; text-align:center;">
                 <tr>
                   <td></td>
                   <td>物料大类</td>
                   <td>物料中类</td>
                   <td>物料小类</td>
                   <td>物料名称</td>
                   <td>品牌</td>
                   <td>规格/型号</td>
                   <td>是否内部物料</td>
                </tr>
                <tr>
                    <td></td>
                   <td>A</td>
                   <td>B</td>
                   <td>C</td>
                   <td>D</td>
                   <td>E</td>
                   <td>F</td>
                   <td>G</td>
                </tr>
                <tr>
                   <td>1</td>
                   <td  bgcolor="#02F78E">安保消防</td>
                   <td bgcolor="#02F78E">安全防护</td>
                   <td bgcolor="#02F78E">车场设施</td>
                   <td bgcolor="#02F78E">限高杆</td>
                   <td bgcolor="#02F78E">华为</td>
                   <td bgcolor="#02F78E">特大</td>
                   <td bgcolor="#02F78E">否</td>
                </tr>
                <tr>
                    <td>2</td>
                   <td bgcolor="#02F78E">安保消防</td>
                   <td bgcolor="#02F78E">安全防护</td>
                   <td bgcolor="#02F78E">车场设施</td>
                   <td bgcolor="#02F78E">限高杆</td>
                   <td bgcolor="#02F78E"></td>
                   <td bgcolor="#02F78E"></td>
                   <td bgcolor="#02F78E">否</td>
                </tr>
                <tr>
                   <td colspan="8">Excel中只需按照对应列做数据排版，只要数据，无需表头（即绿色部分）</td>

                </tr>
            </table>
        </div>
    </div>
<?php }else{?>
    <?php if($output['dr_show'] == 1){?>
        <div class="inpuys" style="margin-top: 20px;">
            物料数据已经加载成功，请检查数据！
            <a href="javascript:;" onclick="check()">
                <input type="button" value="检查导入物料数据" id="submit_btn" class="sub btn btn-info">
            </a>
        </div>
    <?php }else{?>
        <?php if($output['dl_show'] == '1'){?>
        <div class="inpuys" style="margin-top: 20px;">
            物料数据已经检查完毕，请确认异常数据！
            <a href="javascript:;" onclick="breakAdd()">
                <input type="button" value="取消物料导入" id="submit_btn" class="sub btn btn-info">
            </a>
            <a href="javascript:;" onclick="addWlData()">
                <input type="button" value="导入物料数据" id="submit_add" class="sub btn btn-info">
            </a>
            <a href="index.php?act=codemanages&op=export_step" style="float: right">
                <input type="button" value="导出异常数据" id="submit" class="sub btn btn-info">
            </a>
        </div>
        <?php }else{?>
            <div class="inpuys" style="margin-top: 20px;">
                物料数据已经导入完毕，请确认异常数据！
                <a href="index.php?act=codemanages&op=export_step1" style="float: right">
                    <input type="button" value="导出异常数据" id="submit" class="sub btn btn-info">
                </a>
            </div>
    <?php }}}?>
<div class="fixed-bar">
    <div class="item-title">
        <h3>物料管理</h3>
        <ul class="tab-base">
            <li><a <?php if($_GET['op'] == "index"){?> href="JavaScript:void(0);" class="current" <?php }else{?>  href="index.php?act=codemanages&op=index" <?php } ?> ><span>物料列表</span></a></li>
            <li><a <?php if($_GET['op'] == "wl_add"){?> href="JavaScript:void(0);" class="current" <?php }else{?>  href="index.php?act=codemanages&op=wl_add" <?php } ?> ><span>物料导入</span></a></li>
        </ul>
    </div>
</div>
<?php if($output['dl_show'] == '1'){?>
    <table class="table tb-type2">
        <thead>
            <tr class="space">
              <th colspan="15" class="nobg"><?php echo $lang['nc_list'];?></th>
            </tr>
            <tr class="thead">
              <th class="align-center">ID</th>
              <th class="align-center">物料大类</th>
              <th class="align-center">物料中类</th>
              <th class="align-center">物料小类</th>
              <th class="align-center">物料名称</th>
              <th class="align-center">品牌</th>
              <th class="align-center">规格/型号</th>
              <th class="align-center">是否内部物料</th>
              <th class="align-center">错误原因</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($output['err_data']) && is_array($output['err_data'])){ ?>
            <?php foreach($output['err_data'] as $k => $v){ ?>
                <tr class="hover">
                    <td class="align-center"><?php echo $k;?></td>
                    <td class="align-center"><?php echo $v['class_big'];?></td>
                    <td class="align-center"><?php echo $v['class_middel'];?></td>
                    <td class="align-center"><?php echo $v['class_small'];?></td>
                    <td class="align-center"><?php echo $v['name']; ?></td>
                    <td class="align-center"><?php echo $v['brand']; ?></td>
                    <td class="align-center"><?php echo $v['spec']; ?></td>
                    <td class="align-center"><?php echo $v['type_name']==1 ? '否':'是'; ?></td>
                    <td class="align-center"><?php echo $v['err_log']; ?></td>
                </tr>
            <?php } ?>
        <?php }else { ?>
            <?php if(!empty($output['wl_type']) && $output['wl_type']== 'y'){?>
                <?php }else{?>
                    <tr class="no_data">
                      <td colspan="10">未查询到异常数据</td>
                    </tr>
                <?php }?>
        <?php } ?>
        </tbody>
        <tfoot>
            <?php if(!empty($output['code']) && is_array($output['code'])){ ?>
                <tr class="tfoot">
                    <td colspan="16">
                        <div class="pagination"> <?php echo $output['page'];?> </div>
                    </td>
                </tr>
            <?php } ?>
        </tfoot>
    </table>
<?php }else{?>
    <table class="table tb-type2">
        <thead>
        <tr class="space">
            <th colspan="15" class="nobg"><?php echo $lang['nc_list'];?></th>
        </tr>
        <tr class="thead">
            <th class="align-center">物料编号</th>
            <th class="align-center">物料大类</th>
            <th class="align-center">物料中类</th>
            <th class="align-center">物料小类</th>
            <th class="align-center">物料名称</th>
            <th class="align-center">品牌</th>
            <th class="align-center">规格/型号</th>
            <th class="align-center">是否内部物料</th>
            <th class="align-center">采购推送</th>
            <th class="align-center">合同推送</th>
        </tr>
        </thead>
        <tbody>
        <?php if(!empty($output['err_list']) && is_array($output['err_list'])){ ?>
            <?php foreach($output['err_list'] as $k => $v){ ?>
                <tr class="hover">
                    <td class="align-center"><?php echo $v['code'];?></td>
                    <td class="align-center"><?php echo $v['class_big'];?></td>
                    <td class="align-center"><?php echo $v['class_middel'];?></td>
                    <td class="align-center"><?php echo $v['class_small'];?></td>
                    <td class="align-center"><?php echo $v['name']; ?></td>
                    <td class="align-center"><?php echo $v['brand']; ?></td>
                    <td class="align-center"><?php echo $v['spec']; ?></td>
                    <td class="align-center"><?php echo $v['type_name']; ?></td>
                    <td class="align-center"><?php echo $v['cg_log']; ?></td>
                    <td class="align-center"><?php echo $v['ht_log']; ?></td>
                </tr>
            <?php } ?>
        <?php }else { ?>
            <?php if(!empty($output['wl_type']) && $output['wl_type']== 'y'){?>
            <?php }else{?>
                <tr class="no_data">
                    <td colspan="10">未查询到异常数据</td>
                </tr>
            <?php }?>
        <?php } ?>
        </tbody>
    </table>
<?php }?>
</div>

<script type="text/javascript">
function check(){
    if(window.confirm("共计<?php echo $output['numRows'];?>条物料数据，确定开始检查数据吗?")){
        var url = '/admin/index.php?act=codemanages&op=check_wl&ec=<?php echo $output['path'];?>';
        var num = '<?php echo $output['numRows'];?>';
        ajaxBar(url,'','pnum',0,1,num,'');
    }
}


function addWlData(){
    if(window.confirm("共计<?php echo $output['numRows'];?>条物料数据，确定开始导入数据吗?")){
        var url = '/admin/index.php?act=codemanages&op=add_wl&ec=<?php echo $output['path'];?>';
        var num = '<?php echo $output['numRows'];?>';
        ajaxBar(url,'','pnum',0,1,num,'');
    }
}

function breakAdd(){
    if(window.confirm("你确定要取消本次物料导入，如果取消，所有异常数据将无法再次找回?")){
        var url = '/admin/index.php?act=codemanages&op=index';
        window.location.href=url;
    }
}

</script>


<script type="text/javascript">
    var ajaxbar_xmlhttp='';
    var ajaxbar_time='';
    var proajax_start='';
    function ajaxBar (url,form,page,start,psize,pcount,rand) {
        AJAXBar.createWin();
        AJAXBar.openWin();
        if(!pcount) ajaxbar_time = window.setInterval('AJAXBar.proAjax()',10);
        var data=AJAXBar.scanForm(form);
        if(rand) rand='&'+rand+'='+Math.floor(Math.random()*1000000+1);
        var ajaxbar_date = new Date();
        proajax_start = ajaxbar_date.getTime();
        AJAXBar.startAjax(url+data+rand,page,start,start,psize,pcount);
    }

    //AJAXBar对象
    var AJAXBar = {
        createAjax : function () {
            var _xmlhttp;
            try {
                _xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                try {
                    _xmlhttp=new XMLHttpRequest();
                } catch (e) {
                    _xmlhttp=false;
                }
            }
            return _xmlhttp;
        },
        startAjax : function (url,page,start,pnum,psize,pcount) {
            var ajaxbar_font=document.getElementById('ajaxbar_font');
            var ajaxbar_pro=document.getElementById('ajaxbar_pro');
            var ajaxbar_view=document.getElementById('ajaxbar_view');
            if(pnum<=start)ajaxbar_font.innerHTML='正在处理第'+start+'条物料数据...';
            if(pnum<=start)ajaxbar_pro.style.width = '0';
            ajaxbar_xmlhttp = AJAXBar.createAjax();
            if (ajaxbar_xmlhttp) {
                rand = Math.random();//no cache
                ajaxbar_xmlhttp.open('get',url+'&'+page+'='+pnum+'&rand='+rand,true);
                ajaxbar_xmlhttp.send();
                ajaxbar_xmlhttp.onreadystatechange=function() {
                    if (ajaxbar_xmlhttp.readyState==4 && ajaxbar_xmlhttp.status==200) {
                        ajaxbar_view.innerHTML=ajaxbar_xmlhttp.responseText;
                        if(pcount){
                            percent=parseInt(pnum/pcount*100);
                            is_end=parseInt(pnum/pcount);
                            if (is_end) fonthtml='全部完成';
                            else fonthtml='正在处理第'+(pnum+1)+'条物料数据';
                            if (!is_end) {
                                var ajaxbar_date = new Date();
                                var proajax_now = ajaxbar_date.getTime();
                                var proajax_leave = parseInt((proajax_now-proajax_start)/(pnum-start+1)*(pcount-pnum-1));
                                var proajax_hour = Math.floor(proajax_leave/(3600*1000));
                                var proajax_minute = Math.floor(proajax_leave/(60*1000)) - proajax_hour*60 + 1; //统一加一分钟
                            } else {
                                var proajax_hour = 0;
                                var proajax_minute = 0; //统一加一分钟
                            }
                            fonthtml+='（共计'+pcount+'条物料数据，已完成处理'+percent+'%，估计剩'+proajax_hour+'时'+proajax_minute+'分）';
                            if (!is_end) fonthtml+='...';
                            ajaxbar_font.innerHTML=fonthtml;
                            ajaxbar_pro.style.width = percent+'%';
                        }else{
                            var ajaxbar_end=document.getElementById('ajaxbar_end');
                            var is_end=(ajaxbar_end)?parseInt(ajaxbar_end.innerHTML):0;
                            if (is_end) ajaxbar_font.innerHTML='全部完成';
                            else ajaxbar_font.innerHTML='正在处理第'+pnum+'条...';
                            if (is_end) {
                                window.clearInterval(ajaxbar_time);
                                ajaxbar_pro.style.left = '0px';
                                ajaxbar_pro.style.width = '100%';
                            }
                        }
                        if (!is_end) AJAXBar.startAjax(url,page,start,pnum+1,psize,pcount);
                    }
                }
            }
        },
        proAjax : function () {
            var step=5;
            var ajaxbar_bar=document.getElementById('ajaxbar_bar');
            var ajaxbar_pro=document.getElementById('ajaxbar_pro');
            if(!ajaxbar_bar || !ajaxbar_pro)return false;
            ajaxbar_pro.style.width = '5%';
            max = parseInt(ajaxbar_bar.style.width.replace('px',''));
            left = parseInt(ajaxbar_pro.style.left.replace('px',''));
            if(left>=max)left=0;
            ajaxbar_pro.style.left = left+step+'px';
        },
        scanForm : function (form) {
            if(!form)return '';
            var ajaxbar_data='';
            for(var f=0;f<document.forms.length;f++){
                var ajaxbar_form=document.forms[f];
                if(ajaxbar_form.name==form){
                    for(var i=0;i<ajaxbar_form.length;i++){
                        var element=ajaxbar_form[i];
                        if(element.type=="button" || element.type=="submit" || element.type=="reset")continue;
                        if(element.type=="checkbox" || element.type=="radio"){
                            if(element.checked)ajaxbar_data+='&'+element.name+'='+encodeURIComponent(element.value);
                        }else{
                            ajaxbar_data+='&'+element.name+'='+encodeURIComponent(element.value);
                        }
                    }
                    break;
                }
            }
            return ajaxbar_data;
        },
        createWin : function () {
            var ajaxbar_scrleft = document.body.scrollLeft;
            var ajaxbar_scrtop = document.body.scrollTop;
            var ajaxbar_cwidth = document.body.clientWidth;
            var ajaxbar_cheight = document.body.clientHeight;
            var ajaxbar_swidth = document.body.scrollWidth;
            if(window.scrollMaxX != undefined)ajaxbar_swidth += window.scrollMaxX;//兼容火狐
            var ajaxbar_sheight = document.body.scrollHeight;
            if(window.scrollMaxY != undefined)ajaxbar_sheight += window.scrollMaxY;//兼容火狐
            var ajaxbar_awidth = window.screen.availWidth;
            var ajaxbar_aheight = window.screen.availHeight;
            if(ajaxbar_scrleft) var ajaxbar_left = (ajaxbar_cwidth-500)/2+ajaxbar_scrleft+'px';
            else var ajaxbar_left = '50%';
            if(ajaxbar_scrtop) var ajaxbar_top = (ajaxbar_cheight-300)/2+ajaxbar_scrtop+'px';
            else var ajaxbar_top = '100px';
            if(ajaxbar_swidth > ajaxbar_awidth) var ajaxbar_width = ajaxbar_swidth+'px';
            else var ajaxbar_width = '100%';
            if(ajaxbar_sheight > ajaxbar_aheight) var ajaxbar_height = ajaxbar_sheight+'px';
            else var ajaxbar_height = '100%';
            var ajaxbar_win = document.createElement('div');
            ajaxbar_win.setAttribute('id','ajaxbar_win');
            ajaxbar_win.style.cssText = "display:none;position:absolute;margin-left:-250px;width:500px;border:5px solid #0D93BF;background:#fff;z-index:999;";
            ajaxbar_win.style.left = ajaxbar_left;
            ajaxbar_win.style.top = ajaxbar_top;
            document.body.appendChild(ajaxbar_win);
            ajaxbar_win.innerHTML += '<div id="ajaxbar_head" style="background:#0D93BF;height:22px;cursor:pointer;" onclick="AJAXBar.resizeWin()"></div>';
            ajaxbar_win.innerHTML += '<div id="ajaxbar_body"></div>';
            ajaxbar_win.innerHTML += '<div id="ajaxbar_body"></div>';
            var ajaxbar_head=document.getElementById('ajaxbar_head');
            ajaxbar_head.innerHTML += '<span style="font-size:12px;font-weight:bold;color:white;float:left;">物料检查进度条</span>';
            ajaxbar_head.innerHTML += '<a style="font-size:12px;font-weight:bold;color:#FF0000;text-decoration:none;float:right;padding:1px 3px;background:#FFF;" href="javascript:AJAXBar.closeWin()">X</a>';
            var ajaxbar_body=document.getElementById('ajaxbar_body');
            ajaxbar_body.innerHTML += '<div id="ajaxbar_font" style="margin:10px;"></div>';
            ajaxbar_body.innerHTML += '<div id="ajaxbar_bar" style="width:470px;height:20px;margin:10px;overflow:hidden;border:1px solid #0D93BF;"></div>';
            ajaxbar_body.innerHTML += '<div id="ajaxbar_view" style="width:470px;height:200px;overflow:scroll;margin:10px;border:1px solid #0D93BF;"></div>';
            var ajaxbar_bar=document.getElementById('ajaxbar_bar');
            ajaxbar_bar.innerHTML = '<div id="ajaxbar_pro" style="position:relative;left:0px;height:20px;background:#0D93BF;"></div>';
            var ajaxbar_lock = document.createElement('div');
            ajaxbar_lock.setAttribute('id','ajaxbar_lock');
            ajaxbar_lock.style.cssText = "position:absolute;top:0px;left:0px;text-align:center;filter:alpha(opacity=70);opacity:0.3;background:#000;z-index:998;";
            ajaxbar_lock.style.width = ajaxbar_width;
            ajaxbar_lock.style.height = ajaxbar_height;
            document.body.appendChild(ajaxbar_lock);
        },
        removeWin : function () {
            window.clearInterval(ajaxbar_time);
            ajaxbar_xmlhttp.abort();
            var ajaxbar_win=document.getElementById('ajaxbar_win');
            document.body.removeChild(ajaxbar_win);
            var ajaxbar_lock=document.getElementById('ajaxbar_lock');
            document.body.removeChild(ajaxbar_lock);
            window.location.reload();
        },
        openWin : function () {
            var ajaxbar_win=document.getElementById('ajaxbar_win');
            var ajaxbar_body=document.getElementById('ajaxbar_body');
            ajaxbar_win.style.display='';
            ajaxbar_body.style.display='';
        },
        closeWin : function () {
            AJAXBar.removeWin();
        },
        resizeWin : function (){
            var ajaxbar_body=document.getElementById('ajaxbar_body');
            var ajaxbar_dis=ajaxbar_body.style.display;
            ajaxbar_body.style.display=(ajaxbar_dis=='')?'none':'';
        },
        evtKey : function (evt) {
            evt = (evt) ? evt : ((window.event) ? window.event : '')
            var key = evt.keyCode?evt.keyCode:evt.which;
            if(key=='27')AJAXBar.resizeWin();
        }
    }
    document.onkeydown=AJAXBar.evtKey;
</script>
