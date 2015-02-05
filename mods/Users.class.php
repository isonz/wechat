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
		if(!$merch_id || !$open_id) return false;
		$condition = array('merch_id' => $merch_id, 'open_id' => $open_id);
		return self::getData($condition);
	}
	

	static public function setData($data)
	{
		//DB::Debug();
		$merch_id = isset($data['merch_id']) ? $data['merch_id'] : 0;
		$open_id = isset($data['open_id']) ? $data['open_id'] : 0;
		if(!$merch_id || !$open_id) return false;
		
		if(!$info = self::getInfo($merch_id, $open_id)){
			return self::insert($data);
		}else{
			unset($data['merch_id'], $data['open_id'], $data['create_at']);
			foreach ($data as $k=>$v){
				if(0 == $v) continue;
				if(!$v) unset($data[$k]);
			}
			self::update(array('merch_id' => $merch_id, 'open_id' => $open_id), $data); 
			return $info['id'];
		}
	}
	
}


