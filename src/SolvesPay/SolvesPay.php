<?php
namespace SolvesPay;

/**
 * Class SolvesPay
 * @package SolvesPay
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 19/07/2019
 */
abstract class SolvesPay {
    /**
     * @var
     */
    protected $environment;
    /**
     * @var bool
     */
    protected $initialized=false;
    /**
     * @var SolvesPayCompra|null
     */
    protected $solvesCompra=null;

    /**
     * SolvesPay constructor.
     * @param SolvesPayCompra $solvesCompra
     */
    public function __construct(\SolvesPay\SolvesPayCompra $solvesCompra) {
		$this->solvesCompra = $solvesCompra;
		$this->setEnvironmentProduction();
	}

    /**
     * @return SolvesPayCompra|null
     */
    public function getSolvesCompra(): ?\SolvesPay\SolvesPayCompra{
		return $this->solvesCompra;
	}

    /**
     *
     */
    public function setEnvironmentProduction(){
		$this->environment = 'production';
	}

    /**
     *
     */
    public function setEnvironmentSandbox(){
		$this->environment = 'sandbox';
	}

    /**
     * @return bool
     */
    public function isSandbox(): bool{
		return ($this->environment == 'sandbox');
	}

    /**
     * @return bool
     */
    public function isApp(): bool{
		return ($this->solvesCompra->getConnectionDao()->isApp());
	}

    /**
     * @param bool $forceInitialization
     * @return mixed
     */
    public abstract function init($forceInitialization=false);

    /**
     * @return mixed
     */
    public abstract function solicitarPagamento();
	//public abstract function trataNotificacaoSucesso($token, $payerId);

}	
?>