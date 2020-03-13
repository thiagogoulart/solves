<?php
/*
Autor:  Thiago Goulart.
		www.thiagogoulart.com.br

Data de criação: 17/10/2009.
alterado em: 12-02-2014
*/

namespace SolvesDAO;

class DAOColuna {

	private $dao;
	
	private $columnOrder;
    private $tabela;
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
    public function getTabela(){
        return $this->tabela;
    }
    public function setTabela($p){
        $this->tabela = $p;
    }
	public function getNome(){
		return $this->nome;
	}
	public function setNome($p){
		$this->nome = $p;
	}
    public function getNomeWithPrefix(){
        $alias = (\Solves\Solves::isNotBlank($this->dao->getAlias()) ? $this->dao->getAlias().'.': (\Solves\Solves::isNotBlank($this->getTabela()) ? $this->getTabela().'.':''));
        return $alias.$this->getNome();
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
		return \Solves\Solves::isNotBlank($this->orderByType);
	}
	public function getOrderByType(){
		return $this->orderByType;
	}
	public function setOrderByType($p){
		$this->orderByType = $p;
	}

    public function isTipoBoolean(){
        return ('boolean'==$this->tipo);
    }
    public function isTipoTimestamp(){
        return ('timestamp'==$this->tipo);
    }
    public function isTipoDate(){
        return ('date'==$this->tipo);
    }
    public function isTipoTime(){
        return ('time'==$this->tipo);
    }
    public function isTipoMoney(){
        return ('money'==$this->tipo);
    }
    public function isTipoInteger(){
        return ('integer'==$this->tipo);
    }
    public function isTipoDouble(){
        return ('double'==$this->tipo);
    }
    public function isTipoPercentual(){
        return ('percentual'==$this->tipo);
    }
/*END getters e setters*/	
	
	public function getSearchSql($search){
		if($this->isSearchable() && \Solves\Solves::isNotBlank($search)){
			$search = strtoupper($search);
			$search = \Solves\Solves::removeEspacoesExcedentes($search);
			$palavras = explode(" ", $search);	
			$qtdPalavras = count($palavras);
			if($qtdPalavras>0){ 
				$cond = '';
				$qtdAdded = 0;
				for($j=0; $j!=$qtdPalavras; $j++){
					$added = false;
					$w = '';
					$valuePalavra = $this->dao->getValorColunaParaScript($this, $palavras[$j], true);
					if($this->getTipo()=='string'){
						$w .= " UPPER(".$this->getNomeWithPrefix().") LIKE ".$valuePalavra." ";
						$added = true;
						$qtdAdded++;
					}
					else if(is_numeric($search) && $this->getTipo()=='integer'){
						$w .= " ".$this->getNomeWithPrefix()." = ".$valuePalavra." ";
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
			}else{
				return null;
			}
		}
		return null;
	}


}

?>