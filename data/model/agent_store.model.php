<?php
/**
 * 卖家账号模型
 *
 * 
 *
 *
 *
 */

class agent_storeModel extends Model{

    public function __construct(){
        parent::__construct('agent_store');
    }


	/*
	 * 增加 
	 * @param array $param
	 * @return bool
	 */
    public function addStore($param){
        return $this->insert($param);	
    }
	

}
