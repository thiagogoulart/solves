<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 19/07/2019
 */ 
namespace SolvesPay;

abstract class SolvesPay {
	protected $environment;
	protected $initialized=false;
	protected $solvesCompra=null;

	public function __construct(\SolvesPay\SolvesPayCompra $solvesCompra) {
		$this->solvesCompra = $solvesCompra;
		$this->setEnvironmentProduction();
	}

	public function getSolvesCompra(){
		return $this->solvesCompra;
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
	public function isApp(){
		return ($this->solvesCompra->getConnectionDao()->isApp());
	}
	public abstract function init($forceInitialization=false);
	public abstract function solicitarPagamento();
	//public abstract function trataNotificacaoSucesso($token, $payerId);

}	
?>