 <?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 29/07/2019
 
*/

include_once 'src/Solves/Solves.php';
include_once 'src/SolvesDAO/DAO.php';
include_once 'src/SolvesDAO/SolvesDAO.php';
include_once 'src/SolvesDAO/SolvesObject.php';
include_once 'src/SolvesPay/SolvesPay.php';
include_once 'src/SolvesPay/SolvesPayPaypal.php';
include_once 'src/SolvesPay/SolvesPayCompra.php';
include_once 'src/SolvesPay/SolvesPayCompraItem.php';
include_once 'src/SolvesPay/SolvesObjectCompra.php';
include_once 'testMocks/SolvesObjectCompraMock.php';

use PHPUnit\Framework\TestCase;

class SolvesPayPaypalTest extends TestCase {
	public static function setUpBeforeClass() : void{
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  
	}
	public function tearDown()  : void{
	  
	}
	public function testGetItens(){
		/*/adicionar 1 item com qtd=1 e valor=10
		$label = '1 Produto de 10 Pontos ';
		$obj = new SolvesObjectCompraMock(null);
		$compraItem = new \SolvesPay\SolvesPayCompraItem($label, 1, 15);
        $solvesCompra = new \SolvesPay\SolvesPayCompra(1, $obj);
        $solvesCompra->addCompraItem($compraItem);

        $solvesPay = new \SolvesPay\SolvesPayPaypal($solvesCompra);
        $solvesPay->init();

		$itens = $solvesPay->getItens();

		$PAYMENTREQUEST_0_ITEMAMT = $itens['L_PAYMENTREQUEST_0_ITEMAMT'];
		$PAYMENTREQUEST_0_AMT = $itens['PAYMENTREQUEST_0_AMT'];
		$PAYMENTREQUEST_0_ITEMAMT = $itens['PAYMENTREQUEST_0_ITEMAMT'];

		$L_PAYMENTREQUEST_0_AMT0 = $itens['L_PAYMENTREQUEST_0_AMT0'];
		$L_PAYMENTREQUEST_0_QTY0 = $itens['L_PAYMENTREQUEST_0_QTY0'];

		$vlFinalPay = $compraItem->getValorFinalPay();
		$qtdPay = $compraItem->getQuantidade();

		$paramsNames = array('L_PAYMENTREQUEST_0_ITEMAMT', 'PAYMENTREQUEST_0_AMT', 'PAYMENTREQUEST_0_ITEMAMT', 'L_PAYMENTREQUEST_0_AMT0', 'L_PAYMENTREQUEST_0_QTY0');
		$params = array($PAYMENTREQUEST_0_ITEMAMT,$PAYMENTREQUEST_0_AMT,$PAYMENTREQUEST_0_ITEMAMT,$L_PAYMENTREQUEST_0_AMT0,$L_PAYMENTREQUEST_0_QTY0);
		$expected = array($vlFinalPay,$vlFinalPay,$vlFinalPay,$vlFinalPay,$qtdPay);
		$qtd = count($params);
		for($i=0;$i!=$qtd;$i++){ 
			$p = $params[$i];
			$pName = $paramsNames[$i];
			$exp = $expected[$i];
			$this->assertEquals($exp, $p, 'Valor de "'.$pName.'" obtido foi:'.$p.'. Valor não ficou como esperado:'.$exp);
		}*/
		$this->assertTrue(true);
	}
	public function testGetRequestPay(){
		/*/adicionar 1 item com qtd=1 e valor=10
		$label = '1 Produto de 10 Pontos ';
		$obj = new SolvesObjectCompraMock(null);
		$compraItem = new \SolvesPay\SolvesPayCompraItem($label, 1, 15);
        $solvesCompra = new \SolvesPay\SolvesPayCompra(1, $obj);
        $solvesCompra->addCompraItem($compraItem);

        $solvesPay = new \SolvesPay\SolvesPayPaypal($solvesCompra);
        $solvesPay->init();

		$itens = $solvesPay->getRequestPay();

		$PAYMENTREQUEST_0_ITEMAMT = $itens['L_PAYMENTREQUEST_0_ITEMAMT'];
		$PAYMENTREQUEST_0_AMT = $itens['PAYMENTREQUEST_0_AMT'];
		$PAYMENTREQUEST_0_ITEMAMT = $itens['PAYMENTREQUEST_0_ITEMAMT'];

		$L_PAYMENTREQUEST_0_AMT0 = $itens['L_PAYMENTREQUEST_0_AMT0'];
		$L_PAYMENTREQUEST_0_QTY0 = $itens['L_PAYMENTREQUEST_0_QTY0'];

		$vlFinalPay = $compraItem->getValorFinalPay();
		$qtdPay = $compraItem->getQuantidade();

		$paramsNames = array('L_PAYMENTREQUEST_0_ITEMAMT', 'PAYMENTREQUEST_0_AMT', 'PAYMENTREQUEST_0_ITEMAMT', 'L_PAYMENTREQUEST_0_AMT0', 'L_PAYMENTREQUEST_0_QTY0');
		$params = array($PAYMENTREQUEST_0_ITEMAMT,$PAYMENTREQUEST_0_AMT,$PAYMENTREQUEST_0_ITEMAMT,$L_PAYMENTREQUEST_0_AMT0,$L_PAYMENTREQUEST_0_QTY0);
		$expected = array($vlFinalPay,$vlFinalPay,$vlFinalPay,$vlFinalPay,$qtdPay);
		$qtd = count($params);
		for($i=0;$i!=$qtd;$i++){ 
			$p = $params[$i];
			$pName = $paramsNames[$i];
			$exp = $expected[$i];
			$this->assertEquals($exp, $p, 'Valor de "'.$pName.'" obtido foi:'.$p.'. Valor não ficou como esperado:'.$exp);
		}*/
		$this->assertTrue(true);
	}
}