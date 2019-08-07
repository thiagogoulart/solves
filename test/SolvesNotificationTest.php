 <?php
/**

		* @author Thiago G.S. Goulart (SOLVES SOLUÇÕES EM SOFTWARE)
 
		* * https://www.solves.com.br 
 
		* @version 1.0
 
		* @created 29/07/2019
 
*/

include_once 'src/Solves/Solves.php';
include_once 'src/SolvesNotification/SolvesNotification.php';
use PHPUnit\Framework\TestCase;
use Minishlink\WebPush\VAPID;

class SolvesNotificationTest extends TestCase {
	public static function setUpBeforeClass() : void{
	}
	public static function tearDownAfterClass() : void{
	 
	}
	protected function setUp() : void{
	  \Solves\Solves::config('TESTE', '1.0', 'Sistema de Teste', 'http://localhost/', 'Teste teste bla bla..', 'Teste teste bla bla..', '/assets/img/', '/assets/img/logo/');
		\Solves\Solves::configNotifications('BArxBPQPeEfedG2gOiB9fTvlYCgnE0bOfdZlQZZcGMMXDG-EFSYjmdx9Y1oG66FnI1LLNXC1B42i7sYwMt_FIJk', "173Ih3On9oXCg0TigV06cP7I5330m89xseV3BSQPjwo");
	}
	public function tearDown()  : void{
	  
	}
	public function testSend(){
		//var_dump(VAPID::createVapidKeys()); 

		$authToken = 'WNl7//zI16QHeP ZN2jh8g==';
		$content_encoding= 'aes128gcm';
		$endpoint = 'https://fcm.googleapis.com/fcm/send/eADNOzHFx4U:APA91bGoZRY2-dmWvJ1tuF7GDhb_8XGx4M4YHasKuq18AFa-3rwrLl0xnp1q-0zGpASxwDbjX9T4Fch9zs7jKF6gbsmsyxTRT1z7fDSgui-1H4KGqKXzhBz0aiuwUxA68ycVWXcGuGnb';
		$publicKey = 'BBkGtJzTaS6ehtShhl3NveghfppnbRZA7NGgZW7eYdYujgB-YiNN_pKT3X-omM8cU6pyXk4a30eYQnk-Exo1674';

		$idNotification=null;
		$title='Teste MSG';
		$message='Olá, tudo bem?';
		$image=null;
		\SolvesNotification\SolvesNotification::sendNotificationToOneEndpoint($authToken, $content_encoding, $endpoint, $publicKey,
			$idNotification, $title, $message, $image);
	}
}