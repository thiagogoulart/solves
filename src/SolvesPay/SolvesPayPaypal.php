<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 26/06/2019
 */ 
namespace SolvesPay;

class SolvesPayPaypal extends SolvesPay{
	const METHOD_PROCESSAR_PAGAMENTO = 'DoExpressCheckoutPayment';
	const METHOD_SOLICITAR_PAGAMENTO = 'SetExpressCheckout';
	const METHOD_DETALHES_PAGAMENTO = 'GetExpressCheckoutDetails';

	protected static $PAYPAL_USUARIO='';
    protected static $PAYPAL_SENHA='';
    protected static $PAYPAL_ASSINATURA='';
    protected static $PAYPAL_EMAIL='';
    protected static $PAYPAL_BRANDNAME='';
    protected static $PAYPAL_NOTIFYURL='';
    protected static $PAYPAL_RETURNURL='';
    protected static $PAYPAL_CANCELURL='';
    protected static $PAYPAL_PATH_LOG='';


	public function __construct(\SolvesPay\SolvesPayCompra $solvesCompra) {
		parent::__construct($solvesCompra);
	}

    public static function config($PAYPAL_USUARIO, $PAYPAL_SENHA, $PAYPAL_ASSINATURA, $PAYPAL_EMAIL, $PAYPAL_BRANDNAME, $PAYPAL_NOTIFYURL, $PAYPAL_RETURNURL, $PAYPAL_CANCELURL,$PAYPAL_PATH_LOG){
        SolvesPayPaypal::$PAYPAL_USUARIO=$PAYPAL_USUARIO;
    	SolvesPayPaypal::$PAYPAL_SENHA=$PAYPAL_SENHA;
    	SolvesPayPaypal::$PAYPAL_ASSINATURA=$PAYPAL_ASSINATURA;
    	SolvesPayPaypal::$PAYPAL_EMAIL=$PAYPAL_EMAIL;
    	SolvesPayPaypal::$PAYPAL_BRANDNAME=$PAYPAL_BRANDNAME;
    	SolvesPayPaypal::$PAYPAL_NOTIFYURL=$PAYPAL_NOTIFYURL;
    	SolvesPayPaypal::$PAYPAL_RETURNURL=$PAYPAL_RETURNURL;
    	SolvesPayPaypal::$PAYPAL_CANCELURL=$PAYPAL_CANCELURL;
    	SolvesPayPaypal::$PAYPAL_PATH_LOG=$PAYPAL_PATH_LOG;
    }

	public function init($forceInitialization=false){
		if(!$this->initialized || $forceInitialization){			
			$this->initialized= true;
		}
	}
	public function getPaypalUrl(){
		if ($this->isSandbox()) {
            return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            return 'https://www.paypal.com/cgi-bin/webscr';
        }
	}

	/*Está como publico para poder teste*/
	public function getItens(){
		$itens = array();
		$itOrder = 0;
		$valorTotal = 0;
		$solvesCompraItems = $this->getSolvesCompra()->getCompraItens();
		if(isset($solvesCompraItems)){
			foreach($solvesCompraItems as $cp){
				if($itOrder==0){
					$itens = $this->getItem($itOrder, $cp);
				}else{
					$itens = array_merge($itens, $this->getItem($itOrder, $cp));
				}
				$itOrder++;
				$valorTotal = $valorTotal+$cp->getValorFinalPay();
			}
		}
        $itens['L_PAYMENTREQUEST_0_ITEMAMT'] = $valorTotal;
        $itens['PAYMENTREQUEST_0_AMT'] = $valorTotal;
        $itens['PAYMENTREQUEST_0_ITEMAMT'] = $valorTotal;
		return $itens;
	}
	private function getItem($itOrder, $solvesCompraItem){
		return array(
		'L_PAYMENTREQUEST_0_NAME'.$itOrder => $solvesCompraItem->getLabelPay(),
        'L_PAYMENTREQUEST_0_DESC'.$itOrder => $solvesCompraItem->getLabelPay(),
        'L_PAYMENTREQUEST_0_AMT'.$itOrder => $solvesCompraItem->getValorFinalPay(),
        'L_PAYMENTREQUEST_0_QTY'.$itOrder => $solvesCompraItem->getQuantidade());
	}
	public function getRequestForDetalhesPagamento($token){
		$this->init();  
		$requestNvp = array(
            'USER' => SolvesPayPaypal::$PAYPAL_USUARIO,
            'PWD' => SolvesPayPaypal::$PAYPAL_SENHA,
            'SIGNATURE' => SolvesPayPaypal::$PAYPAL_ASSINATURA,
            'VERSION' => '108.0',
            'METHOD'=> self::METHOD_DETALHES_PAGAMENTO,        
            'TOKEN'=> $token,
            'SUBJECT' => SolvesPayPaypal::$PAYPAL_EMAIL
        );
        return $requestNvp;
    }
	public function getRequestForSolicitacaoDePagamento(){
		$this->init();  
		$requestNvp = array(
            'USER' => SolvesPayPaypal::$PAYPAL_USUARIO,
            'PWD' => SolvesPayPaypal::$PAYPAL_SENHA,
            'SIGNATURE' => SolvesPayPaypal::$PAYPAL_ASSINATURA,
            'VERSION' => '108.0',
            'METHOD'=> self::METHOD_SOLICITAR_PAGAMENTO,  
            'NOSHIPPING' => 1,
            'BRANDNAME' => SolvesPayPaypal::$PAYPAL_BRANDNAME,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'BRL',
            'PAYMENTREQUEST_0_NOTIFYURL' => self::getCompleteNotifyUrl(),
            'RETURNURL' => self::getCompleteReturnUrl(),
            'CANCELURL' => self::getCompleteCancelUrl(),
            'BUTTONSOURCE' => 'BR_EC_EMPRESA',
            'PAYMENTREQUEST_0_INVNUM' => $this->getSolvesCompra()->getInvoiceId()
        );
        $itens = $this->getItens();
        $requestNvp = array_merge($requestNvp, $itens);
        $this->log('(getRequestForSolicitacaoDePagamento) '.print_r($requestNvp,true));
        return $requestNvp;
    }

	public function getRequestForProcessamentoDePagamento($token, $payerid){
		$this->init();  
		$requestNvp = array(
            'USER' => SolvesPayPaypal::$PAYPAL_USUARIO,
            'PWD' => SolvesPayPaypal::$PAYPAL_SENHA,
            'SIGNATURE' => SolvesPayPaypal::$PAYPAL_ASSINATURA,
            'VERSION' => '108.0',
            'METHOD'=> self::METHOD_PROCESSAR_PAGAMENTO,          
            'PAYERID'=> $payerid,                
            'TOKEN'=> $token,
            'SUBJECT' => SolvesPayPaypal::$PAYPAL_EMAIL,                
            'NOTIFYURL'=> self::getCompleteNotifyUrl(),     
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
            'PAYMENTREQUEST_0_CURRENCYCODE'=>'BRL'
        );
        $itens = $this->getItens();
        $requestNvp = array_merge($requestNvp, $itens);
        $this->log('(getRequestForProcessamentoDePagamento) '.print_r($requestNvp,true));
        return $requestNvp;
    }
	public function solicitarPagamento(){
		$this->log('(solicitarPagamento) START ');
        $paypalURL = $this->getPaypalUrl();
        $requestNvp = $this->getRequestForSolicitacaoDePagamento();
        $responseNvp = $this->sendNvpRequest($requestNvp, 'solicitarPagamento');

        //Se a operação tiver sido bem sucedida, redirecionamos o cliente para o
        //ambiente de pagamento.
        if (isset($responseNvp['ACK']) && $responseNvp['ACK'] == 'Success') {
            $query = array(
                'cmd'    => '_express-checkout',
                'token'  => $responseNvp['TOKEN']
            );
            
            $redirectURL = sprintf('%s?%s', $paypalURL, http_build_query($query));
            $jsonNvp = json_encode($responseNvp);
            $token = $responseNvp['TOKEN'];
            $transactionId=null;
            if(@$responseNvp['PAYMENTINFO_0_TRANSACTIONID'] != ""){
               $transactionId=$responseNvp['PAYMENTINFO_0_TRANSACTIONID'];
            }
	        $success = $this->getSolvesCompra()->afterDoPayEvent($jsonNvp, $transactionId, $token, $redirectURL);

			$this->log('(solicitarPagamento) END ');
            return $redirectURL;
        } else {
            //Opz, alguma coisa deu errada.
            //Verifique os logs de erro para depuração.
            $message =  print_r($responseNvp,true);
           
            $this->log('(solicitarPagamento) '.$message);
			$this->log('(solicitarPagamento) END ');
            return null;
        }
    }
    private function getCompleteNotifyUrl(){
    	return (\Solves\Solves::stringComecaCom(self::$PAYPAL_NOTIFYURL, 'http') ?  self::$PAYPAL_NOTIFYURL : \Solves\Solves::getCompleteUrl(false, false, self::$PAYPAL_NOTIFYURL));
    }
    private function getCompleteReturnUrl(){
    	return (\Solves\Solves::stringComecaCom(self::$PAYPAL_RETURNURL, 'http') ?  self::$PAYPAL_RETURNURL : \Solves\Solves::getCompleteUrl(false, $this->isApp(), self::$PAYPAL_RETURNURL));
    }
    private function getCompleteCancelUrl(){
    	return (\Solves\Solves::stringComecaCom(self::$PAYPAL_CANCELURL, 'http') ?  self::$PAYPAL_CANCELURL : \Solves\Solves::getCompleteUrl(false, $this->isApp(), self::$PAYPAL_CANCELURL));
    }
    private function log($log){        
        if(\Solves\Solves::isNotBlank(SolvesPayPaypal::$PAYPAL_PATH_LOG)){
        	file_put_contents(SolvesPayPaypal::$PAYPAL_PATH_LOG, "[".date('Y-m-d H:i:s')."]LOG:".$log.PHP_EOL , FILE_APPEND | LOCK_EX);        
        }
    }
	/**
	 * Envia uma requisição NVP para uma API PayPal.
	 *
	 * @param array $requestNvp Define os campos da requisição.
	 * @param boolean $sandbox Define se a requisição será feita no sandbox ou no
	 *                         ambiente de produção.
	 *
	 * @return array Campos retornados pela operação da API. O array de retorno poderá
	 *               ser vazio, caso a operação não seja bem sucedida. Nesse caso, os
	 *               logs de erro deverão ser verificados.
	 */
	private function sendNvpRequest(array $requestNvp, $originMethod=''){
	    //Endpoint da API
	    $apiEndpoint  = 'https://api-3t.' . ($this->isSandbox() ? 'sandbox.': null);
	    $apiEndpoint .= 'paypal.com/nvp';
	 
	    //Executando a operação
	    $curl = curl_init();
	 
	    curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestNvp));
	 
	    $response = urldecode(curl_exec($curl));
	 
	    curl_close($curl);
	 
	    //Tratando a resposta
	    $responseNvp = array();
	 
	    if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
	        foreach ($matches['name'] as $offset => $name) {
	            $responseNvp[$name] = $matches['value'][$offset];
	        }
	    }
	 
	    //Verificando se deu tudo certo e, caso algum erro tenha ocorrido,
	    //gravamos um log para depuração.

	    if (isset($responseNvp['ACK']) && $responseNvp['ACK'] != 'Success') {
	        for ($i = 0; isset($responseNvp['L_ERRORCODE' . $i]); ++$i) {
	            $message = sprintf("(".$originMethod.")(Resultado de sendNvpRequest vindo de '".$originMethod."') PayPal NVP %s[%d]: %s\n",
	                               $responseNvp['L_SEVERITYCODE' . $i],
	                               $responseNvp['L_ERRORCODE' . $i],
	                               $responseNvp['L_LONGMESSAGE' . $i]);
	 
	            //error_log($message);
	            $this->log($message);
	        }
	    }
	 
	    return $responseNvp;
	}
	private function isEmailOfReceiverCorrect($email){
		return (SolvesPayPaypal::$PAYPAL_EMAIL==$email);
	}

	public function trataNotificacaoPagamento($php_server, $php_post, $CONNECTION){	
		$success = false;			
		$request = print_r($php_post,true);

		//use PaypalIPN;
		$ipn = new \SolvesPay\SolvesPayPaypalIPN();
		// Use the sandbox endpoint during testing.

		if($php_server['SERVER_NAME'] == 'localhost'){
		    $ipn->useSandbox();
	        $this->log("--(NOTIFICACAO)-- ".$request);
		} else {
	        $this->log("--(NOTIFICACAO)-- ".$request);
		    error_log($request);
		}
		$ipn->usePHPCerts();
		$verified = $ipn->verifyIPN();

		if ($verified) {
			$transactionId = $php_post['txn_id'];
		    if($php_post['payment_status'] != "" && $php_post['txn_id'] != "" &&
		        $this->isEmailOfReceiverCorrect($php_post['receiver_email']) ){
				$this->log("--(NOTIFICACAO)-- ATRIBUTOS CORRETOS : ". $transactionId);
                $compraId = \SolvesPay\SolvesPayCompra::getIdByInvoiceId($php_post['invoice']);
		        $situacao = $php_post['payment_status'];
				$this->log("--(NOTIFICACAO)-- [ ". $compraId."; ".$situacao."]");

		        if($compraId > 0){
					$this->log("--(NOTIFICACAO)-- ID da compra informado");
					$paid_first_name = $php_post['first_name'];
					$paid_last_name = $php_post['last_name'];
					$paid_business = $php_post['business'];
					$paid_payer_email = $php_post['payer_email'];
					$paid_ipn_track_id = $php_post['ipn_track_id'];
					$paid_transaction_subject = $php_post['transaction_subject'];
					$paid_receiver_id = $php_post['receiver_id'];					

		            $success = $this->getSolvesCompra()->atualizaCompra($compraId, $transactionId, $situacao, $paid_first_name, $paid_last_name, $paid_business, $paid_payer_email, $paid_ipn_track_id, $paid_transaction_subject, $paid_receiver_id);
					$this->log("--(NOTIFICACAO)-- Compra atualizada: ".($success?"SIM":"NÃO"));
		        }else{
					$this->log("--(NOTIFICACAO)-- ID da Compra não informado");
		        }
		    } else {
				$this->log("--(NOTIFICACAO)-- ATRIBUTOS INVÁLIDOS : ". $transactionId);
		    }
		} else {
	        $this->log("--(NOTIFICACAO)-- NÃO VERIFICADO");
	        $this->log($request);
		}
		return $success;
	}
	public function trataNotificacaoSucesso($token, $payerId){
		$this->log('(trataNotificacaoSucesso) START ');		
		if(\Solves\Solves::isNotBlank($token) && \Solves\Solves::isNotBlank($payerId)){ 
	        $requestNvp = $this->getRequestForDetalhesPagamento($token);
	        $retornoConsulta = $this->sendNvpRequest($requestNvp, 'trataNotificacaoSucesso');
	        if(strtoupper($retornoConsulta['ACK']) == 'SUCCESS'  || ($retornoConsulta['ACK']) == 'SuccessWithWarning'){
	            if($retornoConsulta['CHECKOUTSTATUS'] == 'PaymentCompleted'){
					$this->log('(trataNotificacaoSucesso) PaymentCompleted ');
					$this->log('(trataNotificacaoSucesso) END ');
	                //não precisa fazer nada
	            } else if($retornoConsulta['CHECKOUTSTATUS'] == 'PaymentActionNotInitiated'){
	            	$transactionId = null;
	            	$correlationId = $retornoConsulta['CORRELATIONID'];
	            	$retornoNotificacao = $this->getSolvesCompra()->trataNotificacaoSucesso($token, $payerId, $correlationId);
	            	$success = $retornoNotificacao[0];
	            	$msg = $retornoNotificacao[1];
					$this->log('(trataNotificacaoSucesso) END ');	
	            	if($success){
				        $requestNvp = $this->getRequestForProcessamentoDePagamento($token, $payerId);
				        $resultadoPagamento = $this->sendNvpRequest($requestNvp, 'trataNotificacaoSucesso');
		            	if(@$resultadoPagamento['PAYMENTINFO_0_TRANSACTIONID'] != ""){
		            		$transactionId = $resultadoPagamento['PAYMENTINFO_0_TRANSACTIONID'];
		            		$this->getSolvesCompra()->atualizaTransactionID($transactionId);

				        	return 'ok';
		            	}else{
		            		return "<h5>Erro ao encontrar a compra: Compra inválida</h5>" . print_r($resultadoPagamento, true);
		            	}
	            	} else {
			            return $msg;
			        }		                
	            }else{
					$this->log('(trataNotificacaoSucesso) NOT EVEN PaymentCompleted OR PaymentActionNotInitiated ');
					$this->log('(trataNotificacaoSucesso) END ');
	            }
	        }
        }else{
			$this->log('(trataNotificacaoSucesso) END ');	
	        return '<h2>Faltam parâmetros para concluir a transação!</h2>';
        }
    }
}
?>