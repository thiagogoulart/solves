<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 08/05/2019
 */ 
namespace SolvesAuth;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;

class SolvesAuth {
    private static $instance;
    private $firebase;
    private $firebaseServiceAccount;
    private $firebaseFatory;
    private $debug = false;

    public function __construct($initFirebase = true) {
        if($initFirebase){ 
            if(!FIREBASE_CONFIG_JSON_FILE){
                throw new Exception('Configuração do Firebase não definida.');
            }
            $this->firebaseServiceAccount = ServiceAccount::fromJsonFile(__DIR__.'/../'.FIREBASE_CONFIG_JSON_FILE);
            $this->firebaseFatory = (new Factory);
            $this->firebaseFatory = $this->firebaseFatory->withServiceAccount($this->firebaseServiceAccount);
            if(FIREBASE_CONFIG_USER){
                $this->firebaseFatory = $this->firebaseFatory->asUser(FIREBASE_CONFIG_USER);
            }
            $this->firebase =  $this->firebaseFatory->create();
        }
    }

    public static function getInstance($initFirebase = true){
        if(!isset(SolvesAuth::$instance)){
            SolvesAuth::$instance = new SolvesAuth($initFirebase);
        }
        return SolvesAuth::$instance;
    }
    public function checkFirebaseUser($firebaseAuthUser){
        if(isset($firebaseAuthUser) && property_exists($firebaseAuthUser, 'stsTokenManager')){
            $stokenManager = ($firebaseAuthUser->stsTokenManager);
            return ($this->checkFirebaseToken($stokenManager->accessToken));
        }
        return null;
    }

    public function checkFirebaseToken($idTokenString){
        $verifiedIdToken = null;
        try {
            $verifiedIdToken = $this->firebase->getAuth()->verifyIdToken($idTokenString);
        } catch (InvalidToken $e) {
            throw $e;
        }
        if(isset($verifiedIdToken)){
            $uid = $verifiedIdToken->getClaim('sub');
            $user = $this->firebase->getAuth()->getUser($uid);
            return $user;
        }
        return null;
    }
    public function getUserLogado($CONNECTION, $empresaId, $token, $userData, $perfil, $usuario, $createdAt){
        $object = null;
        /*Autentica por TOKEN*/
        $user = $this->checkToken($CONNECTION, $token, $userData);
        if (isset($user) && $user->getId()>0) {
           $object = $user;
        }
        return $object;
    }
    public function checkToken($CONNECTION, $receivedToken, $receivedData){
        $token = $this->createToken($receivedData);
        // We check if token is ok !
       //    echo 'wrong data !['.$receivedData.']';
           //echo 'wrong Token !['.$receivedToken.'] != ['.$token.']';
        if ($receivedToken != $token){
            return null;
        }
        $receivedData = descriptografa($receivedData);
        $arrUserData = json_decode($receivedData);
        $creationTimeToken = descriptografa($arrUserData->t);
        $tipo = descriptografa($arrUserData->z);
        $userIdToken = descriptografa($arrUserData->u);
        $senhaToken = descriptografa($arrUserData->s);
        $token_HTTP_USER_AGENT = descriptografa($arrUserData->HTTP_USER_AGENT);
        $token_CLIENTE_IP = descriptografa($arrUserData->CLIENTE_IP);
        $token_REQUEST_TIME = descriptografa($arrUserData->REQUEST_TIME);

        if(isset($tipo) && isset($creationTimeToken) && isset($userIdToken) && isset($senhaToken)){
            if(isNotBlank($token_HTTP_USER_AGENT) && isNotBlank($token_CLIENTE_IP) && 
                $_SERVER['HTTP_USER_AGENT']==$token_HTTP_USER_AGENT){ 
                 if('autonomo'==$tipo){
                    $user = new Autonomo($CONNECTION);
                    if($this->debug){
                        var_dump($userIdToken);
                        var_dump($senhaToken);
                    }
                    $user = $user->findByIdAndSenha($userIdToken, $senhaToken);
                 }
                 else if('cliente'==$tipo){
                    $user = new Cliente($CONNECTION);
                    $user = $user->findByIdAndSenha($userIdToken, $senhaToken);
                 }
                return $user;
            }
        }
        return null;
    }
    public function createToken($data){
        /* Create a part of token using secretKey and other stuff */
        $tokenGeneric = EMAIL_HOST.$_SERVER["SERVER_NAME"]; 
        /* Encoding token */
        return  hash('sha256', $tokenGeneric.$data);
    }
    public function getToken($data){
        $token = $this->createToken($data);
        return array('token' => $token, 'userData' => $data);
    }
    public function auth($userId, $senha,$tipo='cliente'){
        $userData = new stdClass();
        $userData->t = criptografa(time());
        $userData->u = criptografa($userId);
        $userData->z = criptografa($tipo);
        $userData->s = criptografa($senha);
        $userData->HTTP_USER_AGENT = criptografa($_SERVER['HTTP_USER_AGENT']);
        $userData->CLIENTE_IP = criptografa(getClientIp());
        $userData->REQUEST_TIME = criptografa($_SERVER['REQUEST_TIME']);
        $data = json_encode($userData);
        $token = $this->getToken(criptografa($data));
        return json_encode($token);
    }
    public static function getUserUid($firebaseAuthUser, $firebaseUserChecked){
        return (isNotBlank($firebaseAuthUser) && isNotBlank($firebaseAuthUser->user) ? $firebaseAuthUser->user->uid : $firebaseUserChecked->uid);
    }
    public static function getUserEmail($firebaseAuthUser, $firebaseUserChecked){
        return (isNotBlank($firebaseAuthUser) && isNotBlank($firebaseAuthUser->user) ? $firebaseAuthUser->user->email : $firebaseUserChecked->email);
    }
    public static function getUserPhone($firebaseAuthUser, $firebaseUserChecked){
        return (isNotBlank($firebaseAuthUser) && isNotBlank($firebaseAuthUser->user) ? $firebaseAuthUser->user->phoneNumber : $firebaseUserChecked->phoneNumber);
    }
    public static function getUserName($firebaseAuthUser, $firebaseUserChecked){
        return $firebaseUserChecked->providerData[0]->displayName;
    }
    public static function getUserPhotoUrl($firebaseAuthUser, $firebaseUserChecked){
        $url = null;
        try{
            $url = $firebaseAuthUser->photoURL;
            if(!isNotBlank($url)){
                $url = $firebaseUserChecked->providerData[0]->photoURL;
            }
            if(!isNotBlank($url)){
                $url = $firebaseAuthUser->profile->picture->data->url;
            }
        }catch(Exception $e){

        }
        return $url;
    }
    public static function getCredentialProviderId($firebaseAuthUser, $firebaseUserChecked){
        $c = null;
        try{    
            if(isNotBlank($firebaseAuthUser) && isNotBlank($firebaseAuthUser->user) && isNotBlank($firebaseAuthUser->user->providerData)){
                $c = $firebaseAuthUser->user->providerData[0]->providerId;
            }
            if(!isNotBlank($c)){
                $c = $firebaseAuthUser->credential->providerId;
            }
            if(!isNotBlank($c)){
                $c = $firebaseUserChecked->providerData[0]->providerId;
            }
        }catch(Exception $e){

        }
        return $c;
    }
}
?>