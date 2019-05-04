<?php
/*
Autor:  Thiago Goulart.
		www.thiagogoulart.com.br

Data de criação: 17/10/2009.
alterado em: 12-02-2014
*/

class DAOColuna {

	private $dao;
	
	private $columnOrder;
	private $nome;
	private $label;
	private $colunaLabel;
	private $tipo;
	private $searchable;
	private $orderByType;
	private $join;
	
	private $obrigatorio = false;
	
	
	public function __construct(){
		$this->searchable=false;
		$this->orderByType='';
		$this->obrigatorio = false;
	}
	
	
/*START getters e setters*/	
	public function getColumnOrder(){
		return $this->columnOrder;
	}
	public function setColumnOrder($p){
		$this->columnOrder = $p;
	}
	public function getNome(){
		return $this->nome;
	}
	public function setNome($p){
		$this->nome = $p;
	}
	public function isObrigatorio(){
		return $this->obrigatorio;
	}
	public function setObrigatorio($p){
		$this->obrigatorio = $p;
	}
	public function getJoin(){
		return $this->join;
	}
	public function setJoin($p){
		$this->join = $p;
	}
	public function getLabel(){
		return $this->label;
	}
	public function setLabel($p){
		$this->label = $p;
	}
	public function getColunaLabel(){
		return $this->colunaLabel;
	}
	public function setColunaLabel($p){
		$this->colunaLabel = $p;
	}
	public function getTipo(){
		return $this->tipo;
	}
	public function setTipo($p){
		$this->tipo = $p;
	}
	public function getDao(){
		return $this->dao;
	}
	public function setDao($p){
		$this->dao = $p;
	}
	public function isSearchable(){
		return $this->searchable;
	}
	public function setSearchable($p){
		$this->searchable = $p;
	}
	public function isOrdenable(){
		return isNotBlank($this->orderByType);
	}
	public function getOrderByType(){
		return $this->orderByType;
	}
	public function setOrderByType($p){
		$this->orderByType = $p;
	}
        
        public function isTipoBoolean(){
            return ($this->tipo=='boolean');
        }
/*END getters e setters*/	
	
	public function getSearchSql($search){
		if($this->isSearchable()){
			$cond = '';
			$qtdAdded = 0;
			$search = strtoupper($search);
			$palavras = explode(" ", $search);	
			$qtdPalavras = count($palavras);
			for($j=0; $j!=$qtdPalavras; $j++){
				$table = $this->getDao()->getTabela();						
				$added = false;
				$w = '';
				$valuePalavra = $this->dao->getValorColunaParaScript($this, $palavras[$j], true);
				if($this->getTipo()=='string'){
					$w .= " UPPER(".$table.".".$this->getNome().") LIKE ".$valuePalavra." "; 
					$added = true;
					$qtdAdded++;
				}
				else if(is_numeric($search) && $this->getTipo()=='integer'){
					$w .= " ".$table.".".$this->getNome()." = ".$valuePalavra." ";
					$added = true;
					$qtdAdded++;
				}
				if($qtdAdded>1 && $added){
					$cond .= "AND ".$w;
				}else{
					$cond .= $w;
				}
			}
			return $cond;
		}
		return null;
	}


}

?>