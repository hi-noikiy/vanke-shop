ALTER TABLE `sc_store_joinin`
	ADD COLUMN `joinin_message_open` VARCHAR(250) NULL COMMENT '开店审核意见' AFTER `store_state`;

ALTER TABLE `sc_store_joinin`
	ADD COLUMN `rz_evaluation_audit` VARCHAR(250) NULL DEFAULT NULL COMMENT '评估审核附件（图片）' AFTER `joinin_message_open`;
