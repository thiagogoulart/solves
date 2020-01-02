 <?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 02/01/2020
 
*/

include_once 'src/Solves/Solves.php';
include_once 'src/SolvesUi/SolvesUi.php';
use PHPUnit\Framework\TestCase;

class SolvesUiTest extends TestCase {
	public static function setUpBeforeClass() : void{
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  
	}
	public function tearDown()  : void{
	  
	}
	public function testIsPublicUrl(){
		$publicUrls = ['index','cadastro','login','esqueci_senha','blog','contato','faq','termo_uso','termo_privacidade'];
		\SolvesUi\SolvesUi::setPublicUrls($publicUrls);

		$params = array('index','cadastro','login','esqueci_senha','blog','blog/2020-teste','contato','faq','termo_uso','termo_privacidade','home','meu_perfil','plano','plano/','plano/1','plano/1/action');
		$expected = array(true,true,true,true,true,true,true,true,true,true,false,false,false,false,false,false);
		$qtd = count($params);
		for($i=0;$i!=$qtd;$i++){ 
			$p = $params[$i];
			$s = \SolvesUi\SolvesUi::isPublicUrl($p);
			$this->assertEquals($expected[$i], $s, 'URL ['.$p.'] FOI considerada '.($s?'PUBLICA':'RESTRITA').'. Resultado esperado era:'.($expected[$i]?'PUBLICA':'RESTRITA'));
		}
	}
	public function testIsRestrictedUrl(){
		$publicUrls = ['index','cadastro','login','esqueci_senha','blog','contato','faq','termo_uso','termo_privacidade'];
		\SolvesUi\SolvesUi::setPublicUrls($publicUrls);

		$params = array('index','cadastro','login','esqueci_senha','blog','blog/2020-teste','contato','faq','termo_uso','termo_privacidade','home','meu_perfil','plano','plano/','plano/1','plano/1/action');
		$expected = array(false,false,false,false,false,false,false,false,false,false,true,true,true,true,true,true);
		$qtd = count($params);
		for($i=0;$i!=$qtd;$i++){ 
			$p = $params[$i];
			$s = \SolvesUi\SolvesUi::isRestrictedUrl($p);
			$this->assertEquals($expected[$i], $s, 'URL ['.$p.'] FOI considerada '.($s?'RESTRITA':'PUBLICA').'. Resultado esperado era:'.($expected[$i]?'RESTRITA':'PUBLICA'));
		}
	}
}