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
    private static $app;
    private static $url;
    private static $host;
    private static $port;

    private static $buildApp;

    private static $configRoutes = [];
    private static $routes = [];

    public static function config(string $url, string $host, string $port, ?bool $buildApp=false){
        self::$url = $url;
        self::$host = $host;
        self::$port = $port;
        self::$buildApp = $buildApp;
        if(self::$buildApp){
            self::$app = new \Ratchet\App(self::$host, self::$port);
        }
    }
    public static function addConfigRoute(string $name, string $path){
        self::$configRoutes[$name] = $path;

    }
    public static function addRoute(SolvesWebSocketServerRoute $instance){    
        self::$routes[$instance->getPath()] = $instance;
        if(isset(self::$app)){
            self::$app->route($instance->getPath(), $instance, array('*'));
        }
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
    public static function addRouteEcho(){
        if(isset(self::$app)){
            self::$app->route('/echo', new \Ratchet\Server\EchoServer, array('*'));
        }
    }
    public static function getRoute($path){
        return self::$routes[$path];
    }
    public static function getRoutes(): array{
        return self::$routes;
    }

    public static function startServer(){
        //RUN
        if(isset(self::$app)){
            self::$app->run();
        }
    }

}