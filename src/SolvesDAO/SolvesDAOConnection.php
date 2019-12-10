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


	/*Colunas que não devem estar presentes no retorno da consulta */
	private $exibeColunasSensiveis = true;

	public function __construct() {
	    $this->BD_CONNECTION = null;
		$bd_host = SolvesDAO::getBdHost();
		$bd_port = SolvesDAO::getBdPort();
		$bd_url = SolvesDAO::getBdUrl();
		$bd_user = SolvesDAO::getBdUser();
		$bd_passwd = SolvesDAO::getBdPassword();
		$bd_database = SolvesDAO::getBdDatabase();
	    $this->BD_CONNECTION = self::connectDb($bd_host, $bd_port, $bd_url, $bd_user, $bd_passwd, $bd_database);
	    if(SolvesDAO::isDebug()){
	    	echo '[$bd_host:'.$bd_host.'], [$bd_port:'.$bd_port.'], [$bd_url:'.$bd_url.'], [$bd_user:'.$bd_user.'], [$bd_passwd:'.$bd_passwd.'], [$bd_database:'.$bd_database.']';
	    	var_dump($this->BD_CONNECTION);
	    }
	}
	private static function connectDb($bd_host, $bd_port, $bd_url, $bd_user, $bd_passwd, $bd_database) {
	    $CONNECTION = null;
	    if (SolvesDAO::isSystemDbTypeMySql()) {
	        $CONNECTION = new \mysqli($bd_host, $bd_user, $bd_passwd, $bd_database);
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

	    } else if (SolvesDAO::isSystemDbTypePostgresql()) {
	        $CONNECTION = pg_connect('host=' . $bd_host . ' port=' . $bd_port . ' dbname=' . $bd_database . ' user=' . $bd_user . ' password=' . $bd_passwd) or die("Erro na conexão com o Database PostgreSQL --> " . pg_last_error($CONNECTION));
	        pg_set_client_encoding($CONNECTION, 'utf8');
	    }
	    return $CONNECTION;
	}
	public function close() {
	    if (isset($this->BD_CONNECTION)) {
	        if (SolvesDAO::isSystemDbTypeMySql()) {
	            @$this->BD_CONNECTION->close();
	            @mysqli_close($this->BD_CONNECTION);
	        } else if (SolvesDAO::isSystemDbTypePostgresql()) {
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
  		if(\SolvesDAO\SolvesDAO::isSystemDbTypeMySql()){ 
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
		if(\SolvesDAO\SolvesDAO::isSystemDbTypeMySql()){ 
			if(!$this->commitManual || !$this->transactionOpened){
				$this->BD_CONNECTION->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
				$this->transactionOpened = true;		
			}
		}
	}
	public function rollbackTransaction(){
		if(\SolvesDAO\SolvesDAO::isSystemDbTypeMySql()){ 
			$this->BD_CONNECTION->rollback();
			$this->transactionOpened=false;	
		}	
	}
	public function commitTransaction($doManualCommit=false) : bool{
		$result = true;
		if(\SolvesDAO\SolvesDAO::isSystemDbTypeMySql()){ 
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
?>