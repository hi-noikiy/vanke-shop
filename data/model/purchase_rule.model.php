<?php
/**
 * 系统文章
 *
 * 
 *
 *
 *
 */


class purchase_ruleModel{
	/**
	 * 查询所有系统文章
	 */
	public function getList(){
		$param	= array(
			'table'	=> 'purchase_rule'
		);
		return Db::select($param);
	}
	/**
	 * 根据编号查询一条
	 * 
	 * @param unknown_type $id
	 */
	public function getOneById($id){
		$param	= array(
			'table'	=> 'purchase_rule',
			'field'	=> 'purchase_rule_id',
			'value'	=> $id
		);
		return Db::getRow($param);
	}

	/**
	 * 根据编号查询一条
	 *
	 * @param unknown_type $id
	 */
	public function getOneByIdForShop($id){
		$param	= array(
			'table'	=> 'purchase_rule',
			'field'	=> 'purchase_rule_id',
			'value'	=> $id
		);
		$field ="purchase_rule_id as article_id,title as article_title,publish_department,object_person,content as article_content,attachment,publish_date as article_time,admin_id,city_id,city_name";
		return Db::getRow($param,$field);
	}
//	/**
//	 * 根据标识码查询一条
//	 * 
//	 * @param unknown_type $id
//	 */
//	public function getOneByCode($code){
//		$param	= array(
//			'table'	=> 'document',
//			'field'	=> 'doc_code',
//			'value'	=> $code
//		);
//		return Db::getRow($param);
//	}
	/**
	 * 更新
	 * 
	 * @param unknown_type $param
	 */
	public function update($param){
		return Db::update('purchase_rule',$param,"purchase_rule_id='{$param['purchase_rule_id']}'");
	}
/**
 * 插入
 * 
 * @param type $param
 * @return type
 */
        public function insert($param){
            return Db::insert('purchase_rule',$param);
        }
        
	/**
	 * 列表
	 *
	 * @param array $condition 检索条件
	 * @param obj $page 分页
	 * @return array 数组结构的返回结果
	 */
	public function getPurchaseRuleList($condition,$page=''){
		$param = array();
		$param['field'] = $condition['field'];
		$param['table'] = 'purchase_rule';
		$param['limit'] = $condition['limit'];
		$param['order']	= (empty($condition['order'])?'purchase_rule_id asc':$condition['order']);
		$result = Db::select($param,$page);
		return $result;
	}
}