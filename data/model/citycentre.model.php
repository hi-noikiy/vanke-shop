<?php


class citycentreModel{

	public function getList($condition,$page=''){
		$param	= array();
		$param['table']	= 'city_centre';
		$param['order']	= $condition['order'] ? $condition['order'] : 'id';
		return Db::select($param,$page);
	}

}