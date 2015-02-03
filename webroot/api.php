<?php
//error_log(serialize($GLOBALS["HTTP_RAW_POST_DATA"])."\n\n", 3, "c:\\ison.log");
$msg = Wechat::decrypt();
if(!$msg) exit('');

error_log(serialize($msg)."\n\n", 3, "c:\\ison.log");

$xml_tree = new DOMDocument();
$xml_tree->loadXML($msg);

$ToUserName = $xml_tree->getElementsByTagName('ToUserName');
Wechat::$touser = $ToUserName->item(0)->nodeValue;

$FromUserName = $xml_tree->getElementsByTagName('FromUserName');
Wechat::$fromuser = $FromUserName->item(0)->nodeValue;

$CreateTime = $xml_tree->getElementsByTagName('CreateTime');
Wechat::$createtime = $CreateTime->item(0)->nodeValue;

$MsgType = $xml_tree->getElementsByTagName('MsgType');
Wechat::$msgtype = $MsgType = $MsgType->item(0)->nodeValue;

switch ($MsgType){
	case 'event':
		$Event = $xml_tree->getElementsByTagName('Event');
		$Event = $Event->item(0)->nodeValue;
		if('subscribe' == $Event){
			Wechat::attention($Event, $msg);
		}else if('unsubscribe'==$Event){
			Wechat::attention($Event, $msg);
		}
		break;
	default:
		Wechat::getMsg($msg);
}


exit('success');
