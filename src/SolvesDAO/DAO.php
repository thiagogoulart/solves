<?php
/*
Autor:  Thiago Goulart.
Data de criação: 01/01/2010.
 alteração: 28/03/2014.
 alteração: 19/07/2019.
Última alteração: 18/09/2019.
*/

namespace SolvesDAO;

class DAO {
	
	private $connection;
	
	private $tabela;
	private $pk;
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


	public static $NULL_TIMESTAMP = '0000-00-00 00:00:00';
	
	public function __construct(){
		$this->colunas = array();
		$this->valoresColunas = array();
        $this->outrosFieldsSelect = array();
        $this->outrosFieldsSelectSqlPuro= array();
		$this->colsLabelOrder = array();
		
		$this->charset = "UTF-8";		
		
		$this->msgError = '<br>Solicita&ccedil;&atilde;o n&atilde;o atendida. n&atilde;o foi poss&iacute;vel executar consulta.';
	}
	
	
/*START getters e setters*/	
	public function getConnection() : SolvesDAOConnection{
		return $this->connection;
	}
	public function setConnection(SolvesDAOConnection $p){
		$this->connection = $p;
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
        return "(SELECT COUNT(*) FROM ".$this->getTabela()." ".$sqlCondition.") as num_rows";
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
	public function getColuna($order){
		return $this->colunas[$order];
	}
	public function getColunas(){
		return $this->colunas;
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
		return $this->tabela.'.'.$coluna->getNome();
	}
	public function getColunaByOrder($columnOrder){
		return $this->getColuna($columnOrder);
	}
	
	public function getValorColunaByOrder($columnOrder){
		return $this->valoresColunas[$columnOrder];
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
		return $this->connection->getBdConnection();
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
					$ordenation .= $colName.' '.$coluna->getOrderByType();
					if($i!=$qtd){
						$ordenation .= ", ";
					}
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
			
	//	    echo $sql;
	
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
			$qtd = count($this->getColunas());
			$i=0;

			$sql .= $this->tabela.".".$this->pk;
			
			if(count($this->getColunas())>0){
				$sql .= ", ";
			}
			
			$ordenation = " ORDER BY ";
			
			foreach($this->getColunas() as $coluna){
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
					$ordenation .= $colName.' '.$coluna->getOrderByType();
					if($i!=$qtd){
						$ordenation .= ", ";
					}
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
			
			$sql .= " WHERE ".$this->tabela.".".$this->pk."=".$id." ".@$ordernation." "; 
			
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
			$qtd = count($this->getColunas());
			$i=0;
			
			$sql .= " ".$this->tabela.".".$this->pk;
			
			if(count($this->getColunas())>0){
				$sql .= ", ";
			}
			
			$ordenation = " ORDER BY ";
			$cols = $this->getColunas();
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
					$ordenation .= $colName.' '.$coluna->getOrderByType() .", ";
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
			
			if(substr($sql,strlen($sql)-2,strlen($sql))==", "){
				$sql = substr($sql,0,strlen($sql)-2);
			}
			if(substr($ordenation,strlen($ordenation)-2,strlen($ordenation))==", "){
				$ordenation = substr($ordenation,0,strlen($ordenation)-2);
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
	
	public function getSqlSearchByParams($usuarioId, $params, $useAndByColumn=true){		
		$condition = "";
		$coluna = new DAOColuna();
		$entrou = false;
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
		if(!$entrou){
			$condition = '(1=1)';
		}
		return $condition;
	}
	public function getSqlSearch($search){			
			$search = strtoupper($search);
			$result = array();
			$palavras = explode(" ", $search);
			
			$condition = "WHERE (";
			
			$coluna = new DAOColuna();
			$i=0;
			$v=0;
			$entrou = 0;
			
			if(is_numeric($search)){
				$condition .= " ".$this->tabela.".".$this->pk." = ".$search." ";	
				$entrou = 1;
			}
			$firstSearchable = false;
			$addedOr = false;
			foreach($this->colunas as $coluna){
				$v++;
				if($coluna->isSearchable()){
					$condInit = '';
					if($v>1 && $firstSearchable){
						$addedOr = true;
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
		if($this->tabela){	
			$hasParent = false;	
			$sqlParent = '';		
			$sql = "INSERT INTO ".$this->tabela."(";
												  
			if($this->extendsDao){
				$hasParent = true;
				$sqlParent = "INSERT INTO ".$this->extendsDao->getTabela()."(";
																	   
				$colParent;
				$vcolParent;
			}
			
			if($hasParent){
				$this->sequencePk = $this->extendsDao->getSequencePk();
			}
			if($this->sequencePk && !isset($this->pkValue)){
				$this->pkValue = $this->getSequencePkValue();
			}
											
			$entrou = false;
			$colunas = $this->getColunas();
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
					
			//	echo '$i['.$i.']; $qtdCols['.$qtdCols.'];';
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
			
		//   	echo '<br>SQL: '.$sql.'<br><br>PARENT: '.$sqlParent;
			
			if($hasParent){
				$result = $this->executeQuery($sqlParent,'insert');	
			}
			
			$result = $this->executeQuery($sql,'insert');
			
			return $result;
		}
		else{
		//	echo $this->msgError;
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
                 	if(\SolvesDAO\SolvesDAO::isSystemDbTypeMySql()){
                 		$value = mysqli_real_escape_string($this->getBdConnection(), $value);
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
                $vcol .= (\Solves\Solves::checkBoolean($value) ? 'true' : ((\SolvesDAO\SolvesDAO::isSystemDbTypeMySql())?'0':'false'));
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
	public function getSqlUpdate($id){
		if($this->tabela && $id){
			$hasParent = false;			
			$sql = "UPDATE ".$this->tabela." SET ";
			$colWithValue = "";
			
			if($this->extendsDao){
				$hasParent = true;
				$sqlParent = "UPDATE ".$this->extendsDao->getTabela()." SET ";
																	   
				$colWithValueParent = "";
			}

			$colunas = $this->getColunas();
			$this->qtdColunas = count($colunas);

			$col=''; $vcol='';		
			foreach($colunas as $coluna){
				$i = $coluna->getColumnOrder();
				$valorColuna = $this->getValorColunaByOrder($i);
				$tipo = $coluna->getTipo();
				$entrouParent = false;
				$entrou = false;
				
				$valor = $this->getValorColunaParaScript($coluna, $valorColuna->getValor());
				if($coluna->getDao()->getTabela()==$this->tabela){
                                    $colWithValue .= $coluna->getNome().'='.$valor;
                                    $entrou = true;
                                }
                                else if($hasParent && $coluna->getDao()->getTabela()==$this->extendsDao->getTabela()){
                                    $colWithValueParent .= $coluna->getNome().'='.$valor;
                                    $entrouParent = true;
                                }
				if($i!=($this->qtdColunas)){
					if($entrou){
						$colWithValue .= ', ';
					}
					if($hasParent && $entrouParent){
						$colWithValueParent .= ', ';
					}
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
		//	echo $this->msgError;
			return null;
		}
	}
	public function update($id){
		if($this->tabela && $id){
			$sql = $this->getSqlUpdate($id);
			$result = $this->executeQuery($sql,'update');		
			return $result;
		}
		else{
		//	echo $this->msgError;
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
	public function delete($id, $usuarioId, $ip, $data, $moduloId, $rotinaId){
            if($this->marcaDelete($id, $usuarioId, $ip, $data, $moduloId, $rotinaId)){                    
                //Exclui o registro do banco
                $sql = "DELETE FROM ".$this->tabela." WHERE ".$this->pk."=".$id.";"; 
                $result = $this->executeQuery($sql,'delete');
                return $result;                    
            }else{
            // echo $this->msgError;
                return false;
            }
	}
	private function marcaDelete($id, $usuarioId, $ip, $data, $moduloId, $rotinaId){
		if($this->tabela && $this->pk && 
                        \Solves\Solves::isNotBlank($id) && 
                        \Solves\Solves::isNotBlank($usuarioId) && 
                        \Solves\Solves::isNotBlank($ip) && 
                        \Solves\Solves::isNotBlank($data) && 
                        \Solves\Solves::isNotBlank($moduloId) && 
                        \Solves\Solves::isNotBlank($rotinaId)){
                    
                    $rotina = new ErpRotina($this->connection);
                    $rotina = $rotina->findById($rotinaId);
                    if(isset($rotina)){
                        //Registra INstância de Rotina
                        $rotinaInstancia = new ErpRotinaInstancia($this->connection);
                        $rotinaInstancia = $rotinaInstancia->findOneByRotinaIdAndPkValue($empresaId, $rotinaId, $id);
                        if(isset($rotinaInstancia)){
                            $rotinaInstancia->setUpdatedAt($data);
                            $rotinaInstancia->setPkValue($id);
                            $rotinaInstancia->setFinalizada(true);
                            $rotinaInstancia->update();

                            //Registra Auditoria da ação de EXCLUSÃO
                            $audit = new Auditoria($this->connection);
                            $audit->create($usuarioId, 
                                    $ip, $data,
                                    'Registro excluído de '.$rotina->getLabel().' em '. $data.'.',
                                    TipoAuditoria::$TIPO_EXCLUSAO, 
                                    $rotinaInstancia->getRotinaInstanciaId());
                        }

                        return true;
                    }else{
                    // echo $this->msgError;
                        return false;
                    }
		}
		else{
                // echo $this->msgError;
                    return false;
		}
                return false;
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
		//	echo "<div style=\"display:none\"><br><br><br>".$this->tabela." | ".$op." | ".$sql.". </div>";
			try{						
				$this->openTransaction();
				if(\SolvesDAO\SolvesDAO::isSystemDbTypeMySql()){ 
					$result = $this->getBdConnection()->query($sql);
					if($op=='insert'){
						$id = $this->getBdConnection()->insert_id;
						$result = $id;
					}else{
						$result = true;
					}
				}else if(\SolvesDAO\SolvesDAO::isSystemDbTypePostgresql()){
					$result = pg_query($this->getBdConnection(), $sql);
				}

				if(!$this->commitTransaction()){
					$this->rollbackTransaction();
					$result = false;
				}
			}catch (Exception $e) {
				$result = false;
				$this->rollbackTransaction();
				$erro = $this->msgError.(true ?  "<div style=\"display:none\"><br><br><br>".$this->tabela." | ".$sql.". ".$this->getBdConnection()->error."<div>".$e->getMessage()."</div></div>". $this->getBdConnection()->errno : "");
				throw new Exception($erro);
			}
			return $result;
		}
		else{
		//	echo 'Erro ao executar Operação:'.$this->msgError.' ['.$sql.']';
			return false;
		}
	}
	
	public function sqlToResultArray($sql){
		$this->msgError .= '[consulta]';
		if($sql && $sql!=""){
		//	echo "<div style=\"display:none\"><br><br><br>".$this->tabela." | ".$sql.". </div>";
			$resultado = array();
			if(\SolvesDAO\SolvesDAO::isSystemDbTypeMySql()){ 
				$result = $this->getBdConnection()->query($sql, MYSQLI_USE_RESULT) or die($this->msgError.
                                        (false ? '' : "<div style=\"display:none\"><br><br><br>".$this->tabela." | ".$sql.". ".$this->getBdConnection()->error."</div>"));
				while($dados=$result->fetch_array(MYSQLI_ASSOC)){ 
					$resultado[] = $dados;	
				}
				$dados = null;
				@$result->close();
				/* free result set */
   				@mysqli_free_result($result);
				$result = null;
			}else if(\SolvesDAO\SolvesDAO::isSystemDbTypePostgresql()){
				$result = pg_query($this->getBdConnection(), $sql) or die($this->msgError.
                                        (\Solves\Solves::isProdMode() ? '' : "<div style=\"display:none\"><br><br><br>".$this->tabela." | ".$sql.". </div>"));
					
				while($dados=pg_fetch_array($result)){
					$resultado[] = $dados;	
				}
			}			
			return $resultado;
		}
		else{
		//	echo 'Erro ao executar CONSULTA:'.$this->msgError.' ['.$sql.']';
			return false;
		}
	}

    private function getSqlFormSelectColNameAndLabel($coluna, $includeJoins=true) {
        $joins_sql='';
        $nomeColuna = $coluna->getNome();
        $nomeColunaLabel = $nomeColuna . '_label';
        $colName = $coluna->getDao()->getTabela() . "." . $nomeColuna;
        if ($coluna->getTipo() == "boolean") {
            $colLabel = 'formatBoolean(' . $colName . ')  as ' . $nomeColunaLabel;
        } else if ($coluna->getTipo() == "date") {
            $colLabel = 'formatDate(' . $colName . ')  as ' . $nomeColunaLabel;
        } else if ($coluna->getTipo() == "timestamp") {
            $colLabel = 'formatTimestamp(' . $colName . ')  as ' . $nomeColunaLabel;
        } else if ($coluna->getTipo() == "time") {
            $colLabel = '' . $colName . '  as ' . $nomeColunaLabel;
        } else if ($coluna->getTipo() == "double") {
            $colLabel = $colName . " as " . $nomeColunaLabel;
        } else if ($coluna->getTipo() == "money") {
            $colLabel = 'formatMoney(' . $colName . ')  as ' . $nomeColunaLabel;
        }  else if ($coluna->getTipo() == "percentual") {
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