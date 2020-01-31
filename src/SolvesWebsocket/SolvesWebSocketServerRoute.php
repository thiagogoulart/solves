<?php
namespace SolvesWebsocket;


/**
 * Class SolvesWebSocketServerRoute
 * @package SolvesWebsocket
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 06/01/2020
 */
abstract class SolvesWebSocketServerRoute {

    /**
     * @var string
     * Nome da rota que será utilizado como referencia para buscá-la (Ex: "chat")
     */
    protected $name;
    /**
     * @var string
     * Caminho da rota (Ex: "/chat")
     */
    protected $path;
    /**
     * @var \SplObjectStorage
     * Array (map) de objetos de conexões clientes.
     */
    protected $clients;
    /**
     * @var array
     * Array de objetos de conexões clientes com índice de resourceId.
     */
    protected $connections = array();


    protected $hasPeriodicFunction=false;
    protected $intervalSecondsPeriodicFunction;

    /**
     * SolvesWebSocketServerRoute constructor.
     * @param string $name
     * @param string $path
     */
    public function __construct(string $name, string $path) {
        $this->name = $name;
        $this->path = $path;
        $this->clients = new \SplObjectStorage;
    }

    public function configPeriodicFunction(int $intervalSecondsPeriodicFunction){
        $this->intervalSecondsPeriodicFunction = $intervalSecondsPeriodicFunction;
        $this->hasPeriodicFunction=true;
    }

    public function hasPeriodicFunction(): bool{
        return $this->hasPeriodicFunction;
    }
    public function getIntervalSecondsPeriodicFunction(): int{
        return $this->intervalSecondsPeriodicFunction;
    }
    public function executePeriodicFunction($server, \React\EventLoop\LoopInterface $loop, ?\React\EventLoop\Timer\Timer $timer=null){
        $this->periodicFunction($server, $loop, $timer);
    }

    public abstract function periodicFunction($server, \React\EventLoop\LoopInterface $loop, ?\React\EventLoop\Timer\Timer $timer=null);

    /**
     * @return string
     * Retorna o Nome da rota que será utilizado como referencia para buscá-la (Ex: "chat")
     */
    public function getName(): string{
        return $this->name;
    }
    /**
     * @return string
     * Retorna o caminho da rota (EX: "/chat")
     */
    public function getPath(): string{
        return $this->path;
    }

    /**
     * @return int
     * Retorna a quantidade total de conexões ativas.
     */
    public function getQtdConnections(): int{
        return count($this->connections);
    }

    /**
     * @param \Ratchet\ConnectionInterface $conn
     * Atualiza os arrays de conexões e configurações necessárias quando uma nova conexão é ABERTA.
     */
    protected function registraNovaConexao(\Ratchet\ConnectionInterface $conn){
        $this->connections[$conn->resourceId] = $conn;
        $this->clients->attach($conn);
    }

    /**
     * @param \Ratchet\ConnectionInterface $conn
     * Atualiza os arrays de conexões e configurações necessárias quando uma conexão é FECHADA.
     */
    protected function fechaConexao(\Ratchet\ConnectionInterface $conn){
        $resourceId = $conn->resourceId;
        if(array_key_exists($resourceId, $this->connections)){
            unset($this->connections[$resourceId]);
        }
        $this->clients->detach($conn);
    }

    /**
     * @param int $resourceId
     * @return \Ratchet\ConnectionInterface|null
     * Retorna qual a conexão ativa pelo RESOURCE_ID informado.
     */
    protected function getConnectionClientByResourceId(int $resourceId): ?\Ratchet\ConnectionInterface{
        return (array_key_exists($resourceId, $this->connections) ? $this->connections[$resourceId] : null);
    }

}