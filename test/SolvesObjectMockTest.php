 <?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 16/01/2020
 
*/

include_once 'src/Solves/Solves.php';
include_once 'src/SolvesDAO/SolvesObject.php';
include_once 'src/SolvesDAO/SolvesObjectMock.php';
include_once 'src/SolvesPay/SolvesObjectCompra.php';
include_once 'testMocks/SolvesObjectCompraMock.php';
include_once 'testMocks/SolvesObjectClienteMock.php';
use PHPUnit\Framework\TestCase;

class SolvesObjectMockTest extends TestCase {
	public static function setUpBeforeClass() : void{
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  
	}
	public function tearDown()  : void{
	  
	}
        public function testGettersAndSetters(){
                $con = \SolvesDAO\SolvesDAO::openConnectionMock();
                $obj = new SolvesObjectCompraMock($con);

                $mock = new \SolvesDAO\SolvesObjectMock($obj);
                $mock->vendedor = 'Thiago';
                $this->assertEquals("Thiago", $mock->vendedor, 'vendedor não bate.');

                $mock->valor_total = 10;
                $this->assertEquals(10, $mock->valor_total, 'SetDirect: Atribute direct access valor_total não bate.');
                $this->assertEquals(10, $mock->valorTotal, 'SetDirect: Atribute direct access valorTotal não bate.');
                $this->assertEquals(10, $mock->getValorTotal(), 'SetDirect: GetValorTotal não bate.');

                        $mock->valorTotal = 10;
                $this->assertEquals(10, $mock->valor_total, 'SetDirect valorTotal: Atribute direct access valor_total não bate.');
                $this->assertEquals(10, $mock->valorTotal, 'SetDirect valorTotal: Atribute direct access valorTotal não bate.');
                $this->assertEquals(10, $mock->getValorTotal(), 'SetDirect valorTotal: GetValorTotal não bate.');

                        $mock->vendedor='Alysson';
                $this->assertEquals('Alysson', $mock->vendedor, 'SetDirect: Atribute direct access vendedor não bate.');
                $this->assertEquals('Alysson', $mock->getVendedor(), 'SetDirect:getVendedor não bate.');

                        $mock->setValorTotal(20);
                $this->assertEquals(20, $mock->valor_total, 'using setter: Atribute direct access valor_total não bate.');
                $this->assertEquals(20, $mock->valorTotal, 'using setter: Atribute direct access valorTotal não bate.');
                $this->assertEquals(20, $mock->getValorTotal(), 'using setter: GetValorTotal não bate.');

                        $mock->setVendedor('Alysson Teixeira Mangos');
                $this->assertEquals('Alysson Teixeira Mangos', $mock->vendedor, 'using setter: Atribute direct access vendedor não bate.');
                $this->assertEquals('Alysson Teixeira Mangos', $mock->getVendedor(), 'using setter:getVendedor não bate.');

                //Atributo que possui getter e setter
                        $mock->anotacoes = 'Foi vendido.';
                $this->assertEquals('Foi vendido.', $mock->anotacoes, 'Atributo que possui getter e setter: Atribute direct access anotacoes não bate.');
                $this->assertEquals('Foi vendido.', $mock->getAnotacoes(), 'Atributo que possui getter e setter: getAnotacoes não bate.');
                //Atributo que possui getter e setter
                        $mock->setAnotacoes('NÃO Foi vendido.');
                $this->assertEquals('NÃO Foi vendido.', $mock->anotacoes, 'Atributo que possui getter e setter, usingSetter: Atribute direct access anotacoes não bate.');
                $this->assertEquals('NÃO Foi vendido.', $mock->getAnotacoes(), 'Atributo que possui getter e setter, usingSetter: getAnotacoes não bate.');


                //Atributo que possui getter e setter
                        $mock->createdAt = '2020-01-15';
                $this->assertEquals('2020-01-15', $mock->created_at, 'Atributo que possui getter e setter: Atribute direct access created_at não bate.');
                $this->assertEquals('2020-01-15', $mock->createdAt, 'Atributo que possui getter e setter: Atribute direct access createdAt não bate.');
                $this->assertEquals('2020-01-15', $mock->getCreatedAt(), 'Atributo que possui getter e setter: getCreatedAt não bate.');
                //Atributo que possui getter e setter
                        $mock->setCreatedAt('2020-01-16 08:00:00');
                $this->assertEquals('2020-01-16 08:00:00', $mock->created_at, 'Atributo que possui getter e setter, usingSetter: Atribute direct created_at anotacoes não bate.');
                $this->assertEquals('2020-01-16 08:00:00', $mock->createdAt, 'Atributo que possui getter e setter, usingSetter: Atribute direct createdAt anotacoes não bate.');
                $this->assertEquals('2020-01-16 08:00:00', $mock->getCreatedAt(), 'Atributo que possui getter e setter, usingSetter: getCreatedAt não bate.');
                //Atributo que possui getter e setter
                        $mock->created_at = '2020-01-17';
                $this->assertEquals('2020-01-17', $mock->created_at, 'Atributo que possui getter e setter: Atribute direct access created_at não bate.');
                $this->assertEquals('2020-01-17', $mock->createdAt, 'Atributo que possui getter e setter: Atribute direct access createdAt não bate.');
                $this->assertEquals('2020-01-17', $mock->getCreatedAt(), 'Atributo que possui getter e setter: getCreatedAt não bate.');

                //Atributo que possui getter e setter
                $this->assertEquals('Alysson Teixeira Mangos-NÃO Foi vendido.', $mock->vendedor_com_anotacoes, 'Atributo que possui getter e setter: Atribute direct access vendedor_com_anotacoes não bate.');
                $this->assertEquals('Alysson Teixeira Mangos-NÃO Foi vendido.', $mock->vendedorComAnotacoes, 'Atributo que possui getter e setter: Atribute direct access vendedorComAnotacoes não bate.');
                $this->assertEquals('Alysson Teixeira Mangos-NÃO Foi vendido.', $mock->getVendedorComAnotacoes(), 'Atributo que possui getter e setter: getVendedorComAnotacoes não bate.');
        }
        public function testFindById(){
                $con = \SolvesDAO\SolvesDAO::openConnectionMock();
                $obj = new SolvesObjectCompraMock($con);

                $mock = new \SolvesDAO\SolvesObjectMock($obj);
                $id = 1;
                $mock->mockMethod('findById', function($id) {return ($id==1? "SIM" : null);});
                $result = $mock->findById(1);
                $this->assertEquals("SIM", $result, 'RESULT de findById não bate.');
        }
        public function testSaveReturningId(){
                $con = \SolvesDAO\SolvesDAO::openConnectionMock();
                $obj = new SolvesObjectCompraMock($con);

                $mock = new \SolvesDAO\SolvesObjectMock($obj);
                $mock->mockDaoMethod('save', function() {return 999;});
                $result = $mock->saveReturningId();
                $this->assertEquals(999, $result, 'RESULT de saveReturningId não bate.');
                $this->assertEquals(999, $mock->getId(), 'RESULT de getId depois de ter usado o saveReturningId não bate.');
        }
        public function testToArray(){
                $con = \SolvesDAO\SolvesDAO::openConnectionMock();
                $obj = new SolvesObjectCompraMock($con);
                $obj->vendedor='Thiago';
                $obj->anotacoes='testando 1.2.3..';
                $obj->cliente_id=1;
                $obj->cliente_id_label='Vagner Silva';
                $obj->cliente_id_email='vagner_silva@test.com';

                $arr = $obj->toArray();
                $this->assertTrue(array_key_exists('cliente_id', $arr), 'cliente_id nao existe no array.');
                $this->assertTrue(array_key_exists('cliente_id_label', $arr), 'cliente_id_label nao existe no array. É label da classe join.');
                $this->assertTrue(array_key_exists('cliente_id_email', $arr), 'cliente_id_email nao existe no array. É label da classe join.');
        }
}