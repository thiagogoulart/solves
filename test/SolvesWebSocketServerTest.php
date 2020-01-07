 <?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 29/07/2019
 
*/

include_once 'src/Solves/Solves.php';
include_once 'src/SolvesWebsocket/SolvesWebSocketServer.php';
include_once 'src/SolvesWebsocket/SolvesWebSocketServerRoute.php';
include_once 'src/SolvesWebsocket/SolvesWebSocketServerRouteMessenger.php';
use PHPUnit\Framework\TestCase;

class SolvesWebSocketServerTest extends TestCase {
	public static function setUpBeforeClass() : void{
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  
	}
	public function tearDown()  : void{
	  
	}
	public function testStartServer(){
		$server = new \SolvesWebsocket\SolvesWebSocketServer('localhost',8080);

		$route = new \SolvesWebsocket\SolvesWebSocketServerRouteMessenger('/chat');
		$server->addRoute($route);

	//	$server->startServer();
		$routes = $server->getRoutes();
		$qtdRoutes = count($routes);

		$this->assertEquals(1, $qtdRoutes, 'Quantidade de rotas everia ser 1, obteve:'.$qtdRoutes);
	}
}