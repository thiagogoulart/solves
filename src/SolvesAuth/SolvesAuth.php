<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.1
 * @created 08/05/2019
 * @updated 19/07/2019
 */
namespace SolvesAuth;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;

class SolvesAuth {
    private static $firebase;
    private static $firebaseServiceAccount;
    private static $firebaseFatory;
    private static $debug = false;

    private static $KEY_TOKEN = 'S!@&!#511.;,;.,[[[q1';
    private static $FIREBASE_CONFIG_JSON_FILE;
    private static $FIREBASE_CONFIG_USER;


    /**MOCK*/
    private static $isMock = false;
    private static $mockServerName = null;
    private static $mockUserAgent = null;
    private static $mockRequestTime = null;

    public static function config($firebaseJsonFile, $firebaseUser){
        SolvesAuth::setFirebaseConfigJsonFile($firebaseJsonFile);
        SolvesAuth::setFirebaseConfigUser($firebaseUser);
    }

    public static function mock(string $serverName, string $userAgent, ?string $requestTime=''){
        self::$isMock = true;
        self::$mockServerName = $serverName;
        self::$mockUserAgent = $userAgent;
        self::$mockRequestTime = $requestTime;
    }
    public static function isMock(){
        return self::$isMock;
    }
    public static function getServer(){
        return (self::isMock() ? self::$mockServerName : @$_SERVER["SERVER_NAME"]);
    }
    public static function getHttpUserAgent(){
        return (self::isMock() ? self::$mockUserAgent : @$_SERVER['HTTP_USER_AGENT']);
    }
    public static function getRequestTime(){
        return (self::isMock() ? self::$mockRequestTime : @$_SERVER['REQUEST_TIME']);
    }

    public static function getKeyToken(){return SolvesAuth::$KEY_TOKEN;}
    public static function setFirebaseConfigJsonFile($p){SolvesAuth::$FIREBASE_CONFIG_JSON_FILE = $p;}
    public static function setFirebaseConfigUser($p){SolvesAuth::$FIREBASE_CONFIG_USER = $p;}

    public static function getFirebaseConfigJsonFile(){return __DIR__.'/../'.SolvesAuth::$FIREBASE_CONFIG_JSON_FILE;}
    public static function getFirebaseConfigUser(){return SolvesAuth::$FIREBASE_CONFIG_USER;}

    public static function getFirebase(){SolvesAuth::initFirebase();return SolvesAuth::$firebase;}

    public static function initFirebase() {
        if(SolvesAuth::$firebase==null){
            if(!SolvesAuth::$FIREBASE_CONFIG_JSON_FILE){
                throw new \Exception('Configuração do Firebase não definida.');
            }
            SolvesAuth::$firebaseServiceAccount = ServiceAccount::fromJsonFile(SolvesAuth::getFirebaseConfigJsonFile());
            SolvesAuth::$firebaseFatory = (new Factory);
            SolvesAuth::$firebaseFatory = SolvesAuth::$firebaseFatory->withServiceAccount(SolvesAuth::$firebaseServiceAccount);
            if(SolvesAuth::getFirebaseConfigUser()){
                SolvesAuth::$firebaseFatory = SolvesAuth::$firebaseFatory->asUser(SolvesAuth::getFirebaseConfigUser());
            }
            SolvesAuth::$firebase =  SolvesAuth::$firebaseFatory->create();
        }
    }
    public static function checkFirebaseUser($firebaseAuthUser){
        if(isset($firebaseAuthUser) && property_exists($firebaseAuthUser, 'stsTokenManager')){
            $stokenManager = ($firebaseAuthUser->stsTokenManager);
            return (SolvesAuth::checkFirebaseToken($stokenManager->accessToken));
        }
        return null;
    }

    public static function checkFirebaseToken($idTokenString){
        $verifiedIdToken = null;
        try {
            $verifiedIdToken = SolvesAuth::getFirebase()->getAuth()->verifyIdToken($idTokenString);
        } catch (InvalidToken $e) {
            throw $e;
        }
        if(isset($verifiedIdToken)){
            $uid = $verifiedIdToken->getClaim('sub');
            $user = SolvesAuth::getFirebase()->getAuth()->getUser($uid);
            return $user;
        }
        return null;
    }
    public static function getUserLogado(?\SolvesDAO\SolvesDAOConnection $CONNECTION, $empresaId, $token, $userData, $perfil, $usuario, $createdAt){
        $object = null;
        /*Autentica por TOKEN*/
        $user = SolvesAuth::checkToken($CONNECTION, $token, $userData);
        if (isset($user) && $user->getId()>0) {
            $object = $user;
        }
        return $object;
    }
    public static function checkToken(?\SolvesDAO\SolvesDAOConnection $CONNECTION, $receivedToken, $receivedData){
        if(!\Solves\Solves::isNotBlank($receivedToken) || !\Solves\Solves::isNotBlank($receivedData)){
            return null;
        }
        $token = SolvesAuth::createToken($receivedData);
        // We check if token is ok !
        //    echo 'wrong data !['.$receivedData.']';
        //echo 'wrong Token !['.$receivedToken.'] != ['.$token.']';
        if ($receivedToken != $token){
            return null;
        }
        if(substr($receivedData, 0, 1)=='"' && substr($receivedData, -1)=='"'){
            $receivedData = substr($receivedData, 1, strlen($receivedData)-1);
        }
        $receivedData = \Solves\Solves::descriptografa($receivedData);
        $arrUserData = json_decode($receivedData);
        $creationTimeToken = \Solves\Solves::descriptografa($arrUserData->t);
        $tipo = \Solves\Solves::descriptografa($arrUserData->z);
        $userIdToken = \Solves\Solves::descriptografa($arrUserData->u);
        $senhaToken = \Solves\Solves::descriptografa($arrUserData->s);
        $token_HTTP_USER_AGENT = \Solves\Solves::descriptografa($arrUserData->HTTP_USER_AGENT);
        $token_CLIENTE_IP = \Solves\Solves::descriptografa($arrUserData->CLIENTE_IP);
        $token_REQUEST_TIME = \Solves\Solves::descriptografa($arrUserData->REQUEST_TIME);

        $isSameUserAgentOnRequest = (self::getHttpUserAgent()==$token_HTTP_USER_AGENT);

        if(isset($tipo) && isset($creationTimeToken) && isset($userIdToken) && isset($senhaToken)){
            if(\Solves\Solves::isNotBlank($token_HTTP_USER_AGENT) && \Solves\Solves::isNotBlank($token_CLIENTE_IP)){
                $classe = ucwords($tipo);
                $obj = new $classe($CONNECTION);
                $user = $obj->findByIdAndSenha($userIdToken, $senhaToken);
                return $user;
            }
        }
        return null;
    }
    public static function createToken($data){
        /* Create a part of token using secretKey and other stuff */
        $server = self::getServer();
        if(!\Solves\Solves::isNotBlank($server)){
            $host = \Solves\SolvesConf::getSolvesConfUrls()->getSolvesConfUrlAtivo()->getSiteUrl();
            $arrHost = explode('https://', $host);
            if(count($arrHost)>1){
                $host = $arrHost[1];
            }else{
                $host = $arrHost[0];
                $arrHost = explode('http://', $host);
                $host = (count($arrHost)>1?$arrHost[1]:$arrHost[0]);
            }
            $arrHost = explode('/', $host);
            $server = $arrHost[0];
        }
        if(\Solves\SolvesConf::$SYSTEM_DEBUG_MODE){
            var_dump('SERVER:'. $server);
        }
        $tokenGeneric = SolvesAuth::getKeyToken().$server;
        /* Encoding token */
        return  hash('sha256', $tokenGeneric.$data);
    }
    public static function getToken($data){
        $token = SolvesAuth::createToken($data);
        return array('token' => $token, 'userData' => $data);
    }
    public static function getAuthToken(int $userId, string $senha,$tipo='user'){
        $userData = new \stdClass();
        $userData->t = \Solves\Solves::criptografa(time());
        $userData->u = \Solves\Solves::criptografa($userId);
        $userData->z = \Solves\Solves::criptografa($tipo);
        $userData->s = \Solves\Solves::criptografa($senha);
        $userData->HTTP_USER_AGENT = \Solves\Solves::criptografa(self::getHttpUserAgent());
        $userData->CLIENTE_IP = \Solves\Solves::criptografa(\Solves\Solves::getClientIp());
        $userData->REQUEST_TIME = \Solves\Solves::criptografa(self::getRequestTime());
        $data = json_encode($userData);
        return SolvesAuth::getToken(\Solves\Solves::criptografa($data));
    }
    public static function auth(int $userId, string $senha,$tipo='user'){
        $token = self::getAuthToken($userId, $senha, $tipo);
        return json_encode($token);
    }
    public static function getUserUid($firebaseAuthUser, $firebaseUserChecked){
        return (\Solves\Solves::isNotBlank($firebaseAuthUser) && \Solves\Solves::isNotBlank($firebaseAuthUser->user) ? $firebaseAuthUser->user->uid : $firebaseUserChecked->uid);
    }
    public static function getUserEmail($firebaseAuthUser, $firebaseUserChecked){
        return (\Solves\Solves::isNotBlank($firebaseAuthUser) && \Solves\Solves::isNotBlank($firebaseAuthUser->user) ? $firebaseAuthUser->user->email : $firebaseUserChecked->email);
    }
    public static function getUserPhone($firebaseAuthUser, $firebaseUserChecked){
        return (\Solves\Solves::isNotBlank($firebaseAuthUser) && \Solves\Solves::isNotBlank($firebaseAuthUser->user) ? $firebaseAuthUser->user->phoneNumber : $firebaseUserChecked->phoneNumber);
    }
    public static function getUserName($firebaseAuthUser, $firebaseUserChecked){
        return $firebaseUserChecked->providerData[0]->displayName;
    }
    public static function getUserPhotoUrl($firebaseAuthUser, $firebaseUserChecked){
        $url = null;
        try{
            $url = $firebaseAuthUser->photoURL;
            if(!\Solves\Solves::isNotBlank($url)){
                $url = $firebaseUserChecked->providerData[0]->photoURL;
            }
            if(!\Solves\Solves::isNotBlank($url)){
                $url = $firebaseAuthUser->profile->picture->data->url;
            }
        }catch(\Exception $e){

        }
        return $url;
    }
    public static function getCredentialProviderId($firebaseAuthUser, $firebaseUserChecked){
        $c = null;
        try{
            if(\Solves\Solves::isNotBlank($firebaseAuthUser) && \Solves\Solves::isNotBlank($firebaseAuthUser->user) && \Solves\Solves::isNotBlank($firebaseAuthUser->user->providerData)){
                $c = $firebaseAuthUser->user->providerData[0]->providerId;
            }
            if(!\Solves\Solves::isNotBlank($c)){
                $c = $firebaseAuthUser->credential->providerId;
            }
            if(!\Solves\Solves::isNotBlank($c)){
                $c = $firebaseUserChecked->providerData[0]->providerId;
            }
        }catch(\Exception $e){

        }
        return $c;
    }
}
?>