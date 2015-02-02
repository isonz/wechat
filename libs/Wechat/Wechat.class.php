<?php
class Wechat
{
	static private $_app_id = null;
	static private $_token = null;
	static private $_username = null;
	static private $_original_id = null;
	static private $_name = null;
	static private $_app_secret = null;
	static private $_encodingAesKey = null;
	
	static private $_signature = null;
	static private $_timestamp = null;
	static private $_nonce = null;
	static private $_echostr = null;
	static private $_encrypt_type = "aes";
	static private $_msg_signature = null;
	
	static private $_Merch = array();
	
	static private function _init()
	{
		if(!self::$_signature) self::$_signature = isset($_REQUEST["signature"]) ? $_REQUEST["signature"] : null;
		if(!self::$_timestamp) self::$_timestamp = isset($_REQUEST["timestamp"]) ? $_REQUEST["timestamp"] : null;
		if(!self::$_nonce) self::$_nonce = isset($_REQUEST["nonce"]) ? $_REQUEST["nonce"] : null;
		if(!self::$_echostr) self::$_echostr = isset($_REQUEST["echostr"]) ? $_REQUEST["echostr"] :  null;
		if(!self::$_encrypt_type) self::$_encrypt_type = isset($$_REQUEST["encrypt_type"]) ? $_REQUEST["encrypt_type"] :  null;
		if(!self::$_msg_signature) self::$_msg_signature = isset($_REQUEST["msg_signature"]) ? $_REQUEST["msg_signature"] :  null;
		
		if(!self::$_token) self::$_token = self::_getToken();
		if(!self::$_Merch) self::$_Merch = Merchants::getInfoByToken(self::$_token);
		self::$_app_id = isset(self::$_Merch['app_id']) ? self::$_Merch['app_id'] : null;
		self::$_username = isset(self::$_Merch['username']) ? self::$_Merch['username'] : null;
		self::$_original_id = isset(self::$_Merch['original_id']) ? self::$_Merch['original_id'] : null;
		self::$_name = isset(self::$_Merch['name']) ? self::$_Merch['name'] : null;
		self::$_app_secret = isset(self::$_Merch['app_secret']) ? self::$_Merch['app_secret'] : null;
		self::$_encodingAesKey = isset(self::$_Merch['encodingAesKey']) ? self::$_Merch['encodingAesKey'] : null;
		
		$is_access = isset(self::$_Merch['is_access']) ? self::$_Merch['is_access'] : null;
		if('N' == $is_access) self::_valid();		//验证微信后台的服务器配置合法性
	}
	
	//--- 接收微信的token,默认为绑定地址的最后的参数
	static private function _getToken()
	{
		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
		if(!$uri) header("Location: /");
		$uri = explode("/", $uri);
		$wxtoken = isset($uri[2]) ? $uri[2] : null;
		$wxtoken = explode("?", $wxtoken);
		$wxtoken = isset($wxtoken[0]) ? $wxtoken[0] : null;
		return $wxtoken;
	}
	
	static private function _valid()
	{
		if(self::_checkSignature()){
			echo self::$_echostr;  		//第一次微信后台设置开发环境时才需要
			Merchants::update(array('app_id' =>self::$_app_id), array('is_access'=>'Y'));
			exit;
		}
	}
	
	static private function _checkSignature()
	{	
		$tmpArr = array(self::$_token, self::$_timestamp, self::$_nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == self::$_signature){
			return true;
		}else{
			return false;
		}
	}
	
	static public function decrypt()
	{
		self::_init();
		$from_xml = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : null;
		if(!$from_xml) return false;

		/* //原始格式
		$xml_tree = new DOMDocument();
		$xml_tree->loadXML($from_xml);
		$array_e = $xml_tree->getElementsByTagName('Encrypt');
		$array_s = $xml_tree->getElementsByTagName('MsgSignature');
		$encrypt = $array_e->item(0)->nodeValue;
		$msg_sign = $array_s->item(0)->nodeValue;
		$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
		$from_xml = sprintf($format, $encrypt);
		*/
		
		$msg = '';
		$pc = new WXBizMsgCrypt(self::$_token, self::$_encodingAesKey, self::$_app_id);
		$errCode = $pc->decryptMsg(self::$_msg_signature, self::$_timestamp, self::$_nonce, $from_xml, $msg);
		if ($errCode == 0) return $msg;
		
		ABase::log($errCode);
		return false;
	}
	
	static public function postMsg($type='text', array $data=array())
	{
		self::_init();
		$touserName = 'isonzh';
		$fromuserName = 'PlacentinSwiss';
		$createtime = time();
		$msg = self::msgType($type, $data);
		$text = "<xml><ToUserName><![CDATA[$touserName]]></ToUserName><FromUserName><![CDATA[$fromuserName]]></FromUserName><CreateTime>$createtime</CreateTime><MsgType><![CDATA[$type]]></MsgType>$msg</xml>";
		
		$pc = new WXBizMsgCrypt(self::$_token, self::$_encodingAesKey, self::$_app_id);
		$encryptMsg = '';
		$errCode = $pc->encryptMsg($text, self::$_timestamp, self::$_nonce, $encryptMsg);
		/*
		if ($errCode == 0) {
			print("加密后: " . $encryptMsg . "\n");
		} else {
			print($errCode . "\n");
		}
		*/
		
		
		$xml_tree = new DOMDocument();
		$xml_tree->loadXML($encryptMsg);
		$array_e = $xml_tree->getElementsByTagName('Encrypt');
		$array_s = $xml_tree->getElementsByTagName('MsgSignature');
		$encrypt = $array_e->item(0)->nodeValue;
		$msg_sign = $array_s->item(0)->nodeValue;
		
		$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
		$from_xml = sprintf($format, $encrypt);
		
		// 第三方收到公众号平台发送的消息
		$msg = '';
		$pc = new WXBizMsgCrypt(self::$_token, self::$_encodingAesKey, self::$_app_id);
		
		$errCode = $pc->decryptMsg($msg_sign, self::$_timestamp, self::$_nonce, $from_xml, $msg);
		if ($errCode == 0) {
			print("$msg \n");
		} else {
			print($errCode . "\n");
		}
	}
	
	static public function msgType($type='text', array $data=array())
	{
		switch ($type){
			case 'image':
				
				break;
			case 'voice':
				
				break;
			case 'video':
				
				break;
			case 'music':
				
				break;
			case 'news':
				
				break;
			case 'text':
			default:
				$content = isset($data['content']) ? $data['content'] : '';
				return "<Content><![CDATA[$content]]></Content>";
		}
	}
	
	static public function getMsg()
	{

	}
	
	static public function attention($type='add/cancel', $msg)
	{
		$xml_tree = new DOMDocument();
		$xml_tree->loadXML($msg);
		
		$ToUserName = $xml_tree->getElementsByTagName('ToUserName');
		$ToUserName = $ToUserName->item(0)->nodeValue;
		
		$FromUserName = $xml_tree->getElementsByTagName('FromUserName');
		$FromUserName = $ToUserName->item(0)->nodeValue;
		
		$CreateTime = $xml_tree->getElementsByTagName('CreateTime');
		$CreateTime = $CreateTime->item(0)->nodeValue;
		
		$MsgType = $xml_tree->getElementsByTagName('MsgType');
		$MsgType = $MsgType->item(0)->nodeValue;
		
		$Event = $xml_tree->getElementsByTagName('Event');
		$Event = $Event->item(0)->nodeValue;
		
		$EventKey = $xml_tree->getElementsByTagName('EventKey');
		$EventKey = $EventKey->item(0)->nodeValue;
		
		if('add'== $type){
			$data = array(
				'merch_id'		=> $ToUserName,
				'open_id'		=> $FromUserName,
				'is_attention'	=> 1,
				'create_at'		=> $CreateTime,
			);
			Users::setData($data);
		}else{
			Users::updateAttention($ToUserName, $FromUserName, 0);
		}
		
		$data = array(
				'merch_id'		=> $ToUserName,
				'user_id'		=> $FromUserName,
				'create_at'		=> $CreateTime,
				'msg_type'		=> $MsgType,
				'contents'		=> json_encode(array('Event'=>$Event, 'EventKey'=>$EventKey)),
				'tofrom'		=> 0
		);
		return Chats::setData($data);

	}
	
}
