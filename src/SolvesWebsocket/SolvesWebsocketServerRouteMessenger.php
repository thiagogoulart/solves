<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 06/01/2020
 */
namespace SolvesWebsocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

abstract class SolvesWebSocketServerRouteMessenger extends SolvesWebSocketServerRoute implements MessageComponentInterface {

    public function __construct($path) {
        parent::__construct($path);
    }

    public abstract function onEventMessage(\Ratchet\ConnectionInterface $from, $token, $restName, $methodName, $receiverId, $msg, $msgId);
    public abstract function onEventOpen(\Ratchet\ConnectionInterface $conn);
    public abstract function onEventClose(\Ratchet\ConnectionInterface $conn);
    public abstract function onEventError(\Ratchet\ConnectionInterface $conn, \Exception $e);

    protected function executeRest(string $rest, string $method, string $token, $dados, string $prefixDir=''){
        if(null!=$rest){
            $url = $prefixDir.'rest/'.$rest.'/'.$method;
            $_HTTPREQUEST_SERVER = null;
            $_HTTPREQUEST_PUT=null;
            $_HTTPREQUEST_DELETE=null;
            $_HTTPREQUEST_FILES=null;
            $_HTTPREQUEST_POST=array();
            $_HTTPREQUEST_GET=array();

            $_HTTPREQUEST_GET['p'] = $url;
            $_HTTPREQUEST_POST['dados'] = 'token='.$token.'&dados='.json_encode($dados);

            $router = new \Solves\SolvesRouter($_HTTPREQUEST_SERVER, $_HTTPREQUEST_POST, $_HTTPREQUEST_GET, $_HTTPREQUEST_PUT, $_HTTPREQUEST_DELETE, $_HTTPREQUEST_FILES, '', true);
            $router->setToken($token);

            $fileInclude = $router->getPagInclude();

            if(file_exists($fileInclude)) {
                include $fileInclude;

                $obj = new $rest($router);
                $obj->setNoJson();
                return $obj->execute();
            }else{
                return null;
            }
        }
    }

    public function onMessage(\Ratchet\ConnectionInterface $from, $dataReceived){
        $json = json_decode(utf8_encode($dataReceived), false);
        $msg = $json->dados;

        $token = \Solves\SolvesJson::getPropertyData($json, 'token', true, 'Token');
        $rest = \Solves\SolvesJson::getPropertyData($json, 'rest', true, 'Rest');
        $rest_method = \Solves\SolvesJson::getPropertyData($json, 'rest_method', true, 'RestMethod');
        $receiver_id = \Solves\SolvesJson::getPropertyData($json, 'receiver_id', false, 'receiver_id');
        $msg_id = \Solves\SolvesJson::getPropertyData($json, 'msg_id', false, 'msg_id');

        $this->onEventMessage($from, $token, $rest, $rest_method, $receiver_id, $msg, $msg_id);
    }
    public function onOpen(\Ratchet\ConnectionInterface $conn) {
        $this->registraNovaConexao($conn);
        $this->onEventOpen($conn);
    }
    public function onClose(\Ratchet\ConnectionInterface $conn) {
        $this->fechaConexao($conn);
        $this->onEventClose($conn);
    }
    public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e) {
        $conn->close();
        $this->onEventError($conn, $e);
    }

    public function sendMsgToAll(\Ratchet\ConnectionInterface $from,string $msg, $enviaAoRemetenteTambem = false){
        $json = $this->getJsonObjectMessage($from, $msg);
        $this->sendToAll($from, $json, $enviaAoRemetenteTambem);
    }
    public function sendToAll(\Ratchet\ConnectionInterface $from,object $json, $enviaAoRemetenteTambem = false){
        try{
            foreach ($this->clients as $client) {
                if ($enviaAoRemetenteTambem || $from != $client) {
                    $jsonString = json_encode($json);
                    $client->send($jsonString);
                }
            }
        }catch(\Exception $e){
            $this->devolveMsgDeErro($from, 'Ocorreu um erro ao enviar mensagem a Todos. '.$e->getMessage());
        }
    }
    public function sendMsgToGroup(\Ratchet\ConnectionInterface $from, array $arrClients, string $msg, $enviaAoRemetenteTambem = false){
        $json = $this->getJsonObjectMessage($from, $msg);
        $this->sendToGroup($from, $arrClients, $json, $enviaAoRemetenteTambem);
    }
    public function sendToGroup(\Ratchet\ConnectionInterface $from, array $arrClients, object $json, $enviaAoRemetenteTambem = false){
        try{
            foreach ($arrClients as $client) {
                if ($enviaAoRemetenteTambem || $from != $client) {
                    $jsonString = json_encode($json);
                    $client->send($jsonString);
                }
            }
        }catch(\Exception $e){
            $this->devolveMsgDeErro($from, 'Ocorreu um erro ao enviar mensagem a grupo. '.$e->getMessage());
        }
    }
    public function sendMsgToOne(\Ratchet\ConnectionInterface $from, int $destinatarioResourceId, string $msg){
        $json = $this->getJsonObjectMessage($from, $msg);
        $this->sendToOne( $from, $destinatarioResourceId, $json);
    }
    public function sendToOne(\Ratchet\ConnectionInterface $from, int $destinatarioResourceId, object $json){
        try{
            $connDestino = $this->getConnectionClientByResourceId($destinatarioResourceId);
            if(isset($connDestino)){
                $jsonString = json_encode($json);
                $connDestino->send($jsonString);
            }else{
                $this->devolveMsgDeErro($from, 'Não encontrada conexão para este usuário!');
            }
        }catch(\Exception $e){
            $this->devolveMsgDeErro($from, 'Ocorreu um erro ao enviar mensagem a um usuário. '.$e->getMessage());
        }
    }
    protected function getJsonObjectMessage(\Ratchet\ConnectionInterface $from, string $msg): object{
        $obj = $this->getDefaultJsonObjectMessage($from);
        $obj->msg = $msg;
        $json = json_encode($obj);
        return $json;
    }
    protected function getDefaultJsonObjectMessage(\Ratchet\ConnectionInterface $from, $msg_id=null): object{
        $timestampAtual = \Solves\SolvesTime::getTimestampAtual();
        $timestampAtualLabel = \Solves\SolvesTime::getDataFormatada($timestampAtual);
        $obj = (object) [
            'from_resource_id' => $this->getResourceId($from),
            'datetime' => $timestampAtual,
            'datetime_label' => $timestampAtualLabel
        ];
        if(isset($msg_id)){
            $obj->msg_id = $msg_id;
        }
        return $obj;
    }
    protected function devolveMsgDeErro(\Ratchet\ConnectionInterface $conn, string $msg='Ocorreu um erro na operação!'){
        $this->sendToOne($conn, $this->getResourceId($conn), $msg);
    }
    protected function getHttpRequest(\Ratchet\ConnectionInterface $conn){
        return $conn->httpRequest;
    }
    protected function getHttpRequestHeaders(\Ratchet\ConnectionInterface $conn){
        return $conn->httpRequest->getHeaders();
    }
    public function getResourceId(\Ratchet\ConnectionInterface $conn){
        return $conn->resourceId;
    }
    public function getHost(\Ratchet\ConnectionInterface $conn){
        return $this->getHttpHeaderProperty($conn, 'Host');
    }
    public function getUserAgent(\Ratchet\ConnectionInterface $conn){
        return $this->getHttpHeaderProperty($conn, "User-Agent");
    }
    public function getOrigin(\Ratchet\ConnectionInterface $conn){
        return $this->getHttpHeaderProperty($conn, "Origin");
    }
    public function getCookie(\Ratchet\ConnectionInterface $conn){
        return $this->getHttpHeaderProperty($conn, 'Cookie');
    }
    public function getSecWebSocketVersion(\Ratchet\ConnectionInterface $conn){
        return $this->getHttpHeaderProperty($conn, "Sec-WebSocket-Version");
    }
    public function getSecWebSocketKey(\Ratchet\ConnectionInterface $conn){
        return $this->getHttpHeaderProperty($conn, "Sec-WebSocket-Key");
    }
    private function getHttpHeaderProperty(\Ratchet\ConnectionInterface $conn, string $propName){
        $headers = $this->getHttpRequestHeaders($conn);
        $v = null;
        if(isset($headers) && array_key_exists($propName, $headers)){
            $arr = $headers[$propName];
            $v = ((isset($arr) && count($arr)>0) ? $arr[0] : null);
        }
        return $v;
    }

    protected function getHttpParameter(\Ratchet\ConnectionInterface $conn, string $paramName){
        $querystring = $conn->httpRequest->getUri()->getQuery();
        parse_str($querystring,$queryarray);
        if(isset($queryarray) && array_key_exists($paramName, $queryarray)){
            return $queryarray[$paramName];
        }
        return null;
    }
}