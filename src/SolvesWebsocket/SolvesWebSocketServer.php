<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 06/01/2020
 */ 
namespace SolvesWebsocket;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\SecureServer;
use React\EventLoop\Factory;
use React\Socket\Server;

class SolvesWebSocketServer {
    private static $app;
    private static $url;
    private static $host;
    private static $port;

    private static $buildApp;
    private static $wss;
    private static $local_cert;
    private static $local_pk;

    private static $configRoutes = [];
    private static $routes = [];

    public static function config(string $url, string $host, string $port, ?bool $buildApp=false, ?bool $wss=false, ?string $local_cert=null, ?string $local_pk=null){
        self::$url = $url;
        self::$host = $host;
        self::$port = $port;
        self::$buildApp = $buildApp;
        self::$wss = $wss;
        self::$local_cert = $local_cert;
        self::$local_pk = $local_pk;
    }
    public static function addConfigRoute(string $name, string $path){
        self::$configRoutes[$name] = $path;

    }
    public static function addRoute(SolvesWebSocketServerRoute $instance){    
        self::$routes[$instance->getPath()] = $instance;
    }
    public static function getWsUrl(): ?string{
        return self::$url;
    }
    public static function getRoutesStringObjArr(): string{
        $arrStr = '';
        $first = true;
        foreach(self::$configRoutes as $name=>$path){
            $arrStr.= ($first?'':',').'{name:"'.$name.'",path:"'.$path.'"}';
            $first = false;
        }
        return $arrStr;
    }
    public static function getRoute($path){
        return self::$routes[$path];
    }
    public static function getRoutes(): array{
        return self::$routes;
    }
    public static function getRouteInstance(): ?SolvesWebSocketServerRoute{
        $instance = null;
        if(isset(self::$routes) && count(self::$routes)>0){
            foreach(self::$routes as $key=>$inst){
                $instance = $inst;
                break;
            }
        }
        return $instance;
    }
    public static function startServer(){
        if(!self::$buildApp){
            echo 'NOT ABLE TO BUILD.';
            return;
        }
        $instance = self::getRouteInstance();
        if(isset($instance)){
            if(self::$wss){
                $app = new \Ratchet\Http\HttpServer(
                    new \Ratchet\WebSocket\WsServer(
                        $instance
                    )
                );

                $loop = \React\EventLoop\Factory::create();

                $secure_websockets = new \React\Socket\Server(self::$host.':'.self::$port, $loop);
                $secure_websockets = new \React\Socket\SecureServer($secure_websockets, $loop, [
                    'local_cert' => self::$local_cert,
                    'local_pk' => self::$local_pk,
                    'verify_peer' => false
                ]);

                self::$app = new \Ratchet\Server\IoServer($app, $secure_websockets, $loop);
                /**OLD
                self::$app   = Factory::create();
                $optValue = array(
                    'local_cert'        => self::$local_cert, // path to your cert
                    'local_pk'          => self::$local_pk, // path to your server private key
                    'allow_self_signed' => TRUE, // Allow self signed certs (should be false in production)
                    'verify_peer' => FALSE
                );
                $opts = array('tls' => $optValue);
                $tcpServer = new Server('tls://'.self::$host.':'.self::$port, self::$app, $opts);
                $webSock = $tcpServer;//new SecureServer($tcpServer, self::$app, $optValue);
                // Ratchet magic
                $webServer = new IoServer(
                    new HttpServer(
                        new WsServer(
                            $instance
                        )
                    ),
                    $webSock
                ); */
            }else {
                self::$app = IoServer::factory(
                    new HttpServer(
                        new WsServer(
                            $instance
                        )
                    ),
                    self::$port
                );
            }
            if(isset(self::$app)){
                if($instance->hasPeriodicFunction()){
                    $loop = self::$app;
                    if(self::$app instanceof IoServer){
                        $loop = self::$app->loop;
                    }
                    $server = self::$app;
                    $instance->executePeriodicFunction($server, $loop, null);
                    $loop->addPeriodicTimer($instance->getIntervalSecondsPeriodicFunction(), function(\React\EventLoop\Timer\Timer $timer) use ($instance, $server, $loop) {
                        $instance->executePeriodicFunction($server, $loop, $timer);
                    });
                }
                self::$app->run();
            }
        }else{
            throw new \Exception('INSTANCE NOT FOUND.');
        }
    }

}