 <?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 29/07/2019
 
*/

include_once 'src/Solves/Solves.php';
include_once 'src/SolvesDAO/SolvesDAO.php';
include_once 'src/SolvesDAO/SolvesObject.php';
include_once 'src/SolvesPay/SolvesObjectCompra.php';
include_once 'testMocks/SolvesObjectCompraMock.php';
use PHPUnit\Framework\TestCase;

class SolvesDAOTest extends TestCase {
	public static function setUpBeforeClass() : void{
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  
	}
	public function tearDown()  : void{
	  
	}
    public function testGetSqlUpdate(){
            $con = \SolvesDAO\SolvesDAO::openConnectionMock();                
            $obj = new SolvesObjectCompraMock($con);
            $sql = $obj->getDao()->getSqlUpdate($obj, 7);

            $this->assertEquals('', $sql, 'SQL deveria ser vazio pois nenhum atributo foi alterado.');

            $obj->setVendedor('TESTE');
            $obj->beforeSaveAndUpdate();
            $sql = $obj->getDao()->getSqlUpdate($obj, 7);
            $this->assertEquals("UPDATE compra_mock SET vendedor='TESTE' WHERE compra_mock_id= 7;", $sql, 'SQL deveria ser vazio pois nenhum atributo foi alterado.');
    }
    public function testGetSqlSearchByParams(){
    		$userId = 1;
            $con = \SolvesDAO\SolvesDAO::openConnectionMock();                
            $obj = new SolvesObjectUserMock($con);
            $dao = $obj->getDao();

            //Nome vazio
            $dados = ["nome"=>"","page"=>"1"];
            $sql = $dao->getSqlSearchByParams($userId, $dados);
            $sqlExpected='(1=1)';
            $this->assertEquals($sqlExpected, $sql, 'SQL nao ficou como esperado ['.$sqlExpected.'].');

            //Nome com 1 palavra
            $dados = ["nome"=>"Thiago","page"=>"1"];
            $sql = $dao->getSqlSearchByParams($userId, $dados);
            $sqlExpected=' ( UPPER(user_mock.nome) LIKE \'%THIAGO%\' ) ';
            $this->assertEquals($sqlExpected, $sql, 'SQL nao ficou como esperado ['.$sqlExpected.'].');

            //Nome com 2 palavras separadas por 1 espaço
            $dados = ["nome"=>"Thiago Goulart","page"=>"1"];
            $sql = $dao->getSqlSearchByParams($userId, $dados);
            $sqlExpected=' ( UPPER(user_mock.nome) LIKE \'%THIAGO%\' AND  UPPER(user_mock.nome) LIKE \'%GOULART%\' ) ';
            $this->assertEquals($sqlExpected, $sql, 'SQL nao ficou como esperado ['.$sqlExpected.'].');

            //Nome com 2 palavras separadas por 2 espaços e 1 espaço no final
            $dados = new StdClass;
            $dados->nome ="Thiago  Goulart ";
            $dados->page = "1";
            $sql = $dao->getSqlSearchByParams($userId, $dados);
            $sqlExpected=' ( UPPER(user_mock.nome) LIKE \'%THIAGO%\' AND  UPPER(user_mock.nome) LIKE \'%GOULART%\' ) ';
            $this->assertEquals($sqlExpected, $sql, 'SQL nao ficou como esperado ['.$sqlExpected.'].');

            //Nome com 2 palavras separadas por 2 espaços e 1 espaço no final
            $dados = ["nome"=>"Thiago  Goulart ","page"=>"1"];
            $sql = $dao->getSqlSearchByParams($userId, $dados, false);
            $sqlExpected=' ( UPPER(user_mock.nome) LIKE \'%THIAGO%\' AND  UPPER(user_mock.nome) LIKE \'%GOULART%\' ) ';
            $this->assertEquals($sqlExpected, $sql, 'SQL nao ficou como esperado ['.$sqlExpected.'].');

            //Nome com 2 palavras separadas por 2 espaços e 1 espaço no final
            $dados = ["nome"=>"Thiago  Goulart ","page"=>"1"];
            $sql = $dao->getSqlSearchByParams($userId, $dados, true, true);
            $sqlExpected=' ( UPPER(user_mock.nome) LIKE \'%THIAGO%\' AND  UPPER(user_mock.nome) LIKE \'%GOULART%\' ) ';
            $this->assertEquals($sqlExpected, $sql, 'SQL nao ficou como esperado ['.$sqlExpected.'].');

            //Nome com 2 palavras separadas por 2 espaços e 1 espaço no final
            $dados = ["nome"=>"","page"=>"1"];
            $sql = $dao->getSqlSearchByParams($userId, $dados, true, true);
            $sqlExpected=null;
            $this->assertEquals($sqlExpected, $sql, 'SQL nao ficou como esperado ['.$sqlExpected.'].');
    }
}