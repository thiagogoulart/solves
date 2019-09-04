<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 02/09/2019
 */ 
namespace SolvesPay;

class SolvesPayCompraItem {

	protected $label;
	protected $quantidade;
	protected $valorFinal;

	public function __construct($label, $quantidade, $valorFinal) {
		$this->label = $label;
		$this->quantidade = $quantidade;
		$this->valorFinal = $valorFinal;
	}

	public function getLabel() {return $this->label;}
	public function getQuantidade() {return $this->quantidade;}
	public function getValorFinal() {return $this->valorFinal;}

	public function getLabelPay() {return substr($this->label, 0,99);}
	public function getValorFinalPay() {return \Solves\Solves::getDoubleValue($this->valorFinal);}

}
?>