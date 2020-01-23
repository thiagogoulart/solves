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

    protected $solvesObjId;
    protected $numRows;
    protected $old;
    protected $oldArray;

    public static $PAGINACAO_QTD = 20;

    /**Common atributes */
    protected $created_at= null;
    protected $created_at_label;
    protected $updated_at= null;
    protected $updated_at_label;
    protected $ativo;
    protected $ativoLabel;
    protected $ativo_at= null;
    protected $ativo_at_label;
    protected $inativo_at= null;
    protected $inativo_at_label;
    protected $removed;
    protected $removedLabel;
    protected $removed_at= null;
    protected $removed_at_label;

    protected $arrIdsColunasSensiveis = array();
    protected $mock;
    protected $arrAtributosAlterados = array();

    public function __construct(?\SolvesDAO\SolvesDAOCOnnection $con, $tabela, $pk, $sequencia=null, $parentDao=null) {
        $this->connection = $con;
        $this->parentDao = $parentDao;

        $this->dao = new DAO();
        $this->dao->setConnection($con);
        $this->dao->setTabela($tabela);
        $this->dao->setPk($pk);
        $this->dao->setSequencePk($sequencia);
    }

    public abstract function beforeSaveAndUpdate();
    public abstract function afterSave();
    public abstract function afterUpdate($old);
    public abstract function afterDelete();

    protected function limpaAtributosAlterados(){
        $this->arrAtributosAlterados = array();
    }
    protected function addAtributoAlterado(string $keyNameAtributo, $value){
        $this->arrAtributosAlterados[$keyNameAtributo] = $value;
    }
    public function getAtributosAlterados(){
        return $this->arrAtributosAlterados;
    }

    public function setId($id){
        $this->solvesObjId = $id;
        return $this->set($this->dao->getPk(), $id);
    }
    public function getId(){
        $result = $this->get($this->dao->getPk());
        if(isset($result)){
            return $result;
        }else{
            return $this->solvesObjId;
        }
    }
    public function setMock(\SolvesDAO\SolvesObjectMock $m){
        $this->mock = $m;
        $this->dao->setMock($m);
    }
    public function getMock(): \SolvesDAO\SolvesObjectMock{
        return $this->mock;
    }

    public function setArrIdsColunasSensiveis($p) {
        $this->arrIdsColunasSensiveis = $p;
        $this->dao->setArrIdsColunasSensiveis($this->arrIdsColunasSensiveis);
    }
    public function getConnection() {
        return $this->connection;
    }

    public function getNumRows() {
        return $this->numRows;
    }
    public function setNumRows($p) {
        $this->numRows = $p;
    }


    public function retiraAtributosSensiveisArray($arrItem) {
        return $this->dao->removeAtributosSensiveisDeArray($arrItem, $this->arrIdsColunasSensiveis);
    }

    public function isAtivo() {return \Solves\Solves::checkBoolean($this->ativo);}
    public function getAtivo() {return $this->ativo;}
    public function setAtivo($p, $createdAt=null) {
        $this->ativo = $p;
        $this->setAtivoLabel($this->ativo?'Sim':'Não');
        if(null!=$createdAt){
            if($this->ativo){
                $this->setAtivoAt($createdAt);
            }else{
                $this->setInativoAt($createdAt);
            }
        }
    }

    public function isRemoved() {return \Solves\Solves::checkBoolean($this->removed);}


    protected function getSearchTag($value){
        return (\Solves\Solves::isNotBlank(\Solves\Solves::removeEspacos($value)) ? $value.', ': '');
    }
    protected function getGeneratedShortName($value){
        return \Solves\Solves::getUrlNormalizada($value);
    }
    public static function getClassName() {
        return get_called_class();
    }

    public function saveReturningId() {return $this->save();}

    public function getDao() {return $this->dao;}

    public function getNextIdValue() {return $this->dao->getNextIdValue();}

    public function findByCondition($empresaId, $condition) {$list = $this->dao->findByCondition($condition);return $this->toObjectArray($list);}

    public function findBySearch($search) {
        $list = $this->dao->findBySearch($search);
        $resultado = $this->toObjectArray($list);
        return $resultado;
    }

    public function findById($id) {if (@$id && isset($id)) {$list = $this->findObjectArrayById($id);return $this->toOneObject($list);}return null; }

    public function findObjectArrayById($id) {if (@$id && isset($id)) {return $this->dao->findById($id);}return null;}
    public function save() {
        $this->addValores();
        $id = $this->dao->save();
        $this->setId($id);
        $this->afterSave();
        return $id;
    }
    public function update() {$this->addValores();$result = $this->dao->update($this);$this->afterUpdate($this->old);return $result;}
    public function remove() {$dt = \Solves\SolvesTime::getTimestampAtual();$this->setRemoved(1);$this->setUpdatedAt($dt);$this->setRemovedAt($dt);$result = $this->update();$this->afterDelete();return $result;}

    public function delete(){
        $result =  $this->dao->delete($this->getId());
        $this->afterDelete();
        return $result;
    }

    public function toObjectArray($list) {
        $resultado = array();
        $qtd = (isset($list) && is_array($list) ? count($list) : 0);
        for ($i = 0; $i != $qtd; $i++) {
            $object = $this->getObject($list[$i]);
            $resultado[] = $object;
        }
        return $resultado;
    }
    public function toOneObject($list) {
        $resultado = $this->toObjectArray($list);
        if (isset($resultado) && count($resultado) > 0) {
            $this->old = $resultado[0];
            $this->old->old = $resultado[0];
            $this->oldArray = $list[0];
            $this->old->oldArray = $list[0];
            return $this->old;
        } else {
            return null;
        }
    }
    public function getOld(){
        return $this->old;
    }
    public function getOldArray(){
        return $this->oldArray;
    }
    public function getOldColumnValue(string $colName){
        return (isset($this->oldArray) && array_key_exists($colName, $this->oldArray) ? $this->oldArray[$colName] : null);
    }
    public function getConditionSqlWithPagination($paginacaoAtual, $paginacaoQtd=null, $conditionPadrao = null) {
        if(!\Solves\Solves::isNotBlank($paginacaoQtd)){
            $paginacaoQtd = self::$PAGINACAO_QTD;
        }
        $init = 0;
        if($paginacaoAtual>1){
            $init = ($paginacaoAtual-1)*$paginacaoQtd;
            $this->dao->setLimit($init.','.$paginacaoQtd);
        }else if($paginacaoAtual==1){
            $this->dao->setLimit($init.','.$paginacaoQtd);
        }
        if($conditionPadrao==null){
            $colRemoved = $this->dao->getColunaByNome('removed');
            if(isset($colRemoved) && ($colRemoved->getDao()!=null) ){
                $conditionPadrao = " ".$colRemoved->getNomeWithPrefix()." =0 ";
            }else{
                $conditionPadrao =" 1=1 ";
            }
        }
        $sql = " WHERE ". $conditionPadrao;
        return ($sql);
    }

    protected function addValores() {
        $this->beforeSaveAndUpdate();
        $id = $this->getId();
        $hasId = isset($id);
        if ($hasId) {
            $this->dao->setPkValue($id);
        }
        $cols = $this->dao->getColunas(false);
        foreach($cols as $col){
            $v = $this->get($col->getNome());
            $this->dao->addValorColuna($col->getColumnOrder(), $v);
            if($hasId){
                $vOld = $this->getOldColumnValue($col->getNome());
                if($v!=$vOld){
                    $this->addAtributoAlterado($col->getNome(),$v);
                }
            }
        }
    }

    public function getObject($itemArr) {
        $object = new static($this->dao->getConnection());
        $id = $itemArr[$this->dao->getPk()];
        if ($id) {
            $object->setId($id);
        }
        foreach($itemArr as $key=>$value){
            $object->set($key, $value);
        }
        $object->limpaAtributosAlterados();
        return $object;
    }
    public function toArray() {
        $this->addValores();
        $arr = array();
        $cols = $this->dao->getColunas();

        $arr['id'] = $this->getId();
        $arr[$this->dao->getPk()] = $arr['id'];

        foreach($cols as $col){
            if(isset($this->arrIdsColunasSensiveis) && array_search($col->getColumnOrder(), $this->arrIdsColunasSensiveis)){
                continue;
            }
            //try to find getter of value
            $valor = $this->get($col->getNome());
            if(null==$valor){
                $vCol = $this->dao->getValorColunaByOrder($col->getColumnOrder());
                if(isset($vCol)){
                    $valor = $vCol->getValor(true);
                }
            }
            $arr[$col->getNome()] = $valor;

            //try to find getter of value label
            $valorLabel = $this->get($col->getNome().'_label');
            if(null!=$valorLabel){
                $arr[$col->getNome().'_label'] = $valorLabel;
            }else if(\Solves\Solves::stringTerminaCom($col->getNome(), '_id')){
                $valorLabel = $this->get(substr($col->getNome(), 0, -3).'_label');
                if(null!=$valorLabel){
                    $arr[$col->getNome().'_label'] = $valorLabel;
                }
            }
            $arrJoinValues = $this->getJoinLabelValuesOfColumn($col);
            if(isset($arrJoinValues) && count($arrJoinValues)>0){
                $arr = array_merge($arr, $arrJoinValues);
            }
        }
        return $arr;
    }
    /**
     * pegar colunas label do JOIN para o toArray
     * */
    private function getJoinLabelValuesOfColumn(\SolvesDAO\DAOColuna $coluna): array{
        $arr = [];
        $joins_sql = '';
        $join = $coluna->getJoin();
        $prefixCol = $coluna->getNome().'_';
        while (isset($join)) {
            $daoTarget = $join->getDaoTarget();
            $daoTargetColLabel = $daoTarget->getColunaLabelName();
            $keyLabelName = $prefixCol.$daoTargetColLabel;
            $valorLabel = $this->get($keyLabelName);
            if(null==$valorLabel){
                $valorLabel = $this->get($prefixCol.'label');
            }
            $arr[$keyLabelName] = $valorLabel;

            $join = (isset($var_colunaLabel) ? $var_colunaLabel->getJoin() : null);

            $daoTargetColsLabel = $daoTarget->getColsLabelOrder();
            if(isset($daoTargetColsLabel) && count($daoTargetColsLabel)>0) {
                //other join labels
                foreach ($daoTargetColsLabel as $targetColLabel) {
                    $keyLabelName = $prefixCol.$targetColLabel->getNome();
                    $valorLabel = $this->get($keyLabelName);
                    $arr[$keyLabelName] = $valorLabel;
                }
            }
        }
        return $arr;
    }


    public function __get(string $nomeAtributo){
        return $this->get($nomeAtributo);
    }
    public function __set(string $nomeAtributo, $valor){
        return $this->set($nomeAtributo, $valor);
    }
    public function get(?string $nomeAtributo, $secondChance=false){
        if(!\Solves\Solves::isNotBlank($nomeAtributo)){
            return null;
        }
        $getterName = 'get'.\Solves\Solves::getNomeClasse($nomeAtributo);
        $result = $this->executeMethodIfExists($nomeAtributo);
        if(!$result[0]){
            $result = $this->executeMethodIfExists($getterName);
            if(!$result[0] && property_exists($this,$nomeAtributo)){
                // Getter/Setter not defined so return property if it exists
                $v =  $this->$nomeAtributo;
                return $v;
            }
        }
        if($result[0]){
            return $result[1];
        }else if(!$secondChance){
            $nomeAtributo = \Solves\Solves::getNomeNormalizadoComUnderline($nomeAtributo);
            return $this->get($nomeAtributo, true);
        }
        return null;
    }
    public function set(?string $nomeAtributo, $valor, ?bool $secondChance=false, ?bool $setLabel=false){
        if(!\Solves\Solves::isNotBlank($nomeAtributo)){
            return null;
        }
        $setterName = 'set'.\Solves\Solves::getNomeClasse($nomeAtributo);
        $result = $this->executeMethodIfExists($nomeAtributo, $valor);
        $setouAttr = false;
        if(!$result[0]){
            $result = $this->executeMethodIfExists($setterName, $valor);
            if(!$result[0] && property_exists($this,$nomeAtributo)){
                // Setter not defined so return property if it exists
                if(is_array($valor)){
                    $valor = (count($valor)==1 ? $valor[0] : (count($valor)>1?$valor:null) );
                }
                $this->$nomeAtributo = $valor;
                $setouAttr = true;
            }
        }
        if($setouAttr){
            $this->addAtributoAlterado($nomeAtributo, $valor);
            if(!$setLabel){
                $this->setLabelValue($nomeAtributo, $valor);
            }
        }else if($result[0]){
            $this->addAtributoAlterado($nomeAtributo, $valor);
            if(!$setLabel){
                $this->setLabelValue($nomeAtributo, $valor);
            }
            return $result[1];
        }else if(!$secondChance){
            $nomeAtributo = \Solves\Solves::getNomeNormalizadoComUnderline($nomeAtributo);
            return $this->set($nomeAtributo, $valor, true, $setLabel);
        }else{
            $this->addAtributoAlterado($nomeAtributo, $valor);
        }
        return $this;
    }
    protected function setLabelValue(string $nomeAtributo, $val){
        $nomeAtributo = \Solves\Solves::getNomeNormalizadoComUnderline($nomeAtributo);
        $coluna = $this->dao->getColunaByNome($nomeAtributo);
        if(isset($coluna)){
            $valueLabel = null;
            if ($coluna->isTipoBoolean()) {
                $valueLabel = \Solves\Solves::getBooleanLabel($val);
            } else if ($coluna->isTipoDate()) {
                $valueLabel = \Solves\SolvesTime::getDataFormatadaSemHoras($val);
            } else if ($coluna->isTipoTimestamp()) {
                $valueLabel = \Solves\SolvesTime::getDataFormatada($val);
            } else if ($coluna->isTipoTime()) {
                $valueLabel = \Solves\SolvesTime::getHoraFormatada($val);
            } else if ($coluna->isTipoDouble()) {
                $valueLabel =$val;
            } else if ($coluna->isTipoMoney()) {
                $valueLabel = \Solves\Solves::formatMoney($val);
            }  else if ($coluna->isTipoPercentual()) {
                $valueLabel = $val.' %';
            }else{
                return;
            }
            $this->set($nomeAtributo.'_label', $valueLabel, false, true);
        }
    }
    public function __call($name, $arguments){
        if(\Solves\Solves::isNotBlank($name) && strlen($name)>3){
            $nomeAtributo = lcfirst(substr($name, 3));
            if(\Solves\Solves::stringComecaCom($name, 'get')){
                return $this->get($nomeAtributo);
            }else if(\Solves\Solves::stringComecaCom($name, 'set')){
                return $this->set($nomeAtributo, $arguments);
            }
        }
    }
    private function executeMethodIfExists(string $method, $attrs=null): array{
        $result = array(false, null);
        $v = null;
        if(null!=$attrs && is_array($attrs)){
            $attrs = (count($attrs)==1 ? $attrs[0] : (count($attrs)>1?$attrs:null) );
        }
        $rc = new \ReflectionClass($this);
        $has = $rc->hasMethod($method);
        if($has) {
            $v = $this->$method($attrs);
        }else{
            while($rc->getParentClass()){
                $parent = $rc->getParentClass()->name;
                $rc = new \ReflectionClass($parent);
                $has = $rc->hasMethod($method);
                if($has){
                    $reflectionMethod = new ReflectionMethod($parent, $method);
                    $v = $reflectionMethod->invoke($this, $attrs);
                    break;
                }
            }
        }
        $result[0] = $has;
        $result[1] = $v;
        return $result;
    }
    public function __toString(): string{
        $str = '';
        foreach ($this as $key => $value) {
            $str .= "$key => $value\n";
        }
        return $str;
    }

}
?>