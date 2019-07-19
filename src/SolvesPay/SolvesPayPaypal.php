<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 26/06/2019
 */ 
namespace SolvesPay;

class SolvesPayPaypal extends SolvesPay{

	private $environment;
	private $initialized=false;

	public function __construct() {
		$this->setEnvironmentProduction();
	}

	public function setEnvironmentProduction(){
		$this->environment = 'production';
	}
	public function setEnvironmentSandbox(){
		$this->environment = 'sandbox';
	}
	public function isSandbox(){
		return ($this->environment == 'sandbox');
	}
	public function init($forceInitialization=false){
		if(!$this->initialized || $forceInitialization){
			
		}
	}
	public function getPaypalUrl(){
		if ($this->isSandbox()) {
            return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            return 'https://www.paypal.com/cgi-bin/webscr';
        }
	}

	private function getItens($compraProdutos){
		$itens = array();
		$itOrder = 0;
		$valorTotal = 0;
		foreach($compraProdutos as $cp){
			if($itOrder==0){
				$itens = $this->getItem($itOrder, $cp);
			}else{
				$itens = array_merge($itens, $this->getItem($itOrder, $cp));
			}
			$itOrder++;
			$valorTotal = $valorTotal+$cp->getValorFinalPay();
		}

        $itens['L_PAYMENTREQUEST_0_ITEMAMT'] = $valorTotal;
        $itens['PAYMENTREQUEST_0_AMT'] = $valorTotal;
        $itens['PAYMENTREQUEST_0_ITEMAMT'] = $valorTotal;
		return $itens;
	}
	private function getItem($itOrder, $compraProduto){
		return array('L_PAYMENTREQUEST_0_NAME'.$itOrder => $compraProduto->getProdutoLabelPay(),
        'L_PAYMENTREQUEST_0_DESC'.$itOrder => $compraProduto->getProdutoLabelPay(),
        'L_PAYMENTREQUEST_0_AMT'.$itOrder => $compraProduto->getValorFinalPay(),
        'L_PAYMENTREQUEST_0_QTY'.$itOrder => $compraProduto->getQuantidade());
	}
	private function doRequestPay($cliente, $compra, $compraProdutos){
		$this->init();        
         //Campos da requisição da operação SetExpressCheckout, como ilustrado acima.
        $requestNvp = array(
            'USER' => PAYPAL_USUARIO,
            'PWD' => PAYPAL_SENHA,
            'SIGNATURE' => PAYPAL_ASSINATURA,
            'VERSION' => '108.0',
            'METHOD'=> 'SetExpressCheckout',
            'NOSHIPPING' => 1,
            'BRANDNAME' => PAYPAL_BRANDNAME,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'BRL',
            'PAYMENTREQUEST_0_NOTIFYURL' => PAYPAL_NOTIFYURL,
            'RETURNURL' => PAYPAL_RETURNURL,
            'CANCELURL' => PAYPAL_CANCELURL,
            'BUTTONSOURCE' => 'BR_EC_EMPRESA',
            'PAYMENTREQUEST_0_INVNUM' => $compra->getId()
        );
        $itens = $this->getItens($compraProdutos);
        $this->log('ITENS:'.print_r($itens,true));
        $requestNvp = array_merge($requestNvp, $itens);
        $this->log(print_r($requestNvp,true));
/*,

            'BUTTONSOURCE' => 'BR_EC_EMPRESA'*/
        
        //$this->log(print_r($requestNvp,true));
        //Envia a requisição e obtém a resposta da PayPal
        return $this->sendNvpRequest($requestNvp);
    }
	public function doPay($cliente, $compra, $compraProdutos){
        $paypalURL = $this->getPaypalUrl();
		$responseNvp = $this->doRequestPay($cliente, $compra, $compraProdutos);

        //Se a operação tiver sido bem sucedida, redirecionamos o cliente para o
        //ambiente de pagamento.
        if (isset($responseNvp['ACK']) && $responseNvp['ACK'] == 'Success') {
            $query = array(
                'cmd'    => '_express-checkout',
                'token'  => $responseNvp['TOKEN']
            );
            
            $redirectURL = sprintf('%s?%s', $paypalURL, http_build_query($query));
            $compra->setPayResponse( json_encode($responseNvp) );
            if(@$responseNvp['PAYMENTINFO_0_TRANSACTIONID'] != ""){
                $compra->setTransactionId($responseNvp['PAYMENTINFO_0_TRANSACTIONID']);
            }
            $compra->setTokenPagamento($responseNvp['TOKEN']);
            $compra->setUrlPagamento($redirectURL);
            $compra->update();
            return $redirectURL;
        } else {
            //Opz, alguma coisa deu errada.
            //Verifique os logs de erro para depuração.
            $message =  print_r($responseNvp,true);
           
            $this->log($message);
            return null;
        }
    }

    
    private function log($log){        
        file_put_contents(PAYPAL_PATH_LOG, $log.PHP_EOL , FILE_APPEND | LOCK_EX);        
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
	private function sendNvpRequest(array $requestNvp){
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
	            $message = sprintf("PayPal NVP %s[%d]: %s\n",
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
		return (PAYPAL_EMAIL==$email);
	}
	public function trataNotificacaoPagamento($php_server, $php_post, $CONNECTION){	
		$success = false;			
		$request = "[".date('Y-m-d H:i:s')."]LOG:" . print_r($php_post,true);

		//use PaypalIPN;
		$ipn = new PaypalIPN();
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
		    if($php_post['payment_status'] != "" && $php_post['txn_id'] != "" &&
		        $this->isEmailOfReceiverCorrect($php_post['receiver_email']) ){
		        
				$this->log("--(NOTIFICACAO)-- ATRIBUTOS CORRETOS : ". $php_post['txn_id']);
		        $compra_id = @getIntValue($php_post['invoice']);
		        $situacao = $php_post['payment_status'];
				$this->log("--(NOTIFICACAO)-- [ ". $compra_id."; ".$situacao."]");

		        if($compra_id > 0){
					$this->log("--(NOTIFICACAO)-- ID da compra informado");
		        	$objCompra =  new Compra($CONNECTION);
		            $success = $objCompra->atualizaCompra($compra_id, $situacao);
					$this->log("--(NOTIFICACAO)-- Compra atualizada: ".($success?"SIM":"NÃO"));
		        }else{
					$this->log("--(NOTIFICACAO)-- ID da Compra não informado");
		        }
		    } else {
				$this->log("--(NOTIFICACAO)-- ATRIBUTOS INVÁLIDOS : ". $php_post['txn_id']);
		    }
		} else {
	        $this->log("--(NOTIFICACAO)-- NÃO VERIFICADO");
	        $this->log($request);
		}
		return $success;
	}
	private function solicitarDadosPagamento($token){
        $requestNvp = array(
            'USER' => PAYPAL_USUARIO,
            'PWD' => PAYPAL_SENHA,
            'SIGNATURE' => PAYPAL_ASSINATURA,
            'VERSION' => '108.0',
            'METHOD'=> 'GetExpressCheckoutDetails',                
            'TOKEN'=> $token,
            'SUBJECT' => PAYPAL_EMAIL
        );
        //Envia a requisição e obtém a resposta da PayPal
        $responseNvp = $this->sendNvpRequest($requestNvp, $this->isSandbox());
        ///echo print_r($responseNvp,true);
        //$this->LogPaypal("LOG:" . print_r($responseNvp,true));
        return $responseNvp;
    }
	public function trataNotificacaoSucesso($CONNECTION, $token, $payerId){
		if(isNotBlank($token) && isNotBlank($payerId)){ 
			$retornoConsulta = $this->solicitarDadosPagamento($token);
	        if(strtoupper($retornoConsulta['ACK']) == 'SUCCESS'  || ($retornoConsulta['ACK']) == 'SuccessWithWarning'){
	            if($retornoConsulta['CHECKOUTSTATUS'] == 'PaymentCompleted'){
	                //não precisa fazer nada
	            } else if($retornoConsulta['CHECKOUTSTATUS'] == 'PaymentActionNotInitiated'){
	            	$compra = new Compra($CONNECTION);
	                $compra = $compra->findByToken(getUsuarioId(), $token);
	                if(@isset($compra)){
	                	if($compra->atualizaNotificacaoSucesso($payerId , $retornoConsulta['CORRELATIONID'])){
	                		$cliente = new Cliente($CONNECTION);
					        $cliente = $cliente->findById($compra->getClienteId());
					        
							$compraProduto = new CompraProduto($CONNECTION);
							$compraProdutos = $compraProduto->findByCompraId($compra->getUsuarioId(), $compra->getId());

				            $urlPagamento = $this->doPay($cliente, $compra, $compraProdutos);
				           	return 'ok';
				        } else {
				            return '<h2>Erro</h2>';
				        }	                    
	                } else {
	                    return "<h2>Erro ao encontrar a compra: Token inválido ".$token . '</h2>';
	                }
	            }
	        }
        }else{
	        return '<h2>Faltam parâmetros para concluir a transação!</h2>';
        }
    }
}
?>