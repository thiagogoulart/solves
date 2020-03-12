 <?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 29/07/2019
 
*/

include_once 'src/Solves/Solves.php';
include_once 'src/SolvesDAO/SolvesObject.php';
include_once 'testMocks/SolvesObjectUserMock.php';
use PHPUnit\Framework\TestCase;

class SolvesObjectUserTest extends TestCase {
	public static function setUpBeforeClass() : void{
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  
	}
	public function tearDown()  : void{
	  
	}
	public function testToArray(){
                $con = \SolvesDAO\SolvesDAO::openConnectionMock();
                $empresaId = 1;
                $createdAt = '2020-03-12 09:00:00';
                $email = 'thiago_gsg@hotmail.com';
                $senha='abc';
                $nome='Thiago Goulart';
                $data_nascimento='1990-07-31';
                $img=null;
                $agree=1;
                $cidade_id=1;
                $uf_id=1;
		$obj = SolvesObjectUserMock::create($con, $empresaId, $createdAt, $email, $senha, $nome, $data_nascimento, $img, $agree, $cidade_id,$uf_id);

		$arr = $obj->toArray();
                $this->assertNotNull($arr, 'Retornado NULL em toArray.');
                $this->assertTrue(array_key_exists('nome',$arr), 'Valor de nome deveria ser retornado toArray, porém não está presente no array.');
                $this->assertTrue(array_key_exists('avatar',$arr), 'Valor de avatar deveria ser retornado toArray, porém não está presente no array.');
                $this->assertFalse(array_key_exists('senha',$arr), 'Valor de senha deveria estar oculto no toArray, porém retornou ['.(array_key_exists('senha',$arr) ? $arr['senha'] : 'NULL').'].');
                $this->assertFalse(array_key_exists('senha_label',$arr), 'Valor de senha_label deveria estar oculto no toArray, porém retornou ['.(array_key_exists('senha_label',$arr) ? $arr['senha_label'] : 'NULL').'].');
                $this->assertFalse(array_key_exists('img',$arr), 'Valor de img deveria estar oculto no toArray, porém retornou ['.(array_key_exists('img',$arr) ? $arr['img'] : 'NULL').'].');
        }
}