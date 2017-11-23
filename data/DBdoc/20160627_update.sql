

CREATE TABLE `sc_city_centre` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city_name` varchar(50) NOT NULL COMMENT '城市名称',
  `city_state` tinyint(1) NOT NULL COMMENT '城市状态 1开放 2未开放',
  `back` varchar(200) DEFAULT NULL COMMENT '备用字段',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8
ALTER TABLE `sc_city_centre`
	CHANGE COLUMN `id` `id` INT(10) NOT NULL AUTO_INCREMENT FIRST,
	ADD PRIMARY KEY (`id`);


ALTER TABLE `sc_store_joinin`
	ADD COLUMN `city_center` INT(5) NOT NULL COMMENT '城市中心id' AFTER `store_type_name`;


ALTER TABLE `sc_store_joinin`
	ADD COLUMN `store_state` TINYINT(1) NOT NULL COMMENT '店铺认证状态 1申请认证' AFTER `city_center`;

