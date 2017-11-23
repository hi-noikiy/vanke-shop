ALTER TABLE `sc_city_centre`
	ADD COLUMN `bukrs` VARCHAR(50) NULL COMMENT '城市编号' AFTER `back`;

CREATE TABLE `sc_seller_attribute` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`att_name` VARCHAR(50) NOT NULL COMMENT '属性名称',
	`att_desc` VARCHAR(250) NOT NULL COMMENT '属性描述',
	`att_state` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '1为提交申请，2为已经处理，3为申请不通过',
	`store_id` INT(10) NULL DEFAULT NULL COMMENT '申请店铺ID',
	`store_name` VARCHAR(50) NULL DEFAULT NULL COMMENT '申请店铺名称',
	PRIMARY KEY (`id`)
)
COMMENT='店铺申请属性分类'
COLLATE='utf8_general_ci'
ENGINE=MyISAM;
