<?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 27/11/2018
 
*/

use SolvesDAO\DAO;
use SolvesDAO\DAOJoin;
use SolvesDAO\SolvesObject;
use \SolvesPay\SolvesObjectCompra;

class SolvesObjectCompraMock extends SolvesObjectCompra{ 

	public static $TABELA = '';
 	public static $PK = '';
	public static $SEQUENCIA = '';

    /**
     * @var string
     * nome do vendedor
     */
	protected $vendedor='';

    /**
     * @var string
     * anotações. Tem getter e setter
     */
	protected $anotacoes='';

    /**
     * @var string
     */
	protected $vendedor_com_anotacoes='';


	public function __construct($con, $parentDao=null) {
		parent::__construct($con, self::$TABELA, self::$PK, self::$SEQUENCIA, $parentDao);
	}

	public function getAnotacoes() {return $this->anotacoes;}
	public function setAnotacoes($p) {return $this->anotacoes = $p;}

	public function getVendedorComAnotacoes() {
		return $this->vendedor.'-'.$this->anotacoes;
	}
	public function setVendedorComAnotacoes($p) {return $this->vendedor_com_anotacoes = $p;}


//DAO
    public function beforeSaveAndUpdate(){

    }
    public function afterSave(){

    }
    public function afterUpdate($old){

    }
    public function afterDelete(){

    }

	public function findOneByToken($token, $payerId, $correlationId){
 		$list =  $this->findArrayByToken($token, $payerId, $correlationId);
 		return $this->toOneObject($list);
 	}
  	public function findArrayByToken($token, $payerId, $correlationId){ 
 		return array();
 	}
	public function atualizaCompra($compraId, $transactionId, $situacao): bool{
		$success = $this->atualizaSePagoPorSituacao($situacao);
        if($success){
        	//$this->enviaEmailAprovacaoPagamento($pagamento, $compraProdutos);
        }
        return $success;
	}
	public function atualizaNotificacaoSucesso($token, $payerId, $correlationId): bool{
		$success = false;
		return $success;
	}
  	public function addValores() {
     	$this->atualizaTotais();
  	}
  	public function getObject($itemArr) {
  		 return null; 
 	 }

 	 public function doPay(){
 	 	$response = array();
        return $response; 
 	 }
	public  function afterDoPay($jsonNvp, $transactionId, $token, $redirectURL){
		return false;
	}
}
?>