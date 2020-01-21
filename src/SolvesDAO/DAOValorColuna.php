<?php
/*
Autor:  Thiago Goulart.
		www.thiagogoulart.com.br

Data de cria??o: 02/01/2010.
*/

namespace SolvesDAO;

class DAOValorColuna {

    private $dao;
    private $column;
    private $valor;


    public function __construct(){

    }


    /*START getters e setters*/
    public function getColumn(){
        return $this->column;
    }
    public function setColumn($p){
        $this->column = $p;
    }
    public function getValor(?bool $isOnSelect=false){
        if($this->column->isObrigatorio() && !\Solves\Solves::isNotBlank($this->valor)){
            if($this->column->isTipoBoolean()){
                $this->valor = 'false';
            }else if(!$this->dao->isMock() && !\Solves\Solves::checkBoolean($isOnSelect)){
                $e = new \Exception('Coluna '.$this->column->getNome().' é obrigatória ['.$this->dao->getTabela().'].');
               // var_dump($e->getTraceAsString());
                throw $e;
            }
        }
        return $this->valor;
    }
    public function setValor($p){
        $this->valor = $p;
    }
    public function getDao(){
        return $this->dao;
    }
    public function setDao($p){
        $this->dao = $p;
    }
    /*END getters e setters*/




}

?>