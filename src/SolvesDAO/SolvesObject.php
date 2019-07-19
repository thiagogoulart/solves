<?php
/*
Autor:  Thiago Goulart.
Data de criação: 19/07/2019
*/

namespace SolvesDAO;

abstract class SolvesObject {

	protected $dao;
	protected $parentDao;
	protected $connection;

    protected $numRows;

    /**Common atributes */
	protected $created_at= '0000-00-00 00:00:00';
	protected $created_atLabel;
	protected $updated_at= '0000-00-00 00:00:00';
	protected $updated_atLabel;
	protected $ativo;
	protected $ativoLabel;
	protected $ativo_at= '0000-00-00 00:00:00';
	protected $ativo_atLabel;
	protected $inativo_at= '0000-00-00 00:00:00';
	protected $inativo_atLabel;
	protected $removed;
	protected $removedLabel;
	protected $removed_at= '0000-00-00 00:00:00';
	protected $removed_atLabel;

	public function __construct($con, $tabela, $pk, $sequencia=null, $parentDao=null) {
		$this->connection = $con;
		$this->parentDao = $parentDao;

		$this->dao = new DAO;
		$this->dao->setConnection($con);
		$this->dao->setTabela($tabela);
		$this->dao->setPk($pk);
		$this->dao->setSequencePk($sequencia);
	}


	public abstract function setId($id);
	public abstract function getId();
	public abstract function addValores();
	public abstract function getObject($itemArr);


    public function getConnection() {
        return $this->connection;
    }

    public function getNumRows() {
        return $this->numRows;
    }
    public function setNumRows($p) {
        $this->numRows = $p;
    }



	public function getCreatedAt() {return $this->created_at;}
	public function setCreatedAt($p) {$this->created_at = $p;}
	public function getCreatedAtLabel() {return $this->created_atLabel;}
	public function setCreatedAtLabel($p) {$this->created_atLabel = $p;}

	public function getUpdatedAt() {return $this->updated_at;}
	public function setUpdatedAt($p) {$this->updated_at = $p;}
	public function getUpdatedAtLabel() {return $this->updated_atLabel;}
	public function setUpdatedAtLabel($p) {$this->updated_atLabel = $p;}

	public function getAtivoAt() {return $this->ativo_at;}
	public function setAtivoAt($p) {$this->ativo_at = $p;}
	public function getAtivoAtLabel() {return $this->ativo_atLabel;}
	public function setAtivoAtLabel($p) {$this->ativo_atLabel = $p;}

	public function isAtivo() {return \Solves\Solves::checkBoolean($this->ativo);}
	public function getAtivo() {return $this->ativo;}
	public function setAtivo($p) {$this->ativo = $p;}
	public function getAtivoLabel() {return $this->ativoLabel;}
	public function setAtivoLabel($p) {$this->ativoLabel = $p;}

	public function getInativoAt() {return $this->inativo_at;}
	public function setInativoAt($p) {$this->inativo_at = $p;}
	public function getInativoAtLabel() {return $this->inativo_atLabel;}
	public function setInativoAtLabel($p) {$this->inativo_atLabel = $p;}

	public function isRemoved() {return \Solves\Solves::checkBoolean($this->removed);}
	public function getRemoved() {return $this->removed;}
	public function setRemoved($p) {$this->removed = $p;}
	public function getRemovedLabel() {return $this->removedLabel;}
	public function setRemovedLabel($p) {$this->removedLabel = $p;}

	public function getRemovedAt() {return $this->removed_at;}
	public function setRemovedAt($p) {$this->removed_at = $p;}
	public function getRemovedAtLabel() {return $this->removed_atLabel;}
	public function setRemovedAtLabel($p) {$this->removed_atLabel = $p;}

  	public function saveReturningId() {$id = $this->save();$this->setId($id);return $id;}

 	public function getDao() {return $this->dao;}

  	public function getNextIdValue() {return $this->dao->getNextIdValue();}

 	 public function findByCondition($empresaId, $condition) {$list = $this->dao->findByCondition($condition);return $this->toObjectArray($list);}

    public function findBySearch($search) {
        $list = $this->dao->findBySearch($search);
        $resultado = $this->toObjectArray($list);
        return $resultado;
    }

 	 public function findById($id) {if (@$id && isset($id)) {$list = $this->findObjectArrayById($id);$resultado = $this->toObjectArray($list);if (isset($resultado) && count($resultado) > 0) {return $resultado[0];} }return null;}

 	 public function findObjectArrayById($id) {if (@$id && isset($id)) {return $this->dao->findById($id);}return null;}
 	 public function save() {$this->addValores();return $this->dao->save();}
  	 public function update() {$this->addValores();return $this->dao->update($this->getId());}
  	 public function remove() {$this->setRemoved(1);$this->setUpdatedAt(getTimestampAtual());return $this->update();}

 	 public function toObjectArray($list) {$resultado = array();$qtd = count($list);for ($i = 0; $i != $qtd; $i++) {$object = $this->getObject($list[$i]);$resultado[] = $object;}return $resultado;}

 	 public function toArray() {
  		 $arr = array(); 
  		 $cols = $this->dao->getColunas();
		
		foreach($cols as $col){
			$arr[$col->getNome()] = $this->dao->getValorColunaByOrder($col->getColumnOrder());
		}
  		 return $arr; 
  	}
}
?>