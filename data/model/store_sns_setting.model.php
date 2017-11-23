<?php
/**
 * 店铺动态自动发布
 *
 * 
 *
 *
 *
 */


class store_sns_settingModel extends Model {
    public function __construct(){
        parent::__construct('store_sns_setting');
    }

    /**
     * 获取单条动态设置设置信息
     * 
     * @param unknown $condition
     * @param string $field
     * @return array
     */
    public function getStoreSnsSettingInfo($condition, $field = '*') {
        return $this->field($field)->where($condition)->find();
    }
    
    /**
     * 保存店铺动态设置
     * 
     * @param unknown $insert
     * @return boolean
     */
    public function saveStoreSnsSetting($insert) {
        return $this->insert($insert);
    }


    /**
     * 更新店铺动态设置
     * //@cary_fix 修复设置不能保存的问题
     * @param unknown $insert
     * @return boolean
     */
    public function updateStoreSnsSetting($update, $condition){
        return $this->where($condition)->update($update);
    }
}