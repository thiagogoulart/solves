<?php
/*
Autor:  Thiago Goulart.
Data de criação: 01/01/2010.
 alteração: 28/03/2014.
 alteração: 19/07/2019.
 alteração: 18/09/2019.
Última alteração: 15/06/2021
*/

namespace SolvesDAO;

class DAO {

    private $connection;

    private $tabela;
    private $pk;
    private $alias;
    private $colunaLabelOrder;
    private $colsLabelOrder;
    private $colunas;
    private $valoresColunas;
    private $qtdColunas;
    private $limit;
    private $extendsDao;

    private $outrosFieldsSelect;
    private $outrosFieldsSelectSqlPuro;

    private $sequencePk;
    private $pkValue;
    private $orderByEspecifico;

    private $msgError;
    private $charset;
    private $isMock = false;

    /*Colunas que não devem estar presentes no retorno da consulta */
    private $arrIdsColunasSensiveis = array();
    protected $mock;

    public static $NULL_TIMESTAMP = null;

    public function __construct(?\SolvesDAO\SolvesDAOConnection $con, string $tabela, string $pk, ?string $sequencia=null) {
        $this->setConnection($con);
        $this->setTabela($tabela);
        $this->setPk($pk);
        $this->setSequencePk($sequencia);
        $this->colunas = array();
        $this->valoresColunas = array();
        $this->outrosFieldsSelect = array();
        $this->outrosFieldsSelectSqlPuro= array();
        $this->colsLabelOrder = array();

        $this->charset = "UTF-8";

        $this->msgError = '<br>Solicita&ccedil;&atilde;o n&atilde;o atendida. n&atilde;o foi poss&iacute;vel executar consulta.';
    }

    public function isSystemDbTypeMySql(): bool{return $this->connection->isSystemDbTypeMySql();}
    public function isSystemDbTypePostgresql(): bool{return $this->connection->isSystemDbTypePostgresql();}

    /*START getters e setters*/
    public function setArrIdsColunasSensiveis($p) {
        $this->arrIdsColunasSensiveis = $p;
    }
    public function getConnection() : ?SolvesDAOConnection{
        return $this->connection;
    }
    public function isMock() : bool{
        return $this->isMock;
    }
    public function setMock(\SolvesDAO\SolvesObjectMock $m){
        $this->mock = $m;
    }
    public function getMock(): \SolvesDAO\SolvesObjectMock{
        return $this->mock;
    }
    public function setConnection(SolvesDAOConnection $p=null){
        $this->connection = $p;
        $this->isMock = ($this->getBdConnection() instanceof SolvesDAOConnectionMock);
    }
    public function setOrderByEspecifico($p){
        $this->orderByEspecifico = $p;
    }
    public function getTabela(){
        return $this->tabela;
    }
    public function setTabela($p){
        $this->tabela = $p;
    }
    public function getAlias(){
        return $this->alias;
    }
    public function setAlias($p){
        $this->alias = $p;
    }
    public function getPk(){
        return $this->pk;
    }
    public function setPk($p){
        $this->pk = $p;
    }
    public function getPkWithPrefix(){
        return $this->tabela.'.'.$this->pk;
    }
    public function getPkValue(){
        return $this->pkValue;
    }
    public function setPkValue($p){
        $this->pkValue = $p;
    }
    public function addColunaObrigatoria($order, $nome, $tipo, $searchable, $orderByType){
        $this->addColuna($order, $nome, $tipo, $searchable, $orderByType);
        $this->colunas[$order]->setObrigatorio(true);
    }
    public function addColuna($order, $nome, $tipo, $searchable, $orderByType){
        $coluna = new DAOColuna();
        $coluna->setColumnOrder($order);
        $coluna->setNome($nome);
        $coluna->setLabel($nome);
        $coluna->setTipo($tipo);
        $coluna->setSearchable($searchable);
        $coluna->setOrderByType($orderByType);
        $coluna->setTabela($this->getTabela());
        $coluna->setDao($this);
        $this->colunas[$order] = $coluna;
    }
    public function addOutroFieldSelect($order, $coluna){
        $this->outrosFieldsSelect[$order] = $coluna;
    }
    public function addOutroFieldSelectSqlPuro($sql){
        $this->outrosFieldsSelectSqlPuro[] = $sql;
    }
    public function getSqlColNumRows($sqlCondition){
        $joins_sql = '';
        $cols = $this->getColunas();
        $this->qtdColunas = count($cols);
        foreach($cols as $coluna){
            $arr = $this->getSqlFormSelectColNameAndLabel($coluna);
            if(\Solves\Solves::isNotBlank($arr[2])){
                $joins_sql .= $arr[2];
            }
        }
        return "(SELECT COUNT(*) FROM ".$this->getTabela()." ".$joins_sql." ".$sqlCondition.") as num_rows";
    }
    public function setColunaLabelOrder($order){
        $this->colunas[$order]->setColunaLabel(true);
        $this->colunaLabelOrder = $order;
    }
    public function addColunaLabelOrder($order){
        $this->colunas[$order]->setColunaLabel(true);
        $this->colsLabelOrder[$order] = $this->colunas[$order];
    }
    public function getColunaLabel(){
        return $this->colunas[$this->colunaLabelOrder];
    }
    public function getColunaLabelName(){
        $l = $this->colunas[$this->colunaLabelOrder];
        if(isset($l)){
            return $l->getNome();
        }else{
            return $this->pk;
        }
    }
    public function addJoinWithAlias($type, $colOrder, $daoTarget, $alias){
        $daoColuna = $this->getColuna($colOrder);
        $daoTarget->setAlias($alias);
        $join = new DAOJoin($this, $daoColuna, $type, $colOrder, $daoTarget);
        $join->setAlias($alias);
        $this->colunas[$colOrder]->setJoin($join);
    }
    public function addJoin($type, $colOrder, $daoTarget){
        $daoColuna = $this->getColuna($colOrder);
        $join = new DAOJoin($this, $daoColuna, $type, $colOrder, $daoTarget);
        $this->colunas[$colOrder]->setJoin($join);
    }
    public function getJoin($colOrder){
        return $this->colunas[$colOrder]->getJoin();
    }
    public function getJoinDao($colOrder){
        $join = $this->colunas[$colOrder]->getJoin();
        return $join->getDaoTarget();
    }
    public function getColunaLabelOrder(){
        return $this->colunaLabelOrder;
    }
    public function getColsLabelOrder(){
        return $this->colsLabelOrder;
    }
    public function getColuna($order) : DAOColuna{
        return $this->colunas[$order];
    }
    public function getColunas(?bool $isSelect=true, ?bool $forceAllColumns=false){
        $cols = $this->colunas;
        $naoExibeSensiveis = (!$forceAllColumns && !$this->connection->isExibeColunasSensiveis() && isset($this->arrIdsColunasSensiveis) && count($this->arrIdsColunasSensiveis)>0);
        if($isSelect && $naoExibeSensiveis){
            foreach($this->arrIdsColunasSensiveis as $idColuna){
                unset($cols[$idColuna]);
                unset($cols[$idColuna."_label"]);
            }
        }
        return $cols;
    }
    public function getColunasAlteradas($obj, $id){
        $cols = [];
        $atributos = $obj->getAtributosAlterados();
        if(isset($atributos) && count($atributos)>0){
            foreach($atributos as $keyAtributo=>$valor){
                $keyAtributo = \Solves\Solves::getNomeNormalizadoComUnderline($keyAtributo);
                $col = $this->getColunaByNome($keyAtributo);
                if(isset($col)){
                    $i = $col->getColumnOrder();
                    $valorColuna = $this->getValorColunaByOrder($i);
                    if(!isset($valorColuna)){
                        $this->addValorColuna($i, $valor);
                    }
                    $cols[]= $col;
                }
            }
        }
        return $cols;
    }
    public function getColunaNome($order){
        $coluna = $this->colunas[$order];
        return $coluna->getNome();
    }
    public function getOutraColunaNome($order){
        $coluna = $this->outrosFieldsSelect[$order];
        return $coluna->getNome();
    }
    public function getColunaNomeWithPrefix($order){
        $coluna = $this->colunas[$order];
        if(!isset($coluna)){
            echo 'Coluna não encontrada ['.$this->tabela.':'.$order.'].';
        }
        return $coluna->getNomeWithPrefix();
    }
    public function getColunaByOrder($columnOrder) : DAOColuna{
        return $this->getColuna($columnOrder);
    }
    public function getColunaByNome($columnNome) : DAOColuna{
        $col = new DAOColuna();
        foreach($this->colunas as $coluna) {
            if ($coluna->getNome() == $columnNome) {
                $col = $coluna;
                break;
            }
        }
        return $col;
    }

    public function getValorColunaByOrder($columnOrder){
        return (isset($this->valoresColunas) && array_key_exists($columnOrder, $this->valoresColunas) ? $this->valoresColunas[$columnOrder] : null);
    }

    public function addValorColuna($colOrder, $valor){
        if($colOrder==0){
            $this->pkValue = $valor;
        }
        else{
            $valorColuna = new DAOValorColuna;
            $valorColuna->setColumn($this->getColuna($colOrder));
            $valorColuna->setValor($valor);
            $valorColuna->setDao($this);

            $this->valoresColunas[$colOrder] = $valorColuna;
        }
    }

    public function getValoresColunas(){
        return $this->valoresColunas;
    }
    public function setValoresColunas($p){
        $this->valoresColunas = $p;
    }
    public function getQtdColunas(){
        return $this->qtdColunas;
    }
    public function setQtdColunas($p){
        $this->qtdColunas = $p;
    }
    public function getLimit(){
        return $this->limit;
    }
    public function setLimit($p){
        $this->limit = $p;
    }
    public function getSequencePk(){
        return $this->sequencePk;
    }
    public function setSequencePk($p){
        $this->sequencePk = $p;
    }
    public function getExtendsDao(){
        return $this->extendsDao;
    }
    public function setExtendsDao($p){
        $this->extendsDao = $p;
        $cols = $this->extendsDao->getColunas();

        foreach($cols as $col){
            $this->colunas[] = $col;
        }

        $this->setQtdColunas(count($this->colunas));
    }
    private function getBdConnection(){
        return (isset($this->connection) ? $this->connection->getBdConnection() : null);
    }
    /*END getters e setters*/
    public function getSequencePkValue(){
        $sqlSeq = "SELECT nextval('".$this->sequencePk."'::regclass);";
        $resSequence = $this->sqlToResultArray($sqlSeq);

        $this->pkValue = $resSequence[0][0];
        return $this->pkValue;
    }

    //TODO substituir função MAX
    public function getNextIdValue(){
        if(isset($this->sequencePk) && strlen($this->sequencePk)>1){
            return $this->getSequencePkValue();
        }else{
            $sql = "SELECT MAX(".$this->pk.") FROM ".$this->tabela;
            $result = $this->sqlToResultArray($sql);
            if($result[0][0]+1==0){
                return 1;
            }
            return $result[0][0]+1;
        }
    }

    public function getCount(){
        $sql = "SELECT COUNT(*) as qtd FROM ".$this->tabela;
        $result = $this->sqlToResultArray($sql);
        return $result[0]['qtd'];
    }
    public function getCountByCondition($condition){
        $sql = "SELECT COUNT(*) as qtd FROM ".$this->tabela." ".$condition;
        $result = $this->sqlToResultArray($sql);
        return $result[0]['qtd'];
    }


    public function findAll(){
        if($this->tabela){
            $sql = "SELECT DISTINCT ";
            $joins_sql = '';
            $cols = $this->getColunas();
            $this->qtdColunas = count($cols);
            $qtd = $this->qtdColunas;
            $i=0;

            $sql .= $this->tabela.".".$this->pk;

            if($qtd>0){
                $sql .= ", ";
            }
            $ordenation = " ORDER BY ";
            $hasColOrdenation = false;
            foreach($cols as $coluna){
                $i++;

                $arr = $this->getSqlFormSelectColNameAndLabel($coluna);
                $colName = $arr[0];
                $colLabel = $arr[1];
                $joins_sql .= $arr[2];
                $sql.= $arr[3];

                if($i!=$qtd){
                    $sql .= ", ";
                }
                if($coluna->isOrdenable()){
                    $ordenation .= ($hasColOrdenation?', ':'').$colName.' '.$coluna->getOrderByType();
                    $hasColOrdenation = true;
                }
            }

            foreach($this->outrosFieldsSelect as $otherCol){
                $arr = $this->getSqlFormSelectColNameAndLabel($otherCol);
                $colName = $arr[0];
                $colLabel = $arr[1];
                $sql.= ','.$arr[3];
            }
            foreach($this->outrosFieldsSelectSqlPuro as $sqlColPuro){
                $sql.= ','.$sqlColPuro;
            }
            if($ordenation==" ORDER BY "){
                $ordenation .= "1 ";
            }
            if(substr($ordenation,strlen($ordenation)-2,strlen($ordenation))==", "){
                $ordenation = substr($ordenation,0,strlen($ordenation)-2);
            }

            $sql .= " FROM ";
            if($this->extendsDao){
                $sql .= $this->tabela;
                $sql .= " JOIN ".$this->extendsDao->getTabela()." ON ";
                $sql .= $this->tabela.".".$this->pk."=".$this->extendsDao->getTabela().".".$this->extendsDao->getPk();
            }
            else{
                $sql .= $this->tabela;
            }
            $sql .= $joins_sql;

            if(\Solves\Solves::isNotBlank($this->orderByEspecifico)){
                $sql .= "  ".$this->orderByEspecifico. " ";
            }else{
                $sql .= $ordenation." ";
            }
            if($this->limit){
                $sql .= " LIMIT ".$this->limit;
            }
            $sql .= ";";

            //      echo $sql;

            $result = $this->sqlToResultArray($sql);

            return $result;
        }
        else{
            echo $this->msgError;
            return false;
        }
    }
    public function findById($id){
        if($this->tabela && $id){
            $sql = "SELECT DISTINCT ";
            $joins_sql = '';
            $cols = $this->getColunas();
            $qtd = count($cols);
            $i=0;

            $sql .= $this->tabela.".".$this->pk;

            if($qtd>0){
                $sql .= ", ";
            }

            $ordenation = " ORDER BY ";
            $hasColOrdenation = false;
            foreach($cols as $coluna){
                $i++;

                $arr = $this->getSqlFormSelectColNameAndLabel($coluna);
                $colName = $arr[0];
                $colLabel = $arr[1];
                $joins_sql .= $arr[2];
                $sql.= $arr[3];

                if($i!=$qtd){
                    $sql .= ", ";
                }
                if($coluna->isOrdenable()){
                    $ordenation .= ($hasColOrdenation?', ':'').$colName.' '.$coluna->getOrderByType();
                    $hasColOrdenation = true;
                }
            }
            foreach($this->outrosFieldsSelect as $otherCol){
                $arr = $this->getSqlFormSelectColNameAndLabel($otherCol);
                $colName = $arr[0];
                $colLabel = $arr[1];
                $sql.= ','.$arr[3];
            }
            foreach($this->outrosFieldsSelectSqlPuro as $sqlColPuro){
                $sql.= ','.$sqlColPuro;
            }

            if($ordenation==" ORDER BY "){
                $ordenation .= "1 ";
            }


            $sql .= " FROM ";
            if($this->extendsDao){
                $sql .= $this->tabela;
                $sql .= " JOIN ".$this->extendsDao->getTabela()." ON ";
                $sql .= $this->tabela.".".$this->pk."=".$this->extendsDao->getTabela().".".$this->extendsDao->getPk();
            }
            else{
                $sql .= $this->tabela;
            }
            $sql .= $joins_sql;

            $sql .= " WHERE ".$this->tabela.".".$this->pk."=".$id." ".@$ordenation." ";

            if($this->limit){
                $sql .= " LIMIT ".$this->limit;
            }
            $sql .= ";";

            //echo $sql;

            $result = $this->sqlToResultArray($sql);

            return $result;
        }
        else{
            echo $this->msgError;
            return false;
        }
    }
    public function findByCondition($condition){
        if($this->tabela && $condition){

            $sql = "SELECT DISTINCT ";
            $joins_sql = '';
            $cols = $this->getColunas();
            $qtd = count($cols);
            $i=0;

            $sql .= " ".$this->tabela.".".$this->pk;

            if($qtd>0){
                $sql .= ", ";
            }

            $ordenation = " ORDER BY ";
            $hasColOrdenation = false;
            foreach($cols as $coluna){
                $i++;
                $arr = $this->getSqlFormSelectColNameAndLabel($coluna);
                $colName = $arr[0];
                $colLabel = $arr[1];
                $joins_sql .= $arr[2];
                $sql.= $arr[3];

                if($i!=$qtd){
                    $sql .= ", ";
                }
                if($coluna->isOrdenable()){
                    $ordenation .= ($hasColOrdenation?', ':'').$colName.' '.$coluna->getOrderByType();
                    $hasColOrdenation = true;
                }
            }
            foreach($this->outrosFieldsSelect as $otherCol){
                $arr = $this->getSqlFormSelectColNameAndLabel($otherCol);
                $colName = $arr[0];
                $colLabel = $arr[1];
                $sql.= ','.$arr[3];
            }
            foreach($this->outrosFieldsSelectSqlPuro as $sqlColPuro){
                $sql.= ','.$sqlColPuro;
            }

            if($ordenation==" ORDER BY "){
                $ordenation .= "1 ";
            }

            $sql .= " FROM ";
            if($this->extendsDao){
                $sql .= $this->tabela;
                $sql .= " JOIN ".$this->extendsDao->getTabela()." ON ";
                $sql .= $this->tabela.".".$this->pk."=".$this->extendsDao->getTabela().".".$this->extendsDao->getPk();
            }
            else{
                $sql .= $this->tabela;
            }
            $sql .= $joins_sql;

            if(substr($sql,strlen($sql)-2,strlen($sql))==", "){
                $sql = substr($sql,0,strlen($sql)-2);
            }
            $sql .= " ".$condition;


            if(\Solves\Solves::isNotBlank($this->orderByEspecifico)){
                $sql .= "  ".$this->orderByEspecifico. " ";
            }else{
                $sql .= $ordenation." ";
            }

            if($this->limit){
                $sql .= " LIMIT ".$this->limit;
            }
            $sql .= ";";
            //echo $sql;
            $result = $this->sqlToResultArray($sql);

            return $result;
        }
        else{
            echo $this->msgError;
            return false;
        }
    }


    public function findBySearchAndCondition($search, $condit){
        if($this->tabela && $search && $condit){
            $condition = $this->getSqlSearch($search);
            $condition .= " AND ".$condit;
            //echo $condition;
            return $this->findByCondition($condition);
        }
        else{
            echo $this->msgError;
            return false;
        }
    }

    public function findBySearch($search){
        if($this->tabela && $search){
            $condition = $this->getSqlSearch($search);
            //echo $condition;
            return $this->findByCondition($condition);
        }
        else{
            echo $this->msgError;
            return false;
        }
    }

    public function getSqlSearchByParams(?int $usuarioId, $params=null, ?bool $useAndByColumn=true, ?bool $retornaNuloSeNaoHouverResultado=false){
        $condition = "";
        $entrou = false;
        if(isset($params)){                
            foreach ($params as $key => $search){
                $search = strtoupper($search);
                $result = array();
                $palavras = explode(" ", $search);
                foreach($this->colunas as $coluna){
                    if($coluna->getNome()==$key){
                        $conditionCol =$coluna->getSearchSql($search);
                        if(\Solves\Solves::isNotBlank($conditionCol)){
                            if($entrou){
                                $condition .= ($useAndByColumn?"AND":"OR");
                            }
                            $condition .= " (".$conditionCol.") ";
                            $entrou = true;
                        }
                    }
                }
            }
        }
        if(!$entrou){
            $condition = ($retornaNuloSeNaoHouverResultado ? null : '(1=1)');
        }
        return $condition;
    }
    public function getSqlSearch($search){
        $search = strtoupper($search);
        $palavras = explode(" ", $search);

        $condition = "WHERE (";

        $i=0;
        $v=0;

        if(is_numeric($search)){
            $condition .= " ".$this->tabela.".".$this->pk." = ".$search." ";
        }
        $firstSearchable = false;
        foreach($this->colunas as $coluna){
            $v++;
            if($coluna->isSearchable()){
                $condInit = '';
                if($v>1 && $firstSearchable){
                    $condInit .= " OR ";
                }
                $cond = '';
                $qtdPalavras = count($palavras);
                $qtdAdded = 0;
                for($j=0; $j!=$qtdPalavras; $j++){
                    $table = $coluna->getDao()->getTabela();

                    $added = false;
                    $w = '';
                    if($coluna->getTipo()=='string'){
                        $w .= " UPPER(".$table.".".$coluna->getNome().") LIKE '%".$palavras[$j]."%' ";
                        $added = true;
                        $qtdAdded++;
                    }
                    else if(is_numeric($search) && $coluna->getTipo()=='integer'){
                        $w .= " ".$table.".".$coluna->getNome()." = ".$palavras[$j]." ";
                        $added = true;
                        $qtdAdded++;
                    }
                    if($qtdAdded>1 && $added){
                        $cond .= "AND ".$w;
                    }else{
                        $cond .= $w;
                    }
                    $i++;
                }
                if(strlen($cond)>1){
                    $condition .= $condInit.'('.$cond.')';
                }
                $firstSearchable = true;
            }
        }
        $condition .= ')';
        return $condition;
    }

    public function save(){
        if($this->isMock && isset($this->mock)){
            if($this->getMock()->hasMockDaoMethod( __METHOD__ )){
                return $this->getMock()->executeMockDaoMethod( __METHOD__ );
            }
        }
        if($this->tabela){
            $hasParent = false;
            $sqlParent = '';
            $sql = "INSERT INTO ".$this->tabela."(";

            if($this->extendsDao){
                $hasParent = true;
                $sqlParent = "INSERT INTO ".$this->extendsDao->getTabela()."(";

                /*$colParent;                $vcolParent;*/
            }

            if($hasParent){
                $this->sequencePk = $this->extendsDao->getSequencePk();
            }
            if($this->sequencePk && !isset($this->pkValue)){
                $this->pkValue = $this->getSequencePkValue();
            }

            $entrou = false;
            $colunas = $this->getColunas(false);
            $this->qtdColunas = count($colunas);
            $qtdCols = $this->qtdColunas;

            $col=''; $vcol='';
            $first = true;
            foreach($colunas as $coluna){
                $i = $coluna->getColumnOrder();
                $vColuna = $this->getValorColunaByOrder($i);
                if($first && $this->pkValue){
                    $sql .= $this->pk;
                    $vcol = $this->pkValue;

                    $sql .= ', ';
                    $vcol .= ', ';
                }
                $first = false;
                if($i==0 && $hasParent){
                    $sqlParent .= $this->pk;
                    $vcolParent = $this->pkValue;

                    $sql .= $this->pk;
                    $vcol = $this->pkValue;

                    if($i!=($qtdCols-1)){
                        $sqlParent .= ', ';
                        $vcolParent .= ', ';

                        $sql .= ', ';
                        $vcol .= ', ';
                    }
                }

                if($coluna->getDao()->getTabela()==$this->tabela){
                    $sql .= $coluna->getNome();
                    $vcol .= $this->getValorColunaParaScript($coluna, $vColuna->getValor());

                    //  echo '$i['.$i.']; $qtdCols['.$qtdCols.'];';
                    if($i!=($qtdCols)){
                        $sql .= ', ';
                        $vcol .= ', ';
                    }
                }
                else if($hasParent && $coluna->getDao()->getTabela()==$this->extendsDao->getTabela()){
                    $sqlParent .= $coluna->getNome();
                    $vcolParent .= $this->getValorColunaParaScript($coluna, $vColuna->getValor());
                    if($i!=($qtdCols)){
                        $sqlParent .= ', ';
                        $vcolParent .= ', ';
                    }
                }

                $entrou = true;
            }
            if($entrou){
                if($hasParent){
                    if(substr($sqlParent,strlen($sqlParent)-2,strlen($sqlParent))==", "){
                        $sqlParent = substr($sqlParent,0,strlen($sqlParent)-2);
                    }
                    if(substr($vcolParent,strlen($vcolParent)-2,strlen($vcolParent))==", "){
                        $vcolParent = substr($vcolParent,0,strlen($vcolParent)-2);
                    }

                    $sqlParent .= ")";
                    $sqlParent .= "VALUES (";
                    $sqlParent .= $vcolParent;
                    $sqlParent .= ");";
                }

                if(substr($sql,strlen($sql)-2,strlen($sql))==", "){
                    $sql = substr($sql,0,strlen($sql)-2);
                }
                if(substr($vcol,strlen($vcol)-2,strlen($vcol))==", "){
                    $vcol = substr($vcol,0,strlen($vcol)-2);
                }

                $sql .= ")";
                $sql .= "VALUES (";
                $sql .= $vcol;
                $sql .= ");";
            }
            else{
                $sql = "";
            }

            //      echo '<br>SQL: '.$sql.'<br><br>PARENT: '.$sqlParent;

            if($hasParent){
                $result = $this->executeQuery($sqlParent,'insert');
            }

            $result = $this->executeQuery($sql,'insert');

            return $result;
        }
        else{
            //  echo $this->msgError;
            return false;
        }
    }
    public function getValorColunaParaScript($coluna, $value, $isSearch=false){
        $vcol='';
        $tipo = $coluna->getTipo();
        $isNotNull = $coluna->isObrigatorio();
        $hasValue = (\Solves\Solves::isNotBlank($value));

        if($tipo=="string" || $tipo=="date" || $tipo=="text" || $tipo=="timestamp" || $tipo=="time"){
            if($hasValue){
                if($this->isSystemDbTypeMySql()){
                    $value = ($this->isMock ? $value : mysqli_real_escape_string($this->getBdConnection(), $value));
                }else{
                    $value = addslashes($value);
                }
                if($isSearch){
                    $vcol .= "'%".$value."%'";
                }else{
                    $vcol .= "'".$value."'";
                }
            }else if($isNotNull){
                $vcol .= "''";
            }else{
                $vcol .= "null";
            }
        }else if($tipo=="boolean"){
            $vcol .= (\Solves\Solves::checkBoolean($value) ? 'true' : (($this->isSystemDbTypeMySql())?'0':'false'));
        }
        else if($tipo=="array_int"){
            if($hasValue){
                $vcol .= 'ARRAY['.$value.']';
            }else{
                $vcol .= "null";
            }
        }
        else if($tipo=="double" || $tipo=="money" || $tipo=="percentual"){
            if($hasValue){
                $vcol .= $value;
            }else if($isNotNull){
                $vcol .= "0.0";
            }else{
                $vcol .= "null";
            }
        }
        else if($hasValue){
            $vcol .= $value;
        }
        else{
            $vcol .= "null";
        }
        if(strlen($vcol)==0){
            $vcol .= "null";
        }
        return $vcol;
    }
    public function getSqlUpdate($obj, $id){
        if($this->tabela && isset($obj) && $id){
            $hasParent = false;
            $sql = "UPDATE ".$this->tabela." SET ";
            $colWithValue = "";

            if($this->extendsDao){
                $hasParent = true;
                $sqlParent = "UPDATE ".$this->extendsDao->getTabela()." SET ";

                $colWithValueParent = "";
            }

            $colunas = $this->getColunasAlteradas($obj, $id);
            $qtdCols = (isset($colunas) ? count($colunas) : 0);
            if(0==$qtdCols){
                return null;
            }

            $col=''; $vcol='';
            $hasSetValue = false;
            $hasSetValueParent = false;
            foreach($colunas as $coluna){
                $i = $coluna->getColumnOrder();
                $valorColuna = $this->getValorColunaByOrder($i);
                if(!isset($valorColuna)){
                    continue;
                }
                $tipo = $coluna->getTipo();
                $valor = $this->getValorColunaParaScript($coluna, $valorColuna->getValor());
                if($coluna->getDao()->getTabela()==$this->tabela){
                    $colWithValue .= ($hasSetValue?', ':'').$coluna->getNome().'='.$valor;
                    $hasSetValue = true;
                }
                else if($hasParent && $coluna->getDao()->getTabela()==$this->extendsDao->getTabela()){
                    $colWithValueParent .= ($hasSetValueParent?', ':''). $coluna->getNome().'='.$valor;
                    $hasSetValueParent = true;
                }
            }
            if($hasParent){
                $sqlParent .= $colWithValueParent;
                $sqlParent .= " WHERE ".$this->pk."= ".$id.";";

                $sql = $sqlParent. '  ' .$sql;
            }

            $sql .= $colWithValue;
            $sql .= " WHERE ".$this->pk."= ".$id.";";


            return $sql;
        }
        else{
            //  echo $this->msgError;
            return null;
        }
    }
    public function update($obj){
        $id = (isset($obj) ? $obj->getId() : null);
        if($this->isMock && isset($this->mock)){
            if($this->getMock()->hasMockDaoMethod( __METHOD__ )){
                return $this->getMock()->executeMockDaoMethod( __METHOD__, $id );
            }
        }
        if($this->tabela && $id){
            $sql = $this->getSqlUpdate($obj, $id);
            $result = false;
            if(isset($sql)){
                $result = $this->executeQuery($sql,'update');
            }
            return $result;
        }
        else{
            //  echo $this->msgError;
            return false;
        }
    }
    public function marcarRemovido($id, $usuarioId, $ip, $data, $moduloId, $rotinaId){
        if($this->marcaDelete($id, $usuarioId, $ip, $data, $moduloId, $rotinaId)){
            //Exclui o registro do banco
            $sql = "UPDATE ".$this->tabela." SET removed = true, removed_at=NOW() WHERE ".$this->pk."=".$id.";";
            $result = $this->executeQuery($sql,'update');
            return $result;
        }else{
            // echo $this->msgError;
            return false;
        }
    }

    public function delete($id){
        if($this->isMock && isset($this->mock)){
            if($this->getMock()->hasMockDaoMethod( __METHOD__ )){
                return $this->getMock()->executeMockDaoMethod( __METHOD__, $id );
            }
        }
        //Exclui o registro do banco
        $sql = "DELETE FROM ".$this->tabela." WHERE ".$this->pk."=".$id.";";
        $result = $this->executeQuery($sql,'delete');
        return $result;
    }
    public function deleteComMarcacao($id, $usuarioId, $ip, $data, $moduloId, $rotinaId){
        if($this->marcaDelete($id, $usuarioId, $ip, $data, $moduloId, $rotinaId)){
            //Exclui o registro do banco
            return $this->delete($id);
        }else{
            // echo $this->msgError;
            return false;
        }
    }
    public function mountWherePeriod($campoData, $dataInicio, $dataFim){
        $where = "";
        $campoData.='::date';
        if(\Solves\Solves::isNotBlank($dataInicio) && \Solves\Solves::isNotBlank($dataFim)){
            $where = " ".$campoData." BETWEEN '".$dataInicio."' AND '".$dataFim. "' ";
        }else if(\Solves\Solves::isNotBlank($dataInicio)){
            $where = " ".$campoData." >= '".$dataInicio. "' ";
        }else if(\Solves\Solves::isNotBlank($dataFim)){
            $where = " ".$campoData." <= '".$dataFim. "' ";
        }
        return $where;
    }
    public function mountInValues($array){
        $count = count($array);
        $in = '';
        for ($i = 0; $i !=$count; $i++) {
            if($i!=0){
                $in.=',';
            }
            $in.=$array[$i];
        }
        return $in;
    }
    public function mountInStringValues($array){
        $count = count($array);
        $in = '';
        for ($i = 0; $i !=$count; $i++) {
            if($i!=0){
                $in.=',';
            }
            $in.= "'".$array[$i]."'";
        }
        return $in;
    }

    private function openTransaction(){
        if($this->connection!=null){
            $this->connection->openTransaction();
        }
    }
    private function rollbackTransaction(){
        if($this->connection!=null){
            $this->connection->rollbackTransaction();
        }
    }
    private function commitTransaction(){
        if($this->connection!=null){
            return $this->connection->commitTransaction();
        }
        return false;
    }
    public function executeQuery($sql, $op){
        $this->msgError .= '['.$op.']';
        if($sql && $sql!=""){
            $result = false;
            //echo "<div style=\"display:none\"><br><br><br>".$this->tabela." | ".$op." | ".$sql.". </div>";
            if(!isset($this->connection) || null==$this->getBdConnection()){
                $erro = 'Não encontrada conexão de BD ativa.';
                $this->msgError .= $erro;
                throw new \Exception($erro);
            }
            try{
                $this->openTransaction();
                if($this->isSystemDbTypeMySql()){
                    $result = ($this->isMock ? $this->getBdConnection()->query($sql, $op) : $this->getBdConnection()->query($sql));
                    if($result){
                        if($op=='insert'){
                            $id = $this->getBdConnection()->insert_id;
                            $result = $id;
                        }
                    }else{
                        $this->msgError .= $this->getBdConnection()->error;
                        $erro = $this->msgError.(\Solves\Solves::isDebugMode() ?  "<div style=\"display:none\"><br><br><br>".$this->tabela." | ".$sql.".</div>" : "");
                        $this->rollbackTransaction();
                        throw new \Exception($erro);
                    }
                }else if($this->isSystemDbTypePostgresql()){
                    $result = pg_query($this->getBdConnection(), $sql);
                }

                if(!$this->commitTransaction()){
                    $this->rollbackTransaction();
                    $result = false;
                }
            }catch (\Exception $e) {
                $this->msgError .= $this->getBdConnection()->error;
                $erro = $this->msgError.(\Solves\Solves::isDebugMode() ?  "<div style=\"display:none\"><br><br><br>".$this->tabela." | ".$sql.".</div>" : "");
                $this->rollbackTransaction();
                throw new \Exception($erro);
            }
            return $result;
        }
        else{
            //  echo 'Erro ao executar Operação:'.$this->msgError.' ['.$sql.']';
            return false;
        }
    }

    public function sqlToResultArray($sql){
        $this->msgError .= '[consulta]';
        if($sql && $sql!=""){
            //echo "<div style=\"display:none\"><br><br><br>".$this->tabela." | ".$sql.". </div>";
            $resultado = array();
            if($this->isSystemDbTypeMySql()){
                $result = $this->getBdConnection()->query($sql, MYSQLI_USE_RESULT) or die($this->msgError.
                    (false ? '' : "<div style=\"display:none\"><br><br><br>".$this->tabela." | ".$sql.". ".$this->getBdConnection()->error."</div>"));
                if($this->isMock){
                    $resultado = $result;
                    $dados = null;
                    $result = null;
                }else if($result){
                    while($dados=$result->fetch_array(MYSQLI_ASSOC)){ //} or die('erro no fetch: '.$this->getBdConnection()->error)){
                        $resultado[] = $dados;
                    }
                    $dados = null;
                    @$result->close();
                    /* free result set */
                    @mysqli_free_result($result);
                    $result = null;
                }else{
                    die($this->msgError.' | '.$this->getBdConnection()->error); 
                }
            }else if($this->isSystemDbTypePostgresql()){
                $result = pg_query($this->getBdConnection(), $sql) or die($this->msgError.
                    (\Solves\Solves::isProdMode() ? '' : "<div style=\"display:none\"><br><br><br>".$this->tabela." | ".$sql.". </div>"));

                while($dados=pg_fetch_array($result)){
                    $resultado[] = $dados;
                }
            }
            return $resultado;
        }
        else{
            //  echo 'Erro ao executar CONSULTA:'.$this->msgError.' ['.$sql.']';
            return false;
        }
    }

    private function getSqlFormSelectColNameAndLabel($coluna, $includeJoins=true) {
        $joins_sql='';
        $nomeColuna = $coluna->getNome();
        $nomeColunaLabel = $nomeColuna . '_label';
        $colName = $coluna->getDao()->getTabela() . "." . $nomeColuna;
        if ($coluna->isTipoBoolean()) {
            $colLabel = 'formatBoolean(' . $colName . ')  as ' . $nomeColunaLabel;
        } else if ($coluna->isTipoDate()) {
            $colLabel = 'formatDate(' . $colName . ')  as ' . $nomeColunaLabel;
        } else if ($coluna->isTipoTimestamp()) {
            $colLabel = 'formatTimestamp(' . $colName . ')  as ' . $nomeColunaLabel;
        } else if ($coluna->isTipoTime()) {
            $colLabel = '' . $colName . '  as ' . $nomeColunaLabel;
        } else if ($coluna->isTipoDouble()) {
            $colLabel = $colName . " as " . $nomeColunaLabel;
        } else if ($coluna->isTipoMoney()) {
            $colLabel = 'formatMoney(' . $colName . ')  as ' . $nomeColunaLabel;
        }  else if ($coluna->isTipoPercentual()) {
            $colLabel = '(CASE WHEN '.$colName.' IS NOT NULL THEN replace(round(' . $colName . '::numeric,2)::text, \'.\', \',\')||\' %\'  ELSE \'\' END)  as ' . $nomeColunaLabel;
        } else {
            $join = $coluna->getJoin();
            if (isset($join) && $includeJoins) {
                $origemJoinColName = $colName;
                $joinLabels = '';
                while (isset($join)) {
                    $daoTarget = $join->getDaoTarget();
                    $daoTargetTabela = $daoTarget->getTabela();
                    $daoTargetAlias = $join->getAlias();
                    $hasAlias = (isset($daoTargetAlias));
                    if (!$hasAlias) {
                        $daoTargetAlias = $daoTarget->getTabela();
                    }
                    $daoTargetPk = $daoTarget->getPk();
                    $var_colunaLabel = $daoTarget->getColunaLabel();

                    $daoTargetColLabel = $daoTarget->getColunaLabelName();
                    $joins_sql .= ' ' . $join->getType() . ' JOIN ' . $daoTargetTabela . ' as ' . $daoTargetAlias . ' ON ' . $daoTargetAlias . '.' . $daoTargetPk . ' = ' . $origemJoinColName;

                    $origemJoinColName = $daoTargetAlias . '.' . $daoTargetColLabel;
                    $join = (isset($var_colunaLabel) ? $var_colunaLabel->getJoin() : null);

                    $joinLabels .= (\Solves\Solves::isNotBlank($joinLabels) ? ', ' : ''). $origemJoinColName . ' as ' . $nomeColunaLabel;
                    $daoTargetColsLabel = $daoTarget->getColsLabelOrder();
                    if(isset($daoTargetColsLabel) && count($daoTargetColsLabel)>0){
                        //other join labels
                        foreach($daoTargetColsLabel as $targetColLabel){
                            $origemJoinColName = $daoTargetAlias . '.' . $targetColLabel->getNome();
                            $nomeColunaOtherJoinLabel = $nomeColuna . '_'.$targetColLabel->getNome();
                            $joinLabels .= (\Solves\Solves::isNotBlank($joinLabels) ? ', ' : ''). $origemJoinColName . ' as ' . $nomeColunaOtherJoinLabel;

                            $subjoinLabel = $targetColLabel->getJoin();
                            if(isset($subjoinLabel)){
                                $daoTargetSubJoin = $subjoinLabel->getDaoTarget();
                                $daoTargetSubJoinTabela = $daoTargetSubJoin->getTabela();
                                $daoTargetSubJoinPk = $daoTargetSubJoin->getPk();
                                $daoTargetSubJoinAlias = $subjoinLabel->getAlias();
                                $daoTargetSubJoinColLabel = $daoTarget->getColunaLabelName();
                                $daoTargetColsLabelSubJoin = $daoTargetSubJoin->getColsLabelOrder();
                                if (!isset($daoTargetSubJoinAlias)) {
                                    $daoTargetSubJoinAlias = $daoTargetSubJoin->getTabela();
                                }
                                //subjoin target alias deve ser concatenado com o superior
                                $daoTargetSubJoinAlias = $daoTargetAlias.'_'.$daoTargetSubJoinAlias;
                                $joins_sql .= ' ' . $subjoinLabel->getType() . ' JOIN ' . $daoTargetSubJoinTabela . ' as ' . $daoTargetSubJoinAlias . ' ON ' . $daoTargetSubJoinAlias . '.' . $daoTargetSubJoinPk . ' = ' . $origemJoinColName;

                                $origemSubJoinColName = $daoTargetSubJoinAlias . '.' . $daoTargetSubJoinColLabel;
                                $nomeColunaOtherSubJoinLabel = $nomeColunaOtherJoinLabel. '_'.$daoTargetSubJoinColLabel;
                                $joinLabels .= (\Solves\Solves::isNotBlank($joinLabels) ? ', ' : ''). $origemSubJoinColName . ' as ' . $nomeColunaOtherSubJoinLabel;

                                if(isset($daoTargetColsLabelSubJoin) && count($daoTargetColsLabelSubJoin)>0){
                                    foreach($daoTargetColsLabelSubJoin as $targetColLabelSubJoin){
                                        $origemSubJoinColName = $daoTargetSubJoinAlias . '.' . $targetColLabelSubJoin->getNome();
                                        $nomeColunaOtherSubJoinLabel = $nomeColunaOtherJoinLabel.'_'.$targetColLabelSubJoin->getNome();
                                        $joinLabels .= (\Solves\Solves::isNotBlank($joinLabels) ? ', ' : ''). $origemSubJoinColName . ' as ' . $nomeColunaOtherSubJoinLabel;
                                    }
                                }
                            }
                        }
                    }
                }
                $colLabel = $joinLabels;
            } else {
                $colLabel = $colName . " as " . $nomeColunaLabel;
            }
        }
        $arr = array();
        $arr[0] = $colName;
        $arr[1] = $colLabel;
        $arr[2] = $joins_sql;
        $arr[3] = $colName . ', ' . $colLabel;
        return $arr;
    }

    public function confereParametro($paramValue, $tipo, $maxSize){
        return !strstr($paramValue,"'") && strlen($paramValue)<$maxSize;
    }
    public function removeAtributosSensiveisDeArray($arrItem, $arrIdsColunas){
        foreach($arrIdsColunas as $idColuna){
            unset($arrItem[$this->getColunaNome($idColuna)]);
            unset($arrItem[$this->getColunaNome($idColuna)."_label"]);
        }
        return $arrItem;
    }
}
?>