ALTER TABLE `sc_admin`
	ADD COLUMN `city_id` INT(3) NULL DEFAULT '0' COMMENT '城市中心ID' AFTER `admin_gid`;


ALTER TABLE `sc_store`
	ADD COLUMN `store_city_id` INT(3) NULL DEFAULT NULL COMMENT '城市中心ID' AFTER `store_type_name`;


ALTER TABLE `sc_store_joinin`
	ALTER `store_state` DROP DEFAULT;
ALTER TABLE `sc_store_joinin`
	CHANGE COLUMN `store_state` `store_state` INT(3) NOT NULL COMMENT '店铺认证状态 1申请认证' AFTER `city_center`;
