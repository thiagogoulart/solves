<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 06/01/2020
 */
namespace SolvesWebsocket;


class SolvesWebSocketServerRoute {

    protected $path;
    protected $clients;
    protected $connections = array();

    public function __construct($path) {
        $this->path = $path;
        $this->clients = new \SplObjectStorage;
    }

    public function getPath(){
        return $this->path;
    }

    protected function registraNovaConexao(\Ratchet\ConnectionInterface $conn){
        $this->connections[$conn->resourceId] = $conn;
        $this->clients->attach($conn);
    }
    protected function fechaConexao(\Ratchet\ConnectionInterface $conn){
        $resourceId = $conn->resourceId;
        $key = array_search($resourceId, $this->connections);
        if($key!==false){
            unset($this->connections[$key]);
        }
        $this->clients->detach($conn);
    }

    protected function getConnectionClientByResourceId($resourceId){
        return $this->connections[$resourceId];
    }

}