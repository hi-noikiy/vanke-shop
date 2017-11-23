ALTER TABLE `sc_member`
	ADD COLUMN `city_id` INT(5) UNSIGNED NOT NULL COMMENT '采购员所需城市ID' AFTER `freeze_commission`,
	ADD COLUMN `project_id` INT(5) UNSIGNED NOT NULL COMMENT '采购员所属项目ID' AFTER `city_id`;

ALTER TABLE `sc_store`
	CHANGE COLUMN `store_city_id` `store_city_id` VARCHAR(50) NULL DEFAULT NULL COMMENT '城市中心ID' AFTER `store_type_name`,
	ADD COLUMN `store_city_name` VARCHAR(250) NULL DEFAULT NULL COMMENT '城市中心名称' AFTER `store_city_id`;
