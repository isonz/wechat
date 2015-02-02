<?php
class Users extends ABase
{
	static public $_class = __CLASS__;
	static public $_table = '#_users';
	
	static public function getData(array $condition)
	{
		if(!$condition) return false;
		$info = self::getOne($condition, "*");
		return $info;
	}
	
	static public function getInfo($merch_id, $open_id)
	{
		$condition = array('merch_id' => $merch_id, 'open_id' => $open_id);
		return self::getData($condition);
	}
	

	static public function setData($data)
	{
		//DB::Debug();
		return self::insert($data);
	}
	
	static public function updateAttention($merch_id, $open_id, $value)
	{
		return self::update(array('merch_id' => $merch_id, 'open_id' => $open_id), array('is_attention'=>$value));
	}
	
}


