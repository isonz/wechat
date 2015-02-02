<?php
error_log(serialize($GLOBALS["HTTP_RAW_POST_DATA"])."\n\n", 3, "c:\\ison.log");
$msg = Wechat::decrypt();
if(!$msg) exit('');

error_log(serialize($msg)."\n\n", 3, "c:\\ison.log");

$xml_tree = new DOMDocument();
$xml_tree->loadXML($msg);
$MsgType = $xml_tree->getElementsByTagName('MsgType');
$MsgType = $MsgType->item(0)->nodeValue;

switch ($MsgType){
	case 'event':
		$Event = $xml_tree->getElementsByTagName('Event');
		$Event = $Event->item(0)->nodeValue;
		if('subscribe' == $Event){
			Wechat::attention('add', $msg);
		}else if('unsubscribe'==$Event){
			Wechat::attention('cancel', $msg);
		}
		break;
}


exit('success');
