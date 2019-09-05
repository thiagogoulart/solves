<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 19/07/2019
 */ 
namespace SolvesPay;

abstract class SolvesObjectCompra extends \SolvesDAO\SolvesObject{

	protected $valor_total;
	protected $valor_total_desconto;
	protected $valor_total_final;
	protected $valor_total_pago;
	protected $pago;
	protected $pagoLabel;
	protected $payerid;
	protected $correlationid;
	protected $transactionid;
	protected $token;
	protected $comprovante;
	protected $url;
	protected $aprovado;
	protected $aprovadoLabel;
	protected $situacao;
	protected $data_aprovacao= '0000-00-00 00:00:00';
	protected $data_aprovacaoLabel;

	protected $paid_first_name;
	protected $paid_last_name;
	protected $paid_business;
	protected $paid_payer_email;
	protected $paid_ipn_track_id;
	protected $paid_transaction_subject;
	protected $paid_receiver_id;

	
    public static $S_NOVO = "Pré-cadastro";
    public static $S_AGUARDANDO = "Aguardando autorização do Pagamento";
    public static $S_APROVADO = "Pagamento autorizado";
    public static $S_CANCELADO  = "Pagamento cancelado";

	public function __construct($con, $tabela, $pk, $sequencia=null, $parentDao=null) {
		parent::__construct($con, $tabela, $pk, $sequencia, $parentDao);
	}


	//IMPLEMENT
	public abstract function findOneByToken($token, $payerId, $correlationId);
	//IMPLEMENT
	public abstract function atualizaCompra($compraId, $transactionId, $situacao): bool;
	//IMPLEMENT
	public abstract function atualizaNotificacaoSucesso($token, $payerId, $correlationId): bool;
	//IMPLEMENT
	public abstract function afterDoPay($jsonNvp, $transactionId, $token, $redirectURL);
	
	public function getValorTotal() {return $this->valor_total;}
	public function setValorTotal($p) {$this->valor_total = $p;}

	public function getValorTotalDesconto() {return $this->valor_total_desconto;}
	public function setValorTotalDesconto($p) {$this->valor_total_desconto = $p;}

	public function getValorTotalFinal() {return $this->valor_total_final;}
	public function setValorTotalFinal($p) {$this->valor_total_final = $p;}

	public function getValorTotalPago() {return $this->valor_total_pago;}
	public function setValorTotalPago($p) {$this->valor_total_pago = $p;}

	public function isPago() {return \Solves\Solves::checkBoolean($this->pago);}
	public function getPago() {return $this->pago;}
	public function setPago($p) {$this->pago = $p;}
	public function getPagoLabel() {return $this->pagoLabel;}
	public function setPagoLabel($p) {$this->pagoLabel = $p;}

	public function getPayerid() {return $this->payerid;}
	public function setPayerid($p) {$this->payerid = $p;}

	public function getCorrelationid() {return $this->correlationid;}
	public function setCorrelationid($p) {$this->correlationid = $p;}

	public function getTransactionid() {return $this->transactionid;}
	public function setTransactionid($p) {$this->transactionid = $p;}

	public function getToken() {return $this->token;}
	public function setToken($p) {$this->token = $p;}

	public function getComprovante() {return $this->comprovante;}
	public function setComprovante($p) {$this->comprovante = $p;}

	public function getUrl() {return $this->url;}
	public function setUrl($p) {$this->url = $p;}

	public function isAprovado() {return \Solves\Solves::checkBoolean($this->aprovado);}
	public function getAprovado() {return $this->aprovado;}
	public function setAprovado($p) {$this->aprovado = $p;}
	public function getAprovadoLabel() {return $this->aprovadoLabel;}
	public function setAprovadoLabel($p) {$this->aprovadoLabel = $p;}

	public function getSituacao() {return $this->situacao;}
	public function setSituacao($p) {$this->situacao = $p;}

	public function getDataAprovacao() {return $this->data_aprovacao;}
	public function setDataAprovacao($p) {$this->data_aprovacao = $p;}
	public function getDataAprovacaoLabel() {return $this->data_aprovacaoLabel;}
	public function setDataAprovacaoLabel($p) {$this->data_aprovacaoLabel = $p;}

	public function getPaidFirstName() {return $this->paid_first_name;}
	public function setPaidFirstName($p) {$this->paid_first_name = $p;}

	public function getPaidLastName() {return $this->paid_last_name;}
	public function setPaidLastName($p) {$this->paid_last_name = $p;}

	public function getPaidBusiness() {return $this->paid_business;}
	public function setPaidBusiness($p) {$this->paid_business = $p;}

	public function getPaidPayerEmail() {return $this->paid_payer_email;}
	public function setPaidPayerEmail($p) {$this->paid_payer_email = $p;}

	public function getPaidIpnTrackId() {return $this->paid_ipn_track_id;}
	public function setPaidIpnTrackId($p) {$this->paid_ipn_track_id = $p;}

	public function getPaidTransactionSubject() {return $this->paid_transaction_subject;}
	public function setPaidTransactionSubject($p) {$this->paid_transaction_subject = $p;}

	public function getPaidReceiverId() {return $this->paid_receiver_id;}
	public function setPaidReceiverId($p) {$this->paid_receiver_id = $p;}


	protected function atualizaTotais(){
		if(\Solves\Solves::isNotBlank($this->valor_total)){
			if(\Solves\Solves::isNotBlank($this->valor_total_desconto)){
				$this->valor_total_final = ($this->valor_total - $this->valor_total_desconto);
			}else{
				$this->valor_total_desconto = 0;
				$this->valor_total_final = $this->valor_total;
			}
			if(!\Solves\Solves::isNotBlank($this->valor_total_pago)){
				$this->valor_total_pago = 0;
			}
		}else{
			$this->valor_total = 0;
			$this->valor_total_desconto = 0;
			$this->valor_total_pago = 0;
		}
	}

    public function atualizaPayerID($payerId, $correlationid){
    	$this->setPayerId($payerId);
    	$this->setCorrelationid($correlationid);
    	$this->setSituacao(self::$S_AGUARDANDO);
    	return true;
    }
    public function atualizaTransactionID($transactionId){
    	$this->setTransactionid($transactionId);
    	return true;
    }


	public function atualizaSePagoPorSituacao($situacao, $paid_first_name, $paid_last_name, $paid_business, $paid_payer_email, $paid_ipn_track_id, $paid_transaction_subject, $paid_receiver_id){
        $creditar = false;
        $success = false;
        $this->pago = $aprovado;
        $this->updated_at = \Solves\SolvesTime::getTimestampAtual();

		$this->updatePaidAttrs($paid_first_name, $paid_last_name, $paid_business, $paid_payer_email, $paid_ipn_track_id, $paid_transaction_subject, $paid_receiver_id);
        if($situacao == "Completed"){
            $creditar = ($this->isPago() ? false : true);
            $this->aprovado = true;
            $this->situacao = self::$S_APROVADO;
        	$this->pago =true;
        	$this->valor_total_pago = $this->valor_total_final;   
        	$this->data_aprovacao = $this->updated_at;   
        }          	
        try{
        	$success = $this->update();
        } catch (Exception $e) {
        	$success = false;
        }
        return ($success && $creditar);
	}

	public function updatePaidAttrs($paid_first_name, $paid_last_name, $paid_business, $paid_payer_email, $paid_ipn_track_id, $paid_transaction_subject, $paid_receiver_id){
		$this->setPaidFirstName($paid_first_name);
		$this->setPaidLastName($paid_last_name);
		$this->setPaidBusiness($paid_business);
		$this->setPaidPayerEmail($paid_payer_email);
		$this->setPaidIpnTrackId($paid_ipn_track_id);
		$this->setPaidTransactionSubject($paid_transaction_subject);
		$this->setPaidReceiverId($paid_receiver_id);
	}
}	
?>