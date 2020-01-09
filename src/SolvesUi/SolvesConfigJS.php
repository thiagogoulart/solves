<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 13/08/2019
 */ 
namespace SolvesUi;


class SolvesConfigJS {
    private static $FACEBOOK_MESSENGER_SHOW = false;
    private static $FACEBOOK_PAGE_ID = null;
    private static $FACEBOOK_THEME_COLOR = null;
    private static $FACEBOOK_INITIAL_MSG = null;

    private static $FIREBASE_JSON_CONFIG = '{}';

    private static $FIREBASE_AUTH_GOOGLE = false;
    private static $FIREBASE_AUTH_GOOGLE_CLIENT_ID = null;
    private static $FIREBASE_AUTH_FACEBOOK = false;
    private static $FIREBASE_AUTH_PHONE = false;
    private static $FIREBASE_AUTH_EMAIL = false;

    private static $AUTH_URL_LOGADO_SUCESSO = "meu_perfil";
    private static $AUTH_URL_TERMO_USO = "/termo_uso";
    private static $AUTH_URL_TERMO_PRIVACIDADE = "/termo_privacidade";
    private static $AUTH_FUNCTION_SUCESSO = "loginSuccess";

    public static function getScript(){
        $script = SolvesConfigJS::getFacebookMessenger();
        $script .= SolvesConfigJS::getNotificationsSettings();
        $script .= SolvesConfigJS::getSolvesInit();
        $script .= SolvesConfigJS::getFirebaseInitSettings();
        $script .= SolvesConfigJS::getAuthSettings();
        $script .= SolvesConfigJS::getFirebaseNotificationsSettings();
        $script .= SolvesConfigJS::getWebSocketSettings();
        return $script;
    }


    public static function getFacebookMessenger(){
      if(SolvesConfigJS::$FACEBOOK_MESSENGER_SHOW){ 
          return '//Ui - before Solvesinit
                $.SolvesUi.configFacebookMessenger("'.SolvesConfigJS::$FACEBOOK_PAGE_ID.'", 
                "'.SolvesConfigJS::$FACEBOOK_THEME_COLOR.'", 
                "'.SolvesConfigJS::$FACEBOOK_INITIAL_MSG.'");';
      }
      return '';
    }
    private static function getNotificationsSettings(){
      return '//SolvesNotifications
if(undefined!==$.SolvesNotifications){
   $.SolvesNotifications.serverUrlNotifications = "'.\SolvesNotification\SolvesNotification::getServerSubscriptionsUrl().'"; 
   $.SolvesNotifications.setApplicationServerKey = "'.\SolvesNotification\SolvesNotification::getPublicKey().'";
}';
    }
    private static function getFirebaseNotificationsSettings(){
        //TODO attrs dinamicos
      return '
/*
// Notificações  Firebase
if(undefined!==$.SolvesNotifications){
   $.SolvesNotifications.initFireBaseConfig();
   $.SolvesNotifications.setFirebasePublicVapidKey("...");
   $.SolvesNotifications.setFireBaseTokenDivId(""); 
   $.SolvesNotifications.setFireBasePermissionDivId("permission");
   $.SolvesNotifications.setFireBaseMessagesDivId("messages"); 
}
*/
';
    }
    private static function getWebSocketSettings(){
      return '
//SolvesWebsocket
if(undefined!==$.SolvesWebsocket){
      $.SolvesWebsocket.webSocketUrl="'.\SolvesWebsocket\SolvesWebsocketServer::getWsUrl().'";
      $.SolvesWebsocket.webSocketRoutes=['.\SolvesWebsocket\SolvesWebsocketServer::getRoutesStringObjArr().'];
}
';
    }
    private static function getSolvesInit(){
      return '
//Solves init
$.Solves.init("'.\Solves\Solves::getSiteUrl().'", "'.\Solves\Solves::getSiteTitulo().'", "'.\Solves\Solves::getSystemName().'", "'.\Solves\Solves::getSiteIcone().'");
$.Solves.setRestUrl("'.\Solves\Solves::getRestUrl().'");
$.Solves.setSiteContext("'.\Solves\Solves::getSiteContext().'");';
    }
    private static function getFirebaseInitSettings(){
      return '
//firebase
$.Solves.setFireBaseConfig('.SolvesConfigJS::getFirebaseJsonConfig().');
';
    }
    private static function getAuthSettings(){
      $s =  '
//Autenticação
if(undefined!==$.SolvesAuth){
    $.SolvesAuth.fireBaseAuthDivId = "firebase_login";
';
      if(SolvesConfigJS::$FIREBASE_AUTH_GOOGLE){
        $s.='
        $.SolvesAuth.addFireBaseAuthusedTypes("google");
        $.SolvesAuth.firebaseGoogleAuthClientId = "'.SolvesConfigJS::$FIREBASE_AUTH_GOOGLE_CLIENT_ID.'";
        ';
      }
      if(SolvesConfigJS::$FIREBASE_AUTH_FACEBOOK){
        $s.='
        $.SolvesAuth.addFireBaseAuthusedTypes("facebook");
        ';
      }
      if(SolvesConfigJS::$FIREBASE_AUTH_EMAIL){
        $s.='
        $.SolvesAuth.addFireBaseAuthusedTypes("email");
        ';
      }
      if(SolvesConfigJS::$FIREBASE_AUTH_PHONE){
        $s.='
        $.SolvesAuth.addFireBaseAuthusedTypes("phone");
        ';
      }
      $s.='
        
    $.SolvesAuth.urlLogadoSucesso = "'.SolvesConfigJS::getAuthUrlLogadoSucesso().'";
    $.SolvesAuth.urlTermosUso = "'.SolvesConfigJS::getAuthUrlTermoUso().'";
    $.SolvesAuth.urlTermosPrivacidade = "'.SolvesConfigJS::getAuthUrlTermoPrivacidade().'";
    $.SolvesAuth.setAuthSuccessFunc(function(authResult){'.SolvesConfigJS::getAuthFunctionSucesso().'(authResult)});
    $.SolvesAuth.initFireBaseConfig();

}
';

      return $s;
    }
    public static function useFacebookMessenger($pageId, $themeColor, $initialMsg){
      SolvesConfigJS::$FACEBOOK_MESSENGER_SHOW=true;
      SolvesConfigJS::$FACEBOOK_PAGE_ID = $pageId;
      SolvesConfigJS::$FACEBOOK_THEME_COLOR = $themeColor;
      SolvesConfigJS::$FACEBOOK_INITIAL_MSG = $initialMsg;
    }
    public static function getFirebaseJsonConfig(){
      return SolvesConfigJS::$FIREBASE_JSON_CONFIG;
    }
    public static function setFirebaseJsonConfig($p){
      SolvesConfigJS::$FIREBASE_JSON_CONFIG = $p;
    }
    public static function useFirebaseAuthGoogle($firebaseGoogleClientId){
      SolvesConfigJS::$FIREBASE_AUTH_GOOGLE=true;
      SolvesConfigJS::$FIREBASE_AUTH_GOOGLE_CLIENT_ID = $firebaseGoogleClientId;
    }
    public static function useFirebaseAuthFacebook(){
      SolvesConfigJS::$FIREBASE_AUTH_FACEBOOK=true;
    }
    public static function useFirebaseAuthEmail(){
      SolvesConfigJS::$FIREBASE_AUTH_EMAIL=true;
    }
    public static function useFirebaseAuthPhone(){
      SolvesConfigJS::$FIREBASE_AUTH_PHONE=true;
    }
    public static function getAuthUrlTermoPrivacidade(){
      return SolvesConfigJS::$AUTH_URL_TERMO_PRIVACIDADE;
    }
    public static function setAuthUrlTermoPrivacidade($p){
      SolvesConfigJS::$AUTH_URL_TERMO_PRIVACIDADE = $p;
    }
    public static function getAuthUrlTermoUso(){
      return SolvesConfigJS::$AUTH_URL_TERMO_USO;
    }
    public static function setAuthUrlTermoUso($p){
      SolvesConfigJS::$AUTH_URL_TERMO_USO = $p;
    }
    public static function getAuthUrlLogadoSucesso(){
      return SolvesConfigJS::$AUTH_URL_LOGADO_SUCESSO;
    }
    public static function setAuthUrlLogadoSucesso($p){
      SolvesConfigJS::$AUTH_URL_LOGADO_SUCESSO = $p;
    }
    public static function getAuthFunctionSucesso(){
      return SolvesConfigJS::$AUTH_FUNCTION_SUCESSO;
    }
    public static function setAuthFunctionSucesso($p){
      SolvesConfigJS::$AUTH_FUNCTION_SUCESSO = $p;
    }
}