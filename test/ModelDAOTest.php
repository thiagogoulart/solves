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

class ModelDAOTest extends TestCase {
private static $dao;
	public static function setUpBeforeClass() : void{
		$CONNECTION = openConnectionBase();
		ModelDAOTest::$dao = new DAO;
		ModelDAOTest::$dao->setConnection($CONNECTION);
		ModelDAOTest::$dao->setSequencePk(Cidade::$SEQUENCIA);
		ModelDAOTest::$dao->setTabela(Cidade::$TABELA);
		ModelDAOTest::$dao->setPk(Cidade::$PK);

		ModelDAOTest::$dao->addColunaObrigatoria(1, "uf_id", "integer", true, null);
		ModelDAOTest::$dao->addColunaObrigatoria(2, "nome", "string", true, null);
		ModelDAOTest::$dao->addColunaObrigatoria(3, "sigla", "string", false, null);
		ModelDAOTest::$dao->addColunaObrigatoria(4, "created_at", "timestamp", true, null);
		ModelDAOTest::$dao->addColunaObrigatoria(5, "updated_at", "timestamp", true, null);
		ModelDAOTest::$dao->addColuna(6, "removed", "boolean", true, null);
		ModelDAOTest::$dao->addColunaObrigatoria(7, "removed_at", "timestamp", true, null);
		ModelDAOTest::$dao->addColuna(8, "qtd_estimada_habitantes", "integer", false, null);
		ModelDAOTest::$dao->addColuna(9, "qtd_estimada_area_kms", "double", false, null);
		ModelDAOTest::$dao->addColunaObrigatoria(10, "ativo", "boolean", true, null);
		ModelDAOTest::$dao->addColunaObrigatoria(11, "ativo_at", "timestamp", true, null);
		ModelDAOTest::$dao->addColunaObrigatoria(12, "inativo_at", "timestamp", true, null);
		ModelDAOTest::$dao->addColuna(13, "cep", "string", false, null);
		ModelDAOTest::$dao->addColunaObrigatoria(14, "empresa_id", "integer", false, null);

		ModelDAOTest::$dao->setColunaLabelOrder(2);
		ModelDAOTest::$dao->addColunaLabelOrder(3);
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  
	}
	public function tearDown()  : void{
	  
	}
	public function testGetSqlSearchByParams(){
		$params = array("nome"=>"Teste ABc","uf_id"=>2);
		$s = ModelDAOTest::$dao->getSqlSearchByParams(1, $params);
		$this->assertEquals(" ( UPPER(cidade.nome) LIKE '%TESTE%' AND  UPPER(cidade.nome) LIKE '%ABC%' ) AND ( cidade.uf_id = 2 ) ", $s, 'String1.');

		$params = array("nome"=>"Campo Grande","uf_id"=>12);
		$s = ModelDAOTest::$dao->getSqlSearchByParams(1, $params);
		$this->assertEquals(" ( UPPER(cidade.nome) LIKE '%CAMPO%' AND  UPPER(cidade.nome) LIKE '%GRANDE%' ) AND ( cidade.uf_id = 12 ) ", $s, 'String2.');
	}



}	