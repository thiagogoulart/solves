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
        $this->resgistraNovaConexao($conn);
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
}