<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 06/01/2020
 */ 
namespace SolvesWebsocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\App;

class SolvesWebSocketServer {
    private $app;
    private $host;
    private $port;

    private $routes = [];

    public function __construct($host, $port) {
    	$this->host = $host;
    	$this->port = $port;

    	$this->app = new \Ratchet\App($this->host, $this->port);
    }

    public function addRoute(SolvesWebSocketServerRoute $instance){    
    	$this->routes[] = $instance;
	    $this->app->route($instance->getPath(), $instance, array('*'));
    }
    public function addRouteEcho(){    	
	    $this->app->route('/echo', new \Ratchet\Server\EchoServer, array('*'));
    }
    public function getRoutes(){
    	return $this->routes;
    }

    public function startServer(){
	    //RUN
    	$this->app->run();
    }

}