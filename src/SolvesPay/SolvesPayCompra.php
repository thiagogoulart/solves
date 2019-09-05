<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 02/09/2019
 */ 
namespace SolvesPay;

class SolvesPayCompra {	
	protected $solvesCompraItens=array();
	
	protected $id=null;
	protected $solvesObjectCompra=null;

	public function __construct($id, \SolvesPay\SolvesObjectCompra $solvesObjectCompra) {
		$this->id = $id;
		$this->solvesObjectCompra = $solvesObjectCompra;
		$this->solvesCompraItens=array();
	}
	public function addCompraItem(\SolvesPay\SolvesPayCompraItem $compraItem){
		array_push($this->solvesCompraItens, $compraItem);
	}
	public function getId(){
		return $this->id;
	}
	public function getCompraItens(){
		return $this->solvesCompraItens;
	}
	public function setCompraItens(array $itens){
		$this->solvesCompraItens=$itens;
	}
	public function atualizaCompra($compraId, $transactionId, $situacao, $paid_first_name=null, $paid_last_name=null, $paid_business=null, $paid_payer_email=null, $paid_ipn_track_id=null, $paid_transaction_subject=null, $paid_receiver_id=null){
		if($this->solvesObjectCompra->atualizaSePagoPorSituacao($situacao,$paid_first_name, $paid_last_name, $paid_business, $paid_payer_email, $paid_ipn_track_id, $paid_transaction_subject, $paid_receiver_id)){
			return $this->solvesObjectCompra->atualizaCompra($compraId, $transactionId, $situacao);
		}
		return false;
	}
	public function atualizaTransactionID($transactionId){
		return $this->solvesObjectCompra->atualizaTransactionID($transactionId);
	}
	
	public function trataNotificacaoSucesso($token, $payerId, $correlationId){
		$retornoNotificacao = array();
		$success = false;
		$msg = 'ok';

        $this->solvesObjectCompra = $this->solvesObjectCompra->findOneByToken($token, $payerId, $correlationId);
        if(@isset($this->solvesObjectCompra)){
        	$success = ($this->solvesObjectCompra->atualizaNotificacaoSucesso($token, $payerId, $correlationId));
        	if($success){ 
	           	$msg = 'ok';
	        } else {
	            $msg = '<h2>Erro</h2>';
	        }	                    
        } else {
            $msg = "<h2>Erro ao encontrar a compra: Token inválido ".$token . '</h2>';
        }
        $retornoNotificacao[0] = $success;
        $retornoNotificacao[1] = $msg;
        return $retornoNotificacao;	
	}
	public function afterDoPayEvent($jsonNvp, $transactionId, $token, $redirectURL){
		return $this->solvesObjectCompra->afterDoPay($jsonNvp, $transactionId, $token, $redirectURL);
	}
}
?>