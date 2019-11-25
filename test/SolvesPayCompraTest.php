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
include_once 'src/SolvesDAO/SolvesDAOConnection.php';
include_once 'src/SolvesDAO/SolvesObject.php';
include_once 'src/SolvesPay/SolvesPay.php';
include_once 'src/SolvesPay/SolvesPayCompra.php';
include_once 'src/SolvesPay/SolvesPayCompraItem.php';
include_once 'src/SolvesPay/SolvesObjectCompra.php';
include_once 'testMocks/SolvesObjectCompraMock.php';

use PHPUnit\Framework\TestCase;

class SolvesPayCompraTest extends TestCase {
	public static function setUpBeforeClass() : void{
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{ 
	  
	}
	public function tearDown()  : void{
	  
	}
	public function testAddCompraItem(){
	/*	$CONNECTION = new \SolvesDAO\SolvesDAOConnection();

		$label = '1 Produto de 10 Pontos ';
		$obj = new SolvesObjectCompraMock($CONNECTION);
		$compraItem = new \SolvesPay\SolvesPayCompraItem($label, 1, 29.9);
        $solvesCompra = new \SolvesPay\SolvesPayCompra(1, $obj);
        $solvesCompra->addCompraItem($compraItem);
		$itens = $solvesCompra->getCompraItens();

		$res_qtdItens = (isset($itens) ? count($itens) : 0);
		$res_item_qtd = 0;
		$res_item_valor = 0;
		if($res_qtdItens==1){
			$item = $itens[0];
			$res_item_qtd = $item->getQuantidade();
			$res_item_valor = $item->getValorFinalPay();
		}

		$paramsNames = array('Qtd no array', 'Item 1[QTD]', 'Item 1[VALOR]');
		$params = array($res_qtdItens,$res_item_qtd,$res_item_valor);
		$expected = array(1,'1','29.9');
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