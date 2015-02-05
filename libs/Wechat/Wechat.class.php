<?php
class Wechat
{
	static private $_user_id = 0;
	static private $_merch_id = 0;
	static private $_app_id = null;
	static private $_token = null;
	static private $_is_encrypt = 0;
	static private $_username = null;
	static private $_original_id = null;
	static private $_name = null;
	static private $_app_secret = null;
	static private $_encodingAesKey = null;
	static private $_access_token = null;
	static private $_web_access_token = null;
	
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
	
	static public $accesstokenurl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=#APPID#&secret=#APPSECRET#";
	static public $wxuserinfourl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=#ACCESS_TOKEN#&openid=#OPENID#&lang=zh_CN";
		
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
		self::$_is_encrypt = isset(self::$_Merch['is_encrypt']) ? self::$_Merch['is_encrypt'] : 0;
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
	
	//-- 获取普通access_token {"access_token":"BD58...","expires_in":7200}，在调用前先检查是否已经过期。
	static private function _getAccessToken()
	{
		self::$_access_token = isset(self::$_Merch['access_token']) ? self::$_Merch['access_token'] : null;
		$access_token_expires = isset(self::$_Merch['access_token_expires']) ? (int)self::$_Merch['access_token_expires'] : 0;
		
		if(!self::$_access_token || $access_token_expires < time()){
			$url = self::$accesstokenurl;
			$url = str_replace("#APPID#", self::$_app_id, $url);
			$url = str_replace("#APPSECRET#", self::$_app_secret, $url);
			$info = Func::curlGet($url);
			ABase::log("access token url: ".$url, "access_token", "Info");
			ABase::log("access token info: ".$info, "access_token", "Info");
			
			$info = json_decode($info, true);
			$access_token = isset($info['access_token']) ? $info['access_token'] : null;
			$expires_in = isset($info['expires_in']) ? $info['expires_in'] : null;
			if(!$access_token) return false;
			
			$condition = array('id' => self::$_merch_id);
			$data = array("access_token"=>$access_token, "access_token_expires"=>(time()+$expires_in));
			Merchants::update($condition, $data);
			self::$_access_token = $access_token;
		}

		//self::$_web_access_token = isset(self::$_Merch['web_access_token']) ? self::$_Merch['web_access_token'] : null;
		//$web_access_token_expires = isset(self::$_Merch['web_access_token_expires']) ? self::$_Merch['web_access_token_expires'] : 0;
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
		if(!self::$_is_encrypt) return $from_xml;
		
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
		
		if(self::$_is_encrypt){
			$pc = new WXBizMsgCrypt(self::$_token, self::$_encodingAesKey, self::$_app_id);
			$encryptMsg = '';
			$errCode = $pc->encryptMsg($text, self::$_timestamp, self::$_nonce, $encryptMsg);
			if (0 != $errCode){
				ABase::log($errCode);
				exit('');
			}
		}else{
			$encryptMsg = $text;
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
				$media_id = isset($data['media_id']) ? $data['media_id'] : '';
				return "<Image><MediaId><![CDATA[$media_id]]></MediaId></Image>";
			case 'voice':
				$media_id = isset($data['media_id']) ? $data['media_id'] : '';
				return "<Voice><MediaId><![CDATA[$media_id]]></MediaId></Voice>";
			case 'video':
				$media_id = isset($data['media_id']) ? $data['media_id'] : '';
				$title = isset($data['title']) ? $data['title'] : '';
				$description = isset($data['description']) ? $data['description'] : '';
				return "<Video><MediaId><![CDATA[$media_id]]></MediaId><Title><![CDATA[$title]]></Title><Description><![CDATA[$description]]></Description></Video> ";
			case 'music':
				$media_id = isset($data['media_id']) ? $data['media_id'] : '';
				$title = isset($data['title']) ? $data['title'] : '';
				$description = isset($data['description']) ? $data['description'] : '';
				$music_url = isset($data['music_url']) ? $data['music_url'] : '';
				$hq_music_url = isset($data['hq_music_url']) ? $data['hq_music_url'] : '';
				return "<Music><Title><![CDATA[$title]]></Title><Description><![CDATA[$description]]></Description><MusicUrl><![CDATA[$music_url]]></MusicUrl><HQMusicUrl><![CDATA[$hq_music_url]]></HQMusicUrl><ThumbMediaId><![CDATA[$media_id]]></ThumbMediaId></Music>";
			case 'news':
				$count = isset($data['count']) ? $data['count'] : 0;
				$xml = "<Articles>";
				$items = isset($data['items']) ? $data['items'] : array();
				foreach ($items as $item){
					$title = isset($item['title']) ? $item['title'] : '';
					$description = isset($item['description']) ? $item['description'] : '';
					$picurl = isset($item['picurl']) ? $item['picurl'] : '';
					$url = isset($item['url']) ? $item['url'] : '';
					$xml = $xml."<item><Title><![CDATA[$title]]></Title><Description><![CDATA[$description]]></Description><PicUrl><![CDATA[$picurl]]></PicUrl><Url><![CDATA[$url]]></Url></item>";
				}
				$xml = $xml."</Articles>";
				return $xml;
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
		
		//处理逻辑，回复对应的信息类型和内容。
		
		self::postMsg('text', array('content'=>'欢迎光临！'));
		//self::postMsg('image', array("media_id"=>"6G1UZfrneUZkcF8umKf6CfeLeprVufn8GMTNkF_aqjRzkrDdw76kzE1BnOP1VBaE"));
		//self::postMsg('voice', array("media_id"=>"tJjTL415AFRpC8oo5z8pdEJ9cvc4YGwxneJPSWphgASG5yNqNkSCrZIEz5toOvBp"));
		//self::postMsg('video', array("media_id"=>"", "title"=>"", 'description'=>""));
		//self::postMsg('music', array("media_id"=>"", "title"=>"", 'description'=>"", "music_url"=>"", "hq_music_url"=>""));
		/*
		 self::postMsg('news', array("count"=>0, "items"=>array(
			array("title"=>"","description"=>"","picurl"=>"","url"=>""),
			array("title"=>"","description"=>"","picurl"=>"","url"=>""),
		)));
		*/
	}
	
	static public function attention($type='subscribe', $msg)
	{		
		if(self::$touser != self::$_original_id) return false;
		self::$_user_id = self::getUserInfo(self::$fromuser, $type);
		if(!self::$_user_id) return false;
		
		$data = array(
			'merch_id'		=> self::$_merch_id,
			'user_id'		=> self::$_user_id,
			'create_at'		=> self::$createtime,
			'name'			=> $type,
			'tofrom'		=> 0
		);
		return Events::setData($data);
	}
	
	static public function getUserInfo($open_id, $type='subscribe')
	{
		$subscribe = 0;
		$info = array();
		if('subscribe' == $type){
			self::_getAccessToken();
			if(!self::$_access_token) return 0;
			
			$url = self::$wxuserinfourl;
			$url = str_replace("#ACCESS_TOKEN#", self::$_access_token, $url);
			$url = str_replace("#OPENID#", $open_id, $url);
			$info = Func::curlGet($url);
			ABase::log("user info url: ".$url, "access_token", "Info");
			ABase::log("user info: ".$info, "access_token", "Info");
			
			$info = json_decode($info, true);
			$subscribe = isset($info['subscribe']) ? $info['subscribe'] : -1;
			if(-1 == $subscribe) return 0;
		}
		
		$data = array(
			'merch_id'		=> self::$_merch_id,
			'open_id'		=> $open_id,
			'create_at'		=> self::$createtime,
			'update_at'		=>time()
		);
		
		if(0 == $subscribe){
			$data['is_attention'] = 0;
		}else if(1 == $subscribe){
			if(!isset($info['subscribe_time'])) return 0;
			
			$data['is_attention'] = 1;
			$data['nickname'] = isset($info['nickname']) ? $info['nickname'] : null;
			$data['sex'] = isset($info['sex']) ? $info['sex'] : null;
			$data['language'] = isset($info['language']) ? $info['language'] : null;
			$data['city'] = isset($info['city']) ? $info['city'] : null;
			$data['province'] = isset($info['province']) ? $info['province'] : null;
			$data['country'] = isset($info['country']) ? $info['country'] : null;
			$data['headimgurl'] = isset($info['headimgurl']) ? $info['headimgurl'] : null;
			$data['subscribe_time'] = isset($info['subscribe_time']) ? $info['subscribe_time'] : null;
			$data['unionid'] = isset($info['unionid']) ? $info['unionid'] : null;
		}
		self::$_user_id = Users::setData($data);
		return self::$_user_id;
	}
	
}
