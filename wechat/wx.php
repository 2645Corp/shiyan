<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "Cool2645!");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
if($wechatObj->checkSignature())
{
	$wechatObj->responseMsg();
}
else
{
	echo "Invalid access!";
}

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	//$contentStr = "Welcome to wechat world!";
					$contentStr = $this->replyByKeyword($keyword);
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
					$this->saveMsg($fromUsername,$keyword);
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	public function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	
	private function replyByKeyword($keyword)
	{
		switch($keyword)
		{
			case "hi":
				return "Heyj!";
			case "bye":
				return "See u next time~";
			default:
				return "Welcome to wechat world!";
		}
	}
	
	private function saveMsg($fromUsername,$content)
	{
		if(($logFile = fopen("msglog.txt","a+")) != NULL)
		{
			$timestr = "[" . date('y-m-d h:i:s',time()) . "]";
			$username = "From: " . $fromUsername;
			$content = "Content: " . $content;
			$splitter = "************************************";
			fwrite($logFile,$timestr . "\n" . $username . "\n" . $content . "\n" . $splitter . "\n");
		}
		else
			echo "Log file open error!";
	}
}

?>
