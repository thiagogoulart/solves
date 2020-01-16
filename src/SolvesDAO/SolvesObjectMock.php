<?php
namespace SolvesDAO;

/*
Autor:  Thiago Goulart.
Data de criação: 16/01/2020
*/

class SolvesObjectMock {

    protected $object;
    protected $mockedMethods = [];

    public function __construct($object) {
        $this->object = $object;
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
    public function executeMockMethod(string $methodname, $args){
        $function = $this->getMockMethod($methodname);
        return call_user_func_array($function, $args);
    }
    public function __get(string $nomeAtributo){
      // echo 'Calling __get method ',$nomeAtributo,'<br />';
        return $this->object->get($nomeAtributo);
    }
    public function __set(string $nomeAtributo, $valor){
     //  echo 'Calling __set method ',$nomeAtributo,'<br />';
        return $this->object->set($nomeAtributo, $valor);
    }
    public function __call(string $method_name, ?array $args) {
      // echo 'Calling method ',$method_name,'<br />';
       if($this->hasMockMethod($method_name)){
            return $this->executeMockMethod($method_name, $args);
       }
       return call_user_func_array(array($this->object, $method_name), $args);
    }
}
?>