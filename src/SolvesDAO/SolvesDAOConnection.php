<?php
/*
Autor:  Thiago Goulart.
Data de criação: 18/09/2019
*/

namespace SolvesDAO;

class SolvesDAOConnection {
	private $BD_CONNECTION;

	private $commitManual=false;
	private $transactionOpened=false;

	private $isApp=false;
	private $mock=false;

    protected $connectionName;
    protected $bdHost;
    protected $bdPort;
    protected $bdUrl;
    protected $bdUser;
    protected $bdPassword;
    protected $bdDatabase;
    protected $isMysql=false;
    protected $isPostgres=false;


	/*Colunas que não devem estar presentes no retorno da consulta */
	private $exibeColunasSensiveis = true;

	public function __construct(?\SolvesDAO\SolvesDAOConfigConnection $configConnection, ?bool $mock = false) {
	    $this->BD_CONNECTION = null;
	    $this->mock = $mock;
	    if($this->mock) {
	    	$this->BD_CONNECTION = new SolvesDAOConnectionMock();
	    }else{ 

	        $this->connectionName = $configConnection->getBdConnectionName();
	        $this->isPostgres = $configConnection->isSystemDbTypePostgresql();
        	$this->isMysql = $configConnection->isSystemDbTypeMySql();
	        $this->bdHost = $configConnection->getBdHost();
	        $this->bdPort = $configConnection->getBdPort();
	        $this->bdUrl = $configConnection->getBdUrl();
	        $this->bdUser = $configConnection->getBdUser();
	        $this->bdPassword = $configConnection->getBdPassword();
	        $this->bdDatabase = $configConnection->getBdDatabase();

		    $this->BD_CONNECTION = $this->connectDb();
		    if(SolvesDAO::isDebug()){
		    	echo '[$conName:'.$this->connectionName.'], [$bdHost:'.$this->bdHost.'], [$bdPort:'.$this->bdPort.'], [$bdUrl:'.$this->bdUrl.'], [$bdUser:'.$this->bdUser.'], [$bdDatabase:'.$this->bdDatabase.']';
		    	var_dump($this->BD_CONNECTION);
		    }
	    }
	}
    public function isSystemDbTypeMySql(): bool{return $this->isMysql;}
    public function isSystemDbTypePostgresql(): bool{return $this->isPostgres;}
	private function connectDb() {
	    $CONNECTION = null;
	    if ($this->isSystemDbTypeMySql()) {
	        $CONNECTION = new \Mysqli($this->bdHost, $this->bdUser, $this->bdPassword, $this->bdDatabase);
	        /* check connection */
	        if (mysqli_connect_errno()) {
	            printf("Erro na conexão: %s\n", mysqli_connect_error());
	            $CONNECTION = null;
	            return $CONNECTION;
	        }

	        /* change character set to utf8 */
	        if (!$CONNECTION->set_charset("utf8")) {
	            printf("Erro ao alterar charset para UTF-8: %s\n", $CONNECTION->error);
	            $CONNECTION = null;
	            return $CONNECTION;
	        } 

	    } else if ($this->isSystemDbTypePostgresql()) {
	        $CONNECTION = pg_connect('host=' .$this->bdHost. ' port=' .$this->bdPort. ' dbname=' . $this->bdDatabase . ' user=' .$this->bdUser . ' password=' . $this->bdPassword) or die("Erro na conexão com o Database PostgreSQL --> " . pg_last_error($CONNECTION));
	        pg_set_client_encoding($CONNECTION, 'utf8');
	    }
	    return $CONNECTION;
	}
	public function close() {
	    if (isset($this->BD_CONNECTION)) {
	        if ($this->isSystemDbTypeMySql()) {
	            @$this->BD_CONNECTION->close();
	            @mysqli_close($this->BD_CONNECTION);
	        } else if ($this->isSystemDbTypePostgresql()) {
	            @pg_close($this->BD_CONNECTION);
	        }
	        $this->BD_CONNECTION = null;
	    }
	    return null;
	}
	public function getBdConnection(){
		return $this->BD_CONNECTION;
	}
	public function isCommitManual(){
		return $this->commitManual;
	}
	public function isTransactionOpened(){
		return $this->transactionOpened;
	}
	public function isApp(){
		return $this->isApp;
	}

  	public function setCommitManual(){
  		$this->commitManual = true;
  		if($this->isSystemDbTypeMySql()){ 
			$this->BD_CONNECTION->autocommit(FALSE);
  		}
  	}
	public function isExibeColunasSensiveis(){
		return $this->exibeColunasSensiveis;
	}
  	public function setNaoExibeColunasSensiveis(){
  		$this->exibeColunasSensiveis = false;
  	}
  	public function setIsApp(){
  		$this->isApp = true;
  	}

	public function openTransaction(){
		if($this->isSystemDbTypeMySql()){ 
			if(!$this->commitManual || !$this->transactionOpened){
				$this->BD_CONNECTION->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
				$this->transactionOpened = true;		
			}
		}
	}
	public function rollbackTransaction(){
		if($this->isSystemDbTypeMySql()){ 
			$this->BD_CONNECTION->rollback();
			$this->transactionOpened=false;	
		}	
	}
	public function commitTransaction($doManualCommit=false) : bool{
		$result = true;
		if($this->isSystemDbTypeMySql()){ 
			if(!$this->commitManual || $doManualCommit){
				$result = $this->BD_CONNECTION->commit();	
				$this->transactionOpened=false;
			}
		}
		return $result;	
	}
	public function commit() : bool{
		return $this->commitTransaction(true);
	}

}
class SolvesDAOConnectionMock{
	const TYPE_INSERT = 'insert';
	const TYPE_UPDATE = 'update';
	const TYPE_DELETE = 'delete';

	public $error = '';
	public $insert_id;

	public $transactionStarted = false;
	public $commited = false;
	public $rolledback = false;
	public $closed = false;
	

	public function query(string $sql, $type=null){
		$isConsulta = (isset($type) && MYSQLI_USE_RESULT==$type);
		if($isConsulta){
			return true;
		}else if(isset($type)){
			if(self::TYPE_INSERT==$type){
				return true;
			}else if(self::TYPE_UPDATE==$type){
				return true;
			}else if(self::TYPE_DELETE==$type){
				return true;
			}
		}
		return null;
	}
	public function autocommit(bool $enable){
		$this->autocommit = $enable;
	}
	public function commit(): bool{
		$this->commited=true;
		return $this->commited;
	}
	public function rollback(){
		$this->rolledback=true;
	}
	public function close(){
		$this->closed=true;
	}
	public function begin_transaction($type=null){
		$this->transactionStarted=true;
	}

}
?>