<?php
class Merchants extends ABase
{
	static public $_class = __CLASS__;
	static public $_table = '#_merchants';
	
	static public function getInfo(array $condition)
	{
		if(!$condition) return false;
		$info = self::getOne($condition, "*");
		return $info;
	}
	
	static public function getInfoByAppId($app_id)
	{
		$condition = array('app_id' => $app_id);
		return self::getInfo($condition);
	}
	
	static public function getInfoByToken($token)
	{
		$condition = array('token' => $token);
		return self::getInfo($condition);
	}
		

	static public function setData($name, $value)
	{
		//DB::Debug();
		$val = self::getValue($name);
		if(is_array($val)){
			self::insert(array('name'=>$name, 'value'=>$value));
		}else{
			self::update(array('name'=>$name), array('value'=>$value));
		}
	}
	
	
	
}


