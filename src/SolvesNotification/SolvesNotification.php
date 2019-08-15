<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 26/07/2019
 */ 
namespace SolvesNotification;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\VAPID;

class SolvesNotification {

	private static $serverSubscriptionsUrl='/rest/notifications';
	private static $publicKey='';
	private static $privateKey='';
	private static $senderId = '';
	private static $debug=true;

	public static function config($serverSubscriptionsUrl='/rest/notifications', $publicKey, $privateKey, $senderId){
		if(\Solves\Solves::isNotBlank($serverSubscriptionsUrl)){
			SolvesNotification::$serverSubscriptionsUrl = $serverSubscriptionsUrl;
		}
		SolvesNotification::$publicKey = $publicKey;
		SolvesNotification::$privateKey = $privateKey;
		SolvesNotification::$senderId = $senderId;
	}
    public static function getSenderId(){return SolvesNotification::$senderId;}

	public static function sendNotifications($isSendFirefox, $isSendChrome, $title, $message){ 
		// array of notifications
		$notifications = [
		    SolvesNotification::getFirefoxNotification($title, $message),
		    SolvesNotification::getChromeNotification($title, $message)
		    /*, [
		        'subscription' => Subscription::create([
		            'endpoint' => 'https://example.com/other/endpoint/of/another/vendor/abcdef...',
		            'publicKey' => '(stringOf88Chars)',
		            'authToken' => '(stringOf24Chars)',
		            'contentEncoding' => 'aesgcm', // one of PushManager.supportedContentEncodings
		        ]),
		        'payload' => '{msg:"'.$message.'"}',
		    ], [
		          'subscription' => Subscription::create([ // this is the structure for the working draft from october 2018 (https://www.w3.org/TR/2018/WD-push-api-20181026/) 
		              "endpoint" => "https://example.com/other/endpoint/of/another/vendor/abcdef...",
		              "keys" => [
		                  'p256dh' => '(stringOf88Chars)',
		                  'auth' => '(stringOf24Chars)'
		              ],
		          ]),
		          'payload' => '{msg:"'.$message.'"}',
		      ],*/
		];

		$webPush = new WebPush();

		// send multiple notifications with payload
		foreach ($notifications as $notification) {
		    $webPush->sendNotification(
		        $notification['subscription'],
		        $notification['payload'] // optional (defaults null)
		    );
		}
		return $webPush;
	}

	private static function getFirefoxNotification($title, $message){
		return [
		        'subscription' => Subscription::create([
		            'endpoint' => 'https://updates.push.services.mozilla.com/push/abc...', // Firefox 43+,
		            'publicKey' => 'BPcMbnWQL5GOYX/5LKZXT6sLmHiMsJSiEvIFvfcDvX7IZ9qqtq68onpTPEYmyxSQNiH7UD/98AUcQ12kBoxz/0s=', // base 64 encoded, should be 88 chars
		            'authToken' => 'CxVX6QsVToEGEcjfYPqXQw==', // base 64 encoded, should be 24 chars
		        ]),
		        'payload' => $message,
		    ];
	}

	private static function getChromeNotification($title, $message){
		return [
		        'subscription' => Subscription::create([
		            'endpoint' => 'https://android.googleapis.com/gcm/send/abcdef...', // Chrome
		        ]),
		        'payload' => null,
		    ];
	}

	public static function checkSentResults($webPush){	
		/**
		 * Check sent results
		 * @var MessageSentReport $report
		 */
		foreach ($webPush->flush() as $report) {
		    $endpoint = $report->getRequest()->getUri()->__toString();

		    if ($report->isSuccess()) {
		        echo "[v] Message sent successfully for subscription {$endpoint}.";
		    } else {
		        echo "[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}";
		      //  var_dump($report->getResponse());
		    }
		}
	}
	public static function sendNotificationToOneEndpoint($authToken, $content_encoding, $endpoint, $publicKey,
			$idNotification, $title, $message, $image=null){
		$subscription = SolvesNotification::getSubscriptionObject($authToken, $content_encoding, $endpoint, $publicKey);
		$json = SolvesNotification::mountJsonMessaging($idNotification, $title, $message, $image);
		$auth = array(
    		'GCM' => SolvesNotification::$senderId, 
		    'VAPID' => array(
		        'subject' => \Solves\Solves::getSiteUrl(),
		        'publicKey' => $publicKey, // don't forget that your public key also lives in app.js
		        'privateKey' => SolvesNotification::$privateKey // in the real world, this would be in a secret file
		    )
		);
		$webPush = new WebPush($auth);
		$result = SolvesNotification::sendOneNotification($webPush, $subscription, $json, $publicKey, $authToken);
		if(SolvesNotification::$debug){
			//var_dump(VAPID::createVapidKeys()); 
			SolvesNotification::checkSentResults($webPush);
		}
		return $result;
	}
	private static function mountJsonMessaging($idNotification, $title, $message, $image){
	    if(\Solves\Solves::isNotBlank($idNotification)){
	        $idNotification = \Solves\Solves::getSystemName().'_'.\Solves\Solves::getSystemVersion().'_'.\Solves\SolvesTime::getTimestampAtual().'_'.
	        	time();
	    }
	    $json = array();
	    $json["title"] = $title; 
	    $json["body"] = $message; 
	    $json["tag"] = $idNotification; 
	    $json["icon"]= \Solves\Solves::getSiteIcone();
	    if(\Solves\Solves::isNotBlank($image)){
	      $json["image"] = $image;
	    }
	    $json["vibrate"] = [100, 50, 100];
	 /*   
	 	if(this.actions && this.actions.length>0){
	      $json["actions"] = this.actions;
	    } */
	    return $json;
  }
	private static function getSubscriptionObject($authToken, $content_encoding, $endpoint, $publicKey){ 
		$objJson = array();
		$objJson['auth_token'] = $authToken;
		$objJson['content_encoding'] = $content_encoding;
		$objJson['endpoint'] = $endpoint;
		$objJson['publicKey'] = $publicKey;
		return Subscription::create($objJson);
	}
	private static function sendOneNotification($webPush, $subscription, $json, $userPublicKey, $userAuthToken){ 
		if(is_array($json)){
			$json = json_encode($json);
		}
		/**
		 * send one notification and flush directly
		 * @var \Generator<MessageSentReport> $sent
		 */
		return $webPush->sendNotification(
		    $subscription,
		    $json
		);
	}
	public static function getServerSubscriptionsUrl(){
		return SolvesNotification::$serverSubscriptionsUrl;
	}
	public static function getPublicKey(){
		return SolvesNotification::$publicKey;
	}
}
?>