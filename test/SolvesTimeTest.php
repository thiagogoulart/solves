 <?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 30/07/2019
 
*/

include_once 'src/Solves/Solves.php';
include_once 'src/Solves/SolvesTime.php';
use PHPUnit\Framework\TestCase;

class SolvesTimeTest extends TestCase {
	public static function setUpBeforeClass() : void{
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  
	}
	public function tearDown()  : void{
	  
	}
	public function testAddDiasToTimestamp(){
		$params = array('2019-10-09 07:38:00','2019-10-09 07:38:00', '2019-10-09 07:38:00','2019-10-09 07:38:00','2019-10-09 07:38:00','2019-11-08');
		$paramsDays = array(2,4,10,6,24,30);
		$expected = array('2019-10-11 07:38:00','2019-10-13 07:38:00','2019-10-19 07:38:00', '2019-10-15 07:38:00', '2019-11-02 07:38:00','2019-12-08');

		//Solves\SolvesTime::addDiasToTimestamp('2019-11-08', '30')
		$qtd = count($params);
		for($i=0;$i!=$qtd;$i++){ 
			$p = $params[$i];
			$d = $paramsDays[$i];
			$s = \Solves\SolvesTime::addDiasToTimestamp($p, $d);
			$this->assertEquals($expected[$i], $s, 'Timestamp ['.$p.'], adicionando ['.$d.'] dias, resultou em Timestamp ['.$s.']. Timestamp não ficou como esperada:'.$expected[$i]);
		}
	}
	public function testDiffDatasEmDias(){
		$params = array('2019-10-09 07:38:00','2019-10-09 07:38:00', '2019-10-09 07:38:00','2019-10-09 07:38:00','2019-10-09 07:38:00');
		$paramsDays = array('2019-10-11 07:38:00','2019-10-13 07:38:00','2019-10-19 07:38:00', '2019-10-15 07:38:00', '2019-11-02 07:38:00');
		$expected = array(2,4,10,6,24);
		$qtd = count($params);
		for($i=0;$i!=$qtd;$i++){ 
			$p = $params[$i];
			$d = $paramsDays[$i];
			$s = \Solves\SolvesTime::diffDatasEmDias($p, $d);
			$this->assertEquals($expected[$i], $s, 'inicial ['.$p.'], final ['.$d.'], resultou em qtd dias ['.$s.']. Qtd de dias não ficou como esperada:'.$expected[$i]);
		}
	}

}