 <?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 29/07/2019
 
*/

include_once 'src/Solves/Solves.php';
include_once 'src/SolvesDAO/SolvesObject.php';
include_once 'src/SolvesPay/SolvesObjectCompra.php';
include_once 'testMocks/SolvesObjectCompraMock.php';
use PHPUnit\Framework\TestCase;

class SolvesObjectCompraTest extends TestCase {
	public static function setUpBeforeClass() : void{
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  
	}
	public function tearDown()  : void{
	  
	}
	public function testGettersSetters(){
		$con = null;
		$obj = new SolvesObjectCompraMock($con);

		$obj->valor_total = 10;
        $this->assertEquals(10, $obj->valor_total, 'SetDirect: Atribute direct access valor_total não bate.');
        $this->assertEquals(10, $obj->valorTotal, 'SetDirect: Atribute direct access valorTotal não bate.');
        $this->assertEquals(10, $obj->getValorTotal(), 'SetDirect: GetValorTotal não bate.');

		$obj->valorTotal = 10;
        $this->assertEquals(10, $obj->valor_total, 'SetDirect valorTotal: Atribute direct access valor_total não bate.');
        $this->assertEquals(10, $obj->valorTotal, 'SetDirect valorTotal: Atribute direct access valorTotal não bate.');
        $this->assertEquals(10, $obj->getValorTotal(), 'SetDirect valorTotal: GetValorTotal não bate.');

		$obj->vendedor='Alysson';
        $this->assertEquals('Alysson', $obj->vendedor, 'SetDirect: Atribute direct access vendedor não bate.');
        $this->assertEquals('Alysson', $obj->getVendedor(), 'SetDirect:getVendedor não bate.');

		$obj->setValorTotal(20);
        $this->assertEquals(20, $obj->valor_total, 'using setter: Atribute direct access valor_total não bate.');
        $this->assertEquals(20, $obj->valorTotal, 'using setter: Atribute direct access valorTotal não bate.');
        $this->assertEquals(20, $obj->getValorTotal(), 'using setter: GetValorTotal não bate.');

		$obj->setVendedor('Alysson Teixeira Mangos');
        $this->assertEquals('Alysson Teixeira Mangos', $obj->vendedor, 'using setter: Atribute direct access vendedor não bate.');
        $this->assertEquals('Alysson Teixeira Mangos', $obj->getVendedor(), 'using setter:getVendedor não bate.');

        //Atributo que possui getter e setter
		$obj->anotacoes = 'Foi vendido.';
        $this->assertEquals('Foi vendido.', $obj->anotacoes, 'Atributo que possui getter e setter: Atribute direct access anotacoes não bate.');
        $this->assertEquals('Foi vendido.', $obj->getAnotacoes(), 'Atributo que possui getter e setter: getAnotacoes não bate.');
        //Atributo que possui getter e setter
		$obj->setAnotacoes('NÃO Foi vendido.');
        $this->assertEquals('NÃO Foi vendido.', $obj->anotacoes, 'Atributo que possui getter e setter, usingSetter: Atribute direct access anotacoes não bate.');
        $this->assertEquals('NÃO Foi vendido.', $obj->getAnotacoes(), 'Atributo que possui getter e setter, usingSetter: getAnotacoes não bate.');


        //Atributo que possui getter e setter
		$obj->createdAt = '2020-01-15';
        $this->assertEquals('2020-01-15', $obj->created_at, 'Atributo que possui getter e setter: Atribute direct access created_at não bate.');
        $this->assertEquals('2020-01-15', $obj->createdAt, 'Atributo que possui getter e setter: Atribute direct access createdAt não bate.');
        $this->assertEquals('2020-01-15', $obj->getCreatedAt(), 'Atributo que possui getter e setter: getCreatedAt não bate.');
        //Atributo que possui getter e setter
		$obj->setCreatedAt('2020-01-16 08:00:00');
        $this->assertEquals('2020-01-16 08:00:00', $obj->created_at, 'Atributo que possui getter e setter, usingSetter: Atribute direct created_at anotacoes não bate.');
        $this->assertEquals('2020-01-16 08:00:00', $obj->createdAt, 'Atributo que possui getter e setter, usingSetter: Atribute direct createdAt anotacoes não bate.');
        $this->assertEquals('2020-01-16 08:00:00', $obj->getCreatedAt(), 'Atributo que possui getter e setter, usingSetter: getCreatedAt não bate.');
        //Atributo que possui getter e setter
		$obj->created_at = '2020-01-17';
        $this->assertEquals('2020-01-17', $obj->created_at, 'Atributo que possui getter e setter: Atribute direct access created_at não bate.');
        $this->assertEquals('2020-01-17', $obj->createdAt, 'Atributo que possui getter e setter: Atribute direct access createdAt não bate.');
        $this->assertEquals('2020-01-17', $obj->getCreatedAt(), 'Atributo que possui getter e setter: getCreatedAt não bate.');

        //Atributo que possui getter e setter
        $this->assertEquals('Alysson Teixeira Mangos-NÃO Foi vendido.', $obj->vendedor_com_anotacoes, 'Atributo que possui getter e setter: Atribute direct access vendedor_com_anotacoes não bate.');
        $this->assertEquals('Alysson Teixeira Mangos-NÃO Foi vendido.', $obj->vendedorComAnotacoes, 'Atributo que possui getter e setter: Atribute direct access vendedorComAnotacoes não bate.');
        $this->assertEquals('Alysson Teixeira Mangos-NÃO Foi vendido.', $obj->getVendedorComAnotacoes(), 'Atributo que possui getter e setter: getVendedorComAnotacoes não bate.');
	}
        public function testDeleteAndAfterDelete(){
                $con = \SolvesDAO\SolvesDAO::openConnectionMock();
                $obj = new SolvesObjectCompraMock($con);

                $obj->delete();
                $this->assertEquals(true, $obj->excluido, 'Nao alterou atributo "excluido".');
        }
        public function testAtributosAlterados(){
                $con = \SolvesDAO\SolvesDAO::openConnectionMock();
                $obj = new SolvesObjectCompraMock($con);
                $attrs = $obj->getAtributosAlterados();
                $this->assertEquals(0, count($attrs), 'objeto limpo nao deve ter atributos alterados');
                $obj->setVendedor('TESTE');
                $attrs = $obj->getAtributosAlterados();
                $this->assertEquals(1, count($attrs), 'Somente 1 atributo foi alterado');
        }
}