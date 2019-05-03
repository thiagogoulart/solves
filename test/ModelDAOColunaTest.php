 <?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 20/02/2019
 
*/
include_once '../../site/conf.ini.php';
include_once '../../site/includes/functions.php';
include_once "../../site/model/dao/DAO.class.php";
include_once "../../site/model/dao/DAOColuna.class.php";
include_once "../../site/model/Cidade.class.php";
use PHPUnit\Framework\TestCase;

class ModelDAOColunaTest extends TestCase {
private static $dao;
	public static function setUpBeforeClass() : void{
		$CONNECTION = openConnectionBase();
		ModelDAOColunaTest::$dao = new DAO;
		ModelDAOColunaTest::$dao->setConnection($CONNECTION);
		ModelDAOColunaTest::$dao->setSequencePk(Cidade::$SEQUENCIA);
		ModelDAOColunaTest::$dao->setTabela(Cidade::$TABELA);
		ModelDAOColunaTest::$dao->setPk(Cidade::$PK);

		ModelDAOColunaTest::$dao->addColunaObrigatoria(1, "uf_id", "integer", false, null);
		ModelDAOColunaTest::$dao->addColunaObrigatoria(2, "nome", "string", true, null);
		ModelDAOColunaTest::$dao->addColunaObrigatoria(3, "sigla", "string", false, null);
		ModelDAOColunaTest::$dao->addColunaObrigatoria(4, "created_at", "timestamp", true, null);
		ModelDAOColunaTest::$dao->addColunaObrigatoria(5, "updated_at", "timestamp", true, null);
		ModelDAOColunaTest::$dao->addColuna(6, "removed", "boolean", true, null);
		ModelDAOColunaTest::$dao->addColunaObrigatoria(7, "removed_at", "timestamp", true, null);
		ModelDAOColunaTest::$dao->addColuna(8, "qtd_estimada_habitantes", "integer", false, null);
		ModelDAOColunaTest::$dao->addColuna(9, "qtd_estimada_area_kms", "double", false, null);
		ModelDAOColunaTest::$dao->addColunaObrigatoria(10, "ativo", "boolean", true, null);
		ModelDAOColunaTest::$dao->addColunaObrigatoria(11, "ativo_at", "timestamp", true, null);
		ModelDAOColunaTest::$dao->addColunaObrigatoria(12, "inativo_at", "timestamp", true, null);
		ModelDAOColunaTest::$dao->addColuna(13, "cep", "string", false, null);
		ModelDAOColunaTest::$dao->addColunaObrigatoria(14, "empresa_id", "integer", false, null);

		ModelDAOColunaTest::$dao->setColunaLabelOrder(2);
		ModelDAOColunaTest::$dao->addColunaLabelOrder(3);
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  
	}
	public function tearDown()  : void{
	  
	}
	public function testGetSearchSql(){
		$s = ModelDAOColunaTest::$dao->getColuna(2)->getSearchSql('abc DEF');
		$this->assertEquals(" UPPER(cidade.nome) LIKE '%ABC%' AND  UPPER(cidade.nome) LIKE '%DEF%' ", $s, 'String1.');
	}


}	