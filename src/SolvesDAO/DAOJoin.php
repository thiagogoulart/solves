<?php
/*
Autor:  Thiago Goulart.
Data de criação: 04/10/2013
*/

namespace SolvesDAO;

class DAOJoin {
	
	public static $LEFT_JOIN = 'LEFT';
	public static $RIGHT_JOIN = 'RIGHT';
	public static $INNER_JOIN = 'INNER';
	public static $OUTER_JOIN = 'OUTER';
	public static $JUST_JOIN = '';

	private $dao;
	private $daoColuna;
	
	private $type;
	private $colOrder;
	private $daoTarget;
	private $alias;
	
	
	public function __construct($dao, $daoColuna, $type, $colOrder, $daoTarget){
		$this->dao = $dao;
		$this->daoColuna = $daoColuna;
		$this->type = $type;
		$this->colOrder = $colOrder;
		$this->daoTarget = $daoTarget;
	}
	
	
/*START getters e setters*/	
	public function getColOrder(){
		return $this->colOrder;
	}
	public function setColOrder($p){
		$this->colOrder = $p;
	}
	public function getType(){
		return $this->type;
	}
	public function setType($p){
		$this->type = $p;
	}
	public function getAlias(){
		return $this->alias;
	}
	public function setAlias($p){
		$this->alias = $p;
	}
	public function getDao(){
		return $this->dao;
	}
	public function setDao($p){
		$this->dao = $p;
	}
	public function getDaoTarget(){
		return $this->daoTarget;
	}
	public function setDaoTarget($p){
		$this->daoTarget = $p;
	}
	public function getDaoColuna(){
		return $this->daoColuna;
	}
	public function setDaoColuna($p){
		$this->daoColuna = $p;
	}
/*END getters e setters*/	
	
	


}

?>