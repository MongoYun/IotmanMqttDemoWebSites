<?php
namespace Iot\Controller;
use Think\Controller;
include "/home/wwwroot/iot.cnxel.cn/Application/Iot/Controller/phpMQTT.php";

class WechatController extends Controller {
	/*
	接受到的信息类型有9种，分别使用下面九个常量标识

    Wechat::MSG_TYPE_TEXT //文本消息
    Wechat::MSG_TYPE_IMAGE //图片消息
    Wechat::MSG_TYPE_VOICE //音频消息
    Wechat::MSG_TYPE_VIDEO //视频消息
    Wechat::MSG_TYPE_MUSIC //音乐消息
    Wechat::MSG_TYPE_NEWS //图文消息（推送过来的应该不存在这种类型，但是可以给用户回复该类型消息）
    Wechat::MSG_TYPE_LOCATION //位置消息
    Wechat::MSG_TYPE_LINK //连接消息
    Wechat::MSG_TYPE_EVENT //事件消息

	事件消息又分为下面五种

    Wechat::MSG_EVENT_SUBSCRIBE //订阅
    Wechat::MSG_EVENT_SCAN //二维码扫描
    Wechat::MSG_EVENT_LOCATION //报告位置
    Wechat::MSG_EVENT_CLICK //菜单点击
    Wechat::MSG_EVENT_MASSSENDJOBFINISH //群发消息成功
	*/
	public function Index(){
		//session(array('id'=>$_GET['token']));
		//session_start();
		//$user = A('User');//通过快捷函数实例化控制器对象
		//$user->Index();//调用number()方法
    }
	public function Api(){
		$token = C('WechatApiToken'); //微信后台填写的TOKEN

		$options = array(
		'token'=>$token, //填写你设定的key
		'encodingaeskey'=>'', //填写加密用的EncodingAESKey
		'appid'=>'', //填写高级调用功能的app id
		'appsecret'=>'' //填写高级调用功能的密钥
		);
		/* 加载微信SDK */
		$wechat = new \Com\Wechat($options);
		$wechat->valid();
		/* 获取请求信息 */
		$type = $wechat->getRev()->getRevType();
			//在这里你可以分析用户发送过来的数据来决定需要做出什么样的回复
			echo $wechat::MSGTYPE_TEXT;
			switch($type){
				case $wechat::MSGTYPE_TEXT://文本消息
					$this->WechatDealTextMsg($wechat,$wechat->getRev()->getRevContent());
					break;
				case $wechat::MSGTYPE_EVENT://事件消息
					
					break;
				case $wechat::MSGTYPE_VOICE://语音消息
					$this->WechatDealTextMsg($wechat,$wechat->getRev()->getRevContent());
					break;
				default:
					break;
			}
			return;

    }
	public function WechatDealTextMsg($wechat,$data){
		if($_SERVER["HTTPS"]=="on"){
			$RequseMethod="https://";
		}else{
			$RequseMethod="http://";
		}
		$OpenId=$wechat->getRev()->getRevFrom();
		//如果没有此用户，注册他
		$DeviceCertificationControlList=D('DeviceCertificationControlList');
		$tempDevice["ClientId"]=$OpenId;
		$tempDevice=$DeviceCertificationControlList->DeviceIsExist($tempDevice);
		if(empty($tempDevice)){
			$tempDevice["ClientId"]=$OpenId;
			//$wechat->checkAuth();
			//$tempinfo=$wechat->getUserInfo($OpenId, $lang = 'zh_CN');
			$tempinfo["subscribe_time"]=time();
			$tempDevice["UserName"]=$tempinfo["subscribe_time"];
			$tempDevice["UserPasswd"]=$tempinfo["subscribe_time"];
			$DeviceCertificationControlList->AddDevice($tempDevice);
			$PublishTopicCertificationControlList=D('PublishTopicCertificationControlList');
			$tempPublishc['TopicName']="iotman/".$tempinfo["subscribe_time"];
			$tempPublishc['ClientId']=$tempDevice["ClientId"];
			$PublishTopicCertificationControlList->AddPublishc($tempPublishc);
			$tempSubcribec['TopicName']="iotman/".$tempinfo["subscribe_time"];
			$tempSubcribec['ClientId']=$tempDevice["ClientId"];
			$tempSubcribec['QoSLevel']='2';
			$SubcribeTopicCertificationControlList=D('SubcribeTopicCertificationControlList');
			$SubcribeTopicCertificationControlList->AddSubcribec($tempSubcribec);
			
			$tempDevice["ClientId"]=$tempinfo["subscribe_time"];
			$DeviceCertificationControlList->AddDevice($tempDevice);
			$tempPublishc['TopicName']="iotman/".$tempinfo["subscribe_time"];
			$tempPublishc['ClientId']=$tempDevice["ClientId"];
			$PublishTopicCertificationControlList->AddPublishc($tempPublishc);
			$tempSubcribec['TopicName']="iotman/".$tempinfo["subscribe_time"];
			$tempSubcribec['ClientId']=$tempDevice["ClientId"];
			$tempSubcribec['QoSLevel']='2';
			$SubcribeTopicCertificationControlList->AddSubcribec($tempSubcribec);
			
			$msg="你的账号密码都是".$tempinfo["subscribe_time"]."\n"."客户id是".$tempDevice["ClientId"];
			$wechat->text($msg)->reply();
			return;
		}else{
		$tempDevice=$tempDevice[0];
		}
		//$wechat->text($tempDevice["UserName"])->reply();
		$server = "127.0.0.1";     // change if necessary
		$port = 1883;                     // change if necessary
		$username = $tempDevice["UserName"];                   // set your username
		$password = $tempDevice["UserPasswd"];                   // set your password
		$client_id = $OpenId; // make sure this is unique for connecting to sever - you could use uniqid()
		$mqtt = new phpMQTT($server, $port, $client_id);
		if(!$mqtt->connect(true, NULL, $username, $password)) {
			//exit(1);
		}
		$topics['iotman/'.$tempDevice["UserName"]] = array("qos" => 0, "function" => "procmsg");
		$mqtt->publish('iotman/'.$tempDevice["UserName"], $data, 1);
		$mqtt->subscribe($topics, 0);
		
		//$this->procmsg("1111111", "77777777777777777777");
		$rev=1;
		while($rev==1){
			$rev=$mqtt->proc();
		}
		$rev=1;
		while($rev==1){
			$rev=$mqtt->proc();
		}
		$msg=$rev;
		$wechat->text($msg)->reply();
		//sleep(3);
		$mqtt->close();
		
		
	
	}
	public function procmsg($topic, $msg,$wechat){
		//echo "nihihihihi";
		//echo $msg;
		$wechat->text($msg)->reply();
		exit();
	}
	public function WechatDealEVENT($wechat,$data){
			$content = ''; //回复内容，回复不同类型消息，内容的格式有所不同
			$type    = ''; //回复消息的类型
			/* 响应当前请求(自动回复) */
			$wechat->response($content, $type);
	}
	protected function CheckUserAgent(){
		if(strpos($_SERVER["HTTP_USER_AGENT"],"MicroMessenger")){
			return true;
			
		}else{
			$this->assign('error','请用微信打开！！');// 赋值分页输出
			$this->display('Error.tpl'); // 输出模板
			return false;
		}
	}
	protected function CheckUserInputData(){
		
	}

}
