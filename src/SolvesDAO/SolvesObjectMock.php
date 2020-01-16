<?php
namespace SolvesDAO;

/*
Autor:  Thiago Goulart.
Data de criação: 16/01/2020
*/

class SolvesObjectMock {

    protected $object;
    protected $mockedMethods = [];
    protected $mockedDaoMethods = [];


    public function __construct($object) {
        $this->object = $object;
        $this->object->setMock($this);
    }

//MOCKS
    public function mockMethod(string $methodname, callable $function){
        $this->mockedMethods[$methodname] = $function;
    }
    public function getMockMethod(string $methodname): callable{
        return $this->mockedMethods[$methodname];
    }
    public function hasMockMethod(string $methodname): bool{
        return array_key_exists($methodname, $this->mockedMethods);
    }
    public function executeMockMethod(string $methodname, $args=null){
        $function = $this->getMockMethod($methodname);
        if(null==$args){
            $args=[];
        }
        return call_user_func_array($function, $args);
    }
//DAO MOCKS
    public function mockDaoMethod(string $methodname, callable $function){
        if(\Solves\Solves::stringComecaCom($methodname, 'SolvesDAO\\\DAO::')){
            $arr = explode('SolvesDAO\DAO::', $methodname);
            $methodname = $arr[1];
        }
        $this->mockedDaoMethods[$methodname] = $function;
    }
    public function getMockDaoMethod(string $methodname): callable{
        if(\Solves\Solves::stringComecaCom($methodname, 'SolvesDAO\\\DAO::')){
            $arr = explode('SolvesDAO\DAO::', $methodname);
            $methodname = $arr[1];
        }
        return $this->mockedDaoMethods[$methodname];
    }
    public function hasMockDaoMethod(string $methodname): bool{
        if(\Solves\Solves::stringComecaCom($methodname, 'SolvesDAO\\\DAO::')){
            $arr = explode('SolvesDAO\DAO::', $methodname);
            $methodname = $arr[1];
        }
        return array_key_exists($methodname, $this->mockedDaoMethods);
    }
    public function executeMockDaoMethod(string $methodname, $args=null){
        $function = $this->getMockDaoMethod($methodname);
        if(null==$args){
            $args=[];
        }
        return call_user_func_array($function, $args);
    }
//MAGICS    
    public function __get(string $nomeAtributo){
      // echo 'Calling __get method ',$nomeAtributo,'<br />';
        return $this->object->get($nomeAtributo);
    }
    public function __set(string $nomeAtributo, $valor){
     //  echo 'Calling __set method ',$nomeAtributo,'<br />';
        return $this->object->set($nomeAtributo, $valor);
    }
    public function __call(string $method_name, ?array $args=null) {
      // echo 'Calling method ',$method_name,'<br />';
       if($this->hasMockMethod($method_name)){
            return $this->executeMockMethod($method_name, $args);
       }
        if(null==$args){
            $args=[];
        }
       return call_user_func_array(array($this->object, $method_name), $args);
    }
}
?>