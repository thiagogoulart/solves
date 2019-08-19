 <?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 29/07/2019
 
*/

include_once 'src/Solves/Solves.php';
use PHPUnit\Framework\TestCase;

class SolvesTest extends TestCase {
	public static function setUpBeforeClass() : void{
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  
	}
	public function tearDown()  : void{
	  
	}
	public function testRemoveAcentos(){
		$params = array('coração de cachorro','NoRmaliZação', 'AÇÃO','pré-AÇÃO');
		$expected = array('coracao de cachorro','NoRmaliZacao', 'ACAO', 'pre-ACAO');
		$qtd = count($params);
		for($i=0;$i!=$qtd;$i++){ 
			$p = $params[$i];
			$s = \Solves\Solves::removeAcentos($p);
			$this->assertEquals($expected[$i], $s, 'String ['.$p.'] String ['.$s.']. String não ficou como esperada:'.$expected[$i]);
		}
	}

	public function testGetUrlNormalizada(){
		$params = array("/index","/index_teste","/index_teste/1/2/3","/index _teste/1/2/3 ",'coração de cachorro','CORAÇÃO de cachorro');
		$expected = array("/index","/index_teste","/index_teste/1/2/3","/index-_teste/1/2/3-", 'coracao-de-cachorro', 'coracao-de-cachorro');
		$qtd = count($params);
		for($i=0;$i!=$qtd;$i++){ 
			$p = $params[$i];
			$s = \Solves\Solves::getUrlNormalizada($p);
			$this->assertEquals($expected[$i], $s, 'URL ['.$p.'] ficou ['.$s.']. Url não ficou como esperada:'.$expected[$i]);
		}
	}

	public function testGetUrlName(){
		$params = array("/index?p=1","/index/p2?p=1","/index/p2#p=1","/index?p=1/p2","/index#p=1/p2","autonomos/ms/campo-grande/construcao/pedreiro");
		$expected = array("index","index/p2","index/p2","index","index", "autonomos/ms/campo-grande/construcao/pedreiro");
		$qtd = count($params);
		for($i=0;$i!=$qtd;$i++){ 
			$p = $params[$i];
			$s = \Solves\Solves::getUrlName('',$p, false);
			$this->assertEquals($expected[$i], $s, 'URL ['.$p.'] ficou ['.$s.']. Url não ficou como esperada:'.$expected[$i]);
		}
	}

	public function testGetUrlNameArray(){
		$params = array("index","index/p2","index/p2","index","index", "autonomos/ms/campo-grande/construcao/pedreiro");
		$expected = array(1,2,2,1,1,5);
		$qtd = count($params);
		for($i=0;$i!=$qtd;$i++){ 
			$p = $params[$i];
			$arr = \Solves\Solves::getUrlNameArray($p);
			$s = count($arr);
			$this->assertEquals($expected[$i], $s, 'Quantidade esperada de posições no array para ['.$p.'] ficou ['.$s.']. Array não ficou com qtd esperada:'.$expected[$i]);
		}
	}

	public function testGetUrlNameViewPath(){
		$params = array("filea","tree/filea","filea/fileb","tree/filea/fileb");
		$expected = array("views/filea.php","views/tree/filea.php","views/filea.php","views/tree/filea.php");
		$qtd = count($params);
		for($i=0;$i!=$qtd;$i++){ 
			$p = $params[$i];
			$s = \Solves\Solves::getUrlNameViewPath($p);
			$this->assertEquals($expected[$i], $s, 'URL ['.$p.'] ficou ['.$s.']. Url não ficou como esperada:'.$expected[$i]);
		}
	}
}