<?php
class Events extends ABase
{
	static public $_class = __CLASS__;
	static public $_table = '#_events';
	
	static public function getData(array $condition)
	{
		if(!$condition) return false;
		$info = self::getOne($condition, "*");
		return $info;
	}
	
	static public function getInfo($merch_id, $user_id)
	{
		$condition = array('merch_id' => $merch_id, 'user_id' => $user_id);
		return self::getData($condition);
	}
	

	static public function setData($data)
	{
		//DB::Debug();
		return self::insert($data);
	}
	
	
	
}


