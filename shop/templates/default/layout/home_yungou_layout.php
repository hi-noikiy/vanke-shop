<?php ?>
<?php include template('layout/common_layout');?>
<?php include template('layout/cur_local');?>

<!--屏蔽导航上的商城商品菜单-->
<style>
    .public-nav-layout .all-category:hover .category { display:none;};
</style>
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/yungou_djs_min.js" ></script>

<?php require_once($tpl_file);?>
<script src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/home_index.js" ></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/waypoints.js"></script>
<script language="JavaScript">
 //浮动导航  waypoints.js
 /*
$("#ncgCategory").waypoint(function(event, direction) {
	$(this).parent().toggleClass('sticky', direction === "down");
	event.stopPropagation();
});
 */
//鼠标触及更替li样式
$(document).ready(function(){
    $("#list").hide();
    $("#button_show").click(function(){
        $("#list").toggle();
    });
    $("#button_close").click(function(){
        $("#list").hide();
    });
});
</script>
<?php require_once template('footer');?>
</body></html>