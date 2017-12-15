<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/12/15
 * Time: 上午11:13
 */
?>
<div class="page">
    <div class="fixed-bar">
        <?php include template('supplier/top');?>
    </div>
    <div class="fixed-empty"></div>
    <form method="get" name="formSearch">
        <input type="hidden" value="supplier" name="act">
        <input type="hidden" value="join_list" name="op">
        <table class="tb-type1 noborder search">
            <tbody>
            <tr>
                <th><label for="store_name">供应商名称：</label></th>
                <td><input type="text" value="<?php echo $output['store_name'];?>" name="store_name"
                           style="width: 250px" id="store_name" class="txt"></td>
                <th><label for="owner_and_name">供应商账号：</label></th>
                <td><input type="text" value="<?php echo $output['number'];?>" name="number" id="number" class="txt"></td>
                <th><label>认证状态</label></th>
                <td>
                    <select name="type" class="querySelect">
                        <?php if(!empty($output['join_type']) && is_array($output['join_type'])){ ?>
                            <?php foreach($output['join_type'] as $k => $v){ ?><?php if($k==$_GET['joinin_state']) { echo 'selected'; }?>
                                <option value="<?php echo $k;?>" <?php if(!empty($_GET['type'])){ echo $k==$_GET['type'] ? 'selected':'';}else{echo $k==STORE_JOIN_STATE_RZ ? 'selected':'';}?> >
                                    <?php echo $v;?>
                                </option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </td>

                <!--城市中心-->
                <th><label>城市中心</label></th>
                <td>
                    <select name="city" class="querySelect">
                        <?php if(count($output['city_list'])>0){?>
                            <option value ="" <?php if(empty($_GET['city_id'])) echo 'selected'; ?>  >全部</option>
                            <?php foreach($output['city_list'] as $city_centre){?>
                                <option value ="<?php echo $city_centre['id'];?>" <?php if($_GET['city'] == $city_centre['id']) echo 'selected'; ?>  ><?php echo $city_centre['city_name'];?></option>
                            <?php } }?>
                    </select>
                </td>

                <td><a href="javascript:document.formSearch.submit();" class="btn-search " title="<?php echo $lang['nc_query'];?>">&nbsp;</a>
                    <?php if($output['number'] != '' or $output['store_name'] != '' or $output['type'] != ''  or $output['city'] != ''){?>
                        <a href="index.php?act=supplier&op=join_list" class="btns " title="<?php echo $lang['nc_cancel_search'];?>"><span><?php echo $lang['nc_cancel_search'];?></span></a>
                    <?php }?></td>
            </tr>
            </tbody>
        </table>
    </form>
    <table class="table tb-type2" id="prompt">
    </table>
    <form method="post" id="store_form" name="store_form">
        <table class="table tb-type2">
            <thead>
            <tr class="thead">
                <th>供应商</th>
                <th>供应商账号</th>
                <th>公司所在地</th>
                <th class="align-center">认证状态</th>
                <th class="align-center">所属城市公司</th>
                <th class="align-center">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($output['list']) && is_array($output['list'])){ ?>
                <?php foreach($output['list'] as $k => $v){ ?>
                    <tr class="hover edit">
                        <td><?php echo $v['company_name'];?></td>
                        <td><?php echo $v['member_name'];?></td>
                        <td class="w240"><?php echo $v['company_address'];?></td>
                        <td class="align-center"><?php echo $output['join_type'][$v['joinin_state']]?></td>
                        <td class="align-center"><?php echo $v['city_name'];?></td>
                        <td class="w72 align-center">
                            <?php if($v['joinin_state'] == STORE_JOIN_STATE_RZ){?>
                                <a href="index.php?act=supplier&op=examine_join&city=<?php echo $v['city_center'];?>&member=<?php echo $v['member_id'];?>">审核</a>
                            <?php }else{?>
                                <a href="index.php?act=store&op=store_joinin_detail&member_id=<?php echo $v['member_id'];?>&city=<?php echo $v['city_center'];?>">查看</a>
                            <?php }?>
                        </td>
                    </tr>
                <?php } ?>
            <?php }else { ?>
                <tr class="no_data">
                    <td colspan="10"><?php echo $lang['nc_no_record'];?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr class="tfoot">
                <td></td>
                <td colspan="15">
                    <?php if(!empty($output['list']) && is_array($output['list'])){ ?>
                        <div class="pagination"><?php echo $output['page'];?></div>
                    <?php } ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.edit.js" charset="utf-8"></script>
<script>
    function audit_submit(type){
        $('#type').val(type);
        $("#store_form").submit();
        return true;
    }
</script>

