<?php
class Wechat
{
	static private $_user_id = 0;
	static private $_merch_id = 0;
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
	
	static public $touser = null;
	static public $fromuser = null;
	static public $createtime = null;
	static public $msgtype = null;
	
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
		self::$_merch_id = isset(self::$_Merch['id']) ? self::$_Merch['id'] : 0;
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
		
		//$from_xml = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : null;
		$from_xml = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : ($from_xml = isset($_POST["postxml"]) ? $_POST["postxml"] : null);
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
		$touserName = self::$fromuser;
		$fromuserName = self::$touser;
		$createtime = time();
		$msg = self::postMsgType($type, $data);
		$text = "<xml><ToUserName><![CDATA[$touserName]]></ToUserName><FromUserName><![CDATA[$fromuserName]]></FromUserName><CreateTime>$createtime</CreateTime><MsgType><![CDATA[$type]]></MsgType>$msg</xml>";
			
		$pc = new WXBizMsgCrypt(self::$_token, self::$_encodingAesKey, self::$_app_id);
		$encryptMsg = '';
		$errCode = $pc->encryptMsg($text, self::$_timestamp, self::$_nonce, $encryptMsg);

		if (0 != $errCode){
			ABase::log($errCode);
			exit('');
		}
		
		$data = array(
			'merch_id'		=> self::$_merch_id,
			'user_id'		=> self::$_user_id,
			'create_at'		=> $createtime,
			'msg_type'		=> $type,
			'contents'		=> json_encode($data),
			'tofrom'		=> 1
		);
		$id = Chats::setData($data);
		
		exit($encryptMsg);
	}
	
	static public function postMsgType($type='text', array $data=array())
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
	
	static public function getMsgType($xml_tree)
	{
		switch (self::$msgtype){
			case 'image':
				$PicUrl = $xml_tree->getElementsByTagName('PicUrl');
				$PicUrl = $PicUrl->item(0)->nodeValue;
				$MediaId = $xml_tree->getElementsByTagName('MediaId');
				$MediaId = $MediaId->item(0)->nodeValue;
				$MsgId = $xml_tree->getElementsByTagName('MsgId');
				$MsgId = $MsgId->item(0)->nodeValue;
				return array('PicUrl'=> $PicUrl, 'MediaId'=>$MediaId, 'MsgId'=> $MsgId);
			case 'voice':
				$Format = $xml_tree->getElementsByTagName('Format');
				$Format = $Format->item(0)->nodeValue;
				$MediaId = $xml_tree->getElementsByTagName('MediaId');
				$MediaId = $MediaId->item(0)->nodeValue;
				$Recognition = $xml_tree->getElementsByTagName('Recognition');
				$Recognition = isset($Recognition->item(0)->nodeValue) ? $Recognition->item(0)->nodeValue : null;
				$MsgId = $xml_tree->getElementsByTagName('MsgId');
				$MsgId = $MsgId->item(0)->nodeValue;
				return array('Format'=> $Format, 'MediaId'=>$MediaId, 'Recognition'=>$Recognition, 'MsgId'=> $MsgId);
			case 'video':
				$ThumbMediaId = $xml_tree->getElementsByTagName('ThumbMediaId');
				$ThumbMediaId = $ThumbMediaId->item(0)->nodeValue;
				$MediaId = $xml_tree->getElementsByTagName('MediaId');
				$MediaId = $MediaId->item(0)->nodeValue;
				$MsgId = $xml_tree->getElementsByTagName('MsgId');
				$MsgId = $MsgId->item(0)->nodeValue;
				return array('ThumbMediaId'=> $ThumbMediaId, 'MediaId'=>$MediaId, 'MsgId'=> $MsgId);
			case 'location':
				$Location_X = $xml_tree->getElementsByTagName('Location_X');
				$Location_X = $Location_X->item(0)->nodeValue;
				$Location_Y = $xml_tree->getElementsByTagName('Location_Y');
				$Location_Y = $Location_Y->item(0)->nodeValue;
				$Scale = $xml_tree->getElementsByTagName('Scale');
				$Scale = $Scale->item(0)->nodeValue;
				$Label = $xml_tree->getElementsByTagName('Label');
				$Label = $Label->item(0)->nodeValue;
				$MsgId = $xml_tree->getElementsByTagName('MsgId');
				$MsgId = $MsgId->item(0)->nodeValue;
				return array('Location_X'=> $Location_X, 'Location_Y'=>$Location_Y, 'Scale'=>$Scale, 'Label'=>$Label, 'MsgId'=> $MsgId);
			case 'link':
				$Title = $xml_tree->getElementsByTagName('Title');
				$Title = $Title->item(0)->nodeValue;
				$Description = $xml_tree->getElementsByTagName('Description');
				$Description = $Description->item(0)->nodeValue;
				$Url = $xml_tree->getElementsByTagName('Url');
				$Url = $Url->item(0)->nodeValue;
				$MsgId = $xml_tree->getElementsByTagName('MsgId');
				$MsgId = $MsgId->item(0)->nodeValue;
				return array('Title'=> $Title, 'Description'=>$Description, 'Url'=>$Url, 'MsgId'=> $MsgId);					
			case 'text':
			default:
				$Content = $xml_tree->getElementsByTagName('Content');
				$Content = $Content->item(0)->nodeValue;
				$MsgId = $xml_tree->getElementsByTagName('MsgId');
				$MsgId = $MsgId->item(0)->nodeValue;
				return array('Content'=> $Content,'MsgId'=> $MsgId);
		}
	}
	
	static public function getMsg($msg)
	{
		$xml_tree = new DOMDocument();
		$xml_tree->loadXML($msg);
		$contents = self::getMsgType($xml_tree);
		
		$user = Users::getInfo(self::$_merch_id, self::$fromuser);
		self::$_user_id = isset($user['id']) ? $user['id'] : 0;
		
		$data = array(
			'merch_id'		=> self::$_merch_id,
			'user_id'		=> self::$_user_id,
			'create_at'		=> self::$createtime,
			'msg_type'		=> self::$msgtype,
			'contents'		=> json_encode($contents),
			'tofrom'		=> 0
		);
		$id = Chats::setData($data);
		
		self::postMsg('text', array('content'=>'欢迎光临！'));
	}
	
	static public function attention($type='subscribe/unsubscribe', $msg)
	{		
		if(self::$touser != self::$_original_id) return false;
		$open_id = self::$fromuser;
		
		$user = Users::getInfo(self::$_merch_id, $open_id);
		self::$_user_id = isset($user['id']) ? $user['id'] : 0;
		
		if('subscribe'== $type){
			if(!self::$_user_id){
				$data = array(
					'merch_id'		=> self::$_merch_id,
					'open_id'		=> $open_id,
					'is_attention'	=> 1,
					'create_at'		=> self::$createtime,
				);
				self::$_user_id = Users::setData($data);
			}else{
				Users::updateAttention(self::$_merch_id, $open_id, 1);
			}
		}else{
			Users::updateAttention(self::$_merch_id, $open_id, 0);
		}
		
		$data = array(
				'merch_id'		=> self::$_merch_id,
				'user_id'		=> self::$_user_id,
				'create_at'		=> self::$createtime,
				'name'			=> $type,
				'tofrom'		=> 0
		);
		return Events::setData($data);
	}
	
}
