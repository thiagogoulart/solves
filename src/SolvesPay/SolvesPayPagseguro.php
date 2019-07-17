<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 06/05/2019
 */ 
namespace SolvesPay;

class SolvesPayPagseguro {

	private $environment;
	private $session;
	private $initialized=false;
	private $paymentRegister;

	public function __construct() {
		$this->setEnvironmentProduction();
	}

	public function setEnvironmentProduction(){
		$this->environment = 'production';
	}
	public function setEnvironmentSandbox(){
		$this->environment = 'sandbox';
	}
	public function getSession(){
		return $this->session;
	}
	public function getPaymentRegister(){
		return $this->paymentRegister;
	}
	public function getCredentials(){
		return \PagSeguro\Configuration\Configure::getAccountCredentials();
	}
	private function getPhone($ddd, $telefone){
		/** @var \PagSeguro\Domains\Phone $phone */
		return new \PagSeguro\Domains\Phone($ddd,$telefone, \PagSeguro\Enum\Authorization\PhoneEnum::MOBILE);
	}
	private function getItens($compraProdutos){
		$itens = array();
		foreach($compraProdutos as $cp){
			$itens[] = $this->getItem($cp);
		}
		return $itens;
	}
	private function getItem($compraProduto){
		$item = new \PagSeguro\Domains\Item();
		$item->setAmount($compraProduto->getValorFinalPay());
		$item->setDescription($compraProduto->getProdutoLabelPay());
		$item->setId($compraProduto->getId());
		$item->setQuantity($compraProduto->getQuantidade());
		$item->setShippingCost(0);
		$item->setWeight(0);
		return $item;
	}
	public function init($forceInitialization=false){
		if(!$this->initialized || $forceInitialization){
			try {
			    \PagSeguro\Library::initialize();
			} catch (Exception $e) {
			    die($e);
			}
			\PagSeguro\Library::cmsVersion()->setName(SYSTEM_NAME)->setRelease(VERSAO);
			\PagSeguro\Library::moduleVersion()->setName(SYSTEM_NAME)->setRelease(VERSAO);
			\PagSeguro\Configuration\Configure::setEnvironment($this->environment);
			\PagSeguro\Configuration\Configure::setAccountCredentials(PAGSEGURO_EMAIL,PAGSEGURO_TOKEN);
			\PagSeguro\Configuration\Configure::setCharset(DEFAULT_CHARSET);
			\PagSeguro\Configuration\Configure::setLog(true, PAGSEGURO_PATH_LOG);

			try {
			    $this->session = \PagSeguro\Services\Session::create($this->getCredentials());
			    $this->initialized = true;
			} catch (Exception $e) {
			    die($e->getMessage());
			}
		}
	}
	private function getDocumentCpf($cpf){
		/** @var \PagSeguro\Domains\Document $document */
		return new \PagSeguro\Domains\Document('CPF',$cpf);
	}


	public function doPay($cliente, $compra, $compraProdutos){
		$this->init();
		$payment = new \PagSeguro\Domains\Requests\Payment();

		/**
		 * Nome completo do comprador. Especifica o nome completo do comprador que está realizando o pagamento. Este campo é
		 * opcional e você pode enviá-lo caso já tenha capturado os dados do comprador em seu sistema e queira evitar que ele
		 * preencha esses dados novamente no PagSeguro.
		 *
		 * Presença: Opcional.
		 * Tipo: Texto.
		 * Formato: No mínimo duas sequências de caracteres, com o limite total de 50 caracteres.
		 *
		 * @var string $senderName
		 */
		$payment->setSender()->setName($cliente->getNome());

		/**
		 * E-mail do comprador. Especifica o e-mail do comprador que está realizando o pagamento. Este campo é opcional e você
		 * pode enviá-lo caso já tenha capturado os dados do comprador em seu sistema e queira evitar que ele preencha esses
		 * dados novamente no PagSeguro.
		 *
		 * Presença: Opcional.
		 * Tipo: Texto.
		 * Formato: um e-mail válido (p.e., usuario@site.com.br), com no máximo 60 caracteres.
		 *
		 * @var string $senderEmail
		 */
		$payment->setSender()->setEmail($cliente->getEmail());

		/** @var \PagSeguro\Domains\Phone $phone */
		$payment->setSender()->setPhone()->instance($this->getPhone($cliente->getFone1Ddd(), $cliente->getFone1()));

		/** @var \PagSeguro\Domains\Document $document */
		$payment->setSender()->setDocument()->instance($this->getDocumentCpf($cliente->getCpf()));

		/** @var \PagSeguro\Domains\Address $address 
		//TODO
		$payment->setShipping()->setAddress()->instance($address);
		*/

		/** @var \PagSeguro\Domains\ShippingCost $shippingCost 
		//TODO
		$payment->setShipping()->setCost()->instance($shippingCost);*/

		/** @var \PagSeguro\Domains\ShippingType $shippingType 
		//TODO
		$payment->setShipping()->setType()->instance($shippingType);
		*/

		foreach($compraProdutos as $compraProduto){
			$payment->addItems()->withParameters(
			    $compraProduto->getId(),
			    $compraProduto->getProdutoLabelPay(),
			    $compraProduto->getQuantidade(),
			    $compraProduto->getValorFinalPay(),
			    null,
			    null
			);
		}


		/**
		 * Moeda utilizada. Indica a moeda na qual o pagamento será feito. No momento, a única opção disponível é BRL (Real).
		 *
		 * Presença: Obrigatória.
		 * Tipo: Texto.
		 * Formato: Case sensitive. Somente o valor BRL é aceito.
		 *
		 * @var string $currency
		 */
		$payment->setCurrency('BRL');

		/**
		 * Valor extra. Especifica um valor extra que deve ser adicionado ou subtraído ao valor total do pagamento. Esse valor
		 * pode representar uma taxa extra a ser cobrada no pagamento ou um desconto a ser concedido, caso o valor seja
		 * negativo.
		 *
		 * Presença: Opcional.
		 * Tipo: Número.
		 * Formato: Decimal (positivo ou negativo), com duas casas decimais separadas por ponto (p.e., 1234.56 ou -1234.56),
		 * maior ou igual a -9999999.00 e menor ou igual a 9999999.00. Quando negativo, este valor não pode ser maior ou igual
		 * à soma dos valores dos produtos.
		 *
		 * @var string $extraAmount
		 //TODO
		$payment->setExtraAmount($extraAmount);
		 */

		/**
		 * Código de referência. Define um código para fazer referência ao pagamento. Este código fica associado à transação
		 * criada pelo pagamento e é útil para vincular as transações do PagSeguro às vendas registradas no seu sistema.
		 *
		 * Presença: Opcional.
		 * Tipo: Texto.
		 * Formato: Livre, com o limite de 200 caracteres.
		 *
		 * @var string $reference
		 */
		$payment->setReference($compra->getId());

		/**
		 * URL de redirecionamento após o pagamento. Determina a URL para a qual o comprador será redirecionado após o final do
		 * fluxo de pagamento. Este parâmetro permite que seja informado um endereço de específico para cada pagamento
		 * realizado.
		 *
		 * Presença: Opcional.
		 * Tipo: Texto.
		 * Formato: Uma URL válida, com limite de 255 caracteres.
		 *
		 * @var string $redirectUrl
		 */
		$payment->setRedirectUrl(PAGSEGURO_URL_REDIRECT);

		/**
		 * URL para envio de notificações sobre o pagamento. Determina a URL para a qual o PagSeguro enviará os códigos de
		 * notificação relacionados ao pagamento. Toda vez que houver uma mudança no status da transação e que demandar sua
		 * atenção, uma nova notificação será enviada para este endereço.
		 *
		 * Presença: Opcional.
		 * Tipo: Texto.
		 * Formato: Uma URL válida, com limite de 255 caracteres.
		 *
		 * @var string $notificationUrl
		 */
		$payment->setNotificationUrl(PAGSEGURO_URL_NOTIFICATION);
		

		/* 
		 * ???
		 * Set discount by payment method
		//TODO
		$payment->addPaymentMethod()->withParameters(
		    PagSeguro\Enum\PaymentMethod\Group::CREDIT_CARD,
		    PagSeguro\Enum\PaymentMethod\Config\Keys::DISCOUNT_PERCENT,
		    10.00
		); */

		/*
		 * ???
		 * Set max installments without fee
		 * /
		$payment->addPaymentMethod()->withParameters(
		    PagSeguro\Enum\PaymentMethod\Group::CREDIT_CARD,
		    PagSeguro\Enum\PaymentMethod\Config\Keys::MAX_INSTALLMENTS_NO_INTEREST,
		    PAGSEGURO_NUMPARCELAS_SEMTAXA
		);

		/ *
		 * ???
		 * Set max installments
		
		$payment->addPaymentMethod()->withParameters(
		    PagSeguro\Enum\PaymentMethod\Group::CREDIT_CARD,
		    PagSeguro\Enum\PaymentMethod\Config\Keys::MAX_INSTALLMENTS_LIMIT,
		    PAGSEGURO_NUMPARCELAS_MAXIMO
		); */

		/*
		 * ???
		 * Set accepted payments methods group
		 */
		$payment->acceptPaymentMethod()->groups(
		    \PagSeguro\Enum\PaymentMethod\Group::CREDIT_CARD,
		    \PagSeguro\Enum\PaymentMethod\Group::BOLETO,
		    \PagSeguro\Enum\PaymentMethod\Group::BALANCE
		);

		/*
		 * ???
		 * Set accepted payments methods
		 */
		$payment->acceptPaymentMethod()->name(\PagSeguro\Enum\PaymentMethod\Name::DEBITO_ITAU);

		/*
		 * ???
		 * Exclude accepted payments methods group
		//TODO
		$payment->excludePaymentMethod()->group(\PagSeguro\Enum\PaymentMethod\Group::BOLETO);
 */

		/*
		 * Após realizar uma chamada com sucesso, você deve direcionar o comprador para o fluxo de
		 * pagamento, usando a url de pagamento retornado.
		 */
		try {
		    /** @var \PagSeguro\Domains\Requests\Payment $payment */
		    $this->paymentRegister = $payment->register( $this->getCredentials());
		    return $this->paymentRegister;
		} catch (Exception $e) {
		    die($e->getMessage());
		}
		return null;
	}

}
?>