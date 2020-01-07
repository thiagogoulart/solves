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

    public abstract function onEventMessage(\Ratchet\ConnectionInterface $from, $msg);
    public abstract function onEventOpen(\Ratchet\ConnectionInterface $conn);
    public abstract function onEventClose(\Ratchet\ConnectionInterface $conn);
    public abstract function onEventError(\Ratchet\ConnectionInterface $conn, \Exception $e);


    public function onMessage(\Ratchet\ConnectionInterface $from, $msg){
        $this->onEventMessage($from, $msg);
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

    public function sendToAll(\Ratchet\ConnectionInterface $from, $msg, $enviaAoRemetenteTambem = false){
        foreach ($this->clients as $client) {
            if ($enviaAoRemetenteTambem || $from != $client) {
                $client->send($msg);
            }
        }
    }
    public function sendToOne(\Ratchet\ConnectionInterface $from, $destinatarioResourceId, $msg){
        $connDestino = $this->getConnectionClientByResourceId($destinatarioResourceId);
        $connDestino->send($msg);
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
    private function getHttpHeaderProperty(\Ratchet\ConnectionInterface $conn, $propName){
        $v = $this->getHttpRequestHeaders($conn)[$propName];
        return ((isset($v) && count($v)>0) ? $v[0] : null);
    }
}